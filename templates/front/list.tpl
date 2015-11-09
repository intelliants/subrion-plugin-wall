<div class="media ia-item ia-item-bordered wall-post js-wall-post">
	<div class="media-body">
		<div class="description">
			<div class="js-wall-post-body-text">{$post.body|escape:'html'}</div>

			{if !empty($member) && 0 != $post.member_id && $post.member_id == $member.id}
				<p class="wall-post-actions">
					<a href="#" class="js-wall-post-edit" data-post-id="{$post.id}"><i class="icon-edit"></i> {lang key='edit'}</a>&nbsp;
					<a href="#" class="js-wall-post-delete" data-post-id="{$post.id}"><i class="icon-remove"></i> {lang key='delete'}</a>
				</p>
			{/if}
		</div>	
	</div>
	<div class="ia-item-panel">
		<div class="pull-right">
			{if $post.author_avatar}
				{assign var='author_avatar' value=$post.author_avatar|unserialize}
				{if $author_avatar}
					{printImage imgfile=$author_avatar.path width=60 height=60 title=$post.author}
				{else}
					<img src="{$smarty.const.IA_TPL_URL}img/no-avatar.png" alt="{$post.author}" width="60" height="60">
				{/if}
			{else}
				<img src="{$smarty.const.IA_TPL_URL}img/no-avatar.png" alt="{$post.author}" width="60" height="60">
			{/if}
		</div>

		<p class="name">
			{if  0 == $post.member_id}
				{lang key='guest'}
			{else}
				{ia_url type="link" data=$post item='members' text=$post.author}
			{/if}</p>
		{$post.date|date_format:$core.config.date_format}
	</div>
</div>