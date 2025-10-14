export function showNotesPopup(totalPages = 0, pageFlipInstance) {
  const btnShowNotes = document.getElementById('showNotesBtn');
  const popupNotes = document.getElementById('notesPopupshow');
  const btnCloseNotes = document.getElementById('notesPopupClose');
  const noteTextElement = document.querySelector('.note-text');
  const dateElement = document.getElementById('noteDate');

  if (!btnShowNotes || !popupNotes || !btnCloseNotes || !noteTextElement) {
    console.error('❌ Notes popup elements not found in DOM');
    return;
  }

  const livreId = window.BookConfig.livreId;

  function updateNoteDate() {
    const today = new Date();
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    dateElement.textContent = today.toLocaleDateString('en-US', options);
  }

  function positionPopup() {
    const rect = btnShowNotes.getBoundingClientRect();
    popupNotes.style.position = 'absolute';
    popupNotes.style.top = `${rect.top + window.scrollY - 10}px`;
    popupNotes.style.left = `${rect.left - popupNotes.offsetWidth - 10}px`;
  }

  // 🔹 Charge les notes pour toutes les pages visibles
  async function loadNotesForCurrentPages() {
    if (!pageFlipInstance) return;

    let currentPage = pageFlipInstance.getCurrentPageIndex() + 1; // index commence à 0
    let pages = [currentPage];

    // Si tu montres 2 pages en même temps, ajoute la page suivante si possible
    if (pageFlipInstance.getPageCount && pageFlipInstance.getPageCount() >= currentPage + 1) {
      pages.push(currentPage + 1);
    }

    noteTextElement.innerHTML = ''; // vide le contenu

    for (const page of pages) {
      try {
        const res = await fetch(`/get-note?livre_id=${livreId}&page=${page}`);
        const data = await res.json();
        console.log(`Fetched note for page ${page}:`, data);

        const noteDiv = document.createElement('div');
        noteDiv.style.marginBottom = '8px';
        noteDiv.innerHTML = `<strong>Page ${page}:</strong> ${data.note ? data.note.text : 'No saved notes.'}`;
        noteTextElement.appendChild(noteDiv);
      } catch (err) {
        console.error(`Error fetching note for page ${page}:`, err);
        const noteDiv = document.createElement('div');
        noteDiv.textContent = `Page ${page}: Error loading note.`;
        noteTextElement.appendChild(noteDiv);
      }
    }

    updateNoteDate();
  }

  function showNotes() {
    positionPopup();
    popupNotes.classList.add('show');
    loadNotesForCurrentPages();
  }

  function closeNotes() {
    popupNotes.classList.remove('show');
  }

  function toggleNotes() {
    if (popupNotes.classList.contains('show')) {
      closeNotes();
    } else {
      showNotes();
    }
  }

  btnShowNotes.addEventListener('click', toggleNotes);
  btnCloseNotes.addEventListener('click', closeNotes);

  document.addEventListener('click', (e) => {
    if (!popupNotes.contains(e.target) && e.target !== btnShowNotes) {
      closeNotes();
    }
  });

  // 🔹 Auto refresh si une note est sauvegardée
  window.addEventListener('noteSaved', (e) => {
    const { page } = e.detail;
    let currentPage = pageFlipInstance.getCurrentPageIndex() + 1;
    const pages = [currentPage];
    if (pageFlipInstance.getPageCount && pageFlipInstance.getPageCount() >= currentPage + 1) {
      pages.push(currentPage + 1);
    }
    if (pages.includes(page)) {
      loadNotesForCurrentPages();
    }
  });

  updateNoteDate();
}
