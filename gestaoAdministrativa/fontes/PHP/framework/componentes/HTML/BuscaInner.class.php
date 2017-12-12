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
* Gerar o componente composto com a opcao de busca em POPUP
* Data de Criação: 08/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que monta o HTML da BuscaInner
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package framework
    * @subpackage componentes
*/
class BuscaInner extends ComponenteBase
{
/**
    * @access Private
    * @var Object
*/
var $obCampoCod;

/**
    * @access Private
    * @var Object
*/
var $obCampoCodHidden;

/**
    * @access Private
    * @var Object
*/
var $obCampoDescrHidden;

/**
    * @access Private
    * @var Object
*/
var $obImagem;

/**
    * @access Private
    * @var String
*/
var $stFuncaoCod;

/**
    * @access Private
    * @var String
*/
var $stFuncaoBusca;

/**
    * @access Private
    * @var String
*/
var $stNomeBusca;

/**
    * @access Private
    * @var String
*/
var $stHTML;

/**
    * @access Private
    * @var String
*/
var $stTitle;

/**
    * @access Private
    * @var String
*/
var $stName;

/**
    * @access Private
    * @var String
*/
var $stValue;

/**
    * @access Private
    * @var String
*/
var $stId;

/**
    * @access Private
    * @var Array
*/
var $arValoresBusca;
/**
    * @access Private
    * @var Boolean
*/
var $boMostrarDescricao;
/**
    * @access Private
    * @var Boolean
*/
var $boMonitorarCampoCod;
/**
    * @access Private
    * @var Boolean
*/
var $boLabel;

//SETTERS
/**
    * @access Public
    * @param Object $valor
*/
function setCampoCod($valor) { $this->obCampoCod       = $valor; }

/**
    * @access Public
    * @param Object $valor
*/
function setImagem($valor) { $this->obImagem         = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setFuncaoCod($valor) { $this->stFuncaoCod      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setFuncaoBusca($valor) { $this->stFuncaoBusca    = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setNomeBusca($valor) { $this->stNomeBusca      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setHTML($valor) { $this->stHTML           = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setName($valor) { $this->stName           = $valor; }

/**
    * @access Public
    * @param String $stPaginaBusca
    * @param String $stNomForm
    * @param String $stTipoBusca
*/
function setValoresBusca($stPaginaBusca, $stNomForm ,$stTipoBusca = '')
{
    $this->arValoresBusca = array( 'stPaginaBusca' => $stPaginaBusca, 'stNomForm' => $stNomForm, 'stTipoBusca' => $stTipoBusca );
}

/**
    * @access Public
    * @param String $valor
*/
function setId($valor)
{
    $this->stId = $valor;
    $this->obCampoDescrHidden->setName( $valor );
}

/**
    * @access Public
    * @param String $valor
*/
function setValue($valor)
{
    $this->stValue = $valor;
    $this->obCampoDescrHidden->setValue( $valor );
}
/**
    * @access Public
    * @param Boolean $valor
*/
function setMostrarDescricao($valor)
{
    $this->boMostrarDescricao = $valor;
}
/**
    * @access Public
    * @param Boolean $valor
*/
function setMonitorarCampoCod($valor)
{
    $this->boMonitorarCampoCod = $valor;
}
/**
    * @access Public
    * @param Boolean $valor
*/
function setLabel($valor)
{
    $this->boLabel = $valor;
}

//GETTERS
/**
    * @access Public
    * @return String
*/
function getCampoCod() { return $this->obCampoCod;       }

/**
    * @access Public
    * @return String
*/
function getImagem() { return $this->obImagem;         }

/**
    * @access Public
    * @return String
*/
function getFuncaoCod() { return $this->stFuncaoCod;      }

/**
    * @access Public
    * @return String
*/
function getFuncaoBusca() { return $this->stFuncaoBusca;    }

/**
    * @access Public
    * @return String
*/
function getNomeBusca() { return $this->stNomeBusca;      }

/**
    * @access Public
    * @return String
*/
function getHTML() { return $this->stHTML;           }

/**
    * @access Public
    * @return String
*/
function getName() { return $this->stName;           }

/**
    * @access Public
    * @return String
*/
function getValue() { return $this->stValue;          }

/**
    * @access Public
    * @return String
*/
function getId() { return $this->stId;             }

/**
    * @access Public
    * @return Boolean
*/
function getMostrarDescricao() { return $this->boMostrarDescricao; }

/**
    * @access Public
    * @return Boolean
*/
function getMonitorarCampoCod() { return $this->boMonitorarCampoCod; }

/**
    * @access Public
    * @return Boolean
*/
function getLabel() { return $this->boLabel; }

/**
    * Metodo Construtor
    * @access Public
*/
function BuscaInner()
{
    parent::ComponenteBase();
    //DEFINICAO DO CAMPO COD
    $obCampoCod  = new TextBox;
    $obCampoCod->obEvento->setOnChange( '' );
    $obCampoCod->setSize        ( 10 );
    $obCampoCod->setMaxLength   ( 10 );
    $obCampoCod->setMinLength   ( 1 );
    $obCampoCod->setInteiro     ( true );
    $obCampoCod->setExpReg      ('[^0-9/.-]');
    $obCampoCod->setName        ( "inCampoCod" );
    $this->setCampoCod          ( $obCampoCod );

    //DEFINICAO DA IMAGEM
    $obImagem    = new Img;
    $obImagem->setCaminho ( CAM_FW_IMAGENS."botao_popup.png");
    $obImagem->setAlign   ( "absmiddle" );
    $this->setImagem      ( $obImagem );

    //DEFINICAO DO CAMPO HIDDEN PARA CODIGO
    $this->obCampoCodHidden = new Hidden();
    $this->obCampoCodHidden->setValue( $this->obCampoCod->getValue()      );

    //DEFINICAO DO CAMPO HIDDEN PARA DESCRICAO
    $this->obCampoDescrHidden = new Hidden();
    $this->obCampoDescrHidden->setName ( $this->stId    );
    $this->obCampoDescrHidden->setValue( $this->stValue );

    $this->setMonitorarCampoCod(false);
    $this->setMostrarDescricao( true );

    $this->setDefinicao     ( "BUSCAINNER" );
}

/**
    * Monta o HTML do Objeto Label
    * @access Private
*/
function montaHTML()
{
    if ( $this->getNomeBusca() ) {
        $stNomeBusca = $this->getNomeBusca();
        $this->obCampoCod->setName  ( "inCod".$stNomeBusca );
    }

    if ( is_array( $this->arValoresBusca ) ) {
       $this->obCampoCod->obEvento->setOnChange( $this->obCampoCod->obEvento->getOnChange()."buscaValorBscInner( '".$this->arValoresBusca['stPaginaBusca']."', '".$this->arValoresBusca['stNomForm']."', '".$this->obCampoCod->getName()."',
'".$this->stId."', '".$this->arValoresBusca['stTipoBusca']."' );");
    }

    $this->obCampoCod->montaHTML();
    $this->obImagem->montaHTML();

    $stTitleImagem = strtolower(preg_replace("^\*^","",$this->stRotulo));

    if ($this->getMonitorarCampoCod()) {
        $stTrocaFlag = "boObserver".$this->obCampoCod->getId()."=true;";
        $stLink  = "&nbsp;<a href=\"JavaScript: ".$stTrocaFlag.$this->getFuncaoBusca().";\" title='Buscar ".$stTitleImagem."'>";
    } else {
        $stLink  = "&nbsp;<a href=\"JavaScript: ".$this->getFuncaoBusca().";\" title='Buscar ".$stTitleImagem."'>";
    }

    $stLink .= $this->obImagem->getHTML();
    $stLink .= "</a>";

    $obTabela = new Tabela;
    $obTabela->setWidth(100);
    $obTabela->setCellPadding(0);
    $obTabela->setCellSpacing(0);
    $obTabela->setBorder(0);

    $obTabela->addLinha();

    if ($this->getLabel()) {
        $obTabela->setStyle('display:none;');
    }

    if ($this->obCampoCod->getMinLength() > 0) {
        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setWidth(5);
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obCampoCod->getHTML() );
        $obTabela->ultimaLinha->ultimaCelula->setStyle( "padding-right:5px; " );
        $obTabela->ultimaLinha->commitCelula();
    }

    if ($this->getId()) {
        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setClass( "fakefield" );
        $obTabela->ultimaLinha->ultimaCelula->setWidth( "60" );
        $obTabela->ultimaLinha->ultimaCelula->setHeight( "20" );
        $obTabela->ultimaLinha->ultimaCelula->setStyle( "padding-left:5px; " );
        $obTabela->ultimaLinha->ultimaCelula->setId( $this->getId() );

        if (!$this->getMostrarDescricao() ) {
            $obTabela->ultimaLinha->ultimaCelula->setStyle( "display:none;" );
        }

        $obTabela->ultimaLinha->ultimaCelula->addConteudo( ( $this->getValue() ) ? $this->getValue() : '&nbsp;' );
        $obTabela->ultimaLinha->commitCelula();
    }
    if ( $this->obCampoCod->getMinLength() > 0 || $this->obCampoCod->getMaxLength() == 0 ) {

        if ( trim($this->stValue) ) {
            $stValue = "<script>document.getElementById('".$this->stId."').innerHTML = '".$this->stValue."';</script>";
            $stLink .= $stValue;
        }
        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setClass( "fieldleft" );
        $obTabela->ultimaLinha->ultimaCelula->setValign( "absmiddle" );

        $this->obCampoCodHidden->setName ( 'Hdn'.$this->obCampoCod->getName() );
        $this->obCampoCodHidden->montaHtml();
        $this->obCampoDescrHidden->montaHtml();

        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stLink.$this->obCampoCodHidden->getHtml().$this->obCampoDescrHidden->getHtml() );
        $obTabela->ultimaLinha->commitCelula();
    }

    $obTabela->commitLinha();
    $obTabela->montaHTML();
    $stHTML = $obTabela->getHTML();
    if ( $this->getLabel() ) {
        $stHTML .= "<span id=\"".$this->getId()."_label\" >";
        $stHTML .= $this->obCampoCod->getValue();
        if ( $this->getValue() != '' ) {
            $stHTML .= ' - '.$this->getValue();
        }
        $stHTML .= "</span>";
    }
    $this->setHTML( $stHTML );
}

/**
    * Imprime o HTML do Objeto Label na tela (echo)
    * @access Private
*/
function show()
{
    $this->montaHTML();
    $stHTML = $this->getHTML();
    $stHTML =  trim( $stHTML )."\n";
}
}
?>
