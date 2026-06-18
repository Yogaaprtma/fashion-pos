#!/bin/bash

# Pastikan script dijalankan dari root project Laravel
if [ ! -f "artisan" ]; then
    echo "Harap jalankan script ini dari folder root proyek Laravel."
    exit 1
fi

echo "Memulai proses push commit satu per satu secara otomatis..."

# Daftar file yang ada (untracked dan modified)
FILES=$(git status --porcelain | awk '{print $2}')

if [ -z "$FILES" ]; then
    echo "Tidak ada file yang berubah atau baru untuk di-commit."
    exit 0
fi

# Loop setiap file dan lakukan git add, commit, push
for file in $FILES; do
    echo "Memproses: $file"
    git add "$file"
    git commit -m "feat: add $file"
    
    # Optional: beri jeda 1 detik agar commit timestamp berbeda
    sleep 1
done

echo "Mulai push ke remote..."
git push origin main

echo "Selesai! Semua file telah dipush secara bertahap."
