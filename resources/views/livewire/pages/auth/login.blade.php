<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen flex bg-white">
    
    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-25 xl:px-30 bg-white w-full lg:w-[480px] z-10">
        <div class="mx-auto w-full max-w-sm lg:w-96">
            
            <div class="flex items-center gap-2 mb-10">
                <div class="bg-blue-600 text-white p-1.5 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-slate-900">BIM EST</span>
            </div>

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Welcome back</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Please enter your details to sign in.
                </p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form wire:submit="login" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <div class="mt-1">
                        <input wire:model="form.email" id="email" type="email" required autofocus autocomplete="username" 
                            class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition">
                    </div>
                    @error('form.email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1">
                        <input wire:model="form.password" id="password" type="password" required autocomplete="current-password" 
                            class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition">
                    </div>
                    @error('form.password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input wire:model="form.remember" id="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-500" wire:navigate>
                                Forgot password?
                            </a>
                        </div>
                    @endif
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-slate-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition">
                        Sign in
                        <svg wire:loading wire:target="login" class="animate-spin ml-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500" wire:navigate>
                        Sign up for free
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="hidden lg:block relative w-0 flex-1 bg-slate-900">
        <div class="absolute inset-0 h-full w-full overflow-hidden">
             <img class="absolute inset-0 h-full w-full object-cover opacity-40 grayscale hover:grayscale-0 transition-all duration-700 ease-in-out" 
                 src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop" 
                 alt="Modern Architecture">
            
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
        </div>

        <div class="absolute bottom-0 left-0 right-0 p-20 text-white z-10">
            <div class="flex items-center gap-2 mb-4">
                <span class="px-3 py-1 rounded-full border border-blue-400 text-blue-300 text-xs font-bold uppercase tracking-widest">
                    New Release v1.0
                </span>
            </div>
            <h1 class="text-4xl font-bold mb-4 leading-tight">
                "The future of construction is data-driven."
            </h1>
            <p class="text-slate-300 text-lg max-w-md">
                Automate your Quantity Take-off and BOQ generation directly from Autodesk BIM models.
            </p>
        </div>
    </div>
</div>