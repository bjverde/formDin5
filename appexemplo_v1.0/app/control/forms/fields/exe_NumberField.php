<?php

use Adianti\Registry\TSession;

class exe_NumberField extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();



        $frm = new TFormDin($this,'Campo Numérico');

        $np = $frm->addNumberField('num_pessoa', 'Quantidade de pessoas:', 9, true, 0, true, null, 5, null, null, null, true, true);
        $np->setExampleText('Minimo de 5');
        $frm->addNumberField('num_peso', 'Peso Unitário:', 5, false, 2, true)->setExampleText('Kg');
        $frm->addNumberField('num_preco', 'Preço Unitário:', 9, false, 2, false)->setExampleText('R$');

        $frm->addNumberField('num01', 'inteiros até 100', 3, false, 0, null,null,3,100);
        $frm->addNumberField('num02', 'num real até 100', 6, false, 2,false,null,3,100)->setExampleText('Num max caractes conta . e ,');

        $numericLabel = 'Adianti Numeric:';
        $numeric = new TNumeric('numeric', 2, ',', '.', true);
        $numeric->addValidation($numericLabel, new TMaxValueValidator, array(100));
        $frm->addFields( [new TLabel($numericLabel)],[$numeric] );

        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $frm->setAction( _t('Save'), 'onSave', null, 'fa:save', 'green' );
        $frm->setActionLink( _t('Clear'), 'onClear', null, 'fa:eraser', 'red');

        $this->form = $frm->show();


        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));


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