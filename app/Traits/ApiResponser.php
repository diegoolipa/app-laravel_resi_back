<?php

namespace App\Traits;

trait ApiResponser {

    function successResponse($data, $code = 200, $message = 'Operación exitosa') {
        return response()->json(['status'=>'success','message'=>$message ,'data' => $data], $code);
    }

    function errorResponse($message, $code = 422) {
        return response()->json(['status'=>'error','error' => ['message' => $message, 'code' => $code]], $code);
    }

    function showMessage($message, $code) {
        return $this->successResponse($message, $code);
    }
}
