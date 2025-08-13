@extends('layouts.app')

@section('content')
    <div x-data="{ tab: '{{ old('email') ? 'login' : 'register' }}' }" class="grid lg:grid-cols-2 gap-8 items-center">
        {{-- Left: Hero --}}
        <div class="hidden lg:block">
            <div class="rounded-2xl bg-white shadow-sm border p-8">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Welcome to Archiwiz</h1>
                <p class="mt-3 text-slate-600">Create an account or sign in to continue. Secure, fast, and clean UI powered
                    by Guzzle to your APIs.</p>
                <ul class="mt-6 space-y-3 text-slate-700">
                    <li>• Fully server-validated forms</li>
                    <li>• Session-based bearer token storage</li>
                    <li>• API-driven auth via Guzzle</li>
                </ul>
            </div>
        </div>

        {{-- Right: Auth Card --}}
        <div class="rounded-2xl bg-white shadow-sm border p-6">
            <div class="flex bg-slate-100 rounded-xl p-1 text-sm font-medium w-full">
                <button type="button" @click="tab='login'"
                    :class="tab === 'login' ? 'bg-white shadow text-slate-900' : 'text-slate-600'"
                    class="w-1/2 px-4 py-2 rounded-lg transition">Login</button>
                <button type="button" @click="tab='register'"
                    :class="tab === 'register' ? 'bg-white shadow text-slate-900' : 'text-slate-600'"
                    class="w-1/2 px-4 py-2 rounded-lg transition">Register</button>
            </div>

            {{-- Login --}}
            <form x-show="tab==='login'" x-cloak class="mt-6 space-y-4" action="{{ route('auth.login') }}" method="POST">
                @csrf
                <div>
                    <label class="block text-sm text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-xl border-slate-300 focus:border-brand focus:ring-brand" />
                </div>
                <div>
                    <label class="block text-sm text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" required minlength="6"
                        class="w-full rounded-xl border-slate-300 focus:border-brand focus:ring-brand" />
                </div>
                <button class="w-full rounded-xl bg-slate-900 text-white py-2.5 hover:bg-slate-800">Sign In</button>
            </form>

            {{-- Register --}}
            <form x-show="tab==='register'" x-cloak class="mt-6 space-y-4" action="{{ route('auth.register') }}"
                method="POST">
                @csrf
                <div>
                    <label class="block text-sm text-slate-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-xl border-slate-300 focus:border-brand focus:ring-brand" />
                </div>
                <div>
                    <label class="block text-sm text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-xl border-slate-300 focus:border-brand focus:ring-brand" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-slate-700 mb-1">Password</label>
                        <input type="password" name="password" required minlength="6"
                            class="w-full rounded-xl border-slate-300 focus:border-brand focus:ring-brand" />
                    </div>
                    <div>
                        <label class="block text-sm text-slate-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required minlength="6"
                            class="w-full rounded-xl border-slate-300 focus:border-brand focus:ring-brand" />
                    </div>
                </div>
                <button class="w-full rounded-xl bg-brand text-white py-2.5 hover:bg-brand-dark">Create Account</button>
            </form>
        </div>
    </div>
@endsection
