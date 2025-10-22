// Banque de Sang Platform JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });
    
    // Blood stock status indicators
    const bloodStockElements = document.querySelectorAll('.blood-stock-item');
    bloodStockElements.forEach(element => {
        const count = parseInt(element.querySelector('.stock-count').textContent);
        const statusElement = element.querySelector('.stock-status');
        
        if (count < 10) {
            statusElement.classList.add('text-critical');
            statusElement.textContent = 'Critique';
        } else if (count < 30) {
            statusElement.classList.add('text-low');
            statusElement.textContent = 'Faible';
        } else if (count < 50) {
            statusElement.classList.add('text-moderate');
            statusElement.textContent = 'Modéré';
        } else {
            statusElement.classList.add('text-sufficient');
            statusElement.textContent = 'Suffisant';
        }
    });
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Dashboard charts (would be implemented with Chart.js in a real scenario)
    console.log('BloodLink platform loaded successfully');
});

// AJAX functions for dynamic content loading
function loadBloodStock() {
    // This would fetch blood stock data from the server
    console.log('Loading blood stock data...');
    // In a real implementation, this would make an AJAX request to get data
}

function updateRequestStatus(requestId, status) {
    // This would update the status of a blood request
    console.log(`Updating request ${requestId} to status: ${status}`);
    // In a real implementation, this would make an AJAX request to update the database
}

// Utility functions
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
}

function showNotification(message, type = 'info') {
    // Create and show a notification
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.notification-container').appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}