<?php

class lwECardCleaner
{
    public function __construct($dh)
    {
        $this->dh = $dh;
    }
    
    public function clean()
    {
        $this->dh->deleteECard(time());
    }
}
