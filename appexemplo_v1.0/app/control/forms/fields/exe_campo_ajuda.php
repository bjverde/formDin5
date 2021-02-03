<?php
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
        $frm->setMaximize(true);
        $frm->setHelpOnLine('Titulo', $strHeight, $strWidth, 'ajuda', null);
        
        $frm->addHtmlField('html1', 'Exemplo de ajuda utilizando boxField com arquivo.')->setCss('color', 'blue');
        $frm->addTextField('nome', 'Nome:', 50, false, 50, '', true, null, null, false);
        //$frm->addBoxField('bxNome', null, 'ajuda/ajuda.html', 'ajax', null, null, null, null, null, 'Ver arquivo de ajuda');
        
        $frm->addHtmlField('html2', 'Exemplo de ajuda utilizando boxField com texto.')->setCss('color', 'blue');
        $frm->addTextField('nome2', 'Nome:', 50, false, 50, '', true, null, null, false);
        //$frm->addBoxField('bxNome2', 'Informe o nome completo do proprietário do terreno', null, null, null, null, null, null, null, 'Ver texto de ajuda');
        
        
        $frm->addHtmlField('html3', 'Exemplo de ajuda utilizando boxField com imagem.')->setCss('color', 'blue');
        //$frm->addBoxField('bxImagem', null, $frm->getBase()."js/jquery/facebox/stairs.jpg", null, 'Visualizar Foto:', 'folder.gif', true, null, null, 'Imagem');
        
        
        $frm->addHtmlField('html4', 'Exemplo de ajuda utilizando setHelpOnLine.')->setCss('color', 'blue');
        $frm->addTextField('endereco01', 'Endereço01:', 50, false, 50, '', true, null, null, false)->setHelpOnLine('Como preencher o campo Endereço ?', 300, 800, 'Informe o endereço completo do proprietário do terreno', null, null, false);
        $frm->addTextField('endereco02', 'Endereço02:', 50, false, 50, '', true, null, null, false)->setHelpOnLine('Como preencher o campo Endereço ?', 300, 800, 'ajuda/ajuda.html');
        //$frm->addBoxField('bxAjax', null, "http://localhost", 'ajax', 'Visualizar Ajax:', null, true, null, null, 'Conteudo ajax');
        


        TPage::include_css('app/resources/styles.css');
        $this->html = new THtmlRenderer('app/resources/coming_soon.html');

        // define replacements for the main section
        $replace = array();
        
        // replace the main section variables
        $this->html->enableSection('main', $replace);
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->html);
        
        // add the build to the page
        parent::add($container);
    }
}
