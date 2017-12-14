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
    * Página de Processamento do Férias
    * Data de Criação: 09/06/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.04.22

    $Id: PRManterCadastroFerias.php 64319 2016-01-15 13:51:29Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalLancamentoFerias.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoGeraRegistroFerias.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoFerias.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoFerias.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoFeriasParcela.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculado.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculadoDependente.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoFerias.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculadoDependente.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementarSituacao.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculadoDependente.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoComplementar.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFerias.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFeriasContrato.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFeriasOrgao.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFeriasLocal.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFeriasFuncao.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFeriasLote.class.php";

$arLink = Sessao::read('link');
$stAcao = $request->get("stAcao");
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterCadastroFerias";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS   = "JS".$stPrograma.".js";
$pgProx = $pgList;

sistemalegado::bloqueiaFrames();
flush();

$obErro = new Erro;
switch ($stAcao) {
    case "incluir":
        Sessao::setTrataExcecao(true);

        $boTransacao = Sessao::getTransacao()->inTransacao;

        $stNomeLote = $request->get('stNomeLote');
        $arMeses    = array(1=>"Janeiro",
                            2=>"Fevereiro",
                            3=>"Março",
                            4=>"Abril",
                            5=>"Maio",
                            6=>"Junho",
                            7=>"Julho",
                            8=>"Agosto",
                            9=>"Setembro",
                            10=>"Outubro",
                            11=>"Novembro",
                            12=>"Dezembro");

        $stCompetencia    = $arMeses[$request->get("inCodMes")]."/".$request->get("inAno");
        $stMesCompetencia = ($request->get("inCodMes")<10)?"0".$request->get("inCodMes"):$request->get("inCodMes");
        $stAnoCompetencia = $request->get("inAno");

        if (Sessao::read("boConcederFeriasLote")) {
            $rsLoteFeriasContrato = new recordset();
            switch ($request->get("stTipoFiltro")) {
                case "contrato":
                case "cgm_contrato":
                    $obTPessoalLoteFerias = new TPessoalLoteFerias();
                    $stFiltro = " WHERE nome ilike '%contratos%' AND ano_competencia||mes_competencia = '".$stAnoCompetencia.$stMesCompetencia."'";
                    $obTPessoalLoteFerias->recuperaTodos($rsLoteFeriasContrato,$stFiltro,"",$boTransacao);
                    break;
            }

            $arContratos = array();
            foreach ($request->getAll() as $stCampo=>$stValor) {
                if (strpos($stCampo,"boLote") === 0) {
                    $arValor = explode("_",$stValor);
                    $arContratos[] = array("cod_contrato"          => $arValor[0],
                                           "dias_ferias"           => $arValor[1],
                                           "dias_abono"            => $arValor[2],
                                           "dias_faltas"           => $arValor[3],
                                           "dt_inicial_aquisitivo" => $arValor[4],
                                           "dt_final_aquisitivo"   => $arValor[5]
                                           );
                }
            }

            if (count($arContratos)) {
                $obTPessoalLoteFerias                           = new TPessoalLoteFerias();
                $obTPessoalLoteFeriasLote                       = new TPessoalLoteFeriasLote();
                $obTPessoalLoteFeriasLote->obTPessoalLoteFerias = &$obTPessoalLoteFerias;

                $stFiltro  = " WHERE nome ilike '".$stNomeLote."%'
                                 AND ano_competencia||mes_competencia = '".$stAnoCompetencia.$stMesCompetencia."'";

                $stNomeLote .= " - Competência ".$stCompetencia;

                $obTPessoalLoteFerias->recuperaRelacionamento($rsLoteFerias,$stFiltro);

                if($rsLoteFerias->getNumLinhas() > 0 && $request->get("stTipoFiltro") != "contrato" && $request->get("stTipoFiltro") != "cgm_contrato"){
                    Sessao::getExcecao()->setDescricao($stNomeLote." já foi gerado anteriormente.");
                    sistemalegado::LiberaFrames();
                }

                if ($rsLoteFeriasContrato->getNumLinhas() == -1) {
                    $obTPessoalLoteFerias->setDado("nome"           , $stNomeLote       );
                    $obTPessoalLoteFerias->setDado("mes_competencia", $stMesCompetencia );
                    $obTPessoalLoteFerias->setDado("ano_competencia", $stAnoCompetencia );
                    $obTPessoalLoteFerias->inclusao($boTransacao);

                    $stCodigosFiltro = explode(",", $request->get('stCodigos'));

                    switch ($request->get("stTipoFiltroLote")) {
                        case 'O':
                            $obTPessoalLoteFeriasFiltro = new TPessoalLoteFeriasOrgao();
                            $obTPessoalLoteFeriasFiltro->setDado('cod_lote', $obTPessoalLoteFerias->getDado('cod_lote') );
                            foreach ($stCodigosFiltro as $inCodigoFiltro) {
                                $obTPessoalLoteFeriasFiltro->setDado('cod_orgao', $inCodigoFiltro);
                                $obTPessoalLoteFeriasFiltro->inclusao($boTransacao);
                            }
                            break;
                        case 'L':
                            $obTPessoalLoteFeriasFiltro = new TPessoalLoteFeriasLocal();
                            $obTPessoalLoteFeriasFiltro->setDado('cod_lote', $obTPessoalLoteFerias->getDado('cod_lote') );
                            foreach ($stCodigosFiltro as $inCodigoFiltro) {

                                $obTPessoalLoteFeriasFiltro->setDado('cod_local', $inCodigoFiltro);
                                $obTPessoalLoteFeriasFiltro->inclusao($boTransacao);
                            }
                            break;
                        case 'F':
                            $obTPessoalLoteFeriasFiltro = new TPessoalLoteFeriasFuncao();
                            $obTPessoalLoteFeriasFiltro->setDado('cod_lote', $obTPessoalLoteFerias->getDado('cod_lote') );
                            foreach ($stCodigosFiltro as $inCodigoFiltro) {
                                $obTPessoalLoteFeriasFiltro->setDado('cod_cargo', $inCodigoFiltro);
                                $obTPessoalLoteFeriasFiltro->inclusao($boTransacao);
                            }
                            break;
                    }
                } else
                    $obTPessoalLoteFerias->setDado("cod_lote",$rsLoteFeriasContrato->getCampo("cod_lote"));
            }
        } else {
            include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php";
            $obTPessoalContrato = new TPessoalContrato();
            $stFiltro = " AND contrato.cod_contrato = ".$request->get('inCodContrato');
            $obTPessoalContrato->recuperaCgmDoRegistroServidor($rsContrato,$stFiltro);
            $arContratos[] = array("cod_contrato"=>$request->get('inCodContrato'),"numcgm"=>$rsContrato->getCampo("numcgm"));
        }
        if (count($arContratos)) {
            include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
            include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorNomeacaoPosse.class.php";

            $obTAdministracaoConfiguracao            = new TAdministracaoConfiguracao();
            $obTPessoalContratoServidorNomeacaoPosse = new TPessoalContratoServidorNomeacaoPosse();

            $obTAdministracaoConfiguracao->setDado("exercicio" , Sessao::getExercicio()                    );
            $obTAdministracaoConfiguracao->setDado("cod_modulo", 22                                        );
            $obTAdministracaoConfiguracao->setDado("parametro" , "dtContagemInicial".Sessao::getEntidade() );
            $obTAdministracaoConfiguracao->recuperaPorChave($rsConfiguracao,$boTransacao);

            foreach ($arContratos as $arContrato) {
                $obTPessoalFerias           = new TPessoalFerias;
                $obTPessoalLancamentoFerias = new TPessoalLancamentoFerias;
                $obTPessoalLancamentoFerias->obTPessoalFerias = &$obTPessoalFerias;
                $obFFolhaPagamentoGeraRegistroFerias  = new FFolhaPagamentoGeraRegistroFerias;
                $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;

                if ($request->get('dtInicial') != "") {
                    $dtInicial = $request->get('dtInicial');
                } else {
                    $dtInicial = $arContrato['dt_inicial_aquisitivo'];
                }
                if ($request->get('dtFinal') != "") {
                    $dtFinal = $request->get('dtFinal');
                } else {
                    //$arInicial = explode("/",$dtInicial);
                    //$dtFinal   = date("d/m/Y",mktime(0,0,0,$arInicial[1],$arInicial[0]-1,$arInicial[2]+1));
                    $dtFinal = $arContrato['dt_final_aquisitivo'];
                }

                $stFiltro  = " WHERE cod_contrato = ".$arContrato["cod_contrato"];
                $stFiltro .= "   AND to_char(dt_inicial_aquisitivo,'dd/mm/yyyy') = '".$dtInicial."'";
                $stFiltro .= "   AND to_char(dt_final_aquisitivo,'dd/mm/yyyy') = '".$dtFinal."'";
                $stFiltro .= "   AND cod_forma NOT IN (3,4) ";
                $obTPessoalFerias->recuperaTodos($rsFeriasPeriodo,$stFiltro, " ORDER BY cod_ferias LIMIT 1 ",$boTransacao);

                $stFiltro  = "   AND ferias.cod_contrato = ".$arContrato["cod_contrato"];
                $stFiltro .= "   AND (   dt_inicio BETWEEN to_date('".$request->get('dtInicialFerias')."','dd/mm/yyyy') AND to_date('".$request->get('dtFinalFerias')."','dd/mm/yyyy') ";
                $stFiltro .= "        OR dt_fim    BETWEEN to_date('".$request->get('dtInicialFerias')."','dd/mm/yyyy') AND to_date('".$request->get('dtFinalFerias')."','dd/mm/yyyy') ";
                $stFiltro .= "       ) ";
                $obTPessoalLancamentoFerias->recuperaLancamentoFerias($rsFeriasGozo,$stFiltro, " ORDER BY cod_ferias LIMIT 1 ");
                
                $rsFerias2Parcela = new RecordSet();
                if($request->get('inCodFormaPagamento') == 3 OR $request->get('inCodFormaPagamento') == 4){
                    $stFiltro  = " WHERE cod_contrato = ".$arContrato["cod_contrato"];
                    $stFiltro .= "   AND to_char(dt_inicial_aquisitivo,'dd/mm/yyyy') = '".$dtInicial."'";
                    $stFiltro .= "   AND to_char(dt_final_aquisitivo,'dd/mm/yyyy') = '".$dtFinal."'";
                    $stFiltro .= "   AND cod_forma IN (3,4) ";
                    $obTPessoalFerias->recuperaTodos($rsFerias2Parcela,$stFiltro, " ORDER BY cod_ferias",$boTransacao);
                }

                if ($rsFeriasPeriodo->getNumLinhas() == -1 and $rsFeriasGozo->getNumLinhas() == -1) {
                    $inQuantDiasGozo = (trim($request->get('inQuantDiasGozo')) != "") ? $request->get('inQuantDiasGozo') : $arContrato["dias_ferias"];
                    $inFaltas = ($request->get('inQuantFaltas') == "") ? $arContratos["dias_faltas"] : $request->get('inQuantFaltas');
                    $obTPessoalFerias->setDado("cod_contrato"           , $arContrato["cod_contrato"]                                                                                     );
                    $obTPessoalFerias->setDado("cod_forma"              , $request->get('inCodFormaPagamento')                                                                            );
                    $obTPessoalFerias->setDado("faltas"                 , (trim($inFaltas) != "") ? $inFaltas : 0                                                                         );
                    $obTPessoalFerias->setDado("dias_ferias"            , $inQuantDiasGozo                                                                                                );
                    $obTPessoalFerias->setDado("dias_abono"             , (trim($request->get('inQuantDiasAbono')) != "") ? $request->get('inQuantDiasAbono') : $arContrato["dias_abono"] );
                    $obTPessoalFerias->setDado("dt_inicial_aquisitivo"  , $dtInicial                                                                                                      );
                    $obTPessoalFerias->setDado("dt_final_aquisitivo"    , $dtFinal                                                                                                        );
                    $obTPessoalFerias->inclusao($boTransacao);

                    if (Sessao::read("boConcederFeriasLote")) {
                        //Insere contrato para lotes de ferias ja cadastrados e atualiza o filtro quando tipo lote = Geral G ou Contratos C
                        if ($request->get("stTipoFiltroLote") == 'C' || $request->get("stTipoFiltroLote") == 'G') {
                            $obTPessoalLoteFeriasFiltro = new TPessoalLoteFeriasContrato();
                            $obTPessoalLoteFeriasFiltro->setDado('cod_lote'     , $obTPessoalLoteFerias->getDado('cod_lote') );
                            $obTPessoalLoteFeriasFiltro->setDado('cod_contrato' , $arContrato["cod_contrato"]                );
                            $obTPessoalLoteFeriasFiltro->recuperaPorChave($rsLoteContrato,$boTransacao);
                            if ($rsLoteContrato->getNumLinhas() == -1){
                                $obTPessoalLoteFeriasFiltro->inclusao($boTransacao);
                            }
                        }

                        $obTPessoalLoteFeriasLote->obTPessoalFerias = &$obTPessoalFerias;
                        $obTPessoalLoteFeriasLote->inclusao($boTransacao);
                    }

                    if ($inQuantDiasGozo) {
                        $inCodMes = $request->get('inCodMes') ? $request->get('inCodMes') : $request->get('hdninCodMes');
                        $inCodMes = str_pad($inCodMes,2,"0",STR_PAD_LEFT);
                        $boPagar = $request->get('boPagamento13') ? $request->get('boPagamento13') : '';
                        $boPagar13 = ($boPagar == 1) ? 'true' : 'false';

                        $inCodTipo = $request->get('inCodTipo') ? $request->get('inCodTipo') : 1;
                        $inAnoCompetencia = $request->get('inAno') ? $request->get('inAno') : $request->get('hdninAno');

                        if($rsFerias2Parcela->getNumLinhas()>0){
                            $inCodMes = '';
                            $inAnoCompetencia = '';
                        }

                        $obTPessoalLancamentoFerias->setDado("dt_inicio"              , $request->get('dtInicialFerias') );
                        $obTPessoalLancamentoFerias->setDado("dt_fim"                 , $request->get('dtFinalFerias')   );
                        $obTPessoalLancamentoFerias->setDado("dt_retorno"             , $request->get('dtRetornoFerias') );
                        $obTPessoalLancamentoFerias->setDado("mes_competencia"        , $inCodMes                        );
                        $obTPessoalLancamentoFerias->setDado("ano_competencia"        , $inAnoCompetencia                );
                        $obTPessoalLancamentoFerias->setDado("pagar_13"               , $boPagar13                       );
                        $obTPessoalLancamentoFerias->setDado("cod_tipo"               , $inCodTipo                       );
                        $obTPessoalLancamentoFerias->inclusao($boTransacao);

                        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
                        $arDataFinal = explode("/",$rsUltimaMovimentacao->getCampo("dt_final"));

                        if ($request->get('inCodTipo',null) != null) {
                            if ($inCodMes == $arDataFinal[1] and $request->get('inAno') == $arDataFinal[2]) {
                                $obFFolhaPagamentoGeraRegistroFerias->setDado("cod_contrato"             , $arContrato["cod_contrato"]                                 );
                                $obFFolhaPagamentoGeraRegistroFerias->setDado("cod_periodo_movimentacao" , $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao") );
                                $obFFolhaPagamentoGeraRegistroFerias->setDado("exercicio"                , Sessao::getExercicio()                                      );
                                $obFFolhaPagamentoGeraRegistroFerias->geraRegistroFerias($rsRegistroFerias,$boTransacao);
                            }
                        }

                        ###Assentamento###
                        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAssentamento.class.php";
                        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoContratoServidor.class.php";
                        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php";
                        include_once CAM_GRH_PES_MAPEAMENTO."FPessoalRegistrarEventoPorAssentamento.class.php";

                        $obTPessoalAssentamentoAssentamento           = new TPessoalAssentamentoAssentamento();
                        $obTPessoalAssentamentoGeradoContratoServidor = new TPessoalAssentamentoGeradoContratoServidor();
                        $obTPessoalAssentamentoGerado                 = new TPessoalAssentamentoGerado();
                        $obFPessoalRegistrarEventoPorAssentamento     = new FPessoalRegistrarEventoPorAssentamento();

                        $stFiltro  = " AND contrato_servidor_previdencia.bo_excluido = false";
                        $stFiltro .= " AND assentamento.assentamento_automatico = true";
                        $stFiltro .= " AND classificacao_assentamento.cod_tipo = 2";
                        $stFiltro .= " AND assentamento_assentamento.cod_motivo = 2";
                        $stFiltro .= " AND contrato_servidor_previdencia.cod_contrato = ".$arContrato["cod_contrato"];
                        $obTPessoalAssentamentoAssentamento->recuperaContratoAssentamentoSubDivisao($rsAssentamentoAssentamento,$stFiltro);

                        while (!$rsAssentamentoAssentamento->eof()) {
                            $obErro = $obTPessoalAssentamentoGeradoContratoServidor->proximoCod( $inCodAssentamentoGerado, Sessao::getTransacao()->inTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $obTPessoalAssentamentoGeradoContratoServidor->setDado( "cod_assentamento_gerado" , $inCodAssentamentoGerado    );
                                $obTPessoalAssentamentoGeradoContratoServidor->setDado( "cod_contrato"            , $arContrato["cod_contrato"] );
                                $obErro = $obTPessoalAssentamentoGeradoContratoServidor->inclusao( $boTransacao );
                            }
                            if ( !$obErro->ocorreu() ) {
                                $inCodMes = str_pad($request->get('inCodMes'),2,"0",STR_PAD_LEFT);
                                if (Sessao::read("boConcederFeriasLote")) {
                                    $stObservacao = "Férias do lote ".$stNomeLote;
                                } else {
                                    $stObservacao = "Férias de ".$request->get('dtInicial')." a ".$request->get('dtFinal').",pagas em ".$inCodMes."/".$request->get('inAno');
                                }
                                $obTPessoalAssentamentoGerado->setDado( "cod_assentamento_gerado" , $inCodAssentamentoGerado                                  );
                                $obTPessoalAssentamentoGerado->setDado( "cod_assentamento"        , $rsAssentamentoAssentamento->getCampo("cod_assentamento") );
                                $obTPessoalAssentamentoGerado->setDado( "periodo_inicial"         , $request->get('dtInicialFerias')                          );
                                $obTPessoalAssentamentoGerado->setDado( "periodo_final"           , $request->get('dtFinalFerias')                            );
                                $obTPessoalAssentamentoGerado->setDado( "automatico"              , true                                                      );
                                $obTPessoalAssentamentoGerado->setDado( "observacao"              , $stObservacao                                             );
                                $obErro = $obTPessoalAssentamentoGerado->inclusao( $boTransacao );
                            }
                            if ( !$obErro->ocorreu() and !$request->get('boPagamento13') ) {
                                $obFPessoalRegistrarEventoPorAssentamento->setDado("cod_contrato"     , $arContrato["cod_contrato"]                               );
                                $obFPessoalRegistrarEventoPorAssentamento->setDado("cod_assentamento" , $rsAssentamentoAssentamento->getCampo("cod_assentamento") );
                                $obFPessoalRegistrarEventoPorAssentamento->setDado("acao"             , "incluir"                                                 );
                                $obErro = $obFPessoalRegistrarEventoPorAssentamento->registrarEventoPorAssentamento(Sessao::getTransacao()->inTransacao);
                            }
                            if ( $obErro->ocorreu() ) {
                                exit;
                            }
                            $rsAssentamentoAssentamento->proximo();
                        }
                        Sessao::getExcecao()->setDescricao($obErro->getDescricao());
                        ###Assentamento###

                        if (Sessao::read("boConcederFeriasLote")) {
                            $stMensagem = $stNomeLote." cadastradas com sucesso.";
                        } else {
                            $stMensagem = "Férias de ".$request->get('dtInicialFerias')." a ".$request->get('dtFinalFerias');
                        }
                    } else {
                        $stMensagem = "O servidor não tem direito a gozar férias, em virtude do número de faltas ser superior ao limite legal.";
                    }
                }
                unset($obTPessoalFerias);
                unset($obTPessoalLancamentoFerias);
                unset($obFFolhaPagamentoGeraRegistroFerias);
                unset($obTFolhaPagamentoPeriodoMovimentacao);
            }
        } else {
            $stMensagem = "Não há contratos para gerar férias no filtro selecionado para o lote.";
        }

        Sessao::encerraExcecao();
        if (Sessao::read("boConcederFeriasLote")) {
            sistemaLegado::alertaAviso($pgFilt,$stMensagem ,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,$stMensagem ,"incluir","aviso", Sessao::getId(), "../");
        }
    break;
    case "excluir":
        Sessao::setTrataExcecao(true);
        Sessao::getExcecao()->setLocal("telaprincipal");

        $obTPessoalFerias = new TPessoalFerias;
        $obTPessoalLancamentoFerias = new TPessoalLancamentoFerias();
        $obTPessoalLancamentoFerias->obTPessoalFerias = &$obTPessoalFerias;

        $obTPessoalLoteFerias         = new TPessoalLoteFerias();
        $obTPessoalLoteFeriasContrato = new TPessoalLoteFeriasContrato();
        $obTPessoalLoteFeriasOrgao    = new TPessoalLoteFeriasOrgao();
        $obTPessoalLoteFeriasLocal    = new TPessoalLoteFeriasLocal();
        $obTPessoalLoteFeriasFuncao   = new TPessoalLoteFeriasFuncao();
        $obTPessoalLoteFeriasLote     = new TPessoalLoteFeriasLote();
        $obTPessoalLoteFeriasLote->obTPessoalFerias         = &$obTPessoalFerias;
        $obTPessoalLoteFeriasLote->obTPessoalLoteFerias     = &$obTPessoalLoteFerias;
        $obTPessoalLoteFeriasContrato->obTPessoalLoteFerias = &$obTPessoalLoteFerias;
        $obTPessoalLoteFeriasOrgao->obTPessoalLoteFerias    = &$obTPessoalLoteFerias;
        $obTPessoalLoteFeriasLocal->obTPessoalLoteFerias    = &$obTPessoalLoteFerias;
        $obTPessoalLoteFeriasFuncao->obTPessoalLoteFerias   = &$obTPessoalLoteFerias;

        if ($request->get("boConcederFeriasLote")) {
            $obTPessoalLoteFerias->setDado("cod_lote",$request->get("inCodLote"));
            $stFiltro = " AND lote_ferias_lote.cod_lote = ".$request->get("inCodLote");
        } else {
            $obTPessoalLoteFeriasLote->setDado("cod_ferias",$request->get('inCodFerias'));
            $obTPessoalLoteFeriasLote->recuperaPorChave($rsLoteFeriasLote,$boTransacao);

            $stFiltro = " AND lancamento_ferias.cod_ferias = ".$request->get('inCodFerias');
        }
        $obTPessoalLancamentoFerias->recuperaLancamentoFerias($rsLancamentoFerias,$stFiltro);

        while (!$rsLancamentoFerias->eof()) {
            $obTPessoalFerias->setDado("cod_ferias",$rsLancamentoFerias->getCampo("cod_ferias"));
            $obTPessoalFerias->recuperaPorChave($rsFerias,$boTransacao);

            $inCodContrato = $rsLancamentoFerias->getCampo("cod_contrato");

            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
            $stFiltro = "AND to_char(dt_final,'mm/yyyy') = '".$rsLancamentoFerias->getCampo('mes_competencia')."/".$rsLancamentoFerias->getCampo("ano_competencia")."'";
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltro);
            $inCodPeriodoMovimentacao = $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");

            if ($rsLancamentoFerias->getCampo("cod_tipo") == 2 && $inCodPeriodoMovimentacao != "") {
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php");
                $obTFolhaPagamentoFolhaSituacao = new TFolhaPagamentoFolhaSituacao;
                $stFiltro  = " AND folha_situacao.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
                $stFiltro .= " AND folha_situacao.situacao = 'f'";
                $obTFolhaPagamentoFolhaSituacao->recuperaRelacionamento($rsSituacao,$stFiltro);
                if ($rsSituacao->getNumLinhas() >= 1) {
                    Sessao::getExcecao()->setDescricao("Essas férias não podem ser canceladas pois foram concedidas com pagamento em uma folha salário que se encontra fechada.");
                }
            }
            if ($rsLancamentoFerias->getCampo("cod_tipo") == 3 && $inCodPeriodoMovimentacao != "") {
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementar.class.php");
                $obTFolhaPagamentoComplementar = new TFolhaPagamentoComplementar;
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementarSituacao.class.php");
                $obTFolhaPagamentoComplementarSituacao = new TFolhaPagamentoComplementarSituacao();
                $obTFolhaPagamentoComplementarSituacao->recuperaUltimaFolhaComplementarSituacao($rsComplementarSituacao);
                if (!$rsComplementarSituacao->getCampo("situacao") == 'a') {
                    Sessao::getExcecao()->setDescricao("Essas férias não podem ser canceladas pois foram concedidas com pagamento em uma folha complementar que se encontra fechada.");
                }
            }

            ###Assentamento###
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAssentamento.class.php");
            $obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento();
            // Setando data inicial e final para verificar se existem assentamentos gerados para o contrato
            $obTPessoalAssentamentoAssentamento->setDado("dtInicial", $rsPeriodoMovimentacao->getCampo("dt_inicial"));
            $obTPessoalAssentamentoAssentamento->setDado("dtFinal"  , $rsPeriodoMovimentacao->getCampo("dt_final"));
            $stFiltro  = " AND contrato_servidor_previdencia.bo_excluido = false";
            $stFiltro .= " AND assentamento.assentamento_automatico = true";
            $stFiltro .= " AND classificacao_assentamento.cod_tipo = 2";
            $stFiltro .= " AND assentamento_assentamento.cod_motivo = 2";
            $stFiltro .= " AND contrato_servidor_previdencia.cod_contrato = ".$inCodContrato;
            $obTPessoalAssentamentoAssentamento->recuperaRelacionamento($rsAssentamentoAssentamento,$stFiltro);

            include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoContratoServidor.class.php" );
            include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php" );
            include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoExcluido.class.php" );
            include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalRegistrarEventoPorAssentamento.class.php" );
            $obTPessoalAssentamentoGeradoContratoServidor = new TPessoalAssentamentoGeradoContratoServidor;
            $obTPessoalAssentamentoGerado = new TPessoalAssentamentoGerado;
            $obTPessoalAssentamentoGeradoExcluido = new TPessoalAssentamentoGeradoExcluido;
            $obFPessoalRegistrarEventoPorAssentamento = new FPessoalRegistrarEventoPorAssentamento;

            while (!$rsAssentamentoAssentamento->eof()) {
                if ( !$obErro->ocorreu()) {
                    $obFPessoalRegistrarEventoPorAssentamento->setDado("cod_contrato"       ,$inCodContrato);
                    $obFPessoalRegistrarEventoPorAssentamento->setDado("cod_assentamento"   ,$rsAssentamentoAssentamento->getCampo("cod_assentamento"));
                    $obFPessoalRegistrarEventoPorAssentamento->setDado("acao"               ,"excluir");
                    $obErro = $obFPessoalRegistrarEventoPorAssentamento->registrarEventoPorAssentamento();
                }
                if ( !$obErro->ocorreu() ) {
                    if ($rsLancamentoFerias->getCampo('cod_lote') != "") {

                        $obTPessoalLoteFeriasPesquisa = new TPessoalLoteFerias();
                        $obTPessoalLoteFeriasPesquisa->setDado('cod_lote', $rsLancamentoFerias->getCampo('cod_lote'));
                        $obTPessoalLoteFeriasPesquisa->recuperaPorChave($rsLoteFeriasPesquisa,$boTransacao);

                        $stObservacao = "Férias do lote ".$rsLoteFeriasPesquisa->getCampo('nome');
                    } else {
                        $stObservacao = "Férias de ".$rsFerias->getCampo("dt_inicial_aquisitivo")." a ".$rsFerias->getCampo("dt_final_aquisitivo").",pagas em ".$rsLancamentoFerias->getCampo('mes_competencia')."/".$rsLancamentoFerias->getCampo('ano_competencia');
                    }
                    $stFiltro  = " AND cod_contrato = ".$inCodContrato;
                    $stFiltro .= " AND assentamento_gerado.cod_assentamento = ".$rsAssentamentoAssentamento->getCampo("cod_assentamento");
                    $stFiltro .= " AND trim(assentamento_gerado.observacao) = trim('".$stObservacao."')";
                    $obErro = $obTPessoalAssentamentoGerado->recuperaAssentamentoGerado( $rsAssentamentoGerado,$stFiltro,"");
                }
                if ( !$obErro->ocorreu() ) {
                    if ($rsAssentamentoGerado->getNumLinhas() > -1) {
                        $obTPessoalAssentamentoGeradoExcluido->setDado( "cod_assentamento_gerado" , $rsAssentamentoGerado->getCampo("cod_assentamento_gerado") );
                        $obTPessoalAssentamentoGeradoExcluido->setDado( "timestamp" , $rsAssentamentoGerado->getCampo("timestamp") );
                        $obTPessoalAssentamentoGeradoExcluido->setDado( "descricao" , "Exclusão de assentamento gerado por consequencia do cancelamento das férias que geraram o assentamento." );
                        $obErro = $obTPessoalAssentamentoGeradoExcluido->inclusao($boTransacao);
                    }
                }

                if ( $obErro->ocorreu() ) {
                    break;
                }
                $rsAssentamentoAssentamento->proximo();
            }
            Sessao::getExcecao()->setDescricao($obErro->getDescricao());
            ##Assentamento###

            $arExclui = validaExclusaoConcessao($inCodContrato,$inCodPeriodoMovimentacao);
            $stMensagem = "Competência ".$request->get('inMesCompetencia')."/".$request->get('inAnoCompetencia')." - Matrícula ".$request->get('inRegistro')." cancelado com sucesso";
            if ($request->get("boConcederFeriasLote") or $rsLoteFeriasLote->getNumLinhas() == 1) {
                $obTPessoalLoteFeriasLote->exclusao($boTransacao);

                //exclui o contrato do filtro de lote, se houver
                $obTPessoalLoteFeriasContrato->setDado('cod_contrato', $inCodContrato);
                $obTPessoalLoteFeriasContrato->setDado('cod_lote', $obTPessoalLoteFeriasLote->getDado('cod_lote'));
                $obTPessoalLoteFeriasContrato->exclusao($boTransacao);

                if (!$request->get("boConcederFeriasLote")) {
                    //Exclui o lote caso nao existam mais contratos/ferias vinculadas a ele
                    $obTPessoalLoteFeriasLote->recuperaTodos($rsLoteFeriasLoteExcluir, " WHERE cod_lote = ".$rsLoteFeriasLote->getCampo('cod_lote'),"",$boTransacao );
                    if ($rsLoteFeriasLoteExcluir->getNumLinhas() <= 0) {
                        $obTPessoalLoteFerias->setDado('cod_lote', $rsLoteFeriasLote->getCampo('cod_lote'));
                        $obTPessoalLoteFeriasContrato->setDado('cod_contrato', '');
                        $obTPessoalLoteFeriasContrato->exclusao($boTransacao);
                        $obTPessoalLoteFeriasOrgao->exclusao($boTransacao);
                        $obTPessoalLoteFeriasLocal->exclusao($boTransacao);
                        $obTPessoalLoteFeriasFuncao->exclusao($boTransacao);
                        $obTPessoalLoteFerias->exclusao($boTransacao);
                    }
                }
            }

            $obTPessoalLancamentoFerias->exclusao($boTransacao);
            $obTPessoalFerias->exclusao($boTransacao);

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
            $obTPessoalContrato = new TPessoalContrato();
            $stFiltro = " AND contrato.cod_contrato = ".$inCodContrato;
            $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro);

            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDeducaoDependente.class.php");
            $obTFolhaPagamentoDeducaoDependente = new TFolhaPagamentoDeducaoDependente();
            $obTFolhaPagamentoDeducaoDependente->setDado("numcgm",$rsCGM->getCampo("numcgm"));
            $obTFolhaPagamentoDeducaoDependente->setDado("cod_periodo_movimentacao",$inCodPeriodoMovimentacao);
            $obTFolhaPagamentoDeducaoDependente->setDado("cod_tipo",1);
            $obTFolhaPagamentoDeducaoDependente->exclusao($boTransacao);

            if ($arExclui['ferias']) {
                if ($rsPeriodoMovimentacao->getNumLinhas() > 0) {
                    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php");
                    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoParcela.class.php");
                    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
                    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculadoDependente.class.php");
                    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php");
                    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEvento.class.php");

                    $obTFolhaPagamentoRegistroEvento            = new TFolhaPagamentoRegistroEvento();
                    $obTFolhaPagamentoRegistroEventoParcela     = new TFolhaPagamentoRegistroEventoParcela();
                    $obTFolhaPagamentoEventoCalculado           = new TFolhaPagamentoEventoCalculado();
                    $obTFolhaPagamentoEventoCalculadoDependente = new TFolhaPagamentoEventoCalculadoDependente();
                    $obTFolhaPagamentoLogErroCalculo            = new TFolhaPagamentoLogErroCalculo();
                    $obTFolhaPagamentoUltimoRegistroEvento      = new TFolhaPagamentoUltimoRegistroEvento();
                    $obTFolhaPagamentoEventoCalculadoDependente->obTFolhaPagamentoEventoCalculado = &$obTFolhaPagamentoEventoCalculado;
                    $obTFolhaPagamentoEventoCalculado->obTFolhaPagamentoUltimoRegistroEvento = &$obTFolhaPagamentoUltimoRegistroEvento;
                    $obTFolhaPagamentoLogErroCalculo->obTFolhaPagamentoUltimoRegistroEvento = &$obTFolhaPagamentoUltimoRegistroEvento;
                    $obTFolhaPagamentoRegistroEventoParcela->obTFolhaPagamentoUltimoRegistroEvento = &$obTFolhaPagamentoUltimoRegistroEvento;

                    $obTFolhaPagamentoRegistroEventoFerias          = new TFolhaPagamentoRegistroEventoFerias;
                    $obTFolhaPagamentoUltimoRegistroEventoFerias    = new TFolhaPagamentoUltimoRegistroEventoFerias;
                    $obTFolhaPagamentoEventoFeriasCalculadoDependente = new TFolhaPagamentoEventoFeriasCalculadoDependente;
                    $obTFolhaPagamentoEventoFeriasCalculado         = new TFolhaPagamentoEventoFeriasCalculado;
                    $obTFolhaPagamentoRegistroEventoFeriasParcela   = new TFolhaPagamentoRegistroEventoFeriasParcela;
                    $obTFolhaPagamentoLogErroCalculoFerias          = new TFolhaPagamentoLogErroCalculoFerias;
                    $stFiltro  = "   AND cod_contrato =".$inCodContrato;
                    $stFiltro .= "   AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
                    $obTFolhaPagamentoRegistroEventoFerias->recuperaRelacionamento($rsRegistroFerias,$stFiltro);
                    $obTFolhaPagamentoUltimoRegistroEventoFerias->obTFolhaPagamentoRegistroEventoFerias = &$obTFolhaPagamentoRegistroEventoFerias;
                    $obTFolhaPagamentoEventoFeriasCalculado->obTFolhaPagamentoUltimoRegistroEventoFerias = &$obTFolhaPagamentoUltimoRegistroEventoFerias;
                    $obTFolhaPagamentoEventoFeriasCalculadoDependente->obTFolhaPagamentoEventoFeriasCalculado = &$obTFolhaPagamentoEventoFeriasCalculado;
                    $obTFolhaPagamentoRegistroEventoFeriasParcela->obTFolhaPagamentoUltimoRegistroEventoFerias = &$obTFolhaPagamentoUltimoRegistroEventoFerias;
                    $obTFolhaPagamentoLogErroCalculoFerias->obTFolhaPagamentoUltimoRegistroEventoFerias = &$obTFolhaPagamentoUltimoRegistroEventoFerias;
                    while (!$rsRegistroFerias->eof()) {
                        if ($rsRegistroFerias->getCampo("parcela") > 0) {
                            $stFiltro  = "   AND registro_evento_periodo.cod_contrato =".$inCodContrato;
                            $stFiltro .= "   AND registro_evento_periodo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
                            $stFiltro .= "   AND registro_evento.cod_evento = ".$rsRegistroFerias->getCampo("cod_evento");
                            $stFiltro .= "   AND registro_evento.valor = 0";
                            $stFiltro .= "   AND registro_evento.quantidade = 0";
                            $stFiltro .= "   AND registro_evento.proporcional is true";
                            $obTFolhaPagamentoRegistroEvento->recuperaRegistrosEventos($rsRegistrosEventos,$stFiltro);
                            while (!($rsRegistrosEventos->eof())) {
                                $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_registro",$rsRegistrosEventos->getCampo("cod_registro"));
                                $obTFolhaPagamentoEventoCalculadoDependente->exclusao($boTransacao);
                                $obTFolhaPagamentoEventoCalculado->exclusao($boTransacao);
                                $obTFolhaPagamentoLogErroCalculo->exclusao($boTransacao);
                                $obTFolhaPagamentoRegistroEventoParcela->exclusao($boTransacao);
                                $obTFolhaPagamentoUltimoRegistroEvento->exclusao($boTransacao);
                                $rsRegistrosEventos->proximo();
                            }
                        }

                        $obTFolhaPagamentoRegistroEventoFerias->setDado("cod_registro",$rsRegistroFerias->getCampo("cod_registro"));
                        $obTFolhaPagamentoRegistroEventoFerias->setDado("cod_evento"  ,$rsRegistroFerias->getCampo("cod_evento"));
                        $obTFolhaPagamentoRegistroEventoFerias->setDado("desdobramento"  ,$rsRegistroFerias->getCampo("desdobramento"));
                        $obTFolhaPagamentoRegistroEventoFerias->setDado("timestamp"   ,$rsRegistroFerias->getCampo("timestamp"));
                        $obTFolhaPagamentoLogErroCalculoFerias->exclusao($boTransacao);
                        $obTFolhaPagamentoEventoFeriasCalculadoDependente->exclusao($boTransacao);
                        $obTFolhaPagamentoEventoFeriasCalculado->exclusao($boTransacao);
                        $obTFolhaPagamentoRegistroEventoFeriasParcela->exclusao($boTransacao);
                        $obTFolhaPagamentoUltimoRegistroEventoFerias->exclusao($boTransacao);
                        $rsRegistroFerias->proximo();
                    }

                    if ($rsLancamentoFerias->getCampo("cod_tipo") == 2) {
                        #########################################################################
                        #Exclusao dos eventos calculados de ferias incorporados em salario
                        $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
                        $obTFolhaPagamentoEventoCalculadoDependente = new TFolhaPagamentoEventoCalculadoDependente;
                        $obTFolhaPagamentoUltimoRegistroEvento = new TFolhaPagamentoUltimoRegistroEvento;
                        $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento;
                        $obTFolhaPagamentoRegistroEventoPeriodo = new TFolhaPagamentoRegistroEventoPeriodo;
                        $obTFolhaPagamentoLogErroCalculo = new TFolhaPagamentoLogErroCalculo;
                        $stFiltro  = "   AND cod_contrato =".$inCodContrato;
                        $stFiltro .= "   AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
                        $stFiltro .= "   AND desdobramento IN ('F','A','D')";
                        $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventosCalculados,$stFiltro);
                        while (!$rsEventosCalculados->eof()) {
                            $obTFolhaPagamentoLogErroCalculo->setDado("cod_registro",$rsEventosCalculados->getCampo("cod_registro"));
                            $obTFolhaPagamentoEventoCalculadoDependente->setDado("cod_registro",$rsEventosCalculados->getCampo("cod_registro"));
                            $obTFolhaPagamentoEventoCalculado->setDado("cod_registro",$rsEventosCalculados->getCampo("cod_registro"));
                            $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_registro",$rsEventosCalculados->getCampo("cod_registro"));
                            $obTFolhaPagamentoRegistroEvento->setDado("cod_registro",$rsEventosCalculados->getCampo("cod_registro"));
                            $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro",$rsEventosCalculados->getCampo("cod_registro"));

                            $obTFolhaPagamentoEventoCalculadoDependente->exclusao($boTransacao);
                            $obTFolhaPagamentoEventoCalculado->exclusao($boTransacao);
                            $obTFolhaPagamentoLogErroCalculo->exclusao($boTransacao);
                            $obTFolhaPagamentoUltimoRegistroEvento->exclusao($boTransacao);
                            $obTFolhaPagamentoRegistroEvento->exclusao($boTransacao);
                            $obTFolhaPagamentoRegistroEventoPeriodo->exclusao($boTransacao);
                            $rsEventosCalculados->proximo();
                        }
                        #########################################################################
                    }
                    if ($rsLancamentoFerias->getCampo("cod_tipo") == 3) {
                        #########################################################################
                        #Exclusao dos eventos calculados de ferias incorporados em complementar
                        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
                        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoComplementar.class.php");
                        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php");

                        $stFiltro  = " AND complementar_situacao.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
                        $stFiltro .= " AND complementar_situacao.situacao = 'a'";
                        $obTFolhaPagamentoComplementar->recuperaRelacionamento($rsComplementar,$stFiltro);
                        if ($rsComplementar->getNumLinhas() > 0) {
                            $obTFolhaPagamentoEventoComplementarCalculadoDependente = new TFolhaPagamentoEventoComplementarCalculadoDependente;
                            $obTFolhaPagamentoLogErroCalculoComplementar =  new TFolhaPagamentoLogErroCalculoComplementar;
                            $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
                            $obTFolhaPagamentoUltimoRegistroEventoComplementar = new TFolhaPagamentoUltimoRegistroEventoComplementar;
                            $obTFolhaPagamentoRegistroEventoComplementar = new TFolhaPagamentoRegistroEventoComplementar;
                            $stFiltro  = "   AND cod_contrato =".$inCodContrato;
                            $stFiltro .= "   AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
                            $stFiltro .= "   AND cod_complementar = ".$rsComplementar->getCampo("cod_complementar");
                            $stFiltro .= "   AND registro_evento_complementar.cod_configuracao = 2";
                            $obTFolhaPagamentoUltimoRegistroEventoComplementar = new TFolhaPagamentoUltimoRegistroEventoComplementar;
                            $obTFolhaPagamentoUltimoRegistroEventoComplementar->recuperaRelacionamento($rsUltimoRegistroEventoComplementar,$stFiltro);
                            $stFiltro .= "   AND desdobramento != ''";
                            //$stFiltro .= "   AND registro_evento_complementar.cod_configuracao = 2";
                            $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventosCalculados($rsEventosCalculados,$stFiltro);
                            while (!$rsEventosCalculados->eof()) {
                                $obTFolhaPagamentoEventoComplementarCalculado->setDado("cod_registro",$rsEventosCalculados->getCampo("cod_registro"));
                                $obTFolhaPagamentoEventoComplementarCalculadoDependente->setDado("cod_registro",$rsEventosCalculados->getCampo("cod_registro"));
                                $obTFolhaPagamentoLogErroCalculoComplementar->setDado("cod_registro",$rsEventosCalculados->getCampo("cod_registro"));
                                $obTFolhaPagamentoEventoComplementarCalculado->exclusao($boTransacao);
                                $obTFolhaPagamentoEventoComplementarCalculadoDependente->exclusao($boTransacao);
                                $obTFolhaPagamentoLogErroCalculoComplementar->exclusao($boTransacao);
                                $rsEventosCalculados->proximo();
                            }
                            while (!$rsUltimoRegistroEventoComplementar->eof()) {
                                $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_registro",$rsUltimoRegistroEventoComplementar->getCampo("cod_registro"));
                                $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_registro",$rsUltimoRegistroEventoComplementar->getCampo("cod_registro"));
                                $obTFolhaPagamentoUltimoRegistroEventoComplementar->exclusao($boTransacao);
                                $obTFolhaPagamentoRegistroEventoComplementar->exclusao($boTransacao);
                                $rsUltimoRegistroEventoComplementar->proximo();
                            }
                        }
                        #########################################################################
                    }
                }
            }
            if ($arExclui['salario']) {
                $stMensagem .= ", a Folha Salário deve ser recalculada";
                $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
                $obTFolhaPagamentoEventoCalculadoDependente = new TFolhaPagamentoEventoCalculadoDependente();
                $stFiltro  = " AND cod_contrato = ".$inCodContrato;
                $stFiltro .= " AND registro_evento_periodo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
                $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventosSalarioCalculado,$stFiltro);
                while (!$rsEventosSalarioCalculado->eof()) {
                    $obTFolhaPagamentoEventoCalculadoDependente->setDado("cod_registro",$rsEventosSalarioCalculado->getCampo("cod_registro"));
                    $obTFolhaPagamentoEventoCalculadoDependente->exclusao($boTransacao);
                    $obTFolhaPagamentoEventoCalculado->setDado("cod_registro",$rsEventosSalarioCalculado->getCampo("cod_registro"));
                    $obTFolhaPagamentoEventoCalculado->exclusao($boTransacao);
                    $rsEventosSalarioCalculado->proximo();
                }
            }
            if ($arExclui['complementar']) {
                $stMensagem .= ", a Folha Complementar deve ser recalculada";
                $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado();
                $obTFolhaPagamentoEventoComplementarCalculadoDependente = new TFolhaPagamentoEventoComplementarCalculadoDependente();
                $stFiltro  = " AND cod_contrato = ".$inCodContrato;
                $stFiltro .= " AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
                $stFiltro .= " AND cod_complementar = ".$arExclui["cod_complementar"];
                $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventosCalculados($rsEventosComplementaresCalculados,$stFiltro);
                while (!$rsEventosComplementaresCalculados->eof()) {
                    $obTFolhaPagamentoEventoComplementarCalculadoDependente->setDado("cod_registro",$rsEventosComplementaresCalculados->getCampo("cod_registro"));
                    $obTFolhaPagamentoEventoComplementarCalculadoDependente->exclusao($boTransacao);
                    $obTFolhaPagamentoEventoComplementarCalculado->setDado("cod_registro",$rsEventosComplementaresCalculados->getCampo("cod_registro"));
                    $obTFolhaPagamentoEventoComplementarCalculado->exclusao($boTransacao);
                    $rsEventosComplementaresCalculados->proximo();
                }
            }
            $rsLancamentoFerias->proximo();
        }
        if ($request->get("boConcederFeriasLote")) {
            $obTPessoalLoteFeriasContrato->exclusao($boTransacao);
            $obTPessoalLoteFeriasOrgao->exclusao($boTransacao);
            $obTPessoalLoteFeriasLocal->exclusao($boTransacao);
            $obTPessoalLoteFeriasFuncao->exclusao($boTransacao);
            $obTPessoalLoteFerias->exclusao($boTransacao);
            $stMensagem = $request->get("stDescQuestao")." canceladas com sucesso.";
        }

        Sessao::encerraExcecao();
        if ($request->get("boConcederFeriasLote")) {
            sistemaLegado::alertaAviso($pgFilt,$stMensagem."!" ,"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,$stMensagem."!" ,"excluir","aviso", Sessao::getId(), "../");
        }
    break;
}

function validaExclusaoConcessao($inCodContrato,$inCodPeriodoMovimentacao)
{
    $arExclui = array();
    $arExclui['ferias']  = false;
    $arExclui['salario'] = false;
    $arExclui['complementar'] = false;

    if ($inCodPeriodoMovimentacao != "") {
        $obTFolhaPagamentoFolhaSituacao = new TFolhaPagamentoFolhaSituacao();
        $obTFolhaPagamentoFolhaSituacao->recuperaUltimaFolhaSituacao($rsFolhaSituacao);

        $obTFolhaPagamentoComplementarSituacao = new TFolhaPagamentoComplementarSituacao();
        $obTFolhaPagamentoComplementarSituacao->recuperaUltimaFolhaComplementarSituacao($rsComplementarSituacao);

        $obTFolhaPagamentoEventoFeriasCalculado = new TFolhaPagamentoEventoFeriasCalculado();
        $stFiltro  = " AND cod_contrato = ".$inCodContrato;
        $stFiltro .= " AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
        $obTFolhaPagamentoEventoFeriasCalculado->recuperaEventosCalculados($rsEventosFeriasCalculados,$stFiltro);

        //Adicionada validacao para casos em que a folha ferias nao foi calculada.
        if (!$rsEventosFeriasCalculados->getCampo("timestamp")) {
            $obTFolhaPagamentoUltimoRegistroEventoFerias = new TFolhaPagamentoUltimoRegistroEventoFerias;
            $obTFolhaPagamentoUltimoRegistroEventoFerias->recuperaRegistrosEventoFeriasDoContrato($rsUltimoRegistroEvento,$stFiltro,"","");
            $stTimestampFerias = $rsUltimoRegistroEvento->getCampo("timestamp");
        } else {
            $stTimestampFerias = $rsEventosFeriasCalculados->getCampo("timestamp");
        }

        switch (true) {
            case $rsFolhaSituacao->getCampo("situacao") == 'f' and (($rsComplementarSituacao->getCampo('situacao') == 'f' or $rsComplementarSituacao->getNumLinhas() == -1)):
                if ( $rsComplementarSituacao->getNumLinhas() > 0 ) {
                    if( $rsFolhaSituacao->getCampo("timestamp")        < $stTimestampFerias and
                        $rsComplementarSituacao->getCampo("timestamp") < $stTimestampFerias ){
                        $arExclui['ferias'] = true;
                    }
                } else {
                    if ( $rsFolhaSituacao->getCampo("timestamp")        < $stTimestampFerias ) {
                        $arExclui['ferias'] = true;
                    }
                }
                break;
            case $rsFolhaSituacao->getCampo("situacao") == 'a' and $rsComplementarSituacao->getCampo('situacao') == 'a' /*or $rsComplementarSituacao->getNumLinhas() == -1))*/:
                $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
                $stFiltro  = " AND cod_contrato = ".$inCodContrato;
                $stFiltro .= " AND registro_evento_periodo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
                $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventosSalarioCalculado,$stFiltro);
                $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado();

                //Adicionada validacao para casos em que a folha salario nao foi calculada, sendo assim deve-se buscar o timestamp do registro_evento.
                if (!$rsEventosSalarioCalculado->getCampo("timestamp")) {
                    $obTFolhaPagamentoUltimoRegistroEvento = new TFolhaPagamentoUltimoRegistroEvento;
                    $obTFolhaPagamentoUltimoRegistroEvento->recuperaRelacionamento($rsUltimoRegistroEvento,$stFiltro);
                    $stTimestampSalario = $rsUltimoRegistroEvento->getCampo("timestamp");
                } else {
                    $stTimestampSalario = $rsEventosSalarioCalculado->getCampo("timestamp");
                }

                $stFiltro  = " AND cod_contrato = ".$inCodContrato;
                $stFiltro .= " AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
                $stFiltro .= " AND registro_evento_complementar.cod_configuracao = 2";
                $stFiltro .= " AND cod_complementar = ".$rsComplementarSituacao->getCampo("cod_complementar");

                $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventosCalculados($rsEventosComplementaresCalculados,$stFiltro);

                //Adicionada validacao para casos em que a folha complementar nao foi calculada, sendo assim deve-se buscar o timestamp do registro_eventos_complementar.
                if (!$rsEventosComplementaresCalculados->getCampo("timestamp")) {
                    $obTFolhaPagamentoUltimoRegistroEventoComplementar = new TFolhaPagamentoUltimoRegistroEventoComplementar;
                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->recuperaRelacionamento($rsUltimoRegistroEventoComplementar,$stFiltro);
                    $stTimestampComplementar = $rsUltimoRegistroEventoComplementar->getCampo("timestamp");
                } else {
                    $stTimestampComplementar = $rsEventosComplementaresCalculados->getCampo("timestamp");
                }

                if ( $rsEventosComplementaresCalculados->getNumLinhas() > 0  OR $rsUltimoRegistroEventoComplementar->getNumLinhas() > 0) {
                    if ($stTimestampSalario        < $stTimestampFerias and
                        $stTimestampComplementar   < $stTimestampFerias) {
                        $arExclui['ferias']       = true;
                    }
                    if ($stTimestampSalario        > $stTimestampFerias and
                        $stTimestampComplementar   > $stTimestampFerias) {
                        $arExclui['ferias']       = true;
                        $arExclui['salario']      = true;
                        $arExclui['complementar'] = true;
                        $arExclui['cod_complementar'] = $rsComplementarSituacao->getCampo("cod_complementar");
                    }
                    if ($stTimestampSalario        < $stTimestampFerias and
                        $stTimestampComplementar   > $stTimestampFerias) {

                        $arExclui['ferias']       = true;
                        $arExclui['salario']      = true;
                        $arExclui['complementar'] = true;
                        $arExclui['cod_complementar'] = $rsComplementarSituacao->getCampo("cod_complementar");

                    }
                    if ($stTimestampSalario        > $stTimestampFerias and
                        $stTimestampComplementar   < $stTimestampFerias) {
                        $arExclui['ferias']       = true;
                        $arExclui['complementar'] = true;
                        $arExclui['cod_complementar'] = $rsComplementarSituacao->getCampo("cod_complementar");

                    }
                } else {
                    if ($stTimestampSalario        < $stTimestampFerias) {
                        $arExclui['ferias'] = true;
                    }
                    if ($stTimestampSalario        > $stTimestampFerias) {
                        $arExclui['ferias']       = true;
                        $arExclui['salario']      = true;
                    }
                }
                break;
            case $rsFolhaSituacao->getCampo("situacao") == 'a' and ($rsComplementarSituacao->getCampo('situacao') == 'f' or $rsComplementarSituacao->getNumLinhas() == -1) :
                $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
                $stFiltro  = " AND cod_contrato = ".$inCodContrato;
                $stFiltro .= " AND registro_evento_periodo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
                $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventosSalarioCalculado,$stFiltro);

                $stTimestampSalario = $rsEventosSalarioCalculado->getCampo("timestamp");

                if ($stTimestampSalario        < $stTimestampFerias) {
                    $arExclui['ferias'] = true;
                }
                if ($stTimestampSalario        > $stTimestampFerias) {
                    $arExclui['ferias']       = true;
                    $arExclui['salario']      = true;
                }
                break;
            case $rsFolhaSituacao->getCampo("situacao") == 'f' and $rsComplementarSituacao->getCampo('situacao') == 'a' :
                $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado();
                $stFiltro  = " AND cod_contrato = ".$inCodContrato;
                $stFiltro .= " AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
                $stFiltro .= " AND cod_complementar = ".$rsComplementarSituacao->getCampo("cod_complementar");
                $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventosCalculados($rsEventosComplementaresCalculados,$stFiltro);

                $stTimestampComplementar = $rsEventosComplementaresCalculados->getCampo("timestamp");

                if ($stTimestampComplementar   < $stTimestampFerias) {
                    $arExclui['ferias']       = true;
                }
                if ($stTimestampComplementar   > $stTimestampFerias) {
                    $arExclui['ferias']       = true;
                    $arExclui['complementar'] = true;
                    $arExclui['cod_complementar'] = $rsComplementarSituacao->getCampo("cod_complementar");
                }
                break;
        }
    } else {
        $arExclui['ferias'] = true;
    }

    return $arExclui;
}

?>
