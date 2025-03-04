(function() {
	YAHOO.namespace('ARISoft.Quiz');

	YAHOO.ARISoft.Quiz.Templates = {
		CORRECT_ANSWER: '<h2 class="aq-answer-result-message #{resultClass}">#{resultMessage}</h2><div class="aq-answer-result-summary">#{summary}</div>#{details}#{explanation}',
		EXPLANATION: '<blockquote><p>#{explanation}</p></blockquote>'
	};
})();