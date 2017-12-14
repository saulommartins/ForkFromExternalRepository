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

    * Página de Relatório de Demonstrativo da Aplicação nas Ações e Serviços Púb. de Saúde
    * Data de Criação   : 11/07/2014
    * @author Desenvolvedor: Eduardo Paculski Schitz
    * @ignore
    *   
    * $Id: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

$preview = new PreviewBirt(6, 55, 4);
$preview->setTitulo(' Demonstrativo da Aplicação nas Ações e Serviços Públicos de Saúde');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel( true );

$stFiltroAplicacao = " AND od.cod_entidade IN  (".implode(",", $_REQUEST["inCodEntidade"]).") AND od.cod_recurso = 102 AND od.cod_funcao = 10 AND od.cod_subfuncao IN (122,272,301,302,303,304,305) ";
$boRestos          = $request->get("stRestos") == "true" ? "true" : "false";

switch ($_REQUEST['stDemonstrarDespesa']){
    case "E": $stTipoSituacao = "empenhado"; break;
    case "L": $stTipoSituacao = "liquidado"; break;
    case "P": $stTipoSituacao = "pago";      break;
}

switch ($request->get('inPeriodicidade')) {
    case 1: // Dia
            $preview->addParametro('stDescricaoPeriodo' , "Dia: ".$request->get('stDia') );
    break;
    
    case 2: // Mês
            $preview->addParametro('stDescricaoPeriodo' , $request->get('stDataInicial')." at&eacute; ".$request->get('stDataFinal') );
    break;
    
    case 3: // Ano
            $preview->addParametro('stDescricaoPeriodo' ,  Sessao::getExercicio() );
    break;
    
    case 4: // Intervalo
            $preview->addParametro('stDescricaoPeriodo' , "Intervalo de ".$request->get('stPeriodoInicial')." at&eacute; ".$request->get('stPeriodoFinal') );
    break;
}

$preview->addParametro('stExercicio'      , Sessao::getExercicio()     );
$preview->addParametro('filtro_aplicacao' , $stFiltroAplicacao         );
$preview->addParametro('data_inicial'     , $_REQUEST['stDataInicial'] );
$preview->addParametro('data_final'       , $_REQUEST['stDataFinal']   );
$preview->addParametro('stTipoSituacao'   , $stTipoSituacao            );
$preview->addParametro('boRestos'         , $boRestos                  );
$preview->addParametro('entidades'        , implode(',', $_REQUEST['inCodEntidade']));
$preview->addParametro('demostrar_despesa', $_REQUEST['stDemonstrarDespesa']);

$arAssinaturas = Sessao::read('assinaturas');
$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();

?>