<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détail de l\'archive : ' . $filename) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <style>
                .detail-container {
                    background: linear-gradient(135deg, #0a0a0f 0%, #1a1020 100%);
                    border-radius: 20px;
                    padding: 30px;
                    min-height: 60vh;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                }

                .detail-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 20px;
                    padding-bottom: 20px;
                    border-bottom: 1px solid rgba(255, 100, 50, 0.15);
                }

                .detail-title {
                    color: #ff7043;
                    font-family: monospace;
                    font-size: 1.1rem;
                    letter-spacing: 1px;
                }

                .detail-meta {
                    color: rgba(255, 255, 255, 0.5);
                    font-size: 0.8rem;
                }

                .detail-meta span {
                    color: rgba(255, 255, 255, 0.8);
                }

                .back-link {
                    color: #4fc3f7;
                    text-decoration: none;
                    font-size: 0.9rem;
                    transition: opacity 0.2s;
                }

                .back-link:hover {
                    opacity: 0.7;
                }

                .json-preview {
                    background: rgba(0, 0, 0, 0.4);
                    border: 1px solid rgba(255, 100, 50, 0.1);
                    border-radius: 10px;
                    padding: 20px;
                    margin-bottom: 25px;
                    font-family: monospace;
                    font-size: 0.75rem;
                    color: #4fc3f7;
                    overflow-x: auto;
                    max-height: 200px;
                    overflow-y: auto;
                }

                .msg-list {
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                }

                .archive-msg {
                    background: rgba(255, 255, 255, 0.05);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    border-radius: 10px;
                    padding: 15px 20px;
                    color: #eee;
                    transition: border-color 0.2s;
                }

                .archive-msg:hover {
                    border-color: rgba(255, 100, 50, 0.3);
                }

                .archive-msg-header {
                    display: flex;
                    justify-content: space-between;
                    font-size: 0.75rem;
                    color: rgba(255, 255, 255, 0.5);
                    margin-bottom: 8px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }

                .archive-msg-sender {
                    color: #ff7043;
                    font-weight: bold;
                }

                .archive-msg-content {
                    font-size: 0.9rem;
                    line-height: 1.5;
                }

                .dome-badge {
                    display: inline-block;
                    background: rgba(79, 195, 247, 0.2);
                    color: #4fc3f7;
                    padding: 2px 8px;
                    border-radius: 4px;
                    font-size: 0.7rem;
                    font-weight: bold;
                }
            </style>

            <div class="detail-container">
                <div class="detail-header">
                    <div>
                        <div class="detail-title"> {{ $filename }}</div>
                        <div class="detail-meta" style="margin-top: 8px;">
                            Archive #<span>{{ $content['archive_id'] }}</span>
                            &nbsp;•&nbsp; <span>{{ $content['message_count'] }}</span> messages
                            &nbsp;•&nbsp; Créé le <span>{{ $content['created_at'] }}</span>
                        </div>
                    </div>
                    <a href="{{ route('messages.archives') }}" class="back-link">&larr; Retour aux Archives</a>
                </div>

                <details style="margin-bottom: 25px;">
                    <summary
                        style="color: rgba(255,255,255,0.4); cursor: pointer; font-size: 0.8rem; letter-spacing: 1px; text-transform: uppercase;">
                        Voir le JSON brut
                    </summary>
                    <div class="json-preview">
                        <pre>{{ json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </details>

                <div class="msg-list">
                    @foreach($content['messages'] as $msg)
                        <div class="archive-msg">
                            <div class="archive-msg-header">
                                <span>
                                    <span class="archive-msg-sender">{{ $msg['sender'] }}</span>
                                    <span class="dome-badge">Dôme {{ $msg['sender_dome'] }}</span>
                                    &rarr;
                                    <span class="dome-badge">Dôme {{ $msg['target_dome'] }}</span>
                                </span>
                                <span>{{ $msg['sent_at'] }}</span>
                            </div>
                            <div class="archive-msg-content">{{ $msg['content'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>