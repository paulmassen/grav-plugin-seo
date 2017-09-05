# ![Grav SEO Plugin](https://github.com/paulmassen/grav-plugin-seo/blob/develop/logoseo.png?raw=true)


##### Table of Contents:

* [About](#about)
* [Installation and Updates](#installation-and-updates)
* [Usage](#usage)
* [Contributing](#contributing)
* [License](#license)


## About

`Seo` is an user-friendly plugin for [GetGrav.org](http://getgrav.org) used to manage all your metatags and customize your pages appearance in Search Engine Results or social networks.

This plugins add a SEO tab, where you or your end user can customize these settings. The plugins also has an user-friendly live-update preview functionality as shown below:

![Grav SEO Plugin](https://github.com/paulmassen/grav-plugin-seo/blob/develop/demoseoplugin.gif?raw=true)

#### Feedback needed

As this plugin is in its early stage, please do not hesitate to leave a feedback, to suggest modification or features.

### TO-DO

- [ ] Add more Microdata type
- [ ] Add Translations
- [ ] Add the possibility to add multiple microdata of the same type


## Features

As for now, you can customize how your website will look:
- On Google Search Results
- When shared on Twitter
- When shared on Facebook and Google +

You can also generate Schema.org JSON Microdata from the admin.



## Installation and Updates

Installing or updating the `SEO` plugin can be done in one of three ways. Using the GPM (Grav Package Manager) installation update method (i.e. `bin/gpm install seo`) or manual install by downloading [this plugin](https://github.com/paulmassen/grav-plugin-seo) and extracting all plugin files to

    /your/site/grav/user/plugins/seo

Once installed, the first step is to include the metadata snippet in your base.html.twig template, between <head> and </head> as below:
The if condition is here in order to not throw an error if the plugin is deactivated.
```
{% if config.plugins.seo.enabled %}
{% include 'partials/seo_data.html.twig' %}
{% endif %}
```

Step 2: If you use a Grav Theme or Skeleton, make sure to remove the `<title></title>` tags as well as the meta description tag that comes with most of them inside base.html.twig, as the plugin takes care of rendering it.

Step 3: If you plan on using the Twitter sharing settings, make sure to fill your user ID in tab Plugins > SEO > Twitter ID


## Usage

The `SEO` plugin appends a SEO tab on every pages where you can manage and define how your website will look on search engine results and on social networks. 


## Contributing

You can contribute at any time! Before opening any issue, please search for existing issues!

After that please note:

* If you find a bug, would like to make a feature request or suggest an improvement, [please open a new issue][issues]. If you have any interesting ideas for additions to the syntax please do suggest them as well!
* Feature requests are more likely to get attention if you include a clearly described use case.



## License


