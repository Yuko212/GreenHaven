// Adicione no final do body ou em um arquivo JS separado
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
            // Adicione um indicador visual
            link.style.backgroundColor = '#5a7d51';
            link.style.borderLeft = '4px solid #fff';
        }
    });
});