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
        .active-link {
            color: #27b339 !important;
        }
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
        background-color: #ffffff !important;
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
        margin-left: auto; /* Move to the right */
        margin-right: 50px;
    }

    .nav-link {
        text-decoration: none !important;
        color: #333333 !important;
        font-weight: bold !important;
        transition: color 0.2s ease !important;
        display: flex;
        align-items: center;
        padding: 8px 15px;
        border-radius: 5px;
    }

    .nav-link:hover {
        color: #5de65d !important;
        background-color: #27b339; /* Green background on hover */
    }
</style>
    </style>
</head>

<body>
  <!----Navigation Menu------->
<div class="navigation-bar">
  <div class="header-content">
    <div class="header-title">
      <p>HOSPITAL DASHBOARD</p>
    </div>
    <div class="header-sections">
      <a href="dashboard.php" class="nav-link" onclick="highlightLink(this)">Census</a>
      <a href="revenue-dashboard.php" class="nav-link" onclick="highlightLink(this)">Revenue</a>
    </div>
  </div>
</div>

<script>
function highlightLink(link) {
    // Remove 'active-link' class from all links
    var links = document.querySelectorAll('.nav-link');
    links.forEach(function(item) {
        item.classList.remove('active-link');
    });
    // Add 'active-link' class to the clicked link
    link.classList.add('active-link');
}
</script>

</body>
</html>