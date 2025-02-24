(function (document) {
	document.addEventListener('DOMContentLoaded', function () {
		var form = document.getElementById('eb-event-search');

		if (!form) {
			return;
		}

		form.querySelector('#eb-search-filters-reset-button').addEventListener('click', function () {
			form.search.value = '';
			var locationDropdown = form.elements['location_id'];
			var filterDuration = form.elements['filter_duration'];
			var filterCategory = form.elements['category_id'];

			if (locationDropdown) {
				locationDropdown.value = '0';
			}

			if (filterDuration) {
				filterDuration.value = '';
			}

			if (filterCategory) {
				filterCategory.value = '0';
			}
			
			form.submit();
		});
	});
})(document);