<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Auth' }} • Playlist</title>
    {{-- Tailwind via CDN (swap to Vite in prod) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            DEFAULT: '#0ea5e9',
                            dark: '#0284c7'
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
    <nav class="border-b bg-white/70 backdrop-blur sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('auth.index') }}" class="font-semibold text-slate-800">Playlist Auth</a>
            <a href="{{ route('videos') }}" class="font-semibold text-slate-800">Watch Videos</a>
            <div class="flex items-center gap-3">
                @if (session('api_user'))
                    <span class="text-sm text-slate-600">Hi,
                        {{ session('api_user.name') ?? (session('api_user')['name'] ?? 'User') }}</span>
                    <form action="{{ route('auth.logout') }}" method="POST">
                        @csrf
                        <button class="rounded-xl px-3 py-1.5 text-sm bg-slate-900 text-white hover:bg-slate-800">
                            Logout
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto p-4">
        @if (session('success'))
            <div class="mb-4 rounded-xl bg-green-50 border border-green-200 p-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif
        @if (session('warning'))
            <div class="mb-4 rounded-xl bg-yellow-50 border border-yellow-200 p-3 text-yellow-800">
                {{ session('warning') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-3 text-rose-800">
                <div class="font-semibold mb-1">There were problems:</div>
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                    @if ($errors->has('api_errors'))
                        @foreach ((array) $errors->get('api_errors') as $field => $msgs)
                            @foreach ((array) $msgs as $msg)
                                <li>{{ is_string($field) ? ucfirst($field) . ': ' : '' }}{{ $msg }}</li>
                            @endforeach
                        @endforeach
                    @endif
                </ul>
            </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <footer class="max-w-5xl mx-auto p-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Playlist Group
    </footer>
</body>

</html>
