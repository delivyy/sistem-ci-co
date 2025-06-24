<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sub Sektor Ekonomi Kreatif</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: sans-serif;
      padding: 40px;
    }
    #subsektorChart {
        width: 100%;
      max-width: 1000px;
      margin: auto;
    }
  </style>
</head>
<body>

  <h4>Sub Sektor Ekonomi Kreatif</h4>
  <canvas id="subsektorChart"></canvas>

  <script>
    const dataValues = [140, 200, 120, 100, 160, 30, 130, 110, 20, 40, 80, 200, 160, 140, 110, 190, 90];
    const backgroundColors = dataValues.map((_, i) => i % 2 === 0 ? '#F44336' : '#FFCA28');

    new Chart(document.getElementById('subsektorChart'), {
      type: 'bar',
      data: {
        labels: [
            'Arsitektur', 'Film', 'Fotografi', 'Kriya', 'Kuliner', 'Seni Rupa', 'Produk',
            'Aplikasi', 'Game', 'TV & Radio', 'Fashion', 'Pertunjukan',
            'Desain Interior', 'Periklanan', 'Penerbitan', 'DKV', 'Musik'
        ],
        datasets: [{
            label: 'Jumlah',
            data: dataValues,
            backgroundColor: backgroundColors,
            barThickness: 20
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              drawBorder: false,
              color: '#F3F2F7'
            }
          },
          x: {
            ticks: {
              font: { size: 11 }
            },
            grid: { display: false }
          }
        }
      }
    });
  </script>

</body>
</html>
