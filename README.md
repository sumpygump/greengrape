Greengrape
==========

Greengrape provides a simplified way to create a website without complex
configurations. You simply create content in Markdown files in a structured
set of folders and let the site produce the navigation and layout.

A couple advantages with this approach as compared to traditional CMS packages
are as follows:

 - No database requirement
 - No *admin panel* or complex configuration steps.
 - No *wysiwyg* editors or image upload dialogs.
 - You can just create content in folders and refresh the site to see the 
   changes. Ideally, you would have it in version control, commit the changes
   and then run an update on the server.

Greengrape is written for PHP 7+.

## Quickstart

To download and install greengrape use composer:

    $ mkdir mysite
    $ cd mysite
    $ composer require sumpygump/greengrape
    $ vendor/bin/greengrape-install

Another method to make a project with composer `create-project` as follows:

    $ composer create-project sumpygump/greengrape mysite
    $ cd mysite
    $ bin/greengrape-install

## Manual Download & Installation

### Prerequisite: composer

Greengrape uses a few third-party libraries. You must use [composer][] to fetch the
needed libraries.

If you haven't installed composer yet, you can download the composer.phar
executable. See [download instructions at getcomposer.org](https://getcomposer.org/download/).

You can download Greengrape from Github at https://github.com/sumpygump/greengrape/

### Download as archive

If you download as an archive, you can start making changes and commit into
your own version control.

    $ wget https://github.com/sumpygump/greengrape/archive/master.tar.gz
    $ tar xzf master.tar.gz

This will create a directory called `greengrape-master`

### Clone repository

You can also clone the repository like so:

    $ git clone git://github.com/sumpygump/greengrape.git

Once you have the files on your computer, you need to complete a few steps to
get the site running.

### Manual installation

Now you can complete the setup by running the following

1. Install dependencies with composer

        $ composer.phar install

  This will use the `composer.json` file to install the [Twig][] and [Markdown][]
  libraries.

2. Copy `config.ini-dist` to `config.ini`

        $ cp config.ini-dist config.ini

3. Copy `.htaccess-dist` to `.htaccess` and edit RewriteBase

        $ cp .htaccess-dist .htaccess
        $ vi .htaccess

   You need to edit line 8 in that file. Change it to include the Base URL of
   where you have placed the files. For example, if you put the
   `greengrape-master` directory in `/var/www/testing/greengrape-master` folder in you apache
   webroot, (and assuming `/var/www` is the webroot) you will need to edit the line to read:

        RewriteBase /testing/greengrape-master/

   If you installed this on a server where the greengrape directory is at the
   root, then it should read

        RewriteBase /

4. Make the `cache` directory world writable

        $ chmod a+w cache -R

   You could also update this directory to be owned by your apache user (like
   www-data), but I'll let you decide.

5. Load the site up in your browser and you should see the welcome page.

[composer]: http://getcomposer.org/
[twig]: https://github.com/fabpot/Twig
[Markdown]: https://github.com/dflydev/dflydev-markdown

## Usage

### The Content

All of your content resides in the `content` folder. By default you can see
there is an `index.md` file there. That file contains the Markdown for the the
home page.

You can add more `.md` (Markdown) files in this directory and they will be available in your
browser at a URL sans the `.md` extension. For example, if I created a file
called `hello.md` it would be available at *mysite.com/hello*.

You can add folders in the content folder and these will be treated as top
level navigation. I could add a new `projects` directory and some files in that
folder, like so:

    content
    ├── index.md
    └── projects
        ├── funstuff.md
        └── index.md

The contents of `projects/index.md` would be served up if I accessed the URL
*mysite.com/projects/* and the `funstuff.md` file would be available at
*mysite.com/projects/funstuff*

### Navigation Reflects the Folder Structure

Now when I refresh the home page, I see a top level navigation at the top
showing `Home` and `Projects`. Greengrape automatically adds this in based on
the folders in the root of the `content` folder. It will also generate a
sub-navigation at the second level from the root of `content`. It only
generates navigation automatically as far as that second level, but you can
make folders as deeply nested as you like.

### Sorting and Hiding Navigation Items

The items appear in the navigation according to alphabetical order. You can
alter the order by prepending the folder names with a number and a dot, (e.g.
`01.projects`), the `01.` will be stripped and so the URLs will display as
*mysite.com/projects*.

If you don't want a folder to appear in the navigation list, prepend the folder
name with an underscore (`_`).

You can add all kinds of content using the easily readable Markdown syntax by
just editing folders and files on your computer with the text editor of your
choice. That is the beauty of Greengrape.

For a complete reference of the syntax of Markdown please see [the
Markdown documentation](https://www.markdownguide.org/cheat-sheet/).

### Assets

To add images to your Markdown files, you can add them to the `assets`
directory; `assets/img` is a good place for images. In your Markdown files you
can reference them using the following examples:

    See this image: ![alt text](assets/img/coolimage.png).

or

    See this image: ![alt text][id]
    ...
    [id]: assets/img/coolimage.png "Optional title attribute"

You can also reference images from another domain too, just include the `https://`.
For more information about Markdown syntax for images, see the documentation
for [images in Markdown](https://www.markdownguide.org/basic-syntax/#images-1).

Greengrape will automatically put the correct BaseUrl on the path to the image,
so if the install location of the site changes, your references will stay in
tact.

### Links

Links work the same way in Greengrape as with images. It will include the
BaseUrl of wherever Greengrape is installed to ensure the links stay correct if
the install directory ever changed. Just make the reference as if at the root
of the greengrape folder like so (this is using our earlier example of the
`project/funstuff.md` path):

    Check out the [fun stuff](projects/funstuff) I did.

You can of course add links to other domains as well. Don't forget to include
the `http://`:

    Link to [google](http://google.com/).

## Themes

The `themes` folder is where the layout and styles and javascript reside for
your site. Currently, the only theme is the default theme called, "fulcrum."
Greengrape is setup in a way that you could create a new theme and even switch
themes while not disturbing your content, which lives separately in the
`content` folder.

There is no advanced documentation on making a theme, but to begin, I would
suggest making a copy of the fulcrum theme and editing the files.

The theme files use Twig as the templating engine.

The key files of a theme are the following:

 - `layout.html` - This is the basic layout of the theme and all pages will use
   this as the boilerplate for serving the base HTML tags.

 - `templates/default.html` - The templates folder contains snippets of HTML that
   can be used by content block. Currently it is not possible to assign
   different templates to content files (the `.md` files) but it will be a
   future enhancement to assign a content file to use a specific template file.
   Currently all content files use the default template.

 - `templates/error.html`, `templates/404.html`, `templates/_navigation.html`,
   `templates/_subnavigation.html` - These files are all used internally by
   Greengrape to serve error messages, 404 pages and the navigation. Since it
   is HTML with Twig templating, it is useful to know you can make
   modifications to the output to modify a theme.

 - `css`, `img`, `js` - These folders contain CSS, images and JavaScript files.
   These files can be referenced in the layout.html and templates using the
   following Twig syntax:

        <link rel="stylesheet" href="{{ asset.file('css/bootstrap.min.css') }}" />
        <img class="pull-left" src="{{ asset.file('img/greengrape.jpg') }}"
        <script type="text/javascript" src="{{ asset.file('js/plugins') }}"></script>

   Note that if you leave the extension of the filename, Greengrape will
   automatically append a `.js` or `.css` depending on the folder being
   referenced.

   You can also add other assets to a theme and reference them in the same way,
   using the `asset.file()` syntax.

## Config file

The config file is a simple `.ini` file to provide some settings to Greengrape.
The example file is similar to the following:

    ; Site name
    sitename = "My awesome site"

    ; Theme to use
    theme = "fulcrum"

    ; Whether cache is enabled
    enable_cache = false

    ; Additional view params
    author = "Doc Brown"
    description = "A sample website built with Greengrape"

The most important things about this file are the `sitename`, `theme` and
`enable_cache` settings.

 - `sitename` provides the default title for the site
 - `theme` tells Greengrape with theme to use in the `themes` folder
 - `enable_cache` will enable or disable the cache

You can add other param here and they will be passed into the layout file of
the theme using Twig's `{{ }}` syntax, for example:

    <meta name="author" content="{{ layout.author }}">

Additional params would be useful for include Google Analytics account numbers
that need to be output with the HTML layout, for example.

## Cache

Greengrape's cache will serve up a cached file that represents any content file
that is accessible if the corresponding file exists in the cache, otherwise it
will generate the HTML output and save it to the cache.

There is not a robust way to clear the cache in this version, but it is
possible to clear the cache by deleting the files in the `cache/content`
folder. A more advanced cache clearing system will be developed in future
versions.

It is also possible to clear the cache by appending the query string parameter
`?cache=clear` to a request. This will clear the cache for the given requested
page.

It is suggested for ease of development to adjust the setting in the
config.ini file to disable the cache while the site's content is under
development and then enabling it when it is relatively stable.
