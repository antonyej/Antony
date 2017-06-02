<?php namespace Scrape;

error_reporting(E_ALL);
ini_set('display_errors', 1);
 require './vendor/autoload.php';

/**
 * A basic web scraper class
 */

use \DOMXPath;
use \DOMDocument;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

/**
 * Class Scrape
 * @package Scrape
 */
class Scrape
{
    /**
     * @var Client
     */
    private $webClient;
    /**
     * @var DOMDocument
     */
    private $dom;

    /**
     * Init scraper to scrape $site
     * @param string $site Site to scrape
     * @param int $timeout seconds before request times out. 
     */
    public function __construct($site, $timeout = 2)
    {
        $this->webClient = new Client([
                'base_uri' => $site,
                'timeout' => $timeout
            ]);
    }

    /**
     * Load sub page to site.
     * E.g, '/' loads the site root page
     * @param string $page Page to load
     * @return $this
     */
    public function load($page) {

        try {
            $response = $this->webClient->get($page);
        } catch(ConnectException $e) {
            throw new \RuntimeException(
                    $e->getHandlerContext()['error']
                );
        }

        $html = $response->getBody();

        $this->dom = new DOMDocument;

        // Ignore errors caused by unsupported HTML5 tags
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($html);
        libxml_clear_errors();

        return $this;
    }

    /**
     * Get first nodes matching xpath query
     * below parent node in DOM tree
     * @param $xpath string selector to query the DOM
     * @param $parent \DOMNode to use as query root node
     * @return \DOMNode
     */
    public function getNode($xpath, $parent=null) {
        $nodes = $this->getNodes($xpath, $parent);

        if ($nodes->length === 0) {
            throw new \RuntimeException("No matching node found");
        }

        return $nodes[0];
    }

    /**
     * Get all nodes matching xpath query
     * below parent node in DOM tree
     * @param $xpath string selector to query the DOM
     * @param $parent \DOMNode to use as query root node
     * @return \DOMNodeList
     */
    public function getNodes($xpath, $parent=null) {
        $DomXpath = new DOMXPath($this->dom);
        $nodes = $DomXpath->query($xpath, $parent);
        return $nodes;
    }
}


$scraper = new Scrape('http://archive-grbj-2.s3-website-us-west-1.amazonaws.com/');
$scraper->load('/');

foreach($posts as $post) {
	$article[]['ArticleTitle']=$scraper->getNode('./h2[@class="headline"]/a', $post);
	$article[]['ArticleDate']=$scraper->getNode('./div[@class="date"]', $post);
  	$article[]['ArticleURL'] = $scraper->getNode('./h2[@class="headline"]/a/href', $post);
    $article[]['AuthorName'] = $scraper->getNode('./div[@class="auther"]', $post);
    $article[]['AuthorURL'] = $scraper->getNode('./div[@class="auther"]/a/href', $post);


}	
?>
