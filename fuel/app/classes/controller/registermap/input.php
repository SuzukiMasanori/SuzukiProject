<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/05/19
 * Time: 9:27
 */

/**
 * The Registermap Input Controller.
 *
 * 新規地点登録ページ
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Registermap_Input extends Controller
{
	/**
	 * 猫の匹数設定
	 *
	 * @access  private
	 * @var array
	 */
	private $numbers = array();

	/**
	 * 地点のタイプ
	 *
	 * @access  private
	 * @var array
	 */
	private $types = array();

	/**
	 * 駐車場の有無
	 *
	 * @access  private
	 * @var array
	 */
	private $parkings = array();

	/**
	 * フィールドの初期化
	 */
	function __construct(\Request $request)
	{
		parent::__construct($request);
		$range = range(0, 50);
		unset($range[0]);
		$this->numbers = $range;
		$this->types = Component_Element::$map_types;
		$this->parkings = Component_Element::$parkings;
	}

	/**
	 * 地点登録の値検証
	 *
	 * @access  private
	 * @return  Validation
	 */
	private function registermap_validate()
	{
		$val = Validation::forge();
		$val->add_callable('Component_Validation');
		$val->add('address', '地名')
			->add_rule('required')
			->add_rule('max_length', 50);
		$val->add('place', '地点タイトル')
			->add_rule('required')
			->add_rule('max_length', 50);
		$val->add('description', '概要(説明)')
			->add_rule('max_length', 1000);
		$val->add('num', '匹数')
			->add_rule('numeric_min', 1)
			->add_rule('numeric_max', 50);
		$val->add('type', '地点タイプ')
			->add_rule('numeric_min', 0)
			->add_rule('numeric_max', 7);
		$val->add('parking', '駐車')
			->add_rule('numeric_min', 0)
			->add_rule('numeric_max', 6);
		$val->add('feed', '餌やり')
			->add_rule('required')
			->add_rule('numeric_min', 1)
			->add_rule('numeric_max', 3);
		$val->add('friendly', 'なつき度(5段階)')
			->add_rule('numeric_min', 0)
			->add_rule('numeric_max', 5);

		return $val;
	}

	/**
	 * 地点登録画像の値検証
	 *
	 * @access  private
	 * @return  bool
	 */
	private function registermap_validate_image(&$data)
	{
		$is_thumb = true;
		$config = array(
			'path'        => DOCROOT . 'assets/img/registermap',
			'max_size'    => 1048576, // 1MB
			'overwrite'   => false,
			'auto_rename' => true,
			'randomize'   => true,
		);
		Upload::process($config);
		$file = Upload::get_errors()[0];
		if ($file and ($file['name'] != '' or $file['type'] != '' or $file['size'] != 0))
		{
			$is_thumb = false;
			foreach ($file['errors'] as $error)
			{
				if ($error['error'] == 101 or $error['error'] = 1) $data['image'] = 'ファイルサイズが許容量を超えています';
				else $data['image'] = $error['message'];

				break;
			}
		}

		return $is_thumb;
	}

	/**
	 * 新規地点登録吹き出し表示
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_index()
	{
		if ( ! is_numeric(Input::get('lat')) or ! is_numeric(Input::get('lng')))
		{
			Response::redirect('/registermap/index');
		}

		$data['title'] = '新規地点登録';

		return View_Smarty::forge('registermap/input/index', $data);
	}

	/**
	 * 新規地点登録詳細入力
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_show()
	{
		if ( ! is_numeric(Input::get('lat')) or ! is_numeric(Input::get('lng')))
		{
			Response::redirect('/registermap/index');
		}
		Session::set('lat', Input::get('lat'));
		Session::set('lng', Input::get('lng'));
		/*$address = file_get_contents(
			'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . Input::get('lat') . ',' . Input::get(
				'lng'
			) . '&language=ja&key=AIzaSyDVGyFdA0VNkFp2rW8okx6Hc4LNOYvhqHo&sensor=false'
		);
		$res = json_decode($address, true);
		echo '<pre>';
		print_r($res);
		echo '</pre>';


		$address_arr = array_reverse($res['results'][0]['address_components']);
		echo '<pre>';
		print_r($address_arr);
		echo '</pre>';*/

		$address = file_get_contents(
			'http://reverse.search.olp.yahooapis.jp/OpenLocalPlatform/V1/reverseGeoCoder?lat=' . Input::get(
				'lat'
			) . '&lon=' . Input::get(
				'lng'
			) . '&appid=dj0zaiZpPUJkUERJM05xYWR5OSZzPWNvbnN1bWVyc2VjcmV0Jng9N2M-&output=json'
		);
		$res = json_decode($address, true);
		//		echo '<pre>';
		//		print_r($res);
		//		echo '</pre>';

		$data['geo_address'] = $res['Feature'][0]['Property']['Address'];
		$data['numbers'] = $this->numbers;
		$data['types'] = $this->types;
		$data['parkings'] = $this->parkings;
		$data['title'] = '新規地点登録詳細入力';

		return View_Smarty::forge('registermap/input/show', $data);
	}

	/**
	 * 新規地点登録確認
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_confirm()
	{
		// クロスサイトリクエストフォージェリチェック;
		//if ( ! Security::check_token()) Response::redirect('/registermap/index');

		$is_thumb = true;
		$config = array(
			'path'        => DOCROOT . 'assets/img/registermap',
			'max_size'    => 1048576, // 1MB
			'overwrite'   => false,
			'auto_rename' => true,
			'randomize'   => true,
		);
		Upload::process($config);
		$file = Upload::get_errors()[0];
		if ($file and ($file['name'] != '' or $file['type'] != '' or $file['size'] != 0))
		{
			$is_thumb = false;
			foreach ($file['errors'] as $error)
			{
				if ($error['error'] == 101 or $error['error'] = 1) $data['image'] = 'ファイルサイズが許容量を超えています';
				else $data['image'] = $error['message'];

				break;
			}
		}
		$val = $this->registermap_validate();
		if ($val->run() and $is_thumb)
		{
			if (Upload::is_valid()) Upload::save();
			$data['user_image'] = Upload::get_files()[0]['saved_as'];
			Session::set('registermap_thumb', $data['user_image']);
			$data['type'] = $this->types[Input::post('type') == 0 ? count($this->types) - 1 : Input::post('type')];
			$data['parking'] = $this->parkings[Input::post('parking') == 0 ? count($this->parkings) - 1 : Input::post(
				'parking'
			)];
			$data['title'] = '新規地点登録確認';

			return View_Smarty::forge('registermap/input/confirm', $data);
		}
		else
		{
			foreach ($val->error() as $key => $error) $data[$key] = $error . '';
			$data['title'] = '新規地点登録エラー';
			$data['numbers'] = $this->numbers;
			$data['types'] = $this->types;
			$data['parkings'] = $this->parkings;

			return View_Smarty::forge('registermap/input/show', $data);
		}
	}

	/**
	 * 新規地点登録完了
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_register()
	{
		// クロスサイトリクエストフォージェリチェック;
		//if ( ! Security::check_token()) Response::redirect('/registermap/index');

		$val = $this->registermap_validate();
		if ($val->run())
		{
			$model_registermap = Model_Registermap::forge();
			$model_registermap->set(
				array(
					'users_id'    => Auth::get_user_id()[1],
					'lat'         => Session::get('lat'),
					'lng'         => Session::get('lng'),
					'address'     => Input::post('address'),
					'place'       => Input::post('place'),
					'description' => Input::post('description'),
					'num'         => Input::post('num'),
					'type'        => Input::post('type') == 0 ? count($this->types) - 1 : Input::post('type'),
					'parking'     => Input::post('parking') == 0 ? count($this->parkings) - 1 : Input::post(
						'parking'
					),
					'feed'        => Input::post('feed'),
					'friendly'    => Input::post('friendly'),
					'image'       => Session::get('registermap_thumb'),
					'status_flag' => 1,
				)
			);
			if ($model_registermap->save())
			{
				$data['title'] = '新規地点登録完了';

				return View_Smarty::forge('registermap/input/register', $data);
			}
			else
			{
				$error_arr['address'] = 'データの登録を失敗しました';
				$error_arr['title'] = '新規地点登録エラー';

				return View_Smarty::forge('registermap/input/show', $error_arr);
			}
		}
		else
		{
			foreach ($val->error() as $key => $error) $error_arr[$key] = $error . '';
			$error_arr['title'] = '新規地点登録エラー';

			return View_Smarty::forge('registermap/input/show', $error_arr);
		}
	}

	/**
	 * 地点編集入力
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_edit()
	{
		if ( ! is_numeric(Input::get('register_map_id')) or ! Auth::check())
		{
			Response::redirect('/searchmap/index');
		}
		$registermap = Model_Registermap::find_by_pk(Input::get('register_map_id'));
		if (is_null($registermap))
		{
			Response::redirect('/searchmap/index');
		}
		//error_log(var_export($registermap, true) . PHP_EOL . 'registermap_edit');
		//error_log(var_export($registermap->address, true) . PHP_EOL . 'registermap_edit2');
		$data['registermap'] = $registermap;
		$data['numbers'] = $this->numbers;
		$data['types'] = $this->types;
		$data['parkings'] = $this->parkings;
		$data['title'] = '登録地点更新入力';

		return View_Smarty::forge('registermap/input/show-edit', $data);
	}

	/**
	 * 地点編集確認
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_confirm_edit()
	{
		// クロスサイトリクエストフォージェリチェック;
		//if ( ! Security::check_token()) Response::redirect('/registermap/index');
		$data = array();
		$is_thumb = $this->registermap_validate_image($data);
		$val = $this->registermap_validate();
		$data['registermap'] = Model_Registermap::find_by_pk(Input::post('register_map_id'));
		if ($is_thumb and $val->run())
		{
			if (Upload::is_valid()) Upload::save();
			$data['user_image'] = Upload::get_files()[0]['saved_as'];
			if ( ! $data['user_image'])
			{
				$data['user_image'] = $data['registermap']->image;
			}
			Session::set('registermap_thumb', $data['user_image']);
			//echo $data['user_image'] . 'すずき';

			$data['type'] = $this->types[Input::post('type') == 0 ? count($this->types) - 1 : Input::post('type')];
			$data['parking'] = $this->parkings[Input::post('parking') == 0 ? count(
					$this->parkings
				) - 1 : Input::post('parking')];
			$data['title'] = '登録地点更新入力確認';

			return View_Smarty::forge('registermap/input/confirm-edit', $data);
		}
		else
		{
			foreach ($val->error() as $key => $error) $data[$key] = $error . '';
			$data['title'] = '登録地点更新エラー';
			$data['numbers'] = $this->numbers;
			$data['types'] = $this->types;
			$data['parkings'] = $this->parkings;

			return View_Smarty::forge('registermap/input/show-edit', $data);
		}
	}

	/**
	 * 地点更新完了
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_register_edit()
	{
		// クロスサイトリクエストフォージェリチェック;
		//if ( ! Security::check_token()) Response::redirect('/registermap/index');

		$val = $this->registermap_validate();
		$file_path = null;
		if ($val->run())
		{
			$model_registermap = Model_Registermap::find_by_pk(Input::post('register_map_id'));
			if ($model_registermap->image and (Input::post('delete_image') or $model_registermap->image != Session::get(
						'registermap_thumb'
					))
			)
			{
				$file_path = Asset::find_file($model_registermap->image, 'img', 'registermap/');
				if ($file_path) File::delete($file_path);
			}
			if (Input::post('delete_image')) $image = null;
			else $image = Session::get('registermap_thumb');

			$model_registermap->set(
				array(
					'address'     => Input::post('address'),
					'place'       => Input::post('place'),
					'description' => Input::post('description'),
					'num'         => Input::post('num'),
					'type'        => Input::post('type') == 0 ? count($this->types) - 1 : Input::post('type'),
					'parking'     => Input::post('parking') == 0 ? count($this->parkings) - 1 : Input::post(
						'parking'
					),
					'feed'        => Input::post('feed'),
					'friendly'    => Input::post('friendly'),
					'image'       => $image,
				)
			)
				->is_new(false);
			if ($model_registermap->save())
			{
				if ($file_path)
				{
					$dir_path = str_replace($model_registermap->image, '', $file_path);
					exec('ls ' . $dir_path, $file_list);
					$model_registermap_images = DB::select('image')
						->where('image', 'IS NOT', DB::expr('NULL'))
						->from('register_map')
						->execute()
						->as_array();
					foreach ($file_list as $file)
					{
						foreach ($model_registermap_images as $model_registermap_image)
						{
							if ($file == $model_registermap_image['image']) break;
							if ($model_registermap_image === end($model_registermap_images))
							{
								File::delete($dir_path . $file);
							}
						}
					}
				}
				Response::redirect('/searchmap/result/place?register_map_id=' . Input::post('register_map_id'));
			}
			else
			{
				$error_arr['address'] = 'データの登録を失敗しました';
				$error_arr['title'] = '地点更新エラー';

				return View_Smarty::forge('registermap/input/show-edit', $error_arr);
			}
		}
		else
		{
			foreach ($val->error() as $key => $error) $error_arr[$key] = $error . '';
			$error_arr['title'] = '地点更新エラー';

			return View_Smarty::forge('registermap/input/show-edit', $error_arr);
		}
	}
}