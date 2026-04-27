<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nouvelle intégration dans la Colonie') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Formulaire Bootstrap -->
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <!-- Name -->
                    <div class="mt-4">
                        <x-input-label for="name" value="{{ __('Nom complet') }}" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div class="mt-4">
                        <x-input-label for="email" value="{{ __('Email de transmission') }}" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    
                    <div class="mt-4 text-sm text-gray-600">
                        <p>Note : Le mot de passe temporaire sera généré automatiquement et affiché à la page suivante.</p>
                    </div>

                    <div class="flex items-center mt-4">
                        <x-primary-button class="ml-4">
                            {{ __('Autoriser l\'intégration') }}
                        </x-primary-button>
                        <a class="ml-4 underline text-sm text-gray-600 hover:text-gray-800" href="{{ route('users.index') }}">
                            {{ __('Annuler') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
