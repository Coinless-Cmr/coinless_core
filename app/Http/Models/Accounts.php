<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Accounts extends Model {

    protected $accountsTable   = "accounts";

    public function __construct(){
        parent::__construct();
    }

    public function subscribe($dataInfos) {
        # We looking if there is subscription
        $currentSub 					= $this->getCurrent($dataInfos['userid']);
        $dataInfos['prev_num_days'] 	= 0;
        if(0 < sizeof($currentSub) && 0 < intval($currentSub[0]->remaining_days)) {
            $dataInfos['prev_num_days'] = intval($currentSub[0]->remaining_days);
        }

        # We proceed to create subscription
        # create a trigger to set reg_status on 0 before insert for the userid
        $this->userid       	= $dataInfos['userid'];
        $this->cost_value     	= $dataInfos['cost_value'];
        $this->prev_num_days    = $dataInfos['prev_num_days'];
        $this->num_days     	= $dataInfos['num_days'];
        return $this->save();
    }

    public function getCurrent($userid) {

        /*return $this::where(['userid' => $userid, 'reg_status' => 1])
            ->orderBy('created_at', 'ASC')
            ->orderBy(1)
            ->get();
        */
        $sqlParams = array();
        $sqlParams[]    = $userid;
        $sqlParams[]    = 1;
        return DB::select('SELECT userid, prev_num_days, num_days, total_days, '
            .'datediff(ended_at, now()) AS remaining_days, created_at, ended_at '
            .'FROM '. $this::getTable() . ' WHERE userid = ? AND reg_status = ? '
            //.'AND datediff(ended_at, now()) > 0 '
            .'ORDER BY created_at LIMIT 1 ', $sqlParams);

    }

    public function getAll(){
        return $this::all();
    }
}
