{if 'view_member' == $core.page.name}
    {capture append='tabs_content' name='wall_posts'}
        <div class="ia-wrap">
            {include 'module:wall/block.wall.tpl'}
        </div>
    {/capture}
{/if}
