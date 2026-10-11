// Cart functionality
document.addEventListener('DOMContentLoaded', function() {
    // Load cart from localStorage only once
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    updateCartCount();
    
    // Add event listeners to "Add to Cart" buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn:not([onclick])');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', handleAddToCart);
    });
    
    // Event listeners for quantity buttons on product detail page
    const minusBtn = document.querySelector('.quantity-btn.minus');
    const plusBtn = document.querySelector('.quantity-btn.plus');
    const quantityInput = document.querySelector('.quantity-input');
    
    if (minusBtn && plusBtn && quantityInput) {
        const stock = parseInt(quantityInput.getAttribute('data-stock'));
        
        minusBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            if (value > 1) {
                quantityInput.value = value - 1;
            }
        });
        
        plusBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            if (value < stock) {
                quantityInput.value = value + 1;
            } else {
                alert(`Sorry, we only have ${stock} items in stock.`);
            }
        });
        
        quantityInput.addEventListener('input', function() {
            let value = parseInt(this.value);
            const stock = parseInt(this.getAttribute('data-stock'));
            
            if (isNaN(value) || value < 1) {
                this.value = 1;
            } else if (value > stock) {
                this.value = stock;
                alert(`Sorry, we only have ${stock} items in stock.`);
            }
        });
    }
    
    // Handle cart page functionality
    if (window.location.pathname.includes('cart.php')) {
        displayCartItems();
        updateCartSummary();
        
        // Add event listeners for removing items
        document.querySelector('.cart-items').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item')) {
                const productId = e.target.getAttribute('data-id');
                removeFromCart(productId);
                displayCartItems();
                updateCartSummary();
            }
        });
    }
    
    // Handle checkout page functionality
    if (window.location.pathname.includes('checkout.php')) {
        displayOrderSummary();
        
        // Handle form submission for checkout
        const checkoutForm = document.getElementById('checkout-form');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                const formData = new FormData(checkoutForm);
                
                // Add cart items to form data
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                formData.append('cart_items', JSON.stringify(cart));
                
                // Submit form data to process_order.php
                fetch('process_order.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Clear cart and redirect to confirmation page
                        localStorage.removeItem('cart');
                        window.location.href = 'order_confirmation.php';
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('There was an error processing your order. Please try again.');
                });
            });
        }
    }

    // Help System
    const helpContent = {
        cart: {
            title: "Shopping Cart Help",
            content: `
                <p>Welcome to your shopping cart! Here's how to use it:</p>
                <ul>
                    <li>Use the + and - buttons to adjust item quantities</li>
                    <li>Click "Remove" to remove individual items</li>
                    <li>Use "Remove All Items" to clear your cart</li>
                    <li>Click "Proceed to Checkout" when ready to purchase</li>
                    <li>Use "Continue Shopping" to add more items</li>
                </ul>
            `
        },
        products: {
            title: "Products Help",
            content: `
                <p>Browse our pet food products:</p>
                <ul>
                    <li>Click on a product to view details</li>
                    <li>Use the quantity controls to select amount</li>
                    <li>Click "Add to Cart" to add items to your cart</li>
                    <li>Use the search bar to find specific products</li>
                </ul>
            `
        },
        checkout: {
            title: "Checkout Help",
            content: `
                <p>Complete your purchase:</p>
                <ul>
                    <li>Review your order summary</li>
                    <li>Enter your shipping information</li>
                    <li>Select your payment method</li>
                    <li>Review and confirm your order</li>
                </ul>
            `
        }
    };

    function showHelp(button) {
        const helpBox = document.getElementById('help-box');
        const helpContentDiv = document.getElementById('help-content');
        const helpType = button.getAttribute('data-help');
        const overlay = document.querySelector('.help-overlay');
        
        if (helpContent[helpType]) {
            // Update content
            helpBox.querySelector('h3').textContent = helpContent[helpType].title;
            helpContentDiv.innerHTML = helpContent[helpType].content;
            
            // Show overlay
            overlay.style.display = 'block';
            
            // Center the help box on the screen
            helpBox.style.left = '50%';
            helpBox.style.top = '50%';
            helpBox.style.transform = 'translate(-50%, -50%)';
            
            // Show the help box
            helpBox.style.display = 'block';
            
            // Add active class to button
            button.classList.add('active');
        }
    }

    function hideHelp() {
        const helpBox = document.getElementById('help-box');
        const helpBtn = document.querySelector('.help-btn');
        const overlay = document.querySelector('.help-overlay');
        
        helpBox.style.display = 'none';
        overlay.style.display = 'none';
        
        if (helpBtn) {
            helpBtn.classList.remove('active');
        }
    }

    // Close help box when clicking outside
    document.addEventListener('click', function(event) {
        const helpBox = document.getElementById('help-box');
        const helpBtn = document.querySelector('.help-btn');
        const overlay = document.querySelector('.help-overlay');
        
        if (helpBox && helpBtn && overlay) {
            if (event.target === overlay || (!helpBox.contains(event.target) && event.target !== helpBtn && !helpBtn.contains(event.target))) {
                hideHelp();
            }
        }
    });

    // Initialize help button on page load
    const currentPage = window.location.pathname.split('/').pop().split('.')[0];
    const helpBtn = document.querySelector('.help-btn');
    const closeBtn = document.querySelector('.close-btn');
    
    if (helpBtn && helpContent[currentPage]) {
        helpBtn.setAttribute('data-help', currentPage);
        
        // Add click event listener to the help button
        helpBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const helpBox = document.getElementById('help-box');
            
            if (helpBox.style.display === 'block') {
                hideHelp();
            } else {
                showHelp(this);
            }
        });
    }
    
    // Add click event listener to close button
    if (closeBtn) {
        closeBtn.addEventListener('click', hideHelp);
    }

    // Help button functionality
    const helpButtons = document.querySelectorAll('.help-btn');
    const overlay = document.getElementById('overlay');
    const helpPopup = document.getElementById('helpPopup');
    const popupTitle = document.getElementById('popupTitle');
    const popupContent = document.getElementById('popupContent');

    helpButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productType = this.getAttribute('data-type');
            const productName = this.getAttribute('data-name');
            
            // Set popup content based on product type
            if (productType === 'cat') {
                popupTitle.textContent = 'Cat Food Information';
                popupContent.innerHTML = `
                    <strong>${productName}</strong> is specially formulated for cats.<br><br>
                    <strong>Key Benefits:</strong><br>
                    - Complete and balanced nutrition<br>
                    - Supports healthy digestion<br>
                    - Promotes shiny coat and healthy skin<br>
                    - Formulated for optimal feline health<br><br>
                    <strong>Feeding Guidelines:</strong><br>
                    - Feed according to your cat's age and weight<br>
                    - Always provide fresh water<br>
                    - Store in a cool, dry place
                `;
            } else if (productType === 'dog') {
                popupTitle.textContent = 'Dog Food Information';
                popupContent.innerHTML = `
                    <strong>${productName}</strong> is specially formulated for dogs.<br><br>
                    <strong>Key Benefits:</strong><br>
                    - Complete and balanced nutrition<br>
                    - Supports healthy digestion<br>
                    - Promotes strong muscles and bones<br>
                    - Formulated for optimal canine health<br><br>
                    <strong>Feeding Guidelines:</strong><br>
                    - Feed according to your dog's age and weight<br>
                    - Always provide fresh water<br>
                    - Store in a cool, dry place
                `;
            }
            
            // Show popup and overlay
            overlay.style.display = 'block';
            helpPopup.style.display = 'block';
        });
    });
});

function closeHelpPopup() {
    const overlay = document.getElementById('overlay');
    const helpPopup = document.getElementById('helpPopup');
    
    overlay.style.display = 'none';
    helpPopup.style.display = 'none';
}

// Close popup when clicking outside
document.getElementById('overlay').addEventListener('click', closeHelpPopup);

// Remove the showPopup function and replace with simple alert
function showErrorPopup(message) {
    alert(message);
}

function showSuccessPopup(message) {
    alert(message);
}

// Handle add to cart click
function handleAddToCart(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const productId = this.getAttribute('data-id');
    const productName = this.getAttribute('data-name');
    const productPrice = parseFloat(this.getAttribute('data-price'));
    
    // Get quantity if available
    let quantity = 1;
    const quantityInput = document.querySelector('.quantity-input');
    if (quantityInput) {
        quantity = parseInt(quantityInput.value);
    }
    
    addToCart(productId, productName, productPrice, quantity);
}

// Update cart count display
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = totalItems;
    }
}

// Add item to cart with stock validation
function addToCart(productId, productName, price, quantity = 1) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const stock = parseInt(document.querySelector(`.add-to-cart-btn[data-id="${productId}"]`).getAttribute('data-stock'));
    
    // Check if product already exists in cart
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        // Check if adding more would exceed stock
        const totalQuantity = existingItem.quantity + quantity;
        if (totalQuantity > stock) {
            alert(`Sorry, we only have ${stock} items of ${productName} in stock. You already have ${existingItem.quantity} in your cart.`);
            return;
        }
        existingItem.quantity = totalQuantity;
    } else {
        // Check if quantity exceeds stock for new items
        if (quantity > stock) {
            alert(`Sorry, we only have ${stock} items of ${productName} in stock.`);
            return;
        }
        
        cart.push({
            id: productId,
            name: productName,
            price: price,
            quantity: quantity,
            stock: stock
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
}

// Update quantity in cart
function updateQuantity(productId, change) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const itemIndex = cart.findIndex(item => item.id === productId);
    
    if (itemIndex !== -1) {
        const item = cart[itemIndex];
        const newQuantity = item.quantity + change;
        
        // Get current stock from the product page or use stored stock value
        const currentStock = parseInt(document.querySelector(`.add-to-cart-btn[data-id="${productId}"]`)?.getAttribute('data-stock')) || item.stock;
        
        if (newQuantity > currentStock) {
            alert(`Sorry, we only have ${currentStock} items in stock.`);
            return;
        }
        
        if (newQuantity <= 0) {
            cart.splice(itemIndex, 1);
        } else {
            item.quantity = newQuantity;
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        displayCartItems();
        updateCartSummary();
        updateCartCount();
    }
}

// Remove item from cart
function removeFromCart(productId) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const itemIndex = cart.findIndex(item => item.id === productId);
    
    if (itemIndex !== -1) {
        cart.splice(itemIndex, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        loadCartItems();
    }
}

// Display cart items on cart page
function displayCartItems() {
    const cartItemsContainer = document.querySelector('.cart-items');
    const checkoutBtn = document.getElementById('checkout-btn');
    const removeAllBtn = document.getElementById('remove-all-btn');
    const continueShoppingBtn = document.querySelector('.continue-shopping-btn');
    if (!cartItemsContainer) return;
    
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p class="empty-cart-message">Your cart is empty</p>';
        checkoutBtn.style.display = 'none';
        removeAllBtn.style.display = 'none';
        continueShoppingBtn.style.margin = '0 auto';
        return;
    }
    
    checkoutBtn.style.display = 'block';
    removeAllBtn.style.display = 'block';
    continueShoppingBtn.style.margin = '';
    checkoutBtn.disabled = false;
    
    let html = '';
    
    cart.forEach(item => {
        html += `
            <div class="cart-item">
                <div class="item-details">
                    <h3>${item.name}</h3>
                    <p class="item-price">$${item.price.toFixed(2)}</p>
                </div>
                <div class="item-quantity">
                    <button class="quantity-btn minus" data-id="${item.id}">-</button>
                    <span class="quantity">${item.quantity}</span>
                    <button class="quantity-btn plus" data-id="${item.id}">+</button>
                </div>
                <div class="item-total">
                    <p>$${(item.price * item.quantity).toFixed(2)}</p>
                </div>
                <button class="remove-item" data-id="${item.id}">Remove</button>
            </div>
        `;
    });
    
    cartItemsContainer.innerHTML = html;

    // Add event listeners for quantity buttons
    document.querySelectorAll('.quantity-btn.minus').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            updateQuantity(productId, -1);
        });
    });

    document.querySelectorAll('.quantity-btn.plus').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            updateQuantity(productId, 1);
        });
    });

    // Add event listener for remove all button
    if (removeAllBtn) {
        removeAllBtn.onclick = function() {
            if (confirm('Are you sure you want to remove all items from your cart?')) {
                localStorage.removeItem('cart');
                displayCartItems();
                updateCartSummary();
                updateCartCount();
                alert('All items have been removed from your cart');
            }
        };
    }

    // Add event listener for checkout button
    checkoutBtn.onclick = function() {
        if (cart.length === 0) {
            alert('Your cart is empty. Please add some items before proceeding to checkout.');
            return;
        }
        window.location.href = 'checkout.php';
    };
}

// Update cart summary on cart page
function updateCartSummary() {
    const subtotalElement = document.getElementById('cart-subtotal');
    const checkoutBtn = document.getElementById('checkout-btn');
    if (!subtotalElement) return;
    
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    
    subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
    checkoutBtn.disabled = cart.length === 0;
}

// Display order summary on checkout page
function displayOrderSummary() {
    const cartItemsList = document.getElementById('cart-items-list');
    const cartTotal = document.getElementById('cart-total');
    
    if (!cartItemsList || !cartTotal) return;
    
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    if (cart.length === 0) {
        cartItemsList.innerHTML = '<p class="empty-cart">Your cart is empty</p>';
        cartTotal.textContent = '0.00';
        return;
    }
    
    let html = '';
    let total = 0;
    
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        html += `
            <div class="order-item">
                <div class="item-name">${item.name} x ${item.quantity}</div>
                <div class="item-price">$${itemTotal.toFixed(2)}</div>
            </div>
        `;
    });
    
    cartItemsList.innerHTML = html;
    cartTotal.textContent = total.toFixed(2);
    
    // Generate random order number
    const orderNumber = document.getElementById('order-number');
    if (orderNumber) {
        const randomOrderNum = Math.floor(100000 + Math.random() * 900000);
        orderNumber.textContent = `PFS-${randomOrderNum}`;
    }
}

// Add styles for popups
const style = document.createElement('style');
style.textContent = `
    .error-popup, .success-popup {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
    }
    
    .error-content, .success-content {
        background: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .error-content {
        border-left: 4px solid #dc3545;
    }
    
    .success-content {
        border-left: 4px solid #28a745;
    }
    
    .error-content i {
        color: #dc3545;
        font-size: 1.2rem;
    }
    
    .success-content i {
        color: #28a745;
        font-size: 1.2rem;
    }
    
    .error-content p, .success-content p {
        margin: 0;
        flex-grow: 1;
    }
    
    .close-btn {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 5px 10px;
    }
    
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
`;
document.head.appendChild(style);

