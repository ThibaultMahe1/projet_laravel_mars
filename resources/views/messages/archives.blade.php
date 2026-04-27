<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Archives de Communication') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <style>
                .archive-container {
                    background: linear-gradient(135deg, #0a0a0f 0%, #1a1020 100%);
                    border-radius: 20px;
                    padding: 30px;
                    min-height: 60vh;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                }

                .archive-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 30px;
                }

                .archive-title {
                    color: #fff;
                    font-family: 'Inter', monospace;
                    font-size: 1.2rem;
                    letter-spacing: 2px;
                    text-transform: uppercase;
                    opacity: 0.6;
                }

                .force-btn {
                    background: linear-gradient(135deg, #4fc3f7 0%, #0288d1 100%);
                    color: white;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 8px;
                    font-weight: bold;
                    cursor: pointer;
                    transition: transform 0.2s, box-shadow 0.2s;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    font-size: 0.8rem;
                }

                .force-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 10px 20px rgba(79, 195, 247, 0.3);
                }

                .back-link {
                    color: #ff7043;
                    text-decoration: none;
                    font-size: 0.9rem;
                    transition: opacity 0.2s;
                }

                .back-link:hover {
                    opacity: 0.7;
                }

                .archive-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                    gap: 20px;
                }

                .archive-card {
                    background: rgba(255, 255, 255, 0.05);
                    backdrop-filter: blur(10px);
                    border: 1px solid rgba(255, 100, 50, 0.15);
                    border-radius: 15px;
                    padding: 25px;
                    transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
                    cursor: pointer;
                    text-decoration: none;
                    display: block;
                }

                .archive-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 15px 30px rgba(255, 112, 67, 0.15);
                    border-color: rgba(255, 100, 50, 0.4);
                }

                .archive-card-title {
                    color: #ff7043;
                    font-family: monospace;
                    font-size: 1rem;
                    font-weight: bold;
                    margin-bottom: 12px;
                    letter-spacing: 1px;
                }

                .archive-card-meta {
                    color: rgba(255, 255, 255, 0.5);
                    font-size: 0.8rem;
                    line-height: 1.8;
                }

                .archive-card-meta span {
                    color: rgba(255, 255, 255, 0.8);
                }

                .empty-state {
                    text-align: center;
                    color: rgba(255, 255, 255, 0.4);
                    font-family: monospace;
                    padding: 60px 20px;
                    font-size: 1rem;
                }

                .status-banner {
                    background: rgba(79, 195, 247, 0.15);
                    border: 1px solid rgba(79, 195, 247, 0.3);
                    color: #4fc3f7;
                    padding: 12px 20px;
                    border-radius: 10px;
                    margin-bottom: 20px;
                    font-size: 0.9rem;
                }
            </style>

            <div class="archive-container">
                <div class="archive-header">
                    <div>
                        <div class="archive-title"> Fichiers d'Archive JSON</div>
                        <a href="{{ route('messages.index') }}" class="back-link">&larr; Retour aux Communications</a>
                    </div>
                    <form action="{{ route('messages.archive.force') }}" method="POST">
                        @csrf
                        <button type="submit" class="force-btn"> Archiver maintenant</button>
                    </form>
                </div>

                @if(session('status'))
                    <div class="status-banner"> {{ session('status') }}</div>
                @endif

                @if(count($archives) > 0)
                    <div class="archive-grid">
                        @foreach($archives as $archive)
                            <a href="{{ route('messages.archive.show', $archive['filename']) }}" class="archive-card">
                                <div class="archive-card-title">📄 {{ $archive['filename'] }}</div>
                                <div class="archive-card-meta">
                                    Archive #<span>{{ $archive['archive_id'] }}</span><br>
                                    Messages : <span>{{ $archive['message_count'] }}</span><br>
                                    Plage : <span>{{ $archive['message_range'] }}</span><br>
                                    Taille : <span>{{ $archive['size'] }}</span><br>
                                    Créé le : <span>{{ $archive['created_at'] ?? 'N/A' }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        Aucune archive trouvée.<br>
                        Les archives sont automatiquement créées tous les 250 messages.<br>
                        Vous pouvez aussi en créer une manuellement avec le bouton ci-dessus.
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>