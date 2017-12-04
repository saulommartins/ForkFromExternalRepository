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
    * Página de lista para o casastro de imóvel
    * Data de Criação   : 30/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: LSManterImovel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.12  2006/09/18 10:30:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterImovel";
$pgFilt               = "FL".$stPrograma.".php";
$pgList               = "LS".$stPrograma.".php";
$pgForm               = "FM".$stPrograma."Lote.php";
$pgFormBaixa          = "FM".$stPrograma."Baixa.php";
$pgFormFoto           = "FM".$stPrograma."Foto.php";
$pgFormCaracteristica = "FM".$stPrograma."Caracteristica.php";
$pgProc               = "PR".$stPrograma.".php";
$pgOcul               = "OC".$stPrograma.".php";
$pgJs                 = "JS".$stPrograma.".js";
include_once( $pgJs );
$stCaminho = CAM_GT_CIM_INSTANCIAS."imovel/";

/**
    *Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
*/
$stAcao = $request->get('stAcao');

/**
    * Define arquivos PHP para cada ação
*/

switch ($stAcao) {
    case 'alterar'   : $pgProx = $pgForm; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    case 'reativar'  :
    case 'baixar'    : $pgProx = $pgFormBaixa;break;
    case 'foto'      : $pgProx = $pgFormFoto;break;
    case 'historico' : $pgProx = $pgFormCaracteristica;break;
    DEFAULT          : $pgProx = $pgForm;
}

Sessao::remove('endereco_entrega');

$obRCIMImovel = new RCIMImovel( new RCIMLote );
if (isset($_REQUEST["stTipo"])) {
    if ($_REQUEST["stTipo"] == "urbano") {
        $obRCIMImovel->setTipoLote("urbano");
    } elseif ($_REQUEST["stTipo"] == "rural") {
        $obRCIMImovel->setTipoLote("rural");
    }
}

$link   = Sessao::read('link');

//MANTEM FILTRO E PAGINACAO
$stLink = "&stAcao=".$stAcao;
if ( isset($_GET["pg"]) and  isset($_GET["pos"]) ) {
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

Sessao::write('link', $link);
Sessao::write('stLink', $stLink);

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

if ( isset($_REQUEST["inNumeroInscricao"]) ) {
    $obRCIMImovel->setNumeroInscricao( $_REQUEST["inNumeroInscricao"] );
}

if ( isset($_REQUEST["stNumeroLote"]) ) {
    $obRCIMImovel->roRCIMLote->setNumeroLote( $_REQUEST["stNumeroLote"] );
}

if ( isset($_REQUEST[ "stChaveLocalizacao"]) ) {
    include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"       );
    $obRCIMLocalizacao = new RCIMLocalizacao;
    $obRCIMLocalizacao->setValorComposto($_REQUEST["stChaveLocalizacao"]);
    $obRCIMLocalizacao->consultaCodigoLocalizacao($inCodigoLocalizacao);
    $obRCIMImovel->roRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $inCodigoLocalizacao );
}

if ($stAcao == "reativar") {
    $obRCIMImovel->verificaBaixaImovel( $rsListaImovel );
} else {
    $obRCIMImovel->listarImoveisLista( $rsListaImovel );
    if ( $rsListaImovel->eof() && $_REQUEST["inNumeroInscricao"] ) { //nao encontrou nada, verificar se esta baixado
        $obRCIMImovel->verificaBaixaImovel( $rsListaImovelBaixa );
        if ( !$rsListaImovelBaixa->eof()) {
            $stJs = "alertaAviso('@Imóvel baixado. (".$_REQUEST["inNumeroInscricao"].")','form','erro','".Sessao::getId()."');";

            SistemaLegado::executaFrameOculto($stJs);
        }
    }
}

$rsListaImovel->addStrPad( "valor", strlen( $stMascaraLote ), "0" );

//Verifica se possui Processo, se possuí acrescenta /, exemplo: 012/2013.
for ($i=0;$i<$rsListaImovel->inNumLinhas;$i++) {
    if ($rsListaImovel->arElementos[$i]['cod_processo']!='') {
       $rsListaImovel->arElementos[$i]['cod_processo']=$rsListaImovel->arElementos[$i]['cod_processo']."/";
    }
}

/**
    * Instancia o OBJETO Lista
*/

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsListaImovel );

$obLista->setTitulo ("Registros de imóvel");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Localização" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Lote" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição Imobiliária" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

// campos codigo e logradouro sao montados no SQL
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "valor_composto" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "valor" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "inscricao_municipal" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inInscricaoMunicipal" , "inscricao_municipal" );
$obLista->ultimaAcao->addCampo("&inCodigoLote"         , "cod_lote"            );
$obLista->ultimaAcao->addCampo("&inCodigoSubLote"      , "cod_sublote"         );
$obLista->ultimaAcao->addCampo("&stValorLote"          , "valor"               );
$obLista->ultimaAcao->addCampo("&inCodigoLocalizacao"  , "cod_localizacao"     );
$obLista->ultimaAcao->addCampo("&stTipoLote"           , "tipo_lote"           );
$obLista->ultimaAcao->addCampo("&stDescQuestao"        , "inscricao_municipal" );
$obLista->ultimaAcao->addCampo("&stCreciResponsavel"   , "creci"               );
$obLista->ultimaAcao->addCampo("&stNomeCreci"          , "nome_cgm"            );
$obLista->ultimaAcao->addCampo("&inProcesso"           , "[cod_processo][ano_exercicio]");
if ($stAcao == "reativar") {
    $obLista->ultimaAcao->addCampo("&stTimestamp"      , "timestamp"           );
    $obLista->ultimaAcao->addCampo("&stDTInicio"       , "dt_inicio"           );
    $obLista->ultimaAcao->addCampo("&stJustificativa"  , "justificativa"       );
}

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.09" );
$obFormulario->show();

?>
