# COLORBOX INLINE

# INTRODUCTION

[Colorbox](http://www.jacklmoore.com/colorbox/) is a light-weight, customizable lightbox plugin for
[jQuery](http://jquery.com/). This module extends the Drupal 
[Colorbox module's](https://drupal.org/project/colorbox) integration to add
support for colorbox-ing inline elements in your templates and content.

# FEATURES
colorbox_inline allows you to open content already on the page within a colorbox. 
If you would like to load content via AJAX, you can use 
[colorbox_load](https://www.drupal.org/project/colorbox_load).

# REQUIREMENTS

- Main [colorbox module](https://drupal.org/project/colorbox).
- Color box library (see
[main module readme](https://git.drupalcode.org/project/colorbox/-/blob/HEAD/README.txt))


# INSTALLATION

1. Install the module as normal, see also
[core docs](https://www.drupal.org/documentation/install/modules-themes/modules-8)
2. Make sure to install the Colorbox library as per 
[parent module readme](https://git.drupalcode.org/project/colorbox/-/blob/HEAD/README.txt).


# USAGE
To create an element which opens the colorbox on click:

- Enable the module. No other options or configuration exist.
- Add the attribute `data-colorbox-inline` to an element and make its value a selector for the
element you wish to open. Eg, `<a data-colorbox-inline=".user-login">User Login</a>` will open a
popup with the first element with the class `user-login` as the content.
- Optional add `data-width` and `data-height` to the link to control the size of the modal window.
