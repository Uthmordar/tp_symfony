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
        ['div.dataList>ul>li span', 'title', ['eq'=>0]],
        ['div.seedBlock>strong', 'seeders', []],
        ['div.leechBlock>strong', 'leechers', []],
        ['div.dataList>ul>li span', 'quality', ['eq'=>1]],
        ['div.dataList>ul>li a', 'imdbId', ['eq'=>1]]
    ];
    
    public function __construct(){
        $this->client=new Client();
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
        $crawler=$this->client->request('GET', $this->baseUrl);
        
        $crawler->filter('div.torrentname>div.filmType>a.cellMainLink')->each(function($node) use(&$data, $crawler){
            $ancre=$node->text();
            $link=$crawler->selectLink($node->text());
            $qT=$this->setQualityType($ancre);
            $data[]=['ancre'=>$ancre, 'uri'=>$link->link()->getUri(), 'qualityType'=>$qT, 'genre'=>[]];
        });
        return $data;
    }
    
    /**
     * get data from torrent page
     * @param type $data
     * @param type $k
     * @param type $torrent
     */
    public function getTorrentPageData(&$data, $k, $torrent){
        $this->crawler=$this->client->request('GET', $torrent['uri']);

        $this->crawler->filter('a.magnetlinkButton')->each(function($node) use(&$data, $k){
            $magnet=$node->attr('href');
            preg_match('/btih:(?<hash>\w*)&/', $magnet, $matches);
            $data[$k]['magnet']=$magnet;
            $data[$k]['hash']=$matches['hash'];
        });
        
        foreach($this->torrentPageProvider as $pageProvider){
            $this->getFilterCrawlerText($pageProvider[0], $pageProvider[1], $data, $k, $pageProvider[2]);
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
            $this->crawler->filter($selector)->eq($eq)->each(function($node) use(&$data, $k, $key){
                $data[$k][$key]=$node->text();
            });
        }else{
            $this->crawler->filter($selector)->each(function($node) use(&$data, $k, $key){
                $data[$k][$key]=$node->text();
            });
        }
    }
    
    /**
     * get data from imdbb page
     * @param type $data
     * @param type $k
     * @param type $torrent
     */
    public function getTorrentImdbData(&$data, $k, $torrent){
        $crawler=$this->client->request('GET', 'http://www.imdb.com/title/tt'.$torrent['imdbId']);

        $crawler->filter('h1.header>span.nobr>a')->each(function($node) use(&$data, $k){
            $data[$k]['year']=$node->text();
        });
        $crawler->filter('div[itemprop="director"] span[itemprop="name"]')->each(function($node) use(&$data, $k){
            $data[$k]['director']=$node->text();
        });
        $crawler->filter('#img_primary img[itemprop="image"]')->eq(0)->each(function($node) use(&$data, $k){
            $data[$k]['image']=$node->attr('src');
        });
        $crawler->filter('span[itemprop="ratingValue"]')->each(function($node) use(&$data, $k){
            $data[$k]['rating']=$node->text();
        });
        $crawler->filter('span[itemprop="ratingCount"]')->each(function($node) use(&$data, $k){
            $data[$k]['votes']=str_replace(',', '', $node->text());
        });
        $crawler->filter('span[itemprop="genre"]')->each(function($node) use(&$data, $k){
            $data[$k]['genre'][]=$node->text();
        });
    }
    
    public function setQualityType($name){
        if(preg_match('/( brrip | bluray )/i', $name)){
            return 'BluRay';
        }else if(preg_match('/(hdrip)/i', $name)){
            return 'HD';
        }else if(preg_match('/( cam )/i', $name)){
            return 'cam';
        }else if(preg_match('/( ts )/i', $name)){
            return 'ts';
        }else if(preg_match('/( xvid )/i', $name)){
            return 'XviD';
        }else{
            return 'Unknow';
        }
    }
}