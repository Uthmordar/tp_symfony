<?php
namespace AppBundle\Services;

use Goutte\Client;

class ParserTorrentServices{
    
    protected $data=[];
    protected $client;
    protected $baseUrl='http://kickass.to/movies/?field=seeders&sorder=desc';
    
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
        $crawler=$this->client->request('GET', $torrent['uri']);

        $crawler->filter('div.dataList>ul>li span')->eq(0)->each(function($node) use(&$data, $k){
            $data[$k]['title']=$node->text();
        });
        $crawler->filter('a.magnetlinkButton')->each(function($node) use(&$data, $k){
            $magnet=$node->attr('href');
            preg_match('/btih:(?<hash>\w*)&/', $magnet, $matches);
            $data[$k]['magnet']=$magnet;
            $data[$k]['hash']=$matches['hash'];
        });
        $crawler->filter('div.seedBlock>strong')->each(function($node) use(&$data, $k){
            $data[$k]['seeders']=$node->text();
        });
        $crawler->filter('div.leechBlock>strong')->each(function($node) use(&$data, $k){
            $data[$k]['leechers']=$node->text();
        });
        $crawler->filter('div.dataList>ul>li span')->eq(1)->each(function($node) use(&$data, $k){
            $data[$k]['quality']=$node->text();
        });
        $crawler->filter('div.dataList>ul>li a')->eq(1)->each(function($node) use(&$data, $k){
            $data[$k]['imdbId']=$node->text();
        });
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
            $data[$k]['votes']=$node->text();
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