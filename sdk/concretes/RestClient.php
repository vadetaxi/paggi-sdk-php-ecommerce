<?php
/**
 * This file holds the basic configuration for the RestClient
 *
 * PHP version 5.6, 7.0, 7.1, 7.2
 *
 * @category RestClient_File
 * @package  Paggi
 * @author   Paggi Integracoes <ti-integracoes@paggi.com>
 * @license  GNU GPLv3 https://www.gnu.org/licenses/gpl-3.0.en.html
 * @link     http://developers.paggi.com
 */

namespace Paggi\SDK;

use Doctrine\Common\Inflector\Inflector;
use GuzzleHttp\Psr7\Request;
use Paggi\SDK\Interfaces\IRestClient;

/**
 * This class verify the RestClient
 *
 * PHP version 5.6, 7.0, 7.1, 7.2
 *
 * @category RestClient_Class
 * @package  Paggi
 * @author   Paggi Integracoes <ti-integracoes@paggi.com>
 * @license  GNU GPLv3 https://www.gnu.org/licenses/gpl-3.0.en.html
 * @link     http://developers.paggi.com
 */
class RestClient implements IRestClient
{
    private static $prefixUrl = "https://api.";
    private static $suffixUrl = "paggi.com/v1/";

    /**
     * Function who will set the HTTP method
     *
     * @param string $method Método HTTP
     *
     * @return string
     */
    public function setMethod($method)
    {
        $possible = array(
            "GET",
            "POST",
            "PUT",
            "DELETE",
        );
        $method = in_array(strtoupper($method), $possible)
        ? strtoupper($method) : "";
        return $method;
    }

    /**
     * Function who transform the resource's name in the endpoint
     *
     * @param string $resource Name who will be transformed in the endpoint
     *
     * @return string
     */
    public function getEndPoint($resource)
    {
        $inflector = new Inflector(); //self::$container->get('Inflector');
        $endPoint = strtolower(
            preg_replace(
                '/(?<!^)[A-Z]/',
                '_$0',
                $inflector->pluralize($resource)
            )
        );
        return $endPoint;
    }

    /**
     * Function who verify if the token will expire within one month
     *
     * @param array $data Array with itens to be added to headers
     *
     * @return string
     */
    public function createHeaders(array $data)
    {
        $headers = [];
        foreach ($data as $key => $value) {
            $headers = array_merge($headers, array($key => $value));
        }
        return (empty($headers) ? [] : $headers);
    }

    /**
     * Function who will get the endpoint
     *
     * @param array $data dictionary with data to be put in the request`s body
     *
     * @return string
     */
    public function createBody($data = [])
    {
        return (empty($data) ? [] : $data);
    }

    /**
     * Function who will mount the request url
     *
     * @param string $endpoint   Token for authentication
     * @param string $env        String with the environment
     * @param array  $parameters Parameters for url
     *
     * @return string
     */
    public function mountUrl($endPoint, $env, $id, $objectId = "", $parameters = [], $option = "")
    {
        $url = self::$prefixUrl;
        $url .= !strcmp($env, "Staging") ? "stg." : "";
        $url .= self::$suffixUrl;
        $url .= (strcmp($endPoint, "banks"))
        ? "partners/" . $id . "/"
        : "";
        $url .= $endPoint;
        $url .= empty($objectId) ? "" : "/" . $objectId;
        $url .= $option;
        if (!empty($parameters)) {
            $url .= "?";
            $local = "";
            foreach ($parameters as $key => $value) {
                $local = $key . "=" . $value;
                $local .= "&";
            }
            $url .= substr($local, 0, -1);
        }
        return $url;
    }

    /**
     * Function who create a request
     *
     * @param string #method   Method used in the request
     * @param string $url      URL for request
     * @param array  $headers  Headers of the request
     * @param array  $body     Body for the request
     * @param float  $timeout  seconds to timeout the request
     *
     * @return array
     */
    public function createRequest($method, $url, $headers = [], $body = [], $timeout = 0)
    {
        $client = new \GuzzleHttp\Client();
        array_push($headers, ["User-Agent" => phpversion() . " Guzzle6 " . curl_version()["version"] . " PaggiPHPSDK"]);

        try {
            $request = $client->request(
                $method,
                $url,
                [
                    "headers" => $headers,
                    "json" => $body,
                    "exceptions" => false,
                    "timeout" => $timeout
                ]
            );
        } catch(\GuzzleHttp\Exception\RequestException $e) {
            return new \GuzzleHttp\Psr7\Response(502);
        }

        return $request;
    }
}
