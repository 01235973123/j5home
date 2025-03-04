(function(){
	var AS = YAHOO.ARISoft,
		AQ = AS.Quiz;

	AQ.validators.isQuizNameUnique = AS.core.createDerivedClass(
		YAHOO.ARISoft.validators.asyncValidator, {
			prepare: function() {
				var PM = AS.page.pageManager,
					quizId = 0;
				
				var catEl = YAHOO.util.Dom.get(this.section + "QuizId");
				if (catEl)
					quizId = catEl.value;
				
				this.url = PM.adminBaseUrl + "index.php?option=com_ariquiz&view=quiz&task=ajaxIsQuizNameUnique";
				this.postData = "quizName=" + this.getValue() + "&quizId=" + quizId;
			}
		}
	);
})();