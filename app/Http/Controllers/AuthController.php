<?php


namespace App\Http\Controllers;


use App\Helper\CustomController;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;

class AuthController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        try {
            $email = $this->postField('email');
            $password = $this->postField('password');
            $vendor = Vendor::with([])
                ->where('email', '=', $email)
                ->first();
            if (!$vendor) {
                return $this->jsonNotFoundResponse('user not found');
            }

            $isPasswordValid = Hash::check($password, $vendor->password);
            if (!$isPasswordValid) {
                return $this->jsonUnauthorizedResponse('password did not match');
            }

            $token = $this->generateTokenById($vendor->id, 'vendor');
            return $this->jsonSuccessResponse('success', [
                'access_token' => $token
            ]);
        }catch (\Throwable $e) {
            return $this->jsonErrorResponse('internal server error '.$e->getMessage());
        }
    }
}
