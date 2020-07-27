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


        $frm->addGroupField('fdl8', 'FormDin - 4 campo, label sobre');
        $frm->addTextField('fdl8id1', 'fdl8d1:', 20, false, 20, 'FormDin',false, null, null, true);
        $frm->addTextField('fdl8id2', 'fdl8d2:', 20, false, 20, 'FormDin',false, null, null, true);
        $frm->addTextField('fdl8id3', 'fdl8d3:', 20, false, 20, 'FormDin',false, null, null, true);
        $frm->addTextField('fdl8id4', 'fdl8d4:', 20, false, 20, 'FormDin',false, null, null, true);

        $frm->addFields( [ new TLabel('slider'),  new TSlider('slider') ] );

        $frm->addContent( [new TLabel('Adianti - 4 campos, label sobre', '#5A73DB', 12, '')] );
        $frm->addFields( [ new TLabel('adl8id1'), new TEntry('adl8id1') ] 
                                , [ new TLabel('adl8id2'), new TEntry('adl8id2') ] 
                                , [ new TLabel('adl8id3'), new TEntry('adl8id3') ] 
                                , [ new TLabel('adl8id4'), new TEntry('adl8id4') ] 
                            );


        $this->form = $frm->show();
        //------------------------------------------------------------------------------
        //------------------------------------------------------------------------------
        //------------------------------------------------------------------------------


        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));

        // add form actions
        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:check-circle green');
        $this->form->addActionLink(_t('Clear'),  new TAction([$this, 'clear']), 'fa:eraser red');

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
    public function clear()
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