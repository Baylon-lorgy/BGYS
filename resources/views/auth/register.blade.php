<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
                    <x-text-input id="name" 
                        class="block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                        type="text" 
                        name="name" 
                        placeholder="Name"
                        :value="old('name')" 
                        required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
                <div>
                    <x-text-input id="email" 
                        class="block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                        type="email" 
                        name="email" 
                        placeholder="Email"
                        :value="old('email')" 
                        required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
                <div>
                    <x-text-input id="password" 
                        class="block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            type="password"
                            name="password"
                        placeholder="Password"
                        required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
                <div>
                    <x-text-input id="password_confirmation" 
                        class="block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            type="password"
                        name="password_confirmation"
                        placeholder="Confirm Password"
                        required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Register
                    </button>
                </div>

        <div class="flex items-center justify-end mt-4">
                    <a class="text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                        Already registered?
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
