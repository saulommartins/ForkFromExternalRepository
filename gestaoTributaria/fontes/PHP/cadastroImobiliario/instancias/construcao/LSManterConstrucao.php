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
    * Página de lista para o cadastro de construção
    * Data de Criação   : 10/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: LSManterConstrucao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php";

/**
    * Define o nome dos arquivos PHP
*/

$stPrograma = "ManterConstrucao";
$pgFilt               = "FL".$stPrograma.".php";
$pgList               = "LS".$stPrograma.".php";
$pgForm               = "FM".$stPrograma.".php";
$pgFormAlteracao      = "FM".$stPrograma."Alteracao.php";
$pgFormCaracteristica = "FM".$stPrograma."Caracteristica.php";
$pgFormReforma        = "FM".$stPrograma."Reforma.php";
$pgProc               = "PR".$stPrograma.".php";
$pgOcul               = "OC".$stPrograma.".php";
$stFormBaixa          = "FM".$stPrograma."Baixa.php";

$stCaminho = CAM_GT_CIM_INSTANCIAS."construcao/";

$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "alterar";
}

switch ($stAcao) {
    case 'alterar'   : $pgProx = $pgFormAlteracao;      break;
    case 'reativar'  :
    case 'baixar'    : $pgProx = $stFormBaixa;          break;
    case 'excluir'   : $pgProx = $pgProc;               break;
    case 'historico' : $pgProx = $pgFormCaracteristica; break;
    case 'reforma'   : $pgProx = $pgFormReforma;        break;
    DEFAULT          : $pgProx = $pgForm;
}

if ($request->get("boLimpaFiltro") == "true" ) {
    Sessao::remove('link');
    $_REQUEST["boLimpaFiltro"] = "false";
}

//MANTEM FILTRO E PAGINACAO
$stLink = "&stAcao=".$stAcao;
if (isset($_GET["pg"]) and isset($_GET["pos"])) {
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
$link = Sessao::read('link');

if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link', $link);
$stLink = '';
Sessao::write('stLink', $stLink);

$obRCIMConstrucaoOutros = new RCIMConstrucaoOutros;

if ($request->get("inCodigoConstrucao")) {
    $obRCIMConstrucaoOutros->setCodigoConstrucao($request->get("inCodigoConstrucao"));
    $stLink .= "&inCodigoConstrucao=".$request->get("inCodigoConstrucao");
}

if ($request->get("stDtConstrucao")) {
    $obRCIMConstrucaoOutros->setDataConstrucao($request->get("stDtConstrucao"));
    $stLink .= "&stDtConstrucao=".$request->get("stDtConstrucao");
}

// se selecionou imovel
if ($request->get("inNumeroInscricao")) {
    $obRCIMConstrucaoOutros->obRCIMImovel->setNumeroInscricao($request->get("inNumeroInscricao"));
    $stLink .= "&inNumeroInscricao=".$request->get("inNumeroInscricao");
}

// se selecionou comdominio
if ($request->get("inCodigoCondominio")) {
    $obRCIMConstrucaoOutros->obRCIMCondominio->setCodigoCondominio($request->get("inCodigoCondominio"));
    $stLink .= "&inCodigoCondominio=".$request->get("inCodigoCondominio");
}

$boVinculoConstrucao = $request->get("boVinculoConstrucao");

if ($boVinculoConstrucao == "Imóvel") {
    $obRCIMConstrucaoOutros->setTipoVinculo("'Dependente'"    );
    $stLink .= "&stTipoVinculo=dependente";
} else {
    $obRCIMConstrucaoOutros->setTipoVinculo("'Condomínio'"    );
    $stLink .= "&stTipoVinculo=condominio";
}

Sessao::write('stLink', $stLink);

$obRCIMConstrucaoOutros->listarConstrucoes( $rsListaConstrucoes );
$arContrucaoBaixa = array();
$arContrucao      = array();
$pos_livre_contrucao = $pos_livre_baixa = 0;

if ( !$rsListaConstrucoes->eof() ) {
    $arContrucaoTemp = $rsListaConstrucoes->getElementos();
    $total = count( $arContrucaoTemp );
    $pos_atual = 0;
    $pos_livre_contrucao = 0;
    $pos_livre_baixa = 0;
    while ($pos_atual < $total) {
        if ($arContrucaoTemp[$pos_atual]["situacao"] == "Baixado") {
            $arContrucaoBaixa[$pos_livre_baixa] = $arContrucaoTemp[$pos_atual];
            $pos_livre_baixa++;
        } else {
            $arContrucao[$pos_livre_contrucao] = $arContrucaoTemp[$pos_atual];
            $pos_livre_contrucao++;
        }

        $pos_atual++;
    }
}

if ($stAcao == "reativar") {
    $rsListaConstrucoes = new RecordSet();
    if ($pos_livre_baixa > 0) {
        $rsListaConstrucoes->preenche( $arContrucaoBaixa );
    }
} else {
    $rsListaConstrucoes = new RecordSet();
    if ($pos_livre_contrucao > 0) {
        $rsListaConstrucoes->preenche( $arContrucao );
    }

    $rsListaConstrucoesBaixa = new RecordSet();
    if ($pos_livre_baixa > 0) {
        $rsListaConstrucoesBaixa->preenche( $arContrucaoBaixa );
    }

    if ( $rsListaConstrucoes->eof() && $_REQUEST["inCodigoConstrucao"] ) { //nao encontrou nada, verificar se esta baixado
        if ( !$rsListaConstrucoesBaixa->eof()) {
            $boContrucaoBaixada = false;
            while ( !$rsListaConstrucoesBaixa->eof() ) {
                if ($rsListaConstrucoesBaixa->getCampo("cod_construcao") == $_REQUEST["inCodigoConstrucao"]) {
                    $boContrucaoBaixada = true;
                    break;
                }
            }

            if ($boContrucaoBaixada == true) {
                $stJs = "alertaAviso('@Construção baixada. (".$_REQUEST["inCodigoConstrucao"].")','form','erro','".Sessao::getId()."');";
            }

            SistemaLegado::executaFrameOculto($stJs);
        }
    }
}

$stLink .= "&stAcao=".$stAcao;
$stLink .= "&boVinculoConstrucao=".$request->get("boVinculoConstrucao");

Sessao::write('stLink', $stLink);

$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );
$obLista->setRecordSet( $rsListaConstrucoes );
$obLista->setTitulo ("Registros de Construção");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição da Construção" );
$obLista->ultimoCabecalho->setWidth( 45 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
if ($_REQUEST["boVinculoConstrucao"] == "Imóvel") {
    $obLista->ultimoCabecalho->addConteudo( "Imóvel" );
} else {
    $obLista->ultimoCabecalho->addConteudo( "Condomínio" );
}
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

// campos codigo e logradouro sao montados no SQL
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_construcao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();
$obLista->addDado();
if ($_REQUEST["boVinculoConstrucao"] == "Imóvel") {
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "imovel_cond" );
} else {
    $obLista->ultimoDado->setCampo( "[imovel_cond] - [nom_condominio]" );
}
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inCodigoConstrucao", "cod_construcao"  );
$obLista->ultimaAcao->addCampo("&inNumeroInscricao" , "imovel_cond"     );
$obLista->ultimaAcao->addCampo("&inCodigoCondominio", "imovel_cond"     );
$obLista->ultimaAcao->addCampo("&stNomeCond"        , "nom_condominio"  );
$obLista->ultimaAcao->addCampo("&stDescQuestao"     , "[cod_construcao]-[descricao]"  );
$obLista->ultimaAcao->addCampo("&stDtConstrucao"    , "data_construcao" );
if ($stAcao == "reativar") {
    $obLista->ultimaAcao->addCampo("&stJustificativa", "justificativa" );
    $obLista->ultimaAcao->addCampo("&stTimestamp", "timestamp_baixa" );
    $obLista->ultimaAcao->addCampo("&stDTInicio", "data_baixa" );
}

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.12" );
$obFormulario->show();
?>
