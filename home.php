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
      <h1>Hospital Dashboard</h1>
    </div>
  </div>
</div>
<div id="LoadMenus">
</div>
</body>
</html>