const sdgScores = {};

const sdgMapping = {
    // Pertanyaan 1
    q1_answer1: ["SDG04"],
    q1_answer2: ["SDG02", "SDG08", "SDG12"],
    q1_answer3: ["SDG02", "SDG03", "SDG04"],
    q1_answer4: ["SDG01", "SDG02", "SDG03", "SDG10"],
    q1_answer5: ["SDG04", "SDG08", "SDG09"],
    q1_answer6: ["SDG11"],
    q1_answer7: ["SDG04", "SDG08", "SDG09", "SDG17", "SDG16"],
    q1_answer8: ["SDG09", "SDG11"],
    q1_answer9: ["SDG13", "SDG14", "SDG15"],
    q1_answer10: ["SDG05", "SDG10"],
    q1_answer11: ["SDG03", "SDG08"],
    q1_answer12: ["SDG06", "SDG07", "SDG012"],


    // Pertanyaan 2
    q2_answer1: ["SDG04"],
    q2_answer2: ["SDG10", "SDG11"],
    q2_answer3: ["SDG08", "SDG09", "SDG12"],
    q2_answer4: ["SDG08", "SDG17"],
    q2_answer5: ["SDG11"],
    q2_answer6: ["SDG01", "SDG02", "SDG10"],
    q2_answer7: ["SDG06", "SDG13", "SDG14", "SDG15"],
    q2_answer8: ["SDG16"],

    // Pertanyaan 3
    q3_answer1: ["SDG03", "SDG04"],
    q3_answer2: ["SDG08", "SDG09"],
    q3_answer3: ["SDG05", "SDG10", "SDG16"],
    q3_answer4: ["SDG13", "SDG14", "SDG15"],
    q3_answer5: ["SDG07"],
    q3_answer6: ["SDG09"],
    q3_answer7: ["SDG06"],
    q3_answer8: ["SDG17"],
    q3_answer9: ["SDG11"],
    q3_answer10: ["SDG01", "SDG02"],
    q3_answer11: ["SDG02", "SDG03"],

    // Pertanyaan 4
    q4_answer1: ["SDG12", "SDG13"],
    q4_answer2: ["SDG14", "SDG15"],
    q4_answer3: ["SDG07"],
    q4_answer4: ["SDG06"],
    // q4_answer5: [],

    // Pertanyaan 5
    q5_answer1: ["SDG03"],
    q5_answer2: ["SDG01", "SDG03"],
    q5_answer3: ["SDG04", "SDG05", "SDG10", "SDG16"],
    // q5_answer4: [],

    // Pertanyaan 6
    q6_answer1: ["SDG05", "SDG10"],
    q6_answer2: ["SDG01", "SDG02", "SDG10"],
    q6_answer3: ["SDG05", "SDG08"],
    // q6_answer4: [],

    // Pertanyaan 7
    q7_answer1: ["SDG09"],
    q7_answer2: ["SDG08", "SDG09"],
    q7_answer3: ["SDG07", "SDG12"],
    q7_answer4: ["SDG17"],
    // q7_answer5: [],
};

function calculateScore() {
    const scores = {};
    console.log("Calculating scores:", scores);
    Object.values(sdgMapping)
        .flat()
        .forEach((sdg) => {
            scores[sdg] = 0;
        });

    document
        .querySelectorAll("input[type=checkbox]:checked")
        .forEach((checkbox) => {
            const answerKey = checkbox.id;

            if (sdgMapping[answerKey]) {
                const scoreToAdd = 20;

                sdgMapping[answerKey].forEach((sdg) => {
                    scores[sdg] = (scores[sdg] || 0) + scoreToAdd;
                });
            }
        });

    console.log("Calculated scores:", scores);
    return scores;
}

function showPopup(title, message, callback = null) {
    const popup = document.getElementById("customPopup");
    const popupTitle = document.getElementById("popupTitle");
    const popupMessage = document.getElementById("popupMessage");
    const closeBtn = document.querySelector(".close-btn");
    const confirmBtn = document.getElementById("popupConfirmBtn");

    popupTitle.textContent = title;
    popupMessage.textContent = message;

    popup.style.display = "flex";

    const closePopup = () => {
        popup.style.animation = "popupFadeOut 0.3s ease-out";
        setTimeout(() => {
            popup.style.display = "none";
            popup.style.animation = "popupFadeIn 0.3s ease-out";
            if (callback && typeof callback === "function") {
                callback();
            }
        }, 300);
    };

    closeBtn.onclick = closePopup;
    confirmBtn.onclick = closePopup;

    popup.onclick = (e) => {
        if (e.target === popup) {
            closePopup();
        }
    };
}

function displayScore() {
    const sdgScores = calculateScore();
    console.log("Displaying score with sdgScores:", sdgScores);

    if (!sdgScores || typeof sdgScores !== "object") {
        console.error("sdgScores not found or invalid");
        showPopup("Error", "Terjadi kesalahan dalam menghitung skor.");
        return;
    }

    const bookingId = document.getElementById("bookingId").value.trim();
    if (!bookingId) {
        showPopup("Peringatan", "Silakan masukkan ID Booking terlebih dahulu!");
        return;
    }

    if (!bookingId.match(/^[A-Za-z0-9-]+$/)) {
        showPopup(
            "Peringatan",
            "ID Booking hanya boleh berisi huruf, angka, atau tanda hubung!"
        );
        return;
    }

    const filteredScores = Object.entries(sdgScores)
        .filter(([sdg, score]) => score >= 60)
        .map(([sdg, score]) => ({ sdg, score }));

    console.log("Filtered scores to send:", filteredScores);

    sessionStorage.setItem("sdgScores", JSON.stringify(filteredScores));

    fetch("/save-sdg-result", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify({
            booking_id: bookingId,
            sdg_results: filteredScores,
        }),
    })
        .then((response) => {
            console.log("Server response status:", response.status);
            return response.json();
        })
        .then((data) => {
            console.log("Server response data:", data);
            if (data.success) {
                console.log("Data SDG berhasil disimpan");
                window.location.href = "/result";
            } else {
                showPopup("Pemberitahuan", data.message);
            }
        })
        .catch((error) => {
            console.error("Error saving SDG data:", error);
            showPopup("Error", "Terjadi kesalahan saat menyimpan data.");
        });
}

function validateForm() {
    const questions = [
        { id: 1, prefix: 'q1_answer', count: 12 },
        { id: 2, prefix: 'q2_answer', count: 8 },
        { id: 3, prefix: 'q3_answer', count: 11 },
        { id: 4, prefix: 'q4_answer', count: 5 },
        { id: 5, prefix: 'q5_answer', count: 4 },
        { id: 6, prefix: 'q6_answer', count: 4 },
        { id: 7, prefix: 'q7_answer', count: 5 }
    ];

    for (let i = 0; i < questions.length; i++) {
        const question = questions[i];
        let isAnswered = false;
        for (let j = 1; j <= question.count; j++) {
            if (document.getElementById(`${question.prefix}${j}`)?.checked) {
                isAnswered = true;
                break;
            }
        }
        if (!isAnswered) {
            if (question.id <= 6) {
                const kuisionerIndex = question.id; 
                showPopup(
                    'Peringatan',
                    `Pertanyaan ${question.id} belum diisi. Pilih minimal satu jawaban.`,
                    () => {
                        const targetElement = document.querySelectorAll('.kuisioner')[kuisionerIndex];
                        if (targetElement) {
                            targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        } else {
                            console.error(`Elemen .kuisioner[${kuisionerIndex}] tidak ditemukan`);
                        }
                    }
                );
            } else {
                showPopup(
                    'Peringatan',
                    `Pertanyaan ${question.id} belum diisi. Pilih minimal satu jawaban.`
                );
            }
            return false;
        }
    }
    return true;
}

document.addEventListener("DOMContentLoaded", () => {
    console.log("DOM Loaded");
    const btn = document.getElementById("submitBtn");
    if (!btn) {
        console.log("Tombol submit tidak ditemukan!");
    } else {
        console.log("Tombol ditemukan, listener ditambahkan.");
        btn.addEventListener("click", () => {
            if (validateForm()) {
                displayScore();
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const bookingIdField = document.getElementById("bookingId");

    if (typeof bookingId !== "undefined" && bookingId) {
        bookingIdField.value = bookingId;
    } else {
        const bookingIdFromUrl = getUrlParameter("booking_id");
        const encryptedCode = getUrlParameter("code");

        if (bookingIdFromUrl) {
            bookingIdField.value = bookingIdFromUrl;
        } else if (encryptedCode) {
            fetch(`/api/decrypt-booking?code=${encryptedCode}`)
                .then((response) => response.json())
                .then((data) => {
                    if (data.booking_id) {
                        bookingIdField.value = data.booking_id;
                    }
                })
                .catch((error) => {
                    console.error(
                        "Terjadi kesalahan saat mendekripsi kode booking:",
                        error
                    );
                });
        }
    }
});

function getUrlParameter(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
    var results = regex.exec(location.search);
    return results === null
        ? ""
        : decodeURIComponent(results[1].replace(/\+/g, " "));
}