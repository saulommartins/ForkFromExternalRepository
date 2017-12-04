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
/*
    * Lista de Cadastros de Apostilas de Contratos TCEMG
    * Data de Criação   : 06/05/2014
    
    * @author Analista      Silvia Martins Silva
    * @author Desenvolvedor Michel Teixeira
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: LSManterApostilaContrato.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContrato.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoApostila.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterApostilaContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProx = $pgForm;
$stCaminho = CAM_GPC_TCEMG_INSTANCIAS."configuracao/";

//Define a função do arquivo, ex: incluir, alterar, anular, etc
$stAcao = $request->get('stAcao');
$stLink = "&stAcao=".$stAcao;

$stFiltro = " WHERE ";
if ($_REQUEST['inNumContrato']) {
   $stFiltro .= " contrato.nro_contrato = ". $_REQUEST['inNumContrato']." and ";
}
if ($_REQUEST['stExercicioContrato']) {
   $stFiltro .= " contrato.exercicio = '". $_REQUEST['stExercicioContrato']."' and ";
}
if ($_REQUEST['dtContrato']) {
   $stFiltro .= " contrato.data_assinatura = to_date('". $_REQUEST['dtContrato']."','dd/mm/yyyy') and ";
}
if ($_REQUEST["inCodEntidade"]) {
   $stFiltro .= " contrato.cod_entidade in (".implode(",", $_REQUEST["inCodEntidade"]).") and ";
}

if ($stAcao == "alterar") {
   if ($_REQUEST["inNumeroApostila"]) {
      $stFiltro .= " TCA.cod_apostila = ".$_REQUEST["inNumeroApostila"]." \n and ";
   }
}

$stFiltro .= " (SELECT contrato_rescisao.cod_contrato FROM tcemg.contrato_rescisao
                WHERE contrato_rescisao.cod_contrato=contrato.cod_contrato
                AND contrato_rescisao.exercicio=contrato.exercicio
                AND contrato_rescisao.cod_entidade=contrato.cod_entidade) IS NULL \n";

$rsLista = new RecordSet;
$obLista = new Lista;

if ($stAcao == 'incluir') {   
   $obTTCEMGContrato = new TTCEMGContrato;
   $obTTCEMGContrato->recuperaTodos($rsContratos, $stFiltro, "contrato.cod_entidade,contrato.exercicio,contrato.nro_contrato");
} else {
    $obTTCEMGContratoApostila = new TTCEMGContratoApostila;
    $obTTCEMGContratoApostila->recuperaContratoApostila($rsContratos, $stFiltro, "contrato.cod_entidade,contrato.exercicio,contrato.nro_contrato,TCA.cod_apostila" );
}

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$rsLista->addFormatacao('vl_contrato', 'NUMERIC_BR');
$obLista->setRecordSet( $rsContratos );
$obLista->setTitulo("Contrato cadastrados");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entidade" );
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Contrato" );
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

if ($stAcao == "incluir") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data do Contrato" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
} else {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Apostila" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data da Apostila" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Objeto do Contrato" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

//ADICIONAR DADOS
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[nro_contrato]/[exercicio]" );
$obLista->commitDado();

if ($stAcao == "incluir") {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "data_assinatura" );
    $obLista->commitDado();
} else {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "[cod_apostila]/[exercicio_apostila]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "data_apostila" );
    $obLista->commitDado();
}

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "objeto_contrato" );
$obLista->commitDado();

$obLista->addAcao();
if ($stAcao == 'incluir') {
   $obLista->ultimaAcao->setAcao ( 'SELECIONAR' );
} elseif ($stAcao == 'alterar') {
   $obLista->ultimaAcao->setAcao ( 'ALTERAR' );
} else {
   $obLista->ultimaAcao->setAcao ( 'excluir' );
   $pgProx=$pgProc;
}
$obLista->ultimaAcao->addCampo( "&inNumContrato", "nro_contrato" );
$obLista->ultimaAcao->addCampo( "&inCodEntidade", "cod_entidade" );
$obLista->ultimaAcao->addCampo( "&stExercicioContrato", "exercicio" );

if ($stAcao != 'incluir') {
    $obLista->ultimaAcao->addCampo( "&inCodApostila", "cod_apostila" );
    $obLista->ultimaAcao->addCampo( "&stExercicioApostila", "exercicio_apostila" );
}
$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?stAcao=$stAcao".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();
?>
