// Queue page specific JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Update queue status every 5 seconds
    function updateQueueStatus() {
        fetch('api/queue_status.php')
            .then(response => response.json())
            .then(data => {
                const currentServing = document.getElementById('current-serving');
                const yourPosition = document.getElementById('your-position');

                if (currentServing && data.current_serving) {
                    currentServing.textContent = `Customer #${data.current_serving.queue_number}`;
                }

                if (yourPosition && data.user_queue) {
                    yourPosition.textContent = `#${data.user_queue.queue_number} - ${data.user_queue.status}`;
                }
            })
            .catch(error => console.error('Error updating queue:', error));
    }

    // Initial update
    updateQueueStatus();

    // Update every 5 seconds
    setInterval(updateQueueStatus, 5000);
});