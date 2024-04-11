<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preventive Maintenance</title>
    <link rel="stylesheet" href="style.css">
    
    
</head>
<!----Loading Menus--->
<script>
function loadContent(url) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("LoadMenus").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", url, true);
  xhttp.send();
}
  </script>
<body>
  <!----Navigation Menu------->
<div class="navigation-bar">
  <div class="header-content">
    <div class="header-title">
      <p>Hospital Dashboard</p>
    </div>
    <div class="header-sections">
      <a href="dashboard.php" class="nav-link" >Dashboard</a>
      <a href="scheduling.php" class="nav-link">Scheduling</a>
      <a href="files.php" class="nav-link" >Files</a>
      <a href="ticketing.php" class="nav-link" >Ticketing</a>
      <a href="history.php" class="nav-link" >History</a>
      <a href="history.php" class="nav-link" >User</a>
    </div>
    <div>

    </div>
  </div>
</div>
<div id="LoadMenus">
</div>
</body>
</html>