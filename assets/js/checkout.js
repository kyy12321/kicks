document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('process_checkout.php', {
            method: 'POST',
            body: formData
        });
        
        if (response.redirected) {
            window.location.href = response.url;
            return;
        }

        const data = await response.json();
        
        if (data.success) {
            showOrderComplete();
            document.getElementById('cartSection').style.display = 'none';
            document.getElementById('checkoutSection').style.display = 'none';
        } else {
            showNotification(data.message || 'Order failed', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('An error occurred while processing your order', 'error');
    }
});

function showNotification(message, type = 'success') {
    // Existing notification code
}