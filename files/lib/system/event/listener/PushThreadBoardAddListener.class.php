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
namespace wbb\system\event\listener;

use wbb\acp\form\BoardEditForm;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;

/**
 * Push thread board add listener.
 */
class PushThreadBoardAddListener implements IParameterizedEventListener
{
    /**
     * @inheritdoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_PUSH_THREAD) {
            return;
        }

        switch ($eventName) {
            case 'readFormParameters':
                if (isset($_POST['threadPushEnable'])) {
                    $eventObj->threadPushEnable = 1;
                }
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
                    'threadPushEnable' => $eventObj->threadPushEnable,
                ]);
                break;
        }
    }
}
