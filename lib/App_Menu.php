<?php
/**
 * Application Menu Static Class
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

class App_Menu
{
	private static $_menu = array();

	public static function addMenu($menu)
	{
		if (empty(self::$_menu[ $menu ])) {
			self::$_menu[ $menu ] = array();
		}
		//self::$_menu[ $menu ] = array(
		//	'name' => $name,
		//	'link' => $link,
		//	'menu' => array(),
		//);
	}

	/**
		Add Items to a Menu
	*/
	public static function addMenuItem($menu, $link, $name, $sort=0)
	{
		if (empty(self::$_menu[ $menu ])) {
			self::$_menu[ $menu ] = array();
		}

		if (empty($sort)) {
			$sort = count(self::$_menu[ $menu ]);
		}

		self::$_menu[ $menu ][$sort] = array(
			'name' => $name,
			'link' => $link,
		);
	}

	/**
		Return the Specific Menu
	*/
	public static function getMenu($menu)
	{
		if (empty(self::$_menu[$menu])) {
			return array();
		}
		$m = self::$_menu[$menu];
		ksort($m, SORT_NUMERIC);
		return $m;
	}

}
