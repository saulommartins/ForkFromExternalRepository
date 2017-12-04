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
    * Página de lista para o cadastro de corretagem
    * Data de Criação   : 25/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: LSManterCorretagem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.13
*/

/*
$Log$
Revision 1.8  2006/09/18 10:30:25  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretagem.class.php"  );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretor.class.php"    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImobiliaria.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCorretagem";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgFormInc  = "FM".$stPrograma."Inclusao.php";
$pgFormAlt  = "FM".$stPrograma."Alteracao.php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
include_once( $pgJs );

$stCaminho = CAM_GT_CIM_INSTANCIAS."corretagem/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

// DEFINE LISTA
$rsLista = new RecordSet;

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
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

Sessao::write('link'  , $link);
Sessao::write('stLink', $stLink);

//DEFINICAO DO FILTRO PARA CONSULTA
//$stLink = "";
if ($stAcao == "excluir" && $_REQUEST["boTipoCorretagem"] == "corretor") {
    $obRCIMCorretor  = new RCIMCorretor;
    if ($_REQUEST["stCreciResponsavel"]) {
        $obRCIMCorretor->setRegistroCreci( $_REQUEST["stCreciResponsavel"] );
        $stLink .= "&stCreciResponsavel=".$_REQUEST["stCreciResponsavel"];
    }
    if ($_REQUEST["inNumCGM"]) {
        $obRCIMCorretor->obRCGMPessoaFisica->setNumCGM( $_REQUEST["inNumCGM"] );
        $stLink .= "&inNumCGM=".$_REQUEST["inNumCGM"];
    }
    $obRCIMCorretor->listarCorretores( $rsLista );
} elseif ($stAcao == "excluir") {
    $obRCIMImobiliaria  = new RCIMImobiliaria( new RCIMCorretor );
    if ($_REQUEST["stCreciResponsavel"]) {
        $obRCIMImobiliaria->setRegistroCreci( $_REQUEST["stCreciResponsavel"] );
        $stLink .= "&stCreciResponsavel=".$_REQUEST["stCreciResponsavel"];
    }
    if ($_REQUEST["inNumCGM"]) {
        $obRCIMImobiliaria->obRCGMPessoaJuridica->setNumCGM( $_REQUEST["inNumCGM"] );
        $stLink .= "&inNumCGM=".$_REQUEST["inNumCGM"];
    }
    $obRCIMImobiliaria->listarImobiliarias( $rsLista );
} else {
    $obRCIMCorretor  = new RCIMCorretor;
    $obRCIMImobiliaria  = new RCIMImobiliaria( new RCIMCorretor );
    if ($_REQUEST["stCreciResponsavel"]) {
        $obRCIMImobiliaria->setRegistroCreci( $_REQUEST["stCreciResponsavel"] );
        $obRCIMCorretor->setRegistroCreci( $_REQUEST["stCreciResponsavel"] );
        $stLink .= "&stCreciResponsavel=".$_REQUEST["stCreciResponsavel"];
    }
    if ($_REQUEST["inNumCGM"]) {
        $obRCIMImobiliaria->obRCGMPessoaJuridica->setNumCGM( $_REQUEST["inNumCGM"] );
        $obRCIMCorretor->obRCGMPessoaFisica->setNumCGM( $_REQUEST["inNumCGM"] );
        $stLink .= "&inNumCGM=".$_REQUEST["inNumCGM"];
    }
    $obRCIMImobiliaria->listarImobiliarias( $rsLista );
    $obRCIMCorretor->listarCorretores( $rsLista1 );
}

if ($rsLista1) {
    while (!$rsLista1->eof() ) {
        $arLista = array("creci"   => $rsLista1->getCampo("creci"),
                           "numcgm"  => $rsLista1->getCampo("numcgm"),
                           "nom_cgm" => $rsLista1->getCampo("nom_cgm")   );
        $rsLista->add($arLista);
        $rsLista1->proximo();
    }
}

$stLink .= "&stAcao=".$stAcao;
Sessao::write('stLink', $stLink);

//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "CGM" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CRECI");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome");
$obLista->ultimoCabecalho->setWidth( 62 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "numcgm"  );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "creci"   );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

// Define ACOES
if ($stAcao == "excluir") {
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&stRegistroCreci" , "creci"    );
    $obLista->ultimaAcao->addCampo("&stDescQuestao"   , "[creci]-[nom_cgm]"    );
    $obLista->ultimaAcao->setLink($stCaminho.$pgProc."?".Sessao::getId().$stLink."&stAcao=".$stAcao."&boTipoCorretagem=".$_REQUEST["boTipoCorretagem"] );
    $obLista->commitAcao();
} elseif ($stAcao == "alterar") {
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&stRegistroCreci"    , "creci"       );
    $obLista->ultimaAcao->addCampo("&inNumCGM"           , "numcgm"      );
    $obLista->ultimaAcao->addCampo("&stNomeCGM"          , "nom_cgm"     );
    $obLista->ultimaAcao->addCampo("&stCreciResponsavel" , "responsavel" );
    $obLista->ultimaAcao->addCampo("&stNomeResponsavel"  , "nome_resp"   );
    $obLista->ultimaAcao->setLink($pgFormAlt."?".Sessao::getId().$stLink."&stAcao=".$stAcao."&tipoBuscaCreci=corretor" );
    $obLista->commitAcao();
}
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.13" );
$obFormulario->show();

?>
