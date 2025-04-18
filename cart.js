document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Display cart items
    displayCart();
    
    // Add event listener for clearing cart
    const clearCartButton = document.getElementById('clear-cart');
    if (clearCartButton) {
        clearCartButton.addEventListener('click', function() {
            showConfirmModal();
        });
    }

    // Add event listener for checkout
    const checkoutButton = document.getElementById('checkout');
    if (checkoutButton) {
        checkoutButton.addEventListener('click', function(e) {
            if(cart.length === 0) {
                alert('Your cart is empty!');
                e.preventDefault();
                return;
            }
            
            // Prepare cart data for server-side processing
            fetch('prepare_checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({cart: cart})
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.href = 'checkout.php';
                }
            });
        });
    }
    
    // Handle confirmation modal
    const confirmYesButton = document.getElementById('confirm-yes');
    const confirmNoButton = document.getElementById('confirm-no');
    const confirmModal = document.getElementById('confirm-modal');
    
    if (confirmYesButton && confirmNoButton && confirmModal) {
        confirmYesButton.addEventListener('click', function() {
            // Clear cart
            cart = [];
            localStorage.setItem('cart', JSON.stringify(cart));
            displayCart();
            confirmModal.style.display = 'none';
        });
        
        confirmNoButton.addEventListener('click', function() {
            confirmModal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === confirmModal) {
                confirmModal.style.display = 'none';
            }
        });
    }
    
    // Function to display cart items
    function displayCart() {
        const cartItemsContainer = document.getElementById('cart-items');
        const cartSubtotal = document.getElementById('cart-subtotal');
        const cartCount = document.getElementById('cart-count');
        const cartEmpty = document.getElementById('cart-empty');
        const cartContent = document.getElementById('cart-content');
        
        if (cartItemsContainer && cartSubtotal) {
            if (cart.length === 0) {
                // Show empty cart message
                if (cartEmpty && cartContent) {
                    cartEmpty.style.display = 'block';
                    cartContent.style.display = 'none';
                }
                
                if (cartCount) {
                    cartCount.textContent = '0';
                }
            } else {
                // Show cart content
                if (cartEmpty && cartContent) {
                    cartEmpty.style.display = 'none';
                    cartContent.style.display = 'block';
                }
                
                // Update cart count
                if (cartCount) {
                    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                    cartCount.textContent = totalItems;
                }
                
                // Clear previous items
                cartItemsContainer.innerHTML = '';
                
                // Calculate subtotal
                let subtotal = 0;
                
                // Add each item to the cart
                cart.forEach((item, index) => {
                    const itemTotal = item.price * item.quantity;
                    subtotal += itemTotal;
                    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.name}</td>
                        <td>$${item.price.toFixed(2)}</td>
                        <td>${item.quantity}</td>
                        <td>$${itemTotal.toFixed(2)}</td>
                        <td>
                            <button class="btn btn-danger remove-item" data-id="${item.id}">Remove</button>
                        </td>
                    `;
                    
                    cartItemsContainer.appendChild(row);
                });
                
                // Update subtotal
                cartSubtotal.textContent = `$${subtotal.toFixed(2)}`;
                
                // Add event listeners for remove buttons
                document.querySelectorAll('.remove-item').forEach(button => {
                    button.addEventListener('click', function() {
                        const productId = this.getAttribute('data-id');
                        cart = cart.filter(item => item.id !== productId);
                        localStorage.setItem('cart', JSON.stringify(cart));
                        displayCart();
                    });
                });
            }
        }
    }
    
    // Function to show confirmation modal
    function showConfirmModal() {
        const confirmModal = document.getElementById('confirm-modal');
        if (confirmModal) {
            confirmModal.style.display = 'block';
        }
    }
});

