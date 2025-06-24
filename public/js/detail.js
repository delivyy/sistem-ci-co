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

document.addEventListener('DOMContentLoaded', () => {
    console.log('detail.js loaded');
    updateClock();
    setInterval(updateClock, 1000);
});

document.addEventListener('DOMContentLoaded', function() {
    const rowsPerPage = 10;
    const rows = document.querySelectorAll('.data-row');
    const rowsCount = rows.length;
    const pagesCount = Math.ceil(rowsCount / rowsPerPage);
    const maxVisiblePages = 5;
    let currentPage = 1;

    if (rowsCount > rowsPerPage) {
        const pagination = document.getElementById('pagination');
        
        // Previous button (tetap di kiri dalam grup pagination)
        const prevPageItem = document.createElement('li');
        prevPageItem.className = 'page-item';
        const prevPageLink = document.createElement('a');
        prevPageLink.className = 'page-link';
        prevPageLink.innerHTML = '&laquo;';
        prevPageLink.href = '#';
        prevPageLink.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentPage > 1) {
                showPage(currentPage - 1);
            }
        });
        prevPageItem.appendChild(prevPageLink);
        pagination.appendChild(prevPageItem);
        
        // Container untuk nomor halaman
        const pageNumbersContainer = document.createElement('div');
        pageNumbersContainer.className = 'page-numbers-container';
        pagination.appendChild(pageNumbersContainer);
        
        // Next button (tetap di kanan dalam grup pagination)
        const nextPageItem = document.createElement('li');
        nextPageItem.className = 'page-item';
        const nextPageLink = document.createElement('a');
        nextPageLink.className = 'page-link';
        nextPageLink.innerHTML = '&raquo;';
        nextPageLink.href = '#';
        nextPageLink.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentPage < pagesCount) {
                showPage(currentPage + 1);
            }
        });
        nextPageItem.appendChild(nextPageLink);
        pagination.appendChild(nextPageItem);
        
        // Fungsi untuk membuat tombol halaman
        function createPageButton(pageNum) {
            const pageItem = document.createElement('li');
            pageItem.className = 'page-item';
            const pageLink = document.createElement('a');
            pageLink.className = 'page-link';
            pageLink.textContent = pageNum;
            pageLink.href = '#';
            pageLink.addEventListener('click', function(e) {
                e.preventDefault();
                showPage(pageNum);
            });
            pageItem.appendChild(pageLink);
            return pageItem;
        }
        
        // Fungsi update pagination
        function updatePagination() {
            pageNumbersContainer.innerHTML = '';
            
            if (pagesCount > 0) {
                pageNumbersContainer.appendChild(createPageButton(1));
            }
            
            if (currentPage > maxVisiblePages - 1 && pagesCount > maxVisiblePages + 2) {
                const ellipsis = document.createElement('li');
                ellipsis.className = 'page-item disabled ellipsis';
                ellipsis.innerHTML = '<span class="page-link">...</span>';
                pageNumbersContainer.appendChild(ellipsis);
            }
            
            let startPage = Math.max(2, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(pagesCount - 1, startPage + maxVisiblePages - 1);
            
            if (endPage === pagesCount - 1) {
                startPage = Math.max(2, endPage - maxVisiblePages + 1);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                if (i > 1 && i < pagesCount) {
                    pageNumbersContainer.appendChild(createPageButton(i));
                }
            }
            
            if (endPage < pagesCount - 1 && pagesCount > maxVisiblePages + 2) {
                const ellipsis = document.createElement('li');
                ellipsis.className = 'page-item disabled ellipsis';
                ellipsis.innerHTML = '<span class="page-link">...</span>';
                pageNumbersContainer.appendChild(ellipsis);
            }
            
            if (pagesCount > 1) {
                pageNumbersContainer.appendChild(createPageButton(pagesCount));
            }
            
            // Update active state
            const pageLinks = document.querySelectorAll('.page-link:not(.ellipsis .page-link)');
            pageLinks.forEach(link => {
                const pageNum = parseInt(link.textContent);
                if (!isNaN(pageNum) && pageNum === currentPage) {
                    link.parentElement.classList.add('active');
                } else {
                    link.parentElement.classList.remove('active');
                }
            });
            
            // Update prev/next buttons
            prevPageItem.className = currentPage === 1 ? 'page-item disabled' : 'page-item';
            nextPageItem.className = currentPage === pagesCount ? 'page-item disabled' : 'page-item';
        }
        
        function showPage(page) {
            currentPage = page;
            localStorage.setItem('currentPage', page); // <-- Simpan ke localStorage
        
            rows.forEach(row => row.style.display = 'none');
        
            const startIdx = (page - 1) * rowsPerPage;
            const endIdx = Math.min(startIdx + rowsPerPage, rowsCount);
        
            for (let i = startIdx; i < endIdx; i++) {
                if (rows[i]) rows[i].style.display = '';
            }
        
            updatePagination();
        }
        
        const savedPage = parseInt(localStorage.getItem('currentPage'));
        const pageToShow = !isNaN(savedPage) && savedPage >= 1 && savedPage <= pagesCount ? savedPage : 1;
            showPage(pageToShow);
    } else {
        document.querySelector('.pagination-container').style.display = 'none';
    }
});