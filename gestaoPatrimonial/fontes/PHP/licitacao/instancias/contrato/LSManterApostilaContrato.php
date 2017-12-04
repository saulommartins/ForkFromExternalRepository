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
    * Lista de Cadastros de Apostilas de Contratos
    * Data de Criação   : 25/02/2016
    
    * @author Analista:      Gelson W. Gonçalves  <gelson.goncalves@cnm.org.br>
    * @author Desenvolvedor: Carlos Adriano       <carlos.silva@cnm.org.br>
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: LSManterApostilaContrato.php 64923 2016-04-13 17:45:44Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once TLIC."TLicitacaoContrato.class.php";
include_once TLIC."TLicitacaoContratoApostila.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterApostilaContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProx = $pgForm;
$stCaminho = CAM_GP_LIC_INSTANCIAS."contrato/";

//Define a função do arquivo, ex: incluir, alterar, anular, etc
$stAcao = $request->get('stAcao');
$stLink = "&stAcao=".$stAcao;

$stFiltro = " WHERE ";
if ($_REQUEST['inNumContrato']) {
   $stFiltro .= " contrato.num_contrato = ". $_REQUEST['inNumContrato']." \nAND ";
}
if ($_REQUEST['stExercicioContrato']) {
   $stFiltro .= " contrato.exercicio = '". $_REQUEST['stExercicioContrato']."' \nAND ";
}
if ($_REQUEST['dtContrato']) {
   $stFiltro .= " contrato.dt_assinatura = to_date('". $_REQUEST['dtContrato']."','dd/mm/yyyy') \nAND ";
}
if ($_REQUEST["inCodEntidade"]) {
   $stFiltro .= " contrato.cod_entidade in (".implode(",", $_REQUEST["inCodEntidade"]).") \nAND ";
}

if ($stAcao == "alterar") {
   if ($_REQUEST["inNumeroApostila"]) {
      $stFiltro .= " TCA.cod_apostila = ".$_REQUEST["inNumeroApostila"]." \nAND ";
   }
}

$stFiltro .= " (SELECT rescisao_contrato.num_contrato 
                  FROM licitacao.rescisao_contrato
                 WHERE rescisao_contrato.num_contrato = contrato.num_contrato
                   AND rescisao_contrato.exercicio    = contrato.exercicio
                   AND rescisao_contrato.cod_entidade = contrato.cod_entidade) IS NULL  \n";


$rsLista = new RecordSet;
$obLista = new Lista;

if ($stAcao == 'incluir') {   
   $obTLicitacaoContrato = new TLicitacaoContrato;
   $obTLicitacaoContrato->recuperaTodos($rsContratos, $stFiltro, $stOrdem);
} else {
    $obTLicitacaoContratoApostila = new TLicitacaoContratoApostila;
    $obTLicitacaoContratoApostila->recuperaContratoApostila($rsContratos, $stFiltro, $stOrdem );
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
$obLista->ultimoDado->setCampo( "[num_contrato]/[exercicio]" );
$obLista->commitDado();

if ($stAcao == "incluir") {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_assinatura" );
    $obLista->commitDado();
} else {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "[cod_apostila]/[exercicio]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "data_apostila" );
    $obLista->commitDado();
}

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "objeto" );
$obLista->commitDado();

$obLista->addAcao();
if ($stAcao == 'incluir') {
   $obLista->ultimaAcao->setAcao ( 'SELECIONAR' );
} elseif ($stAcao == 'alterar') {
   $obLista->ultimaAcao->setAcao ( 'alterar' );
} else {
   $obLista->ultimaAcao->setAcao ( 'excluir' );
   $pgProx=$pgProc;
}
$obLista->ultimaAcao->addCampo( "&inNumContrato", "num_contrato" );
$obLista->ultimaAcao->addCampo( "&inCodEntidade", "cod_entidade" );
$obLista->ultimaAcao->addCampo( "&stExercicioContrato", "exercicio" );

if ($stAcao != 'incluir') {
    $obLista->ultimaAcao->addCampo( "&inCodApostila", "cod_apostila" );
    $obLista->ultimaAcao->addCampo( "&stExercicioApostila", "exercicio" );
}
$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?stAcao=$stAcao".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();