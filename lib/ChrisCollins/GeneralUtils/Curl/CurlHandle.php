<?php

namespace ChrisCollins\GeneralUtils\Curl;

/**
 * CurlHandle
 *
 * A class to wrap cURL functionality in an object.
 */
class CurlHandle
{
    /**
     * @var Resource A cURL handle Resource.
     */
    private $handle;

    /**
     * @var array Associative array of options, as cURL does not allow us to retrieve options from the handle itself.
     */
    private array $options = [];

    /**
     * @var array Associative array of information about the last request.
     */
    private array $info = [];

    /**
     * @var int|null The error code for the last request.
     */
    private ?int $errorCode = null;

    /**
     * @var string|null The error message for the last request.
     */
    private ?string $errorMessage = null;

    /**
     * Constructor.
     *
     * @param string|null $url An optional URL to fetch.
     */
    public function __construct(?string $url = null)
    {
        $this->initialise($url);
    }

    /**
     * Initialise (or reinitialise) the object.
     *
     * @param ?string|null $url An optional URL to fetch.
     */
    public function initialise(?string $url = null)
    {
        $this->replaceOptions(
            [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true
            ]
        );

        $this->info = [];
    }

    /**
     * Execute the request and return the content.
     *
     * @return string The content at the URL.
     */
    public function execute(): string
    {
        $this->initialiseHandle();

        $this->applyOptionsToHandle();

        $content = curl_exec($this->handle);

        $this->info = $this->getInfoFromHandle();
        $this->errorCode = $this->getErrorCodeFromHandle();
        $this->errorMessage = $this->getErrorMessageFromHandle();

        $this->closeHandle();

        return $content;
    }

    /**
     * Initialise the handle.
     */
    private function initialiseHandle(): void
    {
        $this->handle = curl_init();
    }

    /**
     * Close the handle, freeing resources.
     */
    private function closeHandle(): void
    {
        curl_close($this->handle);
    }

    // Options.

    /**
     * Accessor method.
     *
     * @return array The value of the property.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Mutator method.
     *
     * @param array $options The new value of the property.
     *
     * @return static
     */
    public function replaceOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get the value of a previously set option.
     *
     * @param int $option The CURLOPT_{name} option to get.
     *
     * @return mixed The value of the option.
     */
    public function getOption(int $option)
    {
        return $this->options[$option] ?? null;
    }

    /**
     * Set a cURL option to be applied to the next request.
     *
     * @param int $option The CURLOPT_{name} option to set.
     * @param mixed $value The value for the option.
     *
     * @return static This object.
     */
    public function setOption(int $option, $value): self
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Set multiple cURL options to be applied to the next request.
     *
     * @param array $options An array of CURLOPT_{name} options.
     *
     * @return static This object.
     */
    public function setOptions(array $options): self
    {
        $this->options = array_replace($this->options, $options);

        return $this;
    }

    /**
     * Clear any options that have been set.
     */
    public function clearOptions(): void
    {
        $this->options = [];
    }

    /**
     * Apply the cached options to the handle.
     *
     * @return bool True if all options were set successfully, false if one of them failed.
     */
    private function applyOptionsToHandle(): bool
    {
        return curl_setopt_array($this->handle, $this->options);
    }

    /**
     * Convenience method to get the URL to fetch.
     *
     * @return string|null The URL, or null if it has not yet been set.
     */
    public function getUrl(): ?string
    {
        return $this->getOption(CURLOPT_URL);
    }

    /**
     * Convenience method to set the URL to fetch.
     *
     * @param string $url The URL.
     *
     * @return static This object.
     */
    public function setUrl($url): self
    {
        $this->setOption(CURLOPT_URL, $url);

        return $this;
    }

    /**
     * Accessor method.
     *
     * @return array The value of the property.
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * Retrieve information about the last request from the handle.
     *
     * @return array An array of information.
     */
    private function getInfoFromHandle(): array
    {
        return $this->handle === null ? [] : curl_getinfo($this->handle);
    }

    /**
     * Accessor method.
     *
     * @return int|null The value of the property.
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * Get the last error code that occurred, or null if there were no problems.
     *
     * @return int|null An integer representing the last error that occurred, or null if there were no problems.
     */
    public function getErrorCodeFromHandle(): ?int
    {
        $errorNumber = null;

        if ($this->handle !== null) {
            $errorNumber = curl_errno($this->handle);
        }

        return $errorNumber === 0 ? null : $errorNumber;
    }

    /**
     * Accessor method.
     *
     * @return string|null The value of the property.
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Get the message for the last error that occurred, or null if there were no problems.
     *
     * @return string|null An error message, or null if there was no error.
     */
    public function getErrorMessageFromHandle(): ?string
    {
        $errorMessage = null;

        if ($this->handle !== null) {
            $errorMessage = curl_error($this->handle);
        }

        return $errorMessage === '' ? null : $errorMessage;
    }

    /**
     * Accessor method.
     *
     * @return Resource The value of the property.
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Mutator method.
     *
     * @param Resource $handle The new value of the property.
     *
     * @return static This object.
     */
    public function setHandle($handle): self
    {
        $this->handle = $handle;

        return $this;
    }
}
