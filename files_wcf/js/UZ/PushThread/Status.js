/**
 * Dialog to show push status
 * 
 * @author        2020-2022 Zaydowicz
 * @license        GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package        com.uz.wbb.push
 */
define(['Ajax', 'Language', 'Ui/Dialog'], function(Ajax, Language, UiDialog) {
    "use strict";

    function UZPushThreadStatus() { this.init(); }

    UZPushThreadStatus.prototype = {
        init: function() {
            var button = elBySel('.jsPushThreadButton');
            button.addEventListener(WCF_CLICK_EVENT, this._showDialog.bind(this));
        },

        _showDialog: function(event) {
            event.preventDefault();

            Ajax.api(this, {
                actionName:    'getStatus',
                parameters:    {
                    threadID:    ~~elData(event.currentTarget, 'object-id')
                }
            });
        },

        _ajaxSuccess: function(data) {
            this._render(data);
        },

        _render: function(data) {
            UiDialog.open(this, data.returnValues.template);
        },

        _ajaxSetup: function() {
            return {
                data: {
                    className: 'wbb\\data\\thread\\push\\ThreadPushAction'
                }
            };
        },

        _dialogSetup: function() {
            return {
                id:         'PushThreadStatus',
                options:     { title: Language.get('wbb.thread.threadPush.title') },
                source:     null
            };
        }
    };

    return UZPushThreadStatus;
});
