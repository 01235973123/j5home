;ARIQuizElement = {
	quizzes: null,
	
	initEl: function(selId) {
		YAHOO.util.Event.on(selId, 'click', function() {
			ARIQuizElement.populate(selId);
			
			YAHOO.util.Event.removeListener(selId, 'click');
		}, this, true);
	},
		
	populate: function(selEl) {
		if (this.quizzes) {
			this.bind(selEl, this.quizzes);
			return ;
		};
		
		var self = this;
		selEl = YAHOO.util.Dom.get(selEl);
		selEl.disabled = true;
		YAHOO.ARISoft.ajax.ajaxManager.asyncRequest(
			'GET',
			'index.php?option=com_ariquiz&view=element&task=ajaxExecute&element=quiz&action=getQuizList', 
			{
				cache: false,
				
				success: function(oResponse) {
					var quizzes = null;
					try {
						var responseText = oResponse.responseText;
						quizzes = YAHOO.lang.JSON.parse(responseText);
					} catch (e) {};

					this.bind(selEl, quizzes);

					selEl.disabled = false;
					this.quizzes = quizzes;
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
	
	bind: function(selEl, quizzes) {
		if (!selEl)
			return ;
		
		selEl = YAHOO.util.Dom.get(selEl);
		selEl.options.length = 0;
		if (quizzes) {
			for (var i = 0, cnt = quizzes.length; i < cnt; i++) {
				var quiz = quizzes[i],
					opt = new Option(quiz.QuizName, quiz.QuizId);
				selEl.options[selEl.options.length] = opt;
			}
		};
	}
};