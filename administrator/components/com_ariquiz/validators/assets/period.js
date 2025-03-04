(function(){
	var AS = YAHOO.ARISoft,
		Dom = YAHOO.util.Dom,
		AQ = AS.Quiz;

	AQ.validators.isPeriodValid = AS.core.createDerivedClass(
		YAHOO.ARISoft.validators.baseValidator, {
			validate : function() {
				var isValid = false,
					count = Dom.get('tbx' + this.ctrlPrefix).value;
				
				if (count.length == 0)
					isValid = true;
				else {
					var parseValue = parseInt(count, 10);
					isValid = (count == parseValue && parseValue >= 0);					
				};

				this.isValid = isValid
				
				return this.isValid;
			}
		}
	);
})();