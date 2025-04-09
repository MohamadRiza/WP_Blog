const positionCalloutContainer = (leave = false) => {
	const calloutCtn = window.document.querySelectorAll('.sb-callout-ctn[data-type="side-menu"]');
	if (calloutCtn[0]) {
		const calloutCtnRect = calloutCtn[0].getBoundingClientRect();
		let calloutCtnRectY = calloutCtnRect.y,
			positionY = calloutCtnRectY + calloutCtnRect.height >= window.innerHeight;


		if (positionY && !leave) {
			calloutCtn[0].style.marginTop =  -1 * ((calloutCtnRectY + calloutCtnRect.height) - window.innerHeight) +"px"
		} else {
			calloutCtn[0].style.marginTop = "0px"
		}

	}
}
window.onload = () => {
	positionCalloutContainer()
	window.addEventListener("resize", (event) => {
		positionCalloutContainer()
	});

	if(document.getElementById("toplevel_page_sb-facebook-feed")) {
		document.getElementById("toplevel_page_sb-facebook-feed").addEventListener("mouseenter", (event) => {
			if (!document.body.classList.contains('index-php')) {
				positionCalloutContainer()
			}
		});
		document.getElementById("toplevel_page_sb-facebook-feed").addEventListener("mouseleave", (event) => {
			if (!document.body.classList.contains('index-php')) {
				positionCalloutContainer(true)
			}
		});
	}
}