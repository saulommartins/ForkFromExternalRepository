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
    * Página de Oculto do Férias
    * Data de Criação: 07/06/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.04.22

    $Id: OCManterCadastroFerias.php 66003 2016-07-06 20:26:49Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php";
include_once CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php";
include_once CAM_GRH_PES_COMPONENTES."ISelectFuncao.class.php";
include_once CAM_GRH_PES_COMPONENTES."IBuscaInnerLotacao.class.php";
include_once CAM_GRH_PES_COMPONENTES."IBuscaInnerLocal.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterCadastroFerias";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function gerarSpan1Form($inPosicao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalLancamentoFerias.class.php");
    $stHtml = "";
    $inCodContrato = $_REQUEST['inCodContrato'];

    $obTPessoalFerias = new TPessoalFerias;
    $stFiltro = " AND cod_contrato = $inCodContrato";
    $stOrdem   = " ORDER BY dt_inicial_aquisitivo,dt_final_aquisitivo";
    $obTPessoalFerias->recuperaRelacionamento($rsFerias,$stFiltro,$stOrdem);

    if ( $rsFerias->getNumLinhas() > 0 and $inPosicao !== 0 ) {
        if ($inPosicao == "") {
            $rsFerias->setUltimoElemento();
        } else {
            $rsFerias->setCorrente( $inPosicao );
        }
        $stPerAquisitoAnterior = $rsFerias->getCampo("dt_inicial_aquisitivo")." a ".$rsFerias->getCampo("dt_final_aquisitivo");
        $obLblPerAquisitoAnterior = new Label ;
        $obLblPerAquisitoAnterior->setRotulo        ( "Período Aquisitivo Anterior"                                         );
        $obLblPerAquisitoAnterior->setName          ( "stPerAquisitoAnterior"                                               );
        $obLblPerAquisitoAnterior->setId            ( "stPerAquisitoAnterior"                                               );
        $obLblPerAquisitoAnterior->setValue         ( $stPerAquisitoAnterior                                                );

        $stFeriasGozadas = $rsFerias->getCampo("dt_inicio")." a ".$rsFerias->getCampo("dt_fim");
        $obLblFeriasGozadas = new Label ;
        $obLblFeriasGozadas->setRotulo              ( "Férias Gozadas em"                                                   );
        $obLblFeriasGozadas->setName                ( "stFeriasGozadas"                                                     );
        $obLblFeriasGozadas->setId                  ( "stFeriasGozadas"                                                     );
        $obLblFeriasGozadas->setValue               ( $stFeriasGozadas                                                      );

        $obFormulario = new Formulario;
        $obFormulario->addTitulo                    ( "Situação Anterior das Férias"                                        );
        $obFormulario->addComponente                ( $obLblPerAquisitoAnterior                                             );
        $obFormulario->addComponente                ( $obLblFeriasGozadas                                                   );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }

    $stJs .= "document.getElementById('spnSpan1').innerHTML = '$stHtml';";

    return $stJs;
}

function gerarSpan2Form($boConsultar=false,$inCodFerias="",$boValidaFolha=true)
{
    include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php" );
    $obDtaDataInicialFerias = new Data;
    $obDtaDataInicialFerias->setRotulo              ( "Data de Início das Férias"                                           );
    $obDtaDataInicialFerias->setName                ( "dtInicialFerias"                                                     );
    $obDtaDataInicialFerias->setValue               ( $dtInicialFerias                                                      );
    $obDtaDataInicialFerias->setTitle               ( "Informe a data de início das férias."                                );
    $obDtaDataInicialFerias->setNull                ( false                                                                 );
    $obDtaDataInicialFerias->obEvento->setOnChange  ( "montaParametrosGET('validarDataInicioFerias','dtInicialFerias,inQuantDiasGozo,inCodContrato');" );

    $obLblDataInicialFerias = new Label;
    $obLblDataInicialFerias->setRotulo              ( "Data de Início das Férias"                                           );
    $obLblDataInicialFerias->setName                ( "dtInicialFerias"                                                     );
    $obLblDataInicialFerias->setId                  ( "dtInicialFerias"                                                     );
    $obLblDataInicialFerias->setValue               ( $_GET['dtInicialFerias']                                              );

    $obLblDataFinalFerias = new Label;
    $obLblDataFinalFerias->setRotulo                ( "Data Término das Férias"                                             );
    $obLblDataFinalFerias->setName                  ( "dtFinalFerias"                                                       );
    $obLblDataFinalFerias->setId                    ( "dtFinalFerias"                                                       );
    $obLblDataFinalFerias->setValue                 ( $_GET['dtFinalFerias']                                                );

    $obHdnDataFinalFerias =  new Hidden;
    $obHdnDataFinalFerias->setName                  ( "dtFinalFerias"                                                       );
    $obHdnDataFinalFerias->setValue                 ( $dtFinalFerias                                                        );

    $obLblDataRetornoFerias = new Label;
    $obLblDataRetornoFerias->setRotulo              ( "Data Prevista de Retorno"                                            );
    $obLblDataRetornoFerias->setName                ( "dtRetornoFerias"                                                     );
    $obLblDataRetornoFerias->setId                  ( "dtRetornoFerias"                                                     );
    $obLblDataRetornoFerias->setValue               ( $_GET['dtRetornoFerias']                                              );

    $obHdnDataRetornoFerias =  new Hidden;
    $obHdnDataRetornoFerias->setName                ( "dtRetornoFerias"                                                     );
    $obHdnDataRetornoFerias->setValue               ( $dtRetornoFerias                                                      );

    $obHdninAno = new Hidden;
    $obHdninAno->setName                            ( "hdninAno"                                                            );
    $obHdninAno->setId                              ( "hdninAno"                                                            );

    $obHdninCodMes = new Hidden;
    $obHdninCodMes->setName                         ( "hdninCodMes"                                                         );
    $obHdninCodMes->setId                           ( "hdninCodMes"                                                         );

    $rsLancamentoFerias = new recordset;
    if (!Sessao::read("boConcederFeriasLote")) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php");
        $obTPessoalFerias = new TPessoalFerias;
        $stFiltro = " AND cod_contrato = ".$_GET['inCodContrato'];
        if ($boConsultar) {
            $stFiltro = " AND ferias.cod_ferias = ".$inCodFerias;
        }
        $obTPessoalFerias->recuperaRelacionamento($rsLancamentoFerias,$stFiltro);
        $rsLancamentoFerias->setUltimoElemento();
        if ( $rsLancamentoFerias->getNumLinhas() > 0 ) {
            $inDia = date("t",$rsLancamentoFerias->getCampo("mes_competencia"));
            $dtCompetenciaAtual = date("d/m/Y",mktime(0,0,0,$rsLancamentoFerias->getCampo("mes_competencia"),$inDia,$rsLancamentoFerias->getCampo("ano_competencia")));
        }
    }
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    $obIFiltroCompetencia = new IFiltroCompetencia(true,$rsPeriodoMovimentacao->getCampo("dt_final"),false);
    $obIFiltroCompetencia->obCmbMes->setTitle("Informe a competência para pagamento.");

    if (Sessao::read('dtCompetencia') != "") {
        $dtCompetencia      = Sessao::read('dtCompetencia');
        $arrMesAno          = explode("/", $dtCompetencia);

        if ($arrMesAno[0] < 10) {
            $arrMesAno[0] = str_replace("0","",$arrMesAno[0]);
        }

        $arrMesAnoCompetenciaAtual = explode("/", $rsPeriodoMovimentacao->getCampo("dt_final"));
        if ($arrMesAnoCompetenciaAtual[1] < 10) {
            $arrMesAnoCompetenciaAtual[1] = str_replace("0","",$arrMesAnoCompetenciaAtual[1]);
        }

        if($arrMesAno[1] > $arrMesAnoCompetenciaAtual[2] ||
           ( $arrMesAno[1] == $arrMesAnoCompetenciaAtual[2] &&
             $arrMesAno[0] >  $arrMesAnoCompetenciaAtual[1] )){
            $obIFiltroCompetencia->obCmbMes->setValue($arrMesAno[0]);
            $obIFiltroCompetencia->obTxtAno->setValue($arrMesAno[1]);
        }
    }

    $obLblCompetencia = new Label;
    $obLblCompetencia->setRotulo                    ( "Competência a ser Pago"                                              );
    $obLblCompetencia->setName                      ( "dtCompetencia"                                                       );
    $obLblCompetencia->setId                        ( "dtCompetencia"                                                       );
    $obLblCompetencia->setValue                     ( $_GET['dtCompetencia']                                                );

    $arCompetencia = explode("/",$_GET['dtCompetencia']);
    $obHdnCodMes = new Hidden;
    $obHdnCodMes->setName                           ( "inCodMes"    );
    $obHdnCodMes->setValue                          ( (int) $arCompetencia[0] );

    $obHdnAno = new Hidden;
    $obHdnAno->setName                              ( "inAno"    );
    $obHdnAno->setValue                             ( $arCompetencia[1] );

    if ($boValidaFolha) {
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoFolha.class.php");
        $obTFolhaPagamentoTipoFolha = new TFolhaPagamentoTipoFolha;
        $obTFolhaPagamentoTipoFolha->recuperaTodos($rsTipoFolha);
        
        $obCmbFolhaPagar = new Select;
        $obCmbFolhaPagar->setRotulo                     ( "Folha em que Será Pago"                                              );
        $obCmbFolhaPagar->setName                       ( "inCodTipo"                                                           );
        $obCmbFolhaPagar->setId                         ( "inCodTipo"                                                           );
        $obCmbFolhaPagar->setValue                      ( $inCodTipo                                                            );
        $obCmbFolhaPagar->setStyle                      ( "width: 200px"                                                        );
        $obCmbFolhaPagar->setCampoID                    ( "cod_tipo"                                                            );
        $obCmbFolhaPagar->setCampoDesc                  ( "descricao"                                                           );
        $obCmbFolhaPagar->addOption                     ( "", "Selecione"                                                       );
        $obCmbFolhaPagar->setTitle                      ( "Informe a folha em que as férias deverá ser paga."                   );
        $obCmbFolhaPagar->setNull                       ( false                                                                 );
        
        $obCmbFolhaPagar->preencheCombo                 ( $rsTipoFolha                                                          );

        $obLblFolhaPagar = new Label;
        $obLblFolhaPagar->setRotulo                     ( "Folha em que Será Pago"                                              );
        $obLblFolhaPagar->setName                       ( "stFolhaPago"                                                         );
        $obLblFolhaPagar->setId                         ( "stFolhaPago"                                                         );
        $obLblFolhaPagar->setValue                      ( $_GET['stFolhaPago']                                                  );

        $obCkbPagamento13 = new Checkbox;
        $obCkbPagamento13->setName                      ( "boPagamento13"                                                       );
        $obCkbPagamento13->setId                        ( "boPagamento13"                                                       );
        $obCkbPagamento13->setValue                     ( true                                                                  );
        $obCkbPagamento13->setChecked                   ( true                                                                  );
        $obCkbPagamento13->setRotulo                    ( "Efetuar Pagamento apenas de 1/3"                                     );
        $obCkbPagamento13->setTitle                     ( "Selecione se deverá ser efetuado o pagamento de apenas 1/3 das férias." );

        $obLblPagamento13 = new Label;
        $obLblPagamento13->setRotulo                    ( "Efetuar Pagamento apenas de 1/3"                                     );
        $obLblPagamento13->setName                      ( "stPagamento13"                                                       );
        $obLblPagamento13->setId                        ( "stPagamento13"                                                       );
        $obLblPagamento13->setValue                     ( $_GET['stPagamento13']                                                );
    }

    if ($boConsultar) {
        if ( $rsLancamentoFerias->getNumLinhas() > 0 ) {
            $obFormulario = new Formulario;
            $obFormulario->addComponente            ( $obLblDataInicialFerias                                               );
            $obFormulario->addComponente            ( $obLblDataFinalFerias                                                 );
            $obFormulario->addHidden                ( $obHdnDataFinalFerias                                                 );
            $obFormulario->addComponente            ( $obLblDataRetornoFerias                                               );
            $obFormulario->addHidden                ( $obHdnDataRetornoFerias                                               );
            $obFormulario->addComponente            ( $obLblCompetencia                                                     );
            $obFormulario->addHidden                ( $obHdnCodMes                                                          );
            $obFormulario->addHidden                ( $obHdnAno                                                             );
            if ($boValidaFolha) {
                $obFormulario->addComponente            ( $obLblFolhaPagar                                                      );
                $obFormulario->addComponente            ( $obLblPagamento13                                                     );
            }
            $obFormulario->montaInnerHTML();
            $obFormulario->obJavaScript->montaJavaScript();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);
            $stHtml = $obFormulario->getHTML();
        }
    } else {
        $obFormulario = new Formulario;
        $obFormulario->agrupaComponentes            ( array($obDtaDataInicialFerias,$obLblDataInicialFerias)                );
        $obFormulario->addComponente                ( $obLblDataFinalFerias                                                 );
        $obFormulario->addHidden                    ( $obHdnDataFinalFerias                                                 );
        $obFormulario->addComponente                ( $obLblDataRetornoFerias                                               );
        $obFormulario->addHidden                    ( $obHdnDataRetornoFerias                                               );
        $obFormulario->addHidden                    ( $obHdninAno                                                           );
        $obFormulario->addHidden                    ( $obHdninCodMes                                                        );
        $obIFiltroCompetencia->geraFormulario       ( $obFormulario                                                         );
        
        if ($boValidaFolha) {
            $obFormulario->addComponente                ( $obCmbFolhaPagar                                                      );
            $obFormulario->addComponente                ( $obCkbPagamento13                                                     );    
        }
        
        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "document.getElementById('spnSpan2').innerHTML = '$stHtml';";
    $stJs .= "f.stEval.value = '$stEval';";

    return $stJs;
}

function gerarSpan3Form()
{
    $obLblMensagem = new Label;
    $obLblMensagem->setId       ( "stMensagem"  );
    $obLblMensagem->setName     ( "stMensagem"  );
    $obLblMensagem->setRotulo   ( "Mensagem"    );
    $obLblMensagem->setValue    ( "O servidor não tem direito a gozar férias, em virtude do número de faltas ser superior ao limite legal." );

    $obFormulario = new Formulario;
    $obFormulario->addComponente($obLblMensagem);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    $stJs .= "document.getElementById('spnSpan2').innerHTML = '$stHtml';";

    return $stJs;
}

function processarForm()
{
    $stJs .= gerarSpan1Form();

    return $stJs;
}

function limparForm()
{
    $stJs .= "if (f.inQuantFaltas) { f.inQuantFaltas.value = ''; }               \n";
    $stJs .= "f.inCodFormaPagamento.value = '';                                  \n";
    $stJs .= "f.inQuantDiasGozo.value = '';                                      \n";
    $stJs .= "d.getElementById('spnSpan2').innerHTML = '';                       \n";
    $stJs .= "if (f.dtInicial) { f.dtInicial.value = '".Sessao::read('dtInicial')."'; }\n";
    $stJs .= "if (f.dtFinal) { f.dtFinal.value   = '".Sessao::read('dtFinal')."'; }    \n";
    $stJs .= "if ( d.getElementById('dtFinal') ) { d.getElementById('dtFinal').innerHTML = '".Sessao::read('dtFinal')."'; } \n";

    return $stJs;
}

function processarConsulta()
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php");
    $obTPessoalFerias = new TPessoalFerias;
    $stFiltro  = " AND cod_contrato = ".$_REQUEST['inCodContrato'];
    $stOrdem   = " ORDER BY dt_inicial_aquisitivo,dt_final_aquisitivo";
    $obTPessoalFerias->recuperaRelacionamento($rsFerias,$stFiltro,$stOrdem);
    while (!$rsFerias->eof()) {
        if ( $rsFerias->getCampo("cod_ferias") == $_REQUEST['inCodFerias'] ) {
            $inPosicao = $rsFerias->getCorrente()-1;
            break;
        }
        $rsFerias->proximo();
    }
    $stJs .= gerarSpan1Form($inPosicao);
    $stJs .= gerarSpan2Form(true,$rsFerias->getCampo("cod_ferias"));

    return $stJs;
}

/************* INICIO - Quantidade de avós para cálculo de dias de gozo de férias *********************/
function validarPeriodoAquisitivo()
{
    $obErro = new Erro;

    if (trim($_GET["dtInicial"])!="" && trim($_GET["dtFinal"])!="") {
        // recupera ultimo periodo aquisitivo de férias
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php");
        $stFiltro  = " WHERE cod_contrato = ".$_GET["inCodContrato"];
        $obTPessoalFerias = new TPessoalFerias();
        $obTPessoalFerias->recuperaTodos($rsFerias, $stFiltro, " ORDER BY dt_final_aquisitivo DESC limit 1");
        
        list($inDia1,$inMes1,$inAno1) = explode("/",$_GET["dtInicial"]);
        list($inDia2,$inMes2,$inAno2) = explode("/",$_GET["dtFinal"]);
        list($inDia3,$inMes3,$inAno3) = explode("/",$rsFerias->getCampo("dt_final_aquisitivo"));

        $nuDtInicial = $inAno1.$inMes1.$inDia1;
        $nuDtFinal   = $inAno2.$inMes2.$inDia2;
        $nuDtFinalUltimaAquisicao = $inAno3.$inMes3.$inDia3;

        if (trim($_GET["dtInicial"]) == trim($_GET["dtFinal"])) {
            $obErro->setDescricao("@O campo Data Final do Período Aquisitivo(".$_GET["dtFinal"].") deve ser maior do que a Data Inicial(".$_GET["dtInicial"].").");
        } else {
            if ( $rsFerias->getNumLinhas() != -1 ) {
                if ( !validaDatasInicialFinalPeriodoAquisitivo($_GET["dtInicial"], $_GET["dtFinal"],$stJs) ) 
                $obErro->setDescricao("@O Campo Data Inicial do Período Aquisitivo(".$_GET["dtInicial"].") não pode ser maior que a data final do período anterior(".$rsFerias->getCampo("dt_final_aquisitivo").").");
            } else {
                if ($nuDtInicial > $nuDtFinal) {
                    $obErro->setDescricao("@O Campo Data Inicial do Período Aquisitivo(".$_GET["dtInicial"].") não pode ser maior que a Data Final(".$_GET["dtFinal"].").");
                } else {
                    if (compreende_fevereiro($_GET["dtInicial"], $_GET["dtFinal"])) {
                        if (abs(days_between($_GET["dtInicial"], $_GET["dtFinal"])) > 367) {
                            $obErro->setDescricao("@O Período Aquisitivo informado é maior do que 1 ano.");
                        }
                    } else {
                        if (abs(days_between($_GET["dtInicial"], $_GET["dtFinal"])) > 365) {
                            $obErro->setDescricao("@O Período Aquisitivo informado é maior do que 1 ano.");
                        }
                    }
                }
            }
        }
    } else {
        $obErro->setDescricao("@O campo Data Inicial e Data Final do Período Aquisitivo devem ser informados.");
    }

    return $obErro;
}

function recuperaQtdDiasParaGozoFerias($dtInicial, $dtFinal)
{
    $inQtdMesesTercoFerias = 0;
    $inMes1 = 0;
    $primeiraVez = false;

    list($inDia1, $inMes1, $inAno1) = explode("/", $dtInicial);
    list($inDia2, $inMes2, $inAno2) = explode("/", $dtFinal);
    $inMeses = months_between($dtInicial, $dtFinal);

    $inDt1 = $inAno1.$inMes1;
    $inDt2 = $inAno2.$inMes2;

    $inMes = $inMes1;
    $inAno = $inAno1;

    $inDiaInicial = $inDia1;
    $dataFinal    =  $dtInicial;

    while (days_between($dtFinal, $dataFinal) > 0) {
        $inQtdMesesTercoFerias++;
        $dataIni = $inDiaInicial."/".str_pad($inMes,2,"0", STR_PAD_LEFT)."/".$inAno;

        if ($primeiraVez === false) {
            $primeiraVez = true;
        } else {
            $dataIni = $dataIniProx;
        }

        $inMes++;
        if ($inMes > 12) {
            $inMes = 1;
            $inAno++;
        }

        $dataFinal = somar_dias($dataIni,30);
        if (implode("-", array_reverse(explode("/",$dataFinal))) > implode("-", array_reverse(explode("/",$dtFinal)))) {
            $dataFinal = $dtFinal;
        }

        if ($primeiraVez === true) {
            $str_data_final = implode("-", array_reverse(explode("/",$dataFinal)));
            $dataIniProx = gmdate('d/m/Y',strtotime('+1 day',strtotime($str_data_final)));
        }
        $inDt1 = $inAno.str_pad($inMes,2,"0", STR_PAD_LEFT);
    }

    if (abs(days_between($dataIni, $dataFinal)) < 15) {
        $inQtdMesesTercoFerias = $inQtdMesesTercoFerias - 1;
    }

    return $inQtdMesesTercoFerias;
}

function days_between($dtInicial, $dtFinal)
{
    list($dia1, $mes1, $ano1) = explode("/", $dtInicial);
    list($dia2, $mes2, $ano2) = explode("/", $dtFinal);

    //calculo timestam das duas datas
    $timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
    $timestamp2 = mktime(4,12,0,$mes2,$dia2,$ano2);

    //diminuo a uma data a outra
    $segundos_diferenca = $timestamp1 - $timestamp2;
    //echo $segundos_diferenca;

    //converto segundos em dias
    $dias_diferenca = $segundos_diferenca / (60 * 60 * 24);

    //obtenho o valor absoluto dos dias (tiro o possível sinal negativo)
    //$dias_diferenca = abs($dias_diferenca);

    //tiro os decimais aos dias de diferenca
    $dias_diferenca = floor($dias_diferenca);

    return $dias_diferenca;
}

function months_between($dtInicial, $dtFinal)
{
    $inTotal = 0;

    list($inDia1, $inMes1, $inAno1) = explode("/", $dtInicial);
    list($inDia2, $inMes2, $inAno2) = explode("/", $dtFinal);

    $inDt1 = $inAno1.$inMes1;
    $inDt2 = $inAno2.$inMes2;

    while ($inDt1 < $inDt2) {
        $inTotal++;
        $inMes1++;

        if ($inMes1 > 12) {
            $inMes1 = 1;
            $inAno1++;
        }
        $inDt1 = $inAno1.str_pad($inMes1,2,"0", STR_PAD_LEFT);
    }

    return $inTotal;
}

function compreende_fevereiro($dtInicial, $dtFinal)
{
    $boRetorno = false;

    list($inDia1, $inMes1, $inAno1) = explode("/", $dtInicial);
    list($inDia2, $inMes2, $inAno2) = explode("/", $dtFinal);

    $inDt1 = $inAno1.$inMes1;
    $inDt2 = $inAno2.$inMes2;

    while ($inDt1 < $inDt2) {
        if ($inMes1 == 2 && is_bissexto($inAno1)) {
            $boRetorno = true;
            break;
        }

        $inMes1++;
        if ($inMes1 > 12) {
            $inMes1 = 1;
            $inAno1++;
        }
        $inDt1 = $inAno1.str_pad($inMes1,2,"0", STR_PAD_LEFT);
    }

    if ($inMes1 == 2 && is_bissexto($inAno1)) {
        $boRetorno = true;
    }

    return $boRetorno;
}

function somar_dias($str_data,$int_qtd_dias_somar = 7)
{
    // Caso seja informado uma data do MySQL do tipo DATETIME - aaaa-mm-dd 00:00:00
    // Transforma para DATE - aaaa-mm-dd
    $str_data = substr($str_data,0,10);

    // Se a data estiver no formato brasileiro: dd/mm/aaaa
    // Converte-a para o padrão americano: aaaa-mm-dd
    if ( preg_match("@/@",$str_data) == 1 ) {
        $str_data = implode("-", array_reverse(explode("/",$str_data)));
    }

    $array_data = explode('-', $str_data);
    $count_days = 0;
    $int_qtd_dias_uteis = 0;

    while ($int_qtd_dias_uteis < $int_qtd_dias_somar) {
        $count_days++;
        $int_qtd_dias_uteis++;
    }

    return gmdate('d/m/Y',strtotime('+'.$count_days.' day',strtotime($str_data)));
}

function is_bissexto($ano)
{
    return ((($ano%4)==0 && ($ano%100)!=0 ) || ($ano%400)==0);
}
/************* FIM - Quantidade de avós para cálculo de dias de gozo de férias *********************/

function processarListaLote()
{
    $stJs = "jQuery('#boTodos').attr('checked','checked')";

    return $stJs;
}

function gerarQuantDiasGozoAbono($inQuantFaltas,$dtInicial,$dtFinal,$inCodFormaPagamento)
{
    $inFeriasProporcionais = recuperaQtdDiasParaGozoFerias($dtInicial, $dtFinal);

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalFormaPagamentoFerias.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalConfiguracaoFerias.class.php");
    $obTPessoalFormaPagamentoFerias = new TPessoalFormaPagamentoFerias;
    $obTPessoalConfiguracaoFerias   = new TPessoalConfiguracaoFerias;
    $obTPessoalFormaPagamentoFerias->setDado("cod_forma",$inCodFormaPagamento);
    $obTPessoalFormaPagamentoFerias->recuperaPorChave($rsFormas);

    $stFiltro  = " WHERE ".$inQuantFaltas." BETWEEN faltas_inicial and faltas_final";
    $stFiltro .= "   AND ferias_proporcionais = ".$inFeriasProporcionais;
    $obTPessoalConfiguracaoFerias->recuperaTodos($rsConfiguracaoFerias,$stFiltro);

    $inQuantDiasGozo    = $rsFormas->getCampo("dias");
    $inQuantDiasAbono   = $rsFormas->getCampo("abono");
    $dtCompetencia      = Sessao::read('dtCompetencia');
    if ($rsConfiguracaoFerias->getNumLinhas() == 1) {
        $inQuantDiasGozo  = round($inQuantDiasGozo*$rsConfiguracaoFerias->getCampo("dias_gozo")/30);
        $inQuantDiasAbono = round(round($rsConfiguracaoFerias->getCampo("dias_gozo")) - $inQuantDiasGozo, 2);
        $inQuantDiasAbono = ( $inCodFormaPagamento == 2 ) ? $inQuantDiasAbono : 0;
    }
    $inQuantDiasGozo  = ( $inQuantDiasGozo < 0 ) ? 0 : $inQuantDiasGozo;

    return array($inQuantDiasGozo,$inQuantDiasAbono);
}

function preencherQuantDiasGozo()
{
    $obErro = new erro;
    if (Sessao::read("boConcederFeriasLote") == FALSE) {
        $obErro = validarPeriodoAquisitivo();
    }

    if (!$obErro->ocorreu()) {
        if ($_REQUEST['inCodFormaPagamento'] != "") {
            if ($_REQUEST['inQuantFaltas']=="") {
                $inQuantFaltas=0;
            } else {
                $inQuantFaltas = $_REQUEST['inQuantFaltas'];
            }
            if (Sessao::read("boConcederFeriasLote") == FALSE) {
                $inFeriasProporcionais = recuperaQtdDiasParaGozoFerias($_REQUEST["dtInicial"], $_REQUEST["dtFinal"]);
            }

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalFormaPagamentoFerias.class.php");
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalConfiguracaoFerias.class.php");
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php");
            $obTPessoalFerias = new TPessoalFerias;
            $obTPessoalFormaPagamentoFerias = new TPessoalFormaPagamentoFerias;
            $obTPessoalConfiguracaoFerias   = new TPessoalConfiguracaoFerias;
            $obTPessoalFormaPagamentoFerias->setDado("cod_forma",$_REQUEST['inCodFormaPagamento']);
            $obTPessoalFormaPagamentoFerias->recuperaPorChave($rsFormas);
            
            if (Sessao::read("boConcederFeriasLote") == FALSE) {
                $stFiltro  = " WHERE ".$inQuantFaltas." BETWEEN faltas_inicial and faltas_final";
                $stFiltro .= "   AND ferias_proporcionais = ".$inFeriasProporcionais;
            }
            $obTPessoalConfiguracaoFerias->recuperaTodos($rsConfiguracaoFerias,$stFiltro);

            $inQuantDiasGozo    = $rsFormas->getCampo("dias");
            $inQuantDiasAbono   = $rsFormas->getCampo("abono");
            $dtCompetencia      = Sessao::read('dtCompetencia');

            if ( $inQuantFaltas >= $rsConfiguracaoFerias->getCampo("faltas_inicial") and $inQuantFaltas <= $rsConfiguracaoFerias->getCampo("faltas_final") and $inQuantDiasGozo != 10 and $inQuantDiasGozo != 15 ) {
                $inQuantDiasGozo  = round($inQuantDiasGozo*$rsConfiguracaoFerias->getCampo("dias_gozo")/30);
                $inQuantDiasAbono = round(round($rsConfiguracaoFerias->getCampo("dias_gozo")) - $inQuantDiasGozo, 2);
            } elseif ($inQuantDiasGozo == 10) {
                $inQuantDiasGozo    = 10;
                $inQuantDiasAbono   = 0;
            } elseif ($inQuantDiasGozo == 15) {
                $inQuantDiasGozo    = 15;
                $inQuantDiasAbono   = 0;
            }

            $inQuantDiasGozo  = ( $inQuantDiasGozo < 0 ) ? 0 : $inQuantDiasGozo;
            Sessao::write('inQuantDiasGozo', $inQuantDiasGozo);
            $stJs .= "d.getElementById('inQuantDiasGozo').disabled = '';                        \n";
            $stJs .= "f.inQuantDiasGozo.value = '".$inQuantDiasGozo."';                         \n";
            $stJs .= "d.getElementById('inQuantDiasAbono').innerHTML = '".$inQuantDiasAbono."'; \n";
            $stJs .= "f.inQuantDiasAbono.value = '".$inQuantDiasAbono."';                       \n";
            if ($inQuantDiasGozo > 0) {
                /*  Validação para não realizar o pagamento mais de 1 vez quando a forma for...
                cod_forma 3 (3 periodos de 10 dias)
                ou
                cod_forma 4 (2 periodos de 15 dias)
                */

                // Necessário carregar da sessão e testar se existe no request, pois quando vai Conceder Férias não usa o request
                $arContratos = Sessao::read("arContratos");

                $stFiltroAux = "";
                if (!empty($arContratos)){
                    foreach ($arContratos as $campo) {
                        if ($campo['cod_contrato'] != "") {
                            $stFiltroAux .= "".$campo["cod_contrato"].",";
                        }
                    }
                }

                if ($stFiltroAux != "") {
                    $stFiltroFerias .= " AND cod_contrato IN (".substr($stFiltroAux,0,-1).") \n";
                }

                if (($_REQUEST['dtInicial'] != "") && ($_REQUEST['dtFinal'] != "")) {
                    $stFiltroFerias .= " AND ferias.dt_inicial_aquisitivo = TO_DATE('".$_REQUEST['dtInicial']."','dd/mm/yyyy') \n";
                    $stFiltroFerias .= " AND ferias.dt_final_aquisitivo = TO_DATE('".$_REQUEST['dtFinal']."','dd/mm/yyyy') \n";
                }

                $stFiltroFerias .= "AND ferias.cod_forma = ".$_REQUEST['inCodFormaPagamento']." \n";
                if (Sessao::read("boConcederFeriasLote") == FALSE) {
                    $obTPessoalFerias->recuperaRelacionamento($rsLancamentoFerias,$stFiltroFerias," ORDER BY ferias.cod_ferias",$boTransacao);
                }else{
                    $rsLancamentoFerias = new RecordSet();
                }

                if ($rsLancamentoFerias->getNumLinhas() > 0){
                    $stJs .= gerarSpan2Form(false,"",false);
                    
                    //Atribui os valores para o hidden e desabilita os campos para o usuario
                    $stJs .= " jQuery('#hdninAno').val(jQuery('#inAno').val()); ";
                    $stJs .= " jQuery('#hdninCodMes').val(jQuery('#inCodMes').val()); ";

                    if ($_REQUEST['inCodFormaPagamento'] == 3 || $_REQUEST['inCodFormaPagamento'] == 4) {
                        $stJs .= " jQuery('#inAno').prop('disabled',true); ";
                        $stJs .= " jQuery('#inCodMes').prop('disabled',true); ";
                    }
                
                }else{
                    $stJs .= gerarSpan2Form(false,"",true);
                }

                if ($_REQUEST['dtInicialFerias'] != "") {
                    $stJs .= validarDataInicioFerias($inQuantDiasGozo);
                }
            
            } else {
                $stJs .= gerarSpan3Form();
            }
        } else {
            $stJs = "f.inQuantDiasGozo.value = '';                         \n";
            $stJs .= "document.getElementById('spnSpan2').innerHTML = '';   \n";
            Sessao::remove('inQuantDiasGozo');
        }
    } else {
        $stJs .= "jQuery('#dtInicial').val('');          \n";
        $stJs .= "jQuery('#dtFinal').val('');            \n";
        $stJs .= "jQuery('#inQuantDiasGozo').val('');    \n";
        $stJs .= "jQuery('#inQuantDiasAbono').html('');  \n";
        $stJs .= "jQuery('#inQuantDiasAbono').val('');   \n";
        $stJs .= "jQuery('#spnSpan2').html('');          \n";
        $stJs .= "jQuery('#inQuantFaltas').val('');      \n";
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');  \n";
    }

    return $stJs;
}

function validarDataInicioFerias($inQuantDiasGozo="")
{
    $obErro = new Erro;
    $inQuantDiasGozo = ( $inQuantDiasGozo != "" ) ? $inQuantDiasGozo : $_GET['inQuantDiasGozo'];
    if ($inQuantDiasGozo > 0 and $_REQUEST['dtInicialFerias'] != "") {
        if ( $inQuantDiasGozo > Sessao::read('inQuantDiasGozo') ) {
            $stJs .= "f.inQuantDiasGozo.value = '".Sessao::read('inQuantDiasGozo')."';\n";
            $inQuantDiasGozo = Sessao::read('inQuantDiasGozo');
            if ( !$obErro->ocorreu() and $inQuantDiasGozo == 0 ) {
                $obErro->setDescricao("O servidor não tem direito a gozar férias, em virtude do número de faltas ser superior ao limite legal.");
            }
            if ( !$obErro->ocorreu() and $inQuantDiasGozo > 0) {
                $obErro->setDescricao("@Campo Quantidade de Dias de Gozo inválido!(o valor não pode ser mair que $inQuantDiasGozo)");
            }
        }
        include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php" );
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php"                  );
        $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
        $obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
        $dtInicialMovimentacao = $rsUltimaMovimentacao->getCampo('dt_final');
        $arInicialMovimentacao = explode("/",$dtInicialMovimentacao);
        $dtInicialMovimentacao = "01/".$arInicialMovimentacao[1]."/".$arInicialMovimentacao[2];
        $dtInicioFerias = $_REQUEST['dtInicialFerias'];
        $arInicioFerias = explode("/",$dtInicioFerias);

        //Verificar Assentamento do tipo 2 (Afastamento) antes das ferias
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php" );
        $obTPessoalAssentamentoGerado = new TPessoalAssentamentoGerado;

        if (isset($_REQUEST['inCodContrato']) && !empty($_REQUEST['inCodContrato'])) {
            $stFiltroAssentamento = " AND contrato.cod_contrato = " . $_REQUEST['inCodContrato'];

            $obErro = $obTPessoalAssentamentoGerado->recuperaMaxAssentamentoGerado($rsAssentamentoGerado, $stFiltroAssentamento, $boTransacao);
            if (!$obErro->ocorreu() && $rsAssentamentoGerado->getNumLinhas() > 0) {
                $stDtFinalAssentamentoAfastamento = SistemaLegado::dataToBr($rsAssentamentoGerado->getCampo('periodo_final'));
                //Caso a data de ferias for anterior que o periodo de retorno emite erro
                if (SistemaLegado::comparaDatas($stDtFinalAssentamentoAfastamento, $dtInicioFerias)) {
                    $obErro->setDescricao("@Campo Data de Início das Férias inválido!(" . $dtInicioFerias . " deve ser maior que a data de retorno do afastamento em " . $stDtFinalAssentamentoAfastamento . ")");
                }
            }
        }

        if (!Sessao::read("boConcederFeriasLote")) {
            $obTPessoalFerias = new TPessoalFerias;
            $stFiltro = " AND cod_contrato = ".$_REQUEST['inCodContrato'];
            $obTPessoalFerias->recuperaRelacionamento($rsFerias,$stFiltro);            
            $rsFerias->setUltimoElemento();
            $dtFinalUltimasFerias = $rsFerias->getCampo("dt_fim");
            if ( !$obErro->ocorreu() and $dtInicioFerias != "" and sistemaLegado::comparaDatas($dtFinalUltimasFerias,$dtInicioFerias) ) {
                $obErro->setDescricao("@Campo Data de Início das Férias inválido!(".$dtInicioFerias ." deve ser maior que ".$dtFinalUltimasFerias.")");
            }
            if ( !$obErro->ocorreu() and $dtInicioFerias != "" and $dtFinalUltimasFerias == $dtInicioFerias ) {
                $obErro->setDescricao("@Campo Data de Início das Férias inválido!(".$dtInicioFerias ." deve ser maior que ".$dtFinalUltimasFerias.")");
            }
        }
        if ( !$obErro->ocorreu() and $dtInicioFerias != "" and sistemaLegado::comparaDatas($dtInicialMovimentacao,$dtInicioFerias) ) {
            $obErro->setDescricao("@Campo Data de Início das Férias inválido!(".$dtInicioFerias ." deve ser maior que ".$dtInicialMovimentacao.")");
        }

        $boInicioFeriasFimDeSemana = false;
        if ( $dtInicioFerias != "" and date('D',mktime(0,0,0,$arInicioFerias[1],$arInicioFerias[0],$arInicioFerias[2])) == "Sun" ) {
            $stJs .= "confirmPopUp('Atenção!','A data ".$dtInicioFerias." é um Domingo! Deseja continuar a cadastrar as ferias?','');";
            $boInicioFeriasFimDeSemana = true;
        }
        if ( $dtInicioFerias != "" and date('D',mktime(0,0,0,$arInicioFerias[1],$arInicioFerias[0],$arInicioFerias[2])) == "Sat" ) {
             $stJs .= "confirmPopUp('Atenção!','A data ".$dtInicioFerias." é um Sábado! Deseja continuar a cadastrar as ferias?','');";
             $boInicioFeriasFimDeSemana = true;
        }

        //se a data inicio das ferias for no sabado ou domingo,
        //muda o valor do botão da confirmPopUp para limpar determinados campos
        if ($boInicioFeriasFimDeSemana) {
            $stJs .= "
                    d.getElementById('btPopUpSim').onclick = function () {
                        f.inAno.focus();
                        removeConfirmPopUp();
                    };
                    d.getElementById('btPopUpNao').onclick = function () {
                            f.dtInicialFerias.value = '';
                            f.dtFinalFerias.value = '';
                            f.dtRetornoFerias.value = '';
                            d.getElementById('dtFinalFerias').innerHTML = '';
                            d.getElementById('dtInicialFerias').innerHTML = '';
                            d.getElementById('dtRetornoFerias').innerHTML = '';
                            f.dtInicialFerias.focus();
                            removeConfirmPopUp();
                    };\n";
        }

        if ( !$obErro->ocorreu() ) {
            $arSemana  = array(0=>"Domingo",1=>"Segunda-feira",2=>"Terça-feira",3=>"Quarta-feira",4=>"Quinta-feira",5=>"Sexta-feira",6=>"Sábado");
            $inIncremento = 0;
            do {
                $dtRetornoFerias = date('d/m/Y',mktime(0,0,0,$arInicioFerias[1],$arInicioFerias[0]+$inQuantDiasGozo+$inIncremento,$arInicioFerias[2]));
                $arRetornoFerias = explode("/",$dtRetornoFerias);
                $inRetorno = date('w',mktime(0,0,0,$arRetornoFerias[1],$arRetornoFerias[0],$arRetornoFerias[2]));
                $inIncremento++;
            } while ( $arSemana[$inRetorno] == "Domingo" or $arSemana[$inRetorno] == "Sábado" );
            $dtFinalFerias   = date('d/m/Y',mktime(0,0,0,$arInicioFerias[1],$arInicioFerias[0]+$inQuantDiasGozo-1,$arInicioFerias[2]));
            $arFinalFerias   = explode("/",$dtFinalFerias);
            $inInicial = date('w',mktime(0,0,0,$arInicioFerias[1],$arInicioFerias[0],$arInicioFerias[2]));
            $inFinal   = date('w',mktime(0,0,0,$arFinalFerias[1],$arFinalFerias[0],$arFinalFerias[2]));
            $stJs .= "f.dtInicialFerias.value = '$dtInicioFerias';                                                                                              \n";
            $stJs .= "d.getElementById('dtRetornoFerias').innerHTML = '$dtRetornoFerias  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ".$arSemana[$inRetorno]."';     \n";
            $stJs .= "d.getElementById('dtFinalFerias').innerHTML = '$dtFinalFerias  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ".$arSemana[$inFinal]."';           \n";
            $stJs .= "d.getElementById('dtInicialFerias').innerHTML = '&nbsp; - ".$arSemana[$inInicial]."';                                                     \n";
            $stJs .= "f.dtRetornoFerias.value = '$dtRetornoFerias';                                                                                             \n";
            $stJs .= "f.dtFinalFerias.value = '$dtFinalFerias';                                                                                                 \n";
        } else {
            if ($inQuantDiasGozo > 0) {
               $stJs .= limparDatas();
            }
            $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');  \n";
        }
    } else {
        $stJs .= limparDatas();
    }

    return $stJs;
}

function limparDatas()
{
    $stJs .= "f.dtInicialFerias.value = '';                         \n";
    $stJs .= "f.dtFinalFerias.value = '';                           \n";
    $stJs .= "f.dtRetornoFerias.value = '';  						\n";
    $stJs .= "d.getElementById('dtFinalFerias').innerHTML = '';     \n";
    $stJs .= "d.getElementById('dtInicialFerias').innerHTML = '';   \n";
    $stJs .= "d.getElementById('dtRetornoFerias').innerHTML = '';   \n";

    return $stJs;
}

function validaDataInicialPeriodoAquisitivo($stData1,&$stJs)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPrimeiraMovimentacao($rsPeriodoMovimentacao);

    list( $dia1,$mes1,$ano1 ) = explode( '/', $stData1 );
    list( $dia2,$mes2,$ano2 ) = explode( '/', $rsPeriodoMovimentacao->getCampo("dt_inicial") );
    
    if ("$ano1$mes1$dia1" >= "$ano2$mes2$dia2") {
        $stJs .= "f.dtInicial.value = '".$stData1."'; \n";
        
        return true;
    } else {
        $stMensagem = "A Data Inicial deve ser maior ou igual a data inicial do primeiro período aquisitivo aberto em ".$rsPeriodoMovimentacao->getCampo('dt_inicial');
        $stJs .= "f.dtInicial.focus();                                          \n";
        $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');   \n";
        $stJs .= "f.dtInicial.value = '';                                       \n";
        $stJs .= "d.getElementById('dtFinal').innerHTML = '';                   \n";
        $stJs .= "f.dtFinal.value = '';                                         \n";
        
        return false;
    }
}

function preencherDataFinal()
{
    $dtFinal = "";
    $dtIncial = $_REQUEST['dtInicial'];
    if ( validaDataInicialPeriodoAquisitivo($dtIncial,$stJs) ) {
        if ($_REQUEST['dtInicial'] != "") {
            $arDataInical = explode("/",$dtIncial);
            $dtFinal = date('d/m/Y',mktime(0,0,0,$arDataInical[1],$arDataInical[0]-1,$arDataInical[2]+1));
            validaDatasInicialFinalPeriodoAquisitivo($dtIncial, $dtFinal, $stJs);

        }
    }
    $stJs .= "d.getElementById('dtFinal').innerHTML = '$dtFinal';   \n";
    $stJs .= "f.dtFinal.value = '$dtFinal';                         \n";

    return $stJs;
}

function validaDatasInicialFinalPeriodoAquisitivo($stDataIncial, $stDataFinal, &$stJs)
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php");
    $rsPeriodoAquisitivo = new RecordSet();
    $obTPessoalFerias = new TPessoalFerias();
    $obTPessoalFerias->setDado("cod_contrato_aquisitivo", $_REQUEST["inCodContrato"]);
    $obTPessoalFerias->setDado("dt_inicial_aquisitivo", $stDataIncial);
    $obTPessoalFerias->setDado("dt_final_aquisitivo", $stDataFinal);
    $obTPessoalFerias->verificaDatasPeriodoAquisitivo($rsPeriodoAquisitivo,"","",$boTransacao);
    
    if($rsPeriodoAquisitivo->getCampo('dias_ferias') == 10) {
        $numDias=0;
        
        while(!$rsPeriodoAquisitivo->eof()) {
            $numDias+= $rsPeriodoAquisitivo->getCampo('dias_ferias');
            $rsPeriodoAquisitivo->proximo();
        }
        
        if($numDias < 30) {
            return true;
        } else {
            $stJs .= "alertaAviso('Este servidor já retirou os 30 dias de férias no qual tem direito.','form','erro','".Sessao::getId()."');   \n";
            return false;
        }
    }

    if($rsPeriodoAquisitivo->getNumLinhas() < 0)
        return true;
    else {
        $stMensagem = "O Periodo Aquisitivo (".$stDataIncial." até ".$stDataFinal.") informado já possui lançamento de ferias.";
        $stJs .= "f.dtInicial.focus();                                          \n";
        $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');   \n";
        return false;
    }
}

function submeter()
{
    if (Sessao::read("boConcederFeriasLote")) {
        $stJs .= "parent.frames[2].Salvar();";
    } else {
        if ( validaDataInicialPeriodoAquisitivo($_REQUEST['dtInicial'],$stJs) ) {
            $stJs .= "parent.frames[2].Salvar();";
        }
    }

    return $stJs;
}

function gravaLoteSessao()
{
    if ($_REQUEST["inCodLote"] != "") {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFerias.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFeriasContrato.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFeriasOrgao.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFeriasLocal.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFeriasFuncao.class.php");
        $obTPessoalLoteFerias = new TPessoalLoteFerias();
        $obTPessoalLoteFerias->recuperaRelacionamento($rsLote, "WHERE cod_lote =".$_REQUEST["inCodLote"]);

        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalRegime.class.php");
        $obTPessoalRegime = new TPessoalRegime();
        $obTPessoalRegime->setDado('cod_regime', $rsLote->getCampo('cod_regime'));
        $obTPessoalRegime->recuperaPorChave($rsRegime);

        $stDescricaoRegime = " - ".$rsRegime->getCampo('descricao');

        $stCodigosLote = "";

        if ($rsLote->getNumLinhas() > 0) {
            switch ($rsLote->getCampo('tipo_filtro')) {
                case "O":
                    $obTPessoalCodigosLote = new TPessoalLoteFeriasOrgao();
                    break;
                case "L":
                    $obTPessoalCodigosLote = new TPessoalLoteFeriasLocal();
                    break;
                case "F":
                    $obTPessoalCodigosLote = new TPessoalLoteFeriasFuncao();
                    break;
                 default:
                    $obTPessoalCodigosLote = new TPessoalLoteFeriasContrato();
                    break;
            }
            $obTPessoalCodigosLote->setDado('cod_lote', $rsLote->getCampo('cod_lote'));
            $obTPessoalCodigosLote->recuperaPorChave($rsCodigosLote);

            switch ($rsLote->getCampo('tipo_filtro')) {
                case "O":
                    while (!$rsCodigosLote->eof()) {
                        $stCodigosLote .= $rsCodigosLote->getCampo('cod_orgao').",";
                        $rsCodigosLote->proximo();
                    }
                    break;
                case "L":
                    while (!$rsCodigosLote->eof()) {
                        $stCodigosLote .= $rsCodigosLote->getCampo('cod_local').",";
                        $rsCodigosLote->proximo();
                    }
                    break;
                case "F":
                    while (!$rsCodigosLote->eof()) {
                        $stCodigosLote .= $rsCodigosLote->getCampo('cod_cargo').",";
                        $rsCodigosLote->proximo();
                    }
                    break;
                 default:
                    while (!$rsCodigosLote->eof()) {
                        $stCodigosLote .= $rsCodigosLote->getCampo('cod_contrato').",";
                        $rsCodigosLote->proximo();
                    }
                    break;
            }
        }

        $stCodigosLote = substr($stCodigosLote, 0, -1);

        Sessao::write("stCodigosLote"        , '('.$stCodigosLote.')');
        Sessao::write("stTipoFiltroLote"     , $rsLote->getCampo('tipo_filtro'));
        Sessao::write("stDecricaoRegimeLote" , $stDescricaoRegime);
        Sessao::write("stNomeLote"           , $rsLote->getCampo("nome"));
    } else {
        Sessao::remove("stCodigosLote");
        Sessao::remove("stTipoFiltroLote");
        Sessao::remove("stDecricaoRegimeLote");
        Sessao::remove("stNomeLote");
    }
}

function alterarPost(Request $request)
{
    global $pgForm,$pgList;
    if ($request->get("boConcederFeriasLote") == "true") {
        if ($request->get("stAcao") == "consultar" OR $request->get("stAcao") == "excluir") {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFerias.class.php");
            $obTPessoalLoteFerias = new TPessoalLoteFerias();
            $obTPessoalLoteFerias->recuperaRelacionamento($rsLote,"","ano_competencia||mes_competencia , nome ASC");

            $obCmbLoteFerias = new Select;
            $obCmbLoteFerias->setRotulo                     ( "Lote de Férias"                                              );
            $obCmbLoteFerias->setName                       ( "inCodLote"                                                   );
            $obCmbLoteFerias->setId                         ( "inCodLote"                                                   );
            $obCmbLoteFerias->setStyle                      ( "width: 450px"                                                );
            $obCmbLoteFerias->setCampoID                    ( "cod_lote"                                                    );
            $obCmbLoteFerias->setCampoDesc                  ( "nome"                                                        );
            $obCmbLoteFerias->addOption                     ( "", "Selecione"                                               );
            $obCmbLoteFerias->setTitle                      ( "Selecione o lote de férias para consultar."                  );
            $obCmbLoteFerias->setNull                       ( false                                                         );
            $obCmbLoteFerias->preencheCombo                 ( $rsLote                                                       );
            $obCmbLoteFerias->obEvento->setOnChange         ( "montaParametrosGET('gravaLoteSessao','inCodLote');"          );

            $obImgNomeLote = new Img();
            $obImgNomeLote->setCaminho   ( CAM_FW_IMAGENS."botao_popup.png");
            $obImgNomeLote->setAlign     ( "absmiddle" );
            $obImgNomeLote->montaHTML();

            $stLink  = "&nbsp;<a href='JavaScript:abrePopUpLote2();' title='Consultar filtro do lote.'>";
            $stLink .= $obImgNomeLote->getHTML();
            $stLink .= "</a>";

            $obLblNomeLote = new Label();
            $obLblNomeLote->setRotulo("Lote de Férias");
            $obLblNomeLote->setValue($stLink);

            $obFormulario = new Formulario;
            $obFormulario->agrupaComponentes(array($obCmbLoteFerias,$obLblNomeLote));
            $obFormulario->montaInnerHTML();
            $obFormulario->obJavaScript->montaJavaScript();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);
            $stHtml = $obFormulario->getHTML();

            if ($request->get("stAcao") == "consultar") {
                $stJs = "f.action = '".$pgList."?".Sessao::getId()."';";
            } else {
                $stJs = "f.action = '".$pgForm."?".Sessao::getId()."';";
            }

            $stJs .= "limpaFormulario();                           \n";
            $stJs .= "f.boConcederFeriasLote.checked = true;       \n";
            $stJs .= "f.stTipoFiltro.value = 'geral';              \n";
            $stJs .= "f.stTipoFiltro.disabled = true;              \n";
        } else {
            $stJs = "f.action = '".$pgForm."?".Sessao::getId()."'; \n";
        }
    } else {
        include_once(CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php");
        Sessao::write('boPossuiEvento', true);
        $obIFiltroCompetencia = new IFiltroCompetencia(true, '', false);
        if ($request->get("stAcao") == "consultar") {
            $obChkFiltarCompetencia = new Checkbox;
            $obChkFiltarCompetencia->setRotulo          ( "Consultar por Competencia de Pagamento"                          );
            $obChkFiltarCompetencia->setTitle           ( "Selecione para consultar férias por competência de pagamento."   );
            $obChkFiltarCompetencia->setName            ( "boConsultarCompetencia"    );
            $obChkFiltarCompetencia->setValue           ( true                        );
            if($request->get("boConsultarCompetencia") == "true" )
                $obChkFiltarCompetencia->setChecked     ( true                        );
            $obChkFiltarCompetencia->obEvento->setOnChange("if (this.checked == true) {this.value=true;} else {this.value=false;}montaParametrosGET('alterarPost','boConcederFeriasLote,stAcao,boConsultarCompetencia');");
            
            $obIFiltroCompetencia = new IFiltroCompetencia();
            $obIFiltroCompetencia->obCmbMes->setValue   ( '' );
        }
        $obIFiltroCompetencia->setRotulo                ( "Competência de Pagamento"                );
        $obIFiltroCompetencia->obCmbMes->setNull        ( true                                      );
        $obIFiltroCompetencia->obCmbMes->setTitle       ( "Informe a competência de pagamento."     );

        $obFormulario = new Formulario;
        if ($request->get("stAcao") == "consultar") {
            $obFormulario->addComponente ( $obChkFiltarCompetencia );

            if ($request->get("boConsultarCompetencia") == "true") {
                $obIFiltroCompetencia->geraFormulario($obFormulario);
            }
        } else {
            $obIFiltroCompetencia->geraFormulario($obFormulario);
        }

        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        $stEval = "";
        $stJs  = "f.action = '".$pgList."?".Sessao::getId()."'; \n";
        $stJs .= "f.stTipoFiltro.value = 'contrato';            \n";

        if ($request->get("boConsultarCompetencia") != "true") {
            if ($request->get('stAcao') == 'consultar') {
                $stJs .= "f.stTipoFiltro.value = 'contrato_todos';  \n";
            }
            $stJs .= "f.stTipoFiltro.disabled = false;          \n";
            $stJs .= "ajaxJavaScript('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&stTipoFiltro='+f.stTipoFiltro.value+'&boQuebrarDisabled='+document.frm.boQuebrarDisabled.value,'gerarSpan' ); \n";
        }
        if ($request->get("stAcao") == "excluir") {
            $stJs .= "f.stTipoFiltro.value = 'contrato';        \n";
            $stJs .= "f.stTipoFiltro.disabled = false;          \n";
        }
    }

    $stJs .= "d.getElementById('spnConcederFeriasLote').innerHTML = '".$stHtml."';                          \n";
    $stJs .= "f.hdnConcederFeriasLote.value = '".$stEval."';                                                \n";
    $stJs .= "jQuery('#limpar').attr('onClick', 'limpaFormulario(); executaFuncaoAjax(\'alterarPost\');' ); \n";

    return $stJs;
}

function incluirContrato()
{
    $obErro      = new erro;
    $arContratos = ( is_array(Sessao::read('arContratos')) ) ? Sessao::read('arContratos') : array();

    if ( !$obErro->ocorreu() and $_GET['inContrato'] == "") {
        $obErro->setDescricao("Campo Matrícula inválido!()");
    }
    if ( !$obErro->ocorreu() ) {
        foreach ($arContratos as $arContrato) {
            if ($arContrato['inContrato'] == $_GET['inContrato']) {
                $obErro->setDescricao("Matrícula já inserida na lista.");
                break;
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " AND registro = ".$_GET['inContrato'];
        $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro);
    }
    if ( !$obErro->ocorreu() ) {
        $arContrato                             = array();
        $arContrato['inId']                     = count($arContratos);
        $arContrato['inContrato']               = $_GET['inContrato'];
        $arContrato['cod_contrato']             = $rsCGM->getCampo("cod_contrato");
        $arContrato['numcgm']                   = $rsCGM->getCampo("numcgm");
        $arContrato['nom_cgm']                  = $rsCGM->getCampo("nom_cgm");
        $arContratos[]                          = $arContrato;
        Sessao::write('arContratos', $arContratos);
        $stJs .= montaListaContratos($arContratos);
        $stJs .= "f.inContrato.value = '';";
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirContrato()
{
    $arContratos = ( is_array(Sessao::read('arContratos')) ? Sessao::read('arContratos') : array());
    $arContratosNovo = array();
    Sessao::write('arContratos', $arContratosNovo);

    foreach ($arContratos as $arContrato) {
        if ($arContrato['inId'] != $_GET['inId']) {
            $inId = sizeof($arContratosNovo);
            $arEvento['inId'] = $inId;
            $arContratosNovo[] = $arContrato;
        }
    }

    Sessao::write('arContratos', $arContratosNovo);
    $stJs .= montaListaContratos($arContratosNovo);

    return $stJs;
}

function montaListaContratos($arContratos)
{
    $rsContratos = new Recordset;
    $rsContratos->preenche($arContratos);

    $obLista = new Lista;
    $obLista->setTitulo("Lista de Matrículas");
    $obLista->setRecordSet( $rsContratos );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "[inContrato]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirContrato');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnContratos').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function excluirLote(Request $request)
{
    global $pgProc;

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFerias.class.php");
    $obTPessoalLoteFerias = new TPessoalLoteFerias();
    $stFiltro = " WHERE cod_lote = ".$request->get("inCodLote");
    $obTPessoalLoteFerias->recuperaTodos($rsLoteFeriasContrato,$stFiltro);
    $stLote = $rsLoteFeriasContrato->getCampo("nome");

    $stId = str_replace("&","*_*",Sessao::getId())."*_*boConcederFeriasLote=true*_*inCodLote=".$request->get("inCodLote");
    $stJs = "alertaQuestao('".CAM_GRH_PES_INSTANCIAS."ferias/".$pgProc."?$stId*_*stAcao=".$request->get("stAcao")."*_*stDescQuestao=".$stLote."','sn_excluir','".Sessao::getId()."');\n";

    return $stJs;
}

function preencherQuantidadeFaltas()
{
    $inQuantFaltas = 0;
    $stJs .= "";
    if (trim($_GET["dtInicial"])!="" && trim($_GET["dtFinal"])!="") {
        
        if( validaDatasInicialFinalPeriodoAquisitivo($_GET["dtInicial"],$_GET["dtFinal"], $stJs) == true ) {
            
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoContratoServidor.class.php");
            $obTPessoalAssentamentoGeradoContratoServidor = new TPessoalAssentamentoGeradoContratoServidor();
            $stFiltro  = " AND assentamento_gerado_contrato_servidor.cod_contrato = ".$_GET["inCodContrato"]." \n";
            $stFiltro .= " AND assentamento_assentamento.cod_motivo = 10 \n";
            $stFiltro .= " AND (assentamento_gerado.periodo_inicial BETWEEN to_date('".$_GET["dtInicial"]."','dd/mm/yyyy') AND to_date('".$_GET["dtFinal"]."','dd/mm/yyyy') \n";
            $stFiltro .= "  OR  assentamento_gerado.periodo_final BETWEEN to_date('".$_GET["dtInicial"]."','dd/mm/yyyy') AND to_date('".$_GET["dtFinal"]."','dd/mm/yyyy')) \n";
            $obTPessoalAssentamentoGeradoContratoServidor->recuperaRelacionamento($rsAssentamentoGerado,$stFiltro);
            
            while (!$rsAssentamentoGerado->eof()) {
                $inQuantFaltas += $rsAssentamentoGerado->getCampo("dias_do_periodo");
                $rsAssentamentoGerado->proximo();
            }
        }
    }
    $stJs .= "f.inQuantFaltas.value = '".$inQuantFaltas."'; \n";

    return $stJs;
}

switch ($request->get('stCtrl')) {
    case "gerarSpan":
        $stJs .= gerarSpan();
    break;
    case "limparFiltro":
        $stJs .= limparFiltro();
    break;
    case "limparForm":
        $stJs .= limparForm();
    break;
    case "processarFiltro":
        $stJs .= processarFiltro();
    break;
    case "processarForm":
        $stJs .= processarForm();
    break;
    case "processarConsulta":
        $stJs .= processarConsulta();
    break;
    case "preencherQuantDiasGozo":
        $stJs .= preencherQuantDiasGozo();
    break;
    case "preencherDataFinal":
        $stJs .= preencherDataFinal();
    break;
    case "validarDataInicioFerias":
        $stJs .= validarDataInicioFerias();
    break;
    case "submeter":
        $stJs .= submeter();
    break;
    case "alterarPost":
        $stJs = alterarPost($request);
        break;
    case "incluirContrato":
        $stJs = incluirContrato();
        break;
    case "excluirContrato":
        $stJs = excluirContrato();
        break;
    case "excluirLote":
        $stJs = excluirLote($request);
        break;
    case "preencherQuantidadeFaltas":
        $stJs = preencherQuantidadeFaltas();
        break;
    case "gravaLoteSessao":
        $stJs = gravaLoteSessao();
        break;
    case "processarListaLote":
        $stJs = processarListaLote();
        break;
    case "limpaCampoInicioFerias":
        $stJs = limpaCampoInicioFerias();
        break;
}

if ($stJs) {
   echo $stJs;
}
?>
