<?php

namespace BitfactoryNL\PostcodeNl\Services;

use Illuminate\Validation\ValidationException;
use BitfactoryNL\PostcodeNl\Address;
use BitfactoryNL\PostcodeNl\Exceptions\AccountSuspended;
use BitfactoryNL\PostcodeNl\Exceptions\AddressNotFound;
use BitfactoryNL\PostcodeNl\Exceptions\Unauthorized;
use BitfactoryNL\PostcodeNl\Http\PostcodeNlClient;
use BitfactoryNL\PostcodeNl\Validators\AddressLookupValidator;

class AddressLookup
{
    /**
     * @var AddressLookupValidator
     */
    protected $validator;

    /**
     * @var PostcodeNlClient
     */
    protected $client;

    /**
     * AddressLookup constructor.
     *
     * @param AddressLookupValidator $validator
     * @param PostcodeNlClient $client
     */
    public function __construct(AddressLookupValidator $validator, PostcodeNlClient $client)
    {
        $this->validator = $validator;
        $this->client = $client;
    }

    /**
     * Performs an address lookup.
     *
     * @param string $postcode
     * @param int $houseNumber
     * @param null|string $houseNumberAddition
     * @return Address
     * @throws ValidationException
     * @throws AccountSuspended
     * @throws AddressNotFound
     * @throws Unauthorized
     */
    public function lookup(string $postcode, int $houseNumber, string $houseNumberAddition = null): Address
    {
        $this->validator->validate(array_filter(compact('postcode', 'houseNumber', 'houseNumberAddition')));

        $uri = $this->getUri($postcode, $houseNumber, $houseNumberAddition);
        $response = $this->client->get($uri);
        $data = json_decode($response->getBody()->getContents(), true);
        return new Address($data);
    }

    /**
     * Returns the URI for the API request.
     *
     * @param string $postcode
     * @param int $houseNumber
     * @param null|string $houseNumberAddition
     * @return string
     */
    public function getUri(string $postcode, int $houseNumber, string $houseNumberAddition = null): string
    {
        return "https://api.postcode.nl/rest/addresses/$postcode/$houseNumber/$houseNumberAddition";
    }
}
