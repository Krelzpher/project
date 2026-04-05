// Navigation logic adapted for multi-page architecture
function navigateTo(pageId) {
    // We use index.html for the home page
    const targetUrl = pageId === 'home' ? 'index.html' : pageId + '.html';
    window.location.href = targetUrl;
}

// Attach click listeners to nav items
document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', (e) => {
        const page = e.currentTarget.dataset.page;
        if (page) navigateTo(page);
    });
});

// Set the active sidebar item on page load based on current URL
window.addEventListener('load', () => {
    const path = window.location.pathname;
    const filename = path.split('/').pop() || 'index.html';

    // Map index.html back to 'home' to match dataset
    const activePageId = filename === 'index.html' ? 'home' : filename.replace('.html', '');

    document.querySelectorAll('.nav-item').forEach(item => {
        if (item.dataset.page === activePageId) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
});