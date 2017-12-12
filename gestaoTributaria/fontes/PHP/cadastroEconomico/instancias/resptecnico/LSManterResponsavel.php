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
    * Lista para Economico >> Responsavel Tecnico
    * Data de Criação   : 18/04/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: LSManterResponsavel.php 63839 2015-10-22 18:08:07Z franver $

    *Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.9  2006/09/15 14:33:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMResponsavelTecnico.class.php" );
include_once ( CAM_GA_CSE_NEGOCIO."RProfissao.class.php" );
include_once ( CAM_GA_CSE_NEGOCIO."RConselho.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RAdministracaoUF.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterResponsavel";
$pgFormInc     = "FM".$stPrograma."Inclusao.php";
$pgFormAlt     = "FM".$stPrograma."Alteracao.php";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$stCaminho = CAM_GT_CEM_INSTANCIAS."resptecnico/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

// INSTANCIA REGRAS UTILIZADAS

$obRConselho                = new RConselho                 ;
$obRCEMResponsavelTecnico   = new RCEMResponsavelTecnico    ;
$obRProfissao               = new RProfissao                ;
$obRUF                      = new RUF                       ;

//MANTEM FILTRO E PAGINACAO

$stLink .= "&stAcao=".$stAcao;
$link = Sessao::read( "link" );
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

Sessao::write( "link", $link );
//DEFINICAO DO FILTRO PARA CONSULTA
$stLink = "";
if ($_REQUEST["inCodigoProfissao"]) {
    $obRCEMResponsavelTecnico->obRProfissao->setCodigoProfissao( $_REQUEST["inCodigoProfissao"] );
    $stLink .= "&inCodigoProfissao=".$_REQUEST["inCodigoProfissao"];
}
if ($_REQUEST["inNumCGM"]) {
    $obRCEMResponsavelTecnico->setNumCgm( $_REQUEST["inNumCGM"] );
    $stLink .= "&inNumCGM=".$_REQUEST["inNumCGM"];
}
if ($_REQUEST["stRegistro"]) {
    $obRCEMResponsavelTecnico->setNumRegistro( $_REQUEST["stRegistro"] );
    $stLink .= "&stRegistro=".$_REQUEST["stRegistro"];
}
if ($_REQUEST["inCodigoUf"]) {
    $obRCEMResponsavelTecnico->setCodigoUF( $_REQUEST["inCodigoUf"] );
    $stLink .= "&inCodigoUf=".$_REQUEST["inCodigoUf"];
}
$obRCEMResponsavelTecnico->listarResponsavelTecnico($rsRespTecnico);

$stLink .= "&stAcao=".$stAcao;


//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsRespTecnico );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CGM ");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Profissão");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Registro");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_profissao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[nom_registro]-[sigla_uf]-[num_registro]" );
$obLista->commitDado();

// Define ACOES
if ($stAcao == "alterar") {
    $obLista->addAcao();
    $stAcao = "alterar";
    $obLista->ultimaAcao->addCampo("&inCodigoProfissao" , "cod_profissao" );
    $obLista->ultimaAcao->addCampo("&inNumCGM"          , "numcgm"        );
    $obLista->ultimaAcao->addCampo("&stNomCGM"          , "nom_cgm"       );
    $obLista->ultimaAcao->addCampo("&stNomRegistro"     , "nom_registro"  );
    $obLista->ultimaAcao->addCampo("&stRegistro"        , "num_registro"  );
    $obLista->ultimaAcao->addCampo("&stUF"              , "cod_uf"  );
    $obLista->ultimaAcao->addCampo("&inSequencia"       , "sequencia"  ); //novo cc

    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->setLink( $pgFormAlt."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "excluir") {
    $obLista->addAcao();
    $stAcao = "excluir";
    $obLista->ultimaAcao->addCampo("&inCodigoProfissao" , "cod_profissao" );
    $obLista->ultimaAcao->addCampo("&inNumCGM"          , "numcgm"        );
    $obLista->ultimaAcao->addCampo("&stRegistro"        , "[nom_registro] [num_registro] [sigla_uf]"  );
    $obLista->ultimaAcao->addCampo("&stDescQuestao"     , "[numcgm] - [nom_cgm]"  );
    $obLista->ultimaAcao->addCampo("&inSequencia"       , "sequencia"  ); //novo cc

    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
}
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.02.04" );
$obFormulario->show();

?>
