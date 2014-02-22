<?php
/**
 * @copyright   2010-2013, The Titon Project
 * @license     http://opensource.org/licenses/bsd-license.php
 * @link        http://titon.io
 */

namespace Titon\Db\Mysql;

use Titon\Db\Query;

/**
 * Defines MySQL only query functionality.
 *
 * @package Titon\Db\Mysql
 */
class MysqlQuery extends Query {

    /**
     * Lock rows and indices during the current transaction.
     *
     * @return $this
     */
    public function lockForUpdate() {
        $this->attribute('lock', MysqlDialect::FOR_UPDATE_LOCK);

        return $this;
    }

    /**
     * Apply a shared lock on rows being read during the current transaction.
     *
     * @return $this
     */
    public function sharedLock() {
        $this->attribute('lock', MysqlDialect::SHARED_LOCK);

        return $this;
    }

}