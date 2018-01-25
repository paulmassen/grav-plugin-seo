# ![Grav SEO Plugin](https://github.com/paulmassen/grav-plugin-seo/blob/master/assets/logoseo.png?raw=true)

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MX77VZWPUKLTU)

##### Table of Contents:


* [About](#about)
* [Features](#features)
* [Installation and Updates](#installation-and-updates)
* [Requirements](#requirements)
* [Usage](#usage)
* [Contributing](#contributing)
* [License](#license)


## About

`Seo` is an user-friendly plugin for [GetGrav.org](http://getgrav.org) used to manage all your metatags in order to customize your pages appearance in Search Engine Results or social networks. The plugin also allows the generation of JSON-LD Microdata.

## Features

### Google
You can see and customize how your page will look on Google Search Results.

![Grav SEO Plugin](https://raw.githubusercontent.com/paulmassen/grav-plugin-seo/master/assets/demoseoplugin.gif)

### Twitter
You can also preview how your page will look when shared on twitter

![Grav SEO Plugin](https://raw.githubusercontent.com/paulmassen/grav-plugin-seo/master/assets/twitter.gif)


### Facebook
And on Facebook

![Facebook Live Preview](https://raw.githubusercontent.com/paulmassen/grav-plugin-seo/master/assets/facebook.gif)


### JSON-LD

You can also generate Schema.org JSON Microdata from the admin.
![Article Microdata](https://raw.githubusercontent.com/paulmassen/grav-plugin-seo/master/assets/article_json.png)
This will generate the following Json-ld between script tags
```JSON
{
    "@context": "http://schema.org",
    "@type": "Article",
    "headline": "Article Title",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "url": "http://yourwebsite.com"
    },
    "articleBody": "Lorem Ipsum dolor sit amet",
    "datePublished": "2017-12-01T00:00:00+00:00",
    "dateModified": "2019-01-01T00:00:00+00:00",
    "description": "Description of the article",
    "author": "Steve Jobs",
    "publisher": {
        "@type": "Organization",
        "name": "Apple",
        "logo": {
            "@type": "ImageObject",
            "url": "http://yourwebsite.com/home/logo.png",
            "width": "200",
            "height": "100"
        }
    },
    "image": {
        "@type": "ImageObject",
        "url": "http://yourwebsite.com/home/myimage.jpg",
        "width": "800",
        "height": "600"
    }
}
```

## Requirements

In order to use the plugin with a custom template, there is two requirements, you must:
- Include in your base template the metadata template that comes shipped with antimatter, such as: `{% include 'partials/metadata.html.twig' %}`
- For Microdatas, you must use Grav's asset manager. If your template has a line with `{{ assets.js() }}`, it will works.

#### Feedback needed

As this plugin is in its early stage, please do not hesitate to leave a feedback, to suggest modification or features.

### TO-DO

- [ ] Add more Microdata type
- [ ] Add Translations (Contribution welcomed!)
- [x] Add the possibility to add multiple microdata of the same type
- [ ] YoastSEO text analysis integration (WIP)
- [ ] Add some microdata on every page




## Installation and Updates

### Updating from Previous releases

As there is a lot of changes from previous releases, be careful when updating, as your previously set values might be lost.
The previous version required to modify your base template, whereas the 2.0+ version of the plugin now adds metadata and microdata automatically to your existing Installation.

Installing or updating the `SEO` plugin can be done in one of three ways. Using the GPM (Grav Package Manager) installation update method (i.e. `bin/gpm install seo`) or manual install by downloading [this plugin](https://github.com/paulmassen/grav-plugin-seo) and extracting all plugin files to

    /your/site/grav/user/plugins/seo

Once installed, the plugin will automatically set the metadata and append the json-ld microdatas to your document.
If you plan on using the Twitter feature, make sure to fill your user ID in tab Plugins > SEO > Twitter ID

## Configuration

Configuration is done through the plugin configuration page, accessible by clicking on Plugins > Seo. On this page, you can choose to enable the microdata fields you will use.
Make sure to fill the Facebook ID field as well as the Twitter ID field, in order for your meta tags to be validated.

## Usage

The `SEO` plugin appends a SEO tab on every pages where you can manage and define how your website will look on search engine results and on social networks. 


## Contributing

You can contribute at any time! Before opening any issue, please search for existing issues!

After that please note:

* If you find a bug, would like to make a feature request or suggest an improvement, [please open a new issue][issues]. If you have any interesting ideas for additions to the syntax please do suggest them as well!
* Feature requests are more likely to get attention if you include a clearly described use case.



## License

