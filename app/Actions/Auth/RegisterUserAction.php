<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Requests\API\RegisterRequest;

class RegisterUserAction
{
    public function execute(RegisterRequest $request): User
    {
        $uuid = $this->createUuid();
        $user = $this->createUser($request, $uuid);
        // $user->sendEmailVerificationNotification();
        // $this->syncRoles($user, $request);

        return $user;
    }

    private function createUser(RegisterRequest $request, string $uuid): User
    {
        return User::create($request->validated() + [
            'uuid' => $uuid,
        ]);
    }

    // private function syncRoles(User $user): array
    // {
    // }

    private function createUuid(): string
    {
        return substr(Str::uuid(), 0, 5);
    }
}
