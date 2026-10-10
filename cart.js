// ============================================
// FIXED CART PAGE JAVASCRIPT
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    
    // Function to get cart items (SAME as in e-commearcAI.js)
    function getCartItems() {
        try {
            const saved = localStorage.getItem('cartItems');
            if (!saved) return [];
            
            const items = JSON.parse(saved);
            return Array.isArray(items) ? items : [];
        } catch (error) {
            console.error('Error loading cart:', error);
            return [];
        }
    }
    
    // Function to save cart items (SAME as in e-commearcAI.js)
    function saveCartItems(items) {
        try {
            localStorage.setItem('cartItems', JSON.stringify(items));
        } catch (error) {
            console.error('Error saving cart:', error);
        }
    }
    
    // Function to update cart header (SAME as in e-commearcAI.js)
    function updateCartHeader() {
        const cartItems = getCartItems();
        
        let totalItems = 0;
        let totalPrice = 0;
        
        if (Array.isArray(cartItems)) {
            cartItems.forEach(item => {
                totalItems += item.quantity || 1;
                totalPrice += (item.price || 0) * (item.quantity || 1);
            });
        }
        
        const cartCountElement = document.querySelector('.cart-count');
        const cartTotalElement = document.querySelector('.cart-total');
        
        if (cartCountElement) {
            cartCountElement.textContent = totalItems;
        }
        if (cartTotalElement) {
            cartTotalElement.textContent = '$' + totalPrice.toFixed(2);
        }
    }
    
    // Load cart and update display
    let cartItems = getCartItems();
    
    function updateCartDisplay() {
        const cartContent = document.getElementById('cart-content');
        
        // Check if cart is empty
        if (!Array.isArray(cartItems) || cartItems.length === 0) {
            cartContent.innerHTML = `
                <div class="empty-cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <p>Your cart is empty</p>
                    <a href="index.html" class="continue-shopping">Continue Shopping</a>
                </div>
            `;
            return;
        }
        
        // Build cart table
        let html = `
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        let totalPrice = 0;
        
        for (let i = 0; i < cartItems.length; i++) {
            const item = cartItems[i];
            const subtotal = (item.price || 0) * (item.quantity || 1);
            totalPrice += subtotal;
            
            html += `
                <tr>
                    <td>
                        <div class="cart-item-info">
                            <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                            <span class="cart-item-name">${item.name}</span>
                        </div>
                    </td>
                    <td>$${(item.price || 0).toFixed(2)}</td>
                    <td>
                        <div class="quantity-controls">
                            <button class="qty-btn minus" data-index="${i}">-</button>
                            <span class="qty-display">${item.quantity || 1}</span>
                            <button class="qty-btn plus" data-index="${i}">+</button>
                        </div>
                    </td>
                    <td>$${subtotal.toFixed(2)}</td>
                    <td>
                        <button class="remove-btn" data-index="${i}">×</button>
                    </td>
                </tr>
            `;
        }
        
        html += `
                </tbody>
            </table>
            
            <div class="cart-summary">
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>$${totalPrice.toFixed(2)}</span>
                </div>
                <button id="checkoutBtn" class="checkout-btn">Checkout</button>
            </div>
        `;
        
        cartContent.innerHTML = html;
        
        // Add event listeners to cart buttons
        addCartEventListeners();
    }
    
    function addCartEventListeners() {
        // Plus buttons
        document.querySelectorAll('.plus').forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                cartItems[index].quantity += 1;
                saveCartItems(cartItems);
                updateCartDisplay();
                updateCartHeader();
            });
        });
        
        // Minus buttons
        document.querySelectorAll('.minus').forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                if (cartItems[index].quantity > 1) {
                    cartItems[index].quantity -= 1;
                    saveCartItems(cartItems);
                    updateCartDisplay();
                    updateCartHeader();
                }
            });
        });
        
        // Remove buttons
        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                cartItems.splice(index, 1);
                saveCartItems(cartItems);
                updateCartDisplay();
                updateCartHeader();
            });
        });
        
        // Checkout button
        const checkoutBtn = document.getElementById('checkoutBtn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', function() {
                document.getElementById('confirmModal').style.display = 'flex';
            });
        }
    }
    
    // Modal handling (from cart.html)
    const confirmModal = document.getElementById('confirmModal');
    const successModal = document.getElementById('successModal');
    
    if (confirmModal) {
        document.getElementById('confirmNo').addEventListener('click', function() {
            confirmModal.style.display = 'none';
        });
    }
    
    if (successModal) {
        document.getElementById('confirmYes').addEventListener('click', function() {
            confirmModal.style.display = 'none';
            
            // Clear cart
            cartItems = [];
            saveCartItems(cartItems);
            
            successModal.style.display = 'flex';
        });
        
        document.getElementById('successOk').addEventListener('click', function() {
            successModal.style.display = 'none';
            updateCartDisplay();
            updateCartHeader();
        });
    }
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === confirmModal) {
            confirmModal.style.display = 'none';
        }
        if (event.target === successModal) {
            successModal.style.display = 'none';
        }
    });
    
    // Initialize cart display
    updateCartDisplay();
    updateCartHeader();
});