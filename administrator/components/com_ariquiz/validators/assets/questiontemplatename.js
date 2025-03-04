(function(){
	var AS = YAHOO.ARISoft,
		AQ = AS.Quiz;

	AQ.validators.isQuestionTemplateNameUnique = AS.core.createDerivedClass(
		YAHOO.ARISoft.validators.asyncValidator, {
			prepare: function() {
				var PM = AS.page.pageManager,
					templateId = 0;
				
				var templateEl = YAHOO.util.Dom.get(this.section + "TemplateId");
				if (templateEl)
					templateId = templateEl.value;
				
				this.url = PM.adminBaseUrl + "index.php?option=com_ariquiz&view=questiontemplate&task=ajaxIsTemplateNameUnique";
				this.postData = "templateName=" + this.getValue() + "&templateId=" + templateId;
			}
		}
	);
})();