<?php
/*------------------------------------------------------------------------
# transaction.html.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;


class HTML_OspropertyTransaction{
	/**
	 * List transaction
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 */
	static function listTransaction($lists,$rows,$pageNav){
		global $mainframe,$jinput;
		$db = Factory::getDBO();
		$configs = OSPHelper::loadConfig();

		ToolBarHelper::title(Text::_('OS_MANAGE_TRANSACTION'),"stack");
		ToolBarHelper::editList('transaction_details');
		ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'transaction_remove');
		ToolBarHelper::cancel();
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osproperty&task=transaction_list" name="adminForm" id="adminForm">
		<table  width="100%">
			<tr>
				<td width="100%" align="left">
					<DIV class="btn-wrapper btn-group transaction">
						<label><strong>
						<?php echo Text::_('OS_START_TIME')?>: 
						</strong></label>
						<?php echo HTMLHelper::_('calendar',$jinput->getString('start_date',''), 'start_date', 'start_date', '%Y-%m-%d', array('class'=>'input-small form-control', 'size'=>'19',  'maxlength'=>'19' , 'placeholder' => '0000-00-00')); ?>
						&nbsp;
						<label><strong>
						<?php echo Text::_('OS_END_TIME')?>: 
						</strong></label>
						<?php echo HTMLHelper::_('calendar',$jinput->getString('end_date',''), 'end_date', 'end_date', '%Y-%m-%d', array('class'=>'input-small form-control', 'size'=>'19',  'maxlength'=>'19' , 'placeholder' => '0000-00-00')); ?>
						<input type="submit" class="btn btn-primary" value="<?php echo Text::_('OS_SUBMIT')?>" />
					</div>
					<DIV class="btn-wrapper btn-group transaction" style="float:right;">
						<?php echo $lists['order_status'];?>
						<?php echo $lists['direction'];?>
					</div>
                </td>
			</tr>
		</table>
		<BR />
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="2%">
						#
					</th>
					<th width="3%" style="text-align:center;">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="10%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_USER'), 'b.name', @$lists['order_Dir'], @$lists['order'] ,'transaction_list'); ?>
					</th>
					<th width="7%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_TOTAL'), 'a.total', @$lists['order_Dir'], @$lists['order'] ,'transaction_list'); ?>
					</th>
					<th width="10%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_ACTION'), 'a.direction', @$lists['order_Dir'], @$lists['order'] ,'transaction_list'); ?>
					</th>
					<th width="23%">
						<?php echo Text::_('OS_PROPERTIES');?>
					</th>
					<th width="10%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_DATE'), 'a.created_on', @$lists['order_Dir'], @$lists['order'] ,'transaction_list'); ?>
					</th>
					<th width="10%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_STATUS'), 'a.order_status', @$lists['order_Dir'], @$lists['order'] ,'transaction_list'); ?>
					</th>
					<th width="10%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_PAYMENT_METHOD'), 'a.transaction_id', @$lists['order_Dir'], @$lists['order'] ,'transaction_list'); ?>
					</th>
                    <th width="2%">
                        <?php echo HTMLHelper::_('grid.sort',   Text::_('ID'), 'a.id', @$lists['order_Dir'], @$lists['order'] ,'transaction_list'); ?>
                    </th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td width="100%" colspan="9" style="text-align:center;">
						<?php
							echo $pageNav->getListFooter();
						?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php
				$db = Factory::getDBO();
				$k = 0;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$checked = HTMLHelper::_('grid.id', $i, $row->id);
					$link 	 = 'index.php?option=com_osproperty&task=transaction_details&cid[]='. $row->id ;
					
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center">
							<?php echo $pageNav->getRowOffset( $i ); ?>
						</td>
						<td align="center" style="text-align:center;">
							<?php echo $checked; ?>
						</td>
						<td align="left">
							<a href="<?php echo $link?>" title="<?php echo Text::_('OS_TRANSACTION_DETAILS')?>">
								<?php
									echo $row->name;
								?>
							</a>
						</td>
						<td align="center">
							<?php
							echo OSPHelper::generatePrice($row->curr, $row->total);
							?>
						</td>
						<td>
							<?php
							switch($row->direction){
								case "1":
									echo Text::_('OS_NEW_PROPERTY');
								break;
                                case "2":
                                    echo Text::_('OS_FEATURED_UPGRADE');
                                break;
                                case "3":
                                    echo Text::_('OS_EXTEND_LIVETIME');
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
								echo $row->created_on;
							?>
						</td>
						<td align="center">
							<?php
							if($row->order_status == "S"){
								//echo Text::_("OS_COMPLETED");
                                echo "<span style='color:green;'>".Text::_('OS_COMPLETED')."</span>";
							}else{
								//echo Text::_("OS_PENDING");
                                echo "<span style='color:red;'>".Text::_('OS_PENDING')."</span>";
							}
							?>
						</td>
						<td align="center">
							<?php
							//echo $row->transaction_id;
							//echo $row->payment_method;
							if($row->total > 0){
								$db->setQuery("Select title from #__osrs_plugins where name like '$row->payment_method'");
								echo $db->loadResult();
							}else{
							}
							?>
						</td>
                        <td align="center">
                            <?php
                            echo $row->id;
                            ?>
                        </td>
					</tr>
				<?php
					$k = 1 - $k;	
				}
				?>
			</tbody>
		</table>
		
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="transaction_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		
		</form>
		<?php
	}
	
	/**
	 * Transaction details
	 *
	 * @param unknown_type $option
	 * @param unknown_type $order
	 * @param unknown_type $configs
	 * @param unknown_type $coupon
	 * @param unknown_type $items
	 * @param unknown_type $agent
	 */
	static function transactionDetails($order,$configs,$items,$agent,$lists){
		global $mainframe;
		ToolBarHelper::title(Text::_('OS_ORDER_DETAILS'));
		ToolBarHelper::save('transaction_save');
		ToolBarHelper::apply('transaction_apply');
		ToolBarHelper::cancel('transaction_back');
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
		<table class="transaction_details_table">
			<tr>
				<td class="transaction_details_table_td1" >
					<span>
						<?php echo Text::_('OS_TRANSACTION_DETAILS')?>: <?php echo $order->id?>
					</span>
				</td>
			</tr>
			<?php
			if($print == 1){
			?>
			<tr>
				<td width="100%" align="right" style="padding:5px;">
					<a href="javascript:printOrder(<?php echo $order->id?>)" title="<?php echo Text::_('OS_PRINT')?>">
						<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/printer.png" border="0" />
					</a>
					<BR>
					<a href="javascript:printOrder(<?php echo $order->id?>)" title="<?php echo Text::_('OS_PRINT')?>">
						<?php echo Text::_('OS_PRINT')?>&nbsp;
					</a>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td class="transaction_details_table_td2">
                    <?php echo Text::_('OS_PAYMENT_METHOD')?>:
                    <?php echo Text::_(os_payments::loadPaymentMethod($order->payment_method)->title); ?>
					<input type="hidden" name="payment_method" id="payment_method" value="<?php echo $order->payment_method; ?>" />
                    &nbsp;&nbsp;&nbsp;
                    <?php echo Text::_('OS_PAYMENT_MADE')?>:
                    <?php
                    echo $lists['payment_made'];
                    ?>
                    &nbsp;&nbsp;&nbsp;
					<?php echo Text::_('OS_PAYMENT_STATUS')?>: 
					<?php
						if($order->order_status == "P"){
							echo "<span style='color:red;' >".Text::_('OS_PENDING')."</span>";
							echo "&nbsp;&nbsp;";
							echo $lists['order_status'];
						}elseif($order->order_status == "S"){
							echo "<span style='color:#008000;'>".Text::_('OS_COMPLETED')."</span>";
							?>
							<input type="hidden" name="order_status" value="S" />
							<?php
						}
					?>
				</td>
			</tr>
			<tr>
				<td class="transaction_details_table_td3">
					<table width="100%">
						<tr>
							<td width="50%" align="right" style="padding:5px;font-size:12px;">
								<strong><?php echo Text::_('OS_WEB_ACCEPT_PAYMENT')?> </strong>
							</td>
							<td width="50%" align="left" style="padding:5px;font-size:12px;">
							(<?php echo Text::_('OS_UNIQUE_TRANSACTION_ID')?> #<?php echo $order->transaction_id?>)
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
			<tr>
				<td class="transaction_details_table_td4">
					<table  width="100%">
						<tr>
							<td class="order_info_data_right">
								<strong><?php echo Text::_('OS_BUSINESS_NAME')?>: </strong>
							</td>
							<td class="order_info_data_left">
								<?php echo $agent->name;?>
							</td>
						</tr>
						<tr>
							<td class="order_info_data_right">
								<strong><?php echo Text::_('OS_EMAIL')?>: </strong>
							</td>
							<td class="order_info_data_left">
								<?php echo $agent->email;?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
            <tr>
                <td class="transaction_details_table_td5">
                    <table  width="100%">
                        <tr>
                            <td width="50%" align="right" style="padding:5px;font-size:12px;" valign="top">
                                <strong><?php echo Text::_('OS_PAYMENT_REASON')?>: </strong> 
                            </td>
                            <td width="50%" align="left" style="padding:5px;font-size:12px;">
                                <?php
								$db = Factory::getDbo();
								$query = "Select a.pro_name,a.id as pid from #__osrs_properties as a"
										." inner join #__osrs_order_details as b on b.pid = a.id"
										." where b.order_id = '$order->id'";
								$db->setQuery($query);
								$properties = $db->loadObjectList();
								$property_str = "";
								for($j=0;$j<count($properties);$j++){
									$property =$properties[$j];
									$j1 = $j + 1;
									$link = "index.php?option=com_osproperty&task=properties_edit&cid[]=".$property->pid;
									
									$property_str .= $j1.". <a href='$link' target='_blank'>".$property->pro_name."</a><div class='clearfix'></div>";
								}
                                switch($order->direction){
                                    case "1":
                                        echo Text::_('OS_NEW_PROPERTY');
                                        break;
                                    case "2":
                                        echo Text::_('OS_FEATURED_UPGRADE');
                                        break;
                                    case "3":
                                        echo Text::_('OS_EXTEND_LIVETIME');
                                        break;
                                }
								echo "<BR />";
								echo $property_str;
                                ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
			<tr>
				<td class="transaction_details_table_td5">
					<table  width="100%">
						<tr>
							<td width="50%" align="right" style="padding:5px;font-size:12px;">
								<strong><?php echo Text::_('OS_TOTAL')?>: </strong>
							</td>
							<td width="50%" align="left" style="padding:5px;font-size:12px;">
							<?php
								echo OSPHelper::generatePrice($order->curr,$order->total);
                                echo "&nbsp;";
                                echo OSPHelper::loadCurrencyCode();
							?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="transaction_details_table_td5">
					<table  width="100%" style="border:0px !important;">
						<tr>
							<td align="left">
								<b><?php echo Text::_('OS_ORDER_DETAILS')?></b>
								<BR>
								<table  width="100%">
									<tr>
										<td class="td_header_cart" width="85%">
											<?php echo Text::_('OS_PROPERTY')?>
										</td>
										<td class="td_header_cart" width="15%" style="border-right:1px solid #E5E5E5;">
											<?php echo Text::_('OS_TOTAL')?> (<?php echo OSPHelper::loadCurrencyCode();?>)
										</td>
									</tr>
									<?php
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
                                            <a href="index.php?option=com_osproperty&task=properties_edit&cid[]=<?php echo $item->pid;?>" target="_blank">
											    <?php echo $item->pro_name?>
                                            </a>
										</td>
										<td class="td_header_cart_item" width="15%" style="background-color:<?php echo $bgcolor?>;">
											<div id="total_price_coupon">
												<?php
                                                if($item->type == 0){
                                                    echo OSPHelper::generatePrice(HelperOspropertyCommon::loadCurrency(),$configs['normal_cost']);
                                                }else{
                                                    echo OSPHelper::generatePrice(HelperOspropertyCommon::loadCurrency(),$configs['general_featured_upgrade_amount']);
                                                }
                                                ?>
											</div>
										</td>
									</tr>
									<?php
									}
									?>
									<tr>
										<td class="td_header_cart_item order_total_label">
											<b>
											<?php echo Text::_('OS_TOTAL')?>
											</b>
											
										</td>
										<td class="td_header_cart_item order_total_price">
											<div id="total_price">
												<?php
                                                echo OSPHelper::generatePrice($order->curr,$order->total);
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
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" id="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="order_id" id="order_id" value="<?php echo $order->id?>" />
		</form>
		<?php
	}
}
?>