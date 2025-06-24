<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;

class DataSDGSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'MCC-2505-QD0O' => "[[\"SDG04\",\"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"], [\"SDG08\",\"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2505-DZ1G' => "[[\"SDG04\",\"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"], [\"SDG16\",\"Membangun masyarakat yang damai, adil, dan transparan.\"]]",
            'MCC-2412-QWPZ' => "[[\"SDG04\",\"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"], [\"SDG10\",\"Mengurangi perbedaan ekonomi dan sosial di antara kelompok masyarakat.\"]]",
        ];

        foreach ($data as $kode => $sdgs) {
            Booking::where('kode_booking', $kode)
                ->update(['sdgs_data' => json_encode($sdgs)]);
        }
    }
}
