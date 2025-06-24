const ChartCenterTextPlugin = {
    id: "centerText",
    beforeDraw(chart) {
        if (!chart.config.options.centerText?.text) return;
        const { width } = chart;
        const { ctx } = chart;
        const txt = chart.config.options.centerText.text;
        ctx.restore();
        const fontSize = (width / 8).toFixed(2);
        ctx.font = `600 ${fontSize}px sans-serif`;
        ctx.textBaseline = "middle";
        ctx.textAlign = "center";
        const centerX = chart.getDatasetMeta(0).data[0]?.x || 0;
        const centerY = chart.getDatasetMeta(0).data[0]?.y || 0;
        ctx.fillStyle = "#2d3748";
        ctx.fillText(txt, centerX, centerY);
        ctx.save();
    },
};

const ChartHorizontalLegend = {
    id: "custom-legend",
    afterUpdate(chart) {
        const container = document.getElementById("legend-container");
        if (!container) {
            console.warn("Legend container (#legend-container) not found");
            return;
        }
        const bidangLabels = container.querySelector("#bidangLabels");
        if (!bidangLabels) {
            console.warn(
                "Legend list (#bidangLabels) not found inside #legend-container"
            );
            return;
        }
        bidangLabels.innerHTML = "";
        chart.data.labels.forEach((label, i) => {
            const type =
                document.querySelector(
                    'input[name="chartBidangToggle"]:checked'
                )?.value || "count";
            const value =
                type === "count"
                    ? chart.data.datasets[0].data[i].toLocaleString("id-ID")
                    : Number(chart.data.datasets[0].data[i]).toFixed(2);
            const color = chart.data.datasets[0].backgroundColor[i];
            const suffix = type === "percent" ? "%" : "";
            const item = document.createElement("li");
            item.classList.add("legend-item");
            item.innerHTML = `
                <div class="legend-color" style="background-color:${color}; width: 12px; height: 12px; border-radius: 50%; display: inline-block; margin-right: 10px;"></div>
                <span class="legend-label">${label}</span>
                <span class="legend-value">${value}${suffix}</span>
            `;
            bidangLabels.appendChild(item);
        });
    },
};

Chart.defaults.animation = false;
Chart.defaults.animations = {
    colors: false,
    x: false,
    y: false,
};

let chartNonKomersial;
let bidangChart;
let chartSektor;
let chartTotal;
let top3Chart;

function showFullPageLoadingOverlay() {
    const existingOverlay = document.querySelector(
        ".full-page-loading-overlay"
    );
    if (existingOverlay) existingOverlay.remove();
    const overlay = document.createElement("div");
    overlay.className = "full-page-loading-overlay";
    overlay.style.position = "fixed";
    overlay.style.top = "0";
    overlay.style.left = "0";
    overlay.style.width = "100%";
    overlay.style.height = "100%";
    overlay.style.background = "rgba(255, 255, 255, 0.8)";
    overlay.style.zIndex = "1000";
    overlay.style.display = "flex";
    overlay.style.alignItems = "center";
    overlay.style.justifyContent = "center";
    const screenEffect = document.createElement("div");
    screenEffect.className = "screen-effect";
    screenEffect.style.width = "100%";
    screenEffect.style.height = "100%";
    screenEffect.style.background =
        "linear-gradient(to right, rgba(200, 200, 200, 0), rgba(200, 200, 200, 0.8), rgba(200, 200, 200, 0))";
    screenEffect.style.animation = "screenRight 1.5s infinite";
    overlay.appendChild(screenEffect);
    document.body.appendChild(overlay);
}

function hideFullPageLoadingOverlay() {
    const overlay = document.querySelector(".full-page-loading-overlay");
    if (overlay) overlay.remove();
}

function showLoadingOverlay(containerId) {
    const container =
        document.getElementById(containerId) ||
        document.querySelector(containerId);
    if (!container) return;
    const existingOverlay = container.querySelector(".loading-overlay");
    if (existingOverlay) existingOverlay.remove();
    const overlay = document.createElement("div");
    overlay.className = "loading-overlay";
    overlay.style.position = "absolute";
    overlay.style.top = "0";
    overlay.style.leftfocus = "0";
    overlay.style.width = "100%";
    overlay.style.height = "100%";
    overlay.style.background = "rgba(255, 255, 255, 0.8)";
    overlay.style.zIndex = "10";
    overlay.style.display = "flex";
    overlay.style.alignItems = "center";
    overlay.style.justifyContent = "center";
    const screenEffect = document.createElement("div");
    screenEffect.className = "screen-effect";
    screenEffect.style.width = "100%";
    screenEffect.style.height = "100%";
    screenEffect.style.background =
        "linear-gradient(to right, rgba(200, 200, 200, 0), rgba(200, 200, 200, 0.8), rgba(200, 200, 200, 0))";
    screenEffect.style.animation = "screenRight 1.5s infinite";
    overlay.appendChild(screenEffect);
    container.style.position = "relative";
    container.appendChild(overlay);
}

function hideLoadingOverlay(containerId) {
    const container =
        document.getElementById(containerId) ||
        document.querySelector(containerId);
    if (!container) return;
    const overlay = container.querySelector(".loading-overlay");
    if (overlay) overlay.remove();
}

function renderChartNonKomersial(jumlah, persen) {
    const canvas = document.getElementById("chartNonKomersial");
    if (!canvas) {
        console.error("Canvas #chartNonKomersial not found");
        return;
    }
    const type =
        document.querySelector('input[name="chartKomersialToggle"]:checked')
            ?.value || "count";
    console.log("Chart type selected:", type);
    const totalJumlah = (jumlah?.komersial ?? 0) + (jumlah?.Empowerment ?? 0); 
    const calculatedPersen = {
        komersial: totalJumlah
            ? Number(((jumlah?.komersial ?? 0) / totalJumlah) * 100).toFixed(2)
            : '0.00',
        Empowerment: totalJumlah 
            ? Number(((jumlah?.Empowerment ?? 0) / totalJumlah) * 100).toFixed(2)
            : '0.00',
    };
    const dataset =
        type === "count"
            ? [jumlah?.Empowerment ?? 0, jumlah?.komersial ?? 0] 
            : [
                  parseFloat(calculatedPersen.Empowerment), 
                  parseFloat(calculatedPersen.komersial),
              ];
    console.log(
        "Rendering chartNonKomersial with raw data - jumlah:",
        jumlah,
        "persen:",
        persen,
        "calculated dataset:",
        dataset
    );
    const container = document.getElementById("chartNonKomersialContainer");
    if (container) {
        canvas.style.display = "block";
        const existingMessage = container.querySelector(".no-data-message");
        if (existingMessage) existingMessage.remove();
    } else {
        console.warn("Container #chartNonKomersialContainer not found");
    }
    const existingChart = Chart.getChart(canvas);
    if (existingChart) existingChart.destroy();
    if (dataset[0] === 0 && dataset[1] === 0) {
        console.warn("No komersial data to display");
        if (container) {
            const message = document.createElement("div");
            message.className = "no-data-message";
            message.textContent = "Tidak ada data komersial untuk ditampilkan";
            message.style.textAlign = "center";
            message.style.color = "#2d3748";
            message.style.marginTop = "20px";
            message.style.fontFamily = '"Barlow", sans-serif';
            container.appendChild(message);
            canvas.style.display = "none";
        }
        return;
    }
    const ctx = canvas.getContext("2d");
    chartNonKomersial = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["Empowerment", "komersial"],
            datasets: [
                {
                    data: dataset,
                    backgroundColor: ["#63b3ed", "#e2e8f0"],
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            animation: false,
            cutout: "50%",
            plugins: {
                legend: {
                    display: true,
                    position: "right",
                    labels: {
                        font: {
                            size: 13,
                            weight: "500",
                            family: '"Barlow", sans-serif',
                        },
                        color: "#92BFFF",
                        padding: 10,
                        usePointStyle: true,
                        pointStyle: "circle",
                        boxWidth: 8,
                        boxHeight: 8,
                        generateLabels: function (chart) {
                            const data = chart.data;
                            return data.labels.map((label, i) => {
                                const value = type === "count" 
                                    ? data.datasets[0].data[i].toLocaleString("id-ID") 
                                    : calculatedPersen[label]; 
                                const suffix = type === "percent" ? "%" : "";
                                return {
                                    text: `${label}: ${value}${suffix}`,
                                    fillStyle:
                                        data.datasets[0].backgroundColor[i],
                                    hidden: isNaN(value),
                                    index: i,
                                    strokeStyle:
                                        data.datasets[0].backgroundColor[i],
                                    lineWidth: 0,
                                };
                            });
                        },
                    },
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || "";
                            const val = type === "count" 
                                ? context.parsed.toLocaleString("id-ID") 
                                : calculatedPersen[label]; 
                            const suffix = type === "count" ? "" : "%";
                            return `${label}: ${val}${suffix}`;
                        },
                    },
                },
                datalabels: {
                    formatter: (value, context) => {
                        if (value === 0) return "";
                        return type === "count" 
                            ? value.toLocaleString("id-ID") 
                            : calculatedPersen[context.chart.data.labels[context.dataIndex]] + "%"; 
                    },
                    color: "#2d3748",
                    font: { weight: "600", size: 12 },
                    anchor: "center",
                    align: "center",
                },
            },
        },
        plugins: [ChartDataLabels],
    });
}

let isFetchingKomersial = false;

function fetchKomersialChartData() {
    if (isFetchingKomersial) {
        console.log("Fetch komersial already in progress, skipping...");
        return;
    }
    isFetchingKomersial = true;
    console.log("Fetching komersial data...");
    const datepickerInput = document.querySelector(".filter-date");
    const dates =
        datepickerInput?.getAttribute("data-dates")?.split(" to ") || [];
    console.log(
        "Current data-dates attribute:",
        datepickerInput?.getAttribute("data-dates")
    );
    console.log("Parsed dates:", dates);
    const params = new URLSearchParams();
    if (dates.length === 2) {
        params.append("start_date", dates[0]);
        params.append("end_date", dates[1]);
        console.log("Sending date range:", `${dates[0]} to ${dates[1]}`);
    } else if (dates.length === 1 && dates[0]) {
        params.append("start_date", dates[0]);
        params.append("end_date", dates[0]);
        console.log("Sending single date:", dates[0]);
    } else {
        console.log("No date range selected, using default data (all periods)");
    }
    console.log("API params:", params.toString());
    showLoadingOverlay("chartNonKomersialContainer");
    return fetch(
        `/api/komersial-data?${params.toString()}&t=${new Date().getTime()}`,
        {
            cache: "no-store",
        }
    )
        .then((response) => {
            if (!response.ok)
                throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then((data) => {
            console.log(
                "Komersial data received with full details:",
                JSON.stringify(data, null, 2)
            );
            if (
                data &&
                data.jumlah &&
                typeof data.jumlah.komersial === "number" &&
                typeof data.jumlah.Empowerment === "number" // Sesuaikan dengan backend
            ) {
                renderChartNonKomersial(data.jumlah, data.persentase);
            } else {
                console.error("Invalid komersial data received:", data);
                renderChartNonKomersial(
                    { komersial: 0, Empowerment: 0 }, // Sesuaikan dengan backend
                    { komersial: 0, Empowerment: 0 } // Sesuaikan dengan backend
                );
            }
        })
        .catch((error) => {
            console.error("Error fetching komersial data:", error);
            renderChartNonKomersial(
                { komersial: 0, Empowerment: 0 }, // Sesuaikan dengan backend
                { komersial: 0, Empowerment: 0 } // Sesuaikan dengan backend
            );
        })
        .finally(() => {
            isFetchingKomersial = false;
            console.log("Fetch komersial completed");
            hideLoadingOverlay("chartNonKomersialContainer");
        });
}


let isFetchingBidang = false;

function updateBidangChart(data) {
    const canvas = document.getElementById("bidangChart");
    if (!canvas) {
        console.error("Canvas #bidangChart not found");
        return;
    }
    const type =
        document.querySelector('input[name="chartBidangToggle"]:checked')
            ?.value || "count";
    console.log("Bidang chart type selected:", type);
    const dataset =
        type === "count"
            ? data.jumlah
            : data.persentase.map((val) => Number(val).toFixed(2));
    console.log("Rendering bidangChart with dataset:", dataset);
    console.log("Labels received from API:", data.labels); 
    const container = document.querySelector(".card-bidang");
    if (container) {
        canvas.style.display = "block";
        const existingMessage = container.querySelector(".no-data-message");
        if (existingMessage) existingMessage.remove();
    } else {
        console.warn("Container .card-bidang not found");
    }
    const existingChart = Chart.getChart(canvas);
    if (existingChart) existingChart.destroy();
    if (!data.labels?.length || dataset.every((val) => val === 0 || val === '0.00')) {
        console.warn("No bidang data to display");
        if (container) {
            const message = document.createElement("div");
            message.className = "no-data-message";
            message.textContent = "Tidak ada data bidang untuk ditampilkan";
            message.style.textAlign = "center";
            message.style.color = "#2d3748";
            message.style.marginTop = "20px";
            message.style.fontFamily = '"Barlow", sans-serif';
            container.appendChild(message);
            canvas.style.display = "none";
        }
        return;
    }
    const ctx = canvas.getContext("2d");
    // Log warna yang diterapkan untuk setiap label
    const backgroundColors = data.labels.map((label) => {
        const color = getColorForLabel(label);
        console.log(`Label: ${label}, Assigned Color: ${color}`);
        return color;
    });
    bidangChart = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: data.labels,
            datasets: [
                {
                    data: type === "count" ? dataset : dataset.map(val => parseFloat(val)),
                    backgroundColor: backgroundColors,
                    borderWidth: 0,
                },
            ],
        },
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: true,
            cutout: "50%",
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || "";
                            const value = type === "count" 
                                ? context.parsed.toLocaleString("id-ID") 
                                : dataset[context.dataIndex];
                            const suffix = type === "count" ? "" : "%";
                            return `${label}: ${value}${suffix}`;
                        },
                    },
                },
                datalabels: {
                    formatter: (value, context) => {
                        if (value === 0) return "";
                        return type === "count" 
                            ? value.toLocaleString("id-ID") 
                            : dataset[context.dataIndex] + "%";
                    },
                    color: "#2d3748",
                    font: { weight: "600", size: 12 },
                    anchor: "center",
                    align: "center",
                },
            },
        },
        plugins: [ChartHorizontalLegend, ChartDataLabels],
    });
    const legendContainer = document.getElementById("legend-container");
    if (legendContainer) {
        const bidangLabels = legendContainer.querySelector("#bidangLabels");
        if (bidangLabels) {
            bidangLabels.innerHTML = "";
            data.labels.forEach((label, i) => {
                const value = type === "count" 
                    ? dataset[i].toLocaleString("id-ID") 
                    : dataset[i];
                const color = getColorForLabel(label);
                const suffix = type === "percent" ? "%" : "";
                const item = document.createElement("li");
                item.classList.add("legend-item");
                item.innerHTML = `
                    <div class="legend-color" style="background-color:${color}; width: 12px; height: 12px; border-radius: 50%; display: inline-block; margin-right: 10px;"></div>
                    <span class="legend-label">${label}</span>
                    <span class="legend-value">${value}${suffix}</span>
                `;
                bidangLabels.appendChild(item);
            });
        } else {
            console.warn(
                "Legend list (#bidangLabels) not found when rendering legend"
            );
        }
    } else {
        console.warn(
            "Legend container (#legend-container) not found when rendering legend"
        );
    }
}

function getColorForLabel(label) {
    const colorMap = {
        komunitas: "#3A59D1",
        dinas: "#7AC6D2",
        kampus: "#3D90D7",
        pemerintah: "#B5FCCD",
        lainnya: "#EEF1DA",
    };
    if (!label || typeof label !== "string") return "#CCCCCC";
    const cleanLabel = label.toLowerCase().trim();
    return colorMap[cleanLabel] || "#CCCCCC";
}


function fetchBidangChartData() {
    if (isFetchingBidang) return;
    isFetchingBidang = true;
    console.log("Fetching bidang data...");
    const datepickerInput = document.querySelector(".filter-date");
    const dates =
        datepickerInput?.getAttribute("data-dates")?.split(" to ") || [];
    console.log(
        "Current data-dates attribute:",
        datepickerInput?.getAttribute("data-dates")
    );
    console.log("Parsed dates:", dates);
    const params = new URLSearchParams();
    if (dates.length === 2) {
        params.append("start_date", dates[0]);
        params.append("end_date", dates[1]);
        console.log("Sending date range:", `${dates[0]} to ${dates[1]}`);
    } else if (dates.length === 1 && dates[0]) {
        params.append("start_date", dates[0]);
        params.append("end_date", dates[0]);
        console.log("Sending single date:", dates[0]);
    } else {
        console.log("No date range selected, using default data (all periods)");
    }
    console.log("API params:", params.toString());
    showLoadingOverlay("cardBidang");
    return fetch(
        `/api/bidang-data?${params.toString()}&t=${new Date().getTime()}`,
        {
            cache: "no-store",
        }
    )
        .then((response) => {
            if (!response.ok)
                throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then((data) => {
            console.log(
                "Bidang data received with full details:",
                JSON.stringify(data, null, 2)
            );
            if (data.labels && data.jumlah && data.persentase) {
                const legendContainer =
                    document.getElementById("legend-container");
                if (legendContainer) {
                    const bidangLabels =
                        legendContainer.querySelector("#bidangLabels");
                    if (bidangLabels) bidangLabels.innerHTML = "";
                }
                updateBidangChart(data);
            } else {
                console.error("Invalid bidang data received:", data);
                updateBidangChart({ labels: [], jumlah: [], persentase: [] });
            }
        })
        .catch((error) => {
            console.error("Error fetching bidang data:", error);
            updateBidangChart({ labels: [], jumlah: [], persentase: [] });
        })
        .finally(() => {
            isFetchingBidang = false;
            hideLoadingOverlay("cardBidang");
        });
}

function getColorForIndex(index) {
    const colors = [
        "#3A59D1",
        "#3D90D7",
        "#7AC6D2",
        "#B5FCCD",
        "#D5E5D5",
        "#EEF1DA",
    ];
    return colors[index % colors.length];
}

let isFetchingSubsektor = false;

function fetchSubsektorData() {
    if (isFetchingSubsektor) return;
    isFetchingSubsektor = true;
    console.log("Fetching subsektor data...");
    const datepickerInput = document.querySelector(".filter-date");
    const dates =
        datepickerInput?.getAttribute("data-dates")?.split(" to ") || [];
    console.log(
        "Current data-dates attribute:",
        datepickerInput?.getAttribute("data-dates")
    );
    console.log("Parsed dates:", dates);
    const params = new URLSearchParams();
    if (dates.length === 2) {
        params.append("start_date", dates[0]);
        params.append("end_date", dates[1]);
        console.log("Sending date range:", `${dates[0]} to ${dates[1]}`);
    } else if (dates.length === 1 && dates[0]) {
        params.append("start_date", dates[0]);
        params.append("end_date", dates[0]);
        console.log("Sending single date:", dates[0]);
    } else {
        console.log("No date range selected, using default data (all periods)");
    }
    console.log("API params:", params.toString());
    showLoadingOverlay("chartSektorContainer");
    return fetch(
        `/api/subsektor-data?${params.toString()}&t=${new Date().getTime()}`,
        {
            cache: "no-store",
        }
    )
        .then((response) => {
            if (!response.ok)
                throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then((data) => {
            console.log(
                "Subsektor data received with full details:",
                JSON.stringify(data, null, 2)
            );
            if (data.labels && data.data && data.labels.length === 17) {
                updateChartSektor(data.labels, data.data);
            } else {
                console.error(
                    "Invalid subsektor data: Expected 17 labels",
                    data
                );
                updateChartSektor(
                    [
                        "Arsitektur",
                        "Film",
                        "Fotografi",
                        "Kriya",
                        "Kuliner",
                        "Seni Rupa",
                        "Produk",
                        "Aplikasi",
                        "Game",
                        "TV & Radio",
                        "Fashion",
                        "Pertunjukan",
                        "Desain Interior",
                        "Periklanan",
                        "Penerbitan",
                        "DKV",
                        "Musik",
                    ],
                    Array(17).fill(0)
                );
            }
        })
        .catch((error) => {
            console.error("Error fetching subsektor data:", error);
            updateChartSektor(
                [
                    "Arsitektur",
                    "Film",
                    "Fotografi",
                    "Kriya",
                    "Kuliner",
                    "Seni Rupa",
                    "Produk",
                    "Aplikasi",
                    "Game",
                    "TV & Radio",
                    "Fashion",
                    "Pertunjukan",
                    "Desain Interior",
                    "Periklanan",
                    "Penerbitan",
                    "DKV",
                    "Musik",
                ],
                Array(17).fill(0)
            );
        })
        .finally(() => {
            isFetchingSubsektor = false;
            hideLoadingOverlay("chartSektorContainer");
        });
}
// Deklarasi variabel global
let dataRuanganLantai = {};
let availableFloors = [];
let chartAkumulasi;
let currentFilter = 'all';

// Fungsi untuk mengambil data akumulasi pengunjung
async function fetchAkumulasiData() {
    try {
        const datepickerInput = document.querySelector(".filter-date");
        const dates = datepickerInput?.getAttribute("data-dates")?.split(" to ") || [];
        showLoading(true);
        
        const params = new URLSearchParams();
        if (dates.length === 2) {
            params.append("start_date", dates[0]);
            params.append("end_date", dates[1]);
        } else if (dates.length === 1 && dates[0]) {
            params.append("start_date", dates[0]);
            params.append("end_date", dates[0]);
        }
        
        let fullUrl = '/api/dashboard/akumulasi-pengunjung';
        const queryString = params.toString();
        if (queryString) {
            fullUrl += `?${queryString}`;
        }
        const response = await fetch(fullUrl);
        
        if (!response.ok) {
            if (response.status === 404) {
                throw new Error('Endpoint API tidak ditemukan. Periksa konfigurasi backend.');
            }
            throw new Error(`Gagal mengambil data: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            if (Object.keys(result.data).length === 0) {
                showMessage('Tidak ada data pengunjung untuk periode ini.');
                dataRuanganLantai = {};
            } else {
                dataRuanganLantai = result.data;
            }
        } else {
            throw new Error(result.message || 'Gagal memuat data.');
        }
    } catch (error) {
        console.error('Error:', error);
        showMessage(error.message || 'Gagal memuat data. Coba lagi nanti.', 'error');
        setDummyData(); // Gunakan data dummy sebagai fallback
    } finally {
        renderAkumulasiChart(currentFilter);
        showLoading(false);
    }
}

// Fungsi untuk mengambil daftar lantai
async function fetchAvailableFloors() {
    try {
        const response = await fetch('/api/dashboard/available-floors');
        
        if (!response.ok) {
            if (response.status === 404) {
                throw new Error('Endpoint lantai tidak ditemukan');
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            availableFloors = result.data;
            updateDropdownMenu();
        } else {
            throw new Error(result.message || 'Gagal mengambil data lantai');
        }
    } catch (error) {
        console.error('Error:', error);
        showMessage('Gagal memuat daftar lantai. Menggunakan data default.', 'warning');
        
        // Default floors jika API gagal
        availableFloors = [2, 3, 4, 5, 6, 7, 8];
        updateDropdownMenu();
    }
}

// Tampilkan pesan ke user
function showMessage(message, type = 'info') {
    const messageBox = document.getElementById('message-box');
    if (!messageBox) return;
    
    messageBox.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
    
    setTimeout(() => {
        messageBox.innerHTML = '';
    }, 5000);
}

// Tampilkan/sembunyikan loading spinner
function showLoading(show) {
    const loader = document.getElementById('loading-spinner');
    if (!loader) return;
    
    loader.style.display = show ? 'block' : 'none';
}

// Fungsi untuk update dropdown menu berdasarkan lantai yang tersedia
function updateDropdownMenu() {
    const dropdownMenu = document.getElementById('dropdownMenu');
    if (!dropdownMenu) return;
    
    dropdownMenu.innerHTML = '<li onclick="filterLantai(\'all\')">General</li>';
    
    availableFloors.forEach(lantai => {
        const li = document.createElement('li');
        li.onclick = () => filterLantai(lantai.toString());
        li.textContent = `${lantai}`;
        dropdownMenu.appendChild(li);
    });
}

// Fungsi fallback untuk data dummy
function setDummyData() {
    dataRuanganLantai = {
        2: { "Main Hall": 20, "Ruang Broadcasting": 25, "Ruang Podcast": 50, "Teras Utara": 20, "Teras Selatan": 10 },
        3: { "Food Lab": 25, "Ruang Kelas": 10, "Ruang Meeting": 10, "Multipurpose Area": 7, "Multifuntion Area": 10, "Open Public Space Utara": 10, "Open Public Space Barat": 10 },
        4: { "Lab Komputer": 25, "Ruang Workshop Kriya": 10, "Ruang Workshop Seni": 0, "Ruang Studio Seni & Rekaman": 7, "Co-Working Space": 10, "Multipurpose Area": 10, "Open Public Space": 10 },
        5: { "Studio Foto": 10, "Ruang Fashion": 3, "Amphitheater 1": 10, "Amphitheater 2": 20, "Co-Working Space 1": 3, "Co-Working Space 2": 10, "Outdoor Lounge": 10 },
        6: { "Perpustakaan 1": 25, "Ruang Baca": 10, "Outdoor Lounge": 8, "Open Public Space": 10 },
        7: { "Auditorium": 28, "Ruang VIP": 10, "Open Public Space": 10 },
        8: { "Rooftop": 25 },
    };
}

// Fungsi untuk render chart akumulasi
function renderAkumulasiChart(lantaiTerpilih = "all") {
    const ctx = document.getElementById("chartAkumulasi");
    if (!ctx) {
        console.error('Canvas chartAkumulasi tidak ditemukan');
        return;
    }

    const ctxContext = ctx.getContext("2d");
    currentFilter = lantaiTerpilih; // Update filter saat ini

    let labels = [];
    let data = [];
    let backgroundColors = [];

    const warnaData = ["#1D3E85", "#4B6382", "#A4B5C4", "#A68868", "#E3C39D", "#D5E5D5", "#EEF1DA"];

    if (lantaiTerpilih === "all") {
        const sortedLantaiEntries = Object.entries(dataRuanganLantai).sort(([a], [b]) => parseInt(a) - parseInt(b));
        labels = sortedLantaiEntries.map(([lantai]) => `${lantai}`); // Menambahkan prefix "Lantai" untuk kejelasan
        data = sortedLantaiEntries.map(([, ruangan]) => Object.values(ruangan).reduce((a, b) => a + b, 0));
        backgroundColors = data.map((_, i) => (i % 2 === 0 ? "#FF5B5B" : "#F7C604"));
    } else {
        const ruangan = dataRuanganLantai[lantaiTerpilih];
        if (!ruangan) {
            console.warn(`Data untuk lantai ${lantaiTerpilih} tidak ditemukan`);
            return;
        }

        const entries = Object.entries(ruangan).sort((a, b) => b[1] - a[1]);
        labels = entries.map(([nama]) => nama);
        data = entries.map(([, jumlah]) => jumlah);
        backgroundColors = entries.map((_, index) => warnaData[index] || warnaData[warnaData.length - 1]);
        ctx.height = Math.min(350, 200 + labels.length * 20);
    }

    if (chartAkumulasi) {
        chartAkumulasi.destroy();
    }

    const maxDataValue = data.length > 0 ? Math.max(...data) : 0;
    const paddingPercentage = 0.15;
    const maxYValue = maxDataValue + (maxDataValue * paddingPercentage);

    const handleChartNavigation = (clickedIndex) => {
        const datepickerInput = document.querySelector(".filter-date");
        const dateString = datepickerInput?.getAttribute("data-dates") || "";
        const dates = dateString.split(" to ");

        let queryString = "";
        const params = new URLSearchParams();

        if (dates.length === 2) {
            params.append("start_date", dates[0]);
            params.append("end_date", dates[1]);
        } else if (dates.length === 1 && dates[0]) {
            params.append("start_date", dates[0]);
            params.append("end_date", dates[0]);
        }

        if (params.toString()) {
            queryString = `?${params.toString()}`;
        }

        if (lantaiTerpilih === "all") {
            let lantaiLabel = labels[clickedIndex];
            let lantai = lantaiLabel.replace();
            window.open(`/lantai/${encodeURIComponent(lantai)}${queryString}`, '_blank');
        } else {
            let ruangan = labels[clickedIndex];
            window.open(`/lantai/${encodeURIComponent(lantaiTerpilih)}/ruangan/${encodeURIComponent(ruangan)}${queryString}`, '_blank');
        }
    };

    chartAkumulasi = new Chart(ctxContext, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: backgroundColors,
                borderColor: backgroundColors,
                borderWidth: 1,
                barThickness: 30,
            }],
        },
        options: {
            // Event klik utama untuk bar
            onClick: (event, elements) => {
                if (elements.length > 0) {
                    handleChartNavigation(elements[0].index);
                }
            },
            
            // Logika hover disederhanakan untuk menghindari masalah performa
            onHover: (event, chartElement) => {
                const canvas = event.native.target;
                canvas.style.cursor = chartElement.length > 0 ? 'pointer' : 'default';
            },

            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { top: 30, bottom: 10 } },
            plugins: {
                legend: { display: false },
                datalabels: {
                    anchor: "end",
                    align: "top",
                    offset: 5,
                    color: "#444",
                    font: { weight: "bold", size: 12 },
                    formatter: (value) => value.toLocaleString('id-ID'),
                    
                    // Listener untuk label angka di atas bar
                    listeners: {
                        // Label tetap bisa diklik
                        click: (context) => {
                            handleChartNavigation(context.dataIndex);
                        },

                        enter: (context) => {
                            context.chart.canvas.style.cursor = 'pointer';
                        },
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: maxYValue,
                    ticks: {
                        stepSize: Math.ceil(maxYValue / 10),
                        callback: (value) => value.toLocaleString('id-ID'),
                    },
                    grid: { display: true, color: '#e0e0e0' },
                },
                x: { grid: { display: false } },
            },
            animation: { duration: 1000, easing: 'easeOutQuart' }
        },
        plugins: [ChartDataLabels],
    });
}

// Fungsi dropdown filter
function filterLantai(lantai) {
    renderAkumulasiChart(lantai);

    const btn = document.querySelector(".dropdown-toggle");
    if (btn) {
        btn.innerHTML = (lantai === "all" ? "Pilih Lantai" : `${lantai}`) + ' <span class="arrow">&#9662;</span>';
    }

    const dropdownMenu = document.getElementById("dropdownMenu");
    if (dropdownMenu) {
        dropdownMenu.classList.remove("show");
    }
}

// Fungsi toggle dropdown
function toggleDropdown() {
    const dropdownMenu = document.getElementById("dropdownMenu");
    if (dropdownMenu) {
        dropdownMenu.classList.toggle("show");
    }
}


// Event listener untuk menutup dropdown saat klik di luar
window.addEventListener("click", function (e) {
    const menu = document.getElementById("dropdownMenu");
    const btn = document.querySelector(".dropdown-toggle");
    if (menu && btn && !menu.contains(e.target) && !btn.contains(e.target)) {
        menu.classList.remove("show");
    }
});

// === INISIALISASI UTAMA APLIKASI ===
document.addEventListener('DOMContentLoaded', function() {
    // 1. Dapatkan tanggal hari ini dalam format YYYY-MM-DD
    const today = new Date().toISOString().slice(0, 10);

    // 2. Temukan elemen filter dan atur nilainya ke hari ini
    const datepickerElement = document.querySelector(".filter-date");
    if (datepickerElement) {
        // Atur atribut 'data-dates' yang akan dibaca oleh fungsi fetch
        datepickerElement.setAttribute("data-dates", today);
        // Atur teks yang terlihat oleh pengguna
        datepickerElement.textContent = today; 
    } else {
        console.error("Elemen .filter-date tidak ditemukan, tidak bisa set tanggal default.");
    }
    
    // 3. Panggil fungsi fetch SETELAH tanggal default diatur
    fetchAkumulasiData();
    fetchAvailableFloors();
});

function updateChartSektor(labels, data) {
    console.log("Updating chartSektor with:", { labels, data });
    const canvas = document.getElementById("chartSektor");
    if (!canvas) {
        console.error("Canvas #chartSektor not found");
        return;
    }

    const blueColors = [
        "#1E3A8A",
        "#1E40AF",
        "#1D4ED8",
        "#2563EB",
        "#3B82F6",
        "#60A5FA",
        "#93C5FD",
        "#312E81",
        "#3730A3",
        "#4F46E5",
        "#6366F1",
        "#06B6D4",
        "#14B8A6",
        "#22D3EE",
        "#2DD4BF",
        "#BFDBFE",
        "#DBEAFE",
    ];

    const indexedData = data.map((value, index) => ({ index, value }));

    indexedData.sort((a, b) => b.value - a.value);

    const sektorBackgroundColors = new Array(data.length).fill("#DBEAFE");
    indexedData.forEach(({ index }, i) => {
        sektorBackgroundColors[index] =
            blueColors[i] || blueColors[blueColors.length - 1];
    });

    console.log("Assigned colors:", sektorBackgroundColors);

    const existingChart = Chart.getChart(canvas);
    if (existingChart) existingChart.destroy();

    const maxDataValue = Math.max(...data, 0);
    const maxYValue =
        maxDataValue === 0 ? 100 : Math.ceil((maxDataValue * 1.1) / 50) * 50;
    const yTickStep = maxYValue <= 100 ? 20 : Math.ceil(maxYValue / 5);

    console.log("Calculated Y-axis settings:", { maxYValue, yTickStep });

    const numberOfBars = labels.length;
    const canvasWidth = canvas.parentElement.clientWidth || 600;
    const barThickness = Math.max(
        12,
        Math.floor(canvasWidth / numberOfBars / 1.5)
    );
    console.log(
        "Calculated barThickness:",
        barThickness,
        "based on canvas width:",
        canvasWidth
    );
    chartSektor = new Chart(canvas, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Jumlah",
                    data: data,
                    backgroundColor: sektorBackgroundColors,
                    barThickness: barThickness,
                    barPercentage: 0.8,
                },
            ],
        },
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const value = context.parsed.y;
                            return `${context.label}: ${value.toLocaleString("id-ID")}`;
                        },
                    },
                },
                datalabels: {
                    anchor: "end",
                    align: "end",
                    offset: 8,
                    formatter: (value) => {
                        return value > 0 ? value.toLocaleString("id-ID") : "";
                    },
                    color: "#2d3748",
                    font: {
                        weight: "600",
                        size: window.innerWidth <= 480 ? 10 : 12,
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: maxYValue,
                    ticks: {
                        stepSize: yTickStep,
                        callback: function (value) {
                            return value.toLocaleString("id-ID");
                        },
                        font: { size: window.innerWidth <= 480 ? 10 : 12 },
                        padding: 10,
                    },
                    grid: { drawBorder: false, color: "#edf2f7" },
                },
                x: {
                    ticks: {
                        font: { size: window.innerWidth <= 480 ? 8 : 10 },
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 45,
                        padding: 8,
                    },
                    grid: { display: false },
                },
            },
        },
        plugins: [ChartDataLabels],
    });
}

let isFetchingBooking = false;

function fetchBookingData() {
    if (isFetchingBooking) return;
    isFetchingBooking = true;
    console.log("Fetching booking data...");
    const datepickerInput = document.querySelector(".filter-date");
    const dates =
        datepickerInput?.getAttribute("data-dates")?.split(" to ") || [];
    console.log(
        "Current data-dates attribute:",
        datepickerInput?.getAttribute("data-dates")
    );
    console.log("Parsed dates:", dates);
    const params = new URLSearchParams();
    if (dates.length === 2) {
        params.append("start_date", dates[0]);
        params.append("end_date", dates[1]);
        console.log("Sending date range:", `${dates[0]} to ${dates[1]}`);
    } else if (dates.length === 1 && dates[0]) {
        params.append("start_date", dates[0]);
        params.append("end_date", dates[0]);
        console.log("Sending single date:", dates[0]);
    } else {
        console.log("No date range selected, using default data (all periods)");
    }
    console.log("API params:", params.toString());
    showFullPageLoadingOverlay();
    return fetch(
        `/api/fetch-and-store-booking-data?${params.toString()}&t=${new Date().getTime()}`,
        {
            cache: "no-store",
        }
    )
        .then((response) => {
            if (!response.ok)
                throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then((data) => {
            console.log(
                "Booking data received with full details:",
                JSON.stringify(data, null, 2)
            );
            if (data.message) {
                updateDashboard(data);
            } else {
                console.error("Error fetching booking data:", data.error);
            }
        })
        .catch((error) => {
            console.error("Error fetching booking data:", error);
        })
        .finally(() => {
            isFetchingBooking = false;
            hideFullPageLoadingOverlay();
        });
}

function formatNumber(number) {
    return Number(number).toLocaleString("id-ID");
}

function updateDashboard(data) {
    const totalAcaraElement = document.getElementById("totalAcara");
    const totalPengunjungElement = document.getElementById("totalPengunjung");
    if (totalAcaraElement && data.total_acara_keseluruhan !== undefined) {
        totalAcaraElement.innerText = formatNumber(
            data.total_acara_keseluruhan
        );
    } else if (totalAcaraElement) {
        totalAcaraElement.innerText = "Data Tidak Tersedia";
    }
    if (
        totalPengunjungElement &&
        data.total_pengunjung_keseluruhan !== undefined
    ) {
        totalPengunjungElement.innerText = formatNumber(
            data.total_pengunjung_keseluruhan
        );
    } else if (totalPengunjungElement) {
        totalPengunjungElement.innerText = "Data Tidak Tersedia";
    }
}

let isFetchingSDGs = false;

function fetchSDGsData() {
    if (isFetchingSDGs) return;
    isFetchingSDGs = true;
    console.log("Fetching SDGs data...");
    const datepickerInput = document.querySelector(".filter-date");
    const dates =
        datepickerInput?.getAttribute("data-dates")?.split(" to ") || [];
    console.log(
        "Current data-dates attribute:",
        datepickerInput?.getAttribute("data-dates")
    );
    console.log("Parsed dates:", dates);
    const params = new URLSearchParams();
    if (dates.length === 2) {
        params.append("start_date", dates[0]);
        params.append("end_date", dates[1]);
        console.log("Sending date range:", `${dates[0]} to ${dates[1]}`);
    } else if (dates.length === 1 && dates[0]) {
        params.append("start_date", dates[0]);
        params.append("end_date", dates[0]);
        console.log("Sending single date:", dates[0]);
    } else {
        console.log(
            "No date range selected, fetching all SDGs data (all periods)"
        );
    }
    console.log("API params:", params.toString());
    showLoadingOverlay("sdgsGridContainer");
    return fetch(
        `/api/sdgs-data?${params.toString()}&t=${new Date().getTime()}`,
        {
            cache: "no-store",
        }
    )
        .then((response) => {
            if (!response.ok)
                throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then((data) => {
            console.log(
                "SDGs data received with full details:",
                JSON.stringify(data, null, 2)
            );
            if (data.success && data.data) {
                updateSDGsGrid(data.data, dates);
            } else {
                console.error("Invalid SDGs data received:", data);
                updateSDGsGrid([], dates);
            }
        })
        .catch((error) => {
            console.error("Error fetching SDGs data:", error);
            updateSDGsGrid([], dates);
        })
        .finally(() => {
            isFetchingSDGs = false;
            hideLoadingOverlay("sdgsGridContainer");
        });
}

function updateSDGsGrid(sdgsData, dates) {
    const sdgsGrid = document.querySelector(".sdgs-grid");
    if (!sdgsGrid) {
        console.error("SDGs grid not found");
        return;
    }
    const type =
        document.querySelector('input[name="sdgsToggle"]:checked')?.value ||
        "count";
    sdgsGrid.innerHTML = "";

    const validSdgs = sdgsData.filter((sdg) => sdg.count > 0);

    if (validSdgs.length === 0) {
        console.warn("Tidak ada data terkait sdgs");
        const message = document.createElement("div");
        message.textContent = "Tidak ada data SDGs untuk ditampilkan";
        message.style.textAlign = "center";
        message.style.color = "#2d3748";
        message.style.marginTop = "20px";
        message.style.fontFamily = '"Barlow", sans-serif';
        sdgsGrid.appendChild(message);
        return;
    }

    validSdgs.forEach((sdg) => {
        console.log("Rendering SDG:", {
            id: sdg.id,
            image: sdg.image,
            count: sdg.count,
            percentage: sdg.percentage,
        });
        const startDate = dates.length >= 1 ? dates[0] : "";
        const endDate = dates.length === 2 ? dates[1] : startDate || "";
        const queryParams =
            startDate && endDate
                ? `?start_date=${startDate}&end_date=${endDate}`
                : "";
        const sdgLink = document.createElement("a");
        sdgLink.href = `/detail-dashboard/${sdg.id}${queryParams}`;
        sdgLink.target = "_blank";
        sdgLink.rel = "noopener noreferrer";
        sdgLink.style.textDecoration = "none";
        
        const sdgCard = document.createElement("div");
        sdgCard.classList.add("sdgs-card");
        sdgCard.style.textDecoration = "none";
        sdgCard.innerHTML = `
            <img class="sdgs-img" src="${sdg.image}" alt="${sdg.id}" onerror="this.src='/logo_sdg/default.png'; console.warn('Failed to load SDG image: ${sdg.image}');">
            <span class="sdgs-value" style="text-decoration: none; border-bottom: none;">${
                type === "count"
                    ? formatNumber(sdg.count)
                    : Number(sdg.percentage).toFixed(2) + "%"
            }</span>
        `;
        sdgLink.appendChild(sdgCard);
        sdgsGrid.appendChild(sdgLink);
    });
}

let isFetchingTotalChart = false;

function fetchTotalChartData() {
    if (isFetchingTotalChart) return;
    isFetchingTotalChart = true;
    console.log("Fetching total chart data...");
    const datepickerInput = document.querySelector(".filter-date");
    const dates =
        datepickerInput?.getAttribute("data-dates")?.split(" to ") || [];
    console.log(
        "Current data-dates attribute:",
        datepickerInput?.getAttribute("data-dates")
    );
    console.log("Parsed dates:", dates);
    const params = new URLSearchParams();
    if (dates.length === 2) {
        params.append("start_date", dates[0]);
        params.append("end_date", dates[1]);
        console.log("Sending date range:", `${dates[0]} to ${dates[1]}`);
    } else if (dates.length === 1 && dates[0]) {
        params.append("start_date", dates[0]);
        params.append("end_date", dates[0]);
        console.log("Sending single date:", dates[0]);
    } else {
        console.log("No date range selected, using default data (all periods)");
    }
    console.log("API params:", params.toString());
    showLoadingOverlay("chartTotalContainer");
    return fetch(
        `/api/total-event-pengunjung?${params.toString()}&t=${new Date().getTime()}`,
        {
            cache: "no-store",
        }
    )
        .then((response) => {
            if (!response.ok)
                throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then((data) => {
            console.log(
                "Total chart data received with full details:",
                JSON.stringify(data, null, 2)
            );
            if (data.labels && data.acara && data.pengunjung) {
                updateTotalChart(data.labels, data.acara, data.pengunjung);
            } else {
                console.error("Invalid total chart data received");
                updateTotalChart(
                    [
                        "Jan",
                        "Feb",
                        "Mar",
                        "Apr",
                        "May",
                        "Jun",
                        "Jul",
                        "Aug",
                        "Sep",
                        "Oct",
                        "Nov",
                        "Dec",
                    ],
                    Array(12).fill(0),
                    Array(12).fill(0)
                );
            }
        })
        .catch((error) => {
            console.error("Error fetching total chart data:", error);
            updateTotalChart(
                [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "May",
                    "Jun",
                    "Jul",
                    "Aug",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dec",
                ],
                Array(12).fill(0),
                Array(12).fill(0)
            );
        })
        .finally(() => {
            isFetchingTotalChart = false;
            hideLoadingOverlay("chartTotalContainer");
        });
}

function updateTotalChart(labels, acaraData, pengunjungData) {
    const totalCanvas = document.getElementById("chartTotal");
    if (!totalCanvas) {
        console.error("Canvas #chartTotal not found");
        return;
    }
    const existingChart = Chart.getChart(totalCanvas);
    if (existingChart) existingChart.destroy();

    const maxAcara = Math.max(...acaraData, 0);
    const maxPengunjung = Math.max(...pengunjungData, 0);
    const maxDataValue = Math.max(maxAcara, maxPengunjung);
    const maxYValue =
        maxDataValue === 0 ? 100 : Math.ceil((maxDataValue * 1.15) / 500) * 500;
    const yTickStep = maxYValue <= 100 ? 20 : Math.ceil(maxYValue / 5);

    console.log("Calculated Y-axis settings:", {
        maxDataValue,
        maxYValue,
        yTickStep,
    });

    chartTotal = new Chart(totalCanvas, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Acara",
                    data: acaraData,
                    borderColor: "#3182ce",
                    backgroundColor: "#3182ce",
                    tension: 0.4,
                    fill: false,
                    pointRadius: 0,
                    datalabels: {
                        align: "top",
                        offset: 7,
                        formatter: (value) =>
                            value > 0 ? value.toLocaleString("id-ID") : "",
                        font: {
                            weight: "600",
                            size: window.innerWidth <= 480 ? 10 : 12,
                            family: '"Barlow", sans-serif',
                        },
                        color: "#2d3748",
                    },
                },
                {
                    label: "Pengunjung",
                    data: pengunjungData,
                    borderColor: "#ed8936",
                    backgroundColor: "#ed8936",
                    tension: 0.4,
                    fill: false,
                    pointRadius: 0,
                    datalabels: {
                        align: "top",
                        offset: 7,
                        formatter: (value) =>
                            value > 0 ? value.toLocaleString("id-ID") : "",
                        font: {
                            weight: "600",
                            size: window.innerWidth <= 480 ? 10 : 12,
                            family: '"Barlow", sans-serif',
                        },
                        color: "#2d3748",
                    },
                },
            ],
        },
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "top",
                    align: "end",
                    labels: {
                        usePointStyle: true,
                        pointStyle: "circle",
                        font: {
                            size: window.innerWidth <= 480 ? 10 : 12,
                            family: '"Barlow", sans-serif',
                        },
                    },
                },
                annotation: {
                    annotations: {},
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.dataset.label || "";
                            const value = context.parsed.y;
                            return `${label}: ${value.toLocaleString("id-ID")}`;
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: maxYValue,
                    ticks: {
                        stepSize: yTickStep,
                        font: {
                            size: window.innerWidth <= 480 ? 10 : 12,
                            family: '"Barlow", sans-serif',
                        },
                        callback: function (value) {
                            return value.toLocaleString("id-ID");
                        },
                    },
                    grid: { display: false },
                },
                x: {
                    grid: { drawBorder: false, color: "#edf2f7" },
                    ticks: {
                        font: {
                            size: window.innerWidth <= 480 ? 10 : 12,
                            family: '"Barlow", sans-serif',
                        },
                    },
                },
            },
        },
        plugins: [ChartDataLabels],
    });
}

let isFetchingTop3SDGs = false;

function fetchTop3SDGsData() {
    if (isFetchingTop3SDGs) {
        console.log("Fetch Top 3 SDGs already in progress, skipping...");
        return;
    }
    isFetchingTop3SDGs = true;
    console.log("Fetching Top 3 SDGs data...");

    const datepickerInput = document.querySelector(".filter-date");
    let datesAttr = datepickerInput?.getAttribute("data-dates")?.trim() || "";
    let dates = datesAttr
        ? datesAttr.split(" to ").map((date) => date.trim())
        : [];

    console.log("Current data-dates attribute:", datesAttr);
    console.log("Parsed dates:", dates);

    const params = new URLSearchParams();

    if (dates.length === 2) {
        params.append("start_date", dates[0]);
        params.append("end_date", dates[1]);
        console.log("Sending date range:", `${dates[0]} to ${dates[1]}`);
    } else if (dates.length === 1 && dates[0]) {
        params.append("start_date", dates[0]);
        params.append("end_date", dates[0]);
        console.log("Sending single date:", dates[0]);
    } else {
        params.append("all", "true");
        console.log("Sending all data (all periods)");
    }

    console.log("API params:", params.toString());
    showLoadingOverlay("top3ChartContainer");

    fetch(`/api/top3-sdgs?${params.toString()}&t=${new Date().getTime()}`, {
        cache: "no-store",
    })
        .then((response) => {
            if (!response.ok) {
                return response.text().then((text) => {
                    throw new Error(
                        `HTTP error! Status: ${response.status}, Text: ${text}`
                    );
                });
            }
            return response.json();
        })
        .then((data) => {
            console.log(
                "Top 3 SDGs data received with full details:",
                JSON.stringify(data, null, 2)
            );
            if (data.success && data.data?.top3?.length > 0) {
                updateTop3Chart(data.data);
            } else {
                console.warn("No valid Top 3 SDGs data received:", data);
                updateTop3Chart({
                    top3: [
                        {
                            id: "1",
                            name: "Tidak Ada Data",
                            count: 0,
                            percentage: 0,
                        },
                        {
                            id: "2",
                            name: "Tidak Ada Data",
                            count: 0,
                            percentage: 0,
                        },
                        {
                            id: "3",
                            name: "Tidak Ada Data",
                            count: 0,
                            percentage: 0,
                        },
                    ],
                    others: [],
                });
            }
        })
        .catch((error) => {
            console.error("Error fetching Top 3 SDGs data:", error.message);
            updateTop3Chart({
                top3: [
                    {
                        id: "1",
                        name: "Tidak Ada Data",
                        count: 0,
                        percentage: 0,
                    },
                    {
                        id: "2",
                        name: "Tidak Ada Data",
                        count: 0,
                        percentage: 0,
                    },
                    {
                        id: "3",
                        name: "Tidak Ada Data",
                        count: 0,
                        percentage: 0,
                    },
                ],
                others: [],
            });
        })
        .finally(() => {
            isFetchingTop3SDGs = false;
            hideLoadingOverlay("top3ChartContainer");
        });
}

function updateTop3Chart(top3Data) {
    const top3Canvas = document.getElementById("top3Chart");
    if (!top3Canvas) {
        console.error("Canvas #top3Chart not found");
        return;
    }
    const existingChart = Chart.getChart(top3Canvas);
    if (existingChart) existingChart.destroy();
    const top3 = Array.isArray(top3Data.top3)
        ? top3Data.top3
        : [
              { id: "1", name: "Tidak Ada Data", count: 0, percentage: 0 },
              { id: "2", name: "Tidak Ada Data", count: 0, percentage: 0 },
              { id: "3", name: "Tidak Ada Data", count: 0, percentage: 0 },
          ];
    const labels = top3.map((item) => {
        const name = item.name || "Unknown";
        if (name.length > 10) {
            const words = name.split(" ");
            let lines = [];
            let currentLine = "";
            for (const word of words) {
                if ((currentLine + word).length > 10) {
                    lines.push(currentLine.trim());
                    currentLine = word + " ";
                } else {
                    currentLine += word + " ";
                }
            }
            if (currentLine) lines.push(currentLine.trim());
            return lines;
        }
        return name;
    });
    top3Chart = new Chart(top3Canvas, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    data: top3.map((item) => item.percentage),
                    backgroundColor: ["#1a365d", "#2b6cb0", "#90cdf4"],
                    barThickness: window.innerWidth <= 480 ? 20 : 25,
                },
            ],
        },
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => `${Number(context.parsed.y).toFixed(2)}%`,
                    },
                },
                datalabels: {
                    anchor: "end",
                    align: "end",
                    formatter: (value) => `${Number(value).toFixed(2)}%`,
                    font: {
                        weight: "600",
                        size: window.innerWidth <= 480 ? 10 : 12,
                    },
                    color: "#2d3748",
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        stepSize: 20,
                        font: { size: window.innerWidth <= 480 ? 10 : 12 },
                        callback: (value) => `${value}%`,
                    },
                    grid: {
                        display: true,
                        drawBorder: false,
                        color: "#edf2f7",
                    },
                },
                x: {
                    ticks: {
                        font: { size: window.innerWidth <= 480 ? 10 : 12 },
                        maxRotation: 45,
                        minRotation: 45,
                    },
                    grid: { display: false },
                },
            },
        },
        plugins: [ChartDataLabels],
    });
    const otherSdgsList = document.getElementById("otherSdgsList");
    if (otherSdgsList) {
        otherSdgsList.innerHTML = "";
        const others = Array.isArray(top3Data.others) ? top3Data.others : [];
        if (others.length === 0) {
            const message = document.createElement("li");
            message.textContent = "Tidak ada SDG lainnya untuk ditampilkan";
            message.style.textAlign = "center";
            message.style.color = "#2d3748";
            message.style.fontFamily = '"Barlow", sans-serif';
            message.style.padding = "10px";
            otherSdgsList.appendChild(message);
        } else {
            otherSdgsList.style.padding = "10px";
            otherSdgsList.style.backgroundColor = "#f7fafc";
            otherSdgsList.style.borderRadius = "8px";
            otherSdgsList.style.boxShadow = "0 2px 4px rgba(0, 0, 0, 0.1)";
            otherSdgsList.style.listStyleType = "none";
            const colors = [
                "#ed8936",
                "#f6e05e",
                "#48bb78",
                "#a0aec0",
                "#ecc94b",
                "#9f7aea",
                "#ed64a6",
                "#63b3ed",
                "#4299e1",
                "#2b6cb0",
            ];
            others.forEach((sdg, index) => {
                const li = document.createElement("li");
                li.style.display = "flex";
                li.style.alignItems = "center";
                li.style.padding = "8px 10px";
                li.style.borderBottom =
                    index < others.length - 1 ? "1px solid #e2e8f0" : "none";
                li.style.fontFamily = '"Barlow", sans-serif';
                const color = colors[index % colors.length];
                const colorSpan = document.createElement("span");
                colorSpan.className = "sdg-color";
                colorSpan.style.backgroundColor = color;
                colorSpan.style.width = "12px";
                colorSpan.style.height = "12px";
                colorSpan.style.borderRadius = "50%";
                colorSpan.style.display = "inline-block";
                colorSpan.style.marginRight = "10px";
                const nameSpan = document.createElement("span");
                nameSpan.textContent = sdg.name;
                nameSpan.style.color = "#2d3748";
                nameSpan.style.fontWeight = "500";
                nameSpan.style.flex = "1";
                const dataSpan = document.createElement("span");
                dataSpan.style.display = "flex";
                dataSpan.style.gap = "10px";
                dataSpan.style.alignItems = "center";
                const countSpan = document.createElement("span");
                countSpan.textContent = sdg.count.toLocaleString("id-ID");
                countSpan.style.color = sdg.count > 0 ? "#3182ce" : "#a0aec0";
                countSpan.style.fontWeight = "600";
                const percentageSpan = document.createElement("span");
                percentageSpan.textContent = `(${Number(sdg.percentage).toFixed(2)}%)`;
                percentageSpan.style.color =
                    sdg.percentage > 0 ? "#38a169" : "#a0aec0";
                percentageSpan.style.fontWeight = "600";
                dataSpan.appendChild(countSpan);
                dataSpan.appendChild(percentageSpan);
                li.appendChild(colorSpan);
                li.appendChild(nameSpan);
                li.appendChild(dataSpan);
                otherSdgsList.appendChild(li);
            });
        }
    } else {
        console.warn("Element #otherSdgsList not found");
    }
}

function initializeTop3Chart() {
    const top3Canvas = document.getElementById("top3Chart");
    if (!top3Canvas) {
        console.error("Canvas #top3Chart not found");
        return;
    }
    const existingChart = Chart.getChart(top3Canvas);
    if (existingChart) existingChart.destroy();
    updateTop3Chart({
        top3: [
            { id: "1", name: "Loading...", count: 0, percentage: 0 },
            { id: "2", name: "Loading...", count: 0, percentage: 0 },
            { id: "3", name: "Loading...", count: 0, percentage: 0 },
        ],
        others: [],
    });
    fetchTop3SDGsData();
}

function initializeDatepicker() {
    const filterElement = document.querySelector(".filter");
    const filterDate = document.querySelector(".filter-date");
    if (!filterElement || !filterDate) {
        console.error("Filter or filter-date elements not found");
        return;
    }
    let datepickerInput = document.querySelector(".filter-datepicker");
    if (!datepickerInput) {
        datepickerInput = document.createElement("input");
        datepickerInput.className = "filter-datepicker";
        datepickerInput.style.opacity = "0";
        datepickerInput.style.position = "absolute";
        datepickerInput.style.width = "0";
        datepickerInput.style.height = "0";
        filterElement.appendChild(datepickerInput);
    }
    try {
        console.log("Initializing Flatpickr on filter-datepicker");
        const today = new Date().toISOString().split("T")[0];
        const fpInstance = flatpickr(datepickerInput, {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInvalidPreload: true,
            defaultDate: [today],
            onChange: function (selectedDates, dateStr, instance) {
                console.log(
                    "Flatpickr onChange triggered. Selected dates:",
                    selectedDates,
                    "Date string:",
                    dateStr
                );
                filterDate.textContent = dateStr ? dateStr : "Semua Periode";
                filterDate.setAttribute("data-dates", dateStr || "");
                console.log(
                    "Updated data-dates attribute:",
                    filterDate.getAttribute("data-dates")
                );
            },
            onClose: function (selectedDates, dateStr, instance) {
                console.log(
                    "Flatpickr closed. Final selected dates:",
                    selectedDates,
                    "Date string:",
                    dateStr
                );
            },
            position: "below",
            appendTo: filterElement,
            static: true,
        });
        filterDate.textContent = today;
        filterDate.setAttribute("data-dates", today);
        console.log(
            "Initial data-dates attribute set to today:",
            filterDate.getAttribute("data-dates")
        );
        filterElement.addEventListener(
            "click",
            () => {
                console.log("Filter element clicked");
                if (fpInstance && typeof fpInstance.open === "function")
                    fpInstance.open();
                else
                    console.warn(
                        "Flatpickr instance not initialized or open not available"
                    );
            },
            { capture: true }
        );
        document.getElementById("confirmDate").addEventListener("click", () => {
            const selectedDates = filterDate.getAttribute("data-dates");
            console.log("Confirm clicked. Current data-dates:", selectedDates);
            if (chartNonKomersial) chartNonKomersial.destroy();
            if (bidangChart) bidangChart.destroy();
            if (chartSektor) chartSektor.destroy();
            if (chartTotal) chartTotal.destroy();
            if (top3Chart) top3Chart.destroy();
            if (chartAkumulasi) chartAkumulasi.destroy();
            const sdgsGrid = document.querySelector(".sdgs-grid");
            if (sdgsGrid) sdgsGrid.innerHTML = "";
            const totalAcaraElement = document.getElementById("totalAcara");
            const totalPengunjungElement =
                document.getElementById("totalPengunjung");
            if (totalAcaraElement) totalAcaraElement.innerText = "";
            if (totalPengunjungElement) totalPengunjungElement.innerText = "";
            showFullPageLoadingOverlay();
            Promise.all([
                fetchBookingData(),
                fetchKomersialChartData(),
                fetchBidangChartData(),
                fetchSubsektorData(),
                fetchSDGsData(),
                fetchTotalChartData(),
                fetchTop3SDGsData(),
                fetchAkumulasiData(),

            ])
                .then(() => hideFullPageLoadingOverlay())
                .catch((error) => {
                    console.error("Error fetching data:", error);
                    hideFullPageLoadingOverlay();
                });
        });
        document.getElementById("resetDate").addEventListener("click", () => {
            console.log("Reset date clicked");
            fpInstance.clear();
            filterDate.textContent = "Semua Periode";
            filterDate.setAttribute("data-dates", "");
            console.log(
                "Reset data-dates attribute:",
                filterDate.getAttribute("data-dates")
            );
            if (chartNonKomersial) chartNonKomersial.destroy();
            if (bidangChart) bidangChart.destroy();
            if (chartSektor) chartSektor.destroy();
            if (chartTotal) chartTotal.destroy();
            if (top3Chart) top3Chart.destroy();
            if (chartAkumulasi) chartAkumulasi.destroy();
            const sdgsGrid = document.querySelector(".sdgs-grid");
            if (sdgsGrid) sdgsGrid.innerHTML = "";
            const totalAcaraElement = document.getElementById("totalAcara");
            const totalPengunjungElement =
                document.getElementById("totalPengunjung");
            if (totalAcaraElement) totalAcaraElement.innerText = "";
            if (totalPengunjungElement) totalPengunjungElement.innerText = "";
            showFullPageLoadingOverlay();
            Promise.all([
                fetchBookingData(),
                fetchKomersialChartData(),
                fetchBidangChartData(),
                fetchSubsektorData(),
                fetchSDGsData(),
                fetchTotalChartData(),
                fetchTop3SDGsData(),
                fetchAkumulasiData(),
            ])
                .then(() => hideFullPageLoadingOverlay())
                .catch((error) => {
                    console.error("Error fetching data after reset:", error);
                    hideFullPageLoadingOverlay();
                });
        });
    } catch (error) {
        console.error("Error initializing Flatpickr:", error);
    }
}

function updateClock() {
    const clockElement = document.querySelector(".header-clock");
    if (clockElement) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, "0");
        const minutes = String(now.getMinutes()).padStart(2, "0");
        const seconds = String(now.getSeconds()).padStart(2, "0");
        clockElement.textContent = `${hours}:${minutes}:${seconds}`;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    console.log("Dashboard.js loaded");
    try {
        initializeTop3Chart();
        initializeDatepicker();
        showFullPageLoadingOverlay();
        Promise.all([
            fetchKomersialChartData(),
            fetchBidangChartData(),
            fetchSubsektorData(),
            fetchBookingData(),
            fetchSDGsData(),
            fetchTotalChartData(),
            fetchTop3SDGsData(),
            fetchAkumulasiData(),
        ])
            .then(() => hideFullPageLoadingOverlay())
            .catch((error) => {
                console.error("Error fetching initial data:", error);
                hideFullPageLoadingOverlay();
            });
        let toggleTimeout = null;
        document
            .querySelectorAll('input[name="chartKomersialToggle"]')
            .forEach((input) => {
                input.addEventListener("change", () => {
                    console.log("Komersial toggle changed to:", input.value);
                    if (toggleTimeout) clearTimeout(toggleTimeout);
                    toggleTimeout = setTimeout(() => {
                        fetchKomersialChartData();
                        toggleTimeout = null;
                    }, 300);
                });
            });
        document
            .querySelectorAll('input[name="sdgsToggle"]')
            .forEach((input) => {
                input.addEventListener("change", () => {
                    console.log("SDGs toggle changed to:", input.value);
                    if (toggleTimeout) clearTimeout(toggleTimeout);
                    toggleTimeout = setTimeout(() => {
                        fetchSDGsData();
                        toggleTimeout = null;
                    }, 300);
                });
            });
        document
            .querySelectorAll('input[name="chartBidangToggle"]')
            .forEach((input) => {
                input.addEventListener("change", () => {
                    console.log("Bidang toggle changed to:", input.value);
                    if (toggleTimeout) clearTimeout(toggleTimeout);
                    toggleTimeout = setTimeout(() => {
                        fetchBidangChartData();
                        toggleTimeout = null;
                    }, 300);
                });
            });
        updateClock();
        setInterval(updateClock, 1000);
        let resizeTimeout;
        window.addEventListener("resize", () => {
            if (resizeTimeout) clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                if (top3Chart) top3Chart.resize();
                if (chartTotal) chartTotal.resize();
                if (chartSektor) chartSektor.resize();
                if (chartNonKomersial) chartNonKomersial.resize();
                if (bidangChart) bidangChart.resize();
            }, 300);
        });
    } catch (error) {
        console.error("Error initializing charts:", error);
    }
});