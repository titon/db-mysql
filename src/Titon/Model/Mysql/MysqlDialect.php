<?php
/**
 * @copyright	Copyright 2010-2013, The Titon Project
 * @license		http://opensource.org/licenses/bsd-license.php
 * @link		http://titon.io
 */

namespace Titon\Model\Mysql;

use Titon\Model\Driver\Dialect\AbstractDialect;
use Titon\Model\Query;

/**
 * Inherit the default dialect rules which were based on MySQL.
 *
 * @package Titon\Model\Mysql
 */
class MysqlDialect extends AbstractDialect {

	const AVG_ROW_LENGTH = 'avgRowLength';
	const BIG_RESULT = 'sqlBigResult';
	const BUFFER_RESULT = 'sqlBufferResult';
	const CACHE = 'sqlCache';
	const CASCADE = 'cascade';
	const CONNECTION = 'connection';
	const DATA_DIRECTORY = 'dataDirectory';
	const DEFAULT_CHARACTER_SET = 'defaultCharacterSet';
	const DEFAULT_COMMENT = 'defaultComment';
	const DELAYED = 'delayed';
	const DELAY_KEY_WRITE = 'delayKeyWrite';
	const DISTINCT_ROW = 'distinctRow';
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
	const SMALL_RESULT = 'sqlSmallResult';
	const STATS_AUTO_RECALC = 'statsAutoRecalc';
	const STATS_PERSISTENT = 'statsPersistent';

	/**
	 * List of full SQL statements.
	 *
	 * @type array
	 */
	protected $_statements = [
		Query::INSERT		=> 'INSERT {a.priority} {a.ignore} INTO {table} {fields} VALUES {values}',
		Query::SELECT		=> 'SELECT {a.distinct} {a.priority} {a.optimize} {a.cache} {fields} FROM {table} {joins} {where} {groupBy} {having} {orderBy} {limit}',
		Query::UPDATE		=> 'UPDATE {a.priority} {a.ignore} {table} {joins} SET {fields} {where} {orderBy} {limit}',
		Query::DELETE		=> 'DELETE {a.priority} {a.quick} {a.ignore} FROM {table} {joins} {where} {orderBy} {limit}',
		Query::TRUNCATE		=> 'TRUNCATE {table}',
		Query::DESCRIBE		=> 'DESCRIBE {table}',
		Query::DROP_TABLE	=> 'DROP {a.temporary} TABLE IF EXISTS {table}',
		Query::CREATE_TABLE	=> "CREATE {a.temporary} TABLE IF NOT EXISTS {table} (\n{columns}{keys}\n) {options}"
	];

	/**
	 * Available attributes for each query type.
	 *
	 * @type array
	 */
	protected $_attributes = [
		Query::INSERT => [
			'priority' => '',
			'ignore' => false
		],
		Query::SELECT => [
			'distinct' => false,
			'priority' => '',
			'optimize' => '',
			'cache' => ''
		],
		Query::UPDATE => [
			'priority' => '',
			'ignore' => false
		],
		Query::DELETE => [
			'priority' => '',
			'quick' => false,
			'ignore' => false
		],
		Query::DROP_TABLE => [
			'temporary' => false
		],
		Query::CREATE_TABLE => [
			'temporary' => false
		],
	];

	/**
	 * Modify clauses and keywords.
	 */
	public function initialize() {
		parent::initialize();

		$this->_keywords = array_replace($this->_keywords, [
			self::AVG_ROW_LENGTH		=> 'AVG_ROW_LENGTH',
			self::BIG_RESULT			=> 'SQL_BIG_RESULT',
			self::BUFFER_RESULT			=> 'SQL_BUFFER_RESULT',
			self::CACHE					=> 'SQL_CACHE',
			self::CONNECTION			=> 'CONNECTION',
			self::DATA_DIRECTORY		=> 'DATA DIRECTORY',
			self::DEFAULT_CHARACTER_SET	=> 'DEFAULT CHARACTER SET',
			self::DEFAULT_COMMENT		=> 'DEFAULT COMMENT',
			self::DELAYED				=> 'DELAYED',
			self::DELAY_KEY_WRITE		=> 'DELAY_KEY_WRITE',
			self::DISTINCT_ROW			=> 'DISTINCTROW',
			self::HIGH_PRIORITY			=> 'HIGH_PRIORITY',
			self::INDEX_DIRECTORY		=> 'INDEX DIRECTORY',
			self::INSERT_METHOD			=> 'INSERT_METHOD',
			self::KEY_BLOCK_SIZE		=> 'KEY_BLOCK_SIZE',
			self::LOW_PRIORITY			=> 'LOW_PRIORITY',
			self::MAX_ROWS				=> 'MAX_ROWS',
			self::MIN_ROWS				=> 'MIN_ROWS',
			self::NO_CACHE				=> 'SQL_NO_CACHE',
			self::PACK_KEYS				=> 'PACK_KEYS',
			self::QUICK					=> 'QUICK',
			self::ROW_FORMAT			=> 'ROW_FORMAT',
			self::SMALL_RESULT			=> 'SQL_SMALL_RESULT',
			self::STATS_AUTO_RECALC		=> 'STATS_AUTO_RECALC',
			self::STATS_PERSISTENT		=> 'STATS_PERSISTENT'
		]);
	}

}