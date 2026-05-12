// Queue page specific JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const yourPosition = document.getElementById('your-position');
    const lastStatus = yourPosition ? yourPosition.getAttribute('data-status') || 'none' : 'none';
    let currentStatus = lastStatus;

    function updateQueueStatus() {
        fetch('api/queue_status.php')
            .then(response => response.json())
            .then(data => {
                if (!data.user_queue) {
                    if (currentStatus !== 'none') {
                        window.location.reload();
                    }
                    currentStatus = 'none';
                    return;
                }

                const orderStatus = data.user_queue.order_status || data.user_queue.status;
                if (!orderStatus) {
                    return;
                }

                if (orderStatus !== currentStatus) {
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error updating queue:', error));
    }

    updateQueueStatus();
    setInterval(updateQueueStatus, 5000);
});