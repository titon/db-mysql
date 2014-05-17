<?php
namespace Titon\Db\Mysql;

class RepositoryTest extends \Titon\Db\RepositoryTest {

    public function testSelect() {
        $query = new MysqlQuery(MysqlQuery::SELECT, $this->object);
        $query->from($this->object->getTable(), 'User')->fields('id', 'username');

        $this->assertEquals($query, $this->object->select('id', 'username'));
    }

}