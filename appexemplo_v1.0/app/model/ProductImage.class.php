<?php
/**
 * Product Active Record
 * @author  Pablo Dall'Oglio
 */
class ProductImage extends TRecord
{
    const TABLENAME = 'product_image';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        
        parent::addAttribute('product_id');
        parent::addAttribute('image');
    }
}
