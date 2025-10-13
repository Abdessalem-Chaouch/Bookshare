// notes.js
export function initNotesPopup(totalPages = 0) {
    const menuNotes = document.getElementById('menu-notes');
    const notesPopup = document.getElementById('notesPopup');
    const notesPopupContent = document.getElementById('notesPopupContent');
    const notesPopupClose = document.getElementById('notesPopupCloseside');

    // valeur du livre (depuis BookConfig)
    const livreIdFromConfig = window.BookConfig && window.BookConfig.livreId ? window.BookConfig.livreId : null;
    const metaCsrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    if (!menuNotes || !notesPopup || !notesPopupContent || !notesPopupClose) return;

    // Ouvrir le popup Notes
    menuNotes.addEventListener('click', async () => {
        try {
            const response = await fetch('/notes-popup');
            const html = await response.text();

            // Injecter le HTML du popup
            notesPopupContent.innerHTML = html;

            // Afficher le popup
            notesPopup.style.display = 'flex';
            notesPopup.setAttribute('aria-hidden', 'false');

            // Récupérer infos contextuelles (au moment de l'ouverture)
            const mainBookId = document.querySelector('#currentBookId')?.value || livreIdFromConfig;
            const mainBookTitle = document.querySelector('.book-header .logo-text')?.innerText.replace(/^📖\s*/, '') || '';

            // Remplir champs cachés / affichés dans le popup (s'ils existent)
            const popupBookId = notesPopupContent.querySelector('#bookId');
            const popupBookTitle = notesPopupContent.querySelector('#bookTitle');
            if (popupBookId && mainBookId) popupBookId.value = mainBookId;
            if (popupBookTitle && mainBookTitle) popupBookTitle.textContent = mainBookTitle.trim();

            // Sélectionner les éléments injectés (maintenant présents dans le DOM du popup)
            const entryDate = notesPopupContent.querySelector('#entryDate');
            const pageNumber = notesPopupContent.querySelector('#pageNumber');
            const journalText = notesPopupContent.querySelector('#journalText');
            const btnSaveNote = notesPopupContent.querySelector('#btnSaveNote') || notesPopupContent.querySelector('.notes-btn-form.save');
            const btnResetNote = notesPopupContent.querySelector('#btnResetNote') || notesPopupContent.querySelector('.notes-btn-form.reset');

            // Si un des éléments critiques est manquant : on log et on retourne (prévenir erreur)
            if (!pageNumber || !journalText) {
                console.warn('notes popup: éléments manquants (pageNumber / journalText).');
                return;
            }

            // Initialiser la date d'entrée
            if (entryDate) {
                entryDate.valueAsDate = new Date();
            }

            // Remplir le select Page
            pageNumber.innerHTML = '<option value="">Select Page</option>';
            for (let i = 1; i <= totalPages; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = `Page ${i}`;
                pageNumber.appendChild(option);
            }

            // Restaurer des notes locales si tu veux (compatibilité ancien code - facultatif)
            const saved = localStorage.getItem('bookNotes');
            let notes = [];
            if (saved) {
                try { notes = JSON.parse(saved); } catch(e){ notes = []; }
            }

            // Fonctions locales
            async function saveNote() {
                // essayer récupérer id du livre injecté dans popup sinon BookConfig
                const livreId = popupBookId?.value || mainBookId || window.BookConfig.livreId;
                const page = pageNumber.value;
                const text = journalText.value;
                const date = entryDate ? entryDate.value : (new Date()).toISOString().slice(0,10);

                if (!text || !page || !livreId) {
                    return alert('Please fill in book, page number, and note text.');
                }

                try {
                    const res = await fetch('/save-note', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': metaCsrf
                        },
                        body: JSON.stringify({ livre_id: livreId, page_number: page, date, text })
                    });

                    const data = await res.json();
                    if (data && data.success) {
                        alert('Note saved successfully! 📚');
                        // Optionnel: sauvegarde locale
                        notes.push({ livre_id: livreId, page_number: page, date, text });
                        try { localStorage.setItem('bookNotes', JSON.stringify(notes)); } catch(e){}
                    } else {
                        console.warn('Save-note response:', data);
                        alert('Error saving note.');
                    }
                } catch (err) {
                    console.error('Error saving note:', err);
                    alert('Error saving note.');
                }
                 // Trigger pour mettre à jour popup si ouvert
const currentPage = pageNumber.value;
window.dispatchEvent(new CustomEvent('noteSaved', {
  detail: { page: parseInt(currentPage), text }
}));

            }

            function clearPage() {
                if (confirm('Are you sure you want to clear this page?')) {
                    journalText.value = '';
                }
            }

          
            // Attacher listeners (les éléments sont recréés à chaque ouverture, donc sécurité contre doublons)
            if (btnSaveNote) {
                btnSaveNote.addEventListener('click', saveNote);
            } else {
                // fallback: chercher bouton via classe si id absent
                const fallbackSave = notesPopupContent.querySelector('[onclick="saveNote()"]');
                if (fallbackSave) fallbackSave.addEventListener('click', saveNote);
            }

            if (btnResetNote) {
                btnResetNote.addEventListener('click', clearPage);
            } else {
                const fallbackReset = notesPopupContent.querySelector('[onclick="clearPage()"]');
                if (fallbackReset) fallbackReset.addEventListener('click', clearPage);
            }

         
        } catch (err) {
            console.error('Failed to load notes popup:', err);
            alert('Impossible de charger les notes.');
        }
    });

    // Fermer le popup
    notesPopupClose.addEventListener('click', () => {
        notesPopup.style.display = 'none';
        notesPopup.setAttribute('aria-hidden', 'true');
        // Optionnel: vider le contenu si tu veux forcer ré-injection à l'ouverture
        // notesPopupContent.innerHTML = '';
    });

    // Fermer si clic à l'extérieur
    notesPopup.addEventListener('click', (ev) => {
        if (ev.target === notesPopup) {
            notesPopup.style.display = 'none';
            notesPopup.setAttribute('aria-hidden', 'true');
        }
    });
}
