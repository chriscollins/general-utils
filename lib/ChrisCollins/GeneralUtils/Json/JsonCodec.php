<?php

namespace ChrisCollins\GeneralUtils\Json;

use ChrisCollins\GeneralUtils\Exception\JsonException;

/**
 * JsonCodec
 *
 * A simple class for encoding and decoding JSON, wrapping PHP's built in JSON functionality.
 */
class JsonCodec
{
    /**
     * @var string Constant for "unknown error" message.
     */
    public const UNKNOWN_ERROR_MESSAGE = 'Unknown error.';

    /**
     * @var array Array of error messages, keyed on error code.
     */
    private static $errorMessages = [
        JSON_ERROR_DEPTH => 'Maximum stack depth exceeded.',
        JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch.',
        JSON_ERROR_CTRL_CHAR => 'Unexpected control character found.',
        JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON.',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded.'
    ];

    /**
     * Decode a JSON string to a PHP object/array.
     *
     * @param string $json The JSON.
     * @param bool $associativeArray If true, an associative array will be returned, otherwise a stdClass object.
     *
     * @throws JsonException Thrown if there was a problem.
     *
     * @return mixed An associative array or stdClass object.
     */
    public function decode(string $json, bool $associativeArray = false)
    {
        $decoded = json_decode($json, $associativeArray);

        $lastError = json_last_error();

        if ($lastError !== JSON_ERROR_NONE) {
            throw new JsonException($this->translateErrorMessage($lastError));
        }

        return $decoded;
    }

    /**
     * Encode a PHP value as a JSON string.
     *
     * @param mixed $value The value to encode.
     * @param int $optionsMask A bitmask of options (see PHP's json_encode function for acceptable values).
     * @param int $depth The maximum depth.
     *
     * @throws JsonException Thrown if the there was an error encoding the value.
     *
     * @return string A JSON string.
     */
    public function encode($value, int $optionsMask = 0, int $depth = 512): string
    {
        $encoded = json_encode($value, $optionsMask, $depth);

        $lastError = json_last_error();

        if ($lastError !== JSON_ERROR_NONE) {
            throw new JsonException($this->translateErrorMessage($lastError));
        }

        return $encoded;
    }

    /**
     * Translate a json_last_error() error code into a readable string.
     *
     * @param int $errorCode The error code.
     *
     * @return string A string error message.
     */
    private function translateErrorMessage(int $errorCode): string
    {
        return self::$errorMessages[$errorCode] ?? self::UNKNOWN_ERROR_MESSAGE;
    }
}
