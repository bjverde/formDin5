<?php

use Adianti\Registry\TSession;

class exe_colunas extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();



        $frm = new TFormDin('Exemplo Layout - Campos');

        $frm->addGroupField('fdl1', 'FormDin - 1 campo');
        $frm->addTextField('fdl1id1', 'fdl1id1:', 20);
        
        $frm->addGroupField('fdl2', 'FormDin - 1 campo campo sobre');
        $frm->addTextField('fdl2id1', 'fdl2d1:', 20, true, 20, 'FormDin',false, null, null, true);


        $frm->addGroupField('fdl3', 'FormDin - 2 campos');
        $frm->addTextField('fdl3id1', 'fdl3id1:', 20, true, 20, 'FormDin',false, null, null, false);
        $frm->addTextField('fdl3id2', 'fdl3id2:', 20, true, 20, 'FormDin',false, null, null, false);
        
        $frm->addGroupField('fdl4', 'FormDin - 2 campo campo sobre');
        $frm->addTextField('fdl4id1', 'fdl4d1:', 20, true, 20, 'FormDin',false, null, null, true);
        $frm->addTextField('fdl4id2', 'fdl4d2:', 20, true, 20, 'FormDin',false, null, null, true);

        $frm->addGroupField('gpx1', 'Teste Novo Grupo 1');
        $frm->addTextField('id1', 'id1:', 20, true, 20, '127.0.0.1'   , true, null, null, false);
        $frm->addTextField('id2', 'id2:', 20, true, 20, 'form_exemplo',false, null, null, false);
        $frm->addTextField('id3', 'id3:', 20, true, 20, 'form_exemplo',false, null, null, false);
        $frm->addTextField('id4', 'id4:', 20, true, 20, 'form_exemplo',false, null, null, false);
        $frm->addTextField('id5', 'id5:', 20, true, 20, 'form_exemplo',false, null, null, false);
        //$frm->addTextField('id6', 'id6:', 20, true, 20, 'form_exemplo',false, null, null, false);


        $frm->addTextField('TEXT01','Texto tam 10', 10);
        $frm->addTextField('TEXT02','Texto obrigatorio', 10,true,null,'inicial',true,null,null,false);
        $frm->addTextField('TEXT03', 'Com valor inicial', 50,true,null,'inicial',false,null,null,true);
        $frm->addTextField('TEXT04', 'Place Holder', 50, null,null,null,null,null,'Place Holder');
        $frm->addTextField('TEXT05', 'Leitura 01', 50, null,null,'inicial')->setReadOnly(true);        
        $x = $frm->addTextField('TEXT06', 'Leitura 02', 50, null,null,'inicial');
        $x->setReadOnly(true);
        $frm->addTextField('TEXT07', 'Leitura 03', 50, null,null,'Label Sobre',null,null,null,true);

        $this->form = $frm->show();
        //------------------------------------------------------------------------------
        //------------------------------------------------------------------------------
        //------------------------------------------------------------------------------
        $this->form->addContent( [new TLabel('Adianti - 1 campo', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl1id1')], [new TEntry('adl1id1') ] );

        $this->form->addContent( [new TLabel('Adianti - 1 campo campo sobre', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl2id1'), new TEntry('adl2id1') ] );


        $this->form->addContent( [new TLabel('Adianti - 2 campos', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl3id1')], [new TEntry('adl3id1') ]
                               ,[ new TLabel('adl3id2')], [new TEntry('adl3id2') ]
                              );

        $this->form->addContent( [new TLabel('Adianti - 2 campos campo sobre', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl4id1'), new TEntry('adl4id1') ] 
                              , [ new TLabel('adl4id2'), new TEntry('adl4id2') ] 
                              );

        $this->form->addContent( [new TLabel('Adianti - 3 campos', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl5id1')], [new TEntry('adl5id1') ]
                               ,[ new TLabel('adl5id2')], [new TEntry('adl5id2') ]
                               ,[ new TLabel('adl5id3')], [new TEntry('adl5id3') ]
                            );

        $this->form->addContent( [new TLabel('Adianti - 3 campos campo sobre', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl6id1'), new TEntry('adl6id1') ] 
                              , [ new TLabel('adl6id2'), new TEntry('adl6id2') ] 
                              , [ new TLabel('adl6id3'), new TEntry('adl6id3') ] 
                            );

        $this->form->addContent( [new TLabel('Adianti - 4 campos', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl7id1')], [new TEntry('adl7id1') ]
                                ,[ new TLabel('adl7id2')], [new TEntry('adl7id2') ]
                                ,[ new TLabel('adl7id3')], [new TEntry('adl7id3') ]
                                ,[ new TLabel('adl7id4')], [new TEntry('adl7id4') ]
                            );

        $this->form->addContent( [new TLabel('Adianti - 4 campos campo sobre', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl8id1'), new TEntry('adl8id1') ] 
                                , [ new TLabel('adl8id2'), new TEntry('adl8id2') ] 
                                , [ new TLabel('adl8id3'), new TEntry('adl8id3') ] 
                                , [ new TLabel('adl8id4'), new TEntry('adl8id4') ] 
                            );

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