<?php

declare(strict_types=1);

namespace ChrisCollins\GeneralUtils\Curl;

use CurlHandle as BaseCurlHandle;

/**
 * CurlHandle
 *
 * A class to wrap cURL functionality in an object.
 */
class CurlHandle
{
    private ?BaseCurlHandle $handle = null;

    /**
     * @var array<string,mixed>
     */
    private array $options = [];

    /**
     * @var array<string,mixed>
     */
    private array $info = [];

    private ?int $errorCode = null;

    private ?string $errorMessage = null;

    public function __construct(?string $url = null)
    {
        $this->initialise($url);
    }

    public function initialise(?string $url = null): void
    {
        $this->replaceOptions(
            [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true
            ]
        );

        $this->info = [];
    }

    public function execute(): ?string
    {
        $this->initialiseHandle();

        $this->applyOptionsToHandle();

        $content = curl_exec($this->handle);

        $this->info = $this->getInfoFromHandle();
        $this->errorCode = $this->getErrorCodeFromHandle();
        $this->errorMessage = $this->getErrorMessageFromHandle();

        $this->closeHandle();

        return $content ?: null;
    }

    private function initialiseHandle(): void
    {
        $this->handle = curl_init();
    }

    private function closeHandle(): void
    {
    }

    // Options.

    /**
     * @return array<string,mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array<string,mixed> $options
     */
    public function replaceOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getOption(int $option): mixed
    {
        return $this->options[$option] ?? null;
    }

    public function setOption(int $option, mixed $value): self
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * @param array<string,mixed> $options An array of CURLOPT_{name} options.
     */
    public function setOptions(array $options): self
    {
        $this->options = array_replace($this->options, $options);

        return $this;
    }

    public function clearOptions(): void
    {
        $this->options = [];
    }

    private function applyOptionsToHandle(): bool
    {
        return curl_setopt_array($this->handle, $this->options);
    }

    public function getUrl(): ?string
    {
        return $this->getOption(CURLOPT_URL);
    }

    public function setUrl(string $url): self
    {
        $this->setOption(CURLOPT_URL, $url);

        return $this;
    }

    /**
     * @return array<string,mixed>
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * @return array<string,mixed>
     */
    private function getInfoFromHandle(): array
    {
        return $this->handle instanceof BaseCurlHandle ? curl_getinfo($this->handle) : [];
    }

    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    public function getErrorCodeFromHandle(): ?int
    {
        $errorNumber = null;

        if ($this->handle instanceof BaseCurlHandle) {
            $errorNumber = curl_errno($this->handle);
        }

        return $errorNumber === 0 ? null : $errorNumber;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getErrorMessageFromHandle(): ?string
    {
        $errorMessage = null;

        if ($this->handle instanceof BaseCurlHandle) {
            $errorMessage = curl_error($this->handle);
        }

        return $errorMessage === '' ? null : $errorMessage;
    }

    public function getHandle(): BaseCurlHandle
    {
        return $this->handle;
    }

    public function setHandle(BaseCurlHandle $handle): self
    {
        $this->handle = $handle;

        return $this;
    }
}
