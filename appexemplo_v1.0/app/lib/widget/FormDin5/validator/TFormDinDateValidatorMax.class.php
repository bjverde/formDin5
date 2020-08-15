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
        
        /*
        $year_pos  = strpos($mask, 'yyyy');
        $month_pos = strpos($mask, 'mm');
        $day_pos   = strpos($mask, 'dd');
        $hour_pos  = strpos($mask, 'hh');
        $min_pos   = strpos($mask, 'ii');
        
        $year      = substr($value, $year_pos, 4);
        $month     = substr($value, $month_pos, 2);
        $day       = substr($value, $day_pos, 2);
        $hour      = substr($value, $hour_pos, 2);
        $mim       = substr($value, $min_pos, 2);
        */

        /*
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone(self::DEFAULT_TIME_ZONE));
        $retorno = $dateTime->format($format);
        $interval = $dtSaida->diff($dtHoje); //If Date is in past then invert will 1
        if($interval->invert == 1){
            $horas24EmMinutos = 24*60;
            $minutos = $interval->days*24*60 + $interval->h*60 + $interval->i;
            $condicaoHojeMaisRecenteQueSaida = ($minutos <= $horas24EmMinutos);;
        }
        */
    }
}
?>
