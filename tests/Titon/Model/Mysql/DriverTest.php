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
		$this->assertEquals('sqlite:', $this->object->getDsn());

		$this->object->config->memory = true;
		$this->assertEquals('sqlite::memory:', $this->object->getDsn());

		$this->object->config->path = '/path/to/sql.db';
		$this->assertEquals('sqlite:/path/to/sql.db', $this->object->getDsn());

		$this->object->config->dsn = 'custom:dsn';
		$this->assertEquals('custom:dsn', $this->object->getDsn());
	}

}