<?php

namespace Wallabag\CoreBundle\Helper;

use GuzzleHttp\Cookie\FileCookieJar as BaseFileCookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Utils;

/**
 * Overidden Cookie behavior to:
 *     - fix multiple concurrent writes (see https://github.com/guzzle/guzzle/pull/1884)
 *     - ignore error when the cookie file is malformatted (resulting in clearing it).
 */
class FileCookieJar extends BaseFileCookieJar
{
    /**
     * Saves the cookies to a file.
     *
     * @param string $filename File to save
     *
     * @throws \RuntimeException if the file cannot be found or created
     */
    public function save($filename)
    {
        $json = [];
        foreach ($this as $cookie) {
            if ($cookie->getExpires() && !$cookie->getDiscard()) {
                $json[] = $cookie->toArray();
            }
        }

        if (false === file_put_contents($filename, json_encode($json), LOCK_EX)) {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException("Unable to save file {$filename}");
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Load cookies from a JSON formatted file.
     *
     * Old cookies are kept unless overwritten by newly loaded ones.
     *
     * @param string $filename cookie file to load
     *
     * @throws \RuntimeException if the file cannot be loaded
     */
    public function load($filename)
    {
        $json = file_get_contents($filename);
        if (false === $json) {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException("Unable to load file {$filename}");
            // @codeCoverageIgnoreEnd
        }

        try {
            $data = Utils::jsonDecode($json, true);
        } catch (\InvalidArgumentException $e) {
            // cookie file is invalid, just ignore the exception and it'll reset the whole cookie file
            $data = '';
        }

        if (\is_array($data)) {
            foreach (Utils::jsonDecode($json, true) as $cookie) {
                $this->setCookie(new SetCookie($cookie));
            }
        } elseif (\strlen($data)) {
            throw new \RuntimeException("Invalid cookie file: {$filename}");
        }
    }
}
