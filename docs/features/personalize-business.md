# Feature Planning: Business Personalization

## Overview

Fitur ini tampil di **dashboard client** dan memberikan dua jenis rekomendasi:

1. **Akun serupa** — akun Instagram/TikTok bisnis lain yang relevan dengan industri client
2. **Rekomendasi konten** — video serupa dari konten yang pernah client upload di Instagram/TikTok mereka sendiri

Tujuannya adalah memberi nilai tambah kepada client Koneva: mereka tidak hanya menerima deliverable, tapi juga insight tentang posisi mereka di pasar dan inspirasi konten.

---

## User Stories

- Sebagai client, saya ingin menghubungkan akun Instagram dan TikTok saya ke portal supaya Koneva bisa menganalisis konten saya.
- Sebagai client, saya ingin melihat akun bisnis lain di industri saya yang performanya bagus, sebagai referensi kompetitor atau inspirasi.
- Sebagai client, saya ingin melihat video dari feed saya sendiri yang dikelompokkan atau dibandingkan berdasarkan performa dan relevansi topik.

---

## Alur Fitur

```
Client Dashboard
└── Tab "Insights" (baru)
    ├── Connect Accounts (Instagram & TikTok)
    ├── Similar Accounts
    │   └── Grid akun serupa berdasarkan industri + kata kunci
    └── Content Recommendations
        └── Video dari feed client, disortir/grouped berdasarkan topik/performa
```

---

## Arsitektur Teknis

### Komponen Baru

| Komponen | Keterangan |
|---|---|
| `SocialAccount` model | Menyimpan token OAuth dan handle Instagram/TikTok milik client |
| `SocialPost` model | Cache video/post yang sudah di-fetch dari API |
| `SocialInsightController` | Controller client-facing untuk halaman Insights |
| `FetchSocialPostsJob` | Queue job untuk fetch konten dari API secara background |
| `SimilarAccountService` | Logic pencarian akun serupa (query API berdasarkan hashtag/industri) |

### Flow Data

```
Client connect akun (OAuth)
    → simpan token di social_accounts
    → dispatch FetchSocialPostsJob
        → fetch posts via Instagram Graph API / TikTok API
        → simpan ke social_posts (cache)
            → tampilkan di dashboard client
                → SimilarAccountService query akun serupa
                    → tampilkan grid rekomendasi
```

---

## Database Schema

### `social_accounts`
```
id
client_id          FK → clients.id
platform           enum: instagram, tiktok
handle             string
access_token       text (encrypted)
token_expires_at   datetime nullable
last_synced_at     datetime nullable
timestamps
```

### `social_posts`
```
id
social_account_id  FK → social_accounts.id
platform_post_id   string (ID dari Instagram/TikTok)
type               enum: video, image, carousel
caption            text nullable
hashtags           json nullable
thumbnail_url      string nullable
video_url          string nullable
view_count         integer nullable
like_count         integer nullable
comment_count      integer nullable
posted_at          datetime
fetched_at         datetime
timestamps
```

---

## API Integrations

### Instagram Graph API
- **Auth**: OAuth 2.0 — client authorize via Facebook Login
- **Endpoints yang dipakai**:
  - `GET /me/media` — ambil daftar post/video
  - `GET /{media-id}` — detail post (metrics, caption, hashtags)
  - `GET /ig_hashtag_search` + `GET /{hashtag-id}/recent_media` — cari konten serupa
- **Limitasi**: hanya akun **Business/Creator** yang bisa akses Graph API, bukan personal account
- **Env baru**: `INSTAGRAM_APP_ID`, `INSTAGRAM_APP_SECRET`

### TikTok Content Posting API (Login Kit)
- **Auth**: OAuth 2.0 via TikTok Login Kit
- **Endpoints yang dipakai**:
  - `GET /v2/video/list/` — ambil video milik user
  - `GET /v2/video/query/` — detail per video (views, likes, hashtags)
- **Limitasi**: TikTok API developer access memerlukan review/approval dari TikTok
- **Env baru**: `TIKTOK_CLIENT_KEY`, `TIKTOK_CLIENT_SECRET`

### Similar Account Discovery
Menggunakan Instagram hashtag search endpoint dari RapidAPI. Hashtag dipetakan dari field `industry` client (lihat `SocialInsightService::INDUSTRY_HASHTAGS`) + hashtag yang paling sering muncul di konten client sendiri.

---

## Implementasi Phases

### Phase 1 — Connect & Fetch (MVP)
- [ ] Tambah tabel `social_accounts` dan `social_posts`
- [ ] Halaman "Connect Accounts" di client dashboard (Instagram dulu)
- [ ] OAuth flow Instagram (redirect → callback → simpan token)
- [ ] `FetchSocialPostsJob` — fetch dan cache video client
- [ ] Tampilkan grid video milik client di tab Insights

### Phase 2 — Content Recommendations
- [ ] Parsing hashtags dari caption video
- [ ] Grouping/sorting video berdasarkan hashtag dominan dan view count
- [ ] UI rekomendasi: "Konten serupa dari feed kamu" dengan filter topik

### Phase 3 — Similar Accounts
- [ ] `SimilarAccountService` — query Instagram hashtag search berdasarkan industri
- [ ] Tampilkan grid akun serupa (foto profil, username, follower count jika tersedia)
- [ ] TikTok OAuth + fetch video (setelah developer access approved)

### Phase 4 — Refinement
- [ ] Background refresh otomatis (scheduled job, misal tiap 24 jam)
- [ ] Notifikasi ke client jika ada update insight baru
- [ ] Admin bisa lihat status sync akun client

---

## Risiko & Open Questions

| Risiko | Mitigasi |
|---|---|
| TikTok API approval bisa lama (weeks) | Mulai dengan Instagram dulu, TikTok di Phase 3 |
| Instagram hanya support Business/Creator account | Tampilkan pesan jelas saat connect: "Pastikan akun Instagram kamu adalah akun Bisnis" |
| Token Instagram expire setelah 60 hari | Implementasi token refresh di `FetchSocialPostsJob` |
| Rate limit API | Cache hasil di `social_posts`, jangan fetch ulang kecuali sudah >6 jam |
| Privacy — client mungkin tidak mau konten mereka di-fetch | Connect bersifat opsional, dengan consent eksplisit |

---

## Env Variables yang Dibutuhkan

```env
# Instagram
INSTAGRAM_APP_ID=
INSTAGRAM_APP_SECRET=
INSTAGRAM_REDIRECT_URI="${APP_URL}/auth/instagram/callback"

# TikTok
TIKTOK_CLIENT_KEY=
TIKTOK_CLIENT_SECRET=
TIKTOK_REDIRECT_URI="${APP_URL}/auth/tiktok/callback"
```

Kedua credential ini perlu didaftarkan di:
- [Meta for Developers](https://developers.facebook.com/) untuk Instagram
- [TikTok for Developers](https://developers.tiktok.com/) untuk TikTok

---

## File yang Akan Dibuat/Diubah

```
app/
  Models/
    SocialAccount.php         (baru)
    SocialPost.php            (baru)
  Http/Controllers/Client/
    SocialInsightController.php  (baru)
  Http/Controllers/Auth/
    InstagramAuthController.php  (baru)
    TiktokAuthController.php     (baru)
  Jobs/
    FetchSocialPostsJob.php   (baru)
  Services/
    SimilarAccountService.php (baru)

database/migrations/
  ..._create_social_accounts_table.php  (baru)
  ..._create_social_posts_table.php     (baru)

resources/views/client/
  insights.blade.php          (baru)
  partials/
    social-connect.blade.php  (baru)
    similar-accounts.blade.php (baru)
    content-grid.blade.php    (baru)

routes/web.php                (tambah routes client insights)
routes/auth.php               (tambah OAuth routes Instagram & TikTok)
```
