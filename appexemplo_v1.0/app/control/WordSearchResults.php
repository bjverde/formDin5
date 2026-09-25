<?php
/**
 * WordSearch Results
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-tutor
 */
class WordSearchResults extends TPage
{
    /**
     * Load search results
     */
    public function onLoad($param)
    {
        $extensions = ['.class.php', '.php'];
        if (!empty($param['input']))
        {
            $entries = array();
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/control'),
                                                       RecursiveIteratorIterator::CHILD_FIRST) as $arquivo)
            {
                foreach ($extensions as $extension)
                {
                    if ( (substr($arquivo, strlen($extension)*-1) == $extension) && strpos($arquivo, 'dbsamples') == false)
                    {
                        $content = file_get_contents($arquivo);
                        if (strpos($content, $param['input']) !== false)
                        {
                            $basename = $arquivo->getBaseName($extension);
                            if (strpos($basename, '.') == false)
                            {
                                $entries[] = $basename;
                            }
                        }
                    }
                }
            }
            
            $datagrid = new BootstrapDatagridWrapper( new TDataGrid );
            $datagrid->disableHtmlConversion();
            $datagrid->width = '100%';
            
            $datagrid->addColumn( new TDataGridColumn('path', _t('Path'), 'left', '90%') );
            
            $action1 = new TDataGridAction([$this, 'onOpen'], ['controller' => '{controller}' ] );
            $datagrid->addAction($action1, _t('Open'), 'far:folder blue');
            
            $action1->setUseButton(true);
            $datagrid->createModel(false);
            
            $parser = new TMenuParser('menu.xml');
            foreach ($entries as $controller)
            {
                $path = $parser->getPath($controller);
                if ($path)
                {
                    $object = new stdClass;
                    $object->controller = $controller;
                    $object->path       = implode(' <i class="fa fa-angle-double-right" aria-hidden="true"></i> ', $path);
                    $datagrid->addItem($object);
                }
            }
            
            $panel = new TPanelGroup( _t('Results') );
            $panel->add($datagrid);
            parent::add($panel);
        }
    }
    
    /**
     * Open controller
     */
    public static function onOpen($param)
    {
        AdiantiCoreApplication::loadPage($param['controller']);
    }
}
