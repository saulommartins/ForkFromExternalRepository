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


    * Filtro para Relatorio de Domicilio Fiscal
    * Data de Criação   : 09/09/2014    
    * @author Desenvolvedor: Evandro Melos
    * @package URBEM    

    * $Id: OCRelatorioDomicilioFiscal.php 59807 2014-09-12 12:31:14Z evandro $
    
*Atividade Gestão Tributária | Cadastro Econômico | Inscrição Econômica | Definir Atividades
            usa o filtro = inscrição economica = 98
seleciona na lista
e na outra tela 04:37:52 PM
vai ter o campo atividade

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php"         );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php"          );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"     );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"     );
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaLocal.class.php"     );

$stPrograma      = "RelatorioDomicilioFiscal";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$stCtrl = $request->get('stCtrl');

$obMontaAtividade = new MontaAtividade;
$obMontaAtividade->setCadastroAtividade( false );

switch ($stCtrl) {
    case "preencheProxCombo":
        $inCodVigencia = $_REQUEST["inCodigoVigencia"];
        if (!$inCodVigencia) {
            $obRCEMAtivi = new RCEMAtividade;
            $obRCEMAtivi->recuperaVigenciaAtual( $rsVigencia );
            unset( $obRCEMAtivi );
            $inCodVigencia = $rsVigencia->getCampo("cod_vigencia");
        }

        if ($_REQUEST["stMascara"] == "Z.99.9.9-9.99") {
            $stNomeComboCnae = "inCodAtividade_".( $_REQUEST["inPosicao"] - 1);
            $stChaveLocal = $_REQUEST[$stNomeComboCnae];
            $inPosicao = $_REQUEST["inPosicao"];

            if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
                $stNomeComboCnae = "inCodAtividade_".( $_REQUEST["inPosicao"] - 2);
                $stChaveLocal = $_REQUEST[$stNomeComboCnae];
                $inPosicao = $_REQUEST["inPosicao"] - 1;
            }

            $arChaveLocal = explode("§" , $stChaveLocal );

            $obMontaAtividade->setCodigoVigencia    ( $inCodVigencia );
            $obMontaAtividade->setCodigoNivel       ( $arChaveLocal[0] );
            $obMontaAtividade->setCodigoAtividade   ( $arChaveLocal[1] );
            $obMontaAtividade->boPopUp = false;
            if ($arChaveLocal[0] == 1) {
                $obMontaAtividade->setValorReduzido   ( $arChaveLocal[2] );
            }else
                if ($arChaveLocal[0] == 2) {
                    $obMontaAtividade->setValorReduzido ( substr( $arChaveLocal[3], 0, 4 ) );
                }else
                    if ($arChaveLocal[0] == 3) {
                        $obMontaAtividade->setValorReduzido ( substr( $arChaveLocal[3], 0, 6 ) );
                    }else
                        if ($arChaveLocal[0] == 4) {
                            $obMontaAtividade->setValorReduzido ( substr( $arChaveLocal[3], 0, 10 ) );
                        } else {
                            $obMontaAtividade->setValorReduzido ( $arChaveLocal[3] );
                        }

            $obMontaAtividade->preencheProxComboCNAE ( $inPosicao , $_REQUEST["inNumNiveis"] );
        } else {
            $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 1);
            $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
            $inPosicao = $_REQUEST["inPosicao"];
            if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
                $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 2);
                $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
                $inPosicao = $_REQUEST["inPosicao"] - 1;
            }
            $arChaveLocal = explode("§" , $stChaveLocal );
            $obMontaAtividade->setCodigoVigencia    ( $inCodAtividade );
            $obMontaAtividade->setCodigoNivel       ( $arChaveLocal[0] );
            $obMontaAtividade->setCodigoAtividade   ( $arChaveLocal[1] );
            $obMontaAtividade->setValorReduzido     ( $arChaveLocal[3] );
            $obMontaAtividade->preencheProxCombo    ( $inPosicao , $_REQUEST["inNumNiveis"] );
        }
        break;

    case "preencheCombosAtividade":
        $inCodVigencia = $_REQUEST["inCodigoVigencia"];
        if (!$inCodVigencia) {
            $obRCEMAtivi = new RCEMAtividade;
            $obRCEMAtivi->recuperaVigenciaAtual( $rsVigencia );
            unset( $obRCEMAtivi );
            $inCodVigencia = $rsVigencia->getCampo("cod_vigencia");
        }

        if ($_REQUEST["stMascara"] == "Z.99.9.9-9.99") {
            $obMontaAtividade->setCodigoVigencia( $inCodVigencia   );
            $obMontaAtividade->setValorReduzido ( $_REQUEST["stChaveAtividade"] );
            $obMontaAtividade->preencheCombos2();
        } else {
            $obMontaAtividade->setCodigoVigencia        ( $inCodVigencia  );
            $obMontaAtividade->setCodigoNivel           ( $_REQUEST["inCodigoNivel"]      );
            $obMontaAtividade->setValorReduzido         ( $_REQUEST["stChaveAtividade"] );
            $obMontaAtividade->setMascara               ( $_REQUEST['stMascara'] );
            $obMontaAtividade->preencheCombosAtividade();
        }
    break;

    case "buscaLogradouroFiltro":
        $obROrganogramaLocal     = new ROrganogramaLocal;
        $rsLogradouro  = new RecordSet;
        if ($_REQUEST["inNumLogradouro"]) {
            $obROrganogramaLocal->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] ) ;
            $obROrganogramaLocal->listarLogradouros( $rsLogradouro );
        }

        if ( $rsLogradouro->eof() ) {
            $js .= 'f.inNumLogradouro.value = "";';
            $js .= 'f.inNumLogradouro.focus();';
            $js .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_REQUEST["inNumLogradouro"].")','form','erro','".Sessao::getId()."');";
        } else {
            $stNomeLogradouro = $rsLogradouro->getCampo ("tipo_nome");
            $js .= "f.stLogradouro.value = '$stNomeLogradouro';";
            $js .= 'd.getElementById("stLogradouro").innerHTML = "'.$stNomeLogradouro.'";';
        }
        SistemaLegado::executaFrameOculto($js);
    break;
}

?>