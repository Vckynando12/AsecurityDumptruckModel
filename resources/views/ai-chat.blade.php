<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat - SMARTCAB</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <div class="bg-white shadow-md p-4">
            <h1 class="text-2xl font-bold text-center">AI Chat</h1>
        </div>

        <!-- Chat Container -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chatContainer">
            <!-- Chat messages will be appended here -->
        </div>

        <!-- Input Area -->
        <div class="bg-white p-4 shadow-md">
            <div class="flex space-x-2">
                <input type="text" id="messageInput" 
                       class="flex-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Ketik pesan Anda...">
                <button id="sendButton" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Kirim
                </button>
            </div>
        </div>
    </div>

    <script>
        const chatContainer = document.getElementById('chatContainer');
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');

        function appendMessage(role, content) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('flex', 'items-start', 'space-x-2');
            
            if (role === 'user') {
                messageDiv.classList.add('justify-end');
                messageDiv.innerHTML = `
                    <div class="bg-blue-100 p-3 rounded-lg max-w-[80%]">
                        <p class="text-gray-800">${content}</p>
                    </div>
                `;
            } else {
                messageDiv.classList.add('justify-start');
                messageDiv.innerHTML = `
                    <div class="bg-gray-200 p-3 rounded-lg max-w-[80%]">
                        <p class="text-gray-800">${content}</p>
                    </div>
                `;
            }
            
            chatContainer.appendChild(messageDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        async function sendMessage() {
            const message = messageInput.value.trim();
            if (!message) return;

            appendMessage('user', message);
            messageInput.value = '';
            sendButton.disabled = true;

            try {
                const response = await fetch('/ai-chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    appendMessage('assistant', data.message);
                    console.log('Chat ID:', data.chatId);
                    console.log('Model:', data.model);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                sendButton.disabled = false;
            }
        }

        // Event Listeners
        sendButton.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
</body>
</html> 