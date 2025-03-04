var ARIElementGroups = new Class({
	options: {
		selectId: null,
		groupClass: 'el-group'
	},

	initialize: function(id, options) {
		this.id = id;
		this.setOptions(options);
		
		var self = this;
		$(this.options.selectId).addEvent('change', (function(event) {
			var ctrl = event.target;

			var groupEl = $(self.id).getElement('.' + self.options.groupClass);
			groupEl.setStyle('display', 'none');
			while ((groupEl = groupEl.getNext()))
			{
				if (groupEl.hasClass(self.options.groupClass))
					groupEl.setStyle('display', 'none');
			}
			$('group_' + self.options.selectId + '_' + ctrl.value).setStyle('display', 'block');
		}).bind(this));
		
		if (typeof(jQuery) != 'undefined' && typeof(jQuery.fn.chosen) != 'undefined')
			jQuery('#' + this.options.selectId).chosen().change(function(e, data) {
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
	}
});
ARIElementGroups.implement(new Options);