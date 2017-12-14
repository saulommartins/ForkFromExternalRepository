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

    * Data de Criação: 07/10/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    * @package URBEM
    * @subpackage

    $Id: $

    * Casos de uso :
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoContrato.class.php");
include_once(TLIC."TLicitacaoContratoAditivos.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterAditivoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProx = $pgForm;
$stCaminho = CAM_GP_COM_INSTANCIAS."contrato/";

//Define a função do arquivo, ex: incluir, alterar, anular, etc
$stAcao = $request->get('stAcao');
$stLink = "&stAcao=".$stAcao;

if ($_REQUEST['inNumContrato'] == "") {
    $_REQUEST['inNumContrato'] = $_REQUEST['inNumContratoBusca'];
}

$stLink = "&stAcao=".$stAcao."&inNumContratoBusca=".$_REQUEST['inNumContratoBusca'];

foreach ($_REQUEST as $key => $value) {
   $param[$key]= $value;
}

Sessao::write('dadosFiltro',$param);

switch ($stAcao) {
   case 'incluirCD':
      $stAcaoLink = "SELECIONAR";
   break;
   case 'alterarCD':
      $stAcaoLink = "ALTERAR";
   break;
   case 'anularCD':
      $stAcaoLink = "ANULAR";
   break;
}

$stFiltro = montaFiltrosConsulta();

$rsLista = new RecordSet;
$obLista = new Lista;

if ($stAcao == 'incluirCD') {
    $obTLicitacaoContrato = new TLicitacaoContrato;
    $obTLicitacaoContrato->recuperaNaoAnuladosContratadoCompraDireta($rsLista, $stFiltro );
} else {
    $obTLicitacaoContrato = new TLicitacaoContratoAditivos();
    $obTLicitacaoContrato->recuperaContratosAditivosCompraDireta($rsLista, $stFiltro );
}

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$rsLista->addFormatacao('valor_contratado', 'NUMERIC_BR');
$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Contrato cadastrados");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Contrato" );
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

if ($stAcao == "incluirCD") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data do Contrato" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
} else {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Aditivo" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Aditivo" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Contratado" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

//ADICIONAR DADOS

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[num_contrato]/[exercicio_contrato]" );
$obLista->commitDado();

if ($stAcao == "incluirCD") {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_assinatura" );
    $obLista->commitDado();
} else {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "[num_aditivo]/[exercicio_aditivo]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_assinatura" );
    $obLista->commitDado();
}

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cgm_contratado] - [nom_cgm]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcaoLink );
$obLista->ultimaAcao->addCampo( "&inNumContrato", "num_contrato" );
$obLista->ultimaAcao->addCampo( "&inCodEntidade", "cod_entidade" );
$obLista->ultimaAcao->addCampo( "&stExercicioContrato", "exercicio_contrato" );
if ($stAcao != 'incluirCD') {
    $obLista->ultimaAcao->addCampo( "&inNumeroAditivo", "num_aditivo" );
    $obLista->ultimaAcao->addCampo( "&stExercicioAditivo", "exercicio_aditivo" );
}
$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();

function montaFiltrosConsulta()
{
   if ($_REQUEST['inNumContrato'] != "") {
      $stFiltro .= " contrato.num_contrato = ". $_REQUEST['inNumContrato']." and ";
   }
   if ($_REQUEST['stExercicioContrato']  != "") {
      $stFiltro .= " contrato.exercicio = '". $_REQUEST['stExercicioContrato']."' and ";
   }
   if ($_REQUEST['dtContrato']  != "") {
      $stFiltro .= " contrato.dt_assinatura = to_date('". $_REQUEST['dtContrato']."','dd/mm/yyyy') and ";
   }
   if ($_REQUEST['inCodContratado']  != "") {
      $stFiltro .= " contrato.cgm_contratado = ".$_REQUEST['inCodContratado']." and ";
   }

   if ($_REQUEST["inNumCGM"]  != "" &&  $stAcao == "incluirCD" ) {
      $stFiltro .= " cgm_entidade.numcgm in (".$_REQUEST["inNumCGM"].") and ";
   } else if ($_REQUEST["inNumCGM"]  != "" && $_REQUEST["stAcao"] == "anularCD") {
        $stFiltro .= " cgm_entidade.numcgm in (".$_REQUEST["inNumCGM"].") and ";
   } else if ($_REQUEST["inNumCGM"]  != "" && $_REQUEST["stAcao"] != "incluirCD" && $_REQUEST["stAcao"] != "anularCD" ) {
      $stFiltro .= " cgm_entidade.numcgm in (".implode(",", $_REQUEST["inNumCGM"]).") and ";
   }

   if ($stAcao == "alterar") {
       if ($_REQUEST["inNumeroAditivo"]  != "") {
           $stFiltro .= " contrato_aditivos.num_aditivo = ".$_REQUEST["inNumeroAditivo"]." and ";
       }
       if ($_REQUEST["stExercioAditivo"]  != "") {
           $stFiltro .= " contrato_aditivos.exercicio = '".$_REQUEST["stExercioAditivo"]."' and ";
       }
   }

   $stFiltro .=  " NOT EXISTS (SELECT 1 
                             FROM licitacao.rescisao_contrato 
                            WHERE rescisao_contrato.exercicio_contrato = contrato.exercicio
                              AND rescisao_contrato.cod_entidade = contrato.cod_entidade
                              AND rescisao_contrato.num_contrato = contrato.num_contrato
                          ) and ";

   $stFiltro .=  " NOT EXISTS (SELECT 1 
                              FROM licitacao.contrato_anulado 
                             WHERE contrato_anulado.exercicio    = contrato.exercicio
                               AND contrato_anulado.cod_entidade = contrato.cod_entidade
                               AND contrato_anulado.num_contrato = contrato.num_contrato
                            ) and ";
   if ($_REQUEST['stAcao'] != "incluirCD") {
       $stFiltro .=  " NOT EXISTS (SELECT 1
                                 FROM licitacao.contrato_aditivos_anulacao
                                WHERE contrato_aditivos.exercicio_contrato = contrato_aditivos_anulacao.exercicio_contrato
                                  AND contrato_aditivos.cod_entidade = contrato_aditivos_anulacao.cod_entidade
                                  AND contrato_aditivos.num_contrato = contrato_aditivos_anulacao.num_contrato
                                  AND contrato_aditivos.exercicio = contrato_aditivos_anulacao.exercicio
                                  AND contrato_aditivos.num_aditivo = contrato_aditivos_anulacao.num_aditivo
                             ) and ";
   }

     $stFiltro = ($stFiltro) ?' WHERE '.substr($stFiltro,0,strlen($stFiltro)-4): '';

   return $stFiltro;
}

?>
