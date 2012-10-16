<?php

class lwECardDatahandler
{

    public function __construct()
    {
        $this->db = lw_registry::getinstance()->getEntry("db");
    }

    public function saveECard($dataArray)
    {
        $this->db->setStatement('INSERT INTO t:lw_ecard (name, absender, empfaenger, betreff, nachricht, hash, ablaufdatum) VALUES ( :name, :absender, :empfaenger, :betreff, :nachricht, :hash, :ablaufdatum) ');
        $this->db->bindParameter("name","s", $dataArray["name"]);
        $this->db->bindParameter("absender","s", $dataArray["absender"]);
        $this->db->bindParameter("empfaenger","s", $dataArray["empfaenger"]);
        $this->db->bindParameter("betreff","s", $dataArray["betreff"]);
        $this->db->bindParameter("nachricht","s", $dataArray["nachricht"]);
        $this->db->bindParameter("hash","s", $dataArray["hash"]);
        $this->db->bindParameter("ablaufdatum","i", time() + (86400 * 30)); // 86400 sek. = 3600sek (1 st.) * 24std.  >>>>> (1 tag in sek.));
        return $this->db->pdbquery();
    }
    
    public function loadECard($hash)
    {
        $this->db->setStatement('SELECT * FROM t:lw_ecard WHERE hash = :hash ');
        $this->db->bindParameter('hash','i',$hash);
        return $this->db->pselect1();
    }
    
    public function deleteECard($datum)
    {
        $this->db->setStatement('DELETE FROM t:lw_ecard WHERE ablaufdatum < :datum ');
        $this->db->bindParameter('hash','i',$datum);
        return $this->db->pdbquery();
    }
    
    public function generateHash()
    {
        $hash = md5(rand(100,9000).date("ymdhis")) ;
        
        $this->db->setStatement('SELECT * FROM t:lw_ecard WHERE hash = :hash ');
        $this->db->bindParameter('hash','s',$hash);
        $result = $this->db->pselect();
        
        if($result == false) return $hash; else $this->generateHash();
    }

}
