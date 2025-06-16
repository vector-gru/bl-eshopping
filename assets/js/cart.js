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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button state
            button.classList.remove('btn-warning');
            button.classList.add('btn-success');
            button.textContent = 'In the Cart';
            button.disabled = true;
            
            // Update cart count in header if it exists
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                const currentCount = parseInt(cartCount.textContent) || 0;
                cartCount.textContent = currentCount + 1;
            }
        } else {
            // If failed, re-enable the button
            button.disabled = false;
            alert('Failed to add item to cart. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        alert('An error occurred. Please try again.');
    });
} 