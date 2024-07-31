<?php

namespace App\Utils;

use App\Library\VoiceRSS;
use App\Models\UserDevice;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM as FacadesFCM;
use Illuminate\Support\Str;

class NotificationPageHelper
{
    public static function notificationStock($result)
    {
        try {
            $tokenDevice = UserDevice::whereNotNull('token')->pluck('token');
            foreach ($tokenDevice as $token) {
                $currentUrl = request()->url();
                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60 * 20);

                $notificationBuilder = new PayloadNotificationBuilder('Stock Obat');
                $notificationBuilder->setBody('Stok ' . $result['nama_brng'] . ' (' . number_format($result['Stok_Akhir'], 2) . ') perlu di re-stock');

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData(['click_action' => $currentUrl]);

                $option = $optionBuilder->build();
                $notification = $notificationBuilder->build();
                $data = $dataBuilder->build();

                $downstreamResponse = FacadesFCM::sendTo($token, $option, $notification, $data);

                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();

                // $tokensToDelete = $downstreamResponse->tokensToDelete();
                // if (count($tokensToDelete) > 0) {
                //     foreach ($tokensToDelete as $token) {
                //         UserDevice::where('token', $token)->update(['token' => Null]); // Hapus token dari tabel pengguna
                //     }
                // }

                // $tokensToModify = $downstreamResponse->tokensToModify();
                // if (count($tokensToModify) > 0) {
                //     foreach ($tokensToModify as $oldToken => $newToken) {
                //         UserDevice::where('token', $oldToken)->update(['token' => $newToken]); // Ubah token lama dengan token baru dalam tabel pengguna
                //     }
                // }

                // $tokensToRetry = $downstreamResponse->tokensToRetry();
                // if (count($tokensToRetry) > 0) {
                //     foreach ($tokensToRetry as $token) {
                //         dispatch(function () use ($token, $option, $notification, $data) {
                //             FacadesFCM::sendTo($token, $option, $notification, $data);
                //         });
                //     }
                // }

                $tokensWithError = $downstreamResponse->tokensWithError();

                if (count($tokensWithError) > 0) {
                    foreach ($tokensWithError as $token => $error) {
                        UserDevice::where('token', $token)->update(['token' => Null]);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error Order: ' . $e->getMessage());
        }
    }

    public static function textToSpeach($text)
    {
        try {

            $tts = new VoiceRSS;
            $voice = $tts->speech([
                'key' => env('VOICE_RSS_API_KEY'),
                'hl' =>  'id-ID',
                'src' => $text,
                'r' => '0',
                'c' => 'mp3',
                'f' => '44khz_16bit_stereo',
                'ssml' => 'false',
                'b64' => 'false'
            ]);

            $filename = Str::uuid() . '.mp3';
            if (empty($voice["error"])) {
                $rawData = $voice["response"];
                if (!File::exists(storage_path('app/public/speeches'))) {
                    Storage::makeDirectory(public_path('storage/speeches'));
                }

                Storage::disk('speeches')->put($filename, $rawData);
                $speechFilelink =  asset('storage/speeches/' . $filename);
                $urls["play-url"] = $speechFilelink;
                $urls["download-file"] = $filename;
                $data = array('status' => 200, 'responseText' => $urls);
                return response()->json($data);
            }

            $data = array('status' => 400, 'responseText' => "Please try again!");
            return response()->json($data);
        } catch (Exception $e) {
            $data = array('status' => 400, 'responseText' => $e->getMessage());
            return response()->json($data);
        }
    }
}
