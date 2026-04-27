<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Colons (Dashboard Administrateur)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
            @endif

            @if(session('temp_password_value'))
            <div x-data="{ copied: false, password: '{{ session('temp_password_value') }}' }" class="p-6 mb-6 border-l-4 border-mars-500 bg-mars-50 rounded-r-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-mars-800">Code d'accès temporaire généré</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Veuillez transmettre ce code à <strong>{{ session('temp_password_user') }}</strong>. Il ne sera affiché qu'une seule fois.
                        </p>
                        <div class="mt-4 flex items-center">
                            <code class="px-4 py-2 bg-white text-gray-800 rounded border border-gray-200 font-mono text-lg tracking-wider">{{ session('temp_password_value') }}</code>
                            
                            <button @click="navigator.clipboard.writeText(password); copied = true; setTimeout(() => copied = false, 2000)" 
                                    class="ml-4 inline-flex items-center px-4 py-2 bg-mars-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-mars-500 focus:bg-mars-500 active:bg-mars-900 focus:outline-none focus:ring-2 focus:ring-mars-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <span x-show="!copied">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    Copier
                                </span>
                                <span x-show="copied" class="text-green-300" style="display: none;">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Copié !
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="flex justify-end mb-4">
                <a href="{{ route('users.create') }}" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Nouveau Colon
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-800 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Etat Mdp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($user->needs_password_change)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Doit changer (Temporaire)</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Définitif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form action="{{ route('users.reset_password', $user->id) }}" method="POST" onsubmit="return confirm('Générer un nouveau mot de passe temporaire pour ce colon ?');">
                                        @csrf
                                        <button type="submit" class="text-mars-500 hover:text-mars-700">Réinitialiser le mot de passe</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
