<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property string userid
 * @property string username
 * @property string phone_number
 * @property string password
 * @method where(string $string, $credential)
 */

class Users extends Model {

    protected $usersTable   = "users";

    public function __construct(){
        parent::__construct();
    }

    private function generateUserID(){
        $newUserID      = str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
        //$usersample     = new User();
        $isFind         = true;
        while ($isFind) {

            if (DB::table($this->usersTable)->where('userid', $newUserID)->exists()) {
                $isFind = true;
            }else{
                $isFind = false;
                break;
            }
        }
        return $newUserID;
    }

    /**
     * Create user in registration proccess
     * @param $aRegisterData
     * @return bool
     */
    public function create($aRegisterData) {
        $this->userid           = $this->generateUserID();
        $this->username         = $aRegisterData['c_username'];
        $this->phone_number     = $aRegisterData['c_phone_number'];
        $this->password         = sha1($aRegisterData['c_password']);
        return $this->save();
    }

    /**
     * Activated the account
     * @param $flag
     * @param $credential
     * @return bool
     */
    public function setIsActivated($flag, $credential) {
        return $this->where('userid', $credential)
            ->orWhere('email', $credential)
            ->update(['is_activated' => 1]);
    }

    /**
     * Saving DOB, Firstname ,Lastname and gender
     * @param $aPersonnalInfos
     * @return Bool
     */
    public function setPersonal($aPersonnalInfos) {
        return $this->where('userid',  $aPersonnalInfos['userid'])
            ->update([
                'birth_date'    => $aPersonnalInfos['birth_date'],
                'firstname'     => $aPersonnalInfos['firstname'],
                'lastname'      => $aPersonnalInfos['lastname'],
                'gender'        => $aPersonnalInfos['gender']
            ]);
    }

    /**
     * Saving Job title, description and tags
     * @param $aJobDetails
     * @return Bool
     */
    public function setJobInformations($aJobDetails) {
        return $this->where('userid',  $aJobDetails['userid'])
            ->update([
                'job_title'         => $aJobDetails['job_title'],
                'job_description'   => $aJobDetails['job_description'],
                'job_tags'          => $aJobDetails['job_tags']
            ]);
    }

    /**
     * Saving email.
     * @param $aEmail
     * @return bool
     */
    public function setEmail($aEmail) {
        return $this->where('userid', $aEmail['userid'])
            ->update(['email'   => $aEmail['email']]);
    }

    /**
     * Saving Phone number.
     * @param $aPhone
     * @return bool
     */
    public function setPhone($aPhone){
        return $this->where('userid', $aPhone['userid'])
            ->update(['phone_number'    => $aPhone['phone_number']]);
    }

    /**
     * Saving Code pin urgency code pin
     * @param $aPinCodes
     * @return bool
     */
    public function setCodePin( $aPinCodes ){
        return $this->where('userid', $aPinCodes['userid'])
            ->update([
                'pincode'           => $aPinCodes['pincode'],
                'urgency_pincode'   => $aPinCodes['urgencypincode']
            ]);
    }

    /** Change the password of the user
     * @param $newPassword
     * @param $credential
     * @return bool
     */
    public function changePassword($newPassword, $credential){
        return $this->where('userid', $credential)
            ->update(['password' => sha1($newPassword)]);
    }

    /**
     * Look credentials for user (login)
     * @param $uCredential
     * @param bool $isPassowrd
     * @param $userPass
     * @return array
     */
    public function findWithCredentials($uCredential, $isPassowrd = false, $userPass = null ){
        $sqlPassCheck   = '';
        $uCredential    = strval($uCredential);
        if (true === $isPassowrd) {
            $sqlPassCheck = "AND password = '".sha1($userPass)."' ";
        }

        $sqlQuery = '
            SELECT userid, username, email, phone_number, job_title, job_description, job_tags, 
                firstname, lastname, fullname, birth_date, gender, address, id_card, 
                created_at, updated_at as last_login 
            FROM '. $this::getTable() . ' WHERE (userid = ? OR username = ? OR email = ? OR phone_number = ? ) 
            '. $sqlPassCheck .' ORDER BY created_at LIMIT 1 
        ';
        $userData = DB::select($sqlQuery, [$uCredential, $uCredential, $uCredential, $uCredential]);
        if (is_array($userData) && 0 < sizeof($userData)) {
            return $userData[0];
        } else {
            return [];
        }
    }

    /**
     * @param $userid
     * @return mixed/array
     */
    public function userDetails($userid){
        return $this::where('userid', $userid)->first();
    }

    /**
     * @return Users[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll(){
        return $this::all();
    }
}
