document.addEventListener('DOMContentLoaded', function() {  // Código aqui só roda quando a página terminar de carregar
    const currentPage = window.location.pathname.split('/').pop(); // Exemplo: Se a URL for "http://site.com/sensor.html", currentPage = "sensor.html"
    const navLinks = document.querySelectorAll('.nav-link'); // Seleciona TODOS os links da navbar (array de elementos)
    
    navLinks.forEach(link => { // Executa isso para cada link encontrado
        if (link.getAttribute('href') === currentPage) {  // Se o link corresponder à página atual...
            link.classList.add('active'); // Adiciona a classe "active" ao link
            link.style.backgroundColor = '#5a7d51';  // Verde escuro
            link.style.borderLeft = '4px solid #fff'; // Borda branca à esquerda
        }
    });
});