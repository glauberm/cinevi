<?php

/**
 * Support for generated tree lists
 * 
 * @version		$Id$
 * @package		ARTIO JoomLIB
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class ATree
{

    /**
     * Get items sorted into setting-off tree by family hiearchy 
     * 
     * @param array $fullList all family members, no filterings
     * @param array $filterList filter suitable family members 
     * @param int $limitstart start list number
     * @param int $limit list size
     * @return array family tree
     */
    function getListTree($fullList, $filterList = null, $limitstart = null, $limit = null)
    {
        $family = ATree::getFamily($fullList);
        $family = ATree::getFamilyTree($family);
        if (! is_null($filterList)) {
            $family = ATree::cleanFamily($family, $filterList);
        }
        if ($limit) {
                    
            $family = array_slice($family, 0, 2);
            
        }
        return $family;
    }

    /**
     * Get items distributed in sets by branches 
     * 
     * @param array $members family members
     * @return array family in branchs sets 
     */
    function getFamily($members)
    {
        $family = array();
        if (is_array($members) && count($members)) {
            foreach ($members as $member) {
                $member->parent_id = $member->parent;
                if (! isset($member->name) && isset($member->title)) {
                    $member->name = $member->title;
                }
                $branch = isset($family[$member->parent]) ? $family[$member->parent] : array();
                $branch[] = $member;
                $family[$member->parent] = $branch;
            }
        }
        return $family;
    }

    /**
     * Get family tree with setting-off format
     * 
     * @param array $family family in branches sets
     * @return array family tree with setting-off
     */
    function getFamilyTree($family)
    {
        return JHTML::_('menu.treerecurse', 0, '', array(), $family);
    }

    /**
     * Get family tree contains only members suitable page filter
     * 
     * @param array $family complet family tree
     * @param array $searched members filter suitable
     * @return array finite family tree
     */
    function cleanFamily($family, $searched)
    {
        $cleanFamily = array();
        foreach ($family as $member) {
            foreach ($searched as $search) {
                if ($member->id == $search->id) {
                    $cleanFamily[] = $member;
                    continue;
                }
            }
        }
        return $cleanFamily;
    }
}
?>