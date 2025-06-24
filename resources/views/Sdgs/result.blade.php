<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>QUISIONER SDG</title>

  <link rel="stylesheet" href="{{ asset('css/result.css') }}">
  {{-- <link rel="stylesheet" href="{{ asset('css/st  yle.css') }}"> --}}

</head>

<body>
  @include('layouts.header')

  <div class="title">
    <h1>Hasil SDGs Event</h1>
  </div>
  <div id="sdg-results"></div>

  <script>
    const baseAssetUrl = "{{ asset('') }}";

    document.addEventListener('DOMContentLoaded', function() {
        const sdgScores = JSON.parse(sessionStorage.getItem('sdgScores')) || [];
        const selectedSDGs = sdgScores.map(item => item.sdg);

        if (selectedSDGs.length === 0) {
            document.getElementById('sdg-results').innerHTML = '<div class="no-result messages"><img src="img/decline.png" alt=""><h1>Terima Kasih!</h1><h2>Saat ini acara tersebut belum sepenuhnya memenuhi indikator atau kriteria yang selaras dengan poin-poin dalam SDGs. Namun, kami sangat menghargai inisiatif yang telah dilakukan.</h2></div>';
            return;
        }

        fetch('/get-sdg-data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ sdgs: selectedSDGs })
        })
        .then(response => response.json())
        .then(data => {
            let htmlContent = '';

            data.forEach(sdg => {
                htmlContent += `
                <div class="sdg-card">
                  <div class="sdg-header">${sdg.nama_sdg}</div>
                  <div class="sdg-content">
                    <img src="${baseAssetUrl}${sdg.logo_sdg}" alt="${sdg.nama_sdg}">
                    <div class="sdg-text">
                      <p>${sdg.deskripsi}</p>
                      <p><strong>Dampak bagi Kota Malang:</strong> ${sdg.dampak}</p>
                      <p><strong>Contoh Acara:</strong>${sdg.contoh}</p>
                    </div>
                  </div>
                </div>
                `;
            });

             htmlContent += `
                <div class="messages">
                  <img src="img/accept.png" alt="">
                  <h1>Terima Kasih!</h1>
                  <h2>Suaramu, Perubahan Nyata!</h2>
                </div>
              `;

            document.getElementById('sdg-results').innerHTML = htmlContent;
        })
        .catch(error => {
            console.error('Error fetching SDG data:', error);
            document.getElementById('sdg-results').innerHTML = '<p>Terjadi kesalahan saat memuat data.</p>';
        });
    });
  </script>
</body>

</html>