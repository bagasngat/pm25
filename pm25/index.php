<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realtime Data Monitor from PM2.5 Sensor</title>
    <style>
    body {
        font-family: sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    header {
        background-color: #007bff;
        color: white;
        padding: 15px 0;
        text-align: center;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    .container {
        max-width: 900px;
        margin: auto;
        padding: 20px;
        flex: 1;
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 28px;
    }

    table {
        width: 100%;
        border-spacing: 0px;
        margin-bottom: 30px;
        border-collapse: collapse;
    }

    table th,
    table td {
        padding: 15px;
        text-align: center;
        border: 1px solid #cacaca;
    }

    table th {
        background-color: #343a40;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 14px;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    table th:first-child {
        border-radius: 8px 0 0 0;
    }

    table th:last-child {
        border-radius: 0 8px 0 0;
    }

    table th,
    table td {
        box-shadow: inset 0px -1px 0px #dee2e6;
    }

    footer {
        background-color: #f8f9fa;
        color: #6c757d;
        text-align: center;
        padding: 15px 0;
        margin-top: auto;
        border-top: 1px solid #e9ecef;
    }

    td:last-child {
        width: auto;
    }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <h2>Realtime Data Monitor from PM2.5 Sensor</h2>
    </header>

    <!-- Main Content -->
    <div class="container">
        <table class="center">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>PM2.5 Sensor</th>
                    <th>Status Udara</th>
                    <th>Status Kipas</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Nama Mahasiswa.</p>
    </footer>

    <!-- Real-time Data Fetching Script -->
    <script>
    var source = new EventSource("get_data.php");
    source.onmessage = function(event) {
        var arrayData = JSON.parse(event.data);
        var dataContainer = document.querySelector('tbody')
        dataContainer.innerHTML = ''
        arrayData.forEach(e => {
            dataContainer.innerHTML += `
                <tr>
                    <td>${e.WAKTU}</td>
                    <td>${e.PM25} ug/m3</td>
                    <td>${e.STATUS_UDARA}</td>
                    <td>${e.FAN}</td>
                </tr>
            `;
        });
    }
    </script>
</body>

</html>