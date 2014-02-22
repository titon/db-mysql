<?php
/**
 * @copyright   2010-2013, The Titon Project
 * @license     http://opensource.org/licenses/bsd-license.php
 * @link        http://titon.io
 */

namespace Titon\Db\Mysql;

use Titon\Db\Driver\AbstractPdoDriver;
use Titon\Db\Driver\Type;
use \PDO;

/**
 * A driver that represents the MySQL database and uses PDO.
 *
 * @package Titon\Db\Mysql
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
     * Set the dialect and timezone being used.
     */
    public function initialize() {
        $this->setDialect(new MysqlDialect($this));

        $flags = $this->getConfig('flags');

        if ($timezone = $this->getConfig('timezone')) {
            if ($timezone === 'UTC') {
                $timezone = '+00:00';
            }

            $flags[PDO::MYSQL_ATTR_INIT_COMMAND] = sprintf('SET time_zone = "%s";', $timezone);
        }

        $this->setConfig('flags', $flags);
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
    public function getDsn() {
        if ($dsn = $this->getConfig('dsn')) {
            return $dsn;
        }

        $params = ['dbname=' . $this->getDatabase()];

        if ($socket = $this->getSocket()) {
            $params[] = 'unix_socket=' . $socket;
        } else {
            $params[] = 'host=' . $this->getHost();
            $params[] = 'port=' . $this->getPort();
        }

        if ($encoding = $this->getEncoding()) {
            $params[] = 'charset=' . $encoding;
        }

        $dsn = $this->getDriver() . ':' . implode(';', $params);

        return $dsn;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled() {
        return extension_loaded('pdo_mysql');
    }

}