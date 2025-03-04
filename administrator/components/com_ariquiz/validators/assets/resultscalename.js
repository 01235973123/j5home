(function(){
	var AS = YAHOO.ARISoft,
		AQ = AS.Quiz;

	AQ.validators.isResultScaleNameUnique = AS.core.createDerivedClass(
		YAHOO.ARISoft.validators.asyncValidator, {
			prepare: function() {
				var PM = AS.page.pageManager,
					scaleId = 0;
				
				var catEl = YAHOO.util.Dom.get(this.section + "ScaleId");
				if (catEl)
					scaleId = catEl.value;
				
				this.url = PM.adminBaseUrl + "index.php?option=com_ariquiz&view=resultscale&task=ajaxIsScaleNameUnique";
				this.postData = "scaleName=" + this.getValue() + "&scaleId=" + scaleId;
			}
		}
	);
})();