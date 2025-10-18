export function initAINarrator(allPagesText, pageFlip) {
    const btnRead = document.getElementById('readPageBtn');
    const audioPlayer = document.getElementById('narratorPlayer');
    if (!btnRead || !audioPlayer) return;

    // Icône dans le bouton
    const icon = btnRead.querySelector('i');

    // Listener au clic
    btnRead.addEventListener('click', async (e) => {
        e.stopPropagation(); // empêche propagation, comme ton exemple summarizeBtn
        const currentPage = pageFlip.getCurrentPageIndex();
        const text = allPagesText[currentPage];
        if (!text) return alert('Page vide');

        try {
            // Si audio déjà en train de jouer, on pause
            if (!audioPlayer.paused) {
                audioPlayer.pause();
                icon.className = 'fa-solid fa-play-circle'; // remettre icône play
                return;
            }

            // Sinon, fetch le TTS et joue
            const res = await fetch('http://127.0.0.1:5000⁠/speak', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ text })
            });

            if (!res.ok) throw new Error('Server error');

            const blob = await res.blob();
            audioPlayer.src = URL.createObjectURL(blob);
            audioPlayer.style.display = 'block';
            await audioPlayer.play();

            // Icône pause pendant lecture
            icon.className = 'fa-solid fa-pause-circle';

            // Quand l'audio se termine
            audioPlayer.onended = () => {
                icon.className = 'fa-solid fa-play-circle';
            };

        } catch (err) {
            console.error('AI Narrator error:', err);
            alert('❌ Impossible de lire cette page.');
        }
    });
}



    // ======== Voice Commands ========
export function initVoiceAssistant(pageFlip, allPagesText, currentLang) {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
        alert("⚠️ La reconnaissance vocale n'est pas supportée par votre navigateur.");
        return;
    }

    // === 🎙️ Initialisation ===
    const recognition = new SpeechRecognition();
    recognition.lang = currentLang || 'fr-FR';
    recognition.continuous = true;
    recognition.interimResults = false;

    const voiceBtn = document.getElementById('btnVoiceDetect');
    const voiceIcon = voiceBtn?.querySelector('i');
    let listening = false;
    let currentUtterance = null;

    // === 🗣️ Commandes reconnues ===
    const commands = {
        read: ['lire', 'lecture', 'commencer lecture', 'lancer lecture', 'lis la page'],
        pause: ['pause', 'pause lecture', 'mets en pause', 'arrête un peu'],
        stop: ['stop', 'arrête', 'stop lecture', 'arrête lecture'],
        next: ['page suivante', 'suivante', 'page d’après', 'suivant'],
        prev: ['page précédente', 'précédente', 'page d’avant', 'avant'],
        note: ['ajouter une note', 'mettre une note', 'note cette page', 'note', 'écrire une note'],
        fullscreen: ['plein écran', 'agrandir écran', 'maximiser', 'mettre en plein écran'],
        zoomin: ['zoomer', 'agrandir', 'zoom avant', 'agrandir écran'],
        zoomout: ['dézoomer', 'réduire', 'zoom arrière', 'rétrécir', 'réduire écran'],
        themeDark: ['mode sombre', 'thème sombre', 'activer mode sombre'],
        themeLight: ['mode clair', 'quitter le mode sombre', 'repasser en mode clair'],
        sound: ['activer son', 'désactiver son', 'couper le son', 'remettre le son', 'son']
    };

    // === 🧩 Fonctions d’actions ===
    function readCurrentPage() {
        const currentIndex = pageFlip.getCurrentPageIndex();
        const text = (allPagesText[currentIndex] || '').replace(/[^\p{L}\p{N}\s.,;:!?'-]/gu, '');
        if (!text) return alert('⚠️ Aucune lecture disponible pour cette page.');
        speechSynthesis.cancel();
        currentUtterance = new SpeechSynthesisUtterance(text);
        currentUtterance.lang = currentLang;
        currentUtterance.rate = 1.1;
        speechSynthesis.speak(currentUtterance);
    }

    function pauseSpeech() { if (speechSynthesis.speaking && !speechSynthesis.paused) speechSynthesis.pause(); }
    function stopSpeech() { if (speechSynthesis.speaking || speechSynthesis.paused) speechSynthesis.cancel(); }

    function toggleFullscreen() { document.getElementById('btnFullscreen')?.click(); }
    function zoomIn() { document.getElementById('btnZoomIn')?.click(); }
    function zoomOut() { document.getElementById('btnZoomOut')?.click(); }
    function toggleSound() { document.getElementById('btnSound')?.click(); }
    function flipNext() { pageFlip.flipNext(); }
    function flipPrev() { pageFlip.flipPrev(); }

    // === 🎨 Thèmes ===
    function activateDarkTheme() {
        const btn = document.getElementById('btnTheme');
        if (btn && !document.body.classList.contains('dark')) btn.click();
    }
    function activateLightTheme() {
        const btn = document.getElementById('btnTheme');
        if (btn && document.body.classList.contains('dark')) btn.click();
    }

    // === 📝 Gestion des notes vocales ===
    function handleVoiceNote(transcript) {
        let pageIndex = pageFlip.getCurrentPageIndex();
        const pageMatch = transcript.match(/page\s*(\d+)/i);
        if (pageMatch) pageIndex = parseInt(pageMatch[1], 10) - 1;

        let noteText = transcript.replace(/.*note(s)?( à la page \d+)?/i, '').trim();
        if (!noteText) return alert("Aucun texte de note détecté. Dictez : 'mettre une note ...'");

        if (!allPagesText[pageIndex]) return alert(`Page ${pageIndex + 1} inexistante.`);
        if (typeof addNote === 'function') addNote(pageIndex, noteText);

        const metaCsrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const livreId = window.BookConfig?.livreId || 1;

        fetch('/save-note', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': metaCsrf
            },
            body: JSON.stringify({
                livre_id: livreId,
                page_number: pageIndex + 1,
                text: noteText,
                date: (new Date()).toISOString().slice(0, 10)
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) console.log('📝 Note sauvegardée ✅', data);
            else console.warn('Erreur sauvegarde note:', data);
        })
        .catch(err => console.error('Erreur fetch note:', err));
    }

    // === 🧠 Fonction de correspondance ===
    function matchCommand(transcript, keywords) {
        return keywords.some(keyword => {
            const normKeyword = keyword.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            return transcript.includes(normKeyword);
        });
    }

    // === 🎧 Résultat vocal ===
    recognition.onresult = (event) => {
        const transcript = Array.from(event.results)
            .map(r => r[0].transcript)
            .join(' ')
            .toLowerCase()
            .trim();

        let clean = transcript
            .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
            .replace(/[^\w\s]/g, "")
            .trim();

        console.log('[🎤 Input]', transcript);
        console.log('🧹 Cleaned:', clean);

        // === 🎯 Actions ===
        if (matchCommand(clean, commands.stop)) { console.log("🎯 stop"); stopSpeech(); }
        else if (matchCommand(clean, commands.pause)) { console.log("🎯 pause"); pauseSpeech(); }
        else if (matchCommand(clean, commands.read)) { console.log("🎯 lecture"); readCurrentPage(); }
        else if (matchCommand(clean, commands.next)) { console.log("🎯 suivante"); flipNext(); }
        else if (matchCommand(clean, commands.prev)) { console.log("🎯 précédente"); flipPrev(); }
        else if (matchCommand(clean, commands.zoomin)) { console.log("🎯 zoom avant"); zoomIn(); }
        else if (matchCommand(clean, commands.zoomout)) { console.log("🎯 zoom arrière"); zoomOut(); }
        else if (matchCommand(clean, commands.fullscreen)) { console.log("🎯 plein écran"); toggleFullscreen(); }
        else if (matchCommand(clean, commands.themeDark)) { console.log("🎯 mode sombre"); activateDarkTheme(); }
        else if (matchCommand(clean, commands.themeLight)) { console.log("🎯 mode clair"); activateLightTheme(); }
        else if (matchCommand(clean, commands.sound)) { console.log("🎯 son"); toggleSound(); }
        else if (matchCommand(clean, commands.note)) { console.log("🎯 note"); handleVoiceNote(transcript); }
    };

    // === ⚠️ Gestion erreurs ===
    recognition.onerror = (e) => {
        if (e.error !== 'no-speech') console.error('Voice error:', e);
    };

    recognition.onend = () => {
        // 🔇 Ne redémarre pas automatiquement
        console.log("🎙️ Reconnaissance arrêtée (attente d’un clic utilisateur)");
    };

    // === 🎙️ Bouton d’activation manuelle ===
    voiceBtn?.addEventListener('click', () => {
        listening = !listening;
        if (listening) {
            try {
                recognition.start();
                voiceIcon.className = 'fa-solid fa-microphone';
                console.log("🎧 Assistant vocal activé");
            } catch (err) {
                console.warn("⚠️ Erreur démarrage micro:", err);
            }
        } else {
            recognition.stop();
            stopSpeech(); // 🛑 Stoppe aussi la lecture audio
            voiceIcon.className = 'fa-solid fa-microphone-slash';
            console.log("🛑 Assistant vocal désactivé (écoute + synthèse arrêtées)");
        }
    });
}




// aiSummary.js


function cleanText(text) {
    if (!text) return '';
    return text
        .normalize('NFKD') // normalise les accents et caractères spéciaux
        .replace(/[\u0000-\u001F\u007F-\u009F]/g, '') // supprime les caractères de contrôle
        .replace(/[©®™]/g, '') // supprime les symboles connus
        .replace(/[^\p{L}\p{N}\s.,;:!?'-]/gu, '') // ne garde que lettres, chiffres et ponctuation de base
        .replace(/\s{2,}/g, ' ') // supprime les espaces multiples
        .trim();
}


export async function summarizeCurrentPage(pageFlip, allPagesText, container) {
    const currentIndex = pageFlip.getCurrentPageIndex();

    const page1 = cleanText(allPagesText[currentIndex] || '');
    const page2 = cleanText(allPagesText[currentIndex + 1] || '');
    const text = (page1 + ' ' + page2).trim();

    if (!text) {
        container.innerHTML = `<p>No text found for this page.</p>`;
        return;
    }

    try {
        // ❌ Avant : await fetch(...)
        // ✅ Correction : stocker la réponse dans une variable
        const response = await fetch('http://127.0.0.1:5000/summarize', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text })
        });

        if (!response.ok) throw new Error(`Flask error: ${response.status}`);
        const data = await response.json();

        container.innerHTML = `
            <div class="message ai">
                <h4>Summary of Pages ${currentIndex + 1}${page2 ? ' & ' + (currentIndex + 2) : ''}</h4>
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
        // 1️⃣ Message initial
        container.innerHTML = `<div class="message ai"><p>⏳ Generating summary for the full book...</p></div>`;

        const blockSize = 5;  // nombre de pages regroupées par bloc
        let partialSummaries = [];

        for (let i = 0; i < allPagesText.length; i += blockSize) {
            const blockText = cleanText(allPagesText.slice(i, i + blockSize).join(' '));

            // 2️⃣ Appel au backend Flask
            const response = await fetch('http://127.0.0.1:5000/summarize', { // URL complète vers Flask
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({text: blockText})
            });

            // 3️⃣ Vérification de la réponse
            if (!response.ok) {
                throw new Error(`Flask server error: ${response.status}`);
            }

            const data = await response.json();

            if (!data.summary) {
                throw new Error("No summary returned from server");
            }

            partialSummaries.push(data.summary);

            // 4️⃣ Affichage progressif
            container.innerHTML = `
                <div class="message ai">
                    <h4>📄 Partial Summary (${partialSummaries.length}/${Math.ceil(allPagesText.length / blockSize)})</h4>
                    <p>${partialSummaries.join(' ')}</p>
                </div>
            `;
        }

        // 5️⃣ Résumé complet
        const fullSummary = partialSummaries.join(' ');

        container.innerHTML = `
            <div class="message ai">
                <h4>📚 Full Book Summary</h4>
                <p>${fullSummary}</p>
            </div>
        `;

    } catch (err) {
        container.innerHTML = `<p style="color:red;">❌ Error: ${err.message}</p>`;
        console.error(err);
    }
}

