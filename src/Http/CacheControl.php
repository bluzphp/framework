<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Http;

use Bluz\Common\Container\Container;
use Bluz\Response\Response;
use DateTime;
use DateTimeZone;
use Exception;

/**
 * HTTP Cache Control
 *
 * Wrapper for working with HTTP headers
 *     - Cache-Control
 *     - Last-Modified
 *     - Expires
 *     - ETag
 *     - Age
 *
 * @package  Bluz\Http
 * @author   Anton Shevchuk
 * @link     http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html
 * @link     http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9
 */
class CacheControl
{
    use Container;

    /**
     * @var Response instance
     */
    protected $response;

    /**
     * Create instance
     *
     * @param Response $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * Prepare Cache-Control header
     *
     * @return void
     */
    protected function updateCacheControlHeader(): void
    {
        $parts = [];
        ksort($this->container);
        foreach ($this->container as $key => $value) {
            if (true === $value) {
                $parts[] = $key;
            } else {
                if (preg_match('#[^a-zA-Z0-9._-]#', (string)$value)) {
                    $value = '"' . $value . '"';
                }
                $parts[] = "$key=$value";
            }
        }
        if (count($parts)) {
            $this->response->setHeader('Cache-Control', implode(', ', $parts));
        }
    }

    /**
     * Marks the response as "private".
     *
     * It makes the response ineligible for serving other clients.
     *
     * @return void
     */
    public function setPrivate(): void
    {
        $this->doDeleteContainer('public');
        $this->doSetContainer('private', true);
        $this->updateCacheControlHeader();
    }

    /**
     * Marks the response as "public".
     *
     * It makes the response eligible for serving other clients.
     *
     * @return void
     */
    public function setPublic(): void
    {
        $this->doDeleteContainer('private');
        $this->doSetContainer('public', true);
        $this->updateCacheControlHeader();
    }

    /**
     * Returns the number of seconds after the time specified in the response's Date
     * header when the response should no longer be considered fresh.
     *
     * First, it checks for a s-maxage directive, then a max-age directive, and then it falls
     * back on an expires header. It returns null when no maximum age can be established.
     *
     * @return integer|null Number of seconds
     */
    public function getMaxAge(): ?int
    {
        if ($this->doContainsContainer('s-maxage')) {
            return (int)$this->doGetContainer('s-maxage');
        }

        if ($this->doContainsContainer('max-age')) {
            return (int)$this->doGetContainer('max-age');
        }

        if ($expires = $this->getExpires()) {
            $expires = DateTime::createFromFormat(DATE_RFC2822, $expires);
            return (int) $expires->format('U') - date('U');
        }

        return null;
    }

    /**
     * Sets the number of seconds after which the response should no longer be considered fresh.
     *
     * This methods sets the Cache-Control max-age directive.
     *
     * @param  integer $value Number of seconds
     *
     * @return void
     */
    public function setMaxAge($value): void
    {
        $this->doSetContainer('max-age', $value);
        $this->updateCacheControlHeader();
    }

    /**
     * Sets the number of seconds after which the response should no longer be considered fresh by shared caches.
     *
     * This methods sets the Cache-Control s-maxage directive.
     *
     * @param  integer $value Number of seconds
     *
     * @return void
     */
    public function setSharedMaxAge($value): void
    {
        $this->setPublic();
        $this->doSetContainer('s-maxage', $value);
        $this->updateCacheControlHeader();
    }

    /**
     * Returns the response's time-to-live in seconds.
     *
     * It returns null when no freshness information is present in the response.
     * When the responses TTL is <= 0, the response may not be served from cache without first
     * revalidating with the origin.
     *
     * @return integer|null The TTL in seconds
     */
    public function getTtl(): ?int
    {
        if ($maxAge = $this->getMaxAge()) {
            return $maxAge - $this->getAge();
        }
        return null;
    }

    /**
     * Sets the response's time-to-live for shared caches.
     *
     * This method adjusts the Cache-Control/s-maxage directive.
     *
     * @param  integer $seconds Number of seconds
     *
     * @return void
     */
    public function setTtl($seconds): void
    {
        $this->setSharedMaxAge($this->getAge() + $seconds);
    }

    /**
     * Sets the response's time-to-live for private/client caches.
     *
     * This method adjusts the Cache-Control/max-age directive.
     *
     * @param  integer $seconds Number of seconds
     *
     * @return void
     */
    public function setClientTtl($seconds): void
    {
        $this->setMaxAge($this->getAge() + $seconds);
    }

    /**
     * Returns the literal value of the ETag HTTP header
     *
     * @return string The ETag HTTP header or null if it does not exist
     */
    public function getEtag(): string
    {
        return $this->response->getHeader('ETag');
    }

    /**
     * Sets the ETag value
     *
     * @param  string $etag The ETag unique identifier
     * @param  bool   $weak Whether you want a weak ETag or not
     *
     * @return void
     */
    public function setEtag($etag, $weak = false): void
    {
        $etag = trim($etag, '"');
        $this->response->setHeader('ETag', (true === $weak ? 'W/' : '') . '"' . $etag . '"');
    }

    /**
     * Returns the age of the response
     *
     * @return integer The age of the response in seconds
     */
    public function getAge(): int
    {
        if ($age = $this->response->getHeader('Age')) {
            return (int)$age;
        }
        return max(time() - date('U'), 0);
    }

    /**
     * Set the age of the response
     *
     * @param  integer $age
     *
     * @return void
     */
    public function setAge($age): void
    {
        $this->response->setHeader('Age', $age);
    }

    /**
     * Returns the value of the Expires header as a DateTime instance
     *
     * @return string A string or null if the header does not exist
     */
    public function getExpires(): string
    {
        return $this->response->getHeader('Expires');
    }

    /**
     * Sets the Expires HTTP header with a DateTime instance
     *
     * @param DateTime|string $date A \DateTime instance or date as string
     *
     * @return void
     * @throws Exception
     */
    public function setExpires($date): void
    {
        if ($date instanceof DateTime) {
            $date = clone $date;
        } else {
            $date = new DateTime($date);
        }

        $date->setTimezone(new DateTimeZone('UTC'));
        $this->response->setHeader('Expires', $date->format('D, d M Y H:i:s') . ' GMT');
    }

    /**
     * Returns the Last-Modified HTTP header as a string
     *
     * @return string A string or null if the header does not exist
     */
    public function getLastModified(): string
    {
        return $this->response->getHeader('Last-Modified');
    }

    /**
     * Sets the Last-Modified HTTP header with a DateTime instance or string
     *
     * @param  DateTime|string $date A \DateTime instance or date as string
     *
     * @return void
     * @throws Exception
     */
    public function setLastModified($date): void
    {
        if ($date instanceof DateTime) {
            $date = clone $date;
        } else {
            $date = new DateTime($date);
        }

        $date->setTimezone(new DateTimeZone('UTC'));
        $this->response->setHeader('Last-Modified', $date->format('D, d M Y H:i:s') . ' GMT');
    }

    /**
     * Marks the response stale by setting the Age header to be equal to the maximum age of the response
     *
     * @return void
     */
    public function expire(): void
    {
        if ($this->getTtl() > 0) {
            $this->setAge($this->getMaxAge());
        }
    }
}
