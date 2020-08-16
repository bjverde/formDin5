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
            $frm->addDateTimeField('dat02', 'Data ISO:', false,false,null,null,null,'dd/mm/yyyy hh:ii')->setToolTip(null, 'Mascara yyy-mm-dd hh:ii');

        $frm->addGroupField('gpx2', 'Valor inicial');
            $dt04 = $frm->addDateTimeField('dat03', 'Data Br:');
            $dt04->setValue('01/08/2020 23:43');
            $frm->addDateTimeField('dat04', 'Data ISO:', false,false,'2020-08-01 23:43',null,null,'yyyy-mm-dd hh:ii')->setToolTip(null, 'Mascara yyy-mm-dd hh:ii');
        
        $frm->addGroupField('gpx3', 'Texto Exemplo');
            $dt05 = $frm->addDateTimeField('dat05', 'Data Br:');
            $dt05->setPlaceHolder('01/08/2020 10:43');
            $frm->addDateTimeField('dat06', 'Data ISO:', false,false,null,null,null,'yyyy-mm-dd hh:ii',null,'2020-08-01 10:43')->setToolTip(null, 'Mascara yyyy-mm-dd');

        $frm->addGroupField('gpx4', 'Outros Exemplos');

        $dtAntes = new DateTime();
        $dtAntes->sub(new DateInterval('P3D'));
        $dtAntes = $dtAntes->format('m/d/Y H:i');
        $dtAntes = explode(" ", $dtAntes);
        $dtAntes = $dtAntes[0].' 10:00';
        
        $dtDepois = new DateTime();
        $dtDepois->add(new DateInterval('P5D'));
        $dtDepois = $dtDepois->format('m/d/Y H:i');
        $dtDepois = explode(" ", $dtDepois);
        $dtDepois = $dtDepois[0].' 15:00';

        $dt2 = $frm->addDateTimeField('dat_intervalEua', 'Data Hora EUA intervalo', null,null,null,$dtAntes,$dtDepois,'mm/dd/yyyy hh:ii');
        $dt2->setToolTip(null, 'Aceita somente datas 3 dias antes e 5 dias depois das 10:00 as 15:00');

        $dtAntes = new DateTime();
        $dtAntes->sub(new DateInterval('P3D'));
        $dtAntes = $dtAntes->format('Y-m-d H:i');
        $dtAntes = explode(" ", $dtAntes);
        $dtAntes = $dtAntes[0].' 10:00';
        
        $dtDepois = new DateTime();
        $dtDepois->add(new DateInterval('P5D'));
        $dtDepois = $dtDepois->format('Y-m-d H:i');
        $dtDepois = explode(" ", $dtDepois);
        $dtDepois = $dtDepois[0].' 15:00';

        $dt3 = $frm->addDateTimeField('dat_intervalIso', 'Data Hora ISO intervalo', null,false,null,$dtAntes,$dtDepois,'yyyy-mm-dd hh:ii');
        $dt3->setToolTip(null, 'Aceita somente datas 3 dias antes e 5 dias depois das 10:00 as 15:00');
        
        $frm->addDateField('dat_nascimento4', 'Data até 1800:', null,null,null, null, '31/12/1800')->setToolTip(null, 'Aceita somente mais novas que 31/12/1800');

        $expires = new TDateTime('expires');
        //$expires->setMask('dd/mm/yyyy');
        //$expires->setDatabaseMask('yyyy-mm-dd');
        $expires->setValue( date('Y-m-d h:i', strtotime("+5 days")) );

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