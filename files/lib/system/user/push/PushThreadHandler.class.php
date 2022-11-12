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
namespace wbb\system\user\push;

use wcf\data\user\User;
use wcf\system\event\EventHandler;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles push thread allowance.
 */
class PushThreadHandler extends SingletonFactory
{
    /**
     * Returns true if the current user can push this thread.
     * Basic checks in template
     */
    public function canPush($thread)
    {
        // additional checks
        $parameters = ['canPush' => true];
        EventHandler::getInstance()->fireAction($this, 'canPushThread', $parameters);
        if (!$parameters['canPush']) {
            return false;
        }

        // basic period in secs
        $period = WCF::getSession()->getPermission('user.board.pushThreadLock') * 3600;

        // creation
        if ($thread->time + $period > TIME_NOW) {
            return false;
        }

        // last push
        $sql = "SELECT    time
                FROM    wbb" . WCF_N . "_thread_push
                WHERE    userID = ? AND threadID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF::getUser()->userID, $thread->threadID]);
        $time = $statement->fetchColumn();

        if (!$time) {
            return true;
        }
        if ($time + $period > TIME_NOW) {
            return false;
        }

        return true;
    }
}
