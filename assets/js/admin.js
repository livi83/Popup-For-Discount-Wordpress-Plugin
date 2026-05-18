(function ($) {
    'use strict';

    $(document).ready(function () {
        $('.pfd-select-media').on('click', function (event) {
            event.preventDefault();

            const button = $(this);
            const field = button.closest('.pfd-media-field');
            const input = field.find('.pfd-media-url');
            const preview = field.closest('td').find('.pfd-media-preview');

            const frame = wp.media({
                title: button.data('title') || 'Select image',
                button: {
                    text: button.data('button-text') || 'Use this image'
                },
                multiple: false
            });

            frame.on('select', function () {
                const attachment = frame.state().get('selection').first().toJSON();

                if (!attachment || !attachment.url) {
                    return;
                }

                input.val(attachment.url);

                preview.html(
                    '<img src="' + attachment.url + '" alt="">'
                );
            });

            frame.open();
        });

        $('.pfd-remove-media').on('click', function (event) {
            event.preventDefault();

            const button = $(this);
            const field = button.closest('.pfd-media-field');
            const input = field.find('.pfd-media-url');
            const preview = field.closest('td').find('.pfd-media-preview');

            input.val('');
            preview.empty();
        });
    });
})(jQuery);