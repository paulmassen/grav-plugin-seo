# ![Grav SEO Plugin](https://github.com/paulmassen/grav_seo_plugin/blob/master/gravlogo3.png?raw=true)


##### Table of Contents:

* [About](#about)
* [Installation and Updates](#installation-and-updates)
* [Usage](#usage)
* [Contributing](#contributing)
* [License](#license)

## Todo

- [x] Do not load tab if page is modular
- [x] Cleanup seo.php
- [x] Clenup doc from external_links plugin from

## About

`Seo` is an user-friendly plugin for [GetGrav.org](http://getgrav.org) used to manage all your metatags and customize your pages appearance in Search Engine Results or social networks.

If you are interested in seeing this plugin in action, here is a demo:

## Features

- 


## Installation and Updates

Installing or updating the `Awesome SEO` plugin can be done in one of three ways. Using the GPM (Grav Package Manager) installation update method (i.e. `bin/gpm install awesome_seo`) or manual install by downloading [this plugin](https://github.com/paulmassen/grav_seo_plugin) and extracting all plugin files to

    /your/site/grav/user/plugins/seo

Once installed, just include the metadata snippet in your base.html.twig template, between <head> and </head> as below:
```
{% include 'partials/seo_data.html.twig' %}
```

#### GPM Installation

####

Notes:

As the plugin takes care of generating the title tag and the description tag, make sure those are removed from your base template.

## Usage

The `Awesome Seo` plugin allows you to set site-wide default value and customize it on a per-page basis. 
Site-wide settings are set in the plugin configuration and are overridden when set on a page.

### Config Defaults



## Contributing

You can contribute at any time! Before opening any issue, please search for existing issues!

After that please note:

* If you find a bug, would like to make a feature request or suggest an improvement, [please open a new issue][issues]. If you have any interesting ideas for additions to the syntax please do suggest them as well!
* Feature requests are more likely to get attention if you include a clearly described use case.


### Support and donations


## License


