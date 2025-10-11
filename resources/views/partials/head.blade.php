<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

@php
    $organization = \App\Models\Organization::first();
    $favicon = $organization && $organization->favicon 
        ? \Illuminate\Support\Facades\Storage::url($organization->favicon)
        : '/favicon.ico';
    
    // For apple-touch-icon, use organization logo or default
    $appleTouchIcon = $organization && $organization->logo
        ? \Illuminate\Support\Facades\Storage::url($organization->logo)
        : '/apple-touch-icon.png';
@endphp

<link rel="icon" href="{{ $favicon }}" sizes="any">
<link rel="apple-touch-icon" href="{{ $appleTouchIcon }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
