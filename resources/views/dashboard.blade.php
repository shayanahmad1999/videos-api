@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    <div class="rounded-2xl bg-white shadow-sm border p-8">
        <h2 class="text-2xl font-semibold text-slate-900">Dashboard</h2>
        <p class="mt-2 text-slate-600">You are logged in via API token.</p>
        {{ session()->get('api_token') }}
        <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="rounded-xl border p-5">
                <div class="text-sm text-slate-500">Name</div>
                <div class="text-lg font-medium">{{ $user['name'] ?? '—' }}</div>
            </div>
            <div class="rounded-xl border p-5">
                <div class="text-sm text-slate-500">Email</div>
                <div class="text-lg font-medium">{{ $user['email'] ?? '—' }}</div>
            </div>
            <div class="rounded-xl border p-5">
                <div class="text-sm text-slate-500">Token Present</div>
                <div class="text-lg font-medium">{{ session()->has('api_token') ? 'Yes' : 'No' }}</div>
            </div>
        </div>

        <form class="mt-8" action="{{ route('auth.logout') }}" method="POST">
            @csrf
            <button class="rounded-xl px-4 py-2 bg-rose-600 text-white hover:bg-rose-500">Logout</button>
        </form>
    </div>
@endsection
