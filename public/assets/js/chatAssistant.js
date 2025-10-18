// ================== Chat Assistant ==================
/*export function initChatAssistant(pageFlip, allPagesText, currentLang) {
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');
    const typingIndicator = document.getElementById('typingIndicator');
    const sendBtn = document.querySelector('.send-btn');
    const chatSidebar = document.getElementById('chatSidebar');

    function addMessage(sender, text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        messageDiv.textContent = text;
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function showTyping() { typingIndicator.style.display = 'block'; }
    function hideTyping() { typingIndicator.style.display = 'none'; }

      function getRelevantContext(question, allPagesText) {
        const chunks = allPagesText.map((text, i) => ({ text, index: i }));
        const words = question.toLowerCase().split(/\s+/);
        // garder les chunks contenant au moins un mot de la question
        const relevantChunks = chunks.filter(c =>
            words.some(w => c.text.toLowerCase().includes(w))
        ).slice(0, 5); // max 5 chunks
        return relevantChunks.map(c => c.text).join(' ').substring(0, 1500);
    }

    async function sendChatMessage() {
        const question = chatInput.value.trim();
        if (!question) return;

        addMessage('user', question);
        chatInput.value = '';
        showTyping();

        // récupérer contexte pertinent
        const context = getRelevantContext(question, allPagesText);

        try {
            const response = await fetch("http://127.0.0.1:5000⁠    /ask", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    question,
                    context,
                    lang: currentLang.startsWith("fr") ? "fr" : "en"
                })
            });

            if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
            const data = await response.json();

            const answer = data?.answer || (currentLang.startsWith("fr") ? "Je ne sais pas." : "I don't know.");
            hideTyping();
            addMessage('ai', answer);

        } catch (error) {
            hideTyping();
            console.error("Erreur fetch :", error);
            addMessage('ai', "❌ Erreur de connexion avec le serveur.");
        }
    }

    // ====== Événements ======
    chatInput.addEventListener("keypress", e => {
        if (e.key === "Enter") sendChatMessage();
    });

    sendBtn?.addEventListener("click", sendChatMessage);

    // ====== Toggle Sidebar ======
    window.toggleChat = () => {
        chatSidebar.classList.add("open");
    };

    window.closeChat = () => {
        chatSidebar.classList.remove("open");
    };
}
*/
// Envoyer une question et afficher la réponse
export async function sendQuestion(allPagesText, question, container) {
    const fullText = allPagesText.join(" "); // tout le livre

    const userMsg = document.createElement('div');
    userMsg.className = 'message user';
    userMsg.innerHTML = `<strong>Vous:</strong> ${question}`;
    container.appendChild(userMsg);

    const aiMsg = document.createElement('div');
    aiMsg.className = 'message ai';
    aiMsg.innerHTML = `<em>⏳ Thinking...</em>`;
    container.appendChild(aiMsg);
    container.scrollTop = container.scrollHeight;

    try {
        const response = await fetch('http://flask:5000/ask', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text: fullText, question })
        });

        const data = await response.json();
        if (!response.ok) throw new Error(data.error || `Error ${response.status}`);

        aiMsg.innerHTML = `
            <strong>🤖 Answer:</strong> ${data.answer}
            <details><summary>📘 Contexts utilisés</summary>
                <ul>${data.contexts.map(c => `<li>${c}</li>`).join('')}</ul>
            </details>`;
        container.scrollTop = container.scrollHeight;
    } catch (err) {
        aiMsg.innerHTML = `<span style="color:red;">❌ ${err.message}</span>`;
        container.scrollTop = container.scrollHeight;
    }
}

// Initialisation UI
export function initChatUI(inputId, buttonId, messagesContainerId, allPagesText) {
    const input = document.getElementById(inputId);
    const sendBtn = document.getElementById(buttonId);
    const container = document.getElementById(messagesContainerId);

    sendBtn.addEventListener('click', () => {
        const question = input.value.trim();
        if (!question) return;
        sendQuestion(allPagesText, question, container);
        input.value = '';
    });

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendBtn.click();
    });
}



export async function askBookQuestion(question, chunks, embeddings, container) {
    const response = await fetch('http://127.0.0.1:5000/ask', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({question, chunks, embeddings})
    });
    const data = await response.json();
    container.innerHTML += `<div class="message user"><p>${question}</p></div>`;
    container.innerHTML += `<div class="message ai"><p>${data.answer}</p></div>`;
}

