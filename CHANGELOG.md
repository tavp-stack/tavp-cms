# Changelog

Semua perubahan penting untuk **tavp-cms** (management panel TAVP Admin) dicatat di sini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) dan versi mengikuti ZeroVer `0.MINOR.PATCH`.

## [Unreleased]

### Fixed
- Rekonsiliasi berkas tema: 20 file `.volt` simpanan kerja menyimpang dari `HEAD` akibat debugging produksi (URL hardcoded). Dimatikan di `NEXT_STEPS.md`, menunggu pemeriksaan sebelum rilis lanjutan.

## [0.5.4] - 07 Aug 2026

### Added
- Settings admin → section **Security** → **Captcha Type** (dropdown `math`/`slider`/`puzzle`), tersimpan di DB, dipakai halaman login. Halaman login membacanya via `security.captcha_type` dengan fallback config.

## [0.5.3] - 05 Aug 2026

### Chore
- Sinkronisasi versi ZeroVer: release ulang menuju commit kompatibel (dependensi `tavp/core ^0.1`). Tidak ada perubahan fitur.

## [0.5.2] - 05 Aug 2026

### Fixed
- Pengiriman email OTP inline-blocking dengan socket timeout keras 15s (bukan deferred shutdown callback yang tidak reliable di PHP-FPM); setiap percobaan di-log.
- Layout email OTP dirombak jadi table-based + inline style agar tampil baik di Gmail/Outlook/Yandex.
- Sinkronisasi tag ZeroVer semua versi v0.x.

## [0.5.1] - 05 Aug 2026

### Fixed
- Login tidak lagi berhenti di "Mengirim kode verifikasi...": `sendOtp()` mengalirkan respons JSON sebelum melakukan dialog SMTP yang lambat.

## [0.5.0] - 05 Aug 2026

### Added
- Alur login halus dengan state machine Alpine.js (email → loading → verifikasi).
- `sendOtp()`/`verify()` mengembalikan JSON saat AJAX (helper `isAjax()`/`json()`).

## [0.4.0] - 05 Aug 2026

### Added
- Modul SEO lengkap: `SeoManager`, `SchemaGenerator`, `SeoAnalyzer`, `RedirectManager`, `LinkChecker`, `ContentSyndicator`, `BacklinkMonitor`, `RankTracker`, `AiMetaGenerator`, `CompetitorWatcher`, RSS, `robots.txt`, social sharing, UI admin (dashboard/settings/redirects/analyzer) + migrasi `seo_meta`, `redirects`, `outbound_links`.
- Messages inbox + tautan di sidebar.
- Menu sidebar dikelompokkan ke section **"SEO & Analytics"**.

### Changed
- Captcha memakai tipe dari config (`config/captcha.php`, `CAPTCHA_TYPE`) bukan hardcoded.
- Verifikasi captcha di `sendOtp` + tema gelap UI puzzle captcha.

## [0.2.x] - 05 Aug 2026

### Added
- Prefiks admin dinamis dari settings/DB (bukan hardcoded `/admin`).
- Media upload API, Prism.js syntax highlighting, embed video.
- Chart analitik via tavpblocks.
- CRUD user sortable, taxonomy, BREAD, menus, settings.
- Revisi/versioning konten.

## [0.1.0] - 05 Aug 2026

### Added
- Baseline: semua modul admin inti (auth OTP, users, content, settings).