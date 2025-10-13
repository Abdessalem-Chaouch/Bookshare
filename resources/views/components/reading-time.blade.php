<div class="reading-time-stats" style="margin: 12px 0; font-family: 'SF Pro Display'; color: #d1a085;">
    <p>⏱ Temps de lecture estimé : <strong>{{ $readingTimeReadable }}</strong></p>
    <p>Progression : <strong id="reading-progress">0%</strong></p>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const totalPages = {{ $totalPages ?? 0 }};
    const totalMinutes = {{ $readingTimeMinutes ?? 0 }};
    const progressElem = document.getElementById('reading-progress');

    if (!window.pageFlip) return;

    window.pageFlip.on('flip', (e) => {
        const currentPage = e.data + 1; // index 0 → page 1
        let percent = Math.round((currentPage / totalPages) * 100);
        progressElem.textContent = percent + '%';
    });
});
</script>
