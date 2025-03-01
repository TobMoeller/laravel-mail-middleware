<?php

namespace TobMoeller\LaravelMailMiddleware\Actions\Logs;

use TobMoeller\LaravelMailMiddleware\Facades\LaravelMailMiddleware;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MessageContext;

class GenerateLogMessage implements GenerateLogMessageContract
{
    public function generate(MessageContext $messageContext): string
    {
        $logMessage = 'LaravelMailMiddleware.MessageSending:';

        if ($className = $messageContext->getOriginatingClassName()) {
            $logMessage .= PHP_EOL.'ClassName: '.$className;
        }

        if (! $messageContext->shouldSendMessage()) {
            $logMessage .= PHP_EOL.'Message was canceled by Middleware!';
        }

        if (LaravelMailMiddleware::logMiddleware()) {
            $logMessage .= $this->generateMiddlewareMessage($messageContext);
        }

        if (LaravelMailMiddleware::logHeaders()) {
            $logMessage .= $this->generateHeadersMessage($messageContext);
        }

        if (LaravelMailMiddleware::logMessageData()) {
            $logMessage .= $this->generateMessageDataMessage($messageContext);
        }

        if (LaravelMailMiddleware::logBody()) {
            $logMessage .= $this->generateBodyMessage($messageContext);
        }

        return $logMessage;
    }

    protected function generateMiddlewareMessage(MessageContext $messageContext): string
    {
        $logMessage = <<<'LOG_MIDDLEWARE'

        ----------
        MIDDLEWARE
        ----------
        LOG_MIDDLEWARE;

        foreach ($messageContext->getLog() as $logEntry) {
            $logMessage .= PHP_EOL.$logEntry;
        }

        return $logMessage;
    }

    protected function generateHeadersMessage(MessageContext $messageContext): string
    {
        return <<<LOG_HEADERS

        ----------
        HEADERS
        ----------
        {$messageContext->getMessage()->getHeaders()->toString()}
        LOG_HEADERS;
    }

    protected function generateMessageDataMessage(MessageContext $messageContext): string
    {
        $data = json_encode($messageContext->getMessageData()) ?: '';

        return <<<LOG_MESSAGE_DATA

        ----------
        DATA
        ----------
        {$data}
        LOG_MESSAGE_DATA;
    }

    protected function generateBodyMessage(MessageContext $messageContext): string
    {
        return <<<LOG_BODY

        ----------
        BODY
        ----------
        {$messageContext->getMessage()->getBody()->toString()}
        LOG_BODY;
    }
}
