(function(){
	var AS = YAHOO.ARISoft,
		AQ = AS.Quiz,
		LM = AS.languageManager,
        DOM = YAHOO.util.Dom;

	AQ.validators.isResultScaleValid = AS.core.createDerivedClass(
		YAHOO.ARISoft.validators.baseValidator, {
			validate : function() {
				var isValid = false,
                    isPercent = (DOM.get(this.ctrlId + 'ScaleType').value != 'Score');
				
				var data = aris.widgets.multiplierControls.getData('tblScaleContainer', 'trScaleTemplate', ['BeginPoint', 'EndPoint']);
				this.errorMessage = 'default';
				if (!this.formatPointValidate(data)) {
					this.errorMessage = LM.getMessage('COM_ARIQUIZ_VALIDATOR_RSRANGEPOINT');
				}
				else if (!this.emptyPointValidate(data)) {
					this.errorMessage = LM.getMessage('COM_ARIQUIZ_VALIDATOR_RSEMPTYSECTION');
				}
				else if (!this.rangePointValidate(data, isPercent)) {
					this.errorMessage = LM.getMessage('COM_ARIQUIZ_VALIDATOR_RSRANGEPOINT');
				}
				else if (!this.intersectPointValidate(data)) {
					this.errorMessage = LM.getMessage('COM_ARIQUIZ_VALIDATOR_RSOVERLAPPOINT');
				}
				else if (!this.emptyAllRangeCoveredPointValidate(data, isPercent)) {
					isValid = confirm(LM.getMessage('COM_ARIQUIZ_WARNING_RSNOTCOVEREDPOINT'));
					if (!isValid) 
						this.errorMessage = LM.getMessage('COM_ARIQUIZ_VALIDATOR_RSNOTCOVEREDPOINT');
				}
				else {
					isValid = true;
				}

				this.isValid = isValid;
				
				return this.isValid;
			},
			
			getSectionData: function(dataItem, parse) {
				parse = parse || false;
				var startPoint = !YAHOO.lang.isUndefined(dataItem['BeginPoint']) ? (parse ? parseFloat(dataItem['BeginPoint']) : YAHOO.lang.trim(dataItem['BeginPoint'])) : null,
					endPoint = !YAHOO.lang.isUndefined(dataItem['EndPoint']) ? (parse ? parseFloat(dataItem['EndPoint']) : YAHOO.lang.trim(dataItem['EndPoint'])) : null;

				if (parse) {
					if (isNaN(startPoint)) 
						startPoint = null;
					if (isNaN(endPoint)) 
						endPoint = null;
					
					if (startPoint != null && endPoint != null && startPoint > endPoint) {
						var tPoint = startPoint;
						startPoint = endPoint;
						endPoint = tPoint;
					}
				};

				return  {'startPoint': startPoint, 'endPoint': endPoint};
			},
			
			isValidSection: function(section) {
				return (section['startPoint'] != null && section['endPoint'] != null); 
			},
			
			formatPointValidate: function(data) {
				data = data || null;
				if (!data || data.length < 1) 
					return true;
				
				var isValid = true;
				for (var i = 0; i < data.length; i++) {
					var section = this.getSectionData(data[i]);
					if ((section['startPoint'] != null && section['startPoint'].length > 0 && section['startPoint'] != parseFloat(section['startPoint'])) ||
						(section['endPoint'] != null && section['endPoint'].length > 0 && section['endPoint'] != parseFloat(section['endPoint']))) {
						isValid = false;
						break;
					}
				};
			
				return isValid;
			},
			
			emptyPointValidate: function(data)
			{
				data = data || null;
				var isValid = (data && data.length > 0);
				if (!isValid) 
					return isValid;
				
				isValid = false;
				for (var i = 0; i < data.length; i++) {
					var section = this.getSectionData(data[i], true);
					if (this.isValidSection(section)) {
						isValid = true;
						break;
					}
				};
				
				return isValid;
			},

			rangePointValidate: function(data, isPercent) {
				data = data || null;
				if (!data || data.length < 1) 
					return true;
				
				var isValid = true;
				for (var i = 0; i < data.length; i++) {
					var section = this.getSectionData(data[i], true);
					if (this.isValidSection(section) && 
						(section['startPoint'] < 0 || (isPercent && section['startPoint'] > 100) ||
						section['endPoint'] < 0 || (isPercent && section['endPoint'] > 100))) {
						isValid = false;
						break;
					}
					
				};
				
				return isValid;
			},
			
			intersectPointValidate: function(data) {
				data = data || null;
				if (!data || data.length < 1) 
					return true;
				
				var isValid = true,
					range = {};
				for (var i = 0; i < data.length; i++) {
					var section = this.getSectionData(data[i], true);
					if (this.isValidSection(section)) {
						var startPoint = section['startPoint'],
							endPoint = section['endPoint'];
						for (var startRange in range) {
							var endRange = range[startRange];
							if ((startRange <= startPoint && startPoint < endRange) || (startPoint <= startRange && startRange < endPoint)) {
								isValid = false;
								break;
							}
						};
						
						range[startPoint] = endPoint;
					}
				};
				
				return isValid;
			},
			
			emptyAllRangeCoveredPointValidate: function(data, isPercent) {
				data = data || null;
				if (!data || data.length < 1) 
					return true;

				var counter = 100;

                if (!isPercent) {
                    counter = 0;

                    for (var i = 0; i < data.length; i++) {
                        var section = this.getSectionData(data[i], true);
                        if (this.isValidSection(section)) {
                            counter = Math.max(section['endPoint'], counter);
                        }
                    }
                }

				for (var i = 0; i < data.length; i++) {
					var section = this.getSectionData(data[i], true);
					if (this.isValidSection(section)) {
						var startPoint = section['startPoint'],
							endPoint = section['endPoint'];

						counter -= endPoint - startPoint;
					}
				};
				
				return (counter <= 0);
			}
		}
	);
})();