<?php declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Command\Command;
use Curl\Curl;

/**
 * Lets you retrieve FRM (fair market rent) and IL (income level) for any state/county.
 */
#[AsCommand(
    name: 'app:ping-hud-api',
    description: 'A simple boilerplate Symfony command',
)]
class PingHudApi extends Command
{
    private const ACCESS_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI2IiwianRpIjoiMWQxZDJiZDAwYjg4MjgzMTFlODEwYmIwMTk3MTc3YjA5N2IyOWUxNmZlMzM2MDhlZGJlZTI4NTU2OTk2NWE5ZTNkY2I4ZTlhYTM1MjY1M2IiLCJpYXQiOjE3Nzc2NzQ0MDMuMjA0Mzg1LCJuYmYiOjE3Nzc2NzQ0MDMuMjA0Mzg4LCJleHAiOjIwOTMyOTM2MDMuMTk5NDY1LCJzdWIiOiIxMjczMTIiLCJzY29wZXMiOltdfQ.lage8Wr8jfSKtVVrAO-NYr4lYcTMVnEvCMUS3Xsebw7hYARKKPQR5zqE-zAJMi8j7KGzwyRGl-eopdS7af8nlg';
    private const USERNAME = 'apmusholt@gmail.com';
    private const PASSWORD = 'hudapi-Us91k.NXZ&A3xNI<h|X';
    private const BASE_URL = 'https://www.huduser.gov/hudapi/public/fmr';

    private const IL_STATE_NUM = '17.0';
    private const ADAMS_COUNTY_FIPS = '1700199999';

    private const LIST_STATES = self::BASE_URL.'/listStates';
    private const LIST_COUNTIES = self::BASE_URL.'/listCounties';
    private const ONE_COUNTY = self::BASE_URL.'/data';

    public function __construct(
        private readonly Curl $curl
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->curl->setHeader('Authorization', 'Bearer '.self::ACCESS_TOKEN);

        /**
         * Get all counties
         */
        //$this->curl->get(self::LIST_COUNTIES.'/IL');

        /**
         * Get FMR for a county.
         */
        $this->curl->get(self::ONE_COUNTY.'/'.self::ADAMS_COUNTY_FIPS);
        $response = $this->curl->getResponse();

        $io->note(print_r($response, true));

        return Command::SUCCESS;
    }
}
