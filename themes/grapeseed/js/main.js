
var AnchorLinks = {
    _selector: 'h2,h3',
    init: function() {
        $(this._selector).each(function() {
            var heading = $(this).clone().find('a').remove().end().html();
            var slug = heading.trim().replace(/ /g, "-").toLowerCase();
            var anchor_link = '<a id="' + slug + '"></a>';
            var anchor_text = '<a href="#' + slug + '"title="Link to ' + heading + '" class="anchor-link icon"> &#182;</a>';
            $(this).before(anchor_link);
            $(anchor_text).appendTo($(this));
        });
    }
};

$(function() {
    AnchorLinks.init();
});
