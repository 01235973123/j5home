(function($) {	
	ARIElementGroups = function(id, options) {
		this.id = id;

		this.options = $.extend({
			selectId: null,
			groupClass: 'el-group'
		}, options);

		this.initialize();
	}

	ARIElementGroups.prototype.initialize = function() {		
		var self = this;
		$('#' + this.options.selectId).on('change', (function(event) {
			var ctrl = event.target;

			var groupEl = $('.' + self.options.groupClass, '#' + self.id);
			groupEl.css({'display': 'none'});
			$('#group_' + self.options.selectId + '_' + ctrl.value).css({'display': 'block'});

			// self.fixParentSize();
		}).bind(this));

		if (typeof(jQuery) != 'undefined' && typeof(jQuery.fn.chosen) != 'undefined')
			$('#' + this.options.selectId).chosen().change(function(e, data) {
				var val = data.selected,
					groupEl = $(self.id).getElement('.' + self.options.groupClass);
				groupEl.setStyle('display', 'none');
				while ((groupEl = groupEl.getNext()))
				{
					if (groupEl.hasClass(self.options.groupClass))
						groupEl.setStyle('display', 'none');
				}
				$('group_' + self.options.selectId + '_' + val).setStyle('display', 'block');
			});
	};

	ARIElementGroups.prototype.fixParentSize = function() {
		var parentSlide = $(this.options.selectId).parent();
		while (parentSlide && !parentSlide.hasClass('jpane-slider')) {
			parentSlide = parentSlide.parent();
			if (parentSlide && typeof(parentSlide.hasClass) == "undefined")
				return ;
		}

		if (parentSlide) {
			var height = $(parentSlide.firstChild).height();
			parentSlide.css('height', height + 'px');
		}
	}
})(jQuery);