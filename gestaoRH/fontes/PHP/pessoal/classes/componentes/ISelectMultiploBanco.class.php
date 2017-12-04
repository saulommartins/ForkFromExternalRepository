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
* Gerar o componente o SelectMultiplo com os bancos
* Data de Criação: 09/11/2005

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Bruce Cruz de Sena

* @package beneficios
* @subpackage componentes

Casos de uso: uc-00.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php" );

/**
    * Cria o componente SelectMultiplo com a Lotação
    * @author Desenvolvedor: Andre Almeida

    * @package beneficios
    * @subpackage componentes
*/
class ISelectMultiploBanco extends SelectMultiplo
{
/**
    * @access Private
    * @var Object
*/
var $obRMonBanco;

/**
    * @access Public
    * @Param Object $valor
*/
function setTMonBanco($valor) { $this->obRMonBanco = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTMonBanco() { return $this->obRMonbanco; }

/**
    * Método Construtor
    * @access Public
*/
function ISelectMultiploBanco($boPreencherAgencia=true, $stFiltro='')
{
    parent::SelectMultiplo();
    include_once(CAM_GT_MON_MAPEAMENTO . "TMONBanco.class.php");

    $obTMapeamento = new TMONBanco();
    $obTMapeamento->recuperaBancos($rsDisponiveis, $stFiltro);

    $rsSelecionados = new Recordset;
    $this->setName       ( "inCodBanco"                        );
    $this->setRotulo     ( "Banco"                             );
    $this->setTitle      ( "Selecione um banco para o filtro." );
    $this->setNomeLista1 ( "inCodBancoDisponiveis"             );
    $this->setRecord1    ( $rsDisponiveis                      );
    $this->setCampoId1   ( "[cod_banco]"                       );
    $this->setCampoDesc1 ( "[num_banco] - [nom_banco]"         );
    $this->setStyle1     ( "width:300px"                       );
    $this->setNomeLista2 ( "inCodBancoSelecionados"            );
    $this->setRecord2    ( $rsSelecionados                     );
    $this->setCampoId2   ( "[cod_banco]"                       );
    $this->setCampoDesc2 ( "[num_banco] - [nom_banco]"         );
    $this->setStyle2     ( "width:300px"                       );

    if ($boPreencherAgencia) {
        $stUrl = CAM_GRH_PES_PROCESSAMENTO."OCSelectMultiploAgencia.php?". Sessao::getId();
        $stOnClick = "selecionaTodosSelect(document.frm.inCodBancoSelecionados);jQuery.post('".$stUrl."',jQuery('#frm').serialize(),function (data) {eval(data);},'html');";
    }

    $this->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
    $this->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
    $this->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
    $this->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
    $this->obSelect1->obEvento->setOnDblClick( $stOnClick );
    $this->obSelect2->obEvento->setOnDblClick( $stOnClick );

}

}

?>
