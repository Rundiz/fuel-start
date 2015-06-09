<?php

namespace TestMod;

class TestModAdmin
{


    public function __construct() 
    {
        // load language
        \Lang::load('testmod::testmod');
    }// __construct


    public function admin_navbar()
    {
        $output = '<li>' . \Html::anchor('testmod/admin', 'Test module plugin') . "\n";
        $output .= '</li>';

        return $output;
    }// admin_navbar


}