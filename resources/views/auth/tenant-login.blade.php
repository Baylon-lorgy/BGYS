<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form method="POST" action="{{ route('tenant.login.submit') }}" class="space-y-6">
                @csrf

                <!-- Domain Name -->
                <div>
                    <x-input id="domain_name" 
                            class="block mt-1 w-full" 
                            type="text" 
                            name="domain_name" 
                            :value="old('domain_name')" 
                            placeholder="Domain Name"
                            required 
                            autofocus />
                </div>

                <!-- Email Address -->
                <div>
                    <x-input id="email" 
                            class="block mt-1 w-full" 
                            type="email" 
                            name="email" 
                            :value="old('email')" 
                            placeholder="Email Address"
                            required />
                </div>

                <!-- Password -->
                <div>
                    <x-input id="password" 
                            class="block mt-1 w-full"
                            type="password"
                            name="password"
                            placeholder="Password"
                            required 
                            autocomplete="current-password" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <div>
                    <x-button class="w-full justify-center">
                        {{ __('Log in') }}
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout> 