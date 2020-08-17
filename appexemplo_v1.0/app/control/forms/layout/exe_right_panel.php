<?php

use Adianti\Registry\TSession;

class exe_right_panel extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        $this->adianti_target_container = 'adianti_right_panel';

        $frm = new TFormDin($this,'Ajuda Lateral');

        $frm->addHtmlField('html1', 'Você está vendo o painel letal', null, 'Dica:', null, 200);        

        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $frm->setAction( _t('Save'), 'onSave', null, 'fa:save', 'green' );
        $frm->setActionLink( _t('Clear'), 'onClear', null, 'fa:eraser', 'red');
        $frm->setActionLink( _t('Close'), 'onClose', null, 'fa:times', 'red');
        $frm->setActionHeaderLink( _t('Close'), 'onClose', null, 'fa:times', 'red');

        $this->form = $frm->show();
        
        // add the table inside the page
        parent::add($this->form);
    }


    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }    
    /**
     * Clear filters
     */
    public function onClear()
    {
        $this->clearFilters();
        $this->onReload();
    }
    public function onSave($param)
    {
        try
        {
            $data = $this->form->getData();
            $this->form->setData($data);
            $this->form->validate();
            
    
            //Função do FormDin para Debug
            FormDinHelper::d($param,'$param');
            FormDinHelper::debug($data,'$data');
            FormDinHelper::debug($_REQUEST,'$_REQUEST');

            new TMessage('info', 'Tudo OK!');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}