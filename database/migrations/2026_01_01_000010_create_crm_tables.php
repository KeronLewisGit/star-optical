<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Key/value site settings (contact details, tracking IDs, hours, etc.)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Patients: the CRM record a lead becomes once they are a customer.
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_number', 20)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 30)->index();
            $table->string('phone_alt', 30)->nullable();
            $table->string('email')->nullable()->index();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->text('medical_notes')->nullable();   // encrypted
            $table->text('allergies')->nullable();       // encrypted
            $table->string('insurance_provider', 150)->nullable();
            $table->string('status', 20)->default('active'); // active | inactive
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Bookings: leads that arrive from the website (or are added by staff).
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 12)->unique();
            $table->string('name', 150);
            $table->string('phone', 30)->index();
            $table->string('email')->nullable();
            $table->string('service', 100);
            $table->date('preferred_date')->nullable();
            $table->string('preferred_time', 30)->nullable();
            $table->text('notes')->nullable();            // customer's message, encrypted
            $table->string('status', 20)->default('new')->index(); // new|contacted|scheduled|completed|cancelled
            $table->string('source', 30)->default('website');
            $table->dateTime('appointment_at')->nullable();
            $table->text('internal_notes')->nullable();   // staff notes, encrypted
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('contacted_at')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Prescriptions recorded for a patient after an eye exam.
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->date('exam_date');
            $table->string('od_sphere', 10)->nullable();
            $table->string('od_cylinder', 10)->nullable();
            $table->string('od_axis', 10)->nullable();
            $table->string('od_add', 10)->nullable();
            $table->string('os_sphere', 10)->nullable();
            $table->string('os_cylinder', 10)->nullable();
            $table->string('os_axis', 10)->nullable();
            $table->string('os_add', 10)->nullable();
            $table->string('pd', 20)->nullable();
            $table->string('examined_by', 150)->nullable();
            $table->text('notes')->nullable();            // encrypted
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Free-text timeline notes on a patient (calls, visits, follow-ups).
        Schema::create('patient_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');                         // encrypted
            $table->timestamps();
        });

        // Promotions shown on the public site carousel.
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('kicker', 60)->nullable();
            $table->string('body', 255)->nullable();
            $table->string('cta_text', 60)->default('Claim on WhatsApp');
            $table->string('theme', 20)->default('blue');
            $table->string('image_path')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->timestamps();
        });

        // Audit trail for every change to sensitive records.
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 40);
            $table->string('subject_type', 120)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('description', 255)->nullable();
            $table->json('changes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at');
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('patient_notes');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('settings');
    }
};
