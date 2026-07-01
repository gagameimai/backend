<?php

/**
 *  表單驗證 錯誤文字重組
 *
 * @param object $data 內容
 * @return string
 */
if(!function_exists('validator_message_change')){
    function validator_message_change($validator) {
        $validator = json_decode($validator, true);

        $html = '';
        foreach ($validator as $field => $content) {
            foreach ($content as $val) {
                $html .= $val . '<br>';
            }
        }
        
        return $html;
    }
}

/**
 *  表單驗證 錯誤文字取得 轉成陣列
 *
 * @param object $data 內容
 * @return string
 */
if(!function_exists('validator_message_get_value')){
    function validator_message_get_value($validator) {
        $validator = json_decode($validator, true);

        $messages = [];
        foreach ($validator as $field => $content) {
            $messages[] = $content;
        }
        
        return $messages;
    }
}