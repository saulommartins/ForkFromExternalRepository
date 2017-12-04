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
    * Gerar o componente o SelectMultiploCargo
    * Data de Criação: 16/03/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package beneficios
    * @subpackage componentes

    Casos de uso: uc-04.04.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';

/**
    * Cria o componente SelectMultiplo para Cargo
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package beneficios
    * @subpackage componentes
*/
class ISelectMultiploCargo
{
/**
   * @access Private
   * @var Object
*/
var $obCmbCargo;
/**
   * @access Private
   * @var Object
*/
var $obCmbFuncao;
/**
   * @access Private
   * @var Boolean
*/
var $boFuncao;

/**
    * @access Public
    * @param Object $valor
*/
function setCmbCargo($valor) { $this->obCmbCargo          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCmbFuncao($valor) { $this->obCmbFuncao         = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setFuncao($valor) { $this->boFuncao            = $valor; }

/**
    * @access Public
    * @return Object
*/
function getCmbCargo() { return $this->obCmbCargo;          }
/**
    * @access Public
    * @return Object
*/
function getCmbFuncao() { return $this->obCmbFuncao;        }
/**
    * @access Public
    * @return Boolean
*/
function getFuncao() { return $this->boFuncao;            }

/**
    * Método Construtor
    * @access Public
*/
function ISelectMultiploCargo($boFuncao = false)
{
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php" );
    $obTPessoalCargo = new TPessoalCargo();
    $stFiltro = ' ';
    $stOrdem = 'descricao';
    $obTPessoalCargo->recuperaTodos( $rsCargo, $stFiltro, $stOrdem );

    $this->setFuncao( $boFuncao );

    $this->obCmbCargo = new SelectMultiplo();
    $this->obCmbCargo->setName              ( 'inCodCargo'                                                  );
    $this->obCmbCargo->setRotulo            ( "Cargo"                                                       );
    $this->obCmbCargo->setTitle             ( "Selecione o(s) cargo(s)."                                    );
    $this->obCmbCargo->SetNomeLista1        ( "inCodCargoDisponiveis"                                       );
    $this->obCmbCargo->setCampoId1          ( '[cod_cargo]'                                                 );
    $this->obCmbCargo->setCampoDesc1        ( '[cod_cargo]-[descricao]'                                     );
    $this->obCmbCargo->setStyle1            ( "width: 300px"                                                );
    $this->obCmbCargo->SetRecord1           (  new recordset                                                );
    $this->obCmbCargo->SetNomeLista2        ( "inCodCargoSelecionados"                                      );
    $this->obCmbCargo->setCampoId2          ( '[cod_cargo]'                                                 );
    $this->obCmbCargo->setCampoDesc2        ( '[cod_cargo] - [descricao]'                                     );
    $this->obCmbCargo->setStyle2            ( "width: 300px"                                                );
    $this->obCmbCargo->SetRecord2           ( $rsCargo                                                      );

    $this->obCmbFuncao = new SelectMultiplo();
    $this->obCmbFuncao->setName              ( 'inCodFuncao'                                                 );
    $this->obCmbFuncao->setRotulo            ( "Função"                                                      );
    $this->obCmbFuncao->setTitle             ( "Selecione a(s) função(ões)."                                 );
    $this->obCmbFuncao->SetNomeLista1        ( 'inCodFuncaoDisponiveis'                                      );
    $this->obCmbFuncao->setCampoId1          ( '[cod_cargo]'                                                 );
    $this->obCmbFuncao->setCampoDesc1        ( '[cod_cargo] - [descricao]'                                   );
    $this->obCmbFuncao->setStyle1            ( "width: 300px"                                                );
    $this->obCmbFuncao->SetRecord1           (  new recordset                                                );
    $this->obCmbFuncao->SetNomeLista2        ( 'inCodFuncaoSelecionados'                                     );
    $this->obCmbFuncao->setCampoId2          ( '[cod_cargo]'                                                 );
    $this->obCmbFuncao->setCampoDesc2        ( '[cod_cargo] - [descricao]'                                   );
    $this->obCmbFuncao->setStyle2            ( "width: 300px"                                                );
    $this->obCmbFuncao->SetRecord2           ( $rsCargo                                                      );

}

/**
    * Monta os combos de competencia
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    if ( $this->getFuncao() ) {
        $obFormulario->addComponente( $this->obCmbFuncao                );
    } else {
        $obFormulario->addComponente( $this->obCmbCargo                 );
    }
}

}

?>
