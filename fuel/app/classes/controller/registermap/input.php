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
		$val->add('desc', '概要(説明)')
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
		if ( ! Security::check_token()) Response::redirect('/registermap/index');

		$is_thumb = true;
		$config = array(
			'path'          => DOCROOT . 'assets/img/registermap',
			'max_size'      => 1048576, // 1MB
			'overwrite'     => false,
			'auto_rename'   => true,
			'randomize'     => true,
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
		if ( ! Security::check_token()) Response::redirect('/registermap/index');

		$val = $this->registermap_validate();
		if ($val->run())
		{
			$model_registermap = Model_Registermap::forge();
			$model_registermap->set(
				array(
					'users_id' => Auth::get_user_id()[1],
					'lat'     => Session::get('lat'),
					'lng'     => Session::get('lng'),
					'address' => Input::post('address'),
					'place'   => Input::post('place'),
					'desc'    => Input::post('desc'),
					'num'     => Input::post('num'),
					'type'    => Input::post('type') == 0 ? count($this->types) - 1 : Input::post('type'),
					'parking' => Input::post('parking') == 0 ? count($this->parkings) - 1 : Input::post(
						'parking'
					),
					'feed'    => Input::post('feed'),
					'image'   => Session::get('registermap_thumb'),
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
}