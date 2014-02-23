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
     * Lock all rows using a shared lock instead of exclusive.
     *
     * @return $this
     */
    public function lockForShare() {
        return $this->attribute('lock', MysqlDialect::SHARED_LOCK);
    }

    /**
     * Lock all rows returned from a select as if they were locked for update.
     *
     * @return $this
     */
    public function lockForUpdate() {
        return $this->attribute('lock', MysqlDialect::FOR_UPDATE_LOCK);
    }

}