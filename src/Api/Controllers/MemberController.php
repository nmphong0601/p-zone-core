<?php

namespace PZone\Core\Api\Controllers;

use PZone\Core\Front\Controllers\RootFrontController;
use Illuminate\Http\Request;
use PZone\Core\Front\Models\ShopOrder;

class MemberController extends RootFrontController
{

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function getInfo(Request $request)
    {
        return response()->json($request->user());
    }
}
