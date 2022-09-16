<?php
namespace wbb\action;
use wbb\data\post\Post;
use wbb\data\post\PostAction;
use wbb\data\thread\Thread;
use wcf\action\AbstractAction;
use wcf\data\user\User;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\NamedUserException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\language\LanguageFactory;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Adds push
 * 
 * @author		2020-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wbb.push
 */
class PushThreadAddAction extends AbstractAction {
	/**
	 * @inheritDoc
	 */
	public $loginRequired = true;
	
	/**
	 * @inheritDoc
	 */
	public $neededModules = ['MODULE_PUSH_THREAD'];
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = [];
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		// must have thread
		if (!isset($_GET['id'])) {
			throw new IllegalLinkException();
		}
		
		// user must have permission
		if (!WCF::getSession()->getPermission('user.board.canPushThread') && !WCF::getSession()->getPermission('user.board.canPushThreadUnlimited')) {
			throw new PermissionDeniedException();
		}
		
		// must have author
		if (empty(MODULE_PUSH_THREAD_PUSHER)) {
			throw new NamedUserException(WCF::getLanguage()->get('wbb.thread.threadPush.noAuthor'));
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function execute() {
		parent::execute();
		
		// get thread
		$thread = new Thread($_GET['id']);
		if (!$thread->threadID) {
			throw new IllegalLinkException();
		}
		
		// check push again, unless unlimited
		if (!WCF::getSession()->getPermission('user.board.canPushThreadUnlimited')) {
			if (WCF::getUser()->userID != $thread->userID) {
				throw new PermissionDeniedException();
			}
			
			$sql = "SELECT time
					FROM	wbb".WCF_N."_thread_push
					WHERE	userID = ? AND threadID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([WCF::getUser()->userID, $thread->threadID]);
			$time = $statement->fetchColumn();
			$period = WCF::getSession()->getPermission('user.board.pushThreadLock') * 3600;
			if ($time + $period > TIME_NOW) {
				throw new PermissionDeniedException();
			}
			
			// additional checks
			$parameters = ['canPush' => true];
			EventHandler::getInstance()->fireAction($this, 'pushThreadBefore', $parameters);
			if (!$parameters['canPush']) {
				throw new PermissionDeniedException();}
		}
		
		// get language
		if ($thread->languageID) {
			$language = LanguageFactory::getInstance()->getLanguage($thread->languageID);
		}
		else {
			$language = LanguageFactory::getInstance()->getDefaultLanguage();
		}
		
		// get input processor
		$htmlInputProcessor = new HtmlInputProcessor();
		$htmlInputProcessor->process($language->getDynamicVariable('wbb.thread.threadPush.text', ['username' => WCF::getUser()->username]), 'com.woltlab.wbb.post');
		
		// add new post, use given user or guest
		$user = User::getUserByUsername(MODULE_PUSH_THREAD_PUSHER);
		$data = [
				'data' => [
						'threadID' => $thread->threadID,
						'time' => TIME_NOW,
						'userID' => $user->userID ? $user->userID : null,
						'username' => $user->userID ? $user->username : MODULE_PUSH_THREAD_PUSHER
				],
				'htmlInputProcessor' => $htmlInputProcessor
		];
		$objectAction = new PostAction([], 'create', $data);
		$resultValues = $objectAction->executeAction();
		
		$postID = $resultValues['returnValues']->postID;
		
		// store push
		$sql = "INSERT INTO	wbb".WCF_N."_thread_push
					(time, userID, threadID)
				VALUES		(?, ?, ?)
				ON DUPLICATE KEY UPDATE time = VALUES(time)";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([TIME_NOW, WCF::getUser()->userID, $thread->threadID]);
		
		// additional action
		$parameters = ['thread' => $thread];
		EventHandler::getInstance()->fireAction($this, 'pushThreadAfter', $parameters);
		
		$this->executed();
		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('Thread', [
				'application' => 'wbb',
				'object' => $thread,
				'postID' => $postID,
				'forceFrontend' => true,
		], '#post' . $postID));
		exit;
	}
}
