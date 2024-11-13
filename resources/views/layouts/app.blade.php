<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUa3TNkHlHj6A27UpdbQ/gPeHnueJLNBpsVHr5V1iw5WBl1t0vzg3WhvZf5p" crossorigin="anonymous">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container-fluid p-0">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-light shadow-sm">
                <div class="container py-3">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="container my-4">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-QF7xA7HpHQ/Suoy9k0PAmusG+ygB28ez5F1ifEGnsuZ85p2sD4FJCLRy1w/hV1Vn" crossorigin="anonymous"></script>
</body>
</html>
