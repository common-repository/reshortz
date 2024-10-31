/**
 * Fallback click to copy
 *
 * @param text
 */
function reshortz_fallback_click_to_copy(text) {
    var textArea = document.createElement("textarea");
    textArea.value = text;

    // Avoid scrolling to bottom
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
    } catch (err) {
    }

    document.body.removeChild(textArea);
}

/**
 * Copy text to clipboard
 *
 * @param text
 */
function reshortz_copy_to_clipboard(text) {
    if (!navigator.clipboard) {
        reshortz_fallback_click_to_copy(text);
        return;
    }
    navigator.clipboard.writeText(text).then(function() {
    }, function(err) {
    });
}

/**
 * Copy to clipboard
 *
 * @type {{init: reshortz_shortcode_copy.init}}
 */
var reshortz_shortcode_copy = {
    init: function() {
        jQuery(document).on('click', '.reshortz_posts-column__shortcode', function(e) {
            e.preventDefault();
            var $this = jQuery(this);
            var text = $this.find('.reshortz_shortcode_text').text();
            reshortz_copy_to_clipboard(text);

            $this.find('.reshortz_text_copied').show();

            setTimeout(function() {
                $this.find('.reshortz_text_copied').hide();
            }, 1500);
        })
    }
};

/**
 * Initialize admin scripts
 */
jQuery(document).ready(function() {
    reshortz_shortcode_copy.init();
})
