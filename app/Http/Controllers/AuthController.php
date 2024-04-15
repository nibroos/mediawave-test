<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginRequest;
use App\Actions\Auth\RegisterUserAction;
use App\Http\Requests\API\RegisterRequest;

class AuthController extends Controller
{
    public function loginUser(LoginRequest $request)
    {
        try {
            $request->authenticate();
            // $request->session()->regenerate();
            $user = auth()->user();

            $data = [];
            $data[] = collect($user)->merge([
                'token' => $user->createToken('token')->plainTextToken,
                'permissions' => []
            ]);

            return apiSuccessGetResponse(collect($data), false, 'Authenticated');
        } catch (\Exception $e) {
            return apiErrorGetResponse($e, 'Failed to login');
        }
    }

    public function logoutUser()
    {
        try {
            DB::beginTransaction();
            request()->user()->currentAccessToken()->delete();
            DB::commit();

            return apiSuccessGetResponse(collect([]), false, 'Successfully logout');
        } catch (\Exception $e) {
            return apiErrorGetResponse($e, 'Failed to logout');
        }
    }

    public function registerUser(RegisterRequest $request, RegisterUserAction $registerUserAction)
    {
        try {
            DB::beginTransaction();
            $user = $registerUserAction->execute($request);
            $request->authenticate();
            // $request->session()->regenerate();
            $user = auth()->user();
            DB::commit();

            $data = [];
            $data[] = collect($user)->merge([
                'token' => $user->createToken('token')->plainTextToken,
            ]);

            return apiSuccessGetResponse(collect($data), false, 'Authenticated');
        } catch (\Exception $e) {
            return apiErrorGetResponse($e, 'Failed to register');
        }
    }

    public function getAuthUser()
    {
        try {
            $user = [];
            $user[] = collect(
                User::find(auth()->id())
            )->merge([
                'token' => request()->bearerToken(),
                'permissions' => []
            ]);

            return apiSuccessGetResponse(collect($user), false, 'Success retrieve user data');
        } catch (\Exception $e) {
            return apiErrorGetResponse($e, 'Failed retrieve user data');
        }
    }
}
