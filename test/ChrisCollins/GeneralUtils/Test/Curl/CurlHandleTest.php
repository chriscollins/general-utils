<?php

declare(strict_types=1);

namespace ChrisCollins\GeneralUtils\Test;

use ChrisCollins\GeneralUtils\Curl\CurlHandle;
use ChrisCollins\GeneralUtils\Test\AbstractTestCase;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * CurlHandleTest
 */
final class CurlHandleTest extends AbstractTestCase
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
    protected $instance;

    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->instance = new CurlHandle();
    }

    #[Test]
    public function constructorSetsUrlIfPresent(): void
    {
        $url = self::EXAMPLE_URL;

        $this->instance = new CurlHandle($url);

        $this->assertEquals($url, $this->instance->getOption(CURLOPT_URL));
    }

    #[Test]
    public function constructorSetsUrlToNullIfNotPresent(): void
    {
        $this->instance = new CurlHandle();

        $this->assertNull($this->instance->getOption(CURLOPT_URL));
    }

    #[Test]
    #[DataProvider('getPropertyNamesAndTestValues')]
    public function gettersReturnValuesSetBySetters(string $propertyName, mixed $propertyValue): void
    {
        $ucfirstPropertyName = ucfirst($propertyName);

        $setter = 'set' . $ucfirstPropertyName;
        $getter = 'get' . $ucfirstPropertyName;

        // Assert setters return the object.
        $object = $this->instance->$setter($propertyValue);
        $this->assertInstanceOf(CurlHandle::class, $object);
        $this->assertEquals($this->instance, $object);

        $this->assertEquals($propertyValue, $this->instance->$getter());
    }

    /**
     * Data provider to provide test values for each property of the object.
     *
     * @return Iterator<(int|string), mixed> An array, each element an array containing a property name and a test value.
     */
    public static function getPropertyNamesAndTestValues(): Iterator
    {
        yield ['handle', curl_init()];
        yield ['url', self::EXAMPLE_URL];
    }

    #[Test]
    public function getOptionRetrievesValueSetBySetOption(): void
    {
        $port = 443;
        $this->assertNull($this->instance->getOption(CURLOPT_PORT));

        $this->instance->setOption(CURLOPT_PORT, $port);
        $this->assertEquals($port, $this->instance->getOption(CURLOPT_PORT));

        $port = 123;
        $this->instance->setOption(CURLOPT_PORT, $port);
        $this->assertEquals($port, $this->instance->getOption(CURLOPT_PORT));
    }

    #[Test]
    public function getOptionRetrievesValuesSetBySetOptions(): void
    {
        $port = 443;
        $timeout = 123;

        $this->assertNull($this->instance->getOption(CURLOPT_PORT));
        $this->assertNull($this->instance->getOption(CURLOPT_TIMEOUT));

        $this->instance->setOptions(
            [
                CURLOPT_PORT => $port,
                CURLOPT_TIMEOUT => $timeout
            ]
        );

        $this->assertEquals($port, $this->instance->getOption(CURLOPT_PORT));
        $this->assertEquals($timeout, $this->instance->getOption(CURLOPT_TIMEOUT));

        $port = 444;

        $this->instance->setOptions(
            [
                CURLOPT_PORT => $port
            ]
        );

        $this->assertEquals($port, $this->instance->getOption(CURLOPT_PORT));
        $this->assertEquals($timeout, $this->instance->getOption(CURLOPT_TIMEOUT));
    }

    #[Test]
    public function getOptionsReturnsAllSetOptions(): void
    {
        $options = [
            CURLOPT_URL => self::EXAMPLE_URL,
            CURLOPT_PORT => 443
        ];

        $this->instance->replaceOptions($options);

        $this->assertEquals($options, $this->instance->getOptions());
    }

    #[Test]
    public function clearOptionsRemovesAnySetOptions(): void
    {
        $optionName = CURLOPT_URL;
        $optionValue = self::EXAMPLE_URL;

        $this->instance->setOption($optionName, $optionValue);

        $this->assertEquals($optionValue, $this->instance->getOption($optionName));

        $this->instance->clearOptions();

        $this->assertNull($this->instance->getOption($optionName));
    }

    #[Test]
    public function replaceOptionsRemovesAnySetOptionsAndSetsNewOptions(): void
    {
        $optionName = CURLOPT_URL;
        $optionValue = self::EXAMPLE_URL;

        $this->instance->setOption($optionName, $optionValue);

        $this->assertEquals($optionValue, $this->instance->getOption($optionName));

        $newOptions = [CURLOPT_PORT => 123];
        $this->instance->replaceOptions($newOptions);

        $this->assertNull($this->instance->getOption($optionName));
    }

    #[Test]
    public function getErrorCodeReturnsNullIfNoErrorHasOccurred(): void
    {
        $this->assertNull($this->instance->getErrorCode());
    }

    #[Test]
    public function getErrorCodeReturnsIntegerIfErrorHasOccurred(): void
    {
        $this->instance->setUrl(self::EXAMPLE_NON_EXISTANT_URL);
        $this->instance->execute();
        $this->assertNotNull($this->instance->getErrorCode());
    }

    #[Test]
    public function getErrorMessageReturnsNullIfNoErrorHasOccurred(): void
    {
        $this->assertNull($this->instance->getErrorMessage());
    }

    #[Test]
    public function getErrorMessageReturnsStringIfErrorHasOccurred(): void
    {
        $this->instance->setUrl(self::EXAMPLE_NON_EXISTANT_URL);
        $this->instance->execute();

        $this->assertNotNull($this->instance->getErrorMessage());
    }

    #[Test]
    public function getInfoReturnsEmptyArrayIfHandleIsNotInitialised(): void
    {
        $this->assertEquals([], $this->instance->getInfo());
    }

    #[Test]
    public function getInfoReturnsArrayIfRequestHasBeenMade(): void
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

    #[Test]
    public function executeReturnsExpectedContent(): void
    {
        $this->instance->setUrl(self::EXAMPLE_URL);
        $content = $this->instance->execute();

        $this->assertStringContainsString('Example Domain', (string) $content);
    }
}
