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
    * Página de Form do Férias
    * Data de Criação: 09/06/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.04.22

    $Id: FMManterCadastroFerias.php 64331 2016-01-15 17:10:44Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalFormaPagamentoFerias.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoFolha.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFerias.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalLancamentoFerias.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterCadastroFerias";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgListLote = "LS".$stPrograma."Lote.php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);
$stAcao = $request->get("stAcao");

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

if (!$request->get("boConcederFeriasLote")) {
    if ($stAcao == "consultar") {
        $obTPessoalFerias = new TPessoalFerias;
        $obTPessoalFerias->setDado("cod_ferias",$request->get('inCodFerias'));
        $obTPessoalFerias->recuperaPorChave($rsFerias);
        $obTPessoalLancamentoFerias = new TPessoalLancamentoFerias;
        $obTPessoalLancamentoFerias->setDado("cod_ferias",$request->get('inCodFerias'));
        $obTPessoalLancamentoFerias->recuperaPorChave($rsLancamentoFerias);

        $obTPessoalFormaPagamentoFerias = new TPessoalFormaPagamentoFerias;
        $obTPessoalFormaPagamentoFerias->setDado("cod_forma",$rsFerias->getCampo("cod_forma"));
        $obTPessoalFormaPagamentoFerias->recuperaPorChave($rsFormaPagamento);
        $obTFolhaPagamentoTipoFolha = new TFolhaPagamentoTipoFolha;
        $obTFolhaPagamentoTipoFolha->setDado("cod_tipo",$rsLancamentoFerias->getCampo("cod_tipo"));
        $obTFolhaPagamentoTipoFolha->recuperaPorChave($rsTipoFolha);
        $arSemana  = array(0=>"Domingo",1=>"Segunda-feira",2=>"Terça-feira",3=>"Quarta-feira",4=>"Quinta-feira",5=>"Sexta-feira",6=>"Sábado");
        $dtInicial          = $rsFerias->getCampo("dt_inicial_aquisitivo");
        $dtFinal            = $rsFerias->getCampo("dt_final_aquisitivo");
        $inQuantDiasGozo    = $rsFerias->getCampo("dias_ferias");
        $inQuantDiasAbono   = $rsFerias->getCampo("dias_abono");
        $inQuantFaltas      = $rsFerias->getCampo("faltas");
        $dtInicialFerias    = $rsLancamentoFerias->getCampo("dt_inicio");
        $arInicialFerias    = explode("/",$dtInicialFerias);
        $inInicial          = date('w',mktime(0,0,0,$arInicialFerias[1],$arInicialFerias[0],$arInicialFerias[2]));
        $dtInicialFerias    = $dtInicialFerias ." - ".$arSemana[$inInicial];
        $dtFinalFerias      = $rsLancamentoFerias->getCampo("dt_fim");
        $arFinalFerias      = explode("/",$dtFinalFerias);
        $inFinal            = date('w',mktime(0,0,0,$arFinalFerias[1],$arFinalFerias[0],$arFinalFerias[2]));
        $dtFinalFerias      = $dtFinalFerias ." - ".$arSemana[$inFinal];
        $dtRetornoFerias    = $rsLancamentoFerias->getCampo("dt_retorno");
        $arRetornoFerias    = explode("/",$dtRetornoFerias);
        $inRetorno          = date('w',mktime(0,0,0,$arRetornoFerias[1],$arRetornoFerias[0],$arRetornoFerias[2]));
        $dtRetornoFerias    = $dtRetornoFerias ." - ".$arSemana[$inRetorno];
        $dtCompetencia      = $rsLancamentoFerias->getCampo("mes_competencia")."/".$rsLancamentoFerias->getCampo("ano_competencia");

        $stPagamento13      = ( $rsLancamentoFerias->getCampo("pagar_13") == "t" ) ? "Sim" : "Não";
        $stFormaPagamento   = $rsFormaPagamento->getCampo("cod_forma") ." - ".$rsFormaPagamento->getCampo("dias")." dia(s) de férias / ".$rsFormaPagamento->getCampo("abono")." dia(s) de abono";
        $stFolhaPago        = $rsTipoFolha->getCampo("descricao");
        $stLink  = "&inCodContrato=".$request->get('inCodContrato');
        $stLink .= "&inCodFerias=".$request->get('inCodFerias');
        $stLink .= "&dtInicialFerias=".$dtInicialFerias;
        $stLink .= "&dtFinalFerias=".$dtFinalFerias;
        $stLink .= "&dtRetornoFerias=".$dtRetornoFerias;
        $stLink .= "&dtCompetencia=".$dtCompetencia;
        $stLink .= "&stFolhaPago=".$stFolhaPago;
        $stLink .= "&stPagamento13=".$stPagamento13;
        $jsOnload   = "executaFuncaoAjax('processarConsulta','$stLink');";
    } else {
        Sessao::write('dtInicial', $request->get("dtInicial"));
        $dtFinal = $request->get("dtFinal");
        Sessao::write('dtFinal', $dtFinal);
        Sessao::write('dtCompetencia', $request->get("inCodMes")."/".$request->get("inAno"));

        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoContratoServidor.class.php");
        $obTPessoalAssentamentoGeradoContratoServidor = new TPessoalAssentamentoGeradoContratoServidor();
        $stFiltro  = " AND assentamento_gerado_contrato_servidor.cod_contrato = ".$request->get("inCodContrato")." \n";
        $stFiltro .= " AND assentamento_assentamento.cod_motivo = 10 \n";
        $stFiltro .= " AND (assentamento_gerado.periodo_inicial BETWEEN to_date('".$request->get("dtInicial")."','dd/mm/yyyy') AND to_date('".$request->get("dtFinal")."','dd/mm/yyyy') \n";
        $stFiltro .= "  OR  assentamento_gerado.periodo_final BETWEEN to_date('".$request->get("dtInicial")."','dd/mm/yyyy') AND to_date('".$request->get("dtFinal")."','dd/mm/yyyy')) \n";
        $obTPessoalAssentamentoGeradoContratoServidor->recuperaRelacionamento($rsAssentamentoGerado,$stFiltro);
        $inQuantFaltas = 0;
        while (!$rsAssentamentoGerado->eof()) {
            $inQuantFaltas += $rsAssentamentoGerado->getCampo("dias_do_periodo");
            $rsAssentamentoGerado->proximo();
        }
        SistemaLegado::executaFramePrincipal("processarForm('Form');");
    }
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnEval =  new HiddenEval;
$obHdnEval->setName  ( "stEval" );
$obHdnEval->setValue ( $stEval  );

$obHdnEval2 =  new HiddenEval;
$obHdnEval2->setName  ( "stEval2" );
$obHdnEval2->setValue ( $stEval2  );

$obLblContrato = new Label ;
$obLblContrato->setRotulo ( "Matrícula"                 );
$obLblContrato->setName   ( "inRegistro"                );
$obLblContrato->setId     ( "inRegistro"                );
$obLblContrato->setValue  ( $request->get("inRegistro") );

$obHdnCodContrato =  new Hidden;
$obHdnCodContrato->setName  ( "inCodContrato"                );
$obHdnCodContrato->setValue ( $request->get("inCodContrato") );

$obHdnContrato =  new Hidden;
$obHdnContrato->setName  ( "inContrato"                );
$obHdnContrato->setValue ( $request->get("inRegistro") );

$obLblCGM = new Label;
$obLblCGM->setRotulo ( "CGM"   );
$obLblCGM->setName   ( "inCGM" );
$obLblCGM->setId     ( "inCGM" );
$obLblCGM->setValue  ( $request->get("inNumCGM") ."-". str_replace("\\","",$request->get("stNomCGM")) );

$obLblLotacao = new Label;
$obLblLotacao->setRotulo ( "Lotação"   );
$obLblLotacao->setName   ( "inLotacao" );
$obLblLotacao->setId     ( "inLotacao" );
$obLblLotacao->setValue  ( $request->get("inCodEstrutural") ."-". $request->get("stDescLotacao") );

$obLblFuncao = new Label;
$obLblFuncao->setRotulo ( "Regime-Função" );
$obLblFuncao->setName   ( "stDescFuncao"  );
$obLblFuncao->setId     ( "stDescFuncao"  );
$obLblFuncao->setValue  ( $request->get("stDescRegime") ."-". $request->get("stDescFuncao") );

$obSpn1 = new Span;
$obSpn1->setId    ( "spnSpan1" );
$obSpn1->setValue ( ""         );

$obDtaDataInicial = new Data;
$obDtaDataInicial->setRotulo           ( "Data Inicial"                                  );
$obDtaDataInicial->setName             ( "dtInicial"                                     );
$obDtaDataInicial->setId               ( "dtInicial"                                     );
$obDtaDataInicial->setValue            ( $request->get("dtInicial")                      );
$obDtaDataInicial->setNull             ( false                                           );
$obDtaDataInicial->setTitle            ( "Informe a data inicial do período aquisitivo." );
$obDtaDataInicial->obEvento->setOnBlur ( "executaFuncaoAjax('preencherDataFinal','&dtInicial='+this.value+'&inCodContrato='+document.frm.inCodContrato.value); if ( !verificaData( this ) ) { this.value = '';} montaParametrosGET('preencherQuantidadeFaltas','dtInicial,dtFinal,inCodContrato');" );

$obLblDataInicial = new Label;
$obLblDataInicial->setRotulo ( "Data Inicial" );
$obLblDataInicial->setName   ( "dtInicial"    );
$obLblDataInicial->setId     ( "dtInicial"    );
$obLblDataInicial->setValue  ( $dtInicial     );

$obHdnDataInicial = new Hidden;
$obHdnDataInicial->setName  ( "dtInicial" );
$obHdnDataInicial->setValue ( $dtInicial  );

$obDtaDataFinal = new Data;
$obDtaDataFinal->setRotulo             ( "Data Final"             );
$obDtaDataFinal->setName               ( "dtFinal"                );
$obDtaDataFinal->setId                 ( "dtFinal"                );
$obDtaDataFinal->setValue              ( $request->get("dtFinal") );
$obDtaDataFinal->setNull               ( false                    );
$obDtaDataFinal->setTitle              ( "Informe a data final do período aquisitivo."                                        );
$obDtaDataFinal->obEvento->setOnChange ( "montaParametrosGET('preencherQuantDiasGozo');"                                      );
$obDtaDataFinal->obEvento->setOnBlur   ( "montaParametrosGET('preencherQuantidadeFaltas','dtInicial,dtFinal,inCodContrato');" );

$obLblDataFinal = new Label;
$obLblDataFinal->setRotulo ( "Data Final" );
$obLblDataFinal->setName   ( "dtFinal"    );
$obLblDataFinal->setId     ( "dtFinal"    );
$obLblDataFinal->setValue  ( $dtFinal     );

$obHdnDataFinal = new Hidden;
$obHdnDataFinal->setName  ( "dtFinal" );
$obHdnDataFinal->setValue ( $dtFinal  );

$rsFormasPagamento = new recordset();

// Necessário carregar da sessão e testar se existe no request, pois quando vai Conceder Férias não usa o request
$arContratos = Sessao::read("arContratos");
$obTPessoalFormaPagamentoFerias = new TPessoalFormaPagamentoFerias;

$stFiltro = "";
$stFiltroAux = "";

if (!empty($arContratos) && !$request->get('dtInicial') && !$request->get('dtFinal')){
    foreach ($arContratos as $campo) {
        if ($campo["cod_contrato"] != "") {
            $stFiltroAux .= "".$campo["cod_contrato"].",";
        }
    }
}else{
    if($request->get("inCodContrato", '') != '')
        $stFiltroAux .= "".$request->get("inCodContrato").",";
}

if ($stFiltroAux != "") {
    $stFiltro .= " AND ferias.cod_contrato IN (".substr($stFiltroAux,0,-1).") \n";
}

if (($request->get('dtInicial') != "") && ($request->get('dtFinal') != "")) {
    $stFiltro .= " AND ferias.dt_inicial_aquisitivo = TO_DATE('".$request->get('dtInicial')."','dd/mm/yyyy')
                   AND ferias.dt_final_aquisitivo   = TO_DATE('".$request->get('dtFinal')."'  ,'dd/mm/yyyy') ";
}

if (!$request->get("boConcederFeriasLote")) {
    $obTPessoalFormaPagamentoFerias->recuperaDiasFeriasRestantes($rsFormasPagamento,$stFiltro,"",$boTransacao);
}

if ($rsFormasPagamento->getNumLinhas() < 0) {
    $stFiltro  = "GROUP BY forma_pagamento_ferias.cod_forma      \n";
    $stFiltro .= "       , forma_pagamento_ferias.codigo         \n";
    $stFiltro .= "       , forma_pagamento_ferias.dias           \n";
    $stFiltro .= "       , forma_pagamento_ferias.abono          \n";
    $stFiltro .= " ORDER BY forma_pagamento_ferias.cod_forma     \n";
    $obTPessoalFormaPagamentoFerias->recuperaRelacionamento($rsFormasPagamento,$stFiltro);
}
$stFiltro = "";

$obCmbFormasPagamento = new Select;
$obCmbFormasPagamento->setRotulo                ( "Formas de Pagamento"                                                 );
$obCmbFormasPagamento->setName                  ( "inCodFormaPagamento"                                                 );
$obCmbFormasPagamento->setValue                 ( $inCodFormaPagamento                                                  );
$obCmbFormasPagamento->setStyle                 ( "width: 300px"                                                        );
$obCmbFormasPagamento->setCampoID               ( "cod_forma"                                                           );
$obCmbFormasPagamento->setCampoDesc             ( "[cod_forma] - [dias] dia(s) de férias / [abono] dia(s) de abono"     );
$obCmbFormasPagamento->addOption                ( "", "Selecione"                                                       );
$obCmbFormasPagamento->setTitle                 ( "Selecione a forma de pagamento das férias."                          );
$obCmbFormasPagamento->setNull                  ( false                                                                 );
$obCmbFormasPagamento->preencheCombo            ( $rsFormasPagamento                                                    );
$obCmbFormasPagamento->obEvento->setOnChange    ( "montaParametrosGET('preencherQuantDiasGozo');"                       );

$obIntQuantFaltas = new Inteiro;
$obIntQuantFaltas->setName                      ( "inQuantFaltas"                                                       );
$obIntQuantFaltas->setRotulo                    ( "Quantidade de Faltas"                                                );
$obIntQuantFaltas->setTitle                     ( "Informe a quantidade de faltas."                                     );
$obIntQuantFaltas->setValue                     ( $inQuantFaltas                                                        );
$obIntQuantFaltas->setAlign                     ( "RIGHT"                                                               );
$obIntQuantFaltas->setMaxLength                 ( 2                                                                     );
$obIntQuantFaltas->setSize                      ( 2                                                                     );
$obIntQuantFaltas->setMaxValue                  ( 31                                                                    );
$obIntQuantFaltas->setMinValue                  ( 1                                                                     );
$obIntQuantFaltas->setNegativo                  ( false                                                                 );
$obIntQuantFaltas->obEvento->setOnChange        ( "montaParametrosGET('preencherQuantDiasGozo');"                       );

$obLblQuantFaltas = new Label;
$obLblQuantFaltas->setRotulo                    ( "Quantidade de Faltas"                                                );
$obLblQuantFaltas->setName                      ( "inQuantFaltas"                                                       );
$obLblQuantFaltas->setId                        ( "inQuantFaltas"                                                       );
$obLblQuantFaltas->setValue                     ( $inQuantFaltas                                                        );

$obLblFormasPagamento = new Label;
$obLblFormasPagamento->setRotulo                ( "Formas de Pagamento"                                                 );
$obLblFormasPagamento->setName                  ( "stFormaPagamento"                                                    );
$obLblFormasPagamento->setId                    ( "stFormaPagamento"                                                    );
$obLblFormasPagamento->setValue                 ( $stFormaPagamento                                                     );

$obNumQuantDiasGozo = new Textbox;
$obNumQuantDiasGozo->setName                    ( "inQuantDiasGozo"                                                     );
$obNumQuantDiasGozo->setId                      ( "inQuantDiasGozo"                                                     );
$obNumQuantDiasGozo->setRotulo                  ( "Quantidade de Dias de Gozo"                                          );
$obNumQuantDiasGozo->setTitle                   ( "Informe a quantidade de dias de gozo das férias."                    );
$obNumQuantDiasGozo->setValue                   ( $inQuantDiasGozo                                                      );
$obNumQuantDiasGozo->setAlign                   ( "RIGHT"                                                               );
$obNumQuantDiasGozo->setMaxLength               ( 4                                                                     );
$obNumQuantDiasGozo->setSize                    ( 5                                                                     );
$obNumQuantDiasGozo->setMascara                 ( '99.9'                                                                );
$obNumQuantDiasGozo->setDisabled                ( true                                                                  );
$obNumQuantDiasGozo->obEvento->setOnChange      ( "montaParametrosGET('validarDataInicioFerias','dtInicialFerias,inQuantDiasGozo,inCodContrato');" );

$obLblQuantDiasAbono = new Label;
$obLblQuantDiasAbono->setRotulo                 ( "Quantidade de Dias de Abono"                                         );
$obLblQuantDiasAbono->setId                     ( "inQuantDiasAbono"                                                    );
$obLblQuantDiasAbono->setValue                  ( $inQuantDiasAbono                                                     );

$obHdnQuantDiasAbono =  new Hidden;
$obHdnQuantDiasAbono->setName                   ( "inQuantDiasAbono"                                                    );
$obHdnQuantDiasAbono->setValue                  ( $inQuantDiasAbono                                                     );

$obLblQuantDiasGozo = new Label;
$obLblQuantDiasGozo->setRotulo                  ( "Quantidade de Dias de Gozo"                                          );
$obLblQuantDiasGozo->setName                    ( "inQuantDiasGozo"                                                     );
$obLblQuantDiasGozo->setId                      ( "inQuantDiasGozo"                                                     );
$obLblQuantDiasGozo->setValue                   ( $inQuantDiasGozo                                                      );

$obLinDadosInformativos3 = new Link;
$obLinDadosInformativos3->setRotulo             ( "Dados Informativos"                                                  );
$obLinDadosInformativos3->setHref               ( "javascript:abrePopUpRegistrosEventosFerias();"                       );
$obLinDadosInformativos3->setValue              ( "Consultar Registro de Evento Ferias"                                 );

if ($request->get("boConcederFeriasLote")) {
    $stNomeLote = "Férias em lote";
    $obHdnTipoFiltro = new Hidden();
    $obHdnTipoFiltro->setName("stTipoFiltro");
    $obHdnTipoFiltro->setValue($request->get("stTipoFiltro"));

    $obHdnCodigos = new Hidden();
    $obHdnCodigos->setName("stCodigos");

    switch ($request->get("stTipoFiltro")) {
        case "contrato":
        case "cgm_contrato":
            $stCodigos = "";
            $arContratos = Sessao::read("arContratos");
            foreach ($arContratos as $arContrato) {
                $stCodigos .= $arContrato["cod_contrato"].",";
            }
            $stCodigos = substr($stCodigos,0,-1);

            $stFiltro = " AND contrato.cod_contrato IN (".$stCodigos.")";

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
            $obTPessoalContrato = new TPessoalContrato();
            $obTPessoalContrato->recuperaContratosFerias($rsContratos,$stFiltro);

            $stCodigos = "";
            while (!$rsContratos->eof()) {
                $stCodigos .= $rsContratos->getCampo('cod_contrato').",";
                $rsContratos->proximo();
            }
            $stCodigos = substr($stCodigos,0,-1);

            $stNomeLote .= " para Contratos";
            $stCodigosLote = "(".$stCodigos.")";
            $obHdnCodigos->setValue($stCodigos);
            $stTipoFiltroLote = 'C';
            break;
        case "lotacao":
            $stNomeLote .= " para Lotação";
            $stCodigosLote = "(".implode(",",$request->get("inCodLotacaoSelecionados")).")";
            $obHdnCodigos->setValue(implode(",",$request->get("inCodLotacaoSelecionados")));
            $stTipoFiltroLote = 'O';
            break;
        case "local":
            $stNomeLote .= " para Local";
            $stCodigosLote = "(".implode(",",$request->get("inCodLocalSelecionados")).")";
            $obHdnCodigos->setValue(implode(",",$request->get("inCodLocalSelecionados")));
            $stTipoFiltroLote = 'L';
            break;
        case "funcao":
            $stNomeLote .= " para Função";
            $stCodigosLote = "(".implode(",",$request->get("inCodFuncaoSelecionados")).")";
            $obHdnCodigos->setValue(implode(",",$request->get("inCodFuncaoSelecionados")));
            $stTipoFiltroLote = 'F';
            break;
        case "geral":
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
            $obTPessoalContrato = new TPessoalContrato();
            $obTPessoalContrato->recuperaContratosFerias($rsContratos,$stFiltro);
            while (!$rsContratos->eof()) {
                $stCodigos .= $rsContratos->getCampo('cod_contrato').",";
                $rsContratos->proximo();
            }
            $stNomeLote .= " Geral";
            $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
            $stCodigosLote = "(".$stCodigos.")";
            $obHdnCodigos->setValue($stCodigos);
            $stTipoFiltroLote = 'G';
            break;
    }

    $obImgNomeLote = new Img();
    $obImgNomeLote->setCaminho   ( CAM_FW_IMAGENS."botao_popup.png");
    $obImgNomeLote->setAlign     ( "absmiddle" );
    $obImgNomeLote->montaHTML();

    $stLink  = "&nbsp;<a href='JavaScript:abrePopUpLote();' title='Consultar filtro do lote.'>";
    $stLink .= $obImgNomeLote->getHTML();
    $stLink .= "</a>";

    $obLblNomeLote = new Label();
    $obLblNomeLote->setRotulo("Nome do Lote");
    $obLblNomeLote->setValue($stNomeLote.$stLink);

    $obHdnNomeLote = new Hidden();
    $obHdnNomeLote->setName("stNomeLote");
    $obHdnNomeLote->setValue($stNomeLote);

    Sessao::write("stTipoFiltroLote"     , $stTipoFiltroLote);
    Sessao::write("stCodigosLote"        , $stCodigosLote);
    Sessao::write("stNomeLote"           , $stNomeLote);

    $obHdnCodLote = new Hidden();
    $obHdnCodLote->setName("inCodLote");
    $obHdnCodLote->setValue($request->get("inCodLote"));

    $obHdnTipoFiltroLote = new Hidden();
    $obHdnTipoFiltroLote->setName("stTipoFiltroLote");
    $obHdnTipoFiltroLote->setValue($stTipoFiltroLote);

    $obHdnVencidas = new Hidden();
    $obHdnVencidas->setName("boApresentarSomenteFerias");
    $obHdnVencidas->setValue($request->get("boApresentarSomenteFerias"));
}

$obSpn2 = new Span;
$obSpn2->setId ( "spnSpan2" );

$obBtnOk = new Ok;
$obBtnOk->setName              ( "btnOk"                                  );
$obBtnOk->obEvento->setOnClick ( "montaParametrosGET('submeter','',true)" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimpar"                        );
$obBtnLimpar->setValue             ( "Limpar"                           );
$obBtnLimpar->setTipo              ( "button"                           );
$obBtnLimpar->obEvento->setOnClick ( "executaFuncaoAjax('limparForm');" );

$stLocation = $pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao;
$obBtnCancelar = new Cancelar();
$obBtnCancelar->obEvento->setOnClick ( "Cancelar('".$stLocation."');" );

if (count(Sessao::read('arListaFerias')) > 1)
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;

$obBtnFormularioCancelar = new Cancelar();
$obBtnFormularioCancelar->obEvento->setOnClick ( "Cancelar('".$stLocation."');" );

$obBtnVoltar = new Button;
$obBtnVoltar->setName              ( "btnVoltar"                   );
$obBtnVoltar->setValue             ( "Fechar"                      );
$obBtnVoltar->setTipo              ( "button"                      );
$obBtnVoltar->obEvento->setOnClick ( "javascript: window.close();" );

//DEFINICAO DO FORM
$obForm = new Form;
if ($request->get("boConcederFeriasLote")) {
    $obForm->setAction($pgListLote);
    $obForm->setTarget("telaPrincipal");
} else {
    $obForm->setAction($pgProc);
    $obForm->setTarget("oculto");
}

if ($request->get("boConcederFeriasLote") and $request->get("stAcao") == "excluir") {
    $obBtnOk->obEvento->setOnClick( "montaParametrosGET('excluirLote','',true)" );

    $obTPessoalLoteFerias = new TPessoalLoteFerias();
    $stFiltro = " WHERE cod_lote = ".$request->get("inCodLote");
    $obTPessoalLoteFerias->recuperaTodos($rsLoteFeriasContrato,$stFiltro);
    $stLote = $rsLoteFeriasContrato->getCampo("nome");

    $obLblNomeLote->setValue($stLote);

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addForm       ( $obForm                                                          );
    $obFormulario->addTitulo     ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
    $obFormulario->addHidden     ( $obHdnAcao                                                       );
    $obFormulario->addHidden     ( $obHdnCtrl                                                       );
    $obFormulario->addTitulo     ( "Concessão Férias em Lote"                                       );
    $obFormulario->addComponente ( $obLblNomeLote                                                   );
    $obFormulario->addHidden     ( $obHdnCodLote                                                    );
    $obFormulario->defineBarra   ( array($obBtnOk,$obBtnCancelar)                                   );
    $obFormulario->show();
} else {
    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addForm               ( $obForm                                                            );
    $obFormulario->addTitulo             ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"   );
    $obFormulario->addHidden             ( $obHdnAcao                                                         );
    $obFormulario->addHidden             ( $obHdnCtrl                                                         );
    $obFormulario->addHidden             ( $obHdnEval,true                                                    );
    $obFormulario->addHidden             ( $obHdnEval2,true                                                   );
    if (!$request->get("boConcederFeriasLote")) {
        Sessao::write("boConcederFeriasLote", false);
        $obFormulario->addHidden         ( $obHdnCodContrato                                                  );
        $obFormulario->addTitulo         ( "Dados da Matrícula"                                               );
        $obFormulario->addComponente     ( $obLblContrato                                                     );
        $obFormulario->addHidden         ( $obHdnContrato                                                     );
        $obFormulario->addComponente     ( $obLblCGM                                                          );
        $obFormulario->addComponente     ( $obLblLotacao                                                      );
        $obFormulario->addComponente     ( $obLblFuncao                                                       );
        $obFormulario->addSpan           ( $obSpn1                                                            );
        $obFormulario->addTitulo         ( "Período Aquisitivo"                                               );
        if ($boFeriasCadastradas) {
            $obFormulario->addComponente ( $obLblDataInicial                                                  );
            $obFormulario->addHidden     ( $obHdnDataInicial                                                  );
        } else {
            $obFormulario->addComponente ( ( $stAcao == "consultar" ) ? $obLblDataInicial : $obDtaDataInicial );
        }
        if ($boFeriasCadastradas) {
            $obFormulario->addComponente ( $obLblDataFinal                                                    );
            $obFormulario->addHidden     ( $obHdnDataFinal                                                    );
        } else {
            $obFormulario->addComponente ( ( $stAcao == "consultar" ) ? $obLblDataFinal : $obDtaDataFinal     );
        }
    } else {
        Sessao::write("boConcederFeriasLote", true);
        $obFormulario->addTitulo     ( "Concessão Férias em Lote" );
        $obFormulario->addComponente ( $obLblNomeLote             );
        $obFormulario->addHidden     ( $obHdnNomeLote             );
        $obFormulario->addHidden     ( $obHdnTipoFiltro           );
        $obFormulario->addHidden     ( $obHdnTipoFiltroLote       );
        $obFormulario->addHidden     ( $obHdnCodigos              );
        $obFormulario->addHidden     ( $obHdnVencidas             );
    }

    $obFormulario->addTitulo         ( "Pagamento"                                                                );
    if (!$request->get("boConcederFeriasLote")) {
        $obFormulario->addComponente ( ( $stAcao == "consultar" ) ? $obLblQuantFaltas : $obIntQuantFaltas         );
    }
    $obFormulario->addComponente     ( ( $stAcao == "consultar" ) ? $obLblFormasPagamento : $obCmbFormasPagamento );
    $obFormulario->addComponente     ( ( $stAcao == "consultar" ) ? $obLblQuantDiasGozo : $obNumQuantDiasGozo     );
    $obFormulario->addComponente     ( $obLblQuantDiasAbono                                                       );
    $obFormulario->addHidden         ( $obHdnQuantDiasAbono                                                       );
    $obFormulario->addSpan           ( $obSpn2                                                                    );
    if ($stAcao == "consultar") {
        $obFormulario->addComponente ( $obLinDadosInformativos3                                                   );
    }
    $obFormulario->defineBarra       ( ( $stAcao == "consultar" ) ? array($obBtnVoltar) : array($obBtnOk,$obBtnLimpar,$obBtnFormularioCancelar) );
    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
