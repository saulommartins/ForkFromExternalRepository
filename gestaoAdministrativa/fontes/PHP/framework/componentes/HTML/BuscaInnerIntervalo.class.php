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
* Gerar componente composto em intervalo com dois busca inner
* Data de Criação: 06/06/2005

* @author Desenvolvedor: Lucas Teixeira Stephanou

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que monta o HTML da BuscaInnerIntervalo
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @package framework
    * @subpackage componentes
*/
class BuscaInnerIntervalo extends Objeto
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
var $obImagem;

/**
    * @access Private
    * @var Object
*/
var $obCampoCod2;

/**
    * @access Private
    * @var Object
*/
var $obImagem2;

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
var $stFuncaoCod2;

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
var $stRotulo;

/**
    * @access Private
    * @var String
*/
var $stTitle;

/**
    * @access Private
    * @var Boolean
*/
var $boNull;

/**
    * @access Private
    * @var String
*/
var $stDefinicao;

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
    * @var Boolean
*/
var $boMonitorarCampoCod;

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
function setCampoCod2($valor) { $this->obCampoCod2      = $valor; }

/**
    * @access Public
    * @param Object $valor
*/
function setLabelIntervalo($valor) { $this->obLabelIntervalo = $valor; }

/**
    * @access Public
    * @param Object $valor
*/
function setImagem($valor) { $this->obImagem         = $valor; }

/**
    * @access Public
    * @param Object $valor
*/
function setImagem2($valor) { $this->obImagem2        = $valor; }

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
function setFuncaoCod2($valor) { $this->stFuncaoCod2     = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setFuncaoBusca2($valor) { $this->stFuncaoBusca2   = $valor; }

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
function setRotulo($valor) { $this->stRotulo         = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setTitle($valor) { $this->stTitle      =  mb_check_encoding($valor, 'UTF-8') ? utf8_decode($valor) : $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setNull($valor) { $this->boNull           = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setDefinicao($valor) { $this->stDefinicao      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setName($valor) { $this->stName           = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setValue($valor) { $this->stValue          = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setId($valor) { $this->stId             = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setMonitorarCampoCod($valor)
{
    $this->boMonitorarCampoCod = $valor;
}

//GETTERS
/**
    * @access Public
    * @return Object
*/
function getCampoCod() { return $this->obCampoCod;       }

/**
    * @access Public
    * @return Object
*/
function getCampoCod2() { return $this->obCampoCod2;      }

/**
    * @access Public
    * @return Object
*/
function getLabelIntervalo() { return $this->obLabelIntervalo; }

/**
    * @access Public
    * @return Object
*/
function getImagem() { return $this->obImagem;         }

/**
    * @access Public
    * @return Object
*/
function getImagem2() { return $this->obImagem2;        }

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
function getFuncaoCod2() { return $this->stFuncaoCod2;     }

/**
    * @access Public
    * @return String
*/
function getFuncaoBusca2() { return $this->stFuncaoBusca2;   }

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
function getRotulo() { return $this->stRotulo;         }

/**
    * @access Public
    * @return String
*/
function getTitle() { return $this->stTitle;          }

/**
    * @access Public
    * @return Boolean
*/
function getNull() { return $this->boNull;           }

/**
    * @access Public
    * @return String
*/
function getDefinicao() { return $this->stDefinicao;      }

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
function getId() { return $this->stId;          	}
/**
    * @access Public
    * @return Boolean
*/
function getMonitorarCampoCod() { return $this->boMonitorarCampoCod; }

/**
    * Metodo Construtor
    * @access Public
*/
function BuscaInnerIntervalo()
{
    //DEFINICAO DO CAMPO COD

    $obCampoCod  = new TextBox;
    $obCampoCod->obEvento->setOnChange( $this->getFuncaoCod() );
    $obCampoCod->setSize        ( 10 );
    $obCampoCod->setMaxLength   ( 10 );
    $obCampoCod->setMinLength   ( 1 );
    $obCampoCod->setInteiro     ( true );
    $obCampoCod->setExpReg      ('[^0-9/.-]');
    $obCampoCod->setName        ( "inCampoCod" );
    $this->setCampoCod          ( $obCampoCod );
    $obCampoCod2  = new TextBox;
    $obCampoCod2->obEvento->setOnChange( $this->getFuncaoCod2() );
    $obCampoCod2->setSize        ( 10 );
    $obCampoCod2->setMaxLength   ( 10 );
    $obCampoCod2->setMinLength   ( 1 );
    $obCampoCod2->setInteiro     ( true );
    $obCampoCod2->setExpReg       ('[^0-9/.-]');
    $obCampoCod2->setName        ( "inCampoCod2" );
    $this->setCampoCod2          ( $obCampoCod2 );

    //DEFINICAO DA IMAGEM
    $obImagem    = new Img;
    $obImagem->setCaminho   ( CAM_FW_IMAGENS."botao_popup.png");
    $obImagem->setAlign     ( "middle" );
    $this->setImagem        ( $obImagem );
    $this->setNull          ( true );
    $obImagem2    = new Img;
    $obImagem2->setCaminho   ( CAM_FW_IMAGENS."botao_popup.png");
    $obImagem2->setAlign     ( "absmiddle" );
    $this->setImagem2        ( $obImagem2 );
    $this->setNull          ( true );

    $obCampoLabel = new Label;
    $this->setLabelIntervalo	( $obCampoLabel );
    $this->setMonitorarCampoCod ( false );

    $this->setDefinicao     ( "BUSCAINNER" );
}

/**
    * Monta o HTML do Objeto BuscaInnerIntervalo
    * @access Protected
*/
function montaHTML()
{
    if ( $this->getNomeBusca() ) {
        $stNomeBusca = $this->getNomeBusca();
        $this->obCampoCod->setName  ( "inCod".$stNomeBusca );
    }
    $this->obLabelIntervalo->montaHTML();
    $this->obCampoCod->montaHTML    ();
    $this->obImagem->montaHTML      ();
    $this->obCampoCod2->montaHTML   ();
    $this->obImagem2->montaHTML     ();

//    $stTitleImagem = preg_replace( "/[^[:alnum:]+]/","",$this->stRotulo);
    $stTitleImagem = strtolower(preg_replace("/\*/", "",$this->stRotulo));
    $stLink  = "&nbsp;<a href=\"JavaScript: ".$this->getFuncaoBusca().";\" title='Buscar ".$stTitleImagem."'>";
    $stLink .= $this->obImagem->getHTML();
    $stLink .= "</a>";

    $stLink2 = "&nbsp;<a href=\"JavaScript: ".$this->getFuncaoBusca2().";\" title='Buscar ".$stTitleImagem."'>";
    $stLink2.= $this->obImagem2->getHTML();
    $stLink2.= "</a>";

    $obTabela = new Tabela;
    $obTabela->setCellPadding( 0 );
    $obTabela->setCellSpacing( 0 );
    $obTabela->setWidth( 100 );
    $obTabela->addLinha();

// campo cod inicial
        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setClass( "field" );
        $obTabela->ultimaLinha->ultimaCelula->setWidth( "12" );
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obCampoCod->getHTML() );
        $obTabela->ultimaLinha->commitCelula();
// açao do cod inicial
        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setWidth( "5" );
        $obTabela->ultimaLinha->ultimaCelula->setClass( "fieldleft" );
        $obTabela->ultimaLinha->ultimaCelula->setValign( "top" );
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stLink );
        $obTabela->ultimaLinha->commitCelula();
// label para intervalo
        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setWidth( "5" );
        $obTabela->ultimaLinha->ultimaCelula->setClass( "intervalo" );
        $obTabela->ultimaLinha->ultimaCelula->setValign( "top" );
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obLabelIntervalo->getHTML() );
        $obTabela->ultimaLinha->commitCelula();
// campo cod final
        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setClass( "field" );
        $obTabela->ultimaLinha->ultimaCelula->setWidth( "12" );
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obCampoCod2->getHTML() );
        $obTabela->ultimaLinha->commitCelula();
//ação do cod final
        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setWidth( "*" );
        $obTabela->ultimaLinha->ultimaCelula->setClass( "fieldleft" );
        $obTabela->ultimaLinha->ultimaCelula->setValign( "top" );
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stLink2 );
        $obTabela->ultimaLinha->commitCelula();
// comita linha
    $obTabela->commitLinha();
    $obTabela->montaHTML();
    $this->setHTML( $obTabela->getHTML() );
}

/**
    * Imprime o HTML do Objeto BuscaInnerIntervalo na tela (echo)
    * @access Public
*/
function show()
{
    $this->montaHTML();
    $stHTML = $this->getHTML();
    $stHTML =  trim( $stHTML )."\n";
}
}

?>
