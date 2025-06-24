<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kuesioner SDG</title>
    <link rel="stylesheet" href="{{ asset('css/quiz.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    @include('layouts.header')

    <div class="title">
      <h1>Dari MCC Untuk Dunia</h1>
      <h2>#SDGsAction</h2>
    </div>

    <div class="pengertian">
      <span>Sustainable Development Goals <b>(SDGs)</b></span> merupakan 17 tujuan global yang
      ditetapkan oleh Perserikatan Bangsa-Bangsa (PBB) untuk menciptakan dunia yang lebih sejahtera dan
      berkelanjutan. Tujuan dari SDGs sendiri mencakup berbagai aspek, seperti pengentasan
      kemiskinan, peningkatan kesehatan, pendidikan, serta perlindungan
      lingkungan. Penerapan SDGs di Kota Malang diharapkan dapat membantu
      meningkatkan kesejahteraan masyarakat, mendukung perkembangan ekonomi
      kreatif, serta menjaga kelestarian lingkungan agar kota tetap nyaman dan
      berkelanjutan.
    </div>

    <div class="instruksi">
      <div class="title-instruksi">
        <h1>Instruksi Pengisian Kuesioner</h1>
      </div>
      <div class="list-instruksi">
        <ol>
          <li>Pilih jawaban sesuai dengan acara yang dilaksanakan</li>
          <li>Setiap pertanyaan dapat memiliki lebih dari satu jawaban</li>
          <li>
            Hasil akhir merupakan nilai SDGs yang terkandung dalam acara dan
            akan digunakan MCC sebagai arsip data
          </li>
        </ol>
      </div>
    </div>

    <script>
      // Mengisi booking ID
      const bookingId = "{{ $bookingId ?? '' }}";
      document.getElementById('bookingId').value = bookingId;
    </script>

    <div class="kuisioner">
      <div class="question">
        <h3>ID Booking</H3>
      </div>
      <input type="text" id="bookingId" name="bookingId" class="form-input" readonly style="background-color: #f5f5f5;" />
    </div>

    <!-- PERTANYAAN 1 -->
    <div class="kuisioner">
      <div class="question">
        <h3>1. Apa jenis utama dari event yang akan diselenggarakan?</h3>
      </div>
      <div class="answer">
        <form>
          <input type="checkbox" id="q1_answer1" name="q1_answer1" value="q1_answer1"/>
          <label for="q1_answer1">Seminar / Workshop / Edukasi</label><br />
          <input type="checkbox" id="q1_answer2" name="q1_answer2" value="q1_answer2"/>
          <!-- <label for="q1_answer2"> Festival Kuliner / Produk Lokal</label><br /> -->
          <label for="q1_answer2">Festival Kuliner / Produksi Lokal</label><br />
          <input type="checkbox" id="q1_answer3" name="q1_answer3" value="q1_answer3"/>
          <label for="q1_answer3">Program Ketahanan Pangan / Pertanian Kota / Edukasi Gizi</label><br />
          <input type="checkbox" id="q1_answer4" name="q1_answer4" value="q1_answer4"/>
          <label for="q1_answer4">Kegiatan Sosial / Penggalangan Dana</label><br />
          <input type="checkbox" id="q1_answer5" name="q1_answer5" value="q1_answer5"/>
          <label for="q1_answer5">Kompetisi / Hackathon</label><br />
          <input type="checkbox" id="q1_answer6" name="q1_answer6" value="q1_answer6"/>
          <label for="q1_answer6">Pameran Seni & Budaya</label><br />
          <input type="checkbox" id="q1_answer7" name="q1_answer7" value="q1_answer7"/>
          <label for="q1_answer7">Rapat / Forum Bisnis / Networking</label><br />
          <input type="checkbox" id="q1_answer8" name="q1_answer8" value="q1_answer8"/>
          <label for="q1_answer8">Event Teknologi & Inovasi</label><br />
          <input type="checkbox" id="q1_answer9" name="q1_answer9" value="q1_answer9"/>
          <label for="q1_answer9">Kampanye Kesadaran Lingkungan</label><br />
          <input type="checkbox" id="q1_answer10" name="q1_answer10" value="q1_answer10"/>
          <label for="q1_answer10">Pelatihan tentang Kesetaraan Gender & Keterlibatan Sosial</label><br />
          <input type="checkbox" id="q1_answer11" name="q1_answer11" value="q1_answer11"/>
          <label for="q1_answer11">Program Pengembangan Jasmani</label><br />
          <input type="checkbox" id="q1_answer12" name="q1_answer12" value="q1_answer12"/>
          <label for="q1_answer12">Seminar Keberlanjutan & Energi Terbarukan</label><br />
        </form>
      </div>
    </div>

    <!-- PERTANYAAN 2 -->
    <div class="kuisioner">
      <div class="question">
        <h3>2. Siapa target utama (pengunjung atau peserta) dari event ini?</h3>
      </div>
      <div class="answer">
        <form>
          <input type="checkbox" id="q2_answer1" name="q2_answer1" value="q2_answer1" />
          <label for="q2_answer1"> Pelajar / Mahasiswa</label><br />
          <input type="checkbox" id="q2_answer2" name="q2_answer2" value="q2_answer2" />
          <label for="q2_answer2"> Masyarakat Umum</label><br />
          <input type="checkbox" id="q2_answer3" name="q2_answer3" value="q2_answer3" />
          <label for="q2_answer3"> UMKM / Startup</label><br />
          <input type="checkbox" id="q2_answer4" name="q2_answer4" value="q2_answer4" />
          <label for="q2_answer4"> Perusahaan / Investor</label><br />
          <input type="checkbox" id="q2_answer5" name="q2_answer5" value="q2_answer5" />
          <label for="q2_answer5"> Komunitas Seni & Budaya</label><br />
          <input type="checkbox" id="q2_answer6" name="q2_answer6" value="q2_answer6" />
          <label for="q2_answer6"> Kelompok Rentan (Difabel, Lansia, Kelompok Masyarakat Prasejahtera, dsb.)</label><br />
          <input type="checkbox" id="q2_answer7" name="q2_answer7" value="q2_answer7" />
          <label for="q2_answer7"> Organisasi Lingkungan & Sosial</label><br />
          <input type="checkbox" id="q2_answer8" name="q2_answer8" value="q2_answer8" />
          <label for="q2_answer8"> Pemerintahan</label><br />
        </form>
      </div>
    </div>

    <!-- PERTANYAAN 3 -->
    <div class="kuisioner">
      <div class="question">
        <h3>3. Apa tujuan utama event ini?</h3>
      </div>
      <div class="answer">
        <form>
          <input type="checkbox" id="q3_answer1" name="q3_answer1" value="q3_answer1" />
          <label for="q3_answer1"> Memberikan edukasi atau pelatihan</label><br />
          <input type="checkbox" id="q3_answer2" name="q3_answer2" value="q3_answer2" />
          <label for="q3_answer2"> Memberdayakan ekonomi lokal</label><br />
          <input type="checkbox" id="q3_answer3" name="q3_answer3" value="q3_answer3" />
          <label for="q3_answer3"> Meningkatkan kesadaran sosial</label><br />
          <input type="checkbox" id="q3_answer4" name="q3_answer4" value="q3_answer4" />
          <label for="q3_answer4"> Melindungi lingkungan</label><br />
          <input type="checkbox" id="q3_answer5" name="q3_answer5" value="q3_answer5" />
          <label for="q3_answer5"> Mempromosikan energi bersih</label><br />
          <input type="checkbox" id="q3_answer6" name="q3_answer6" value="q3_answer6" />
          <label for="q3_answer6"> Mendukung inovasi dan teknologi</label><br />
          <input type="checkbox" id="q3_answer7" name="q3_answer7" value="q3_answer7" />
          <label for="q3_answer7"> Mendukung pengelolaan air dan sanitasi</label><br />
          <input type="checkbox" id="q3_answer8" name="q3_answer8" value="q3_answer8" />
          <label for="q3_answer8"> Membangun kerja sama lintas bidang / sektor</label><br />
          <input type="checkbox" id="q3_answer9" name="q3_answer9" value="q3_answer9" />
          <label for="q3_answer9"> Memajukan budaya dan seni lokal</label><br />
          <input type="checkbox" id="q3_answer10" name="q3_answer10" value="q3_answer10" />
          <label for="q3_answer10"> Memberdayakan masyarakat</label><br />
          <input type="checkbox" id="q3_answer11" name="q3_answer11" value="q3_answer11" />
          <label for="q3_answer11"> Meningkatkan ketahanan pangan dan akses pangan bergizi</label><br />
        </form>
      </div>
    </div>

    <!-- PERTANYAAN 4 -->
    <div class="kuisioner">
      <div class="question">
        <h3>4. Apakah event ini memiliki dampak pada lingkungan?</h3>
      </div>
      <div class="answer">
        <form>
          <input type="checkbox" id="q4_answer1" name="q4_answer1" value="q4_answer1" />
          <label for="q4_answer1"> Ya, mengurangi limbah dan pemanasan global</label><br />
          <input type="checkbox" id="q4_answer2" name="q4_answer2" value="q4_answer2" />
          <label for="q4_answer2"> Ya, meningkatkan pengelolaan sumber daya alam berkelanjutan</label><br />
          <input type="checkbox" id="q4_answer3" name="q4_answer3" value="q4_answer3" />
          <label for="q4_answer3"> Ya, mempromosikan energi bersih dan efisiensi energi</label><br />
          <input type="checkbox" id="q4_answer4" name="q4_answer4" value="q4_answer4" />
          <label for="q4_answer4"> Ya, menyediakan edukasi atau akses air bersih dan sanitasi</label><br />
          <input type="checkbox" id="q4_answer5" name="q4_answer5" value="q4_answer5" />
          <label for="q4_answer5"> Tidak secara langsung</label><br />
        </form>
      </div>
    </div>

    <!-- PERTANYAAN 5 -->
    <div class="kuisioner">
      <div class="question">
        <h3>5. Apakah event ini mendukung kesehatan dan kesejahteraan masyarakat?</h3>
      </div>
      <div class="answer">
        <form>
          <input type="checkbox" id="q5_answer1" name="q5_answer1" value="q5_answer1" />
          <label for="q5_answer1"> Ya, ada program kesehatan atau olahraga</label><br />
          <input type="checkbox" id="q5_answer2" name="q5_answer2" value="q5_answer2" />
          <label for="q5_answer2"> Ya, ada penggalangan dana untuk layanan kesehatan</label><br />
          <input type="checkbox" id="q5_answer3" name="q5_answer3" value="q5_answer3" />
          <label for="q5_answer3"> Ya, ada edukasi kesehatan mental & sosial</label><br />
          <input type="checkbox" id="q5_answer4" name="q5_answer4" value="q5_answer4" />
          <label for="q5_answer4"> Tidak secara langsung</label><br />
        </form>
      </div>
    </div>

    <!-- PERTANYAAN 6 -->
    <div class="kuisioner">
      <div class="question">
        <h3>6. Apakah event ini mendukung kesetaraan gender atau kesetaraan dalam masyarakat?</h3>
      </div>
      <div class="answer">
        <form>
          <input type="checkbox" id="q6_answer1" name="q6_answer1" value="q6_answer1" />
          <label for="q6_answer1"> Ya, event ini terbuka untuk semua orang tanpa terkecuali</label><br />
          <input type="checkbox" id="q6_answer2" name="q6_answer2" value="q6_answer2" />
          <label for="q6_answer2"> Ya, event ini mendukung kelompok yang membutuhkan bantuan lebih (seperti penyandang disabilitas, lansia, atau kelompok masyarakat prasejahtera)</label><br />
          <input type="checkbox" id="q6_answer3" name="q6_answer3" value="q6_answer3" />
          <label for="q6_answer3"> Ya, event ini mendorong perempuan untuk lebih aktif di dunia kerja dan usaha</label><br />
          <input type="checkbox" id="q6_answer4" name="q6_answer4" value="q6_answer4" />
          <label for="q6_answer4"> Tidak secara langsung</label><br />
        </form>
      </div>
    </div>

    <!-- PERTANYAAN 7 -->
    <div class="kuisioner">
      <div class="question">
        <h3>7. Apakah event ini mendorong inovasi dan kemajuan industri?</h3>
      </div>
      <div class="answer">
        <form>
          <input type="checkbox" id="q7_answer1" name="q7_answer1" value="q7_answer1" />
          <label for="q7_answer1"> Ya, fokus pada teknologi dan industri kreatif</label><br />
          <input type="checkbox" id="q7_answer2" name="q7_answer2" value="q7_answer2" />
          <label for="q7_answer2"> Ya, mendukung UMKM dan startup</label><br />
          <input type="checkbox" id="q7_answer3" name="q7_answer3" value="q7_answer3" />
          <label for="q7_answer3"> Ya, mempromosikan energi & industri ramah lingkungan</label><br />
          <input type="checkbox" id="q7_answer4" name="q7_answer4" value="q7_answer4" />
          <label for="q7_answer4"> Ya, membangun kemitraan global & lokal</label><br />
          <input type="checkbox" id="q7_answer5" name="q7_answer5" value="q7_answer5" />
          <label for="q7_answer5"> Tidak secara langsung</label><br />
        </form>
      </div>
    </div>

    <div class="submit">
      <button id="submitBtn">Submit</button>
    </div>

    <!-- Custom Popup -->
    <div id="customPopup" class="popup-container">
      <div class="popup-content">
        <div class="popup-header">
          <h3 id="popupTitle">Notifikasi</h3>
          <span class="close-btn">×</span>
        </div>
        <div class="popup-body">
          <p id="popupMessage">Ini adalah pesan popup kustom.</p>
        </div>
        <div class="popup-footer">
          <button id="popupConfirmBtn" class="popup-btn confirm">OK</button>
        </div>
      </div>
    </div>

    <script src="{{ asset('js/index.js') }}"></script>
  </body>
</html>