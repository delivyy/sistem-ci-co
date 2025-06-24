document.addEventListener("DOMContentLoaded", function () {
    // Handle modal button clicks to set the booking ID
    const modalButtons = document.querySelectorAll(
        '[data-bs-target="#dutyOfficerModal"]'
    );

    modalButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const bookingId = this.getAttribute("data-booking-id");
            const hiddenInput = document.getElementById("bookingId");
            if (hiddenInput) {
                hiddenInput.value = bookingId;
            }
        });
    });
});

// Function to update the booking status
function updateStatus(bookingId, newStatus) {
    fetch(`/bookings/${bookingId}/update-status`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        body: JSON.stringify({ status: newStatus }),
    })
        .then((response) => response.json())
        .then((data) => {
            console.log(data); // Log data for debugging

            if (data.success) {
                Swal.fire({
                    icon: "success",
                    title: "Status Updated",
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false,
                }).then(() => {
                    location.reload(); // Reload the page to show the updated status
                });
            } else {
                Swal.fire("Error", "Failed to update status", "error");
            }
        })
        .catch((error) => {
            Swal.fire(
                "Error",
                "An error occurred while updating the status.",
                "error"
            );
        });
}

// Function to update the items per page
function updatePerPage() {
    const perPage = document.getElementById("per-page").value;
    const currentUrl = "{{ url()->current() }}";
    window.location.href = `${currentUrl}?per_page=${perPage}`;
}

function copyToClipboard(text, button) {
    // Gunakan Clipboard API jika tersedia
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard
            .writeText(text)
            .then(function () {
                showCopyFeedback(button, true);
            })
            .catch(function () {
                // Fallback jika clipboard API gagal
                fallbackCopyTextToClipboard(text, button);
            });
    } else {
        // Fallback untuk browser lama
        fallbackCopyTextToClipboard(text, button);
    }
}

function fallbackCopyTextToClipboard(text, button) {
    var textArea = document.createElement("textarea");
    textArea.value = text;

    // Pastikan textarea tidak terlihat
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        var successful = document.execCommand("copy");
        showCopyFeedback(button, successful);
    } catch (err) {
        showCopyFeedback(button, false);
    }

    document.body.removeChild(textArea);
}

function showCopyFeedback(button, success) {
    var icon = button.querySelector("i");
    var originalClass = icon.className;
    var originalTitle = button.getAttribute("title");

    if (success) {
        // Ubah icon menjadi check
        icon.className = "fas fa-check text-success";
        button.setAttribute("title", "Link berhasil disalin!");

        // Kembali ke icon asli setelah 2 detik
        setTimeout(function () {
            icon.className = originalClass;
            button.setAttribute("title", originalTitle);
        }, 2000);
    } else {
        // Tampilkan error feedback
        icon.className = "fas fa-times text-danger";
        button.setAttribute("title", "Gagal menyalin link");

        setTimeout(function () {
            icon.className = originalClass;
            button.setAttribute("title", originalTitle);
        }, 2000);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.querySelector(".dropdown-toggle-user");
    const menu = document.querySelector(".dropdown-menu-user");

    if (toggle && menu) {
        toggle.addEventListener("click", function (e) {
            e.stopPropagation();
            menu.classList.toggle("show");
        });

        document.addEventListener("click", function () {
            menu.classList.remove("show");
        });
    }
});
