<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'c_password' => 'required|same:password',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Auth::login($user);

            $success = [
                'token' => $user->createToken('MyApp')->plainTextToken,
                'name'  => $user->name,
            ];

            return $this->sendResponse($success, 'User registered successfully.', 201);
        } catch (Exception $e) {
            return $this->sendError('Registration Failed.', $e->getMessage(), 500);
        }
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                
                $success = [
                    'token' => $user->createToken('MyApp')->plainTextToken,
                    'name'  => $user->name,
                ];
                
                return $this->sendResponse($success, 'User login successfully.');
            }
            return $this->sendError('Unauthorized.', ['error' => 'Invalid credentials'], 401);
        } catch (Exception $e) {
            return $this->sendError('Login Failed.', $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (! $user) {
                return $this->sendError('Not authenticated.', ['error' => 'No user'], 401);
            }

            if ($user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }

            return $this->sendResponse(['message' => 'success'], 'User logout successfully.', 200);
        } catch (Exception $e) {
            return $this->sendError('Logout Failed.', $e->getMessage(), 500);
        }
    }
}
