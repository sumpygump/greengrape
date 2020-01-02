---
title = Welcome
---

# Welcome!

Hello, this is the home page of your Greengrape site. If you are seeing this
message, it means that your site is up and running properly, but you haven't
customized the home page.

If you haven't already, take a moment to read a little below about how Greengrape
works and what files you should edit to add content to your site.

## 1.0 Directory Structure

If you take a look at the folder structure of your Greengrape site you will
see the following:

 - `cache` - This folder stores cached versions of the content.
 - `content` - This is where you will edit the actual content files in markdown.
 - `themes` - This is where theme files and assets are located.

## 2.0 Content Files

The *content* folder stores all of the content of your site. All the content
files are saved as *markdown* files (files should end in the .md extension).

    content/
    └── index.md

The file for the home page is called `index.md` and is located in the root of
the `content` folder.

### 2.1 Add New Page

You can add more pages to your site by adding additional
.md files in the content folder.

Open up your text editor and enter the following markdown text:

    # Hello world
    This is a new page on my site.

Then save it as `hello.md` in the content folder. After that, in your browser,
navigate to yoursite.com/hello (replacing yoursite.com with the actual domain
and path where you installed Greengrape). You should see the contents of that
page.

## 3.0 Themes

Theme files are located in the *themes* folder.

    themes/
    └── default
        ├── css
        │   └── main.css
        ├── img
        │   └── greengrape.jpg
        ├── js
        │   └── main.js
        ├── layout.html
        └── templates
            ├── default.html
            └── error.html

