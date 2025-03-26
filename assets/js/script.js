document.addEventListener('DOMContentLoaded', () => {
    // Theme toggle functionality
    const themeToggle = document.querySelector('.theme-toggle');
    const body = document.body;
    const icon = themeToggle.querySelector('i');
    
    // Check for saved theme preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
    }
    
    // Toggle theme
    themeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        
        if (body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        } else {
            localStorage.setItem('theme', 'light');
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
        }
    });
    
    // Connection form submission
    const connectionForm = document.getElementById('connection-form');
    if (connectionForm) {
        connectionForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const formData = new FormData(connectionForm);  {
            e.preventDefault();
            
            const formData = new FormData(connectionForm);
            
            // Show loading state
            const submitButton = connectionForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
            submitButton.disabled = true;
            
            // Clear previous results
            const resultContainer = document.createElement('div');
            resultContainer.className = 'connection-result';
            const existingResult = document.querySelector('.connection-result');
            if (existingResult) {
                existingResult.remove();
            }
            
            // Send AJAX request
            fetch('test-connection.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Create result message
                if (data.success) {
                    resultContainer.className = 'connection-result success';
                    resultContainer.innerHTML = `
                        <div class="status-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="status-content">
                            <h3>Connection Successful</h3>
                            <p>${data.message}</p>
                            ${data.server_info ? `<p>Server: ${data.server_info}</p>` : ''}
                        </div>
                    `;
                } else {
                    resultContainer.className = 'connection-result error';
                    resultContainer.innerHTML = `
                        <div class="status-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="status-content">
                            <h3>Connection Failed</h3>
                            <p>${data.message}</p>
                        </div>
                    `;
                }
                
                // Add result to page
                connectionForm.after(resultContainer);
                
                // Scroll to result
                resultContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            })
            .catch(error => {
                resultContainer.className = 'connection-result error';
                resultContainer.innerHTML = `
                    <div class="status-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="status-content">
                        <h3>Error</h3>
                        <p>An unexpected error occurred: ${error.message}</p>
                    </div>
                `;
                connectionForm.after(resultContainer);
            })
            .finally(() => {
                // Restore button state
                submitButton.innerHTML = originalButtonText;
                submitButton.disabled = false;
            });\
        });
    }
    
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

