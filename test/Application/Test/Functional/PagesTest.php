<?php

namespace Application\Test\Functional;

use Application\Test\WebTestCase;

class PagesTest extends WebTestCase
{
    /**
     * Checks if the required pages exist.
     *
     * @dataProvider urlExistingPagesProvider
     */
    public function testIfMainPagesExist($url)
    {
        $client = $this->createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isOk());
    }

    /**
     * @return array
     */
    public function urlExistingPagesProvider()
    {
        return [
            ['/application/'],
            ['/application/participate'],
            ['/members-area/login'],
            ['/members-area/reset-password'],
        ];
    }
}
