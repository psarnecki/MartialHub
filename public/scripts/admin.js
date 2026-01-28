document.addEventListener('DOMContentLoaded', () => {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();

            const url = this.getAttribute('href');
            const row = this.closest('tr');

            if (!confirm('Are you sure you want to delete this user?')) {
                return;
            }

            try {
                const response = await fetch(url, {
                    method: 'GET'
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    row.style.transition = 'opacity 0.4s ease';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 400);
                } else {
                    alert('Error: Could not delete user.');
                }
            } catch (error) {
                console.error('Fetch error:', error);
                alert('An error occurred while deleting the user.');
            }
        });
    });
});