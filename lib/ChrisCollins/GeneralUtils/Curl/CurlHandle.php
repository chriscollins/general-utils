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
    protected $handle = null;

    /**
     * @var array Associative array of options, as cURL does not allow us to retrieve options from the handle itself.
     */
    protected $options = array();

    /**
     * @var array Associative array of information about the last request.
     */
    protected $info = array();

    /**
     * @var int|null The error code for the last request.
     */
    protected $errorCode = null;

    /**
     * @var int|null The error message for the last request.
     */
    protected $errorMessage = null;

    /**
     * Constructor.
     *
     * @param string|null $url An optional URL to fetch.
     */
    public function __construct($url = null)
    {
        $this->initialise($url);
    }

    /**
     * Initialise (or reinitialise) the object.
     *
     * @param string|null $url An optional URL to fetch.
     */
    public function initialise($url = null)
    {
        $this->replaceOptions(
            array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true
            )
        );

        $this->info = array();
    }

    /**
     * Execute the request and return the content.
     *
     * @return string The content at the URL.
     */
    public function execute()
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
    protected function initialiseHandle()
    {
        $this->handle = curl_init();
    }

    /**
     * Close the handle, freeing resources.
     */
    protected function closeHandle()
    {
        curl_close($this->handle);
    }

    // Options.

    /**
     * Accessor method.
     *
     * @return array The value of the property.
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Mutator method.
     *
     * @param array $options The new value of the property.
     */
    public function replaceOptions(array $options)
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
    public function getOption($option)
    {
        return isset($this->options[$option]) ? $this->options[$option] : null;
    }

    /**
     * Set a cURL option to be applied to the next request.
     *
     * @param int $option The CURLOPT_{name} option to set.
     * @param mixed $value The value for the option.
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * Set multiple cURL options to be applied to the next request.
     *
     * @param array $options An array of CURLOPT_{name} options.
     */
    public function setOptions(array $options)
    {
        $this->options = array_replace($this->options, $options);
    }

    /**
     * Clear any options that have been set.
     */
    public function clearOptions()
    {
        $this->options = array();
    }

    /**
     * Apply the cached options to the handle.
     *
     * @return boolean True if all options were set successfully, false if one of them failed.
     */
    protected function applyOptionsToHandle()
    {
        return curl_setopt_array($this->handle, $this->options);
    }

    /**
     * Convenience method to get the URL to fetch.
     *
     * @return string|null The URL, or null if it has not yet been set.
     */
    public function getUrl()
    {
        return $this->getOption(CURLOPT_URL);
    }

    /**
     * Convenience method to set the URL to fetch.
     *
     * @param string $url The URL.
     *
     * @return CurlHandle This object.
     */
    public function setUrl($url)
    {
        $this->setOption(CURLOPT_URL, $url);

        return $this;
    }

    /**
     * Accessor method.
     *
     * @return array The value of the property.
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Retrieve information about the last request from the handle.
     *
     * @return array An array of information.
     */
    protected function getInfoFromHandle()
    {
        return $this->handle === null ? array() : curl_getinfo($this->handle);
    }

    /**
     * Accessor method.
     *
     * @return array The value of the property.
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Get the last error code that occurred, or null if there were no problems.
     *
     * @return int|null An integer representing the last error that occurred, or null if there were no problems.
     */
    public function getErrorCodeFromHandle()
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
     * @return array The value of the property.
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Get the message for the last error that occurred, or null if there were no problems.
     *
     * @return string|null An error message, or null if there was no error.
     */
    public function getErrorMessageFromHandle()
    {
        $errorMessage = null;

        if ($this->handle !== null) {
            $errorMessage = curl_errno($this->handle);
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
     * @return CurlHandle This object.
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;

        return $this;
    }
}
