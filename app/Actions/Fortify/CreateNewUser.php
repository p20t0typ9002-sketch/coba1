<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateNewUser implements \Laravel\Fortify\Contracts\CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        // Membuat username otomatis dari nama pengguna.
        $baseUsername = Str::slug($input['name'], '_');
        $baseUsername = substr($baseUsername ?: 'peminjam', 0, 10);

        $username = $baseUsername;
        $counter = 1;

        // Pastikan username belum digunakan.
        while (User::where('username', $username)->exists()) {
            $suffix = '_' . $counter++;
            $username = substr(
                $baseUsername,
                0,
                15 - strlen($suffix)
            ) . $suffix;
        }

        return User::create([
            'name' => $input['name'],
            'username' => $username,
            'email' => $input['email'],
            'password' => $input['password'],
            'level' => 'peminjam',
        ]);
    }
}
