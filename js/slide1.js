document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.querySelector('.navbar-toggler');
    const sidebar = document.getElementById('sidebar');
    toggleButton.addEventListener('click', () => {
        sidebar.classList.toggle('show');
    });
});
