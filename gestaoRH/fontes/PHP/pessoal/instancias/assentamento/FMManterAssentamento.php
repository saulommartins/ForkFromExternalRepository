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
    * Página de Formulário para cadastro de Assentamento
    * Data de Criação   : 06/08/2005

    * @author Vandré Miguel Ramos

    * @ignore

    $Id: FMManterAssentamento.php 66365 2016-08-18 14:39:09Z evandro $

    Caso de uso: uc-04.04.08

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalVantagem.class.php"           );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamento.class.php"       );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php" );

//Define o nome dos arquivos PHP
$arLink = Sessao::read('link');
$stPrograma = "ManterAssentamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php"."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$arLink["pg"]."&pos=".$arLink["pos"];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

Sessao::write('Faixas', array());
Sessao::write('Correcoes', array());
Sessao::write('stAba', "assentamento");

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$rsOperador               = new RecordSet;
$rsEsfera                 = new RecordSet;
$rsCausaMovimentacao      = new RecordSet;
$rsFaixas                 = new RecordSet;
$rsSefip                  = new RecordSet;
$rsEvento                 = new RecordSet;
$rsRegimeVinculado        = new RecordSet;
$rsNorma                  = new RecordSet;
$rsTipoNorma              = new RecordSet;
$rsClassificacao          = new RecordSet;
$rsAssentamento           = new RecordSet;
$rsSubDivisaoDisponiveis  = new RecordSet;
$rsSubDivisaoSelecionados = new RecordSet;
$rsEventosDisponiveis     = new RecordSet;
$rsEventosSelecionados    = new RecordSet;

$rsAssentamentoRegime = $rsAssentamentoNorma = $rsAssentamentoEvento = $rsAssentamentoSefip = $rsAssentamentoCausaMovimentacao = new RecordSet;
$obRPessoalVantagem      = new RPessoalVantagem;
$obRPessoalAssentamento  = new RPessoalAssentamento($obRPessoalVantagem);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty($stAcao)||$stAcao=="incluir" ) {
    $obRPessoalAssentamento->obRPessoalClassificacaoAssentamento->listarClassificacao( $rsClassificacao );
    $obRPessoalAssentamento->obRPessoalRegime->listarRegime( $rsRegimeVinculado );
    $obRPessoalAssentamento->addEvento();
    $obRPessoalAssentamento->roUltimoFolhaPagamentoEvento->listarEvento( $rsEventosDisponiveis,"","" );
    $obRPessoalAssentamento->obRPessoalSefip->listar( $rsSefip );
    $obRPessoalAssentamento->obRNorma->obRTipoNorma->listarTodos( $rsTipoNorma );
    $obRPessoalAssentamento->obRPessoalEsferaOrigem->listar($rsEsfera,"","");
    $obRPessoalAssentamento->listarOperador($rsOperador);
    $obRPessoalAssentamento->obRPessoalAssentamentoMotivo->listarAssentamentoMotivo($rsMotivo);

    $stAcao = "incluir";
    $obRPessoalAssentamento->addPessoalSubDivisao();
    $obRPessoalAssentamento->roUltimoPessoalSubDivisao->listarSubDivisao($rsSubDivisaoDisponiveis,$stFiltro,"",$boTransacao);
    $obRPessoalAssentamento->obRPessoalAssentamentoFaixaDesconto->listarAssentamentoFaixaDesconto( $rsFaixas );

    $js .= "buscaValor('listarRescisao');";
    $js .= "focusIncluir();";
    $js .= "limpaCampos();";
    $js .= "bloqueiaAbas();";
} elseif ($stAcao == 'alterar') {
    $obRPessoalAssentamento->setCodAssentamento( $_REQUEST['inCodAssentamento'] );
    $stLink = $pgList."?".Sessao::getId()."&inCodClassificacao=".$_REQUEST['inCodClassificacao'];
    $obRPessoalAssentamento->listarAssentamento( $rsAssentamento,"");
    $obRPessoalAssentamento->obRPessoalClassificacaoAssentamento->listarClassificacao( $rsClassificacao );
    //Aba Assentamento
    $inCodClassificacaoTxt      = $inCodClassificacao       = $rsAssentamento->getCampo('cod_classificacao');
    $stDescricao                = $rsAssentamento->getCampo('descricao');
    $stSigla                    = $rsAssentamento->getCampo('sigla');
    $stAbreviacao               = $rsAssentamento->getCampo('abreviacao');
    $boGradeEfetividade         = $rsAssentamento->getCampo('grade_efetividade');
    $boRelFuncaoGratificada     = $rsAssentamento->getCampo('rel_funcao_gratificada');
    $boEventoAutomatico         = $rsAssentamento->getCampo('evento_automatico');
    $boAssentamentoAutomatico   = $rsAssentamento->getCampo('assentamento_automatico');
    $obRPessoalAssentamento->obRPessoalAssentamentoMotivo->setCodMotivo( $rsAssentamento->getCampo('cod_motivo') );
    $obRPessoalAssentamento->obRPessoalAssentamentoMotivo->listarAssentamentoMotivo($rsMotivo);
    $stMotivo                   = $rsMotivo->getCampo('cod_motivo')." - ".$rsMotivo->getCampo('descricao');
    Sessao::write("inCodMotivo", $rsMotivo->getCampo('cod_motivo'));
    Sessao::write("inQuantDiasOnusEmpregador", $rsAssentamento->getCampo('quant_dias_onus_empregador'));
    Sessao::write("inQuantDiasLicencaPremio", $rsAssentamento->getCampo('quant_dias_licenca_premio'));    
    $obRPessoalAssentamento->obRNorma->obRTipoNorma->listarTodos( $rsTipoNorma );
    $obRPessoalAssentamento->obRNorma->setCodNorma( $rsAssentamento->getCampo('cod_norma') );
    $obRPessoalAssentamento->obRNorma->listar( $rsNorma );
    $inCodNormaTxt = str_replace(' ','',$rsNorma->getCampo('num_norma'));
    $inCodNorma = $rsNorma->getCampo('cod_norma');
    Sessao::write('inCodNormaTxt', $inCodNormaTxt);
    $obRPessoalAssentamento->obRNorma->consultar( $rsAssentamentoNorma);
    $inCodTipoNormaTxt = $inCodTipoNorma = $obRPessoalAssentamento->obRNorma->obRTipoNorma->getCodTipoNorma();

    $obRPessoalAssentamento->obRPessoalEsferaOrigem->listar($rsEsfera,"","");
    $inCodEsferaTxt = $inCodEsfera = $rsAssentamento->getCampo('cod_esfera');

    $obRPessoalAssentamento->listarOperador($rsOperador);
    $inCodOperadorTxt = $inCodOperador = $rsAssentamento->getCampo('cod_operador');

    $obRPessoalAssentamento->listarSubDivisaoDisponiveis($rsSubDivisaoDisponiveis,$stFiltro,"",$boTransacao);
    $obRPessoalAssentamento->listarSubDivisaoSelecionadas($rsSubDivisaoSelecionados,$stFiltro,"",$boTransacao);

    //Aba Afastamento
    $obRPessoalAssentamento->obRPessoalAssentamentoFaixaDesconto->setCodAssentamento( $_REQUEST['inCodAssentamento'] );
    $obRPessoalAssentamento->consultaAssentamentoMovSefipSaida( $rsCodSefip,"" );
    $obRPessoalAssentamento->obRPessoalSefip->listar( $rsSefip );

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoRaisAfastamento.class.php");
    $obTPessoalAssentamentoRaisAfastamento = new TPessoalAssentamentoRaisAfastamento();
    $obTPessoalAssentamentoRaisAfastamento->setDado("cod_assentamento",$rsAssentamento->getCampo("cod_assentamento"));
    $obTPessoalAssentamentoRaisAfastamento->setDado("timestamp",$rsAssentamento->getCampo("timestamp"));
    $obTPessoalAssentamentoRaisAfastamento->recuperaPorChave($rsRais);

    $inCodSefip                     = $rsCodSefip->getCampo('num_sefip');
    $inCodSefipTxt                  = $rsCodSefip->getCampo('num_sefip');
    $inCodRais                      = $rsRais->getCampo("cod_rais");
    $dtDataInicioAssentamento       = $rsAssentamento->getCampo('data_inicial');
    $dtDataFinalAssentamento        = $rsAssentamento->getCampo('data_final');
    $dtDataInicio                   = $rsAssentamento->getCampo('data_inicial_vantagem');
    $dtDataEncerramento             = $rsAssentamento->getCampo('data_final_vantagem');
    $boCancelarDireito              = $rsAssentamento->getCampo('cancelar_direito');
    $inQuantidadeDias               = $rsAssentamento->getCampo('dia');

    $obRPessoalAssentamento->obRPessoalAssentamentoFaixaDesconto->consultarAssentamentoFaixaDesconto( $rsFaixas );
    $obRPessoalAssentamento->obRPessoalSefip->listar( $rsSefip );
    $inCount = 0;
    $rsFaixas->addFormatacao("percentual_desconto", "NUMERIC_BR");
    $arFaixas = array();
    while ( !$rsFaixas->eof() ) {
        $arTMP['inId']              = ++$inCount;
        $arTMP['inCodFaixas']       = $rsFaixas->getCampo("cod_faixa");
        $arTMP['inInicioIntervalo'] = $rsFaixas->getCampo("valor_inicial");
        $arTMP['inFimIntervalo']    = $rsFaixas->getCampo("valor_final");
        $arTMP['flPercentualDesc']  = $rsFaixas->getCampo("percentual_desconto");
        $arFaixas[] = $arTMP;
        $rsFaixas->proximo();
    }
    Sessao::write('Faixas', $arFaixas);

    //Aba Vantagem
    $obRPessoalAssentamento->obRPessoalVantagem->addPessoalFaixaCorrecao();
    $obRPessoalAssentamento->obRPessoalVantagem->roUltimoPessoalFaixaCorrecao->setCodAssentamento($_REQUEST['inCodAssentamento']);
    $obRPessoalAssentamento->obRPessoalVantagem->roUltimoPessoalFaixaCorrecao->listarFaixaCorrecao($rsFaixaCorrecao);
    $inCount = 0;
    $arCorrecoes = array();
    while ( !$rsFaixaCorrecao->eof() ) {
        $arTMP['inId']                    = ++$inCount;
        $arTMP['inQuantidadeMeses']       = $rsFaixaCorrecao->getCampo('quant_meses');
        $arTMP['nuPercentualCorrecao']    = str_replace( ".", ",", $rsFaixaCorrecao->getCampo('percentual_correcao') );
        $arCorrecoes[] = $arTMP;
        $rsFaixaCorrecao->proximo();
    }
    Sessao::write('Correcoes', $arCorrecoes);

    $dtDataInicio       = $rsAssentamento->getCampo('data_inicial_vantagem');
    $dtDataEncerramento = $rsAssentamento->getCampo('data_final_vantagem');

    $js .= "bloqueiaAbas();";
    $js .= "buscaValor('preencheInner');";

}
//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );
$obForm->setEncType     ( "multipart/form-data" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

$obHdnCodAssentamento = new Hidden;
$obHdnCodAssentamento->setName         ( "inCodAssentamento" );
$obHdnCodAssentamento->setValue        ( $_REQUEST['inCodAssentamento'] );

include_once 'FMManterAssentamentoAbaAssentamento.php';
include_once 'FMManterAssentamentoAbaAfastamento.php';
include_once 'FMManterAssentamentoAbaRescisao.php';
include_once 'FMManterAssentamentoAbaVantagem.php';

$obBtnOk     = new ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName               ( "btnLimpar"                                       );
$obBtnLimpar->setValue              ( "Limpar"                                          );
$obBtnLimpar->setTipo               ( "button"                                          );
$obBtnLimpar->obEvento->setOnClick  ( "buscaValor('limparAba');"                        );

//DEFINICAO DO FORMULARIO

$obFormulario = new FormularioAbas;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo             ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );

$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addHidden        ( $obHdnAcao                );
$obFormulario->addHidden        ( $obHdnCodAssentamento     );

//Aba Identificacao
$obFormulario->addAba( "Dados para Assentamento" );
$obFormulario->addTitulo ( "Dados do Assentamento"     );
$obFormulario->addHidden             ( $obHdnTipoClassificacao );
$obFormulario->addComponenteComposto ( $obTxtClassificacao, $obCmbClassificacao  );
if ($_REQUEST['stAcao']=='alterar') {
   $obFormulario->addComponente    ( $obLblDescricao            );
   $obFormulario->addComponente    ( $obLblSigla                );
   $obFormulario->addComponente    ( $obTxtAbreviacao               );
   $obFormulario->addHidden        ( $obHdnDescricao            );
   $obFormulario->addHidden        ( $obHdnSigla                );
   $obFormulario->addHidden        ( $obHdnNorma                );
   $obFormulario->addHidden        ( $obHdnTipoNorma            );
   $obFormulario->addHidden        ( $obHdnOperador             );
   $obFormulario->addHidden        ( $obHdnCodClassificacao     );
} else {
  $obFormulario->addComponente    ( $obTxtDescricao           );
  $obFormulario->addComponente    ( $obTxtSigla               );
  $obFormulario->addComponente    ( $obTxtAbreviacao               );
}
$obFormulario->addComponente            ( $obChkAssentamentoInicio          );
$obFormulario->addComponente            ( $obChkGradeEfetividade            );
$obFormulario->addComponente            ( $obChkRelFuncaoGratificada        );
$obFormulario->addComponenteComposto    ( $obTxtTipoNorma, $obCmbTipoNorma  );
$obFormulario->addComponenteComposto    ( $obTxtNorma, $obCmbNorma          );
$obFormulario->addComponente            ( $obLblDtPublicacao                );
$obFormulario->addHidden                ( $obHdnDtPublicacao                );
$obFormulario->addComponente            ( $obCmbRegimeSubDivisao            );
$obFormulario->addComponenteComposto    ( $obTxtEsfera, $obCmbEsfera        );
$obFormulario->addComponenteComposto    ( $obTxtOperador, $obCmbOperador    );
$obFormulario->addTitulo                ( "Período de Validade do Assentamento" );
$obFormulario->addComponente            ( $obDataInicioAssentamento         );
$obFormulario->addComponente            ( $obDataFinalAssentamento          );
$obFormulario->addComponente            ( $obChkCancelarDireito             );
$obFormulario->addTitulo                ( "Inserção/Proporção de Eventos" );
$obFormulario->addComponente            ( $obChkEventoAutomatico            );
$obFormulario->addSpan                  ( $obSpnEvento                      );
$obFormulario->addHidden                ( $obHdnEventoEval,true             );
$obFormulario->addComponente            ( $obChkInformarEventosProporcionalizacao );
$obFormulario->addSpan                  ( $obSpnEvento2                     );
$obFormulario->addHidden                ( $obHdnEventoEval2,true            );
$obFormulario->addTitulo                ( "Comportamento do Assentamento"   );
if ($_REQUEST['stAcao']=='alterar') {
    $obFormulario->addComponente        ( $obLblMotivo                      );
} else {
    $obFormulario->addComponenteComposto    ( $obTxtMotivo, $obCmbMotivo        );
}
$obFormulario->addSpan($obSpnMotivo);
$obFormulario->addHidden($obHdnMotido,true);
$obFormulario->addComponenteComposto    ( $obRdnAssentamentoAutomaticoTrue,$obRdnAssentamentoAutomaticoFalse      );

//Aba Afastamento
$obFormulario->addAba                   ( "Afastamento Temporário"              );
$obFormulario->addTitulo                ( "Código de Movimento da Sefip"        );
$obFormulario->addComponenteComposto    ( $obTxtCodigoSefip, $obCmbCodigoSefip  );
$obFormulario->addComponenteComposto    ( $obTxtCodigoRais, $obCmbCodigoRais  );
$obFormulario->addTitulo                ( "Período de Duração do Afastamento"   );
$obFormulario->addComponente            ( $obTxtQuantidadeDias                  );
if ($stAcao != "alterar" || $rsFaixas->eof()) {
    $obFormulario->addTitulo        ( "Intervalo de Dias para Proporção de Salário"             );
    $obFormulario->addHidden        ( $obHdnIdIntervalo                                         );
    $obFormulario->addComponente    ( $obTxtInicioIntervalo                                     );
    $obFormulario->addComponente    ( $obTxtFimIntervalo                                        );
    $obFormulario->addComponente    ( $obTxtDesconto                                            );
    $obFormulario->defineBarraAba   ( array($obBtnIncluir,$obBtnAlterar,$obBtnLimpar) ,'',''    );
    $obFormulario->addSpan          ( $obSpnFaixas                                              );
}

//Aba Rescisao
$obFormulario->addAba       ( "Afastamento Permanente"          );
$obFormulario->addTitulo    ( "Vínculo com Causa de Rescisão"   );
$obFormulario->addSpan      ( $obSpnRescisao                    );

//Aba Vantagem
$obFormulario->addAba           ( "Vantagem"                        );
$obFormulario->addTitulo        ( "Dados para Vantagem"             );
$obFormulario->addHidden        ( $obHdnCorrecaoId                  );
$obFormulario->addComponente    ( $obDataInicio                     );
$obFormulario->addComponente    ( $obDataEncerramento               );
$obFormulario->addTitulo        ( "Definição de Correções"          );
$obFormulario->addComponente    ( $obTxtQuantidadeMeses             );
$obFormulario->addComponente    ( $obTxtPercentualCorrecao          );
$obFormulario->defineBarraAba   ( array($obBtnIncluirVantagem,$obBtnAlterarVantagem,$obBtnLimparVantagem) ,'',''    );
$obFormulario->addSpan          ( $obSpnVantagens                   );

if ($_REQUEST['stAcao']=='alterar') {
    $obFormulario->Cancelar($stLink);
} else {
    //$obFormulario->OK();
    $obFormulario->defineBarra( array($obBtnOk,$obBtnLimpar) );
}
$obFormulario->show();

if( $_REQUEST['stAcao'] == 'alterar')
    SistemaLegado::BloqueiaFrames();

sistemaLegado::executaFrameOculto($js);
?>
