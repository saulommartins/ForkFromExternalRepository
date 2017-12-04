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
  * Página de
  * Data de criação : 27/09/2006

    * @author Analista:
    * @author Programador: Lucas Teixeira Stephanou

    $Revision: 17477 $
    $Name$
    $Author: hboaventura $
    $Date: 2006-11-07 16:55:35 -0200 (Ter, 07 Nov 2006) $

    Caso de uso: uc-03.01.21
**/

/*
$Log$
Revision 1.4  2006/11/07 18:54:48  hboaventura
bug #7179#

Revision 1.3  2006/10/05 10:47:19  domluc
Corrigido caso de uso

Revision 1.2  2006/09/29 11:11:46  domluc
Data de Aquisição e Placa incluidas como filtro

Revision 1.1  2006/09/27 17:50:31  domluc
Caso de Uso 03.01.21

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                                                              );
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                      );
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php");
include_once( CAM_GP_PAT_NEGOCIO."RPatrimonioAtributoPatrimonio.class.php");

class RPatrimonioRelatorioListaPatrimonial extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRPatrimonioNatureza;
/**
    * @var Object
    * @access Private
*/
var $obRPatrimonioGrupo;
/**
    * @var Object
    * @access Private
*/
var $obRPatrimonioEspecie;
/**
    * @var Object
    * @access Private
*/
var $inCodNatureza;
/**
    * @var Object
    * @access Private
*/

var $inCodGrupo;
/**
    * @var Object
    * @access Private
*/

var $inCodOrgao;
/**
    * @var Object
    * @access Private
*/

var $inCodUnidade;
/**
    * @var Object
    * @access Private
*/

var $inCodDepartamento;
/**
    * @var Object
    * @access Private
*/

var $inCodSetor;
/**
    * @var Object
    * @access Private
*/

var $inCodLocal;
/**
    * @var Object
    * @access Private
*/
var $inCodEspecie;
/**
    * @var Object
    * @access Private
*/

var $obRRelatorio;

/**
    * @var Object
    * @access Private
*/
var $obTPatrimononioBem;
/**
    * @var Object
    * @access Private
*/
var $obRPatrimonioRelatorioListaPatrimonial;
/**
    * @var Object
    * @access Private
*/
var $stTipoRelatorio;
/**
    * @var Object
    * @access Private
*/
var $stHistorico;

/**
    * @var Object
    * @access Private
*/
var $inCodBemInicial;

/**
    * @var Object
    * @access Private
*/
var $inCodBemFinal;

/**
    * @var Integer
    * @access Private
*/
var $inCodFornecedor;

/**
    * @var Object
    * @access Private
*/
var $maxAtributo;
/**
    * @var Object
    * @access Private
*/
var $stDataInicial;
/**
    * @var Object
    * @access Private
*/
var $stDataFinal;
/**
    * @var Object
    * @access Private
*/
var $boQuebraPagina;
/**
    * @var String
    * @access Private
*/
var $arOrdem ;
/**
    * @var Object
    * @access Private
*/
var $stNumPlacaInicial;
/**
    * @var Object
    * @access Private
*/
var $stNumPlacaFinal;
/**
     * @access Public
     * @param Object $valor
*/
function setTipoRelatorio($valor) { $this->stTipoRelatorio      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/

function setHistorico($valor) { $this->stHistorico      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setMaxAtributo($valor) { $this->inMaxAtributo      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setCodBemInicial($valor) { $this->inCodBemInicial      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodBemFinal($valor) { $this->inCodBemFinal      = $valor; }

/**
     * @access Public
     * @param Integer $valor
*/
function setCodFornecedor($valor) { $this->inCodFornecedor      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setNumPlacaInicial($valor) { $this->stNumPlacaInicial   = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setNumPlacaFinal($valor) { $this->stNumPlacaFinal = $valor; }

/**
     * @access Public
     * @return Object
*/
function getTipoRelatorio() { return $this->stTipoRelatorio;                }

/**
     * @access Public
     * @return Object
*/
function getHistorico() { return $this->stHistorico;                }
/**
     * @access Public
     * @return Object
*/
function getMaxAtributo() { return $this->inMaxAtributo;                }

/**
     * @access Public
     * @return Object
*/
function getCodBemInicial() { return $this->stDataInicial;                }
/**
     * @access Public
     * @return Object
*/
function getCodBemFinal() { return $this->stDataFinal;                }

/**
     * @access Public
     * @param Object $valor
*/
function setOrdem($valor) { $this->arOrdem            = $valor; }
/**
/**
     * @access Public
     * @param Object $valor
*/
function setCodNatureza($valor) { $this->inCodNatureza      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodGrupo($valor) { $this->inCodGrupo      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodOrgao($valor) { $this->inCodOrgao      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodUnidade($valor) { $this->inCodUnidade      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodDepartamento($valor) { $this->inCodDepartamento      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodSetor($valor) { $this->inCodSetor      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodLocal($valor) { $this->inCodLocal      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEspecie($valor) { $this->inCodEspecie     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicial($valor) { $this->stDataInicial     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinal($valor) { $this->stDataFinal     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setQuebraPagina($valor) { $this->boQuebraPagina     = $valor; }
/*
     * @access Public
     * @return Object
*/
function getCodNatureza() { return $this->inCodNatureza;                }
/**
     * @access Public
     * @return Object
*/
function getCodGrupo() { return $this->inCodGrupo;                }
/**
     * @access Public
     * @return Object
*/
function getCodOrgao() { return $this->inCodOrgao;                }
/**
     * @access Public
     * @return Object
*/
function getCodUnidade() { return $this->inCodUnidade;                }
/**
     * @access Public
     * @return Object
*/
function getCodDepartamento() { return $this->inCodDepartamento;                }
/**
     * @access Public
     * @return Object
*/
function getCodSetor() { return $this->inCodSetor;                }
/**
     * @access Public
     * @return Object
*/
function getCodLocal() { return $this->inCodLocal;                }
/**
     * @access Public
     * @return Object
*/
function getCodEspecie() { return $this->inCodEspecie;                }
/**
     * @access Public
     * @return Object
*/
function getDataInicial() { return $this->stDataInicial;              }
/**
     * @access Public
     * @return Object
*/
function getDataFinal() { return $this->stDataFinal;                }
/**
     * @access Public
     * @return Object
*/
function getQuebraPagina() { return $this->boQuebraPagina;                }
/**
     * @access Public
     * @return Object
*/
function getOrdem() { return $this->arOrdem ;                }
/**
     * @access Public
     * @return Object
*/
function getNumPlacaInicial() { return $this->stNumPlacaInicial;          }
/**
     * @access Public
     * @return Object
*/
function getPlacaFinal() { return $this->stNumPlacaFinal;                }

/**
    * Método Construtor
    * @access Private
*/

function RPatrimonioRelatorioListaPatrimonial()
{
    $this->obRRelatorio                     = new RRelatorio;
    $this->obTPatrimononioBem               = new TPatrimonioBem;
}
/**
    * Método abstrato
    * @access Public
*/
function GeraRecordSet(&$rsRecordSet, $stFiltro="", $boTransacao="")
{
    $this->obTPatrimononioBem->setDado("inCodNatureza"     , $this->inCodNatureza);
    $this->obTPatrimononioBem->setDado("inCodGrupo"        , $this->inCodGrupo);
    $this->obTPatrimononioBem->setDado("inCodEspecie"      , $this->inCodEspecie);
    $this->obTPatrimononioBem->setDado("inCodBemInicial"   , $this->inCodBemInicial);
    $this->obTPatrimononioBem->setDado("inCodBemFinal"     , $this->inCodBemFinal);
    $this->obTPatrimononioBem->setDado("inCodFornecedor"   , $this->inCodFornecedor);
    $this->obTPatrimononioBem->setDado("inCodOrgao"        , $this->inCodOrgao);
    $this->obTPatrimononioBem->setDado("inCodUnidade"      , $this->inCodUnidade);
    $this->obTPatrimononioBem->setDado("inCodDepartamento" , $this->inCodDepartamento);
    $this->obTPatrimononioBem->setDado("inCodSetor"        , $this->inCodSetor);
    $this->obTPatrimononioBem->setDado("inCodLocal"        , $this->inCodLocal);
    $this->obTPatrimononioBem->setDado("stDataInicial"     , $this->stDataInicial);
    $this->obTPatrimononioBem->setDado("stDataFinal"       , $this->stDataFinal);

    $this->obTPatrimononioBem->setDado("stNumPlacaInicial" , $this->stNumPlacaInicial);
    $this->obTPatrimononioBem->setDado("stNumPlacaFinal"   , $this->stNumPlacaFinal);

    // setar filtro
    $stOrdem  =  implode( ' , ', $this->getOrdem() );

    $obErro = $this->obTPatrimononioBem->recuperaListaPatrimonialCompleta( $rsRecordSet , $stFiltro , $stOrdem , $boTransacao );
    if ( !$obErro->ocorreu()) {
        $rsRecordSet->addFormatacao("vl_bem","NUMERIC_BR_NULL");
    }

} //metodo

} //classe
