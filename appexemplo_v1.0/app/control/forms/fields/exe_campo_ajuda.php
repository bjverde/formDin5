<?php

use Adianti\Widget\Form\TLabel;

class exe_campo_ajuda extends TPage
{
    private $html;
    
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        $frm = new TFormDin($this,'Campos com campo Ajuda e campo BoxField');
        //$frm->setMaximize(true);
        //$frm->setHelpOnLine('Titulo', $strHeight, $strWidth, 'ajuda', null);
        
        $frm->addHtmlField('html1', 'Exemplo de ajuda utilizando boxField com arquivo.')->setCss('color', 'blue');
        $frm->addTextField('nome', 'Nome:', 50, false, 50, '', true, null, null, false);
        
        $label = new TLabel('xxx');

        FormDinHelper::debug( $label );

        //$frm->addBoxField('bxNome', null, 'ajuda/ajuda.html', 'ajax', null, null, null, null, null, 'Ver arquivo de ajuda');
        
        //$frm->addHtmlField('html2', 'Exemplo de ajuda utilizando boxField com texto.')->setCss('color', 'blue');
        //$frm->addTextField('nome2', 'Nome:', 50, false, 50, '', true, null, null, false);
        //$frm->addBoxField('bxNome2', 'Informe o nome completo do proprietário do terreno', null, null, null, null, null, null, null, 'Ver texto de ajuda');
        
        
        //$frm->addHtmlField('html3', 'Exemplo de ajuda utilizando boxField com imagem.')->setCss('color', 'blue');
        //$frm->addBoxField('bxImagem', null, $frm->getBase()."js/jquery/facebox/stairs.jpg", null, 'Visualizar Foto:', 'folder.gif', true, null, null, 'Imagem');
        
        
        //$frm->addHtmlField('html4', 'Exemplo de ajuda utilizando setHelpOnLine.')->setCss('color', 'blue');
        //$frm->addTextField('endereco01', 'Endereço01:', 50, false, 50, '', true, null, null, false)->setHelpOnLine('Como preencher o campo Endereço ?', 300, 800, 'Informe o endereço completo do proprietário do terreno', null, null, false);
        //$frm->addTextField('endereco02', 'Endereço02:', 50, false, 50, '', true, null, null, false)->setHelpOnLine('Como preencher o campo Endereço ?', 300, 800, 'ajuda/ajuda.html');
        //$frm->addBoxField('bxAjax', null, "http://localhost", 'ajax', 'Visualizar Ajax:', null, true, null, null, 'Conteudo ajax');
        

        $this->form = $frm->show();


        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));


        // creates the page structure using a table
        $formDinBreadCrumb = new TFormDinBreadCrumb(__CLASS__);
        $vbox = $formDinBreadCrumb->getAdiantiObj();
        $vbox->add($this->form);
        
        // add the table inside the page
        parent::add($vbox);
    }
}
