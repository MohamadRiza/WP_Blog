function docReady(fn) {
  // see if DOM is already available
  if (
    document.readyState === "complete" ||
    document.readyState === "interactive"
  ) {
    // call on next available tick
    setTimeout(fn, 1);
  } else {
    document.addEventListener("DOMContentLoaded", fn);
  }
}

docReady(function () {

	document.body.addEventListener("click", function (event) {
    // Check if the clicked element has the specific class
    if (
      event.target &&
      ( event.target.classList.contains("sb-elementor-cta-link") )
    ) {
      const href = event.target.getAttribute("href");
      window.open(href, "_blank");
    }


  });




});