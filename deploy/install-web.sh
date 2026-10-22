#!/usr/bin/env bash
# =====================================================================
#  Setup kontainer web admin + API PKBM Widya Utama (jalankan di host Proxmox)
#  Pakai: bash <(curl -fsSL https://raw.githubusercontent.com/setiyawanjr95-rgb/pkbm-widya-utama/main/deploy/install-web.sh)
# =====================================================================
set -euo pipefail

# ---------- Konfigurasi (boleh diubah) ----------
REPO="${REPO:-setiyawanjr95-rgb/pkbm-widya-utama}"
BRANCH="${BRANCH:-main}"
CTID="${CTID:-102}"
CT_HOSTNAME="pkbm-web"
CORES=2
RAM_MB=2048
SWAP_MB=1024
DISK_GB=20
STORAGE="local-lvm"
TPL_STORAGE="local"
BRIDGE="vmbr0"
IP_CONFIG="${IP_CONFIG:-dhcp}"          # atau statis, contoh: "192.168.101.7/24,gw=192.168.101.1"
APP_URL="${APP_URL:-https://pkbm.netwarga.my.id}"
SEED_DEMO="${SEED_DEMO:-true}"          # true = isi data contoh (Siti Aisyah, 4 mapel) buat uji coba
CT_PASSWORD="$(openssl rand -hex 6)"
# -----------------------------------------------

log() { echo -e "\n\033[1;34m==> $*\033[0m"; }

if pct status "$CTID" >/dev/null 2>&1; then
  echo "Kontainer $CTID sudah ada. Untuk update aplikasi pakai: pct exec $CTID -- update-pkbm-web"
  exit 1
fi

log "Kontainer baru: ID $CTID, hostname $CT_HOSTNAME"
pveam update >/dev/null
TPL="$(pveam available --section system | awk '/debian-12-standard/ {print $2}' | sort -V | tail -1)"
[ -n "$TPL" ] || { echo "Template Debian 12 tidak ditemukan"; exit 1; }
pveam list "$TPL_STORAGE" | grep -q "$TPL" || pveam download "$TPL_STORAGE" "$TPL"

pct create "$CTID" "$TPL_STORAGE:vztmpl/$TPL" \
  --hostname "$CT_HOSTNAME" --cores "$CORES" --memory "$RAM_MB" --swap "$SWAP_MB" \
  --rootfs "$STORAGE:$DISK_GB" \
  --net0 "name=eth0,bridge=$BRIDGE,ip=$IP_CONFIG" \
  --unprivileged 1 --features nesting=1 --onboot 1 \
  --password "$CT_PASSWORD" \
  --description "Web admin + API PKBM Widya Utama (Laravel + Filament + MariaDB). $APP_URL"
pct start "$CTID"

log "Menunggu jaringan kontainer"
for i in $(seq 1 60); do
  if pct exec "$CTID" -- bash -c "getent hosts deb.debian.org >/dev/null && ip -4 addr show eth0 | grep -q inet"; then break; fi
  sleep 2
done

log "Mengambil script instalasi"
curl -fsSL "https://raw.githubusercontent.com/$REPO/$BRANCH/deploy/ct-setup-web.sh" -o /tmp/pkbm-ct-setup-web.sh
pct push "$CTID" /tmp/pkbm-ct-setup-web.sh /root/ct-setup.sh

log "Install di dalam kontainer (5-10 menit)"
pct exec "$CTID" -- env APP_URL="$APP_URL" SEED_DEMO="$SEED_DEMO" REPO="$REPO" BRANCH="$BRANCH" bash /root/ct-setup.sh </dev/null

CT_IP="$(pct exec "$CTID" -- bash -c "ip -4 -o addr show eth0 | awk '{print \$4}' | cut -d/ -f1")"
ADMIN_PASS="$(pct exec "$CTID" -- bash -c ". /root/pkbm-web-credentials.txt && echo \$ADMIN_PASS")"
cat <<DONE

=====================================================================
 Web admin PKBM siap!
 Lokal        : http://$CT_IP/admin
 Publik       : $APP_URL/admin   (setelah hostname ditambah di Cloudflare)
 API app      : $APP_URL/api
 Login admin  : email admin@pkbm.local / password $ADMIN_PASS
 Root CT $CTID : password $CT_PASSWORD
 Kredensial tersimpan di kontainer: /root/pkbm-web-credentials.txt
 Akun contoh app (kalau SEED_DEMO=true): NISN 1234567890 / siswa123

 Cloudflare: Zero Trust > Networks > Tunnels > tunnel yang dipakai LXC 100
 > Public Hostname > Add: subdomain pkbm, domain netwarga.my.id,
 service HTTP, URL $CT_IP:80
=====================================================================
DONE
