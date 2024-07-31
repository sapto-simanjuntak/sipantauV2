<?php

use App\Models\BaseModel;
use App\Models\FormLabel;
use App\Models\RiwayatBarangMedis;
use App\Models\StatusTriage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\QRcode;

if (!function_exists('_rp_for_report')) {
    function _rp_for_report($type, $amount)
    {
        if ($type == 'pdf') {
            return \App\Utils\Formatter::rupiah($amount);
        }
        return floatval($amount);
    }
}

if (!function_exists('_rp')) {
    function _rp($amount)
    {
        return \App\Utils\Formatter::rupiah($amount);
    }
}

if (!function_exists('_rp_rounded')) {
    function _rp_rounded($amount)
    {
        return \App\Utils\Formatter::rupiahRounded($amount);
    }
}

if (!function_exists('_cur2dec')) {
    function _cur2dec($amount)
    {
        return number_format($amount, 2, ',', '.');
    }
}

if (!function_exists('_date')) {
    function _date($str_date, $showHours = true)
    {
        if (!$showHours) {
            return ($str_date != '0000-00-00' && $str_date != null) ? date('d F Y', strtotime($str_date)) : '-';
        }
        return ($str_date != '0000-00-00 00:00:00' && $str_date != null) ? date('d F Y, H:i:s', strtotime($str_date)) : '-';
    }
}

if (!function_exists('_date2')) {
    function _date2($str_date, $showHours = true)
    {
        if (!$showHours) {
            return ($str_date != '0000-00-00' && $str_date != null) ? date('d/m/Y', strtotime($str_date)) : '-';
        }
        return ($str_date != '0000-00-00 00:00:00' && $str_date != null) ? date('d/m/Y H:i:s', strtotime($str_date)) : '-';
    }
}
if (!function_exists('_date3')) {
    function _date3($str_date, $showHours = true)
    {
        if (!$showHours) {
            return ($str_date != '0000-00-00' && $str_date != null) ? date('Y/m/d', strtotime($str_date)) : '-';
        }
        return ($str_date != '0000-00-00 00:00:00' && $str_date != null) ? date('Y/m/d H:i:s', strtotime($str_date)) : '-';
    }
}

if (!function_exists('_date_format')) {
    function _date_format($times, $showHours = true)
    {
        setlocale(LC_ALL, 'IND');

        if (!$showHours) {
            if (gettype($times) === 'object') {
                $date = Carbon::create($times->toDateTimeString())->formatLocalized('%d %b %Y');
            } else {
                $date = Carbon::create($times)->formatLocalized('%d %b %Y');
            }
        } else {
            if (gettype($times) === 'object') {
                $date = Carbon::create($times->toDateTimeString())->formatLocalized('%d %b %Y, %H:%M');
            } else {
                $date = Carbon::create($times)->formatLocalized('%d %b %Y, %H:%M');
            }
        }

        return $date;
    }
}

if (!function_exists('_cap')) {
    function _cap($string)
    {
        return ucwords(str_replace('_', ' ', $string));
    }
}

// -------------------------------------------

if (!function_exists('snake_case')) {
    /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    function snake_case($value, $delimiter = '_')
    {
        return Str::snake($value, $delimiter);
    }
}

if (!function_exists('str_plural')) {
    /**
     * Get the plural form of an English word.
     *
     * @param  string  $value
     * @param  int  $count
     * @return string
     */
    function str_plural($value, $count = 2)
    {
        return Str::plural($value, $count);
    }
}

if (!function_exists('title_case')) {
    /**
     * Convert a value to title case.
     *
     * @param  string  $value
     * @return string
     */
    function title_case($value)
    {
        return Str::title($value);
    }
}

if (!function_exists('str_slug')) {
    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param  string  $title
     * @param  string  $separator
     * @param  string  $language
     * @return string
     */
    function str_slug($title, $separator = '-', $language = 'en')
    {
        return Str::slug($title, $separator, $language);
    }
}

if (!function_exists('str_limit')) {
    /**
     * Limit the number of characters in a string.
     *
     * @param  string  $value
     * @param  int  $limit
     * @param  string  $end
     * @return string
     */
    function str_limit($value, $limit = 10, $end = '...')
    {
        return Str::limit($value, $limit, $end);
    }
}

if (!function_exists('_stockDepoFarmasiQuery')) {
    function _stockDepoFarmasiQuery()
    {
        $model = RiwayatBarangMedis::on('mysql_khanza');
        $query =
            $model->newQuery()
            ->join(
                DB::raw('(SELECT kode_brng, MAX(CONCAT(tanggal, jam)) as max_waktu
                        FROM riwayat_barang_medis
                        GROUP BY kode_brng) as max_time'),
                function ($join) {
                    $join->on('riwayat_barang_medis.kode_brng', '=', 'max_time.kode_brng')
                        ->whereRaw("CONCAT(riwayat_barang_medis.tanggal, riwayat_barang_medis.jam) = max_time.max_waktu");
                }
            )
            ->join('databarang', 'riwayat_barang_medis.kode_brng', '=', 'databarang.kode_brng')
            ->join('bangsal', 'riwayat_barang_medis.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('zk_stock_depo_farmasi', 'zk_stock_depo_farmasi.kd_barang', '=', 'riwayat_barang_medis.kode_brng')
            ->select([
                'riwayat_barang_medis.kode_brng',
                DB::raw('SUM(CASE WHEN riwayat_barang_medis.posisi = "pemberian obat" THEN riwayat_barang_medis.keluar ELSE 0 END) as total_keluar'),
                'databarang.nama_brng',
                DB::raw('MAX(riwayat_barang_medis.tanggal) as tanggal'),
                DB::raw('MAX(riwayat_barang_medis.jam) as jam'),
                'riwayat_barang_medis.posisi',
                DB::raw('MAX(COALESCE(riwayat_barang_medis.stok_akhir, riwayat_barang_medis.stok_awal)) as Stok_Akhir'),
                'riwayat_barang_medis.kd_bangsal',
                'bangsal.nm_bangsal',
                'zk_stock_depo_farmasi.minimal_stock as minimal_stock'
            ])
            ->whereIn('riwayat_barang_medis.kd_bangsal', ['B0063', 'B0073', 'AP'])
            ->where('riwayat_barang_medis.tanggal', date('Y-m-d'))
            ->groupBy('riwayat_barang_medis.kode_brng', 'databarang.nama_brng', 'riwayat_barang_medis.posisi', 'Stok_Akhir', 'riwayat_barang_medis.kd_bangsal', 'bangsal.nm_bangsal', 'zk_stock_depo_farmasi.minimal_stock')
            ->havingRaw('(riwayat_barang_medis.stok_akhir / zk_stock_depo_farmasi.minimal_stock) * 100 <= 20')
            ->orderByDesc('total_keluar');

        return $query;
    }
}

if (!function_exists('_monitoringTriage')) {
    function _monitoringTriage()
    {
        $data = StatusTriage::on('mysql_khanza')->where('tanggalperiksa', date('Y-m-d'))
            ->whereHas('regPeriksa', function ($query) {
                $query->where('status_lanjut', BaseModel::STATUS_LANJUT_RAJAL);
            })
            ->whereDoesntHave('billing')
            ->with('regPeriksa.pasien')
            ->with('regPeriksa.dataTriageIgd')
            ->with('billing');

        return $data;
    }
}

if (!function_exists('_inventoryCard')) {
    function _inventoryCard($startDate, $endDate, $depo)
    {
        $query = RiwayatBarangMedis::on('mysql_khanza')
            ->join('bangsal', 'riwayat_barang_medis.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('databarang', 'riwayat_barang_medis.kode_brng', '=', 'databarang.kode_brng');

        if (!$startDate && !$endDate) {
            $startDate = now()->toDateString();
            $endDate = now()->toDateString();
        }

        if ($depo) {
            $query->where('riwayat_barang_medis.kd_bangsal', $depo);
        }

        $query->whereBetween('riwayat_barang_medis.tanggal', [$startDate, $endDate]);
        $query->orderBy('riwayat_barang_medis.tanggal');
        $query->orderBy('riwayat_barang_medis.jam');

        return $query;
    }
}

if (!function_exists('_inventoryCardSummary')) {
    function _inventoryCardSummary($startDate, $endDate, $depo)
    {
        $query = RiwayatBarangMedis::on('mysql_khanza')
            ->join('databarang', 'riwayat_barang_medis.kode_brng', '=', 'databarang.kode_brng')
            ->join('bangsal', 'riwayat_barang_medis.kd_bangsal', '=', 'bangsal.kd_bangsal');

        if (!$startDate && !$endDate) {
            $startDate = now()->toDateString();
            $endDate = now()->toDateString();
        }

        $query->selectRaw('riwayat_barang_medis.kode_brng,
        databarang.nama_brng,
        bangsal.kd_bangsal as kd_bangsal,
        riwayat_barang_medis.tanggal as tanggal,
        max(riwayat_barang_medis.jam) as jam,
        first_value(riwayat_barang_medis.stok_awal) over (partition by riwayat_barang_medis.kode_brng, riwayat_barang_medis.tanggal order by riwayat_barang_medis.jam) as stok_awal,
        sum(riwayat_barang_medis.masuk) as masuk,
        sum(riwayat_barang_medis.keluar) as keluar,
        (SELECT COALESCE(rm.stok_akhir, 0)
            FROM riwayat_barang_medis AS rm
            WHERE rm.kode_brng = riwayat_barang_medis.kode_brng
                AND rm.tanggal = riwayat_barang_medis.tanggal
                AND rm.kd_bangsal = riwayat_barang_medis.kd_bangsal
            ORDER BY rm.tanggal DESC, rm.jam DESC
            LIMIT 1) as stok_akhir')
            ->whereBetween('riwayat_barang_medis.tanggal', [$startDate, $endDate]);

        if ($depo) {
            $query->where('riwayat_barang_medis.kd_bangsal', $depo);
        }

        $query->groupBy(['databarang.kode_brng', 'riwayat_barang_medis.tanggal', 'bangsal.kd_bangsal']);

        return $query;
    }
}

if (!function_exists('_select2')) {
    function _select2($model, $column, $search = null)
    {
        $query = $model::query();

        if ($search) {
            $query->where($column, 'like', '%' . $search . '%');
        }

        return $query->get();
    }
}

if (!function_exists('_match_label_value')) {
    function _match_label_value($data, $label)
    {
        $labelReplacement = FormLabel::where('unique', $label)->first();

        if ($labelReplacement) {
            $relation = $labelReplacement->relation;
            if (isset($data->$relation)) {
                if (is_string($data->$relation)) {
                    // jika nama relasi dan index name nya sama, soal di khanza suka gitu penamaan relasi nya
                    $relations = $relation . 's';
                    $value = $data->$relations->{$labelReplacement->replace};
                } else {
                    $value = $data->$relation->{$labelReplacement->replace};
                }
            } else {
                if (isset($labelReplacement->replace)) {
                    $value = $data->{$labelReplacement->replace};
                } else {
                    $value = null;
                }
            }

            return ucwords(strtolower($value));
        } else {
            return null;
        }
    }
}

if (!function_exists('_match_document_value')) {
    function _match_document_value($data, $html, $signName = null)
    {
        // Variable untuk menyimpan nilai @* dari $html yang tidak cocok dengan $data
        $not_matched_values = [];

        // Pisahkan HTML menjadi potongan-potongan yang mengandung @*
        preg_match_all('/@[^<>\s]+/', $html, $matches);

        // Loop through each matched value
        foreach ($matches[0] as $matched_value) {
            $found = false;
            // Loop through each key-value pair in $data
            foreach ($data as $key => $value) {
                // Jika nilai cocok dengan salah satu kunci di $data, tandai sebagai ditemukan
                if (strpos($matched_value, "@$key") !== false) {
                    $found = true;
                    break;
                }
            }
            // Jika nilai tidak cocok dengan kunci di $data, tambahkan ke array $not_matched_values
            if (!$found) {
                $not_matched_values[] = $matched_value;
            }
        }

        foreach ($data as $key => $value) {
            $pattern = "/(?<!\w)@" . preg_quote($key, '/') . "(?!\w)/";


            if (preg_match($pattern, $html)) {
                foreach ([$value] as $replacement) {
                    if (Storage::exists('public/' . $replacement)) {
                        $isForDOMPDF = env('IS_FOR_DOMPDF', false);
                        if ($isForDOMPDF == true) {
                            $imageTag = '<img src="' . public_path('storage/' . $replacement) . '" alt="' . $key . '" width="200">';
                        } else {
                            $imageTag = '<img src="' . Storage::url($replacement) . '" alt="' . $key . '" width="200">';
                        }
                        $html = preg_replace($pattern, $imageTag, $html);
                    } elseif (is_string($replacement)) {
                        $html = preg_replace($pattern, ucwords(strtolower($replacement)), $html);
                    } else {
                        $html = preg_replace($pattern, '................', $html);
                    }
                }
            }
        }

        foreach ($not_matched_values as $key => $value) {
            if ($value == '@tanda_tangan_pasien_atau_wali') {
                if ($signName) {
                    foreach ($signName as $index => $sign) {
                        if ($sign->name == str_replace('@', '', $value)) {
                            $html = str_replace($value, '<img src="' . $sign->signature . '" width="150" />', $html);
                        }
                    }
                } else {
                    $html = str_replace($value, '<canvas id="canvas1" class="signature-canvas" data-name="tanda_tangan_pasien_atau_wali"></canvas>', $html);
                }
            } else if ($value == '@tanda_tangan_petugas') {
                if ($signName) {
                    foreach ($signName as $index => $sign) {
                        if ($sign->name == str_replace('@', '', $value)) {
                            $html = str_replace($value, '<img src="' . $sign->signature . '" width="150" />', $html);
                        }
                    }
                } else {
                    $html = str_replace($value, '<canvas id="canvas2" class="signature-canvas" data-name="tanda_tangan_petugas"></canvas>', $html);
                }
            } else if ($value == '@tanda_tangan_dokter_umum') {
                if ($signName) {
                    foreach ($signName as $index => $sign) {
                        if ($sign->name == str_replace('@', '', $value)) {
                            $html = str_replace($value, '<img src="' . $sign->signature . '" width="150" />', $html);
                        }
                    }
                } else {
                    $html = str_replace($value, '<canvas id="canvas3" class="signature-canvas" data-name="tanda_tangan_dokter_umum"></canvas>', $html);
                }
            } else if ($value == '@tanda_tangan_dokter_spesialis') {
                if ($signName) {
                    foreach ($signName as $index => $sign) {
                        if ($sign->name == str_replace('@', '', $value)) {
                            $html = str_replace($value, '<img src="' . $sign->signature . '" width="150" />', $html);
                        }
                    }
                } else {
                    $html = str_replace($value, '<canvas id="canvas4" class="signature-canvas" data-name="tanda_tangan_dokter_spesialis"></canvas>', $html);
                }
            } else if ($value == '@tanda_tangan_direktur') {
                if ($signName) {
                    foreach ($signName as $index => $sign) {
                        if ($sign->name == str_replace('@', '', $value)) {
                            $html = str_replace($value, '<img src="' . $sign->signature . '" width="150" />', $html);
                        }
                    }
                } else {
                    $html = str_replace($value, '<canvas id="canvas5" class="signature-canvas" data-name="tanda_tangan_direktur"></canvas>', $html);
                }
            } else if ($value == '@tanda_tangan_kabag_tu') {
                if ($signName) {
                    foreach ($signName as $index => $sign) {
                        if ($sign->name == str_replace('@', '', $value)) {
                            $html = str_replace($value, '<img src="' . $sign->signature . '" width="150" />', $html);
                        }
                    }
                } else {
                    $html = str_replace($value, '<canvas id="canvas6" class="signature-canvas" data-name="tanda_tangan_kabag_tu"></canvas>', $html);
                }
            } else if ($value == '@tanda_tangan_ksp') {
                if ($signName) {
                    foreach ($signName as $index => $sign) {
                        if ($sign->name == str_replace('@', '', $value)) {
                            $html = str_replace($value, '<img src="' . $sign->signature . '" width="150" />', $html);
                        }
                    }
                } else {
                    $html = str_replace($value, '<canvas id="canvas7" class="signature-canvas" data-name="tanda_tangan_ksp"></canvas>', $html);
                }
            } else {
                $html = str_replace($value, '................', $html);
            }
        }

        return $html;
    }
}
