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
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
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
            alert(data.message || 'Failed to add item to cart. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        alert('An error occurred while adding to cart. Please try again.');
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
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
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
            alert(data.message || 'Failed to add item to wishlist. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        alert('An error occurred while adding to wishlist. Please try again.');
    });
} 