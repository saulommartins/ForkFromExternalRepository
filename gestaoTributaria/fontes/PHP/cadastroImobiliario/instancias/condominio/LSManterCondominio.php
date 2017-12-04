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
    * Página de Formulário da Caonfiguração do cadastro imobiliario
    * Data de Criação   : 18/03/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: LSManterCondominio.php 63230 2015-08-05 20:49:42Z arthur $

    * Casos de uso: uc-05.01.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterCondominio";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgFormAlterar = "FM".$stPrograma.".php";
$pgFormReforma = "FM".$stPrograma."Reforma.php";
$pgFormCaracteristica = "FM".$stPrograma."Caracteristica.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$stCaminho = CAM_GT_CIM_INSTANCIAS."condominio/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//DEFINE LISTA
$obRCIMCondominio = new RCIMCondominio;
$rsLista          = new RecordSet;

//MANTEM FILTRO E PAGINACAO
$link = '';
$stLink .= "&stAcao=".$stAcao;
if ( $request->get("pg") and  $request->get("pos") ) {
    $link["pg"]  = $request->get("pg");
    $link["pos"] = $request->get("pos");
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
Sessao::write('link', $link);
Sessao::write('stLink', $stLink);

//DEFINICAO DO FILTRO PARA CONSULTA
$stLink = "";

if ($_REQUEST["inCodigoCondominio"]) {
    $obRCIMCondominio->setCodigoCondominio( $_REQUEST["inCodigoCondominio"] );
    $stLink .= "&inCodigoCondominio=".$_REQUEST["inCodigoCondominio"];
}
if ($_REQUEST["stNomCondominio"]) {
    $obRCIMCondominio->setNomCondominio( $_REQUEST["stNomCondominio"] );
    $stLink .= "&stNomCondominio=".$_REQUEST["stNomCondominio"];
}
if ($_REQUEST["inCodigoTipo"] <> "") {
    $obRCIMCondominio->setCodigoTipo( $_REQUEST["inCodigoTipo"] );
    $stLink .= "&inCodigoTipo=".$_REQUEST["inCodigoTipo"];
}
if ($_REQUEST["inNumCGM"]) {
    $obRCIMCondominio->obRCGM->setNumCgm( $_REQUEST["inNumCGM"] );
    $stLink .= "&inNumCGM=".$_REQUEST["inNumCGM"];
}
$stLink .= "&stAcao=".$stAcao;

Sessao::write('stLink', $stLink);

$obRCIMCondominio->listarCondominios( $rsLista );

$rsLista->addFormatacao( "area_total_comum", "NUMERIC_BR" );

//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome");
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Tipo");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_condominio" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_condominio" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_tipo" );
$obLista->commitDado();

// Define ACOES
$obLista->addAcao();
$obLista->ultimaAcao->addCampo("&inCodigoCondominio" , "cod_condominio"  );
$obLista->ultimaAcao->addCampo("&stNomCondominio"    , "nom_condominio"  );
$obLista->ultimaAcao->addCampo("&inCodLote"          , "cod_lote"        );
$obLista->ultimaAcao->addCampo("&stValorLote"        , "valor"           );
$obLista->ultimaAcao->addCampo("&stNomLocalizacao"   , "nom_localizacao" );
$obLista->ultimaAcao->addCampo("&stLocalizacao"      , "codigo_composto" );

if ($stAcao == "alterar") {
    $stAcao = "alterar";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"  , "cod_processo"     );
    $obLista->ultimaAcao->addCampo("&inExercicio"       , "ano_exercicio"    );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"      , "cod_tipo"         );
    $obLista->ultimaAcao->addCampo("&inAreaTotalComum"  , "area_total_comum" );
    $obLista->ultimaAcao->addCampo("&inNumCGM"          , "numcgm"           );
    $obLista->ultimaAcao->addCampo("&stNomCGM"          , "nom_cgm"          );
    $obLista->ultimaAcao->setLink( $pgFormAlterar."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
} elseif ($stAcao == "excluir") {
    $stAcao = "excluir";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&stDescQuestao" , "[cod_condominio] - [nom_condominio]"   );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
} elseif ($stAcao == "historico") {
    $stAcao = "historico";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"  , "cod_processo"     );
    $obLista->ultimaAcao->addCampo("&inExercicio"       , "ano_exercicio"    );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"      , "cod_tipo"         );
    $obLista->ultimaAcao->addCampo("&stNomTipo"         , "nom_tipo"         );
    $obLista->ultimaAcao->addCampo("&inAreaTotalComum"  , "area_total_comum" );
    $obLista->ultimaAcao->addCampo("&inNumCGM"          , "numcgm"           );
    $obLista->ultimaAcao->addCampo("&stNomCGM"          , "nom_cgm"          );
    $obLista->ultimaAcao->setLink( $pgFormCaracteristica."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
} elseif ($stAcao == "reforma") {
    $stAcao = "reforma";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"  , "cod_processo"     );
    $obLista->ultimaAcao->addCampo("&inExercicio"       , "ano_exercicio"    );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"      , "cod_tipo"         );
    $obLista->ultimaAcao->addCampo("&stNomTipo"         , "nom_tipo"         );
    $obLista->ultimaAcao->addCampo("&inAreaTotalComum"  , "area_total_comum" );
    $obLista->ultimaAcao->addCampo("&inNumCGM"          , "numcgm"           );
    $obLista->ultimaAcao->addCampo("&stNomCGM"          , "nom_cgm"          );
    $obLista->ultimaAcao->setLink( $pgFormReforma."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
}

$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.14" );
$obFormulario->show();

?>