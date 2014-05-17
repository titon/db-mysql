<?php
namespace Titon\Db\Mysql;

use Titon\Common\Config;
use Titon\Db\Driver\Dialect;
use Titon\Db\Driver\Schema;
use Titon\Db\Query;
use Titon\Test\Stub\Repository\User;

/**
 * @property \Titon\Db\Mysql\MysqlDialect $object
 */
class DialectTest extends \Titon\Db\Driver\DialectTest {

    protected function setUp() {
        $this->driver = new MysqlDriver(Config::get('db'));
        $this->driver->connect();

        $this->object = $this->driver->getDialect();
    }

    public function testBuildCreateIndex() {
        parent::testBuildCreateIndex();

        $query = new Query(Query::CREATE_INDEX, new User());
        $query->fields('profile_id')->from('users', 'idx')
            ->attribute([
                'type' => MysqlDialect::FULLTEXT,
                'using' => function(Dialect $dialect) {
                    return sprintf($dialect->getClause(MysqlDialect::USING), $dialect->getKeyword(MysqlDialect::BTREE));
                }
            ]);

        $this->assertRegExp('/CREATE FULLTEXT INDEX (`|\")idx(`|\") ON (`|\")users(`|\") \((`|\")profile_id(`|\")\) USING BTREE/', $this->object->buildCreateIndex($query));
    }

    public function testBuildCreateTable() {
        $schema = new Schema('foobar');
        $schema->addColumn('column', [
            'type' => 'int',
            'ai' => true
        ]);

        $query = new Query(Query::CREATE_TABLE, new User());
        $query->schema($schema);

        $this->assertEquals("CREATE  TABLE IF NOT EXISTS `foobar` (\n`column` INT NOT NULL AUTO_INCREMENT\n);", $this->object->buildCreateTable($query));

        $schema->addColumn('column', [
            'type' => 'int',
            'ai' => true,
            'primary' => true
        ]);

        $this->assertEquals("CREATE  TABLE IF NOT EXISTS `foobar` (\n`column` INT NOT NULL AUTO_INCREMENT,\nPRIMARY KEY (`column`)\n);", $this->object->buildCreateTable($query));

        $schema->addColumn('column2', [
            'type' => 'int',
            'null' => true,
            'index' => true
        ]);

        $this->assertEquals("CREATE  TABLE IF NOT EXISTS `foobar` (\n`column` INT NOT NULL AUTO_INCREMENT,\n`column2` INT NULL,\nPRIMARY KEY (`column`)\n);", $this->object->buildCreateTable($query));

        $schema->addOption('engine', 'InnoDB');

        $this->assertEquals("CREATE  TABLE IF NOT EXISTS `foobar` (\n`column` INT NOT NULL AUTO_INCREMENT,\n`column2` INT NULL,\nPRIMARY KEY (`column`)\n) ENGINE InnoDB;", $this->object->buildCreateTable($query));

        $schema = new Schema('foobar');
        $schema->addColumn('column', [
            'type' => 'int',
            'ai' => true
        ]);

        $query = new Query(Query::CREATE_TABLE, new User());
        $query->schema($schema)->attribute('temporary', true);

        $this->assertEquals("CREATE TEMPORARY TABLE IF NOT EXISTS `foobar` (\n`column` INT NOT NULL AUTO_INCREMENT\n);", $this->object->buildCreateTable($query));
    }

    public function testBuildDeleteQuick() {
        $query = new Query(Query::DELETE, new User());
        $query->from('foobar')->attribute('quick', true);

        $this->assertRegExp('/DELETE\s+QUICK\s+FROM `foobar`;/', $this->object->buildDelete($query));
    }

    public function testBuildDeleteIgnore() {
        $query = new Query(Query::DELETE, new User());
        $query->from('foobar')->attribute('ignore', true);

        $this->assertRegExp('/DELETE\s+IGNORE\s+FROM `foobar`;/', $this->object->buildDelete($query));
    }

    public function testBuildDropTableTemporary() {
        $query = new Query(Query::DROP_TABLE, new User());
        $query->from('foobar')->attribute('temporary', true);

        $this->assertRegExp('/DROP TEMPORARY TABLE IF EXISTS `foobar`;/', $this->object->buildDropTable($query));
    }

    public function testBuildInsertIgnore() {
        $query = new Query(Query::INSERT, new User());
        $query->from('foobar')->fields([
            'email' => 'email@domain.com',
            'website' => 'http://titon.io'
        ]);

        $query->attribute('ignore', true);
        $this->assertRegExp('/INSERT\s+IGNORE\s+INTO `foobar` \(`email`, `website`\) VALUES \(\?, \?\);/', $this->object->buildInsert($query));
    }

    public function testBuildInsertPriority() {
        $query = new Query(Query::INSERT, new User());
        $query->from('foobar')->fields([
            'email' => 'email@domain.com',
            'website' => 'http://titon.io'
        ]);

        $query->attribute('priority', 'highPriority');
        $this->assertRegExp('/INSERT HIGH_PRIORITY  INTO `foobar` \(`email`, `website`\) VALUES \(\?, \?\);/', $this->object->buildInsert($query));
    }

    public function testBuildSelectDistinct() {
        $query = new Query(Query::SELECT, new User());
        $query->from('foobar')->attribute('distinct', true);

        $this->assertRegExp('/SELECT\s+DISTINCT\s+\* FROM `foobar`;/', $this->object->buildSelect($query));

        $query->attribute('distinct', 'all');
        $this->assertRegExp('/SELECT\s+ALL\s+\* FROM `foobar`;/', $this->object->buildSelect($query));
    }

    public function testBuildSelectOptimize() {
        $query = new Query(Query::SELECT, new User());
        $query->from('foobar')->attribute('optimize', 'sqlBufferResult');

        $this->assertRegExp('/SELECT\s+SQL_BUFFER_RESULT\s+\* FROM `foobar`;/', $this->object->buildSelect($query));
    }

    public function testBuildSelectCache() {
        $query = new Query(Query::SELECT, new User());
        $query->from('foobar')->attribute('cache', 'sqlCache');

        $this->assertRegExp('/SELECT\s+SQL_CACHE\s+\* FROM `foobar`;/', $this->object->buildSelect($query));
    }

    public function testBuildSelectUnions() {
        $user = new User();
        $query = $user->select('id');
        $query->union($query->subQuery('id')->from('u1'));

        $this->assertRegExp('/\(SELECT\s+(`|\")?id(`|\")? FROM (`|\")?users(`|\")?\s+\) UNION  \(SELECT\s+(`|\")?id(`|\")? FROM (`|\")?u1(`|\")?\);/', $this->object->buildSelect($query));

        // more joins
        $query->union($query->subQuery('id')->from('u2'), 'all');

        $this->assertRegExp('/\(SELECT\s+(`|\")?id(`|\")? FROM (`|\")?users(`|\")?\s+\) UNION  \(SELECT\s+(`|\")?id(`|\")? FROM (`|\")?u1(`|\")?\) UNION ALL \(SELECT\s+(`|\")?id(`|\")? FROM (`|\")?u2(`|\")?\);/', $this->object->buildSelect($query));

        // order by limit
        $query->orderBy('id', 'DESC')->limit(10);

        $this->assertRegExp('/\(SELECT\s+(`|\")?id(`|\")? FROM (`|\")?users(`|\")?\s+\) UNION  \(SELECT\s+(`|\")?id(`|\")? FROM (`|\")?u1(`|\")?\) UNION ALL \(SELECT\s+(`|\")?id(`|\")? FROM (`|\")?u2(`|\")?\) ORDER BY (`|\")?id(`|\")? DESC LIMIT 10;/', $this->object->buildSelect($query));
    }

    public function testBuildSelectLocking() {
        $query = new MysqlQuery(Query::SELECT, new User());
        $query->from('users')->where('name', 'like', '%miles%');

        $query->lockForShare();
        $this->assertRegExp('/SELECT\s+\* FROM\s+`users`\s+WHERE `name` LIKE \?\s+LOCK IN SHARE MODE;/', $this->object->buildSelect($query));

        $query->lockForUpdate();
        $this->assertRegExp('/SELECT\s+\* FROM\s+`users`\s+WHERE `name` LIKE \?\s+FOR UPDATE;/', $this->object->buildSelect($query));
    }

    public function testBuildUpdateIgnore() {
        $query = new Query(Query::UPDATE, new User());
        $query->from('foobar')->fields(['username' => 'miles'])->attribute('ignore', true);

        $this->assertRegExp('/UPDATE\s+IGNORE\s+`foobar`\s+SET `username` = \?;/', $this->object->buildUpdate($query));
    }

    public function testBuildUpdatePriority() {
        $query = new Query(Query::UPDATE, new User());
        $query->from('foobar')->fields(['username' => 'miles'])->attribute('priority', 'lowPriority');

        $this->assertRegExp('/UPDATE LOW_PRIORITY  `foobar`\s+SET `username` = \?;/', $this->object->buildUpdate($query));
    }

    public function testFormatCompounds() {
        $query = new Query(Query::INSERT, new User());

        $query->union($query->subQuery('id')->from('u1'));
        $this->assertRegExp('/\) UNION\s+\(SELECT\s+(`|\")?id(`|\")? FROM (`|\")?u1(`|\")?\)/', $this->object->formatCompounds($query->getCompounds()));

        // all
        $query->union($query->subQuery('id')->from('u2'), 'all');
        $this->assertRegExp('/\) UNION\s+\(SELECT\s+(`|\")?id(`|\")? FROM (`|\")?u1(`|\")?\) UNION ALL \(SELECT\s+(`|\")?id(`|\")? FROM (`|\")?u2(`|\")?\)/', $this->object->formatCompounds($query->getCompounds()));

        // distinct
        $query = new Query(Query::INSERT, new User());
        $query->union($query->subQuery('id')->from('u1'), 'distinct');
        $this->assertRegExp('/\) UNION DISTINCT \(SELECT\s+(`|\")?id(`|\")? FROM (`|\")?u1(`|\")?\)/', $this->object->formatCompounds($query->getCompounds()));
    }

    public function testGetStatement() {
        $this->assertEquals(new Dialect\Statement('INSERT {priority} {ignore} INTO {table} {fields} VALUES {values}'), $this->object->getStatement('insert'));
    }

    public function testRenderStatement() {
        $this->assertEquals('SELECT     * FROM tableName;', $this->object->renderStatement(Query::SELECT, [
            'table' => 'tableName',
            'fields' => '*'
        ]));
    }

}