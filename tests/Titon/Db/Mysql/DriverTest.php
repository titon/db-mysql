<?php
/**
 * @copyright   2010-2013, The Titon Project
 * @license     http://opensource.org/licenses/bsd-license.php
 * @link        http://titon.io
 */

namespace Titon\Db\Mysql;

use Titon\Common\Config;
use Titon\Test\Stub\Table\User;

/**
 * Test class for driver specific testing.
 */
class DriverTest extends \Titon\Db\Driver\PdoDriverTest {

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new MysqlDriver('default', Config::get('db'));
        $this->object->connect();

        $this->table = new User();
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