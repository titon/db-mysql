<?php
/**
 * @copyright	Copyright 2010-2013, The Titon Project
 * @license		http://opensource.org/licenses/bsd-license.php
 * @link		http://titon.io
 */

namespace Titon\Model\Sqlite;

use Titon\Common\Config;
use Titon\Model\Mysql\MysqlDriver;
use Titon\Test\Stub\Model\User;

/**
 * Test class for driver specific testing.
 */
class DriverTest extends \Titon\Model\Driver\PdoDriverTest {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new MysqlDriver('default', Config::get('db'));
		$this->object->connect();

		$this->model = new User();
	}

	/**
	 * Test DSN building.
	 */
	public function testGetDsn() {
		$this->assertEquals('mysql:dbname=titon_test;host=127.0.0.1;port=3306;charset=utf8', $this->object->getDsn());

		$this->object->config->port = 1337;
		$this->assertEquals('mysql:dbname=titon_test;host=127.0.0.1;port=1337;charset=utf8', $this->object->getDsn());

		$this->object->config->socket = '/path/to/unix.sock';
		$this->assertEquals('mysql:dbname=titon_test;unix_socket=/path/to/unix.sock;charset=utf8', $this->object->getDsn());

		$this->object->config->dsn = 'custom:dsn';
		$this->assertEquals('custom:dsn', $this->object->getDsn());
	}

}