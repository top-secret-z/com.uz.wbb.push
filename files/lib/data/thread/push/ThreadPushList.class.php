<?php
namespace wbb\data\thread\push;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of pushes.
 *  
 * @author		2020-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wbb.push
 */
class ThreadPushList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = ThreadPush::class;
}
