<?php

function message($type,$message){
    session()->put('message',[
        'type' => $type,
        'message' => $message  
    ]);
}