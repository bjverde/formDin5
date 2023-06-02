<?php
/**
 * DocumentationView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class DocumentationView extends TPage
{
    private $label;
    private $source;
    protected $form; // registration form
    
    public function __construct()
    {
        // parent classs constructor
        parent::__construct();

        $class = empty(TSession::getValue('classCode'))?'':TSession::getValue('classCode');
        // creates form
        $this->form = new BootstrapFormBuilder('form');
        $this->form->setFormTitle('Source Code class: '.$class);

        $objhtml  = new TElement('div');
        $html = '<a href="index.php?class='.$class.'">Voltar para a Class:'.$class.'</a>';
        $objhtml->add($html);
        $this->form->addFields( [$objhtml]);
        
        $config = AdiantiApplicationConfig::get();
        ini_set('highlight.comment', $config['highlight']['comment']);
        ini_set('highlight.default', $config['highlight']['default']);
        ini_set('highlight.html',    $config['highlight']['html']);
        ini_set('highlight.keyword', $config['highlight']['keyword']);
        ini_set('highlight.string',  $config['highlight']['string']);
        
        // scroll to put the source inside
        $wrapper = new TElement('div');
        $wrapper->class = 'sourcecodewrapper';
        
        $this->source = new TSourceCode;
        $this->source->generateRowNumbers();
        $wrapper->add($this->source);
        //parent::add($wrapper);
        $this->form->addContent([$wrapper]);

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width:100%';
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }
    
    /**
     * Method onHelp
     * Shows the source code for this sample, along with an explanation
     */
    function onHelp($param)
    {
        if (isset($param['classname']) AND $param['classname'])
        {
            $folder    = 'app/control';
            $classname = TSession::getValue('classCode');
            $list = new RecursiveDirectoryIterator($folder);
            $it   = new RecursiveIteratorIterator($list,RecursiveIteratorIterator::SELF_FIRST);

            foreach ($it as $entry){
                if ( strpos($entry, $classname) !== false ) {
                    $this->source->loadFile("$entry");
                    return;
                }
            }
        } else {
            $this->source->loadFile('index.php');
        }
    }
}
