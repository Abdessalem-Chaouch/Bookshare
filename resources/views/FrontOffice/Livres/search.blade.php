<div class="search-body">
    <div class="search-section">
        <div class="search-container">
            <input type="text" class="search-bar" id="searchBar" placeholder="Search your content..." onkeyup="handleSearch(event)">
            <button class="search-btn" onclick="performSearch()" title="Search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
            </button>
        </div>
    </div>

    <script>
        function performSearch() {
            const searchTerm = document.getElementById('searchBar').value.trim();
            
            if (!searchTerm) {
                alert('Please enter a search term');
                return;
            }
            
            // Here you can add your search logic
            alert(`Searching for: "${searchTerm}"`);
            console.log('Search term:', searchTerm);
        }

        function handleSearch(event) {
            if (event.key === 'Enter') {
                performSearch();
            }
        }

        // Focus the search bar when page loads
        window.onload = function() {
            document.getElementById('searchBar').focus();
        }
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'98c84106c3fdfb9e',t:'MTc2MDEyMTcwOC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script>

 <link rel="stylesheet" href="{{ asset('notes.css') }}">
