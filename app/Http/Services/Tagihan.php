<?php

namespace App\Http\Services;

class Tagihan
{
    /**
     * Pembayaran KKN
     * @param string $nim
     * @param string $th_akademik_kode 
     * @param string $jenis_pembayaran 
     * @param string $jumlah 
     * @return object kkn data
     */
    public static function kkn($nim = null, $th_akademik_kode = null, $jenis_pembayaran = null, $jumlah = null)
    {
        $post = [
            'nim' => $nim,
            'th_akademik_kode' => $th_akademik_kode,
            'jenis_pembayaran' => $jenis_pembayaran,
            'jumlah' => $jumlah,
        ];

        $apiKey = config('simkeu.simkeu_api_key');
        $url = config('simkeu.simkeu_url') . "tagihan/kkn";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: $apiKey",

        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);
        return $response;
    }

    /**
     * KKN UPDATE
     * @param string $id
     * @param string $jenis_pembayaran 
     * @param string $jumlah 
     * @return object kkn data
     */
    public static function kknUpdate($id = null, $jenis_pembayaran = null, $jumlah = null)
    {
        $post = [
            'id' => $id,
            'jenis_pembayaran' => $jenis_pembayaran,
            'jumlah' => $jumlah,
        ];

        $apiKey = config('simkeu.simkeu_api_key');
        $url = config('simkeu.simkeu_url') . "tagihan/kknUpdate";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: $apiKey",

        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);
        return $response;
    }

    /**
     * KKN DELETE
     * @param string $id
     * @return object kkn data
     */
    public static function kknDelete($id = null)
    {
        $post = [
            'id' => $id,
        ];

        $apiKey = config('simkeu.simkeu_api_key');
        $url = config('simkeu.simkeu_url') . "tagihan/kknDelete";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: $apiKey",

        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);
        return $response;
    }
}
