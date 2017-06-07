<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/05/15
 * Time: 15:27
 */

/**
 * The Mypage Password Controller.
 *
 * 会員情報変更
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Mypage_Profile extends Controller
{
	/**
	 * プロフィールの値検証
	 *
	 * @access  private
	 * @return  Validation
	 */
	private function profile_validate()
	{
		$val = Validation::forge();
		$val->add_callable('Component_Validation');
		$val->add('username', 'ユーザー名')
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule(
				'username_double_check', true
			);
		$val->add('email', 'メールアドレス')
			->add_rule('required')
			->add_rule('valid_email')
			->add_rule('email_double_check', true);
		$val->add('pr', '自己紹介（PR）')
			->add_rule('max_length', 1000);
		$val->add('cat', '好きな猫')
			->add_rule('max_length', 50);

		return $val;
	}

	/**
	 * 会員情報変更入力画面
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_index()
	{
		$data['title'] = 'プロフィールの編集';

		return View_Smarty::forge('mypage/profile/index', $data);
	}

	/**
	 * 会員情報変更確認画面。値検証エラーの場合は入力画面表示
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_confirm()
	{
		$error_arr = array();
		$is_thumb = true;
		$val = $this->profile_validate();
		$config = array(
			'path'        => DOCROOT . 'assets/img/user',
			'new_name'    => Auth::get_user_id()[1] . '_tmp',
			'max_size'    => 1048576, // 1MB
			'overwrite'   => true,
			'auto_rename' => false,
			//'randomize'     => true,
			//'ext_whitelist' => array('img', 'jpg', 'jpeg', 'gif', 'png'),
			'ext_whitelist' => array('jpg'),
		);
		Upload::process($config);
		$file = Upload::get_errors()[0];
		if ($file and ($file['name'] != '' or $file['type'] != '' or $file['size'] != 0))
		{
			$is_thumb = false;
			foreach ($file['errors'] as $error)
			{
				//class FileError
				if ($error['error'] == 101 or $error['error'] = 1) $error_arr['image'] = 'ファイルサイズが許容量を超えています';
				else $error_arr['image'] = $error['message'];

				break;
			}
		}

		if ($val->run() and $is_thumb)
		{
			if (Upload::is_valid()) Upload::save();
			$data['user_image'] = Upload::get_files()[0]['saved_as'];
			$data['title'] = 'プロフィールの編集確認';

			return View_Smarty::forge('mypage/profile/confirm', $data);
		}
		else
		{
			foreach ($val->error() as $key => $error) $error_arr[$key] = $error . '';
			$error_arr['title'] = 'プロフィール編集エラー';

			return View_Smarty::forge('mypage/profile/index', $error_arr);
		}
	}

	/**
	 * 会員情報変更、成功時は成功画面。値検証エラーの場合は入力画面表示
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_modify()
	{
		$val = $this->profile_validate();
		$error_arr = array();

		if ($val->run())
		{
			try
			{
				$is_update_user = Auth::update_user(
					array(
						'email'    => Input::post('email'),
						'pr'       => Input::post('pr'),
						'cat'      => Input::post('cat'),
					)
				);

				if ($is_update_user)
				{
					$user_img_dir_path = DOCROOT . 'assets/img/user/';
					$user_img_tmp_path = $user_img_dir_path . Auth::get_user_id()[1] . '_tmp.jpg';
					if (File::exists($user_img_tmp_path))
					{
						File::rename(
							$user_img_tmp_path, $user_img_dir_path . Auth::get_user_id()[1] . '.jpg'
						);
					}

					if (Input::post('username') == Auth::get_screen_name())
					{
						$data['title'] = 'プロフィール編集完了';

						return View_Smarty::forge('mypage/profile/modify', $data);
					}
					else
					{
						$model_user = Model_Users::find_one_by('username', Auth::get_screen_name());
						$model_user->set(array('username' => Input::post('username')));
						if ($model_user->save()) Response::redirect("/user/login");
						else $error_arr['username'] = 'ユーザ名を編集できませんでした';
					}
				}
				else
				{
					$error_arr['username'] = 'プロフィールを編集できませんでした';
				}
			}
			catch (Exception $e)
			{
				$error_arr['username'] = $e->getMessage();
			}
		}
		else
		{
			foreach ($val->error() as $key => $error) $error_arr[$key] = $error . '';
		}
		$error_arr['title'] = 'プロフィール編集エラー';

		return View_Smarty::forge('mypage/profile/index', $error_arr);
	}
}