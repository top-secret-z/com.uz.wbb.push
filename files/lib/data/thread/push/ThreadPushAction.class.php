<?php
namespace wbb\data\thread\push;
use wbb\data\thread\Thread;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * Executes push-related actions.
 *  
 * @author		2020-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wbb.push
 */
class ThreadPushAction extends AbstractDatabaseObjectAction{
	/**
	 * @inheritDoc
	 */
	protected $className = ThreadPushEditor::class;
	
	/**
	 * thread
	 */
	public $thread = null;
	
	/**
	 * Validates GetStatus action.
	 */
	public function validateGetStatus() {
		if (!isset($this->parameters['threadID'])) {
			throw new PermissionDeniedException();
		}
		$this->thread = new Thread($this->parameters['threadID']);
		if (!$this->thread->threadID) {
			throw new IllegalLinkException();
		}
		
		if (!WCF::getSession()->getPermission('user.board.canPushThread')) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * Executes the getStatus action.
	 */
	public function getStatus() {
		$period = WCF::getSession()->getPermission('user.board.pushThreadLock') * 3600;
		$wait = $temp = 0;
		
		// creation
		if ($this->thread->time + $period > TIME_NOW) {
			$wait = $this->thread->time + $period - TIME_NOW;
		}
		
		// last push
		$sql = "SELECT time
				FROM	wbb".WCF_N."_thread_push
				WHERE	userID = ? AND threadID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([WCF::getUser()->userID, $this->thread->threadID]);
		$time = $statement->fetchColumn();
		if ($time && $time + $period > TIME_NOW) {
			$temp = $time + $period - TIME_NOW;
		}
		
		if ($temp > $wait) {
			$wait = $temp;
		}
		
		WCF::getTPL()->assign([
				'wait' => $wait
		]);
		
		return [
				'template' => WCF::getTPL()->fetch('pushThreadStatusDialog', 'wbb')
		];
	}
}
