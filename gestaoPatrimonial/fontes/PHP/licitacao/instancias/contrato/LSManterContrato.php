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
* Página de Listagem dos Documentos
* Data de Criação   : 04/07/2007

* @author Analista: Gelson Gonsalves
* @author Desenvolvedor: Leandro André Zis

* @ignore

* $Id: LSManterContrato.php 66048 2016-07-12 19:37:02Z carlos.silva $

* Casos de uso: uc-03.05.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoContrato.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgAnular = "FMAnularContrato.php";

$stCaminho = CAM_GP_LIC_INSTANCIAS."contrato/";

$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

switch ($stAcao) {
    case 'consultar': $pgProx = 'FMConsultarContrato.php'; break;
    case 'alterar': $pgProx = $pgForm; break;
    case 'anular': $pgProx = $pgAnular; break;
    case 'rescindir':  $pgProx = 'FMManterRescindirContrato.php'; break;
}

$stLink = "&stAcao=".$stAcao;

$filtro = Sessao::read('filtro');
if ($_REQUEST['inCodLicitacao'] || $_REQUEST['stDataInicial'] || $_REQUEST['inCodMapa'] || $_REQUEST['inCodContrato']) {
    foreach ($_REQUEST as $key => $value) {
        $filtro[$key] = $value;
    }
} else {
    if ($filtro) {
        foreach ($filtro as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }
    Sessao::write('paginando', true);
}

Sessao::write('filtro', $filtro);
Sessao::write('pos', $request->get('pos'));
Sessao::write('pg', $request->get('pg'));
Sessao::write('paginando', $request->get('paginando'));

$obTLicitacaoContrato = new TLicitacaoContrato;
$rsLista = new RecordSet;

if ($_REQUEST['inCodLicitacao']) {
   $stFiltro .= " contrato_licitacao.cod_licitacao = ". $_REQUEST['inCodLicitacao']." \nand ";
}
if ($_REQUEST['inCodModalidade']) {
    $stFiltro .= " contrato_licitacao.cod_modalidade = ". $_REQUEST['inCodModalidade']." \nand ";
}
if ($_REQUEST['stMapaCompras']) {
    $arMapaCompras = explode('/', $_REQUEST['stMapaCompras']);

    $stFiltro .= " licitacao.cod_mapa = ".$arMapaCompras[0]." \nand ";
    $exercicio = $arMapaCompras[1] != "" ? $arMapaCompras[1] : Sessao::getExercicio();
    $stFiltro .= " licitacao.exercicio_mapa = ".$exercicio." \nand ";
}
if ($_REQUEST['inNumContrato']) {
   $stFiltro .= " contrato.numero_contrato = ". $_REQUEST['inNumContrato']." \nand ";
}
if ($_REQUEST['stDataInicial']) {
   $stFiltro .= " contrato.dt_assinatura between to_date('". $_REQUEST['stDataInicial']."','dd/mm/yyyy') and to_date('". $_REQUEST['stDataFinal']."', 'dd/mm/yyyy') \nand ";
}

if ($_REQUEST["inNumCGM"]) {
   $stFiltro .= " cgm_entidade.numcgm in (".implode(",", $_REQUEST["inNumCGM"]).") \nand ";
}

$stFiltro = ($stFiltro)?' and '.substr($stFiltro,0,strlen($stFiltro)-4):'';
$obTLicitacaoContrato->recuperaNaoAnuladosContratado($rsLista, $stFiltro );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$rsLista->addFormatacao('valor_contratado', 'NUMERIC_BR');
$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Contrato cadastrados");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entidade" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Contrato" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data do Contrato" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Valor Total" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_entidade] - [entidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[numero_contrato]/[exercicio_contrato]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_assinatura" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "valor_contratado" );
$obLista->commitDado();

$obLista->addAcao();

$obLista->ultimaAcao->setAcao ( $stAcao );
$obLista->ultimaAcao->addCampo( "&inNumContrato", "num_contrato" );
$obLista->ultimaAcao->addCampo( "inCodEntidade", "cod_entidade" );
$obLista->ultimaAcao->addCampo( "stExercicio", "exercicio_contrato" );
$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();
