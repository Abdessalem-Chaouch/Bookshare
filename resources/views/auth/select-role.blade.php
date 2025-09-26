<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Choisissez votre rôle pour continuer') }}
    </div>

    <form method="POST" action="{{ route('facebook.role') }}">
        @csrf

        <div class="mt-4">
            <x-input-label for="role" :value="__('Rôle')" />
            <select id="role" name="role" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Sélectionnez un rôle</option>
                <option value="visiteur">Visiteur</option>
                <option value="auteur">Auteur</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Continuer') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>