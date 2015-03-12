/*******************
 * dv-template v0.2
 * 2015, devvoh.com, https://github.com/devvoh/dv-template
 *
 * common.js is for your site-specific scripts
 */

$(function() {

    // catch login link click
    $('.dvbb-login-link').on('click', function() {
        $('.dvbb-login-form').toggle();
        return false;
    });

    // catch login form 'submit'
    $('.dvbb-login-form .dv-form-button').on('click', function() {
        var data = {};
        data.username = $('.dvbb-login-form input[name="username"]').val();
        data.password = $('.dvbb-login-form input[name="password"]').val();
        var ajaxUrl = $(this).data('ajax-url');
        $.post(
            ajaxUrl,
            data,
            function(data) {
                if (data === null) {
                    // no data means no user found with this user/pass combo
                    alert('Your login information isn\'t correct');
                } else {
                    // non-validated users cannot log in
                    if (data.validated_at === null) {
                        alert('You have not yet validated your account. Check the e-mail address you signed up with for instructions.');
                    } else {
                        window.location.reload();
                    }
                }
            }
        );
        return false;
    });

    // catch logout button click
    $('[data-ajax-url]').on('click', function() {

        // ignore the login link
        if ($(this).parents('.dvbb-login-form').length == 0) {
            var ajaxUrl = $(this).data('ajax-url');
            var ajaxRedirect = $(this).data('ajax-redirect');
            $.get(
                ajaxUrl,
                function() {
                    // no redirect given means reload
                    if (ajaxRedirect === undefined) {
                        window.location.reload();
                    } else {
                        window.location.href = ajaxRedirect;
                    }

                }
            );
        }
        return false;
    });

    // catch clicks on bbcode links
    $('[data-bbcode-tag]').on('click', function() {
        handleBBcodeButton($(this));
        var target = $(this).data('bbcode-target');
        $('body').animate({scrollTop: $(target).offset().top});
        $(target).focus();
        return false;
    })

});

function checkVisibleSelect(element) {
    // add handler for this element
    $(element).on('change', function() {
        checkVisibleSelectValue(element);
    });

    $(element).trigger('change');

    $(element).find('option').css('background', 'white');
}

function checkRightSelect(element) {
    // add handler for this element
    $(element).on('change', function() {
        checkRightSelectValue(element);
    });

    $(element).trigger('change');

    $(element).find('option').css('background', 'white');
}

function checkVisibleSelectValue(element) {
    // check value
    if ($(element).val() == 0) {
        $(element).removeClass('dv-background-correct').addClass('dv-background-alert');
    } else if ($(element).val() == 1) {
        $(element).removeClass('dv-background-alert').addClass('dv-background-correct');
    }
}

function checkRightSelectValue(element) {
    // check value
    if ($(element).val() == 0 || $(element).val() == 2) {
        $(element).removeClass('dv-background-correct').addClass('dv-background-alert');
    } else {
        $(element).removeClass('dv-background-alert').addClass('dv-background-correct');
    }
}

/**
 * For now, we simply append the bbcode to the end of the textarea
 * @param button
 */
function handleBBcodeButton(button) {

    var target = $(button.data('bbcode-target'));
    var currentValue = target.val();
    switch (button.data('bbcode-tag')) {

        case 'b':
            target.val(currentValue + '[b][/b]');
            break;
        case 'i':
            target.val(currentValue + '[i][/i]');
            break;
        case 'u':
            target.val(currentValue + '[u][/u]');
            break;
        case 's':
            target.val(currentValue + '[s][/s]');
            break;
        case 'color':
            target.val(currentValue + '[color=red][/color]');
            break;
        case 'left':
            target.val(currentValue + '[left][/left]');
            break;
        case 'center':
            target.val(currentValue + '[center][/center]');
            break;
        case 'right':
            target.val(currentValue + '[right][/right]');
            break;
        case 'justify':
            target.val(currentValue + '[justify][/justify]');
            break;
        case 'quote':
            target.val(currentValue + '[quote=username][/quote]');
            break;
        case 'code':
            target.val(currentValue + '[code][/code]');
            break;
        case 'url':
            target.val(currentValue + '[url=url]text[/url]');
            break;
        case 'img':
            target.val(currentValue + '[img][/img]');
            break;
        case 'video':
            target.val(currentValue + '[video=youtube][/video]');
            break;

    }

}