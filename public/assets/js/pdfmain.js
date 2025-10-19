import { loadAndRenderPDF } from './pdfLoader.js';
import { initBookmark } from './bookmarks.js';
import { initReadingPopup } from './progress.js';
import { initNotesPopup } from './notes.js';
import { initSearchPopup } from './searchpopup.js';
import { initTranslatePopup } from './translate.js';
import { showNotesPopup } from './shownotepopup.js';
import {initAINarrator,initVoiceAssistant,summarizeCurrentPage,summarizeWholeBook} from './aiNarrator.js'
import {initChatUI} from './chatAssistant.js'


document.addEventListener('DOMContentLoaded', async () => {
    const { pdfUrl, coverUrl, livre } = window.BookConfig;

    let startPage = 0;
    try {
        const res = await fetch(`/bookmark/load?book_url=${encodeURIComponent(pdfUrl)}`);
        const data = await res.json();
        if (data && data.page != null) startPage = data.page;
    } catch (e) {
        console.warn('Impossible de charger le bookmark', e);
    }

    const { pageFlip, allPagesText } = await loadAndRenderPDF(
        'book-container',
        pdfUrl,
        'pageCounter',
        pdfUrl
    );

    const bookmark = initBookmark({ pageFlip, containerId: 'book-container', bookUrl: pdfUrl });
   // await bookmark.loadBookmark();

    // Lecture locale simple
 const btnRead = document.getElementById('readPageBtn');
const btnPause = document.getElementById('pauseBtn');
const audioIcon = btnRead.querySelector('i');

let currentUtterance = null;
let isPaused = false;
const currentLang = navigator.language.startsWith('fr') ? 'fr' : 'en';

// Exemple de texte

let currentPage = 0;

function startReading() {
    const text = allPagesText[currentPage];
    if (!text) return alert("Page vide");

    currentUtterance = new SpeechSynthesisUtterance(text);
    currentUtterance.lang = 'fr-FR';

    currentUtterance.onstart = () => {
        audioIcon.className = 'fa-solid fa-pause-circle';
    };
    currentUtterance.onend = () => {
        audioIcon.className = 'fa-solid fa-play-circle';
        currentUtterance = null;
    };

    speechSynthesis.speak(currentUtterance);
    isPaused = false;
}

// Play / Resume button
btnRead.addEventListener('click', () => {
    if (speechSynthesis.speaking && isPaused) {
        speechSynthesis.resume();
        isPaused = false;
        audioIcon.className = 'fa-solid fa-pause-circle';
    } else if (!speechSynthesis.speaking) {
        startReading();
    }
});

// Pause button
btnPause.addEventListener('click', () => {
    if (speechSynthesis.speaking && !isPaused) {
        speechSynthesis.pause();
        isPaused = true;
        audioIcon.className = 'fa-solid fa-play-circle';
    }
});


    // Auto-save bookmark
    pageFlip.on('flip', () => {
        bookmark.saveBookmark(
            pageFlip.getCurrentPageIndex(),
            bookmark.horizontalAxis ? parseFloat(bookmark.horizontalAxis.style.top) : 0,
            bookmark.verticalAxis ? parseFloat(bookmark.verticalAxis.style.left) : 0
        );
    });

    const totalPages = pageFlip.getPageCount();

    initVoiceAssistant(pageFlip, allPagesText, currentLang);
//initChatAssistant(pageFlip, allPagesText, currentLang);
    initReadingPopup(pdfUrl, coverUrl, livre, totalPages);
    initNotesPopup(totalPages);
    initSearchPopup();
    initTranslatePopup();
    showNotesPopup(totalPages, pageFlip);
    initAINarrator(allPagesText, pageFlip);

// Faire fonctionner le bouton avec ta navbar existante


    const summarizeBtn = document.getElementById('summarizeBtn');

    // Au clic sur le bouton "Summarize", on affiche la navbar
    summarizeBtn.addEventListener('click', (e) => {
        e.stopPropagation(); // empêche de fermer le menu immédiatement
        toggleNavbar();
    });

    // Fonction toggle pour afficher/cacher la navbar
    function toggleNavbar() {
        const navbar = document.getElementById('aiNavbar');
        navbar.classList.toggle('show');
    }

    // Fermer le résumé
    function closeSummary() {
        document.getElementById('summarySidebar').classList.remove('open');
    }

    // Ouvrir le résumé selon le type choisi
    async function openSummary(type) {
        document.getElementById('aiNavbar').classList.remove('show');
        const sidebar = document.getElementById('summarySidebar');
        const title = document.getElementById('summaryTitle');
        const container = document.getElementById('chatMessages');

        container.innerHTML = `<div class="message ai"><p>⏳ Generating summary...</p></div>`;
        sidebar.classList.add('open');

        if(type === 'all') {
            title.textContent = '📚 All Pages Summary';
            await summarizeWholeBook(allPagesText, container);
        } else {
            title.textContent = '📖 Current Page Summary';
            await summarizeCurrentPage(pageFlip, allPagesText, container);
        }
    }

    // Fermer la navbar si clic à l’extérieur
    document.addEventListener('click', (e) => {
        const navbar = document.getElementById('aiNavbar');
        if (!navbar.contains(e.target) && e.target.id !== 'summarizeBtn') {
            navbar.classList.remove('show');
        }
    });

    // Rendre les fonctions accessibles globalement
    window.toggleNavbar = toggleNavbar;
    window.openSummary = openSummary;
    window.closeSummary = closeSummary;
 //initChatUI('messageInput', 'sendMessageBtn', 'messages', allPagesText);
 
 const askContainer = document.getElementById("askMessages");
    const sendBtn = document.getElementById("sendMessageBtn");
    const input = document.getElementById("messageInput");

    let chunks = [];
    let embeddings = [];
    let embeddingsReady = false;

    function addMessage(text, sender = "ai") {
        const msg = document.createElement("div");
        msg.className = `msg ${sender}`;
        msg.innerHTML = `<p>${text}</p>`;
        askContainer.appendChild(msg);
        askContainer.scrollTop = askContainer.scrollHeight;
    }

    async function embedBook(text) {
        addMessage("⏳ Génération des embeddings...", "ai");
        try {
            const response = await fetch('http://127.0.0.1:5000/embed_book', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ text })
            });
            const data = await response.json();
            chunks = data.chunks;
            embeddings = data.embeddings;
            embeddingsReady = true;
            addMessage("✅ Livre chargé et embeddings générés.", "ai");
        } catch (err) {
            addMessage(`❌ Erreur lors de la génération des embeddings : ${err.message}`, "ai");
        }
    }

    // Génération des embeddings dès que le PDF est chargé
    await embedBook(allPagesText.join(" "));

   async function askBookQuestion(question) {
    showTyping(); // ➤ AJOUT ICI : montrer le typing pendant le fetch

    try {
        if (!embeddingsReady) {
            await embedBook(allPagesText.join(" "));
        }

        const response = await fetch('http://127.0.0.1:5000/ask', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ question, chunks, embeddings })
        });

        const data = await response.json();
        
        hideTyping(); // ➤ AJOUT : cacher typing après réponse
        
        addMessage(data.answer, "ai"); // On affiche proprement la réponse
    } catch (err) {
        hideTyping(); // ➤ AJOUT en cas d'erreur aussi
        addMessage(`❌ Erreur : ${err.message}`, "ai");
    }
}

// ➤ AJOUT : fonctions showTyping et hideTyping (à coller en haut)
function showTyping() {
    const typingIndicator = document.getElementById('typingIndicator');
    typingIndicator.style.display = 'flex';
    askContainer.scrollTop = askContainer.scrollHeight;
}

function hideTyping() {
    const typingIndicator = document.getElementById('typingIndicator');
    typingIndicator.style.display = 'none';
}

    sendBtn.addEventListener("click", async () => {
        const question = input.value.trim();
        if (!question) return;
        addMessage(question, "user");
        input.value = "";
        await askBookQuestion(question);
    });

    input.addEventListener("keypress", (e) => {
        if (e.key === "Enter") sendBtn.click();
    });


});
