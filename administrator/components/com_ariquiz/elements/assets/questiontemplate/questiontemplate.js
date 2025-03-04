;ARIQuestionTemplateElement = {
	initEl: function(selId) {
		YAHOO.util.Event.on(selId, 'click', function() {
			ARIQuestionTemplateElement.populate(selId);
			
			YAHOO.util.Event.removeListener(selId, 'click');
		}, this, true);
	},
	
	populate: function(selEl) {
		selEl = YAHOO.util.Dom.get(selEl);
		selEl.disabled = true;
		YAHOO.ARISoft.ajax.ajaxManager.asyncRequest(
			'GET',
			'index.php?option=com_ariquiz&view=element&task=ajaxExecute&element=questiontemplate&action=getTemplateList', 
			{
				cache: false,
				
				success: function(oResponse) {
					var templates = null;
					try {
						var responseText = oResponse.responseText;
						templates = YAHOO.lang.JSON.parse(responseText);
					} catch (e) {};

					selEl.options.length = 0;
					if (templates) {
						for (var i = 0, cnt = templates.length; i < cnt; i++) {
							var template = templates[i],
								opt = new Option(template.TemplateName, template.TemplateId);
							selEl.options[selEl.options.length] = opt;
						}
					};

					selEl.disabled = false;
				},
				
				failure: function(oResponse) {
					selEl.disabled = false;
					//YAHOO.ARISoft.page.pageManager.sendInfoMessage('fail');
				}
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
	}
};