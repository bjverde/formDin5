<?php

use Adianti\Registry\TSession;

class exe_video extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();



        $frm = new TFormDin($this,'Exemplo Video HTML5');

        $frm->addGroupField('g1', 'Video Basico, sempre tem controle');
        $frm->addVidoHtml5( 'v1','Vídeo exemplo',null,null,'app/images/mov_bbb.mp4');
        
        $frm->addGroupField('g2', 'Video Basico, SEM controle');
        $frm->addVidoHtml5( 'v2','Vídeo exemplo',null,null,'app/images/mov_bbb.mp4',false);

        $frm->addGroupField('g3', 'Video Basico, SEM controle');
        $frm->addVidoHtml5( 'v3','Vídeo exemplo',null,null,'app/images/mov_bbb.mp4',false,false,false);

        $this->form = $frm->show();
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