<?php
/**
 * System generated by SysGen (System Generator with Formdin Framework) 
 * Download SysGenAd: https://github.com/bjverde/sysgenad
 * Download Formdin5 Framework: https://github.com/bjverde/formDin5
 * 
 * SysGen  Version: 0.1.0
 * FormDin Version: 5.0.0
 * 
 * System APPEV1 created in: 2020-08-03 02:23:14
 */
class Tb_paginacao extends TRecord
{
    const TABLENAME = 'tb_paginacao';
    const PRIMARYKEY= 'id';
    const IDPOLICY  = 'serial'; //{max, serial}

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
    }

}
?>