<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/05/10
 * Time: 13:28
 */

/**
 * The Mypage Index Controller.
 *
 * マイページの初期表示
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Mypage_Index extends Controller
{
	/**
	 * マイページのプロフィール
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_index()
	{
		$data['title'] = 'プロフィール';
		$data['user_image'] = null;
		if (File::exists(DOCROOT . 'assets/img/user/' . Auth::get_user_id()[1] . '.jpg'))
		{
			$data['user_image'] = 'user/' . Auth::get_user_id()[1] . '.jpg';
		}

		return View_Smarty::forge('mypage/index', $data);
	}
}