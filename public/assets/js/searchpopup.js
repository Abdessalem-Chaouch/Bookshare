// search.js
export function initSearchPopup() {
    const menuSearch = document.getElementById('menu-search');
    const searchPopup = document.getElementById('searchPopup');
    const searchPopupContent = document.getElementById('searchPopupContent');
    const searchPopupClose = document.getElementById('searchPopupClose');

    menuSearch.addEventListener('click', async () => {
        const html = await (await fetch('/search-popup')).text();
        searchPopupContent.innerHTML = html;
        searchPopup.style.display = 'flex';
        searchPopup.setAttribute('aria-hidden', 'false');
    });

    searchPopupClose.addEventListener('click', () => {
        searchPopup.style.display = 'none';
        searchPopup.setAttribute('aria-hidden', 'true');
    });

    searchPopup.addEventListener('click', e => {
        if (e.target === searchPopup) {
            searchPopup.style.display = 'none';
            searchPopup.setAttribute('aria-hidden', 'true');
        }
    });
}
