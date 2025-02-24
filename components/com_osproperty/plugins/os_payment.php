<?php
/**
 * @version		1.1.1
 * @package		Joomla
 * @subpackage	OS Property
 * @author		Dang Thuc Dam
 * @copyright	Copyright (C) 2015 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die ;
use Joomla\CMS\Factory;
class os_payment {
	/**
	 * Title of payment method
	 * @var string
	 */
	var $title = null ;

	var $description = null;
	/**
	 * Name of payment method
	 *
	 * @var string
	 */
	var $_name = null ;
	/**
	 * Creditcard payment method ?
	 *
	 * @var string
	 */
	var $_creditCard = false ;
	/**
	 * Require card cvv code ?
	 *
	 * @var boolean
	 */
	var $_cardCvv = false ;
	/**
	 * Require creditcard type ?
	 *
	 * @var boolean
	 */
	var $_cardType = false ;
	/**
	 * Require card holder name ?
	 *
	 * @var boolean
	 */
	var $_cardHolderName = false ;	
	
	function os_payment() {
		$this->loadLanguage();	
	}	
	/**
	 * Getter method for name property
	 *
	 * @return string
	 */
	function getName() {
		return $this->_name ;
	}
	/**
	 * Setter method for name property
	 *
	 * @param string $value
	 */
	function setName($value) {
		$this->_name = $value ;
	}
	
	function getTitle() {
		return $this->title ;
	}

	function setTitle($title) {
		$this->title = $title ;
	}

    function getDescription() {
        return $this->description ;
    }

    function setDescription($desc) {
        $this->description = $desc ;
    }

	/**
	 * Getter method for cardType property
	 *
	 * @return boolean
	 */
	function getCreditCard() {
		if ($this->_creditCard)
			return 1 ;
		else 
			return 0 ;					
	}
	/**
	 * Setter method for creditCard
	 *
	 * @param  $value
	 */
	function setCreditCard($value) {
		$this->_creditCard = $value ;
	}
	/**
	 * Setter method for cardCvv
	 *
	 * @return boolean
	 */
	function getCardCvv() {
		if ($this->_cardCvv)
			return 1 ;
		else 
			return 0 ;			
	}
	/**
	 * Setter method for cardCvv
	 *
	 * @param boolean
	 */
	function setCardCvv($value) {
		$this->_cardCvv = $value ;
	}
	/**
	 * Getter method for cardType
	 *
	 * @return boolean
	 */
	function getCardType() {
		if ($this->_cardType)
			return 1 ;
		else 
			return 0 ;			
	}
	/**
	 * Setter method for CardType property
	 *
	 * @param boolean $value
	 */
	function setCardType($value) {
		$this->_cardType = $value ;
	}
	/**
	 * Getter method for CardHolderName
	 *
	 * @return boolean
	 */
	function getCardHolderName() {
		if ($this->_cardHolderName)
			return 1 ;
		else 
			return 0 ;			
	}
	/**
	 * Setter method for CardHolderName
	 *
	 * @param boolean $value
	 */
	function setCardHolderName($value) {
		$this->_cardHolderName = $value ;
	}
	/**
	 * Load language file for this payment plugin
	 *
	 */
	function loadLanguage() {
		$pluginName = $this->getName() ;
		$lang = Factory::getLanguage() ;
		$tag = $lang->getTag();
		if (!$tag)
			$tag = 'en-GB' ;									
		$lang->load($pluginName, JPATH_ROOT, $tag);		
	}
}
?>