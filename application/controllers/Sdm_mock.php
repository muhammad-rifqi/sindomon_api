<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sdm_mock extends CI_Controller
{

	public function index_get()
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'data'    => [
					['id' => '1'],
				],
			]));
	}

	public function index_post()
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'data'    => [
					['id' => '1'],
				],
			]));
	}
}
