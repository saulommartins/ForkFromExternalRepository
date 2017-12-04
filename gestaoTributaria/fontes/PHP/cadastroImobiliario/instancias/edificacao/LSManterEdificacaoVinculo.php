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
    * Página de lista para o cadastro de edificação
    * Data de Criação   : 12/08/2004

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    * $Id: LSManterEdificacaoVinculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.7  2006/09/18 10:30:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"   );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEdificacao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgFormAlterar = "FM".$stPrograma."Alteracao.php";
$pgFormReforma = "FM".$stPrograma."Reforma.php";
$pgFormCaracteristica = "FM".$stPrograma."Caracteristica.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

$stCaminho = CAM_GT_CIM_INSTANCIAS."edificacao/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//DEFINE LISTA
$obRCIMEdificacao = new RCIMEdificacao;
$rsLista          = new RecordSet;

$stLink = "";

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
//$stLink .= "&stTipo=".$_REQUEST["stTipo"];
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
$link["boVinculoEdificacao"] = $_REQUEST["boVinculoEdificacao"];

Sessao::write('link', $link);

//DEFINICAO DO FILTRO PARA CONSULTA
if ($_REQUEST["inCodigoConstrucao"]) {
    $obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["inCodigoConstrucao"] );
    $stLink .= "&inCodigoConstrucao=".$_REQUEST["inCodigoConstrucao"];
}
if ($_REQUEST["inCodigoTipoEdificacao"]) {
    $obRCIMEdificacao->setCodigoTipo( $_REQUEST["inCodigoTipoEdificacao"] );
    $stLink .= "&inCodigoConstrucao=".$_REQUEST["inCodigoConstrucao"];
}
if ($_REQUEST["boVinculoEdificacao"]) {
    $obRCIMEdificacao->setTipoVinculo( $_REQUEST["boVinculoEdificacao"] );
    $stLink .= "&boVinculoEdificacao=".$_REQUEST["boVinculoEdificacao"];
}
if ($_REQUEST["inInscricaoMunicipal"]) {
    $obRCIMEdificacao->obRCIMImovel->setNumeroInscricao( $_REQUEST["inInscricaoMunicipal"] );
    $stLink .= "&inInscricaoMunicipal=".$_REQUEST["inInscricaoMunicipal"];
}
$obRCIMEdificacao->listarUnidadesAutonomas( $rsLista );

$stLink .= "&stAcao=".$stAcao;

Sessao::write('stLink', $stLink);

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

$rsLista->addStrPad( "numero_lote", strlen( $stMascaraLote ), "0" );
$rsLista->addFormatacao( "area_total", "NUMERIC_BR" );

//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );
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
$obLista->ultimoCabecalho->addConteudo("Localização");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Lote");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Área Total" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_construcao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "valor_composto" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "numero_lote" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_tipo"       );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "area_total"      );
$obLista->commitDado();

// Define ACOES
$obLista->addAcao();
$stAcao = "selecionar";
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodigoConstrucao"   , "cod_construcao"  );
$obLista->ultimaAcao->addCampo("&flAreaTotal"          , "area_total"      );
$obLista->ultimaAcao->addCampo("&flAreaTotalEdificada" , "area_total"      );
$obLista->ultimaAcao->addCampo("&flAreaConstruida"     , "area_unidade"    );
$obLista->ultimaAcao->addCampo("&flAreaUnidade"        , "area_unidade"    );
$obLista->ultimaAcao->setLink( $pgFormVinculo."?".Sessao::getId().$stLink."&boAdicionar=true&stAcao=incluir" );
$obLista->commitAcao();

$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.11" );
$obFormulario->show();

?>
