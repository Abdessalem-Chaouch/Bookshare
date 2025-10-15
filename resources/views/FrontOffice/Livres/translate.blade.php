
       
    <div class="journal-container">
        <div class="corner-ornament top-left"></div>
        <div class="corner-ornament top-right"></div>
        <div class="corner-ornament bottom-left"></div>
        <div class="corner-ornament bottom-right"></div>
        
        <div class="journal-header">
            <h1 class="journal-title">Translation Journal</h1>
            <p class="journal-subtitle">Bridge languages, connect worlds</p>
            <div class="decorative-border"></div>
        </div>
        
        <div class="journal-content">
            <div class="language-section">
                <select class="language-select" id="fromLang">
                    <option value="en">English</option>
                    <option value="fr">Français</option>
                    <option value="es">Español</option>
                    <option value="de">Deutsch</option>
                    <option value="it">Italiano</option>
                    <option value="pt">Português</option>
                    <option value="ru">Русский</option>
                    <option value="ja">日本語</option>
                    <option value="ko">한국어</option>
                    <option value="zh">中文</option>
                    <option value="ar">العربية</option>
                </select>
                
                <button class="swap-btn" onclick="swapLanguages()" title="Swap languages">⇄</button>
                
                <select class="language-select" id="toLang">
                    <option value="fr">Français</option>
                    <option value="en">English</option>
                    <option value="es">Español</option>
                    <option value="de">Deutsch</option>
                    <option value="it">Italiano</option>
                    <option value="pt">Português</option>
                    <option value="ru">Русский</option>
                    <option value="ja">日本語</option>
                    <option value="ko">한국어</option>
                    <option value="zh">中文</option>
                    <option value="ar">العربية</option>
                </select>
            </div>
            
            <div class="translation-area">
                <div class="area-label">Original Text</div>
                <div class="page-lines">
                    <textarea class="text-area" placeholder="Enter text to translate..." id="originalText"></textarea>
                </div>
            </div>
            
            <div class="translation-area">
                <div class="area-label">Translation</div>
                <div class="page-lines">
                    <textarea class="text-area" placeholder="Write your translation here..." id="translatedText"></textarea>
                </div>
            </div>
        </div>
        
        
    </div>

    <script>
        let translations = [];

        function swapLanguages() {
            const fromLang = document.getElementById('fromLang');
            const toLang = document.getElementById('toLang');
            const originalText = document.getElementById('originalText');
            const translatedText = document.getElementById('translatedText');
            
            // Swap language selections
            const tempLang = fromLang.value;
            fromLang.value = toLang.value;
            toLang.value = tempLang;
            
            // Swap text content
            const tempText = originalText.value;
            originalText.value = translatedText.value;
            translatedText.value = tempText;
        }

        function saveTranslation() {
            const fromLang = document.getElementById('fromLang').value;
            const toLang = document.getElementById('toLang').value;
            const originalText = document.getElementById('originalText').value;
            const translatedText = document.getElementById('translatedText').value;
            
            if (originalText.trim() && translatedText.trim()) {
                const translation = {
                    id: Date.now(),
                    fromLang: fromLang,
                    toLang: toLang,
                    original: originalText,
                    translated: translatedText,
                    date: new Date().toLocaleDateString()
                };
                
                translations.push(translation);
                localStorage.setItem('translations', JSON.stringify(translations));
                
                alert('Translation saved successfully! 🌍');
            } else {
                alert('Please fill in both original text and translation.');
            }
        }

        function clearAll() {
            if (confirm('Clear both text areas?')) {
                document.getElementById('originalText').value = '';
                document.getElementById('translatedText').value = '';
            }
        }

        function copyTranslation() {
            const translatedText = document.getElementById('translatedText').value;
            if (translatedText.trim()) {
                navigator.clipboard.writeText(translatedText).then(() => {
                    alert('Translation copied to clipboard! 📋');
                }).catch(() => {
                    alert('Could not copy to clipboard');
                });
            } else {
                alert('No translation to copy');
            }
        }

        function printPage() {
            window.print();
        }

        // Load saved translations on page load
        window.onload = function() {
            const saved = localStorage.getItem('translations');
            if (saved) {
                translations = JSON.parse(saved);
            }
        }

        // Auto-save as user types
        let saveTimeout;
        function autoSave() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                const originalText = document.getElementById('originalText').value;
                const translatedText = document.getElementById('translatedText').value;
                if (originalText.trim() || translatedText.trim()) {
                    localStorage.setItem('currentTranslation', JSON.stringify({
                        original: originalText,
                        translated: translatedText
                    }));
                }
            }, 1000);
        }

        document.getElementById('originalText').addEventListener('input', autoSave);
        document.getElementById('translatedText').addEventListener('input', autoSave);

        // Load current work
        const currentWork = localStorage.getItem('currentTranslation');
        if (currentWork) {
            const work = JSON.parse(currentWork);
            document.getElementById('originalText').value = work.original;
            document.getElementById('translatedText').value = work.translated;
        }
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'98c7ca11365cea4b',t:'MTc2MDExNjgzNi4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script>
 <link rel="stylesheet" href="{{ asset('notes.css') }}">

