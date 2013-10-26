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
    const UNKNOWN_ERROR_MESSAGE = 'Unknown error.';

    /**
     * @var array Array of error messages, keyed on error code.
     */
    protected static $errorMessages = array(
        JSON_ERROR_DEPTH => 'Maximum stack depth exceeded.',
        JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch.',
        JSON_ERROR_CTRL_CHAR => 'Unexpected control character found.',
        JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON.',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded.'
    );

    /**
     * Decode a JSON string to a PHP object/array.
     *
     * @param string $json The JSON.
     * @param boolean $associativeArray If true, an associative array will be returned, otherwise a stdClass object.
     *
     * @return mixed An associative array or stdClass object.  If there was a problem, null is returned.
     */
    public function decode($json, $associativeArray = false)
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
     *
     * @return string A JSON string.
     *
     * @todo Support the depth parameter when PHP 5.5 is more prevalent.
     */
    public function encode($value, $optionsMask = 0)
    {
        $encoded = json_encode($value, $optionsMask);

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
     * @return string|null A message, or null if there was no error.
     */
    protected function translateErrorMessage($errorCode)
    {
        $message = self::UNKNOWN_ERROR_MESSAGE;

        if (isset(self::$errorMessages[$errorCode])) {
            $message = self::$errorMessages[$errorCode];
        }

        return $message;
    }
}
