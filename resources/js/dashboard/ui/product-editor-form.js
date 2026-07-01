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
        const existingItem = document.getElementById('existingPhotoItem');
        const removeCheckbox = document.getElementById('removePhotoCheckbox');
        
        // Count if the existing item is visible and not checked for removal
        if (existingItem && existingItem.style.display !== 'none' && (!removeCheckbox || !removeCheckbox.checked)) {
            return 1;
        }
        return 0;
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

    const handlePhotoFileChange = () => {
        if (!photoFileInput || !photoList) return;
        
        const file = photoFileInput.files?.[0];
        if (file) {
            const objectUrl = URL.createObjectURL(file);
            
            // Clean up any old preview
            let newPreview = document.getElementById('newPhotoPreview');
            if (newPreview) {
                const img = newPreview.querySelector('img');
                if (img && img.src.startsWith('blob:')) {
                    URL.revokeObjectURL(img.src);
                }
                newPreview.remove();
            }
            
            // Create preview item dynamically
            newPreview = document.createElement('div');
            newPreview.className = 'product-photo-item';
            newPreview.id = 'newPhotoPreview';
            newPreview.innerHTML = `
                <div class="product-photo-thumb cursor-zoom-in transition-all duration-300 hover:scale-[1.03]" data-zoomable>
                    <img src="${objectUrl}" alt="Preview Foto Baru">
                </div>
                <div class="product-photo-meta">
                    <p class="product-photo-name">${file.name}</p>
                </div>
                <div class="product-photo-actions">
                    <button type="button" class="product-photo-remove" id="btnCancelNewPhoto">
                        Batal
                    </button>
                </div>
            `;
            
            const addButton = photoList.querySelector('.product-photo-add');
            if (addButton) {
                photoList.insertBefore(newPreview, addButton);
            } else {
                photoList.appendChild(newPreview);
            }
            
            // Hide the existing photo item since it's going to be replaced
            const existingPhotoItem = document.getElementById('existingPhotoItem');
            if (existingPhotoItem) {
                existingPhotoItem.style.display = 'none';
            }
        }
        applyPhotoState();
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

    // Listeners
    hasDimensionInput?.addEventListener('change', applyDimensionState);
    hasPhotoInput?.addEventListener('change', applyPhotoState);
    photoFileInput?.addEventListener('change', handlePhotoFileChange);
    typeSelect?.addEventListener('change', applyTypeState);

    // Cancel new photo handler (event delegation)
    photoList?.addEventListener('click', (event) => {
        const btnCancel = event.target.closest('#btnCancelNewPhoto');
        if (!btnCancel) return;

        const newPreview = document.getElementById('newPhotoPreview');
        if (newPreview) {
            const img = newPreview.querySelector('img');
            if (img && img.src.startsWith('blob:')) {
                URL.revokeObjectURL(img.src);
            }
            newPreview.remove();
        }

        if (photoFileInput) {
            photoFileInput.value = '';
        }

        const existingPhotoItem = document.getElementById('existingPhotoItem');
        if (existingPhotoItem) {
            existingPhotoItem.style.display = '';
        }

        const removeCheckbox = document.getElementById('removePhotoCheckbox');
        if (removeCheckbox) {
            removeCheckbox.checked = false;
            existingPhotoItem?.classList.remove('opacity-50');
        }

        applyPhotoState();
    });

    // Checkbox styling change
    const removeCheckbox = document.getElementById('removePhotoCheckbox');
    const existingPhotoItem = document.getElementById('existingPhotoItem');
    if (removeCheckbox && existingPhotoItem) {
        removeCheckbox.addEventListener('change', () => {
            existingPhotoItem.classList.toggle('opacity-50', removeCheckbox.checked);
            applyPhotoState();
        });
    }

    applyDimensionState();
    applyPhotoState();
    applyTypeState();
}
