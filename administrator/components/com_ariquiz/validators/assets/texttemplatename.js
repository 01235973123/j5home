(function(){
	var AS = YAHOO.ARISoft,
		AQ = AS.Quiz;

	AQ.validators.isTexttemplateNameUnique = AS.core.createDerivedClass(
		YAHOO.ARISoft.validators.asyncValidator, {
			prepare: function() {
				var PM = AS.page.pageManager,
					templateId = 0;
				
				var templateEl = YAHOO.util.Dom.get(this.prefix + this.relatedElement);
				if (templateEl)
					templateId = templateEl.value;
				
				this.url = PM.adminBaseUrl + "index.php?option=com_ariquiz&view=resulttemplate&task=ajaxIsTemplateNameUnique";
				this.postData = "templateName=" + this.getValue() + "&templateId=" + templateId + "&templateGroup=" + this.templateGroup;
			}
		}
	);
})();