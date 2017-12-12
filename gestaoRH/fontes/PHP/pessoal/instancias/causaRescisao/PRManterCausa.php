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
    * Página de Processamento de Pessoal Causa Rescisao
    * Data de Criação   : 05/05/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 31465 $
    $Name$
    $Author: souzadl $
    $Date: 2007-12-13 10:56:56 -0200 (Qui, 13 Dez 2007) $

    * Casos de uso :uc-04.04.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaRescisao.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCasoCausa.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCasoCausaSubDivisao.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalSefip.class.php");

$arLink = Sessao::read('link');
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterCausa";
$pgFilt = "FL".$stPrograma.".php?".$stLink;
$pgList = "LS".$stPrograma.".php?".$stLink;
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obTPessoalCausaRescisao       = new TPessoalCausaRescisao();
$obTPessoalCasoCausa           = new TPessoalCasoCausa();
$obTPessoalCasoCausaSubDivisao = new TPessoalCasoCausaSubDivisao();
$obTPessoalSefip               = new TPessoalSefip();

function procurarCaso($arCasos,$inCodCasoCausa)
{
    foreach ($arCasos as $arCaso) {
        if ($arCaso['inCodCasoCausa'] == $inCodCasoCausa) {
            $boRetorno =  true;
            break;
        } else {
            $boRetorno = false;
        }
    }

    return $boRetorno;
}

switch ($stAcao) {
    case "incluir":
        
        $obErro = new Erro();
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        
        $stFiltro = " WHERE num_causa = '".$_POST['inNumCausa']."'";
        $obTPessoalCausaRescisao->recuperaTodos($rsNumeroCausaRescisao, $stFiltro);
                
        if ( $rsNumeroCausaRescisao->getNumLinhas() >= 1 ) {
            $obErro->setDescricao( "Código ".$_POST['inNumCausa']." já cadastrado. Informe um código diferente." );
        }        
        
        if ( $obErro->ocorreu() ) {
            
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            
        } else {
            
            $stFiltro = " WHERE num_sefip = '".$_POST['inCodTxtSefipSaida']."'";
            $obTPessoalSefip->recuperaTodos($rsSefip,$stFiltro);
            
            $obTPessoalCausaRescisao->setDado("cod_sefip_saida",$rsSefip->getCampo("cod_sefip"));
            $obTPessoalCausaRescisao->setDado("num_causa",$_POST['inNumCausa']);
            $obTPessoalCausaRescisao->setDado("descricao",$_POST['stDescricaoCausa']);
            $obTPessoalCausaRescisao->setDado("cod_caged",$_POST['inCodCaged']);
            $obTPessoalCausaRescisao->setDado("cod_causa_afastamento", $_POST['inNumCausaMTE']);
            $obTPessoalCausaRescisao->inclusao($obTransacao);
            
            $inCodCausaRescisao = $obTPessoalCausaRescisao->getDado("cod_causa_rescisao");
    
            if ($_POST["inNumCaged"] != "") {
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCaged.class.php");
                $obTPessoalCaged = new TPessoalCaged();
                $stFiltro = " WHERE num_caged = ".$_POST["inNumCaged"];
                $obTPessoalCaged->recuperaTodos($rsCaged,$stFiltro);
    
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaRescisaoCaged.class.php");
                $obTPessoalCausaRescisaoCaged = new TPessoalCausaRescisaoCaged();
                $obTPessoalCausaRescisaoCaged->setDado("cod_causa_rescisao",$inCodCausaRescisao);
                $obTPessoalCausaRescisaoCaged->setDado("cod_caged",$rsCaged->getCampo("cod_caged"));
                $obTPessoalCausaRescisaoCaged->inclusao($obTransacao);
            }
    
            foreach (Sessao::read('arCasosCausa') as $arCasoCausa) {
                
                if(!empty($inCodCasoCausa))
                    $obTPessoalCasoCausa->setDado("cod_caso_causa"          ,$inCodCasoCausa);
                    
                $obTPessoalCasoCausa->setDado("cod_periodo"                 ,$arCasoCausa['inCodPeriodo']);
                $obTPessoalCasoCausa->setDado("cod_causa_rescisao"          ,$inCodCausaRescisao);
                $obTPessoalCasoCausa->setDado("descricao"                   ,$arCasoCausa['stDescricaoCaso']);
                $obTPessoalCasoCausa->setDado("paga_aviso_previo"           ,$arCasoCausa['boPagaAvisoPrevio']);
                $obTPessoalCasoCausa->setDado("paga_ferias_vencida"         ,$arCasoCausa['boFeriasVencidas']);
                $obTPessoalCasoCausa->setDado("cod_saque_fgts"              ,$arCasoCausa['inCodSaqueFGTS']);
                $obTPessoalCasoCausa->setDado("perc_cont_social"            ,$arCasoCausa['flContribuicao']);
                $obTPessoalCasoCausa->setDado("multa_fgts"                  ,$arCasoCausa['flMultaFGTS']);
                $obTPessoalCasoCausa->setDado("inc_fgts_ferias"             ,$arCasoCausa['boFeriasFGTS']);
                $obTPessoalCasoCausa->setDado("inc_fgts_aviso_previo"       ,$arCasoCausa['boAvisoPrevioFGTS']);
                $obTPessoalCasoCausa->setDado("inc_fgts_13"                 ,$arCasoCausa['bo13FGTS']);
                $obTPessoalCasoCausa->setDado("inc_irrf_ferias"             ,$arCasoCausa['boFeriasIRRF']);
                $obTPessoalCasoCausa->setDado("inc_irrf_aviso_previo"       ,$arCasoCausa['boAvisoPrevioIRRF']);
                $obTPessoalCasoCausa->setDado("inc_irrf_13"                 ,$arCasoCausa['bo13IRRF']);
                $obTPessoalCasoCausa->setDado("inc_prev_ferias"             ,$arCasoCausa['boFeriasPrevidencia']);
                $obTPessoalCasoCausa->setDado("inc_prev_aviso_previo"       ,$arCasoCausa['boAvisoPrevioPrevidencia']);
                $obTPessoalCasoCausa->setDado("inc_prev_13"                 ,$arCasoCausa['bo13Previdencia']);
                $obTPessoalCasoCausa->setDado("paga_ferias_proporcional"    ,$arCasoCausa['boFeriasProporcionais']);
                $obTPessoalCasoCausa->setDado("inden_art_479"               ,$arCasoCausa['boArtigo479']);
                $obTPessoalCasoCausa->inclusao($obTransacao);
                
                $inCodCasoCausa = $obTPessoalCasoCausa->getDado("cod_caso_causa");
                
                foreach ($arCasoCausa['inCodRegimeSelecionados'] as $inCodSubDivisao) {
                    $obTPessoalCasoCausaSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisao);
                    $obTPessoalCasoCausaSubDivisao->setDado("cod_caso_causa",$inCodCasoCausa);
                    $obTPessoalCasoCausaSubDivisao->inclusao($obTransacao);
                }
                
                $inCodCasoCausa++;
            }
            $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro);
            sistemaLegado::alertaAviso($pgForm,"Incluir Causa realizada com sucesso." ,"incluir","aviso", Sessao::getId(), "../");
        }
        
    break;

    case "alterar":
        Sessao::setTrataExcecao(true);

        $stFiltro = " WHERE num_sefip = '".$_POST['inCodTxtSefipSaida']."'";
        $obTPessoalSefip->recuperaTodos($rsSefip,$stFiltro);
        $obTPessoalCausaRescisao->setDado("cod_causa_rescisao",$_POST['inCodCausaRescisao']);
        $obTPessoalCausaRescisao->consultar();
        $obTPessoalCausaRescisao->setDado("cod_sefip_saida",$rsSefip->getCampo("cod_sefip"));
        $obTPessoalCausaRescisao->setDado("cod_causa_afastamento",$_POST['inNumCausaMTETxt']);
        $obTPessoalCausaRescisao->alteracao();

        if ($_POST["inNumCaged"] != "") {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCaged.class.php");
            $obTPessoalCaged = new TPessoalCaged();
            $stFiltro = " WHERE num_caged = ".$_POST["inNumCaged"];
            $obTPessoalCaged->recuperaTodos($rsCaged,$stFiltro);

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaRescisaoCaged.class.php");
            $obTPessoalCausaRescisaoCaged = new TPessoalCausaRescisaoCaged();
            $obTPessoalCausaRescisaoCaged->setDado("cod_causa_rescisao",$_POST['inCodCausaRescisao']);
            $obTPessoalCausaRescisaoCaged->exclusao();
            $obTPessoalCausaRescisaoCaged->setDado("cod_caged",$rsCaged->getCampo("cod_caged"));
            $obTPessoalCausaRescisaoCaged->inclusao();
        }

        $stFiltro = " WHERE cod_causa_rescisao = ".$_POST['inCodCausaRescisao'];
        $obTPessoalCasoCausa->recuperaTodos($rsCasoCausa,$stFiltro);
        while (!$rsCasoCausa->eof()) {
            if ( !procurarCaso(Sessao::read('arCasosCausa'),$rsCasoCausa->getCampo("cod_caso_causa")) ) {
                $stFiltro = " WHERE cod_caso_causa = ".$rsCasoCausa->getCampo("cod_caso_causa");
                $obTPessoalCasoCausaSubDivisao->recuperaTodos($rsCasoCausaSubDivisao, $stFiltro);
                if ($rsCasoCausaSubDivisao->getNumLinhas()!=-1) {
                    $obTPessoalCasoCausaSubDivisao->setDado("cod_caso_causa",$rsCasoCausa->getCampo("cod_caso_causa"));
                    $obTPessoalCasoCausaSubDivisao->setDado("cod_sub_divisao","");
                    $obTPessoalCasoCausaSubDivisao->exclusao();
                }
                $obTPessoalCasoCausa->setDado("cod_caso_causa",$rsCasoCausa->getCampo("cod_caso_causa"));
                $obTPessoalCasoCausa->exclusao();
            }
            $rsCasoCausa->proximo();
        }
        if (!Sessao::getExcecao()->ocorreu()) {
            foreach (Sessao::read('arCasosCausa') as $arCasoCausa) {
                $obTPessoalCasoCausa->setDado("cod_periodo"                 ,$arCasoCausa['inCodPeriodo']);
                $obTPessoalCasoCausa->setDado("cod_causa_rescisao"          ,$_POST['inCodCausaRescisao']);
                $obTPessoalCasoCausa->setDado("descricao"                   ,$arCasoCausa['stDescricaoCaso']);
                $obTPessoalCasoCausa->setDado("paga_aviso_previo"           ,$arCasoCausa['boPagaAvisoPrevio']);
                $obTPessoalCasoCausa->setDado("paga_ferias_vencida"         ,$arCasoCausa['boFeriasVencidas']);
                $obTPessoalCasoCausa->setDado("cod_saque_fgts"              ,$arCasoCausa['inCodSaqueFGTS']);
                $obTPessoalCasoCausa->setDado("perc_cont_social"            ,$arCasoCausa['flContribuicao']);
                $obTPessoalCasoCausa->setDado("multa_fgts"                  ,$arCasoCausa['flMultaFGTS']);
                $obTPessoalCasoCausa->setDado("inc_fgts_ferias"             ,$arCasoCausa['boFeriasFGTS']);
                $obTPessoalCasoCausa->setDado("inc_fgts_aviso_previo"       ,$arCasoCausa['boAvisoPrevioFGTS']);
                $obTPessoalCasoCausa->setDado("inc_fgts_13"                 ,$arCasoCausa['bo13FGTS']);
                $obTPessoalCasoCausa->setDado("inc_irrf_ferias"             ,$arCasoCausa['boFeriasIRRF']);
                $obTPessoalCasoCausa->setDado("inc_irrf_aviso_previo"       ,$arCasoCausa['boAvisoPrevioIRRF']);
                $obTPessoalCasoCausa->setDado("inc_irrf_13"                 ,$arCasoCausa['bo13IRRF']);
                $obTPessoalCasoCausa->setDado("inc_prev_ferias"             ,$arCasoCausa['boFeriasPrevidencia']);
                $obTPessoalCasoCausa->setDado("inc_prev_aviso_previo"       ,$arCasoCausa['boAvisoPrevioPrevidencia']);
                $obTPessoalCasoCausa->setDado("inc_prev_13"                 ,$arCasoCausa['bo13Previdencia']);
                $obTPessoalCasoCausa->setDado("paga_ferias_proporcional"    ,$arCasoCausa['boFeriasProporcionais']);
                $obTPessoalCasoCausa->setDado("inden_art_479"               ,$arCasoCausa['boArtigo479']);
                if ($arCasoCausa['inCodCasoCausa'] != "") {
                    $obTPessoalCasoCausa->setDado("cod_caso_causa",$arCasoCausa['inCodCasoCausa']);
                    $obTPessoalCasoCausa->alteracao();
                    $inCodCasoCausa = $arCasoCausa['inCodCasoCausa'];
                } else {
                    $obTPessoalCasoCausa->setDado("cod_caso_causa","");
                    $obTPessoalCasoCausa->inclusao();
                    $inCodCasoCausa = $obTPessoalCasoCausa->getDado("cod_caso_causa");
                }

                $obTPessoalCasoCausaSubDivisao->setDado("cod_caso_causa",$inCodCasoCausa);
                $obTPessoalCasoCausaSubDivisao->setDado("cod_sub_divisao","");
                $obTPessoalCasoCausaSubDivisao->exclusao();
                if (is_array($arCasoCausa['inCodRegimeSelecionados'])) {
                    foreach ($arCasoCausa['inCodRegimeSelecionados'] as $inCodSubDivisao) {
                        $obTPessoalCasoCausaSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisao);
                        $obTPessoalCasoCausaSubDivisao->inclusao();
                    }
                }
            }
        }
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList,"Alterar Causa realizada com sucesso." ,"alterar","aviso", Sessao::getId(), "../");
    break;
    case "excluir";
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $stFiltro = " and caso_causa.cod_causa_rescisao = ".$_GET['inCodigoCausa'];
        $obTPessoalCasoCausa->recuperaRelacionamentoContrato($rsContrato,$stFiltro);
        if ( $rsContrato->getNumLinhas() < 0 ) {
            $stFiltro = " WHERE cod_causa_rescisao = ".$_GET['inCodigoCausa'];
            $obTPessoalCasoCausa->recuperaTodos($rsCasoCausa,$stFiltro);
            while (!$rsCasoCausa->eof()) {
                $stFiltro = " WHERE cod_caso_causa = ".$rsCasoCausa->getCampo("cod_caso_causa");
                $obTPessoalCasoCausaSubDivisao->setDado("cod_caso_causa",$rsCasoCausa->getCampo("cod_caso_causa"));
                $obErro = $obTPessoalCasoCausaSubDivisao->exclusao($obTransacao);

                $obTPessoalCasoCausa->setDado("cod_caso_causa",$rsCasoCausa->getCampo("cod_caso_causa"));
                $obErro = $obTPessoalCasoCausa->exclusao($obTransacao);
                $rsCasoCausa->proximo();
            }
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaRescisaoCaged.class.php");
            $obTPessoalCausaRescisaoCaged = new TPessoalCausaRescisaoCaged();
            $obTPessoalCausaRescisaoCaged->setDado("cod_causa_rescisao",$_GET['inCodigoCausa']);
            $obErro = $obTPessoalCausaRescisaoCaged->exclusao($obTransacao);

            $obTPessoalCausaRescisao->setDado("cod_causa_rescisao",$_GET['inCodigoCausa']);
            $obErro = $obTPessoalCausaRescisao->exclusao($obTransacao);

            if (!$obErro->ocorreu()) {
                $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro);
                sistemaLegado::alertaAviso($pgList,"Excluir Causa realizada com sucesso." ,"excluir","aviso", Sessao::getId(), "../");
                break;

            } else {
                sistemaLegado::alertaAviso($pgList,"Essa Causa de Rescisão está vinculada a um ou mais contratos, por este motivo não pode ser excluída.", "n_excluir","erro", Sessao::getId(), "../");
                break;
            }

        } else {
            sistemaLegado::alertaAviso($pgList,"Essa Causa de Rescisão está vinculada a um ou mais contratos, por este motivo não pode ser excluída." ,"n_excluir","erro", Sessao::getId(), "../");
            break;
        }
    break;

}
?>
