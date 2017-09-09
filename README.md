# ![Grav SEO Plugin](https://github.com/paulmassen/grav-plugin-seo/blob/develop/logoseo.png?raw=true)


##### Table of Contents:

* [About](#about)
* [Installation and Updates](#installation-and-updates)
* [Usage](#usage)
* [Contributing](#contributing)
* [License](#license)


## About

`Seo` is an user-friendly plugin for [GetGrav.org](http://getgrav.org) used to manage all your metatags in order to customize your pages appearance in Search Engine Results or social networks. The plugin also allows the generation of JSON-LD Microdata.

## Features

### Google
You can see and customize how your page will look on Google Search Results.

![Grav SEO Plugin](https://github.com/paulmassen/grav-plugin-seo/blob/master/demoseoplugin.gif?raw=true)

### Twitter
You can also preview how your page will look when shared on twitter

![Grav SEO Plugin](https://github.com/paulmassen/grav-plugin-seo/blob/master/demoseoplugin.gif?raw=true)


### Facebook
And on Facebook

![Facebook Live Preview](https://raw.githubusercontent.com/paulmassen/grav-plugin-seo/develop/facebook.gif)


###JSON-LD

You can also generate Schema.org JSON Microdata from the admin.
![Article Microdata](https://raw.githubusercontent.com/paulmassen/grav-plugin-seo/develop/article_json.png)
This will generate the following Json-ld between script tags
```
<script type="application/ld+json">
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
</script>
```
#### Feedback needed

As this plugin is in its early stage, please do not hesitate to leave a feedback, to suggest modification or features.

### TO-DO

- [ ] Add more Microdata type
- [ ] Add Translations
- [x] Add the possibility to add multiple microdata of the same type




## Installation and Updates

Installing or updating the `SEO` plugin can be done in one of three ways. Using the GPM (Grav Package Manager) installation update method (i.e. `bin/gpm install seo`) or manual install by downloading [this plugin](https://github.com/paulmassen/grav-plugin-seo) and extracting all plugin files to

    /your/site/grav/user/plugins/seo

Once installed, the only thing you have to do is to add 
```
{{ json }}
```
between the `<head>` and `</head>` tags in your base.html.twig
Don't forget to also add the default partials/metadata.html.twig in order to output the twitter and opengraph meta tags.
The first lines of your base.html.twig should looks like that:
```     
<title>{{ header.title|e('html') }} </title>
{{ json }}
{% include 'partials/metadata.html.twig' %}
```
If you plan on using the Twitter feature, make sure to fill your user ID in tab Plugins > SEO > Twitter ID

## Configuration


## Usage

The `SEO` plugin appends a SEO tab on every pages where you can manage and define how your website will look on search engine results and on social networks. 


## Contributing

You can contribute at any time! Before opening any issue, please search for existing issues!

After that please note:

* If you find a bug, would like to make a feature request or suggest an improvement, [please open a new issue][issues]. If you have any interesting ideas for additions to the syntax please do suggest them as well!
* Feature requests are more likely to get attention if you include a clearly described use case.



## License

