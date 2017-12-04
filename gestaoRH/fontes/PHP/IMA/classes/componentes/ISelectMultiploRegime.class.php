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
    * Gerar o componente o SelectMultiploRegime
    * Data de Criação: 21/03/2006

    * @author Analista: Dagiane
    * @author Desenvolvedor: Tiago Finger

    * @package ima
    * @subpackage componentes

    Casos de uso: uc-04.08.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalRegime.class.php" );
/**
    * Cria o componente SelectMultiplo para Regime
    * @author Desenvolvedor: Tiago Finger

    * @package configuracao
    * @subpackage componentes
*/
class ISelectMultiploRegime
{
/**
   * @access Private
   * @var Object
*/
var $obRPessoalRegime;
/**
   * @access Private
   * @var Object
*/
var $obCmbRegime;
/**
   *@acess Private
   *@var String
*/
var $stExtensao;
/**
   *@acess Private
   *@var String
*/
var $stOnClick;

/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalRegime($valor) { $this->obRPessoalRegime    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCmbRegime($valor) { $this->obCmbRegime         = $valor; }
/**
    * @access Public
    * @return String
*/
function setExtensao($valor) { $this->stExtensao          = $valor; }
/**
    * @access Public
    * @return Object valor
*/
function setFuncaoOnClick($valor)
{
    $this->obCmbRegime->obGerenciaSelects->obBotao1->obEvento->setOnClick( $valor );
    $this->obCmbRegime->obGerenciaSelects->obBotao2->obEvento->setOnClick( $valor );
    $this->obCmbRegime->obGerenciaSelects->obBotao3->obEvento->setOnClick( $valor );
    $this->obCmbRegime->obGerenciaSelects->obBotao4->obEvento->setOnClick( $valor );
    $this->obCmbRegime->obSelect1->obEvento->setOnDblClick( $valor );
    $this->obCmbRegime->obSelect2->obEvento->setOnDblClick( $valor );
}
/**
    * @access Public
    * @return Object
*/
function getRPessoalRegime() { return $this->obRPessoalRegime;    }
/**
    * @access Public
    * @return Object
*/
function getCmbRegime() { return $this->obCmbRegime;         }
/**
    * @access Public
    * @return String
*/
function getExtensao() { return $this->stExtensao;          }
/**
    * @access Public
    * @return String
*/
function getFuncaoOnClick() { return $this->stOnClick;        }
/**
    * Método Construtor
    * @access Public
*/
function ISelectMultiploRegime($stExtensao = '')
{
    $this->setExtensao( $stExtensao );
    $this->obRPessoalRegime = new RPessoalRegime;
    $this->obRPessoalRegime->listarRegime( $rsRegime );

    $this->obCmbRegime = new SelectMultiplo();
    $this->obCmbRegime->setName         ( "inCodRegime".$this->getExtensao()                                     );
    $this->obCmbRegime->setRotulo       ( "Regime"                                                               );
    $this->obCmbRegime->setTitle        ( "Selecione o(s) regime(s)."                                            );
    $this->obCmbRegime->SetNomeLista1   ( "inCodRegimeDisponiveis".$this->getExtensao()                          );
    $this->obCmbRegime->setCampoId1     ( '[cod_regime]'                                                         );
    $this->obCmbRegime->setCampoDesc1   ( '[descricao]'                                                          );
    $this->obCmbRegime->setStyle1       ( "width: 300px"                                                         );
    $this->obCmbRegime->SetRecord1      ( $rsRegime                                                              );
    $this->obCmbRegime->SetNomeLista2   ( "inCodRegimeSelecionados".$this->getExtensao()                        );
    $this->obCmbRegime->setCampoId2     ( "[cod_regime_".$this->getExtensao()."]"                                 );
    $this->obCmbRegime->setCampoDesc2   ( "[descricao_".$this->getExtensao()."]"                                 );
    $this->obCmbRegime->setStyle2       ( "width: 300px"                                                         );
    $this->obCmbRegime->SetRecord2      ( new recordset                                                          );
}

/**
    * Monta os combos de competencia
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $obFormulario->addComponente    ( $this->obCmbRegime                );
}
}

?>
