<?php

use Adianti\Registry\TSession;

class exe_CheckField extends TPage
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

        $frm = new TFormDin($this,'Exemplo do Campo Checkbox ');

        $listItems = $this->getListItems();
        
        $frm->addGroupField('gp1', 'Grupo');
            //$frm->addTextField('nm_anexo_doc', 'Arquivo de Word (DOC) associado pelo Advogado ou Estagiário:&nbsp;&nbsp;',35,false,35,null,true,null,null,null,true);
            //$frm->addButton('Baixar',null,'btn_baixar_anexo',"baixar_anexo('doc')",null,false,false,'last16.gif',null,'Baixar');
            //$frm->addHtmlField('st_publico_doc',null,null,null,20,100,false)->setCss('font','9px sans-serif')->setCss('border','1px solid red');
            $frm->addCheckField('st_publico', 'Na análise do Advogado esta peça deve ser pública?', false, null, null, null, null, null, null, null, null, true);
        
            $frm->addCheckField('campo_1', 'Confirma ?', true);
            $frm->addCheckField('campo_2', 'Bioma Obrigatório para o cadastramentodas espécies:', true, '1=Cerrado,2=Mata Atlântica,3=Caatinga', null, null, null, null, null, null, null, true);
            $frm->addCheckField('campo_3', 'Bioma Não Obrigatório:', false, '1=Cerrado,2=Mata Atlântica,3=Caatinga');
            //$frm->addCheckField('campo_4', 'Exemplo 4:', false, 'N', null, null);
        
            //$frm->addCheckField('cd_especie', 'Espécies:', false, '1=Amarela,2=Branca,3=Vermelha')->addEvent('onChange', 'cd_especieChange()');
        $frm->closeGroup();

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