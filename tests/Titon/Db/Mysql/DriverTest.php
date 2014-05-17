<?php
namespace Titon\Db\Mysql;

use Titon\Common\Config;
use Titon\Test\Stub\Repository\User;

/**
 * @property \Titon\Db\Mysql\MysqlDriver $object
 */
class DriverTest extends \Titon\Db\Driver\PdoDriverTest {

    protected function setUp() {
        $this->object = new MysqlDriver(Config::get('db'));
        $this->object->connect();

        $this->table = new User();
    }

    public function testGetDsn() {
        $this->assertEquals('mysql:dbname=titon_test;host=127.0.0.1;port=3306;charset=utf8', $this->object->getDsn());

        $this->object->setConfig('port', 1337);
        $this->assertEquals('mysql:dbname=titon_test;host=127.0.0.1;port=1337;charset=utf8', $this->object->getDsn());

        $this->object->setConfig('socket', '/path/to/unix.sock');
        $this->assertEquals('mysql:dbname=titon_test;unix_socket=/path/to/unix.sock;charset=utf8', $this->object->getDsn());

        $this->object->setConfig('dsn', 'custom:dsn');
        $this->assertEquals('custom:dsn', $this->object->getDsn());
    }

}