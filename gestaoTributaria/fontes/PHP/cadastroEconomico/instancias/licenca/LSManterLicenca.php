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
    * Lista para Licença
    * Data de Criação   : 17/11/2004
    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fabio Bertoldi Rodrigues
    * @package URBEM
    * @subpackage Regra

    * $Id: LSManterLicenca.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
Revision 1.11  2006/11/23 11:08:54  cercato
bug #7537#

Revision 1.10  2006/10/17 16:37:37  dibueno
Alterações devido a utilização de componentes para BuscaInners

Revision 1.9  2006/09/15 14:33:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicenca.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterLicenca";
$pgFilt          = "FL".$stPrograma.".php";
$pgFiltAlterar   = "FLAlterarLicenca.php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgFormAtividade = "FMConcederLicencaAtividade.php";
$pgFormEspecial  = "FMConcederLicencaEspecial.php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
include_once( $pgJs );

$stCaminho = CAM_GT_CEM_INSTANCIAS."licenca/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//DEFINE LISTA
$obRCEMLicenca = new RCEMLicenca;
$rsLista       = new RecordSet;

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
$link = Sessao::read( "link" );
if ($_GET["pg"] and $_GET["pos"]) {
    $link["pg"] = $_GET["pg"];
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
$especieLicenca = ucfirst($_REQUEST["stAcao"]);

//DEFINICAO DO FILTRO PARA CONSULTA
$stLink = "";
if ($_REQUEST["inInscricaoEconomica"]) {
    $obRCEMLicenca->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST["inInscricaoEconomica"] );
    $stLink .= "&inInscricaoEconomica=".$_REQUEST["inInscricaoEconomica"];
}
if ($_REQUEST["inCGM"]) {
    $obRCEMLicenca->obRCGM->setNumCGM( $_REQUEST["inCGM"] );
    $stLink .= "&inNumCGM=".$_REQUEST["inCGM"];
}
if ($_REQUEST["stLicenca"]) {
    $newLicenca = explode ( "/" , $_REQUEST["stLicenca"] );
    $obRCEMLicenca->setCodigoLicenca( $newLicenca[0] );
    $stLink .= "&inCodigoLicenca=".$newLicenca[0];
    $obRCEMLicenca->setExercicio( $newLicenca[1] );
    $stLink .= "&stExercicio=".$newLicenca[1];
}
if ($_REQUEST["stAcao"] == atividade OR $_REQUEST["stAcao"] == especial) {
    $obRCEMLicenca->setEspecieLicenca( $especieLicenca );
    $stLink .= "&stEspecieLicenca=".$especieLicenca;
}

if ($_REQUEST["stAcao"] == "cancelar") {
    $obRCEMLicenca->listarLicencasSuspensasAtivas( $rsLista );
} elseif ($_REQUEST["stAcao"] == "suspender") {
    $obRCEMLicenca->listarLicencas( $rsLista );
} else {
    $obRCEMLicenca->listarLicencas( $rsLista );
}

$stLink .= "&stAcao=".$stAcao;

//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Número da Licença ");
$obLista->ultimoCabecalho->setWidth( 18 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição Econômica" );
$obLista->ultimoCabecalho->setWidth( 18 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "CGM" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome");
$obLista->ultimoCabecalho->setWidth( 44 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_licenca"         );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao_economica" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "numcgm"              );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm"             );

$obLista->commitDado();

// Define ACOES
if ($stAcao == "atividade") {
    $obLista->addAcao();
    $stAcao = "atividade";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoLicenca"     , "cod_licenca"         );
    $obLista->ultimaAcao->addCampo("&stExercicio"         , "exercicio"           );
    $obLista->ultimaAcao->addCampo("&stEspecieLicenca"    , "especie_licenca"     );
    $obLista->ultimaAcao->addCampo("&inInscricaoEconomica", "inscricao_economica" );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"    , "cod_processo"        );
    $obLista->ultimaAcao->addCampo("&stExercicioProcesso" , "exercicio_processo"  );
    $obLista->ultimaAcao->addCampo("&dtDataInicio"        , "dt_inicio"           );
    $obLista->ultimaAcao->addCampo("&dtDataTermino"       , "dt_termino"          );
    $obLista->ultimaAcao->addCampo("&inCodigoTipoDiversa" , "cod_tipo_diversa"    );
    $obLista->ultimaAcao->setLink( $pgFormAtividade."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "especial") {
    $obLista->addAcao();
    $stAcao = "especial";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoLicenca"     , "cod_licenca"         );
    $obLista->ultimaAcao->addCampo("&stExercicio"         , "exercicio"           );
    $obLista->ultimaAcao->addCampo("&stEspecieLicenca"    , "especie_licenca"     );
    $obLista->ultimaAcao->addCampo("&inInscricaoEconomica", "inscricao_economica" );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"    , "cod_processo"        );
    $obLista->ultimaAcao->addCampo("&stExercicioProcesso" , "exercicio_processo"  );
    $obLista->ultimaAcao->addCampo("&dtDataInicio"        , "dt_inicio"           );
    $obLista->ultimaAcao->addCampo("&dtDataTermino"       , "dt_termino"          );
    $obLista->ultimaAcao->addCampo("&inCodigoTipoDiversa" , "cod_tipo_diversa"    );
    $obLista->ultimaAcao->setLink( $pgFormEspecial."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "baixar") {
    $obLista->addAcao();
    $stAcao = "baixar";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoLicenca"     , "cod_licenca"         );
    $obLista->ultimaAcao->addCampo("&stExercicio"         , "exercicio"           );
    $obLista->ultimaAcao->addCampo("&dtDataConcessao"     , "dt_inicio"           );
    $obLista->ultimaAcao->addCampo("&stEspecieLicenca"    , "especie_licenca"     );
    $obLista->ultimaAcao->addCampo("&inNumCGM"            , "numcgm"              );
    $obLista->ultimaAcao->addCampo("&stNomeCGM"           , "nom_cgm"             );
    $obLista->ultimaAcao->addCampo("&inCodigoTipoDiversa" , "cod_tipo_diversa"    );
    $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "suspender") {
    $obLista->addAcao();
    $stAcao = "suspender";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoLicenca"     , "cod_licenca"         );
    $obLista->ultimaAcao->addCampo("&stExercicio"         , "exercicio"           );
    $obLista->ultimaAcao->addCampo("&dtDataConcessao"     , "dt_inicio"           );
    $obLista->ultimaAcao->addCampo("&stEspecieLicenca"    , "especie_licenca"     );
    $obLista->ultimaAcao->addCampo("&inNumCGM"            , "numcgm"              );
    $obLista->ultimaAcao->addCampo("&stNomeCGM"           , "nom_cgm"             );
    $obLista->ultimaAcao->addCampo("&inCodigoTipoDiversa" , "cod_tipo_diversa"    );
    $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "cancelar") {
    $obLista->addAcao();
    $stAcao = "cancelar";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoLicenca"     , "cod_licenca"         );
    $obLista->ultimaAcao->addCampo("&stExercicio"         , "exercicio"           );
    $obLista->ultimaAcao->addCampo("&stEspecieLicenca"    , "especie_licenca"     );
    $obLista->ultimaAcao->addCampo("&inNumCGM"            , "numcgm"              );
    $obLista->ultimaAcao->addCampo("&stNomeCGM"           , "nom_cgm"             );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"    , "cod_processo_baixa"  );
    $obLista->ultimaAcao->addCampo("&stExercicioProcesso" , "exercicio_processo"  );
    $obLista->ultimaAcao->addCampo("&dtDataSuspensao"     , "dt_susp_inicio"      );
    $obLista->ultimaAcao->addCampo("&dtDataTermino"       , "dt_susp_termino"     );
    $obLista->ultimaAcao->addCampo("&stMotivo"            , "motivo"              );
    $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "cassar") {
    $obLista->addAcao();
    $stAcao = "cassar";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoLicenca"     , "cod_licenca"         );
    $obLista->ultimaAcao->addCampo("&stExercicio"         , "exercicio"           );
    $obLista->ultimaAcao->addCampo("&stEspecieLicenca"    , "especie_licenca"     );
    $obLista->ultimaAcao->addCampo("&inNumCGM"            , "numcgm"              );
    $obLista->ultimaAcao->addCampo("&stNomeCGM"           , "nom_cgm"             );
    $obLista->ultimaAcao->addCampo("&inCodigoTipoDiversa" , "cod_tipo_diversa"    );
    $obLista->ultimaAcao->addCampo("&dtDataConcessao"     , "dt_inicio"           );
    $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
}
$obLista->show();

// DEFINE BOTOES
$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );
$botoes = array ($obBtnFiltro);

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

//DEFINE FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.02.12" );
$obFormulario->addHidden   ($obHdnAcao          );
$obFormulario->defineBarra ($botoes,'left',''   );
//$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
