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
    * Lista de configuração de Obras e Serviços de Engenharia
    * Data de Criação   : 21/09/2015
    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Michel Teixeira
    * 
    * $Id: LSManterConfiguracaoObrasServicos.php 63771 2015-10-08 13:39:13Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAObra.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoObrasServicos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao                 = $request->get('stAcao');
$inCodEntidade          = $request->get('inCodEntidade');
$stExercicio            = $request->get('stExercicio');
$inCodTipoObra          = $request->get('inCodTipoObra');
$stNroObra              = $request->get('stNroObra');
$stExercicioLicitacao   = $request->get('stExercicioLicitacao');
$inCodModalidade        = $request->get('inCodModalidade');
$arLicitacao            = explode('/', $request->get('inCodLicitacao'));
$inCodLicitacao         = $arLicitacao[0];

$stCaminho = CAM_GPC_TCMBA_INSTANCIAS."configuracao/";

if ($stAcao == 'alterar') {
    Sessao::write('arLink',$_REQUEST);
    $stCaminho = $pgForm;
} else {
    $stCaminho .= $pgProc;
}

$obTTCMBAObra = new TTCMBAObra;

$stFiltro = "";

if($inCodEntidade!='')
    $stFiltro .= "obra.cod_entidade = ".$inCodEntidade." AND ";

if($stExercicio!='')
    $stFiltro .= "obra.exercicio = '".$stExercicio."' AND ";

if($inCodTipoObra!='')
    $stFiltro .= "obra.cod_tipo = ".$inCodTipoObra." AND ";

if($stNroObra!='')
    $stFiltro .= "obra.nro_obra = '".$stNroObra."' AND ";

if($stExercicioLicitacao!='' && $inCodModalidade!='' && $inCodLicitacao!=''){
    $stFiltro .= "obra.exercicio_licitacao = '".$stExercicioLicitacao."' AND ";
    $stFiltro .= "obra.cod_modalidade = ".$inCodModalidade." AND ";
    $stFiltro .= "obra.cod_licitacao = ".$inCodLicitacao." AND ";
}

if ($stFiltro != '')
    $stFiltro = ' WHERE '.substr($stFiltro,0,strlen($stFiltro)-4);

$stOrder = " ORDER BY obra.exercicio, obra.cod_entidade, LPAD(obra.nro_obra::VARCHAR, 10, '0') ";

$obTTCMBAObra->recuperaObra($rsObra, $stFiltro, $stOrder);

$obLista = new Lista;
$obLista->setRecordSet( $rsObra );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Exercício");
$obLista->ultimoCabecalho->setWidth( 6 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entidade" );
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo Obra" );
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Número da Obra" );
$obLista->ultimoCabecalho->setWidth( 14 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Licitação" );
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[cod_entidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[nom_tipo]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[nro_obra]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[st_licitacao]" );
$obLista->commitDado();

$stLink = "&inCodEntidade=".$inCodEntidade."&stExercicio=".$stExercicio."&inCodTipoObra=".$inCodTipoObra."&stNroObra".$stNroObra;
$stLink .= "&stExercicioLicitacao=".$stExercicioLicitacao."&inCodModalidade=".$inCodModalidade."&inCodLicitacao=".$request->get('inCodLicitacao');

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodEntidade"     , "cod_entidade");
$obLista->ultimaAcao->addCampo("&inCodObra"         , "cod_obra");
$obLista->ultimaAcao->addCampo("&stExercicio"       , "exercicio");
$obLista->ultimaAcao->addCampo("&inCodTipo"         , "cod_tipo");
$obLista->ultimaAcao->addCampo("stNroObra"          , "nro_obra");
$obLista->ultimaAcao->addCampo("&stDescQuestao"     , "[nro_obra]/[exercicio]");
//$obLista->ultimaAcao->setLink ($stCaminho."?".Sessao::getId()."&stAcao=".$stAcao.$stLink);
$obLista->ultimaAcao->setLink ($stCaminho."?stAcao=".$stAcao."&".Sessao::getId().$stLink);
$obLista->commitAcao();
$obLista->show();

?>