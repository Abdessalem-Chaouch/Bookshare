// pdfLoader.js
export async function loadAndRenderPDF(containerId, pdfUrl, pageCounterId, bookUrl) {
    const container = document.getElementById(containerId);
    const pageCounter = document.getElementById(pageCounterId);
    const wrapper = document.getElementById('readerWrapper');
    const floatingToolbar = document.getElementById('floatingToolbar');
    const btnZoomIn = document.getElementById('btnZoomIn');
    const btnZoomOut = document.getElementById('btnZoomOut');
    const btnFullscreen = document.getElementById('btnFullscreen');
    const btnTheme = document.getElementById('btnTheme');
    const themeIcon = document.getElementById('themeIcon');
    const btnSound = document.getElementById('btnSound');
    const soundIcon = document.getElementById('soundIcon');
    const btnNext = document.getElementById('nextPage');
    const btnPrev = document.getElementById('prevPage');
    const sidebarMarkPage = document.getElementById('sidebarMarkPage');
    const loader = document.getElementById('pdfLoader');

    if (!container) throw new Error('Container not found');

    const pageAudio = new Audio(window.BookConfig.flipAudioUrl);
    pageAudio.volume = 0.4;

    let soundEnabled = true;
    let zoom = 1.0;
    let startPage = 0;
    const allPagesText = [];
    let currentPageIndex = 0;
    const images = [];

    // Charger bookmark
    try {
        const res = await fetch(`/bookmark/load?book_url=${encodeURIComponent(bookUrl)}`);
        const data = await res.json();
        if (data && data.page != null) startPage = data.page;
    } catch (e) {
        console.warn('Bookmark load failed', e);
    }

    function computePageSize() {
        const contW = Math.max(600, Math.min(1200, container.clientWidth));
        const pageW = Math.floor(contW / 2) - 6;
        const pageH = Math.max(420, Math.floor(container.clientHeight) - 12);
        return { pageW, pageH };
    }

    const { pageW, pageH } = computePageSize();

    const pageFlip = new St.PageFlip(container, {
        width: pageW,
        height: pageH,
        size: 'fixed',
        showCover: true,
        drawShadow: true,
        flippingTime: 500,
        startPage
    });

    // ===== Chargement PDF optimisé =====
    let pdfDoc = null;
    try {
        const loadingTask = pdfjsLib.getDocument(pdfUrl);
        pdfDoc = await loadingTask.promise;
        const totalPages = pdfDoc.numPages;

        // Rendu parallèle pour accélérer le chargement
        const renderPromises = [];
        for (let i = 1; i <= totalPages; i++) {
            renderPromises.push(
                (async (pageIndex) => {
                    const page = await pdfDoc.getPage(pageIndex);
                    const scale = 2.5; // qualité raisonnable
                    const viewport = page.getViewport({ scale });
                    const canvas = document.createElement('canvas');
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    const ctx = canvas.getContext('2d');
                    ctx.imageSmoothingEnabled = true;
                    ctx.imageSmoothingQuality = 'high';
                    await page.render({ canvasContext: ctx, viewport }).promise;

                    const textContent = await page.getTextContent();
                    const text = textContent.items.map(item => item.str).join(' ');

                    allPagesText[pageIndex - 1] = text;
                    images[pageIndex - 1] = canvas.toDataURL('image/png');

                    if (pageIndex === 1) currentPageIndex = 0;
                })(i)
            );
        }
        await Promise.all(renderPromises);

    } catch (err) {
        console.error('PDF render error', err);
        alert('Erreur lors du chargement du PDF.');
        return;
    }

    // Charger toutes les images dans PageFlip
    pageFlip.loadFromImages(images);

    // ===== Initialisation PageFlip =====
    await new Promise(resolve => {
        let initDone = false;
        pageFlip.on('init', () => {
            const total = pageFlip.getPageCount();
            pageCounter.textContent = `${startPage + 1} / ${total}`;
            sidebarMarkPage.textContent = startPage + 1;

            if (startPage > 0 && startPage < total) {
                pageFlip.flipToPage(startPage);
                currentPageIndex = startPage;
            }

            updateNavButtons();
            initDone = true;
            resolve();
        });

        setTimeout(() => { if (!initDone) resolve(); }, 2000);
    });

    function updateCounter() {
        const cur = pageFlip.getCurrentPageIndex ? pageFlip.getCurrentPageIndex() : 0;
        pageCounter.textContent = `${cur + 1} / ${pdfDoc.numPages}`;
    }

    function updateNavButtons() {
        const current = pageFlip.getCurrentPageIndex();
        const total = pageFlip.getPageCount();
        if (btnPrev) btnPrev.style.display = (current > 0) ? 'inline-flex' : 'none';
        if (btnNext) btnNext.style.display = (current < total - 2) ? 'inline-flex' : 'none';
    }

    function applyZoom() {
        container.style.transform = `scale(${zoom})`;
        container.style.transformOrigin = 'top center';
        if (zoom > 1) wrapper.classList.add('zoom-scroll'); else wrapper.classList.remove('zoom-scroll');
    }

    btnNext.addEventListener('click', (e) => { e.preventDefault(); pageFlip.flipNext(); });
    btnPrev.addEventListener('click', (e) => { e.preventDefault(); pageFlip.flipPrev(); });

    pageFlip.on('flip', () => { 
        currentPageIndex = pageFlip.getCurrentPageIndex();
        updateCounter(); 
        updateNavButtons();
        if (soundEnabled) pageAudio.play().catch(()=>{});
    });

    btnZoomIn.addEventListener('click', (e) => { e.preventDefault(); zoom = Math.min(2.4, +(zoom + 0.2).toFixed(2)); applyZoom(); });
    btnZoomOut.addEventListener('click', (e) => { e.preventDefault(); zoom = Math.max(0.6, +(zoom - 0.2).toFixed(2)); applyZoom(); });

    btnFullscreen.addEventListener('click', async () => {
        const fullscreenWrapper = document.getElementById('fullscreenWrapper');
        const navbar = document.querySelector('nav');
        const footer = document.querySelector('footer');
        try {
            if (!document.fullscreenElement) {
                if (navbar) navbar.style.display = 'none';
                if (footer) footer.style.display = 'none';
                await fullscreenWrapper.requestFullscreen();
                fullscreenWrapper.style.position = 'fixed';
                fullscreenWrapper.style.inset = '0';
                fullscreenWrapper.style.zIndex = '9999';
                fullscreenWrapper.style.display = 'flex';
                fullscreenWrapper.style.flexDirection = 'column';
                fullscreenWrapper.style.alignItems = 'center';
                fullscreenWrapper.style.justifyContent = 'center';
                container.style.width = '100vw';
                container.style.height = '100vh';
                container.style.maxWidth = 'none';
                container.style.maxHeight = 'none';
                container.style.borderRadius = '0';
                pageFlip.updateFromOptions({ width: window.innerWidth, height: window.innerHeight });
            } else {
                await document.exitFullscreen();
                fullscreenWrapper.removeAttribute('style');
                container.removeAttribute('style');
                wrapper.classList.remove('zoom-scroll');
                if (navbar) navbar.style.display = '';
                if (footer) footer.style.display = '';
                pageFlip.updateFromOptions({ width: pageW, height: pageH });
            }
        } catch (err) { console.warn('Fullscreen failed', err); }
    });

// Charger le thème sauvegardé (si déjà choisi)
if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
    themeIcon.classList.replace('fa-moon', 'fa-sun');
}

btnTheme.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');

    themeIcon.classList.toggle('fa-moon', !isDark);
    themeIcon.classList.toggle('fa-sun', isDark);

    // Sauvegarder le thème dans le navigateur
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
});
    btnSound.addEventListener('click', () => {
        soundEnabled = !soundEnabled;
        soundIcon.classList.toggle('fa-volume-xmark', !soundEnabled);
        soundIcon.classList.toggle('fa-volume-high', soundEnabled);
    });

    loader.style.display = 'none';
    applyZoom();
    updateNavButtons();
    updateCounter();

    window._bookshare_pageFlip = pageFlip;
    window._bookshare_images = images;
    window._bookshare_reloadZoom = (z) => { zoom = z || zoom; applyZoom(); updateCounter(); };

    return { pageFlip, allPagesText };
}
