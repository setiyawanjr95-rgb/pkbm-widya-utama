# PKBM Widya Utama

Aplikasi pendidikan PKBM Widya Utama.

- `android/`: app Android warga belajar (Flutter)
- `web/`: web admin (Filament) + REST API (Laravel 13, Sanctum, MariaDB). Detail di `web/README-PKBM.md`.
- `deploy/`: script pasang & update di Proxmox

## Pasang web admin (shell Proxmox, sekali saja)
```
bash <(curl -fsSL https://raw.githubusercontent.com/setiyawanjr95-rgb/pkbm-widya-utama/main/deploy/install-web.sh)
```

## Update web admin
```
pct exec 102 -- /usr/local/bin/update-pkbm-web
```

## Build ulang APK dari kode terbaru
```
pct exec 100 -- bash -c "curl -fsSL https://raw.githubusercontent.com/setiyawanjr95-rgb/pkbm-widya-utama/main/deploy/update-apk.sh | bash"
```
