export function initProductEditorForm() {
    const form = document.getElementById('productForm');
    if (!form) return;

    const hasDimensionInput = document.getElementById('productHasDimension');
    const dimensionFields = document.getElementById('productDimensionFields');
    const dimensionToggleState = document.getElementById('dimensionToggleState');

    const hasPhotoInput = document.getElementById('productHasPhoto');
    const photoFields = document.getElementById('productPhotoFields');
    const photoToggleState = document.getElementById('photoToggleState');
    const photoFileInput = document.getElementById('productPhotoFile');
    const photoCurrentText = document.getElementById('productPhotoCurrent');
    const photoList = document.getElementById('productPhotoList');

    const typeSelect = document.getElementById('productType');
    const stockInput = document.getElementById('productStock');

    const applyDimensionState = () => {
        if (!hasDimensionInput || !dimensionFields) return;

        const enabled = hasDimensionInput.checked;
        dimensionFields.disabled = !enabled;
        dimensionFields.classList.toggle('is-disabled', !enabled);
        dimensionFields.classList.toggle('is-enabled', enabled);

        if (dimensionToggleState) {
            dimensionToggleState.textContent = enabled ? 'Enabled' : 'Disabled';
            dimensionToggleState.classList.toggle('on', enabled);
            dimensionToggleState.classList.toggle('off', !enabled);
        }
    };

    const countExistingPhotos = () => {
        if (!photoList) return 0;
        return photoList.querySelectorAll('.product-photo-item').length;
    };

    const applyPhotoState = () => {
        if (!hasPhotoInput || !photoFields) return;

        const enabled = hasPhotoInput.checked;
        photoFields.disabled = !enabled;
        photoFields.classList.toggle('is-disabled', !enabled);
        photoFields.classList.toggle('is-enabled', enabled);

        if (photoToggleState) {
            photoToggleState.textContent = enabled ? 'Enabled' : 'Disabled';
            photoToggleState.classList.toggle('on', enabled);
            photoToggleState.classList.toggle('off', !enabled);
        }

        if (!enabled && photoCurrentText) {
            photoCurrentText.textContent = 'Fitur foto nonaktif.';
            return;
        }

        const existing = countExistingPhotos();
        const selected = photoFileInput?.files?.length || 0;

        if (photoCurrentText) {
            if (selected > 0) {
                photoCurrentText.textContent = `${existing} foto tersimpan, ${selected} foto baru dipilih.`;
            } else {
                photoCurrentText.textContent = `${existing} foto tersimpan.`;
            }
        }
    };

    const applyTypeState = () => {
        if (!typeSelect || !stockInput) return;

        const isNonStock = typeSelect.value === 'non-stock';
        stockInput.disabled = isNonStock;
        stockInput.classList.toggle('is-field-disabled', isNonStock);
        if (isNonStock) {
            stockInput.value = '0';
        }
    };

    hasDimensionInput?.addEventListener('change', applyDimensionState);
    hasPhotoInput?.addEventListener('change', applyPhotoState);
    photoFileInput?.addEventListener('change', applyPhotoState);
    typeSelect?.addEventListener('change', applyTypeState);

    applyDimensionState();
    applyPhotoState();
    applyTypeState();
}
