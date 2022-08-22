<?php

namespace ChrisCollins\GeneralUtils\Test;

use ChrisCollins\GeneralUtils\Test\AbstractTestCase;
use ChrisCollins\GeneralUtils\Curl\CurlHandle;

/**
 * CurlHandleTest
 */
class CurlHandleTest extends AbstractTestCase
{
    /**
     * @var string Constant for an example URL.
     */
    const EXAMPLE_URL = 'http://example.com/';

    /**
     * @var string Constant for an example URL that does not exist.
     */
    const EXAMPLE_NON_EXISTANT_URL = 'http://hhhhhhhhhhsaaadsahdsaaaaaaahhhhhhzhhhhhaaaaa.com';

    /**
     * @var CurlHandle A CurlHandle instance.
     */
    protected $instance = null;

    /**
     * Set up.
     */
    public function setUp(): void
    {
        $this->instance = new CurlHandle();
    }

    public function testConstructorSetsUrlIfPresent(): void
    {
        $url = self::EXAMPLE_URL;

        $this->instance = new CurlHandle($url);

        $this->assertEquals($url, $this->instance->getOption(CURLOPT_URL));
    }

    public function testConstructorSetsUrlToNullIfNotPresent(): void
    {
        $this->instance = new CurlHandle();

        $this->assertNull($this->instance->getOption(CURLOPT_URL));
    }

    /**
     * testGettersReturnValuesSetBySetters
     *
     * @param string $propertyName The name of the property.
     * @param mixed $propertyValue The value of the property.
     *
     * @dataProvider getPropertyNamesAndTestValues
     */
    public function testGettersReturnValuesSetBySetters($propertyName, $propertyValue): void
    {
        $ucfirstPropertyName = ucfirst($propertyName);

        $setter = 'set' . $ucfirstPropertyName;
        $getter = 'get' . $ucfirstPropertyName;

        // Assert setters return the object.
        $object = $this->instance->$setter($propertyValue);
        $this->assertInstanceOf('ChrisCollins\GeneralUtils\Curl\CurlHandle', $object);
        $this->assertEquals($this->instance, $object);

        $this->assertEquals($propertyValue, $this->instance->$getter());
    }

    /**
     * Data provider to provide test values for each property of the object.
     *
     * @return array An array, each element an array containing a property name and a test value.
     */
    public static function getPropertyNamesAndTestValues()
    {
        return array(
            array('handle', 'test'),
            array('url', self::EXAMPLE_URL)
        );
    }

    public function testGetOptionRetrievesValueSetBySetOption(): void
    {
        $port = 443;
        $this->assertNull($this->instance->getOption(CURLOPT_PORT));

        $this->instance->setOption(CURLOPT_PORT, $port);
        $this->assertEquals($port, $this->instance->getOption(CURLOPT_PORT));

        $port = 123;
        $this->instance->setOption(CURLOPT_PORT, $port);
        $this->assertEquals($port, $this->instance->getOption(CURLOPT_PORT));
    }

    public function testGetOptionRetrievesValuesSetBySetOptions(): void
    {
        $port = 443;
        $timeout = 123;

        $this->assertNull($this->instance->getOption(CURLOPT_PORT));
        $this->assertNull($this->instance->getOption(CURLOPT_TIMEOUT));

        $this->instance->setOptions(
            array(
                CURLOPT_PORT => $port,
                CURLOPT_TIMEOUT => $timeout
            )
        );

        $this->assertEquals($port, $this->instance->getOption(CURLOPT_PORT));
        $this->assertEquals($timeout, $this->instance->getOption(CURLOPT_TIMEOUT));

        $port = 444;

        $this->instance->setOptions(
            array(
                CURLOPT_PORT => $port
            )
        );

        $this->assertEquals($port, $this->instance->getOption(CURLOPT_PORT));
        $this->assertEquals($timeout, $this->instance->getOption(CURLOPT_TIMEOUT));
    }

    public function testGetOptionsReturnsAllSetOptions(): void
    {
        $options = array(
            CURLOPT_URL => self::EXAMPLE_URL,
            CURLOPT_PORT => 443
        );

        $this->instance->replaceOptions($options);

        $this->assertEquals($options, $this->instance->getOptions());
    }

    public function testClearOptionsRemovesAnySetOptions(): void
    {
        $optionName = CURLOPT_URL;
        $optionValue = self::EXAMPLE_URL;

        $this->instance->setOption($optionName, $optionValue);

        $this->assertEquals($optionValue, $this->instance->getOption($optionName));

        $this->instance->clearOptions();

        $this->assertNull($this->instance->getOption($optionName));
    }

    public function testReplaceOptionsRemovesAnySetOptionsAndSetsNewOptions(): void
    {
        $optionName = CURLOPT_URL;
        $optionValue = self::EXAMPLE_URL;

        $this->instance->setOption($optionName, $optionValue);

        $this->assertEquals($optionValue, $this->instance->getOption($optionName));

        $newOptions = array(CURLOPT_PORT => 123);
        $this->instance->replaceOptions($newOptions);

        $this->assertNull($this->instance->getOption($optionName));
    }

    public function testGetErrorCodeReturnsNullIfNoErrorHasOccurred(): void
    {
        $this->assertNull($this->instance->getErrorCode());
    }

    public function testGetErrorCodeReturnsIntegerIfErrorHasOccurred(): void
    {
        $this->instance->setUrl(self::EXAMPLE_NON_EXISTANT_URL);
        $this->instance->execute();
        $this->assertNotNull($this->instance->getErrorCode());
    }

    public function testGetErrorMessageReturnsNullIfNoErrorHasOccurred(): void
    {
        $this->assertNull($this->instance->getErrorMessage());
    }

    public function testGetErrorMessageReturnsStringIfErrorHasOccurred(): void
    {
        $this->instance->setUrl(self::EXAMPLE_NON_EXISTANT_URL);
        $this->instance->execute();

        $this->assertNotNull($this->instance->getErrorMessage());
    }

    public function testGetInfoReturnsEmptyArrayIfHandleIsNotInitialised(): void
    {
        $this->assertEquals(array(), $this->instance->getInfo());
    }

    public function testGetInfoReturnsArrayIfRequestHasBeenMade(): void
    {
        $url = self::EXAMPLE_URL;

        $this->instance->setUrl($url);
        $this->instance->execute();
        $info = $this->instance->getInfo();

        $this->assertIsArray($info);
        $this->assertNotEmpty($info);
        $this->assertEquals($url, $info['url']);
        $this->assertEquals(200, $info['http_code']);
    }

    public function testExecuteReturnsExpectedContent(): void
    {
        $this->instance->setUrl(self::EXAMPLE_URL);
        $content = $this->instance->execute();

        $this->assertStringContainsString('Example Domain', $content);
    }
}
