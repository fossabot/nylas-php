<?php namespace Nylas\Messages;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Message Search
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/23
 */
class Search
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Search constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * search messages list
     *
     * @param string $q
     * @param string $accessToken
     * @return mixed
     */
    public function messages(string $q, string $accessToken = null)
    {
        $params =
        [
            'q'            => $q,
            'access_token' => $accessToken ?? $this->options->getAccessToken(),
        ];

        $rules = V::keySet(
            V::key('q', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty())
        );

        V::doValidate($rules, $params);

        $query  = ['q' => $params['q']];
        $header = ['Authorization' => $params['access_token']];

        return $this->options
        ->getRequest()
        ->setQuery($query)
        ->setHeaderParams($header)
        ->get(API::LIST['searchMessages']);
    }

    // ------------------------------------------------------------------------------

}