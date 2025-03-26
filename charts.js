// Configuração inicial dos gráficos (dados mockados para demonstração)
document.addEventListener('DOMContentLoaded', function() {
    // Elementos para integração futura com Power BI
    const powerBiConfig = {
        embedUrl: "", // Será preenchido quando conectar ao Power BI
        reportId: "",
        accessToken: ""
    };

    // Configuração comum para todos os gráficos
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                mode: 'index',
                intersect: false,
            }
        },
        scales: {
            y: {
                beginAtZero: false
            }
        }
    };

    // Gráfico de Temperatura
    const tempCtx = document.getElementById('tempChart').getContext('2d');
    const tempChart = new Chart(tempCtx, {
        type: 'line',
        data: {
            labels: generateTimeLabels(24),
            datasets: [{
                label: 'Temperatura (°C)',
                data: generateRandomData(24, 15, 30),
                borderColor: '#ff6384',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: chartOptions
    });

    // Gráfico de Umidade
    const humidityCtx = document.getElementById('humidityChart').getContext('2d');
    const humidityChart = new Chart(humidityCtx, {
        type: 'line',
        data: {
            labels: generateTimeLabels(24),
            datasets: [{
                label: 'Umidade (%)',
                data: generateRandomData(24, 40, 80),
                borderColor: '#36a2eb',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: chartOptions
    });

    // Gráfico de Luminosidade
    const lightCtx = document.getElementById('lightChart').getContext('2d');
    const lightChart = new Chart(lightCtx, {
        type: 'line',
        data: {
            labels: generateTimeLabels(24),
            datasets: [{
                label: 'Luminosidade (Lux)',
                data: generateRandomData(24, 0, 100),
                borderColor: '#ffcd56',
                backgroundColor: 'rgba(255, 205, 86, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: chartOptions
    });

    // Gráfico de Nível de Água
    const waterCtx = document.getElementById('waterChart').getContext('2d');
    const waterChart = new Chart(waterCtx, {
        type: 'line',
        data: {
            labels: generateTimeLabels(24),
            datasets: [{
                label: 'Nível de Água (%)',
                data: generateRandomData(24, 20, 100),
                borderColor: '#4bc0c0',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: chartOptions
    });

    // Funções auxiliares para dados de demonstração
    function generateTimeLabels(hours) {
        return Array.from({length: hours}, (_, i) => {
            const date = new Date();
            date.setHours(date.getHours() - hours + i);
            return date.getHours() + 'h';
        });
    }

    function generateRandomData(count, min, max) {
        return Array.from({length: count}, () => 
            Math.floor(Math.random() * (max - min + 1)) + min
        );
    }

    // Controle dos filtros de tempo
    document.querySelectorAll('.time-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.time-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Aqui você implementaria a mudança de período nos gráficos
            // Quando conectar ao Power BI, isso atualizaria os dados do relatório
            console.log('Período selecionado:', this.dataset.period);
        });
    });
});