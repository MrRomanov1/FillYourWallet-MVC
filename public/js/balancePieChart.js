var ctxP = document.getElementById("pieChartBalance").getContext('2d');
var myPieChart = new Chart(ctxP, {
    type: 'pie',
    data: {
    labels: [{% for value in balance.expenseTotal %}{{ (value['name']|json_encode|raw) }}{{','}}{% endfor %}],
    datasets: [{
    data: [{% for value in balance.expenseTotal %}{{ (value['ROUND(SUM(expenses.amount), 2)'])}}{{','}}{% endfor %}],
    backgroundColor: ["#c858d0", "#9c46d0", "#e01e85", "#ff7300", "#ffae00", "#ffee00", "#52d726", "#1baa2e", "#2dcb74", "#97d9ff"],
    hoverBackgroundColor: ["#b747c0", "#8b35c0", "#d00d74", "#ee6200", "#eead00", "#eedd00", "#41c615", "#0aaa1d", "#2cba63", "#86c8ee"]
    }]
    },
    options: {
    responsive: true
    }
});
 