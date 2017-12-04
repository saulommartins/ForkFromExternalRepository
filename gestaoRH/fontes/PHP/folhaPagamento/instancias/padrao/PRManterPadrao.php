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
    * Página de Formulario de Inclusao/Alteracao/Exclusao de Pessoal-Padrao
    * Data de Criação   : 03/12/2004

    * @author Analista: ???
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-04 12:26:42 -0300 (Qua, 04 Jul 2007) $

    * Casos de uso :uc-04.05.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php"                                       );

$stAcao = $request->get('stAcao');
$link = Sessao::read("link");
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterPadrao";
$pgFilt      = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList      = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc      = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul      = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJS        = "JS".$stPrograma.".js";

$obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;

switch ($stAcao) {
    case "incluir":
        $flHorasMensais  = str_replace ( ",", ".", $_REQUEST[ "stHorasMensais"  ] );
        $flHorasSemanais = str_replace ( ",", ".", $_REQUEST[ "stHorasSemanais" ] );

        $obRFolhaPagamentoPadrao->setDescricaoPadrao    ( $_REQUEST[ "stDescricao"     ] );
        $obRFolhaPagamentoPadrao->setHorasMensais       ( $flHorasMensais                );
        $obRFolhaPagamentoPadrao->setHorasSemanais      ( $flHorasSemanais               );
        $obRFolhaPagamentoPadrao->setValor              ( $_REQUEST[ "stValorPadrao"         ] );
        $obRFolhaPagamentoPadrao->obRNorma->setCodNorma ( $_REQUEST[ "inCodNorma"      ] );
        $obRFolhaPagamentoPadrao->setVigencia           ( $_REQUEST['dtVigencia']        );

        $arProgressoes = Sessao::read("Progressao");
        for ($inCount = 0; $inCount < count ($arProgressoes); $inCount++) {
            $obRFolhaPagamentoPadrao->addNivelPadrao ();
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setDescricaoNivelPadrao ( $arProgressoes[$inCount]["descricao" ] );
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setValor                ( $arProgressoes[$inCount]["valor"     ] );
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setPercentual           ( $arProgressoes[$inCount]["percentual"] );
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setQtdMeses             ( $arProgressoes[$inCount]["qtdmeses"  ] );
        }

        $obErro = $obRFolhaPagamentoPadrao->salvarPadrao();
        if ( !$obErro->ocorreu() ) {
            $stOrigem = Sessao::read("stOrigem"); // Váriavel setada na popup do cadastro de cargos

            if (trim($stOrigem)=="CARGO") {
                $js  = " var nextPos = window.parent.window.opener.document.getElementById('inCodPadrao').options.length; \n";
                $js .= " window.parent.window.opener.document.getElementById('inCodPadrao').options[nextPos] = new Option('".addslashes($_REQUEST[ "stDescricao"])." - ".addslashes($_REQUEST[ "stValorPadrao"])."', '".$obRFolhaPagamentoPadrao->getCodPadrao()."'); \n";
                $js .= " window.parent.window.opener.document.getElementById('inCodPadrao').options[nextPos].selected = true; \n";
                $js .= " window.parent.window.opener.document.getElementById('inCodPadraoTxt').value = ".$obRFolhaPagamentoPadrao->getCodPadrao()."; \n";
                $js .= " window.parent.window.close(); \n";

                SistemaLegado::executaFrameOculto($js);
            } else {
                SistemaLegado::alertaAviso($pgForm.$stLink,"Descrição Padrão: ".$_REQUEST['stDescricao'],"incluir","aviso", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $flHorasMensais  = str_replace ( ",", ".", $_REQUEST[ "hdnHorasMensais"  ] );
        $flHorasSemanais = str_replace ( ",", ".", $_REQUEST[ "hdnHorasSemanais" ] );

        $obRFolhaPagamentoPadrao->setCodPadrao          ( $_REQUEST[ "hdnCodPadrao"      ] );
        $obRFolhaPagamentoPadrao->setDescricaoPadrao    ( $_REQUEST[ "hdnDescricaoPadrao"] );
        $obRFolhaPagamentoPadrao->setHorasMensais       ( $flHorasMensais                  );
        $obRFolhaPagamentoPadrao->setHorasSemanais      ( $flHorasSemanais                 );
        $obRFolhaPagamentoPadrao->setValor              ( $_REQUEST[ "stValorPadrao"           ] );
        $obRFolhaPagamentoPadrao->obRNorma->setCodNorma ( $_REQUEST[ "inCodNorma"        ] );
        $obRFolhaPagamentoPadrao->setVigencia           ( $_REQUEST['dtVigencia'         ] );

        $arProgressoes = Sessao::read("Progressao");
        for ($inCount = 0; $inCount < count ($arProgressoes); $inCount++) {
            $obRFolhaPagamentoPadrao->addNivelPadrao ();
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setCodNivelPadrao       ( $arProgressoes[$inCount]["inCodNivelPadrao"] );
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setDescricaoNivelPadrao ( $arProgressoes[$inCount]["descricao" ] );
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setValor                ( $arProgressoes[$inCount]["valor"     ] );
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setPercentual           ( $arProgressoes[$inCount]["percentual"] );
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setQtdMeses             ( $arProgressoes[$inCount]["qtdmeses"  ] );
        }

        $obErro = $obRFolhaPagamentoPadrao->salvarPadrao();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Descrição Padrão: ".$_REQUEST[ "hdnDescricaoPadrao"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir":
        //$flHorasMensais  = str_replace ( ",", ".", $_REQUEST[ "stHorasMensais"  ] );
        //$flHorasSemanais = str_replace ( ",", ".", $_REQUEST[ "stHorasSemanais" ] );

        $obRFolhaPagamentoPadrao->setCodPadrao          ( $_REQUEST[ "inCodPadrao"     ] );
        //$obRFolhaPagamentoPadrao->setDescricaoPadrao    ( $_REQUEST[ "stDescricao"     ] );
        //$obRFolhaPagamentoPadrao->setHorasMensais       ( $flHorasMensais                );
        //$obRFolhaPagamentoPadrao->setHorasSemanais      ( $flHorasSemanais               );
        //$obRFolhaPagamentoPadrao->setValor              ( $_REQUEST[ "stValor"         ] );
        //$obRFolhaPagamentoPadrao->obRNorma->setCodNorma ( $_REQUEST[ "inCodNorma"      ] );

        $obErro = $obRFolhaPagamentoPadrao->excluirPadrao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Descrição Padrão: ".$_REQUEST['stDescricao'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;
}
?>
