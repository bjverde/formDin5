<?php

use Adianti\Registry\TSession;

class exe_checklist extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onSave, onClear, onEdit...
    use Adianti\Base\AdiantiStandardFormTrait;
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin($this,'Exemplo do Campo Check Field');

        $listItems = $this->getListItems();
        $checkList = new TFormDinCheckList('checkPessoa','Selecione a Pessoa',false,$listItems);
        $checkList->addColumn('code','Id Pessoa','center','10%');
        $checkList->addColumn('name','Nome','left','70%');
        $checkList->addColumn('address','Endereço','left','20%');

        $frm->addCheckList($checkList,false);

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


    function getListItems()
    {
        $listItems = array();

        $item = new StdClass;
        $item->code     = '1';
        $item->name     = 'Aretha Franklin';
        $item->address  = 'Memphis, Tennessee';
        $item->phone    = '1111-1111';        
        $listItems[] = $item;
        
        $item = new StdClass;
        $item->code     = '2';
        $item->name     = 'Eric Clapton';
        $item->address  = 'Ripley, Surrey';
        $item->phone    = '2222-2222';
        $listItems[] = $item;
        
        $item = new StdClass;
        $item->code     = '3';
        $item->name     = 'B.B. King';
        $item->address  = 'Itta Bena, Mississippi';
        $item->phone    = '3333-3333';
        $listItems[] = $item;
        
        $item = new StdClass;
        $item->code     = '4';
        $item->name     = 'Janis Joplin';
        $item->address  = 'Port Arthur, Texas';
        $item->phone    = '4444-4444';
        $listItems[] = $item;

        $item = new StdClass;
        $item->code     = '5';
        $item->name     = 'Janis Martin';
        $item->address  = 'Port Arthur, Bahia';
        $item->phone    = '4444-55555';
        $listItems[] = $item;
        

        $item = new StdClass;
        $item->code     = '6';
        $item->name     = 'Maria Martin';
        $item->address  = 'Caldas, Amazonas';
        $item->phone    = '6666-55555';
        $listItems[] = $item;


        $item = new StdClass;
        $item->code     = '7';
        $item->name     = 'Maria Martin';
        $item->address  = 'Caldas, Amazonas';
        $item->phone    = '6666-55555';
        $listItems[] = $item;

        $item = new StdClass;
        $item->code     = '8';
        $item->name     = 'Aretha Franklin';
        $item->address  = 'Memphis, Tennessee';
        $item->phone    = '1111-1111';        
        $listItems[] = $item;        

        $item = new StdClass;
        $item->code     = '9';
        $item->name     = 'Aretha Franklin';
        $item->address  = 'Memphis, Tennessee';
        $item->phone    = '1111-1111';        
        $listItems[] = $item;

        return $listItems;
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