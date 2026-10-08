<?php
/**
 * Tb_mapcord Active Record
 * @author  Antigravity
 */
class Tb_mapcord extends TRecord
{
    const TABLENAME    = 'tb_mapcord';
    const PRIMARYKEY   = 'idtmapcord';
    const IDPOLICY     = 'serial'; // {max, serial}
    const CACHECONTROL = 'TAPCache';
    
    const CREATEDAT = 'dat_inclusao';
    const UPDATEDAT = 'dat_update';
    const DELETEDAT = 'dat_del';

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('txnome');
        parent::addAttribute('mapcord_lat');
        parent::addAttribute('mapcord_lon');
        parent::addAttribute('dat_inclusao');
        parent::addAttribute('dat_update');
        parent::addAttribute('dat_del');
    }
}
