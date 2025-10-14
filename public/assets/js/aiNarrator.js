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
            const res = await fetch('http://127.0.0.1:8000/speak', {
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
    
export function initVoiceCommands(pageFlip, allPagesText) {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) return alert("⚠️ SpeechRecognition non supporté");

    const recognition = new SpeechRecognition();
    recognition.lang = 'fr-FR';
    recognition.continuous = true;
    recognition.interimResults = false; // <-- mettre false pour fiabilité

    const voiceBtn = document.getElementById('btnVoiceDetect');
    const voiceIcon = voiceBtn?.querySelector('i');
    let listening = false;

    const nextAliases = ['page suivante', 'suivante'];
    const prevAliases = ['page précédente', 'précédente'];
    const readAliases = ['lire page', 'lire cette page', 'lecture'];
    const pauseAliases = ['pause lecture', 'pause'];
    const stopAliases = ['stop lecture', 'arrêter', 'stop'];
    const noteAliases = ['mettre une note', 'ajouter une note'];

    function readCurrentPage() {
        const currentIndex = pageFlip.getCurrentPageIndex();
        const page1 = allPagesText[currentIndex] || '';
        const page2 = allPagesText[currentIndex + 1] || '';
        let text = (page1 + ' ' + page2).trim();

        text = text.replace(/©/g, '')
                   .replace(/®/g, '')
                   .replace(/[^\p{L}\p{N}\s.,;:!?'-]/gu, '')
                   .replace(/\s{2,}/g, ' ')
                   .trim();

        if (!text) return alert('Texte vide après nettoyage');

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'fr-FR';
        utterance.rate = 1.2;
        speechSynthesis.cancel();
        speechSynthesis.speak(utterance);
    }

    function handleVoiceNote(transcript) {
        let pageIndex = pageFlip.getCurrentPageIndex();
        const pageMatch = transcript.match(/page (\d+)/);
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
            if (data.success) console.log('Note sauvegardée ✅', data);
            else console.warn('Erreur sauvegarde note:', data);
        })
        .catch(err => console.error('Erreur fetch note:', err));
    }

    recognition.onresult = (event) => {
        const transcript = Array.from(event.results)
            .map(r => r[0].transcript)
            .join(' ')
            .toLowerCase()
            .trim();

        console.log('[Voice Command]', transcript);

        if (nextAliases.some(a => transcript.includes(a))) pageFlip.flipNext();
        else if (prevAliases.some(a => transcript.includes(a))) pageFlip.flipPrev();
        else if (readAliases.some(a => transcript.includes(a))) readCurrentPage();
        else if (pauseAliases.some(a => transcript.includes(a))) speechSynthesis.pause();
        else if (stopAliases.some(a => transcript.includes(a))) speechSynthesis.cancel();
        else if (noteAliases.some(a => transcript.includes(a))) handleVoiceNote(transcript);
    };

    recognition.onerror = (e) => console.error('Voice command error:', e);
    recognition.onend = () => { if (listening) recognition.start(); };

    voiceBtn?.addEventListener('click', () => {
        listening = !listening;
        if (listening) {
            recognition.start();
            if (voiceIcon) voiceIcon.className = 'fa-solid fa-microphone';
        } else {
            recognition.stop();
            if (voiceIcon) voiceIcon.className = 'fa-solid fa-microphone-slash';
        }
    });

    const readBtn = document.getElementById('readPageBtn');
    if (readBtn) readBtn.addEventListener('click', readCurrentPage);
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

    // Prendre la page actuelle + la suivante pour simuler une double page
    const page1 = cleanText(allPagesText[currentIndex] || '');
    const page2 = cleanText(allPagesText[currentIndex + 1] || ''); // peut être vide si fin du livre
    const text = (page1 + ' ' + page2).trim();

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
        container.innerHTML = `<div class="message ai"><p>⏳ Generating summary for the full book...</p></div>`;

        const blockSize = 5; // Nombre de pages par bloc
        let partialSummaries = [];

        for (let i = 0; i < allPagesText.length; i += blockSize) {
            const blockText = cleanText(allPagesText.slice(i, i + blockSize).join(' '));

            const response = await fetch('http://127.0.0.1:5000/summarize', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ text: blockText })
            });

            if (!response.ok) throw new Error(`Flask error: ${response.status}`);
            const data = await response.json();
            partialSummaries.push(data.summary);
        }

        const fullSummary = partialSummaries.join(' ');

        container.innerHTML = `
            <div class="message ai">
                <h4>📚 Full Book Summary</h4>
                <p>${fullSummary}</p>
            </div>`;
    } catch (err) {
        container.innerHTML = `<p style="color:red;">❌ Error: ${err.message}</p>`;
    }
}
