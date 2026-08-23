<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-300">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <title inertia>AMYN Madrasah - Striving for the Deeds</title>

    <script>
      (function() {
        var theme = localStorage.getItem('theme');
        if (theme === 'light') {
          document.documentElement.classList.remove('dark');
        } else {
          document.documentElement.classList.add('dark');
        }
      })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Cinzel:wght@600;700;800&display=swap" rel="stylesheet">

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
  </head>
  <body class="font-sans antialiased h-full bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 selection:bg-amber-500 selection:text-slate-950 transition-colors duration-300">
    @inertia
  </body>
</html>
