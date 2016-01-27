$(function () {

    var $body = $('body');

    /**
     * Select2
     */
    $('.select2').each(function () {
        var options = {};

        if ($(this).attr('data-select2-tags') == 'true') {
            options.tags = true;
        }

        $(this).select2(options);
    });

    $body.on('click', '[data-ctrl-confirm]', function (e) {
        var $el = $(this);
        var message = $(this).data('ctrl-confirm');
        if (!message) message = 'Please confirm this action';

        var $modal;
        if ($(this).attr('data-ctrl-confirm-modal')) {
            $modal = $($(this).data('ctrl-confirm-modal'));
        } else {
            $modal = $(
                '<div class="modal fade" tabindex="-1">\
                    <div class="modal-dialog modal-sm">\
                        <div class="modal-content">\
                            <div class="modal-body">' + message + '</div>\
                            <div class="modal-footer">\
                                <button type="button" class="btn btn-success confirm-action" data-dismiss="modal"">Confirm</button>\
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
                            </div>\
                        </div>\
                    </div>\
                </div>'
            );
        }

        $modal.on('click', 'button.confirm-action', function () {
            window.location = $el.attr('href');
        });

        $modal.modal();

        e.stopPropagation();
        e.preventDefault();
        return false;
    });
});

/**
 * google prettify syntax highlighter
 */
function google_code_prettify()
{
    if (prettyPrint) {
        var prettify = false;
        var blocks = document.querySelectorAll('pre code');
        for (var i = 0; i < blocks.length; i++) {
            if (0 > blocks[i].className.indexOf('prettyprint')) {
                blocks[i].className += ' prettyprint linenums';
                prettify = true;
            }
        }
        if (prettify) {
            prettyPrint();
        }
    }
}

window.onload = google_code_prettify();
