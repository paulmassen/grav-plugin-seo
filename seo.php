<?php
/**
 * SEO v2.3.5
 *
 * This plugin adds an SEO Tab to every pages for managing SEO data.
 *
 * Licensed under the MIT license, see LICENSE.
 *
 * @package     SEO
 * @version     3.0
 * @link        <https://github.com/paulmassen/grav-plugin-seo>
 * @author      Paul Massendari <paul@massendari.com>
 * @copyright   2020, Paul Massendari
 * @license     <http://opensource.org/licenses/MIT>        MIT
 */

namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Page\Page;
use Grav\Common\Data\Blueprints;
use Grav\Common\Page\Pages;
use RocketTheme\Toolbox\Event\Event;
use Grav\Common\Grav;
use Grav\Common\Page\Media;
use Grav\Common\Helpers\Exif;
use Grav\Common\Page\Medium\AbstractMedia;
use Grav\Common\Iterator;


/**
 * SEO Plugin
 *
 * This plugin adds an user-friendly SEO tab for your user to manage metadata tags
 * and appearance on Search Engine Results and Social Networks.
 */

class SeoPlugin extends Plugin
{

    /** -------------
     * Public methods
     * --------------
     */

    /**
     * Return a list of subscribed events.
     *
     * @return array    The list of events of the plugin of the form
     *                      'name' => ['method_name', priority].
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            'onPageInitialized'    => ['onPageInitialized', 0],
           // 'onPageContentRaw' => ['onPageContentRaw', 0],
          //  'onBlueprintCreated' => ['onBlueprintCreated',  0]
        ];
    }

    private function cleanArray(array $array): array 
{
    foreach ($array as $key => &$value) {
        if (is_array($value)) {
            $value = $this->cleanArray($value);
        }
        
        if (empty($value) && $value !== 0 && $value !== '0') {
            unset($array[$key]);
        }
    }
    
    return $array;
}
  
/**
 * Récupère les métadonnées d'une image (dimensions et URL)
 * 
 * @param string|null $imageUrl URL de l'image à analyser
 * @return array{width: string, height: string, url: string}
 */
private function seoGetImage(?string $imageUrl): array
{
    // Si l'URL est vide, retourner directement les valeurs par défaut
    if (empty($imageUrl)) {
        return [
            'width' => '0',
            'height' => '0',
            'url' => '',
        ];
    }

    try {
        // Extraction du chemin et du nom de fichier
        if (!preg_match('~((\/[^\/]+)+)\/([^\/]+)~', $imageUrl, $matches)) {
            throw new \RuntimeException('Format d\'URL invalide');
        }

        $imagePath = $matches[1];
        $imageName = $matches[3];

        // Récupération de la page
        $page = $this->grav['page']->find($imagePath);
        if (!$page) {
            throw new \RuntimeException("Page non trouvée: $imagePath");
        }

        // Vérification de la présence d'images
        $images = $page->media()->images();
        if (empty($images)) {
            throw new \RuntimeException("Aucune image trouvée");
        }

        // Recherche de l'image spécifique
        $availableImages = array_keys($images);
        $imageIndex = array_search($imageName, $availableImages);
        if ($imageIndex === false) {
            throw new \RuntimeException("Image spécifique non trouvée");
        }

        $imageKey = $availableImages[$imageIndex];
        $image = $images[$imageKey];
        
        // Vérification du chemin de l'image
        if (!$image || !$image->path() || !file_exists($image->path())) {
            throw new \RuntimeException("Fichier image invalide ou inaccessible");
        }

        $dimensions = @getimagesize($image->path());
        if ($dimensions === false) {
            throw new \RuntimeException("Impossible de lire les dimensions de l'image");
        }

        return [
            'width' => (string)$dimensions[0],
            'height' => (string)$dimensions[1],
            'url' => $image->url(),
        ];

    } catch (\Exception $e) {
        // Log l'erreur mais ne casse pas le site
        $this->grav['log']->debug('SEO Plugin - Image Warning: ' . $e->getMessage());
        
        return [
            'width' => '0',
            'height' => '0',
            'url' => '',
        ];
    }
}
    /**
 * Nettoie et convertit le texte Markdown en texte brut
 * 
 * @param string $text Le texte Markdown à nettoyer
 * @param int $maxLength Longueur maximale du texte retourné (défaut: 320)
 * @return string Le texte nettoyé
 */
    private const MARKDOWN_RULES = [
        // Suppression des inclusions Twig
        '/{%[\s\S]*?%}[\s\S]*?/' => '',
        
        // Suppression des balises HTML spécifiques
        '/<style[^>]*?>.*?<\/style>/si' => '',
        '/<script[^>]*?>.*?<\/script>/si' => '',
        
        // Conversion de la syntaxe Markdown
        '/^#+\s*(.*)$/m' => '$1',                  // Titres
        '/^[*\-_]{3,}$/m' => '',                   // Lignes horizontales
        '/!\[([^\]]*)\]\([^)]+\)/' => '',          // Images
        '/\[([^\]]+)\]\([^)]+\)/' => '$1',         // Liens
        '/[*_]{2}(.*?)[*_]{2}/' => '$1',           // Gras
        '/[*_](.*?)[*_]/' => '$1',                 // Italique
        '/~~(.*?)~~/' => '$1',                     // Barré
        '/:`(.*?)`/' => '$1',                      // Code inline
        '/^```[\s\S]*?```$/m' => '',               // Blocs de code
        '/^[*\-+]\s+(.*)$/m' => '$1',              // Listes non ordonnées
        '/^\d+\.\s+(.*)$/m' => '$1',               // Listes ordonnées
        '/^>\s*(.*)$/m' => '$1',                   // Citations
        '/<!--[\s\S]*?-->/' => '',                 // Commentaires HTML
    ];

    private function cleanMarkdown(string $text, int $maxLength = 320): string 
{

    // Nettoyage initial
    $text = strip_tags($text);

    // Application des règles de nettoyage Markdown
    foreach (self::MARKDOWN_RULES as $pattern => $replacement) {
        $text = preg_replace($pattern, $replacement, $text);
    }

    // Nettoyage final
    $text = preg_replace('/\s+/', ' ', $text);           // Remplace les espaces multiples
    $text = str_replace(["\r", "\n"], ' ', $text);       // Remplace les retours à la ligne
    $text = preg_replace('/\. \./', '.', $text);         // Corrige la ponctuation
    $text = trim($text);                                 // Supprime les espaces aux extrémités

    // Retourne le texte tronqué à la longueur maximale
    return mb_substr($text, 0, $maxLength);
}
    

    /**
     * Initialize configuration
     */
    public function onPluginsInitialized()
    {

        // Set default events
        $events = [
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
           // 'onPageContentRaw' => ['onPageContentRaw', 0],
        ];

        // Set admin specific events
        if ($this->isAdmin()) {
            $this->active = false;
            $events = [
                'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
                'onBlueprintCreated' => ['onBlueprintCreated', 0],
               // 'onPageContentRaw' => ['onPageContentRaw', 0],
            ];
        }

        // Register events
  
        $this->enable($events);
    }
    public function onPageInitialized()
    {
        $page = $this->grav['page'];
        $config = $this->mergeConfig($page);
        $content = strip_tags($page->content());
        $assets = $this->grav['assets'];
        $customjson = "";
        $outputcustomjson = "";
        $pattern = '~((\/[^\/]+)+)\/([^\/]+)~';
        $replacement = '$1';
        $outputjson = "";
        $uri = $this->grav['uri'];
        $route = $this->config->get('plugins.admin.route');
        $microdata = [];
        $meta = $page->metadata(null);
        $cleanedMarkdown = $this->cleanMarkdown($page->content());
       
        if (isset($page->header()->googletitle)) {
            $page->header()->displaytitle = $page->header()->title;  // Keep original title available for template use
            $page->header()->title = $page->header()->googletitle;
        };
        if (isset($page->header()->googledesc)) {
            
            $meta['description']['name']      = 'description';
            $meta['description']['content']   = $page->header()->googledesc;
        
        } else {
            $meta['description']['name']      = 'description';
            $meta['description']['content']   = $cleanedMarkdown;
        };
        
             /**
             * Set Twitter Metatags
             */

        if (property_exists($page->header(),'twitterenable')) {
        if ($page->header()->twitterenable == 'true') {
        
            if (isset($config['twitterid'])) {
                $meta['twitter:site']['name']      = 'twitter:site';
                $meta['twitter:site']['property']  = 'twitter:site';
                $meta['twitter:site']['content']   = $config->twitterid;
            };
            if (isset($page->header()->twittercardoptions)) {
                $meta['twitter:card']['name']      = 'twitter:card';
                $meta['twitter:card']['property']  = 'twitter:card';
                $meta['twitter:card']['content']   = $page->header()->twittercardoptions;
            } else {
                $meta['twitter:card']['name']      = 'twitter:card';
                $meta['twitter:card']['property']  = 'twitter:card';
                $meta['twitter:card']['content']   = 'summary_large_image';
            };
            
            if (isset($page->header()->twittertitle)) {
                $meta['twitter:title']['name']      = 'twitter:title';
                $meta['twitter:title']['property']  = 'twitter:title';
                $meta['twitter:title']['content']   = $page->header()->twittertitle;
            } else {
                $meta['twitter:title']['name']      = 'twitter:title';
                $meta['twitter:title']['property']  = 'twitter:title';
                $meta['twitter:title']['content']   = $page->title() . ' | ' . $this->config->get('site.title');
            };
            if (isset($page->header()->twitterdescription)) {
                $meta['twitter:description']['name']      = 'twitter:description';
                $meta['twitter:description']['property']  = 'twitter:description';
                $meta['twitter:description']['content']   = $page->header()->twitterdescription;
            } else {
                $meta['twitter:description']['name']      = 'twitter:description';
                $meta['twitter:description']['property']  = 'twitter:description';
                $meta['twitter:description']['content']   =  $cleanedMarkdown;
            };
            if (isset($page->header()->twittershareimg)) {
                $meta['twitter:image']['name']      = 'twitter:image';
                $meta['twitter:image']['property']  = 'twitter:image';
                $twittershareimg = $page->header()->twittershareimg;
                $imagedata = $this->seoGetimage($twittershareimg);
                $meta['twitter:image']['content']   = $this->grav['uri']->base() . $imagedata['url'];
            } elseif(!empty($page->media()->images())) {
                
                $meta['twitter:image']['name']      = 'twitter:image';
                $meta['twitter:image']['property']  = 'twitter:image';
                $imgobject = $page->media()->images();
                $getfirst = array_shift($imgobject);
                $firstimage = $getfirst->url();
                //$imagedata = $this->seoGetimage($firstimage);
                $meta['twitter:image']['content']   = $this->grav['uri']->base() . $firstimage;
            };
            $meta['twitter:url']['name']      = 'twitter:url';
            $meta['twitter:url']['property']  = 'twitter:url';
            $meta['twitter:url']['content']   = $page->url(true);
        }
        }
         if (property_exists($page->header(),'facebookenable')){
         if ($page->header()->facebookenable == 'true') {
         
                //$meta['og:sitename']['name']        = 'og:sitename';
                $meta['og:site_name']['property']    = 'og:site_name';
                $meta['og:site_name']['content']     = $this->config->get('site.title');
            if (isset($page->header()->facebooktitle)) {
                //$meta['og:title']['name']           = 'og:title';
                $meta['og:title']['property']       = 'og:title';
                $meta['og:title']['content']        = $page->header()->facebooktitle;
            } else {
               // $meta['og:title']['name']           = 'og:title';
                $meta['og:title']['property']       = 'og:title';
                $meta['og:title']['content']        = $page->title();
            }
            if (isset($config['facebookid'])) {
                //$meta['twitter:site']['name']      = 'twitter:site';
                $meta['fb:app_id']['property']  = 'fb:app_id';
                $meta['fb:app_id']['content']   = $config->facebookid;
            };
                //$meta['og:type']['name']            = 'og:type';
                $meta['og:type']['property']        = 'og:type';
                $meta['og:type']['content']         = 'article';
               // $meta['og:url']['name']             = 'og:url';
                $meta['og:url']['property']         = 'og:url';
                $meta['og:url']['content']          = $this->grav['page']->canonical(true);
            if (isset($page->header()->facebookdesc)) {
                //$meta['og:description']['name']     = 'og:description';
                $meta['og:description']['property'] = 'og:description';
                $meta['og:description']['content'] =  substr($this->cleanMarkdown($page->header()->facebookdesc),0,320);
            } else {
               // $meta['og:description']['name']     = 'og:description';
                $meta['og:description']['property'] = 'og:description';
                $meta['og:description']['content'] =  $cleanedMarkdown;
            }
            if (isset($page->header()->facebookauthor)) {
              //  $meta['article:author']['name']     = 'article:author';
                $meta['article:author']['property'] = 'article:author';
                $meta['article:author']['content'] =   $page->header()->facebookauthor;
            }
            if (isset($page->header()->facebookimg)) {
               // $meta['og:image']['name']     = 'og:image';
                $meta['og:image']['property'] = 'og:image';
                $facebookimg = $page->header()->facebookimg;
                $imagedata = $this->seoGetimage($facebookimg);
                $meta['og:image']['content'] =  $this->grav['uri']->base() . $imagedata['url'];
            } elseif(!empty($page->media()->images())) {
                $meta['og:image']['property'] = 'og:image';
                $imgobject = $page->media()->images();
                $getfirst = array_shift($imgobject);
                $firstimage = $getfirst->url();
                //$imagedata = $this->seoGetimage($firstimage);
                $meta['twitter:image']['content']   = $this->grav['uri']->base() . $firstimage;
                $meta['og:image']['content'] =  $this->grav['uri']->base() . $firstimage;
            }
       
         }
             
         }
        // Add metadata
      $page->metadata($meta);
        // Set Json-Ld Microdata
        // Article Microdata
     if (property_exists($page->header(), 'musiceventenabled')) {
    if ($page->header()->musiceventenabled && $this->config['plugins']['seo']['musicevent']) {
        $musiceventsarray = $page->header()->musicevents ?? [];
        
        // Vérifier que nous avons un array valide et non vide
        if (is_array($musiceventsarray) && !empty($musiceventsarray)) {
            foreach ($musiceventsarray as $event) {
                $performerarray = [];  // Initialiser pour chaque événement
                $workarray = [];       // Initialiser pour chaque événement
                $musiceventimage = null;  // Initialiser pour chaque événement

                // Gestion des performers
                if (!empty($event['musicevent_performer']) && is_array($event['musicevent_performer'])) {
                    foreach ($event['musicevent_performer'] as $artist) {
                        $performerarray[] = [
                            '@type' => $artist['performer_type'] ?? 'PerformingGroup',
                            'name' => $artist['name'] ?? '',
                            'sameAs' => $artist['sameAs'] ?? '',
                        ];
                    }
                }

                // Gestion des œuvres interprétées
                if (!empty($event['musicevent_workPerformed']) && is_array($event['musicevent_workPerformed'])) {
                    foreach ($event['musicevent_workPerformed'] as $work) {
                        $workarray[] = [
                            'name' => $work['name'] ?? '',
                            'sameAs' => $work['sameAs'] ?? '',
                        ];
                    }
                }

                // Gestion de l'image
                if (!empty($event['musicevent_image'])) {
                    $imagedata = $this->seoGetImage($event['musicevent_image']);
                    if (!empty($imagedata['url'])) {
                        $musiceventimage = [
                            '@type' => 'ImageObject',
                            'width' => $imagedata['width'],
                            'height' => $imagedata['height'],
                            'url' => $this->grav['uri']->base() . $imagedata['url'],
                        ];
                    }
                }

                // Construction de l'événement
                $eventData = [
                    '@context' => 'http://schema.org',
                    '@type' => 'MusicEvent',
                    'name' => $event['musicevent_location_name'] ?? '',
                    'location' => [
                        '@type' => 'MusicVenue',
                        'name' => $event['musicevent_location_name'] ?? '',
                        'address' => $event['musicevent_location_address'] ?? '',
                    ],
                    'description' => $event['musicevent_description'] ?? '',
                    'url' => $event['musicevent_url'] ?? '',
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => $event['musicevent_offers_price'] ?? '',
                        'priceCurrency' => $event['musicevent_offers_priceCurrency'] ?? '',
                        'url' => $event['musicevent_offers_url'] ?? '',
                    ],
                ];

                // Ajouter les champs optionnels seulement s'ils existent
                if (!empty($performerarray)) {
                    $eventData['performer'] = $performerarray;
                }
                if (!empty($workarray)) {
                    $eventData['workPerformed'] = $workarray;
                }
                if ($musiceventimage) {
                    $eventData['image'] = $musiceventimage;
                }

                // Gestion des dates
                if (!empty($event['musicevent_startdate'])) {
                    $eventData['startDate'] = date("c", strtotime($event['musicevent_startdate']));
                }
                if (!empty($event['musicevent_enddate'])) {
                    $eventData['endDate'] = date("c", strtotime($event['musicevent_enddate']));
                }

                $microdata[] = $eventData;
            }
        }
    }
}
       if (property_exists($page->header(), 'eventenabled')) {
    if ($page->header()->eventenabled && $this->config['plugins']['seo']['event']) {
        $eventsarray = $page->header()->addevent ?? [];
        
        // Vérifier que nous avons un array valide et non vide
        if (is_array($eventsarray) && !empty($eventsarray)) {
            foreach ($eventsarray as $event) {
                // Préparer l'adresse seulement si les données nécessaires existent
                $address = [
                    '@type' => 'PostalAddress',
                ];
                
                // Ajouter les champs d'adresse seulement s'ils existent
                if (!empty($event['event_location_address_addressLocality'])) {
                    $address['addressLocality'] = $event['event_location_address_addressLocality'];
                }
                if (!empty($event['event_location_address_addressRegion'])) {
                    $address['addressRegion'] = $event['event_location_address_addressRegion'];
                }
                if (!empty($event['event_location_streetAddress'])) {
                    $address['streetAddress'] = $event['event_location_streetAddress'];
                }

                // Préparer l'offre seulement si les données nécessaires existent
                $offers = [
                    '@type' => 'Offer',
                ];
                if (!empty($event['event_offers_price'])) {
                    $offers['price'] = $event['event_offers_price'];
                }
                if (!empty($event['event_offers_currency'])) {
                    $offers['priceCurrency'] = $event['event_offers_currency'];
                }
                if (!empty($event['event_offers_url'])) {
                    $offers['url'] = $event['event_offers_url'];
                }

                // Construction de l'événement de base
                $eventData = [
                    '@context' => 'http://schema.org',
                    '@type' => 'Event',
                    'name' => $event['event_name'] ?? '',
                    'location' => [
                        '@type' => 'Place',
                        'name' => $event['event_location_name'] ?? '',
                        'address' => $address,
                    ],
                ];

                // Ajouter l'URL de la location si elle existe
                if (!empty($event['musicevent_location_url'])) {
                    $eventData['location']['url'] = $event['musicevent_location_url'];
                }

                // Ajouter la description si elle existe
                if (!empty($event['event_description'])) {
                    $eventData['description'] = $event['event_description'];
                }

                // Ajouter les offres si elles ne sont pas vides
                if (count(array_filter($offers)) > 1) { // > 1 car @type est toujours présent
                    $eventData['offers'] = $offers;
                }

                // Gestion des dates
                if (!empty($event['event_startDate'])) {
                    $startDate = strtotime($event['event_startDate']);
                    if ($startDate) {
                        $eventData['startDate'] = date("c", $startDate);
                    }
                }
                if (!empty($event['event_endDate'])) {
                    $endDate = strtotime($event['event_endDate']);
                    if ($endDate) {
                        $eventData['endDate'] = date("c", $endDate);
                    }
                }

                $microdata[] = array_filter($eventData, function($value) {
                    return $value !== null && $value !== '';
                });
            }
        }
    }
}
     if (property_exists($page->header(), 'personenabled')) {
    if ($page->header()->personenabled && $this->config['plugins']['seo']['person']) {
        $personarray = $page->header()->addperson ?? [];
        
        // Vérification que $personarray est un array et n'est pas vide
        if (is_array($personarray) && !empty($personarray)) {
            foreach ($personarray as $person) {
                $microdata[] = [
                    '@context' => 'http://schema.org',
                    '@type' => 'Person',
                    'name' => $person['person_name'] ?? null,
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressLocality' => $person['person_address_addressLocality'] ?? null,
                        'addressRegion' => $person['person_address_addressRegion'] ?? null,
                    ],
                    'jobTitle' => $person['person_jobTitle'] ?? null,
                ];
            }
        }
    }
}
        if (property_exists($page->header(),'orgaenabled')){
       if ($page->header()->orgaenabled and $this->config['plugins']['seo']['organization']) {
        if (isset($page->header()->orga['founders'])){
        foreach ($page->header()->orga['founders'] as $founder){
                  $founderarray[] = [
                      '@type' => 'Person',
                      'name' => @$founder['name'],
                    ];    
                 }
        }
        if (isset($page->header()->orga['similar'])){
            foreach ($page->header()->orga['similar'] as $similar){
                      $similararray[] = $similar['sameas'];    
                     }
        }
        if (isset($page->header()->orga['areaserved'])){
            foreach ($page->header()->orga['areaserved'] as $areaserved){
                      $areaservedarray[] = $areaserved['area']; 
                     }
        }   
        if (isset($page->header()->orga['openingHours'])){
            foreach ($page->header()->orga['openingHours'] as $hours){
                      $openingHours[] = $hours['entry'];    
                     }
        }
        if (isset($page->header()->orga['offercatalog'])){
            foreach ($page->header()->orga['offercatalog'] as $offer) {
                if (array_key_exists('offereditem', $offer)) {
                    foreach ($offer['offereditem'] as $service) {
                        $offerarray[] = [
                            '@type' => 'OfferCatalog',
                            'name' => @$offer['offer'],
                            'description' => @$offer['description'],
                            'url' => @$offer['url'],
                            'image' => @$offer['image'],
                            'itemListElement' => [
                                '@type' => 'Offer',
                                'itemOffered' => [
                                    '@type' => 'Service',
                                    'name' => @$service['name'],
                                    'url' => @$service['url'],
                                ],
                            ],
                        ];
                    }
                } else {
                        $offerarray[] = [
                            '@type' => 'OfferCatalog',
                            'name' => @$offer['offer'],
                            'description' => @$offer['description'],
                            'url' => @$offer['url'],
                            'image' => @$offer['image'],
                        ];
                }
            }
        }

        if (property_exists($page->header(),'orgaratingenabled')){

        if ($page->header()->orgaratingenabled){
        $orgarating = [
                      '@type' => 'AggregateRating',
                      'ratingValue' => @$page->header()->orga['ratingValue'],
                      'reviewCount' => @$page->header()->orga['reviewCount'],
                      ];
        } 

        } 
        $microdata[] = [
                  '@context' => 'http://schema.org',
                  '@type' => 'Organization',
                  'name' => @$page->header()->orga['name'],
                  'legalname' => @$page->header()->orga['legalname'],
                  'taxid' => @$page->header()->orga['taxid'],
                  'vatid' => @$page->header()->orga['vatid'],
                  'areaServed' => @$areaservedarray,
                  'description' => @$page->header()->orga['description'],
                  
                  'address' => [
                      '@type' => 'PostalAddress',
                      'streetAddress' => @$page->header()->orga['streetaddress'],
                      'addressLocality' => @$page->header()->orga['city'],
                      'addressRegion' => @$page->header()->orga['state'],
                      'postalCode' => @$page->header()->orga['zipcode'],
                      ],
                  'telephone' => @$page->header()->orga['phone'],
                  'logo' => @$page->header()->orga['logo'],
                  'url' => @$page->header()->orga['url'],
                  'openingHours' => @$openingHours,
                  'email' => @$page->header()->orga['email'],
                  'foundingDate' => @$page->header()->orga['foundingDate'],
                  'aggregateRating' => @$orgarating,
                  'paymentAccepted' => @$page->header()->orga['paymentAccepted'],
                  'founders' => @$founderarray,
                  'sameAs' => @$similararray,
                  'hasOfferCatalog' => @$offerarray
                  ];
                 
                  
           
       }
       }
        if (property_exists($page->header(),'restaurantenabled')){
        if ($page->header()->restaurantenabled and $this->config['plugins']['seo']['restaurant']) {
         if (isset($page->header()->restaurant['image'])){
            $imageurl = $page->header()->restaurant['image'];
            $imagedata = $this->seoGetimage($imageurl);
            $restaurantimage = [
                 
                      '@type' => 'ImageObject',
                      'width' => $imagedata['width'],
                      'height' => $imagedata['height'],
                      'url' => $this->grav['uri']->base() .  $imagedata['url'],
                      
                      ];
                
            }
              $microdata[] = [
                  '@context' => 'http://schema.org',
                  '@type' => 'Restaurant',
                  'name' => @$page->header()->restaurant['name'],
                  
                  'address' => [
                      '@type' => 'PostalAddress',
                      'addressLocality' => @$page->header()->restaurant['address_addressLocality'],
                      'addressRegion' => @$page->header()->restaurant['address_addressRegion'],
                      'streetAddress' => @$page->header()->restaurant['address_streetAddress'],
                      'postalCode' => @$page->header()->restaurant['address_postalCode'],
                      ],
                  'areaserved' => @$areaservedarray,
                  'servesCuisine' => @$page->header()->restaurant['servesCuisine'],
                  'priceRange' => @$page->header()->restaurant['priceRange'],
                  'image' => @$restaurantimage,
                  'telephone' => @$page->header()->restaurant['telephone'],
                  
                  ];
            

       }
        }
    if (property_exists($page->header(),'productenabled')){
        if ($page->header()->productenabled and $this->config['plugins']['seo']['product']) {
         if (isset($page->header()->product['image'])){
             $productimagearray = []; 
             $productimages = $page->header()->product['image'];
            
            
             foreach ($productimages as $key => $value){
            $imagearray = $productimages[$key];
            foreach($imagearray as $newkey => $newvalue){
                $imagedata = $this->seoGetimage($imagearray[$newkey]);
                $productimage[] = 
                $this->grav['uri']->base() .  $imagedata['url'];
               
            };
            
             };
         }
         if (isset($page->header()->product['addoffer'])){
             
             $offers = $page->header()->product['addoffer'];
             foreach ($offers as $key => $value){
                 $offer[$key] = [
                      '@type' => 'Offer',
                      'priceCurrency' => @$offers[$key]['offer_priceCurrency'],
                      'price' => @$offers[$key]['offer_price'],
                      'validFrom' => @$offers[$key]['offer_validFrom'],
                      'priceValidUntil' => @$offers[$key]['offer_validUntil'],
                      'availability' => @$offers[$key]['offer_availability'],
                     ];
             };
         }
         else { $offer = ''; }       
            
              $microdata[] = [
                  '@context' => 'http://schema.org',
                  '@type' => 'Product',
                  'name' => @$page->header()->product['name'],
                  'category' => @$page->header()->product['category'],
                  'brand' => [
                      '@type' => 'Thing',
                      'name' => @$page->header()->product['brand'],
                      ],
                  'offers' => $offer,
                  'description' => @$page->header()->product['description'],
                  'image' => @$productimage,
                  'aggregateRating' => [
                      '@type' => 'AggregateRating',
                      'ratingValue' => @$page->header()->product['ratingValue'],
                      'reviewCount' => @$page->header()->product['reviewCount'],
                      
                      ]
                  ];
       }
        }
     if (property_exists($page->header(),'articleenabled')){
            if (isset($page->header()->article['headline'])){
               $headline =  $page->header()->article['headline'];
            } else {
                $headline = $page->title();
            }
       if ($page->header()->articleenabled and $this->config['plugins']['seo']['article']) {
        $microdata['article'] = [
    '@context' => 'http://schema.org',
    '@type' => 'Article',
    'headline' => $headline,
    'mainEntityOfPage' => [
        "@type" => "WebPage",
        'url' => $this->grav['uri']->base(),
    ],
    'articleBody' => $this->cleanMarkdown($content),
    'datePublished' => date("c", strtotime($page->header()->article['datePublished'] ?? '') ?: $page->date()),
    'dateModified' => date("c", strtotime($page->header()->article['dateModified'] ?? '') ?: $page->date()),
];
        if (isset($page->header()->article['description'])) {
            $microdata['article']['description'] = $page->header()->article['description'];
           }
           else {
             $microdata['article']['description'] = substr($content,0,140); 
           };

         if (isset($page->header()->article['author'])) {
            $microdata['article']['author'] = $page->header()->article['author'];
           };
           if (isset($page->header()->article['publisher_name'])) {
            $microdata['article']['publisher']['@type'] = 'Organization';
            $microdata['article']['publisher']['name'] = @$page->header()->article['publisher_name'];
           };
           if (isset($page->header()->article['publisher_logo_url'])) {
            $publisherlogourl = $page->header()->article['publisher_logo_url'];
            $imagedata = $this->seoGetimage($publisherlogourl);
            $microdata['article']['publisher']['logo']['@type'] = 'ImageObject';
            $microdata['article']['publisher']['logo']['url'] = $this->grav['uri']->base() . $imagedata['url'];
            $microdata['article']['publisher']['logo']['width'] =  $imagedata['width'];
            $microdata['article']['publisher']['logo']['height'] =  $imagedata['height'];
            
           };
           if (isset($page->header()->article['image_url'])) {
            $microdata['article']['image']['@type'] = 'ImageObject';
            $imageurl = $page->header()->article['image_url'];
            $imagedata = $this->seoGetimage($imageurl);
            $microdata['article']['image']['url'] = $this->grav['uri']->base() . $imagedata['url'];
            $microdata['article']['image']['width'] = $imagedata['width'];
            $microdata['article']['image']['height'] = $imagedata['height'];
          
            }
       }       
      };
      // Encode to json
     /*foreach ($microdata as $key => $value){
        if ($value === null){
           unset($microdata[$key]);
        }
    }*/
    // $microdata = array_map('array_filter', $microdata);
    $microdata = $this->cleanArray($microdata);
    $customjson = @$page->header()->add_json;
     foreach ($microdata as $key => $value){
        
        
        $jsonscript =   PHP_EOL . '<script type="application/ld+json">' . PHP_EOL . json_encode($microdata[$key], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT ) . PHP_EOL . '</script>';
        $outputjson = $outputjson . $jsonscript;
      }
    if(!empty($customjson)){
      foreach($customjson as $json){
        $buildjson = PHP_EOL . '<script type="application/ld+json">' . PHP_EOL . $json['custom_json'] . PHP_EOL . '</script>';
        $outputcustomjson = $outputcustomjson . $buildjson ;
      }
      $outputjson = $outputjson . $outputcustomjson;
    }
          
      
      $outputjson = '</script>' . $outputjson . '<script>';
      $this->grav['twig']->twig_vars['json'] = $outputjson;
      $this->grav['twig']->twig_vars['myvar'] = $outputjson;
      //Do not add to addInlineJs if there is no microdata
       if($outputjson != "</script><script>"){
      $assets->addInlineJs($outputjson, 100);
      }
     // return $outputjson;
    }

     
    


    /**
     * Extend page blueprints with SEO configuration options.
     *
     * @param Event $event
     */
    public function onBlueprintCreated(Event $event)
 {
     $newtype = $event['type'];
     if (0 === strpos($newtype, 'modular/')) {
        } else {
            $blueprint = $event['blueprint'];
        if ($blueprint->get('form/fields/tabs', null, '/')) {
            
            $blueprints = new Blueprints(__DIR__ . '/blueprints/');
            $extends = $blueprints->get($this->name);
            $blueprint->extend($extends, true);
        
        }
        }
        
    }


    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }
    
}
