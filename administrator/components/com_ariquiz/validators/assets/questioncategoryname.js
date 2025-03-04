(function(){
	var AS = YAHOO.ARISoft,
		AQ = AS.Quiz;

	AQ.validators.isQuestionCategoryNameUnique = AS.core.createDerivedClass(
		YAHOO.ARISoft.validators.asyncValidator, {
			prepare: function() {
				var PM = AS.page.pageManager,
					categoryId = 0,
					quizId = 0;
				
				var catEl = YAHOO.util.Dom.get(this.section + "QuestionCategoryId");
				if (catEl)
					categoryId = catEl.value;
				
				var quizEl = YAHOO.util.Dom.get(this.section + "QuizId");
				if (quizEl)
					quizId = quizEl.value;

				this.url = PM.adminBaseUrl + "index.php?option=com_ariquiz&view=questioncategory&task=ajaxIsCategoryNameUnique";
				this.postData = "categoryName=" + this.getValue() + "&categoryId=" + categoryId + "&quizId=" + quizId;
			}
		}
	);
})();