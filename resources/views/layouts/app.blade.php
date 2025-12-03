<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BIM TECH') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; }
            
            /* Custom Scrollbar for Sidebar */
            .sidebar-scroll::-webkit-scrollbar { width: 5px; }
            .sidebar-scroll::-webkit-scrollbar-track { background: #1e293b; }
            .sidebar-scroll::-webkit-scrollbar-thumb { background: #475569; border-radius: 10px; }
            .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: #64748b; }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800">
        
        <div class="min-h-screen flex flex-col md:flex-row">
            
            <livewire:layout.navigation />

            <main class="flex-1 md:ml-64 w-full min-h-screen transition-all duration-300 ease-in-out">
                
                <div class="md:hidden bg-slate-900 text-white p-4 flex justify-between items-center sticky top-0 z-40">
                    <span class="font-bold text-lg">BIM TECH</span>
                    </div>

                <div class="p-6 md:p-10">
                    {{ $slot }}
                </div>

                <div class="border-t border-slate-200 p-6 text-center text-xs text-slate-400">
                    &copy; {{ date('Y') }} BIM TECH System. Version 1.0
                </div>
            </main>
        </div>
    </body>
</html>