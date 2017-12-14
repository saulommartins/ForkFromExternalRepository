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
    * Página de Listagem de Itens
    * Data de Criação   : 30/09/2014
    * @author Desenvolvedor: Evandro Melos
    $Id: LSEncerrarConta.php 61444 2015-01-16 17:32:17Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "EncerrarConta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";

$stCaminho   = CAM_GF_CONT_INSTANCIAS."planoConta/";

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "encerrar";
}

$arFiltro = Sessao::read('filtro');
if ( !Sessao::read('paginando')) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('filtro', $arFiltro);
    Sessao::write('pg', $_GET['pg'] ? $_GET['pg'] : 0);
    Sessao::write('pos', $_GET['pos']? $_GET['pos'] : 0);
    Sessao::write('paginando', true);
} else {
    Sessao::write('pg', $_GET['pg']);
    Sessao::write('pos', $_GET['pos']);
    $_REQUEST['inCodReduzido'] = $arFiltro['inCodReduzido'];
    $_REQUEST['stCodClass'   ] = $arFiltro['stCodClass'   ];
    $_REQUEST['stDescricao'  ] = $arFiltro['stDescricao'  ];
    $_REQUEST['inCodEntidade'] = $arFiltro['inCodEntidade'];
}

if ($_REQUEST['inCodEntidade']) {
   foreach ($_REQUEST['inCodEntidade'] as $value) {
       $stCodEntidade .= $value . " , ";
   }
}
$stCodEntidade = substr($stCodEntidade,0,strlen($stCodEntidade)-2);

$obRContabilidadePlanoBanco->setExercicio             ( Sessao::getExercicio()     );
$obRContabilidadePlanoBanco->setCodPlano              ( $_REQUEST['inCodReduzido'] );
$obRContabilidadePlanoBanco->setCodEstrutural         ( $_REQUEST['stCodClass']    );
$obRContabilidadePlanoBanco->setNomConta              ( $_REQUEST['stDescricao']   );
$obRContabilidadePlanoBanco->setCodigoEntidade        ( $stCodEntidade             );
$obRContabilidadePlanoBanco->setNumAgencia            ( $_REQUEST['inNumAgencia']  );
$obRContabilidadePlanoBanco->setNumBanco              ( $_REQUEST['inNumBanco']    );
$obRContabilidadePlanoBanco->setContaCorrente         ( $_REQUEST['stContaCorrente'] );
$obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($_REQUEST['inCodRecurso']);

if ($stAcao == "encerrar"){
    $obRContabilidadePlanoBanco->setFiltroEncerrado   ( "encerrar"                 );
}elseif ($stAcao == "excluir"){
    $obRContabilidadePlanoBanco->setFiltroEncerrado   ( "excluir"                  );
}

$obRContabilidadePlanoBanco->listarPlanoContaEntidade ( $rsLista , 'cod_estrutural');

$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
}

$obRContabilidadePlanoBanco->obROrcamentoEntidade->obRCGM->setNumCGM(Sessao::read('numCgm'));
$obRContabilidadePlanoBanco->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );
$obRContabilidadePlanoBanco->recuperaMascaraConta( $stMascara );

$rsLista->setPrimeiroElemento();
while ( !$rsLista->eof() ) {
    $rsLista->setCampo('cod_estrutural', SistemaLegado::doMask($rsLista->getCampo('cod_estrutural'), $stMascara));
    $rsLista->proximo();
}

$rsLista->setPrimeiroElemento();

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Classificação");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Reduzido");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição ");
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_estrutural" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_plano" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_conta" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "inCodPlano" , "cod_plano" );
$obLista->ultimaAcao->addCampo( "inCodBanco" , "cod_banco" );
$obLista->ultimaAcao->addCampo( "stNumBanco" , "num_banco" );
$obLista->ultimaAcao->addCampo( "inNumBanco" , "num_banco" );
$obLista->ultimaAcao->addCampo( "inCodAgencia" , "cod_agencia" );
$obLista->ultimaAcao->addCampo( "stCodAgencia" , "cod_agencia" );
$obLista->ultimaAcao->addCampo( "stNumAgencia" , "num_agencia" );
$obLista->ultimaAcao->addCampo( "inNumAgencia" , "num_agencia" );
$obLista->ultimaAcao->addCampo( "stExercicio", "exercicio" );
$obLista->ultimaAcao->addCampo( "inCodConta" , "cod_conta" );
$obLista->ultimaAcao->addCampo( "stNomConta" , "nom_conta" );
$obLista->ultimaAcao->addCampo( "stCodEstrutural", "cod_estrutural" );
$obLista->ultimaAcao->addCampo( "stContaCorrente", "conta_corrente" );
$obLista->ultimaAcao->addCampo( "inContaCorrente", "cod_conta_corrente" );
$obLista->ultimaAcao->addCampo( "inCodRecurso", "cod_recurso" );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("&stDescQuestao", "nom_conta");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink."&frameDestino=oculto&" );
} else {
    $obLista->ultimaAcao->addCampo("&stDescQuestao", "nom_conta");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink."&frameDestino=oculto&" );
}

$obLista->commitAcao();
$obLista->show();
?>
