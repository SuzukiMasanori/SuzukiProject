<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/05/17
 * Time: 15:35
 */

/**
 * The Registermap Index Controller.
 *
 * 地点検索初期表示
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Searchmap_Index extends Controller
{
	/**
	 * 地点検索ページ初期表示
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_index()
	{
		//echo '地図検索';
		$data['title'] = 'マップ検索';

		return View_Smarty::forge('searchmap/index', $data);
	}
}