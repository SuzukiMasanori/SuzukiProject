<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/05/12
 * Time: 14:43
 */

/**
 * The Mypage Password Controller.
 *
 * 会員パスワード変更
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Mypage_Password extends Controller
{
	/**
	 * パスワード変更入力画面
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_index()
	{
		$data['title'] = 'パスワード変更';

		return View_Smarty::forge('mypage/password/index', $data);
	}

	/**
	 * パスワード変更処理
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_modify()
	{
		$val = Validation::forge();
		$val->add_callable('Component_Validation');
		$val->add('password', 'パスワード')
			->add_rule('required')
			->add_rule('password_confirm');
		$val->add('new_password', 'パスワードの変更')
			->add_rule('required')
			->add_rule(
				'password_same', Input::post('new_password_confirm')
			)
			->add_rule('max_length', 50);;
		$val->add('new_password_confirm', 'パスワードの変更(確認)')
			->add_rule('required');

		if ($val->run())
		{
			Auth::change_password(Input::post('password'), Input::post('new_password'));
			$data['title'] = 'パスワード変更完了';

			return View_Smarty::forge('mypage/password/modify', $data);
		}
		else
		{
			$error_arr = array();
			foreach ($val->error() as $key => $error) $error_arr[$key] = $error . '';
			$error_arr['title'] = 'パスワード変更エラー';

			return View_Smarty::forge('mypage/password/index', $error_arr);
		}
	}
}