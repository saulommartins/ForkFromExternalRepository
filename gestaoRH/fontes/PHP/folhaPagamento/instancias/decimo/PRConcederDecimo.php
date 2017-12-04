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
    * Página de Processamento do Conceder de 13º Salário
    * Data de Criação: 14/09/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.24

    $Id: PRConcederDecimo.php 65813 2016-06-20 17:39:54Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConcessaoDecimo.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalcularFolhas.class.php";

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ConcederDecimo";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$rsContratos = new Recordset;
$rsContratos->preenche(is_array(Sessao::read('arContratos'))?Sessao::read('arContratos'):array());

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);

switch ($stAcao) {
    case "inserir":
        if ( $rsContratos->getNumLinhas() <= 0 ) {
            $stValoresFiltro = "";
            $obTFolhaPagamentoConcessaoDecimo = new TFolhaPagamentoConcessaoDecimo();

            switch ($request->get('stTipoFiltro')) {
                case "reg_sub_fun_esp":
                    $obTFolhaPagamentoConcessaoDecimo->setDado( "inCodRegime"    , implode(",",$request->get("inCodRegimeSelecionadosFunc")) );
                    $obTFolhaPagamentoConcessaoDecimo->setDado( "inCodSubDivisao", implode(",",$request->get("inCodSubDivisaoSelecionadosFunc")) );
                    $obTFolhaPagamentoConcessaoDecimo->setDado( "inCodCargo"     , implode(",",$request->get("inCodFuncaoSelecionados")) );
                    $inCodEspecialidadeSelecionadosFunc = $request->get('inCodEspecialidadeSelecionadosFunc');
                    if (is_array($inCodEspecialidadeSelecionadosFunc)) {
                        $obTFolhaPagamentoConcessaoDecimo->setDado( "inCodEspecialidade", implode(",",$inCodEspecialidadeSelecionadosFunc) );
                    }
                    break;

                case "lotacao":
                    $obTFolhaPagamentoConcessaoDecimo->setDado( "inCodOrgao", implode(",",$request->get("inCodLotacaoSelecionados")) );
                    break;

                case "local":
                    $obTFolhaPagamentoConcessaoDecimo->setDado( "inCodLocal", implode(",",$request->get("inCodLocalSelecionados")) );
                    break;
            }

            $obTFolhaPagamentoConcessaoDecimo->setDado( "inCodPeriodoMovimentacao", $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao") );
            $obTFolhaPagamentoConcessaoDecimo->recuperaContratosConcessaoDecimo($rsContratos);
        }

        $boContinuar = "true";
        if ($request->get('stDesdobramento') == "C") {
            include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoDecimo.class.php";
            $obTFolhaPagamentoUltimoRegistroEventoDecimo = new TFolhaPagamentoUltimoRegistroEventoDecimo();
            $stFiltro  = " AND ultimo_registro_evento_decimo.desdobramento = 'D'";
            $stFiltro .= " AND registro_evento_decimo.cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
            $obTFolhaPagamentoUltimoRegistroEventoDecimo->recuperaRegistrosDeEventoSemCalculo($rsRegistros,$stFiltro);
            if ( $rsRegistros->getNumLinhas() > 0 ) {
                $boContinuar = "false";
                sistemaLegado::alertaAviso($pgList."&stOpcao=T4","","incluir","aviso", Sessao::getId(), "../");
            } else {
                $boContinuar = $request->get('boContinuar');
                if ($boContinuar != "true") {
                    $stCaminho   = CAM_GRH_FOL_INSTANCIAS."decimo/PRConcederDecimo.php";
                    $stLink = Sessao::getId()."&boContinuar=true&stDescQuestao=ATENÇÃO! Ao conceder Complementação de 13º Salário, não será mais possível efetuar concessão ou calcular saldo de décimo terceiro neste exercício. Deseja continuar?";
                    $stLink = str_replace( '&', '*_*', $stLink );
                    SistemaLegado::executaFrameOculto("alertaQuestao('".$stCaminho."?".$stLink."','sn_excluir','".Sessao::getId()."');");
                }
            }
        }
        if ($boContinuar == "true") {
            include_once CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoGeraRegistroDecimo.class.php";
            include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAdiantamento.class.php";
            include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php";
            $obTPessoalContrato = new TPessoalContrato();

            $arDtFinal = explode("/",$rsUltimaMovimentacao->getCampo("dt_final"));
            $stFiltro  = " WHERE to_char(dt_final,'yyyy') = '".$arDtFinal[2]."'";
            $stOrdem  = " cod_periodo_movimentacao LIMIT 1";
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsTodosPeriodos,$stFiltro,$stOrdem);
            $obFFolhaPagamentoGeraRegistroDecimo = new FFolhaPagamentoGeraRegistroDecimo();
            $obTFolhaPagamentoConcessaoDecimo = new TFolhaPagamentoConcessaoDecimo();
            $obTFolhaPagamentoConfiguracaoAdiantamento = new TFolhaPagamentoConfiguracaoAdiantamento();
            $obTFolhaPagamentoConfiguracaoAdiantamento->obTFolhaPagamentoConcessaoDecimo = &$obTFolhaPagamentoConcessaoDecimo;
            $arContratos = array();
            $arContratosErro = array();
            $inCalculados = 0;
            $inNumContratos = $rsContratos->getNumLinhas();
            $obErro = new erro();
            $obTransacao = new Transacao;
            while (!$rsContratos->eof()) {
                $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
                if($obErro->ocorreu()) break;

                $nuPorcentagem  = number_format(($inCalculados*100/$inNumContratos), 2, ',', ' ');
                $stFiltro = " AND contrato.cod_contrato = ".$rsContratos->getCampo("cod_contrato");

                $obErro = $obTPessoalContrato->recuperaCgmDoRegistro($rsContrato,$stFiltro,'',$boTransacao);
                if($obErro->ocorreu()) break;

                $stMensagem = "Concedendo: ".$rsContrato->getCampo("registro")."-".$rsContrato->getCampo("nom_cgm");
                RFolhaPagamentoCalcularFolhas::percentageBar($nuPorcentagem,$stMensagem);

                $obTFolhaPagamentoConcessaoDecimo->setDado("cod_contrato",$rsContratos->getCampo("cod_contrato"));
                $obTFolhaPagamentoConcessaoDecimo->setDado("desdobramento",$request->get('stDesdobramento'));
                $obTFolhaPagamentoConcessaoDecimo->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
                $obTFolhaPagamentoConcessaoDecimo->setDado("folha_salario",$request->get('boPagEmFolhaSalario'));

                $stFiltro  = " WHERE cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $stFiltro .= "   AND cod_periodo_movimentacao <= ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltro .= "   AND cod_periodo_movimentacao >=".$rsTodosPeriodos->getCampo("cod_periodo_movimentacao");
                $stFiltro .= "   AND desdobramento = '".$request->get('stDesdobramento')."'";
                $obErro = $obTFolhaPagamentoConcessaoDecimo->recuperaTodos($rsConcessao,$stFiltro,'',$boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }

                if ($request->get('stDesdobramento') == "A") {
                    if ( $rsConcessao->getNumLinhas() == -1 ) {
                        $nuPercentualPagamento = str_replace(".","",$request->get('nuPercentualPagamento'));
                        $nuPercentualPagamento = (float) str_replace(",",".",$nuPercentualPagamento);
                        $obTFolhaPagamentoConfiguracaoAdiantamento->setDado("percentual"                ,$nuPercentualPagamento);
                        $obTFolhaPagamentoConfiguracaoAdiantamento->setDado("vantagens_fixas"           ,$request->get('boGerarSomenteVantagem'));

                        $obErro = $obTFolhaPagamentoConcessaoDecimo->inclusao($boTransacao);
                        if($obErro->ocorreu()) break;

                        $obErro = $obTFolhaPagamentoConfiguracaoAdiantamento->inclusao($boTransacao);
                        if($obErro->ocorreu()) break;

                        $inIndex = count($arContratos);
                        $arContratos[$inIndex]['registro']     = $rsContrato->getCampo("registro");
                        $arContratos[$inIndex]['numcgm']       = $rsContrato->getCampo("numcgm");
                        $arContratos[$inIndex]['nom_cgm']      = $rsContrato->getCampo("nom_cgm");

                        $obFFolhaPagamentoGeraRegistroDecimo->setDado("cod_contrato"            ,$rsContratos->getCampo("cod_contrato"));
                        $obFFolhaPagamentoGeraRegistroDecimo->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obFFolhaPagamentoGeraRegistroDecimo->setDado("desdobramento"           ,$request->get('stDesdobramento'));
                        $obErro = $obFFolhaPagamentoGeraRegistroDecimo->geraRegistroDecimo($rsGerar,$boTransacao);
                        if($obErro->ocorreu()) break;

                    } else {
                        $inIndex = count($arContratosErro);
                        $arContratosErro[$inIndex]['registro']     = $rsContrato->getCampo("registro");
                        $arContratosErro[$inIndex]['numcgm']       = $rsContrato->getCampo("numcgm");
                        $arContratosErro[$inIndex]['nom_cgm']      = $rsContrato->getCampo("nom_cgm");
                        $arContratosErro[$inIndex]['motivo']       = "A matrícula já possui concessão de adiantamento de 13º, no exercício";
                    }
                } else {
                    if ( $rsConcessao->getNumLinhas() == -1 ) {
                        $obErro = $obTFolhaPagamentoConcessaoDecimo->inclusao($boTransacao);
                        if($obErro->ocorreu()) break;

                        $obFFolhaPagamentoGeraRegistroDecimo->setDado("cod_contrato"            ,$rsContratos->getCampo("cod_contrato"));
                        $obFFolhaPagamentoGeraRegistroDecimo->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obFFolhaPagamentoGeraRegistroDecimo->setDado("desdobramento"           ,$request->get('stDesdobramento'));
                        $obErro = $obErro = $obFFolhaPagamentoGeraRegistroDecimo->geraRegistroDecimo($rsGerar,$boTransacao);
                        if($obErro->ocorreu()) break;

                        $inIndex = count($arContratos);
                        $arContratos[$inIndex]['registro']     = $rsContrato->getCampo("registro");
                        $arContratos[$inIndex]['numcgm']       = $rsContrato->getCampo("numcgm");
                        $arContratos[$inIndex]['nom_cgm']      = $rsContrato->getCampo("nom_cgm");
                    } else {
                        $inIndex = count($arContratosErro);
                        $arContratosErro[$inIndex]['registro']     = $rsContrato->getCampo("registro");
                        $arContratosErro[$inIndex]['numcgm']       = $rsContrato->getCampo("numcgm");
                        $arContratosErro[$inIndex]['nom_cgm']      = $rsContrato->getCampo("nom_cgm");
                        $arContratosErro[$inIndex]['motivo']       = "A matrícula já possui concessão de Saldo de 13º Salário, no exercício";
                    }
                }

                $inCalculados++;
                if ($inCalculados == $inNumContratos) {
                    RFolhaPagamentoCalcularFolhas::percentageBar("100");
                }
                $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
                $rsContratos->proximo();
            }

            if ( !$obErro->ocorreu() ) {
                Sessao::write('arContratos',$arContratos);
                Sessao::write('arContratosErro',$arContratosErro);
                SistemaLegado::alertaAviso($pgList."&stOpcao=T3","Concessão concluída!","incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::LiberaFrames();
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

        }
        SistemaLegado::LiberaFrames();
    break;
    case "excluir":
    case "cancelar":
        $stJs .= "BloqueiaFrames(true,false);";
        $stJs .= "jQuery('#showLoading',parent.frames[2].document).css('width','500px');";
        $stJs .= "jQuery('#showLoading',parent.frames[2].document).css('margin','-25px 0px 0px -250px;');";
        echo "<script>$stJs</script>";
        flush();

        $obTransacao = new Transacao();
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConcessaoDecimo.class.php";
        $obTFolhaPagamentoConcessaoDecimo = new TFolhaPagamentoConcessaoDecimo();

        $stValoresFiltro = "";
        switch ($request->get('stTipoFiltro')) {
           case "lotacao":
                $inCodLotacaoSelecionados = $request->get('inCodLotacaoSelecionados');
                $arCodLotacao = ( is_array($inCodLotacaoSelecionados) ) ? $inCodLotacaoSelecionados : Sessao::read('arCodLotacao');
                if ( is_array($arCodLotacao) ) {
                    Sessao::write('arCodLotacao',$arCodLotacao);
                    $stValoresFiltro = implode(",",$arCodLotacao);
                }
           case "local":
                $inCodLocalSelecionados = $request->get('inCodLocalSelecionados');
                $arCodLocal = ( is_array($inCodLocalSelecionados) ) ? $inCodLocalSelecionados : Sessao::read('arCodLocal');
                if ( is_array($arCodLocal) ) {
                    Sessao::write('arCodLocal',$arCodLocal);
                    $stValoresFiltro = implode(",",$arCodLocal);
                }
           case "reg_sub_fun_esp":
                $inCodRegimeSelecionadosFunc        = $request->get('inCodRegimeSelecionadosFunc');
                $inCodSubDivisaoSelecionadosFunc    = $request->get('inCodSubDivisaoSelecionadosFunc');
                $inCodFuncaoSelecionados            = $request->get('inCodFuncaoSelecionados');
                $inCodEspecialidadeSelecionadosFunc = $request->get('inCodEspecialidadeSelecionadosFunc');

                $arCodRegime        = ( is_array($inCodRegimeSelecionadosFunc)        ) ? $inCodRegimeSelecionadosFunc        : Sessao::read('arCodRegime');
                $arCodSubDivisao    = ( is_array($inCodSubDivisaoSelecionadosFunc)    ) ? $inCodSubDivisaoSelecionadosFunc    : Sessao::read('arCodSubDivisao');
                $arCodFuncao        = ( is_array($inCodFuncaoSelecionados)            ) ? $inCodFuncaoSelecionados            : Sessao::read('arCodFuncao');
                $arCodEspecialidade = ( is_array($inCodEspecialidadeSelecionadosFunc) ) ? $inCodEspecialidadeSelecionadosFunc : Sessao::read('arCodEspecialidade');

                if (is_array($arCodRegime)) {
                    $stValoresFiltro  = implode(",",$arCodRegime)."#";
                    $stValoresFiltro .= implode(",",$arCodSubDivisao)."#";
                    $stValoresFiltro .= implode(",",$arCodFuncao)."#";
                    if (is_array($arCodEspecialidade)) {
                       $stValoresFiltro .= implode(",",$arCodEspecialidade);
                    }
                }
            case "geral":
                $stFiltro  = " WHERE cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltro .= "   AND concessao_decimo.folha_salario IS ".Sessao::read("boPagEmFolhaSalario");

                $obTFolhaPagamentoConcessaoDecimo->setDado("stConfiguracao","cgm,oo,f,ef,l");
                $obTFolhaPagamentoConcessaoDecimo->setDado("stTipoFiltro",$request->get("stTipoFiltro"));
                $obTFolhaPagamentoConcessaoDecimo->setDado("inCodPeriodoMovimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
                $obTFolhaPagamentoConcessaoDecimo->setDado("stValoresFiltro",$stValoresFiltro);
                $obErro = $obTFolhaPagamentoConcessaoDecimo->recuperaContratosParaCancelar($rsContratos1,$stFiltro,'',$boTransacao);
                if($obErro->ocorreu()) break;

                $obErro = $obTFolhaPagamentoConcessaoDecimo->recuperaContratosParaCancelarPensionista($rsContratos2,$stFiltro,'',$boTransacao);
                if($obErro->ocorreu()) break;

                $arContratos1 = $rsContratos1->getElementos();
                $arContratos2 = $rsContratos2->getElementos();

                switch (true) {
                    case $arContratos1 != 0 and $arContratos2 != 0 :
                        $arContratos  = array_merge($arContratos1,$arContratos2);
                        break;
                    case $arContratos1 != 0 and $arContratos2 == 0 :
                        $arContratos  = array_merge($arContratos1);
                        break;
                    case $arContratos1 == 0 and $arContratos2 != 0 :
                        $arContratos  = array_merge($arContratos2);
                        break;
                }
                $rsContratos = new RecordSet();
                $rsContratos->preenche($arContratos);

                include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDeducaoDependente.class.php";
                $obTFolhaPagamentoDeducaoDependente = new TFolhaPagamentoDeducaoDependente();
                $arCodContratos = array();
                $inCalculados = 0;
                $inNumContratos = $rsContratos->getNumLinhas();

                while (!$rsContratos->eof()) {
                    $nuPorcentagem  = number_format(($inCalculados*100/$inNumContratos), 2, ',', ' ');
                    $stFiltro = " AND contrato.cod_contrato = ".$rsContratos->getCampo("cod_contrato");

                    $obTPessoalContrato = new TPessoalContrato;
                    $obErro = $obTPessoalContrato->recuperaCgmDoRegistro($rsContrato,$stFiltro,'',$boTransacao);
                    if($obErro->ocorreu()) break;

                    $stMensagem = "Cancelando: ".$rsContrato->getCampo("registro")."-".$rsContrato->getCampo("nom_cgm");
                    RFolhaPagamentoCalcularFolhas::percentageBar($nuPorcentagem,$stMensagem);

                    $stFiltro = " WHERE deducao_dependente.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= "   AND deducao_dependente.cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= "   AND deducao_dependente.cod_tipo = 4";
                    $obTFolhaPagamentoDeducaoDependente->recuperaTodos($rsDeducaoDependente,$stFiltro,'',$boTransacao);
                    if ($rsDeducaoDependente->getNumLinhas() == 1) {
                        $arCodContratos[] = array("cod_contrato"=>$rsContratos->getCampo("cod_contrato"));
                    }
                    $obErro = deletarConcessaoDecimo($rsContratos->getCampo("cod_contrato"),$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"),$boTransacao);
                    if($obErro->ocorreu()) break;

                    $inCalculados++;
                    if ($inCalculados == $inNumContratos) {
                        RFolhaPagamentoCalcularFolhas::percentageBar("99.99");
                    }
                    $rsContratos->proximo();
                }

                RFolhaPagamentoCalcularFolhas::percentageBar("99.99", "Recalculando sal&atilde;rio de matr&iacute;culas concedidas");

                if (!$obErro->ocorreu()) {
                    //Recalculo do contrato
                    $rsContratos = new recordset;
                    $rsContratos->preenche($arCodContratos);
                    $obErro = recalcularSalario($rsContratos,$boTransacao);
                }

                break;
            default:
                SistemaLegado::BloqueiaFrames(true, false);
                flush();

                $obErro = deletarConcessaoDecimo($request->get('inCodContrato'),$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"),$boTransacao);

                if (!$obErro->ocorreu()) {
                    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDeducaoDependente.class.php";
                    $obTFolhaPagamentoDeducaoDependente = new TFolhaPagamentoDeducaoDependente();
                    $arCodContratos = array();

                    $stFiltro  = " WHERE deducao_dependente.cod_contrato = ".$request->get('inCodContrato');
                    $stFiltro .= "   AND deducao_dependente.cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= "   AND deducao_dependente.cod_tipo = 4";
                    $obTFolhaPagamentoDeducaoDependente->recuperaTodos($rsDeducaoDependente,$stFiltro,'',$boTransacao);
                    if ($rsDeducaoDependente->getNumLinhas() == 1) {
                        $arCodContratos[] = array("cod_contrato"=>$request->get('inCodContrato'));
                    }

                    //Recalculo do contrato
                    $rsContratos = new recordset;
                    $rsContratos->preenche($arCodContratos);
                    $obErro = recalcularSalario($rsContratos,$boTransacao);
                }
                break;
        }

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

        SistemaLegado::LiberaFrames();
        if (!$obErro->ocorreu()) {
            switch ($request->get('stTipoFiltro')) {
                case "lotacao":
                case "local":
                case "reg_sub_fun_esp":
                case "geral":
                    SistemaLegado::alertaAviso($pgFilt,"Exclusão concluída.","excluir","aviso", Sessao::getId(), "../");
                    break;
                default:
                    SistemaLegado::alertaAviso($pgList,"Exclusão concluída","excluir","aviso", Sessao::getId(), "../");
                    break;
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;
}

function deletarConcessaoDecimo($inCodContrato,$inCodPeriodoMovimentacao,$boTransacao)
{
    $obErro = new Erro();

    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoDecimo.class.php";
    $obTFolhaPagamentoUltimoRegistroEventoDecimo =  new TFolhaPagamentoUltimoRegistroEventoDecimo;
    $stFiltro  = " AND cod_contrato = ".$inCodContrato;
    $stFiltro .= " AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
    $obErro = $obTFolhaPagamentoUltimoRegistroEventoDecimo->recuperaRegistrosEventoDecimoDoContrato($rsRegistros,$stFiltro,'',$boTransacao);
    if($obErro->ocorreu()) return $obErro;

    while (!$rsRegistros->eof()) {
        $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("cod_registro",$rsRegistros->getCampo("cod_registro"));
        $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("cod_evento",$rsRegistros->getCampo("cod_evento"));
        $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("desdobramento",$rsRegistros->getCampo("desdobramento"));
        $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("timestamp",$rsRegistros->getCampo("timestamp"));
        $obErro = $obTFolhaPagamentoUltimoRegistroEventoDecimo->deletarUltimoRegistroEvento($boTransacao);
        if($obErro->ocorreu()) return $obErro;

        $rsRegistros->proximo();
    }

    //Exclusão dos contratos com pagamento de décimo em salário
    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConcessaoDecimo.class.php";
    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAdiantamento.class.php";
    $obTFolhaPagamentoConcessaoDecimo = new TFolhaPagamentoConcessaoDecimo();
    $obTFolhaPagamentoConfiguracaoAdiantamento = new TFolhaPagamentoConfiguracaoAdiantamento();
    $obTFolhaPagamentoConfiguracaoAdiantamento->obTFolhaPagamentoConcessaoDecimo = &$obTFolhaPagamentoConcessaoDecimo;

    $stFiltro  = " WHERE cod_contrato = ".$inCodContrato;
    $stFiltro .= "   AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
    $stFiltro .= "   AND desdobramento = 'A'";
    $stFiltro .= "   AND folha_salario IS TRUE";
    $obErro = $obTFolhaPagamentoConcessaoDecimo->recuperaTodos($rsConcessoDecimo,$stFiltro,'',$boTransacao);
    if($obErro->ocorreu()) return $obErro;

    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEvento.class.php";
    $obTFolhaPagamentoUltimoRegistroEvento = new TFolhaPagamentoUltimoRegistroEvento();
    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php";
    $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
    while (!$rsConcessoDecimo->eof()) {
        $stFiltro  = "   AND cod_contrato =".$inCodContrato;
        $stFiltro .= "   AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
        $stFiltro .= "   AND desdobramento = 'I'";
        $obErro = $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventosCalculados,$stFiltro,'',$boTransacao);
        if($obErro->ocorreu()) return $obErro;

        while (!$rsEventosCalculados->eof()) {
            $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_registro",$rsEventosCalculados->getCampo("cod_registro"));
            $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_evento",$rsEventosCalculados->getCampo("cod_evento"));
            $obTFolhaPagamentoUltimoRegistroEvento->setDado("desdobramento",$rsEventosCalculados->getCampo("desdobramento"));
            $obTFolhaPagamentoUltimoRegistroEvento->setDado("timestamp",$rsEventosCalculados->getCampo("timestamp"));
            $obErro = $obTFolhaPagamentoUltimoRegistroEvento->deletarUltimoRegistroEvento($boTransacao);
            if($obErro->ocorreu()) return $obErro;

            $rsEventosCalculados->proximo();
        }
        $rsConcessoDecimo->proximo();
    }

    $obTFolhaPagamentoConcessaoDecimo->setDado("cod_contrato",$inCodContrato);
    $obTFolhaPagamentoConcessaoDecimo->setDado("cod_periodo_movimentacao",$inCodPeriodoMovimentacao);
    $obErro = $obTFolhaPagamentoConfiguracaoAdiantamento->exclusao($boTransacao);
    if($obErro->ocorreu()) return $obErro;

    $obErro = $obTFolhaPagamentoConcessaoDecimo->exclusao($boTransacao);

    return $obErro;
}

function recalcularSalario($rsContratos,$boTransacao)
{
    $obErro = new Erro();

    //Recalcula folha salário de contratos com dependente
    //isso serve para no caso do cancelamento de um décimo onde está
    //sendo incorporado a dedução de dependente, essa dedução passe para
    //a folha salário do contrato
    $stCodContratos = "";
    while (!$rsContratos->eof()) {
        $stCodContratos .= $rsContratos->getCampo("cod_contrato").",";
        $rsContratos->proximo();
    }
    $stCodContratos = substr($stCodContratos,0,strlen($stCodContratos)-1);

    include_once CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoDeletarInformacoesCalculo.class.php";
    $obFFolhaPagamentoDeletarInformacoesCalculo = new FFolhaPagamentoDeletarInformacoesCalculo();
    $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("stTipoFolha"          ,"S"            );
    $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("inCodComplementar"    ,0              );
    $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("stCodContratos"       ,$stCodContratos );
    $obErro = $obFFolhaPagamentoDeletarInformacoesCalculo->deletarInformacoesCalculo($rsDeletar, $boTransacao);
    if($obErro->ocorreu()) return $obErro;

    include_once CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoCalculaFolha.class.php";
    $obFFolhaPagamentoCalculaFolha = new FFolhaPagamentoCalculaFolha();
    $rsContratos->setPrimeiroElemento();
    while ( !$rsContratos->eof() ) {
        $obFFolhaPagamentoCalculaFolha->setDado('cod_contrato',$rsContratos->getCampo("cod_contrato"));
        $obFFolhaPagamentoCalculaFolha->setDado('boErro','f');
        $obErro = $obFFolhaPagamentoCalculaFolha->calculaFolha($rsCalcula, $boTransacao);
        if($obErro->ocorreu()) return $obErro;
        $rsContratos->proximo();
    }

    return $obErro;
}

?>
