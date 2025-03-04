;ARIQuestionCategoryElement = {
	categories: {},
	
	initEl: function(selId, section, parentElId) {
		YAHOO.util.Event.onDOMReady(function() {
			if (parentElId) {
				var selQuiz = YAHOO.util.Dom.get(section + parentElId);
				
				YAHOO.util.Event.on(selQuiz, 'change', function() {
					ARIQuestionCategoryElement.populate(selId, selQuiz.value);
					
					YAHOO.util.Event.removeListener(selId, 'click');
				});
			};
			
			YAHOO.util.Event.on(selId, 'click', function() {
				var selQuiz = YAHOO.util.Dom.get(section + parentElId);

				ARIQuestionCategoryElement.populate(selId, selQuiz.value);
				
				YAHOO.util.Event.removeListener(selId, 'click');
			});
		});
	},
		
	populate: function(selEl, quizId) {
		if (typeof(this.categories[quizId]) != "undefined") {
			this.bind(selEl, this.categories[quizId]);
			return ;
		};
		
		var self = this;
		selEl = YAHOO.util.Dom.get(selEl);
		selEl.disabled = true;
		YAHOO.ARISoft.ajax.ajaxManager.asyncRequest(
			'GET',
			'index.php?option=com_ariquiz&view=element&task=ajaxExecute&element=questioncategory&action=getCategoryList&quizId=' + quizId, 
			{
				cache: false,
				
				success: function(oResponse) {
					var categories = null;
					try {
						var responseText = oResponse.responseText;
						categories = YAHOO.lang.JSON.parse(responseText);
					} catch (e) {};

					this.bind(selEl, categories);

					selEl.disabled = false;
					this.categories[quizId] = categories;
				},
				
				failure: function(oResponse) {
					selEl.disabled = false;
					//YAHOO.ARISoft.page.pageManager.sendInfoMessage('fail');
				},
				
				scope: self
			},
			null,
			null,
			{
				containerId: selEl,
				loadingMessage: '<div class="ari-loading width100">' + YAHOO.ARISoft.languageManager.getMessage('COM_ARIQUIZ_LOADING') + '</div>',
				overlayCfg: { 
					visible:false, 
					constraintoviewport:true, 
					close: false,
					draggable: false,
					autofillheight: 'body',
					zIndex: 10000
				}
			}
		);
	},
	
	bind: function(selEl, categories) {
		if (!selEl)
			return ;

		selEl = YAHOO.util.Dom.get(selEl);
		selEl.options.length = 0;
		if (categories) {
			for (var i = 0, cnt = categories.length; i < cnt; i++) {
				var category = categories[i],
					opt = new Option(category.CategoryName, category.QuestionCategoryId);
				selEl.options[selEl.options.length] = opt;
			}
		};
	}
};