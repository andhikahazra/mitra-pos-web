export function initFlashMessages() {
    const flashes = Array.from(document.querySelectorAll('.flash-message[data-auto-dismiss]'));
    if (!flashes.length) return;

    flashes.forEach((flash) => {
        const timeout = Number(flash.getAttribute('data-auto-dismiss') || 5000);

        window.setTimeout(() => {
            flash.classList.add('is-hiding');

            window.setTimeout(() => {
                flash.remove();
            }, 220);
        }, timeout);
    });
}
