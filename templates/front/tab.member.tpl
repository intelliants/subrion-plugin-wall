{if 'view_member' == $core.page.name}
	{capture append='tabs_content' name='wall_posts'}
		<div class="ia-wrap">
			{include file="modules/wall/templates/front/block.wall.tpl" isView=true}
		</div>
	{/capture}
{/if}
