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
* Página de Processamento
* Data de Criação   : 05/12/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Id: PRManterCalculoSalario.php 65913 2016-06-29 14:02:53Z carlos.silva $

* Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalcularFolhas.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPensaoEvento.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFeriasEvento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDecimoEvento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaPrevidencia.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSalarioFamilia.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrf.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFgts.class.php";

$stAcao = $request->get("stAcao");

$stPrograma = "ManterCalculoSalario";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;



$obRFolhaPagamentoCalcularFolhas = new RFolhaPagamentoCalcularFolhas();
$obRFolhaPagamentoCalcularFolhas->setTipoFiltro($request->get('stTipoFiltro'));

switch ($request->get('stTipoFiltro')) {
    case 'contrato':
    case 'cgm_contrato':
        $obRFolhaPagamentoCalcularFolhas->setCodigos(Sessao::read('arContratos'));
        break;
    case 'local':
        $obRFolhaPagamentoCalcularFolhas->setCodigos($request->get('inCodLocalSelecionados'));
        break;
    case 'lotacao':
        $obRFolhaPagamentoCalcularFolhas->setCodigos($request->get('inCodLotacaoSelecionados'));
        break;
    case "evento":
        $arCodEvento = array();

        $arEventos = Sessao::read("arEventos");
        foreach( $arEventos AS $key => $evento ){
            $arCodEvento[] = $evento['inCodEvento'];
        }

        $obRFolhaPagamentoCalcularFolhas->setCodigos($arCodEvento);
        break;
}

//Executa abertura da folha
//Procedimento identico ao utilizado na Abertura do Período de Movimentação
$obRFolhaPagamentoCalcularFolhas->procedimentoCalculo($boTransacao);

?>