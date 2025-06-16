function addToCart(button, itemId, userId) {
    // Disable the button immediately to prevent double clicks
    button.disabled = true;
    
    // Create form data
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('user_id', userId);

    // Send AJAX request
    fetch('Template/ajax-cart.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin' // Include cookies in the request
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Cart response:', data); // Debug log
        
        if (data.success) {
            // Update button state
            button.classList.remove('btn-warning');
            button.classList.add('btn-success');
            button.innerHTML = '<i class="fas fa-shopping-cart"></i> In Cart';
            button.disabled = true;
            
            // Update cart count in header if it exists
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                const currentCount = parseInt(cartCount.textContent) || 0;
                cartCount.textContent = currentCount + 1;
            }
        } else {
            // If failed, re-enable the button and show specific error message
            button.disabled = false;
            let errorMessage = data.message || 'Failed to add item to cart.';
            if (data.debug) {
                console.error('Cart debug info:', data.debug);
                errorMessage += '\n\nDebug info has been logged to console.';
            }
            alert(errorMessage);
        }
    })
    .catch(error => {
        console.error('Cart error:', error);
        button.disabled = false;
        alert('An error occurred while adding to cart: ' + error.message);
    });
}

function addToWishlist(button, itemId, userId) {
    // Disable the button immediately to prevent double clicks
    button.disabled = true;
    
    // Create form data
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('user_id', userId);

    // Send AJAX request
    fetch('Template/ajax-wishlist.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin' // Include cookies in the request
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Wishlist response:', data); // Debug log
        
        if (data.success) {
            // Update button state
            button.classList.remove('btn-warning');
            button.classList.add('btn-success');
            button.innerHTML = '<i class="fas fa-heart"></i> In Wishlist';
            button.disabled = true;
            
            // Update wishlist count in header if it exists
            const wishlistCount = document.querySelector('.wishlist-count');
            if (wishlistCount) {
                const currentCount = parseInt(wishlistCount.textContent) || 0;
                wishlistCount.textContent = currentCount + 1;
            }
        } else {
            // If failed, re-enable the button and show specific error message
            button.disabled = false;
            let errorMessage = data.message || 'Failed to add item to wishlist.';
            if (data.debug) {
                console.error('Wishlist debug info:', data.debug);
                errorMessage += '\n\nDebug info has been logged to console.';
            }
            alert(errorMessage);
        }
    })
    .catch(error => {
        console.error('Wishlist error:', error);
        button.disabled = false;
        alert('An error occurred while adding to wishlist: ' + error.message);
    });
} 