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
    * Página de Listagem de Atividades
    * Data de Criação   : 25/11/2004

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: LSManterAtividade.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.07

*/

/*
$Log$
Revision 1.8  2007/04/26 14:54:18  cercato
Bug #9220#

Revision 1.7  2006/09/15 14:32:31  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAtividade";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

$stCaminho = CAM_GT_CEM_INSTANCIAS."ativeconomica/";

$obRCEMAtividade = new RCEMAtividade;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar': $pgProx = $pgFormNivel; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
$link = Sessao::read( "link" );
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write( "link", $link );
$arTmpNivelVigencia = explode('-',$_REQUEST["inCodigoNivel"]);
$inCodigoNivel = $arTmpNivelVigencia[0];
$inCodigoVigencia = $arTmpNivelVigencia[1];

if (!$inCodigoVigencia) {
    $inCodigoVigencia = $_REQUEST["inVigenciaAtual"];
}

//MONTA O FILTRO
if ($_REQUEST["stValorComposto"]) {
    //RETIRA O PONTO FINAL DO VALOR COMPOSTO CASO EXISTA
    $obRCEMAtividade->setValorComposto( $_REQUEST["stValorComposto"] );
}
if ($_REQUEST["stNomeAtividade"]) {
    $obRCEMAtividade->setNomeAtividade( $_REQUEST["stNomeAtividade"] );
}
if ($inCodigoNivel) {
    $obRCEMAtividade->setCodigoNivel( $inCodigoNivel );
}
if ($inCodigoVigencia) {
    $obRCEMAtividade->setCodigoVigencia( $inCodigoVigencia );
}

$obRCEMAtividade->listarAtividadeAtualeProxima( $rsListaAtividade );

$obLista = new Lista;

$obLista->setRecordSet( $rsListaAtividade );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nível" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor_composto" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_nivel" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_atividade" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodigoVigencia",    "cod_vigencia"     );
$obLista->ultimaAcao->addCampo("&inCodigoNivel",       "cod_nivel"        );
$obLista->ultimaAcao->addCampo("&inCodigoAtividade",   "cod_atividade"    );
$obLista->ultimaAcao->addCampo("&stValorComposto",     "valor_composto"   );
$obLista->ultimaAcao->addCampo("&stValorReduzido",     "valor_reduzido"   );
$obLista->ultimaAcao->addCampo("&stNomeAtividade",     "nom_atividade"    );
$obLista->ultimaAcao->addCampo("&stDescQuestao",       "nom_atividade"    );
$obLista->ultimaAcao->addCampo("&flAliquota",          "aliquota"         );
$obLista->ultimaAcao->addCampo("&inCodCnae",           "cod_cnae"         );
$obLista->ultimaAcao->addCampo("&stValorCompostoCnae", "cod_estrutural_cnae");
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.02.07" );
$obFormulario->show();

?>
