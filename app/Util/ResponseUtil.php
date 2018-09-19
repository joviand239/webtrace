<?php

namespace App\Util;

class ResponseUtil {

	public static function Success($data = '') {
		return ['status'=>'success', 'data'=>$data];
	}
    public static function Error($msg) {
        return ['status'=>'error', 'msg'=>$msg];
    }
    public static function Unauthorized($msg) {
        return ['status'=>'unauthorized', 'msg'=>$msg];
    }
}