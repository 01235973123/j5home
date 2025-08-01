<?php
/**
 * @package            Joomla
 * @subpackage         Membership Pro
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2020 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die ;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

Factory::getApplication()
	->getDocument()
	->getWebAssetManager()
	->useScript('core')
	->registerAndUseScript('com_osmembership.admin-language-default', 'media/com_osmembership/js/admin-language-default.min.js');
?>
<form action="index.php?option=com_osmembership&view=language" method="post" name="adminForm" id="adminForm">
    <div id="j-main-container" class="mp-joomla4-container">
        <div id="filter-bar" class="btn-toolbar js-stools-container-filters-visible">
            <div class="filter-search btn-group pull-left">
                <input type="text" name="filter_search" inputmode="search" id="filter_search" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip form-control" title="<?php echo HTMLHelper::tooltipText('OSM_SEARCH_LANGUAGE_DESC'); ?>" />
            </div>
            <div class="btn-group pull-left">
                <button id="eb-clear-button" type="button" class="btn btn-primary hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_CLEAR'); ?>"><span class="icon-remove"></span></button>
            </div>
            <div class="btn-group pull-right">
			    <?php
					echo $this->lists['filter_item'];
					echo $this->lists['filter_language'];
				?>
            </div>
        </div>
        <div class="clearfix"></div>
        <table class="adminlist table table-striped" id="lang_table">
            <thead>
                <tr>
                    <th class="key" style="width:20%; text-align: left;"><?php echo Text::_('OSM_KEY'); ?></th>
                    <th class="key" style="width:40%; text-align: left;"><?php echo Text::_('OSM_ORIGINAL'); ?></th>
                    <th class="key" style="width:40%; text-align: left;"><?php echo Text::_('OSM_TRANSLATION'); ?></th>
                </tr>
            </thead>
            <tbody id="eb-translation-table">
            <?php
				if (str_contains($this->state->filter_item, 'admin'))
				{
					$languageItem = substr($this->state->filter_item, 6);
				}
				else
				{
					$languageItem = $this->state->filter_item;
				}

				$keys = [];

				$original = $this->items['en-GB'][$languageItem];
				$trans    = $this->items[$this->state->filter_language][$languageItem];

				foreach ($original as  $key=>$value)
				{
					$keys[] = $key;

					if (isset($trans[$key]))
					{
						$translatedValue = $trans[$key];
						$missing = false ;
					}
					else
					{
						$translatedValue = $value;
						$missing = true ;
					}
					?>
                        <tr>
                            <td class="key" style="text-align: left;"><?php echo $key; ?></td>
                            <td style="text-align: left;"><?php echo $value; ?></td>
                            <td>
                                <input type="text" name="<?php echo $key; ?>" class="form-control eb-language-item-value" value="<?php echo $this->escape($translatedValue);  ?>" />
                                <?php
									if ($missing)
									{
									?>
                                        <span style="color:red;">*</span>
                                    <?php
									}
								?>
                            </td>
                    </tr>
                    <?php
				}
			?>
            </tbody>
        </table>
    </div>
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<form action="index.php?option=com_osmembership&view=language" method="post" name="translateForm" id="translateForm">
    <input type="hidden" name="task" value=""/>
    <input type="hidden" id="translate_new_keys" name="new_keys" value=""/>
    <input type="hidden" id="translate_new_values" name="new_values" value=""/>
    <input type="hidden" id="translate_keys" name="keys" value="<?php echo implode(',', $keys) ?>"/>
    <input type="hidden" id="translate_values" name="values" value=""/>
    <input type="hidden" id="translate_filter_item" name="filter_item" value=""/>
    <input type="hidden" id="translate_filter_search" name="filter_search"/>
    <input type="hidden" id="translate_filter_language" name="filter_language" value=""/>
</form>