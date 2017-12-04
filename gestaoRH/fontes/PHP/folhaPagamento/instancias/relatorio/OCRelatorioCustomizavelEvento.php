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
    * Oculto de Relatório Customizável de Eventos
    * Data de Criação: 17/04/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30930 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-03-26 15:13:59 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-04.05.51
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaComplementar.class.php"                         );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php"                                    );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                      );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploRegSubCarEsp.class.php"                          );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                               );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                 );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioCustomizavelEvento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function validaQuantidadeEventos()
{
    $inQtnEventos = Sessao::read('inQtnEventos');
    $stJs .= "obEventosSelecionados = f.inCodEventoSelecionados;                                                       \n";
    $stJs .= "obEventosDisponiveis  = f.inCodEventoDisponiveis;                                                        \n";
    $stJs .= "inQtnEventos = ".$inQtnEventos.";                                                                        \n";
    $stJs .= "if (obEventosSelecionados.length > inQtnEventos) {                                                       \n";
    $stJs .= "    ini  = obEventosSelecionados.length-1;                                                               \n";
    $stJs .= "    arEventosDisponiveis = new Array();                                                                  \n";
    $stJs .= "    while (ini >= inQtnEventos) {                                                                       \n";
    $stJs .= "        arEventosDisponiveis.unshift(obEventosSelecionados[ini]);                                        \n";
    $stJs .= "        ini--;                                                                                           \n";
    $stJs .= "    }                                                                                                    \n";
    $stJs .= "    for (ini=0;ini<=obEventosDisponiveis.length;ini++) {                                                   \n";
    $stJs .= "        arEventosDisponiveis.unshift(obEventosDisponiveis[ini]);                                         \n";
    $stJs .= "    }                                                                                                    \n";
    $stJs .= "    if (obEventosDisponiveis.length == 0) {                                                              \n";
    $stJs .= "        arEventosDisponiveis.shift();                                                                    \n";
    $stJs .= "    } else {                                                                                               \n";
    $stJs .= "        limpaSelect(obEventosDisponiveis,0);                                                             \n";
    $stJs .= "        arEventosDisponiveis.reverse();                                                                  \n";
    $stJs .= "    }                                                                                                    \n";
    $stJs .= "    for (ini=0;ini<arEventosDisponiveis.length;ini++) {                                                    \n";
    $stJs .= "        obEventosDisponiveis[ini] = arEventosDisponiveis[ini];                                           \n";
    $stJs .= "    }                                                                                                    \n";
    $stJs .= "    alertaAviso('@Podem ser selecionados no máximo ".$inQtnEventos." eventos.','form','erro','".Sessao::getId()."');         \n";
    $stJs .= "}                                                                                                        \n";

    return $stJs;
}

switch ($_REQUEST["stCtrl"]) {
    case "validaQuantidadeEventos":
        $stJs = validaQuantidadeEventos();
        break;

}

if ($stJs) {
    echo $stJs;
}

?>
