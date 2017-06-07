<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/06/05
 * Time: 16:27
 */
class Controller_Searchmap_Rest extends Controller_Rest
{
	public function post_latlng()
	{
		//error_log(var_export($_POST, true) . 'ajax2' . PHP_EOL);

		$data = Model_Registermap::find(array(
			'select' => array('register_map_id', 'lat', 'lng', 'place', ),
			'where' => array(
				array('lat', '>=', Input::post('f_b')),
				array('lat', '<=', Input::post('f_f')),
				array('lng', '>=', Input::post('b_b')),
				array('lng', '<=', Input::post('b_f')),
			),
		));

		return $this->response($data);
	}
}