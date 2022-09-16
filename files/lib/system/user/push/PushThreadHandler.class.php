<?php
namespace wbb\system\user\push;
use wcf\data\user\User;
use wcf\system\event\EventHandler;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles push thread allowance.
 * 
 * @author		2020-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wbb.push
 */
class PushThreadHandler extends SingletonFactory {
	/**
	 * Returns true if the current user can push this thread.
	 * Basic checks in template
	 */
	public function canPush($thread) {
		// additional checks
		$parameters = ['canPush' => true];
		EventHandler::getInstance()->fireAction($this, 'canPushThread', $parameters);
		if (!$parameters['canPush']) {
			return false;
		}
		
		// basic period in secs
		$period = WCF::getSession()->getPermission('user.board.pushThreadLock') * 3600;
		
		// creation
		if ($thread->time + $period > TIME_NOW) return false;
		
		// last push
		$sql = "SELECT	time
				FROM	wbb".WCF_N."_thread_push
				WHERE	userID = ? AND threadID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([WCF::getUser()->userID, $thread->threadID]);
		$time = $statement->fetchColumn();
		
		if (!$time) return true;
		if ($time + $period > TIME_NOW) return false;
		
		return true;
	}
}
