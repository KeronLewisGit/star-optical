<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_can_be_converted_into_a_new_patient(): void
    {
        $staff = User::factory()->create();
        $booking = Booking::factory()->create(['name' => 'Marcus De Silva', 'phone' => '18685550001', 'email' => 'marcus@example.com', 'notes' => 'Blurry at night']);

        $response = $this->actingAs($staff)->post(route('admin.bookings.convert', $booking));

        $patient = Patient::first();
        $this->assertNotNull($patient);
        $this->assertSame('Marcus', $patient->first_name);
        $this->assertSame('De Silva', $patient->last_name);
        $this->assertSame('18685550001', $patient->phone);
        $this->assertSame('marcus@example.com', $patient->email);
        $this->assertSame('SO-000001', $patient->patient_number);
        $this->assertSame($staff->id, $patient->created_by);

        $booking->refresh();
        $this->assertSame($patient->id, $booking->patient_id);
        $this->assertSame('scheduled', $booking->status);

        $this->assertStringContainsString('Blurry at night', $patient->notes()->first()->body);
        $this->assertDatabaseHas('activity_logs', ['action' => 'converted', 'subject_id' => $booking->id, 'user_id' => $staff->id]);

        $response->assertRedirect(route('admin.patients.show', $patient));
    }

    public function test_booking_can_be_linked_to_an_existing_patient_with_the_same_phone(): void
    {
        $staff = User::factory()->create();
        $existing = Patient::factory()->create(['phone' => '18685550002']);
        $booking = Booking::factory()->create(['phone' => '(868) 555-0002']);

        $this->actingAs($staff)->get(route('admin.bookings.show', $booking))
            ->assertOk()->assertSee('Possible existing patient')->assertSee($existing->patient_number);

        $this->actingAs($staff)->post(route('admin.bookings.convert', $booking), ['patient_id' => $existing->id]);

        $this->assertDatabaseCount('patients', 1);
        $this->assertSame($existing->id, $booking->fresh()->patient_id);
    }

    public function test_converted_booking_cannot_be_converted_twice(): void
    {
        $staff = User::factory()->create();
        $patient = Patient::factory()->create();
        $booking = Booking::factory()->create(['patient_id' => $patient->id]);

        $this->actingAs($staff)->post(route('admin.bookings.convert', $booking))->assertForbidden();
    }

    public function test_patient_record_can_be_completed_with_encrypted_medical_data_and_prescription(): void
    {
        $staff = User::factory()->create();
        $patient = Patient::factory()->create();

        $this->actingAs($staff)->put(route('admin.patients.update', $patient), [
            'first_name' => $patient->first_name, 'last_name' => $patient->last_name, 'phone' => $patient->phone,
            'date_of_birth' => '1990-05-04', 'gender' => 'female', 'status' => 'active',
            'medical_notes' => 'Diabetic, annual retinal check', 'allergies' => 'Penicillin',
        ])->assertRedirect(route('admin.patients.show', $patient));

        $patient->refresh();
        $this->assertSame('Diabetic, annual retinal check', $patient->medical_notes);
        $this->assertNotSame('Penicillin', \DB::table('patients')->value('allergies'));

        $this->actingAs($staff)->post(route('admin.patients.prescriptions.store', $patient), [
            'exam_date' => now()->toDateString(), 'od_sphere' => '-1.25', 'od_cylinder' => '-0.50', 'od_axis' => '180',
            'os_sphere' => '-1.00', 'pd' => '63', 'examined_by' => 'Dr. Ali',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('prescriptions', ['patient_id' => $patient->id, 'od_sphere' => '-1.25', 'created_by' => $staff->id]);

        $this->actingAs($staff)->post(route('admin.patients.prescriptions.store', $patient), [
            'exam_date' => now()->toDateString(), 'od_axis' => '999',
        ])->assertSessionHasErrors('od_axis');

        $this->actingAs($staff)->get(route('admin.patients.show', $patient))
            ->assertOk()->assertSee('Penicillin')->assertSee('Dr. Ali');
    }

    public function test_patient_changes_are_audited_without_leaking_encrypted_values(): void
    {
        $staff = User::factory()->create();
        $patient = Patient::factory()->create(['first_name' => 'Ann']);

        $this->actingAs($staff)->put(route('admin.patients.update', $patient), [
            'first_name' => 'Anne', 'last_name' => $patient->last_name, 'phone' => $patient->phone, 'status' => 'active',
            'medical_notes' => 'Secret condition',
        ]);

        $log = ActivityLog::where('subject_type', Patient::class)->where('action', 'updated')->latest('id')->first();
        $this->assertSame(['from' => 'Ann', 'to' => 'Anne'], $log->changes['first_name']);
        $this->assertSame('[changed]', $log->changes['medical_notes']['to']);
        $this->assertStringNotContainsString('Secret condition', json_encode($log->changes));
    }

    public function test_patients_can_be_searched_by_formatted_phone(): void
    {
        $staff = User::factory()->create();
        Patient::factory()->create(['first_name' => 'Priya', 'phone' => '18685557777']);
        Patient::factory()->create(['first_name' => 'Other', 'phone' => '18685550000']);

        $this->actingAs($staff)->get(route('admin.patients.index', ['q' => '555-7777']))
            ->assertOk()->assertSee('Priya')->assertDontSee('Other');
    }
}
