$(function () {
    var vUrl = intelli.config.url + 'wall.json';

    $('.js-wall-post-submit').click(function (e) {
        e.preventDefault();

        if (1 == intelli.config.wall_allow_wysiwyg) {
            $('textarea[name="body"]').val(CKEDITOR.instances['body'].getData());
        }

        var params = {
            url: vUrl,
            type: 'post',
            data: $(this).closest('.js-wall-post-form').serialize()
        };

        $.ajax(params).done(function (response) {
            $('.js-wall-post-body').val('').trigger('keyup');
            intelli.notifFloatBox({msg: response.messages, autohide: true, type: response.error ? 'error' : 'success'});
            if (typeof response.html != 'undefined' && !response.error) {
                $('.js-wall-post-list .alert').remove();
                $('<div style="display:none;margin-bottom:30px;">' + response.html + '</div>').prependTo('.js-wall-post-list').fadeIn(800);
                if (1 == intelli.config.wall_allow_wysiwyg) {
                    CKEDITOR.instances['body'].setData('');
                }
            }
        });
    });

    $('.js-wall-post-list').on('click', '.js-wall-post-delete', function (e) {
        e.preventDefault();
        var that = this;

        if (confirm(_t('are_you_sure_to_delete_this_post'))) {
            intelli.post(vUrl, {action: 'delete', id: $(this).data('post-id')}).success(function (response) {
                intelli.notifFloatBox({
                    msg: response.messages,
                    autohide: true,
                    type: response.error ? 'error' : 'success'
                });
                if (!response.error) {
                    $(that).closest('.js-wall-post').fadeOut(800);
                }
            });
        }
    });

    $('.js-wall-post-list').on('click', '.js-wall-post-edit', function (e) {
        e.preventDefault();

        var body_section = $(this).parent().parent().children('.js-wall-post-body-text');
        var prev_body = body_section.text();

        body_section.html('');
        body_section.append('<textarea rows="5" class="input-block-level js-wall-post-body-edit" style="resize: none;">' + prev_body + '</textarea>').append('<a href="#" class="btn btn-mini btn-success pull-left js-wall-post-update" data-post-id=' + $(this).data('post-id') + '><i class="icon-ok"></i> ' + _t('save') + '</a>');

        body_section.children('.js-wall-post-update').click(function (e) {
            e.preventDefault();

            var new_body = body_section.children('.js-wall-post-body-edit').val();
            var post_id = $(this).data('post-id');

            intelli.post(vUrl, {action: 'edit', id: post_id, body: new_body}).success(function (response) {
                intelli.notifFloatBox({
                    msg: response.messages,
                    autohide: true,
                    type: response.error ? 'error' : 'success'
                });
                if (!response.error) {
                    body_section.children('.js-wall-post-body-edit').remove();
                    body_section.html(response.html);
                }
            });
        });
    });

    $('.js-btn-wall-more').click(function (e) {
        e.preventDefault();
        var params = {
            url: vUrl,
            type: 'get',
            data: {
                action: 'read',
                start: parseInt($('.js-wall-post').length),
                limit: intelli.config.posts_per_load
            }
        };

        $.ajax(params).done(function (response) {
            if (typeof response.html != 'undefined' && 0 != response.html.length && !response.error) {
                $('<div style="display:none;margin-bottom:30px;">' + response.html + '</div>').insertBefore('.js-btn-wall-more').fadeIn(800);
                if (parseInt($('.js-wall-post').length) >= response.total) {
                    $('.js-btn-wall-more').hide();
                }
            }
            else {
                $('.js-btn-wall-more').hide();
            }
        });
    });
});