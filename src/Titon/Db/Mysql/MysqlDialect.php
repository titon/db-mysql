<?php
/**
 * @copyright   2010-2013, The Titon Project
 * @license     http://opensource.org/licenses/bsd-license.php
 * @link        http://titon.io
 */

namespace Titon\Db\Mysql;

use Titon\Db\Driver\Dialect\AbstractPdoDialect;
use Titon\Db\Driver\Dialect\Statement;
use Titon\Db\Query;

/**
 * Inherit the default dialect rules which were based on MySQL.
 *
 * @package Titon\Db\Mysql
 */
class MysqlDialect extends AbstractPdoDialect {

    const AVG_ROW_LENGTH = 'avgRowLength';
    const BIG_RESULT = 'sqlBigResult';
    const BUFFER_RESULT = 'sqlBufferResult';
    const BTREE = 'btree';
    const CACHE = 'sqlCache';
    const CONNECTION = 'connection';
    const DATA_DIRECTORY = 'dataDirectory';
    const DEFAULT_CHARACTER_SET = 'defaultCharacterSet';
    const DEFAULT_COMMENT = 'defaultComment';
    const DELAYED = 'delayed';
    const DELAY_KEY_WRITE = 'delayKeyWrite';
    const DISTINCT_ROW = 'distinctRow';
    const FOR_UPDATE_LOCK = 'forUpdateLock';
    const FULLTEXT = 'fulltext';
    const HASH = 'hash';
    const HIGH_PRIORITY = 'highPriority';
    const INDEX_DIRECTORY = 'indexDirectory';
    const INSERT_METHOD = 'insertMethod';
    const KEY_BLOCK_SIZE = 'keyBlockSize';
    const LOW_PRIORITY = 'lowPriority';
    const MAX_ROWS = 'maxRows';
    const MIN_ROWS = 'minRows';
    const NO_CACHE = 'sqlNoCache';
    const PACK_KEYS = 'packKeys';
    const QUICK = 'quick';
    const ROW_FORMAT = 'rowFormat';
    const SHARED_LOCK = 'sharedLock';
    const SMALL_RESULT = 'sqlSmallResult';
    const SPATIAL = 'spatial';
    const STATS_AUTO_RECALC = 'statsAutoRecalc';
    const STATS_PERSISTENT = 'statsPersistent';
    const UNIQUE = 'unique';
    const USING = 'using';

    /**
     * Modify clauses and keywords.
     */
    public function initialize() {
        parent::initialize();

        $this->addClauses([
            self::UNION => 'UNION {flag} (%s)',
            self::USING => 'USING %s'
        ]);

        $this->addKeywords([
            self::AVG_ROW_LENGTH        => 'AVG_ROW_LENGTH',
            self::BIG_RESULT            => 'SQL_BIG_RESULT',
            self::BUFFER_RESULT         => 'SQL_BUFFER_RESULT',
            self::BTREE                 => 'BTREE',
            self::CACHE                 => 'SQL_CACHE',
            self::CONNECTION            => 'CONNECTION',
            self::DATA_DIRECTORY        => 'DATA DIRECTORY',
            self::DEFAULT_CHARACTER_SET => 'DEFAULT CHARACTER SET',
            self::DEFAULT_COMMENT       => 'DEFAULT COMMENT',
            self::DELAYED               => 'DELAYED',
            self::DELAY_KEY_WRITE       => 'DELAY_KEY_WRITE',
            self::DISTINCT_ROW          => 'DISTINCTROW',
            self::FOR_UPDATE_LOCK       => 'FOR UPDATE',
            self::FULLTEXT              => 'FULLTEXT',
            self::HASH                  => 'HASH',
            self::HIGH_PRIORITY         => 'HIGH_PRIORITY',
            self::INDEX_DIRECTORY       => 'INDEX DIRECTORY',
            self::INSERT_METHOD         => 'INSERT_METHOD',
            self::KEY_BLOCK_SIZE        => 'KEY_BLOCK_SIZE',
            self::LOW_PRIORITY          => 'LOW_PRIORITY',
            self::MAX_ROWS              => 'MAX_ROWS',
            self::MIN_ROWS              => 'MIN_ROWS',
            self::NO_CACHE              => 'SQL_NO_CACHE',
            self::PACK_KEYS             => 'PACK_KEYS',
            self::QUICK                 => 'QUICK',
            self::ROW_FORMAT            => 'ROW_FORMAT',
            self::SHARED_LOCK           => 'LOCK IN SHARE MODE',
            self::SMALL_RESULT          => 'SQL_SMALL_RESULT',
            self::SPATIAL               => 'SPATIAL',
            self::STATS_AUTO_RECALC     => 'STATS_AUTO_RECALC',
            self::STATS_PERSISTENT      => 'STATS_PERSISTENT',
            self::UNIQUE                => 'UNIQUE'
        ]);

        $this->addStatements([
            Query::INSERT        => new Statement('INSERT {priority} {ignore} INTO {table} {fields} VALUES {values}'),
            Query::SELECT        => new Statement('SELECT {distinct} {priority} {optimize} {cache} {fields} FROM {table} {joins} {where} {groupBy} {having} {compounds} {orderBy} {limit} {lock}'),
            Query::UPDATE        => new Statement('UPDATE {priority} {ignore} {table} {joins} SET {fields} {where} {orderBy} {limit}'),
            Query::DELETE        => new Statement('DELETE {priority} {quick} {ignore} FROM {table} {joins} {where} {orderBy} {limit}'),
            Query::TRUNCATE      => new Statement('TRUNCATE {table}'),
            Query::CREATE_TABLE  => new Statement("CREATE {temporary} TABLE IF NOT EXISTS {table} (\n{columns}{keys}\n) {options}"),
            Query::CREATE_INDEX  => new Statement('CREATE {type} INDEX {index} ON {table} ({fields}) {using}'),
            Query::DROP_TABLE    => new Statement('DROP {temporary} TABLE IF EXISTS {table}'),
            Query::DROP_INDEX    => new Statement('DROP INDEX {index} ON {table}')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildSelect(Query $query) {
        $statement = parent::buildSelect($query);

        // Primary select needs to be wrapped in parenthesis, so add a (
        // The closing ) is added by formatCompounds()
        if ($query->getCompounds()) {
            $statement = '(' . $statement;
        }

        return $statement;
    }

    /**
     * {@inheritdoc}
     */
    public function formatCompounds(array $queries) {
        if ($queries) {
            // Return a ) as we need to wrap the primary select query
            return ') ' . parent::formatCompounds($queries);
        }

        return '';
    }

}