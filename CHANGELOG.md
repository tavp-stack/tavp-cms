# Changelog

Semua perubahan penting untuk **tavp-cms** (TAVP CMS admin panel).

Format mengikuti [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) dan versi mengikuti ZeroVer `0.MINOR.PATCH`.

## [0.5.2] - 2026-08-04

### Fixed
- **OTP reliability (severity tinggi):** email OTP dikirim **inline blocking** dengan socket timeout keras 15s (bukan deferred shutdown callback yang tidak reliable di PHP-FPM). Setiap attempt di-log (`[TAVP CMS] OTP send: email=... sent=yes/no`).
- **Layout email OTP:** dirombak ke table-based + inline style di setiap elemen agar tampil baik di Gmail/Outlook/Yandex (sebelumnya pakai `<style>` block yang sering di-strip).

## [0.5.1] - 2026-08-04

### Fixed
- **Login stuck di "Mengirim kode verifikasi...":** `sendOtp()` tidak lagi memblok JSON response dengan SMTP dialog yang lambat.

## [0.5.0] - 2026-08-03

### Added
- **Smooth login flow** dengan Alpine.js state machine (email → loading → verify).
- `sendOtp()`/`verify()` mengembalikan JSON saat request AJAX, dengan helper `isAjax()` & `json()`.

## [0.4.0] - 2026-07

### Added
- **SEO module lengkap**: SeoManager, SchemaGenerator, SeoAnalyzer, RedirectManager, LinkChecker, ContentSyndicator, BacklinkMonitor, RankTracker, AiMetaGenerator, CompetitorWatcher, RSS, robots.txt, social sharing, admin UI (dashboard/settings/redirects/analyzer) + migrasi (seo_meta, redirects, outbound_links).
- **Messages inbox** + tautan di sidebar.
- Menu sidebar diorganisir ke section **"SEO & Analytics"**.

### Changed
- Captcha pakai tipe dari config (`config/captcha.php`, `CAPTCHA_TYPE`) bukan hardcoded.
- Captcha verification di `sendOtp` + puzzle captcha UI dark theme.

## [0.3.0] - sebelumnya

- Admin prefix dinamis dari settings/DB (bukan hardcoded `/admin`).
- Media upload API, Prism.js syntax highlighting, video embed.
- Analytics chart via tavpblocks.
- User CRUD sortable, taxonomy, BREAD, menus, settings.
- Revisions/versioning konten.
