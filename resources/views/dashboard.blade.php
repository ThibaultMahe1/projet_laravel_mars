<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Moniteur des Biocapteurs - Base Martienne') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="sensorData()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Affichage administrateur : sélection de la cible -->
            @can('manage-users')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-mars-800 mb-4">Aperçu Général Administratif</h3>
                <div class="flex items-center space-x-4">
                    <label for="user-select" class="text-sm font-medium text-gray-700">Sélectionnez le colon à monitorer :</label>
                    <select id="user-select" x-model="selectedUser" class="mt-1 block max-w-md rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-mars-500 focus:outline-none focus:ring-mars-500 sm:text-sm">
                        <option value="Me">Vous même ({{ Auth::user()->name }})</option>
                        @foreach (\App\Models\User::all() as $u)
                            @if($u->id !== Auth::id())
                                <option value="{{ $u->name }}">Colon : {{ $u->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            @endcan

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 relative">
                
                <!-- Overlay de synchronisation -->
                <div x-cloak x-show="syncing" x-transition.opacity.duration.300ms class="absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex flex-col items-center justify-center rounded-lg">
                    <svg class="animate-spin h-12 w-12 text-mars-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-bold text-mars-900 tracking-widest uppercase">Établissement liaison biostatique...</span>
                </div>
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">
                            Télémétrie en temps réel 
                            @can('manage-users')
                            <span class="text-mars-600" x-text="selectedUser === 'Me' ? '(Vous)' : '(' + selectedUser + ')'"></span>
                            @endcan
                        </h3>
                        <p class="text-sm text-gray-500 mt-1 flex items-center">
                            <span class="relative flex h-3 w-3 mr-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            Liaison biocapteurs stabilisée (Latence: <span x-text="ping"></span> ms)
                        </p>
                    </div>
                </div>

                <!-- Grille des statistiques -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 transition-all duration-300" :class="{'opacity-25 blur-sm': syncing}">
                    
                    <!-- Colonne 3D (Projection holographique) -->
                    <div class="lg:col-span-2 flex flex-col items-center justify-center bg-mars-950 border border-mars-800 rounded-xl p-4 shadow-[0_0_30px_rgba(253,77,12,0.15)] relative overflow-hidden h-96 lg:h-[600px] order-last lg:order-first">
                        <div class="text-xs text-mars-400 uppercase tracking-widest font-mono shrink-0 mb-2 w-full text-center z-10 absolute top-4">Scan Biométrique 3D</div>
                        
                        <!-- Récipient principal pour le canvas ThreeJS -->
                        <div id="hologram-container" class="w-full h-full relative z-10" x-init="
                            setTimeout(() => { 
                                if(window.initHologram) { window.initHologram('hologram-container', '/models/votre-modele-humain.glb') } 
                            }, 500)
                        ">
                        </div>

                        <!-- Éclairage du sol artificiel HTML / Tailwind pour appuyer l'immersion -->
                        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 w-3/4 h-10 bg-mars-500/20 blur-xl rounded-full z-0 pointer-events-none"></div>
                        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 w-1/3 h-3 bg-mars-400/40 blur-md rounded-[100%] z-0 pointer-events-none"></div>
                    </div>

                    <div class="lg:col-span-1 grid grid-cols-1 gap-6">
                        <!-- Température corporelle -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-mars-50 to-white border border-mars-100 rounded-xl p-6 shadow-sm transition-all duration-500"
                         :class="{'ring-2 ring-red-500 border-red-500': tempWarning}">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-medium text-mars-600 uppercase tracking-wider">Température</div>
                            <svg class="h-6 w-6 text-mars-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="mt-4 flex items-baseline text-4xl font-extrabold text-gray-900 transition-all duration-300" x-text="temp.toFixed(1)">
                            37.2
                        </div>
                        <div class="text-sm text-gray-500">°C (Cible: 37.0)</div>
                    </div>

                    <!-- Rythme cardiaque -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-mars-50 to-white border border-mars-100 rounded-xl p-6 shadow-sm transition-all duration-500"
                         :class="{'ring-2 ring-red-500 border-red-500': bpmWarning}">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-medium text-mars-600 uppercase tracking-wider">Rythme Cardiaque</div>
                            <svg class="h-6 w-6 text-mars-500" :class="{'animate-pulse text-red-500': true}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <div class="mt-4 flex items-baseline text-4xl font-extrabold text-gray-900 transition-all duration-300" x-text="bpm">
                            75
                        </div>
                        <div class="text-sm text-gray-500 text-left w-full">BPM</div>
                    </div>

                    <!-- Saturation O2 -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-mars-50 to-white border border-mars-100 rounded-xl p-6 shadow-sm transition-all duration-500"
                         :class="{'ring-2 ring-red-500 border-red-500': o2Warning}">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-medium text-mars-600 uppercase tracking-wider">O2 Sanguin</div>
                            <svg class="h-6 w-6 text-mars-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                        <div class="mt-4 flex items-baseline text-4xl font-extrabold text-gray-900 transition-all duration-300">
                            <span x-text="o2"></span><span class="text-2xl ml-1">%</span>
                        </div>
                        <div class="text-sm text-gray-500">SpO2</div>
                    </div>
                </div>
                </div>
                
                <div class="mt-8 text-center text-xs text-gray-400">
                    Système de santé vital MarsCorp - <em>Synchronisation en cours</em>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Alpine de génération de données temps réel fictives -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sensorData', () => ({
                selectedUser: 'Me',
                temp: 37.0,
                bpm: 72,
                o2: 98,
                ping: 14,
                syncing: false,
                
                get tempWarning() { return this.temp > 37.8 || this.temp < 36.1; },
                get bpmWarning() { return this.bpm > 100 || this.bpm < 50; },
                get o2Warning() { return this.o2 < 95; },

                init() {
                    // Capter le changement d'utilisateur
                    this.$watch('selectedUser', (value) => {
                        this.forceResync();
                    });

                    // Boucle de variation aléatoire simulant la vie réelle
                    setInterval(() => {
                        if(this.syncing) return; // Ne pas modifier pendant la charge

                        // Changement de la température (-0.1 à +0.1)
                        if(Math.random() > 0.5) {
                            let mod = (Math.random() * 0.2 - 0.1);
                            this.temp = Math.max(36.5, Math.min(38.0, this.temp + mod));
                        }

                        // Changement des BPM (-3 à +4)
                        let bpmVar = Math.floor(Math.random() * 8) - 3;
                        this.bpm = Math.max(60, Math.min(120, this.bpm + bpmVar));

                        // Changement O2 (-1 à +1, tend vers 98-99)
                        if(Math.random() > 0.7) {
                            let oxVar = Math.floor(Math.random() * 3) - 1;
                            this.o2 = Math.max(93, Math.min(100, this.o2 + oxVar));
                        }

                        // Ping varié
                        this.ping = Math.floor(Math.random() * 20) + 12;

                    }, 2000); // Mise à jour toutes les 2 secondes
                },

                forceResync() {
                    this.syncing = true;
                    // Simule le temps de basculement radio martien (0.5s à 1.5s)
                    let lag = Math.floor(Math.random() * 1000) + 500;
                    
                    setTimeout(() => {
                        this.syncing = false;
                        
                        // Recalculer complètement la base des statistiques pour le nouvel utilisateur ciblé
                        this.temp = 36.5 + (Math.random() * 1.5); // Entre 36.5 et 38.0
                        this.bpm = Math.floor(Math.random() * (110 - 60 + 1)) + 60; // Entre 60 et 110 bpm
                        this.o2 = Math.floor(Math.random() * (100 - 94 + 1)) + 94; // Entre 94 et 100%
                        this.ping = Math.floor(Math.random() * 60) + 30; // Un ping un peu plus haut lors de la recherche initiale

                    }, lag);
                }
            }))
        })
    </script>
</x-app-layout>
