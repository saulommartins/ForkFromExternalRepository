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
* Gerar o componente tipo select de acordo com os valores setados pelo usuário.
* Data de Criação: 05/02/2003

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

$Id: Select.class.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-01.01.00

*/

/**
    * Classe que gera o HTML do Select

    * @package framework
    * @subpackage componentes
*/
class Select extends Componente
{
/**
    * @access Private
    * @var String
*/
var $stCampoId;

/**
    * @access Private
    * @var String
*/
var $stCampoDesc;

/**
    * @access Private
    * @var String
*/
var $stValorAuxiliar;

/**
    * @access Private
    * @var String
*/
var $stCampoAuxiliar;

/**
    * @access Private
    * @var String
*/
var $arOption;

/**
    * @access Private
    * @var String
*/
var $boMultiple;

/**
    * @access Private
    * @var String
*/
var $inSize;

/**
    * @access Private
    * @var Boolean
*/
var $boDependente;

/**
    * @access Private
    * @var Boolean
*/
var $boLabel;

//SETTERS
/**
    * @access Public
    * @param String $Valor
*/
function setCampoId($valor) { $this->stCampoId        = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setCampoDesc($valor) { $this->stCampoDesc      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setValorAuxiliar($valor) { $this->stValorAuxiliar  = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setCampoAuxiliar($valor) { $this->stCampoAuxiliar  = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setOption($valor) { $this->arOption         = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setSize($valor) { $this->inSize           = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setMultiple($valor) { $this->boMultiple       = $valor; }

/**
    * @access Public
    * @param Boolean $Valor
*/
function setDependente($valor) { $this->boDependente        = $valor; }

/**
    * @access Public
    * @param Boolean $Valor
*/
function setLabel($valor) { $this->boLabel           = $valor; }

//GETTERS
/**
    * @access Public
    * @return String
*/
function getCampoId() { return $this->stCampoId;       }

/**
    * @access Public
    * @return String
*/
function getCampoDesc() { return $this->stCampoDesc;     }

/**
    * @access Public
    * @return String
*/
function getValorAuxiliar() { return $this->stValorAuxiliar; }

/**
    * @access Public
    * @return String
*/
function getCampoAuxiliar() { return $this->stCampoAuxiliar; }

/**
    * @access Public
    * @return String
*/
function getOption() { return $this->arOption;        }

/**
    * @access Public
    * @return String
*/
function getSize() { return $this->inSize;          }

/**
    * @access Public
    * @return String
*/
function getMultiple() { return $this->boMultiple;      }

/**
    * @access Public
    * @return Boolean
*/
function getDependente() { return $this->boDependente;      }

/**
    * @access Public
    * @return Boolean
*/
function getLabel() { return $this->boLabel;      }

/**
    * Método construtor
    * @access Public
*/
function Select()
{
    parent::Componente();//CHAMA O METODO CONSTRUTOR DA CLASSE BASE
    $this->setName( "select" );
    $arOption = array();
    $this->setOption( $arOption );
    $this->setDefinicao ( "select" );
    $this->setDependente (false);
}

//METODOS DA CLASSE

/**
    * Acrescenta Options no Combo
    * @param String $options
    * @access Public
*/
function setOptions($options)
{
    foreach ($options as $key => $val) {
        $obOption = new Option;
        $obOption->setValor( $key );
        $obOption->setCampo( $val );
        $obOption->setTitle( $val );
        $arOption[] = $obOption;
    }

    $this->setOption( $arOption );
}

/**
    * Acrescenta um Option no Combo
    * @param String $stValue
    * @param String $stCampo
    * @access Public
*/
function addOption($stValue = "", $stCampo = "",$selected = "")
{
    $obOption = new Option;
    $obOption->setValor( $stValue );
    $obOption->setCampo( $stCampo );
    $obOption->setTitle( $stCampo );
    $obOption->setSelected($selected);
    $arOption = $this->getOption();
    $arOption[] = $obOption;
    $arOption = $this->setOption( $arOption );
}

/**
    * Preenche o Combo utilizando um RecordSet
    * @param Object $stValue
    * @access Public
*/
function preencheCombo($rsElementosCombo)
{
    $stDesc      = "";
    $stId        = "";
    $rsElementos = clone $rsElementosCombo;

    $stCampoId      = $this->getCampoId();
    $stCampoDesc    = $this->getCampoDesc();
    while ( !$rsElementos->eof() ) {
        if ( $this->getCampoAuxiliar() ) {
            if ( $rsElementos->getCampo( $this->getCampoId() ) == $this->getValue() ) {
                $this->setValorAuxiliar( $rsElementos->getCampo( $this->getCampoAuxiliar() ) );
            }
        }
        //Alterado para permitir chave composta no Value
        if (strstr($stCampoId,'[') || strstr($stCampoId,']')) {
            for ($inCount=0; $inCount<strlen($stCampoId); $inCount++) {
                if ($stCampoId[ $inCount ] == '[') $inInicialId = $inCount;
                if (($stCampoId[ $inCount ] == ']') && isset($inInicialId) ) {
                    $stId .= $rsElementos->getCampo( trim( substr($stCampoId,$inInicialId+1,(($inCount-$inInicialId)-1)) ) );
                    unset($inInicialId);
                }elseif( !isset($inInicialId) )
                    $stId .= $stCampoId[ $inCount ];
            }
        } else {
            $stId = $rsElementos->getCampo( $stCampoId );
        }
        //Alterado para permitir chave composta na Descricao
        if (strstr($stCampoDesc,'[') || strstr($stCampoDesc,']')) {
            for ($inCount=0; $inCount<strlen($stCampoDesc); $inCount++) {
                if ($stCampoDesc[ $inCount ] == '[') $inInicialDs = $inCount;
                if (($stCampoDesc[ $inCount ] == ']') && isset($inInicialDs) ) {
                    $stDesc .= $rsElementos->getCampo( trim( substr($stCampoDesc,$inInicialDs+1,(($inCount-$inInicialDs)-1)) ) );
                    unset($inInicialDs);
                }elseif( !isset($inInicialDs) )
                    $stDesc .= $stCampoDesc[ $inCount ];
            }
        } else {
            $stDesc = $rsElementos->getCampo( $stCampoDesc );
        }

        $this->addOption( $stId , $stDesc );
        $stId   = '';
        $stDesc = '';
        $rsElementos->proximo();
    }

    return true;
}

/**
    * Monta o HTML do Objeto Select
    * @access Protected
*/
function montaHtml()
{
    //seta o display none para o setLabel(true)
    if ( $this->getLabel() ) {
        $this->setStyle( 'display:none;' );
    }

    parent::montaHtml();
    $stHtml  = $this->getHtml();
    $stHtml  = substr( $stHtml, 0, strlen($stHtml) - 1 );
    $stHtml  = "<select ".$stHtml;
    if ( $this->getSize() ) {
        $stHtml .= " size='".$this->getSize()."' ";
    }
    if ( $this->getMultiple() ) {
        $stHtml .= " multiple ";
    }
    $stHtml .= ">\n";
    $stOption = "";
    $arOption = $this->getOption();
    if ( count($arOption) ) {
        foreach ($arOption as $obOption) {
            $stOptionTmp = "<option ";
            $stOptionTmp .= "value=\"".$obOption->getValor()."\" ";
            if ( $obOption->getLabel() ) {
                $stOptionTmp .= "label=\"".$obOption->getLabel()."\" ";
            }
            if ( $obOption->getId() ) {
                $stOptionTmp .= "id=\"".$obOption->getId()."\" ";
            }
            if ( $obOption->getDisabled() ) {
                $stOptionTmp .= "disabled ";
            }
            if ( $obOption->getReadOnly() ) {
                $stOptionTmp .= "readonly ";
            }

            $valorOption = $obOption->getValor();

            if ((($valorOption != '') && ($obOption->getValor() == $this->getValue())) || $obOption->getSelected() == 'selected') {
                $stOptionTmp .= "selected=\"selected\"";
            }

            if ($obOption->getTitle() AND $this->getMultiple() ) {
                $stOptionTmp .= "title=\"".$obOption->getTitle()."\" ";
            }

            $stOption .= "    ".trim($stOptionTmp).">".$obOption->getCampo()."</option>\n";
        }
    }
    $stHtml .= $stOption;
    $stHtml .= "</select>";
    if ( $this->getLabel() ) {
        $stHtml .= "<span id=\"".$this->getId()."_label\" >";
        foreach ($arOption as $obOption) {
            if ( $this->getValue() == $obOption->getValor() ) {
                $stHtml .= $obOption->getCampo();
            }
        }
        $stHtml .= "</span>";
    }
    $this->setHtml($stHtml);
}

/**
    * Imprime o HTML do Objeto Label na tela (echo)
    * @access Public
*/
function show()
{
    $this->montaHtml();
    $stHtml = $this->getHtml();
    echo $stHtml;
}

}
?>
