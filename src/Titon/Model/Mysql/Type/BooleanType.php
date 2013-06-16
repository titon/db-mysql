<?php
/**
 * @copyright	Copyright 2010-2013, The Titon Project
 * @license		http://opensource.org/licenses/bsd-license.php
 * @link		http://titon.io
 */

namespace Titon\Model\Mysql\Type;

use Titon\Model\Driver\Type\BooleanType as BoolType;

/**
 * MySQL doesn't support a true boolean type, so uses TINYINT(1).
 *
 * @package Titon\Model\Mysql\Type
 */
class BooleanType extends BoolType {

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultOptions() {
		return ['type' => 'tinyint', 'length' => 1];
	}

}