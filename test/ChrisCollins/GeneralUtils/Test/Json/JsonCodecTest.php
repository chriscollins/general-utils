<?php

declare(strict_types=1);

namespace ChrisCollins\GeneralUtils\Test\Json;

use PHPUnit\Framework\Attributes\Test;
use ChrisCollins\GeneralUtils\Test\AbstractTestCase;
use ChrisCollins\GeneralUtils\Json\JsonCodec;
use ChrisCollins\GeneralUtils\Exception\JsonException;
use ChrisCollins\GeneralUtils\Test\Fixture\GenericJsonFixture;
use stdClass;

/**
 * JsonCodecTest
 */
final class JsonCodecTest extends AbstractTestCase
{
    /**
     * @var JsonCodec A JsonCodec instance.
     */
    protected $instance;

    /**
     * @var GenericJsonFixture A GenericJsonFixture instance.
     */
    protected $genericJsonFixture;

    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->genericJsonFixture = new GenericJsonFixture();

        $this->instance = new JsonCodec();
    }

    // Decode.

    #[Test]
    public function decodeReturnsExpectedObject(): void
    {
        $json = $this->genericJsonFixture->getJsonFromFile('valid.json');
        $actual = $this->instance->decode($json);

        $expected = $this->getValidObject();

        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function decodeReturnsExpectedArrayWhenSecondParameterIsTrue(): void
    {
        $json = $this->genericJsonFixture->getJsonFromFile('valid.json');
        $actual = $this->instance->decode($json, true);

        $expected = $this->getValidArray();

        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function decodeThrowsExceptionWhenJsonIsInvalid(): void
    {
        $exceptionThrown = false;

        try {
            $json = $this->genericJsonFixture->getJsonFromFile('invalid.json');
            $decoded = $this->instance->decode($json);
        } catch (JsonException) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    #[Test]
    public function decodeThrowsExceptionWhenJsonContainsBadControlChar(): void
    {
        $exceptionThrown = false;

        $backspace = chr(8);

        try {
            $json = '{"x": "' . $backspace . '"}';
            $decoded = $this->instance->decode($json);
        } catch (JsonException) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    #[Test]
    public function decodeThrowsExceptionWhenStateMismatchIsCaused(): void
    {
        $exceptionThrown = false;

        try {
            $json = '{"x":"y"}}';
            $decoded = $this->instance->decode($json);
        } catch (JsonException) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    #[Test]
    public function decodeThrowsExceptionWhenMaxDepthIsExceeded(): void
    {
        $exceptionThrown = false;

        $repetitions = 513; // Max stack depth is 512.

        $json = '{' . str_repeat('"x":{', $repetitions) . '"a":"b"' . str_repeat('}', $repetitions) . '}';

        try {
            $decoded = $this->instance->decode($json);
        } catch (JsonException) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    // Encode.

    #[Test]
    public function encodeOnObjectReturnsExpectedString(): void
    {
        $object = $this->getValidObject();
        $actual = $this->instance->encode($object);

        $expected = preg_replace('#\s#', '', $this->genericJsonFixture->getJsonFromFile('valid.json'));

        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function encodeOnArrayReturnsExpectedString(): void
    {
        $object = $this->getValidArray();
        $actual = $this->instance->encode($object);

        $expected = preg_replace('#\s#', '', $this->genericJsonFixture->getJsonFromFile('valid.json'));

        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function encodeThrowsExceptionWhenArrayIsInvalid(): void
    {
        $exceptionThrown = false;

        try {
            $array = $this->getValidArray();
            $array['a'] = mb_convert_encoding('é', 'UTF-16', 'UTF-8'); // JSON must be in UTF-8, UTF-16 should break it.
            $decoded = $this->instance->encode($array);
        } catch (JsonException) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    /**
     * Get an array representation of the valid JSON.
     *
     * @return array An associative array representing valid.json.
     */
    protected function getValidArray()
    {
        return [
            'x' => 'y',
            'y' => 1,
            'z' => true
        ];
    }

    /**
     * Get an object representation of the valid JSON.
     *
     * @return array An associative array representing valid.json.
     */
    protected function getValidObject()
    {
        $object = new stdClass();
        $object->x = 'y';
        $object->y = 1;
        $object->z = true;

        return $object;
    }
}
