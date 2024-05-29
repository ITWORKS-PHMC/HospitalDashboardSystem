<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Roboto&display=swap");

        /* Style for active link */
   

        body {
            width: 100vw !important;
            margin-top: 20px !important;
            margin: 0 auto !important;
            font-family: "Roboto", sans-serif !important;
            background-color: #f5f5f5 !important;
            overflow-x: hidden;
        }

        .navigation-bar {
            width: 100% !important;
            height: 60px !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 0 20px !important;
            /* background-color: #ffffff !important; */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }

        .header-content {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            width: 100vw !important;
        }

        .header-title {
            font-family: "Roboto", sans-serif !important;
            font-size: 1.6rem !important;
            font-weight: bold !important;
            color: #333333 !important;
            margin-right: 20px !important;
        }

        .header-sections {
            display: flex !important;
            font-family: "Roboto", sans-serif !important;
            font-size: 1.4rem !important;
            margin-left: auto; 
            margin-right: 50px;
        }
          .nav-buttonCensus {
              background-color:#32CD32;
              color: black;
              font-weight: bold;
              border: none;
              border-radius: 5px;
              padding: 10px 20px;
              cursor: pointer;
              font-size: 16px;
              transition: background-color 0.3s, color 0.3s;
              margin-right: 10px;
          }

          .nav-buttonCensus:hover {
              background: linear-gradient(135deg, #00b38f, #3cc2b7);
          }
          .nav-buttonRevenue {
              background-color:#32CD32;
              font-weight: bold;
              color: black;
              border: none;
              border-radius: 5px;
              padding: 10px 20px;
              cursor: pointer;
              font-size: 16px;
              transition: background-color 0.3s, color 0.3s;
              margin-right: 10px;
          }

          .nav-buttonRevenue:hover {
              background: linear-gradient(135deg, #00b38f, #3cc2b7);
          }
    </style>
</head>

<body>
<!-- Navigation Menu -->
<div class="navigation-bar">
    <div class="header-content">
        <div class="header-title">
            <p>HOSPITAL DASHBOARD</p>
        </div>
        <div class="header-sections">
            <button onclick="location.href='dashboard.php'" class="nav-buttonCensus">CENSUS</button>
            <button onclick="location.href='revenue-dashboard.php'" class="nav-buttonRevenue">REVENUE</button>

        </div>
    </div>
</div>
</body>
</html>
