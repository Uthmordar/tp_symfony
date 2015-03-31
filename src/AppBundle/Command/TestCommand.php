<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class TestCommand extends ContainerAwareCommand{
    protected function configure(){
        $this->setName("torrent:parse:KickAss")->setDescription('Command to parse kick ass and get url');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $container=$this->getContainer();
        $parser=$container->get('parser_torrent_service');
        
        $data=$parser->getDataTorrent();
         
        $register=$container->get('register_from_parser');
        if($register->registerDatas($data)){
            $output->writeln('<info>Torrents registered with success</info>');
        }else{
            $output->writeln('<error>an error has occured</error>');
        }
    }
}