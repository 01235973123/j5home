/*------------------------------------------------------------------------
# counter.js - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2018 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

/**
*
* textarea counter
*
* @param string textarea - textarea obj (id)
* @param int min - min count number
* @param int max - max count number
* 
**/

textcounter = function(conf)
{
	this.maxLength = conf.max;
	this.minLength = conf.min;

	this.conf = conf;
}

textcounter.prototype = 
{
	init: function()
	{
		var obj = this;
		
		this.element = document.getElementById(this.conf.textarea);
		this.count = document.getElementById(this.conf.textarea+'_counter');

		// init counter form
		this.count.readOnly = true;
		this.count.size = '4';
		this.count.maxLength = '4';

		if((this.minLength >= 0 && this.maxLength >= 0) || (isNaN(this.minLength) && this.maxLength >= 0))
		{
			this.count.value = this.maxLength;

			this.counting(true);

			// atach event for text form
			this.element.onkeydown = function()
			{
				obj.counting(true);
			}

			this.element.onkeyup = function()
			{
				obj.counting(true);
			}
		}

		if((this.minLength >= 0 && isNaN(this.maxLength)))
		{
			this.count.value = this.minLength;

			this.counting(false);

			// atach event for text form
			this.element.onkeydown = function()
			{
				obj.counting(false);
			}

			this.element.onkeyup = function()
			{
				obj.counting(false);
			}
		}
	},

	counting: function(decrease)
	{
		if(decrease)
		{
			if(this.element.value.length > this.maxLength)
			{
				this.element.value = this.element.value.substring(0, this.maxLength);
				$(this.element).animate({scrollTop:50000}, 0);
			}
			else
			{
				this.count.value = this.maxLength - this.element.value.length;
			}
		}
		else
		{
			this.count.value = this.element.value.length;
		}
	}
}
