<?php

use Adianti\Registry\TSession;

class exe_DateField extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin($this,'Exemplo Campo Data');

        $frm->addDateField('dat_nascimento', 'Data:', true);

        $dt2 = $frm->addDateField('dat_nascimento2', 'Data no ano 1990:', null,null,null,'01/01/1990','31/12/1990');
        $dt2->setToolTip(null, 'Aceita somente Datas entre 01/01/1990 e 31/12/1990');
        
        $dt3 = $frm->addDateField('dat_nascimento3', 'Data mais novos que 2000:', null,null,null,'01/01/2000');
        $dt3->setToolTip(null, 'Aceita somente mais novas que 01/01/2000');
        
        $frm->addDateField('dat_nascimento4', 'Data até 1800:', null,null,null, null, '31/12/1800')->setToolTip(null, 'Aceita somente mais novas que 31/12/1800');

        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $frm->setAction( _t('Save'), 'onSave', null, 'fa:save', 'green' );
        $frm->setActionLink( _t('Clear'), 'onClear', null, 'fa:eraser', 'red');

        $this->form = $frm->show();

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