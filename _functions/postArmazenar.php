
<?php

    class postValores {
        public $campo;
        public $valor;
    }

    class armazPost {
        public $armazenamento;

        function add($campo,$valor){
            
            $postEst = new postValores();
            $postEst->campo = $campo;
            $postEst->valor = $valor;
            
            $this->armazenamento[] = $postEst;
        }

        function get($campo){
            
            foreach ($arr as $this->armazenamento) {
                if($arr->campo == $campo){
                    return $arr->valor;
                }
            }
            
            return null;
        }
    }

?>