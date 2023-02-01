<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Http;

/**
 * Hypertext Transfer Protocol (HTTP) Status Code Registry
 *
 * @package  Bluz\Http
 */
enum StatusCode: int
{
    case CONTINUE = 100;
    case SWITCHING_PROTOCOLS = 101;
    case PROCESSING = 102;            // RFC2518
    case EARLY_HINTS = 103;           // RFC8297
    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NON_AUTHORITATIVE_INFORMATION = 203;
    case NO_CONTENT = 204;
    case RESET_CONTENT = 205;
    case PARTIAL_CONTENT = 206;
    case MULTI_STATUS = 207;          // RFC4918
    case ALREADY_REPORTED = 208;      // RFC5842
    case IM_USED = 226;               // RFC3229
    case MULTIPLE_CHOICES = 300;
    case MOVED_PERMANENTLY = 301;
    case FOUND = 302;
    case SEE_OTHER = 303;
    case NOT_MODIFIED = 304;
    case USE_PROXY = 305;
    case SWITCH_PROXY = 306;
    case TEMPORARY_REDIRECT = 307;
    case PERMANENTLY_REDIRECT = 308;  // RFC7238
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case PAYMENT_REQUIRED = 402;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case NOT_ACCEPTABLE = 406;
    case PROXY_AUTHENTICATION_REQUIRED = 407;
    case REQUEST_TIMEOUT = 408;
    case CONFLICT = 409;
    case GONE = 410;
    case LENGTH_REQUIRED = 411;
    case PRECONDITION_FAILED = 412;
    case REQUEST_ENTITY_TOO_LARGE = 413;
    case REQUEST_URI_TOO_LONG = 414;
    case UNSUPPORTED_MEDIA_TYPE = 415;
    case REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    case EXPECTATION_FAILED = 417;
    case I_AM_A_TEAPOT = 418;                                               // RFC2324
    case MISDIRECTED_REQUEST = 421;                                         // RFC7540
    case UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    case LOCKED = 423;                                                      // RFC4918
    case FAILED_DEPENDENCY = 424;                                           // RFC4918
    case TOO_EARLY = 425;                                                   // RFC8740
    case UPGRADE_REQUIRED = 426;                                            // RFC2817
    case PRECONDITION_REQUIRED = 428;                                       // RFC6585
    case TOO_MANY_REQUESTS = 429;                                           // RFC6585
    case REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    case UNAVAILABLE_FOR_LEGAL_REASONS = 451;                               // RFC7725
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;
    case BAD_GATEWAY = 502;
    case SERVICE_UNAVAILABLE = 503;
    case GATEWAY_TIMEOUT = 504;
    case VERSION_NOT_SUPPORTED = 505;
    case VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    case INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    case LOOP_DETECTED = 508;                                               // RFC5842
    case NOT_EXTENDED = 510;                                                // RFC2774
    case NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585

    /**
     * Status codes translation table
     *
     * The list of codes is complete according to the
     * {@link http://www.iana.org/assignments/http-status-codes/ Hypertext Transfer Protocol Status Code Registry}
     */
    public function message(): string
    {
        return match ($this) {
            self::CONTINUE => _('Continue'),
            self::SWITCHING_PROTOCOLS => _('Switching Protocols'),
            self::PROCESSING => _('Processing'),
            self::EARLY_HINTS => _('Early Hints'),
            self::OK => _('OK'),
            self::CREATED => _('Created'),
            self::ACCEPTED => _('Accepted'),
            self::NON_AUTHORITATIVE_INFORMATION => _('Non-Authoritative Information'),
            self::NO_CONTENT => _('No Content'),
            self::RESET_CONTENT => _('Reset Content'),
            self::PARTIAL_CONTENT => _('Partial Content'),
            self::MULTI_STATUS => _('Multi-Status'),
            self::ALREADY_REPORTED => _('Already Reported'),
            self::IM_USED => _('IM Used'),
            self::MULTIPLE_CHOICES => _('Multiple Choices'),
            self::MOVED_PERMANENTLY => _('Moved Permanently'),
            self::FOUND => _('Found'),
            self::SEE_OTHER => _('See Other'),
            self::NOT_MODIFIED => _('Not Modified'),
            self::USE_PROXY => _('Use Proxy'),
            self::SWITCH_PROXY => _('Switch Proxy is a reserved and unused status code'),
            self::TEMPORARY_REDIRECT => _('Temporary Redirect'),
            self::PERMANENTLY_REDIRECT => _('Permanent Redirect'),
            self::BAD_REQUEST => _('Bad Request'),
            self::UNAUTHORIZED => _('Unauthorized'),
            self::PAYMENT_REQUIRED => _('Payment Required'),
            self::FORBIDDEN => _('Forbidden'),
            self::NOT_FOUND => _('Not Found'),
            self::METHOD_NOT_ALLOWED => _('Method Not Allowed'),
            self::NOT_ACCEPTABLE => _('Not Acceptable'),
            self::PROXY_AUTHENTICATION_REQUIRED => _('Proxy Authentication Required'),
            self::REQUEST_TIMEOUT => _('Request Timeout'),
            self::CONFLICT => _('Conflict'),
            self::GONE => _('Gone'),
            self::LENGTH_REQUIRED => _('Length Required'),
            self::PRECONDITION_FAILED => _('Precondition Failed'),
            self::REQUEST_ENTITY_TOO_LARGE => _('Payload Too Large'),
            self::REQUEST_URI_TOO_LONG => _('URI Too Long'),
            self::UNSUPPORTED_MEDIA_TYPE => _('Unsupported Media Type'),
            self::REQUESTED_RANGE_NOT_SATISFIABLE => _('Range Not Satisfiable'),
            self::EXPECTATION_FAILED => _('Expectation Failed'),
            self::I_AM_A_TEAPOT => _('I\'m a teapot'),
            self::MISDIRECTED_REQUEST => _('Misdirected Request'),
            self::UNPROCESSABLE_ENTITY => _('Unprocessable Entity'),
            self::LOCKED => _('Locked'),
            self::FAILED_DEPENDENCY => _('Failed Dependency'),
            self::TOO_EARLY => _('Too Early'),
            self::UPGRADE_REQUIRED => _('Upgrade Required'),
            self::PRECONDITION_REQUIRED => _('Precondition Required'),
            self::TOO_MANY_REQUESTS => _('Too Many Requests'),
            self::REQUEST_HEADER_FIELDS_TOO_LARGE => _('Request Header Fields Too Large'),
            self::UNAVAILABLE_FOR_LEGAL_REASONS => _('Unavailable For Legal Reasons'),
            self::INTERNAL_SERVER_ERROR => _('Internal Server Error'),
            self::NOT_IMPLEMENTED => _('Not Implemented'),
            self::BAD_GATEWAY => _('Bad Gateway'),
            self::SERVICE_UNAVAILABLE => _('Service Unavailable'),
            self::GATEWAY_TIMEOUT => _('Gateway Timeout'),
            self::VERSION_NOT_SUPPORTED => _('HTTP Version Not Supported'),
            self::VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL => _('Variant Also Negotiates (Experimental)'),
            self::INSUFFICIENT_STORAGE => _('Insufficient Storage'),
            self::LOOP_DETECTED => _('Loop Detected'),
            self::NOT_EXTENDED => _('Not Extended'),
            self::NETWORK_AUTHENTICATION_REQUIRED => _('Network Authentication Required'),
        };
    }
}
