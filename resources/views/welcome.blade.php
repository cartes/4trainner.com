<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>FoxFit | Empower Your Fitness Journey</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;family=Outfit:wght@300;400;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 font-sans transition-colors duration-300">
    <div id="app"
         data-can-login="{{ Route::has('login') ? 'true' : 'false' }}"
         data-can-register="{{ Route::has('register') ? 'true' : 'false' }}"
         data-auth-check="{{ Auth::check() ? 'true' : 'false' }}"
         data-login-url="{{ route('login') }}"
         data-register-url="{{ route('register') }}"
         data-dashboard-url="{{ url('/dashboard') }}"
    ></div>
</body>
</html>
