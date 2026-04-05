function navigateTo(pageId) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active-page'));

    const activePage = document.getElementById(pageId);
    if (activePage) activePage.classList.add('active-page');

    document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));

    const navItem = Array.from(document.querySelectorAll('.nav-item')).find(el => el.dataset.page === pageId);
    if (navItem) navItem.classList.add('active');
}


document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', (e) => {
        const page = e.currentTarget.dataset.page;
        if (page) navigateTo(page);
    });
});

window.addEventListener('load', () => {

    const activeNow = document.querySelector('.page.active-page')?.id;
    if (activeNow) {
        const navToActive = Array.from(document.querySelectorAll('.nav-item')).find(el => el.dataset.page === activeNow);
        if (navToActive) navToActive.classList.add('active');
    } else {
        navigateTo('home');
    }
});