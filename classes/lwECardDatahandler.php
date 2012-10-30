<?php

/*

name = name
absender = opt1text
empfaenger = opt2text
betreff = opt3text
nachricht = opt1clob
hash = opt4text
ablaufdatum = opt1number

*/

class lwECardDatahandler
{

    public function __construct()
    {
        $this->db = lw_registry::getinstance()->getEntry("db");
    }

    public function saveECard($dataArray)
    {
        $this->db->setStatement('INSERT INTO t:lw_master (lw_object, name, opt1text, opt2text, opt3text, opt1clob, opt4text, opt1number) VALUES ( :object,  :name, :absender, :empfaenger, :betreff, :nachricht, :hash, :ablaufdatum) ');
        $this->db->bindParameter('object', 's', 'lw_ecard');
        $this->db->bindParameter("name", "s", $dataArray["name"]);
        $this->db->bindParameter("absender", "s", $dataArray["absender"]);
        $this->db->bindParameter("empfaenger", "s", $dataArray["empfaenger"]);
        $this->db->bindParameter("betreff", "s", $dataArray["betreff"]);
        $this->db->bindParameter("nachricht", "s", htmlentities($dataArray["nachricht"]));
        $this->db->bindParameter("hash", "s", $dataArray["hash"]);
        $this->db->bindParameter("ablaufdatum", "i", time() + (86400 * $dataArray["auto-delete"])); // 86400 sek. = 3600sek (1 st.) * 24std.  >>>>> (1 tag in sek.));
        //die($this->db->prepare());
        return $this->db->pdbquery();
    }
    
    public function loadECard($hash)
    {
        $this->db->setStatement('SELECT name, opt1text as absender, opt2text as empfaenger, opt3text as betreff, opt1clob as nachricht, opt4text as hash, opt1number as ablaufdatum FROM t:lw_master WHERE lw_object = :object AND opt4text = :hash ');
        $this->db->bindParameter('object', 's', 'lw_ecard');
        $this->db->bindParameter('hash', 's', $hash);
        //die($this->db->prepare());
        return $this->db->pselect1();
    }
    
    public function deleteECard($datum)
    {
        $this->db->setStatement('DELETE FROM t:lw_master WHERE lw_object = :object AND opt1number < :datum ');
        $this->db->bindParameter('object', 's', 'lw_ecard');
        $this->db->bindParameter('datum','i',$datum);
        //die($this->db->prepare());
        //return $this->db->pdbquery();
    }
    
    public function generateHash()
    {
        $hash = md5(rand(100,9000).date("ymdhis")) ;
        
        $this->db->setStatement('SELECT * FROM t:lw_master WHERE lw_object = :object AND hash = :hash ');
        $this->db->bindParameter('object', 's', 'lw_ecard');
        $this->db->bindParameter('hash','s',$hash);
        $result = $this->db->pselect();
        
        if($result == false) return $hash; else $this->generateHash();
    }

}
