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
    * Página de Lista das Especificação de Identificadores de Uso
    * Data de Criação   : 30/10/2007

    * @author Desenvolvedor: Anderson cAko Konze

    $Id: LSIdentificadorUso.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "IdentificadorUso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'excluir'  : $pgProx = $pgProc; break;
    DEFAULT         : $pgProx = $pgForm;
}

$arFiltro = Sessao::read('filtro');
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro['filtro'][$stCampo] = $stValor;
    }
    $inPg = $_GET['pg'] ? $_GET['pg'] : 0;
    $inPos = $_GET['pos']? $_GET['pos'] : 0;
    $boPaginando = true;

    Sessao::write('filtro',$arFiltro);
    Sessao::write('pg',$inPg);
    Sessao::write('pos',$inPos);
    Sessao::write('paginando',$boPaginando);
} else {
    $inPg = $_GET['pg'];
    $inPos = $_GET['pos'];
    foreach ($arFiltro['filtro'] AS $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
}

$stLink .= "&stAcao=".$stAcao;

include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoIdentificadorUso.class.php");
$obTOrcamentoIdentificadorUso = new TOrcamentoIdentificadorUso;
$obTOrcamentoIdentificadorUso->setDado('exercicio', Sessao::getExercicio() );

if ($stAcao == 'excluir') {
    $stFiltro .= "
        AND NOT EXISTS ( SELECT 1
                           FROM orcamento.recurso_destinacao
                          WHERE recurso_destinacao.exercicio = identificador_uso.exercicio
                            AND recurso_destinacao.cod_uso   = identificador_uso.cod_uso
                       )
    ";
}
if ($_REQUEST['inCodIdUso']) {
    $stFiltro .= " AND cod_uso = ".$_REQUEST['inCodIdUso']." ";
}
if ($_REQUEST['stDescricao']) {
    $stFiltro .= " AND descricao ilike '%".$_REQUEST['stDescricao']."%' ";
}

$stFiltro .= " ORDER BY cod_uso ";

$obTOrcamentoIdentificadorUso->recuperaTodos( $rsIdUso, " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_uso is not null ".$stFiltro );

$obLista = new Lista;
$obLista->setRecordSet( $rsIdUso);
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("IDUSO");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição");
$obLista->ultimoCabecalho->setWidth( 75 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_uso" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
if ($stAcao == 'excluir') {
    $obLista->ultimaAcao->addCampo("&cod_uso"      , "cod_uso"   );
    $obLista->ultimaAcao->addCampo("&stDescricao"  , "descricao"   );
    $obLista->ultimaAcao->addCampo("&stDescQuestao", "[cod_uso] - [descricao]" );
    $obLista->ultimaAcao->setLink( CAM_GF_ORC_INSTANCIAS."destinacaoRecursos/".$pgProx."?".Sessao::getId().$stLink."&frameDestino=oculto" );
} else {
    $obLista->ultimaAcao->addCampo("&inCodIdUso"  , "cod_uso"     );
    $obLista->ultimaAcao->addCampo("&stDescricao" , "descricao"   );
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
?>
