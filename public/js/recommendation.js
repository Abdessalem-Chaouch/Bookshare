function loadRecommendation() {
    fetch('/recommendation')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showRecommendation(data.data);
            }
        })
        .catch(error => console.log('Erreur:', error));
}

function showRecommendation(data) {
    const container = document.getElementById('recommendation-container');
    if (!container) return;

    const html = `
        <div class="alert alert-info">
            <h5>🤖 Recommandation AI</h5>
            <p><strong>Votre usage:</strong> ${data.current_usage.emprunts_mois} emprunts/mois, Budget: ${data.current_usage.budget}€</p>
            <p><strong>Recommandation:</strong> ${data.recommendation}</p>
            <p>${data.message}</p>
        </div>
    `;
    
    container.innerHTML = html;
}

// Auto-load si utilisateur connecté
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('[data-user-authenticated]')) {
        loadRecommendation();
    }
});