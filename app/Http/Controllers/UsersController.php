<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Users as UsersModel;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller {

    protected static $usersModel;
    private static $aResponse;

    public function __construct(UsersModel $usersModel) {
        self::$usersModel   = $usersModel;
        self::$aResponse    = array();
    }

    /**
     * Index of the controller.
     */
    public function index() {
        //return self::$usersModel->getAll();
        return response()->json(self::$usersModel->getAll());
    }


    public function tests(){
        $aUserData  = self::$usersModel->findWithCredentials('699009473');
        self::$aResponse = [ 'status' => 'success', 'message' => 'Successfully login', 'datas' => $aUserData ];
        return response()->json(self::$aResponse);
    }

    /** Get user details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDetails(Request $request) {
        return response()->json(
            self::$usersModel->userDetails($request->input('userid'))
        );
    }

    /**
     * Log in the user with theirs credentials
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signin(Request $request) {

        $validator = Validator::make($request->all(), [
            'c_credential'      => 'bail|required|min:4|max:190',
            'c_password'      => 'bail|min:6|max:255|'
        ]);

        if ($validator->fails()) {
            self::$aResponse = [ 'status' => 'error', 'message' => 'Incorrects credentials',
                'datas' => json_decode($validator->errors()) ];
        } else {
            # we proccess with model
            $aUserData = self::$usersModel->findWithCredentials(
                $request->input('c_credential'), true,
                $request->input('c_password'));
            if ( true === is_object($aUserData) ) {
                self::$aResponse = [ 'status' => 'success', 'message' => 'Successfully login',
                    'datas' => $aUserData ];
            } else {
                self::$aResponse = [ 'status' => 'error', 'message' => 'Incorrects credentials',
                    'datas' => [] ];
            }
        }
        return response()->json(self::$aResponse);
    }

    /**
     * Registration for new user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(Request $request) {

        # Bail is used for checking step by step on the field
        # phone_number validator is registered on app/Providers/AppServiceProvider.php
        $objValidator = Validator::make($request->all(), [
            'c_username'      => 'bail|required|string|min:4|max:190|regex:/^\S*$/u|unique:users,username',
            'c_phone_number'  => 'bail|required|numeric|unique:users,phone_number',
            'c_password'      => 'bail|min:6|max:255|'
        ]);

        #   Look for validation.
        if ($objValidator->fails()) {

            self::$aResponse = [ 'status' => 'error', 'message' => 'Incorrects credentials',
                'datas' => json_decode($objValidator->errors()) ];
        } else {

            $userFormData = array(
                'c_username'      => $request->input('c_username'),
                'c_phone_number'  => $request->input('c_phone_number'),
                'c_password'      => $request->input('c_password')
            );

            #   Going to the model
            if (true === self::$usersModel->create($userFormData)) {
                # We should return the userid
                self::$aResponse = [ 'status'  => 'success', 'message' => 'Welcome to Coinless',
                        'datas' => self::$usersModel->findWithCredentials($request->input('c_username') ) ];
            } else {

                self::$aResponse = [ 'status' => 'error', 'message' => 'An error occured', 'datas' => [] ];
            }
        }

        return response()->json(self::$aResponse);
    }

    /**
     * Setting DateOfBirth, firstname and lastname
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPersonal(Request $request){

        $objValidator = Validator::make($request->all(), [
            'birth_date'    => 'bail|date_format:"Y-m-d"',
            'firstname'     => 'bail|string|min:2|max:255',
            'lastname'      => 'bail|string|min:2|max:255',
            'gender'        => 'bail|string|min:4|max:7'
        ]);

        #   Look for validation.
        if ($objValidator->fails()) {

            self::$aResponse = [ 'status' => 'error', 'message' => 'Incorrects form data',
                'datas' => json_decode($objValidator->errors()) ];
        } else {

            if ( 1 === self::$usersModel->setPersonal($request->input()) ) {

                self::$aResponse = [
                    'status'    => 'success',
                    'message'   => 'Informations saved',
                    'datas'     => self::$usersModel->findWithCredentials($request->input('userid'))
                ];
            } else {

                self::$aResponse = [
                    'status'    => 'error',
                    'message'   => 'Error occured!',
                    'datas'     => []
                ];
            }
        }

        return response()->json(self::$aResponse);
    }

    /**
     * Setting information about job title, job description and job tags
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setJobInformation(Request $request){

        $objValidator = Validator::make($request->all(), [
            'job_title'         => 'bail|string|min:4',
            'job_description'   => 'bail|string|min:4',
            'job_tags'          => 'bail|string|min:4'
        ]);

        #   Look for validation.
        if ($objValidator->fails()) {

            self::$aResponse = [ 'status' => 'error', 'message' => 'Incorrects form data',
                'datas' => json_decode($objValidator->errors()) ];
        } else {

            if ( 1 === self::$usersModel->setJobInformations($request->input()) ) {

                self::$aResponse = [
                    'status'    => 'success',
                    'message'   => 'Informations saved',
                    'datas'     => self::$usersModel->findWithCredentials($request->input('userid'))
                ];
            } else {

                self::$aResponse = [
                    'status'    => 'error',
                    'message'   => 'Error occured!',
                    'datas'     => []
                ];
            }
        }

        return response()->json(self::$aResponse);
    }

    /**
     * Set a new password
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request) {

        $objValidator = Validator::make($request->all(), [
            'userid'               => 'bail|min:6|max:255|required|string|',
            'c_password'           => 'bail|min:6|max:255|required',
            'c_password_confirm'   => 'bail|min:6|max:255|required_with:c_password|same:c_password'
        ]);

        #   Look for validation.
        if ($objValidator->fails()) {

            self::$aResponse = [ 'status' => 'error', 'message' => 'Incorrects form data',
                'datas' => json_decode($objValidator->errors()) ];
        } else {

            if ( self::$usersModel->changePassword($request->input('c_password'), $request->input('userid')) ) {
                self::$aResponse = [
                    'status'    => 'success',
                    'message'   => 'New password saved',
                    'datas'     => []
                ];
            } else {
                self::$aResponse = [
                    'status'    => 'error',
                    'message'   => 'Error occured!',
                    'datas'     => []
                ];
            }
        }

        return response()->json(self::$aResponse);
    }

}
