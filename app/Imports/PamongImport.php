<?php

namespace App\Imports;

use App\Models\Pamong;
use App\Models\Peserta;
use App\Models\PamongPeserta;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PamongImport implements ToCollection, WithHeadingRow
{
    private $pamong;
    private $newData = 0;
    private $total = 0;

    public function __construct(Pamong $pamong)
    {
        $this->pamong = $pamong;
    }
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if ($row['nim']) {
                $peserta = Peserta::where('nim', $row['nim'])->first();

                if (!$peserta) {
                    return abort(500, 'NIM ' . $row['nim'] . ' tidak ditemukan');
                }

                $this->total++;

                $checkPeserta = PamongPeserta::where('peserta_id', $peserta->id)->exists();
                if ($checkPeserta) {
                    continue;
                }

                $pamongPeserta = PamongPeserta::where([
                    ['pamong_id', $this->pamong->id],
                    ['peserta_id', $peserta->id]
                ])->first();

                if ($pamongPeserta) {
                    continue;
                }

                $this->newData++;
                PamongPeserta::create([
                    'pamong_id' => $this->pamong->id,
                    'peserta_id' => $peserta->id
                ]);
            }
        }
    }

    public function getResponse()
    {
        return "$this->newData data baru dari $this->total total data";
    }
}
