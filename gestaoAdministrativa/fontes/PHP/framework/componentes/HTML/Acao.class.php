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
* Manipular com as ações que o usuário pode escolher em uma lista
* Data de Criação: 10/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que manipulas as ações

    * @package framework
    * @subpackage componentes
*/
class Acao extends Objeto
{
/**
    * @access Private
    * @var String
*/
var $stAcao;

/**
    * @access Private
    * @var Object
*/
var $obBotao;

/**
    * @access Private
    * @var String
*/
var $stLink;

/**
    * @access Private
    * @var String
*/
var $stTarget;

/**
    * @access Private
    * @var String
*/
var $stLinkId;

/**
    * @access Private
    * @var Array
*/
var $arCampo;//CAMPOS DO RECORDSET PARA O LINK

/**
    * @access Private
    * @var String
*/
var $stHTML;

/**
    * @access Private
    * @var Object
*/
var $obFuncao;//VERIFICA SE O LINK SERA UMA FUNÇÃO JAVASCRIPT, SE FOR NA CLASSE LISTA
              //O USUARIO PODERA PASSAR COMPOS DO RECORDSET COMO PARAMETROS

/**
    * @access Private
    * @var Boolean
*/
var $boUnicoBotao;
/**
    * @access Private
    * @var String
*/
var $stAcaoUnicoBotao;
/**
    * @access Private
    * @var Boolean
*/
var $boFuncaoAjax;

/**
    * @access Private
    * @var String
*/

var $stTipoLink;

/**
    * @access Private
    * @var String
*/

var $stClassInput;

//SETTERS
/**
    * @access Public
    * @param String $Valor
*/
function setAcao($valor) { $this->stAcao   = $valor; }

/**
    * @access Public
    * @param Object $Valor
*/
function setBotao($valor) { $this->obBotao  = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setLink($valor) { $this->stLink   = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setTipoLink($valor) { $this->stTipoLink   = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setTarget($valor) { $this->stTarget = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setLinkId($valor) { $this->stLinkId  = $valor; }

/**
    * @access Public
    * @param Array $Valor
*/
function setCampo($valor) { $this->arCampo  = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setHTML($valor) { $this->stHTML   = $valor; }

/**
    * @access Public
    * @param Boolean $Valor
*/
function setFuncao($valor) { $this->boFuncao = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setFuncaoAjax($valor) { $this->boFuncaoAjax = $valor; }

/**
    * @access Public
    * @param Boolean $Valor
*/
function setUnicoBotao($valor) { $this->boUnicoBotao = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setAcaoUnicoBotao($valor) { $this->stAcaoUnicoBotao = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setClassInput($valor) { $this->stClassInput = $valor; }

//GETTERS
/**
    * @access Public
    * @return String
*/
function getAcao() { return $this->stAcao;   }

/**
    * @access Public
    * @return Object
*/
function getBotao() { return $this->obBotao;  }

/**
    * @access Public
    * @return String
*/
function getLink() { return $this->stLink;   }

/**
    * @access Public
    * @return String
*/
function getTipoLink() { return $this->stTipoLink;   }

/**
    * @access Public
    * @return String
*/
function getTarget() { return $this->stTarget; }

/**
    * @access Public
    * @return String
*/
function getLinkId() { return $this->stLinkId; }

/**
    * @access Public
    * @return Array
*/
function getCampo() { return $this->arCampo;  }

/**
    * @access Public
    * @return String
*/
function getHTML() { return $this->stHTML;   }

/**
    * @access Public
    * @return Boolean
*/
function getFuncao() { return $this->boFuncao; }
/**
    * @access Public
    * @return Boolean
*/
function getFuncaoAjax() { return $this->boFuncaoAjax; }

/**
    * @access Public
    * @return Boolean
*/
function getUnicoBotao() { return $this->boUnicoBotao; }
/**
    * @access Public
    * @return String
*/
function getAcaoUnicoBotao() { return $this->stAcaoUnicoBotao; }
/**
    * @access Public
    * @return String
*/
function getClassInput() { return $this->stClassInput; }

/**
    * Metodo Construtor
    * @access Public
*/
function Acao()
{
    $obBotao = new Img;
    $obBotao->setBorder("0");
    $this->setBotao( $obBotao );
    $arCampo = array();
    $this->setCampo( $arCampo );
    $this->setFuncao( false );
    $this->setUnicoBotao( false );
}

/**
    * @access Public
    * @param String $stIndice
    * @param String $stCampo
*/
function addCampo($stIndice, $stCampo)
{
    $arCampo = $this->getCampo();
    $arCampo[$stIndice] = $stCampo;
    $this->setCampo( $arCampo );
}

/**
    * Monta o HTML do Objeto Acao
    * @access Protected
*/
function montaHTML()
{
    //INCLUI O ARRAY arAcao COM AS ACOES CADASTRADAS
    include ( CAM_FW_INCLUDE."acaoBotao.inc.php");

    $stTitle =  isset($arTitle[strtoupper($this->getAcao())]) ? $arTitle[strtoupper($this->getAcao())] : $this->getAcao();

    $stAcao = strtoupper( $this->getAcao() );
    if ( !array_key_exists( $stAcao, $arAcao ) ) {
        $stAcao = strtolower($stTitle);
        $stAcao = ucfirst($stAcao);
        $stHTML = $stAcao;
    } else {
        $stTitle = strtolower($stTitle);
        $stTitle == "remover" ? $stTitle = "excluir" : $stTitle = $stTitle;
        $stTitle = ucfirst($stTitle);
        $this->obBotao->setCaminho( $arAcao[$stAcao] );
        $this->obBotao->setTitle( $stTitle );
        $this->obBotao->montaHTML();
        $stHTML = $this->obBotao->getHTML();
    }

    if (strtolower($this->getTipoLink()) == "checkbox") {
        $stClass = "";
        if ($this->getClassInput()) {
            $stClass = " class=\"".$this->getClassInput()."\" ";
        }
        if ( $this->getLink() ) {
            $stHTML = "<input type=\"checkbox\" onClick=\"".$this->getLink()." ".$stClass." \">";
        }
    } else {
        if ( $this->getLink() ) {
            $stTarget = "";
            if ( $this->getTarget() ) {
                $stTarget = " target=".$this->getTarget();
            }
            $stLinkId = "";
            if ( $this->getLinkId() ) {
                $stLinkId = " id='".$this->getLinkId()."'";
            }
            $stHTML = "<a href=\"".$this->getLink()."\"".$stTarget.$stLinkId.">".$stHTML."</a>";
        }
    }
    $this->setHTML( $stHTML );
}

/**
    * Imprime o HTML do Objeto Acao na tela (echo)
    * @access Public
*/
function show()
{
    $this->montaHTML();
    echo $this->getHTML();
}

}
?>
