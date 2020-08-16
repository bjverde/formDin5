<?php

use Adianti\Registry\TSession;

class exe_DateTimeField extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin($this,'Exemplo Campo Data Hora');

        $frm->addGroupField('gpx1', 'Formato da Data');
            $frm->addDateTimeField('dat01', 'Data Br:', true);
            $frm->addDateTimeField('dat02', 'Data ISO:', false,false,null,null,null,'yyyy-mm-dd hh:ii')->setToolTip(null, 'Mascara yyy-mm-dd hh:ii');

        $frm->addGroupField('gpx2', 'Valor inicial');
            $dt04 = $frm->addDateTimeField('dat03', 'Data Br:');
            $dt04->setValue('01/08/2020 23:43');
            $frm->addDateTimeField('dat04', 'Data ISO:', false,false,'2020-08-01 23:43',null,null,'yyy-mm-dd hh:ii')->setToolTip(null, 'Mascara yyy-mm-dd hh:ii');
        
        $frm->addGroupField('gpx3', 'Texto Exemplo');
            $dt05 = $frm->addDateTimeField('dat05', 'Data Br:');
            $dt05->setPlaceHolder('01/08/2020');
            $frm->addDateTimeField('dat06', 'Data ISO:', false,false,null,null,null,'yyyy-mm-dd',null,'2020/08/01')->setToolTip(null, 'Mascara yyyy-mm-dd');

        $frm->addGroupField('gpx4', 'Outros Exemplos');
        $dt2 = $frm->addDateTimeField('dat_nascimento2', 'Data no ano 1990:', null,null,null,'01/01/1990','31/12/1990');
        $dt2->setToolTip(null, 'Aceita somente Datas entre 01/01/1990 e 31/12/1990');
        
        $dt3 = $frm->addDateTimeField('dat_nascimento3', 'Data mais novos que 2000:', null,null,null,'01/01/2000');
        $dt3->setToolTip(null, 'Aceita somente mais novas que 01/01/2000');
        
        $frm->addDateField('dat_nascimento4', 'Data até 1800:', null,null,null, null, '31/12/1800')->setToolTip(null, 'Aceita somente mais novas que 31/12/1800');

        $expires = new TDateTime('expires');
        //$expires->setMask('dd/mm/yyyy');
        //$expires->setDatabaseMask('yyyy-mm-dd');
        $expires->setValue( date('Y-m-d', strtotime("+1 days")) );

        $frm->addFields( [new TLabel('Expires at')], [$expires]);

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