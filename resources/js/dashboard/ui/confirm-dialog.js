export function initConfirmDialog() {
    const modal = document.getElementById('confirmActionModal');
    const titleEl = document.getElementById('confirmActionTitle');
    const messageEl = document.getElementById('confirmActionMessage');
    const cancelBtn = document.getElementById('confirmActionCancel');
    const submitBtn = document.getElementById('confirmActionSubmit');

    if (!modal || !titleEl || !messageEl || !cancelBtn || !submitBtn) return;

    let pendingForm = null;

    const closeModal = () => {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        pendingForm = null;
    };

    const openModal = (form) => {
        pendingForm = form;

        const title = form.getAttribute('data-confirm-title') || 'Konfirmasi';
        const message = form.getAttribute('data-confirm-message') || 'Lanjutkan aksi ini?';

        titleEl.textContent = title;
        messageEl.textContent = message;

        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
    };

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-confirm-submit="true"]');
        if (!form) return;

        event.preventDefault();
        openModal(form);
    });

    cancelBtn.addEventListener('click', closeModal);

    submitBtn.addEventListener('click', () => {
        if (!pendingForm) {
            closeModal();
            return;
        }

        const form = pendingForm;
        closeModal();
        form.submit();
    });

    modal.addEventListener('click', (event) => {
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
