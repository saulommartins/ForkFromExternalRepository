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
    * Página de Frame Oculto de Modalidade
    * Data de Criação   : 21/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCManterModalidade.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloDocumento.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncao.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONAcrescimo.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONCredito.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeCredito.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeAcrescimo.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeReducao.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeReducaoAcrescimo.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeReducaoCredito.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeParcela.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeDocumento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidade.class.php" );

function montaListaCreditos($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista );
        $obLista->setTitulo ( "Lista de Créditos" );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Código" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Descrição" );
        $obLista->ultimoCabecalho->setWidth ( 20 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "inCodCredito" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "stCredito" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );

        $obLista->ultimaAcao->setLink ( "JavaScript:excluirCredito();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","inCodCredito" );
        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnListaCredito').innerHTML = '".$stHTML."';\n";

    return $js;
}

function montaListaAcrescimos($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista );
        $obLista->setTitulo ( "Lista de Acrescimos Legais" );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Código" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Descrição" );
        $obLista->ultimoCabecalho->setWidth ( 20 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Incidência" );
        $obLista->ultimoCabecalho->setWidth ( 20 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Regra" );
        $obLista->ultimoCabecalho->setWidth ( 40 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "inCodAcrescimo" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "stDescAcrescimo" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "stAcrescimoIncidenciaDesc" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "[inCodFuncaoAC] - [stNomFuncaoAC]" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );

        $obLista->ultimaAcao->setLink ( "JavaScript:excluirAcrescimo();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","inCodAcrescimo" );
        $obLista->ultimaAcao->addCampo ( "inIndice2","stAcrescimoIncidenciaDesc" );

        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnListaAcrescimo').innerHTML = '".$stHTML."';\n";

    return $js;
}

function montaListaReducoes($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista );
        $obLista->setTitulo ( "Lista de Reduções" );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Tipo" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Valor" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Regra" );
        $obLista->ultimoCabecalho->setWidth ( 40 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Incidência" );
        $obLista->ultimoCabecalho->setWidth ( 30 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "stTipo" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "stValor" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "[inCodFuncaoRD] - [stNomFuncaoRD]" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "descricao" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "Definir" );
        $obLista->ultimaAcao->setFuncao ( true );
        $obLista->ultimaAcao->setLink ( "JavaScript:alterarReducao();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","inCodFuncaoRD" );
        $obLista->ultimaAcao->addCampo ( "inIndice2","stValor" );
        $obLista->ultimaAcao->addCampo ( "inIndice3","stTipoReducao" );
        $obLista->commitAcao ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );
        $obLista->ultimaAcao->setLink ( "JavaScript:excluirReducao();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","inCodFuncaoRD" );
        $obLista->ultimaAcao->addCampo ( "inIndice2","stValor" );
        $obLista->ultimaAcao->addCampo ( "inIndice3","stTipoReducao" );
        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnListaReducao').innerHTML = '".$stHTML."';\n";

    return $js;
}

function montaListaReducoesCredito($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista );
        $obLista->setTitulo ( "Lista de Incidências da Redução" );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Código" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Descrição" );
        $obLista->ultimoCabecalho->setWidth ( 20 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Tipo" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "valor" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "descricao" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "tipo" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );
        $obLista->ultimaAcao->setLink ( "JavaScript:excluirReducaoCredito();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","valor" );
        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnListaReducaoCreditos').innerHTML = '".$stHTML."';\n";

    return $js;
}

function montaListaParcelas($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista );
        $obLista->setTitulo ( "Lista de Parcelas" );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Limite Valor Inicial (R$)" );
        $obLista->ultimoCabecalho->setWidth ( 20 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Limite Valor Final (R$)" );
        $obLista->ultimoCabecalho->setWidth ( 20 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Quantidade" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Valor Mínimo (R$)" );
        $obLista->ultimoCabecalho->setWidth ( 20 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "flLimiteValorInicial" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "flLimiteValorFinal" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "inQtdParcelas" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "flValorMinimo" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );

        $obLista->ultimaAcao->setLink ( "JavaScript:excluirParcela();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","flLimiteValorInicial" );
        $obLista->ultimaAcao->addCampo ( "inIndice2","flLimiteValorFinal" );
        $obLista->ultimaAcao->addCampo ( "inIndice3","inQtdParcelas" );
        $obLista->ultimaAcao->addCampo ( "inIndice4","flValorMinimo" );
        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnListaParcela').innerHTML = '".$stHTML."';\n";

    return $js;
}

function montaListaDocumentos($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista );
        $obLista->setTitulo ( "Lista de Documentos" );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Código" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Nome" );
        $obLista->ultimoCabecalho->setWidth ( 45 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "stCodDocumento" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "stNomDocumento" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );

        $obLista->ultimaAcao->setLink ( "JavaScript:excluirDocumento();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","stCodDocumento" );
        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnListaDocumento').innerHTML = '".$stHTML."';\n";

    return $js;
}

function preencheFuncao($stCodFnc, $stNomFnc)
{
    $stJs = "";
    if ($_GET[ $stCodFnc ]) {
        $obTFuncao = new TAdministracaoFuncao;
        $arCodFuncao = explode('.', $_GET[ $stCodFnc ] );
        if ( ( $_GET["stCodModulo"] == $arCodFuncao[0] ) && ( $_GET["stCodBiblioteca"] == $arCodFuncao[1] ) ) {
            $obTFuncao->setDado( "cod_biblioteca", $arCodFuncao[1] );
            $obTFuncao->setDado( "cod_modulo", $arCodFuncao[0] );
            $obTFuncao->setDado( "cod_funcao", $arCodFuncao[2] );
            $obTFuncao->recuperaPorChave( $rsFuncao );
            if ( $rsFuncao->Eof() ) {
                $stJs = "f.".$stCodFnc.".value ='';\n";
                $stJs .= "f.".$stCodFnc.".focus();\n";
                $stJs .= "d.getElementById('".$stNomFnc."').innerHTML = '&nbsp;';\n";
                $stJs .= "alertaAviso('@Código informado não existe. (".$_GET[ $stCodFnc ].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs = "d.getElementById('".$stNomFnc."').innerHTML = '".$rsFuncao->getCampo("nom_funcao")."';\n";
            }
        } else {
            $stJs = "alertaAviso('@Código informado inválido. (".$_GET[ $stCodFnc ].")','form','erro','".Sessao::getId()."');";
            $stJs .= "f.".$stCodFnc.".value ='';\n";
            $stJs .= "f.".$stCodFnc.".focus();\n";
            $stJs .= "d.getElementById('".$stNomFnc."').innerHTML = '&nbsp;';\n";
        }
    } else {
        $stJs = "f.".$stCodFnc.".value ='';\n";
        $stJs .= "d.getElementById('".$stNomFnc."').innerHTML = '&nbsp;';\n";
        if ($_GET[ $stCodFnc ] == '0') {
            $stJs .= "alertaAviso('@Código informado não existe. (".$_GET[ $stCodFnc ].")','form','erro','".Sessao::getId()."');";
        }
    }

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case "buscaModalidade":
        $stDescricao = '&nbsp;';
        $stNomCampo = $_GET["stNomCampoCod"];
        if ($_GET[$stNomCampo]) {

            $obTDATModalidade = new TDATModalidade;
            $stFiltro = " WHERE dm.ativa = 't' AND dm.cod_modalidade = ".$_GET[$stNomCampo]." AND dmv.vigencia_inicial <= '".date('Y-m-d')."' AND dmv.vigencia_final >= '".date('Y-m-d')."'";
            if ($_GET["tipoModalidade"]) {
                if ($_GET["tipoModalidade"] == 4) {
                    $stFiltro .= " AND ( dmv.cod_tipo_modalidade = 2 OR dmv.cod_tipo_modalidade = 3 ) ";
                } else {
                    $stFiltro .= " AND dmv.cod_tipo_modalidade = ".$_GET['tipoModalidade'];
                }
            }

            $obTDATModalidade->recuperaListaModalidade( $rsModalidade, $stFiltro, " ORDER BY dm.cod_modalidade " );
            if ( !$rsModalidade->Eof() ) {
                $stDescricao = $rsModalidade->getCampo("descricao");
                $stJs = "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', 'frm', '".$stDescricao."');";
            } else {
                $stJs = 'f.'.$_GET["stNomCampoCod"].'.value = "";';
                $stJs .= "d.getElementById('".$_GET['stIdCampoDesc']."').innerHTML = '&nbsp;';\n";
                $stJs .= "alertaAviso('@Código modalidade inválido (".$_GET[$stNomCampo] .").','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "d.getElementById('".$_GET['stIdCampoDesc']."').innerHTML = '&nbsp;';\n";
        }

        echo $stJs;
        break;
/*
#######################################################################################
                                    preencheListas
######################################################################################
*/
    case "preencheListas":
        if ( ($_GET["inCodModalidade"] >= 0)  && $_GET["stTimeStamp"] ) {
            $stFiltro = " WHERE timestamp = '".$_GET["stTimeStamp"]."' AND cod_modalidade = ".$_GET["inCodModalidade"];

            //modalidade_credito
            $obTDATModalidadeCredito = new TDATModalidadeCredito;
            $obTDATModalidadeCredito->recuperaListaCredito( $rsCreditos, $stFiltro );
            $inX = 0;
            $arCreditoSessao = Sessao::read('credito');
            while ( !$rsCreditos->Eof() ) {
                $arCreditoSessao[$inX]['inCodCredito'] = $rsCreditos->getCampo("codcredito");
                $arCreditoSessao[$inX]['stCredito'] = $rsCreditos->getCampo("descricao");

                $inX++;
                $rsCreditos->proximo();
            }
            if ( is_array($arCreditoSessao )) {
                Sessao::write('credito', $arCreditoSessao);
                $rsCreditos = new RecordSet;
                $rsCreditos->preenche( $arCreditoSessao );
                $js = montaListaCreditos( $rsCreditos );
            }

            //modalidade_acrescimo
            $obTDATModalidadeAcrescimo = new TDATModalidadeAcrescimo;
            $obTDATModalidadeAcrescimo->recuperaListaAcrescimo( $rsAcrescimos, $stFiltro );
            $inX = 0;
            $arAcrescimoSessao = Sessao::read('acrescimo');

            while ( !$rsAcrescimos->Eof() ) {
                if ( $rsAcrescimos->getCampo("pagamento") == "t" ) {
                    $arAcrescimoSessao[$inX]['stAcrescimoIncidenciaDesc'] = "Pagamentos";
                    $arAcrescimoSessao[$inX]['stAcrescimoIncidencia']     = true;
                } else {
                    $arAcrescimoSessao[$inX]['stAcrescimoIncidenciaDesc'] = "Inscrição em Dívida / Cobranças";
                    $arAcrescimoSessao[$inX]['stAcrescimoIncidencia']     = false;
                }

                $arAcrescimoSessao[$inX]['stNomFuncaoAC']   = $rsAcrescimos->getCampo("nom_funcao");
                $arAcrescimoSessao[$inX]['inCodFuncaoAC']   = $rsAcrescimos->getCampo("cod_funcao");
                $arAcrescimoSessao[$inX]['cod_tipo']        = $rsAcrescimos->getCampo("cod_tipo");
                $arAcrescimoSessao[$inX]['inCodAcrescimo']  = $rsAcrescimos->getCampo("cod_acrescimo");
                $arAcrescimoSessao[$inX]['stDescAcrescimo'] = $rsAcrescimos->getCampo("descricao_acrescimo");

                $inX++;
                $rsAcrescimos->proximo();
            }

            if ( is_array($arAcrescimoSessao) ) {
                Sessao::write('acrescimo', $arAcrescimoSessao);
                $rsAcrescimos = new RecordSet;
                $rsAcrescimos->preenche( $arAcrescimoSessao );
                $js .= montaListaAcrescimos( $rsAcrescimos );
            }

            if ($_GET["inTipo"] != 1) {
                //modalidade_reducao
                $obTDATModalidadeReducao = new TDATModalidadeReducao;
                $obTDATModalidadeReducao->recuperaListaReducao( $rsReducao, $stFiltro );
                $inX = 0;
                $arReducaoSessao = Sessao::read('reducao');

                while ( !$rsReducao->Eof() ) {
                    $stFiltroIncidencia = " WHERE dm.timestamp = '".$_GET["stTimeStamp"]."' AND dm.cod_modalidade = ".$_GET["inCodModalidade"]." AND dm.cod_funcao = ".$rsReducao->getCampo("cod_funcao_r")." AND dm.cod_biblioteca = ".$rsReducao->getCampo("cod_biblioteca")." AND dm.cod_modulo = ".$rsReducao->getCampo("cod_modulo")." AND dm.percentual = '".$rsReducao->getCampo("percentual")."' AND dm.valor = ".$rsReducao->getCampo("valor");
                    $obTDATModalidadeReducaoAcrescimo = new TDATModalidadeReducaoAcrescimo;
                    $obTDATModalidadeReducaoAcrescimo->recuperaListaReducaoAcrescimo( $rsListaIncidencia, $stFiltroIncidencia );

                    $stReducao = "";
                    $arDadosIncidencia = array();
                    $inTotalIncidencia = 0;
                    $arTMPDados = $rsListaIncidencia->getElementos();
                    For ( $inH=0; $inH<count( $arTMPDados ); $inH++ ) {
                        $arDadosIncidencia[$inTotalIncidencia] = $arTMPDados[$inH];
                        $arDadosIncidencia[$inTotalIncidencia]["tipo"] = "Acréscimo";
                        $stReducao .= $arTMPDados[$inH]["valor"]." - ".$arTMPDados[$inH]["descricao"]."<br>";
                        $inTotalIncidencia++;
                    }

                    $obTDATModalidadeReducaoCredito = new TDATModalidadeReducaoCredito;
                    $obTDATModalidadeReducaoCredito->recuperaListaReducaoCredito( $rsListaIncidencia, $stFiltroIncidencia );

                    $arTMPDados = $rsListaIncidencia->getElementos();
                    For ( $inH=0; $inH<count( $arTMPDados ); $inH++ ) {
                        $arDadosIncidencia[$inTotalIncidencia] = $arTMPDados[$inH];
                        $arDadosIncidencia[$inTotalIncidencia]["tipo"] = "Crédito";
                        $stReducao .= $arTMPDados[$inH]["valor"]." - ".$arTMPDados[$inH]["descricao"]."<br>";
                        $inTotalIncidencia++;
                    }

                    $arReducaoSessao[$inX]['reducaoinc']    = $arDadosIncidencia;
                    $arReducaoSessao[$inX]['descricao']     = $stReducao;
                    $arReducaoSessao[$inX]['stNomFuncaoRD'] = $rsReducao->getCampo("nom_funcao");
                    $arReducaoSessao[$inX]['inCodFuncaoRD'] = $rsReducao->getCampo("cod_funcao");

                    if ( $rsReducao->getCampo("percentual") == "t" ) {
                        $arReducaoSessao[$inX]['stTipoReducao'] = "valor_percentual";
                        $arReducaoSessao[$inX]['stTipo'] = "percentual";
                    } else {
                        $arReducaoSessao[$inX]['stTipoReducao'] = "valor_absoluto";
                        $arReducaoSessao[$inX]['stTipo'] = "absoluto";
                    }

                    $arReducaoSessao[$inX]['stValor'] = $rsReducao->getCampo("valor");

                    $inX++;
                    $rsReducao->proximo();
                }

                if ( is_array($arReducaoSessao)) {
                    Sessao::write('reducao', $arReducaoSessao);
                    $rsReducao = new RecordSet;
                    $rsReducao->preenche( $arReducaoSessao );
                    $rsReducao->addFormatacao( "stValor", "NUMERIC_BR" );

                    $js .= montaListaReducoes( $rsReducao );
                }

                if ($_GET["inTipo"] != 2) {
                    //modalidade_parcela
                    $obTDATModalidadeParcela = new TDATModalidadeParcela;
                    $obTDATModalidadeParcela->recuperaTodos( $rsParcela, $stFiltro );
                    $inX = 0;
                    $arParcelasSessao = Sessao::read('parcelas');

                    while ( !$rsParcela->Eof() ) {
                        $arParcelasSessao[$inX]['flLimiteValorInicial'] = $rsParcela->getCampo("vlr_limite_inicial");
                        $arParcelasSessao[$inX]['flLimiteValorFinal']   = $rsParcela->getCampo("vlr_limite_final");
                        $arParcelasSessao[$inX]['inQtdParcelas']        = $rsParcela->getCampo("qtd_parcela");
                        $arParcelasSessao[$inX]['flValorMinimo']        = $rsParcela->getCampo("vlr_minimo");

                        $inX++;
                        $rsParcela->proximo();
                    }

                    if ( is_array($arParcelasSessao)) {
                        Sessao::write('parcelas', $arParcelasSessao);
                        $rsParcela = new RecordSet;
                        $rsParcela->preenche( $arParcelasSessao );
                        $rsParcela->addFormatacao( "flLimiteValorInicial", "NUMERIC_BR" );
                        $rsParcela->addFormatacao( "flLimiteValorFinal", "NUMERIC_BR" );
                        $rsParcela->addFormatacao( "flValorMinimo", "NUMERIC_BR" );

                        $js .=  montaListaParcelas( $rsParcela );
                    } else {
                        Sessao::write('parcelas', array());
                    }
                }
            }

            //modalidade_documento
            $obTDATModalidadeDocumento = new TDATModalidadeDocumento;
            $obTDATModalidadeDocumento->recuperaListaDocumento( $rsDocumentos, $stFiltro );
            $inX = 0;
            $arDocumentosSessao = Sessao::read('documentos');

            while ( !$rsDocumentos->Eof() ) {
                $arDocumentosSessao[$inX]['stCodDocumento']     = $rsDocumentos->getCampo("cod_documento");
                $arDocumentosSessao[$inX]['cod_tipo_documento'] = $rsDocumentos->getCampo("cod_tipo_documento");
                $arDocumentosSessao[$inX]['stNomDocumento']     = $rsDocumentos->getCampo("nome_documento");

                $inX++;
                $rsDocumentos->proximo();
            }
            if ( is_array($arDocumentosSessao)) {
                Sessao::write('documentos', $arDocumentosSessao);
                $rsDocumentos = new RecordSet;
                $rsDocumentos->preenche( $arDocumentosSessao );
                $js .= montaListaDocumentos( $rsDocumentos );
            }
            echo $js;

        }
        break;
//################################################################################################
    case "ExcluirReducaoCredito":
        $arReducaoincSessao = Sessao::read('reducaoinc');
        $nregistros = count ( $arReducaoincSessao );
        $inCountArray = 0;
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ($arReducaoincSessao[$inCount]["valor"] != $_REQUEST["inIndice1"]) {
                $arTmpAcrescimo[$inCountArray] = $arReducaoincSessao[$inCount];
                $inCountArray++;
            }
        }

        Sessao::write('reducaoinc', $arTmpAcrescimo);
        $rsDados = new RecordSet;
        if ($inCountArray)
            $rsDados->preenche( $arTmpAcrescimo );

        $js = montaListaReducoesCredito( $rsDados );
        sistemaLegado::executaFrameOculto( $js );
        break;

    case "ExcluirCredito":
        $arTmpCredito = array();
        $inCountArray = 0;
        $arCreditoSessao =  Sessao::read('credito');
        $nregistros = count ( $arCreditoSessao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ($arCreditoSessao[$inCount]["inCodCredito"] != $_REQUEST["inIndice1"]) {
                $arTmpCredito[$inCountArray] = $arCreditoSessao[$inCount];
                $inCountArray++;
            }
        }
        Sessao::write('credito', $arTmpCredito);

        $rsCredito = new RecordSet;
        $rsCredito->preenche ( $arTmpCredito );

        $js = montaListaCreditos( $rsCredito );
        sistemaLegado::executaFrameOculto( $js );
        break;

    case "IncluirCredito":
        if ($_GET["inCodCredito"]) {
            $inCodCredito = $_GET["inCodCredito"];
            $arCreditoSessao = Sessao::read('credito');
            $inRegistros = count ( $arCreditoSessao );
            for ($inX=0; $inX<$inRegistros; $inX++) {
                if ($arCreditoSessao[$inX]['inCodCredito'] == $inCodCredito) {
                    $js = "alertaAviso('Crédito (Cód: ".$inCodCredito.") já está na lista de documentos.   ','form','erro','".Sessao::getId()."');";

                    $js .= "f.stCodDocumento.value = '';\n";
                    $js .= "f.stCodDocumentoTxt.value = '';\n";
                    echo $js;
                    exit;
                }
            }

            $obTMONCredito = new TMONCredito;
            $inCodCreditoComposto = explode('.', $inCodCredito );
            $stFiltro = "WHERE ";
            $stFiltro .= " \n mc.cod_credito = ".$inCodCreditoComposto[0]." AND ";
            $stFiltro .= " \n me.cod_especie = ".$inCodCreditoComposto[1]." AND ";
            $stFiltro .= " \n mg.cod_genero = ".$inCodCreditoComposto[2]." AND ";
            $stFiltro .= " \n mn.cod_natureza = ".$inCodCreditoComposto[3];
            $obTMONCredito->recuperaRelacionamento( $rsGrupos, $stFiltro );

            if ( !$rsGrupos->Eof() ) {
                $arCreditoSessao[$inRegistros]['inCodCredito'] = $inCodCredito;
                $arCreditoSessao[$inRegistros]['stCredito']    = $rsGrupos->getCampo("descricao_credito");
                Sessao::write('credito', $arCreditoSessao);

                $rsCreditos = new RecordSet;
                $rsCreditos->preenche( $arCreditoSessao );
                $js =  montaListaCreditos( $rsCreditos );
                $js .= "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
                $js .= "f.inCodCredito.value ='';\n";

                if ($_GET["stIncidencia"] == "credito") {
                    $obCmbCredito = new Select;
                    $obCmbCredito->setRotulo               ( "Crédito"    );
                    $obCmbCredito->setTitle                ( "Crédito"    );
                    $obCmbCredito->setName                 ( "cmbCredito"  );
                    $obCmbCredito->addOption               ( "", "Selecione" );
                    $obCmbCredito->setCampoId              ( "[inCodCredito]-[stCredito]" );
                    $obCmbCredito->setCampoDesc            ( "stCredito" );
                    $obCmbCredito->preencheCombo           ( $rsCreditos );
                    $obCmbCredito->setNull                 ( false );
                    $obCmbCredito->setStyle                ( "width: 220px" );

                    $obFormulario = new Formulario;
                    $obFormulario->addComponente( $obCmbCredito );
                    $obFormulario->montaInnerHTML();

                    $js .= "d.getElementById('spnListaReducaoIncidencia').innerHTML = '". $obFormulario->getHTML(). "';\n";
                }

                echo $js;
            }
        } else {
            $js = "alertaAviso('@Campo \'Crédito\' não está preenchido.','form','erro','".Sessao::getId()."');";

            echo $js;
        }
        break;

    case "limpaCredito":
        $stJs = "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
        $stJs .= "f.inCodCredito.value ='';\n";
        echo $stJs;
        break;

    case "ExcluirAcrescimo":
        $arTmpAcrescimo = array();
        $inCountArray = 0;
        $arAcrescimoSessao = Sessao::read('acrescimo');
        $nregistros = count ( $arAcrescimoSessao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ($arAcrescimoSessao[$inCount]["inCodAcrescimo"] != $_REQUEST["inIndice1"]
                || $arAcrescimoSessao[$inCount]["stAcrescimoIncidenciaDesc"] != $_REQUEST["inIndice2"]) {
                $arTmpAcrescimo[$inCountArray] = $arAcrescimoSessao[$inCount];
                $inCountArray++;
            }
        }

        Sessao::write('acrescimo', $arTmpAcrescimo);

        $rsAcrescimo = new RecordSet;
        $rsAcrescimo->preenche ( $arTmpAcrescimo );

        $js = montaListaAcrescimos( $rsAcrescimo );
        sistemaLegado::executaFrameOculto( $js );
        break;

    case "IncluirAcrescimo":
        if ($_GET["inCodAcrescimo"] && $_GET["inCodFuncaoAC"] && $_GET["stAcrescimoIncidencia"]) {
            $arAcrescimoSessao = Sessao::read('acrescimo');
            $inRegistros = count ( $arAcrescimoSessao );
            $arDados = explode( ".", $_GET["inCodAcrescimo"] );
            for ($inX=0; $inX<$inRegistros; $inX++) {

                if ( ($arAcrescimoSessao[$inX]['inCodAcrescimo'] == $_GET["inCodAcrescimo"] )
                  && ($arAcrescimoSessao[$inX]['inCodFuncaoAC'] == $_GET["inCodFuncaoAC"] )
                  && (($_GET["stAcrescimoIncidencia"]=="ambos")?true:($arAcrescimoSessao[$inX]['stAcrescimoIncidencia'] == $_GET["stAcrescimoIncidencia"]))
                ) {
                    $js = "alertaAviso('@Um acrescimo similar já está na lista de acrescimos legais.   ','form','erro','".Sessao::getId()."');";
                    $js .= "d.getElementById('stDescricaoAcrescimo').innerHTML = '&nbsp;';\n";
                    $js .= "d.getElementById('stFuncaoAC').innerHTML = '&nbsp;';\n";
                    $js .= "f.inCodAcrescimo.value ='';\n";
                    $js .= "f.stAcrescimoIncidencia[0].checked = false;\n";
                    $js .= "f.stAcrescimoIncidencia[1].checked = false;\n";
                    $js .= "f.stAcrescimoIncidencia[2].checked = false;\n";
                    $js .= "f.inCodFuncaoAC.value ='';\n";
                    echo $js;
                    exit;
                }
            }

            //buscar nome da funcao
            $arCodFuncao = explode('.', $_GET["inCodFuncaoAC"] );

            $obTFuncao = new TAdministracaoFuncao;
            $obTFuncao->setDado( "cod_biblioteca", $arCodFuncao[1] );
            $obTFuncao->setDado( "cod_modulo", $arCodFuncao[0] );
            $obTFuncao->setDado( "cod_funcao", $arCodFuncao[2] );
            $obTFuncao->recuperaPorChave( $rsFuncao );

            if ( $_GET["stAcrescimoIncidencia"] == "ambos" )
                $inX = 2;
            else
                $inX = 1;

            $obTMONAcrescimo = new TMONAcrescimo();
            $obTMONAcrescimo->setDado('cod_acrescimo', $arDados[0] );
            $obTMONAcrescimo->setDado('cod_tipo', $arDados[1] );
            $obTMONAcrescimo->recuperaPorChave($rsRecordSet);
            $arAcrescimoSessao = Sessao::read('acrescimo');

            for ($inY=0; $inY<$inX; $inY++) {
                $arAcrescimoSessao[$inRegistros]['stNomFuncaoAC'] = $rsFuncao->getCampo("nom_funcao");
                $arAcrescimoSessao[$inRegistros]['inCodFuncaoAC'] = $_GET["inCodFuncaoAC"];
                if ($_GET["stAcrescimoIncidencia"] == "ambos") {
                    if ($inY) {
                        $arAcrescimoSessao[$inRegistros]['stAcrescimoIncidenciaDesc'] = "Pagamentos";
                        $arAcrescimoSessao[$inRegistros]['stAcrescimoIncidencia']     = true;
                    } else {
                        $arAcrescimoSessao[$inRegistros]['stAcrescimoIncidenciaDesc'] = "Inscrição em Dívida / Cobranças";
                        $arAcrescimoSessao[$inRegistros]['stAcrescimoIncidencia']     = false;
                    }
                } else {
                    $arAcrescimoSessao[$inRegistros]['stAcrescimoIncidencia'] = $_GET["stAcrescimoIncidencia"];
                    if ( $_GET["stAcrescimoIncidencia"] == 'true' )
                        $arAcrescimoSessao[$inRegistros]['stAcrescimoIncidenciaDesc'] = "Pagamentos";
                    else
                        $arAcrescimoSessao[$inRegistros]['stAcrescimoIncidenciaDesc'] = "Inscrição em Dívida / Cobranças";
                }

                $arAcrescimoSessao[$inRegistros]['cod_tipo']        = $rsRecordSet->getCampo('cod_tipo');
                $arAcrescimoSessao[$inRegistros]['inCodAcrescimo']  = $_GET["inCodAcrescimo"];
                $arAcrescimoSessao[$inRegistros]['stDescAcrescimo'] = $rsRecordSet->getCampo('descricao_acrescimo');

               $inRegistros++;
            }
            Sessao::write('acrescimo', $arAcrescimoSessao);
            $rsAcrescimo = new RecordSet;
            $rsAcrescimo->preenche( $arAcrescimoSessao);

            $js =  montaListaAcrescimos( $rsAcrescimo );
            $js .= "d.getElementById('stDescricaoAcrescimo').innerHTML = '&nbsp;';\n";
            $js .= "d.getElementById('stFuncaoAC').innerHTML = '&nbsp;';\n";
            $js .= "f.inCodAcrescimo.value ='';\n";
            $js .= "f.inCodFuncaoAC.value ='';\n";
            $js .= "f.stAcrescimoIncidencia[0].checked = false;\n";
            $js .= "f.stAcrescimoIncidencia[1].checked = false;\n";
            $js .= "f.stAcrescimoIncidencia[2].checked = false;\n";

            if ($_GET["stIncidencia"] == "acrescimo") {
                $obCmbAcrescimo = new Select;
                $obCmbAcrescimo->setRotulo               ( "Acréscimo"    );
                $obCmbAcrescimo->setTitle                ( "Acréscimo"    );
                $obCmbAcrescimo->setName                 ( "cmbAcrescimo"  );
                $obCmbAcrescimo->addOption               ( "", "Selecione" );
                $obCmbAcrescimo->setCampoId              ( "[cod_tipo]-[inCodAcrescimo]-[stDescAcrescimo]-[stAcrescimoIncidencia]" );
                $obCmbAcrescimo->setCampoDesc            ( "stDescAcrescimo" );
                $obCmbAcrescimo->preencheCombo           ( $rsAcrescimo );
                $obCmbAcrescimo->setNull                 ( false );
                $obCmbAcrescimo->setStyle                ( "width: 220px" );

                $obFormulario = new Formulario;
                $obFormulario->addComponente($obCmbAcrescimo);
                $obFormulario->montaInnerHTML();

                $js .= "d.getElementById('spnListaReducaoIncidencia').innerHTML = '". $obFormulario->getHTML(). "';\n";
            }

            echo $js;
        } else {
            if ( !$_GET["stAcrescimoIncidencia"] )
                $js = "alertaAviso('@Campo \'Incidência\' não está preenchido.','form','erro','".Sessao::getId()."');";
            else
            if ( !$_GET["inCodFuncaoAC"] )
                $js = "alertaAviso('@Campo \'Regra de Utilização\' não está preenchido.','form','erro','".Sessao::getId()."');";
            else
                $js = "alertaAviso('@Campo \'Acréscimo\' não está preenchido.','form','erro','".Sessao::getId()."');";

            echo $js;
        }
        break;

    case "limpaAcrescimo":
        $stJs = "d.getElementById('stDescricaoAcrescimo').innerHTML = '&nbsp;';\n";
        $stJs .= "d.getElementById('stFuncaoAC').innerHTML = '&nbsp;';\n";
        $stJs .= "f.inCodAcrescimo.value ='';\n";
        $stJs .= "f.inCodFuncaoAC.value ='';\n";
        echo $stJs;
        break;

    case "LimparSessao":
        Sessao::remove('documentos');
        Sessao::remove('parcelas');
        Sessao::remove('reducao');
        Sessao::remove('acrescimo');
        Sessao::remove('credito');
        break;

    case "AlterarReducao":
        $arReducaoSessao = Sessao::read('reducao');
        $nregistros = count ( $arReducaoSessao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ( ($arReducaoSessao[$inCount]["inCodFuncaoRD"] == $_REQUEST["inIndice1"])
                && ($arReducaoSessao[$inCount]["stValor"] == $_REQUEST["inIndice2"])
                && ($arReducaoSessao[$inCount]["stTipoReducao"] == $_REQUEST["inIndice3"]) ) {
                $arReducaoincSessao = $arReducaoincSessao[$inCount]["reducaoinc"];
                Sessao::write('reducaoinc', $arReducaoincSessao);

                $js = "d.getElementById('stFuncaoRD').innerHTML = '".$arReducaoSessao[$inCount]["stNomFuncaoRD"]."';\n";
                $js .= "f.inCodFuncaoRD.value ='".$arReducaoSessao[$inCount]["inCodFuncaoRD"]."';\n";
                $js .= "f.stValor.value ='".$arReducaoSessao[$inCount]["stValor"]."';\n";
                $js .= "f.stTipoReducao.value ='".$arReducaoSessao[$inCount]["stTipoReducao"]."';\n";
                if ($arReducaoSessao[$inCount]["stTipoReducao"] == "valor_percentual") {
                    $js .= "f.stTipoReducao[0].checked = true;\n";
                    $js .= "f.stTipoReducao[1].checked = false;\n";
                } else {
                    $js .= "f.stTipoReducao[0].checked = false;\n";
                    $js .= "f.stTipoReducao[1].checked = true;\n";
                }

                $rsDados = new RecordSet;
                $rsDados->preenche( $arReducaoincSessao );
                $js .= montaListaReducoesCredito( $rsDados );

                Sessao::write('reducao_alteracao', $inCount);
                sistemaLegado::executaFrameOculto( $js );

                break;
            }
        }
        break;

    case "ExcluirReducao":
        $arTmpReducoes = array();
        $inCountArray = 0;
        $arReducaoSessao = Sessao::read('reducao');
        $nregistros = count ( $arReducaoSessao );

        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ( ($arReducaoSessao[$inCount]["inCodFuncaoRD"]   != $_REQUEST["inIndice1"])
                || (number_format( $arReducaoSessao[$inCount]["stValor"], 2, ",", "." ) != number_format( $_REQUEST["inIndice2"], 2, ",", "." ) )
                || ($arReducaoSessao[$inCount]["stTipoReducao"] != $_REQUEST["inIndice3"]) ) {
                $arTmpReducoes[$inCountArray] = $arReducaoSessao[$inCount];
                $inCountArray++;
            }
        }
        $arReducaoSessao = $arTmpReducoes;
        Sessao::write('reducao', $arReducaoSessao );
        $rsReducao = new RecordSet;
        $rsReducao->preenche ( $arReducaoSessao );

        $js = montaListaReducoes( $rsReducao );
        sistemaLegado::executaFrameOculto( $js );
        break;

    case "montaIncidenciaAcrescimo":
        $rsAcrescimos = new RecordSet;
        $arAcrescimoSessao = Sessao::read('acrescimo');
        $rsAcrescimos->preenche( $arAcrescimoSessao );

        $obCmbAcrescimo = new Select;
        $obCmbAcrescimo->setRotulo               ( "Acréscimo"    );
        $obCmbAcrescimo->setTitle                ( "Acréscimo"    );
        $obCmbAcrescimo->setName                 ( "cmbAcrescimo"  );
        $obCmbAcrescimo->addOption               ( "", "Selecione" );
        $obCmbAcrescimo->setCampoId              ( "[cod_tipo]-[inCodAcrescimo]-[stDescAcrescimo]-[stAcrescimoIncidencia]" );
        $obCmbAcrescimo->setCampoDesc            ( "stDescAcrescimo" );
        $obCmbAcrescimo->preencheCombo           ( $rsAcrescimos );
        $obCmbAcrescimo->setNull                 ( false );
        $obCmbAcrescimo->setStyle                ( "width: 220px" );

        $obFormulario = new Formulario;
        $obFormulario->addComponente($obCmbAcrescimo);
        $obFormulario->montaInnerHTML();

        $js = "d.getElementById('spnListaReducaoIncidencia').innerHTML = '". $obFormulario->getHTML(). "';\n";
        echo $js;
        break;

    case "montaIncidenciaCredito":
        $rsCreditos = new RecordSet;
        $arAcrescimoSessao = Sessao::read('credito');
        $rsCreditos->preenche( $arAcrescimoSessao );

        $obCmbCredito = new Select;
        $obCmbCredito->setRotulo               ( "Crédito"    );
        $obCmbCredito->setTitle                ( "Crédito"    );
        $obCmbCredito->setName                 ( "cmbCredito"  );
        $obCmbCredito->addOption               ( "", "Selecione" );
        $obCmbCredito->setCampoId              ( "[inCodCredito]-[stCredito]" );
        $obCmbCredito->setCampoDesc            ( "stCredito" );
        $obCmbCredito->preencheCombo           ( $rsCreditos );
        $obCmbCredito->setNull                 ( false );
        $obCmbCredito->setStyle                ( "width: 220px" );

        $obFormulario = new Formulario;
        $obFormulario->addComponente( $obCmbCredito );
        $obFormulario->montaInnerHTML();

        $js = "d.getElementById('spnListaReducaoIncidencia').innerHTML = '". $obFormulario->getHTML(). "';\n";
        echo $js;
        break;

    case "incluirReducaoCredito":
        $rsDados = new RecordSet;
        $arReducaoincSessao = Sessao::read('reducaoinc');
        if (($_GET["stIncidencia"] == "credito") && $_GET["cmbCredito"] ) {
            $arTMPCred = explode( "-", $_GET["cmbCredito"] );
            $inTotal = count( $arReducaoincSessao );
            for ($inX=0; $inX<$inTotal; $inX++) {
                if ($arReducaoincSessao[$inX]["valor"] == $arTMPCred[0]) {
                    $js = "alertaAviso('@O crédito informado (".$arTMPCred[0].") já está na lista de incidências da redução.','form','erro','".Sessao::getId()."');";
                    echo $js;
                    exit;
                }
            }

            $arReducaoincSessao[$inTotal]["valor"] = $arTMPCred[0];
            $arReducaoincSessao[$inTotal]["descricao"] = $arTMPCred[1];
            $arReducaoincSessao[$inTotal]["tipo"] = "Crédito";

            Sessao::write('reducaoinc', $arReducaoincSessao);
            $rsDados->preenche( $arReducaoincSessao );
        }else
            if (($_GET["stIncidencia"] == "acrescimo") && $_GET["cmbAcrescimo"] ) {
                $arReducaoincSessao = Sessao::read('reducaoinc');
                $inTotal = count( $arReducaoincSessao );
                $arTMPAcresc = explode ( "-", $_GET["cmbAcrescimo"] );
                for ($inX=0; $inX<$inTotal; $inX++) {
                    if ($arReducaoincSessao[$inX]["valor"] == $arTMPAcresc[1]) {
                        $js = "alertaAviso('@O acréscimo informado (".$arTMPAcresc[1].") já está na lista de incidências da redução.','form','erro','".Sessao::getId()."');";
                        echo $js;
                        exit;
                    }
                }

                $arReducaoincSessao[$inTotal]["pagamento"] = $arTMPAcresc[3];
                $arReducaoincSessao[$inTotal]["valor"] = $arTMPAcresc[1];
                $arReducaoincSessao[$inTotal]["descricao"] = $arTMPAcresc[2];
                $arReducaoincSessao[$inTotal]["cod_tipo"] = $arTMPAcresc[0];
                $arReducaoincSessao[$inTotal]["tipo"] = "Acréscimo";

                Sessao::write('reducaoinc', $arReducaoincSessao);
                $rsDados->preenche( $arReducaoincSessao );
            } else {
                if ( !$_GET["stIncidencia"] )
                    $js = "alertaAviso('@O campo Incidência está vazio.','form','erro','".Sessao::getId()."');";
                else
                    if (($_GET["stIncidencia"] == "acrescimo") && !$_GET["cmbAcrescimo"])
                        $js = "alertaAviso('@O campo Acréscimo está vazio.','form','erro','".Sessao::getId()."');";
                    else
                        $js = "alertaAviso('@O campo Crédito está vazio.','form','erro','".Sessao::getId()."');";

                echo $js;
                exit;
            }

        $js .= montaListaReducoesCredito( $rsDados );

        echo $js;
        break;

    case "incluirReducao":
        $arReducaoincSessao = Sessao::read('reducaoinc');
        $arReducaoSessao    = Sessao::read('reducao');
        if ( $_GET["inCodFuncaoRD"] && $_GET["stValor"] && $_GET["stTipoReducao"] && count($arReducaoincSessao) ) {
            $inRegistros = count ( $arReducaoSessao );
            for ($inX=0; $inX<$inRegistros; $inX++) {
                if ($arReducaoSessao[$inX]['inCodFuncaoRD'] == $_GET["inCodFuncaoRD"]
                  && $arReducaoSessao[$inX]['stValor']       == $_GET["stValor"]
                  && $arReducaoSessao[$inX]['stTipoReducao'] == $_GET["stTipoReducao"]) {
                    if ( Sessao::read('reducao_alteracao')   == $inX ) {
                        continue;
                    }

                    $js = "alertaAviso('@Uma redução similar já está na lista de parcelas.   ','form','erro','".Sessao::getId()."');";

                    $js .= "d.getElementById('stFuncaoRD').innerHTML = '&nbsp;';\n";
                    $js .= "f.inCodFuncaoRD.value ='';\n";
                    $js .= "f.stValor.value ='';\n";
                    $js .= "f.stTipoReducao.value ='';\n";
                    $js .= "f.stTipoReducao[0].checked = false;\n";
                    $js .= "f.stTipoReducao[1].checked = false;\n";

                    echo $js;
                    exit;
                }
            }

            //buscar nome da funcao
            $arCodFuncao = explode('.', $_GET["inCodFuncaoRD"] );

            $obTFuncao = new TAdministracaoFuncao;
            $obTFuncao->setDado( "cod_biblioteca", $arCodFuncao[1] );
            $obTFuncao->setDado( "cod_modulo", $arCodFuncao[0] );
            $obTFuncao->setDado( "cod_funcao", $arCodFuncao[2] );
            $obTFuncao->recuperaPorChave( $rsFuncao );

            $stReducao = "";
            for ( $inX=0; $inX<count($arReducaoincSessao); $inX++ )
                $stReducao .= $arReducaoincSessao[$inX]["valor"]." - ".$arReducaoincSessao[$inX]["descricao"]."<br>";

            if ( Sessao::read('reducao_alteracao') != -1 ) {
                $inRegistros = Sessao::read("reducao_alteracao");
                Sessao::write('reducao_alteracao', -1);
            }

            if ( $_GET["stTipoReducao"] == "valor_percentual" )
                $arReducaoSessao[$inRegistros]['stTipo'] = "percentual";
            else
                $arReducaoSessao[$inRegistros]['stTipo'] = "absoluto";

            $arReducaoSessao[$inRegistros]['stNomFuncaoRD'] = $rsFuncao->getCampo("nom_funcao");
            $arReducaoSessao[$inRegistros]['inCodFuncaoRD'] = $_GET["inCodFuncaoRD"];
            $arReducaoSessao[$inRegistros]['stValor']       = $_GET["stValor"];
            $arReducaoSessao[$inRegistros]['stTipoReducao'] = $_GET["stTipoReducao"];
            $arReducaoSessao[$inRegistros]['reducaoinc']    = Sessao::read('reducaoinc');
            $arReducaoSessao[$inRegistros]['descricao']     = $stReducao;

            Sessao::remove('reducaoinc');
            Sessao::write('reducao', $arReducaoSessao);

            $rsDados = new RecordSet;
            $rsReducao = new RecordSet;
            $rsReducao->preenche( $arReducaoSessao );
            $rsReducao->addFormatacao( "stValor", "NUMERIC_BR" );

            $js =  montaListaReducoes( $rsReducao );
            $js .= montaListaReducoesCredito( $rsDados );
            $js .= "d.getElementById('stFuncaoRD').innerHTML = '&nbsp;';\n";
            $js .= "f.inCodFuncaoRD.value ='';\n";
            $js .= "f.stValor.value ='';\n";
            $js .= "f.stTipoReducao.value ='';\n";
            $js .= "f.stTipoReducao[0].checked = false;\n";
            $js .= "f.stTipoReducao[1].checked = false;\n";
            echo $js;
        } else {
            if ( !count( Sessao::read('reducaoinc')) )
                $js = "alertaAviso('@Lista de Incidências da Redução está vazia.','form','erro','".Sessao::getId()."');";
            if ( !$_GET["inCodFuncaoRD"] )
                $js = "alertaAviso('@Campo \'Regra de Utilização\' não está preenchido.','form','erro','".Sessao::getId()."');";
            else
            if ( !$_GET["stValor"] )
                $js = "alertaAviso('@Campo \'Valor\' não está preenchido.','form','erro','".Sessao::getId()."');";
            else
                $js = "alertaAviso('@Campo \'Tipo da Redução\' não está preenchido.','form','erro','".Sessao::getId()."');";

            echo $js;
        }
        break;

    case "limpaReducao":
        $stJs = "d.getElementById('stFuncaoRD').innerHTML = '&nbsp;';\n";
        $stJs .= "f.inCodFuncaoRD.value ='';\n";
        $stJs .= "f.stValor.value ='';\n";
        $stJs .= "f.stTipoReducao.value ='';\n";
        $stJs .= "f.stTipoReducao[0].checked = false;\n";
        $stJs .= "f.stTipoReducao[1].checked = false;\n";
        echo $stJs;
        break;

    case "ExcluirParcela":
        $arTmpParcelas = array();
        $inCountArray = 0;
        $arParcelasSessao = Sessao::read('parcelas');
        $nregistros = count ( $arParcelasSessao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ($arParcelasSessao[$inCount]["flLimiteValorInicial"] != $_REQUEST["inIndice1"]
                && $arParcelasSessao[$inCount]["flLimiteValorFinal"] != $_REQUEST["inIndice2"]
                && $arParcelasSessao[$inCount]["inQtdParcelas"]      != $_REQUEST["inIndice3"]
                && $arParcelasSessao[$inCount]["flValorMinimo"]      != $_REQUEST["inIndice4"]) {
                $arTmpParcelas[$inCountArray] = $arParcelasSessao[$inCount];
                $inCountArray++;
            }
        }
        $arParcelasSessao = $arTmpParcelas;
        Sessao::write('parcelas', $arParcelasSessao);

        $rsParcelas = new RecordSet;
        $rsParcelas->preenche ( $arParcelasSessao );

        $js = montaListaParcelas ( $rsParcelas );
        sistemaLegado::executaFrameOculto( $js );
        break;

    case "incluirParcela":
        if ($_GET["flLimiteValorInicial"] && $_GET["flLimiteValorFinal"] && $_GET["inQtdParcelas"] && $_GET["flValorMinimo"]) {

            $flLimiteValorInicial = str_replace ( '.', '', $_GET["flLimiteValorInicial"] );
            $flLimiteValorInicial = str_replace ( ',', '.', $flLimiteValorInicial );

            $flLimiteValorFinal = str_replace ( '.', '', $_GET['flLimiteValorFinal'] );
            $flLimiteValorFinal = str_replace ( ',', '.', $flLimiteValorFinal );

            $flValorMinimo = str_replace ( '.', '', $_GET["flValorMinimo"] );
            $flValorMinimo = str_replace ( ',', '.', $flValorMinimo );

            $inRegistros = count ( $arParcelasSessao );
            for ($inX=0; $inX<$inRegistros; $inX++) {
                if ($arParcelasSessao[$inX]['flLimiteValorInicial'] >= $flLimiteValorInicial && $arParcelasSessao[$inX]['flLimiteValorFinal'] <= $flLimiteValorFinal) {
                    $js = "alertaAviso('@Parcela possuí limites similares.','form','erro','".Sessao::getId()."');";
                    echo $js;
                    exit;
                }else
                if ($arParcelasSessao[$inX]['flLimiteValorInicial'] == $flLimiteValorInicial  && $arParcelasSessao[$inX]['flLimiteValorFinal'] == $flLimiteValorFinal
                 && $arParcelasSessao[$inX]['inQtdParcelas'] == $_GET["inQtdParcelas"]
                 && $arParcelasSessao[$inX]['flValorMinimo'] == $flValorMinimo) {
                    $js = "alertaAviso('@Uma parcela similar já está na lista de parcelas.   ','form','erro','".Sessao::getId()."');";

                    $js .= "f.flLimiteValorInicial.value = '';\n";
                    $js .= "f.flLimiteValorFinal.value = '';\n";
                    $js .= "f.inQtdParcelas.value = '';\n";
                    $js .= "f.flValorMinimo.value = '';\n";

                    echo $js;
                    exit;
                }
            }

            $arParcelasSessao[$inRegistros]['flLimiteValorInicial'] = $flLimiteValorInicial;
            $arParcelasSessao[$inRegistros]['flLimiteValorFinal']   = $flLimiteValorFinal;
            $arParcelasSessao[$inRegistros]['inQtdParcelas']        = $_GET["inQtdParcelas"];
            $arParcelasSessao[$inRegistros]['flValorMinimo']        = $flValorMinimo;
            Sessao::Write('parcelas', $arParcelasSessao);

            $rsParcelas = new RecordSet;
            $rsParcelas->preenche( $arParcelasSessao );
            $rsParcelas->addFormatacao( "flLimiteValorInicial", "NUMERIC_BR" );
            $rsParcelas->addFormatacao( "flLimiteValorFinal"  , "NUMERIC_BR" );
            $rsParcelas->addFormatacao( "flValorMinimo"       , "NUMERIC_BR" );

            $js =  montaListaParcelas( $rsParcelas );
            $js .= "f.flLimiteValorInicial.value = '';\n";
            $js .= "f.flLimiteValorFinal.value = '';\n";
            $js .= "f.inQtdParcelas.value = '';\n";
            $js .= "f.flValorMinimo.value = '';\n";
            echo $js;
        } else {
            if ( !$_GET["flLimiteValorInicial"] )
                $js = "alertaAviso('@Campo \'Limite Valor Inicial\' não está preenchido.','form','erro','".Sessao::getId()."');";
            else
            if ( !$_GET["flLimiteValorFinal"] )
                $js = "alertaAviso('@Campo \'Limite Valor Final\' não está preenchido.','form','erro','".Sessao::getId()."');";
            else
            if ( !$_GET["inQtdParcelas"] )
                $js = "alertaAviso('@Campo \'Quantidade de Parcelas\' não está preenchido.','form','erro','".Sessao::getId()."');";
            else
                $js = "alertaAviso('@Campo \'Valor Mínimo\' não está preenchido.','form','erro','".Sessao::getId()."');";

            echo $js;
        }
        break;

    case "limpaParcela":
        $js = "f.flLimiteValorInicial.value = '';\n";
        $js .= "f.flLimiteValorFinal.value = '';\n";
        $js .= "f.inQtdParcelas.value = '';\n";
        $js .= "f.flValorMinimo.value = '';\n";
        echo $js;
        break;

    case "limpaDocumento":
        $js .= "f.stCodDocumento.value = '';\n";
        $js .= "f.stCodDocumentoTxt.value = '';\n";
        echo $js;
        break;

    case "ExcluirDocumento":
        $stCodDocumentoExcluir = $_REQUEST["inIndice1"];
        $arTmpDocumentos = array();
        $inCountArray = 0;
        $arDocumentosSessao = Sessao::read('documentos');
        $nregistros = count ( $arDocumentosSessao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ($arDocumentosSessao[$inCount]["stCodDocumento"] != $stCodDocumentoExcluir) {
                $arTmpDocumentos[$inCountArray] = $arDocumentosSessao[$inCount];
                $inCountArray++;
            }
        }
        Sessao::write('documentos', $arTmpDocumentos);

        $rsDocumentos = new RecordSet;
        $rsDocumentos->preenche ( $arTmpDocumentos );

        $js = montaListaDocumentos ( $rsDocumentos );
        sistemaLegado::executaFrameOculto( $js );
        break;

    case "IncluirDocumento":
        if ($_GET["stCodDocumento"]) {
            $stCodDocumento = $_GET["stCodDocumento"];
            $arDocumentosSessao = Sessao::read('documentos');
            $inRegistros = count ( $arDocumentosSessao );
            for ($inX=0; $inX<$inRegistros; $inX++) {
                if ($arDocumentosSessao[$inX]['stCodDocumento'] == $stCodDocumento) {
                    $js = "alertaAviso('@Documento (Cód: ".$stCodDocumento.") já está na lista de documentos.   ','form','erro','".Sessao::getId()."');";

                    $js .= "f.stCodDocumento.value = '';\n";
                    $js .= "f.stCodDocumentoTxt.value = '';\n";
                    echo $js;
                    exit;
                }
            }

            $stFiltro = "where a.cod_documento = ".$stCodDocumento;
            $obTModeloDocumento = new TAdministracaoModeloDocumento;
            $obTModeloDocumento->recuperaRelacionamento($rsDocumentos, $stFiltro);
            if ( !$rsDocumentos->Eof() ) {
                $arDocumentosSessao[$inRegistros]['stCodDocumento']     = $stCodDocumento;
                $arDocumentosSessao[$inRegistros]['cod_tipo_documento'] = $rsDocumentos->getCampo( "cod_tipo_documento" );
                $arDocumentosSessao[$inRegistros]['stNomDocumento']     = $rsDocumentos->getCampo( "nome_documento" );

                $rsDocumentos = new RecordSet;
                Sessao::write('documentos', $arDocumentosSessao);
                $rsDocumentos->preenche( $arDocumentosSessao );
                $js =  montaListaDocumentos( $rsDocumentos );
                $js .= "f.stCodDocumento.value = '';\n";
                $js .= "f.stCodDocumentoTxt.value = '';\n";
                echo $js;
            }
        } else {
            $js = "alertaAviso('@Campo \'Documento\' não está preenchido.','form','erro','".Sessao::getId()."');";

            echo $js;
        }
        break;

    case "preencheFuncaoAC":
        echo preencheFuncao( "inCodFuncaoAC", "stFuncaoAC" );
        break;

    case "preencheFuncaoRD":
        echo preencheFuncao( "inCodFuncaoRD", "stFuncaoRD" );
        break;
}
