export function initBookmark({ pageFlip, containerId, bookUrl }) {
    const container = document.getElementById(containerId);
    const btnBookmark = document.getElementById('btnBookmark');
    const bookmarkIcon = document.getElementById('bookmarkIcon');

    const sidebarMark = document.getElementById('bookmarksidebarMark');
    const sidebarMarkPage = document.getElementById('sidebarMarkPage');
    const sidebarMarkLine = document.getElementById('sidebarMarkLine');
    const sidebarMarkCol = document.getElementById('sidebarMarkCol');
    const closesidebarMark = document.getElementById('closesidebarMark');
    const marksList = document.getElementById('marksList');
const token = document.querySelector('meta[name="csrf-token"]').content;

    let horizontalAxis = null;
    let verticalAxis = null;

    // Conteneur des boutons
    const buttonsContainer = document.createElement('div');
    buttonsContainer.style.display = 'flex';
    buttonsContainer.style.gap = '8px';
    buttonsContainer.style.marginTop = '12px';

    // --- Reset Button ---
    const btnReset = document.createElement('button');
    btnReset.classList.add('tool-btn');
    btnReset.style.flex = '1';
    btnReset.innerHTML = '<i class="fa-solid fa-rotate-right"></i>';
    btnReset.addEventListener('click', async () => {
        if(horizontalAxis) horizontalAxis.style.top = '0';
        if(verticalAxis) verticalAxis.style.left = '0';
        updateMarker();
        if(pageFlip && pageFlip.flipToPage) pageFlip.flipToPage(0);
        await saveBookmark(0, 0, 0);
        bookmarkIcon.classList.replace('fa-solid', 'fa-regular');
    });

    // --- Save Button ---
    const btnSave = document.createElement('button');
    btnSave.classList.add('tool-btn');
    btnSave.style.flex = '1';
    btnSave.innerHTML = '<i class="fa-solid fa-floppy-disk"></i>';
    btnSave.addEventListener('click', () => {
        const page = pageFlip.getCurrentPageIndex();
        const posY = horizontalAxis ? parseFloat(horizontalAxis.style.top) : 0;
        const posX = verticalAxis ? parseFloat(verticalAxis.style.left) : 0;
        saveBookmark(page, posY, posX);
    });

    buttonsContainer.appendChild(btnReset);
    buttonsContainer.appendChild(btnSave);
    sidebarMark.insertBefore(buttonsContainer, marksList);

    closesidebarMark.addEventListener('click', () => {
        sidebarMark.style.right = '-320px';
        if(horizontalAxis) horizontalAxis.style.display = 'none';
        if(verticalAxis) verticalAxis.style.display = 'none';
        const marker = document.getElementById('positionIcon');
        if(marker) marker.style.display = 'none';
    });

    function createAxes() {
        if(horizontalAxis) horizontalAxis.remove();
        if(verticalAxis) verticalAxis.remove();

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

    function updateMarker(posX=null, posY=null){
        if(!horizontalAxis || !verticalAxis) return;
        if(posX===null) posX=parseFloat(verticalAxis.style.left);
        if(posY===null) posY=parseFloat(horizontalAxis.style.top);

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

        const markerSize=24;
        const axisOffset=3;
        const shiftLeft=4;
        const shiftUp=20;

        marker.style.left = (posX-markerSize/2 + axisOffset/2 - shiftLeft)+'px';
        marker.style.top = (posY-markerSize/2 + axisOffset/2 - shiftUp)+'px';

        sidebarMarkLine.textContent = Math.round(posY);
        sidebarMarkCol.textContent = Math.round(posX);
    }

    function makeDraggable(el, axis){
        let start=0, initialPos=0;
        el.addEventListener('mousedown', e=>{
            e.preventDefault();
            if(axis==='y'){ start=e.clientY; initialPos=el.offsetTop; }
            else { start=e.clientX; initialPos=el.offsetLeft; }

            document.body.style.userSelect='none';

            function onMove(ev){
                let newPos;
                if(axis==='y'){
                    newPos = Math.max(0, Math.min(container.clientHeight, initialPos+ev.clientY-start));
                    el.style.top=newPos+'px';
                    updateMarker(null,newPos);
                } else {
                    newPos = Math.max(0, Math.min(container.clientWidth, initialPos+ev.clientX-start));
                    el.style.left=newPos+'px';
                    updateMarker(newPos,null);
                }
            }

            function onUp(){
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onUp);
                document.body.style.userSelect='';
                const page = pageFlip.getCurrentPageIndex();
                const posY = horizontalAxis ? parseFloat(horizontalAxis.style.top) : 0;
                const posX = verticalAxis ? parseFloat(verticalAxis.style.left) : 0;
                saveBookmark(page, posY, posX);
            }

            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
        });
    }

    async function loadBookmark(){
        try{
            const res = await fetch(`/bookmark/load?book_url=${encodeURIComponent(bookUrl)}`);
            const data = await res.json();
            if(!data) return;
            const scrollX = data.scroll_x ?? 50;
            const scrollY = data.scroll_y ?? 50;
            updateMarker(scrollX, scrollY);
            if(horizontalAxis) horizontalAxis.style.top=scrollY+'px';
            if(verticalAxis) verticalAxis.style.left=scrollX+'px';
            pageFlip.flipToPage(data.page ?? 0);
            if((data.page ?? 0)>0) bookmarkIcon.classList.replace('fa-regular','fa-solid');
        } catch(e){ console.warn('Bookmark load failed',e); }
    }

    async function saveBookmark(page=currentPageIndex, posY=0, posX=0){
        try{
            await fetch('/bookmark/save',{
                method:'POST',
                headers:{
                        'Content-Type':'application/json',
                        'X-CSRF-TOKEN': token
                    },

                body: JSON.stringify({
                    book_url: bookUrl,
                    page: page,
                    scroll_y: posY,
                    scroll_x: posX
                })
            });
            sidebarMarkPage.textContent = page+1;
            sidebarMarkLine.textContent = Math.round(posY);
            sidebarMarkCol.textContent = Math.round(posX);
            bookmarkIcon.classList.replace('fa-regular','fa-solid');
        } catch(e){ console.warn('Impossible de sauvegarder bookmark',e); }
    }

    btnBookmark.addEventListener('click', ()=>{
        createAxes();
        loadBookmark();
        sidebarMark.style.right='0';
    });

    return { loadBookmark, saveBookmark, updateMarker, createAxes, horizontalAxis, verticalAxis };
}
