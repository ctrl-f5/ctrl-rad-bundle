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