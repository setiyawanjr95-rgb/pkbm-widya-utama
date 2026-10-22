#!/usr/bin/env bash
# Ambil source app Android terbaru dari GitHub lalu build APK di LXC 100 (pkbm-apk).
# Pakai (di shell Proxmox): pct exec 100 -- bash -c "curl -fsSL https://raw.githubusercontent.com/setiyawanjr95-rgb/pkbm-widya-utama/main/deploy/update-apk.sh | bash"
set -euo pipefail
REPO="${REPO:-setiyawanjr95-rgb/pkbm-widya-utama}"
BRANCH="${BRANCH:-main}"
APP=/opt/pkbm/app
TMP="$(mktemp -d)"
curl -fsSL "https://codeload.github.com/$REPO/tar.gz/refs/heads/$BRANCH" | tar -xz -C "$TMP" --strip-components=2 --wildcards '*/android/*'
rm -rf "$APP/lib"
cp -r "$TMP/lib" "$APP/lib"
cp "$TMP/pubspec.yaml" "$APP/pubspec.yaml"
cp "$TMP/android/app/src/main/AndroidManifest.xml" "$APP/android/app/src/main/AndroidManifest.xml"
rm -rf "$TMP"
/usr/local/bin/build-pkbm
# Link publik (Cloudflare) memakai nama tetap, arahkan ke build terbaru
ln -sf "$(readlink /opt/pkbm/dist/pkbm-widya-utama-terbaru.apk)" /opt/pkbm/dist/pkbm-widya-utama.apk
echo "Link publik: https://apk-pkbm.netwarga.my.id/pkbm-widya-utama.apk"
