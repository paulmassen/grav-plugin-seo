# v2.3.5
## 09/14/2018
1. [](#fix)
    * Changed the currency field from a select field to a text field to allow rare currencies
    * Fixed the button to validate the page microdata

# v2.3.4
## 03/19/2018
1. [](#fix)
    * Fixed whoops on opengraph image
    
# v2.3.2
## 01/26/2018
1. [](#fix)
    * Fixed opengraph and twitter error
    * Fixed facebook default not fetched from plugin configuration


# v2.3.1
## 01/26/2018
1. [](#fix)
    * Fixed whoops on twitter image
    
# v2.3.0
## 01/26/2018
1. [](#fix)
    * Fixed tag not stripped from description
    * Added areaserved to organization and restaurant microdata
    * Fixed event date problem

# v2.2.0
## 01/26/2018
1. [](#new)
    * Fixed script and style tags in page content causing problem
    * Few languages fixes
    * Re-added date modified and date published that mysteriously disappeared
    
# v2.1.9
## 01/09/2018
1. [](#new)
    * Fixed Changelog causing trouble with GPM
    
# v2.1.8
## 01/08/2018
1. [](#new)
    * Added Nederland translation
    * Fixed founder field causing crash when empty 
    * Fixed default twitter image not being displayed
    * Fixed default values for opengraph and twitter
    * Small changes in microdata blueprints

# v2.1.7
## 12/17/2017
1. [](#new)
    * Added product microdata

# v2.1.6
## 12/12/2017
1. [](#new)
    * Added better styling for the fieldset field
    * Added icons on fieldsets fields
    * Added some required fields to the organization microdata
    * Added button label on the microdata tabs.

1. [](#fix)
    * Fixed the error Invalid Index Type on blueprints that did not use a "content" field
    * Fixed the music album microdata not being displayed
    * Removed duplicate attribute on the blueprint

# v2.1.5
## 12/07/2017
1. [](#new)
    * Removed the empty script tags that appeared on page that did not use microdata
    * Added some required fields to the organization microdata

# v2.1.4
## 11/22/2017
1. [](#fix)
    * Fixed the url of structured data testing tool for non-english language
   
# v2.1.3
## 11/22/2017
1. [](#new)
    * Added a button to validate the page microdata with Google structured data testing tool

# v2.1.2
## 10/12/2017
1. [](#bugfix)
    * Fixed site_name open graph
    
# v2.1.1
## 10/12/2017
1. [](#bugfix)
    * Fixed duplicate toggle
    
# v2.1.0
## 10/12/2017
1. [](#bugfix)
    * Added highlight to Toggle fields
    * Few languages fixes
    * Added url for Organization microdata
    * Few css fixes
    * Added more placeholder

# v2.0.9
## 10/04/2017
1. [](#bugfix)
    * Fixed string manipulation causing error on some install

# v2.0.8
## 10/04/2017
1. [](#new)
    * Added Logo url to Organization Microdata

# v2.0.7
## 10/03/2017
1. [](#new)
    * Added Organization Microdata

# v2.0.6
## 09/10/2017
1. [](#bugfix)
    * Changed Description

# v2.0.5
## 09/10/2017
1. [](#bugfix)
    * First Stable Release - Reads the docs before updating from a 1.0.x Version!
    * Added Person Microdata

# v2.0.4
## 09/10/2017
1. [](#bugfix)
    * Microdata is now automatically added to pages
    * Page date and last modified is modified automatically in article microdata
    


# v2.0.3
## 09/08/2017
1. [](#bugfix)
    * Fixed error on empty fields
    * Added function for getting image width and height


# v2.0.2
## 09/08/2017
1. [](#bugfix)
    * Fixed missing language.yaml entry
    * Added style vertical to microdata for better readability
    * Fixed many bugs
    * Fixed bug when image on a modular folder

# v2.0.0
## 09/05/2017

1. [](#new)
    * Complete Refactor of the plugin with proper metadata integration
    * Users now just have to add {{json}} in the head of their base.html.twig
    * Added configuration for enabling or disabling microdata fields
    * Use of the fieldset and conditional fields
    * Changed filepicker for the new mediapicker field

# v1.0.6
## 06/07/2017

1. [](#bugfix)
    * Added Twitter URL Meta

# v1.0.5
## 06/06/2017

1. [](#bugfix)
    * Fix Twitter title not displayed correctly

# v1.0.4
## 06/02/2017

1. [](#new)
    * New Microdata type: Music Event, MusicAlbum
    * Toggle will now display or hide the related fields 

# v1.0.3
## 06/01/2017

1. [](#new)
    * New feature: Microdata generation
2. [](#bugfix)
    * Fix Page title not rendered correctly

# v0.9.1
## 06/01/2017

1. [](#new)
    * Removed the site-wide configuration.
    * Multiples layout fix
2. [](#bugfix)
    * Fix blueprint causing toggle fields to duplicate.
    * Fix fallback value when fields are not filled.
