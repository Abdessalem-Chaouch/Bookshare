export function initReadingPopup(pdfUrl, coverUrl, livre, totalPages) {
    const menuStart = document.getElementById('menu-start');
    const readingPopup = document.getElementById('readingPopup');
    const readingPopupContent = document.getElementById('readingPopupContent');
    const readingPopupClose = document.getElementById('readingPopupClose');

    // ✅ Ajout récupération CSRF token depuis meta
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

    if (!menuStart || !readingPopup || !readingPopupContent || !readingPopupClose) return;

    menuStart.addEventListener('click', async () => {
        try {
            const response = await fetch('/reading-popup');
            const html = await response.text();
            readingPopupContent.innerHTML = html;

            // afficher popup
            readingPopup.style.display = 'flex';
            readingPopup.setAttribute('aria-hidden', 'false');

            // appliquer cover
            const coverDiv = readingPopupContent.querySelector('#cover-fill');
            if (coverDiv && coverUrl) {
                coverDiv.style.backgroundImage = `url('${coverUrl}')`;
            }

            // DOM popup
            const btnToggle = readingPopupContent.querySelector('#btnToggleReading');
            const btnReset = readingPopupContent.querySelector('#btnResetProgress');
            const readingTimeElem = readingPopupContent.querySelector('#reading-time');
            const readingProgressElem = readingPopupContent.querySelector('#reading-progress');

            let isReading = false;
            let timerInterval = null;

            // Temps initial depuis base
            console.log("***************secondsspent", livre.reading_time);

            let secondsSpent = Number(livre.reading_time ? livre.reading_time * 60 : 0) || 0;
            console.log("***************secondsspent", secondsSpent);
            // totalMinutes basé sur livre.total_reading_time si existant
            const totalMinutes = Number(livre.total_reading_time || Math.ceil(totalPages * 2));

            function updateUI() {
                const percent = Math.min(Math.round((secondsSpent / (totalMinutes * 60)) * 100), 100);
                if (readingTimeElem) readingTimeElem.textContent = `${Math.floor(secondsSpent / 60)} / ${totalMinutes} min`;
                if (readingProgressElem) readingProgressElem.textContent = percent + '%';
                if (coverDiv) coverDiv.style.height = percent + '%';
            }

            function startReading() {
                if (isReading) return;
                isReading = true;
                if (btnToggle) btnToggle.textContent = 'Stop Reading';

                timerInterval = setInterval(() => {
                    secondsSpent++;
                    updateUI();
                    console.log("Seconds spent:", secondsSpent); // ✅ Debug temps
                    if (secondsSpent % 30 === 0) saveSeconds(30);
                }, 1000);
            }

            function stopReading() {
                if (!isReading) return;
                isReading = false;
                if (btnToggle) btnToggle.textContent = 'Start Reading';
                if (timerInterval) {
                    clearInterval(timerInterval);
                    timerInterval = null;
                }
            }

            async function saveSeconds(n) {
                try {
                    const bookIdProg = livre.id;
                    const res = await fetch(`/livres/${bookIdProg}/update-read-time`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            // ✅ Correction CSRF dynamique
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ seconds: n })
                    });
                    if (!res.ok) throw new Error('Erreur réseau');
                } catch (e) {
                    console.warn('Erreur en sauvegardant le temps de lecture :', e);
                    // ✅ On garde le timer actif même si erreur
                }
            }

            function resetReading() {
                stopReading();
                secondsSpent = 0;
                updateUI();
                const bookIdProg = livre.id;
                fetch(`/livres/${bookIdProg}/reset-read-time`, {
                    method: 'POST',
                    headers: { 
                        // ✅ Correction CSRF ici aussi
                        'X-CSRF-TOKEN': csrfToken 
                    }
                }).catch(e => console.warn('Erreur reset:', e));
            }

            // Boutons
            if (btnToggle) btnToggle.addEventListener('click', () => isReading ? stopReading() : startReading());
            if (btnReset) btnReset.addEventListener('click', resetReading);

            // Initial UI update
            updateUI();

            // TTS bouton
            const readBtn = readingPopupContent.querySelector('#readPageBtn');
            const audioPlayer = readingPopupContent.querySelector('#narratorPlayer');
            if (readBtn && audioPlayer) {
                readBtn.addEventListener('click', async () => {
                    const resText = await fetch(`/pdf-text?page=1&pdf=${encodeURIComponent(pdfUrl)}`);
                    const data = await resText.json();
                    const text = data.text || 'Page vide';
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'fr-FR';
                    speechSynthesis.cancel();
                    speechSynthesis.speak(utterance);
                });
            }

        } catch (err) {
            console.error('Erreur loading reading popup:', err);
        }
    });

    // Fermer popup
    readingPopupClose.addEventListener('click', () => {
        readingPopup.style.display = 'none';
        readingPopup.setAttribute('aria-hidden', 'true');
    });

    readingPopup.addEventListener('click', (ev) => {
        if (ev.target === readingPopup) {
            readingPopup.style.display = 'none';
            readingPopup.setAttribute('aria-hidden', 'true');
        }
    });
}
