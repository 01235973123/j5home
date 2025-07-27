<?php
/**
 * @package     MPF
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2016 - 2025 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/**
 * Layout variables
 *
 * @var string $filterSearchLabel
 * @var string $filterSearchDescription
 */

// Need to check to see if filter fields should be shown
$filtersActiveClass = $this->filtersActive ? ' js-stools-container-filters-visible' : '';
?>
<div class="js-stools" role="search">
	<div class="js-stools-container-bar">
		<div class="btn-toolbar">
			<div class="filter-search-bar btn-group">
				<div class="input-group">
					<input type="text" name="filter_search" id="filter_search" inputmode="search" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="form-control" aria-describedby="filter_search-desc" />
					<div role="tooltip" id="filter_search-desc" class="filter-search-bar__description">
						<?php echo htmlspecialchars(Text::_($filterSearchDescription), ENT_COMPAT, 'UTF-8'); ?>
					</div>
					<span class="filter-search-bar__label visually-hidden">
			            <?php echo Text::_($filterSearchLabel);; ?>
			        </span>
					<button type="submit" class="filter-search-bar__button btn btn-primary" aria-label="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
						<span class="filter-search-bar__button-icon icon-search" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="filter-search-actions btn-group">
				<button type="button" class="filter-search-actions__button btn btn-primary js-stools-btn-filter">
					<?php echo Text::_('JFILTER_OPTIONS'); ?>
					<span class="icon-angle-down" aria-hidden="true"></span>
				</button>
				<button type="button" class="filter-search-actions__button btn btn-primary js-stools-btn-clear">
					<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</div>
            <div class="ordering-select">
                <div class="js-stools-field-list">
					<?php echo $this->pagination->getLimitBox(); ?>
                </div>
            </div>
		</div>
	</div>
	<div class="mp-no-margin-left js-stools-container-filters clearfix<?php echo $filtersActiveClass; ?>">
		<?php echo $this->loadTemplate('searchtools_filters'); ?>
	</div>
</div>