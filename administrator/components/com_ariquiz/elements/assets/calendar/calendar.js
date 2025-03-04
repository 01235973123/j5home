;function ARICalendarElement(containerId, prefix, id, val) {
	var Event = YAHOO.util.Event,
		Dom = YAHOO.util.Dom;
	
	Event.onDOMReady(function() {
		var ddlHour = Dom.get('ddlStartHour' + prefix),
			ddlMinute = Dom.get('ddlStartMinute' + prefix),
			hidCtrl = Dom.get(id),
			cal = new YAHOO.ARISoft.widgets.Calendar(
				prefix, 
				prefix + "Container", 
				{
					selected: val,
					close:false,
					iframe:false,
					dateElement: "tbx" + prefix,
					hiddenDateElement: id
				},
				{
					context: ["tbx" + prefix, "tl", "br", null, [0, -10]]
				},
				prefix + "Holder"
			),
			dateHandler = function() {
				var ts = cal.getTimeStamp();
				if (ts == 0)
					return ;
			
				var time = 0;
			
				if (ddlHour)
					time += parseInt(ddlHour.value, 10) * 60 * 60;
				
				if (ddlMinute)
					time += parseInt(ddlMinute.value, 10) * 60;
				
				hidCtrl.value = (ts + time);
			};
		YAHOO.ARISoft.page.pageManager.addControl(prefix, id, cal);
		
		cal.selectEvent.subscribe(function() {
			dateHandler();
		});
		cal.clearEvent.subscribe(function() {
			if (ddlHour)
    			ddlHour.value = "0";
    		if (ddlMinute)
    			ddlMinute.value = "0";
		});

		if (ddlHour)
			Event.on(ddlHour, "change", function() {
				dateHandler();
			});
		if (ddlMinute)
			Event.on(ddlMinute, "change", function() {
				dateHandler();
			});

		Dom.getElementsByClassName("ari-date-reset", null, containerId, function(el) {
			Event.on(el, "click", function() {
				cal.deselectAll();
	    		cal.clear();
	    		
	    		if (ddlHour)
	    			ddlHour.value = "0";
	    		if (ddlMinute)
	    			ddlMinute.value = "0";
			});
		});
	});
};