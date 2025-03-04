(function (document, Joomla) {
	document.addEventListener('DOMContentLoaded', function () {
		var calendarEl = document.getElementById('eb_full_calendar');
		var calendarOptions = Joomla.getOptions('calendarOptions');

		if (Joomla.getOptions('displayEventInTooltip')) {
			calendarOptions['eventDidMount'] = function (arg) {
				if (arg.event.extendedProps.tooltip) {
					var element = jQuery(arg.el);
					element.tooltip({
						title: arg.event.extendedProps.tooltip,
						trigger: 'hover',
						placement: 'top',
						container: 'body',
						html: true,
						sanitize: false
					});
				}

				if (arg.event.extendedProps.eventFull) {
					var eventContainerEl = arg.el.querySelector('.fc-event-title');

					if (eventContainerEl) {
						eventContainerEl.classList.add('eb-event-full');
					}
				}
			}
		}

		var calendar = new FullCalendar.Calendar(calendarEl, calendarOptions);
		calendar.render();

		if (Joomla.getOptions('showFilterBar')) {
			document.getElementById('btn-apply-calendar-filter').addEventListener('click', function (event) {
				calendar.getEventSources().forEach(function (source) {
					source.remove();
				});

				var url = calendarOptions.eventSources[0];
				var categoryFilter = document.getElementById('filter_category_id');
				var locationFilter = document.getElementById('filter_location_id');
				var search = document.getElementById('filter_search');


				if (categoryFilter && categoryFilter.value > 0) {
					url = appendToUrlWithEncoding(url, 'id', categoryFilter.value);
				}

				if (locationFilter && locationFilter.value > 0) {
					url = appendToUrlWithEncoding(url, 'filter_location_id', locationFilter.value);
				}

				if (search.value.length > 0) {
					url = appendToUrlWithEncoding(url, 'search', search.value);
				}

				calendar.addEventSource({
					url: url
				});

				calendar.refetchEvents();
			});
		}

		function appendToUrlWithEncoding(url, param, value) {
			// Encode the parameter and value to make them URL-safe
			var encodedParam = encodeURIComponent(param);
			var encodedValue = encodeURIComponent(value);

			// Check if the URL already has parameters
			var separator = url.includes('?') ? '&' : '?';

			return url + separator + encodedParam + '=' + encodedValue;
		}
	});
})(document, Joomla);