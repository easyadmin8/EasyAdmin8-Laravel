document.addEventListener('DOMContentLoaded', () => {
    const app = document.querySelector('[data-website-app]');
    if (!app) return;

    const toggle = app.querySelector('[data-menu-toggle]');
    const drawer = app.querySelector('[data-menu-drawer]');
    const closer = app.querySelector('[data-menu-close]');

    const closeDrawer = () => {
        if (!drawer || !toggle) return;
        drawer.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('is-menu-open');
    };

    if (toggle && drawer) {
        toggle.addEventListener('click', () => {
            const isOpen = drawer.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            document.body.classList.toggle('is-menu-open', isOpen);
        });
    }

    if (closer) closer.addEventListener('click', closeDrawer);
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) closeDrawer();
    });
});
