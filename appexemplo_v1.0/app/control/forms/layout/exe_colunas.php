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
        
        $frm->addGroupField('fdl2', 'FormDin - 1 campo, label sobre');
        $frm->addTextField('fdl2id1', 'fdl2d1:', 20, false, 20, 'FormDin',false, null, null, true);

        $frm->addGroupField('fdl3', 'FormDin - 2 campos');
        $frm->addTextField('fdl3id1', 'fdl3id1:', 20, false, 20, 'FormDin',false, null, null, false);
        $frm->addTextField('fdl3id2', 'fdl3id2:', 20, false, 20, 'FormDin',false, null, null, false);
        
        $frm->addGroupField('fdl4', 'FormDin - 2 campo, label sobre');
        $frm->addTextField('fdl4id1', 'fdl4d1:', 20, false, 20, 'FormDin',false, null, null, true);
        $frm->addTextField('fdl4id2', 'fdl4d2:', 20, false, 20, 'FormDin',false, null, null, true);

        $frm->addGroupField('fdl5', 'FormDin - 3 campos');
        $frm->addTextField('fdl5id1', 'fdl5id1:', 20, false, 20, 'FormDin',false, null, null, false);
        $frm->addTextField('fdl5id2', 'fdl5id2:', 20, false, 20, 'FormDin',false, null, null, false);
        $frm->addTextField('fdl5id3', 'fdl5id3:', 20, false, 20, 'FormDin',false, null, null, false);
        
        $frm->addGroupField('fdl6', 'FormDin - 3 campo, label sobre');
        $frm->addTextField('fdl6id1', 'fdl6d1:', 20, false, 20, 'FormDin',false, null, null, true);
        $frm->addTextField('fdl6id2', 'fdl6d2:', 20, false, 20, 'FormDin',false, null, null, true);
        $frm->addTextField('fdl6id3', 'fdl6d3:', 20, false, 20, 'FormDin',false, null, null, true);

        $frm->addGroupField('fdl7', 'FormDin - 4 campos');
        $frm->addTextField('fdl7id1', 'fdl7id1:', 20, false, 20, 'FormDin',false, null, null, false);
        $frm->addTextField('fdl7id2', 'fdl7id2:', 20, false, 20, 'FormDin',false, null, null, false);
        $frm->addTextField('fdl7id3', 'fdl7id3:', 20, false, 20, 'FormDin',false, null, null, false);
        $frm->addTextField('fdl7id4', 'fdl7id4:', 20, false, 20, 'FormDin',false, null, null, false);
        
        $frm->addGroupField('fdl8', 'FormDin - 4 campo, label sobre');
        $frm->addTextField('fdl8id1', 'fdl8d1:', 20, false, 20, 'FormDin',false, null, null, true);
        $frm->addTextField('fdl8id2', 'fdl8d2:', 20, false, 20, 'FormDin',false, null, null, true);
        $frm->addTextField('fdl8id3', 'fdl8d3:', 20, false, 20, 'FormDin',false, null, null, true);
        $frm->addTextField('fdl8id4', 'fdl8d4:', 20, false, 20, 'FormDin',false, null, null, true);


        $this->form = $frm->show();
        //------------------------------------------------------------------------------
        //------------------------------------------------------------------------------
        //------------------------------------------------------------------------------
        $this->form->addContent( [new TLabel('Adianti - 1 campo', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl1id1')], [new TEntry('adl1id1') ] );

        $this->form->addContent( [new TLabel('Adianti - 1 campo, label sobre', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl2id1'), new TEntry('adl2id1') ] );


        $this->form->addContent( [new TLabel('Adianti - 2 campos', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl3id1')], [new TEntry('adl3id1') ]
                               ,[ new TLabel('adl3id2')], [new TEntry('adl3id2') ]
                              );

        $this->form->addContent( [new TLabel('Adianti - 2 campos, label sobre', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl4id1'), new TEntry('adl4id1') ] 
                              , [ new TLabel('adl4id2'), new TEntry('adl4id2') ] 
                              );

        $this->form->addContent( [new TLabel('Adianti - 3 campos', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl5id1')], [new TEntry('adl5id1') ]
                               ,[ new TLabel('adl5id2')], [new TEntry('adl5id2') ]
                               ,[ new TLabel('adl5id3')], [new TEntry('adl5id3') ]
                            );

        $this->form->addContent( [new TLabel('Adianti - 3 campos, label sobre', '#5A73DB', 12, '')] );
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

        $this->form->addContent( [new TLabel('Adianti - 4 campos, label sobre', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl8id1'), new TEntry('adl8id1') ] 
                                , [ new TLabel('adl8id2'), new TEntry('adl8id2') ] 
                                , [ new TLabel('adl8id3'), new TEntry('adl8id3') ] 
                                , [ new TLabel('adl8id4'), new TEntry('adl8id4') ] 
                            );

        $this->form->addContent( [new TLabel('Adianti - 5 campos', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl9id1')], [new TEntry('adl9id1') ]
                                ,[ new TLabel('adl9id2')], [new TEntry('adl9id2') ]
                                ,[ new TLabel('adl9id3')], [new TEntry('adl9id3') ]
                                ,[ new TLabel('adl9id4')], [new TEntry('adl9id4') ]
                                ,[ new TLabel('adl9id5')], [new TEntry('adl9id5') ]
                            );

        $this->form->addContent( [new TLabel('Adianti - 5 campos, label sobre', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl10id1'), new TEntry('adl10id1') ] 
                                , [ new TLabel('adl10id2'), new TEntry('adl10id2') ] 
                                , [ new TLabel('adl10id3'), new TEntry('adl10id3') ] 
                                , [ new TLabel('adl10id4'), new TEntry('adl10id4') ] 
                                , [ new TLabel('adl10id5'), new TEntry('adl10id5') ] 
                            );

        $this->form->addContent( [new TLabel('Adianti - 6 campos', '#5A73DB', 12, '')] );
        $this->form->addFields( [ new TLabel('adl11id1')], [new TEntry('adl11id1') ]
                                ,[ new TLabel('adl11id2')], [new TEntry('adl11id2') ]
                                ,[ new TLabel('adl11id3')], [new TEntry('adl11id3') ]
                                ,[ new TLabel('adl11id4')], [new TEntry('adl11id4') ]
                                ,[ new TLabel('adl11id5')], [new TEntry('adl11id5') ]
                                ,[ new TLabel('adl11id6')], [new TEntry('adl11id6') ]
                            );

        $this->form->addContent( [new TLabel('Adianti - 6 campos, label sobre', '#5A73DB', 12, '')] );
        $this->form->addFields(   [ new TLabel('adl12id1'), new TEntry('adl12id1') ] 
                                , [ new TLabel('adl12id2'), new TEntry('adl12id2') ] 
                                , [ new TLabel('adl12id3'), new TEntry('adl12id3') ] 
                                , [ new TLabel('adl12id4'), new TEntry('adl12id4') ] 
                                , [ new TLabel('adl12id5'), new TEntry('adl12id5') ] 
                                , [ new TLabel('adl12id6'), new TEntry('adl12id6') ] 
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