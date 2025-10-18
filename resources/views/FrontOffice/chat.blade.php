<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot de Réclamations</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; background: #f8f8f8; }
        .chat-container { max-width: 600px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .messages { height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; }
        .message { margin-bottom: 10px; }
        .user { color: blue; }
        .bot { color: green; }
        input[type=text] { width: 80%; padding: 10px; border-radius: 4px; border: 1px solid #ccc; }
        button { padding: 10px 15px; border: none; background: #9c9259; color: white; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="chat-container">
        <h2>Chatbot de Réclamations</h2>
        <div class="messages" id="messages"></div>
        <input type="text" id="userMessage" placeholder="Tapez votre message...">
        <button id="sendBtn">Envoyer</button>
    </div>

    <script>
        const sendBtn = document.getElementById('sendBtn');
        const userMessageInput = document.getElementById('userMessage');
        const messagesDiv = document.getElementById('messages');

        sendBtn.addEventListener('click', async () => {
            const message = userMessageInput.value.trim();
            if(!message) return;

            // Affiche le message utilisateur
            const userDiv = document.createElement('div');
            userDiv.classList.add('message', 'user');
            userDiv.textContent = "Vous : " + message;
            messagesDiv.appendChild(userDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;

            // Envoie la requête au backend Laravel
            const response = await fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();
            const botDiv = document.createElement('div');
            botDiv.classList.add('message', 'bot');
            botDiv.textContent = "Bot : " + (data.response || "Je n'ai pas compris.");
            messagesDiv.appendChild(botDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;

            userMessageInput.value = '';
            userMessageInput.focus();
        });

        // Permet d'envoyer avec la touche Enter
        userMessageInput.addEventListener('keydown', (e) => {
            if(e.key === 'Enter') sendBtn.click();
        });
    </script>
</body>
</html>
