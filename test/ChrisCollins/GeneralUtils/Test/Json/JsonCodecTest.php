<?php

namespace ChrisCollins\GeneralUtils\Test;

use ChrisCollins\GeneralUtils\Json\JsonCodec;
use ChrisCollins\GeneralUtils\Exception\JsonException;
use ChrisCollins\GeneralUtils\Test\Fixture\GenericJsonFixture;
use \stdClass;

/**
 * JsonCodecTest
 */
class JsonCodecTest extends AbstractTestCase
{
    /**
     * @var JsonCodec A JsonCodec instance.
     */
    protected $instance = null;

    /**
     * @var GenericJsonFixture A GenericJsonFixture instance.
     */
    protected $genericJsonFixture = null;

    /**
     * Set up.
     */
    public function setUp()
    {
        $this->genericJsonFixture = new GenericJsonFixture();

        $this->instance = new JsonCodec();
    }

    // Decode.

    public function testDecodeReturnsExpectedObject()
    {
        $json = $this->genericJsonFixture->getJsonFromFile('valid.json');
        $actual = $this->instance->decode($json);

        $expected = $this->getValidObject();

        $this->assertEquals($expected, $actual);
    }

    public function testDecodeReturnsExpectedArrayWhenSecondParameterIsTrue()
    {
        $json = $this->genericJsonFixture->getJsonFromFile('valid.json');
        $actual = $this->instance->decode($json, true);

        $expected = $this->getValidArray();

        $this->assertEquals($expected, $actual);
    }

    public function testDecodeThrowsExceptionWhenJsonIsInvalid()
    {
        $exceptionThrown = false;

        try {
            $json = $this->genericJsonFixture->getJsonFromFile('invalid.json');
            $decoded = $this->instance->decode($json);
        } catch (JsonException $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    public function testDecodeThrowsExceptionWhenJsonContainsBadControlChar()
    {
        $exceptionThrown = false;

        $backspace = chr(8);

        try {
            $json = '{"x": "' . $backspace . '"}';
            $decoded = $this->instance->decode($json);
        } catch (JsonException $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    public function testDecodeThrowsExceptionWhenStateMismatchIsCaused()
    {
        $exceptionThrown = false;

        try {
            $json = '{"x":"y"}}';
            $decoded = $this->instance->decode($json);
        } catch (JsonException $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    public function testDecodeThrowsExceptionWhenMaxDepthIsExceeded()
    {
        $exceptionThrown = false;

        $repetitions = 513; // Max stack depth is 512.

        $json = '{' . str_repeat('"x":{', $repetitions) . '"a":"b"' . str_repeat('}', $repetitions) . '}';

        try {
            $decoded = $this->instance->decode($json);
        } catch (JsonException $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    // Encode.

    public function testEncodeOnObjectReturnsExpectedString()
    {
        $object = $this->getValidObject();
        $actual = $this->instance->encode($object);

        $expected = preg_replace('#\s#', '', $this->genericJsonFixture->getJsonFromFile('valid.json'));

        $this->assertEquals($expected, $actual);
    }

    public function testEncodeOnArrayReturnsExpectedString()
    {
        $object = $this->getValidArray();
        $actual = $this->instance->encode($object);

        $expected = preg_replace('#\s#', '', $this->genericJsonFixture->getJsonFromFile('valid.json'));

        $this->assertEquals($expected, $actual);
    }

    public function testEncodeThrowsExceptionWhenArrayIsInvalid()
    {
        $exceptionThrown = false;

        try {
            $array = $this->getValidArray();
            $array['a'] = mb_convert_encoding('é', 'UTF-16', 'UTF-8'); // JSON must be in UTF-8, UTF-16 should break it.
            $decoded = $this->instance->encode($array);
        } catch (JsonException $e) {
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
        return array(
            'x' => 'y',
            'y' => 1,
            'z' => true
        );
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
