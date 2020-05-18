<?php

namespace BitfactoryNL\PostcodeNl\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controller;
use BitfactoryNL\PostcodeNl\Exceptions\AccountSuspended;
use BitfactoryNL\PostcodeNl\Exceptions\AddressNotFound;
use BitfactoryNL\PostcodeNl\Exceptions\Unauthorized;
use BitfactoryNL\PostcodeNl\Services\AddressLookup;

class AddressController extends Controller
{
    /**
     * @var AddressLookup
     */
    protected $lookup;

    /**
     * AddressController constructor.
     *
     * @param AddressLookup $lookup
     */
    public function __construct(AddressLookup $lookup)
    {
        $this->lookup = $lookup;
    }

    /**
     * Performs a Dutch address lookup and returns a JSON response.
     *
     * @param string $postcode
     * @param int|string $houseNumber
     * @param null|string $houseNumberAddition
     * @return JsonResponse
     */
    public function get(string $postcode, string $houseNumber, string $houseNumberAddition = null): JsonResponse
    {
        $postcode = strtoupper(str_replace(' ', '', $postcode));

        try {
            $address = $this->lookup->lookup(str_replace(' ', '', $postcode), (int)$houseNumber, $houseNumberAddition);
            return response()->json($address);
        } catch (ValidationException $e) {
            abort(400, 'Bad Request');
        } catch (Unauthorized $e) {
            abort(401, 'Unauthorized');
        } catch (AccountSuspended $e) {
            abort(403, 'Account suspended');
        } catch (AddressNotFound $e) {
            abort(404, 'Not Found');
        }
    }
}
