<?php
/*------------------------------------------------------------------------
# payment.html.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
class HTML_OspropertyPayment{
	/**
	 * Order details Form
	 *
	 * @param unknown_type $option
	 * @param unknown_type $order
	 * @param unknown_type $configs
	 * @param unknown_type $coupon
	 * @param unknown_type $items
	 */
	static function orderDetailsForm($option,$order,$configs,$coupon,$items,$agent,$print){
		global $mainframe;
		?>
		<script type="text/javascript">
		function printOrder(order_id){
			link = "<?php echo Uri::root()?>index2.php?option=com_osproperty&task=order_details&print=1&no_html=1&id=" + order_id;
			window.open(link,'mywindow','width=600,height=700,resizable=1,scrollbars=1,toolbar=0,location=0,menubar=0');
		}
		</script>
		<table  width="100%" style="border:1px solid #CCC;font-family:Arial;font-size:12px;">
			<tr>
				<td width="100%" style="padding:10px;" align="left" valign="top">
					<font style="font-weight:bold;display: inline;line-height: 125%;padding: 0;color: #C88039;   font-size: 1.33em;">
					<?php echo Text::_('Transaction details')?>: <?php echo $order->id?>
					</font>
				</td>
			</tr>
			<?php
			if($print == 1){
			?>
			<tr>
				<td width="100%" align="right" style="padding:5px;font-size:12px;">
					<a href="javascript:printOrder(<?php echo $order->id?>)" title="<?php echo Text::_('Print Order')?>">
						<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/printer.png" border="0">
					</a>
					<BR />
					<a href="javascript:printOrder(<?php echo $order->id?>)" title="Print Order">
					<?php echo Text::_('Print')?>&nbsp;
					</a>
					
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td style="padding:10px;background-color:#F9F9CF;font-size:12px;" align="right">
					<?php echo Text::_('Payment Status')?>: <?php
					if($order->order_status == "P"){
						echo "<font color='red'>Pending</font>";
					}elseif($order->order_status == "S"){
						echo "<font color='green'>Completed</font>";
					}
					
					?>
				</td>
			</tr>
			<tr>
				<td style="padding:10px;border-bottom:1px dotted #CCC;font-size:12px;" align="center">
					<table  width="100%">
						<tr>
							<td width="50%" align="right" style="padding:5px;font-size:12px;">
							<strong><?php echo Text::_('Web Accept Payment Received')?> </strong>
							</td>
							<td width="50%" align="left" style="padding:5px;font-size:12px;">
							(<?php echo Text::_('Unique Transaction ID')?> #<?php echo $order->transaction_id?>)
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
			<tr>
				<td style="padding:10px;border-bottom:1px dotted #CCC;font-size:12px;" align="center">
					<table  width="100%">
						<tr>
							<td width="50%" align="right" style="padding:5px;font-size:12px;">
							<strong><?php echo Text::_('Business name')?>: </strong>
							</td>
							<td width="50%" align="left" style="padding:5px;font-size:12px;">
							<?php echo $agent->name?>
							</td>
						</tr>
						<tr>
							<td width="50%" align="right" style="padding:5px;font-size:12px;">
							<strong><?php echo Text::_('Email')?>: </strong>
							</td>
							<td width="50%" align="left" style="padding:5px;font-size:12px;">
							<?php echo $agent->email?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
			<tr>
				<td style="padding:10px;border-bottom:1px dotted #CCC;" align="center">
					<table  width="100%">
						<tr>
							<td width="50%" align="right" style="padding:5px;font-size:12px;">
							<strong><?php echo Text::_('Total')?>: </strong>
							</td>
							<td width="50%" align="left" style="padding:5px;font-size:12px;">
							<?php echo HelperOspropertyCommon::loadDefaultCurrency(1)." ".OSPHelper::showPrice($order->total)?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<style>
			.td_header_cart{
				background-color:#E5E5E5;
				padding-left:10px;
				padding:5px;
				font-size:13px;
				font-weight:bold;
				border-right:1px solid white;
				text-align:left;
			}
			
			.td_header_cart_item{
				background-color:white;
				border-bottom:1px solid #E5E5E5;
				padding:5px;
				text-align:left;
				padding-left:10px;
				border-right:1px solid #E5E5E5;
				font-size:12px;
			}
			</style>
			<tr>
				<td style="padding:10px;border-bottom:1px dotted #CCC;" align="center">
					<table  width="100%" style="border:0px !important;">
						<tr>
							<td align="left">
								<strong><?php echo Text::_('Order details')?></strong>
								<BR />
								<table  width="100%">
									<tr>
										<td class="td_header_cart" width="85%">
											<?php echo Text::_('Property')?>
										</td>
										<td class="td_header_cart" width="15%" style="border-right:1px solid #E5E5E5;">
											<?php echo Text::_('Total')?>
										</td>
									</tr>
									<?php
									//if($order->quantity > 0){
									for($i=0;$i<count($items);$i++){
										$item = $items[$i];
										if($i % 2 == 0){
											$bgcolor = "#FDF5F5";
										}else{
											$bgcolor = "white";
										}
									?>
									<tr>
										<td class="td_header_cart_item" width="45%" style="background-color:<?php echo $bgcolor?>;">
											<?php echo $item->pro_name?>
										</td>
										<td class="td_header_cart_item" width="15%" style="background-color:<?php echo $bgcolor?>;">
											<div id="total_price_coupon">
											<?php
												echo HelperOspropertyCommon::loadDefaultCurrency(1);
												echo "&nbsp;";
												echo OSPHelper::showPrice($configClass['general_paid_listing']);
												
												
											?> 
											</div>
										</td>
									</tr>
									<?php
									}
									?>
									<tr>
										<td class="td_header_cart_item" width="45%" style="background-color:#D4DDFB;color:red;">
											<strong>
											<?php echo Text::_('Total')?>
											</strong>
											
										</td>
										<td class="td_header_cart_item" width="15%" style="background-color:#D4DDFB;">
											<div id="total_price">
												<?php
												echo HelperOspropertyCommon::loadDefaultCurrency(1)." ".OSPHelper::showPrice($order->total);
												?>
											</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php			
		if($print == 1){
			?>
			<script type="text/javascript">
			window.print();
			</script>
			<?php
		}
		?>
		<?php
	}

	static function listOrdersHistory($orders){
		global $configClass;
		?>
		<table class="orders-history-table">
			<tr>
				<th width="5%">
					#
				</th>
				<th WIDTH="20%">
					<?php echo Text::_('OS_PAYMENT_FOR');?>
				</th>
				<th WIDTH="30%">
					<?php echo Text::_('OS_PROPERTIES');?>
				</th>
				<th WIDTH="15%">
					<?php echo Text::_('OS_PAYMENT_DATE');?>
				</th>
				<th WIDTH="15%">
					<?php echo Text::_('OS_GROSS_AMOUNT');?>
				</th>
				<th WIDTH="15%">
					<?php echo Text::_('OS_STATUS');?>
				</th>
			</tr>
			<tbody>
				<?php
				$db = Factory::getDBO();
				$k = 0;
				for ($i=0, $n=count($orders); $i < $n; $i++) {
					$row = $orders[$i];
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center">
							<?php echo $i+1; ?>
						</td>
						<td>
							<?php
							switch($row->direction){
								case "0":
									echo Text::_('OS_NEW_PROPERTY');
								break;
                                case "1":
                                    echo Text::_('OS_FEATURED_UPGRADE');
                                break;
                                case "2":
                                    echo Text::_('OS_EXTEND_LIVE_TIME');
                                break;
							}
							?>
						</td>
						<td align="left"> 
							<?php
							echo $row->property;
							?>
						</td>
						<td align="center">
							<?php
								echo date($configClass['general_date_format'],strtotime($row->created_on));
							?>
						</td>
						<td style="text-align:center;">
							<?php
							echo OSPHelper::generatePrice($row->curr, $row->total);
							?>
						</td>
						<td align="center">
							<?php
							if($row->order_status == "S"){
                                echo "<span style='color:green;'>".Text::_('OS_COMPLETED')."</span>";
							}else{
                                echo "<span style='color:red;'>".Text::_('OS_PENDING')."</span>";
							}
							?>
						</td>
					</tr>
				<?php
					$k = 1 - $k;	
				}
				?>
			</tbody>
		</table>
		<?php
	}
}
?>