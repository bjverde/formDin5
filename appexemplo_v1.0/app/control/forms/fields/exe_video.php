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
        $frm->addHiddenField('id'); //POG para evitar problema de noticie
        $frm->addVideoHtml5( 'v1','Vídeo exemplo',null,null,'app/images/mov_bbb.mp4');        
        $frm->addVideoHtml5( 'v2','Vídeo sem controle',null,null,'app/images/mov_bbb.mp4',false);

        $frm->addGroupField('g3', 'Atenção especial com autoplay');
        $frm->addVideoHtml5( 'v3','Vídeo com controle, autoplay e loop',null,true,'app/images/mov_bbb.mp4',true,true,true);
        https://developer.chrome.com/blog/autoplay/
        $msg = '<b>Teve uma mudança com autoplay do vídeo e audio</b>.'
              .'<br>Não é possível deixar o autoplay ligado com audio, então só ira funcionar com muted ligado'
              .'<br><a href="https://developer.chrome.com/blog/autoplay/">Autoplay policy in Chrome</a>';
        $frm->addHtmlField('html1',$msg, null, 'Dica:', null, 200);
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