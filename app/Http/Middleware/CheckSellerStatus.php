<?php

namespace App\Http\Middleware;

use App\Enums\SellerApprovalType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSellerStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Assuming you're checking the status of the authenticated seller
        $seller = auth('sellerApi')->user();

        // Check if the seller is Approved
        if ($seller && $seller->approval_status != SellerApprovalType::getTypeApproved()) {
            return response()->json([
                'message' => 'Your account is not approved yet.',
            ], 403);
        }
        return $next($request);
    }
}
