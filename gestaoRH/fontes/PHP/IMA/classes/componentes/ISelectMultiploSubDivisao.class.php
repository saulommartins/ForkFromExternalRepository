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
    * Gerar o componente o SelectMultiploSubDivisao
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
    * Cria o componente SelectMultiplo para SubDivisão
    * @author Desenvolvedor: Tiago Finger

    * @package configuracao
    * @subpackage componentes
*/
class ISelectMultiploSubDivisao
{
/**
   * @access Private
   * @var Object
*/
var $obCmbSubDivisao;
/**
   *@acess Private
   *@var String
*/
var $stExtensao;
/**
    * @access Public
    * @param Object $valor
*/
function setCmbSubDivisao($valor) { $this->obCmbSubDivisao     = $valor; }
/**
    * @access Public
    * @return String
*/
function setExtensao($valor) { $this->stExtensao          = $valor; }
/**
    * @access Public
    * @return Object
*/
function getCmbSubDivisao() { return $this->obCmbSubDivisao;               }
/**
    * @access Public
    * @return String
*/
function getExtensao() { return $this->stExtensao;                    }
/**
    * Método Construtor
    * @access Public
*/
function ISelectMultiploSubDivisao($stExtensao = '')
{
    $this->setExtensao( $stExtensao );
    $this->obCmbSubDivisao = new SelectMultiplo();
    $this->obCmbSubDivisao->setName         ( "inCodSubDivisao$this->getExtensao()"                         );
    $this->obCmbSubDivisao->setRotulo       ( "Subdivisão"                                                  );
    $this->obCmbSubDivisao->setTitle        ( "Selecione a(s) subdivisão(ões)."                             );
    $this->obCmbSubDivisao->SetNomeLista1   ( 'inCodSubDivisaoDisponiveis'.$this->getExtensao()             );
    $this->obCmbSubDivisao->setCampoId1     ( '[cod_sub_divisao]'                                           );
    $this->obCmbSubDivisao->setCampoDesc1   ( '[nom_sub_divisao]'                                           );
    $this->obCmbSubDivisao->setStyle1       ( "width: 300px"                                                );
    $this->obCmbSubDivisao->SetRecord1      ( new recordset                                                 );
    $this->obCmbSubDivisao->SetNomeLista2   ( 'inCodSubDivisaoSelecionados'.$this->getExtensao()            );
    $this->obCmbSubDivisao->setCampoId2     ( '[cod_sub_divisao]'                                           );
    $this->obCmbSubDivisao->setCampoDesc2   ( '[nom_sub_divisao]'                                           );
    $this->obCmbSubDivisao->setStyle2       ( "width: 300px"                                                );
    $this->obCmbSubDivisao->SetRecord2      ( new recordset                                                 );
}

/**
    * Monta os combos de competencia:
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $obFormulario->addComponente    ( $this->obCmbSubDivisao            );
}

}

?>
