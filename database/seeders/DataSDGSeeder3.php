<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;

class DataSDGSeeder3 extends Seeder
{
    public function run(): void
    {
        $data = [
// JANUARI
            'MCC-2501-KFXL' => "[[\"SDG08\",\"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2501-D1XI' => "[[\"SDG08\",\"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"], [\"SDG04\",\"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2412-QWPZ' => "[[\"SDG04\",\"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"], [\"SDG10\",\"Mengurangi perbedaan ekonomi dan sosial di antara kelompok masyarakat.\"]]",
            'MCC-2412-UTWM' => "[[\"SDG04\",\"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2412-ZGYA' => "[[\"SDG04\",\"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2412-4ZCH' => "[[\"SDG09\",\"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan\"]]",
            'MCC-2412-Y4W0' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2412-ULOX' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG17\", \" Meningkatkan kolaborasi dalam industri kreatif.\"]]",
            'MCC-2412-PFU5' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG17\", \" Meningkatkan kolaborasi dalam industri kreatif.\"]]",
            'MCC-2412-JJVA' => "[[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            
            'MCC-2412-WA4X' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2501-ILWM' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG16\", \"Membangun masyarakat yang damai, adil, dan transparan.\"], [\"SDG17\", \"Bekerja sama untuk mencapai semua tujuan SDGs.\"]]",
            'MCC-2501-WYXS' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG17\", \"Meningkatkan kolaborasi dalam industri kreatif.\"]]",
            'MCC-2412-KYIG' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"],[\"SDG12\", \"Menggunakan sumber daya secara bijak dan mengurangi limbah.\"]]",
            'MCC-2412-BPQB' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG03\", \"Meningkatkan kesehatan dan kesejahteraan semua orang, termasuk akses ke layanan kesehatan dan gaya hidup sehat.\"]]",
            'MCC-2412-AEUY' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2412-91DO' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG10\", \"Mengurangi perbedaan ekonomi dan sosial di antara kelompok masyarakat.\"]]",
            'MCC-2412-FNF9' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG10\", \"Mengurangi perbedaan ekonomi dan sosial di antara kelompok masyarakat.\"],[\"SDG16\", \"Membangun masyarakat yang damai, adil, dan transparan.\"]]",
            'MCC-2412-JJVA' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG10\", \"Mengurangi perbedaan ekonomi dan sosial di antara kelompok masyarakat.\"]]",
            'MCC-2412-20HB' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            
            'MCC-2412-5EC8' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",

// FEBRUARI

            'MCC-2501-WYKL' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG03\", \"Meningkatkan kesehatan dan kesejahteraan semua orang, termasuk akses ke layanan kesehatan dan gaya hidup sehat.\"]]",
            'MCC-2501-KKEB' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG10\", \"Mengurangi perbedaan ekonomi dan sosial di antara kelompok masyarakat.\"]]",
            'MCC-2501-WAZX' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2501-WM8A' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2501-5CUF' => "[[\"SDG05\", \"Memberikan hak yang sama bagi laki-laki dan perempuan dalam semua aspek kehidupan, termasuk pekerjaan dan pendidikan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2501-0U0C' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2501-OYZA' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2412-VLP8' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG17\", \"Bekerja sama untuk mencapai semua tujuan SDGs.\"]]",
            'MCC-2412-DASB' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2412-0FEC' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",

            'MCC-2412-4JS3' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2412-WYOH' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2412-MYAC' => "[[\"SDG05\", \"Memberikan hak yang sama bagi laki-laki dan perempuan dalam semua aspek kehidupan, termasuk pekerjaan dan pendidikan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2411-XSBO' => "[[\"SDG03\", \"Meningkatkan kesehatan dan kesejahteraan semua orang, termasuk akses ke layanan kesehatan dan gaya hidup sehat.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2501-OYUV' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2501-D72Y' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2501-GTOT' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2501-NGTB' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2501-I98H' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2501-NOE3' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG02\", \"Memastikan semua orang memiliki akses ke makanan bergizi dan mengembangkan sistem pangan yang berkelanjutan.\"]]",

            'MCC-2501-U1XV' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2501-KSBV' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG05\", \"Memberikan hak yang sama bagi laki-laki dan perempuan dalam semua aspek kehidupan, termasuk pekerjaan dan pendidikan.\"]]",
            'MCC-2501-V1TY' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2412-XRLR' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG12\", \"Menggunakan sumber daya secara bijak dan mengurangi limbah.\"]]",
            'MCC-2501-PPD2' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG02\", \"Memastikan semua orang memiliki akses ke makanan bergizi dan mengembangkan sistem pangan yang berkelanjutan.\"]]",
            'MCC-2412-K0BQ' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2412-GIB1' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG02\", \"Memastikan semua orang memiliki akses ke makanan bergizi dan mengembangkan sistem pangan yang berkelanjutan.\"]]",
            'MCC-2502-OBMR' => "[[\"SDG03\", \"Meningkatkan kesehatan dan kesejahteraan semua orang, termasuk akses ke layanan kesehatan dan gaya hidup sehat.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2502-7XWW' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2501-BPX7' => "[[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",

// MARET

            'MCC-2502-GIMS' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG17\", \"Bekerja sama untuk mencapai semua tujuan SDGs.\"]]",
            'MCC-2502-LYPW' => "[[\"SDG02\", \"Memastikan semua orang memiliki akses ke makanan bergizi dan mengembangkan sistem pangan yang berkelanjutan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2502-CPIJ' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2502-XAXN' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2502-U8XK' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2502-QHLK' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2502-SNXA' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2502-LYC5' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2502-NO01' => "[[\"SDG02\", \"Memastikan semua orang memiliki akses ke makanan bergizi dan mengembangkan sistem pangan yang berkelanjutan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2502-AL4K' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",

            'MCC-2502-CCO7' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2502-X2MD' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2501-EBLO' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2501-OOYC' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG03\", \"Meningkatkan kesehatan dan kesejahteraan semua orang, termasuk akses ke layanan kesehatan dan gaya hidup sehat.\"]]",
            'MCC-2501-YRSN' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2501-RGMI' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG17\", \"Bekerja sama untuk mencapai semua tujuan SDGs.\"]]",
            'MCC-2502-JRS6' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2502-EGHL' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG16\", \"Membangun masyarakat yang damai, adil, dan transparan.\"]]",
            'MCC-2502-TRAY' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG16\", \"Membangun masyarakat yang damai, adil, dan transparan.\"]]",
            'MCC-2502-MWQ5' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",

            'MCC-2502-71CE' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2502-5LCK' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2501-UMGS' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG12\", \"Menggunakan sumber daya secara bijak dan mengurangi limbah.\"]]",
            'MCC-2501-MQBG' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2503-RIYM' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2502-4XGF' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2502-6FM7' => "[[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2502-0HMA' => "[[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"],[\"SDG17\", \"Bekerja sama untuk mencapai semua tujuan SDGs.\"]]",
            'MCC-2502-THHX' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG12\", \"Menggunakan sumber daya secara bijak dan mengurangi limbah.\"]]",
            'MCC-2503-0PNH' => "[[\"SDG10\", \"Mengurangi perbedaan ekonomi dan sosial di antara kelompok masyarakat.\"],[\"SDG16\", \"Membangun masyarakat yang damai, adil, dan transparan.\"]]",

// APRIL
            'MCC-2504-B8LM' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2504-ZLIJ' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2504-TBQI' => "[[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2504-RIVP' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG16\", \"Membangun masyarakat yang damai, adil, dan transparan.\"]]",
            'MCC-2504-OXFX' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2504-DV5N' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG05\", \"Memberikan hak yang sama bagi laki-laki dan perempuan dalam semua aspek kehidupan, termasuk pekerjaan dan pendidikan.\"]]",
            'MCC-2504-KDGQ' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG11\", \"Membuat kota dan permukiman inklusif, aman, tahan lama, dan berkelanjutan.\"]]",
            'MCC-2503-1E46' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2504-KDGQ' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG11\", \"Membuat kota dan permukiman inklusif, aman, tahan lama, dan berkelanjutan.\"]]",
            'MCC-2503-YOW4' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",

            'MCC-2504-LQES' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2504-DP6D' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG10\", \"Mengurangi perbedaan ekonomi dan sosial di antara kelompok masyarakat.\"]]",
            'MCC-2503-CKWC' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2503-XIFS' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2503-JRW3' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2502-W3EL' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2502-F2WG' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2504-PMTZ' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG16\", \"Membangun masyarakat yang damai, adil, dan transparan.\"]]",
            'MCC-2504-4YLV' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2504-6EWC' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",

            'MCC-2502-W3EL' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2504-5QMH' => "[[\"SDG05\", \"Memberikan hak yang sama bagi laki-laki dan perempuan dalam semua aspek kehidupan, termasuk pekerjaan dan pendidikan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2504-SV5C' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG10\", \"Mengurangi perbedaan ekonomi dan sosial di antara kelompok masyarakat.\"]]",
            'MCC-2504-BJYB' => "[[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2504-7TNS' => "[[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2504-LO1B' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG05\", \"Memberikan hak yang sama bagi laki-laki dan perempuan dalam semua aspek kehidupan, termasuk pekerjaan dan pendidikan.\"]]",
            'MCC-2504-SVUB' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2504-MD8B' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2503-CJA8' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2504-XKFF' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",

// MEI

            'MCC-2505-VAEV' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2504-TXFS' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2504-QXCU' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2504-2OYP' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2504-TLRS' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2504-TUTF' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2504-ZR5A' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2504-QX4M' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2504-FJMW' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2504-2WZA' => "[[\"SDG16\", \"Membangun masyarakat yang damai, adil, dan transparan.\"],[\"SDG17\", \"Bekerja sama untuk mencapai semua tujuan SDGs.\"]]",

            'MCC-2504-LJKX' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2504-7HLB' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2504-BPJL' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2504-QQT5' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2503-PW1D' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2503-SIDW' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2503-R8BE' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"]]",
            'MCC-2504-N2JL' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2503-HHLU' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG16\", \"Membangun masyarakat yang damai, adil, dan transparan.\"]]",
            'MCC-2504-MXER' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG05\", \"Memberikan hak yang sama bagi laki-laki dan perempuan dalam semua aspek kehidupan, termasuk pekerjaan dan pendidikan.\"]]",

            'MCC-2504-6JR9' => "[[\"SDG05\", \"Memberikan hak yang sama bagi laki-laki dan perempuan dalam semua aspek kehidupan, termasuk pekerjaan dan pendidikan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2503-R8BE' => "[[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2503-SIDW' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"]]",
            'MCC-2503-YIUS' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG03\", \"Meningkatkan kesehatan dan kesejahteraan semua orang, termasuk akses ke layanan kesehatan dan gaya hidup sehat.\"]]",
            'MCC-2505-9DXI' => "[[\"SDG09\", \"Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.\"],[\"SDG17\", \"Bekerja sama untuk mencapai semua tujuan SDGs.\"]]",
            'MCC-2504-IVFJ' => "[[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"],[\"SDG10\", \"Mengurangi perbedaan ekonomi dan sosial di antara kelompok masyarakat.\"]]]",
            'MCC-2504-FKFH' => "[[\"SDG08\", \"Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.\"],[\"SDG17\", \"Bekerja sama untuk mencapai semua tujuan SDGs.\"]]",
            'MCC-2504-ONHY' => "[[\"SDG03\", \"Meningkatkan kesehatan dan kesejahteraan semua orang, termasuk akses ke layanan kesehatan dan gaya hidup sehat.\"],[\"SDG04\", \"Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.\"]]",
            'MCC-2505-AHT5' => "[[\"SDG03\", \"Meningkatkan kesehatan dan kesejahteraan dengan mendorong partisipasi sosial dan aktivitas fisik melalui pertunjukan.\"],[\"SDG04\", \"Mengembangkan keterampilan seni dan budaya sebagai bagian dari pendidikan inklusif dan berkualitas.\"]]",
            'MCC-2504-6HDL' => "[[\"SDG08\", \"Mendukung pertumbuhan ekonomi yang inklusif dan berkelanjutan melalui pengembangan industri kreatif game.\"],[\"SDG09\", \"Mendorong inovasi dan infrastruktur teknologi dalam bidang pengembangan game dan hiburan digital.\"]]",
            
        ];

        foreach ($data as $kode => $sdgs) {
            Booking::where('kode_booking', $kode)
                ->update(['sdgs_data' => json_encode($sdgs)]);
        }
    }
}
