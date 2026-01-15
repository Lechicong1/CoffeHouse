document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const rangeMin = document.getElementById('range-min');
    const rangeMax = document.getElementById('range-max');
    const displayMin = document.getElementById('price-min-display');
    const displayMax = document.getElementById('price-max-display');
    const progress = document.getElementById('slider-range-highlight');
    const btnSort = document.getElementById('btn-sort');
    const productsGrid = document.getElementById('products-grid');
    const productCards = document.querySelectorAll('.menu-card');
    const productCountLabel = document.querySelector('.products-count');
    const currentSortInput = document.getElementById('current-sort'); // Hidden input for state
    
    // Config
    const minGap = 5000; 

    // Guard clause
    if (!rangeMin || !rangeMax || !productsGrid) return; 

    function formatCurrency(params) {
        return params.toLocaleString('vi-VN') + 'đ';
    }

    // --- VISUAL UI UPDATES ---
    function updateSlider(e) {
        let minVal = parseInt(rangeMin.value);
        let maxVal = parseInt(rangeMax.value);

        // Prevent crossing
        if (maxVal - minVal < minGap) {
            if (e && e.target && e.target.id === "range-min") {
                rangeMin.value = maxVal - minGap;
                minVal = parseInt(rangeMin.value);
            } else {
                rangeMax.value = minVal + minGap;
                maxVal = parseInt(rangeMax.value);
            }
        }

        // Update Text
        displayMin.textContent = formatCurrency(minVal);
        displayMax.textContent = formatCurrency(maxVal);

        // Update Progress Bar
        const range = rangeMin.max - rangeMin.min;
        const leftPercent = ((minVal - rangeMin.min) / range) * 100;
        const widthPercent = ((maxVal - minVal) / range) * 100;

        progress.style.left = leftPercent + '%';
        progress.style.width = widthPercent + '%';
    }

    // --- CLIENT SIDE FILTERING LOGIC ---
    function filterAndSortProducts() {
        const minPrice = parseInt(rangeMin.value);
        const maxPrice = parseInt(rangeMax.value);
        const sortDirection = currentSortInput.value; // 'asc' or 'desc'
        
        // 1. Convert NodeList to Array for sorting
        let visibleProducts = Array.from(productCards);

        // 2. Filter logic
        visibleProducts.forEach(card => {
            const price = parseInt(card.getAttribute('data-price')) || 0;
            if (price >= minPrice && price <= maxPrice) {
                card.style.display = ''; // Reset display to CSS default (block/flex)
                card.classList.add('visible'); // Mark as visible for sorting logic if needed
            } else {
                card.style.display = 'none';
                card.classList.remove('visible');
            }
        });

        // 3. Update Count
        const visibleCount = document.querySelectorAll('.menu-card.visible').length; // Or just check style.display !== 'none'
        // Need to be careful counting if display is manipulated directly
        const actualVisibleCount = Array.from(productCards).filter(c => c.style.display !== 'none').length;
        if (productCountLabel) {
            productCountLabel.textContent = actualVisibleCount + ' sản phẩm';
        }

        // 4. Sort logic
        // Only sort the visible ones? No, sort all, the hidden ones stay hidden.
        // But re-appending moves them in DOM.
        
        const sortedProducts = Array.from(productCards).sort((a, b) => {
            const priceA = parseInt(a.getAttribute('data-price')) || 0;
            const priceB = parseInt(b.getAttribute('data-price')) || 0;
            
            if (sortDirection === 'asc') {
                return priceA - priceB;
            } else {
                return priceB - priceA;
            }
        });

        // Re-append to grid in new order
        // Note: verify if this causes flicker. Usually fast enough.
        productsGrid.innerHTML = '';
        sortedProducts.forEach(card => productsGrid.appendChild(card));
    }

    // --- EVENT LISTENERS ---

    // Range Sliders
    rangeMin.addEventListener('input', (e) => {
        updateSlider(e);
        filterAndSortProducts(); // Filter efficiently on drag? Or wait for change? 
        // Modern browsers handle layout thrashing well, but 'change' is safer for heavy DOM.
        // User asked for "smoother", real-time feel usually implies 'input'.
        // Let's try 'input' for instant feedback.
    });

    rangeMax.addEventListener('input', (e) => {
        updateSlider(e);
        filterAndSortProducts();
    });

    // Sort Button
    if (btnSort) {
        btnSort.addEventListener('click', function() {
            let sort = currentSortInput.value;
            // Toggle
            sort = (sort === 'asc') ? 'desc' : 'asc';
            currentSortInput.value = sort;

            // Update Icon/Text
            const icon = btnSort.querySelector('i');
            const text = btnSort.firstChild; // The text node
            
            // Just update HTML content simply
            btnSort.innerHTML = `Sắp xếp: Giá ${sort === 'asc' ? 'tăng dần' : 'giảm dần'} <i class="fas fa-sort-amount-${sort === 'asc' ? 'down-alt' : 'up'}"></i>`;

            filterAndSortProducts();
        });
    }

    // --- INIT ---
    updateSlider();
    // Do one initial sort/filter based on default state
    filterAndSortProducts(); 
});
