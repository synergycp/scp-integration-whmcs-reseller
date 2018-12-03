<?php

use Scp\WhmcsReseller\App;

class TestCase extends PHPUnit_Framework_TestCase {
	/**
	 * @return App
	 */
	protected function newApp() {
		return new App(['']);
	}

	/**
	 * @return App
	 */
	protected function app() {
		return App::get(['']);
	}
}
