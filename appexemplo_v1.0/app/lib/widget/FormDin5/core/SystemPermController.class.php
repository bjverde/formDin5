<?php
class SystemPermController
{
    private static $database = Constantes::DATABASE_PERMISSION;

    public function __construct(){
    }
    //--------------------------------------------------------------------------------    
    /**
     * Cria um combo de usuários para usar em formulários compativel com diferentes versões do Adianti
     * @param string $idCombo - ID do combo
     * @return TDBCombo | null
     */
    public static function getTDBComboUser(string $idCombo, TCriteria|null $criteria=null){
        $combo = null;
        if (class_exists('SystemUser')) {
            $combo = new TDBCombo($idCombo, Constantes::DATABASE_PERMISSION, 'SystemUser', 'id', '{name}','name asc' , $criteria);
            $combo->enableSearch();
            $combo->setSize('100%');        
        }
        if (class_exists('SystemUsers')) {
            $combo = new TDBCombo($idCombo, Constantes::DATABASE_PERMISSION, 'SystemUsers', 'id', '{name}','name asc' , $criteria);
            $combo->enableSearch();
            $combo->setSize('100%');
        }
        return $combo; // retorna o objeto salvo
    }
    //--------------------------------------------------------------------------------
    /**
     * Busca usuário por ID, compatível com diferentes versões do Adianti
     * @param int $system_user_id - ID do usuário
     * @return object | null
     */        
    public static function getUserById($system_user_id){
        try{
            $conn = TTransaction::open(self::$database); // open transaction
            $usuario = null;
            if (class_exists('SystemUser')) {
                $usuario = SystemUser::find($system_user_id);
            }
            if (class_exists('SystemUsers')) {
                $criteria = new TCriteria;
                $criteria->add(new TFilter('id', '=', $system_user_id));

                $repository = new TRepository('SystemUsers');
                $objects = $repository->load($criteria);
                $usuario = $objects ? $objects[0] : null;
            }
            TTransaction::close();
            return $usuario; // retorna o objeto salvo
        }catch (Exception $e) {
            TTransaction::rollback();
            throw $e;
        }
    }    
    public static function getNomeUserById($system_user_id){
        try{
            if( is_numeric($system_user_id) && $system_user_id>0 ){
                $usuario = SystemPermController::getUserById($system_user_id);
                if(is_object($usuario) && isset($usuario->name)){
                    return $usuario->name; // retorna o objeto salvo
                }else{
                    return '';
                }
            }else{
                return '';
            }
        }catch (Exception $e) {
            TTransaction::rollback();
            throw $e;
        }
    }
    //--------------------------------------------------------------------------------    
    public static function getUnitById($system_unit_id){
        try{
            $conn = TTransaction::open(self::$database); // open transaction
            $unidade = SystemUnit::find($system_unit_id);
            TTransaction::close();
            return $unidade; // retorna o objeto salvo
        }catch (Exception $e) {
            TTransaction::rollback();
            throw $e;
        }
    }
    //--------------------------------------------------------------------------------    
    public static function getNomeUnitById($system_unit_id){
        try{
            $unidade = SystemPermController::getUnitById($system_unit_id);
            return $unidade->name; // retorna o objeto salvo
        }catch (Exception $e) {
            TTransaction::rollback();
            throw $e;
        }
    }
}//fim classe