document.addEventListener("DOMContentLoaded", function () {
  var activeLinkHref = sessionStorage.getItem("activeLinkHref");
  if (activeLinkHref !== null) {
    var links = document.querySelectorAll(".nav-link");
    links.forEach(function (link) {
      if (link.getAttribute("href") === activeLinkHref) {
        link.classList.add("active-link");
      }
    });
  } else {
    // If activeLinkHref is null (no link previously clicked), set it to the href of "Census" link by default
    function highlightLink(link, event) {
      event.preventDefault(); // Prevent default behavior of the anchor tag
      var links = document.querySelectorAll(".nav-link");
      links.forEach(function (item) {
        item.classList.remove("active-link"); // Remove active-link class from all links
      });
      link.classList.add("active-link"); // Add active-link class to the clicked link

      // Store the href of the clicked link in session storage
      sessionStorage.setItem("activeLinkHref", link.getAttribute("href"));
      console.log("Stored active link href:", link.getAttribute("href"));
    }
  }
  document.addEventListener("DOMContentLoaded", function () {
    var activeLinkHref = sessionStorage.getItem("activeLinkHref");
    console.log("Retrieved active link href:", activeLinkHref);
    if (activeLinkHref !== null) {
      var links = document.querySelectorAll(".nav-link");
      links.forEach(function (link) {
        if (link.getAttribute("href") === activeLinkHref) {
          link.classList.add("active-link");
        }
      });
    }
  });
});

// Attach click event listeners to the links
document.querySelectorAll(".nav-link").forEach(function (link) {
  link.addEventListener("click", function (event) {
    highlightLink(link, event);
  });
});
