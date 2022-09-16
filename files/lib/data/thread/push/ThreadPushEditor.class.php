<?php
namespace wbb\data\thread\push;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit pushes.
 *  
 * @author		2020-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wbb.push
 */
class ThreadPushEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	public static $baseClass = ThreadPush::class;
}
