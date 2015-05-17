function init() {
    var prettify = false;
    var blocks = document.querySelectorAll('pre code')
    for (var i = 0; i < blocks.length ; i++) {
        blocks[i].className += 'prettyprint linenums';
        prettify = true;
    }
    if (prettify) {
        prettyPrint();
    }
}
window.onload = init;