
var resize_div_id = 'cartDiv';
var narrow_limit = 1100;
var wide_limit = 2000;

var prev_w = 0;
var dupe_count = 0;

function getBrowserWidth() {
    if (window.innerWidth) {
        return window.innerWidth;
    } else if (document.documentElement &&
        document.documentElement.clientWidth != 0) {
        return document.documentElement.clientWidth;
    } else if (document.body) {
        return document.body.clientWidth;
    }
    return 0;
}

function resize_css()
{
    var w = getBrowserWidth(); 
    var c = (w < narrow_limit) ? 'block-cart-narrow' : (w > wide_limit) ? 'block-cart' : 'block-cart';
    


    document.getElementById(resize_div_id).className = c;
    prev_w = w;
}

// set the event handlers
window.onresize = resize_css;
