<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen flex bg-white">
    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white w-full lg:w-[480px] z-10">
        <div class="mx-auto w-full max-w-sm lg:w-96">
            <div class="flex items-center gap-2 mb-10">
                <div class="bg-blue-600 text-white p-1.5 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-slate-900">BIM EST</span>
            </div>

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Create account</h2>
                <p class="mt-2 text-sm text-gray-600">Start your 5D BIM journey today.</p>
            </div>

            <form wire:submit="register" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input wire:model="name" type="text" required autofocus class="mt-1 appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email address</label>
                    <input wire:model="email" type="email" required class="mt-1 appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input wire:model="password" type="password" required class="mt-1 appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input wire:model="password_confirmation" type="password" required class="mt-1 appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    Register
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500" wire:navigate>
                        Log in
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="hidden lg:block relative w-0 flex-1 bg-slate-900">
        <div class="absolute inset-0 h-full w-full overflow-hidden">
            <img class="absolute inset-0 h-full w-full object-cover opacity-40" src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop" alt="Architecture">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 p-20 text-white z-10">
            <h1 class="text-4xl font-bold mb-4">Join the Revolution.</h1>
            <p class="text-slate-300 text-lg">Create precise cost estimates in minutes, not days.</p>
        </div>
    </div>
</div>