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
	 */
	protected $_config = [
		'port' => 3306,
		'flags' => [
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
		]
	];

	/**
	 * Set the dialect and commands to flags.
	 */
	public function initialize() {
		$this->setDialect(new MysqlDialect($this));

		$init = [];

		if ($timezone = $this->config->timezone) {
			if ($timezone === 'UTC') {
				$timezone = '+0:00';
			}

			$init[] = sprintf("SET time_zone = '%s'", $timezone);
		}

		if ($encoding = $this->config->encoding) {
			$init[] = 'SET NAMES ' . $encoding;
		}

		if ($init) {
			$flags = $this->config->flags;
			$flags[PDO::MYSQL_ATTR_INIT_COMMAND] = implode(';', $init);

			$this->config->flags = $flags;
		}
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