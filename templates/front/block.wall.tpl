{if !(isset($isView) && $isView)}
    {if (!$core.config.wall_allow_guests && !empty($member)) || $core.config.wall_allow_guests}
        <form action="" class="ia-form clearfix js-wall-post-form">
            {preventCsrf}
            <input type="hidden" name="action" value="add">
            {if $core.config.wall_allow_wysiwyg}
                {ia_wysiwyg value="" name="body"}
            {else}
                <textarea name="body" id="" rows="5" class="input-block-level js-wall-post-body" style="resize: none; width: 100%">{if isset($body)}{$body}{/if}</textarea>
            {/if}
            <div class="js-wall-post-counter"></div>
            <button class="btn btn-small btn-primary pull-right js-wall-post-submit"><i class="icon-ok-circle"></i> {lang key='sumbit_post'}</button>
        </form>
    {else}
        <div class="alert alert-info">{lang key='guests_warning'}</div>
    {/if}
{/if}

<div class="wall-posts clearfix js-wall-post-list">
    {if isset($latest_wall_posts) && !empty($latest_wall_posts)}
        {foreach $latest_wall_posts as $post}
            {include 'module:wall/list.tpl'}
        {/foreach}
        {if $num_total_wall_posts > $core.config.posts_per_load}
            <a href="#" class="btn btn-small btn-block js-btn-wall-more"><i class="icon-download"></i> {lang key='more'}</a>
        {/if}
    {else}
        <div class="alert alert-info">{lang key='no_posts'}</div>
    {/if}
</div>

{ia_add_js}
$(function()
{
    $('.js-wall-post-body').dodosTextCounter({$core.config.post_max_chars},
    {
        counterDisplayElement: 'span',
        counterDisplayClass: 'js-wall-post-counter'
    });
    $('.js-wall-post-counter').addClass('textcounter').addClass('muted').wrap('<p class="help-block text-right js-wall-post-counter-wrapper"></p>');
});
{/ia_add_js}

{ia_print_js files='_IA_URL_modules/wall/js/frontend/block.wall,jquery/plugins/jquery.textcounter'}
