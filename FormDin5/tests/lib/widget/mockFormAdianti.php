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

    public function onClear()
    {
    }

    public function onReload()
    {
    }

    public function onEdit()    
    {
    }
    
    public function onDelete()
    {
    }
}