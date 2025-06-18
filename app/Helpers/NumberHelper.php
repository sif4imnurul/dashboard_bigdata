<?php

// app/Helpers/NumberHelper.php

if (!function_exists('formatHumanNumber')) {
    /**
     * Memformat angka besar menjadi format Rupiah yang ringkas (Miliar, Juta, Ribu).
     * Contoh: 5250000000 menjadi Rp 5.25 Miliar
     *
     * @param float|int|string $number Angka yang akan diformat.
     * @return string Angka yang sudah diformat atau 'N/A'.
     */
    function formatHumanNumber($number)
    {
        if (!is_numeric($number)) {
            return 'N/A';
        }
        
        if ($number == 0) {
            return 'Rp 0';
        }

        $absNumber = abs($number);
        $prefix = $number < 0 ? '-' : '';

        // Triliun (T)
        if ($absNumber >= 1e12) {
            return $prefix . 'Rp ' . number_format($absNumber / 1e12, 2) . ' T';
        }
        // Miliar (M)
        if ($absNumber >= 1e9) {
            return $prefix . 'Rp ' . number_format($absNumber / 1e9, 2) . ' Miliar';
        }
        // Juta (Jt)
        if ($absNumber >= 1e6) {
            return $prefix . 'Rp ' . number_format($absNumber / 1e6, 2) . ' Juta';
        }
        // Ribu (Rb)
        if ($absNumber >= 1e3) {
            return $prefix . 'Rp ' . number_format($absNumber / 1e3, 2) . ' Ribu';
        }

        return $prefix . 'Rp ' . number_format($absNumber, 2, ',', '.');
    }
}


// FUNGSI BARU UNTUK SUBSECTOR
if (!function_exists('formatSubsector')) {
    /**
     * Mengambil hanya nama subsector dari string (misal: "12. Perkebunan" menjadi "Perkebunan").
     *
     * @param string|null $subsectorString
     * @return string
     */
    function formatSubsector($subsectorString)
    {
        // Jika input kosong atau bukan string, kembalikan apa adanya.
        if (empty($subsectorString) || !is_string($subsectorString)) {
            return $subsectorString ?? 'N/A';
        }

        // Pisahkan string berdasarkan titik pertama kali muncul
        $parts = explode('.', $subsectorString, 2);

        // Jika ada dua bagian, ambil bagian kedua dan hapus spasi di awal
        if (count($parts) === 2) {
            return trim($parts[1]);
        }
        
        // Jika format tidak sesuai, kembalikan string aslinya
        return $subsectorString;
    }
}