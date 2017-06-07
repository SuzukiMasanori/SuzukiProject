<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/05/12
 * Time: 9:19
 */

/**
 * The Mypage Leave Controller.
 *
 * 会員退会
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Mypage_Leave extends Controller
{
	/**
	 * 退会処理のパスワード入力画面
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_index()
	{
		$data['title'] = '退会';

		return View_Smarty::forge('mypage/leave/index', $data);
	}

	/**
	 * 退会処理
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_quit()
	{
		$val = Validation::forge();
		$val->add_callable('Component_Validation');
		$val->add('password', 'パスワード')->add_rule('required')->add_rule('password_confirm');

		if ($val->run())
		{
			Auth::delete_user(Auth::get_screen_name());
			Auth::logout();
			$data['title'] = '退会完了';

			return View_Smarty::forge('mypage/leave/quit', $data);
		}
		else
		{
			$error_arr = array();
			foreach ($val->error() as $key => $error) $error_arr[$key] = $error . '';
			$error_arr['title'] = '退会エラー';

			return View_Smarty::forge('mypage/leave/index', $error_arr);
		}
	}
}