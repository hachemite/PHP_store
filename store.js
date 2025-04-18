document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    updateCartCount();
    
    // Add event listeners to all "Add to Cart" buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-name');
            const productPrice = parseFloat(this.getAttribute('data-price'));
            
            // Check if product already in cart
            const existingItem = cart.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    quantity: 1
                });
            }
            
            // Save cart to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
            showNotification(`${productName} added to cart!`);
        });
    });
    
    // Close modal when clicking on X
    const closeModal = document.querySelector('.close');
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            const modal = document.getElementById('cart-notification');
            modal.style.display = 'none';
        });
    }
    
    // Close modal when clicking outside of it
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('cart-notification');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Update cart count in header
    function updateCartCount() {
        const cartCountElements = document.querySelectorAll('#cart-count, .cart-badge');
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        cartCountElements.forEach(el => el.textContent = totalItems);
    }
    
    // Show notification
    function showNotification(message) {
        const modal = document.getElementById('cart-notification');
        const notificationMessage = document.getElementById('notification-message');
        
        if (modal && notificationMessage) {
            notificationMessage.textContent = message;
            modal.style.display = 'block';
            setTimeout(() => modal.style.display = 'none', 2000);
        }
    }
});