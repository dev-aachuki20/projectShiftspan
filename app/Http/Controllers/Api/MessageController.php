<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Api\APIController;
use Symfony\Component\HttpFoundation\Response;


class MessageController extends APIController
{
    public function getGroupSubjects(){
        try {
            $data['subjects'] =   getSetting('message_subject');
           
            return $this->respondOk([
                'status'        => true,
                'message'       => trans('messages.record_retrieved_successfully'),
                'data'          => $data,
            ])->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }
}
