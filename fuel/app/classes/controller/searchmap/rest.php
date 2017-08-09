<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/06/05
 * Time: 16:27
 * The Searchmap Rest Controller_Rest.
 *
 * 地点検索結果表示用ajax
 *
 * @package  app
 * @extends  Controller_Rest
 */
class Controller_Searchmap_Rest extends Controller_Rest
{
	/**
	 * 地点検索結果pin立て用データ
	 *
	 * @access  public
	 * @return  Response
	 */
	public function post_latlng()
	{
		//error_log(var_export($_POST, true) . 'ajax2' . PHP_EOL);
		//		$data = Model_Registermap::find(
		//			array(
		//				'select'   => array('register_map_id', 'lat', 'lng', 'place', 'address', 'description', 'image', 'created_at'),
		//				'where'    => array(
		//					array('lat', '>=', Input::post('f_b')),
		//					array('lat', '<=', Input::post('f_f')),
		//					array('lng', '>=', Input::post('b_b')),
		//					array('lng', '<=', Input::post('b_f')),
		//					'status_flag' => 1,
		//				),
		//				'order_by' => array(
		//					'views' => 'desc',
		//				),
		//			)
		//		);
		//error_log('ajaxtuuka' . PHP_EOL);
		$data = DB::select(
			'register_map_id', 'lat', 'lng', 'place', 'address', 'description', 'image', 'register_map.created_at',
			'username'
		)
			->from('register_map')
			->join('users', 'inner')
			->on('users_id', '=', 'id')
			->where(
				array(
					array('lat', '>=', Input::post('f_b')),
					array('lat', '<=', Input::post('f_f')),
					array('lng', '>=', Input::post('b_b')),
					array('lng', '<=', Input::post('b_f')),
					'status_flag' => 1,
				)
			)
			->execute()
			->as_array();
		//error_log(var_export($data, true) . 'ajax2' . PHP_EOL);
		foreach ($data as $key => $val)
		{
			$data[$key]['created_at'] = date('Y年m月d日 H時i分', $val['created_at']);
		}

		return $this->response($data);
	}

	/**
	 * 地点投稿登録と登録データ送信
	 *
	 * @access  public
	 * @return  Response
	 */
	public function post_comment()
	{
		$val = Validation::forge();
		$val->add('register_map_id', 'マップ登録番号')
			->add_rule('required')
			->add_rule('valid_string', 'numeric');
		$val->add('comment', 'コメント')
			->add_rule('required')
			->add_rule('max_length', 1000);
		$val->add('max_comment_id', 'comment_id')
			->add_rule('required')
			->add_rule('valid_string', 'numeric');

		$data = array();
		$data['error'] = null;

		// 画像
		$is_image = true;
		if (Input::post('image') != 'null')
		{
			$config = array(
				'path'        => DOCROOT . 'assets/img/comment',
				'max_size'    => 1048576, // 1MB
				'overwrite'   => false,
				'auto_rename' => true,
				'randomize'   => true,
			);
			Upload::process($config);
			$file = Upload::get_errors()[0];
			if ($file and ($file['name'] != '' or $file['type'] != '' or $file['size'] != 0))
			{
				$is_image = false;
				foreach ($file['errors'] as $error)
				{
					if ($error['error'] == 101 or $error['error'] = 1) $data['error']['image'] = 'ファイルサイズが許容量を超えています';
					else $data['error']['image'] = $error['message'];

					break;
				}
			}
		}

		if ($val->run() and $is_image)
		{
			if (Input::post('image') != 'null')
			{
				if (Upload::is_valid()) Upload::save();
				$data['image'] = Upload::get_files()[0]['saved_as'];
			}
			//DBの登録
			$model_comment = Model_Comment::forge();
			$model_comment->set(
				array(
					'register_map_id' => Input::post('register_map_id'),
					'users_id'        => Auth::get_user_id()[1],
					'comment'         => Input::post('comment'),
					'image'           => $data['image'],
					'status_flag'     => 1,
				)
			);
			if ( ! $model_comment->save())
			{
				$data['error']['db'] = 'データの登録を失敗しました';
			}
			error_log(var_export("通貨１", true) . 'ajax2' . PHP_EOL);
			$data['comment'] = DB::select(
				'comment_id', 'users_id', 'comment', 'image', 'comment.created_at', 'username'
			)
				->from('comment')
				->join('users', 'inner')
				->on('users_id', '=', 'id')
				->where(
					array(
						'register_map_id' => Input::post('register_map_id'),
						'status_flag'     => 1,
					)
				)
				->where('comment_id', '>', Input::post('max_comment_id'))
				->order_by('comment_id')
				->execute()
				->as_array();
			error_log(var_export($data['comment'], true) . 'ajax3' . PHP_EOL);

			return $this->response($data);
		}
		else
		{
			foreach ($val->error() as $key => $error) $data['error'][$key] = $error . '';

			return $this->response($data);
		}
	}

	/**
	 * 地点投稿削除
	 *
	 * @access  public
	 * @return  Response
	 */
	function post_delete_comment()
	{
		//error_log(var_export($_POST, true) . 'ajax2' . PHP_EOL);
		Model_Comment::forge()
			->set(
				array(
					'comment_id'  => Input::post('comment_id'),
					'status_flag' => 3,
				)
			)
			->is_new(false)
			->save(false);

		return $this->response();
	}

	/**
	 * マイ地点登録
	 *
	 * @access  public
	 * @return  Response
	 */
	function post_register_my_point()
	{
		Model_Mypoint::forge()
			->set(
				array(
					'register_map_id' => Input::post('register_map_id'),
					'users_id'        => Auth::get_user_id()[1],
				)
			)
			->save(false);

		return $this->response();
	}
}