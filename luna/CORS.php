<?php

namespace Luna;

/**
 * Class CORS
 * @package Luna
 *
 * Usage:
 * 
 * $router->attach('\Luna\CORS', '*')
 *     ->restrict('GET', '/post/{id}');
 * 
 */
class CORS extends \Zaphpa\BaseMiddleware {

  private $domain;

  function __construct($domain = '*') {
    $this->domain = $domain;
  }
  
  function preroute(&$req, &$res) {

    $allowedMethods = self::$context['http_method'];

    $res->addHeader("Access-Control-Allow-Origin", $this->domain);
    $headers = array (
        "Access-Control-Allow-Methods" => $allowedMethods,
        "Access-Control-Allow-Headers" => array (
            "origin", "accept", "content-type", "authorization",
            "x-http-method-override", "x-pingother", "x-requested-with",
            "if-match", "if-modified-since", "if-none-match", "if-unmodified-since"
        ),
        "Access-Control-Expose-Headers" => array (
            "tag", "link",
            "X-RateLimit-Limit", "X-RateLimit-Remaining", "X-RateLimit-Reset",
            "X-OAuth-Scopes", "X-Accepted-OAuth-Scopes"
        )
    );

    foreach ($headers as $key => $vals) {
      $res->addHeader($key, $vals);
    }
  }
}
