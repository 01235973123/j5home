<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

AriKernel::import('Joomla.Models.Model');
AriKernel::import('Joomla.Database.DBUtils');
AriKernel::import('Utils.ArrayHelper');

class AriQuizModelCategories extends AriModel 
{
	function getCategoryCount($filter = null)
	{
		$db =& $this->getDBO();

		$query = AriDBUtils::getQuery();
		$query->select('COUNT(*)');
		$query->from('#__ariquizcategory C');
		$query->where('C.lft > 0');
		
		if ($filter)
			$query = $this->_applyFilter($query, $filter);

		$db->setQuery((string)$query);
		$count = $db->loadResult();

		return $count;
	}
	
	function getCategoryList($filter = null, $ignoreRoot = true)
	{
		$db =& $this->getDBO();
		if (empty($filter))
			$filter = new AriDataFilter();

		$query = AriDBUtils::getQuery();
		$query->select(
			array(
				'C.CategoryId',
				'C.CategoryName',
				'C.parent_id',
				'C.lft',
				'C.rgt',
				'C.level'
			)
		);
		$query->from('#__ariquizcategory C');
		if ($ignoreRoot)
			$query->where('C.lft > 0');
		if ($filter)
		{
			$query = $this->_applyFilter($query, $filter);
			$query = $filter->applyToQuery($query);
		}

		$db->setQuery((string)$query, $filter->getConfigValue('startOffset'), $filter->getConfigValue('limit'));
		$categories = $db->loadObjectList();
		
		return $categories;
	}
	
	function deleteCategory($idList, $newCategoryId = 0)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;
		
		$db =& $this->getDBO();
		
		$catStr = join(',', $idList);
		// get categories with sub categories
		$query = sprintf(
			'SELECT DISTINCT C.CategoryId FROM #__ariquizcategory C,#__ariquizcategory P WHERE C.lft > 0 AND C.lft >= P.lft AND C.rgt <= P.rgt AND P.CategoryId IN (%1$s)',
			$catStr
		);
		$db->setQuery($query);
		$fullIdList = J3_0 ? $db->loadColumn() : $db->loadResultArray();

		if (!$fullIdList || ($newCategoryId > 0 && in_array($newCategoryId, $fullIdList)))
			return false;

		$catStr = join(',', $fullIdList);		
		$query = array();

		if ($newCategoryId == 0)
			$query[] = sprintf('DELETE FROM #__ariquizquizcategory WHERE CategoryId IN (%1$s)', 
				$catStr
			);
		else
		{
			$query[] = sprintf('UPDATE #__ariquizquizcategory SET CategoryId = %1$d WHERE CategoryId IN (%2$s)',
				$newCategoryId,
				$catStr
			);
		}

		foreach ($query as $queryItem)
		{
			$db->setQuery($queryItem);
			$db->execute();
		}
		
		$result = true;
		foreach ($idList as $catId)
		{
			$cat = $this->getTable('category');
			$cat->load($catId);
			if (!$cat->delete($catId, true))
				$result = false;
		}

		return $result;
	}
	
	function _applyFilter($query, $filter)
	{
		if (empty($filter))
			return $query;		
					
		$filterPredicates = $filter->getConfigValue('filter');
		if (!empty($filterPredicates['IgnoreCategoryId']))
		{
			$query->from(
				sprintf(
					'(SELECT T.lft AS lft,T.rgt AS rgt FROM #__ariquizcategory T WHERE T.CategoryId = %d) IC',
					$filterPredicates['IgnoreCategoryId']
				)
			);
			$query->where('(C.lft < IC.lft OR C.rgt > IC.rgt)');
		}
		
		return $query;
	}
	
	function orderUp($categoryId)
	{
		$categoryId = intval($categoryId, 10);
		
		if ($categoryId < 1)
			return false;

		$cat = $this->getTable('category');
		if (!$cat->load($categoryId))
			return false;

		$parentCat = $this->getTable('category');
		if (!$parentCat->load($cat->parent_id))
			return false;

		$res = $cat->orderUp(null);
		$cat->rebuild($parentCat->CategoryId, $parentCat->lft, $parentCat->level, $parentCat->path);

		return $res;
	}

	function orderDown($categoryId)
	{
		$categoryId = intval($categoryId, 10);
		
		if ($categoryId < 1)
			return false;

		$cat = $this->getTable('category');
		if (!$cat->load($categoryId))
			return false;
			
		$parentCat = $this->getTable('category');
		if (!$parentCat->load($cat->parent_id))
			return false;

		$res = $cat->orderDown(null);
		$cat->rebuild($parentCat->CategoryId, $parentCat->lft, $parentCat->level, $parentCat->path);
		
		return $res;
	}
	
	function rebuild()
	{
		$cat = $this->getTable('category');
		return $cat->rebuild();
	}
	
	function getCategoriesEndPoints()
	{
		$points = array();
		$db =& $this->getDBO();
		
		$db->setQuery(
			'SELECT parent_id,MIN(lft) AS StartLft, MAX(lft) AS EndLft FROM #__ariquizcategory WHERE lft > 0 GROUP BY parent_id'
		);
		$result = $db->loadObjectList();
		if (is_null($result))
		{
			return $points;
		}
		
		foreach ($result as $point)
		{
			$points[$point->parent_id] = $point;
		}
		
		return $points;
	}

    function getCategoriesAccessLevels()
    {
        $db = $this->getDBO();

        $db->setQuery(
            sprintf(
                'SELECT DISTINCT P.CategoryId,P.parent_id AS ParentCategoryId,P.access AS Access FROM #__ariquizcategory P,#__ariquizcategory C WHERE P.lft <= C.lft AND P.rgt >= C.rgt AND P.lft > 0 ORDER BY P.lft ASC'
            )
        );

        $tree = $db->loadObjectList('CategoryId');
        if (is_null($tree))
        {
            return null;
        }

        foreach ($tree as $catId => &$cat)
        {
            if ($cat->Access != -1 || !isset($tree[$cat->ParentCategoryId]))
                continue ;

            $parentCat =& $tree[$cat->ParentCategoryId];
            do
            {
                if ($parentCat->Access != -1)
                {
                    $cat->Access = $parentCat->Access;
                    break;
                }

                if (isset($tree[$parentCat->ParentCategoryId]))
                    $parentCat =& $tree[$parentCat->ParentCategoryId];
                else
                    $parentCat = null;
            } while ($parentCat && $parentCat->Access == -1);
        }

        return $tree;
    }
	
	function getCategoriesTree($idList, $parentCategoryId = 0)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return null;
		
		$db =& $this->getDBO();
		if ($parentCategoryId > 0)
		{
			$db->setQuery(
				sprintf(
					'SELECT DISTINCT P.CategoryId,P.parent_id AS ParentCategoryId,P.level,P.CategoryName,P.Description FROM #__ariquizcategory P,#__ariquizcategory C,#__ariquizcategory L WHERE L.CategoryId = %2$d AND C.CategoryId IN (%1$s) AND P.lft <= C.lft AND P.rgt >= C.rgt AND P.lft >= L.lft AND P.rgt <= L.rgt ORDER BY P.lft ASC',
					join(',', $idList),
					$parentCategoryId
				)
			);
		}
		else
		{
			$db->setQuery(
				sprintf(
					'SELECT DISTINCT P.CategoryId,P.parent_id AS ParentCategoryId,P.level,P.CategoryName,P.Description FROM #__ariquizcategory P,#__ariquizcategory C WHERE C.CategoryId IN (%1$s) AND P.lft <= C.lft AND P.rgt >= C.rgt AND P.lft > 0 ORDER BY P.lft ASC',
					join(',', $idList)
				)
			);
		}
		$tree = $db->loadObjectList('CategoryId');
		if (is_null($tree))
		{
			return null;
		}

		return $tree;
	}
}