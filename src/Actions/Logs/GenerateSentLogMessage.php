<?php

namespace TobMoeller\LaravelMailMiddleware\Actions\Logs;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use TobMoeller\LaravelMailMiddleware\Facades\LaravelMailMiddleware;
use TobMoeller\LaravelMailMiddleware\MailSentMiddleware\SentMessageContext;

class GenerateSentLogMessage implements GenerateSentLogMessageContract
{
    public function generate(SentMessageContext $messageContext): string
    {
        $isEmail = ($message = $messageContext->getMessage()) instanceof Email;
        $logMessage = 'LaravelMailMiddleware.MessageSent:';

        if ($className = $messageContext->getOriginatingClassName()) {
            $logMessage .= PHP_EOL.'ClassName: '.$className;
        }

        if (LaravelMailMiddleware::sentLogMiddleware()) {
            $logMessage .= $this->generateMiddlewareMessage($messageContext);
        }

        if ($isEmail && LaravelMailMiddleware::sentLogHeaders()) {
            $logMessage .= $this->generateHeadersMessage($message);
        }

        if (LaravelMailMiddleware::sentLogMessageData()) {
            $logMessage .= $this->generateMessageDataMessage($messageContext);
        }

        if (LaravelMailMiddleware::sentLogDebugInformation()) {
            $logMessage .= $this->generateDebugMessage($messageContext);
        }

        if ($isEmail && LaravelMailMiddleware::sentLogBody()) {
            $logMessage .= $this->generateBodyMessage($message);
        }

        // Handle RawMessage
        if (! $isEmail &&
            LaravelMailMiddleware::sentLogHeaders() &&
            LaravelMailMiddleware::sentLogBody()
        ) {
            $logMessage .= $this->generateRawMessageMessage($message);
        } elseif (! $isEmail &&
            (
                LaravelMailMiddleware::sentLogHeaders() ||
                LaravelMailMiddleware::sentLogBody()
            )
        ) {
            $logMessage .= PHP_EOL.__('RawMessages can only be logged including headers and body');
        }

        return $logMessage;
    }

    protected function generateMiddlewareMessage(SentMessageContext $messageContext): string
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

    protected function generateHeadersMessage(Email $message): string
    {
        return <<<LOG_HEADERS

        ----------
        HEADERS
        ----------
        {$message->getHeaders()->toString()}
        LOG_HEADERS;
    }

    protected function generateMessageDataMessage(SentMessageContext $messageContext): string
    {
        $data = json_encode($messageContext->getMessageData()) ?: '';

        return <<<LOG_MESSAGE_DATA

        ----------
        DATA
        ----------
        {$data}
        LOG_MESSAGE_DATA;
    }

    protected function generateBodyMessage(Email $message): string
    {
        return <<<LOG_BODY

        ----------
        BODY
        ----------
        {$message->getBody()->toString()}
        LOG_BODY;
    }

    protected function generateRawMessageMessage(RawMessage $message): string
    {
        return <<<LOG_RAW_MESSAGE

        ----------
        RAW
        ----------
        {$message->toString()}
        LOG_RAW_MESSAGE;
    }

    protected function generateDebugMessage(SentMessageContext $messageContext): string
    {
        return <<<LOG_DEBUG_INFORMATION

        ----------
        DEBUG
        ----------
        {$messageContext->getDebugInformation()}
        LOG_DEBUG_INFORMATION;
    }
}
