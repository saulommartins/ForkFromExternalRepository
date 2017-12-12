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
    * Processamento de Inicialização de Vale-Tranporte Servidor
    * Data de Criação: 03/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Eduardo Antunez

    * @ignore

    $Revision: 30931 $
    $Name$
    $Author: souzadl $
    $Date: 2006-09-26 07:00:14 -0300 (Ter, 26 Set 2006) $

    * Casos de uso: uc-04.06.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioContratoServidorConcessaoValeTransporte.class.php"       );

$stAcao = $request->get('stAcao');
$arSessaoLink = Sessao::read('link');
$stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterInicializacaoValeTransporte";
$pgFilt    = "FL".$stPrograma.".php?".Sessao::getId().$stLink;
$pgList    = "LS".$stPrograma.".php?".Sessao::getId().$stLink;
$pgForm    = "FM".$stPrograma.".php?".Sessao::getId().$stLink;
$pgProc    = "PR".$stPrograma.".php?".Sessao::getId().$stLink;
$pgOcul    = "OC".$stPrograma.".php?".Sessao::getId().$stLink;

$obRBeneficioContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;
$obErro = new Erro;

switch ($stAcao) {
    case "incluir":
        switch ($_POST['stInicializacao']) {
            case "contrato":
            case "cgm":
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->setRegistro($_POST['inContrato']);
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->consultarContrato();
                $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes($_POST['inMes']);
                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio($_POST['inAno']);
                $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->inicializarConcessaoValeTransporte();
            break;
            case "grupo":
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setCodGrupo($_POST['inCodGrupo']);
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes($_POST['inMes']);
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio($_POST['inAno']);
                $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->inicializarConcessaoValeTransporte();
            break;
            case "geral":
                $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes($_POST['inMes']);
                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio($_POST['inAno']);
                $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->inicializarConcessaoValeTransporteGeral();
            break;
        }
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"","incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "excluir":
        //Contratos
        $arContratos = Sessao::read('arContratosInicializados');
        $arContratos = (is_array($arContratos))?$arContratos:array();

        $inI = 0;
        $inCodContrato = 0;
        while ( $inI < count($arContratos) ) {
            if ($arContratos[$inI]['contrato'] != $inCodContrato) {
                $inCodContrato = $arContratos[$inI]['contrato'];
                $obRBeneficioContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->setRegistro($arContratos[$inI]['contrato']);
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->consultarContrato();
            }
            $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
            $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodConcessao($arContratos[$inI]['concessao']);
            $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes      ($arContratos[$inI]['mes']      );
            $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio   ($arContratos[$inI]['ano']      );
            if (!isset($arContratos[$inI+1]) || $arContratos[$inI+1]['contrato'] != $inCodContrato) {
                $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->excluirAssociacao();
            }
            $inI++;
        }
        //Grupos
        $arGrupos = Sessao::read('arGruposInicializados');
        $arGrupos = (is_array($arGrupos))?$arGrupos:array();
        $inI = 0;
        $inCodGrupo = 0;
        while ( $inI < count($arGrupos) ) {
            if ($arGrupos[$inI]['cod_grupo'] != $inCodGrupo) {
                $inCodGrupo = $arGrupos[$inI]['cod_grupo'];
                $obRBeneficioContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setCodGrupo($arGrupos[$inI]['cod_grupo']);
            }
            $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
            $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodConcessao($arGrupos[$inI]['concessao']);
            $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes      ($arGrupos[$inI]['mes']      );
            $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio   ($arGrupos[$inI]['ano']      );
            if (!isset($arGrupos[$inI+1]) || $arGrupos[$inI+1]['cod_grupo'] != $inCodGrupo) {
                $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->excluirAssociacao();
            }
            $inI++;
        }
        if ( !$obErro->ocorreu() ) {
            if (Sessao::read('stInicializacao') == 'geral') {
                $stMensagem = "Geral";
            } elseif (Sessao::read('stInicializacao') == 'contrato' || Sessao::read('stInicializacao') == 'cgm' ) {
                $stMensagem = "Matrícula ".$inCodContrato;
            } elseif (Sessao::read('stInicializacao') == 'grupo') {
                $stMensagem = "Grupo ".$inCodGrupo;
            }
            sistemaLegado::alertaAviso($pgForm,$stMensagem,"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgForm,urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;
}
?>
