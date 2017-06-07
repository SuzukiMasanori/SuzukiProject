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
 * 新規地点登録ページ
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Registermap_Index extends Controller
{
	/**
	 * 新規地点登録ページ初期表示
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_index()
	{
		$data['title'] = '新規地点登録';

		return View_Smarty::forge('registermap/index', $data);
	}
}