<?php

use Adianti\Registry\TSession;

class exe_RadioField extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin($this,'Exemplo do Campo Radiobutton');
        
        $listTipo = array(1=>'A',2=>'B',3=>'B',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J');

        $frm->addGroupField('gp1', 'Grupo 1');
            $frm->addRadioField('sit_cancelado1', 'Ativo:', true, 'S=SIM,N=Não', null, false, null, 2, null, null, null, false);//->addEvent('onDblclick','dblClick(this)');
            $listGenero = array('M'=>'<span tooltip="true" title="Sexo Masculino">Masculino</span>','F'=>'<span tooltip="true" title="Sexo Feminino">Feminino</span>');
            $frm->addRadioField('sit_genero', 'Genero com Tooltip:', true, $listGenero, null, false, null, 2, null, null, null, false);
            $frm->addRadioField('sit_genero2', 'Genero 2 s/quebra:', true, 'M=Masculino,F=Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino ', null, false, null, 1, 400, null, null, false, true);
            $frm->addRadioField('sit_genero3', 'Genero 3 c/quebra:', true, 'M=Masculino,F=Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino Feminino ', null, false, null, 1, 400, null, null, false, false);            
            $frm->addRadioField('sit_genero4', 'Em 5 Colunas:', false, $listTipo, null, true, null, 5, null, null, null, false, false);        
            $frm->addRadioField('sit_genero5', 'Em 3 Colunas, com valor setado:', false, $listTipo, null, true, 7, 3, null, null, null, false, false);
        $frm->closeGroup();

        $frm->addGroupField('gp2', 'Novidades do FormDin5 devido o Adianti');

        $sit_genero5 = $frm->addRadioField('sit_genero6', 'Em 5 Colunas, estilo botão e tooptip:', false, $listTipo, null, true, 7, 5, null, null, null, false, false);        
        $sit_genero5->setUseButton(true);
        $listaLabel = $sit_genero5->getLabels();
        $labelItem7 = $listaLabel[7];
        $labelItem7->setTip("Item mais escolhido");
        $labelItem7->setSize(100);


        $sit_cancelado2 = $frm->addRadioField('sit_cancelado2', 'Ativo (vertical):', false, 'S=SIM,N=Não', null, false, null, 2, null, null, null, false);
        $sit_cancelado2->setLayout('vertical');

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