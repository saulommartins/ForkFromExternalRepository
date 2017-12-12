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
 * Data de Criação   : 17/11/2004

 * @author Analista: Ricardo Lopes de Alencar
 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
 * @author Desenvolvedor: Fábio Bertoldi Rodrigues

 * @ignore

 * $Id: LSManterEdificacao.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.11
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php";

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

include_once $pgJs;

$stCaminho = CAM_GT_CIM_INSTANCIAS."edificacao/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "alterar";
}

//DEFINE LISTA
$obRCIMEdificacao = new RCIMEdificacao;
$rsLista          = new RecordSet;

$stLink = "";

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;

if (isset($_GET["pg"]) and isset($_GET["pos"])) {
    $link = array();
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

if (is_array($link)) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

$link["boVinculoEdificacao"] = $_REQUEST["boVinculoEdificacao"];

Sessao::write('link', $link);

//DEFINICAO DO FILTRO PARA CONSULTA
if ($request->get("inCodigoConstrucao")) {
    $obRCIMEdificacao->setCodigoConstrucao( $request->get("inCodigoConstrucao") );
    $stLink .= "&inCodigoConstrucao=".$request->get("inCodigoConstrucao");
}

if ($request->get("inCodigoTipoEdificacao")) {
    $obRCIMEdificacao->setCodigoTipo( $request->get("inCodigoTipoEdificacao") );
    $stLink .= "&inCodigoConstrucao=".$request->get("inCodigoConstrucao");
}

if ($request->get("boVinculoEdificacao")) {
    $obRCIMEdificacao->setTipoVinculo( $request->get("boVinculoEdificacao") );
    $stLink .= "&boVinculoEdificacao=".$request->get("boVinculoEdificacao");
}

if ($request->get("inInscricaoMunicipal")) {
    $obRCIMEdificacao->obRCIMImovel->setNumeroInscricao( $request->get("inInscricaoMunicipal") );
    $stLink .= "&inInscricaoMunicipal=".$request->get("inInscricaoMunicipal");
}

if ($request->get("inCodigoCondominio")) {
    $obRCIMEdificacao->obRCIMCondominio->setCodigoCondominio( $request->get("inCodigoCondominio") );
    $stLink .= "&inCodigoCondominio=".$request->get("inCodigoCondominio");
}

if ($request->get("stNumeroLote")) {
    $obRCIMEdificacao->obRCIMImovel->roRCIMLote->setNumeroLote( $request->get("stNumeroLote") );
    $stLink .= "&stNumeroLote=".$request->get("stNumeroLote");
}

if ($request->get("stChaveLocalizacao")) {
    include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"       );
    $obRCIMLocalizacao = new RCIMLocalizacao;
    $obRCIMLocalizacao->setValorComposto($request->get("stChaveLocalizacao"));
    $obRCIMLocalizacao->consultaCodigoLocalizacao($inCodigoLocalizacao);
    $stLink .= "&inNumNiveis=".$request->get("inNumNiveis");
    $obRCIMEdificacao->obRCIMImovel->roRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $inCodigoLocalizacao );
}

if ($stAcao == "reativar") {
    $obRCIMEdificacao->boListarBaixadas = true;
    if ($request->get("boVinculoEdificacao") == "Condomínio" ) {
         $obRCIMEdificacao->listarEdificacoes( $rsLista );
    } else {
         $obRCIMEdificacao->listarEdificacoesImovelBaixa( $rsLista );
    }
} else {
    if ( $request->get("boVinculoEdificacao") == "Condomínio" ) {
        $obRCIMEdificacao->listarEdificacoes( $rsLista );
        //VERIFICAR AQUI CONDOMINIO BAIXADO
        if ( $rsLista->eof() && $request->get("inCodigoConstrucao") && $request->get("inCodigoCondominio") ) {
            $obRCIMEdificacao->boListarBaixadas = true;
            $obRCIMEdificacao->listarEdificacoes( $rsListaBaixados );
            if ( !$rsListaBaixados->eof() ) {
                $stJs = "alertaAviso('@Condomínio baixado. (Código: ".$request->get("inCodigoConstrucao")." Condomínio: ".$request->get("inCodigoCondominio").")','form','erro','".Sessao::getId()."');";

                SistemaLegado::executaFrameOculto($stJs);
            }
        }
    } else {
        $obRCIMEdificacao-> boListarBaixadas = false;
        if ( $stAcao == "baixar" )
            $obRCIMEdificacao->listarEdificacoesImovelBaixa( $rsLista );
        else
            $obRCIMEdificacao->listarEdificacoesImovelAlteracao( $rsLista );

        //VERIFICAR AQUI IMOVEL BAIXADO
        if ( $rsLista->eof() && $_REQUEST["inCodigoConstrucao"] && $request->get("inInscricaoMunicipal") ) {
            $obRCIMEdificacao->boListarBaixadas = true;
            $obRCIMEdificacao->listarEdificacoesImovelBaixa( $rsListaBaixados );
            if ( !$rsListaBaixados->eof() ) {
                $stJs = "alertaAviso('@Imóvel baixado. (Código: ".$request->get("inCodigoConstrucao")." Inscrição Imobiliária: ".$request->get("inInscricaoMunicipal").")','form','erro','".Sessao::getId()."');";

                SistemaLegado::executaFrameOculto($stJs);
            }
        }
    }
}

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

$rsLista->addFormatacao( "area_total", "NUMERIC_BR" );
$rsLista->addFormatacao( "area_unidade", "NUMERIC_BR" );
$rsLista->addFormatacao( "area_real", "NUMERIC_BR" );
$rsLista->addFormatacao( "area_imovel_construcao", "NUMERIC_BR" );
$rsLista->addStrPad( "numero_lote", strlen( $stMascaraLote ), "0" );

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
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
if ($_REQUEST["boVinculoEdificacao"]== "Imóvel") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Localização");
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Lote");
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
}
$obLista->addCabecalho();
if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
    $obLista->ultimoCabecalho->addConteudo("Condomínio");
} elseif ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
    $obLista->ultimoCabecalho->addConteudo("Imóvel");
}
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Área" );
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
if ( $stAcao == "reativar" || $stAcao == "baixar")
    $obLista->ultimoDado->setCampo( "cod_construcao_dep_aut" );
else
    $obLista->ultimoDado->setCampo( "cod_construcao" );
$obLista->commitDado();

if ($_REQUEST["boVinculoEdificacao"]== "Imóvel") {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "valor_composto" );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "numero_lote" );
    $obLista->commitDado();
}
$obLista->addDado();
if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
    $obLista->ultimoDado->setCampo( "[imovel_cond] - [nom_condominio]"    );
} elseif ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "imovel_cond"    );
}
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_tipo"       );
$obLista->commitDado();
if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "area_real"      );
    $obLista->commitDado();
} else {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "area_unidade"      );
    $obLista->commitDado();
}

// Define ACOES
if ($stAcao == "alterar") {
    $obLista->addAcao();
    $stAcao = "alterar";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoConstrucao"      , "cod_construcao"     );
    $obLista->ultimaAcao->addCampo("&stTipoEdificacao"        , "nom_tipo"           );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"            , "cod_tipo"           );
    $obLista->ultimaAcao->addCampo("&stImovelCond"            , "imovel_cond"        );
    $obLista->ultimaAcao->addCampo("&stTipoUnidade"           , "tipo_vinculo"       );
    $obLista->ultimaAcao->addCampo("&hdnVinculoEdificacao"    , "tipo_vinculo"       );
    $obLista->ultimaAcao->addCampo("&flAreaTotalEdificada"    , "area_imovel_construcao" );
    $obLista->ultimaAcao->addCampo("&flAreaConstruida"        , "area_real"          );
    $obLista->ultimaAcao->addCampo("&flAreaUnidade"           , "area_unidade"       );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"        , "cod_processo"       );
    $obLista->ultimaAcao->addCampo("&stDtConstrucao"          , "data_construcao"    );
    $obLista->ultimaAcao->addCampo("&hdnAnoExercicioProcesso" , "exercicio"          );
    $obLista->ultimaAcao->setLink( $pgFormAlterar."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "incluir") {
    $obLista->addAcao();
    $stAcao = "selecionar";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoConstrucao"      , "cod_construcao"            );
    $obLista->ultimaAcao->addCampo("&flAreaConstruida"        , "area_unidade"              );
    $obLista->ultimaAcao->addCampo("&flAreaTotalEdificada"    , "area_imovel_construcao"    );
    $obLista->ultimaAcao->setLink( $pgFormVinculo."?".Sessao::getId().$stLink."&stAcao=incluir" );
    $obLista->commitAcao();
} elseif ($stAcao == "excluir") {
    $obLista->addAcao();
    $stAcao = "excluir";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoConstrucao"      , "cod_construcao"     );
    $obLista->ultimaAcao->addCampo("&stTipoEdificacao"        , "nom_tipo"           );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"            , "cod_tipo"           );
    $obLista->ultimaAcao->addCampo("&stImovelCond"            , "imovel_cond"        );
    $obLista->ultimaAcao->addCampo("&stTipoUnidade"           , "tipo_vinculo"       );
    $obLista->ultimaAcao->addCampo("&hdnVinculoEdificacao"    , "tipo_vinculo"       );
    $obLista->ultimaAcao->addCampo("&flAreaConstruida"        , "area_real"          );
    $obLista->ultimaAcao->addCampo("&flAreaTotalEdificada"    , "area_real"          );
    $obLista->ultimaAcao->addCampo("&flAreaUnidade"           , "area_unidade"       );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"        , "cod_processo"       );
    $obLista->ultimaAcao->addCampo("&hdnAnoExercicioProcesso" , "exercicio"          );
    $obLista->ultimaAcao->addCampo("&stDescQuestao"           , "[cod_construcao]"   );
    $obLista->ultimaAcao->addCampo("&stDtConstrucao"          , "data_construcao"    );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "reativar") {
    $obLista->addAcao();
    $stAcao = "reativar";
    $obLista->ultimaAcao->setAcao( $stAcao );
    if ( $_REQUEST["boVinculoEdificacao"] == "Condomínio" )
        $obLista->ultimaAcao->addCampo("&inCodigoConstrucao"            , "cod_construcao"  );
    else
        $obLista->ultimaAcao->addCampo("&inCodigoConstrucao"            , "cod_construcao_dep_aut"  );

    $obLista->ultimaAcao->addCampo("&inCodigoConstrucaoAutonoma"    , "cod_construcao_autonoma" );
    $obLista->ultimaAcao->addCampo("&stTipoEdificacao"              , "nom_tipo"                );
    $obLista->ultimaAcao->addCampo("&stImovelCond"                  , "imovel_cond"             );
    $obLista->ultimaAcao->addCampo("&stTipoUnidade"                 , "tipo_vinculo"            );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"                  , "cod_tipo"                );
    $obLista->ultimaAcao->addCampo("&inCodigoTipoAutonoma"          , "cod_tipo_autonoma"       );
    $obLista->ultimaAcao->addCampo("&flAreaConstruida"              , "area_real"               );
    $obLista->ultimaAcao->addCampo("&flAreaTotalEdificada"          , "area_real"               );
    $obLista->ultimaAcao->addCampo("&flAreaUnidade"                 , "area_unidade"            );
    $obLista->ultimaAcao->addCampo("&stDtConstrucao"                , "data_construcao"         );
    $obLista->ultimaAcao->addCampo("&stTimestamp"                   , "timestamp_baixa"         );
    $obLista->ultimaAcao->addCampo("&stJustificativa"               , "justificativa_unidade"   );
    $obLista->ultimaAcao->addCampo("&stDTInicio"                    , "dt_inicio_unidade"       );

    $obLista->ultimaAcao->setLink( $pgFormVinculo."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "baixar") {
    $obLista->addAcao();
    $stAcao = "baixar";

    $obLista->ultimaAcao->setAcao( $stAcao );
    if ( $_REQUEST["boVinculoEdificacao"] == "Condomínio" )
        $obLista->ultimaAcao->addCampo("&inCodigoConstrucao"            , "cod_construcao"  );
    else
        $obLista->ultimaAcao->addCampo("&inCodigoConstrucao"            , "cod_construcao_dep_aut"  );

    $obLista->ultimaAcao->addCampo("&inCodigoConstrucaoAutonoma"    , "cod_construcao_autonoma" );
    $obLista->ultimaAcao->addCampo("&stTipoEdificacao"              , "nom_tipo"                );
    $obLista->ultimaAcao->addCampo("&stImovelCond"                  , "imovel_cond"             );
    $obLista->ultimaAcao->addCampo("&stTipoUnidade"                 , "tipo_vinculo"            );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"                  , "cod_tipo"                );
    $obLista->ultimaAcao->addCampo("&inCodigoTipoAutonoma"          , "cod_tipo_autonoma"       );
    $obLista->ultimaAcao->addCampo("&flAreaConstruida"              , "area_real"               );
    $obLista->ultimaAcao->addCampo("&flAreaTotalEdificada"          , "area_real"          );
    $obLista->ultimaAcao->addCampo("&flAreaUnidade"                 , "area_unidade"       );
    $obLista->ultimaAcao->addCampo("&stDtConstrucao"                , "data_construcao"    );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"        , "cod_processo"       );
    $obLista->ultimaAcao->addCampo("&hdnAnoExercicioProcesso" , "exercicio"          );
    $obLista->ultimaAcao->setLink( $pgFormVinculo."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "historico") {
    $obLista->addAcao();
    $stAcao = "historico";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoConstrucao"      , "cod_construcao"     );
    $obLista->ultimaAcao->addCampo("&inCodigoConstrucaoAut"   , "cod_construcao_autonoma"   );
    $obLista->ultimaAcao->addCampo("&stTipoEdificacao"        , "nom_tipo"           );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"            , "cod_tipo"           );
    $obLista->ultimaAcao->addCampo("&stImovelCond"            , "imovel_cond"        );
    $obLista->ultimaAcao->addCampo("&stTipoUnidade"           , "tipo_vinculo"       );
    $obLista->ultimaAcao->addCampo("&hdnVinculoEdificacao"    , "tipo_vinculo"       );
    $obLista->ultimaAcao->addCampo("&flAreaConstruida"        , "area_real"          );
    $obLista->ultimaAcao->addCampo("&flAreaTotalEdificada"    , "area_real"          );
    $obLista->ultimaAcao->addCampo("&flAreaUnidade"           , "area_unidade"       );
    $obLista->ultimaAcao->addCampo("&stComplemento"           , "complemento"        );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"        , "cod_processo"       );
    $obLista->ultimaAcao->addCampo("&hdnAnoExercicioProcesso" , "exercicio"          );
    $obLista->ultimaAcao->setLink( $pgFormCaracteristica."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "reforma") {
    $obLista->addAcao();
    $stAcao = "reforma";

    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoConstrucao"      , "cod_construcao"            );
    $obLista->ultimaAcao->addCampo("&inCodigoConstrucaoAut"   , "cod_construcao_autonoma"   );
    $obLista->ultimaAcao->addCampo("&stTipoEdificacao"        , "nom_tipo"                  );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"            , "cod_tipo_autonoma"         );
    $obLista->ultimaAcao->addCampo("&stImovelCond"            , "imovel_cond"               );
    $obLista->ultimaAcao->addCampo("&stTipoUnidade"           , "tipo_vinculo"              );
    $obLista->ultimaAcao->addCampo("&hdnVinculoEdificacao"    , "tipo_vinculo"              );
    $obLista->ultimaAcao->addCampo("&stNumero"                , "numero"                    );
    $obLista->ultimaAcao->addCampo("&stComplemento"           , "complemento"               );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"        , "cod_processo"              );
    $obLista->ultimaAcao->addCampo("&hdnAnoExercicioProcesso" , "exercicio"          );
    $obLista->ultimaAcao->addCampo("&flAreaTotalEdificada"    , "area_imovel_construcao"    );
    $obLista->ultimaAcao->addCampo("&flAreaConstruida"        , "area_unidade"              );
    $obLista->ultimaAcao->addCampo("&flAreaUnidade"           , "area_unidade"              );
    $obLista->ultimaAcao->setLink( $pgFormReforma."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
}
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.11" );
$obFormulario->show();

?>
