<?php declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Curl\Curl;

#[AsCommand(
    name: 'app:ping-zillow-api',
    description: 'A simple boilerplate Symfony command',
)]
class PingZillowApi extends Command
{
    private const BASE_URL = 'https://unofficial-zillow-api2.p.rapidapi.com';
    private const ACCESS_TOKEN = '3dd0b676b1msh51540137ae395fep119632jsnab92ba18c391';

    public function __construct(
        private readonly Curl $curl
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->curl->setHeader('x-rapidapi-key ', self::ACCESS_TOKEN);
        $this->curl->setHeader('x-rapidapi-host ', self::BASE_URL);
        $this->curl->setHeader('Content-Type', 'application/json');

        $this->curl->get(self::BASE_URL.'/property/all?zpid=91321078');

        $response = $this->curl->getResponse();

        $io->note(print_r($response, true));

        return Command::SUCCESS;
    }
}
