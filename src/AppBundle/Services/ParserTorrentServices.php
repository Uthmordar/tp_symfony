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
        $this->getTorrentList($this->data);
        foreach($this->data as $k=>$torrent){
            $this->getTorrentPageData($this->data, $k, $torrent);
        }
        foreach($this->data as $k=>$torrent){
            if(empty($torrent['imdbId'])){
                continue;
            }
            $this->getTorrentImdbData($this->data, $k, $torrent);
        }
        return $this->data;
    }
    
    /**
     * get html from $baseUrl && get ancre uri from page
     * @param type $data
     * @return type
     */
    public function getTorrentList(&$data){
        $this->crawler=$this->client->request('GET', $this->baseUrl);
        
        $this->crawler->filter('div.torrentname>div.filmType>a.cellMainLink')->each(function($node) use(&$data, $crawler){
            $ancre=$node->text();
            $link=$this->crawler->selectLink($node->text());
            $qT=$this->setQualityType($ancre);
            $data[]=['ancre'=>$ancre, 'uri'=>$link->link()->getUri(), 'qualityType'=>$qT, 'genre'=>[]];
        });
        return $data;
    }
    
    /**
     * get data from torrent page
     * @param type $data
     * @param type $k current data table index
     * @param type $torrent
     */
    public function getTorrentPageData(&$data, $k, $torrent){
        $this->crawler=$this->client->request('GET', $torrent['uri']);
       
        foreach($this->torrentPageProvider as $pageProvider){
            $this->getFilterCrawlerText($pageProvider[0], $pageProvider[1], $data, $k, $pageProvider[2]);
        }
        if(!empty($data[$k]['magnet'])){
            preg_match('/btih:(?<hash>\w*)&/', $data[$k]['magnet'], $matches);
            $data[$k]['hash']=$matches['hash'];
        }
    }
    
    /**
     * get data from imdbb page
     * @param type $data
     * @param type $k
     * @param type $torrent
     */
    public function getTorrentImdbData(&$data, $k, $torrent){
        $this->crawler=$this->client->request('GET', 'http://www.imdb.com/title/tt'.$torrent['imdbId']);
        
        foreach($this->imdbProvider as $imdbProvider){
            $this->getFilterCrawlerText($imdbProvider[0], $imdbProvider[1], $data, $k, $imdbProvider[2]);
        }
    }
    
    /**
     * shortCut for simple filter crawler request with eq facultative param
     * @param type $selector
     * @param type $key
     * @param type $data
     * @param type $k
     * @param type $params
     */
    public function getFilterCrawlerText($selector, $key, &$data, $k, $params=[]){
        if(isset($params['eq'])){
            $eq=intval($params['eq']);
            $this->crawler->filter($selector)->eq($eq)->each(function($node) use(&$data, $k, $key, $params){
                $data[$k][$key]=(isset($params['get']))? $node->$params['get']['fn']($params['get']['param']) : $node->text();
            });
        }else if(isset($params['multiple']) && $params['multiple']){
            $this->crawler->filter($selector)->each(function($node) use(&$data, $k, $key, $params){
                $data[$k][$key][]=(isset($params['get']))? $node->$params['get']['fn']($params['get']['param']) : $node->text();
            });
        }else{
            $this->crawler->filter($selector)->each(function($node) use(&$data, $k, $key, $params){
                $data[$k][$key]=(isset($params['get']))? $node->$params['get']['fn']($params['get']['param']) : $node->text();
            });
        }
        if(isset($params['filter']) && is_callable($params['filter'])){
            $data[$k][$key]=$params['filter']($data[$k][$key]);
        }
    }
          
    public function setQualityType($name){
        preg_match('/(?<BluRay> brrip | bluray)|(?<HD>hdrip|HDTC)|(?<cam> cam )|(?<ts> ts )|(?<XviD>xvid|dvdrip)/i', $name, $matches);
        if($matches){
            foreach($matches as $q=>$match){
                if(!is_int($q)){
                    return $q;
                }
            }
        }
        return 'Unknow';
    }
}