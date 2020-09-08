<?php
/**
 * ViewSales Active Record
 * @author  <your-name-here>
 */
class ViewSales extends TRecord
{
    const TABLENAME  = 'view_sales';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}
    
    /**
	CREATE VIEW view_sales AS
	SELECT id,         name,
		   address,    phone,
		   birthdate,  status,
		   email,      gender,
		   city_id,    category_id,
	  (SELECT sum(total)
	   FROM sale
	   WHERE customer_id = customer.id ) AS total,

	  (SELECT max(date)
	   FROM sale
	   WHERE customer_id = customer.id ) AS last_date
	FROM customer
     */
     
     
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('address');
        parent::addAttribute('phone');
        parent::addAttribute('birthdate');
        parent::addAttribute('status');
        parent::addAttribute('email');
        parent::addAttribute('gender');
        parent::addAttribute('city_id');
        parent::addAttribute('category_id');
        parent::addAttribute('total');
        parent::addAttribute('last_date');
    }


}
