<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        DB::table('prodi')->insert([
            [
                'id' => 3,
                'alias' => "ESY",
                'nama' => "Ekonomi Syariah",
                'jenjang' => "S1",
                'warna' => "success",
            ],
            [
                'id' => 4,
                'alias' => "BKI",
                'nama' => "Bimbingan dan Konseling Islam",
                'jenjang' => "S1",
                'warna' => "success",
            ],
            [
                'id' => 5,
                'alias' => "KPI",
                'nama' => "Komunikasi dan Penyiaran Islam",
                'jenjang' => "S1",
                'warna' => "success",
            ],
            [
                'id' => 6,
                'alias' => "AS-HK",
                'nama' => "Hukum Keluarga Islam (Ahwal Al Syakhshiyah)",
                'jenjang' => "S1",
                'warna' => "success",
            ],
            [
                'id' => 7,
                'alias' => "SKI",
                'nama' => "Sejarah Peradaban Islam",
                'jenjang' => "S1",
                'warna' => "success",
            ],
            [
                'id' => 8,
                'alias' => "PAI",
                'nama' => "Pendidikan Agama Islam",
                'jenjang' => "S1",
                'warna' => "success",
            ],
            [
                'id' => 9,
                'alias' => "MPI",
                'nama' => "Manajemen Pendidikan Islam",
                'jenjang' => "S1",
                'warna' => "success",
            ],
            [
                'id' => 10,
                'alias' => "PBA",
                'nama' => "Pendidikan Bahasa Arab",
                'jenjang' => "S1",
                'warna' => "success",
            ],
            [
                'id' => 11,
                'alias' => "PBAS2",
                'nama' => "Pendidikan Bahasa Arab S2",
                'jenjang' => "S2",
                'warna' => "primary",
            ],
            [
                'id' => 12,
                'alias' => "MPIS2",
                'nama' => "Manajemen Pendidikan Islam S2",
                'jenjang' => "S2",
                'warna' => "primary",
            ],
            [
                'id' => 13,
                'alias' => "PAIS3",
                'nama' => "Pendidikan Agama Islam S3",
                'jenjang' => "S3",
                'warna' => "danger",
            ],
        ]);
        DB::table('api')->insert([
            [
                'uri' => 'https://api.fonnte.com/send',
                'type' => 'notif_wa_fonnte',
                'token' => '8u98uP5WD5XHDCMre9Ux',
                'userkey' => null,
            ],
            [
                'uri' => 'https://console.zenziva.net/wareguler/api/sendWA/',
                'type' => 'notif_wa_zenziva',
                'token' => '50c4a36e0d5e244747ff3f62',
                'userkey' => '4b7c9a2bda06',
            ],
        ]);
        DB::table('status')->insert([
            [
                'nama' => 'terdaftar',
                'warna' => 'dark',
            ],
            [
                'nama' => 'terverifikasi',
                'warna' => 'primary',
            ],
            [
                'nama' => 'datang',
                'warna' => 'warning',
            ],
            [
                'nama' => 'selesai',
                'warna' => 'success',
            ],
            [
                'nama' => 'ditolak',
                'warna' => 'danger',
            ],
            [
                'nama' => 'bermasalah',
                'warna' => 'danger',
            ],
        ]);
        DB::table('setting')->insert([
            [
                'slug' => 'otp',
                'value' => 0,
            ],
            [
                'slug' => 'vendor_notifikasi',
                'value' => 'fonnte',
            ],
            [
                'slug' => 'isi_pesan_wa',
                'value' => 'assalamualaikum',
            ],
        ]);
        DB::table('tahun')->insert([
            // [
            //     'kode' => '23',
            //     'nama' => '2023',
            //     'status' => 'Y',
            // ],
            [
                'kode' => '20241',
                'nama' => '2024',
                'status' => 'Y',
            ],
        ]);
        DB::table('role')->insert([
            [
                'nama' => 'admin',
                'prioritas' => 1,
            ],
            [
                'nama' => 'peserta',
                'prioritas' => 2,
            ],
            [
                'nama' => 'keuangan',
                'prioritas' => 3,
            ],
            [
                'nama' => 'dpl',
                'prioritas' => 4,
            ],
            [
                'nama' => 'pengawas',
                'prioritas' => 5,
            ],
            [
                'nama' => 'pamong',
                'prioritas' => 6,
            ],
        ]);

        /*DB::table('users')->insert([*/
        /*    [*/
        /*        'username' => 'admin',*/
        /*        'nama' => 'Admin',*/
        /*        'email' => 'admin@gmail.com',*/
        /*        'password' => Hash::make('prolapdalwa123'),*/
        /*        'role_id' => 1,*/
        /*        'jenis_kelamin' => '*',*/
        /*        'no_unik' => 'prolapdalwa123'*/
        /*    ],*/
        /*    [*/
        /*        'username' => 'keuangan',*/
        /*        'nama' => 'Keuangan',*/
        /*        'email' => 'keuangan@gmail.com',*/
        /*        'password' => Hash::make('keuangandalwa'),*/
        /*        'role_id' => 3,*/
        /*        'jenis_kelamin' => '*',*/
        /*        'no_unik' => 'keuangandalwa'*/
        /*    ],*/
        /*    [*/
        /*        'username' => 'keuanganbanin',*/
        /*        'nama' => 'Keuangan Banin',*/
        /*        'email' => 'keuanganbanin@gmail.com',*/
        /*        'password' => Hash::make('keuangandalwa'),*/
        /*        'role_id' => 3,*/
        /*        'jenis_kelamin' => 'Laki-laki',*/
        /*        'no_unik' => 'keuangandalwa'*/
        /*    ],*/
        /*    [*/
        /*        'username' => 'keuanganbanat',*/
        /*        'nama' => 'Keuangan Banat',*/
        /*        'email' => 'keuanganbanat@gmail.com',*/
        /*        'password' => Hash::make('keuangandalwa'),*/
        /*        'role_id' => 3,*/
        /*        'jenis_kelamin' => 'Perempuan',*/
        /*        'no_unik' => 'keuangandalwa'*/
        /*    ],*/
        /*]);*/

        DB::table('list_dokumen')->insert([
            [
                'tipe' => 'Foto Resmi Background Merah',
                'status' => 'wajib',
                'upload' => 'png,jpg,jpeg',
            ],
            [
                'tipe' => 'Kwitansi Lunas Jamiah',
                'status' => 'opsional',
                'upload' => 'jpg, jpeg,pdf',
            ],
        ]);

        DB::table('jadwal')->insert([
            [
                'tahun_id' => 1,
                'mulai' => \Carbon::parse('2024-05-24'),
                'berakhir' => \Carbon::parse('2024-10-10'),
            ]
        ]);

        DB::table('kuota')->insert([
            [
                'tahun_id' => 1,
                'jenis' => "Laki-laki",
                'kuota' => 10
            ],
            [
                'tahun_id' => 1,
                'jenis' => "Perempuan",
                'kuota' => 20
            ],
        ]);

        DB::table('biaya')->insert([
            [
                'tahun_id' => 1,
                'jenjang' => "S1",
                'jumlah' => 2500000
            ],
            [
                'tahun_id' => 1,
                'jenjang' => "S2",
                'jumlah' => 2500000
            ],
            [
                'tahun_id' => 1,
                'jenjang' => "S3",
                'jumlah' => 2500000
            ],
        ]);
    }
}

