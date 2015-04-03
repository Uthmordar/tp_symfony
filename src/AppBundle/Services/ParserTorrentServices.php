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
        'magnet'    =>['a.magnetlinkButton', ['get'=>['fn'=>'attr', 'param'=>'href']]],
        'title'     =>['div.dataList>ul>li span', ['eq'=>0]],
        'seeders'   =>['div.seedBlock>strong', []],
        'leechers'  =>['div.leechBlock>strong', []],
        'quality'   =>['div.dataList>ul>li span', ['eq'=>1]],
        'imdbId'    =>['div.dataList>ul>li a', ['eq'=>1]],
    ];

    /**
     * data provider selector for imdb page
     * @var type 
     */
    protected $imdbProvider=[
        'year'      =>['h1.header>span.nobr>a', []],
        'director'  =>['div[itemprop="director"] span[itemprop="name"]', []],
        'image'     =>['#img_primary img[itemprop="image"]', ['eq'=>0, 'get'=>['fn'=>'attr', 'param'=>'src']]],
        'rating'    =>['span[itemprop="ratingValue"]', []],
        'genre'     =>['span[itemprop="genre"]', ['multiple'=>1]]
    ];

    public function __construct(){
        $this->client=new Client();
        $t=function($text){
            return str_replace(',', '', $text);
        };
        $this->imdbProvider['votes']=['span[itemprop="ratingCount"]', ['filter'=>$t]];
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
            $link=$this->crawler->selectLink($ancre);
            $qT=$this->getQualityType($ancre);
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
        foreach($provider as $key=>$pageParams){
            $this->getFilterCrawlerText($pageParams[0], $key, $k, $pageParams[1]);
        }
        return $this;
    }

    /**
     * shortCut for simple filter crawler request with eq facultative param
     * @param type $selector
     * @param type $key
     * @param type $k
     * @param type $params ['get'=>['fn'=>'attr', 'param'=>'src'],  'multiple'=>true, 'filter'=>Closure]
     */
    public function getFilterCrawlerText($selector, $key, $k, $params=[]){
        $filter=$this->crawler->filter($selector);
        if(isset($params['eq'])){
            $filter=$filter->eq(intval($params['eq']));
        }
        $filter->each(function($node) use($k, $key, $params){
            $result=(isset($params['get']))? $node->$params['get']['fn']($params['get']['param']) : $node->text();
            if(isset($params['filter']) && is_callable($params['filter'])){
                $result=$params['filter']($result);
            }
            if(isset($params['multiple']) && $params['multiple']){
               $this->data[$k][$key][]=$result;
            }else{
               $this->data[$k][$key]=$result;
            }
        });
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
    public function getQualityType($name){
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