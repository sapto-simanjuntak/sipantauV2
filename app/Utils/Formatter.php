<?php

namespace App\Utils;

use App\Models\Master\Customer;
use App\Models\Master\CustomerRank;
use App\Models\Master\MeasurementUnit;
use App\Models\Master\PaymentTerm;
use App\Models\Master\Product;
use App\Models\Master\Category;
use App\Models\Master\Supplier;
use App\Models\Purchasing\SalesOrder;

/**
 * Class Formatter
 * @package App\Utils
 * Helper class to format strings
 */
class Formatter
{
    /**
     * https://stackoverflow.com/questions/14531679/remove-useless-zero-digits-from-decimals-in-php/26980002#26980002
     * @param $amount
     * @param $trimDecimalZeroes
     * @return string
     */
    public static function currency($amount, $trimDecimalZeroes = true)
    {
        $decimal_point = '.';
        $thousands_sep = ',';
        $string = number_format($amount, 2, $decimal_point, $thousands_sep);
        if ($trimDecimalZeroes) {
            $string = (strpos($string, $decimal_point) !== false) ?
                rtrim(rtrim($string, '0'), $decimal_point) : $string;
        }
        return $string;
    }
    public static function rupiah($amount)
    {
        return 'Rp ' . self::currency($amount);
    }
    public static function rupiahRounded($amount)
    {
        return 'Rp ' . self::currency(round($amount));
    }
    public static function kilograms($amount)
    {
        return self::currency($amount, 0) . ' kg';
    }

    public static function truncateWithEllipsis($str, $len = 30)
    {
        return strlen($str) > $len ? substr($str, 0, $len) . "..." : $str;
    }

    public static function mask($str, $start = 0, $length = null)
    {
        $mask = preg_replace("/\S/", "*", $str);
        if (is_null($length)) {
            $mask = substr($mask, $start);
            $str = substr_replace($str, $mask, $start);
        } else {
            $mask = substr($mask, $start, $length);
            $str = substr_replace($str, $mask, $start, $length);
        }
        return $str;
    }

    /**
     * https://github.com/yogirzlsinatrya/terbilang/blob/master/src/Yogirzlsinatrya/Terbilang/Terbilang.php
     * @param $number
     * @return mixed|null|string
     */
    public static function rupiahTerbilang($number)
    {
        $number = intval($number);

        if (!is_numeric($number)) {
            return '';
        }

        $base = array('Nol', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan');
        $numeric = array('1000000000000000', '1000000000000', '1000000000000', 1000000000, 1000000, 1000, 100, 10, 1);
        $unit = array('Kuadriliun', 'Triliun', 'Biliun', 'Milyar', 'Juta', 'Ribu', 'Ratus', 'Puluh', '');
        $str = null;
        $i = 0;
        if ($number == 0) {
            $str = 'nol';
        } else {
            while ($number != 0) {
                $count = (int) ($number / $numeric[$i]);
                if ($count >= 10) {
                    $str .= static::rupiahTerbilang($count) . ' ' . $unit[$i] . ' ';
                } elseif ($count > 0 && $count < 10) {
                    $str .= $base[$count] . ' ' . $unit[$i] . ' ';
                }
                $number -= $numeric[$i] * $count;
                $i++;
            }
            $str = preg_replace('/Satu Puluh (\w+)/i', '\1 Belas', $str);
            //$str = preg_replace('/Satu Ribu/', 'Seribu\1', $str);
            $str = preg_replace('/Satu Ratus/', 'Seratus\1', $str);
            $str = preg_replace('/Satu Puluh/', 'Sepuluh\1', $str);
            $str = preg_replace('/Satu Belas/', 'Sebelas\1', $str);
            $str = preg_replace('/\s{2,}/', ' ', trim($str));
        }
        return $str;
    }

    public static function viewBtn($url)
    {
        return '<a class="btn btn-xs btn-primary" href="' . $url . '">
                    <i class="fa fa-eye"></i> View
                </a>';
    }
}
