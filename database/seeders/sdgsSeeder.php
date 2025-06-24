<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class sdgsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('sdgs')->insert([
            [
                'id_sdgs' => 'SDG01',
                'nama_sdg' => 'SDG 1: Tanpa Kemiskinan',
                'logo_sdg' => 'logo_sdg/sdg01.png',
                'deskripsi' => 'Menghapuskan kemiskinan dalam segala bentuk dan memastikan semua orang memiliki akses ke kebutuhan dasar seperti makanan, tempat tinggal, dan pekerjaan yang layak.',
                'dampak' => 'Membantu UMKM dan usaha kreatif berkembang sehingga menciptakan lebih banyak lapangan kerja.',
                'contoh' => 'Pelatihan bisnis untuk usaha kecil.'
            ],
        [
            'id_sdgs' => 'SDG02',
            'nama_sdg' => 'SDG 2: Tanpa Kelaparan',
            'logo_sdg' => 'logo_sdg/sdg02.png',
            'deskripsi' => 'Memastikan semua orang memiliki akses ke makanan bergizi dan mengembangkan sistem pangan yang berkelanjutan.',
            'dampak' => 'Meningkatkan inovasi di industri kuliner dan menciptakan lapangan kerja di sektor makanan.',
            'contoh' => 'Workshop pengolahan makanan sehat'
        ],
        [
            'id_sdgs' => 'SDG03',
            'nama_sdg' => 'SDG 3: Hidup Sehat dan Sejahtera',
            'logo_sdg' => 'logo_sdg/sdg03.png',
            'deskripsi' => 'Meningkatkan kesehatan dan kesejahteraan semua orang, termasuk akses ke layanan kesehatan dan gaya hidup sehat.',
            'dampak' => 'Meningkatkan kesadaran hidup sehat melalui komunitas kreatif.',
            'contoh' => 'Poundfit dan latihan karate'
        ],
        [
            'id_sdgs' => 'SDG04',
            'nama_sdg' => 'SDG 4: Pendidikan Berkualitas',
            'logo_sdg' => 'logo_sdg/sdg04.png',
            'deskripsi' => 'Memberikan pendidikan yang baik dan merata bagi semua orang agar memiliki keterampilan untuk masa depan.',
            'dampak' => 'Membantu anak muda mengembangkan keterampilan kreatif untuk bekerja atau berbisnis.',
            'contoh' => 'Kelas Basic Content Creator'
        ],
        [
            'id_sdgs' => 'SDG05',
            'nama_sdg' => 'SDG 5: Kesetaraan Gender',
            'logo_sdg' => 'logo_sdg/sdg05.png',
            'deskripsi' => 'Memberikan hak yang sama bagi laki-laki dan perempuan dalam semua aspek kehidupan, termasuk pekerjaan dan pendidikan.',
            'dampak' => 'Meningkatkan peran perempuan dalam industri kreatif.',
            'contoh' => 'Podcast pemberdayaan perempuan'
        ],
        [ 
            'id_sdgs' => 'SDG06',
            'nama_sdg' => 'SDG 6: Air Bersih dan Sanitasi',
            'logo_sdg' => 'logo_sdg/sdg06.png',
            'deskripsi' => 'Menjamin ketersediaan dan pengelolaan air bersih serta sanitasi yang layak untuk semua orang demi kesehatan dan kesejahteraan.',
            'dampak' => 'Meningkatkan akses air bersih dan sanitasi di kawasan padat penduduk dan daerah pinggiran untuk mendukung kesehatan masyarakat dan pariwisata berkelanjutan.',
            'contoh' => 'Belum terdata di MCC'
        ],
        [
            'id_sdgs' => 'SDG07',
            'nama_sdg' => 'SDG 7: Energi Bersih dan Terjangkau',
            'logo_sdg' => 'logo_sdg/sdg07.png',
            'deskripsi' => 'Menggunakan sumber energi yang ramah lingkungan dan dapat diperoleh dengan harga yang terjangkau.',
            'dampak' => 'Mendorong penggunaan energi terbarukan dalam bisnis kreatif.',
            'contoh' => 'Training Optimalisasi Manajemen Energi Untuk Efisiensi Operasional'
        ],
        [
            'id_sdgs' => 'SDG08',
            'nama_sdg' => 'SDG 8: Pekerjaan Layak dan Pertumbuhan Ekonomi',
            'logo_sdg' => 'logo_sdg/sdg08.png',
            'deskripsi' => 'Meningkatkan kesempatan kerja yang layak dan membangun ekonomi yang stabil untuk semua orang.',
            'dampak' => 'Meningkatkan lapangan kerja dalam sektor ekonomi kreatif.',
            'contoh' => 'Sosialisasi dan Pembekalan Praktek Kerja Lapangan & Outing Class'
        ],
        [
            'id_sdgs' => 'SDG09',
            'nama_sdg' => 'SDG 9:  Industri, Inovasi, dan Infrastruktur',
            'logo_sdg' => 'logo_sdg/sdg09.png',
            'deskripsi' => 'Mendorong pembangunan industri dan inovasi yang mendukung ekonomi berkelanjutan.',
            'dampak' => 'Mendukung pengembangan teknologi di sektor kreatif.',
            'contoh' => 'Seminar dan Workshop Teknologi Thermal dalam Industri Makanan Skala UMKM'
        ],
        [
            'id_sdgs' => 'SDG10',
            'nama_sdg' => 'SDG 10:  Berkurangnya Kesenjangan',
            'logo_sdg' => 'logo_sdg/sdg10.png',
            'deskripsi' => 'Mengurangi perbedaan ekonomi dan sosial di antara kelompok masyarakat.',
            'dampak' => 'Membantu kelompok kurang beruntung agar bisa ikut serta dalam kegiatan ekonomi kreatif.',
            'contoh' => 'Diskusi Panduan Akses Keadilan dan Perlindungan Hukum Bagi Penyandang Disabilitas'
        ],
        [
            'id_sdgs' => 'SDG11',
            'nama_sdg' => 'SDG 11:  Kota dan Permukiman yang Berkelanjutan',
            'logo_sdg' => 'logo_sdg/sdg11.png',
            'deskripsi' => 'Membangun kota yang ramah lingkungan, nyaman, dan layak huni.',
            'dampak' => 'Mengembangkan lingkungan kota yang mendukung kreativitas.',
            'contoh' => 'Forum Inovasi Infrastruktur Kota Berkelanjutan'
        ],
        [
            'id_sdgs' => 'SDG12',
            'nama_sdg' => 'SDG 12:  Konsumsi dan Produksi yang Bertanggung Jawab',
            'logo_sdg' => 'logo_sdg/sdg12.png',
            'deskripsi' => 'Menggunakan sumber daya secara bijak dan mengurangi limbah.',
            'dampak' => 'Mendorong bisnis kreatif yang ramah lingkungan.',
            'contoh' => 'Pelatihan daur ulang bahan bekas'
        ],
        [
            'id_sdgs' => 'SDG13',
            'nama_sdg' => 'SDG 13:  Penanganan Perubahan Iklim',
            'logo_sdg' => 'logo_sdg/sdg13.png',
            'deskripsi' => 'Mengambil tindakan cepat untuk mengatasi perubahan iklim dan dampaknya melalui kebijakan dan edukasi lingkungan.',
            'dampak' => 'Mengurangi polusi dan emisi karbon melalui transportasi ramah lingkungan dan penghijauan kota.',
            'contoh' => 'Diplomasi Malang Raya: "Mengambil peran Agent Of Change Mengubah Lingkungan yang Cerdas dan Berkelanjutan”'
        ],
        [
            'id_sdgs' => 'SDG14',
            'nama_sdg' => 'SDG 14:  Ekosistem Lautan',
            'logo_sdg' => 'logo_sdg/sdg14.png',
            'deskripsi' => 'Melestarikan dan memanfaatkan sumber daya laut secara berkelanjutan untuk menjaga keseimbangan ekosistem dan mata pencaharian masyarakat pesisir.',
            'dampak' => 'Melestarikan pesisir selatan melalui edukasi dan pelibatan masyarakat pesisir dalam menjaga kebersihan laut dan ekowisata.',
            'contoh' => 'Dialog Biru: Sinergi Mangrove, Lamun, dan Terumbu Karang Bagi Ketahanan Ekosistem Laut'
        ],
        [
            'id_sdgs' => 'SDG15',
            'nama_sdg' => 'SDG 15:  Ekosistem Daratan',
            'logo_sdg' => 'logo_sdg/sdg15.png',
            'deskripsi' => 'Menjaga hutan, satwa liar, dan lingkungan alam lainnya.',
            'dampak' => 'Meningkatkan kesadaran pelestarian lingkungan.',
            'contoh' => 'Dharma Wanita Persatuan (DWP) Kota Malang - Pelatihan Ecoprint.'
        ],
        [
            'id_sdgs' => 'SDG16',   
            'nama_sdg' => 'SDG 16:  Perdamaian, Keadilan, dan Kelembagaan yang Kuat',
            'logo_sdg' => 'logo_sdg/sdg16.png',
            'deskripsi' => 'Membangun masyarakat yang damai, adil, dan transparan.',
            'dampak' => ' Mendukung komunitas kreatif yang inklusif.',
            'contoh' => 'Webinar Antikorupsi'
        ],
        [
            'id_sdgs' => 'SDG17',
            'nama_sdg' => 'SDG 17:  Kemitraan untuk Mencapai Tujuan',
            'logo_sdg' => 'logo_sdg/sdg17.png',
            'deskripsi' => 'Bekerja sama untuk mencapai semua tujuan SDGs.',
            'dampak' => ' Meningkatkan kolaborasi dalam industri kreatif.',
            'contoh' => 'Forum diskusi bisnis kreatif'
        ]
        ]
    );
    }
}
