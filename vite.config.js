import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',

                // CSS ADMIN
                'resources/css/admin/layout.css',
                'resources/css/admin/admin.css',
                'resources/css/admin/dashboard.css',
                'resources/css/admin/kategori.css',
                'resources/css/admin/kelas.css',
                'resources/css/admin/laporan.css',
                'resources/css/admin/orang-tua.css',
                'resources/css/admin/pembayaran.css',
                'resources/css/admin/siswa.css',
                'resources/css/admin/tagihan.css',
                'resources/css/admin/profil.css',
                'resources/css/admin/tahun-ajaran.css',

                // CSS AUTH
                'resources/css/auth/login.css',

                // CSS ORANG TUA
                'resources/css/orang-tua/layout.css',
                'resources/css/orang-tua/dashboard.css',
                'resources/css/orang-tua/pembayaran.css',
            ],
            refresh: true,
        }),
    ],
});