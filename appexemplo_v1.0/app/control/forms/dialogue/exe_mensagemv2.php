<?php

use Adianti\Registry\TSession;

class exe_mensagemv2 extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin('Exemplo Mensagem apenas PHP');
        //$text[] = 'linha1';
        //$text[] = "linha2 <b>'xxx'</b> linha2";
        //$frm->setMessage($text,TFormDinMessage::TYPE_WARING);

        $frm->addButton($this,'Msg Alert', null, 'msgAlert',  null, null, true, false);
        $frm->addButton($this,'Msg Info', null, 'msgInfo',  null, null, true, false);
        $frm->addButton($this,'Msg Error', null, 'msgError',  null, null, true, false);
        //$frm->addButton($this,'Msg POP Sucesso', null, 'msgpopsu',  null, null, false, false);
        //$frm->addButton($this,'Msg POP Error', null, 'msgpoperror', null, null, false, false);
        //$frm->addButton($this,'Msg POP Attention', null, 'msgpopattention', null, null, false, false);
        
        $this->form = $frm->show();

        //$this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));

        // add form actions
        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        //$this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:check-circle green');
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

    public function msgAlert()
    {
        $text[] = 'linha1 de mensagem em HTML';
        $text[] = "linha2 <b>'xxx'</b> linha2";
        $text = TFormDinMessage::messageTransform($text);
        new TMessage(TFormDinMessage::TYPE_WARING, $text);
        //new TMessage(TFormDinMessage::TYPE_WARING, AdiantiCoreTranslator::translate('Record saved'));
    }

    public function msgInfo()
    {
        $text[] = 'linha1 de mensagem em HTML';
        $text[] = "linha2 <b>'xxx'</b> linha2";
        $text = TFormDinMessage::messageTransform($text);
        new TMessage(TFormDinMessage::TYPE_INFO, $text);
    }

    public function msgError()
    {
        $text[] = 'linha1 de mensagem em HTML <span style="color:red">falando que deu MMM</span>';
        $text[] = "linha2 <b>'xxx'</b> linha2";
        $text = TFormDinMessage::messageTransform($text);
        new TMessage(TFormDinMessage::TYPE_ERROR, $text);
    }

}