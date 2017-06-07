<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/05/10
 * Time: 9:51
 */

/**
 * The User Login Controller.
 *
 * 会員ログイン
 *
 * @package  app
 * @extends  Controller
 */
class Controller_User_Login extends Controller
{
	/**
	 * ログイン時最初に表示される入力画面
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_index()
	{
		$data['title'] = 'ログイン';

		return View_Smarty::forge('user/login', $data);
	}

	/**
	 * ログイン処理、成功した場合はトップページへリダイレクト
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_login()
	{
		if (Input::post('keep_user') == 'on')
		{
			Session::set('input_username', Input::post('username'));
			Session::set('input_password', Input::post('password'));
			Session::set('keep_user', 'checked');
		}
		else
		{
			Session::delete('input_username');
			Session::delete('input_password');
			Session::delete('keep_user');
		}

		if (Auth::login(Input::post('username'), Input::post('password')))
		{
			Response::redirect("/");
		}
		else
		{
			$error_arr['alert'] = 'ログイン情報が違います';
			$error_arr['title'] = 'ログインエラー';

			return View_Smarty::forge('user/login', $error_arr);
		}
	}

	/**
	 * ログアウト処理、トップページへリダイレクト
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_logout()
	{
		Auth::logout();
		Response::redirect("/");
	}
}