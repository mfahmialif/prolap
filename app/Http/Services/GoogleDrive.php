<?php
namespace App\Http\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GoogleDrive
{
    public const uploadType = BulkData::uploadType; // local or google_drive

    public static function getData($filename, $dir = '/')
    {
        if (self::uploadType === 'local') {
            $file['path'] = 'dokumen/' . $filename;
            return $file;
        }

        $recursive = false; // Get subdirectories also?
        $contents  = collect(Storage::disk('google')->listContents($dir, $recursive));
        $file      = $contents
            ->where('type', '=', 'file')
            ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
            ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
            ->first(); // there can be duplicate file names!
        if ($file) {
            if ($file['dirname'] != "") {
                $file['path'] = str_replace($file['dirname'] . '/', '', $file['path']);
            }
        }
        return $file;
    }

    public static function getAllData($dir = '/')
    {
        $recursive = false; // Get subdirectories also?
        $contents  = collect(Storage::disk('google')->listContents($dir, $recursive));

        return $contents;
    }

    public static function delete($filename, $dir = '/')
    {
        try {

            if (self::uploadType === 'local') {
                $localPath = public_path('dokumen');

                if (is_dir($localPath)) {
                    unlink($localPath . '/' . $filename);
                }

                $response = [
                    "status"  => true,
                    "message" => "success delete",
                    "name"    => $filename,
                ];
                return $response;
            }

            $recursive = false; // Get subdirectories also?
            $contents  = collect(Storage::disk('google')->listContents($dir, $recursive));
            $file      = $contents
                ->where('type', '=', 'file')
                ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
                ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
                ->first(); // there can be duplicate file names!
            if ($file) {
                Storage::disk('google')->delete($file['path']);
            }

            $response = [
                "status"  => true,
                "message" => "success delete",
                "name"    => @$file['name'],
            ];
            return $response;
        } catch (\Throwable $th) {
            $response = [
                "status"  => false,
                "message" => "failed delete",
                "name"    => null,
            ];
            return $response;
        }
    }

    public static function deleteWithPath($path, $dir = '/')
    {
        try {
            if (strpos($path, 'dokumen/') === 0) {
                $localPath = public_path('dokumen');

                if (file_exists(public_path($path)) && is_file(public_path($path))) {
                    unlink(public_path($path));
                }

                $response = [
                    "status"  => true,
                    "message" => "success delete",
                ];
                return $response;
            }

            Storage::disk('google')->delete($path);

            $response = [
                "status"  => true,
                "message" => "success delete",
            ];
            return $response;
        } catch (\Throwable $th) {
            $response = [
                "status"  => false,
                "message" => "failed delete",
                "name"    => null,
            ];
            return $response;
        }
    }

    public static function download($filename, $dir = '/', $customName = false)
    {
        if (self::uploadType === 'local') {
            $localPath = public_path('dokumen');
            if (is_dir($localPath)) {
                $filePath = $localPath . '/' . $filename;
                if (file_exists($filePath)) {
                    return response()->download($filePath, $customName ? $customName : $filename);
                }
            }
            abort(404, 'File not found');
        }
        $recursive = false; // Get subdirectories also?
        $contents  = collect(Storage::disk('google')->listContents($dir, $recursive));
        $file      = $contents
            ->where('type', '=', 'file')
            ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
            ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
            ->first(); // there can be duplicate file names!
        $rawData  = Storage::disk('google')->get($file['path']);
        $filename = $customName ? $customName . '.' . pathinfo($filename, PATHINFO_EXTENSION) : $filename;
        return response($rawData, 200)
            ->header('ContentType', $file['mimetype'])
            ->header('Content-Disposition', "attachment; filename=$filename");
    }

    public static function directDownload($filename, $dir = '/', $customName = false)
    {
        if ($customName != false) {
            return route('operasi.dokumen.download') . "?filename=$filename&dir=$dir&custom_name=$customName";
        }
        return route('operasi.dokumen.download') . "?filename=$filename&dir=$dir";
    }

    public static function link($path)
    {

        if (strpos($path, 'dokumen/') === 0) {
            return asset($path);
        }

        $link = null;
        if ($path != null) {
            $link = "https://drive.google.com/file/d/" . $path . "/view";
        }
        return $link;
    }

    public static function showImage($path)
    {
        if (self::uploadType === 'local') {
            return asset($path);
        }

        $link = null;
        if ($path != null) {
            $link = "https://drive.google.com/thumbnail?id=$path&sz=w1000";
        }
        return $link;
    }

    public static function upload($file, $kategori = 'UNCATEGORIZED', $dir = '/')
    {
        try {
            $extensi = $file->extension();
            // $namaDokumen = 'File' . date('YmdHis') . uniqid() . '.' . $extensi;
            $namaOriginal = $file->getClientOriginalName();
            $namaDokumen  = $kategori . '-' . date('Ymd') . '-' . uniqid() . '.' . $extensi;
            // $content = file_get_contents($file->getRealPath());

            // ambil isi file (paling aman & portable)
            $content     = $file->get();
            $namaDokumen = Helper::changeFormatSymbol($namaDokumen);

            if (self::uploadType === 'local') {
                $localPath = public_path('dokumen');

                // buat folder kalau belum ada
                if (! is_dir($localPath)) {
                    mkdir($localPath, 0755, true);
                }

                file_put_contents($localPath . '/' . $namaDokumen, $content);
            } else {
                Storage::disk('google')->put($dir . $namaDokumen, $content);
            }

            $response = [
                "status"  => true,
                "message" => "success upload",
                "name"    => $namaDokumen,
            ];
            return $response;
        } catch (\Throwable $th) {
            $response = [
                "status"  => false,
                "message" => $th->getMessage(),
                "name"    => null,
            ];
            return $response;
        }
    }

    public function edit($newfile, $oldfile, $dir = '/')
    {
        try {
            $file        = $newfile;
            $extensi     = $file->extension();
            $namaDokumen = 'File' . date('YmdHis') . uniqid() . '.' . $extensi;
            $content     = File::get($file->getRealPath());
            $upload      = Storage::disk('google')->put($namaDokumen, $content);
            if ($upload) {
                if ($oldfile != null) {
                    $recursive = false; // Get subdirectories also?
                    $contents  = collect(Storage::disk('google')->listContents($dir, $recursive));
                    $file      = $contents
                        ->where('type', '=', 'file')
                        ->where('filename', '=', pathinfo($oldfile, PATHINFO_FILENAME))
                        ->where('extension', '=', pathinfo($oldfile, PATHINFO_EXTENSION))
                        ->first(); // there can be duplicate file names!
                    Storage::disk('google')->delete($file['path']);
                }
            }
            $response = [
                "status"  => true,
                "message" => "success edit",
            ];
            return $response;
        } catch (\Throwable $th) {
            $response = [
                "status"  => false,
                "message" => "failed edit",
            ];
            return $response;
        }
    }

}
