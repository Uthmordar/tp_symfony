<?php
namespace AppBundle\Services;

use Goutte\Client;

class ParserTorrentServices{
    protected $data=[];
    protected $client;
    protected $baseUrl='http://kickass.to/movies/?field=seeders&sorder=desc';
    protected $crawler;
    
    /**
     * data provider selector for torrent page
     * @var type 
     */
    protected $torrentPageProvider=[
        ['a.magnetlinkButton', 'magnet', ['get'=>['fn'=>'attr', 'param'=>'href']]],
        ['div.dataList>ul>li span', 'title', ['eq'=>0]],
        ['div.seedBlock>strong', 'seeders', []],
        ['div.leechBlock>strong', 'leechers', []],
        ['div.dataList>ul>li span', 'quality', ['eq'=>1]],
        ['div.dataList>ul>li a', 'imdbId', ['eq'=>1]],
    ];
    
    /**
     * data provider selector for imdb page
     * @var type 
     */
    protected $imdbProvider=[
        ['h1.header>span.nobr>a', 'year', []],
        ['div[itemprop="director"] span[itemprop="name"]', 'director', []],
        ['#img_primary img[itemprop="image"]', 'image', ['eq'=>0, 'get'=>['fn'=>'attr', 'param'=>'src']]],
        ['span[itemprop="ratingValue"]', 'rating', []],
        ['span[itemprop="genre"]', 'genre', ['multiple'=>1]]
    ];
    
    public function __construct(){
        $this->client=new Client();
        $t=function($text){
            return str_replace(',', '', $text);
        };
        $this->imdbProvider[]=['span[itemprop="ratingCount"]', 'votes', ['filter'=>$t]];
    }
    
    /**
     * get data from url
     * @return type
     */
    public function getDataTorrent(){
        $this->getTorrentList();
        foreach($this->data as $k=>$torrent){
            $this->setRequestPageData($k, $this->torrentPageProvider, $torrent['uri'])->setHash($k);
        }
        foreach($this->data as $k=>&$torrent){
            if(empty($torrent['imdbId'])){
                unset($this->data[$k]);
                continue;
            }
            $this->setRequestPageData($k, $this->imdbProvider, 'http://www.imdb.com/title/tt'.$torrent['imdbId']);
        }
        unset($torrent);
        return $this->data;
    }
    
    /**
     * get html from $baseUrl && get ancre uri from page
     * @return type
     */
    public function getTorrentList(){
        $this->crawler=$this->client->request('GET', $this->baseUrl);

        $this->crawler->filter('div.torrentname>div.filmType>a.cellMainLink')->each(function($node){
            $ancre=$node->text();
            $link=$this->crawler->selectLink($node->text());
            $qT=$this->setQualityType($ancre);
            $this->data[]=['ancre'=>$ancre, 'uri'=>$link->link()->getUri(), 'qualityType'=>$qT, 'genre'=>[], 'votes'=>0, 'rating'=>0];
        });
    }
    
    /**
     * get data from request page
     * @param type index $k
     * @param array $provider
     * @param type uri $request
     */
    public function setRequestPageData($k, $provider, $request){
        $this->crawler=$this->client->request('GET', $request);
        foreach($provider as $pageParams){
            $this->getFilterCrawlerText($pageParams[0], $pageParams[1], $k, $pageParams[2]);
        }
        return $this;
    }
        
    /**
     * shortCut for simple filter crawler request with eq facultative param
     * @param type $selector
     * @param type $key
     * @param type $k
     * @param type $params
     */
    public function getFilterCrawlerText($selector, $key, $k, $params=[]){
        if(isset($params['eq'])){
            $eq=intval($params['eq']);
            $this->crawler->filter($selector)->eq($eq)->each(function($node) use($k, $key, $params){
                $this->data[$k][$key]=(isset($params['get']))? $node->$params['get']['fn']($params['get']['param']) : $node->text();
            });
        }else if(isset($params['multiple']) && $params['multiple']){
            $this->crawler->filter($selector)->each(function($node) use($k, $key, $params){
                $this->data[$k][$key][]=(isset($params['get']))? $node->$params['get']['fn']($params['get']['param']) : $node->text();
            });
        }else{
            $this->crawler->filter($selector)->each(function($node) use($k, $key, $params){
                $this->data[$k][$key]=(isset($params['get']))? $node->$params['get']['fn']($params['get']['param']) : $node->text();
            });
        }
        if(isset($params['filter']) && is_callable($params['filter']) && !empty($this->data[$k][$key])){
            $this->data[$k][$key]=$params['filter']($this->data[$k][$key]);
        }
    }
    
    /**
     * get magnet && set hash from it
     * @param type $k
     */
    public function setHash($k){
        if(!empty($this->data[$k]['magnet'])){
            preg_match('/btih:(?<hash>\w*)&/', $this->data[$k]['magnet'], $matches);
            $this->data[$k]['hash']=(!empty($matches['hash']))? $matches['hash'] : '';
        }
    }
          
    /**
     * extract quality type from torrent name
     * @param type $name
     * @return string
     */
    public function setQualityType($name){
        preg_match('/(?<BluRay> brrip | bluray)|(?<HD>hdrip|HDTC)|(?<cam> cam )|(?<ts> ts )|(?<XviD>xvid|dvdrip)/i', $name, $matches);
        if($matches){
            foreach($matches as $q=>$match){
                if(!is_int($q)){
                    return $q;
                }
            }
        }
        return 'Unknown';
    }
}