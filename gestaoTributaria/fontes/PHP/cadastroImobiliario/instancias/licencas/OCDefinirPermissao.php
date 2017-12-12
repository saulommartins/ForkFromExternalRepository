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
    * Página de Frame Oculto de Definir Permissao
    * Data de Criação   : 17/03/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * $Id: OCDefinirPermissao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.28
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoLicenca.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMPermissao.class.php" );

function montaListaUsuarios($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista );
        $obLista->setTitulo ( "Lista de Usuários" );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "CGM" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Nome" );
        $obLista->ultimoCabecalho->setWidth ( 30 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Tipo de Licença" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "inCGM" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "stNomCGM" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "[inTipoLicenca] - [nom_tipo]" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );

        $obLista->ultimaAcao->setLink ( "JavaScript:excluirUsuario();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1", "inCGM" );
        $obLista->ultimaAcao->addCampo ( "inIndice2", "inTipoLicenca" );
        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnListaPermissao').innerHTML = '".$stHTML."';\n";

    return $js;
}

switch ($_REQUEST['stCtrl']) {
    case "ListaPermissoes":
        $obTCIMPermissao = new TCIMPermissao;
        $obTCIMPermissao->recuperaListaPermissoes( $rsLista );
        $arPermissoesSessao = array();
        if ( !$rsLista->Eof() ) {
            while ( !$rsLista->Eof() ) {
                $arPermissoesSessao[] = array (
                                                "inCGM"         => $rsLista->getCampo("incgm"),
                                                "stNomCGM"      => $rsLista->getCampo("stnomcgm"),
                                                "inTipoLicenca" => $rsLista->getCampo("intipolicenca"),
                                                "nom_tipo"      => $rsLista->getCampo("nom_tipo")
                                               );
                $rsLista->proximo();
            }

            Sessao::write('permissoes', $arPermissoesSessao);
            $rsLista->preenche( $arPermissoesSessao );
            $stJs = montaListaUsuarios( $rsLista );
            echo $stJs;
        }
        break;

    case "IncluirPermissao":
        if (!$_GET["cmbTipoLicenca"]) {
            $stJs = "alertaAviso( '@Nenhum tipo de licença foi selecionado.', 'form', 'erro', '".Sessao::getId()."' );";
        }else
        if (!$_GET["inCGM"]) {
            $stJs = "alertaAviso( '@Campo usuário vazio.', 'form', 'erro', '".Sessao::getId()."' );";
        } else {
            $boEstaNaLista = false;
            $arPermissoesSessao = Sessao::read('permissoes');
            for ( $inX=0; $inX<count( $arPermissoesSessao ); $inX++ ) {
                if ( ( $_GET["inCGM"] == $arPermissoesSessao[$inX]["inCGM"] ) && ( $_GET["cmbTipoLicenca"] == $arPermissoesSessao[$inX]["inTipoLicenca"] ) ) {
                    $boEstaNaLista = true;
                    break;
                }
            }

            if ($boEstaNaLista) {
                $stJs = "alertaAviso( '@Usuário já se encontra na lista de permissões.', 'form', 'erro', '".Sessao::getId()."' );";
            } else {
                $obTCIMTipoLicenca = new TCIMTipoLicenca;
                $stFiltro = " WHERE cod_tipo = ".$_GET["cmbTipoLicenca"];
                $obTCIMTipoLicenca->recuperaTodos( $rsTipoLicenca, $stFiltro );

                $arPermissoesSessao[] = array(
                                               "inCGM"          => $_GET["inCGM"],
                                               "stNomCGM"       => $_GET["stNomCGM"],
                                               "inTipoLicenca"  => $_GET["cmbTipoLicenca"],
                                               "nom_tipo"       => $rsTipoLicenca->getCampo("nom_tipo")
                                              );
                $rsListaUsuarios = new RecordSet;
                $rsListaUsuarios->preenche( $arPermissoesSessao );
                Sessao::write('permissoes', $arPermissoesSessao);

                $stJs .= montaListaUsuarios( $rsListaUsuarios );
            }

            $stJs .= 'f.inCGM.value = "";';
            $stJs .= 'f.inTipoLicenca.value = "";';
            $stJs .= "d.getElementById('stNomCGM').innerHTML = '&nbsp;';\n";
            $stJs .= "f.cmbTipoLicenca.options[0].selected = true;\n";
        }

        echo $stJs;
        break;

    case "ExcluirUsuario":
        $inCGM = $_GET["inIndice1"];
        $inTipoLicenca = $_GET["inIndice2"];
        $arTMP = array();
        $arPermissoesSessao = Sessao::read('permissoes');
        for ( $inX=0; $inX<count($arPermissoesSessao); $inX++ ) {
            if ( ( $arPermissoesSessao[$inX]["inCGM"] != $inCGM ) || ( $arPermissoesSessao[$inX]["inTipoLicenca"] != $inTipoLicenca ) ) {
                $arTMP[] = $arPermissoesSessao[$inX];
            }
        }

        Sessao::write('permissoes', $arTMP);
        $rsListaUsuarios = new RecordSet;
        $rsListaUsuarios->preenche( $arTMP );
        $stJs .= montaListaUsuarios( $rsListaUsuarios );
        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "LimparSessao":
        Sessao::remove('permissoes');
        break;
}
