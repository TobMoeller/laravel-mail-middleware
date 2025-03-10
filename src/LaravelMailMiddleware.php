<?php

namespace TobMoeller\LaravelMailMiddleware;

use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use Psr\Log\LogLevel;
use ReflectionClass;
use Throwable;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MailMiddlewareContract;

class LaravelMailMiddleware
{
    public function enabled(): bool
    {
        return (bool) Config::get('mail-middleware.enabled', false);
    }

    // --------------------------------------------------------------------------------
    // Middleware Configuration
    // --------------------------------------------------------------------------------

    public function mailMiddlewareEnabled(): bool
    {
        return (bool) Config::get('mail-middleware.sending.middleware.enabled', true);
    }

    /**
     * @return array<int, MailMiddlewareContract|class-string<MailMiddlewareContract>>
     */
    public function mailMiddleware(): array
    {
        $middleware = Config::get('mail-middleware.sending.middleware.pipeline');

        /** @phpstan-ignore return.type */
        return is_array($middleware) ? $middleware : [];
    }

    /**
     * @return array<int, string>
     */
    public function allowedDomainList(): array
    {
        $allowedDomains = Config::get('mail-middleware.sending.middleware.allowed.domains');

        return $this->extractArrayFromConfig($allowedDomains);
    }

    /**
     * @return array<int, string>
     */
    public function allowedEmailList(): array
    {
        $allowedEmails = Config::get('mail-middleware.sending.middleware.allowed.emails');

        return $this->extractArrayFromConfig($allowedEmails);
    }

    /**
     * @return array<int, string>
     */
    public function globalToEmailList(): array
    {
        $toEmails = Config::get('mail-middleware.sending.middleware.global.to');

        return $this->extractArrayFromConfig($toEmails);
    }

    /**
     * @return array<int, string>
     */
    public function globalCcEmailList(): array
    {
        $ccEmails = Config::get('mail-middleware.sending.middleware.global.cc');

        return $this->extractArrayFromConfig($ccEmails);
    }

    /**
     * @return array<int, string>
     */
    public function globalBccEmailList(): array
    {
        $bccEmails = Config::get('mail-middleware.sending.middleware.global.bcc');

        return $this->extractArrayFromConfig($bccEmails);
    }

    /**
     * Extracts the array from a config value that can be
     * either a semicolon separated string or an array
     *
     * @return array<int, string>
     */
    protected function extractArrayFromConfig(mixed $configValue): array
    {
        if (is_string($configValue)) {
            return explode(';', $configValue);
        }

        if (is_array($configValue)) {
            /** @phpstan-ignore return.type */
            return $configValue;
        }

        return [];
    }

    // --------------------------------------------------------------------------------
    // Sent Middleware Configuration
    // --------------------------------------------------------------------------------

    public function sentMailMiddlewareEnabled(): bool
    {
        return (bool) Config::get('mail-middleware.sent.middleware.enabled', true);
    }

    /**
     * @TODO change contract
     *
     * @return array<int, MailMiddlewareContract|class-string<MailMiddlewareContract>>
     */
    public function sentMailMiddleware(): array
    {
        $middleware = Config::get('mail-middleware.sent.middleware.pipeline');

        /** @phpstan-ignore return.type */
        return is_array($middleware) ? $middleware : [];
    }

    // --------------------------------------------------------------------------------
    // Log Configuration
    // --------------------------------------------------------------------------------

    public function logEnabled(): bool
    {
        return (bool) Config::get('mail-middleware.sending.log.enabled', false);
    }

    public function logChannel(): string
    {
        $channel = Config::get('mail-middleware.sending.log.channel');
        $channel = is_string($channel) ? $channel : Config::get('logging.default');

        return is_string($channel) ? $channel : 'stack';
    }

    protected function validateLogLevel(string $level): bool
    {
        $allowedLevels = array_values((new ReflectionClass(LogLevel::class))->getConstants());

        return in_array($level, $allowedLevels);
    }

    /**
     * @throws Throwable
     */
    public function logLevel(): string
    {
        $level = Config::get('mail-middleware.sending.log.level');

        throw_unless(
            is_string($level) &&
            $this->validateLogLevel($level = mb_strtolower($level)),
            InvalidArgumentException::class,
            'Invalid log level provided'
        );

        return $level;
    }

    public function logMiddleware(): bool
    {
        return (bool) Config::get('mail-middleware.sending.log.include.middleware');
    }

    public function logHeaders(): bool
    {
        return (bool) Config::get('mail-middleware.sending.log.include.headers');
    }

    public function logMessageData(): bool
    {
        return (bool) Config::get('mail-middleware.sending.log.include.message_data');
    }

    public function logBody(): bool
    {
        return (bool) Config::get('mail-middleware.sending.log.include.body');
    }

    // --------------------------------------------------------------------------------
    // Sent Log Configuration
    // --------------------------------------------------------------------------------

    public function sentLogEnabled(): bool
    {
        return (bool) (Config::get('mail-middleware.sent.log.enabled') ?? $this->logEnabled());
    }

    public function sentLogChannel(): string
    {
        $channel = Config::get('mail-middleware.sent.log.channel');

        return is_string($channel) ? $channel : $this->logChannel();
    }

    /**
     * @throws Throwable
     */
    public function sentLogLevel(): string
    {
        $level = Config::get('mail-middleware.sent.log.level');

        if ($level === null) {
            $level = $this->logLevel();
        } else {
            throw_unless(
                is_string($level) &&
                $this->validateLogLevel($level = mb_strtolower($level)),
                InvalidArgumentException::class,
                'Invalid log level provided'
            );
        }

        return $level;
    }

    public function sentLogMiddleware(): bool
    {
        return (bool) (Config::get('mail-middleware.sent.log.include.middleware') ?? $this->logMiddleware());
    }

    public function sentLogHeaders(): bool
    {
        return (bool) (Config::get('mail-middleware.sent.log.include.headers') ?? $this->logHeaders());
    }

    public function sentLogMessageData(): bool
    {
        return (bool) (Config::get('mail-middleware.sent.log.include.message_data') ?? $this->logMessageData());
    }

    public function sentLogDebugInformation(): bool
    {
        return (bool) Config::get('mail-middleware.sent.log.include.debug', false);
    }

    public function sentLogBody(): bool
    {
        return (bool) (Config::get('mail-middleware.sent.log.include.body') ?? $this->logBody());
    }
}
