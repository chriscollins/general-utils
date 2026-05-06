<?php

declare(strict_types=1);

namespace ChrisCollins\GeneralUtils\Json;

use ChrisCollins\GeneralUtils\Exception\JsonException;
use stdClass;

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
     * @throws JsonException Thrown if there was a problem.
     */
    public function decode(string $json, bool $associativeArray = false): array|stdClass
    {
        $decoded = json_decode($json, $associativeArray);

        $lastError = json_last_error();

        if ($lastError !== JSON_ERROR_NONE) {
            throw new JsonException($this->translateErrorMessage($lastError));
        }

        return $decoded;
    }

    /**
     * @throws JsonException Thrown if the there was an error encoding the value.
     */
    public function encode(mixed $value, int $optionsMask = 0, int $depth = 512): string
    {
        $encoded = json_encode($value, $optionsMask, $depth);

        $lastError = json_last_error();

        if ($lastError !== JSON_ERROR_NONE) {
            throw new JsonException($this->translateErrorMessage($lastError));
        }

        return $encoded;
    }

    private function translateErrorMessage(int $errorCode): string
    {
        return self::$errorMessages[$errorCode] ?? self::UNKNOWN_ERROR_MESSAGE;
    }
}
