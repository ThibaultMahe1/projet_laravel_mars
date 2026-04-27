<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Bienvenue sur Mars ! Ceci est votre première connexion. Vous devez changer le mot de passe temporaire fourni par l\'Administrateur avant de continuer.') }}
    </div>

    <form method="POST" action="{{ route('password.change.update') }}">
        @csrf

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="{{ __('Nouveau mot de passe') }}" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="{{ __('Confirmer le mot de passe') }}" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Changer le mot de passe') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
