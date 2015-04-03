<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Run parser torrent services => get torrent data from given sites
 * Run register from parser services => register/update torrent data 
 */
class TestCommand extends ContainerAwareCommand{
    protected function configure(){
        $this->setName("torrent:parse:KickAss")->setDescription('Command to parse kick ass and get url');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $container=$this->getContainer();
        $parser=$container->get('parser_torrent_service');
        $register=$container->get('register_from_parser');

        $data=array_reverse($parser->getDataTorrent());

        if($register->registerDatas($data)){
            $output->writeln('<info>Torrents registered with success</info>');
        }else{
            $output->writeln('<error>an error has occured</error>');
        }
    }
}