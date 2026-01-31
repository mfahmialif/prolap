<?php

namespace App\Http\Services;

class BulkData
{
    public const dirGdrive = [
        "dokumen" => "/16M1lFCtSK0asb0f-aGxVZtqvht7PXKf8/",
        "root" => "/",
    ];
    public const jenisKelamin = [
        "Laki-Laki",
        "Perempuan"
    ];
    public const jenisKelaminAssoc = [
        "Laki-Laki" => "Laki-Laki",
        "Perempuan" => "Perempuan"
    ];
    public const pekerjaan = [
        "PIMPINAN PONDOK",
        "USTADZ",
        "GURU",
        "Lainnya"
    ];
    public const pendidikanAsal = [
        "Pondok Pesantren / Madrasah",
        "Sekolah Umum (Negeri/Swasta)",
        "Lainnya"
    ];

    public const statusValueNama = [
        [
            "value" => 1,
            "nama" => "AKTIF"
        ],
        [
            "value" => 0,
            "nama" => "TIDAK AKTIF"
        ]
    ];
    public const vendor = ["fonnte", "zenziva"];
    public const color = [
        "aktif" => "success",
        "tidak aktif" => "danger",
        1 => "success",
        0 => "danger",
        "belum" => "warning",
    ];
    public const maxSizeUpload = 102400;

    public const bobotSupervisor = [
        'dpl' => 60,
        'pengawas' => 20,
        'pamong' => 0
    ];

    public const uploadType = "local"; // google_drive or local
}
