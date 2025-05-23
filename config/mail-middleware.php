<?php

use Psr\Log\LogLevel;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\AddGlobalBcc;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\AddGlobalCc;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\AddGlobalTo;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\BccFilter;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\CcFilter;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\EnsureRecipients;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\ToFilter;

return [
    /*
     * Enables the mail middleware package.
     */
    'enabled' => env('MAIL_MIDDLEWARE_PACKAGE_ENABLED', false),

    /*
     * ------------------------------------------------------------------------------
     * Configuration that applies to the message before it is sent.
     * ------------------------------------------------------------------------------
     */
    'sending' => [
        /*
         * Enables the mail middleware.
         */
        'middleware' => [
            'enabled' => env('MAIL_MIDDLEWARE_ENABLED', true),

            /*
             * Define the mail middleware every message should be passed through.
             * Can be either a class-string or an instance. Class-strings will
             * be instantiated through Laravel's service container.
             *
             * All middleware must implement the MailMiddlewareContract
             */
            'pipeline' => [
                ToFilter::class,
                CcFilter::class,
                BccFilter::class,
                AddGlobalTo::class,
                AddGlobalCc::class,
                AddGlobalBcc::class,
                EnsureRecipients::class,
            ],

            /*
             * Define the domains and email addresses that are allowed
             * to receive mails from your application.
             * All other recipients will be filtered out.
             */
            'allowed' => [

                /*
                 * Can either be a singular domain string,
                 * a semicolon separated list of domains or
                 * an array of domain strings.
                 *
                 * e.g.
                 * 'bar.com'
                 * 'foo.com;bar.com;...'
                 * ['foo.com', 'bar.com']
                 */
                'domains' => env('MAIL_MIDDLEWARE_ALLOWED_DOMAINS'),

                /*
                 * Can either be a singular email address string,
                 * a semicolon separated list of email addresses or
                 * an array of email address strings (only in config).
                 *
                 * e.g.
                 * 'foo@bar.com'
                 * 'foo@bar.com;bar@foo.com;...'
                 * ['foo.com', 'bar.com']
                 */
                'emails' => env('MAIL_MIDDLEWARE_ALLOWED_EMAILS'),
            ],

            /*
             * Define global recipients to be added to every mail sent.
             * Each one can either be a singular email address string,
             * a semicolon separated list of email addresses or
             * an array of email address strings (only in config)
             *
             * e.g.
             * 'foo@bar.com'
             * 'foo@bar.com;bar@foo.com;...'
             * ['foo.com', 'bar.com']
             */
            'global' => [
                'to' => env('MAIL_MIDDLEWARE_GLOBAL_TO'),
                'cc' => env('MAIL_MIDDLEWARE_GLOBAL_CC'),
                'bcc' => env('MAIL_MIDDLEWARE_GLOBAL_BCC'),
            ],
        ],

        /*
         * Configure the logging of filtered mails.
         */
        'log' => [
            /*
             * Enables the log.
             */
            'enabled' => env('MAIL_MIDDLEWARE_LOG_ENABLED', false),

            /*
             * Define a custom logging channel for your filtered message
             * logs. Leave empty (null) to default to Laravel's default
             * logging channel (config: logging.default). If this is
             * undefined, it will fall back to the 'stack' channel.
             */
            'channel' => env('MAIL_MIDDLEWARE_LOG_CHANNEL'),

            /*
             * Define the log level to log your filtered messages in.
             */
            'level' => env('MAIL_MIDDLEWARE_LOG_LEVEL', LogLevel::INFO),

            /*
             * Define, what parts of the message should be logged.
             */
            'include' => [
                /*
                 * Each middleware can add messages to the log through the
                 * message context that is passed through the pipeline.
                 */
                'middleware' => true,

                /*
                 * Log the final message headers.
                 */
                'headers' => true,

                /*
                 * Log the message data.
                 */
                'message_data' => false,

                /*
                 * Log the final message body.
                 */
                'body' => false,
            ],
        ],
    ],

    /*
     * ------------------------------------------------------------------------------
     * Configuration that applies to the message after it is sent.
     * ------------------------------------------------------------------------------
     */
    'sent' => [
        /*
         * Enables the mail sent middleware.
         */
        'middleware' => [
            'enabled' => env('MAIL_MIDDLEWARE_SENT_ENABLED', true),

            /*
             * Define the mail sent middleware every sent message should be passed
             * through. Can be either a class-string or an instance. Class-strings
             * will be instantiated through Laravel's service container.
             *
             * All middleware must implement the MailSentMiddlewareContract
             */
            'pipeline' => [
                //
            ],
        ],

        /*
         * Configure the logging of filtered mails.
         */
        'log' => [
            /*
             * Enables the logging of sent messages.
             * Defaults to sending log if set to null.
             */
            'enabled' => env('MAIL_MIDDLEWARE_SENT_LOG_ENABLED'),

            /*
             * Define a custom logging channel for your sent messages.
             * Defaults to sending log channel if set to null.
             */
            'channel' => env('MAIL_MIDDLEWARE_SENT_LOG_CHANNEL'),

            /*
             * Define the log level to log your sent messages.
             * Defaults to sending log level if set to null.
             */
            'level' => env('MAIL_MIDDLEWARE_SENT_LOG_LEVEL'),

            /*
             * Define, what parts of the sent message should be logged.
             */
            'include' => [
                /*
                 * Each middleware can add messages to the log through the
                 * message context that is passed through the pipeline.
                 */
                'middleware' => false,

                /*
                 * Log the sent message headers.
                 */
                'headers' => true,

                /*
                 * Log the sent message data.
                 */
                'message_data' => false,

                /*
                 * Log the sent message debug information.
                 */
                'debug' => false,

                /*
                 * Log the sent message body.
                 */
                'body' => false,
            ],
        ],
    ],
];
