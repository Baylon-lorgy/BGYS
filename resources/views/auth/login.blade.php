<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#1A1D21]">
        <div>
            <h1 class="text-3xl font-bold text-[#56CCF2] mb-2 flex items-center gap-2">
                <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <!-- Chef's Hat -->
                    <path d="M12 2C9.85 2 7.89 2.87 6.34 4.34L5.63 3.63C5.24 3.24 4.63 3.24 4.24 3.63C3.85 4.02 3.85 4.63 4.24 5.02L4.95 5.73C3.9 7 3.24 8.57 3.06 10.27C2.3 10.41 1.7 11.08 1.7 11.9C1.7 12.84 2.5 13.6 3.47 13.6H20.53C21.5 13.6 22.3 12.84 22.3 11.9C22.3 11.08 21.7 10.41 20.94 10.27C20.76 8.57 20.1 7 19.05 5.73L19.76 5.02C20.15 4.63 20.15 4.02 19.76 3.63C19.37 3.24 18.76 3.24 18.37 3.63L17.66 4.34C16.11 2.87 14.15 2 12 2Z"/>
                    
                    <!-- Rolling Pin -->
                    <path d="M20 16H4C3.45 16 3 16.45 3 17V18C3 18.55 3.45 19 4 19H20C20.55 19 21 18.55 21 18V17C21 16.45 20.55 16 20 16Z"/>
                    <path d="M19 15H5C4.45 15 4 15.45 4 16C4 16.55 4.45 17 5 17H19C19.55 17 20 16.55 20 16C20 15.45 19.55 15 19 15Z"/>
                    <path d="M19 19H5C4.45 19 4 19.45 4 20C4 20.55 4.45 21 5 21H19C19.55 21 20 20.55 20 20C20 19.45 19.55 19 19 19Z"/>
                </svg>
                {{ config('app.name') }}
            </h1>
        </div>
        <div class="w-full sm:max-w-md mt-6 px-8 py-6 bg-[#22262A] shadow-xl overflow-hidden sm:rounded-lg border border-[#2F3338]">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-text-input id="email" 
                        class="block w-full px-4 py-3 border bg-[#2F3338] text-white border-[#3A3F45] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#56CCF2] focus:border-transparent placeholder-gray-400" 
                        type="email" 
                        name="email" 
                        placeholder="Email"
                        :value="old('email')" 
                        required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
                </div>

                <!-- Password -->
                <div>
                    <x-text-input id="password" 
                        class="block w-full px-4 py-3 border bg-[#2F3338] text-white border-[#3A3F45] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#56CCF2] focus:border-transparent placeholder-gray-400"
                        type="password"
                        name="password"
                        placeholder="Password"
                        required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" 
                            class="rounded border-[#3A3F45] bg-[#2F3338] text-[#56CCF2] shadow-sm focus:ring-[#56CCF2]" 
                            name="remember">
                        <span class="ml-2 text-sm text-gray-300">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-[#56CCF2] hover:text-[#2F80ED] transition-colors" 
                            href="{{ route('password.request') }}">
                            Forgot your password?
                        </a>
                    @endif
                </div>

                <div>
                    <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-[#2F80ED] to-[#56CCF2] hover:from-[#56CCF2] hover:to-[#2F80ED] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#56CCF2] transform transition-all hover:-translate-y-0.5">
                        Log in
                    </button>
                </div>

                <div class="flex items-center justify-center mt-4">
                    <a class="text-sm text-gray-300 hover:text-[#56CCF2] transition-colors" 
                        href="{{ route('register') }}">
                        Need an account? <span class="text-[#56CCF2]">Register</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
