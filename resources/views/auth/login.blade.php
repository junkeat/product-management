<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · Product Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <main class="flex min-h-screen items-center justify-center px-5 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 flex items-center justify-center gap-3 text-lg font-semibold tracking-tight">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-sm font-bold text-white">PM</span>
                Product Manager
            </div>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="mb-7">
                    <p class="mb-2 text-sm font-medium text-indigo-600">Welcome back</p>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-950">Sign in to your account</h1>
                    <p class="mt-2 text-sm text-slate-500">Enter your credentials to access the products dashboard.</p>
                </div>

                @if ($errors->any())
                    <div role="alert" class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email"
                            required autofocus
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" value="1"
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        Remember me
                    </label>

                    <button type="submit"
                        class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700">
                        Sign in
                    </button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
