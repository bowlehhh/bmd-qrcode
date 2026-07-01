import { Html5Qrcode } from 'html5-qrcode';

import.meta.glob([
    '../images/**',
]);

const mobileSidebar = document.getElementById('app-sidebar');
const mobileSidebarOverlay = document.getElementById('mobile-sidebar-overlay');
const openMobileSidebarButton = document.getElementById('open-mobile-sidebar');
const closeMobileSidebarButton = document.getElementById('close-mobile-sidebar');

if (mobileSidebar && mobileSidebarOverlay) {
    const openSidebar = () => {
        mobileSidebar.classList.remove('-translate-x-full');
        mobileSidebarOverlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    const closeSidebar = () => {
        mobileSidebar.classList.add('-translate-x-full');
        mobileSidebarOverlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    openMobileSidebarButton?.addEventListener('click', openSidebar);
    closeMobileSidebarButton?.addEventListener('click', closeSidebar);
    mobileSidebarOverlay.addEventListener('click', closeSidebar);

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            mobileSidebarOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
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

if (loadingOverlay && loadingForms.length > 0) {
    loadingForms.forEach((form) => {
        form.addEventListener('submit', () => {
            loadingOverlay.classList.remove('hidden');
            loadingOverlay.classList.add('flex');

            form.querySelectorAll('button[type="submit"]').forEach((button) => {
                button.setAttribute('disabled', 'disabled');
                button.textContent = 'Memproses...';
            });
        });
    });
}

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

if (printSelectionModal && openPrintModalButton && printSelectionForm && assetResults) {
    const selectionEndpoint = printSelectionForm.dataset.selectionEndpoint;
    const initialSelectedIds = JSON.parse(printSelectionForm.dataset.initialSelected || '[]');
    const selectedAssetIds = new Set(initialSelectedIds);
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
        badge.textContent = asset.last_printed_at ? 'Sudah dicetak' : 'Belum dicetak';
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

    printSelectionForm.addEventListener('submit', () => {
        syncHiddenInputs();
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
        fillText('modal-category', asset.category);
        fillText('modal-brand', asset.brand);
        fillText('modal-year', asset.year_acquired);
        fillText('modal-location', asset.location);
        fillText('modal-person-in-charge', asset.person_in_charge);
        fillText('modal-is-in-use', asset.is_in_use ? 'Ya' : 'Tidak');
        fillText('modal-condition', asset.condition);
        fillText('modal-description', asset.description);

        const detailLink = document.getElementById('modal-detail-link');

        if (detailLink) {
            detailLink.href = asset.detail_url;
        }

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
