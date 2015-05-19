<?php

namespace Slimore\Mvc;

class Model
{
	private   $app;
	protected $db;

	public function __construct()
	{
		$this->app = \Slimore\Mvc\Application::getInstance();
	}
}