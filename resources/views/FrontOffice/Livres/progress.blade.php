



    
    <div class="journal-container">
        <div class="corner-ornament top-left"></div>
        <div class="corner-ornament top-right"></div>
        <div class="corner-ornament bottom-left"></div>
        <div class="corner-ornament bottom-right"></div>
        
        <div class="journal-header">
            <h1 class="journal-title">Reading Tracker</h1>
            <p class="journal-subtitle">Track your reading progress</p>
            <div class="decorative-border"></div>
        </div>
        
        <div class="reading-section">


            <div class="reading-time-stats">
                <p class="stat-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle; margin-right: 5px;">
                        <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L11,13V7H12.5V12.2L17,14.7L16.2,16.2Z"/>
                    </svg>
                    Estimated reading time: <span id="reading-time">--</span>
                </p>
                <p class="stat-item">Progress: <span id="reading-progress">0%</span></p>
            </div>

            <div class="containerBookFill">
                <div class="container">
                    <div class="book">
                        <div class="cover-fill" id="cover-fill"></div>
                    </div>
                </div>
            </div>

            <div class="popup-actions">
                <button id="btnToggleReading" class="tool-btn-notes">Start Reading</button>
                <button id="btnResetProgress" class="tool-btn-notes">Reset</button>
            </div>
        </div>
    </div>

 <link rel="stylesheet" href="{{ asset('notes.css') }}">
