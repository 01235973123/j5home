(function($) {
    var HAS_TIP_CLASS = 'hasTip';

	$(document).ready(function() {
		$('.ari-quiz-container .control-label>LABEL').each(function() {
			var $label = $(this);
            var withTooltip = $label.hasClass(HAS_TIP_CLASS);

            if (!withTooltip) {
                return;
            }

            var title = $label.attr('title');

			$label.attr('data-bs-toggle', 'tooltip');

            if (title) {
                title = title.replace(/^[^:]*::/, '');
                $label.attr('title', title);
            }
		});

		new bootstrap.Popover(document.body, {
			selector: '.' + HAS_TIP_CLASS,
			trigger: 'hover focus',
			html: true,
		});
	});
})(jQuery);