import { loadAndRenderPDF } from './pdfLoader.js';
import { initBookmark } from './bookmarks.js';
import { initReadingPopup } from './progress.js';
import { initNotesPopup } from './notes.js';
import { initSearchPopup } from './searchpopup.js';
import { initTranslatePopup } from './translate.js';
import { showNotesPopup } from './shownotepopup.js';
import {initAINarrator,initVoiceCommands,summarizeCurrentPage,summarizeWholeBook} from './aiNarrator.js'


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
    await bookmark.loadBookmark();

    // Lecture locale simple
    document.getElementById('readPageBtn')?.addEventListener('click', () => {
        const text = allPagesText[pageFlip.getCurrentPageIndex()];
        if (!text) return alert('Page vide');
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'fr-FR';
        speechSynthesis.cancel();
        speechSynthesis.speak(utterance);
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

    initVoiceCommands(pageFlip, allPagesText);
    initReadingPopup(pdfUrl, coverUrl, livre, totalPages);
    initNotesPopup(totalPages);
    initSearchPopup();
    initTranslatePopup();
    showNotesPopup(totalPages, pageFlip);
    initAINarrator(allPagesText, pageFlip);

    // === Gestion AI Summary ===
    function toggleNavbar() {
        document.getElementById('aiNavbar').classList.toggle('show');
    }

    function closeSummary() {
        document.getElementById('summarySidebar').classList.remove('open');
    }

    async function openSummary(type) {
        document.getElementById('aiNavbar').classList.remove('show');
        const sidebar = document.getElementById('summarySidebar');
        const title = document.getElementById('summaryTitle');
        const container = document.getElementById('chatMessages');

        container.innerHTML = `<div class="message ai"><p>⏳ Generating summary...</p></div>`;
        sidebar.classList.add('open');

        if (type === 'all') {
            title.textContent = '📚 All Pages Summary';
            await summarizeWholeBook(allPagesText, container);
        } else {
            title.textContent = '📖 Current Page Summary';
            await summarizeCurrentPage(pageFlip, allPagesText, container);
        }
    }

    // Cacher navbar si clic à l’extérieur
    document.addEventListener('click', (e) => {
        const navbar = document.getElementById('aiNavbar');
        const aiBtn = document.querySelector('.ai-main-btn');
        if (!navbar.contains(e.target) && !aiBtn.contains(e.target)) navbar.classList.remove('show');
    });

    // ✅ Rendez ces fonctions accessibles depuis ton HTML
    window.toggleNavbar = toggleNavbar;
    window.openSummary = openSummary;
    window.closeSummary = closeSummary;
});
