<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/06/05
 * Time: 13:15
 */

/**
 * The Registermap result Controller.
 *
 * 地点検索結果表示
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Searchmap_Result extends Controller
{
	/**
	 * 地点検索結果初期表示
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_index()
	{
		//echo '地図検索結果';
		//echo '<br>' . Input::get('pref');
		$data['title'] = 'マップ検索';
		//error_log('testsuzuki');/////////////////////////////////////////////////エラーの掃き出し場所

		return View_Smarty::forge('searchmap/result', $data);
	}
}