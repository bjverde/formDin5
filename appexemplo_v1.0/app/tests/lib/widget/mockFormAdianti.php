<?php

/**
 * Mock de Form com FormDin sobre Adianti
 */
class mockFormDinComAdianti extends TPage
{

    // trait com onSave, onClear, onEdit...
    use Adianti\Base\AdiantiStandardFormTrait;
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;
    
    public function onSave()
    {
    }

    public static function onSaveStatic()
    {
    }

    public function onClear()
    {
    }

    public function onReload($param = null){}
    public function onAction($param = null){}
    public function onCustom($param = null){}

    public function onEdit()    
    {
    }
    
    public function onDelete()
    {
    }
}