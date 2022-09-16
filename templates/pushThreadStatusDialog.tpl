<p>{lang}wbb.thread.threadPush.push.time{/lang}</p>
<p>{if $wait>0}{@$wait|dateDiff:0:1}{else}{lang}wbb.thread.threadPush.push.time.now{/lang}{/if}</p>
{event name='pushThreadDialog'}