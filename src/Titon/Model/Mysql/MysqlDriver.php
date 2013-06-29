<?php
/**
 * @copyright	Copyright 2010-2013, The Titon Project
 * @license		http://opensource.org/licenses/bsd-license.php
 * @link		http://titon.io
 */

namespace Titon\Model\Mysql;

use Titon\Model\Driver\AbstractPdoDriver;
use Titon\Model\Driver\Type;
use \PDO;

/**
 * A driver that represents the MySQL database and uses PDO.
 *
 * @package Titon\Model\Mysql
 */
class MysqlDriver extends AbstractPdoDriver {

	/**
	 * Configuration.
	 */
	protected $_config = [
		'port' => 3306,
		'flags' => [
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
		]
	];

	/**
	 * Set the timezone being used.
	 */
	public function initialize() {
		$this->setDialect(new MysqlDialect($this));

		$flags = $this->config->flags;

		if ($timezone = $this->config->timezone) {
			if ($timezone === 'UTC') {
				$timezone = '+00:00';
			}

			$flags[PDO::MYSQL_ATTR_INIT_COMMAND] = sprintf('SET time_zone = "%s";', $timezone);
		}

		$this->config->flags = $flags;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDriver() {
		return 'mysql';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSupportedTypes() {
		return [
			'tinyint' => 'Titon\Model\Driver\Type\IntType',
			'smallint' => 'Titon\Model\Driver\Type\IntType',
			'mediumint' => 'Titon\Model\Driver\Type\IntType',
			'int' => 'Titon\Model\Driver\Type\IntType',
			'integer' => 'Titon\Model\Driver\Type\IntType',
			'bigint' => 'Titon\Model\Driver\Type\BigintType',
			'float' => 'Titon\Model\Driver\Type\FloatType',
			'double' => 'Titon\Model\Driver\Type\DoubleType',
			'decimal' => 'Titon\Model\Driver\Type\DecimalType',
			'boolean' => 'Titon\Model\Driver\Type\BooleanType',
			'date' => 'Titon\Model\Driver\Type\DateType',
			'datetime' => 'Titon\Model\Driver\Type\DatetimeType',
			'timestamp' => 'Titon\Model\Driver\Type\DatetimeType',
			'time' => 'Titon\Model\Driver\Type\TimeType',
			'year' => 'Titon\Model\Driver\Type\YearType',
			'char' => 'Titon\Model\Driver\Type\CharType',
			'varchar' => 'Titon\Model\Driver\Type\StringType',
			'tinytext' => 'Titon\Model\Driver\Type\TextType',
			'mediumtext' => 'Titon\Model\Driver\Type\TextType',
			'text' => 'Titon\Model\Driver\Type\TextType',
			'longtext' => 'Titon\Model\Driver\Type\TextType',
			'tinyblob' => 'Titon\Model\Driver\Type\BlobType',
			'mediumblob' => 'Titon\Model\Driver\Type\BlobType',
			'blob' => 'Titon\Model\Driver\Type\BlobType',
			'longblob' => 'Titon\Model\Driver\Type\BlobType',
			'bit' => 'Titon\Model\Driver\Type\BinaryType',
			'binary' => 'Titon\Model\Driver\Type\BinaryType',
			'varbinary' => 'Titon\Model\Driver\Type\BinaryType',
			'serial' => 'Titon\Model\Driver\Type\SerialType',
			// enum
			// set
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function isEnabled() {
		return extension_loaded('pdo_mysql');
	}

}