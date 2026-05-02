<?php declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Command\Command;
use Curl\Curl;

/**
 * Unofficial Zillow API
 * https://rapidapi.com/azabdurapidapi/api/unofficial-zillow-api2
 */
#[AsCommand(
    name: 'app:ping-zillow-api',
    description: 'A simple boilerplate Symfony command',
)]
class PingZillowApi extends Command
{
    private const string BASE_URL = 'https://unofficial-zillow-api2.p.rapidapi.com';
    private const string HOST_HEADER = 'unofficial-zillow-api2.p.rapidapi.com';
    private const string ACCESS_TOKEN = '3dd0b676b1msh51540137ae395fep119632jsnab92ba18c391';

    private const float LONGITUDE_12TH_STREET = -91.3999;

    public function __construct(
        private readonly Curl $curl
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->curl->setHeader('x-rapidapi-key', self::ACCESS_TOKEN);
        $this->curl->setHeader('x-rapidapi-host', self::HOST_HEADER);
        $this->curl->setHeader('Content-Type', 'application/json');

        /**
         * Get all addresses
         */
        $this->curl->post(self::BASE_URL.'/search/address', [
            'location' => 'Quincy, IL',
            // 'status' => 'for_sale'
        ]);

        $response = $this->curl->getResponse();
        $listings = $response->listings;

        $buyBoxListings = [];
        foreach ($listings as $listing) {

            $longitude = floatval($listing->longitude);
            $price = floatval($listing->price);
            $homeType = $listing->homeType;

            $isEastOf12thStreet = $longitude > self::LONGITUDE_12TH_STREET;
            $hasPrice = $price > 0;
            $isUnder150k = $price < 150000;
            $isOver30k = $price > 30000;
            $isNotLot = $homeType !== 'LOT';
            $isNotManufactured = $homeType !== 'MANUFACTURED';

            $meetsCriteria = $isEastOf12thStreet && $hasPrice && $isUnder150k && $isOver30k && $isNotLot && $isNotManufactured;

            if ($meetsCriteria) {
                $buyBoxListings[] = $listing;
            }
        }


        foreach($buyBoxListings as $listing) {

            $io->section("Listing: ".$listing->address);
            $io->writeln("Long: ".$listing->longitude);
            $io->success("Price: ".$listing->price);
            $io->writeln("Detail URL: ".$listing->detailUrl);
            $io->writeln("Home Type: ".$listing->homeType);
            $io->writeln("Home Status: ".$listing->homeStatus);
        }

        return Command::SUCCESS;
    }
}
