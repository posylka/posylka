<?php


namespace app\core\router;

use app\core\enums\HttpStatus;
use app\core\Json;
use InvalidArgumentException;

class Response
{
    private array $headers;
    private array $content;
    private int $statusCode;
    private string $statusText;
    private string $charset = 'UTF-8';
    private bool $isSuccess = true;
    private string $message = '';
    private array $errors = [];
    private string $errorCode = '';

    public function __construct($content = [], int $status = 200, array $headers = [])
    {
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setHeaders($headers);
    }

    /**
     * Sends HTTP headers and content.
     */
    public function send(): void
    {
        header(sprintf('HTTP/1.1 %s %s', $this->getStatusCode(), $this->getStatusText()));
        header('Content-Type: application/json; charset=' . $this->charset);
        foreach ($this->headers as $sKey => $sValue) {
            header(sprintf('%s: %s', $sKey, $sValue));
        }
        echo Json::encode([
            'success' => $this->getIsSuccess(),
            'message' => $this->getMessage(),
            'status_code' => $this->getStatusCode(),
            'data' => $this->getContent(),
            'errors' => $this->getErrors(),
            'error_code' => $this->getErrorCode(),
        ]);
        if (\function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif (\function_exists('litespeed_finish_request')) {
            litespeed_finish_request();
        } elseif (!\in_array(\PHP_SAPI, ['cli', 'phpdbg'], true)) {
            static::closeOutputBuffers(0, true);
        }
    }

    /**
     * Sets the response content.
     */
    public function setContent(array $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setError(string $field, array $messages): self
    {
        $this->errors[$field] = $messages;

        return $this;
    }

    public function setErrors(array $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Gets the current response content.
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * Sets the response headers.
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Gets the current response headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Sets the response isSuccess.
     */
    public function setIsSuccess(bool $isSuccess): self
    {
        $this->isSuccess = $isSuccess;

        return $this;
    }

    /**
     * Gets the current response isSuccess.
     */
    public function getIsSuccess(): bool
    {
        return $this->isSuccess;
    }

    /**
     * Sets the response message.
     */
    public function setMessage($message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Gets the current response message.
     */
    public function getMessage(): string
    {
        return $this->message ?? ($this->getIsSuccess() ? __('messages.success') : __('errors.error')) . '!';
    }

    /**
     * Sets the response status code.
     * If the status text is null it will be automatically populated for the known
     * status codes and left empty otherwise.
     * @throws InvalidArgumentException When the HTTP status code is not valid
     */
    final public function setStatusCode(int $code, string $text = null): self
    {
        $this->statusCode = $code;
        if ($this->isInvalid()) {
            throw new InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $code));
        }
        if (null === $text) {
            $this->statusText = HttpStatus::tryFrom($code)->text();
        } else {
            $this->statusText = $text;
        }

        return $this;
    }

    /**
     * Retrieves the status code for the current web response.
     */
    final public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Retrieves the status code for the current web response.
     */
    final public function getStatusText(): string
    {
        return $this->statusText ?? HttpStatus::tryFrom($this->statusCode)->text();
    }

    /**
     * Cleans or flushes output buffers up to target level.
     * Resulting level can be greater than target level if a non-removable buffer has been encountered.
     */
    private static function closeOutputBuffers(int $targetLevel, bool $flush): void
    {
        $status = ob_get_status(true);
        $level = \count($status);
        $flags = \PHP_OUTPUT_HANDLER_REMOVABLE | ($flush ? \PHP_OUTPUT_HANDLER_FLUSHABLE : \PHP_OUTPUT_HANDLER_CLEANABLE);

        while (
            $level-- > $targetLevel
            && ($s = $status[$level])
            && (!isset($s['del']) ? !isset($s['flags']) || ($s['flags'] & $flags) === $flags : $s['del'])
        ) {
            if ($flush) {
                ob_end_flush();
            } else {
                ob_end_clean();
            }
        }
    }

    /**
     * Is response invalid?
     */
    final public function isInvalid(): bool
    {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function setErrorCode(string $errorCode): self
    {
        $this->errorCode = $errorCode;

        return $this;
    }
}
