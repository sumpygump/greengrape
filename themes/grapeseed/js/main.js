
const AnchorLinks = {
    _selector: 'h1,h2,h3,h4,h5,h6',
    sluglist: {},
    init: function() {
        this.bind();
        this.goto();
    },
    bind: function() {
        const self = this;
        $(this._selector).each(function() {
            const heading = $(this).clone().find('a').remove().end().text();
            const slug = self.addSlug(heading.trim().replace(/ /g, "-").toLowerCase());
            const anchor_link = '<a id="' + slug + '"></a>';
            const anchor_text = '<a href="#' + slug + '"title="Link to ' + heading + '" class="anchor-link icon"> &#182;</a>';
            $(this).before(anchor_link);
            $(anchor_text).appendTo($(this));
        });
    },
    addSlug: function(slugName) {
        if (typeof this.sluglist[slugName] !== 'undefined') {
            // If we already had a heading with this exact name, append a digit
            // to the slug
            return slugName + '-' + (++this.sluglist[slugName]);
        }

        this.sluglist[slugName] = 0;
        return slugName;
    },
    goto: function() {
        if (location.hash) {
            const yourElement = document.getElementById(location.hash.replace('#', ''));
            const y = yourElement.getBoundingClientRect().top + window.pageYOffset;

            window.scrollTo({top: y, behavior: 'smooth'});
        }
    }
};

const HighlightCode = {
    init: function() {
        hljs.initHighlighting();
    }
}

$(function() {
    AnchorLinks.init();
    HighlightCode.init();
});
