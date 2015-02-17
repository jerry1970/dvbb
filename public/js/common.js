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
        $.post(
            $('.dvbb-login-link').data('ajax-url'),
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
    $('.dvbb-logout-link').on('click', function() {
        $.get(
            $('.dvbb-logout-link').data('ajax-url'),
            function() {
                window.location.reload();
            }
        );
        return false;
    });

});