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
* Página de processamento do vale transporte
* Data de Criação: 11/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30922 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioValeTransporte.class.php" );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$arSessaoLink = Sessao::read('link');
$stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterValeTransporte";
$pgFilt    = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList    = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm    = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc    = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul    = "OC".$stPrograma.".php?stAcao=$stAcao";

$obRBeneficioValeTransporte  = new RBeneficioValeTransporte;

switch ($stAcao) {
    case "incluir":
        $obRBeneficioValeTransporte->obRBeneficioFornecedorValeTransporte->setNumCGM( $_POST['inNumCGM']                );
        $obRBeneficioValeTransporte->setCusto                                       ( $_POST['flCusto']                 );
        $obRBeneficioValeTransporte->setInicioVigencia                              ( $_POST['dtVigencia']              );
        $obRBeneficioValeTransporte->obRBeneficioItinerario->setCodLinhaDestino     ( $_POST['inCodLinhaDestino']       );
        $obRBeneficioValeTransporte->obRBeneficioItinerario->setCodLinhaOrigem      ( $_POST['inCodLinhaOrigem']        );
        $obRBeneficioValeTransporte->obRBeneficioItinerario->setCodMunicipioDestino ( $_POST['inCodMunicipioDestino']   );
        $obRBeneficioValeTransporte->obRBeneficioItinerario->setCodMunicipioOrigem  ( $_POST['inCodMunicipioOrigem']    );
        $obRBeneficioValeTransporte->obRBeneficioItinerario->setCodUFDestino        ( $_POST['inCodUFDestino']          );
        $obRBeneficioValeTransporte->obRBeneficioItinerario->setCodUFOrigem         ( $_POST['inCodUFOrigem']           );
        $obErro = $obRBeneficioValeTransporte->incluirValeTransporte();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm,"Fornecedor: ".$_POST['inNumCGM'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $obRBeneficioValeTransporte->setCodValeTransporte                           ( $_POST['inCodValeTransporte']     );
        $obRBeneficioValeTransporte->setCusto                                       ( $_POST['flCusto']                 );
        $obRBeneficioValeTransporte->setInicioVigencia                              ( $_POST['dtVigencia']              );
        $rsCusto = new Recordset;
        $obRBeneficioValeTransporte->listarCusto( $rsCusto );
        $boValido = true;
        while ( !$rsCusto->eof() ) {
            list( $dia1,$mes1,$ano1 ) = explode( '/', $rsCusto->getCampo( 'inicio_vigencia' ) );
            list( $dia2,$mes2,$ano2 ) = explode( '/', $obRBeneficioValeTransporte->getInicioVigencia() );
            if ("$ano1$mes1$dia1" >= "$ano2$mes2$dia2") {
                $dtVigenciaAntiga = $rsCusto->getCampo( 'inicio_vigencia' );
                $boValido = false;
                break;
            }
            $rsCusto->proximo();
        }
        if ($boValido) {
            $obErro = $obRBeneficioValeTransporte->alterarValeTransporte();
            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgList,"Fornecedor: ".$_POST['inNumCGM'],"alterar","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            }
        } else {
            $stMensagem = "A vigência do custo informada tem que ser maior que ".$dtVigenciaAntiga;
            sistemaLegado::exibeAviso(urlencode($stMensagem),"n_alterar","erro");
        }
    break;

    case "excluir":
        $obRBeneficioValeTransporte->setCodValeTransporte( $_REQUEST['inCodValeTransporte']   );
        $obErro = $obRBeneficioValeTransporte->excluirValeTransporte();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList."?".$stFiltro,"Fornecedor: ".$_REQUEST['inNumCGM'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;
}
?>
