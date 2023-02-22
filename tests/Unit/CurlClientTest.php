<?php

namespace Onramplab\TwilioEnhancement\Tests\Unit;

use Mockery;
use Onramplab\TwilioEnhancement\CurlClient;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Twilio\Rest\Client;

/**
*  @author Onramplab
*/
class CurlClientTest extends TestCase
{
    /**
     * @test
     */
    public function should_log_when_passing_logger(): void
    {
        $loggerMock = Mockery::mock(LoggerInterface::class);
        $httpClient = new CurlClient([], $loggerMock);

        $loggerMock->shouldReceive('debug')->once();

        $response = $httpClient->request('get', 'https://api.twilio.com/2010-04-01/Accounts/$TWILIO_ACCOUNT_SID/Usage/Records/LastMonth.json?PageSize=20');

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function canBeMockedForRestClient(): void
    {
        $httpClientMock =  Mockery::mock(CurlClient::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $client = new Client('TEST_USERNAME', 'TEST_PASSWORD');
        $client->setHttpClient($httpClientMock);

        $jsonPath = __DIR__ . '/../../tests/Data/usage-records.json';
        $jsonContent = file_get_contents($jsonPath);
        $expectedResponseData = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);

        $httpClientMock->shouldReceive('getBody')->once()->andReturn(file_get_contents($jsonPath));
        $httpClientMock->shouldReceive('getStatusCode')->once()->andReturn(200);

        $response = $client->request('get', 'https://api.twilio.com/2010-04-01/Accounts/$TWILIO_ACCOUNT_SID/Usage/Records/LastMonth.json?PageSize=20');

        $responseData = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedResponseData['end'], $responseData['end']);
    }
}
