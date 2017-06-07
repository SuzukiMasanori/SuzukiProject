<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/05/26
 * Time: 15:54
 */

/**
 * DBのデータと表示データを紐づける配列設定
 *
 * @package  app
 * @extends  Controller
 */
class Component_Element
{
	public static $map_types = array(
		'選んでください',
		'公園',
		'寺社仏閣',
		'路地・地域猫',
		'河原・海岸',
		'商業施設',
		'私有地',
		'その他',
	);

	public static $parkings = array(
		'選んでください',
		'不可',
		'近隣にコインパーキング有',
		'有料駐車場有',
		'無料駐車場有',
		'路駐可',
		'不明',
	);
}