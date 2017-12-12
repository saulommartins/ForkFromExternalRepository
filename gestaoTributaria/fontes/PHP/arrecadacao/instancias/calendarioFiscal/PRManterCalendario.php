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
    * Página de processamento para Definição de Calendário Fiscal
    * Data de Criação   : 19/05/2005

    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: PRManterCalendario.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.03.03
*/

/*
$Log$
Revision 1.13  2006/10/25 19:31:25  hboaventura
bug #6968#

Revision 1.12  2006/09/15 11:50:32  fabio
corrigidas tags de caso de uso

Revision 1.11  2006/09/15 11:02:23  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalendarioFiscal.class.php");
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"           );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read( 'link' );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterCalendario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgListVencimento = "LSManterVencimentos.php";
include_once ( $pgJS );

function alertaAvisoRedirect($location="", $objeto="", $tipo="n_incluir", $chamada="erro", $sessao, $caminho="", $func="")
{
    ;
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                alertaAviso      ( "'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'","'.$caminho.'");
/*              mudaMenu         ( "'.$func.'"     );*/
                mudaTelaPrincipal( "'.$location.'" );
           </script>';
}

$obErro = new Erro;
$obRARRCalendarioFiscal = new RARRCalendarioFiscal;
//$obRARRGrupoCredito     = new RARRGrupoCredito;

$obAtributos = new MontaAtributos;
$obAtributos->setName( "Atributo_" );
$obAtributos->recuperaVetor( $arChave );

switch ($stAcao) {
    case "incluir":
        $arDadosGrupo = explode("/", $_REQUEST['inCodGrupo'] );
        $obRARRCalendarioFiscal->setCodigoGrupo( $arDadosGrupo[0] );
        $obRARRCalendarioFiscal->setAnoExercicio ( $arDadosGrupo[1] );

        $obRARRCalendarioFiscal->setValorMinimo( $_REQUEST['inMinLancamento'] );
        $obRARRCalendarioFiscal->setValorMinimoParcela( $_REQUEST['inMinParcela'] );
        $obRARRCalendarioFiscal->setValorMinimoIntegral( $_REQUEST['inMinIntegral'] );
        $arGrupos = Sessao::read( 'grupos' );
        if ( count( $arGrupos ) != 0 ) {
            foreach ($arGrupos as $inChave => $arGrupo) {
                $obRARRCalendarioFiscal->addCalendarioGrupoVencimento();
                $obRARRCalendarioFiscal->roUltimoGrupoVencimento->setDescricao( $arGrupo['stDescricao'] );
                $obRARRCalendarioFiscal->roUltimoGrupoVencimento->setCodigoVencimento( $arGrupo['inCodigo'] );
                $obRARRCalendarioFiscal->roUltimoGrupoVencimento->setVencimentoValorIntegral( $arGrupo['dtDataVencimento'] );

                $obRARRCalendarioFiscal->roUltimoGrupoVencimento->setLimiteInicial( $arGrupo['inLimiteInicial'] );
                $obRARRCalendarioFiscal->roUltimoGrupoVencimento->setLimiteFinal( $arGrupo['inLimiteFinal'] );
                if ($arGrupo["stUtilizarCotaUnica"] == "Sim")
                    $obRARRCalendarioFiscal->roUltimoGrupoVencimento->setUtilizarCotaUnica( true );
                else
                    $obRARRCalendarioFiscal->roUltimoGrupoVencimento->setUtilizarCotaUnica( false );
            }
        } else {
            $obErro->setDescricao( "É necessário a inclusão de pelo menos um grupo." );
        }

        // dados de atributos dinamicos
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRARRCalendarioFiscal->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRARRCalendarioFiscal->definirCalendario();
        }
        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST["boSegueVencimentos"] == 1) {
                $stAviso = "Calendário Fiscal definido com sucesso! (grupo de crédito:".$obRARRCalendarioFiscal->getCodigoGrupo().")";

                alertaAvisoRedirect($pgListVencimento."?stAcao=incluir&inCodGrupo=".$obRARRCalendarioFiscal->getCodigoGrupo(), $stAviso, "definir", "aviso", Sessao::getId(), "./", "223" );
            } else {
                sistemaLegado::alertaAviso($pgForm,"Calendário incluído: ".$obRARRCalendarioFiscal->getCodigoGrupo(), "incluir", "aviso", Sessao::getId(), "../");
            }
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), "n_incluir", "erro");
        }
        break;

    case "alterar":
        $obRARRCalendarioFiscal->setCodigoGrupo( $_REQUEST['inCodigoCredito'] );
        $obRARRCalendarioFiscal->setAnoExercicio ( $_REQUEST['stExercicio'] );
        $obRARRCalendarioFiscal->setValorMinimo( $_REQUEST['inMinLancamento'] );
        $obRARRCalendarioFiscal->setValorMinimoParcela( $_REQUEST['inMinParcela'] );
        $obRARRCalendarioFiscal->setValorMinimoIntegral( $_REQUEST['inMinIntegral']);

        $arGrupos = Sessao::read( 'grupos' );
        if ( count( $arGrupos ) != 0 ) {
            foreach ($arGrupos as $inChave => $arGrupo) {
                $obRARRCalendarioFiscal->addCalendarioGrupoVencimento();
                $obRARRCalendarioFiscal->roUltimoGrupoVencimento->roRARRCalendarioFiscal->setCodigoGrupo( $_REQUEST['inCodigoCredito'] );
                $obRARRCalendarioFiscal->roUltimoGrupoVencimento->setDescricao( $arGrupo['stDescricao'] );
                $obRARRCalendarioFiscal->roUltimoGrupoVencimento->setCodigoVencimento( $arGrupo['inCodigo'] );
                $obRARRCalendarioFiscal->roUltimoGrupoVencimento->setVencimentoValorIntegral( $arGrupo['dtDataVencimento'] );
                $obRARRCalendarioFiscal->roUltimoGrupoVencimento->setLimiteInicial ( $arGrupo['inLimiteInicial'] );
                $obRARRCalendarioFiscal->roUltimoGrupoVencimento->setLimiteFinal ( $arGrupo['inLimiteFinal'] );
                if ($arGrupo["stUtilizarCotaUnica"] == "Sim")
                    $obRARRCalendarioFiscal->roUltimoGrupoVencimento->setUtilizarCotaUnica( true );
                else
                    $obRARRCalendarioFiscal->roUltimoGrupoVencimento->setUtilizarCotaUnica( false );
//            $obErro = $obRARRCalendarioFiscal->alterarCalendario();
            }
        } else {
            $obErro->setDescricao( "É necessário a inclusão de pelo menos um grupo." );
        }

        // dados de atributos dinamicos
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRARRCalendarioFiscal->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        if ( !$obErro->ocorreu() ) {

            $obErro = $obRARRCalendarioFiscal->alterarCalendario();
        }

        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST["boSegueVencimentos"] == 1) {
                $stAviso = "Grupo de Créditos:".$_REQUEST["inCodigoCredito"];
                alertaAvisoRedirect($pgListVencimento."?stAcao=incluir&inCodGrupo=".$_REQUEST["inCodigoCredito"],$stAviso,"alterar","aviso",Sessao::getId(),"./","223" );
            } else {
                sistemaLegado::alertaAviso($pgList,"Calendário alterado: ".$_REQUEST['inCodigoCredito'],"alterar","aviso", Sessao::getId(), "../");
            }
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
        break;

    case "excluir":
        $obRARRCalendarioFiscal->setCodigoGrupo ( $_REQUEST['inCodigoCredito'] );
        $obRARRCalendarioFiscal->setAnoExercicio ( $_REQUEST["stExercicio"] );
        $obRARRCalendarioFiscal->addCalendarioGrupoVencimento();

        $obErro = $obRARRCalendarioFiscal->excluirCalendario();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Calendário excluído: ".$_REQUEST["inCodigoCredito"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;

}
