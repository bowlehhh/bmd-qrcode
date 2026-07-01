<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Massal Barcode Aset</title>
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
            padding: 20px;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .actions button,
        .actions a {
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

        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 18px;
            padding: 8mm 6mm;
            background: #fff;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.28);
            display: grid;
            grid-template-columns: repeat(2, 96mm);
            gap: 7mm 6mm;
            justify-content: center;
            align-content: start;
        }

        .label {
            position: relative;
            width: 96mm;
            height: 64mm;
            overflow: hidden;
            border: 1.2px solid var(--primary-soft);
            border-radius: 5mm;
            background: #fcfdff;
        }

        .side-panel {
            position: absolute;
            top: 0;
            right: 0;
            width: 31mm;
            height: 100%;
            overflow: hidden;
            border-top-right-radius: 5mm;
            border-bottom-right-radius: 5mm;
        }

        .side-panel svg {
            display: block;
            width: 100%;
            height: 100%;
        }

        .side-content {
            position: absolute;
            top: 5.2mm;
            right: 3.4mm;
            z-index: 2;
            width: 15.4mm;
            color: #fff;
        }

        .circuit {
            position: absolute;
            right: 2.8mm;
            bottom: 3.2mm;
            z-index: 2;
            width: 18.5mm;
            height: 18.5mm;
            opacity: 0.3;
        }

        .circuit span {
            position: absolute;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 99px;
        }

        .circuit span:nth-child(1) { left: 1.4mm; top: 1.8mm; width: 0.35mm; height: 13mm; }
        .circuit span:nth-child(2) { left: 5mm; top: 3.5mm; width: 0.35mm; height: 11mm; }
        .circuit span:nth-child(3) { left: 8.6mm; top: 1mm; width: 0.35mm; height: 14mm; }
        .circuit span:nth-child(4) { left: 12.2mm; top: 3.5mm; width: 0.35mm; height: 11mm; }
        .circuit span:nth-child(5) { left: 2.2mm; top: 6.4mm; width: 5mm; height: 0.35mm; }
        .circuit span:nth-child(6) { left: 6.2mm; top: 9.4mm; width: 5mm; height: 0.35mm; }
        .circuit span:nth-child(7) { left: 10mm; top: 7mm; width: 4.2mm; height: 0.35mm; }
        .circuit span:nth-child(8) { left: 4mm; top: 13.8mm; width: 5mm; height: 0.35mm; }
        .circuit span:nth-child(9) { left: 1.4mm; top: 1.8mm; width: 4.6mm; height: 0.35mm; transform: rotate(18deg); transform-origin: left center; }
        .circuit span:nth-child(10) { left: 7mm; top: 5.2mm; width: 4.6mm; height: 0.35mm; transform: rotate(18deg); transform-origin: left center; }
        .circuit span:nth-child(11) { left: 3.4mm; top: 11.8mm; width: 4.6mm; height: 0.35mm; transform: rotate(18deg); transform-origin: left center; }
        .circuit span:nth-child(12) { left: 10.8mm; top: 12.2mm; width: 3.8mm; height: 0.35mm; transform: rotate(18deg); transform-origin: left center; }

        .circuit::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle, rgba(255,255,255,.9) 0 1px, transparent 1.3px),
                radial-gradient(circle, rgba(255,255,255,.9) 0 1px, transparent 1.3px),
                radial-gradient(circle, rgba(255,255,255,.9) 0 1px, transparent 1.3px),
                radial-gradient(circle, rgba(255,255,255,.9) 0 1px, transparent 1.3px),
                radial-gradient(circle, rgba(255,255,255,.9) 0 1px, transparent 1.3px),
                radial-gradient(circle, rgba(255,255,255,.9) 0 1px, transparent 1.3px);
            background-repeat: no-repeat;
            background-size: 3px 3px;
            background-position:
                1.4mm 5.4mm,
                6mm 4.8mm,
                10.6mm 6.2mm,
                3.8mm 13mm,
                8.8mm 10.8mm,
                13.2mm 12.8mm;
            pointer-events: none;
        }

        .badge {
            display: flex;
            gap: 2mm;
            align-items: flex-start;
        }

        .badge-mark {
            width: 4.4mm;
            height: 4.4mm;
            flex: 0 0 auto;
        }

        .badge-title {
            font-size: 3.5pt;
            line-height: 1.15;
            font-weight: 800;
            text-transform: uppercase;
        }

        .badge-subtitle {
            margin-top: 0.5mm;
            font-size: 2.25pt;
            line-height: 1.12;
            text-transform: uppercase;
        }

        .content {
            position: relative;
            z-index: 3;
            width: calc(100% - 31mm);
            height: 100%;
            padding: 4.8mm 4mm 9.8mm 4.8mm;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 2.2mm;
            padding-bottom: 2.4mm;
            border-bottom: 1px solid #97b4e5;
        }

        .logo-block {
            display: flex;
            align-items: center;
            gap: 2.5mm;
        }

        .logo-mark {
            width: 9.5mm;
            height: 9.5mm;
            object-fit: contain;
            flex: 0 0 auto;
        }

        .logo-divider {
            width: 0.8px;
            height: 9.8mm;
            background: #7ea1db;
        }

        .header-title {
            color: var(--primary-dark);
            font-size: 4.95pt;
            line-height: 1.08;
            font-weight: 800;
            text-transform: uppercase;
        }

        .body {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 24.5mm;
            gap: 2.6mm;
            align-items: center;
            padding-top: 2.8mm;
        }

        .caption {
            margin: 0;
            color: var(--primary-dark);
            font-size: 4.2pt;
            font-weight: 800;
            text-transform: uppercase;
        }

        .bmd {
            display: flex;
            align-items: baseline;
            gap: 1.7mm;
            margin: 1.2mm 0 0;
            color: #08080b;
            font-size: 0;
            line-height: 1;
            font-weight: 900;
            letter-spacing: 0;
        }

        .bmd span {
            display: inline-block;
            font-size: 23pt;
            line-height: 0.88;
        }

        .bmd span:nth-child(1),
        .bmd span:nth-child(3) {
            transform: scaleX(1.02);
        }

        .bmd span:nth-child(2) {
            transform: scaleX(1.13);
        }

        .asset-title {
            display: flex;
            align-items: center;
            gap: 1.5mm;
            margin-top: 2.4mm;
        }

        .asset-title svg {
            width: 4.9mm;
            height: 4.9mm;
            color: #7a99d0;
        }

        .asset-title strong {
            display: block;
            color: var(--primary);
            font-size: 3.95pt;
            line-height: 1.1;
            font-weight: 800;
            text-transform: uppercase;
        }

        .asset-title span {
            display: block;
            margin-top: 0.4mm;
            color: var(--muted);
            font-size: 2.3pt;
            line-height: 1.1;
            font-weight: 700;
            text-transform: uppercase;
        }

        .qr-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24mm;
            height: 24mm;
            background: #fff;
            transform: translate(-0.4mm, -1.4mm);
        }

        .qr-box svg {
            width: 22mm;
            height: 22mm;
            display: block;
        }

        .footer {
            position: absolute;
            left: 4.8mm;
            right: 4mm;
            bottom: 4mm;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.3mm 1.8mm;
            padding-top: 1.3mm;
            border-top: 1px solid #edf2ff;
        }

        .item {
            display: grid;
            grid-template-columns: 4.1mm 1fr;
            gap: 0.9mm;
            align-items: start;
            min-width: 0;
        }

        .item svg {
            width: 3.3mm;
            height: 3.3mm;
            color: var(--primary);
        }

        .item-label {
            color: var(--primary-dark);
            font-size: 2.55pt;
            line-height: 1.1;
            font-weight: 800;
            text-transform: uppercase;
        }

        .item-value {
            margin-top: 0.2mm;
            color: var(--muted);
            font-size: 2.05pt;
            line-height: 1.05;
            font-weight: 700;
            text-transform: uppercase;
            word-break: break-word;
        }

        .blank {
            border: 1px dashed #d6dfef;
            background: #fafcff;
        }

        .sheet.count-1 {
            grid-template-columns: 96mm;
            justify-content: center;
            align-content: center;
        }

        @media screen and (max-width: 768px) {
            body {
                background: #48515c;
            }

            .page {
                padding: 12px;
            }

            .actions {
                flex-wrap: wrap;
                margin-bottom: 14px;
            }

            .actions button,
            .actions a {
                width: 100%;
                text-align: center;
            }

            .sheet {
                width: 100%;
                min-height: auto;
                padding: 14px;
                grid-template-columns: 1fr;
                gap: 14px;
                justify-items: center;
            }

            .label {
                width: 100%;
                max-width: 96mm;
                height: auto;
                aspect-ratio: 96 / 62;
            }

            .blank {
                display: none;
            }

            .sheet.count-1 {
                grid-template-columns: 1fr;
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

            .sheet {
                width: auto;
                min-height: auto;
                margin: 0 0 10mm;
                padding: 0;
                box-shadow: none;
                page-break-after: always;
            }

            .sheet:last-child {
                page-break-after: auto;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="page">
        <div class="actions">
            <button onclick="window.print()">Cetak 4 per Lembar</button>
            <a href="{{ route('assets.index') }}">Kembali ke Data Aset</a>
        </div>

        @forelse ($assetGroups as $group)
            <section class="sheet count-{{ $group->count() }}">
                @foreach ($group as $asset)
                    <article class="label">
                        <div class="side-panel" aria-hidden="true">
                            <svg viewBox="0 0 180 276" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                                <defs>
                                    <linearGradient id="panelBlue{{ $loop->parent->index }}{{ $loop->index }}" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#2f65ca"/>
                                        <stop offset="100%" stop-color="#2151ab"/>
                                    </linearGradient>
                                </defs>
                                <path d="M24 0H180V276H24V196C33 186 39 167 39 138C39 109 33 90 24 80V0Z" fill="url(#panelBlue{{ $loop->parent->index }}{{ $loop->index }})"/>
                                <path d="M0 0H24V80C33 90 39 109 39 138C39 167 33 186 24 196V276H0V0Z" fill="#fcfdff"/>
                                <g opacity="0.2" stroke="#ffffff" stroke-width="1">
                                    <path d="M54 0V276"/>
                                    <path d="M86 0V276"/>
                                    <path d="M118 0V276"/>
                                    <path d="M30 62H180"/>
                                    <path d="M30 144H180"/>
                                    <path d="M30 224H180"/>
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
                    </article>
                @endforeach

                @if ($group->count() > 1)
                    @for ($i = $group->count(); $i < 4; $i++)
                        <div class="label blank"></div>
                    @endfor
                @endif
            </section>
        @empty
            <section class="sheet">
                <div class="label blank"></div>
            </section>
        @endforelse
    </div>
</body>
</html>
