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
    * Página de Formulario para Configuração de Tipos de Diárias
    * Data de Criação: 19/08/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: FMConcederDiarias.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.09.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_DIA_MAPEAMENTO."TDiariasDiaria.class.php"                                        );
include_once ( CAM_GRH_DIA_MAPEAMENTO."TDiariasTipoDiaria.class.php"                                    );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"                                              );
include_once ( CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php"                                   );
include_once ( CAM_FRAMEWORK."componentes/HTML/MontaPaisEstadoMunicipio.class.php"                      );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php"                             );

include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php"                              );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorRegimeFuncao.class.php"                  );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSubDivisaoFuncao.class.php"              );

//Define o nome dos arquivos PHP
$stPrograma = "ConcederDiarias";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgDeta = "DT".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao        = $_REQUEST['stAcao'];
$inCodContrato = $_REQUEST['inCodContrato'];
$inRegistro    = $_REQUEST['inRegistro'];
$inCodDiaria   = $_REQUEST['inCodDiaria'];
$stTimestamp   = $_REQUEST['stTimestamp'];

if($stAcao == 'conceder')
    $jsOnload  = "executaFuncaoAjax('preencherConcessoes', '&inCodContrato=$inCodContrato');";

//Carrega dados de Servidor
$rsDetalhamentoContrato = new RecordSet();

$obTPessoalContratoServidorRegimeFuncao = new TPessoalContratoServidorRegimeFuncao();
$obTPessoalContratoServidorRegimeFuncao->recuperaRegimeDeContratos( $rsDetalhamentoContrato, " AND contrato_servidor_regime_funcao.cod_contrato = ".$inCodContrato );

if ($rsDetalhamentoContrato->getNumLinhas() > 0) {
    $stRegimeSubFun .= $rsDetalhamentoContrato->getCampo('descricao');
}

$obTPessoalContratoServidorSubDivisaoFuncao = new TPessoalContratoServidorSubDivisaoFuncao();
$obTPessoalContratoServidorSubDivisaoFuncao->recuperaSubDivisaoDeContratos( $rsDetalhamentoContrato, " AND contrato_servidor_sub_divisao_funcao.cod_contrato = ".$inCodContrato );

if ($rsDetalhamentoContrato->getNumLinhas() > 0) {
    $stRegimeSubFun .= " / ".$rsDetalhamentoContrato->getCampo('descricao');
}

$obTPessoalContratoServidor = new TPessoalContratoServidor();
$obTPessoalContratoServidor->recuperaContratosFuncaoExercida( $rsDetalhamentoContrato, " AND pcs.cod_contrato = ".$inCodContrato );

if ($rsDetalhamentoContrato->getNumLinhas() > 0) {
    if ($rsDetalhamentoContrato->getCampo('cod_cargo')) {

        $obTPessoalCargo = new TPessoalCargo();
        $obTPessoalCargo->setDado('cod_cargo', $rsDetalhamentoContrato->getCampo('cod_cargo'));
        $obTPessoalCargo->recuperaPorChave($rsDetalhamentoContrato);

        if ($rsDetalhamentoContrato->getNumLinhas() > 0) {
            $stRegimeSubFun .= " / ".$rsDetalhamentoContrato->getCampo('descricao');
        }

    }
}

$obTDiariasDiaria = new TDiariasDiaria();
$obTDiariasDiaria->recuperaListaContratos( $rsDetalhamentoContrato, " AND contrato.cod_contrato = ".$inCodContrato );

if ($rsDetalhamentoContrato->getNumLinhas() > 0) {
    $stNomcgm           = $rsDetalhamentoContrato->getCampo('nom_cgm');
    $stLotacaoDescricao = $rsDetalhamentoContrato->getCampo('cod_estrutural')." - ".$rsDetalhamentoContrato->getCampo('descricao_lotacao');
}

//Carrega Dados de UF Configuracao
$inCodEstadoConfiguracao = SistemaLegado::pegaConfiguracao('cod_uf');

if (is_numeric($inCodEstadoConfiguracao)) {
    $obTUF = new TUF();
    $obTUF->setDado('cod_uf', $inCodEstadoConfiguracao);
    $obTUF->recuperaPorChave($rsUFConfiguracao);

    if ($rsUFConfiguracao->getNumLinhas() > 0) {
        $inCodPaisConfiguracao = $rsUFConfiguracao->getCampo('cod_pais');
    }
}

Sessao::write('inCodEstadoConfiguracao', $inCodEstadoConfiguracao);

$obHdnAcao =  new Hidden;
$obHdnAcao->setName( "stAcao");
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

$obHdnCodContrato = new Hidden();
$obHdnCodContrato->setName("inCodContrato");
$obHdnCodContrato->setId("inCodContrato");
$obHdnCodContrato->setValue($inCodContrato);

$obHdnRegistro = new Hidden();
$obHdnRegistro->setName("inRegistro");
$obHdnRegistro->setId("inRegistro");
$obHdnRegistro->setValue($inRegistro);

$obLblNome = new Label();
$obLblNome->setRotulo("Matr&iacute;cula");
$obLblNome->setId("stNomcgm");
$obLblNome->setValue( $inRegistro." - ".$stNomcgm );

$obLblRegimeSubFun = new Label();
$obLblRegimeSubFun->setRotulo( "Regime/Subdivisão/Função" );
$obLblRegimeSubFun->setId("stRegimeSubFun");
$obLblRegimeSubFun->setValue( $stRegimeSubFun );

$obLblLotacaoDescricao = new Label();
$obLblLotacaoDescricao->setRotulo("Lotação");
$obLblLotacaoDescricao->setId("stLotacaoDescricao");
$obLblLotacaoDescricao->setValue( $stLotacaoDescricao );

$obLblDtAto = new Label();
$obLblDtAto->setId("dtAto");
$obLblDtAto->setRotulo( "Data Lei/Decreto" );

$obLblValorDiaria = new Label();
$obLblValorDiaria->setId("nuValorDiariaFormatado");
$obLblValorDiaria->setRotulo( "Valor da Diária" );

if ($stAcao == "conceder") {

        $obHdnAssinatura= new Hidden();
        $obHdnAssinatura->setName("stAssinatura");
        $obHdnAssinatura->setId("stAssinatura");

        $obHdnCodDiariaChave = new Hidden();
        $obHdnCodDiariaChave->setName("inCodDiariaChave");
        $obHdnCodDiariaChave->setId("inCodDiariaChave");

        $obHdnCodContratoChave= new Hidden();
        $obHdnCodContratoChave->setName("inCodContratoChave");
        $obHdnCodContratoChave->setId("inCodContratoChave");

        $obHdnTimestampChave = new Hidden();
        $obHdnTimestampChave->setName("stTimestampChave");
        $obHdnTimestampChave->setId("stTimestampChave");

        $obDtInicial = new Data();
        $obDtInicial->setTitle("Informe o período da viagem");
        $obDtInicial->setRotulo("Período da Viagem");
        $obDtInicial->setName("dtInicio");
        $obDtInicial->setId("dtInicio");
        $obDtInicial->setNullBarra(false);
        $obDtInicial->obEvento->setOnChange("executaFuncaoAjax('preencherQuantidadeValorDiarias', '&nuValorDiaria='+jQuery('#nuValorDiaria').val()+'&stAssinatura='+jQuery('#stAssinatura').val()+'&dtInicio='+jQuery('#dtInicio').val()+'&dtTermino='+jQuery('#dtTermino').val()+'&hrInicio='+jQuery('#hrInicio').val()+'&hrTermino='+jQuery('#hrTermino').val());");

        $obLblData = new Label();
        $obLblData->setValue("&nbsp;à&nbsp;");

        $obDtFinal = new Data();
        $obDtFinal->setName("dtTermino");
        $obDtFinal->setId("dtTermino");
        $obDtFinal->setNullBarra(false);
        $obDtFinal->obEvento->setOnChange("executaFuncaoAjax('preencherQuantidadeValorDiarias', '&nuValorDiaria='+jQuery('#nuValorDiaria').val()+'&dtInicio='+jQuery('#dtInicio').val()+'&dtTermino='+jQuery('#dtTermino').val()+'&hrInicio='+jQuery('#hrInicio').val()+'&hrTermino='+jQuery('#hrTermino').val());");

        $obHoraSaida = new Hora();
        $obHoraSaida->setTitle("Informe a hora de saída");
        $obHoraSaida->setRotulo("Hora de Saída");
        $obHoraSaida->setName("hrInicio");
        $obHoraSaida->setId("hrInicio");
        $obHoraSaida->setValue("00:00");
        $obHoraSaida->setNullBarra(false);
        $obHoraSaida->obEvento->setOnChange("executaFuncaoAjax('preencherQuantidadeValorDiarias', '&nuValorDiaria='+jQuery('#nuValorDiaria').val()+'&stAssinatura='+jQuery('#stAssinatura').val()+'&dtInicio='+jQuery('#dtInicio').val()+'&dtTermino='+jQuery('#dtTermino').val()+'&hrInicio='+jQuery('#hrInicio').val()+'&hrTermino='+jQuery('#hrTermino').val());");

        $obHoraRetorno = new Hora();
        $obHoraRetorno->setTitle("Informe a hora de chegada");
        $obHoraRetorno->setRotulo("Hora de Retorno");
        $obHoraRetorno->setName("hrTermino");
        $obHoraRetorno->setId("hrTermino");
        $obHoraRetorno->setValue("00:00");
        $obHoraRetorno->setNullBarra(false);
        $obHoraRetorno->obEvento->setOnChange("executaFuncaoAjax('preencherQuantidadeValorDiarias', '&nuValorDiaria='+jQuery('#nuValorDiaria').val()+'&stAssinatura='+jQuery('#stAssinatura').val()+'&dtInicio='+jQuery('#dtInicio').val()+'&dtTermino='+jQuery('#dtTermino').val()+'&hrInicio='+jQuery('#hrInicio').val()+'&hrTermino='+jQuery('#hrTermino').val());");

        $obTipoNormaNorma = new IBuscaInnerNorma( false, true );
        $obTipoNormaNorma->obITextBoxSelectTipoNorma->obSelect->setDisabled( true );
        $obTipoNormaNorma->obITextBoxSelectTipoNorma->obTextBox->setReadOnly( true );
        $obTipoNormaNorma->obBscNorma->setRotulo("Lei/Decreto" );
        $obTipoNormaNorma->obBscNorma->setTitle("Informe a lei ou decreto que autoriza o pagamento das diárias");
        //$obTipoNormaNorma->obBscNorma->obCampoCod->obEvento->setOnBlur( "executaFuncaoAjax('preencherDetalhesNorma', '&nuExercicioNorma='+this.value);" );
        $obTipoNormaNorma->obBscNorma->obCampoCod->obEvento->setOnChange("executaFuncaoAjax('preencherDetalhesNorma', '&nuExercicioNorma='+this.value);");
//        $stTipoNormaOnChange = $obTipoNormaNorma->obBscNorma->obCampoCod->obEvento->getOnChange();
//        $stTipoNormaOnChange = ($stTipoNormaOnChange != "")?$stTipoNormaOnChange.";":"";
//        $obTipoNormaNorma->obBscNorma->obCampoCod->obEvento->setOnBlur( $stTipoNormaOnChange." executaFuncaoAjax('preencherDetalhesNorma', '&nuExercicioNorma='+this.value);" );

        $obMontaPaisEstadoMunicipio = new MontaPaisEstadoMunicipio();
        $obMontaPaisEstadoMunicipio->setExibeEstado(true);
        $obMontaPaisEstadoMunicipio->setExibeMunicipio(true);
        $obMontaPaisEstadoMunicipio->getComboPais()->setNullBarra(false);
        $obMontaPaisEstadoMunicipio->getComboPais()->setTitle("Informe o país destino da viagem");
        $obMontaPaisEstadoMunicipio->getComboPais()->setRotulo("País Destino");
        $obMontaPaisEstadoMunicipio->getComboEstado()->setNullBarra(false);
        $obMontaPaisEstadoMunicipio->getComboEstado()->setTitle("Informe o estado destino da viagem");
        $obMontaPaisEstadoMunicipio->getComboEstado()->setRotulo("Estado Destino");
        $obMontaPaisEstadoMunicipio->getComboMunicipio()->setNullBarra(false);
        $obMontaPaisEstadoMunicipio->getComboMunicipio()->setTitle("Informe a cidade destino da viagem");
        $obMontaPaisEstadoMunicipio->getComboMunicipio()->setRotulo("Cidade Destino");
        $obMontaPaisEstadoMunicipio->setPais($inCodPaisConfiguracao);
        $obMontaPaisEstadoMunicipio->setEstado($inCodEstadoConfiguracao);

        $obTxtMotivo = new TextArea();
        $obTxtMotivo->setRotulo("Motivo da Viagem");
        $obTxtMotivo->setTitle("Informe o motivo da viagem");
        $obTxtMotivo->setStyle('width:350px');
        $obTxtMotivo->setRows(5);
        $obTxtMotivo->setName("stMotivo");
        $obTxtMotivo->setId("stMotivo");
        $obTxtMotivo->setNullBarra(false);

        $obHdnValorDiaria = new Hidden();
        $obHdnValorDiaria->setId("nuValorDiaria");
        $obHdnValorDiaria->setName("nuValorDiaria");
        $obHdnValorDiaria->setRotulo( "Valor da diária" );
        $obHdnValorDiaria->setNullBarra(false);

        $obTxtQuantidade = new Numerico();
        $obTxtQuantidade->setRotulo("Quantidade");
        $obTxtQuantidade->setSize(10);
        $obTxtQuantidade->setFloat(true);
        $obTxtQuantidade->setName("nuQuantidade");
        $obTxtQuantidade->setId("nuQuantidade");
        $obTxtQuantidade->setNullBarra(false);
        $obTxtQuantidade->setValue("1,00");
        $obTxtQuantidade->setTitle("Informe a quantidade de diárias.");
        $obTxtQuantidade->obEvento->setOnChange( " executaFuncaoAjax('preencherValorTotal', '&nuValorDiaria='+document.getElementById('nuValorDiaria').value+'&nuQuantidade='+document.getElementById('nuQuantidade').value); " );

        $obTxtValorTotal = new Numerico();
        $obTxtValorTotal->setRotulo("Valor Total");
        $obTxtValorTotal->setSize(10);
        $obTxtValorTotal->setFloat(true);
        $obTxtValorTotal->setName("nuValorTotal");
        $obTxtValorTotal->setId("nuValorTotal");
        $obTxtValorTotal->setNullBarra(false);
        $obTxtValorTotal->setTitle("Informe o valor total das diárias.");

        $obTDiariasTipoDiaria = new TDiariasTipoDiaria();
        $obTDiariasTipoDiaria->recuperaTipoDiariaEmVigencia($rsTipoDiarias);

        $obSelectTipoDiarias = new Select();
        $obSelectTipoDiarias->setRotulo("Tipo de Diária");
        $obSelectTipoDiarias->setName("inCodTipo");
        $obSelectTipoDiarias->setId("inCodTipo");
        $obSelectTipoDiarias->setStyle("width:300px;");
        $obSelectTipoDiarias->setTitle("Informe o tipo de diária. Deve ser previamente cadastrada na configuração das diárias.");
        $obSelectTipoDiarias->setNullBarra(false);
        $obSelectTipoDiarias->addOption("", "Selecione");
        $obSelectTipoDiarias->setCampoId('cod_tipo');
        $obSelectTipoDiarias->setCampoDesc('nom_tipo');
        $obSelectTipoDiarias->preencheCombo($rsTipoDiarias);
        $obSelectTipoDiarias->obEvento->setOnChange( " executaFuncaoAjax('preencherValorDiaria', '&boPreencherValorTotal=true&nuQuantidade='+document.getElementById('nuQuantidade').value+'&inCodTipo='+this.options[this.selectedIndex].value); " );

        // while (!$rsTipoDiarias->eof()) {
        //     $obSelectTipoDiarias->addOption($rsTipoDiarias->getCampo("cod_tipo"), $rsTipoDiarias->getCampo("nom_tipo"));
        //     $rsTipoDiarias->proximo();
        // }

        /*
        $obDtPagamento = new Data();
        $obDtPagamento->setRotulo("Data do Pagamento");
        $obDtPagamento->setName("dtPagamento");
        $obDtPagamento->setId("dtPagamento");
        $obDtPagamento->setNullBarra(false);
        */

        $obSpanConcessoes = new Span;
        $obSpanConcessoes->setId( "spnConcessoes" );

        $obSpanEmpenho = new Span;
        $obSpanEmpenho->setId( "spnEmpenho" );

        $obBtnOk = new Ok();
        $obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter', '', true);");

        $obBtnLimparForm = new Button();
        $obBtnLimparForm->obEvento->setOnClick("executaFuncaoAjax('limparForm');");
        $obBtnLimparForm->setValue('Limpar');

        //////////////////////

        $stName = "Concessao";

        $obBtnIncluir = new Button;
        $obBtnIncluir->setName              ( "btIncluir$stName"    );
        $obBtnIncluir->setId                ( "btIncluir$stName"    );
        $obBtnIncluir->setValue             ( "Incluir"             );
        $obBtnIncluir->obEvento->setOnClick ( " if ( ValidaConcessao() ) { montaParametrosGET('incluir$stName', '', true); }" );

        $obBtnAlterar = new Button;
        $obBtnAlterar->setName              ( "btAlterar$stName"    );
        $obBtnAlterar->setId                ( "btAlterar$stName"    );
        $obBtnAlterar->setValue             ( "Alterar"             );
        $obBtnAlterar->setDisabled          ( true                  );
        $obBtnAlterar->obEvento->setOnClick ( " if ( ValidaConcessao() ) { montaParametrosGET('alterar$stName', '', true); }" );

        $obBtnLimpar = new Button;
        $obBtnLimpar->setName              ( "btLimpar$stName"          );
        $obBtnLimpar->setValue             ( "Limpar"                   );
        $obBtnLimpar->obEvento->setOnClick ( "limpaFormulario$stName();");

        /////////////////////

        $obFormulario = new Formulario;
        $obFormulario->addForm( $obForm );
        $obFormulario->addHidden                        	( $obHdnAcao                                                            );
        $obFormulario->addHidden                        	( $obHdnCtrl                                                            );
        $obFormulario->addTitulo 							( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() , "right" 	);
        $obFormulario->addTitulo             				( "Dados do Servidor"                     		                        );
        $obFormulario->addHidden         				    ( $obHdnCodContrato     									            );
        $obFormulario->addHidden         				    ( $obHdnRegistro     									                );
        $obFormulario->addComponente                        ( $obLblNome                                                            );
        $obFormulario->addComponente                        ( $obLblRegimeSubFun                                                    );
        $obFormulario->addComponente                        ( $obLblLotacaoDescricao                                                );
        $obFormulario->addTitulo             				( "Informações das Diárias"                     		                );
        $obFormulario->addHidden         				    ( $obHdnCodDiariaChave    							                    );
        $obFormulario->addHidden         				    ( $obHdnCodContratoChave    							                );
        $obFormulario->addHidden         				    ( $obHdnTimestampChave        							                );
        $obTipoNormaNorma->geraFormulario                   ( $obFormulario                                                         );
        $obFormulario->addComponente                        ( $obLblDtAto                                                           );
        $obFormulario->agrupaComponentes                    ( array($obDtInicial, $obLblData, $obDtFinal)                           );
        $obFormulario->addComponente                        ( $obHoraSaida                                                          );
        $obFormulario->addComponente                        ( $obHoraRetorno                                                        );
        $obMontaPaisEstadoMunicipio->geraFormulario         ( $obFormulario                                                         );
        $obFormulario->addComponente                        ( $obTxtMotivo                                                          );
        $obFormulario->addComponente                        ( $obSelectTipoDiarias                                                  );
        $obFormulario->addComponente                        ( $obLblValorDiaria                                                     );
        $obFormulario->addHidden                            ( $obHdnValorDiaria                                                     );
        $obFormulario->addComponente                        ( $obTxtQuantidade                                                      );
        $obFormulario->addComponente                        ( $obTxtValorTotal                                                      );
        //$obFormulario->addComponente                        ( $obDtPagamento                                                        );
        $obFormulario->addHidden                            ( $obHdnAssinatura                                                      );
        $obFormulario->addSpan              				( $obSpanEmpenho           									            );

        $obFormulario->defineBarra                          ( array($obBtnIncluir,$obBtnAlterar,$obBtnLimpar)                       );
        $obFormulario->addSpan              				( $obSpanConcessoes           									        );

        $obFormulario->defineBarra                          ( array($obBtnOk,$obBtnLimparForm)                                      );
        $obFormulario->Show();

} else { //if stAcao == consultar

        $obTDiariasDiaria = new TDiariasDiaria();
        $obTDiariasDiaria->setDado('cod_diaria', $inCodDiaria);
        $obTDiariasDiaria->setDado('timestamp',  $stTimestamp);
        $stFiltroDiaria = " AND diaria.timestamp = '$stTimestamp' AND diaria.cod_diaria = ".$inCodDiaria;
        $obTDiariasDiaria->recuperaRelacionamento($rsDiaria, $stFiltroDiaria);

        //////////////////////

        $obHdnCodDiaria = new Hidden();
        $obHdnCodDiaria->setName("inCodDiaria");
        $obHdnCodDiaria->setId("inCodDiaria");
        $obHdnCodDiaria->setValue($inCodDiaria);

        $obHdnTimestamp = new Hidden();
        $obHdnTimestamp->setName("stTimestamp");
        $obHdnTimestamp->setId("stTimestamp");
        $obHdnTimestamp->setValue($stTimestamp);

        $obLblTipoNormaNorma = new Label();
        $obLblTipoNormaNorma->setId("stNorma");
        $obLblTipoNormaNorma->setRotulo( "Lei/Decreto" );
        $obLblTipoNormaNorma->setValue($rsDiaria->getCampo('num_norma_exercicio')." - ".$rsDiaria->getCampo('norma_descricao'));

        $obLblDtAto->setValue( SistemaLegado::dataToBr($rsDiaria->getCampo('dt_publicacao')) );

        if ($rsDiaria->getCampo('hr_inicio') != "") {
            $stHoraSaida = " (a partir das ".substr($rsDiaria->getCampo('hr_inicio'), 0, 5)."h)";
        }

        if ($rsDiaria->getCampo('hr_termino') != "") {
            $stHoraRetorno = " (até as ".substr($rsDiaria->getCampo('hr_termino'),0,5)."h)";
        }

        $obLblPeriodoViagem = new Label();
        $obLblPeriodoViagem->setRotulo("Período da Viagem");
        $obLblPeriodoViagem->setId("stPeriodoViagem");
        $obLblPeriodoViagem->setValue( $rsDiaria->getCampo('dt_inicio')."$stHoraSaida à ".$rsDiaria->getCampo('dt_termino').$stHoraRetorno );

        $obLblPais = new Label();
        $obLblPais->setId("stPais");
        $obLblPais->setRotulo("País Destino");

        $obLblEstado = new Label();
        $obLblEstado->setId("stEstado");
        $obLblEstado->setRotulo("Estado Destino");

        $obTUF = new TUF();
        $obTUF->setDado('cod_uf', $rsDiaria->getCampo('cod_uf'));
        $obTUF->recuperaPorChave($rsUF);
        if ($rsUF->getNumLinhas()>0) {
            $obLblEstado->setValue( $rsUF->getCampo('nom_uf') );

            $obTPais = new TPais();
            $obTPais->setDado('cod_pais', $rsUF->getCampo('cod_pais'));
            $obTPais->recuperaPorChave($rsPais);
            if ($rsPais->getNumLinhas()>0) {
                $obLblPais->setValue( $rsPais->getCampo('nom_pais') );
            }
        }

        $obLblMunicipio = new Label();
        $obLblMunicipio->setId("stMunicipio");
        $obLblMunicipio->setRotulo("Cidade Destino");

        $obTMunicipio = new TMunicipio();
        $obTMunicipio->setDado('cod_uf', $rsDiaria->getCampo('cod_uf'));
        $obTMunicipio->setDado('cod_municipio', $rsDiaria->getCampo('cod_municipio'));
        $obTMunicipio->recuperaPorChave($rsMunicipio);
        if ($rsMunicipio->getNumLinhas()>0) {
            $obLblMunicipio->setValue( $rsMunicipio->getCampo('nom_municipio') );
        }

        $obLblMotivo = new Label();
        $obLblMotivo->setId("stMotivo");
        $obLblMotivo->setRotulo("Motivo da Viagem");
        $obLblMotivo->setValue( $rsDiaria->getCampo('motivo') );

        $obTDiariasTipoDiaria = new TDiariasTipoDiaria();
        $obTDiariasTipoDiaria->setDado('cod_tipo',  $rsDiaria->getCampo('cod_tipo') );
        $obTDiariasTipoDiaria->recuperaPorChave($rsTipoDiaria);

        $obLblTipoDiaria = new Label();
        $obLblTipoDiaria->setId("stTipoDiaria");
        $obLblTipoDiaria->setRotulo("Tipo de Diária");

        if ($rsTipoDiaria->getNumLinhas()>0) {
            $obLblTipoDiaria->setValue( $rsTipoDiaria->getCampo('nom_tipo') );
        }

        $obLblValorDiaria->setValue( "R$ ".number_format( $rsDiaria->getCampo('vl_unitario'), 2, ",", "." ) );

        $obLblQuantidade = new Label();
        $obLblQuantidade->setId("nuQuantidade");
        $obLblQuantidade->setRotulo("Quantidade");
        $obLblQuantidade->setValue(  number_format($rsDiaria->getCampo('quantidade'), 2, ",", ".")  );

        $obLblValorTotal = new Label();
        $obLblValorTotal->setId("nuValorTotal");
        $obLblValorTotal->setRotulo("Valor Total");
        $obLblValorTotal->setValue(  "R$ ".number_format($rsDiaria->getCampo('vl_total'), 2, ",", ".")  );

        $obLblAutorizacaoEmpenho = new Label();
        $obLblAutorizacaoEmpenho->setRotulo("Autorização Empenho");
        $obLblAutorizacaoEmpenho->setValue(  $rsDiaria->getCampo('autorizacao_empenho')  );

        $obLblDataAutorizacaoEmpenho = new Label();
        $obLblDataAutorizacaoEmpenho->setRotulo("Data do Pagamento");
        $obLblDataAutorizacaoEmpenho->setValue(  $rsDiaria->getCampo('dt_autorizacao_empenho')  );

        /*
        $obLblDtPagamento = new Label();
        $obLblDtPagamento->setId("dtPagamento");
        $obLblDtPagamento->setRotulo("Data do Pagamento");
        $obLblDtPagamento->setValue( $rsDiaria->getCampo('dt_pagamento') );
        */

        //////////////////////

        $obBtnLista = new Button;
        $obBtnLista->setName              ( "btLista"    );
        $obBtnLista->setId                ( "btLista"    );
        $obBtnLista->setValue             ( "Ok/Lista"   );
        $obBtnLista->obEvento->setOnClick ( " salvarLista(); " );

        $obBtnFiltro = new Button;
        $obBtnFiltro->setName              ( "btFiltro"    );
        $obBtnFiltro->setId                ( "btFiltro"    );
        $obBtnFiltro->setValue             ( "Ok/Filtro"   );
        $obBtnFiltro->obEvento->setOnClick ( " salvarFiltro(); " );

        $obBtnRecibo = new Button;
        $obBtnRecibo->setName              ( "btRecibo"    );
        $obBtnRecibo->setId                ( "btRecibo"    );
        $obBtnRecibo->setValue             ( "Recibo"      );
        $obBtnRecibo->obEvento->setOnClick ( " salvarRecibo(); " );

        $obHdnRetorno = new Hidden();
        $obHdnRetorno->setName("stRetorno");
        $obHdnRetorno->setId("stRetorno");
        $obHdnRetorno->setValue("filtro");

        /////////////////////

        $obForm->setTarget ( "telaPrincipal" );

        $obFormulario = new Formulario;
        $obFormulario->addForm( $obForm );
        $obFormulario->addHidden                        	( $obHdnAcao                                                            );
        $obFormulario->addHidden                        	( $obHdnCtrl                                                            );
        $obFormulario->addHidden                        	( $obHdnRetorno                                                         );
        $obFormulario->addTitulo 							( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() , "right" 	);
        $obFormulario->addTitulo             				( "Dados do Servidor"                     		                        );
        $obFormulario->addHidden         				    ( $obHdnCodContrato     									            );
        $obFormulario->addHidden         				    ( $obHdnRegistro     									                );
        $obFormulario->addHidden         				    ( $obHdnCodDiaria     									                );
        $obFormulario->addHidden         				    ( $obHdnTimestamp     									                );
        $obFormulario->addComponente                        ( $obLblNome                                                            );
        $obFormulario->addComponente                        ( $obLblRegimeSubFun                                                    );
        $obFormulario->addComponente                        ( $obLblLotacaoDescricao                                                );
        $obFormulario->addTitulo             				( "Informações das Diárias"                     		                );
        $obFormulario->addComponente                        ( $obLblTipoNormaNorma                                                  );
        $obFormulario->addComponente                        ( $obLblDtAto                                                           );
        $obFormulario->addComponente                        ( $obLblPeriodoViagem                                                   );
        $obFormulario->addComponente                        ( $obLblPais                                                            );
        $obFormulario->addComponente                        ( $obLblEstado                                                          );
        $obFormulario->addComponente                        ( $obLblMunicipio                                                       );
        $obFormulario->addComponente                        ( $obLblMotivo                                                          );
        $obFormulario->addComponente                        ( $obLblTipoDiaria                                                      );
        $obFormulario->addComponente                        ( $obLblValorDiaria                                                     );
        $obFormulario->addComponente                        ( $obLblQuantidade                                                      );
        $obFormulario->addComponente                        ( $obLblValorTotal                                                      );
        //$obFormulario->addComponente                        ( $obLblDtPagamento                                                     );

        if ( $rsDiaria->getCampo('autorizacao_empenho') != "" ) {
            $obFormulario->addTitulo             				( "Informações de Pagamento das Diárias"                     		);
            $obFormulario->addComponente                        ( $obLblAutorizacaoEmpenho                                          );
            $obFormulario->addComponente                        ( $obLblDataAutorizacaoEmpenho                                      );
        }

        $obFormulario->defineBarra                          ( array($obBtnLista,$obBtnFiltro,$obBtnRecibo)                          );
        $obFormulario->Show();

}

include_once($pgJS);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
