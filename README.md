# TAVP CMS

A content management system built on the TAVP Stack. WordPress-familiar,
Voyager-style admin, thin and fast — with pluggable storage, headless API,
and a feature set that works out of the box.

> **Version: 0.3.0** — ZeroVer `0.MINOR.PATCH`.

## What it is

Thin. Light. Not bloated. TAVP CMS is a **standalone CMS** that runs on top
of `tavp/core` only. No external auth packages, no admin panel packages, no
component libraries required — it ships with its own implementations.

| Concern | Implementation |
|---------|---------------|
| Foundation (routing, ORM, Volt) | `tavp/core` |
| Admin auth (OTP email) | `AdminAuth` (built-in) |
| Admin UI | PHP templates + Tailwind (built-in) |
| BREAD CRUD | `BreadManager` (built-in) |
| Storage | `DatabaseStore` or `FlatFileStore` (pluggable) |

## Pluggable storage

You are not locked into a database. Choose the driver in `config/cms.php`:

- **database** — WordPress/Voyager style, single `contents` table with JSON data column
- **flatfile** — Statamic style, Markdown + YAML front matter per record

The rest of the CMS behaves identically regardless of driver.

## Features (what actually works)

### Core
- **BREAD** — generic Browse/Read/Edit/Add/Delete for any content type
- **Pages & Posts** — built-in content types with fields, validation, slug generation
- **OTP passwordless admin** — email code via Mailpit/SMTP, session-based
- **Content revisions** — automatic version history on every save, one-click rollback
- **Full-text search** — search across all content types from admin UI and API
- **Headless REST API** — full CRUD + pagination + search + taxonomy via `/api/cms`
- **Taxonomy** — categories (hierarchical) + tags (flat), attached to any content type
- **SEO sitemap** — auto-generated `sitemap.xml` from published content
- **RBAC-lite** — email-to-role mapping with wildcard permission patterns
- **Cached storage** — read-through cache (file or in-memory) wrapping the storage layer
- **Field validation** — server-side rules (required, min, max, email, unique, etc.)
- **Blog routes** — `/blog`, `/blog/{slug}`, `/category/{slug}`, `/tag/{slug}`
- **Theme system** — Volt templates under `/themes`, active theme configurable

### Planned (not yet implemented)
- Media library admin UI
- Menu builder admin UI
- Settings admin UI
- Scheduled publishing (`cms:publish` CLI)
- Per-record SEO meta fields

## Requirements

- PHP 8.3+
- Phalcon 5.16+ (compiled extension)
- `tavp/core` ^1.1

## Install

```bash
composer create-project tavp/cms my-site
cd my-site
tavp migrate
tavp serve
```

## Quick start

### 1. Configure

```bash
# .env
CMS_STORAGE=flatfile
CMS_ADMIN_EMAILS=you@example.com
APP_URL=https://mysite.com
```

### 2. Run migrations

```bash
tavp migrate
```

### 3. Admin

Visit `/admin`, sign in with your allowed email via OTP.

### 4. Headless API

```bash
# List content types
curl -H "Authorization: Bearer YOUR_TOKEN" https://mysite.com/api/cms/types

# Browse posts
curl -H "Authorization: Bearer YOUR_TOKEN" https://mysite.com/api/cms/post

# Search
curl -H "Authorization: Bearer YOUR_TOKEN" "https://mysite.com/api/cms/search?q=hello"
```

## Content types

Define content types in `config/cms.php` or use the admin BREAD UI:

```php
'fields' => [
    ['name' => 'title', 'type' => 'text', 'required' => true, 'rules' => ['max:200']],
    ['name' => 'slug', 'type' => 'slug', 'from' => 'title', 'rules' => ['unique']],
    ['name' => 'body', 'type' => 'richtext'],
    ['name' => 'status', 'type' => 'select', 'options' => ['draft', 'published']],
],
```

## Structure

```
config/cms.php                     Storage driver, content types, admin, mail,
                                   taxonomy, api, cache, seo
src/Storage/                       ContentStore + DatabaseStore + FlatFileStore
src/Content/                       ContentType, Field, FieldType, Validator
src/Bread/                         BreadManager (Browse/Read/Edit/Add/Delete)
src/Admin/                         AdminAuth, AdminController, ContentController,
                                   DashboardController, RevisionController,
                                   SearchController, TaxonomyController
src/Api/                           ApiController + ApiTokenService
src/Taxonomy/                      TaxonomyManager + Term
src/Revisions/                     RevisionStore (versioning + rollback)
src/Search/                        SearchEngine (full-text across types)
src/Cache/                         CachedContentStore (read-through decorator)
src/Auth/                          RbacGuard (RBAC-lite)
src/Seo/                           SitemapController
src/Publishing/                    PublishScheduler
src/Media/                         MediaLibrary (storage only, no admin UI yet)
src/Menu/                          MenuBuilder (storage only, no admin UI yet)
src/Settings/                      Settings (storage only, no admin UI yet)
src/Webhooks/                      WebhookManager
resources/admin/                   Admin UI templates (10 views)
themes/default/                    Volt + Tailwind default theme
database/migrations/               Schema migrations
routes/web.php                     Front-end routes (blog, taxonomy, catch-all)
```

## API Reference

All endpoints require a Bearer token via `Authorization: Bearer <token>`.

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/cms/types` | List content types |
| GET | `/api/cms/{type}` | Browse records (paginated) |
| GET | `/api/cms/{type}/{id}` | Read a record |
| POST | `/api/cms/{type}` | Create a record |
| PUT | `/api/cms/{type}/{id}` | Update a record |
| DELETE | `/api/cms/{type}/{id}` | Delete a record |
| GET | `/api/cms/search?q={query}` | Full-text search |
| GET | `/api/cms/taxonomy/{type}` | List taxonomy terms |
| POST | `/api/cms/taxonomy` | Create a taxonomy term |
| GET | `/api/cms/{type}/{id}/revisions` | Revision history |
| POST | `/api/cms/{type}/{id}/rollback/{ts}` | Rollback to revision |

## License

MIT
