<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2025 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

return [
	'activate_invoice_feature' => '0',
	'send_invoice_to_customer' => '0',
	'invoice_start_number'     => '1',
	'invoice_prefix'           => 'IV',
	'invoice_number_length'    => '5',
	'invoice_format'           => '<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tbody>
<tr>
<td align="left" width="100%">
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tbody>
<tr>
<td width="100%">
<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
<tbody>
<tr>
<td align="left" valign="top" width="50%">
<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
<tbody>
<tr>
<td align="left" width="50%">Company Name:</td>
<td align="left">Ossolution Team</td>
</tr>
<tr>
<td align="left" width="50%">URL:</td>
<td align="left">http://www.joomdonation.com</td>
</tr>
<tr>
<td align="left" width="50%">Phone:</td>
<td align="left">84-972409994</td>
</tr>
<tr>
<td align="left" width="50%">E-mail:</td>
<td align="left">contact@joomdonation.com</td>
</tr>
<tr>
<td align="left" width="50%">Address:</td>
<td align="left">Lang Ha - Ba Dinh - Ha Noi</td>
</tr>
</tbody>
</table>
</td>
<td align="right" valign="middle" width="50%"><img style="border: 0;" src="media/com_osmembership/invoice_logo.png" alt="" /></td>
</tr>
<tr>
<td colspan="2" align="left" width="100%">
<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
<tbody>
<tr>
<td align="left" valign="top" width="50%">
<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
<tbody>
<tr>
<td style="background-color: #d6d6d6;" colspan="2" align="left">
<h4 style="margin: 0px;">Customer Information</h4>
</td>
</tr>
<tr>
<td align="left" width="50%">Name:</td>
<td align="left">[NAME]</td>
</tr>
<tr>
<td align="left" width="50%">Company:</td>
<td align="left">[ORGANIZATION]</td>
</tr>
<tr>
<td align="left" width="50%">Phone:</td>
<td align="left">[PHONE]</td>
</tr>
<tr>
<td align="left" width="50%">Email:</td>
<td align="left">[EMAIL]</td>
</tr>
<tr>
<td align="left" width="50%">Address:</td>
<td align="left">[ADDRESS], [CITY], [STATE], [COUNTRY]</td>
</tr>
</tbody>
</table>
</td>
<td align="left" valign="top" width="50%">
<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
<tbody>
<tr>
<td style="background-color: #d6d6d6;" colspan="2" align="left">
<h4 style="margin: 0px;">Invoice Information</h4>
</td>
</tr>
<tr>
<td align="left" width="50%">Invoice Number:</td>
<td align="left">[INVOICE_NUMBER]</td>
</tr>
<tr>
<td align="left" width="50%">Invoice Date:</td>
<td align="left">[INVOICE_DATE]</td>
</tr>
<tr>
<td align="left" width="50%">Invoice Status:</td>
<td align="left">[INVOICE_STATUS]</td>
</tr>
<tr>
<td align="left" width="50%"> </td>
<td align="left"> </td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td style="background-color: #d6d6d6;" colspan="2" align="left">
<h4 style="margin: 0px;">Order Items</h4>
</td>
</tr>
<tr>
<td colspan="2" align="left" width="100%">
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tbody>
<tr>
<td align="left" valign="top" width="10%">#</td>
<td align="left" valign="top" width="60%">Name</td>
<td align="right" valign="top" width="20%">Price</td>
<td align="left" valign="top" width="10%">Sub Total</td>
</tr>
<tr>
<td align="left" valign="top" width="10%">[ITEM_QUANTITY]</td>
<td align="left" valign="top" width="60%">[ITEM_NAME]</td>
<td align="right" valign="top" width="20%">[ITEM_AMOUNT]</td>
<td align="left" valign="top" width="10%">[ITEM_SUB_TOTAL]</td>
</tr>
<tr>
<td colspan="3" align="right" valign="top" width="90%">Discount :</td>
<td align="left" valign="top" width="10%">[DISCOUNT_AMOUNT]</td>
</tr>
<tr>
<td colspan="3" align="right" valign="top" width="90%">Subtotal :</td>
<td align="left" valign="top" width="10%">[SUB_TOTAL]</td>
</tr>
<tr>
<td colspan="3" align="right" valign="top" width="90%">Tax :</td>
<td align="left" valign="top" width="10%">[TAX_AMOUNT]</td>
</tr>
<tr>
<td colspan="3" align="right" valign="top" width="90%">Total :</td>
<td align="left" valign="top" width="10%">[TOTAL_AMOUNT]</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>',
	'card_layout'              => '<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
<tbody>
<tr>
<td align="left" width="50%">Membership ID</td>
<td align="left">[MEMBERSHIP_ID]</td>
</tr>
<tr>
<td align="left" width="50%">Members since</td>
<td align="left">[REGISTER_DATE]</td>
</tr>
<tr>
<td align="left" width="50%">Name:</td>
<td align="left">[NAME]</td>
</tr>
<tr>
<td align="left" width="50%">Company:</td>
<td align="left">[ORGANIZATION]</td>
</tr>
<tr>
<td align="left" width="50%">Phone:</td>
<td align="left">[PHONE]</td>
</tr>
<tr>
<td align="left" width="50%">Email:</td>
<td align="left">[EMAIL]</td>
</tr>
<tr>
<td align="left" width="50%">Address:</td>
<td align="left">[ADDRESS], [CITY], [STATE], [COUNTRY]</td>
</tr>
<tr>
<td align="left" width="50%">[QRCODE]</td>
<td align="left"> </td>
</tr>
</tbody>
</table>',
];