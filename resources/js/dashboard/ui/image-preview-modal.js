export function initImagePreviewModal() {
    const modal = document.getElementById('imagePreviewModal');
    const modalImage = document.getElementById('imagePreviewModalTarget');
    const closeBtn = document.getElementById('closeImagePreviewModal');

    if (!modal || !modalImage || !closeBtn) return;

    const closeModal = () => {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        modalImage.src = '';
    };

    const openModal = (src) => {
        modalImage.src = src;
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
    };

    // Event delegation for clicks on elements with data-zoomable
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-zoomable]');
        if (!trigger) return;

        // If the target is an image, use its src, otherwise try to find an img child
        let src = '';
        if (trigger.tagName === 'IMG') {
            src = trigger.src;
        } else {
            src = trigger.getAttribute('data-zoomable') || trigger.querySelector('img')?.src;
        }

        if (src) {
            event.preventDefault();
            openModal(src);
        }
    });

    closeBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', (event) => {
        // If clicking backdrop (modal itself)
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('open')) {
            closeModal();
        }
    });
}
