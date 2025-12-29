/* Barista Logic */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Barista Dashboard Loaded');

    // Auto refresh every 60 seconds to check for new orders
    setInterval(function() {
        window.location.reload();
    }, 60000);
});
