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
    * Gerar o componente o SelectRegSubCarEsp
    * Data de Criação: 19/06/2006

    * @author Analista: Vandre Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package beneficios
    * @subpackage componentes

    Casos de uso: uc-04.04.00

*/

include_once ( CAM_GRH_PES_COMPONENTES."ISelectFuncao.class.php"                        );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectCargo.class.php"                         );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectRegime.class.php"                        );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectSubDivisao.class.php"                    );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectEspecialidade.class.php"                 );

/**
    * Cria o componente Select para Regime/SubDivisão/Cargo/Especialidade
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package beneficios
    * @subpackage componentes
*/
class ISelectRegSubCarEsp
{
/**
   * @access Private
   * @var Object
*/
var $obHdnHidden;
/**
   * @access Private
   * @var Object
*/
var $obISelectRegime;
/**
   * @access Private
   * @var Object
*/
var $obISelectSubDivisao;
/**
   * @access Private
   * @var Object
*/
var $obISelectCargo;
/**
   * @access Private
   * @var Object
*/
var $obISelectFuncao;
/**
   * @access Private
   * @var Object
*/
var $obISelectEspecialidade;
/**
   * @access Private
   * @var Boolean
*/
var $boFuncao;

/**
    * @access Public
    * @param Object $valor
*/
function setHiddenEval($valor) { $this->obHiddenEval         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setISelectRegime($valor) { $this->obISelectRegime         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setISelectSubDivisao($valor) { $this->obISelectSubDivisao     = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setISelectCargo($valor) { $this->obISelectCargo          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setISelectFuncao($valor) { $this->obISelectFuncao         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setISelectEspecialidade($valor) { $this->obISelectEspecialidade  = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setFuncao($valor) { $this->boFuncao            = $valor; }

/**
    * @access Public
    * @return Object
*/
function getHiddenEval() { return $this->obHiddenEval;         }
/**
    * @access Public
    * @return Object
*/
function getISelectRegime() { return $this->obISelectRegime;         }
/**
    * @access Public
    * @return Object
*/
function getISelectSubDivisao() { return $this->obISelectSubDivisao;     }
/**
    * @access Public
    * @return Object
*/
function getISelectCargo() { return $this->obISelectCargo;          }
/**
    * @access Public
    * @return Object
*/
function getISelectFuncao() { return $this->obISelectFuncao;        }
/**
    * @access Public
    * @return Object
*/
function getISelectEspecialidade() { return $this->obISelectEspecialidade;  }
/**
    * @access Public
    * @return Boolean
*/
function getFuncao() { return $this->boFuncao;            }

/**
    * Método Construtor
    * @access Public
*/
function ISelectRegSubCarEsp($boFuncao = false)
{
    $this->setFuncao                ( $boFuncao                         );
    $this->setHiddenEval            ( new HiddenEval                    );
    $this->obHiddenEval->setName    ( "hdnHiddenEvalRegSubCarEsp"       );
    $this->setISelectRegime         ( new ISelectRegime                 );
    $stName = $this->obISelectRegime->obTxtRegime->getName();
    if ( $this->getFuncao() ) {
        $this->obISelectRegime->obCmbRegime->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCISelectRegSubCarEsp.php?".Sessao::getId()."&".$stName."='+this.value, 'preencherSubDivisaoFuncao' );");
    } else {
        $this->obISelectRegime->obCmbRegime->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCISelectRegSubCarEsp.php?".Sessao::getId()."&".$stName."='+this.value, 'preencherSubDivisao' );");
    }
    $this->obISelectRegime->obCmbRegime->setNull(false);
    $this->obISelectRegime->obTxtRegime->setNull(false);

    $this->setISelectSubDivisao     ( new ISelectSubDivisao(false)      );
    $stName = $this->obISelectSubDivisao->obTxtSubDivisao->getName();
    if ( $this->getFuncao() ) {
        $this->obISelectSubDivisao->obCmbSubDivisao->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCISelectRegSubCarEsp.php?".Sessao::getId()."&".$stName."='+this.value, 'preencherFuncao' );");
        $this->obISelectSubDivisao->obCmbSubDivisao->setNull(false);
        $this->obISelectSubDivisao->obTxtSubDivisao->setNull(false);
    } else {
        $this->obISelectSubDivisao->obCmbSubDivisao->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCISelectRegSubCarEsp.php?".Sessao::getId()."&".$stName."='+this.value, 'preencherCargo' );");
        $this->obISelectSubDivisao->obCmbSubDivisao->setNull(false);
        $this->obISelectSubDivisao->obTxtSubDivisao->setNull(false);
    }

    $this->setISelectCargo          ( new ISelectCargo(false)           );
    $stName = $this->obISelectCargo->obTxtCargo->getName();
    $this->obISelectCargo->obCmbCargo->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCISelectRegSubCarEsp.php?".Sessao::getId()."&".$stName."='+this.value, 'preencherEspecialidade' );");
    $this->obISelectCargo->obCmbCargo->setNull(false);
    $this->obISelectCargo->obTxtCargo->setNull(false);

    $this->setISelectFuncao         ( new ISelectFuncao(false)          );
    $stName = $this->obISelectFuncao->obTxtFuncao->getName();
    $this->obISelectFuncao->obCmbFuncao->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCISelectRegSubCarEsp.php?".Sessao::getId()."&".$stName."='+this.value, 'preencherEspecialidade' );");
    $this->obISelectFuncao->obCmbFuncao->setNull(false);
    $this->obISelectFuncao->obTxtFuncao->setNull(false);

    $this->setISelectEspecialidade  ( new ISelectEspecialidade(false)   );
}

/**
    * Monta os combos de competencia
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $obFormulario->addHidden( $this->obHiddenEval,true );
    $this->obISelectRegime->geraFormulario($obFormulario);
    $this->obISelectSubDivisao->geraFormulario($obFormulario);
    if ( $this->getFuncao() ) {
        $this->obISelectFuncao->geraFormulario($obFormulario);
    } else {
        $this->obISelectCargo->geraFormulario($obFormulario);
    }
    $this->obISelectEspecialidade->geraFormulario($obFormulario);
}

}

?>
