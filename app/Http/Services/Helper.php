<?php
namespace App\Http\Services;

use App\Models\Jadwal;
use App\Models\PoskoDpl;
use App\Models\Tahun;

class Helper
{
    public static function idrToDouble($idrString)
    {
        $idrString = preg_replace("/[^0-9]/", "", $idrString);

        // Convert the string to a double
        $idrDecimal = (double) $idrString;
        return $idrDecimal;
    }
    public static function doubleToIdr($idrString)
    {
        return 'Rp ' . number_format($idrString, 0, ',', '.');

    }

    public static function terbilang($nilai)
    {
        if ($nilai < 0) {
            $hasil = "minus " . trim(Helper::penyebut($nilai));
        } else {
            $hasil = trim(Helper::penyebut($nilai));
        }
        return $hasil;
    }

    public static function penyebut($nilai)
    {
        $nilai = abs($nilai);
        $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $temp  = "";

        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = Helper::penyebut($nilai - 10) . " Belas";
        } else if ($nilai < 100) {
            $temp = Helper::penyebut($nilai / 10) . " Puluh" . Helper::penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . Helper::penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = Helper::penyebut($nilai / 100) . " Ratus" . Helper::penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . Helper::penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = Helper::penyebut($nilai / 1000) . " Ribu" . Helper::penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = Helper::penyebut($nilai / 1000000) . " Juta" . Helper::penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = Helper::penyebut($nilai / 1000000000) . " Milyar" . Helper::penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = Helper::penyebut($nilai / 1000000000000) . " Triliun" . Helper::penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

    public static function getUppercaseChars($inputString)
    {
        $uppercaseChars = '';

        // Loop through each character in the string
        for ($i = 0; $i < strlen($inputString); $i++) {
            $char = $inputString[$i];

            // Check if the character is uppercase
            if (ctype_upper($char)) {
                // Append the uppercase character to the result string
                $uppercaseChars .= $char;
            }
        }

        return $uppercaseChars;
    }

    public static function changeFormatSymbol($string)
    {
        $charactersToReplace = ['\\', '/', ':', '*', '?', '<', '>', '|'];
        $replacement         = '-';

        $newString = \Str::replace($charactersToReplace, $replacement, $string);
        $newString = preg_replace('/[^A-Za-z0-9\-\_\.]/', '_', $newString);
        return $newString;
    }

    public static function formatNumber($angka)
    {
        return number_format($angka, 0, ",", ".");
    }

    /**
     * Summary of getEnumValues
     * @param mixed $table
     * @param mixed $column
     * @param mixed $deleteColumn [array]
     * @return array
     */
    public static function getEnumValues($table, $column, $deleteColumn = false)
    {
        try {
            $type = \DB::select(\DB::raw("SHOW COLUMNS FROM $table WHERE Field = '$column'"))[0]->Type;
            preg_match('/^enum\((.*)\)$/', $type, $matches);
            $enum = [];

            foreach (explode(',', $matches[1]) as $value) {
                $v = trim($value, "'");
                array_push($enum, $v);
            }

            if ($deleteColumn != false) {
                foreach ($deleteColumn as $column) {
                    $key = array_search($column, $enum);
                    if ($key !== false) {
                        unset($enum[$key]);
                    }

                }
                $enum = array_values($enum);
            }
            return $enum;
        } catch (\Throwable $th) {
            echo $th->getMessage();
            die();
        }
    }

    public static function getColor($color)
    {
        $bulkData = BulkData::color;
        return $bulkData[strtolower($color)];
    }

    public static function getColorCode($color)
    {
        switch ($color) {
            case 'primary':
                $code = '#3B71CA';
                break;
            case 'success':
                $code = '#14A44D';
                break;
            case 'warning':
                $code = '#E4A11B';
                break;
            case 'danger':
                $code = '#DC4C64';
                break;
            case 'secondary':
                $code = '#9FA6B2';
                break;
            case 'dark':
                $code = '#332D2D';
                break;
            default:
                $code = '#54B4D3';
        }
        return $code;
    }

    public static function changeName($string)
    {
        $charactersToReplace = ['\\', '/', ':', '*', '?', '<', '>', '|', '-', '_'];
        $replacement         = ' ';

        $newString = \Str::replace($charactersToReplace, $replacement, $string);
        return \Str::upper($newString);
    }

    public static function checkRegister()
    {
        $tahun = Tahun::aktif();

        $jadwal = Jadwal::where('tahun_id', $tahun->id)->first();

        $mulai    = \Carbon::parse($jadwal->mulai)->startOfDay();
        $berakhir = \Carbon::parse($jadwal->berakhir)->endOfDay();
        $sekarang = \Carbon::now();

        $dibuka = true;
        if ($sekarang->lt($mulai) || $sekarang->gt($berakhir)) {
            $dibuka = false;
        }

        return $dibuka;
    }

    public static function generateRandomString($length = 8)
    {
        return rand(12345, 54321);
        // $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        // $charactersLength = strlen($characters);
        // $randomString = '';
        // for ($i = 0; $i < $length; $i++) {
        //     $randomString .= $characters[rand(0, $charactersLength - 1)];
        // }
        // return $randomString;
    }

    public static function getTheme()
    {
        $theme = \Cookie::get('theme');
        $theme = $theme ? $theme : 'light';
        return $theme;
    }

    public static function setTheme($theme)
    {
        \Cookie::queue(\Cookie::forever('theme', $theme));
        return 'success';
    }

    public static function getColorAbsensi($status)
    {
        switch ($status) {
            case 'Hadir':
                $color = 'success';
                break;
            case 'Izin':
                $color = 'warning';
                break;
            case 'Sakit':
                $color = 'info';
                break;
            case 'Tidak Hadir':
                $color = 'danger';
                break;
            case 'Alpha':
                $color = 'danger';
                break;
            case 'Belum Absen':
                $color = 'secondary';
                break;
        }

        return $color;
    }

    public static function getBulan($bln)
    {
        switch ($bln) {
            case 1:
                return "Januari";
            case 2:
                return "Februari";
            case 3:
                return "Maret";
            case 4:
                return "April";
            case 5:
                return "Mei";
            case 6:
                return "Juni";
            case 7:
                return "Juli";
            case 8:
                return "Agustus";
            case 9:
                return "September";
            case 10:
                return "Oktober";
            case 11:
                return "November";
            case 12:
                return "Desember";
        }
    }

    public static function formatDate($date)
    {
        $date = explode('-', $date);
        return $date[2] . ' ' . self::getBulan($date[1]) . ' ' . $date[0];
    }

    public static function formatDateWithTime($date)
    {
        return \Carbon::parse($date)->format('d F Y, H:i') . ' WIB';
    }

    public static function getStatusDokumenWajibDpl(PoskoDpl $poskoDpl)
    {
        try {
            return [
                'status'  => true,
                'message' => 'Success',
            ];
            $report = [];

            if (! $poskoDpl->rubrikPenilaian) {
                $report[] = 'Rubrik Penilaian';
            }
            if (! $poskoDpl->beritaAcara) {
                $report[] = 'Berita Acara';
            }
            if (! $poskoDpl->dokumentasi) {
                $report[] = 'Dokumentasi Foto';
            }
            if ($poskoDpl->dokumentasi) {
                if (! $poskoDpl->dokumentasi->where('tipe', 'foto')->first()) {
                    $report[] = 'Dokumentasi Foto';
                }
            }

            if (count($report) > 0) {
                return abort(500, 'Lengkapi dokumen sebelum mengisi nilai : ' . implode(', ', $report));
            }
            return [
                'status'  => true,
                'message' => 'Success',
            ];
        } catch (\Throwable $th) {
            return [
                'status'  => false,
                'message' => $th->getMessage(),
            ];
        }
    }

    public static function roleAccess($access, $role)
    {
        $user = \Auth::user();
        if ($user->role->nama == $role) {
            if ($access->id != $user[$role]->id) {
                return false;
            }
        }

        return true;
    }

    public static function deleteFile($path)
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
