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
* Gerar o componente composto com a opcao de busca em POPUP e editar a descrição
* Data de Criação: 22/06/2006

* @author Desenvolvedor: Tonismar Régis Bernardo

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que monta o HTML do PopUpEdit
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package framework
    * @subpackage componentes
*/

class PopUpEdit extends ComponenteBase
{
var $boNull;
var $obCampoCod;
var $obCampoTexto;
var $obBtnBuscar;
var $obBtnLimpar;
var $stFuncaoCod;
var $stFuncaoBusca;
var $stNomeBusca;
var $stHTML;
var $stName;
var $stValue;
var $stId;
var $arValoresBusca;

// setters
function setCampoCod($valor) { $this->obCampoCod     = $valor; }
function setCampoTexto($valor) { $this->obCampoTexto   = $valor; }
function setImagemBuscar($valor) { $this->obImagemBuscar = $valor; }
function setImagemLimpar($valor) { $this->obImagemLimpar = $valor; }
function setFuncaoCod($valor) { $this->stFuncaoCod    = $valor; }
function setFuncaoBusca($valor) { $this->stFuncaoBusca  = $valor; }
function setNomeBusca($valor) { $this->stNomeBusca    = $valor; }
function setHTML($valor) { $this->stHTML         = $valor; }
function setName($valor) { $this->stName         = $valor; }
function setValue($valor) { $this->stValue        = $valor; }
function setId($valor) { $this->stId           = $valor; }
function setNull($valor) { $this->boNull         = $valor; }
function setValoresBusca($stPaginaBusca, $stNomForm ,$stTipoBusca = '')
{
    $this->arValoresBusca = array( 'stPaginaBusca' => $stPaginaBusca, 'stNomForm' => $stNomForm, 'stTipoBusca' => $stTipoBusca );
}

// getters
function getCampoCod() { return $this->obCampoCod;       }
function getCampoTexto() { return $this->obCampoTexto;     }
function getImagemBuscar() { return $this->obImagemBuscar;   }
function getImagemLimpar() { return $this->obImagemLimpar;   }
function getFuncaoCod() { return $this->stFuncaoCod;      }
function getFuncaoBusca() { return $this->stFuncaoBusca;    }
function getNomeBusca() { return $this->stNomeBusca;      }
function getHtml() { return $this->stHTML;           }
function getName() { return $this->stName;           }
function getValue() { return $this->stValue;          }
function getId() { return $this->stId;             }
function getNull() { return $this->boNull;           }

// constructor
function PopUpEdit(&$obForm)
{
    parent::ComponenteBase();
    // definicao do campo codigo
    $obCampoCod = new TextBox;
    $obCampoCod->setRotulo            ( $this->getRotulo() );
    $obCampoCod->obEvento->setOnChange( '' );
    $obCampoCod->setSize              ( 10 );
    $obCampoCod->setMaxLength         ( 10 );
    $obCampoCod->setMinLength         ( 1  );
    $obCampoCod->setInteiro           ( true );
    $obCampoCod->setName              ($this->getName());
    $obCampoCod->setNull              ($this->getNull());
    $this->setCampoCod                ( $obCampoCod    );

    // definicao do campo texto
    $obCampoTexto = new TextArea;
    $obCampoTexto->setName            ( $this->getName());
    $obCampoTexto->setRotulo          ( $this->getRotulo() );
    $obCampoTexto->setCols            ( 50 );
    $obCampoTexto->setRows            ( 5  );
    $obCampoTexto->setNull            ($this->getNull());
    $this->setCampoTexto              ( $obCampoTexto  );

    $obImgBuscar    = new Img;
    $obImgBuscar->setCaminho          ( CAM_FW_IMAGENS."botao_popup.png");
    $obImgBuscar->setAlign            ( "absmiddle"  );
    $this->setImagemBuscar            ( $obImgBuscar );

    $obImgLimpar    = new Img;
    $obImgLimpar->setCaminho          ( CAM_FW_IMAGENS."btnLimpar.png");
    $obImgLimpar->setAlign            ( "absmiddle"  );
    $this->setImagemLimpar            ( $obImgLimpar );

    $this->setName                    ( $this->obCampoCod->getName() );
    $this->setDefinicao               ( 'POPUPEDIT' );
    $this->obForm = &$obForm;
}

// monta o Html
function montaHTML()
{
//  $this->obCampoTexto->obEvento->setOnChange  ( $this->obCampoCod->getName().".value = '';" );
    $this->obCampoTexto->obEvento->setOnKeyPress( "if (event.keyCode!=9) { ".$this->obCampoCod->getName().".value = '';}" );

    if ( $this->getNomeBusca() ) {
        $stNomeBusca = $this->getNomeBusca();
        $this->obCampoCod->setName( 'inCod'.$stNomeBusca );
    }
    if ( is_array( $this->arValoresBusca ) ) {
        $this->obCampoCod->obEvento->setOnChange( $this->obCampoCod->obEvento->getOnChange()."buscaValorBscInner( '".$this->arValoresBusca['stPaginaBusca']."', '".$this->arValoresBusca['stNomForm']."', '".$this->obCampoCod->getName()."','".$this->obCampoTexto->getName()."', '".$this->arValoresBusca['stTipoBusca']."' );");
    }

    $this->obCampoCod->montaHTML();
    $this->obCampoTexto->montaHTML();
    $this->obImagemBuscar->montaHTML();
    $this->obImagemLimpar->montaHTML();

    $stTitleImagem = strtolower(preg_replace("/\*/","",$this->stRotulo));
    $stLink  = "&nbsp;<a href=\"JavaScript: ".$this->getFuncaoBusca().";\" title='Buscar ".$stTitleImagem."'>";
    $stLink .= $this->obImagemBuscar->getHTML();
    $stLink .= "</a>";

    $stLink .= "&nbsp;&nbsp;<a href='#' onclick=\"document.".$this->obForm->getName().".".$this->obCampoCod->getName().".value = '';document.".$this->obForm->getName().".".$this->obCampoTexto->getName().".value = '';\" title='Limpar'>";
    $stLink .= $this->obImagemLimpar->getHTML();
    $stLink .= "</a>";

    $obTabela = new Tabela;
    $obTabela->setCellPadding( 0 );
    $obTabela->setCellSpacing( 0 );
    $obTabela->setWidth( 100 );
    $obTabela->addLinha();
    if ( $this->obCampoCod->getMinLength() > 0 ) {
        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setClass( "field" );
        $obTabela->ultimaLinha->ultimaCelula->setWidth( "10" );
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obCampoCod->getHTML().$stLink.'<br>'.$this->obCampoTexto->getHTML() );
        $obTabela->ultimaLinha->commitCelula();
    }

    $obTabela->commitLinha();
    $obTabela->montaHTML();
    $this->setHTML( $obTabela->getHTML() );
}

function show()
{
    $this->montaHTML();
    $stHTML = $this->getHTML();
    $stHTML =  trim( $stHTML )."\n";
}

} // end class
