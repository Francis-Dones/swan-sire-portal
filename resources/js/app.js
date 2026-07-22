// Percentage calculation helpers
window.calculatePercentage = (value, total) => {
    if (total === 0) return 0;
    return ((value / total) * 100).toFixed(1);
};

// Update progress bars dynamically
window.updateProgressBars = () => {
    document.querySelectorAll('.dynamic-progress').forEach(element => {
        const yes = parseInt(element.dataset.yes) || 0;
        const total = parseInt(element.dataset.total) || 0;
        const percentage = calculatePercentage(yes, total);
        
        const progressBar = element.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = percentage + '%';
            progressBar.textContent = percentage + '%';
        }
    });
};

// Color coding based on percentage
window.getPercentageColor = (percentage) => {
    if (percentage >= 90) return 'success';
    if (percentage >= 70) return 'info';
    if (percentage >= 50) return 'warning';
    return 'danger';
};

// Auto-refresh statistics (optional)
if (document.getElementById('auto-refresh-stats')) {
    setInterval(() => {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newStats = doc.querySelector('.statistics-container');
                const currentStats = document.querySelector('.statistics-container');
                if (newStats && currentStats) {
                    currentStats.innerHTML = newStats.innerHTML;
                }
            });
    }, 30000); // Refresh every 30 seconds
}