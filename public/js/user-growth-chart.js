/**
 * User Growth Chart
 * Initializes Chart.js for admin dashboard user growth visualization
 */
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const ctx = document.getElementById('usersGrowthChart');
    if (!ctx) {
        return;
    }

    // Get data from data attributes
    const dataElement = document.getElementById('chartDataContainer');
    let labels = [];
    let values = [];

    if (dataElement) {
        labels = JSON.parse(dataElement.dataset.labels || '[]');
        values = JSON.parse(dataElement.dataset.values || '[]');
    }

    // If no data provided, use empty dataset
    if (!labels.length || !values.length) {
        labels = [];
        values = [];
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'New users',
                data: values,
                borderColor: '#9810FA',
                backgroundColor: 'rgba(152,16,250,0.08)',
                tension: 0.3,
                fill: true,
                pointRadius: 3,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
});
