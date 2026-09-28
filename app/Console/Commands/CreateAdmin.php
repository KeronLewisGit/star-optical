<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {--name=} {--email=} {--password=}';

    protected $description = 'Create an administrator account (public registration is disabled).';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Full name');
        $email = strtolower($this->option('email') ?: $this->ask('Email address'));
        $password = $this->option('password') ?: $this->secret('Password (min 12 chars, upper/lower/number/symbol)');

        $validator = Validator::make(compact('name', 'email', 'password'), [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email:rfc', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->info("Administrator {$user->email} created. Sign in at ".route('login'));

        return self::SUCCESS;
    }
}
