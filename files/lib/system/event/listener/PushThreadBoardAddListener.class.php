<?php
namespace wbb\system\event\listener;
use wbb\acp\form\BoardEditForm;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;

/**
 * Push thread board add listener.
 *
 * @author		2020-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wbb.push
 */
class PushThreadBoardAddListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_PUSH_THREAD) return;
		
		switch ($eventName) {
			case 'readFormParameters':
				if (isset($_POST['threadPushEnable'])) $eventObj->threadPushEnable = 1;
				break;
				
			case 'readParameters':
				$eventObj->threadPushEnable = 0;
				break;
			
			case 'readData':
				if ($eventObj instanceof BoardEditForm) {
					if (empty($_POST)) {
						$eventObj->threadPushEnable = $eventObj->board->threadPushEnable;
					}
				}
				break;
				
			case 'save':
				$eventObj->additionalFields['threadPushEnable'] = $eventObj->threadPushEnable;
				break;
				
			case 'assignVariables':
				WCF::getTPL()->assign([
					'threadPushEnable' => $eventObj->threadPushEnable
				]);
				break;
		}
	}
}
