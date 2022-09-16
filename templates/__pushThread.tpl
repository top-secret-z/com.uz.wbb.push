{if MODULE_PUSH_THREAD && $board->threadPushEnable}
	{if $__wcf->session->getPermission('user.board.canPushThreadUnlimited')}
		<li><a href="{link application='wbb' controller='PushThreadAdd' id=$thread->threadID}{/link}" title="{lang}wbb.thread.threadPush.push{/lang}" class="button" onclick="WCF.System.Confirmation.show('{lang}wbb.thread.threadPush.push.sure{/lang}', $.proxy(function (action) { if (action == 'confirm') window.location.href = $(this).attr('href'); }, this)); return false;"><span class="icon icon16 fa-arrow-up"></span> <span>{lang}wbb.thread.threadPush.push{/lang}</span></a></li>
	{else}
		{if $__wcf->user->userID==$thread->userID && $__wcf->session->getPermission('user.board.canPushThread')}
			{if $__wcf->getPushThreadHandler()->canPush($thread)}
				{assign var='addLang' value=''}
				{event name='pushThreadPush'}
				
				<li><a href="{link application='wbb' controller='PushThreadAdd' id=$thread->threadID}{/link}" title="{lang}wbb.thread.threadPush.push{/lang}" class="button" onclick="WCF.System.Confirmation.show('{lang}wbb.thread.threadPush.push.sure{/lang}{if $addLang} {lang}{$addLang}{/lang}{/if}', $.proxy(function (action) { if (action == 'confirm') window.location.href = $(this).attr('href'); }, this)); return false;"><span class="icon icon16 fa-arrow-up"></span> <span>{lang}wbb.thread.threadPush.push{/lang}</span></a></li>
			{else}
				<script data-relocate="true">
					require(['Language', 'UZ/PushThread/Status'], function (Language, UZPushThreadStatus) {
						Language.addObject({
							'wbb.thread.threadPush.title':	'{jslang}wbb.thread.threadPush.title{/jslang}'
						});
						new UZPushThreadStatus();
					});
				</script>
				
				<li><a href="#" class="button jsOnly jsPushThreadButton" data-object-id="{@$thread->threadID}"><span class="icon icon16 fa-times"></span> <span>{lang}wbb.thread.threadPush.push.not{/lang}</span></a></li>
			{/if}
		{/if}
	{/if}
{/if}
