
 document.addEventListener('DOMContentLoaded', async () => {
    // show loader once at page load
 const loader = document.getElementById('pdfLoader');

  // Blade -> JS safe
  //const pdfUrl = @json($pdfUrl);
  //const userName = @json(auth()->user()->email ?? 'USER');

const bookId = encodeURIComponent(pdfUrl); // identifiant pour bookmark
let currentPage = 0; // page actuelle, globale

 const btnBookmark = document.getElementById('btnBookmark');
const bookmarkIcon = document.getElementById('bookmarkIcon');

//const bookUrl = @json($pdfUrl); // blade

  // DOM refs
  const container = document.getElementById('book-container');
  const wrapper = document.getElementById('readerWrapper');
  const pageCounter = document.getElementById('pageCounter');
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
const sidebarMark = document.getElementById('bookmarksidebarMark');
const sidebarMarkPage = document.getElementById('sidebarMarkPage');
const sidebarMarkLine = document.getElementById('sidebarMarkLine');
const sidebarMarkCol = document.getElementById('sidebarMarkCol');
const closesidebarMark = document.getElementById('closesidebarMark');
const readerWrapper = document.getElementById('readerWrapper');
const marksList = document.getElementById('marksList');

// Conteneur des boutons
const buttonsContainer = document.createElement('div');
buttonsContainer.style.display = 'flex';
buttonsContainer.style.gap = '8px';
buttonsContainer.style.marginTop = '12px';

// --- Bouton Reset ---
const btnReset = document.createElement('button');
btnReset.classList.add('tool-btn');
btnReset.style.flex = '1';
btnReset.style.fontSize = '16px';
btnReset.style.padding = '6px';
btnReset.style.borderRadius = '12px';
btnReset.style.background = 'rgba(255,255,255,0.25)';
btnReset.style.backdropFilter = 'blur(6px)';
btnReset.style.color = '#d1a085';
btnReset.style.display = 'flex';
btnReset.style.alignItems = 'center';
btnReset.style.justifyContent = 'center';
btnReset.innerHTML = '<i class="fa-solid fa-rotate-right"></i>';

// Hover
btnReset.addEventListener('mouseenter', () => { btnReset.style.color = '#a97450ff'; });
btnReset.addEventListener('mouseleave', () => { btnReset.style.color = '#d1a085'; });

// --- Bouton Save ---
const btnSave = document.createElement('button');
btnSave.classList.add('tool-btn');
btnSave.style.flex = '1';
btnSave.style.fontSize = '16px';
btnSave.style.padding = '6px';
btnSave.style.borderRadius = '12px';
btnSave.style.background = 'rgba(255,255,255,0.25)';
btnSave.style.backdropFilter = 'blur(6px)';
btnSave.style.color = '#d1a085';
btnSave.style.display = 'flex';
btnSave.style.alignItems = 'center';
btnSave.style.justifyContent = 'center';
btnSave.innerHTML = '<i class="fa-solid fa-floppy-disk"></i>'; // icône save

btnSave.addEventListener('mouseenter', () => { btnSave.style.color = '#a97450ff'; });
btnSave.addEventListener('mouseleave', () => { btnSave.style.color = '#d1a085'; });
btnSave.addEventListener('click', () => {
  saveBookmark();
  
    // Récupère la page actuelle directement depuis pageFlip
    const currentPage = pageFlip.getCurrentPageIndex(); 


    // Récupère la position actuelle du marker
    const markY = currentMark ? parseFloat(currentMark.style.top) : parseFloat(horizontalAxis.style.top);
    const markX = currentXMark ? parseFloat(currentXMark.style.left) : parseFloat(verticalAxis.style.left);

});

buttonsContainer.appendChild(btnReset);
buttonsContainer.appendChild(btnSave);

sidebarMark.insertBefore(buttonsContainer, marksList);

closesidebarMark.addEventListener('click', () => {
    sidebarMark.style.right = '-320px';

    // Masquer les axes et le marker s’ils existent
    if (horizontalAxis) horizontalAxis.style.display = 'none';
    if (verticalAxis) verticalAxis.style.display = 'none';
    if (positionIcon) positionIcon.style.display = 'none';
});



let currentMarkY = null;
let currentMarkX = null;
let isDraggingY = false;
let startY = 0;
let isDraggingX = false;
let startX = 0;
const EDGE_OFFSET = 10;
let horizontalAxis = null;
let verticalAxis = null;



// Init


btnBookmark.addEventListener('click', async () => { 
    loadBookmark();
createAxes();
    // Récupère la page actuelle directement depuis pageFlip
    const currentPage = pageFlip.getCurrentPageIndex(); 

    // Affiche la sidebar
    sidebarMark.style.right = '0';

    // Récupère la position actuelle du marker
    const markY = currentMark ? parseFloat(currentMark.style.top) : parseFloat(horizontalAxis.style.top);
    const markX = currentXMark ? parseFloat(currentXMark.style.left) : parseFloat(verticalAxis.style.left);

    
    // Met à jour les infos dans la sidebar
    sidebarMarkPage.textContent = currentPage + 1; // Page commence à 1
    sidebarMarkLine.textContent = markY !== null ? Math.round(markY) : 0;
    sidebarMarkCol.textContent = markX !== null ? Math.round(markX) : 0;

    bookmarkIcon.classList.replace('fa-regular', 'fa-solid');


});


function updateMarker(posX = null, posY = null) {
    if(!horizontalAxis || !verticalAxis) return;

    if(posX === null) posX = parseFloat(verticalAxis.style.left);
    if(posY === null) posY = parseFloat(horizontalAxis.style.top);

    let marker = document.getElementById('positionIcon');
    if(!marker){
        marker = document.createElement('div');
        marker.id = 'positionIcon';
        marker.textContent = '📍';
        marker.style.position = 'absolute';
        marker.style.fontSize = '24px';
        marker.style.zIndex = 3500;
        marker.style.pointerEvents = 'none';
        container.appendChild(marker);
    }

    const markerSize = 24;      // taille du marker
    const axisOffset = 3;        // largeur/hauteur des axes
    const shiftLeft = 4;         // décalage vers la gauche
    const shiftUp = 20;           // décalage vers le haut

    marker.style.left = (posX - markerSize / 2 + axisOffset / 2 - shiftLeft) + 'px';
    marker.style.top  = (posY - markerSize / 2 + axisOffset / 2 - shiftUp) + 'px';

    sidebarMarkLine.textContent = Math.round(posY);
    sidebarMarkCol.textContent = Math.round(posX);
}
function createAxes() {
    if(horizontalAxis) horizontalAxis.remove();
    if(verticalAxis) verticalAxis.remove();

    // Horizontal
    horizontalAxis = document.createElement('div');
    horizontalAxis.classList.add('axis-line', 'horizontal');
    horizontalAxis.style.position = 'absolute';
    horizontalAxis.style.top = '50px';
    horizontalAxis.style.left = '0';
    horizontalAxis.style.width = '100%';
    horizontalAxis.style.height = '3px';
    horizontalAxis.style.background = '#d1a085';
    horizontalAxis.style.cursor = 'grab';
    container.appendChild(horizontalAxis);
    makeDraggable(horizontalAxis, 'y');

    // Vertical
    verticalAxis = document.createElement('div');
    verticalAxis.classList.add('axis-line', 'vertical');
    verticalAxis.style.position = 'absolute';
    verticalAxis.style.left = '50px';
    verticalAxis.style.top = '0';
    verticalAxis.style.width = '3px';
    verticalAxis.style.height = '100%';
    verticalAxis.style.background = '#d1a085';
    verticalAxis.style.cursor = 'grab';
    container.appendChild(verticalAxis);
    makeDraggable(verticalAxis, 'x');
}

function makeDraggable(el, axis) {
    let start = 0, initialPos = 0;

    el.addEventListener('mousedown', e => {
        e.preventDefault();
        if(axis==='y'){ start=e.clientY; initialPos=el.offsetTop; }
        else { start=e.clientX; initialPos=el.offsetLeft; }

        document.body.style.userSelect='none';

        function onMove(ev){
            let newPos;
            if(axis==='y'){
                newPos = Math.max(0, Math.min(container.clientHeight, initialPos + ev.clientY - start));
                el.style.top = newPos+'px';
                updateMarker(null, newPos);
            } else {
                newPos = Math.max(0, Math.min(container.clientWidth, initialPos + ev.clientX - start));
                el.style.left = newPos+'px';
                updateMarker(newPos, null);
            }
        }

        function onUp(){
            document.removeEventListener('mousemove', onMove);
            document.removeEventListener('mouseup', onUp);
            document.body.style.userSelect='';
            saveBookmark();
        }

        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    });
}
async function loadBookmark() {
    try {
        const res = await fetch(`/bookmark/load?book_url=${encodeURIComponent(bookUrl)}`);
        const data = await res.json();
        if(!data) return;

        const scrollX = data.scroll_x != null ? data.scroll_x : 50;
        const scrollY = data.scroll_y != null ? data.scroll_y : 50;
        updateMarker(scrollX, scrollY);
        if(horizontalAxis) horizontalAxis.style.top = scrollY + 'px';
        if(verticalAxis) verticalAxis.style.left = scrollX + 'px';

        pageFlip.flipToPage(data.page || 0);
        if((data.page || 0) > 0) bookmarkIcon.classList.replace('fa-regular', 'fa-solid');

        // Afficher le marker après que les axes ont leur position finale
    

    } catch(e) {
        console.warn('Bookmark load failed', e);
    }
}
async function saveBookmark() {
    const posY = horizontalAxis ? parseFloat(horizontalAxis.style.top) : 0;
    const posX = verticalAxis ? parseFloat(verticalAxis.style.left) : 0;
    const currentPage = pageFlip.getCurrentPageIndex();

    sidebarMarkPage.textContent = currentPage + 1;
    sidebarMarkLine.textContent = Math.round(posY);
    sidebarMarkCol.textContent = Math.round(posX);

    bookmarkIcon.classList.replace('fa-regular', 'fa-solid');

    try {
        await fetch('/bookmark/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                book_url: bookUrl,
                page: currentPage,
                scroll_y: posY,
                scroll_x: posX
            })
        });
    } catch(e) {
        console.warn('Impossible de sauvegarder bookmark', e);
    }
}
btnReset.addEventListener('click', async () => {
    // Reset axes
    if(horizontalAxis) horizontalAxis.style.top = '0';
    if(verticalAxis) verticalAxis.style.left = '0';
    updateMarker();

    // Reset page à la première page
    if(pageFlip && typeof pageFlip.flipToPage === 'function') {
        pageFlip.flipToPage(0);       // flip côté UI
    }

    // Mettre à jour le compteur sidebar
    sidebarMarkPage.textContent = 1; // page 1 côté sidebar
    sidebarMarkLine.textContent = 0;
    sidebarMarkCol.textContent = 0;

    // Sauvegarder bookmark côté base
    const posY = horizontalAxis ? parseFloat(horizontalAxis.style.top) : 0;
    const posX = verticalAxis ? parseFloat(verticalAxis.style.left) : 0;
    try {
        await fetch('/bookmark/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                book_url: bookUrl,
                page: 0,        // page reset
                scroll_y: posY,
                scroll_x: posX
            })
        });
    } catch(e) {
        console.warn('Impossible de sauvegarder bookmark', e);
    }

    // Mettre à jour l’icône bookmark
    bookmarkIcon.classList.replace('fa-solid', 'fa-regular');
});



  // disable right-click & common shortcuts (basic deterrent)
  /*document.addEventListener('contextmenu', e => e.preventDefault());
  document.addEventListener('keydown', e => {
    if (e.ctrlKey && ['s','u','c','p'].includes(e.key.toLowerCase())) e.preventDefault();
  });
*/
 
  const pageAudio = new Audio("{{ asset('assets/video/flipAudio.m4a') }}");
pageAudio.volume = 0.4; // ajuster le volume
  let soundEnabled = true;

  // prepare pageFlip sizes based on container
  function computePageSize() {
    // We'll create one-page size roughly half container width for two-page spread look
    const contW = Math.max(600, Math.min(1200, container.clientWidth));
    const pageW = Math.floor(contW / 2) - 6;
    const pageH = Math.max(420, Math.floor(container.clientHeight) - 12);
    return { pageW, pageH };
  }
 startPage = 0;
 try {
const res = await fetch(`/bookmark/load?book_url=${encodeURIComponent(bookUrl)}`);
 const data = await res.json();
    if (data && data.page != null) {
        startPage = data.page; // récupère la page sauvegardée
        console.log(data.page);
    }
} catch(e) {
    console.warn('Bookmark load failed', e);
}
  // create PageFlip instance
  const { pageW, pageH } = computePageSize();
let pageFlip = new St.PageFlip(container, {
    width: pageW,
    height: pageH,
    size: 'fixed',
    showCover: true,
    drawShadow: true,
    flippingTime: 500,
     startPage: startPage
});

if (startPage > 0) {
    bookmarkIcon.classList.replace('fa-regular', 'fa-solid');
}

  // render PDF -> images (once)
  let pdfDoc = null;
  let totalPages = 0;
  const images = [];
  try {
    const loadingTask = pdfjsLib.getDocument(pdfUrl);
    pdfDoc = await loadingTask.promise;
    totalPages = pdfDoc.numPages;

    for (let i = 1; i <= totalPages; i++) {
      const page = await pdfDoc.getPage(i);
      const scale = 4; // augmente la résolution pour une meilleure qualité
const viewport = page.getViewport({ scale });
const canvas = document.createElement('canvas');
canvas.width = viewport.width;
canvas.height = viewport.height;
const ctx = canvas.getContext('2d');

// active le rendu haute qualité
ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';

await page.render({ canvasContext: ctx, viewport }).promise;



images.push(canvas.toDataURL('image/png'));

    }
  } catch (err) {
    console.error('PDF render error', err);
    alert('Erreur lors du chargement du PDF. Vérifie l’URL ou la console.');
    return;
  }

  // load images into PageFlip and wait for init event
  let initDone = false;
  pageFlip.loadFromImages(images);
  await new Promise(resolve => {
pageFlip.on('init', () => {
    const total = pageFlip.getPageCount();
    pageCounter.textContent = `${startPage + 1} / ${total}`;
    sidebarMarkPage.textContent = startPage + 1; // page initiale
    if (startPage > 0) pageFlip.flipToPage(startPage);
    updateNavButtons();
});

// ✅ Met à jour la page dans la sidebar à chaque flip
pageFlip.on('flip', (e) => {
    const currentPage = e.data + 1; // index 0 → page 1
    pageCounter.textContent = `${currentPage} / ${pageFlip.getPageCount()}`;
    sidebarMarkPage.textContent = currentPage; // mise à jour live
});



    // fallback: resolve after 1s if init never fires
    setTimeout(() => { if (!initDone) resolve(); }, 1200);
  });



  
  // helper to update counter
  function updateCounter() {
    try {
      const cur = (typeof pageFlip.getCurrentPageIndex === 'function') ? pageFlip.getCurrentPageIndex() : 0;
      pageCounter.textContent = `${cur + 1} / ${totalPages}`;
    } catch {
      pageCounter.textContent = `1 / ${totalPages}`;
    }
  }



// ------------------ SHOW/HIDE PREV & NEXT BUTTONS ------------------

  btnNext.addEventListener('click', (e) => { e.preventDefault(); try { pageFlip.flipNext(); } catch(err){ console.warn(err); } });
  btnPrev.addEventListener('click', (e) => { e.preventDefault(); try { pageFlip.flipPrev(); } catch(err){ console.warn(err); } });

  pageFlip.on('flip', () => { updateCounter(); if (soundEnabled) pageAudio.play().catch(()=>{}); });
  pageFlip.on('init', () => { updateCounter();});
 function updateNavButtons() {
    const currentPage = pageFlip.getCurrentPageIndex(); // index courant
    const total = pageFlip.getPageCount();

    if (btnPrev) btnPrev.style.display = (currentPage > 0) ? 'inline-flex' : 'none';
    if (btnNext) btnNext.style.display = (currentPage < total - 2) ? 'inline-flex' : 'none';
}

pageFlip.on('flip',() => {
updateCounter();
    updateNavButtons();
saveBookmark;
});


    


pageFlip.on('init', updateNavButtons,updateCounter());
// initial call
updateNavButtons();
loader.style.display='none';
// hide loader after first load
 if (loader) loader.style.display = 'none';

  /* ------------------ ZOOM (CSS-based, instant) ------------------ */
  let zoom = 1.0;
  function applyZoom() {
    // Apply transform to the container (scale) - transform origin top center for natural zoom
    container.style.transform = `scale(${zoom})`;
    container.style.transformOrigin = 'top center';

    // if zoom > 1 allow wrapper scrolling (so user can scroll to see full page)
    if (zoom > 1) wrapper.classList.add('zoom-scroll'); else wrapper.classList.remove('zoom-scroll');
  }

  btnZoomIn.addEventListener('click', (e) => {
    e.preventDefault();
    zoom = Math.min(2.4, +(zoom + 0.2).toFixed(2));
    applyZoom();
  });
  btnZoomOut.addEventListener('click', (e) => {
    e.preventDefault();
    zoom = Math.max(0.6, +(zoom - 0.2).toFixed(2));
    applyZoom();
  });

  /* ------------------ FULLSCREEN ------------------ */
    btnFullscreen.addEventListener('click', async () => {
    const fullscreenWrapper = document.getElementById('fullscreenWrapper');
    const navbar = document.querySelector('nav');
    const footer = document.querySelector('footer');

    try {
        if (!document.fullscreenElement) {
        // Masquer la navbar et le footer
        if (navbar) navbar.style.display = 'none';
        if (footer) footer.style.display = 'none';

        // Plein écran du livre + toolbar
        await fullscreenWrapper.requestFullscreen();

        fullscreenWrapper.style.position = 'fixed';
        fullscreenWrapper.style.inset = '0';
        fullscreenWrapper.style.zIndex = '9999';
        fullscreenWrapper.style.background = '#f8f3ef';
        fullscreenWrapper.style.display = 'flex';
        fullscreenWrapper.style.flexDirection = 'column';
        fullscreenWrapper.style.alignItems = 'center';
        fullscreenWrapper.style.justifyContent = 'center';
        fullscreenWrapper.style.overflow = 'auto'; // scroll si zoom > 1

        // Adapter le livre à l’écran
        container.style.width = '100vw';
        container.style.height = '100vh';
        container.style.maxWidth = 'none';
        container.style.maxHeight = 'none';
        container.style.borderRadius = '0';

        // Autoriser le scroll quand zoomé
        wrapper.classList.add('zoom-scroll');

        // Ajuster la mise à jour de taille PageFlip
        pageFlip.updateFromOptions({
            width: window.innerWidth,
            height: window.innerHeight
        });

        } else {
        await document.exitFullscreen();

        // Rétablir tout
        fullscreenWrapper.removeAttribute('style');
        container.removeAttribute('style');
        wrapper.classList.remove('zoom-scroll');

        // Restaurer navbar et footer
        if (navbar) navbar.style.display = '';
        if (footer) footer.style.display = '';

        // Revenir à la taille initiale du livre
        pageFlip.updateFromOptions({
            width: 1100,  // remets ici ta taille initiale
            height: 800
        });
        }
    } catch (err) {
        console.warn('Fullscreen failed', err);
    }
    });

    // When leaving fullscreen (via ESC), restore container sizes
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) {
        container.style.width = '';
        container.style.height = '';
        container.style.borderRadius = '';
        wrapper.classList.remove('zoom-scroll');
       
          btnFullscreen.innerHTML = '<i class="fa-solid fa-expand"></i>';
        }
        else{
      btnFullscreen.innerHTML = '<i class="fa-solid fa-compress"></i>';
        }
    });

  /* ------------------ THEME & SOUND ------------------ */
  btnTheme.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    themeIcon.classList.toggle('fa-moon', !isDark);
    themeIcon.classList.toggle('fa-sun', isDark);
    
  });
  btnSound.addEventListener('click', () => {
      soundEnabled = !soundEnabled;
      soundIcon.classList.toggle('fa-volume-xmark', !soundEnabled);
      soundIcon.classList.toggle('fa-volume-high', soundEnabled);
  });

  pageFlip.on('flip', () => { 
      updateCounter();
    updateNavButtons();
      if (soundEnabled) {
          const flipAudio = new Audio("{{ asset('assets/video/flipAudio.m4a') }}");
          flipAudio.volume = 0.4;
          flipAudio.play().catch(() => {});
      }
  });

  /* ------------------ FLOATING TOOLBAR AUTO-HIDE ------------------ */
  const toolbar = floatingToolbar;
  let toolbarTimer = null;
  function showToolbar() {
    toolbar.classList.remove('hidden');
    if (toolbarTimer) clearTimeout(toolbarTimer);
    toolbarTimer = setTimeout(() => toolbar.classList.add('hidden'), 2500);
  }
  container.addEventListener('mousemove', showToolbar);
  container.addEventListener('touchstart', showToolbar);
  btnNext.addEventListener('mouseenter', showToolbar);
  btnPrev.addEventListener('mouseenter', showToolbar);
  toolbarTimer = setTimeout(() => toolbar.classList.add('hidden'), 1600);

  /* ------------------ RESIZE handling (keep layout stable) ------------------ */
  let resizeTimer = null;
  window.addEventListener('resize', () => {
    if (resizeTimer) clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      container.style.willChange = 'auto';
      setTimeout(()=>container.style.willChange = '', 200);
    }, 300);
  });

  window._bookshare_pageFlip = pageFlip;
  window._bookshare_images = images;
  window._bookshare_reloadZoom = (z) => { zoom = z || zoom; applyZoom(); updateCounter(); };

  applyZoom();
 

});
