    // ======== A.I Narrator Button ========
    export function initAINarrator(allPagesText, pageFlip) {
        const btnRead = document.getElementById('readPageBtn');
        const audioPlayer = document.getElementById('narratorPlayer');
        if (!btnRead || !audioPlayer) return;

        btnRead.addEventListener('click', async () => {
            const currentPage = pageFlip.getCurrentPageIndex();
            const text = allPagesText[currentPage];
            if (!text) return alert('Page vide');

            try {
                const res = await fetch('http://127.0.0.1:8000/speak', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ text })
                });

                if (!res.ok) throw new Error('Server error');

                const blob = await res.blob();
                audioPlayer.src = URL.createObjectURL(blob);
                audioPlayer.style.display = 'block';
                audioPlayer.play();
            } catch (e) {
                console.error('AI Narrator error:', e);
                alert('❌ Impossible de lire cette page.');
            }
        });
    }

    // ======== Voice Commands ========
    export function initVoiceCommands(pageFlip, allPagesText) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) return alert("⚠️ SpeechRecognition non supporté");

        const recognition = new SpeechRecognition();
        recognition.lang = 'fr-FR';
        recognition.continuous = true;
        recognition.interimResults = false;

        const audioPlayer = document.getElementById('narratorPlayer');

        // Commandes vocales
        const nextAliases = ['page suivante', 'suivante'];
        const prevAliases = ['page précédente', 'précédente'];
        const readAliases = ['lire page', 'lire cette page', 'lecture'];
        const pauseAliases = ['pause lecture', 'pause'];
        const stopAliases = ['stop lecture', 'arrêter', 'stop'];

        // Fonction pour lire la page actuelle
        async function readCurrentPage() {
            const currentPage = pageFlip.getCurrentPageIndex();
            const text = allPagesText[currentPage];
            if (!text) return alert('Aucun texte trouvé');

            try {
                console.log("🎙️ Envoi du texte à Flask...");

                const response = await fetch('http://127.0.0.1:5000/speak', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ text, lang: 'fr' })
                });

                if (!response.ok) throw new Error(`Flask error: ${response.status}`);

                const blob = await response.blob();
                const audioUrl = URL.createObjectURL(blob);

                audioPlayer.src = audioUrl;
                audioPlayer.style.display = 'block';
                audioPlayer.play();

                console.log("✅ Lecture audio commencée !");
            } catch (err) {
                console.error('AI Narrator error:', err);
                alert('❌ Erreur lors de la lecture : ' + err.message);
            }
        }

        // Résultat reconnaissance vocale
        recognition.onresult = (event) => {
            const last = event.results[event.results.length - 1][0].transcript.trim().toLowerCase();
            console.log('[Voice Command]', last);

            if (nextAliases.some(a => last.includes(a))) pageFlip.flipNext();
            else if (prevAliases.some(a => last.includes(a))) pageFlip.flipPrev();
            else if (readAliases.some(a => last.includes(a))) readCurrentPage();
            else if (pauseAliases.some(a => last.includes(a))) audioPlayer.pause();
            else if (stopAliases.some(a => last.includes(a))) {
                audioPlayer.pause();
                audioPlayer.currentTime = 0;
            }
        };

        recognition.onerror = (e) => console.error('Voice command error:', e);
        recognition.onend = () => recognition.start(); // auto-restart
        recognition.start();

        // Contrôles via boutons si besoin (ajoute ces boutons dans ton HTML)
        const readBtn = document.getElementById('readPageBtn');
        const pauseBtn = document.getElementById('pauseBtn');
        const stopBtn = document.getElementById('stopBtn');

        if (readBtn) readBtn.addEventListener('click', readCurrentPage);
        if (pauseBtn) pauseBtn.addEventListener('click', () => audioPlayer.pause());
        if (stopBtn) stopBtn.addEventListener('click', () => {
            audioPlayer.pause();
            audioPlayer.currentTime = 0;
        });
    }

// aiSummary.js

export async function summarizeCurrentPage(pageFlip, allPagesText, container) {
    const currentPage = pageFlip.getCurrentPageIndex();
    const text = allPagesText[currentPage];
    if (!text) {
        container.innerHTML = `<p>No text found for this page.</p>`;
        return;
    }

    try {
        const response = await fetch('http://127.0.0.1:5000/summarize', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text })
        });

        if (!response.ok) throw new Error(`Flask error: ${response.status}`);
        const data = await response.json();

        container.innerHTML = `
            <div class="message ai">
                <h4>Summary of Page ${currentPage + 1}</h4>
                <p>${data.summary}</p>
            </div>`;
    } catch (err) {
        container.innerHTML = `<p style="color:red;">❌ Error: ${err.message}</p>`;
    }
}

export async function summarizeWholeBook(allPagesText, container) {
    if (!allPagesText || allPagesText.length === 0) {
        container.innerHTML = `<p>No text found for the full summary.</p>`;
        return;
    }

    try {
        const fullText = allPagesText.join(' ');
        const response = await fetch('http://127.0.0.1:5000/summarize', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text: fullText })
        });

        if (!response.ok) throw new Error(`Flask error: ${response.status}`);
        const data = await response.json();

        container.innerHTML = `
            <div class="message ai">
                <h4>📚 Full Book Summary</h4>
                <p>${data.summary}</p>
            </div>`;
    } catch (err) {
        container.innerHTML = `<p style="color:red;">❌ Error: ${err.message}</p>`;
    }
}
