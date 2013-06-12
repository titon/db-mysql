<?php
/**
 * @copyright	Copyright 2010-2013, The Titon Project
 * @license		http://opensource.org/licenses/bsd-license.php
 * @link		http://titon.io
 */

namespace Titon\Model\Mysql;

use Titon\Model\Driver\AbstractPdoDriver;
use \PDO;

/**
 * A driver that represents the MySQL database and uses PDO.
 *
 * @package Titon\Model\Mysql
 */
class Mysql extends AbstractPdoDriver {

	/**
	 * Configuration.
	 *
	 * @type array {
	 *		@type int $port	Default port
	 * ]
	 */
	protected $_config = [
		'port' => 3306
	];

	/**
	 * Default MySQL flags.
	 *
	 * @type array
	 */
	protected $_flags = [
		PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
	];

	/**
	 * Set the dialect.
	 */
	public function initialize() {
		$this->setDialect(new MysqlDialect($this));
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
	public function isEnabled() {
		return extension_loaded('pdo_mysql');
	}

}