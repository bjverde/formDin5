<?php
/**
 * Date validation Max Date
 *
 * @version    1.0
 * @package    validator
 * @author     Reinaldo A. Barreto Jr
 */
class TFormDinDateValidatorMax extends TFieldValidator
{
    /**
     * Validate a given value
     * @param $label Identifies the value to be validated in case of exception
     * @param $value Value to be validated
     * @param $parameters aditional 0=>MaskDate, 1=>MaxLimitDate
     */
    public function validate($label, $value, $parameters = NULL)
    {
        $defaultMask = 'yyyy-mm-dd hh:ii';
        $maskDat = $parameters[0];
        $maxLimitDate = $parameters[1];

        $dateValue    = TDateTime::convertToMask($value, $maskDat, $defaultMask);
        $maxLimitDate = TDateTime::convertToMask($maxLimitDate, $maskDat, $defaultMask);

        $dateValue = new DateTime($dateValue);
        $maxLimitDate = new DateTime($maxLimitDate);

        $interval = $dateValue->diff($maxLimitDate); //If Date is in past then invert will 1
        if($interval->invert == 1){
            throw new InvalidArgumentException("A data do campo $label não pode ser posterior a data $parameters[1]");
        }    
    }
}
?>
