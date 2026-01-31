<?php

namespace App\Imports;

use App\Models\Posko;
use App\Models\Peserta;
use App\Models\PoskoPeserta;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PoskoImport implements ToCollection, WithHeadingRow
{
    private $posko;
    private $newData = 0;
    private $total = 0;

    public function __construct(Posko $posko)
    {
        $this->posko = $posko;
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
                
                $checkPosko = PoskoPeserta::where('peserta_id', $peserta->id)->exists();
                if($checkPosko){
                    continue;
                }

                $poskoPeserta = PoskoPeserta::where([
                    ['posko_id', $this->posko->id],
                    ['peserta_id', $peserta->id]
                ])->first();

                if ($poskoPeserta) {
                    continue;
                }

                $this->newData++;
                PoskoPeserta::create([
                    'posko_id' => $this->posko->id,
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
