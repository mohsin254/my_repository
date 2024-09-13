<?php
include 'connect.php';

// Fetch aggregated sales data
$sales_sql = "SELECT date, SUM(total_amount) as total_amount FROM sales GROUP BY date ORDER BY date ASC";
$sales_result = $conn->query($sales_sql);

$sales_dates = [];
$sales_amounts = [];

while ($row = $sales_result->fetch_assoc()) {
    $sales_dates[] = $row['date'];
    $sales_amounts[] = $row['total_amount'];
}

// Fetch aggregated purchase data
$purchase_sql = "SELECT date, SUM(total_amount) as total_amount FROM purchase GROUP BY date ORDER BY date ASC";
$purchase_result = $conn->query($purchase_sql);

$purchase_dates = [];
$purchase_amounts = [];

while ($row = $purchase_result->fetch_assoc()) {
    $purchase_dates[] = $row['date'];
    $purchase_amounts[] = $row['total_amount'];
}

// Encode data to pass to JavaScript
$sales_dates_json = json_encode($sales_dates);
$sales_amounts_json = json_encode($sales_amounts);
$purchase_dates_json = json_encode($purchase_dates);
$purchase_amounts_json = json_encode($purchase_amounts);
?>


<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
  <meta charset="UTF-8">
  <title>IEW</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/date-fns@2.30.0"></script>

  <style>
    .container-fluid {
      color: black;
      padding: 20px;
    }
    #myChart {
      max-width: 100%;
      height: 500px;
    }
  </style>
</head>

<body>
  <?php include 'header.php'; // Include the navigation ?>

  <div class="container-fluid">
    <canvas id="myChart"></canvas>
  </div>

  <?php include 'footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>

  <script>
  // Fetch data from PHP
  const salesDates = <?php echo $sales_dates_json; ?>;
  const salesAmounts = <?php echo $sales_amounts_json; ?>;
  const purchaseDates = <?php echo $purchase_dates_json; ?>;
  const purchaseAmounts = <?php echo $purchase_amounts_json; ?>;

  // Combine and sort unique dates
  const allDates = [...new Set([...salesDates, ...purchaseDates])].sort();

  // Create a map for fast lookups
  const salesMap = salesDates.reduce((acc, date, index) => {
      acc[date] = salesAmounts[index];
      return acc;
  }, {});

  const purchaseMap = purchaseDates.reduce((acc, date, index) => {
      acc[date] = purchaseAmounts[index];
      return acc;
  }, {});

  // Map each date to aggregated sales and purchases amounts
  const salesData = allDates.map(date => salesMap[date] || 0);
  const purchaseData = allDates.map(date => purchaseMap[date] || 0);

  // Format the dates for display
  const formatDate = (date) => {
      const [year, month, day] = date.split('-');
      return `${day}-${month}-${year.slice(2)}`;
  };

  const formattedDates = allDates.map(formatDate);

  // Create a Chart.js line chart
  const ctx = document.getElementById('myChart').getContext('2d');
  const myChart = new Chart(ctx, {
      type: 'line',
      data: {
          labels: formattedDates,
          datasets: [
              {
                  label: 'Sales',
                  data: salesData,
                  borderColor: 'green',
                  backgroundColor: 'rgba(0, 128, 0, 0.2)',
                  fill: true,
                  tension: 0.1
              },
              {
                  label: 'Purchases',
                  data: purchaseData,
                  borderColor: 'red',
                  backgroundColor: 'rgba(255, 0, 0, 0.2)',
                  fill: true,
                  tension: 0.1
              }
          ]
      },
      options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
              legend: {
                  display: true,
                  position: 'top',
              }
          },
          scales: {
              x: {
                  title: {
                      display: true,
                      text: 'Date'
                  },
                  ticks: {
                      autoSkip: true,
                      maxTicksLimit: 20
                  }
              },
              y: {
                  title: {
                      display: true,
                      text: 'Amount (Rs)'
                  },
                  beginAtZero: true
              }
          }
      }
  });
</script>

</body>
</html>
