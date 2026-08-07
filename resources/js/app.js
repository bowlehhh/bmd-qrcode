import { Html5Qrcode } from 'html5-qrcode';

import.meta.glob([
    '../images/**',
]);

const mobileSidebar = document.getElementById('app-sidebar');
const mobileSidebarOverlay = document.getElementById('mobile-sidebar-overlay');
const openMobileSidebarButton = document.getElementById('open-mobile-sidebar');
const closeMobileSidebarButton = document.getElementById('close-mobile-sidebar');

if (mobileSidebar && mobileSidebarOverlay) {
    const setSidebarOpen = (isOpen) => {
        mobileSidebar.classList.toggle('-translate-x-full', !isOpen);
        mobileSidebarOverlay.classList.toggle('hidden', !isOpen);
        document.body.classList.toggle('overflow-hidden', isOpen);
        document.body.classList.toggle('touch-none', isOpen);
        openMobileSidebarButton?.setAttribute('aria-expanded', `${isOpen}`);
        mobileSidebar.setAttribute('aria-hidden', `${window.innerWidth < 1024 && !isOpen}`);
    };

    const openSidebar = () => setSidebarOpen(true);
    const closeSidebar = () => setSidebarOpen(false);

    openMobileSidebarButton?.addEventListener('click', openSidebar);
    closeMobileSidebarButton?.addEventListener('click', closeSidebar);
    mobileSidebarOverlay.addEventListener('click', closeSidebar);

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            closeSidebar();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });
}

const logoutForm = document.querySelector('[data-logout-form]');
const logoutModal = document.getElementById('logout-modal');
const cancelLogoutButton = document.getElementById('cancel-logout');
const confirmLogoutButton = document.getElementById('confirm-logout');
let pendingLogoutForm = null;

if (logoutForm && logoutModal) {
    logoutForm.addEventListener('submit', (event) => {
        event.preventDefault();
        pendingLogoutForm = logoutForm;
        logoutModal.classList.remove('hidden');
        logoutModal.classList.add('flex');
    });

    cancelLogoutButton?.addEventListener('click', () => {
        logoutModal.classList.add('hidden');
        logoutModal.classList.remove('flex');
        pendingLogoutForm = null;
    });

    confirmLogoutButton?.addEventListener('click', () => {
        if (!pendingLogoutForm) {
            return;
        }

        confirmLogoutButton.disabled = true;
        confirmLogoutButton.textContent = 'Memproses...';
        pendingLogoutForm.submit();
    });

    logoutModal.addEventListener('click', (event) => {
        if (event.target === logoutModal) {
            logoutModal.classList.add('hidden');
            logoutModal.classList.remove('flex');
            pendingLogoutForm = null;
        }
    });
}

const loadingOverlay = document.getElementById('loading-overlay');
const loadingForms = document.querySelectorAll('[data-loading-form]');
const deleteForms = document.querySelectorAll('[data-confirm-delete]');
const deleteModal = document.getElementById('delete-modal');
const deleteAssetLabel = document.getElementById('delete-asset-label');
const cancelDeleteButton = document.getElementById('cancel-delete');
const confirmDeleteButton = document.getElementById('confirm-delete');
const backToScanButton = document.getElementById('back-to-scan');
let pendingDeleteForm = null;

const closeDeleteModal = () => {
    deleteModal?.classList.add('hidden');
    deleteModal?.classList.remove('flex');
    pendingDeleteForm = null;
};

if (deleteModal) {
    deleteForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            pendingDeleteForm = form;

            if (deleteAssetLabel) {
                deleteAssetLabel.textContent = form.dataset.assetLabel || 'aset ini';
            }

            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
        });
    });

    cancelDeleteButton?.addEventListener('click', closeDeleteModal);

    confirmDeleteButton?.addEventListener('click', () => {
        if (!pendingDeleteForm) {
            return;
        }

        confirmDeleteButton.disabled = true;
        confirmDeleteButton.textContent = 'Menghapus...';
        pendingDeleteForm.submit();
    });

    deleteModal.addEventListener('click', (event) => {
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    });
}

backToScanButton?.addEventListener('click', () => {
    if (window.history.length > 1) {
        window.history.back();
        return;
    }

    window.location.assign('/');
});

if (loadingOverlay && loadingForms.length > 0) {
    loadingForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.photoIsCompressing === 'true') {
                event.preventDefault();
                window.alert('Foto sedang dikompres. Tunggu sampai proses selesai sebelum menyimpan aset.');

                return;
            }

            loadingOverlay.classList.remove('hidden');
            loadingOverlay.classList.add('flex');

            form.querySelectorAll('button[type="submit"]').forEach((button) => {
                button.setAttribute('disabled', 'disabled');
                button.textContent = 'Memproses...';
            });
        });
    });
}

const photoInputs = document.querySelectorAll('[data-photo-input]');
const maxPhotoBytes = 2 * 1024 * 1024;
const targetPhotoBytes = Math.floor(1.9 * 1024 * 1024);
const maximumPhotoDimension = 2048;

const formatFileSize = (bytes) => `${(bytes / 1024 / 1024).toFixed(2)} MB`;

const loadImage = (file) => new Promise((resolve, reject) => {
    const image = new Image();
    const imageUrl = URL.createObjectURL(file);

    image.addEventListener('load', () => {
        URL.revokeObjectURL(imageUrl);
        resolve(image);
    }, { once: true });
    image.addEventListener('error', () => {
        URL.revokeObjectURL(imageUrl);
        reject(new Error('Foto tidak dapat dibaca.'));
    }, { once: true });
    image.src = imageUrl;
});

const canvasToJpeg = (canvas, quality) => new Promise((resolve) => {
    canvas.toBlob(resolve, 'image/jpeg', quality);
});

const createPhotoCanvas = (image, width, height) => {
    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const context = canvas.getContext('2d');

    context.fillStyle = '#ffffff';
    context.fillRect(0, 0, width, height);
    context.drawImage(image, 0, 0, width, height);

    return canvas;
};

const compressPhoto = async (file) => {
    const image = await loadImage(file);
    const initialScale = Math.min(1, maximumPhotoDimension / Math.max(image.naturalWidth, image.naturalHeight));
    let width = Math.max(1, Math.round(image.naturalWidth * initialScale));
    let height = Math.max(1, Math.round(image.naturalHeight * initialScale));

    for (let resizeAttempt = 0; resizeAttempt < 6; resizeAttempt += 1) {
        const canvas = createPhotoCanvas(image, width, height);
        const initialBlob = await canvasToJpeg(canvas, 0.82);

        if (initialBlob && initialBlob.size <= targetPhotoBytes) {
            return initialBlob;
        }

        let lowestQuality = 0.3;
        let highestQuality = 0.82;
        let bestBlob = null;

        for (let qualityAttempt = 0; qualityAttempt < 7; qualityAttempt += 1) {
            const quality = (lowestQuality + highestQuality) / 2;
            const blob = await canvasToJpeg(canvas, quality);

            if (!blob) {
                break;
            }

            if (blob.size <= targetPhotoBytes) {
                bestBlob = blob;
                lowestQuality = quality;
            } else {
                highestQuality = quality;
            }
        }

        if (bestBlob) {
            return bestBlob;
        }

        width = Math.max(480, Math.round(width * 0.75));
        height = Math.max(480, Math.round(height * 0.75));
    }

    throw new Error('Foto tidak dapat dikompres hingga ukuran maksimal 2 MB.');
};

photoInputs.forEach((input) => {
    const form = input.closest('form');
    const feedback = input.parentElement?.querySelector('[data-photo-feedback]');

    if (form && !form.hasAttribute('data-loading-form')) {
        form.addEventListener('submit', (event) => {
            if (form.dataset.photoIsCompressing === 'true') {
                event.preventDefault();
                window.alert('Foto sedang dikompres. Tunggu sampai proses selesai sebelum menyimpan aset.');
            }
        });
    }

    const setFeedback = (message, type = 'info') => {
        if (!feedback) {
            return;
        }

        feedback.textContent = message;
        feedback.classList.remove('hidden', 'text-slate-500', 'text-cyan-700', 'text-rose-600');
        feedback.classList.add(type === 'error' ? 'text-rose-600' : (type === 'success' ? 'text-cyan-700' : 'text-slate-500'));
    };

    input.addEventListener('change', async () => {
        const [file] = input.files || [];

        if (!file) {
            feedback?.classList.add('hidden');
            return;
        }

        if (file.size <= maxPhotoBytes) {
            setFeedback(`Ukuran foto ${formatFileSize(file.size)}. Siap diunggah.`, 'success');
            return;
        }

        if (!file.type.startsWith('image/')) {
            input.value = '';
            setFeedback('File harus berupa gambar JPG, PNG, atau WebP.', 'error');
            window.alert('Foto tidak valid. Pilih gambar JPG, PNG, atau WebP.');
            return;
        }

        form?.setAttribute('data-photo-is-compressing', 'true');
        input.disabled = true;
        setFeedback(`Foto ${formatFileSize(file.size)} sedang dikompres otomatis...`);

        try {
            const compressedPhoto = await compressPhoto(file);
            const fileName = `${file.name.replace(/\.[^.]+$/, '') || 'foto-aset'}.jpg`;
            const compressedFile = new File([compressedPhoto], fileName, {
                type: 'image/jpeg',
                lastModified: Date.now(),
            });
            const transfer = new DataTransfer();
            transfer.items.add(compressedFile);
            input.files = transfer.files;
            setFeedback(`Foto dikompres otomatis dari ${formatFileSize(file.size)} menjadi ${formatFileSize(compressedFile.size)}.`, 'success');
        } catch (error) {
            input.value = '';
            const message = error?.message || 'Foto tidak dapat dikompres otomatis.';
            setFeedback(message, 'error');
            window.alert(`${message} Pilih foto lain dengan ukuran lebih kecil.`);
        } finally {
            input.disabled = false;
            form?.removeAttribute('data-photo-is-compressing');
        }
    });
});

const printSelectionModal = document.getElementById('print-selection-modal');
const openPrintModalButton = document.querySelector('[data-open-print-modal]');
const closePrintModalButton = document.querySelector('[data-close-print-modal]');
const printSelectionForm = document.querySelector('[data-print-selection-form]');
const selectVisibleAssetsCheckbox = document.querySelector('[data-select-visible-assets]');
const selectedCountElement = document.querySelector('[data-selected-count]');
const assetSearchInput = document.querySelector('[data-asset-search]');
const assetResults = document.querySelector('[data-asset-results]');
const selectionEmptyState = document.querySelector('[data-selection-empty]');
const selectionLoading = document.querySelector('[data-selection-loading]');
const selectionError = document.querySelector('[data-selection-error]');
const selectionLoadMoreWrap = document.querySelector('[data-selection-load-more-wrap]');
const selectionLoadMoreButton = document.querySelector('[data-selection-load-more]');
const selectionHiddenInputs = document.querySelector('[data-selection-hidden-inputs]');
const folderExportButton = document.querySelector('[data-export-to-folder]');
const folderExportStatus = document.querySelector('[data-folder-export-status]');

if (printSelectionModal && openPrintModalButton && printSelectionForm && assetResults) {
    const selectionEndpoint = printSelectionForm.dataset.selectionEndpoint;
    const wordExportBaseUrl = printSelectionForm.dataset.wordExportBase;
    const initialSelectedIds = JSON.parse(printSelectionForm.dataset.initialSelected || '[]');
    const selectedAssetIds = new Set(initialSelectedIds);
    const selectedAssets = new Map();
    let selectionPage = 1;
    let selectionHasMorePages = false;
    let selectionKeyword = '';
    let isLoadingSelection = false;
    let searchDebounceTimer;

    const updateSelectedCount = () => {
        if (selectedCountElement) {
            selectedCountElement.textContent = `${selectedAssetIds.size}`;
        }

        const visibleCheckboxes = Array.from(assetResults.querySelectorAll('[data-asset-checkbox]'));

        if (selectVisibleAssetsCheckbox) {
            selectVisibleAssetsCheckbox.checked = visibleCheckboxes.length > 0
                && visibleCheckboxes.every((checkbox) => selectedAssetIds.has(Number(checkbox.value)));
        }
    };

    const syncHiddenInputs = () => {
        selectionHiddenInputs.innerHTML = '';

        Array.from(selectedAssetIds)
            .sort((left, right) => left - right)
            .forEach((assetId) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'asset_ids[]';
                input.value = `${assetId}`;
                selectionHiddenInputs.appendChild(input);
            });
    };

    const setSelectionLoading = (isLoading) => {
        isLoadingSelection = isLoading;
        selectionLoading.classList.toggle('hidden', !isLoading);
    };

    const renderSelectionItem = (asset) => {
        selectedAssets.set(asset.id, asset);

        const wrapper = document.createElement('label');
        wrapper.className = 'flex cursor-pointer flex-col gap-3 rounded-2xl border border-slate-200 px-4 py-4 hover:border-cyan-200 hover:bg-cyan-50/40 sm:flex-row sm:items-start sm:justify-between sm:gap-4';
        const content = document.createElement('div');
        content.className = 'flex items-start gap-4';

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.value = `${asset.id}`;
        checkbox.dataset.assetCheckbox = 'true';
        checkbox.className = 'mt-1 h-4 w-4 rounded border-slate-300 text-cyan-600';
        checkbox.checked = selectedAssetIds.has(asset.id);

        const textWrap = document.createElement('div');
        textWrap.className = 'min-w-0';

        const code = document.createElement('p');
        code.className = 'break-words font-semibold text-slate-900';
        code.textContent = asset.asset_code;

        const name = document.createElement('p');
        name.className = 'mt-1 break-words text-sm text-slate-600';
        name.textContent = asset.name;

        const location = document.createElement('p');
        location.className = 'mt-1 break-words text-sm text-slate-500';
        location.textContent = asset.location;

        textWrap.append(code, name);

        if (asset.register_number) {
            const registerNumber = document.createElement('p');
            registerNumber.className = 'mt-1 break-words text-xs text-slate-400';
            registerNumber.textContent = `Register: ${asset.register_number}`;
            textWrap.appendChild(registerNumber);
        }

        textWrap.appendChild(location);
        content.append(checkbox, textWrap);

        const statusWrap = document.createElement('div');
        statusWrap.className = 'sm:shrink-0';

        const badge = document.createElement('span');
        badge.className = asset.last_printed_at
            ? 'rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700'
            : 'rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700';
        badge.textContent = asset.last_printed_at ? 'Sudah diexport' : 'Belum diexport';
        statusWrap.appendChild(badge);

        if (asset.last_printed_at) {
            const printedAt = document.createElement('p');
            printedAt.className = 'mt-2 text-xs text-slate-400 sm:text-right';
            printedAt.textContent = asset.last_printed_at;
            statusWrap.appendChild(printedAt);
        }

        wrapper.append(content, statusWrap);

        checkbox?.addEventListener('change', () => {
            const assetId = Number(checkbox.value);

            if (checkbox.checked) {
                selectedAssetIds.add(assetId);
            } else {
                selectedAssetIds.delete(assetId);
            }

            syncHiddenInputs();
            updateSelectedCount();
        });

        return wrapper;
    };

    const renderSelectionResults = (assets, append = false) => {
        if (!append) {
            assetResults.innerHTML = '';
        }

        assets.forEach((asset) => {
            assetResults.appendChild(renderSelectionItem(asset));
        });

        const hasVisibleItems = assetResults.children.length > 0;
        selectionEmptyState.classList.toggle('hidden', hasVisibleItems || isLoadingSelection);
        selectionLoadMoreWrap.classList.toggle('hidden', !selectionHasMorePages);
        updateSelectedCount();
    };

    const loadSelections = async ({ append = false } = {}) => {
        if (!selectionEndpoint || isLoadingSelection) {
            return;
        }

        setSelectionLoading(true);
        selectionError.classList.add('hidden');

        try {
            const url = new URL(selectionEndpoint, window.location.origin);
            url.searchParams.set('page', `${selectionPage}`);

            if (selectionKeyword) {
                url.searchParams.set('q', selectionKeyword);
            }

            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Daftar aset gagal dimuat. Coba lagi.');
            }

            const payload = await response.json();
            selectionHasMorePages = payload.current_page < payload.last_page;
            renderSelectionResults(payload.data || [], append);
        } catch (error) {
            selectionError.textContent = error.message || 'Daftar aset gagal dimuat.';
            selectionError.classList.remove('hidden');
            selectionLoadMoreWrap.classList.add('hidden');
        } finally {
            setSelectionLoading(false);
            selectionEmptyState.classList.toggle('hidden', assetResults.children.length > 0 || !selectionError.classList.contains('hidden'));
        }
    };

    openPrintModalButton.addEventListener('click', () => {
        printSelectionModal.classList.remove('hidden');
        printSelectionModal.classList.add('flex');
        selectionPage = 1;
        loadSelections();
        updateSelectedCount();
        assetSearchInput?.focus();
    });

    closePrintModalButton?.addEventListener('click', () => {
        printSelectionModal.classList.add('hidden');
        printSelectionModal.classList.remove('flex');
    });

    printSelectionModal.addEventListener('click', (event) => {
        if (event.target === printSelectionModal) {
            printSelectionModal.classList.add('hidden');
            printSelectionModal.classList.remove('flex');
        }
    });

    selectVisibleAssetsCheckbox?.addEventListener('change', () => {
        const visibleCheckboxes = Array.from(assetResults.querySelectorAll('[data-asset-checkbox]'));

        visibleCheckboxes.forEach((checkbox) => {
            const assetId = Number(checkbox.value);
            checkbox.checked = selectVisibleAssetsCheckbox.checked;

            if (selectVisibleAssetsCheckbox.checked) {
                selectedAssetIds.add(assetId);
            } else {
                selectedAssetIds.delete(assetId);
            }
        });

        syncHiddenInputs();
        updateSelectedCount();
    });

    assetSearchInput?.addEventListener('input', () => {
        window.clearTimeout(searchDebounceTimer);

        searchDebounceTimer = window.setTimeout(() => {
            selectionKeyword = assetSearchInput.value.trim();
            selectionPage = 1;
            loadSelections();
        }, 300);
    });

    selectionLoadMoreButton?.addEventListener('click', () => {
        if (!selectionHasMorePages) {
            return;
        }

        selectionPage += 1;
        loadSelections({ append: true });
    });

    const showFolderExportStatus = (message, type = 'info') => {
        if (!folderExportStatus) {
            return;
        }

        folderExportStatus.textContent = message;
        folderExportStatus.className = type === 'error'
            ? 'mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700'
            : 'mt-4 rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800';
    };

    const getAvailableFileName = async (directoryHandle, filename) => {
        const extensionIndex = filename.lastIndexOf('.');
        const baseName = extensionIndex === -1 ? filename : filename.slice(0, extensionIndex);
        const extension = extensionIndex === -1 ? '' : filename.slice(extensionIndex);
        let number = 0;

        while (true) {
            const candidate = number === 0 ? filename : `${baseName} (${number})${extension}`;

            try {
                await directoryHandle.getFileHandle(candidate);
                number += 1;
            } catch (error) {
                if (error?.name === 'NotFoundError') {
                    return candidate;
                }

                throw error;
            }
        }
    };

    const saveAssetWordToFolder = async (asset, directoryHandle) => {
        const response = await fetch(`${wordExportBaseUrl}/${encodeURIComponent(asset.asset_code)}/export-word`, {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            },
        });

        if (!response.ok) {
            throw new Error(`Export Word untuk ${asset.name} gagal.`);
        }

        const documentBlob = await response.blob();
        const contentDisposition = response.headers.get('content-disposition') || '';
        const encodedFilename = contentDisposition.match(/filename\*=UTF-8''([^;]+)/i)?.[1];
        const plainFilename = contentDisposition.match(/filename="?([^";]+)"?/i)?.[1];
        const serverFilename = encodedFilename ? decodeURIComponent(encodedFilename) : plainFilename;
        const filename = await getAvailableFileName(directoryHandle, serverFilename || `${asset.name} - ${asset.asset_code}.docx`);
        const fileHandle = await directoryHandle.getFileHandle(filename, { create: true });
        const writable = await fileHandle.createWritable();

        await writable.write(documentBlob);
        await writable.close();
    };

    const exportSelectedAssetsToFolder = async () => {
        syncHiddenInputs();

        if (!('showDirectoryPicker' in window)) {
            showFolderExportStatus('Browser ini belum mendukung simpan langsung ke folder. Gunakan Chrome atau Edge di desktop.', 'error');
            return;
        }

        if (selectedAssetIds.size === 0) {
            showFolderExportStatus('Pilih minimal satu aset untuk diexport.', 'error');
            return;
        }

        const selectedAssetsForExport = Array.from(selectedAssetIds)
            .map((assetId) => selectedAssets.get(assetId))
            .filter(Boolean);

        if (selectedAssetsForExport.length !== selectedAssetIds.size) {
            showFolderExportStatus('Muat ulang daftar aset lalu pilih kembali aset yang akan diexport.', 'error');
            return;
        }

        let parentDirectoryHandle;

        try {
            parentDirectoryHandle = await window.showDirectoryPicker({
                id: 'kodebarang-export',
                mode: 'readwrite',
                startIn: 'desktop',
            });
        } catch (error) {
            if (error?.name !== 'AbortError') {
                showFolderExportStatus('Folder tujuan tidak dapat dibuka. Pastikan izin tulis diberikan.', 'error');
            }

            return;
        }

        const originalButtonText = folderExportButton?.textContent;

        try {
            folderExportButton?.setAttribute('disabled', 'disabled');
            if (folderExportButton) {
                folderExportButton.textContent = 'Menyimpan...';
            }
            showFolderExportStatus('Membuka folder tujuan...');

            const exportDirectoryHandle = parentDirectoryHandle;

            for (const [index, asset] of selectedAssetsForExport.entries()) {
                showFolderExportStatus(`Menyimpan ${index + 1} dari ${selectedAssetsForExport.length}: ${asset.name}`);
                await saveAssetWordToFolder(asset, exportDirectoryHandle);
            }

            showFolderExportStatus(`${selectedAssetsForExport.length} file Word berhasil disimpan di folder yang dipilih.`);
        } catch (error) {
            showFolderExportStatus(error?.message || 'Export ke folder gagal. Coba lagi.', 'error');
        } finally {
            folderExportButton?.removeAttribute('disabled');

            if (folderExportButton && originalButtonText) {
                folderExportButton.textContent = originalButtonText;
            }
        }
    };

    printSelectionForm.addEventListener('submit', (event) => {
        event.preventDefault();
        exportSelectedAssetsToFolder();
    });

    syncHiddenInputs();
    updateSelectedCount();
}

const scannerRoot = document.querySelector('[data-asset-scanner]');

if (scannerRoot) {
    const startButton = document.getElementById('start-scanner');
    const readerId = 'reader';
    const statusElement = document.getElementById('scanner-status');
    const helpElement = document.getElementById('scanner-help');
    const modal = document.getElementById('asset-modal');
    const closeModalButton = document.getElementById('close-asset-modal');
    const modalPhoto = document.getElementById('modal-photo');
    const modalPhotoEmpty = document.getElementById('modal-photo-empty');
    let html5QrCode;
    let scannerActive = false;

    const setStatus = (message) => {
        if (statusElement) {
            statusElement.textContent = message;
        }
    };

    const setHelp = (message) => {
        if (helpElement) {
            helpElement.textContent = message;
        }
    };

    const fillText = (id, value) => {
        const element = document.getElementById(id);

        if (element) {
            element.textContent = value || '-';
        }
    };

    const openModal = (asset) => {
        fillText('modal-name', asset.name);
        fillText('modal-code', `${asset.asset_code} - ${asset.location}`);
        fillText('modal-asset-code', asset.asset_code);
        fillText('modal-register-number', asset.register_number);
        fillText('modal-brand', asset.brand);
        fillText('modal-year', asset.year_acquired);
        fillText('modal-location', asset.location);
        fillText('modal-person-in-charge', asset.person_in_charge);
        fillText('modal-condition', asset.condition);
        fillText('modal-description', asset.description);

        if (asset.photo_url) {
            modalPhoto.src = asset.photo_url;
            modalPhoto.alt = asset.name;
            modalPhoto.classList.remove('hidden');
            modalPhotoEmpty.classList.add('hidden');
        } else {
            modalPhoto.src = '';
            modalPhoto.classList.add('hidden');
            modalPhotoEmpty.classList.remove('hidden');
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    const stopScanner = async () => {
        if (!html5QrCode || !scannerActive) {
            return;
        }

        await html5QrCode.stop();
        await html5QrCode.clear();
        scannerActive = false;
    };

    const getLookupUrl = (decodedText) => {
        const value = decodedText.trim();

        if (/^https?:\/\//i.test(value)) {
            const parsedUrl = new URL(value);
            const path = parsedUrl.pathname.replace(/\/$/, '');

            if (path.includes('/aset/')) {
                const code = decodeURIComponent(path.split('/').filter(Boolean).pop() || '');
                return `${window.location.origin}/aset/${encodeURIComponent(code)}/lookup`;
            }

            return `${window.location.origin}${path}/lookup`;
        }

        if (value.startsWith('/')) {
            const path = value.replace(/\/$/, '');

            if (path.includes('/aset/')) {
                const code = decodeURIComponent(path.split('/').filter(Boolean).pop() || '');
                return `${window.location.origin}/aset/${encodeURIComponent(code)}/lookup`;
            }

            return `${window.location.origin}${path}/lookup`;
        }

        return `${window.location.origin}/aset/${encodeURIComponent(value)}/lookup`;
    };

    const onScanSuccess = async (decodedText) => {
        try {
            setStatus(`Barcode terbaca: ${decodedText}`);
            await stopScanner();
            startButton.textContent = 'Scan Ulang';

            const response = await fetch(getLookupUrl(decodedText), {
                headers: {
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Data aset tidak ditemukan.');
            }

            const asset = await response.json();
            openModal(asset);
        } catch (error) {
            setStatus(error.message || 'Gagal membaca data barcode.');
            setHelp('Pastikan QR mengarah ke aset yang ada. Jika QR lama dibuat dari host lain, sistem sekarang akan mencoba membaca memakai host yang sedang dibuka.');
        }
    };

    const explainCameraFailure = (error) => {
        if (!window.isSecureContext) {
            return 'Kamera diblokir karena halaman dibuka lewat HTTP biasa. Buka aplikasi lewat HTTPS agar kamera HP bisa dipakai.';
        }

        if (!navigator.mediaDevices?.getUserMedia) {
            return 'Browser ini belum mendukung akses kamera.';
        }

        const message = `${error?.message || ''}`.toLowerCase();

        if (message.includes('permission') || message.includes('denied')) {
            return 'Izin kamera ditolak. Aktifkan izin kamera browser lalu coba lagi.';
        }

        if (message.includes('secure') || message.includes('insecure')) {
            return 'Akses kamera butuh HTTPS atau alamat lokal yang aman.';
        }

        return 'Kamera tidak bisa dibuka di perangkat ini. Coba lagi setelah izin kamera aktif dan akses aplikasi lewat HTTPS.';
    };

    const startScanner = async () => {
        scannerRoot.classList.remove('hidden');
        setStatus('Meminta izin kamera...');
        setHelp('Arahkan kamera ke QR/barcode aset. Setelah terbaca, pop up detail barang akan muncul otomatis.');

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode(readerId);
        }

        const cameraConfig = { facingMode: 'environment' };

        await html5QrCode.start(
            cameraConfig,
            {
                fps: 10,
                qrbox: { width: 240, height: 240 },
                aspectRatio: 1,
            },
            onScanSuccess,
            () => {}
        );

        scannerActive = true;
        setStatus('Scanner aktif. Arahkan kamera ke QR/barcode aset.');
    };

    startButton?.addEventListener('click', async () => {
        try {
            startButton.disabled = true;
            startButton.textContent = 'Membuka Kamera...';
            await startScanner();
            startButton.textContent = 'Scan Ulang';
        } catch (error) {
            setStatus(explainCameraFailure(error));
            setHelp('Pastikan browser diberi izin kamera dan aplikasi dibuka lewat HTTPS, bukan HTTP biasa.');
            startButton.textContent = 'Mulai Scan Sekarang';
        } finally {
            startButton.disabled = false;
        }
    });

    closeModalButton?.addEventListener('click', closeModal);
    modal?.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });
}
