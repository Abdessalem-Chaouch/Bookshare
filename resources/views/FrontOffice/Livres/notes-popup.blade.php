
<div class="notesbody">
    <div class="journal-container">
        <div class="corner-ornament top-left"></div>
        <div class="corner-ornament top-right"></div>
        <div class="corner-ornament bottom-left"></div>
        <div class="corner-ornament bottom-right"></div>
        
        <div class="journal-header">
            <h1 class="journal-title">Book Notes</h1>
            <p class="journal-subtitle">Capture insights from every page you read</p>
            <div class="decorative-border"></div>
        </div>
        
        <div class="journal-content">
            <div class="date-section">
                <label id="bookTitle" class="bookTitle"></label>
                <input type="hidden" id="bookId" value="">

                <select class="date-input" id="pageNumber" style="width: 80px; margin-right: 8px; font-size: 0.8rem; padding: 6px 8px;">
                    <option value="">Page</option>
                </select>
                <input type="date" class="date-input" id="entryDate" style="width: 100px; font-size: 0.8rem; padding: 6px 8px;">
            </div>
            
            <div class="page-lines">
                <textarea class="writing-area" placeholder="Write your notes about this page here..." id="journalText"></textarea>
            </div>
        </div>
        
        <div class="journal-tools">
            <button class="notes-btn-form" onclick="saveNote()">Save Note</button>
            <button class="notes-btn-form" onclick="clearPage()">Clear Page</button>
        </div>
    </div>
</div>
    <script>
        // Set today's date by default
        document.getElementById('entryDate').valueAsDate = new Date();
        
        let notes = [];

    
        function clearPage() {
            if (confirm('Are you sure you want to clear this page?')) {
                document.getElementById('journalText').value = '';
            }
        }

        function printNotes() {
            window.print();
        }

  

        // Load saved notes on page load
        window.onload = function() {
            const saved = localStorage.getItem('bookNotes');
            if (saved) {
                notes = JSON.parse(saved);
            }
            populatePageDropdown();
        }

        // Auto-save as user types
        let saveTimeout;
        document.getElementById('journalText').addEventListener('input', function() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                const text = this.value;
                if (text.trim()) {
                    localStorage.setItem('currentNote', text);
                }
            }, 1000);
        });

        // Load current note
        const currentNote = localStorage.getItem('currentNote');
        if (currentNote) {
            document.getElementById('journalText').value = currentNote;
        }
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'98c7716a35d3ea4b',t:'MTc2MDExMzIwNC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
    <link rel="stylesheet" href="{{ asset('notes.css') }}">
