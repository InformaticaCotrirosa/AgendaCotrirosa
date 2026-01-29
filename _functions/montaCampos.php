

<?php
    

    class linha {
        public $aCmpLin;

        
        function addSpan($aIdeCmp,$aClsCmp,$aValCmp){
            
            $aCmpTmp = new Campos();
            $aCmpTmp->aEleCmp = "SPAN";
            $aCmpTmp->aIdeCmp = $aIdeCmp;
            $aCmpTmp->aClsCmp = $aClsCmp;
            $aCmpTmp->aValCmp = $aValCmp;
            $aCmpTmp->aOptCmp = "";
            $aCmpTmp->aOvaCmp = false;
            $aCmpTmp->aKeyCmp = false;
            $aCmpTmp->aFocCmp = false;
            $aCmpTmp->aTypCmp = "";
            $aCmpTmp->aBusCmp = "";
            $this->aCmpLin[] = $aCmpTmp;
            
        }
        
                           //(ID,CLASS DIV,VALOR,KEYUPEVENT,FOCUSOUTEVENT,TYPE,PARBUSCBUT)
        function addInput($aIdeCmp,$aClsCmp,$aValCmp,$aKeyCmp,$aFocCmp,$aTypCmp,$aBusCmp){
   
            $aCmpTmp = new Campos();
            $aCmpTmp->aEleCmp = "INPUT";
            $aCmpTmp->aIdeCmp = $aIdeCmp;
            $aCmpTmp->aClsCmp = $aClsCmp;
            $aCmpTmp->aValCmp = $aValCmp;
            $aCmpTmp->aOptCmp = "";
            $aCmpTmp->aOvaCmp = false;
            $aCmpTmp->aKeyCmp = $aKeyCmp;
            $aCmpTmp->aFocCmp = $aFocCmp;
            $aCmpTmp->aTypCmp = $aTypCmp;
            $aCmpTmp->aBusCmp = $aBusCmp;
            $this->aCmpLin[] = $aCmpTmp;

        }
                          // (ID,CLASS DIV,VALOR,LISTA,VAZIO ELEM,KEYUPEVENT,FOCUSOUTEVENT) 
        function addSelect($aIdeCmp,$aClsCmp,$aValCmp,$aOptCmp,$aOvaCmp,$aKeyCmp,$aFocCmp){

            $aCmpTmp = new Campos();
            $aCmpTmp->aEleCmp = "SELECT";
            $aCmpTmp->aIdeCmp = $aIdeCmp;
            $aCmpTmp->aClsCmp = $aClsCmp;
            $aCmpTmp->aValCmp = $aValCmp;
            $aCmpTmp->aOptCmp = $aOptCmp;
            $aCmpTmp->aOvaCmp = $aOvaCmp;
            $aCmpTmp->aKeyCmp = $aKeyCmp;
            $aCmpTmp->aFocCmp = $aFocCmp;
            $aCmpTmp->aTypCmp = "";
            $aCmpTmp->aBusCmp = "";
            $this->aCmpLin[] = $aCmpTmp;
      
        }

        function montaHtml($aClsLin){
          
            $aEleDiv = "<div ";
            if($aClsCmp !== ""){
                $aEleDiv .= " class='$aClsLin' ";
            }
            $aEleDiv .= ">";

            foreach ($this->aCmpLin as $aCmpPrc) {	
				$aEleDiv .=  $aCmpPrc->montaHtml();
			}

            $aEleDiv .=  "</div>";

			return $aEleDiv;	
         
        }
        
    }

    class campos{
        
        public $aEleCmp;
        public $aIdeCmp;
        public $aClsCmp;
        public $aValCmp;
        public $aOptCmp; // RETURN OPTION();
        public $aKeyCmp; //TRUE/FALSE
        public $aFocCmp; //TRUE/FALSE
        public $aTypCmp;
        public $aBusCmp;
        public $aOvaCmp; //TRUE/FALSE

        function montaHtml(){

            //if (isset($_POST['BSalvar']) && isset($_POST[$aIdeCmp])){ $aValCmp = $_POST[$aIdeCmp]; }
            
            $armazPost = unserialize($_SESSION['armazPost']);
            $cUsuCad = unserialize($_SESSION['userCadastro']);
            $aDisCmp = $cUsuCad->getPermissaoCampo($this->aIdeCmp);
            $aElePrc = "";
            $aDisPrc = "";

            //if(isset($armazPost.get($aIdeCmp))){ $aValCmp = $armazPost.get($aIdeCmp); }
         
            
            if($this->aKeyCmp){
                $aElePrc .= " onkeyup='keyup_$this->aIdeCmp()' ";
            }
            if($this->aFocCmp){
                $aElePrc .= " onfocusout='focout_$this->aIdeCmp()' ";
            }
            if($this->aTypCmp != ""){
                $aElePrc .= " type='$this->aTypCmp' ";
            }
            
            if(!$aDisCmp){
                $aDisPrc = " disabled ";
            }
            
       
           
            if($this->aEleCmp == "INPUT"){
                
            
                $aElePrc = "<input id='$this->aIdeCmp' name='$this->aIdeCmp'  class='campoValor $aDisPrc' " . $aElePrc;

                if($this->aValCmp !== ""){
                    $aElePrc .= " value='$this->aValCmp' ";
                }

                $aElePrc .= "> ";

                if($this->aBusCmp !== ""){
                   $aElePrc .="<a  id='busca_$this->aElePrc' class='button popupBuscaBtn  $aDisPrc' onclick='openPopupBusca($this->aBusCmp)' ><img src='../_imagens/search.png'></a>";
                }
            
            }

            
            if($this->aEleCmp == "SELECT"){
                $aElePrc = "<select id='$this->aIdeCmp' name='$this->aIdeCmp' " . $aElePrc . " class='campoValor $aDisPrc'>";
                if($this->aOptCmp !== ""){
                    $aElePrc .= optionLista($this->aOptCmp,$this->aValCmp,$this->aOvaCmp);
                }
                $aElePrc .= "</select>";
            }

            

            if($this->aEleCmp == "SPAN"){
                $aElePrc = "<span ";
                if($this->aIdeCmp !== ""){
                    $aElePrc .= " id= '$this->aIdeCmp' name='$this->aIdeCmp' class='descricaoRetorno'";
                }
                $aElePrc .= ">$this->aValCmp</span>";
            }

            $aEleDiv = "<div ";
            if($aClsCmp !== ""){
                $aEleDiv .= " class='$this->aClsCmp' ";
            }
            $aEleDiv = $aEleDiv . ">" . $aElePrc . "</div>";

            return $aEleDiv;
            
        }
    }


?>