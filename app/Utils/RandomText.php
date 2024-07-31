<?php

namespace App\Utils;

class RandomText
{

    public static function generate($length = 11, $lowercase = true, $numbers = true)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        if ($lowercase) {
            $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        }
        if ($numbers) {
            $codeAlphabet .= "0123456789";
        }
        $max = strlen($codeAlphabet);

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }
}
