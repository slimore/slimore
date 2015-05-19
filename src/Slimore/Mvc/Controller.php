<?php

namespace Slimore\Mvc;

class Controller
{
	protected $app;

	public function __construct()
	{
		$this->app = \Slimore\Mvc\Application::getInstance();
	}
}