(function(){
	var AS = YAHOO.ARISoft,
		AQ = AS.Quiz;

	AQ.validators.isCategoryNameUnique = AS.core.createDerivedClass(
		YAHOO.ARISoft.validators.asyncValidator, {
			prepare: function() {
				var PM = AS.page.pageManager,
					categoryId = 0;
				
				var catEl = YAHOO.util.Dom.get(this.section + "CategoryId");
				if (catEl)
					categoryId = catEl.value;
				
				this.url = PM.adminBaseUrl + "index.php?option=com_ariquiz&view=category&task=ajaxIsCategoryNameUnique";
				this.postData = "categoryName=" + this.getValue() + "&categoryId=" + categoryId;
			}
		}
	);
})();