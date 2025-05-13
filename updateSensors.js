const apiBaseUrl = "http://localhost:88/aula3004/api/leituras";

async function fetchLatestSensorData() {
    try {
        const response = await fetch(`${apiBaseUrl}`);
        if (!response.ok) {
            throw new Error(`Erro ao buscar dados: ${response.status}`);
        }
        const data = await response.json();

        console.log(data)

        // Pega o último registro do array
        const latestData = data[data.length - 1];
        console.log(latestData);

        updateSensorDisplay(latestData);
    } catch (error) {
        console.error("Erro ao buscar os dados mais recentes:", error);
    }
}

function updateSensorDisplay(data) {
    const sensorDataElements = document.querySelectorAll(".current-value");
    if (sensorDataElements.length >= 4) {
        sensorDataElements[0].textContent = `${data.luminosidade}%`; // Luminosidade
        sensorDataElements[1].textContent = `${data.temperatura}°C`; // Temperatura
        sensorDataElements[2].textContent = `${data.umidade}%`; // Umidade
        sensorDataElements[3].textContent = `${data.agua}%`; // Nível de Água
    } else {
        console.error("Elementos .sensor-data não encontrados no DOM.");
    }
}

document.addEventListener("DOMContentLoaded", fetchLatestSensorData);