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

        $frm->addGroupField('gpx1', 'Campos FormDin5');
            $np = $frm->addNumberField('num_pessoa', 'Quantidade de pessoas:', 9, true, 0, true, null, 5, null, null, null, true, true);
            $np->setExampleText('Minimo de 5');
            $frm->addNumberField('num_peso', 'Peso Unitário:', 5, false, 2, true)->setExampleText('Kg');
            $frm->addNumberField('num_preco', 'Preço Unitário:', 9, false, 2, false)->setExampleText('R$');

            $frm->addNumberField('numfd01', 'inteiros até 100', 3, false, 0, null,null,3,100);
            $frm->addNumberField('numfd02', 'num real até 100', 6, false, 2,false,null,3,100)->setExampleText('Num max caractes conta . e ,');

            $num03 = $frm->addNumberField('numfd03', 'Milhar Ponto'   , 10,false,2,true,null,null,null,true);
            $num03->setPlaceHolder('Um texto de exemplo Número');
            $frm->addNumberField('numfd04', 'Milhar Virgular', 10,false,2,false,'12345678',null,null,',');

            $frm->addNumberField('numfd05', 'Num Brasil', 10,false,2,true,'12345678',null,null,'.');
            $frm->addNumberField('numfd06', 'Num EUA'   , 10,false,2,false,'12345678',null,null,',');

        $frm->closeGroup();

        $frm->addGroupField('gpx2', 'Campos FormDin5 para comparar com Adianti');
            $numfd07 = $frm->addNumberField('numfd07','FD Celsius'   ,6,true ,3,null ,null,10,'80,999');
            $numfd07->setExampleText('Termometro Celsius, valor 10,000 a 80,999');
            
            $numfd08 = $frm->addNumberField('numfd08','FD Fahrenheit',5,false,2,false,'75.74','50.00','177.78',null,null,null,null,null,null,null,null,null,'.');
            $numfd08->setExampleText('Termometro Fahrenheit, valor 50.00 a 177.78, no paradrão Americano');
        $frm->closeGroup();

        $frm->addGroupField('gpx3', 'Campos Adianti para comparar com FormDin5');

            $numad07Label = 'AD Celsius';
            $numad07 = new TNumeric('numad07', 3, ',', '.', true);
            $numad07->addValidation($numad07Label, new TRequiredValidator);
            $numad07->addValidation($numad07Label, new TMaxLengthValidator, array(6));
            $numad07->addValidation($numad07Label, new TMinValueValidator, array(10));
            $numad07->addValidation($numad07Label, new TMaxValueValidator, array('80,999'));
            $numad08->setTip('Termometro Celsius, valor 10,000 a 80,999');

            $numad08Label = 'AD Fahrenheit';
            $numad08 = new TNumeric('numad08', 2, '.', ',', true);
            $numad08->addValidation($numad08Label, new TMaxLengthValidator, array(5));
            $numad08->addValidation($numad08Label, new TMinValueValidator, array('50.00'));
            $numad08->addValidation($numad08Label, new TMaxValueValidator, array('177.78'));
            $numad08->setValue('75.74');
            $numad08->setTip('Termometro Fahrenheit, valor 50.00 a 177.78, no paradrão Americano');

            $frm->addFields( [new TLabel($numad07Label, 'red')],[$numad07],[new TLabel($numad08Label)],[$numad08] );

        $frm->closeGroup();

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