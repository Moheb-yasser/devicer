
document.addEventListener('DOMContentLoaded', function() {

    const scrollTopBtn = document.querySelector('.scroll-top');
    
    if (scrollTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        });
        
        scrollTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // ============================================
    // 2. CART FUNCTIONALITY (FIXED VERSION)
    // ============================================
    
    // Function to get cart items from localStorage (ALWAYS returns an array)
    function getCartItems() {
        try {
            const saved = localStorage.getItem('cartItems');
            if (!saved) return [];
            
            const items = JSON.parse(saved);
            // Double-check it's an array
            return Array.isArray(items) ? items : [];
        } catch (error) {
            console.error('Error loading cart:', error);
            return [];
        }
    }
    
    // Function to save cart items to localStorage
    function saveCartItems(items) {
        try {
            localStorage.setItem('cartItems', JSON.stringify(items));
        } catch (error) {
            console.error('Error saving cart:', error);
        }
    }
    
    // Function to update cart count in header
    function updateCartHeader() {
        const cartItems = getCartItems();
        
        let totalItems = 0;
        let totalPrice = 0;
        
        // Check if cartItems is an array before using forEach
        if (Array.isArray(cartItems)) {
            cartItems.forEach(item => {
                totalItems += item.quantity || 1;
                totalPrice += (item.price || 0) * (item.quantity || 1);
            });
        }
        
        // Update cart count elements
        const cartCountElement = document.querySelector('.cart-count');
        const cartTotalElement = document.querySelector('.cart-total');
        
        if (cartCountElement) {
            cartCountElement.textContent = totalItems;
        }
        if (cartTotalElement) {
            cartTotalElement.textContent = '$' + totalPrice.toFixed(2);
        }
    }
    
    // Initialize cart header on page load
    updateCartHeader();
    
    // ============================================
    // 3. ADD TO CART FUNCTIONALITY (FIXED)
    // ============================================
    
    // Listen for clicks on "ADD TO CART" buttons
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('btn-add-cart')) {
            event.preventDefault();
            
            // Get product details
            const productCard = event.target.closest('.product-card');
            const productId = productCard.getAttribute('data-id');
            const productName = productCard.querySelector('.product-name').textContent;
            const priceText = productCard.querySelector('.current-price').textContent;
            const productPrice = parseFloat(priceText.replace('$', '').replace(',', ''));
            const productImage = productCard.querySelector('img').src;
            
            // Get current cart
            const cartItems = getCartItems();
            
            // Check if product already exists in cart
            const existingIndex = cartItems.findIndex(item => item.id === productId);
            
            if (existingIndex >= 0) {
                // Product exists - increase quantity
                cartItems[existingIndex].quantity += 1;
            } else {
                // Product doesn't exist - add new item
                cartItems.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    image: productImage,
                    quantity: 1
                });
            }
            
            saveCartItems(cartItems);
            
            updateCartHeader();
            
            const originalText = event.target.textContent;
            event.target.textContent = 'ADDED!';
            event.target.style.backgroundColor = '#4CAF50';
            event.target.style.color = 'white';
            
            setTimeout(() => {
                event.target.textContent = originalText;
                event.target.style.backgroundColor = '';
                event.target.style.color = '';
            }, 1500);
        }
    });
    
    if (document.querySelector('.hero-slider')) {
        const slides = document.querySelectorAll('.slide');
        if (slides.length > 0) {
            let currentSlide = 0;
            
            function showSlide(index) {
                slides.forEach(slide => slide.classList.remove('active'));
                slides[index].classList.add('active');
            }
            
            function nextSlide() {
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
            }
            
            setInterval(nextSlide, 4000);
            showSlide(0);
        }
    }
    
    const searchInput = document.querySelector('.search-input');
    const searchBtn = document.querySelector('.search-btn');
    
    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', function() {
            if (searchInput.value.trim()) {
                alert('Searching for: ' + searchInput.value);
            }
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchBtn.click();
            }
        });
    }
    
    if (document.querySelector('.products-grid')) {
        const productCards = document.querySelectorAll('.product-card');
        
        productCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    }
});