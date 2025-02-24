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

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
class os_payments {	
	/**
	 * Get list of payment methods
	 *
	 * @return array
	 */
	static function getPaymentMethods($loadOffline = true, $onlyRecurring = false) {
		static $methods ;			
		if (!$methods) {
			define('JPAYMENT_METHODS_PATH', JPATH_ROOT.'/components/com_osproperty//plugins/') ;
			$db = Factory::getDBO() ;
			if ($loadOffline) {
				$sql = 'SELECT * FROM #__osrs_plugins WHERE published=1 ' ;
			} else {
				$sql = 'SELECT * FROM #__osrs_plugins WHERE published=1 AND name != "os_offline" ' ;
			}
			$sql .= " ORDER BY ordering " ;
			$db->setQuery($sql) ;
			$rows = $db->loadObjectList();
			foreach ($rows as $row) {
				if (file_exists(JPAYMENT_METHODS_PATH.$row->name.'.php')) {
					require_once JPAYMENT_METHODS_PATH.$row->name.'.php';
                    //$params                 = new Registry($row->params);
                    //$method                 = new $row->name($params);
                    //$method->setTitle($row->title);
                    //$method->setDescription($row->description);
					//$methods[]              = $method ;

					$method = new $row->name(new Registry($row->params));
					$method->setTitle($row->title);
					$method->setDescription($row->description);
					$methods[] = $method ;	
				}
			}
		}		
		return $methods ;
	}
	/**
	 * Write the javascript objects to show the page
	 *
	 * @return string
	 */		
	static function writeJavascriptObjects() {
		$methods =  os_payments::getPaymentMethods();
		$jsString = " methods = new PaymentMethods();\n" ;			
		if (count($methods)) {
			foreach ($methods as $method) {
				$jsString .= " method = new PaymentMethod('".$method->getName()."',".$method->getCreditCard().",".$method->getCardType().",".$method->getCardCvv().",".$method->getCardHolderName().");\n" ;
				$jsString .= " methods.Add(method);\n";								
			}
		}
		echo $jsString ;
	}
	/**
	 * Load information about the payment method
	 *
	 * @param string $name Name of the payment method
	 */
	static function loadPaymentMethod($name) {
		$db = & Factory::getDBO() ;
		$sql = 'SELECT * FROM #__osrs_plugins WHERE name="'.$name.'"';
		$db->setQuery($sql) ;
		return $db->loadObject();
	}
	/**
	 * Get default payment gateway
	 *
	 * @return string
	 */
	static function getDefautPaymentMethod() {
		$db = & Factory::getDBO() ;
		$sql = 'SELECT name FROM #__osrs_plugins WHERE published=1 ORDER BY ordering LIMIT 1';
		$db->setQuery($sql) ;		
		return $db->loadResult();	
	}
	/**
	 * Get the payment method object based on it's name
	 *
	 * @param string $name
	 * @return object
	 */		
	static function getPaymentMethod($name) {
		$methods = os_payments::getPaymentMethods() ;
		foreach ($methods as $method) {
			if ($method->getName() == $name) {
				return $method ;		
			}
		}
		return null ;
	}
}
?>