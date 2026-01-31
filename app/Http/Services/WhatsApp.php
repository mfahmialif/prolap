<?php

namespace App\Http\Services;

use App\Models\Api;

class WhatsApp
{
    public static function localize_us_number($phone)
    {
        $numbers_only = preg_replace("/[^\d]/", "", $phone);
        return preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "$1$2$3", $numbers_only);
    }

    public static function formatPhoneNumber($phone, $type = 'whatsapp')
    {
        $numbers_only = preg_replace("/[^\d]/", "", $phone);
        $res = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "$1$2$3", $numbers_only);
        $first = substr($res, 0, 1);

        if ($first == '0') {
            $wa = '62' . substr($res, 1);
            $sms = $res;
        } else {
            $wa = $res;
            $sms = '0' . substr($res, 2);
        }

        if ($type == 'whatsapp') {
            return $wa;
        } else {
            return $sms;
        }
    }

    public static function wa_pingnotif($param)
    {
        $q = Api::where('type', $param['type'])->first();

        if ($q) {
            $send_data = [
                'message' => $param['message'],
                'number_phone' => $param['phone'],
            ];

            $field = str_replace("+", " ", http_build_query($send_data));
            $apikey = $q->token;
            $header = [
                'key: ' . $apikey,
                'Content-Type: application/x-www-form-urlencoded',
            ];

            $curl = curl_init();
            curl_setopt_array(
                $curl,
                [
                    CURLOPT_URL => $q->uri,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $field,
                    CURLOPT_HTTPHEADER => $header,
                ]
            );

            $exec = curl_exec($curl);
            $respon = json_decode($exec);

            if (curl_error($curl)) {
                throw new \Exception(curl_error($curl));
            } else if (curl_error($curl)) {
                return false;
            }
            curl_close($curl);
            return $respon;
        } else {
            throw new \Exception('API Tidak Ditemukan');
        }
    }

    public static function wa_fonnte($param)
    {
        $q = Api::where('type', $param['type'])->first();
        if ($q) {
            $send_data = [
                'target' => $param['phone'],
                'message' => $param['message']
            ];

            $field = str_replace("+", " ", http_build_query($send_data));
            $apikey = $q->token;

            $header = [
                'Authorization: ' . $apikey
            ];

            $curl = curl_init();
            curl_setopt_array(
                $curl,
                [
                    CURLOPT_URL => $q->uri,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $field,
                    CURLOPT_HTTPHEADER => $header,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0
                ]
            );

            $exec = curl_exec($curl);
            $respon = json_decode($exec);

            if (curl_error($curl)) {
                throw new \Exception(curl_error($curl));
            } else if (curl_error($curl)) {
                return false;
            }
            curl_close($curl);
            return $respon;
        } else {
            throw new \Exception('API Tidak Ditemukan');
        }
    }

    public static function wa_satuconnect($param)
    {
        $q = Api::where('type', $param['type'])->first();
        if (!$q) {
            throw new \Exception('API Tidak Ditemukan');
        }

        // Normalisasi nomor: hanya digit, tanpa plus/spasi
        $phone = preg_replace('/\D+/', '', (string)($param['phone'] ?? ''));

        $payload = [
            'deviceID'     => (string) $q->userkey,   // pastikan sesuai docs: "deviceID"
            'phoneNumbers' => $phone,                 // string, bukan array
            'message'      => (string) ($param['message'] ?? ''),
        ];

        $headers = [
            'Authorization: Bearer ' . $q->token,
            'Content-Type: application/json',
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => rtrim($q->uri, '/') . '/agent/message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER     => $headers,
            // Jika sertifikat SSL valid, sebaiknya true. Matikan hanya jika memang perlu debugging:
            // CURLOPT_SSL_VERIFYHOST => 2,
            // CURLOPT_SSL_VERIFYPEER => 1,
        ]);

        $exec = curl_exec($curl);
        $curlErrNo = curl_errno($curl);
        $curlErr   = curl_error($curl);
        $httpCode  = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);

        if ($curlErrNo) {
            throw new \Exception("cURL error ({$curlErrNo}): {$curlErr}");
        }

        // Coba decode JSON respons
        $decoded = json_decode($exec, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Server mungkin mengembalikan HTML/text saat error
            throw new \Exception("Gagal parse respons server (HTTP {$httpCode}): {$exec}");
        }

        // Tangani pola error dari API
        if (!$decoded || (isset($decoded['status']) && $decoded['status'] === false)) {
            $msg = $decoded['message'] ?? 'Request gagal';
            $details = $decoded['error']['details'] ?? null;
            throw new \Exception("SatuConnect error (HTTP {$httpCode}): {$msg}" . ($details ? " - {$details}" : ''));
        }

        return (object) $decoded;
    }

    public static function wa_zenziva($param)
    {
        $q = Api::where('type', $param['type'])->first();

        if ($q) {
            $userkey = $q->userkey;
            $passkey = $q->token;
            $url = $q->uri;

            $field = [
                'userkey' => $userkey,
                'passkey' => $passkey,
                'to' => $param['phone'],
                'message' => $param['message']
            ];

            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, $url);
            curl_setopt($curlHandle, CURLOPT_HEADER, 0);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
            curl_setopt($curlHandle, CURLOPT_POST, 1);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $field);

            $results = json_decode(curl_exec($curlHandle), true);

            if (curl_error($curlHandle)) {
                throw new \Exception(curl_error($curlHandle));
            } else if (curl_error($curlHandle)) {
                return false;
            }

            curl_close($curlHandle);
            return $results;
        } else {
            throw new \Exception('API Tidak Ditemukan');
        }
    }

    public static function sms($param)
    {
        $ins = [
            'DestinationNumber' => WhatsApp::formatPhoneNumber($param['phone'], 'sms'),
            'TextDecoded' => $param['message'],
            'CreatorID' => 'Gammu'
        ];

        // $q_insert = WhatsApp::m_global->db_insert('outbox', $ins);

        // if(!$q_insert){
        //     return false;
        // }
        return true;
    }

    public static function _notif($param)
    {
        $status = true;
        if ($param['vendor'] == 'pingnotif') {
            $send = WhatsApp::wa_pingnotif($param);
        } elseif ($param['vendor'] == 'fonnte') {
            $send = WhatsApp::wa_fonnte($param);
            $status = $send->status;
        } elseif ($param['vendor'] == 'zenziva') {
            $send = WhatsApp::wa_zenziva($param);
            $status = $send['status'];
        } elseif ($param['vendor'] == 'sms') {
            $send = WhatsApp::sms($param);
        } elseif ($param['vendor'] == 'satuconnect') {
            $send = WhatsApp::wa_satuconnect($param);
        }

        if (!$status) {
            return false;
        }
        if (!$send) {
            return false;
        }

        return true;
    }
}
