#!/usr/bin/env bash

set -euo pipefail

project_root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
release_name="${1:-bmd-qrcode-release}"
output_dir="$project_root/dist"
archive_path="$output_dir/$release_name.zip"
stage_dir="$(mktemp -d "${TMPDIR:-/tmp}/bmd-qrcode-release.XXXXXX")"

cleanup() {
    rm -rf "$stage_dir"
}

trap cleanup EXIT

if [[ ! "$release_name" =~ ^[A-Za-z0-9._-]+$ ]]; then
    echo "Nama rilis hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung." >&2
    exit 1
fi

if [[ -e "$archive_path" ]]; then
    echo "Arsip sudah ada: $archive_path. Gunakan nama rilis lain agar tidak tertimpa." >&2
    exit 1
fi

cd "$project_root"

composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
npm ci
npm run build

if [[ ! -d vendor || ! -f public/build/manifest.json ]]; then
    echo "Vendor atau hasil build frontend tidak ditemukan. Arsip tidak dibuat." >&2
    exit 1
fi

mkdir -p "$output_dir"
mkdir -p "$stage_dir/$release_name"

rsync -a \
    --exclude '.env' \
    --exclude '.env.*' \
    --exclude '.git' \
    --exclude 'dist' \
    --exclude 'node_modules' \
    --exclude 'public/hot' \
    --exclude 'public/fonts-manifest.dev.json' \
    --exclude 'storage/logs/*' \
    --exclude 'storage/framework/cache/data/*' \
    --exclude 'storage/framework/sessions/*' \
    --exclude 'storage/framework/views/*' \
    --exclude 'storage/app/private/exports/*' \
    --exclude 'tests' \
    ./ "$stage_dir/$release_name/"

(
    cd "$stage_dir"
    zip -qr "$archive_path" "$release_name"
)

echo "Arsip deploy selesai: $archive_path"
echo "Isi .env langsung di server, lalu jalankan migrate, seed, storage:link, dan optimize."
