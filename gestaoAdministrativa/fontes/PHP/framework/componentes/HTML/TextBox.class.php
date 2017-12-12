<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
* Gerar o componente tipo text de acordo com os valores setados pelo Usuário
* Data de Criação: 05/02/2004

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Diego Barbosa Victoria

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Gera o componente tipo text de acordo com os valores setados pelo Usuário
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Documentor: Diego Barbosa Victoria

    * @package Interface
    * @subpackage Componente
*/
class TextBox extends Componente
{
//PROPRIEDADES DA CLASSE
/**
    * @access Private
    * @var Integer
*/
var $inMaxLength;

/**
    * @access Private
    * @var Integer
*/
var $inMinLength;//DETERMINA SE O CAMPO TERA UM NUMERO MINIMO DE CARACTERES

/**
    * @access Private
    * @var Integer
*/
var $inSize;

/**
    * @access Private
    * @var Boolean
*/
var $boInteiro;

/**
    * @access Private
    * @var Boolean
*/
var $boFloat;

/**
    * @access Private
    * @var Integer
*/
var $inDecimais;

/**
    * @access Private
    * @var Char
*/
var $chDecimal;//CARACTER DO DECIMAL

/**
    * @access Private
    * @var Char
*/
var $chMilhar;//CARACTER DO MILHAR

/**
    * @access Private
    * @var String
*/
var $stAlign;
/**
    * @access Private
    * @var String
*/
var $stMascara;
/**
    * @access Private
    * @var String
*/
var $stPreencheComZeros;
/**
    * @access Private
    * @var Boolean
*/
var $boAlfaNumerico;
/**
    * @access Private
    * @var Boolean
*/
var $boAcento;
/**
    * @access Private
    * @var Boolean
*/
var $boCaracteres;
/**
    * @access Private
    * @var String
*/
var $stCaracteres;
/**
    * @access Private
    * @var Boolean
*/
var $boEspacosExtras;
/**
    * @access Private
    * @var Boolean
*/
var $boCaracteresAceitos;
/**
    * @access Private
    * @var String
*/
var $stExpRegCaracteresAceitos;
/**
    * @access Private
    * @var Array
*/
var $arValoresBusca;
/**
    * @access Private
    * @var Array
*/
var $stAutoComplete;

/**
    * @access Private
    * @var Boolean
*/

var $boLabel;
/**
    * @access Public
    * @param Integer $valor
*/

function setMaxLength($valor) { $this->inMaxLength  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setMinLength($valor) { $this->inMinLength  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setSize($valor) { $this->inSize       = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setDecimais($valor) { $this->inDecimais   = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setInteiro($valor) { $this->boInteiro    = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setFloat($valor) { $this->boFloat      = $valor; }
/**
    * @access Public
    * @param Char $valor
*/
function setDecimal($valor) { $this->chDecimal    = $valor; }
/**
    * @access Public
    * @param Char $valor
*/
function setMilhar($valor) { $this->chMilhar     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setAlign($valor) { $this->stAlign      = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setToLowerCase($valor) { $this->boToLowerCase= $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setToUpperCase($valor) { $this->boToUpperCase= $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setPreencheComZeros($valor) { $this->stPreencheComZeros  = trim($valor); }
/**
    * @access Public
    * @param Boolean $valor
*/
function setAlfaNumerico($valor) { $this->boAlfaNumerico = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setAcento($valor) { $this->boAcento = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setAutoComplete($valor) { $this->stAutoComplete = $valor; }
/**
    * @access Public
    * @access Boolean $boolean
    * @param String $valor
*/
function setValidaCaracteres($boolean , $valor = "") { $this->boCaracteres = $boolean;
                                                        $this->stCaracteres = $valor;   }
/**
    * @access Public
    * @param Boolean $valor
*/
function setEspacosExtras($valor) { $this->boEspacosExtras = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setBoCaracteresAceitos($valor) { $this->boCaracteresAceitos = $valor; }
/**
    * @access Public
    * @access Boolean $boolean
    * @param String $valor
*/
function setCaracteresAceitos($valor = "")
{
   $this->setBoCaracteresAceitos( true );
   $this->stExpRegCaracteresAceitos = $valor;
}
/**
    * @access Public
    * @param String $stPaginaBusca
    * @param String $stNomForm
    * @param String $stTipoBusca
*/
function setValoresBusca($stPaginaBusca, $stNomForm = 'frm' ,$stTipoBusca = '')
{
    $this->arValoresBusca = array( 'stPaginaBusca' => $stPaginaBusca, 'stNomForm' => $stNomForm, 'stTipoBusca' => $stTipoBusca );
}
/**
    * @access Public
    * @param String $stExpReg
*/
function setExpReg($stExpReg)
{
    $this->stExpReg = $stExpReg;
}
/**
    * @access Public
    * @param Boolean $valor
*/
function setLabel($valor) { $this->boLabel = $valor; }

/**
    * @access Private
    * @return Integer
*/
function getMaxLength() { return $this->inMaxLength;  }
/**
    * @access Private
    * @return Integer
*/
function getMinLength() { return $this->inMinLength;  }
/**
    * @access Private
    * @return Integer
*/
function getSize() { return $this->inSize;       }
/**
    * @access Private
    * @return Boolean
*/
function getInteiro() { return $this->boInteiro;    }
/**
    * @access Private
    * @return Integer
*/
function getDecimais() { return $this->inDecimais;   }
/**
    * @access Private
    * @return Boolean
*/
function getFloat() { return $this->boFloat;      }
/**
    * @access Private
    * @return Char
*/
function getDecimal() { return $this->chDecimal;    }
/**
    * @access Private
    * @return Char
*/
function getMilhar() { return $this->chMilhar;     }
/**
    * @access Private
    * @return String
*/
function getAlign() { return $this->stAlign;      }
/**
    * @access Private
    * @return Boolean
*/
function getToLowerCase() { return $this->boToLowerCase;}
/**
    * @access Private
    * @return Boolean
*/
function getToUpperCase() { return $this->boToUpperCase;}
/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara;  }
/**
    * @access Public
    * @return String
*/
function getPreencheComZeros() { return $this->stPreencheComZeros;  }
/**
    * @access Public
    * @return Boolean
*/
function getAlfaNumerico() { return $this->boAlfaNumerico;  }
/**
    * @access Public
    * @return Boolean
*/
function getValidaCaracteres() { return $this->boCaracteres;  }
/**
     * @access Public
     * @return Boolean
 */
function getStringCaracteres() { return $this->stCaracteres;  }
/**
    * @access Public
    * @return Boolean
*/
function getEspacosExtras() { return $this->boEspacosExtras;  }
/**
    * @access Public
    * @return Boolean $valor
*/
function getBoCaracteresAceitos() { return $this->boCaracteresAceitos; }
/**
    * @access Public
    * @return String $valor
*/
function getCaracteresAceitos() { return $this->stExpRegCaracteresAceitos; }
/**
    * @access Public
    * @return String $valor
*/
function getAutoComplete() { return $this->stAutoComplete           ; }
/**
    * @access Public
    * @return String $valor
*/
function getLabel() { return $this->boLabel;                    }
/**
    * @access Public
    * @return String $valor
*/
function getExpReg()
{
    return $this->stExpReg;
}
/**
    * Método Construtor
    * @access Public
*/
function TextBox()
{
    parent::Componente();//CHAMA O METODO CONSTRUTOR DA CLASSE BASE
    $this->setMaxLength           ( 10       );
    $this->setSize                ( 10       );
    $this->setInteiro             ( false    );
    $this->setFloat               ( false    );
    $this->setDecimais            ( 2        );
    $this->setDecimal             ( ","      );
    $this->setMilhar              ( "."      );
    $this->setAlign               ( "left"   );
    $this->setTipo                ( "text"   );
    $this->setName                ( "stText" );
    $this->setDefinicao           ( "text"   );
    $this->setToUpperCase         ( false    );
    $this->setToLowerCase         ( false    );
    $this->setAlfaNumerico        ( false    );
    $this->setMascara             ( ""       );
    $this->setPreencheComZeros    ( false    );
    $this->setAcento              ( true     );
    $this->setValidaCaracteres    ( false    );
    $this->setEspacosExtras       ( true     );
    $this->setBoCaracteresAceitos ( false    );
    $this->setAutoComplete        ( TRUE     );
    $this->setLabel               ( false    );
    $this->setExpReg              ('[^0-9]' );
}

/**
    * Monta o HTML do Objeto TextBox
    * @access Protected
*/
function montaHtml()
{
    if ( $this->getInteiro() ) {

        if ($this->getMascara() != '') {
            $caracteres = preg_replace('/[0-9]/', '', $this->getMascara());
            $expReg = '[^0-9'.$caracteres.']';
        } else {
            $expReg = $this->getExpReg();
        }

        $stJs = "if (this.value.search(new RegExp(&quot;".$expReg."&quot;, &quot;g&quot;)) > -1) { this.value = &quot;&quot;; }";
        $this->obEvento->setOnKeyPress($stJs.$this->obEvento->getOnKeyPress()."return inteiro( event );" );
        $this->obEvento->setOnBlur($stJs.$this->obEvento->getOnBlur() );
    } elseif ( $this->getFloat() ) {
        if (strpos($this->obEvento->getOnKeyPress(),'return tfloat(') === false ) {
            $this->obEvento->setOnKeyPress( $this->obEvento->getOnKeyPress()."return tfloat( this, event );" );
        } else {
            $this->obEvento->setOnKeyPress( $this->obEvento->getOnKeyPress() );
        }
        if ( $this->getDecimais() ) {
            if (strpos($this->obEvento->getOnBlur(),'floatDecimal(') === false ) {
                $this->obEvento->setOnBlur( $this->obEvento->getOnBlur() . "floatDecimal(this, '".$this->getDecimais()."', event );" );
            } else {
                $this->obEvento->setOnBlur($this->obEvento->getOnBlur());
            }
        }
    }
    if ( $this->getToUpperCase() ) {
        $this->obEvento->setOnBlur( $this->obEvento->getOnBlur()."toUpperCase(this);" );
    }
    if ( $this->getToLowerCase() ) {
        $this->obEvento->setOnBlur( $this->obEvento->getOnBlur()."toLowerCase(this);" );
    }
    if ( $this->getMinLength() ) {
        $this->obEvento->setOnBlur( $this->obEvento->getOnBlur()."validaMinLength(this,".$this->getMinLength().");" );
    }
    if ($this->boAlfaNumerico && !$this->boInteiro) {
        $this->obEvento->setOnKeyPress( $this->obEvento->getOnKeyPress()."return alfaNumerico( this , event );" );
    }
    if (!$this->boAcento && !$this->boInteiro) {
        $this->obEvento->setOnChange( $this->obEvento->getOnChange()."removeAcentos( this , event );" );
    }
    if (!$this->boEspacosExtras && !$this->boInteiro) {
        $this->obEvento->setOnBlur( $this->obEvento->getOnBlur()."return removeEspacosExtras( this, event );" );
    }
    if ( $this->getBoCaracteresAceitos() && !$this->boInteiro ) {
        $this->obEvento->setOnKeyPress( $this->obEvento->getOnKeyPress()."return validaExpressao( this, event, '". $this->getCaracteresAceitos() ."');" );
    }
    if ( trim($this->stMascara) != '' ) {
        $this->setMaxLength ( strlen( $this->stMascara ) );
        $this->setSize      ( strlen( $this->stMascara ) + 1 );
        $this->obEvento->setOnKeyUp($this->obEvento->getOnKeyUp()."mascaraDinamico(&quot;".$this->stMascara."&quot;, this, event);");
        if ($this->stPreencheComZeros) {
            $this->obEvento->setOnChange("preencheComZeros('".$this->stMascara."', this, '".$this->stPreencheComZeros."');".$this->obEvento->getOnChange());
        }
    }
     if ( is_array( $this->arValoresBusca ) ) {
       $this->obEvento->setOnChange( $this->obEvento->getOnChange().
                                     "buscaValorBscInner( '".$this->arValoresBusca['stPaginaBusca']."',
                                                          '".$this->arValoresBusca['stNomForm']."',
                                                          '".$this->getName()."',
                                                          '".$this->stId."',
                                                          '".$this->arValoresBusca['stTipoBusca']."' );");
    }
    if ( $this->getLabel() ) {
        $this->setStyle ( 'display:none;' );
    }
    parent::montaHtml();
    $stHtml = $this->getHtml();
    $stHtml = substr( $stHtml, 0, strlen($stHtml) - 1 );
    if ( $this->getMaxLength() ) {
        $stHtml .= "maxlength=\"".$this->getMaxLength()."\" ";
    }
    if ( $this->getSize() ) {
        $stHtml .= "size=\"".($this->getSize() + 1)."\" ";
    }
    if ( $this->getAutoComplete() == FALSE ) {
        $stHtml .= " autocomplete=\"off\" ";
    }
    $stHtml .= "STYLE='{text-align: ".$this->getAlign().";}' ";
    $stHtml .= ">";
    if ( $this->getLabel() ) {
        $stHtml .= "<span id=\"".$this->getId()."_label\" >";
        $stHtml .= $this->getValue();
        $stHtml .= "</span>";
    }
    $this->setHtml($stHtml);
}

}
?>
