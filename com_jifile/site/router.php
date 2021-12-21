<?php
/**
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @param	array
 * @return	array
 */
function JifileBuildRoute(&$query)
{
	// initialize variable
	$segments = array();
	$key 	  = "";

	if (isset($query['view'])) {
		unset($query['view']);
	}
	if (isset($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (isset($query['key'])) {
		$segments[] = $query['key'];
		unset($query['key']);
	}
	if (isset($query['filename'])) {
		$segments[] = $query['filename'];
		unset($query['filename']);
	}
	
	return $segments;
}

/**
 * @param	array
 * @return	array
 */
function JifileParseRoute($segments)
{
	$vars = array();

	switch ($segments[0]) {
		case 'download':
			$vars['task'] 		= $segments[0];
			$vars['key'] 		= $segments[1];
		break;
		default:
			$vars['task'] 		= $segments[0];
			$vars['searchword'] = $segments[1];
			$vars['view'] 		= 'search';
		break;
	}

	return $vars;
}
