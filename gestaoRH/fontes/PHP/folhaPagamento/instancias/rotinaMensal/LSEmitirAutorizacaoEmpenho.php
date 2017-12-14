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
    * Lista
    * Data de Criação: 18/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30924 $
    $Name$
    $Author: souzadl $
    $Date: 2007-09-27 12:19:30 -0300 (Qui, 27 Set 2007) $

    * Casos de uso: uc-04.05.62
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = 'EmitirAutorizacaoEmpenho';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgProcRel = "PR".$stPrograma."Relatorio.php";
$pgOcul = "OC".$stPrograma.".php";
$pgDeta = "DT".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get("stAcao");

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

Sessao::write("origem",$request->get("stOrigem"));
if ($request->get("stOrigem") == "d") {
    Sessao::write("stTipoFiltro",$request->get("stTipoFiltro"));
    $rsDiariasAutorizacaoEmpenho = new RecordSet();
    $stCodigos = "";
    switch ($request->get("stTipoFiltro")) {
        case "contrato":
        case "cgm_contrato":
            foreach (Sessao::read('arContratos') as $arContrato) {
                $stCodigos .= $arContrato["cod_contrato"].",";
            }
            $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
            break;
        case "lotacao":
            $stCodigos = implode(",",$request->get("inCodLotacaoSelecionados"));
            break;
        case "periodo":
            $stCodigos = $request->get("stDataInicial")."#".$request->get("stDataFinal");
            break;
    }
    Sessao::write("stCodigos",$stCodigos);
    include_once(CAM_GRH_DIA_MAPEAMENTO."TDiariasDiaria.class.php");
    $obTDiariasDiaria = new TDiariasDiaria();
    $obTDiariasDiaria->setDado("stTipoFiltro",$request->get("stTipoFiltro"));
    $obTDiariasDiaria->setDado("stCodigos",$stCodigos);
    $obTDiariasDiaria->recuperaDiariasAutorizacaoEmpenho($rsDiariasAutorizacaoEmpenho);

    $inId = 0;
    while (!$rsDiariasAutorizacaoEmpenho->eof()) {
        $rsDiariasAutorizacaoEmpenho->setCampo("inId",$inId);
        $inId++;
        $rsDiariasAutorizacaoEmpenho->proximo();
    }

    Sessao::write("arEmissaoEmpenho",$rsDiariasAutorizacaoEmpenho->getElementos());
    $jsOnload = "executaFuncaoAjax('gerarSpanResumoEmissaoAutorizacaoEmpenhoDiarias');";
} else {
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",$request->get("inCodMes"));
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$request->get("inAno"));
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsCompetencia);

    $arrEmissaoEmpenho = Sessao::read("arEmissaoEmpenho");
    $stJoinAtributo    = "";

    if (!isset($arrEmissaoEmpenho)) {
        switch ($request->get("stTipoFiltro")) {
            case "contrato":
            case "cgm_contrato":
                $stFiltro = " AND cadastro.cod_contrato IN (";
                foreach (Sessao::read('arContratos') as $arContrato) {
                    $stFiltro .= $arContrato["cod_contrato"].",";
                }
                $stFiltro = substr($stFiltro,0,strlen($stFiltro)-1).")";
                break;
            case "lotacao":
                $stFiltro = " AND cadastro.cod_orgao IN (".implode(",",$request->get("inCodLotacaoSelecionados")).")";
                break;
            case "local":
                $stFiltro = " AND cadastro.cod_local IN (".implode(",",$request->get("inCodLocalSelecionados")).")";
                break;
            case "atributo_servidor":
                include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php");
                $obTAdministracaoAtributoDinamico = new TAdministracaoAtributoDinamico();
                $obTAdministracaoAtributoDinamico->setDado("cod_modulo",22);
                $obTAdministracaoAtributoDinamico->setDado("cod_cadastro",$request->get("inCodCadastro"));
                $obTAdministracaoAtributoDinamico->setDado("cod_atributo",$request->get("inCodAtributo"));
                $obTAdministracaoAtributoDinamico->recuperaPorChave($rsAtributo);

                $stJoinAtributo .= "JOIN (SELECT atributo_contrato_servidor_valor.*                                                                     \n";
                $stJoinAtributo .= "          FROM pessoal.atributo_contrato_servidor_valor                                   \n";
                $stJoinAtributo .= "             , (SELECT cod_contrato                                                                                 \n";
                $stJoinAtributo .= "                     , cod_atributo                                                                                 \n";
                $stJoinAtributo .= "                     , max(timestamp) as timestamp                                                                  \n";
                $stJoinAtributo .= "                  FROM pessoal.atributo_contrato_servidor_valor                           \n";
                $stJoinAtributo .= "                GROUP BY cod_contrato                                                                               \n";
                $stJoinAtributo .= "                       , cod_atributo) as max_atributo_contrato_servidor_valor                                      \n";
                $stJoinAtributo .= "         WHERE atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato    \n";
                $stJoinAtributo .= "           AND atributo_contrato_servidor_valor.timestamp = max_atributo_contrato_servidor_valor.timestamp          \n";
                $stJoinAtributo .= "           AND atributo_contrato_servidor_valor.cod_atributo = max_atributo_contrato_servidor_valor.cod_atributo    \n";
                $stJoinAtributo .= "           AND atributo_contrato_servidor_valor.cod_atributo = ".$request->get("inCodAtributo")."                          \n";
                if ($rsAtributo->getCampo("cod_tipo") == 4) {
                    $arValores = $request->get("Atributo_".$request->get("inCodAtributo")."_".$request->get("inCodCadastro")."_Selecionados");
                    $stValor = implode(",",$arValores);
                    $stJoinAtributo .= "           AND atributo_contrato_servidor_valor.valor IN (".$stValor.")) AS atributo \n";
                } else {
                    $stJoinAtributo .= "           AND atributo_contrato_servidor_valor.valor = \'".$request->get("Atributo_".$request->get("inCodAtributo")."_".$request->get("inCodCadastro"))."\') AS atributo \n";
                }
                $stJoinAtributo .= "   ON contrato.cod_contrato = atributo.cod_contrato                                                                 \n";
                break;
            case "atributo_pensionista":
                include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php");
                $obTAdministracaoAtributoDinamico = new TAdministracaoAtributoDinamico();
                $obTAdministracaoAtributoDinamico->setDado("cod_modulo",22);
                $obTAdministracaoAtributoDinamico->setDado("cod_cadastro",$request->get("inCodCadastro"));
                $obTAdministracaoAtributoDinamico->setDado("cod_atributo",$request->get("inCodAtributo"));
                $obTAdministracaoAtributoDinamico->recuperaPorChave($rsAtributo);

                $stJoinAtributo .= "JOIN (SELECT atributo_contrato_pensionista.*                                                                        \n";
                $stJoinAtributo .= "          FROM pessoal.atributo_contrato_pensionista                                                                \n";
                $stJoinAtributo .= "             , (SELECT cod_contrato                                                                                 \n";
                $stJoinAtributo .= "                     , cod_atributo                                                                                 \n";
                $stJoinAtributo .= "                     , max(timestamp) as timestamp                                                                  \n";
                $stJoinAtributo .= "                  FROM pessoal.atributo_contrato_pensionista                                                        \n";
                $stJoinAtributo .= "                GROUP BY cod_contrato                                                                               \n";
                $stJoinAtributo .= "                       , cod_atributo) as max_atributo_contrato_pensionista                                         \n";
                $stJoinAtributo .= "         WHERE atributo_contrato_pensionista.cod_contrato = max_atributo_contrato_pensionista.cod_contrato          \n";
                $stJoinAtributo .= "           AND atributo_contrato_pensionista.timestamp    = max_atributo_contrato_pensionista.timestamp             \n";
                $stJoinAtributo .= "           AND atributo_contrato_pensionista.cod_atributo = max_atributo_contrato_pensionista.cod_atributo          \n";
                $stJoinAtributo .= "           AND atributo_contrato_pensionista.cod_atributo = ".$request->get("inCodAtributo")."                             \n";
                if ($rsAtributo->getCampo("cod_tipo") == 4) {
                    $arValores = $request->get("Atributo_".$request->get("inCodAtributo")."_".$request->get("inCodCadastro")."_Selecionados");
                    $stValor = implode(",",$arValores);
                    $stJoinAtributo .= "           AND atributo_contrato_pensionista.valor IN (".$stValor.")) AS atributo                               \n";
                } else {
                    $stJoinAtributo .= "           AND atributo_contrato_pensionista.valor = \'".$request->get("Atributo_".$request->get("inCodAtributo")."_".$request->get("inCodCadastro"))."\') AS atributo \n";
                }
                $stJoinAtributo .= "   ON contrato.cod_contrato = atributo.cod_contrato                                                                 \n";
                break;
        }
        $rsEmissaoEmpenho = new RecordSet();

        if ($request->get("inCodComplementar", "") != "") {
            $stFiltro .= " AND cod_complementar = ".$request->get("inCodComplementar");
        }

        Sessao::write("cod_periodo_movimentacao",$rsCompetencia->getCampo("cod_periodo_movimentacao"));
        Sessao::write("dt_final",$rsCompetencia->getCampo("dt_final"));
        Sessao::write("cod_configuracao",$request->get("inCodConfiguracao"));
        Sessao::write("cod_configuracao_autorizacao",$request->get("inCodConfiguracaoAutorizacao"));
        Sessao::write("cadastro",$request->get("stSituacao"));
        Sessao::write("cod_previdencia",($request->get("inCodPrevidencia"))?$request->get("inCodPrevidencia"):0);
        Sessao::write("filtro",$stFiltro);
        Sessao::write("join",$stJoinAtributo);
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLla.class.php");
        $obTFolhaPagamentoConfiguracaoEmpenhoLla = new TFolhaPagamentoConfiguracaoEmpenhoLla();
        $obTFolhaPagamentoConfiguracaoEmpenhoLla->setDado("cod_periodo_movimentacao",$rsCompetencia->getCampo("cod_periodo_movimentacao"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLla->setDado("cod_configuracao",$request->get("inCodConfiguracao"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLla->setDado("cod_configuracao_autorizacao",$request->get("inCodConfiguracaoAutorizacao"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLla->setDado("cadastro",$request->get("stSituacao"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLla->setDado("origem",$request->get("stOrigem"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLla->setDado("cod_previdencia",($request->get("inCodPrevidencia"))?$request->get("inCodPrevidencia"):0);
        $obTFolhaPagamentoConfiguracaoEmpenhoLla->setDado("exercicio",$request->get('inAno'));
        $obTFolhaPagamentoConfiguracaoEmpenhoLla->setDado("filtro",$stFiltro);
        $obTFolhaPagamentoConfiguracaoEmpenhoLla->setDado("join",$stJoinAtributo);
        $obTFolhaPagamentoConfiguracaoEmpenhoLla->resumoEmissaoAutorizacaoEmpenho($rsEmissaoEmpenho,"","orgao,unidade");        
        
        if ($rsEmissaoEmpenho->getNumLinhas() > 1) {
            $stDotacaoAnterior = '';
            foreach ($rsEmissaoEmpenho->getElementos() as $key => $emissaoEmpenho) {
                $stDotacao = $emissaoEmpenho['red_dotacao'].$emissaoEmpenho['num_pao'];
                if ($stDotacao == $stDotacaoAnterior) {
                    $arAux[$stDotacaoAnterior]['orgao']           = $emissaoEmpenho['orgao'];
                    $arAux[$stDotacaoAnterior]['unidade']         = $emissaoEmpenho['unidade'];
                    $arAux[$stDotacaoAnterior]['saldo_dotacao']   = $emissaoEmpenho['saldo_dotacao'];
                    $arAux[$stDotacaoAnterior]['red_dotacao']     = $emissaoEmpenho['red_dotacao'];
                    $arAux[$stDotacaoAnterior]['rubrica_despesa'] = $emissaoEmpenho['rubrica_despesa'];
                    $arAux[$stDotacaoAnterior]['num_pao']         = $emissaoEmpenho['num_pao'];
                    $arAux[$stDotacaoAnterior]['desc_pao']        = $emissaoEmpenho['desc_pao'];
                    $arAux[$stDotacaoAnterior]['fornecedor']      = $emissaoEmpenho['fornecedor'];
                    $arAux[$stDotacaoAnterior]['valor']           = $arAux[$stDotacaoAnterior]['valor'] + $emissaoEmpenho['valor'];                        
                    if ( !strstr($arAux[$stDotacaoAnterior]['lla'], $emissaoEmpenho['lla']) ){
                        $arAux[$stDotacaoAnterior]['lla']        .= $emissaoEmpenho['lla'];
                    }
                    if ( !strstr($arAux[$stDotacaoAnterior]['evento'], $emissaoEmpenho['evento']) ){
                        $arAux[$stDotacaoAnterior]['evento']    .= $emissaoEmpenho['evento'];
                    }
                }else{
                    $arAux[$stDotacao] = $emissaoEmpenho;
                }
                $stDotacaoAnterior = $emissaoEmpenho['red_dotacao'].$emissaoEmpenho['num_pao'];
            }                                
            $arEmissoesEmpenho = array_merge($arAux);
        }else{
            $arEmissoesEmpenho = $rsEmissaoEmpenho->getElementos();
        }
        

        $arTemp = array();
        foreach ($arEmissoesEmpenho as $inIndex=>$arEmissaoEmpenho) {
            $arEmissaoEmpenho["inId"] = $inIndex;
            $arTemp[] = $arEmissaoEmpenho;
        }
        Sessao::write("arEmissaoEmpenho",$arTemp);
        Sessao::write("inCodConfiguracaoAutorizacao",$request->get("inCodConfiguracaoAutorizacao"));
    } else {
        $rsEmissaoEmpenho = new RecordSet();
        $rsEmissaoEmpenho->preenche(Sessao::read("arEmissaoEmpenho"));
    }
    $jsOnload = "executaFuncaoAjax('gerarSpanResumoEmissaoAutorizacaoEmpenho');";
}

$obForm = new Form;
$obForm->setAction ( $pgProcRel  );
$obForm->setTarget("telaPrincipal");

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

$obSpnResumoEmissao = new Span();
$obSpnResumoEmissao->setId("spnResumoEmissaoAutorizacoesEmpenho");

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter');");

$obBtnImprimir = new Ok();
$obBtnImprimir->setValue("Imprimir");
$obBtnImprimir->setName("imprimir");
$obBtnImprimir->setId("imprimir");

$stLocation = $pgForm."?".Sessao::getId()."&stAcao=".$stAcao;
$obBtnCancelar = new Button();
$obBtnCancelar->setValue("Cancelar");
$obBtnCancelar->obEvento->setOnClick("Cancelar('".$stLocation."');");

$arEmissaoEmpenho = Sessao::read("arEmissaoEmpenho");
if (count($arEmissaoEmpenho) == 0) {
    $obBtnOk->setDisabled(true);
    $obBtnImprimir->setDisabled(true);
}

$obFormulario = new Formulario();
$obFormulario->addForm   ( $obForm    );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addSpan($obSpnResumoEmissao);
$obFormulario->defineBarra(array($obBtnOk,$obBtnImprimir,$obBtnCancelar),"","");
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
