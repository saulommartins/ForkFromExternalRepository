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
    * Arquivo instância para popup de CGM
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-01.02.92, uc-02.08.05

    $Id: LSProcurarCgm.php 63969 2015-11-12 18:43:12Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCgm";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);
$stFncJavaScript = " function insereCGM(num,nom) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " if ( window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."') ) { window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; } \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum; \n";

$inner = isset($inner) ? $inner : null;
$stAcao = isset($stAcao) ? $stAcao : "" ;

if ($inner!=0) {
    $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".Hdn".$_REQUEST["campoNum"].".value = sNum; \n";
}
if ($_REQUEST["campoNom"]) {
    $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNom"].".value = sNom; \n";
    $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".focus(); \n";
}

if ($_REQUEST["campoNom"] == "nomSeguradora") {
    $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".dtVencimento.focus(); \n";
}
if ($_REQUEST["campoNom"] == "sFornecedor") {
    $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".valorBem.select(); \n";
}
if ($_REQUEST["campoNom"] == "nomMotorista") {
    $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".focus(); \n";
}

$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$obTCGM = new TCGM;
$obMascara = new Mascara;
$stFiltro = "";
$stLink   = "";

//Definição do filtro de acordo com os valores informados
$stLink .= "&stTipoPessoa=".$request->get("stTipoPessoa");

if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom='.$request->get('campoNom');
}
if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm='.$request->get('nomForm');
}
if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum='.$request->get('campoNum');
}

// Caso tenha algum valor agregado, inclui nos parâmetros da URL.
if ( !empty($_REQUEST["stTabelaVinculo"] )) {
    $stLink .= '&stTabelaVinculo='.$_REQUEST['stTabelaVinculo'];
}

// Caso tenha algum valor agregado, inclui nos parâmetros da URL.
if ( !empty($_REQUEST["stCampoVinculo"] )) {
    $stLink .= '&stCampoVinculo='.$_REQUEST['stCampoVinculo'];
}

if ( $request->get("stTipoBusca") == "usuario" ) {
    $stFiltro .= " AND CGM.numcgm IN (select numcgm from administracao.usuario where status='A') ";
    $stLink .= '&stTipoBusca='.$_REQUEST['stTipoBusca'];
} else {
    if ( $request->get("stTipoPessoa") == "F" ) {
        $stFiltro .= " AND pf.numcgm is not null ";
    }
    if ( $request->get("stTipoPessoa") == "J" ) {
        $stFiltro .= " AND pj.numcgm is not null ";
    }
}

if ( $request->get("stNomeCgm") ) {
    $stHdnNomeCgm=str_replace("'","' || '\\\\\\''",$_REQUEST['stHdnNomeCgm']);
    $stFiltro .= " AND lower(nom_cgm) like  lower('".$stHdnNomeCgm."') ";
    $stLink   .= "&stNomeCgm=".$_REQUEST["stNomeCgm"]."&stHdnNomeCgm=".$_REQUEST["stHdnNomeCgm"];
}
if ( $request->get("stCPF") ) {
    $inCPF = $_REQUEST["stCPF"];
    $obMascara->desmascaraDado( $inCPF );
    $stFiltro .= " AND pf.cpf = '".$inCPF."' ";
    $stLink   .= "&stCPF=".$_REQUEST["stCPF"];
}
if ( $request->get("stCNPJ") ) {
    $inCNPJ = $_REQUEST["stCNPJ"];
    $obMascara->desmascaraDado( $inCNPJ );
    $stFiltro .= " AND pj.cnpj = '".$inCNPJ."' ";
    $stLink   .= "&stCNPJ=".$_REQUEST["stCNPJ"];
}
if ( $request->get("stNomeFantasia") ) {
    $stFiltro .= " AND  lower(pj.nom_fantasia) like lower('".$_REQUEST["stHdnNomeFantasia"]."')";
    $stLink   .= "&stNomeFantasia=".$_REQUEST["stNomeFantasia"]."&stHdnNomeFantasia=".$_REQUEST["stHdnNomeFantasia"];
}

if ($request->get('buscaContrato') == 'compraDireta') {
    $stFiltro .= " AND EXISTS( SELECT 1
                                 FROM licitacao.contrato
                                    , licitacao.contrato_compra_direta
                                WHERE contrato.cgm_contratado = CGM.numcgm
                                  AND contrato.num_contrato = contrato_compra_direta.num_contrato
                                  AND contrato.cod_entidade = contrato_compra_direta.cod_entidade
                                  AND contrato.exercicio = contrato_compra_direta.exercicio
                                  AND NOT EXISTS ( SELECT 1
                                                     FROM licitacao.contrato_anulado
                                                    WHERE contrato.num_contrato = contrato_anulado.num_contrato
                                                      AND contrato.cod_entidade = contrato_anulado.cod_entidade
                                                      AND contrato.exercicio = contrato_anulado.exercicio
                                                 )
                                 AND NOT EXISTS ( SELECT 1
                                                     FROM licitacao.rescisao_contrato
                                                    WHERE contrato.num_contrato = rescisao_contrato.num_contrato
                                                      AND contrato.cod_entidade = rescisao_contrato.cod_entidade
                                                      AND contrato.exercicio = rescisao_contrato.exercicio
                                                 )
                            )";
}

if ($request->get('buscaContrato') == 'licitacao') {
    $stFiltro .= " AND EXISTS( SELECT 1
                                 FROM licitacao.contrato
                                    , licitacao.contrato_licitacao
                                WHERE contrato.cgm_contratado = CGM.numcgm
                                  AND contrato.num_contrato = contrato_licitacao.num_contrato
                                  AND contrato.cod_entidade = contrato_licitacao.cod_entidade
                                  AND contrato.exercicio = contrato_licitacao.exercicio
                                  AND NOT EXISTS ( SELECT 1
                                                     FROM licitacao.contrato_anulado
                                                    WHERE contrato.num_contrato = contrato_anulado.num_contrato
                                                      AND contrato.cod_entidade = contrato_anulado.cod_entidade
                                                      AND contrato.exercicio = contrato_anulado.exercicio
                                                 )
                                 AND NOT EXISTS ( SELECT 1
                                                     FROM licitacao.rescisao_contrato
                                                    WHERE contrato.num_contrato = rescisao_contrato.num_contrato
                                                      AND contrato.cod_entidade = rescisao_contrato.cod_entidade
                                                      AND contrato.exercicio = rescisao_contrato.exercicio
                                                 )
                            )";
}

//faz busca dos CGM's utilizando o filtro setado
$stLink .= "&stAcao=".$stAcao;
$rsLista = new RecordSet;

/*
    nova parte usando a classe de sessao
*/
$arCampo = Sessao::read($request->get('stId'));

if ($arCampo['FLIPopUpCGMVinculado'] !="") {
    $stFiltro.= $arCampo['FLIPopUpCGMVinculado'];
}

if ($arCampo['stFiltroVinculado'] !="") {
    $stFiltroVinculado.= $arCampo['stFiltroVinculado'];
}
if ( $request->get('stTabelaVinculo') == 'patrimonio.bem_responsavel' ) {
    $stFiltroVinculado .= " AND tabela_vinculo.dt_fim IS NULL";
}

if ( $request->get('stTabelaVinculo') ) {
    switch ($request->get('stTipoBusca')) {
        case "vinculadoPlanoSaude":
            $obTCGM->recuperaRelacionamentoVinculadoPlanoSaude( $rsLista, $stFiltro, " ORDER BY lower(CGM.nom_cgm)", $boTransacao , $_REQUEST['stTabelaVinculo'] , $_REQUEST['stCampoVinculo'], $stFiltroVinculado);
        break;

        case "vinculoComissaoLicitacao":
            $stFiltroVinculado = "AND licitacao.cod_licitacao = ".$request->get('hdnCodLicitacao')." AND licitacao.cod_modalidade = ".$request->get('hdnCodModalidade')." AND comissao_licitacao.cod_comissao = ".$request->get('hdnCodComissao');
            $obTCGM->recuperaRelacionamentoVinculadoComissaoLicitacao( $rsLista, $stFiltro, " ORDER BY lower(sw_cgm.nom_cgm)", $boTransacao , "" , "", $stFiltroVinculado);
        break;

        case 'orgaoGestor':
            $obTCGM->recuperaOrgaoGerenciador( $rsLista, $stFiltro, " ORDER BY lower(CGM.nom_cgm)", $boTransacao );
        break;
        
        default:
            $obTCGM->recuperaRelacionamentoVinculado( $rsLista, $stFiltro, " ORDER BY lower(CGM.nom_cgm)", $boTransacao , $_REQUEST['stTabelaVinculo'] , $_REQUEST['stCampoVinculo'], $stFiltroVinculado);
        break;

    }
} else {
    $obTCGM->recuperaRelacionamentoSintetico( $rsLista, $stFiltro, " ORDER BY lower(CGM.nom_cgm)" );
}

for ($i=0;$i<$rsLista->inNumLinhas;$i++) {
    $rsLista->arElementos[$i]['nom_cgm']=stripslashes(stripslashes($rsLista->arElementos[$i]['nom_cgm']));
}

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:window.close(); insereCGM();" );
$obLista->ultimaAcao->addCampo("1","numcgm");
$obLista->ultimaAcao->addCampo("2","nom_cgm");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
