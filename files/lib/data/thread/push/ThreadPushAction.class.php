<?php

/*
 * Copyright by Udo Zaydowicz.
 * Modified by SoftCreatR.dev.
 *
 * License: http://opensource.org/licenses/lgpl-license.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
namespace wbb\data\thread\push;

use wbb\data\thread\Thread;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * Executes push-related actions.
 */
class ThreadPushAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = ThreadPushEditor::class;

    /**
     * thread
     */
    public $thread;

    /**
     * Validates GetStatus action.
     */
    public function validateGetStatus()
    {
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
    public function getStatus()
    {
        $period = WCF::getSession()->getPermission('user.board.pushThreadLock') * 3600;
        $wait = $temp = 0;

        // creation
        if ($this->thread->time + $period > TIME_NOW) {
            $wait = $this->thread->time + $period - TIME_NOW;
        }

        // last push
        $sql = "SELECT time
                FROM    wbb" . WCF_N . "_thread_push
                WHERE    userID = ? AND threadID = ?";
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
            'wait' => $wait,
        ]);

        return [
            'template' => WCF::getTPL()->fetch('pushThreadStatusDialog', 'wbb'),
        ];
    }
}
