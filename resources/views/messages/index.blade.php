<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Centre de Communication Inter-Dômes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <style>
                .mars-container {
                    background: url('https://images.unsplash.com/photo-1614730321146-b6fa6a46bcb4?q=80&w=2000&auto=format&fit=crop') no-repeat center center;
                    background-size: cover;
                    border-radius: 20px;
                    padding: 20px;
                    min-height: 70vh;
                    max-height: 80vh;
                    display: flex;
                    flex-direction: column;
                    position: relative;
                    overflow: hidden;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                }

                .mars-glass {
                    background: rgba(15, 10, 10, 0.65);
                    backdrop-filter: blur(16px);
                    -webkit-backdrop-filter: blur(16px);
                    border: 1px solid rgba(255, 100, 50, 0.15);
                    border-radius: 15px;
                }

                .chat-area {
                    flex-grow: 1;
                    overflow-y: auto;
                    padding: 20px;
                    display: flex;
                    flex-direction: column;
                    gap: 15px;
                }

                .message-box {
                    max-width: 75%;
                    padding: 15px 20px;
                    border-radius: 12px;
                    position: relative;
                    color: #eee;
                    font-family: 'Inter', sans-serif;
                }

                .msg-incoming {
                    align-self: flex-start;
                    background: rgba(30, 40, 50, 0.7);
                    border-left: 4px solid #4fc3f7;
                    border-bottom-left-radius: 0;
                }

                .msg-outgoing {
                    align-self: flex-end;
                    background: rgba(200, 70, 30, 0.7);
                    border-right: 4px solid #ff7043;
                    border-bottom-right-radius: 0;
                }

                .msg-header {
                    display: flex;
                    justify-content: space-between;
                    font-size: 0.75rem;
                    color: rgba(255, 255, 255, 0.6);
                    margin-bottom: 8px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }

                .sender-name {
                    font-weight: bold;
                    color: #fff;
                }

                .msg-incoming .sender-name {
                    color: #4fc3f7;
                }

                .msg-outgoing .sender-name {
                    color: #ffab91;
                }

                .compose-area {
                    margin-top: 20px;
                    padding: 20px;
                    display: flex;
                    gap: 15px;
                    align-items: center;
                }

                .dome-select {
                    background: rgba(0, 0, 0, 0.5);
                    color: #fff;
                    border: 1px solid rgba(255, 100, 50, 0.3);
                    padding: 12px;
                    border-radius: 8px;
                    outline: none;
                }

                .dome-select option {
                    background: #111;
                }

                .chat-input {
                    flex-grow: 1;
                    background: rgba(0, 0, 0, 0.5);
                    color: #fff;
                    border: 1px solid rgba(255, 100, 50, 0.3);
                    padding: 15px;
                    border-radius: 8px;
                    outline: none;
                    transition: all 0.3s ease;
                }

                .chat-input:focus {
                    border-color: #ff7043;
                    box-shadow: 0 0 15px rgba(255, 112, 67, 0.2);
                }

                .send-btn {
                    background: linear-gradient(135deg, #ff7043 0%, #d84315 100%);
                    color: white;
                    border: none;
                    padding: 15px 30px;
                    border-radius: 8px;
                    font-weight: bold;
                    cursor: pointer;
                    transition: transform 0.2s, box-shadow 0.2s;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }

                .send-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 10px 20px rgba(216, 67, 21, 0.4);
                }

                /* Scrollbar */
                ::-webkit-scrollbar {
                    width: 8px;
                }

                ::-webkit-scrollbar-track {
                    background: rgba(0, 0, 0, 0.2);
                    border-radius: 10px;
                }

                ::-webkit-scrollbar-thumb {
                    background: rgba(255, 112, 67, 0.5);
                    border-radius: 10px;
                }

                ::-webkit-scrollbar-thumb:hover {
                    background: rgba(255, 112, 67, 0.8);
                }

                .typing-indicator {
                    color: rgba(255, 255, 255, 0.6);
                    font-size: 0.8rem;
                    font-style: italic;
                    margin-top: 10px;
                    display: none;
                    animation: pulse 1.5s infinite;
                }

                @keyframes pulse {
                    0% {
                        opacity: 0.5;
                    }

                    50% {
                        opacity: 1;
                    }

                    100% {
                        opacity: 0.5;
                    }
                }
            </style>

            <div class="mars-container">
                <div
                    style="position:absolute; top:20px; left:30px; z-index:10; color:white; opacity:0.5; font-family:monospace; font-size:12px; letter-spacing:2px;">
                    SYSTEME COM // DOME {{ $userDome ?? '?' }}
                </div>
                <a href="{{ route('messages.archives') }}"
                    style="position:absolute; top:20px; right:30px; z-index:10; color:#4fc3f7; opacity:0.7; font-family:monospace; font-size:12px; letter-spacing:1px; text-decoration:none; transition: opacity 0.2s;"
                    onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                    ARCHIVES JSON
                </a>

                <div class="mars-glass chat-area" id="chatContainer">
                    @forelse($messages as $msg)
                        @php
                            $isOutgoing = $msg->sender_id === Auth::id();
                        @endphp
                        <div class="message-box {{ $isOutgoing ? 'msg-outgoing' : 'msg-incoming' }}">
                            <div class="msg-header">
                                <span style="display: flex; gap: 8px;">
                                    <span class="sender-name">{{ $msg->sender->name }}</span>
                                    @if(!$isOutgoing)
                                        <span style="opacity:0.75">({{ $msg->sender->detail->dome ?? 'Inconnu' }} &rarr;
                                            {{ $userDome }})</span>
                                    @else
                                        <span style="opacity:0.75">(&rarr; Dôme {{ $msg->target_dome }})</span>
                                    @endif
                                </span>
                                <span>{{ $msg->created_at->format('H:i:s') }}</span>
                            </div>
                            <div class="msg-body">
                                {{ $msg->content }}
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center; color:white; opacity:0.5; margin-top:40px; font-family:monospace;">
                            Aucune transmission détectée pour le moment.
                        </div>
                    @endforelse

                    <div id="typingIndicator" class="typing-indicator">
                        <span id="typingUserName">Quelqu'un</span> est en train d'écrire...
                    </div>
                </div>

                <div class="mars-glass compose-area">
                    <form action="{{ route('messages.store') }}" method="POST"
                        style="width:100%; display:flex; gap:16px;">
                        @csrf
                        <select name="target_dome" class="dome-select" required>
                            <option value="">Destinataire...</option>
                            <option value="A" {{ $userDome == 'B' ? 'selected' : '' }}>Dôme A</option>
                            <option value="B" {{ $userDome == 'A' ? 'selected' : '' }}>Dôme B</option>
                        </select>

                        <input type="text" name="content" class="chat-input"
                            placeholder="Entrez votre message à transmettre..." required autocomplete="off">

                        <button type="submit" class="send-btn">Transmettre</button>
                    </form>
                </div>
            </div>

            <script>
                const chatContainer = document.getElementById('chatContainer');
                const typingIndicator = document.getElementById('typingIndicator');
                const typingUserName = document.getElementById('typingUserName');
                const chatInput = document.querySelector('.chat-input');
                const domeSelect = document.querySelector('.dome-select');
                const form = document.querySelector('form');

                let lastMessageId = {{ $messages->last()->id ?? 0 }};
                let authUserId = {{ Auth::id() }};
                let userDome = '{{ $userDome }}';

                // Scroll to bottom initially
                chatContainer.scrollTop = chatContainer.scrollHeight;

                // Handle Typing Event
                chatInput.addEventListener('input', () => {
                    const targetDome = domeSelect.value;
                    if (targetDome.trim() !== '') {
                        fetch('{{ route("messages.typing") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ target_dome: targetDome })
                        });
                    }
                });

                // Handle Form Submit via AJAX
                form.addEventListener('submit', (e) => {
                    e.preventDefault();

                    const content = chatInput.value;
                    const targetDome = domeSelect.value;

                    fetch('{{ route("messages.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ target_dome: targetDome, content: content })
                    }).then(res => res.json()).then(data => {
                        if (data.status === 'success') {
                            chatInput.value = '';
                            fetchMessages(); // Immediately fetch to show the message
                        }
                    });
                });

                // Polling for new messages and typing status
                function fetchMessages() {
                    fetch(`{{ route('messages.fetch') }}?last_id=${lastMessageId}`)
                        .then(res => res.json())
                        .then(data => {
                            // Handle typing status
                            if (data.typing) {
                                typingUserName.textContent = data.typing;
                                typingIndicator.style.display = 'block';
                            } else {
                                typingIndicator.style.display = 'none';
                            }

                            // Handle new messages
                            if (data.messages && data.messages.length > 0) {
                                data.messages.forEach(msg => {
                                    const isOutgoing = msg.sender_id === authUserId;
                                    let destinationText = '';

                                    if (!isOutgoing) {
                                        destinationText = `(${msg.sender_dome} &rarr; ${userDome})`;
                                    } else {
                                        destinationText = `(&rarr; Dôme ${msg.target_dome})`;
                                    }

                                    const msgHtml = `
                                        <div class="message-box ${isOutgoing ? 'msg-outgoing' : 'msg-incoming'}">
                                            <div class="msg-header">
                                                <span style="display: flex; gap: 8px;">
                                                    <span class="sender-name">${msg.sender_name}</span> 
                                                    <span style="opacity:0.75">${destinationText}</span>
                                                </span>
                                                <span>${msg.created_at}</span>
                                            </div>
                                            <div class="msg-body">
                                                ${msg.content}
                                            </div>
                                        </div>
                                    `;

                                    // Insert before the typing indicator
                                    typingIndicator.insertAdjacentHTML('beforebegin', msgHtml);
                                    lastMessageId = msg.id;
                                });
                                chatContainer.scrollTop = chatContainer.scrollHeight;
                            }
                        });
                }

                // Poll every 2 seconds
                setInterval(fetchMessages, 2000);
            </script>

        </div>
    </div>
</x-app-layout>