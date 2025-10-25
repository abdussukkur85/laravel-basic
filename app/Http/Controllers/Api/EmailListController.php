<?php

namespace App\Http\Controllers\Api;

use App\Models\EmailList;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;

class EmailListController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $emails = EmailList::latest()->get();

        if ($emails->isEmpty()) {
            return $this->errorResponse('No emails found', 404);
        }

        return $this->successResponse($emails, 'Email list fetched successfully');
    }
}
