<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthenticatedController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function register(Request $request, ApiClient $api)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $resp = $api->post('/user-register', $validated);

        if (!$resp['ok']) {
            return back()->withInput()->withErrors(['api' => $resp['error'] ?? 'Registration failed', 'api_errors' => $resp['errors'] ?? []]);
        }

        // If your API logs the user in automatically and returns token/user, you can store it here.
        // Otherwise, show success and let them switch to login tab.
        return back()->with('success', 'Account created! Please log in.');
    }

    public function login(Request $request, ApiClient $api)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $resp = $api->post('/user-login', $validated);

        if (!$resp['ok']) {
            return back()->withInput()->withErrors(['api' => $resp['error'] ?? 'Login failed', 'api_errors' => $resp['errors'] ?? []]);
        }

        // Expecting something like: ['token' => '...', 'user' => ['name'=>..., 'email'=>...]]
        $data = $resp['data'] ?? [];
        session([
            'api_token' => $data['token'] ?? null,
            'api_user'  => $data['user']  ?? null,
        ]);

        return redirect()->route('dashboard')->with('success', 'Welcome back!');
    }

    public function logout(Request $request, ApiClient $api)
    {
        $token = session('api_token');
        if ($token) {
            $api->post('/user-logout', [], $token); // ignore response; we’ll clear session either way
        }
        $request->session()->forget(['api_token', 'api_user']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.index')->with('success', 'You have been logged out.');
    }

    public function dashboard()
    {
        $user = session('api_user');
        return view('dashboard', compact('user'));
    }
}
