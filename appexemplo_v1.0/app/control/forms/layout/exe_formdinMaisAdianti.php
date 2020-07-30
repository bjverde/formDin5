<?php

use Adianti\Registry\TSession;

class exe_formdinMaisAdianti extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();



        $frm = new TFormDin($this,'Exemplo Layout - Campos FormDin e Adinati Nativos Juntos');


        $frm->addGroupField('fl8', 'FormDin - 4 campo, label sobre');
        $frm->addTextField('fl81', 'fl81:', 20, false, 20, 'FormDin',false, null, null, true);
        $frm->addTextField('fl82', 'fl82:', 20, false, 20, 'FormDin',false, null, null, true);
        $frm->addTextField('fl83', 'fl83:', 20, true, 20, 'Texto inicial',false, null, null, true);
        $frm->addTextField('fl84', 'fl84:', 20, false, 20, 'FormDin',false, null, null, true);

        $frm->addFields( [ new TLabel('slider'),  new TSlider('slider') ] );

        $txtLabelAl83 = 'al83';
        $al83 = new TEntry('al83');
        $al83->addValidation($txtLabelAl83, new TRequiredValidator);
        $al83->addValidation($txtLabelAl83, new TMaxLengthValidator, array(20));
        $al83->setValue('Texto inicial');
        
        $frm->addContent( [new TLabel('Adianti - 4 campos, label sobre')] );
        $frm->addFields( [ new TLabel('al81'), new TEntry('al81') ] 
                       , [ new TLabel('al82'), new TEntry('al82') ] 
                       , [ new TLabel('al83', 'red'), $al83 ] 
                       , [ new TLabel('al84'), new TEntry('al84') ] 
                       );

        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $frm->setAction( _t('Save'), 'onSave', null, 'fa:save', 'green' );
        $frm->setActionLink( _t('Clear'), 'onClear', null, 'fa:eraser', 'red');                            

        $this->form = $frm->show();
        //------------------------------------------------------------------------------
        //------------------------------------------------------------------------------
        //------------------------------------------------------------------------------


        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));

        /*
        // add form actions
        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:check-circle green');
        $this->form->addActionLink(_t('Clear'),  new TAction([$this, 'onClear']), 'fa:eraser red');
        */

        // creates the page structure using a table
        $formDinBreadCrumb = new TFormDinBreadCrumb(__CLASS__);
        $vbox = $formDinBreadCrumb->getAdiantiObj();
        $vbox->add($this->form);
        
        // add the table inside the page
        parent::add($vbox);
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
        $data = $this->form->getData();
        $this->form->setData($data);

        //Função do FormDin para Debug
        FormDinHelper::d($param,'$param');
        FormDinHelper::debug($data,'$data');
        FormDinHelper::debug($_REQUEST,'$_REQUEST');
    }
}