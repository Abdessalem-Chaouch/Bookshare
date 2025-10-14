// translate.js
export function initTranslatePopup() {
    const menuTranslate = document.getElementById('menu-translate');
    const translatePopup = document.getElementById('translatePopup');
    const translatePopupContent = document.getElementById('translatePopupContent');
    const translatePopupClose = document.getElementById('translatePopupClose');

    menuTranslate.addEventListener('click', async () => {
        const html = await (await fetch('/translate-popup')).text();
        translatePopupContent.innerHTML = html;
        translatePopup.style.display = 'flex';
        translatePopup.setAttribute('aria-hidden', 'false');
    });

    translatePopupClose.addEventListener('click', () => {
        translatePopup.style.display = 'none';
        translatePopup.setAttribute('aria-hidden', 'true');
    });

    translatePopup.addEventListener('click', e => {
        if (e.target === translatePopup) {
            translatePopup.style.display = 'none';
            translatePopup.setAttribute('aria-hidden', 'true');
        }
    });
}
