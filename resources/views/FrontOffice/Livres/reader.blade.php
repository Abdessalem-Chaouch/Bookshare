@extends('baseF')

@section('content')


<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="module" src="{{ asset('assets/js/pdfmain.js') }}"></script>

<!-- HEADER -->
<div class="book-header">
  <span class="logo-text">📖{{ $title }}</span>
  <div id="pageCounter" class="page-counter">0 / 0</div>
</div>

   

<audio id="narratorPlayer" class="tool-btn" controls style="display:none;  position:absolute; top:340px; right:80px;"></audio>

<!-- FLOATING TOOLBAR (auto-hide) -->

<div id="fullscreenWrapper" >
     
  <div class="readerWrapper" id="readerWrapper">
   
    <div id="book-container" style="position: relative;">
      <!-- Loader -->
      <div id="pdfLoader" style="
        position: absolute;
        inset: 0;
        background: rgba(255, 244, 240, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #d1a085;
        font-family: 'SF Pro Display', sans-serif;
        z-index: 10;">
        Please be patient... Loading your book 📖
      </div>



    </div>
  </div>

<div style="position:absolute; top:540px; right:220px;">
  <button id="showNotesBtn" style="color:#d1a085;" class="show-notes-btn" title="notes">
    <i class="fa-regular fa-comment"></i>
  </button>
</div>

<div class="notes-popup" id="notesPopupshow">
  <div class="notes-header">
    <h3 class="notes-title">Reading Notes</h3>
    <button class="close-btn" id="notesPopupClose" title="Close">×</button>
  </div>
  <div class="notes-content" id="notesContent">
    <div class="note-date" id="noteDate"></div>
    <div class="note-text" id="noteText"></div>
    <div class="note-action" id="noteAction"></div>
  </div>
</div>



  <div class="nav-row">
    <a href="#" id="prevPage" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Previous Page</a>
    <a href="#" id="nextPage" class="btn-outline">Next Page <i class="fa-solid fa-arrow-right"></i></a>
  </div>



 

<div id="bookmarksidebarMark">
  <div class="sidebar-header">
    <h3>Mark page</h3>
    <button id="closesidebarMark" class="tool-btn" title="Close">&times;</button>
  </div>

  <hr>

  <div class="marker-info">
    <div class="info-block">
      <span class="label">Page</span>
      <span id="sidebarMarkPage" class="value">0</span>
    </div>
    <div class="info-block">
      <span class="label">Y Position</span>
      <span id="sidebarMarkLine" class="value">0 px</span>
    </div>
    <div class="info-block">
      <span class="label">X Position</span>
      <span id="sidebarMarkCol" class="value">0 px</span>
    </div>
  </div>
  <div class="sidebar-header marker-info ">
    <h3>Actions</h3>
    <div></div>
  </div>
  <div id="marksList"></div>
</div>


  <aside class="sidebarleft">
    <div class="logoSidebarLeft">
      <img src="{{ asset('storage/' . $livre->photo_couverture) }}" alt="logoSidebarLeft" />
      <h2>My Book</h2>
    </div>

    <ul class="links">
      <hr />
      <h4>Menu</h4>

      <li id="menu-start" class="menu-item">
        <span class="material-symbols-outlined">menu_book</span>
        <a href="javascript:void(0)">Start Reading</a>
      </li>

      <li id="menu-progress" class="menu-item">
        <span class="material-symbols-outlined">trending_up</span>
        <a href="javascript:void(0)">Progress</a>
      </li>

      <li id="menu-notes" class="menu-item">
        <span class="material-symbols-outlined">note_alt</span>
        <a href="javascript:void(0)">Notes</a>
      </li>
      <li data-action="goals" id="menu-goals">
        <span class="material-symbols-outlined">flag</span>
        <a href="javascript:void(0)">Reading Goals</a>
      </li>

      <li data-action="translate" id="menu-translate">
        <span class="material-symbols-outlined">translate</span>
        <a href="javascript:void(0)">Translate</a>
      </li>

      <li data-action="search" id="menu-search">
        <span class="material-symbols-outlined">search</span>
        <a href="javascript:void(0)">Search</a>
      </li>
    </ul>
  </aside>


<div id="readingPopup" class="popup-overlay" aria-hidden="true">
  <div class="popup-content" role="dialog" aria-modal="true">
    <button id="readingPopupClose" class="close-btn">&times;</button>
    <div id="readingPopupContent">
    </div>
  </div>
</div>

<!-- Notes Popup (kept in Blade) -->
<div id="notesPopup" class="popup-overlay" aria-hidden="true" style="display:none;">
  <div class="popup-content" role="dialog" aria-modal="true">
    <button id="notesPopupCloseside" class="close-btn" aria-label="Close">&times;</button>
    <div id="notesPopupContent">
      <!-- Contenu chargé dynamiquement ici (via /notes-popup) -->
    </div>
  </div>
</div>

<!-- Popup containers -->
<div id="searchPopup" class="popup-overlay" aria-hidden="true">
   <div class="popup-content" role="dialog" aria-modal="true">
    <button id="searchPopupClose" class="close-btn">&times;</button>
    <div id="searchPopupContent">
    </div>
  </div>
</div>
<div id="translatePopup" class="popup-overlay" aria-hidden="true">
   <div class="popup-content" role="dialog" aria-modal="true">
    <button id="translatePopupClose" class="close-btn">&times;</button>
    <div id="translatePopupContent">
    </div>
  </div>
</div>




<div id="floatingToolbar" class="floating-toolbar">
  <button id="btnZoomOut" style="color: #d1a085;" class="tool-btn" title="Zoom out"><i class="fa-solid fa-magnifying-glass-minus"></i></button>
  <button id="btnZoomIn" style="color: #d1a085;" class="tool-btn" title="Zoom in"><i class="fa-solid fa-magnifying-glass-plus"></i></button>
  <button id="btnFullscreen" style="color: #d1a085;" class="tool-btn" title="Fullscreen"><i class="fa-solid fa-expand"></i></button>
  <button id="btnTheme" style="color: #d1a085;"  class="tool-btn" title="Toggle theme"><i id="themeIcon" class="fa-solid fa-moon"></i></button>
  <button id="btnSound" style="color: #d1a085;" class="tool-btn" title="Toggle page sound"><i id="soundIcon" class="fa-solid fa-volume-high"></i></button>
  <button id="btnBookmark" style="color: #d1a085;" class="tool-btn" title="Mark the page"><i id="bookmarkIcon" class="fa-regular fa-bookmark"></i></button>
<button id="btnVoiceDetect" style="color: #d1a085;" class="tool-btn" title="Voice Detection"><i class="fa-solid fa-microphone"></i></button>
<button id="readPageBtn" style="color: #d1a085;" class="tool-btn" title="Play Audio"> <i class="fa-solid fa-play-circle"></i></button>
<button id="pauseBtn" style="color: #d1a085;" class="tool-btn" title="Pause Audio">
    <i class="fa-solid fa-pause-circle"></i>
</button>
<button id="summarizeBtn" style="color: #d1a085;" class="tool-btn" title="Summarize">
    <i class="fa-solid fa-file-lines"></i>
</button>

<!-- AI Navigation Bar -->
<div id="aiNavbar" class="ai-navbar">
    <div class="resumer-resumer-navbar-content">
        <h4>Choose AI Action</h4>
        <button class="resumer-nav-option" onclick="openSummary('all')">
            <span>📚</span>
            Summarize All Pages
        </button>
        <button class="resumer-nav-option" onclick="openSummary('page')">
            <span>📖</span>
            Summarize This Page
        </button>
    </div>
</div>




   
</div>

<!-- Bouton pour ouvrir le chat -->
<button class="chat-toggle-btn" onclick="toggleChat()">
    <i class="fa-solid fa-book-open"></i>
</button>

<!-- Chatbot -->
<div id="askChatBox" class="ask-chatbox hidden">
   <div class="ask-header">
    <h3>⚡ AI Chat</h3><button class="close-chat-btn" onclick="toggleChat()">✖</button>
   </div>
   <div id="askMessages" class="ask-messages">
    <div class="msg ai">
     Bonjour! Je suis votre assistant IA. Comment puis-je vous aider aujourd'hui?
    </div>
   </div>
   <div class="typing-indicator" id="typingIndicator">
    <div class="typing-dots"><span></span> <span></span> <span></span>
    </div>
   </div>
   <div class="ask-input-area"><input type="text" id="messageInput" placeholder="Posez votre question ici..." onkeypress="handleEnter(event)"> <button id="sendMessageBtn" onclick="sendMessage()">➤</button>
   </div>
  </div>



<!-- Summary Chat Sidebar -->
<div id="summarySidebar">
    <div class="summary-header">
        <h3 id="summaryTitle">🤖 AI Summarizer</h3>
        <button class="close-btn" onclick="closeSummary()">&times;</button>
    </div>

    <div class="chat-messages" id="chatMessages">
        <!-- Résumé s’affichera ici -->
    </div>
</div>
<!--
<div id="messagerieContainer" class="messagerie">
    <div id="messages" class="messages"></div>
    <input type="text" id="messageInput" placeholder="Posez votre question ici..." />
    <button id="sendMessageBtn">Envoyer</button>
</div>




 Chat <button class="chat-toggle-btn" onclick="toggleChat()">
    <i class="fa-solid fa-book-open"></i>
</button>


   
    <div id="chatSidebar">
        <div class="chat-header">
            <h3>📖 Chat Assistant Book</h3>
            <button class="close-btn" onclick="closeChat()">&times;</button>
        </div>
        
        <div class="chat-messages" id="chatMessages">
            <div class="message ai">
                Hello! I'm your Chat Assistant Book. I'm here to help you with any questions or assistance you need. How can I help you today?
            </div>
        </div>

        <div class="typing-indicator" id="typingIndicator">
            <div class="typing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <div class="chat-input-container" style="padding: 20px;
            background: transparent;
            border-top: 1px solid #d4c4a8;
            position: relative;
            z-index: 1;">
            <div style="position: relative; display: flex; align-items: center; gap: 10px;">
               <input 
                  type="text"       
                  id="chatInput" 
                  placeholder="Type your message here..." 
                  style="
                      width: calc(100% - 60px);
                      padding: 14px 20px;
                      border: 2px solid #8b7355;
                      border-radius: 25px;
                      font-size: 14px;
                      outline: none;
                      transition: all 0.3s ease;
                      background: #faf7f0;
                      color: #3d2914;
                      box-shadow: inset 0 2px 5px rgba(139,115,85,0.1);
                      box-sizing: border-box;
                      "
                    >
<button 
    class="send-btn"
    style="
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        background: #8b7355;
        color: #faf7f0;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(139,115,85,0.3);
        font-size: 16px;
    "
>➤</button>

            </div>
        </div>
    </div>
Sidebar -->




<script>
    window.BookConfig = {
        pdfUrl: @json($pdfUrl),
        userName: @json(auth()->user()->email ?? 'USER'),
        livreId: @json($livre->id ?? null),
        livre:@json($livre),
        csrfToken: '{{ csrf_token() }}',
        bookUrl: @json($pdfUrl),
        secondsSpent: Number(@json($livre->reading_time ? $livre->reading_time * 60 : 0)) || 0,
        coverUrl:'{{ $livre->photo_couverture ? asset('storage/' . $livre->photo_couverture) : '' }}',
       flipAudioUrl: "{{ asset('assets/video/flipAudio.m4a') }}",
  };
  function toggleChat() {
    const chatBox = document.getElementById("askChatBox");
    chatBox.classList.toggle("hidden");
}

</script>

<link rel="stylesheet" href="{{ asset('BookView.css') }}">
<link rel="stylesheet" href="{{ asset('notes.css') }}">

@endsection



