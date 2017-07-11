<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Response;

use Bluz\Application\Exception\NotAcceptableException;
use Bluz\Cli\CliResponse;
use Bluz\Common\Options;
use Bluz\Controller\Controller;
use Bluz\Http\StatusCode;
use Bluz\Layout\Layout;
use Bluz\Proxy\Messages;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Response\SapiEmitter;

/**
 * Response Container
 *
 * @package  Bluz\Response
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Response
 */
class Response
{
    use Options;

    /**
     * @var string HTTP protocol version
     */
    protected $protocol = '1.1';

    /**
     * @var integer response code equal to HTTP status codes
     */
    protected $code = StatusCode::OK;

    /**
     * @var string|null HTTP Phrase
     */
    protected $phrase;

    /**
     * @var array list of headers
     */
    protected $headers = [];

    /**
     * @var array list of cookies
     */
    protected $cookies = [];

    /**
     * @var Controller
     */
    protected $body;

    /**
     * @var string CLI|HTML|JSON|FILE
     */
    protected $type = 'HTML';

    /**
     * send
     *
     * @throws NotAcceptableException
     * @throws \InvalidArgumentException
     */
    public function send()
    {
        $body = $this->getBody();

        $this->sendCookies();

        switch (true) {
            case 'CLI' === $this->type:
                // no CLI response
                return;
            case is_null($body):
            case StatusCode::NO_CONTENT === $this->getStatusCode():
                $response = new EmptyResponse($this->getStatusCode(), $this->getHeaders());
                break;
            case StatusCode::MOVED_PERMANENTLY === $this->getStatusCode():
            case StatusCode::FOUND === $this->getStatusCode():
                $response = new RedirectResponse(
                    $this->getHeader('Location'),
                    $this->getStatusCode(),
                    $this->getHeaders()
                );
                break;
            case 'JSON' === $this->type:
                // JSON response
                // setup messages
                if (Messages::count()) {
                    $this->setHeader('Bluz-Notify', json_encode(Messages::popAll()));
                }

                // encode body data to JSON
                $response = new JsonResponse(
                    $body,
                    $this->getStatusCode(),
                    $this->getHeaders()
                );
                break;
            case 'FILE' === $this->type:
                // File attachment
                $response = new AttachmentResponse(
                    $this->body->getData()->get('FILE'),
                    $this->getStatusCode(),
                    $this->getHeaders()
                );
                break;
            case 'HTML' === $this->type:
            default:
                // HTML response
                $response = new HtmlResponse(
                    (string)$body,
                    $this->getStatusCode(),
                    $this->getHeaders()
                );
                break;
        }

        $emitter = new SapiEmitter();
        $emitter->emit($response);
    }

    /**
     * Set Response Type, one of JSON, HTML or CLI
     *
     * @param $type
     */
    public function switchType($type)
    {
        // switch statement by content type
        switch ($type) {
            case 'JSON':
                $this->setHeader('Content-Type', 'application/json');
                break;
            case 'CLI':
            case 'FILE':
            case 'HTML':
            default:
                break;
        }

        $this->type = $type;
    }

    /**
     * Gets the HTTP protocol version as a string
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion()
    {
        return $this->protocol;
    }

    /**
     * Gets the response Status-Code
     *
     * The Status-Code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return integer status code.
     */
    public function getStatusCode()
    {
        return $this->code;
    }

    /**
     * Sets the status code of this response
     *
     * @param  integer $code the 3-digit integer result code to set.
     *
     * @return void
     */
    public function setStatusCode($code)
    {
        $this->code = (int)$code;
    }

    /**
     * Gets the response Reason-Phrase, a short textual description of the Status-Code
     *
     * Because a Reason-Phrase is not a required element in response
     * Status-Line, the Reason-Phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 2616 recommended reason phrase for the
     * response's Status-Code.
     *
     * @return string|null reason phrase, or null if unknown.
     */
    public function getReasonPhrase()
    {
        return $this->phrase;
    }

    /**
     * Sets the Reason-Phrase of the response
     *
     * If no Reason-Phrase is specified, implementations MAY choose to default
     * to the RFC 2616 recommended reason phrase for the response's Status-Code.
     *
     * @param string $phrase the Reason-Phrase to set.
     */
    public function setReasonPhrase($phrase)
    {
        $this->phrase = $phrase;
    }

    /**
     * Retrieve a header by the given case-insensitive name as a string
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * @param  string $header case-insensitive header name.
     *
     * @return string
     */
    public function getHeader($header)
    {
        if ($this->hasHeader($header)) {
            return implode(', ', $this->headers[$header]);
        }
        return '';
    }

    /**
     * Retrieves a header by the given case-insensitive name as an array of strings
     *
     * @param  string $header Case-insensitive header name.
     *
     * @return string[]
     */
    public function getHeaderAsArray($header)
    {
        if ($this->hasHeader($header)) {
            return $this->headers[$header];
        }
        return [];
    }

    /**
     * Checks if a header exists by the given case-insensitive name
     *
     * @param  string $header case-insensitive header name.
     *
     * @return bool returns true if any header names match the given header
     *              name using a case-insensitive string comparison. Returns false if
     *              no matching header name is found in the message.
     */
    public function hasHeader($header)
    {
        return isset($this->headers[$header]);
    }

    /**
     * Sets a header, replacing any existing values of any headers with the
     * same case-insensitive name
     *
     * The header name is case-insensitive. The header values MUST be a string
     * or an array of strings.
     *
     * @param  string          $header header name
     * @param  string|string[] $value  header value(s)
     *
     * @return void
     */
    public function setHeader($header, $value)
    {
        $this->headers[$header] = (array)$value;
    }

    /**
     * Appends a header value for the specified header
     *
     * Existing values for the specified header will be maintained. The new
     * value will be appended to the existing list.
     *
     * @param  string $header header name to add
     * @param  string $value  value of the header
     *
     * @return void
     */
    public function addHeader($header, $value)
    {
        if ($this->hasHeader($header)) {
            $this->headers[$header][] = $value;
        } else {
            $this->setHeader($header, $value);
        }
    }

    /**
     * Remove a specific header by case-insensitive name.
     *
     * @param  string $header HTTP header to remove
     *
     * @return void
     */
    public function removeHeader($header)
    {
        unset($this->headers[$header]);
    }

    /**
     * Gets all message headers
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     * @return array returns an associative array of the message's headers.
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Sets headers, replacing any headers that have already been set on the message
     *
     * The array keys MUST be a string. The array values must be either a
     * string or an array of strings.
     *
     * @param  array $headers Headers to set.
     *
     * @return void
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Merges in an associative array of headers.
     *
     * Each array key MUST be a string representing the case-insensitive name
     * of a header. Each value MUST be either a string or an array of strings.
     * For each value, the value is appended to any existing header of the same
     * name, or, if a header does not already exist by the given name, then the
     * header is added.
     *
     * @param  array $headers Associative array of headers to add to the message
     *
     * @return void
     */
    public function addHeaders(array $headers)
    {
        $this->headers = array_merge_recursive($this->headers, $headers);
    }

    /**
     * Remove all headers
     *
     * @return void
     */
    public function removeHeaders()
    {
        $this->headers = [];
    }

    /**
     * Set response body
     *
     * @param  mixed $body
     *
     * @return void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get response body
     *
     * @return Controller|Layout
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Clear response body
     *
     * @return void
     */
    public function clearBody()
    {
        $this->body = null;
    }

    /**
     * Set Cookie
     *
     * @param  string               $name
     * @param  string               $value
     * @param  int|string|\DateTime $expire
     * @param  string               $path
     * @param  string               $domain
     * @param  bool                 $secure
     * @param  bool                 $httpOnly
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    public function setCookie(
        $name,
        $value = '',
        $expire = 0,
        $path = '/',
        $domain = '',
        $secure = false,
        $httpOnly = false
    ) {
        // from PHP source code
        if (preg_match("/[=,; \t\r\n\013\014]/", $name)) {
            throw new \InvalidArgumentException('The cookie name contains invalid characters.');
        }

        if (empty($name)) {
            throw new \InvalidArgumentException('The cookie name cannot be empty.');
        }

        // convert expiration time to a Unix timestamp
        if ($expire instanceof \DateTime) {
            $expire = $expire->format('U');
        } elseif (!is_numeric($expire)) {
            $expire = strtotime($expire);
            if (false === $expire || -1 === $expire) {
                throw new \InvalidArgumentException('The cookie expiration time is not valid.');
            }
        }

        $this->cookies[$name] = [
            'name' => $name,
            'value' => $value,
            'expire' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => (bool)$secure,
            'httpOnly' => (bool)$httpOnly
        ];
    }

    /**
     * Get Cookie by name
     *
     * @param  string $name
     *
     * @return array|null
     */
    public function getCookie($name)
    {
        return $this->cookies[$name] ?? null;
    }

    /**
     * Process Cookies to Header
     *
     *   Set-Cookie: <name>=<value>[; <name>=<value>]...
     *   [; expires=<date>][; domain=<domain_name>]
     *   [; path=<some_path>][; secure][; httponly]
     *
     * @return void
     */
    protected function sendCookies()
    {
        foreach ($this->cookies as $cookie) {
            setcookie(...array_values($cookie));
        }
    }
}
