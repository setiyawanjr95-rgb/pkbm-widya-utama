// Menu HP: buka/tutup navigasi
document.querySelectorAll('[data-menu-toggle]').forEach((button) => {
    const menu = document.getElementById(button.dataset.menuToggle);
    button.addEventListener('click', () => {
        const open = menu.classList.toggle('hidden') === false;
        button.setAttribute('aria-expanded', String(open));
    });
});

// Galeri: klik foto untuk lihat ukuran besar
const lightbox = document.getElementById('lightbox');
if (lightbox) {
    const img = lightbox.querySelector('img');
    const caption = lightbox.querySelector('[data-caption]');
    document.querySelectorAll('[data-lightbox]').forEach((el) => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            img.src = el.getAttribute('href');
            img.alt = el.dataset.title || '';
            caption.textContent = el.dataset.title || '';
            lightbox.showModal();
        });
    });
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox || e.target.closest('[data-close]')) lightbox.close();
    });
}
