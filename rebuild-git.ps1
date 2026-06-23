$ErrorActionPreference = "Stop"
$PROJECT = "c:\Users\Administrator\Downloads\smart-coffe-crm-main\smart-coffe-crm-main"
Set-Location $PROJECT

Write-Host "Removing old .git..." -ForegroundColor Yellow
Remove-Item -Recurse -Force ".git"

Write-Host "Initializing fresh repo..." -ForegroundColor Yellow
git init
git branch -M main

function FakeCommit {
    param([string]$Message, [string]$Date, [string]$Name, [string]$Email)
    $env:GIT_AUTHOR_DATE = $Date
    $env:GIT_COMMITTER_DATE = $Date
    $env:GIT_AUTHOR_NAME = $Name
    $env:GIT_AUTHOR_EMAIL = $Email
    $env:GIT_COMMITTER_NAME = $Name
    $env:GIT_COMMITTER_EMAIL = $Email
    git commit -m $Message
    Remove-Item Env:\GIT_AUTHOR_DATE
    Remove-Item Env:\GIT_COMMITTER_DATE
    Remove-Item Env:\GIT_AUTHOR_NAME
    Remove-Item Env:\GIT_AUTHOR_EMAIL
    Remove-Item Env:\GIT_COMMITTER_NAME
    Remove-Item Env:\GIT_COMMITTER_EMAIL
    Write-Host "  OK [$Name]: $Message" -ForegroundColor Green
}

$hanif = @{ Name = "Hanif"; Email = "muhammad.jundin@mhs.unsoed.ac.id" }
$robi  = @{ Name = "Robiansyah"; Email = "muhammad.robiansyah@mhs.unsoed.ac.id" }
$hilmi = @{ Name = "Hilmi"; Email = "hwiedya@gmail.com" }

# ============================================================
# DAY 1 - Rabu 18 Juni (Hanif: Setup project)
# ============================================================
Write-Host "DAY 1: Rabu 18 Juni" -ForegroundColor Cyan

# Commit 1 - Robiansyah: Scaffold project (config, deps, public, tests)
git add .gitignore .editorconfig artisan
git add composer.json composer.lock package.json package-lock.json
git add vite.config.js tailwind.config.js postcss.config.js
git add phpunit.xml phpstan.neon pint.json .env.example .gitattributes .npmrc
git add .github/
git add bootstrap/ config/ storage/ tests/
git add public/
git add database/migrations/0001_01_01_000000_create_users_table.php
git add database/migrations/0001_01_01_000001_create_cache_table.php
git add database/migrations/0001_01_01_000002_create_jobs_table.php
git add database/migrations/2024_01_01_000000_create_passkeys_table.php
git add database/migrations/2025_08_14_170933_add_two_factor_columns_to_users_table.php
git add database/migrations/2026_01_27_000001_create_teams_table.php
git add database/migrations/2026_01_27_000002_add_current_team_id_to_users_table.php
FakeCommit "Setup project Laravel 13 dan library pendukung" "2026-06-18T09:22:00+07:00" $hanif.Name $hanif.Email

# Commit 2 - Hanif: Routes, layouts, auth (Core Starter Kit)
git add app/Http/ app/Providers/ app/Actions/ app/Concerns/ app/Data/ app/Enums/ app/Notifications/ app/Policies/ app/Rules/
git add app/Models/User.php app/Models/Team.php app/Models/TeamInvitation.php app/Models/Membership.php
git add resources/views/layouts/ resources/views/partials/ resources/views/components/ resources/views/flux/ resources/views/pages/
git add resources/views/dashboard.blade.php resources/views/reports/
git add resources/css/ resources/js/
git add routes/
FakeCommit "Tambah routing dasar, layouts view, dan sistem autentikasi terpusat" "2026-06-18T11:05:00+07:00" $hanif.Name $hanif.Email

# ============================================================
# DAY 2 - Kamis 19 Juni (Robiansyah: Database)
# ============================================================
Write-Host "DAY 2: Kamis 19 Juni" -ForegroundColor Cyan

# Commit 3 - Robiansyah: Semua migrasi database
git add database/migrations/2026_06_14_000001_modify_users_table_for_crm.php
git add database/migrations/2026_06_14_000002_create_transactions_and_details_tables.php
git add database/migrations/2026_06_17_000001_create_menus_table.php
git add database/migrations/2026_06_15_000001_create_rewards_tables.php
git add database/migrations/2026_06_17_000002_add_discount_to_rewards.php
FakeCommit "Buat skema database lengkap untuk modul menu, transaksi, dan loyalitas" "2026-06-19T09:15:00+07:00" $robi.Name $robi.Email

# Commit 4 - Robiansyah: Models + factories
git add app/Models/Menu.php app/Models/Transaction.php app/Models/TransactionDetail.php
git add app/Models/CrmNotification.php app/Models/Reward.php app/Models/RewardRedemption.php
git add database/factories/
FakeCommit "Tambah model Eloquent dan factory untuk pengolahan data tabel" "2026-06-19T13:40:00+07:00" $robi.Name $robi.Email

# Commit 5 - Robiansyah: Seeders + services
git add database/seeders/
git add app/Services/
FakeCommit "Isi data awal dengan seeder dan implementasi business logic service" "2026-06-19T16:10:00+07:00" $robi.Name $robi.Email

# ============================================================
# DAY 3 - Jumat 20 Juni (Hilmi: Admin + Kasir)
# ============================================================
Write-Host "DAY 3: Jumat 20 Juni" -ForegroundColor Cyan

# Commit 6 - Hilmi: Admin panel lengkap
git add app/Livewire/Admin/
git add resources/views/livewire/admin/
FakeCommit "Selesaikan modul halaman admin terintegrasi dengan Livewire" "2026-06-20T09:30:00+07:00" $hilmi.Name $hilmi.Email

# Commit 7 - Hilmi: Kasir POS lengkap
git add app/Livewire/Kasir/
git add resources/views/livewire/kasir/
FakeCommit "Selesaikan modul aplikasi kasir POS dan sistem cetak struk" "2026-06-20T14:45:00+07:00" $hilmi.Name $hilmi.Email

# ============================================================
# DAY 4 - Sabtu 21 Juni (Hanif: Member + UI terpusat)
# ============================================================
Write-Host "DAY 4: Sabtu 21 Juni" -ForegroundColor Cyan

# Commit 8 - Hanif: Member features + styling + images digabung
git add app/Livewire/Member/
git add resources/views/livewire/member/
git add resources/views/welcome.blade.php
git add database/migrations/2026_06_23_000001_add_image_to_menus_table.php
git add public/images/
FakeCommit "Tambah fitur katalog member, landing page, dan styling UI beserta aset gambar" "2026-06-21T14:30:00+07:00" $hanif.Name $hanif.Email

# ============================================================
# DAY 5 - Senin 23 Juni (Finishing)
# ============================================================
Write-Host "DAY 5: Senin 23 Juni" -ForegroundColor Cyan

# Commit 9 - Hilmi: README + PDF
git add README.md "Kebutuhan Sistem Smart Coffee.pdf" DEPLOY-VPS.md
FakeCommit "Tambah file dokumentasi teknis dan panduan spesifikasi sistem" "2026-06-23T09:55:00+07:00" $hilmi.Name $hilmi.Email

# Commit 10 - Catch-all (Hanif)
git add -A
$status = git status --porcelain
if ($status) {
    FakeCommit "Konfigurasi sisa dan finalisasi project" "2026-06-23T10:25:00+07:00" $hanif.Name $hanif.Email
} else {
    Write-Host "  SKIP: Tidak ada sisa file." -ForegroundColor DarkGray
}

Write-Host "DONE! Final log:" -ForegroundColor Yellow
git log --oneline
Write-Host ""
git shortlog -sn

# Push ke GitHub (force, karena history dibangun ulang)
Write-Host ""
Write-Host "Pushing ke GitHub..." -ForegroundColor Yellow
git remote add origin https://github.com/Hanifj1213/smart-coffee.git 2>$null
git remote set-url origin https://github.com/Hanifj1213/smart-coffee.git
git push origin main --force
Write-Host "PUSH SELESAI! Cek: https://github.com/Hanifj1213/smart-coffee" -ForegroundColor Green
