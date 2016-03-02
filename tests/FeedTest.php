<?php

namespace Spatie\Feed\Test;

use XMLReader;

class FeedTest extends TestCase
{
    protected $feedNames = ['feed1', 'feed2'];

    /** @test */
    public function it_registers_routes_where_feeds_will_be_available()
    {
        collect($this->feedNames)->each(function (string $feedName) {

            $this->assertEquals(200, $this->call('GET', "/{$feedName}")->getStatusCode());

        });
    }

    /** @test */
    public function a_feed_contains_xml_content()
    {
        collect($this->feedNames)->each(function (string $feedName) {

            $generatedFeedContent = $this->call('GET', "/{$feedName}")->getContent();

            $this->assertTrue($this->validateXml($generatedFeedContent));

        });
    }

    /** @test */
    public function all_feed_items_have_expected_data()
    {
        collect($this->feedNames)->each(function (string $feedName) {

            $stubContent = file_get_contents("tests/stubs/{$feedName}.xml");
            $generatedFeedContent = $this->call('GET', "/{$feedName}")->getContent();

            $this->assertEquals($stubContent, $generatedFeedContent);
        });
    }

    protected function validateXml(string $content) : bool
    {
        $file = 'tests/temp/validate.xml';

        file_put_contents($file, $content);

        $xmlReader = new XMLReader();
        $xmlReader->open($file);
        $xmlReader->setParserProperty($xmlReader::VALIDATE, true);

        return $xmlReader->isValid();
    }
}