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
    * Página de Formulário da Caonfiguração do cadastro imobiliario
    * Data de Criação   : 18/03/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: PRManterCondominio.php 63230 2015-08-05 20:49:42Z arthur $

    * Casos de uso: uc-05.01.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php"      );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma    = "ManterCondominio";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php?";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$pgFormConstrucao = "../construcao/FMManterConstrucaoVinculo.php";
$pgFormEdificacao = "../edificacao/FMManterEdificacaoVinculo.php";

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

function alertaAvisoRedirect($location="", $objeto="", $tipo="n_incluir", $chamada="erro", $sessao, $caminho="", $func="")
{
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                //alertaAviso      ( "'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'","'.$caminho.'");
                mudaMenu         ( "'.$func.'"     );
                mudaTelaPrincipal( "'.$location.'" );
           </script>';
}

switch ($stAcao) {
    case "incluir":

        $obErro = new Erro;

        $obRCIMCondominio = new RCIMCondominio;
        $obRCIMCondominio->setCodigoTipo            ( $_REQUEST["inCodigoTipo"]     );
        $obRCIMCondominio->setNomCondominio         ( $_REQUEST["stNomCondominio"]  );
        $obRCIMCondominio->obRCGM->setNumCGM        ( $_REQUEST["inNumCGM"]         );
        $obRCIMCondominio->setAreaTotalComum        ( $_REQUEST["inAreaTotalComum"] );

        $flValor = str_replace( ",", ".", str_replace( ".", "", $_REQUEST["inAreaTotalComum"] ) );
        if ($flValor < 0.01) {
            SistemaLegado::exibeAviso( "Campo 'Área Total Comum' deve ser maior que zero!","n_incluir","erro" );
            exit;
        }

        //seta valores do processo
        if ($_REQUEST["inProcesso"]) {
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
            $obRCIMCondominio->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
            $obRCIMCondominio->obRProcesso->setExercicio      ( $arProcesso[1] );
        }

        //monta array de atributos dinamicos
        foreach ($arChave as $key => $value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode( "," , $value );
            }
            $obRCIMCondominio->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        //-------------------------------- LOTES
        $arLotesSessao = Sessao::read('lotes');

        if ( count($arLotesSessao ) > 0 ) {
            for ($inCount=0;$inCount< count($arLotesSessao);$inCount++) {
                $arLote = array();
                $arLote['inCodigoLote'] = $arLotesSessao[$inCount]['inCodLote'] ;
                $arLote['inNumLote'] = $arLotesSessao[$inCount]['inNumLote'];
                $obRCIMCondominio->addLote( $arLote );

            }
            $obErro = $obRCIMCondominio->incluirCondominio();
        } else {
            $obErro->setDescricao( 'É necessário incluir ao menos um lote.' );
        }

        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST['stProximaPagina'] == "condominio") {
                SistemaLegado::alertaAviso( $pgForm."?stAcao=incluir","Condomínio: ".$_REQUEST["stNomCondominio"],"incluir","aviso", Sessao::getId(), "../" );
            } elseif ($_REQUEST['stProximaPagina'] == "edificacao") {
                SistemaLegado::alertaAviso( $pgForm."?stAcao=incluir","Condomínio: ".$_REQUEST["stNomCondominio"],"incluir","aviso", Sessao::getId(), "../" );
                session_regenerate_id();
                Sessao::setId( "PHPSESSID=".session_id());
                $sessao->geraURLRandomica();
                Sessao::write('acao'  ,"751");
                Sessao::write('modulo', "12");
                alertaAvisoRedirect( $pgFormEdificacao."?stAcao=incluir&boVinculoEdificacao=Condomínio&inCodigoCondominio=".$obRCIMCondominio->getCodigoCondominio(), $obRCIMCondominio->getCodigoCondominio()." - ".$obRCIMCondominio->getNomCondominio(), "incluir","aviso", Sessao::getId(), "../", "183" );
            } elseif ($_REQUEST['stProximaPagina'] == "construcao") {
                SistemaLegado::alertaAviso( $pgForm."?stAcao=incluir","Condomínio: ".$_REQUEST["stNomCondominio"],"incluir","aviso", Sessao::getId(), "../" );
                session_regenerate_id();
                Sessao::setId( "PHPSESSID=".session_id());
                $sessao->geraURLRandomica();
                Sessao::write('acao', "757");
                Sessao::write('modulo',"12");
                alertaAvisoRedirect( $pgFormConstrucao."?stAcao=incluir&boVinculoConstrucao=condominio&inCodigoCondominio=".$obRCIMCondominio->getCodigoCondominio(),"Condomínio: ".$_REQUEST["stNomCondominio"],"incluir","aviso", Sessao::getId(), "../", "184" );
            } elseif ($_REQUEST['stProximaPagina'] == "") {
                SistemaLegado::alertaAviso( $pgForm."?stAcao=incluir","Condomínio: ".$_REQUEST["stNomCondominio"],"incluir","aviso", Sessao::getId(), "../" );
            }
        } else {
            SistemaLegado::exibeAviso( urlencode($obErro->getDescricao()),"n_incluir","erro" );
        }
    break;
    case "alterar":

        $obErro = new Erro;

        $obRCIMCondominio = new RCIMCondominio;

        $obRCIMCondominio->setCodigoCondominio      ( $_REQUEST["inCodigoCondominio"]       );
        $obRCIMCondominio->setCodigoTipo            ( $_REQUEST["inCodigoTipo"]             );
        $obRCIMCondominio->setNomCondominio         ( $_REQUEST["stNomCondominio"]          );
        $obRCIMCondominio->setAreaTotalComum        ( $_REQUEST["inAreaTotalComum"]         );
        $obRCIMCondominio->setTimestampCondominio   ( $_REQUEST["hdnTimestampCondominio"]   );

        $flValor = str_replace( ",", ".", str_replace( ".", "", $_REQUEST["inAreaTotalComum"] ) );
        if ($flValor < 0.01) {
            SistemaLegado::exibeAviso( "Campo 'Área Total Comum' deve ser maior que zero!","n_incluir","erro" );
            exit;
        }

        //seta valores do processo
        if ($_REQUEST["inProcesso"]) {
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
            $obRCIMCondominio->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
            $obRCIMCondominio->obRProcesso->setExercicio      ( $arProcesso[1] );
        }

        if ($_REQUEST["inNumCGM"]) {
            $obRCIMCondominio->obRCGM->setNumCGM  ( $_REQUEST["inNumCGM"]            );
        }

        //monta array de atributos dinamicos
        foreach ($arChave as $key => $value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode( "," , $value );
            }
            $obRCIMCondominio->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        //-------------------------------- LOTES
        $arLotesSessao = Sessao::read('lotes');
        if ( count ($arLotesSessao ) > 0 ) {
            for ($inCount=0;$inCount< count($arLotesSessao);$inCount++) {
                $arLote = array();
                $arLote['inCodigoLote'] = $arLotesSessao[$inCount]['inCodLote'] ;
                $arLote['inNumLote']    = $arLotesSessao[$inCount]['inNumLote'];
                $obRCIMCondominio->addLote( $arLote );
            }
        } else {
            $obErro->setDescricao( 'É necessário incluir ao menos um lote.' );
        }

        $obErro = $obRCIMCondominio->alterarCondominio();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList.$stLink,$obRCIMCondominio->getCodigoCondominio()." - ".$obRCIMCondominio->getNomCondominio(), "alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
        break;

    case "excluir":
        $obRCIMCondominio = new RCIMCondominio;
        $obRCIMCondominio->setCodigoCondominio( $_REQUEST["inCodigoCondominio"] );
        $obErro = $obRCIMCondominio->excluirCondominio();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir",$_REQUEST["stDescQuestao"], "excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;

    case "historico":
        $obRCIMCondominio = new RCIMCondominio;

        $obRCIMCondominio->setCodigoCondominio( $_REQUEST["inCodigoCondominio"]  );

        $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"]    );
        $obRCIMCondominio->obRProcesso->setCodigoProcesso( $arProcesso[0] );
        $obRCIMCondominio->obRProcesso->setExercicio     ( $arProcesso[1] );

        //monta array de atributos dinamicos
        foreach ($arChave as $key => $value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode( "," , $value );
            }
            $obRCIMCondominio->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        $obErro = $obRCIMCondominio->alterarCaracteristica();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList.$stLink,"Condomínio: ".$obRCIMCondominio->getCodigoCondominio()." - ".$_REQUEST["stNomCondominio"], "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
/****** REFORMA ****/

    case "reforma":
        $flAreaComum = str_replace("," , ".", $_REQUEST["inAreaTotalComum"] );
        if ($flAreaComum <= 0) {
            SistemaLegado::exibeAviso("Campo Área Total Comum deve ser maior que zero!","n_reforma","erro");
            exit;
        }

        $obRCIMCondominio = new RCIMCondominio;
        $obRCIMCondominio->setCodigoCondominio( $_REQUEST["inCodigoCondominio"]  );
        $obRCIMCondominio->setAreaTotalComum  ( $_REQUEST["inAreaTotalComum"]    );

        //seta valores do processo
        if ($_REQUEST["inProcesso"]) {
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
            $obRCIMCondominio->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
            $obRCIMCondominio->obRProcesso->setExercicio      ( $arProcesso[1] );
        }

        $obErro = $obRCIMCondominio->incluirReforma();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=reforma","Inclusão de Reforma concluida com sucesso! Condomínio: ".$obRCIMCondominio->getCodigoCondominio()." - ".$_REQUEST["stNomCondominio"],"cc","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_reforma","erro");
        }

}

?>