YAHOO.util.Event.onDOMReady(function() {
	var Dom = YAHOO.util.Dom,
		IS_STARTED = false,
		DD_CLASSES = ['aq-ddcorrelation-label-answerholder', 'aq-ddcorrelation-answerholder', 'aq-ddcorrelation-dd-element', 'aq-ddcorrelation-answerholder-out'];
	
	function touchHandler(event) {
		if (event.type == 'touchstart') {
			IS_STARTED = false;

			if (!event["target"])
				return ;
	
			var el = event.target,
				found = false;

			for (var i = 0; i < DD_CLASSES.length; i++) {
				if (Dom.hasClass(el, DD_CLASSES[i]) || Dom.getAncestorByClassName(el, DD_CLASSES[i])) {
					found = true;
					break;
				}
			};
			
			if (!found)
				return ;

			IS_STARTED = true;
		} else if (!IS_STARTED) {
			return ;
		} else {
			if (event.type == 'touchend' || event.type == 'touchcancel')
				IS_STARTED = false;
		}

	    var touch = event.changedTouches[0];

	    var simulatedEvent = document.createEvent("MouseEvent");
	        simulatedEvent.initMouseEvent({
	        touchstart: "mousedown",
	        touchmove: "mousemove",
	        touchend: "mouseup"
	    }[event.type], true, true, window, 1,
	        touch.screenX, touch.screenY,
	        touch.clientX, touch.clientY, false,
	        false, false, false, 0, null);

	    touch.target.dispatchEvent(simulatedEvent);
	    event.preventDefault();
	};

    document.addEventListener("touchstart", touchHandler, true);
    document.addEventListener("touchmove", touchHandler, true);
    document.addEventListener("touchend", touchHandler, true);
    document.addEventListener("touchcancel", touchHandler, true);
});