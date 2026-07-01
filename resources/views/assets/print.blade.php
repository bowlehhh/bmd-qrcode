<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak QR {{ $asset->asset_code }}</title>
    <style>
        :root {
            --primary: #1d4ea3;
            --primary-dark: #163c86;
            --primary-soft: #d9e6ff;
            --ink: #101114;
            --muted: #56657f;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #666b73;
            color: var(--ink);
        }

        .page {
            min-height: 100vh;
            padding: 24px;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .actions a,
        .actions button {
            border: 0;
            border-radius: 14px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .actions button {
            background: #0f172a;
            color: #fff;
        }

        .actions a {
            background: #fff;
            color: #0f172a;
        }

        .paper {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 18mm 10mm;
            background: #fff;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.28);
        }

        .label-wrap {
            display: flex;
            justify-content: center;
        }

        .label {
            position: relative;
            width: 160mm;
            height: 75mm;
            overflow: hidden;
            border: 1.4px solid var(--primary-soft);
            border-radius: 6mm;
            background: #fcfdff;
            box-shadow: 0 10px 22px rgba(29, 78, 163, 0.08);
        }

        .side-panel {
            position: absolute;
            top: 0;
            right: 0;
            width: 44mm;
            height: 100%;
            overflow: hidden;
            border-top-right-radius: 6mm;
            border-bottom-right-radius: 6mm;
        }

        .side-panel svg {
            display: block;
            width: 100%;
            height: 100%;
        }

        .side-content {
            position: absolute;
            top: 6.8mm;
            right: 4.8mm;
            z-index: 2;
            width: 22.5mm;
            color: #fff;
        }

        .badge {
            display: flex;
            gap: 1.8mm;
            align-items: flex-start;
        }

        .badge-mark {
            width: 6.2mm;
            height: 6.2mm;
            flex: 0 0 auto;
        }

        .badge-title {
            font-size: 5.2pt;
            line-height: 1.1;
            font-weight: 800;
            text-transform: uppercase;
        }

        .badge-subtitle {
            margin-top: 0.5mm;
            font-size: 3.35pt;
            line-height: 1.18;
            text-transform: uppercase;
        }

        .circuit {
            position: absolute;
            right: 4.5mm;
            bottom: 7mm;
            z-index: 2;
            width: 24mm;
            height: 24mm;
            opacity: 0.3;
        }

        .circuit span {
            position: absolute;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 99px;
        }

        .circuit span:nth-child(1) { left: 2mm; top: 2mm; width: 0.45mm; height: 18mm; }
        .circuit span:nth-child(2) { left: 7mm; top: 4mm; width: 0.45mm; height: 16mm; }
        .circuit span:nth-child(3) { left: 12mm; top: 1mm; width: 0.45mm; height: 19mm; }
        .circuit span:nth-child(4) { left: 17mm; top: 4mm; width: 0.45mm; height: 16mm; }
        .circuit span:nth-child(5) { left: 3mm; top: 8mm; width: 7mm; height: 0.45mm; }
        .circuit span:nth-child(6) { left: 8mm; top: 12mm; width: 7mm; height: 0.45mm; }
        .circuit span:nth-child(7) { left: 13mm; top: 9mm; width: 6mm; height: 0.45mm; }
        .circuit span:nth-child(8) { left: 5mm; top: 18mm; width: 7mm; height: 0.45mm; }
        .circuit span:nth-child(9) { left: 2mm; top: 2mm; width: 6mm; height: 0.45mm; transform: rotate(18deg); transform-origin: left center; }
        .circuit span:nth-child(10) { left: 9mm; top: 6mm; width: 6mm; height: 0.45mm; transform: rotate(18deg); transform-origin: left center; }
        .circuit span:nth-child(11) { left: 4mm; top: 15mm; width: 6mm; height: 0.45mm; transform: rotate(18deg); transform-origin: left center; }
        .circuit span:nth-child(12) { left: 13mm; top: 15mm; width: 5mm; height: 0.45mm; transform: rotate(18deg); transform-origin: left center; }
        .circuit span:nth-child(13) { left: 18mm; top: 18mm; width: 0.45mm; height: 4mm; }
        .circuit span:nth-child(14) { left: 1.8mm; top: 20mm; width: 4mm; height: 0.45mm; }
        .circuit span:nth-child(15) { left: 10mm; top: 21mm; width: 5mm; height: 0.45mm; }
        .circuit span:nth-child(16) { left: 16mm; top: 22mm; width: 4mm; height: 0.45mm; }

        .circuit::before,
        .circuit::after {
            content: "";
            position: absolute;
            inset: 0;
            background-repeat: no-repeat;
            pointer-events: none;
        }

        .circuit::before {
            background-image:
                radial-gradient(circle, rgba(255,255,255,.9) 0 1.3px, transparent 1.6px),
                radial-gradient(circle, rgba(255,255,255,.9) 0 1.2px, transparent 1.5px),
                radial-gradient(circle, rgba(255,255,255,.9) 0 1.2px, transparent 1.5px),
                radial-gradient(circle, rgba(255,255,255,.9) 0 1.2px, transparent 1.5px),
                radial-gradient(circle, rgba(255,255,255,.9) 0 1.2px, transparent 1.5px),
                radial-gradient(circle, rgba(255,255,255,.9) 0 1.2px, transparent 1.5px),
                radial-gradient(circle, rgba(255,255,255,.9) 0 1.2px, transparent 1.5px);
            background-size: 4px 4px;
            background-position:
                2mm 6mm,
                8mm 5.2mm,
                14mm 7.8mm,
                5mm 16.5mm,
                11.8mm 13.8mm,
                17.2mm 16.8mm,
                19.4mm 10.5mm;
        }

        .content {
            position: relative;
            z-index: 3;
            width: calc(100% - 44mm);
            height: 100%;
            padding: 6.2mm 6mm 13.8mm 6.8mm;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 3.1mm;
            padding-bottom: 3.2mm;
            border-bottom: 1.1px solid #97b4e5;
        }

        .logo-block {
            display: flex;
            align-items: center;
            gap: 3mm;
        }

        .logo-mark {
            width: 13.8mm;
            height: 13.8mm;
            flex: 0 0 auto;
            object-fit: contain;
        }

        .logo-divider {
            width: 0.8px;
            height: 12.2mm;
            background: #7ea1db;
        }

        .header-title {
            color: var(--primary-dark);
            font-size: 8.2pt;
            line-height: 1.08;
            font-weight: 800;
            text-transform: uppercase;
        }

        .body {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 41mm;
            gap: 4.2mm;
            align-items: center;
            padding-top: 4mm;
        }

        .caption {
            margin: 0;
            color: var(--primary-dark);
            font-size: 5.75pt;
            font-weight: 800;
            text-transform: uppercase;
        }

        .bmd {
            display: flex;
            align-items: baseline;
            gap: 2.7mm;
            margin: 1.9mm 0 0;
            color: #08080b;
            font-size: 0;
            line-height: 1;
            font-weight: 900;
            letter-spacing: 0;
        }

        .bmd span {
            display: inline-block;
            font-size: 40pt;
            line-height: 0.88;
            transform-origin: left bottom;
        }

        .bmd span:nth-child(1),
        .bmd span:nth-child(3) {
            transform: scaleX(1.02);
        }

        .bmd span:nth-child(2) {
            transform: scaleX(1.14);
        }

        .asset-title {
            display: flex;
            align-items: center;
            gap: 2.2mm;
            margin-top: 3.8mm;
        }

        .asset-title svg {
            width: 7mm;
            height: 7mm;
            color: #7a99d0;
        }

        .asset-title strong {
            display: block;
            color: var(--primary);
            font-size: 5.8pt;
            line-height: 1.1;
            font-weight: 800;
            text-transform: uppercase;
        }

        .asset-title span {
            display: block;
            margin-top: 0.35mm;
            color: var(--muted);
            font-size: 3.35pt;
            line-height: 1.14;
            font-weight: 700;
            text-transform: uppercase;
        }

        .qr-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40mm;
            height: 40mm;
            background: #fff;
            transform: translate(-0.8mm, -2.2mm);
        }

        .qr-box svg {
            width: 37mm;
            height: 37mm;
            display: block;
        }

        .footer {
            position: absolute;
            left: 6.8mm;
            right: 6mm;
            bottom: 5mm;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.8mm;
            padding-top: 1.8mm;
            border-top: 1px solid #edf2ff;
        }

        .item {
            display: grid;
            grid-template-columns: 5.4mm 1fr;
            gap: 1.1mm;
            align-items: start;
            min-width: 0;
        }

        .item svg {
            width: 4.8mm;
            height: 4.8mm;
            color: var(--primary);
        }

        .item-label {
            color: var(--primary-dark);
            font-size: 3.55pt;
            line-height: 1.1;
            font-weight: 800;
            text-transform: uppercase;
        }

        .item-value {
            margin-top: 0.25mm;
            color: var(--muted);
            font-size: 2.7pt;
            line-height: 1.08;
            font-weight: 700;
            text-transform: uppercase;
            word-break: break-word;
        }

        @media screen and (max-width: 768px) {
            body {
                background: #48515c;
            }

            .page {
                min-height: auto;
                padding: 12px;
            }

            .actions {
                flex-wrap: wrap;
                margin-bottom: 14px;
            }

            .actions a,
            .actions button {
                width: 100%;
                text-align: center;
            }

            .paper {
                width: 100%;
                min-height: auto;
                padding: 16px 10px;
            }

            .label {
                width: 100%;
                height: auto;
                aspect-ratio: 160 / 73;
            }
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
            }

            body {
                background: #fff;
            }

            .page {
                padding: 0;
            }

            .actions {
                display: none;
            }

            .paper {
                width: auto;
                min-height: auto;
                padding: 0;
                box-shadow: none;
            }

            .label-wrap {
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="page">
        <div class="actions">
            <button onclick="window.print()">Cetak Sekarang</button>
            <a href="{{ route('assets.download', $asset) }}">Download QR</a>
        </div>

        <div class="paper">
            <div class="label-wrap">
                <section class="label">
                    <div class="side-panel" aria-hidden="true">
                        <svg viewBox="0 0 160 276" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="panelBlue" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#2f65ca"/>
                                    <stop offset="100%" stop-color="#2151ab"/>
                                </linearGradient>
                            </defs>
                            <path d="M24 0H160V276H24V196C33 186 39 167 39 138C39 109 33 90 24 80V0Z" fill="url(#panelBlue)"/>
                            <path d="M0 0H24V80C33 90 39 109 39 138C39 167 33 186 24 196V276H0V0Z" fill="#fcfdff"/>
                            <g opacity="0.2" stroke="#ffffff" stroke-width="1">
                                <path d="M54 0V276"/>
                                <path d="M86 0V276"/>
                                <path d="M118 0V276"/>
                                <path d="M30 62H160"/>
                                <path d="M30 144H160"/>
                                <path d="M30 224H160"/>
                            </g>
                        </svg>
                    </div>

                    <div class="side-content">
                        <div class="badge">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="badge-mark">
                                    <path d="M8 4H6C4.9 4 4 4.9 4 6V8M16 4H18C19.1 4 20 4.9 20 6V8M20 16V18C20 19.1 19.1 20 18 20H16M8 20H6C4.9 20 4 19.1 4 18V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M9 9H10.5V10.5H9V9ZM13.5 9H15V10.5H13.5V9ZM9 13.5H10.5V15H9V13.5ZM13.5 13.5H15V15H13.5V13.5Z" fill="currentColor"/>
                                </svg>
                                <div>
                                    <div class="badge-title">Digitalisasi Aset</div>
                                    <div class="badge-subtitle">Untuk layanan pengelolaan aset lebih baik</div>
                            </div>
                        </div>
                    </div>

                    <div class="circuit" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                    <div class="content">
                        <div class="header">
                            <div class="logo-block">
                                <img src="{{ asset('branding/logo-kominfo-kubar.jpeg') }}" alt="Logo Diskominfo" class="logo-mark">
                                <div class="logo-divider"></div>
                            </div>
                            <div class="header-title">
                                Dinas Komunikasi dan Informatika<br>
                                Kabupaten Kutai Barat
                            </div>
                        </div>

                        <div class="body">
                            <div>
                                <p class="caption">Kode Aset</p>
                                <div class="bmd" aria-label="BMD">
                                    <span>B</span><span>M</span><span>D</span>
                                </div>

                                <div class="asset-title">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="3" y="4" width="18" height="12" rx="1.8" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M9 20H15M12 16V20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <div>
                                        <strong>Aset Milik Daerah</strong>
                                        <span>Jaga, Gunakan, dan Rawat Dengan Baik</span>
                                    </div>
                                </div>
                            </div>

                            <div class="qr-box">
                                {!! Storage::disk('public')->get($asset->qr_code_path) !!}
                            </div>
                        </div>

                        <div class="footer">
                            <div class="item">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M5.5 19.5C6.7 16.4 9.1 14.8 12 14.8C14.9 14.8 17.3 16.4 18.5 19.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                    <div>
                                        <div class="item-label">Aman &amp; Terintegrasi</div>
                                        <div class="item-value">Data aman, aset terlindungi</div>
                                    </div>
                                </div>

                            <div class="item">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="3.5" y="5.5" width="17" height="15" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M7 3.5V7.5M17 3.5V7.5M3.5 10H20.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                    <div>
                                        <div class="item-label">Tahun Pengadaan</div>
                                        <div class="item-value">{{ $asset->year_acquired ?: '-' }}</div>
                                    </div>
                                </div>

                            <div class="item">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 20.5H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M6 20.5V10.5H10V20.5M14 20.5V4.5H18V20.5" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                                    <div>
                                        <div class="item-label">Pemerintah</div>
                                        <div class="item-value">Kabupaten Kutai Barat</div>
                                    </div>
                                </div>

                                <div class="item">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M5.5 19.5C6.7 16.4 9.1 14.8 12 14.8C14.9 14.8 17.3 16.4 18.5 19.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <div>
                                        <div class="item-label">Penanggung Jawab</div>
                                        <div class="item-value">{{ $asset->person_in_charge ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </section>
            </div>
        </div>
    </div>
</body>
</html>
