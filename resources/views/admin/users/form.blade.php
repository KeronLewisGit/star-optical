@php $editing = $user->exists; @endphp
<x-admin-layout :title="$editing ? 'Edit '.$user->name : 'Add staff account'">
    <form method="POST" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" class="max-w-xl card" data-once>
        @csrf
        @if($editing)@method('PUT')@endif
        <div class="card-body space-y-4">
            <x-form.input name="name" label="Full name" :value="$user->name" required />
            <x-form.input name="email" label="Email" type="email" :value="$user->email" required />
            <x-form.select name="role" label="Role" :options="['staff' => 'Staff', 'admin' => 'Administrator']" :value="$user->role" required />
            <x-form.input name="password" label="{{ $editing ? 'New password (leave blank to keep)' : 'Password' }}" type="password" :required="! $editing" autocomplete="new-password" help="At least 12 characters with upper and lower case letters, a number and a symbol." />
            <x-form.input name="password_confirmation" label="Confirm password" type="password" :required="! $editing" autocomplete="new-password" />
            <div class="flex gap-2 pt-2"><button class="btn-primary">{{ $editing ? 'Save' : 'Create account' }}</button><a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a></div>
        </div>
    </form>
</x-admin-layout>
