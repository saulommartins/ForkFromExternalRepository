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
  * Página de
  * Data de criação : 09/06/2005

    * @author Programador: Vandré Miguel Ramos

    $Id: FMManterAssentamentoAbaAssentamento.php 66365 2016-08-18 14:39:09Z evandro $

    Caso de uso: uc-04.04.08
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
$obTxtClassificacao = new TextBox;
$obTxtClassificacao->setRotulo              ( "Classificação"                                   );
$obTxtClassificacao->setTitle               ( "Informe a classificação do assentamento."         );
$obTxtClassificacao->setName                ( "inCodClassificacaoTxt"                           );
$obTxtClassificacao->setValue               ( $inCodClassificacaoTxt                            );
$obTxtClassificacao->setSize                ( 6                                                 );
$obTxtClassificacao->setMaxLength           ( 3                                                 );
$obTxtClassificacao->setInteiro             ( true                                              );
if( $stAcao == 'incluir' )
    $obTxtClassificacao->setNull            ( false                                             );

$obHdnTipoClassificacao =  new Hidden;
$obHdnTipoClassificacao->setName            ( "hdnCodTipo"                                      );
$obHdnTipoClassificacao->setValue           ( $hdnCodTipo                                       );

$obHdnCodClassificacao =  new Hidden;
$obHdnCodClassificacao->setName             ( "hdnCodClassificacao"                             );
$obHdnCodClassificacao->setValue            ( $inCodClassificacaoTxt                            );

$obCmbClassificacao = new Select;
$obCmbClassificacao->setRotulo              ( "Classificação"                                   );
$obCmbClassificacao->setTitle               ( "Informe a classificação do assentamento."         );
$obCmbClassificacao->setName                ( "inCodClassificacao"                              );
$obCmbClassificacao->setValue               ( $inCodClassificacao                               );
$obCmbClassificacao->setStyle               ( "width: 200px"                                    );
$obCmbClassificacao->setCampoID             ( "cod_classificacao"                               );
$obCmbClassificacao->setCampoDesc           ( "descricao"                                       );
$obCmbClassificacao->addOption              ( "", "Selecione"                                   );
$obCmbClassificacao->preencheCombo          ( $rsClassificacao                                  );
$obCmbClassificacao->obEvento->setOnChange  ( "buscaValor('desbloqueiaAbas');"                  );
if( $stAcao == 'incluir' )
    $obCmbClassificacao->setNull            ( false                                             );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo                  ( "Descrição"                                       );
$obTxtDescricao->setTitle                   ( "Informe a descrição do assentamento."             );
$obTxtDescricao->setName                    ( "stDescricao"                                     );
$obTxtDescricao->setValue                   ( $stDescricao                                      );
$obTxtDescricao->setSize                    ( 40                                                );
$obTxtDescricao->setMaxLength               ( 80                                                );
$obTxtDescricao->setNull                    ( false                                             );

$obHdnDescricao =  new Hidden;
$obHdnDescricao->setName                    ( "hdnDescricao"                                    );
$obHdnDescricao->setValue                   ( $stDescricao                                      );

$obLblDescricao = new Label;
$obLblDescricao->setRotulo                  ( "Descrição"                                       );
$obLblDescricao->setName                    ( "lblDescricaoAssentamento"                        );
$obLblDescricao->setValue                   ( $stDescricao                                      );

$obTxtSigla = new TextBox;
$obTxtSigla->setRotulo                      ( "Sigla"                                           );
$obTxtSigla->setTitle                       ( "Informe a sigla do assentamento."                 );
$obTxtSigla->setName                        ( "stSigla"                                         );
$obTxtSigla->setValue                       ( $stSigla                                          );
$obTxtSigla->setSize                        ( 10                                                );
$obTxtSigla->setMaxLength                   ( 10                                                );
$obTxtSigla->setNull                        ( false                                             );
$obTxtSigla->setToLowerCase                 ( true                                              );

$obHdnSigla =  new Hidden;
$obHdnSigla->setName                        ( "hdnSigla"                                        );
$obHdnSigla->setValue                       ( $stSigla                                          );

$obLblSigla = new Label;
$obLblSigla->setRotulo                      ( "Sigla"                                           );
$obLblSigla->setName                        ( "lblSigla"                                        );
$obLblSigla->setValue                       ( $stSigla                                          );

$obTxtAbreviacao = new TextBox;
$obTxtAbreviacao->setRotulo                      ( "Abreviação"                                           );
$obTxtAbreviacao->setTitle                       ( "Informe a abreviação do assentamento."                 );
$obTxtAbreviacao->setName                        ( "stAbreviacao"                                         );
$obTxtAbreviacao->setValue                       ( $stAbreviacao                                          );
$obTxtAbreviacao->setSize                        ( 5                                                );
$obTxtAbreviacao->setMaxLength                   ( 3                                                );

$obChkAssentamentoInicio = new CheckBox;
$obChkAssentamentoInicio->setName           ( "boAssentamentoInicio"                                                       );
$obChkAssentamentoInicio->setRotulo         ( "Assentamento de Início"                                                     );
$obChkAssentamentoInicio->setTitle          ( "Informe se o assentamento será classificado como assentamento de início."    );
$obChkAssentamentoInicio->setValue          ( "t"                                                                          );
$obChkAssentamentoInicio->setChecked        ( $boGradeEfetividade == 't'                                                   );

$obChkGradeEfetividade = new CheckBox;
$obChkGradeEfetividade->setName             ( "boGradeEfetividade"                              );
$obChkGradeEfetividade->setRotulo           ( "Grade de Efetividade"                            );
$obChkGradeEfetividade->setTitle            ( "Informe se o assentamento deve ser relacionado na grade de efetividade." );
$obChkGradeEfetividade->setValue            ( "t"                                               );
$obChkGradeEfetividade->setChecked          ( $boGradeEfetividade == 't'                        );

$obChkRelFuncaoGratificada = new CheckBox;
$obChkRelFuncaoGratificada->setName         ( "boRelFuncaoGratificada"                          );
$obChkRelFuncaoGratificada->setRotulo       ( "Relatório de Função Gratificada"                 );
$obChkRelFuncaoGratificada->setTitle        ( "Informe se o assentamento deve ser relacionado no relatório de função gratificada." );
$obChkRelFuncaoGratificada->setValue        ( "t"                                               );
$obChkRelFuncaoGratificada->setChecked      ( $boRelFuncaoGratificada == 't'                    );

$obChkEventoAutomatico = new CheckBox;
$obChkEventoAutomatico->setName                   ( "boEventoAutomatico"                              );
$obChkEventoAutomatico->setRotulo                 ( "Lançar Evento Automático"                        );
$obChkEventoAutomatico->setTitle                  ( "Informe se o assentamento possuirá lançamento automático."  );
$obChkEventoAutomatico->setValue                  ( "t"                                               );
$obChkEventoAutomatico->setChecked                ( $boEventoAutomatico == 't'                        );
$obChkEventoAutomatico->obEvento->setOnChange     ( "buscaValor('MostraEvento');"                     );

$obTxtTipoNorma = new TextBox;
$obTxtTipoNorma->setRotulo                  ( "Tipo de Norma"                                   );
$obTxtTipoNorma->setTitle                   ( "Informe o tipo de norma."                         );
$obTxtTipoNorma->setName                    ( "inCodTipoNormaTxt"                               );
$obTxtTipoNorma->setValue                   ( $inCodTipoNormaTxt                                );
$obTxtTipoNorma->setSize                    ( 6                                                 );
$obTxtTipoNorma->setMaxLength               ( 4                                                 );
$obTxtTipoNorma->setInteiro                 ( true                                              );
$obTxtTipoNorma->obEvento->setOnChange      ( "buscaValor('MontaNorma');"                       );
if( $stAcao == 'incluir' )
    $obTxtTipoNorma->setNull                ( false                                             );

$obCmbTipoNorma = new Select;
$obCmbTipoNorma->setRotulo                  ( "Tipo de Norma"                                   );
$obCmbTipoNorma->setTitle                   ( "Informe o tipo de norma."                         );
$obCmbTipoNorma->setName                    ( "inCodTipoNorma"                                  );
$obCmbTipoNorma->setValue                   ( $inCodTipoNorma                                   );
$obCmbTipoNorma->setStyle                   ( "width: 200px"                                    );
$obCmbTipoNorma->setCampoID                 ( "cod_tipo_norma"                                  );
$obCmbTipoNorma->setCampoDesc               ( "nom_tipo_norma"                                  );
$obCmbTipoNorma->addOption                  ( "", "Selecione"                                   );
$obCmbTipoNorma->preencheCombo              ( $rsTipoNorma                                      );
$obCmbTipoNorma->obEvento->setOnChange      ( "buscaValor('MontaNorma');"                       );
if( $stAcao == 'incluir' )
    $obCmbTipoNorma->setNull                ( false                                             );

$obHdnTipoNorma =  new Hidden;
$obHdnTipoNorma->setName                    ( "hdnCodTipoNorma"                                 );
$obHdnTipoNorma->setValue                   ( $inCodTipoNorma                                   );

$obTxtNorma = new TextBox;
$obTxtNorma->setRotulo                      ( "Norma"                                           );
$obTxtNorma->setTitle                       ( "Informe a norma."                                 );
$obTxtNorma->setName                        ( "inCodNormaTxt"                                   );
$obTxtNorma->setValue                       ( $inCodNormaTxt                                    );
$obTxtNorma->setSize                        ( 6                                                 );
$obTxtNorma->setMaxLength                   ( 6                                                 );
$obTxtNorma->setInteiro                     ( true                                              );
if( $stAcao == 'incluir' )
    $obTxtNorma->setNull                    ( false                                             );

$obCmbNorma = new Select;
$obCmbNorma->setRotulo                      ( "Norma"                                           );
$obCmbNorma->setTitle                       ( "Informe a norma."                                 );
$obCmbNorma->setName                        ( "inCodNorma"                                      );
$obCmbNorma->setValue                       ( $inCodNorma                                       );
$obCmbNorma->setStyle                       ( "width: 200px"                                    );
$obCmbNorma->setCampoID                     ( "num_norma"                                       );
$obCmbNorma->setCampoDesc                   ( "nom_norma"                                       );
$obCmbNorma->addOption                      ( "", "Selecione"                                   );
$obCmbNorma->obEvento->setOnChange          ( "buscaValor('preencheDataNormaSelecionada');"     );
if( $stAcao == 'incluir' )
    $obCmbNorma->setNull                    ( false                                             );

$obHdnNorma =  new Hidden;
$obHdnNorma->setName                        ( "hdnCodNorma"                                     );
$obHdnNorma->setValue                       ( $inCodNorma                                       );

$obCmbRegimeSubDivisao = new SelectMultiplo();
$obCmbRegimeSubDivisao->setName             ( 'inCodRegime'                                     );
$obCmbRegimeSubDivisao->setRotulo           ( "Regime/SubDivisão"                               );
$obCmbRegimeSubDivisao->setNull             ( false                                             );
$obCmbRegimeSubDivisao->setTitle            ( "Informe o regime/subdivisão do assentameto."      );

// lista de atributos disponiveis
$obCmbRegimeSubDivisao->SetNomeLista1       ( 'inCodRegimeDisponiveis'                          );
$obCmbRegimeSubDivisao->setCampoId1         ( '[cod_sub_divisao]/[nom_regime]/[nom_sub_divisao]');
$obCmbRegimeSubDivisao->setCampoDesc1       ( '[nom_regime]/[nom_sub_divisao]'                  );
$obCmbRegimeSubDivisao->setStyle1           ( "width: 300px"                                    );
$obCmbRegimeSubDivisao->SetRecord1          ( $rsSubDivisaoDisponiveis                          );

// lista de atributos selecionados
$obCmbRegimeSubDivisao->SetNomeLista2       ( 'inCodRegimeSelecionados'                         );
$obCmbRegimeSubDivisao->setCampoId2         ( '[cod_sub_divisao]/[nom_regime]/[nom_sub_divisao]');
$obCmbRegimeSubDivisao->setCampoDesc2       ( '[nom_regime]/[nom_sub_divisao]'                  );
$obCmbRegimeSubDivisao->setStyle2           ( "width: 300px"                                    );
$obCmbRegimeSubDivisao->SetRecord2          ( $rsSubDivisaoSelecionados                         );

$obTxtEsfera = new TextBox;
$obTxtEsfera->setRotulo                     ( "Esfera"                                          );
$obTxtEsfera->setTitle                      ( "Informe a esfera do assentamento."                );
$obTxtEsfera->setName                       ( "inCodEsferaTxt"                                  );
$obTxtEsfera->setValue                      ( $inCodEsferaTxt                                   );
$obTxtEsfera->setSize                       ( 6                                                 );
$obTxtEsfera->setMaxLength                  ( 3                                                 );
$obTxtEsfera->setInteiro                    ( true                                              );
$obTxtEsfera->obEvento->setOnChange("buscaValor('processarOperador')");

$obCmbEsfera = new Select;
$obCmbEsfera->setRotulo                     ( "Esfera"                                          );
$obCmbEsfera->setTitle                      ( "Informe a esfera do assentamento."                );
$obCmbEsfera->setName                       ( "inCodEsfera"                                     );
$obCmbEsfera->setValue                      ( $inCodEsfera                                      );
$obCmbEsfera->setStyle                      ( "width: 200px"                                    );
$obCmbEsfera->setCampoID                    ( "cod_esfera"                                      );
$obCmbEsfera->setCampoDesc                  ( "descricao"                                       );
$obCmbEsfera->addOption                     ( "", "Selecione"                                   );
$obCmbEsfera->setNull                       ( false                                             );
$obCmbEsfera->preencheCombo                 ( $rsEsfera                                         );
$obCmbEsfera->obEvento->setOnChange("buscaValor('processarOperador')");

$obTxtOperador = new TextBox;
$obTxtOperador->setRotulo                   ( "Operador"                                        );
$obTxtOperador->setTitle                    ( "Informe o operador do assentamento."              );
$obTxtOperador->setName                     ( "inCodOperadorTxt"                                );
$obTxtOperador->setId                       ( "inCodOperadorTxt"                                );
$obTxtOperador->setValue                    ( $inCodOperadorTxt                                 );
$obTxtOperador->setSize                     ( 6                                                 );
$obTxtOperador->setMaxLength                ( 3                                                 );
$obTxtOperador->setInteiro                  ( true                                              );
$obTxtOperador->setNull                     ( false                                             );

$obCmbOperador = new Select;
$obCmbOperador->setRotulo                   ( "Operador"                                        );
$obCmbOperador->setTitle                    ( "Informe o operador do assentamento."              );
$obCmbOperador->setName                     ( "inCodOperador"                                   );
$obCmbOperador->setId                       ( "inCodOperador"                                   );
$obCmbOperador->setValue                    ( $inCodOperador                                    );
$obCmbOperador->setStyle                    ( "width: 200px"                                    );
$obCmbOperador->setCampoID                  ( "cod_operador"                                    );
$obCmbOperador->setCampoDesc                ( "descricao"                                       );
$obCmbOperador->addOption                   ( "", "Selecione"                                   );
$obCmbOperador->setNull                     ( false                                             );
$obCmbOperador->preencheCombo               ( $rsOperador                                       );

$obHdnOperador =  new Hidden;
$obHdnOperador->setName                     ( "hdnCodOperador"                                  );
$obHdnOperador->setValue                    ( $inCodOperador                                    );

$obSpnEvento = new Span;
$obSpnEvento->setId                         ( 'spnEvento'                                       );

$obHdnEventoEval = new HiddenEval;
$obHdnEventoEval->setName                   ( "stEventoEval"                                    );
$obHdnEventoEval->setValue                  ( ""                                                );

$obDataInicioAssentamento = new Data;
$obDataInicioAssentamento->setRotulo                    ( "Data Inicial"                                    );
$obDataInicioAssentamento->setTitle                     ( "Informe a data inicial referente ao período do assentamento." );
$obDataInicioAssentamento->setName                      ( "dtDataInicioAssentamento"                        );
$obDataInicioAssentamento->setSize                      ( 10                                                );
$obDataInicioAssentamento->setMaxLength                 ( 10                                                );
$obDataInicioAssentamento->setInteiro                   ( false                                             );
$obDataInicioAssentamento->setValue                     ( $dtDataInicioAssentamento                         );

$obDataFinalAssentamento = new Data;
$obDataFinalAssentamento->setRotulo                     ( "Data Final"                                      );
$obDataFinalAssentamento->setTitle                      ( "Informe a data final referente ao período do assentamento." );
$obDataFinalAssentamento->setName                       ( "dtDataFinalAssentamento"                         );
$obDataFinalAssentamento->setSize                       ( 10                                                );
$obDataFinalAssentamento->setMaxLength                  ( 10                                                );
$obDataFinalAssentamento->setInteiro                    ( false                                             );
$obDataFinalAssentamento->setValue                      ( $dtDataFinalAssentamento                          );

$obChkCancelarDireito = new CheckBox;
$obChkCancelarDireito->setName                          ( "boCancelarDireito"                               );
$obChkCancelarDireito->setRotulo                        ( "Cancelar Direito ao Encerrar Validade do Assentamento" );
$obChkCancelarDireito->setTitle                         ( "Informe se o assentamento continuará vigente mesmo após vencer a validade."  );
$obChkCancelarDireito->setValue                         ( "t"                                               );
$obChkCancelarDireito->setChecked                       ( $boCancelarDireito == 't'                         );

$obChkInformarEventosProporcionalizacao = new CheckBox;
$obChkInformarEventosProporcionalizacao->setName        ( "boInformarEventosProporcionalizacao"             );
$obChkInformarEventosProporcionalizacao->setRotulo      ( "Informar Eventos que Sofrem Proporcionalização"  );
$obChkInformarEventosProporcionalizacao->setTitle       ( "Informe se o assentamento possuirá eventos que sofrem proporcionalização."  );
$obChkInformarEventosProporcionalizacao->setValue       ( "t"                                               );
$obChkInformarEventosProporcionalizacao->setChecked     ( $boInformarEventosProporcionalizacao == 't'       );
$obChkInformarEventosProporcionalizacao->obEvento->setOnChange( "buscaValor('gerarSpanEventos2');"       );

$obSpnEvento2 = new Span;
$obSpnEvento2->setId                                    ( 'spnEvento2'                                      );

$obHdnEventoEval2 = new HiddenEval;
$obHdnEventoEval2->setName                              ( "stEventoEval2"                                   );
$obHdnEventoEval2->setValue                             ( ""                                                );

$obTxtMotivo = new TextBox;
$obTxtMotivo->setRotulo                   ( "Motivo"                                          );
$obTxtMotivo->setTitle                    ( "Informe o motivo do assentamento."               );
$obTxtMotivo->setName                     ( "inCodMotivoTxt"                                  );
$obTxtMotivo->setValue                    ( $inCodMotivoTxt                                   );
$obTxtMotivo->setSize                     ( 6                                                 );
$obTxtMotivo->setMaxLength                ( 3                                                 );
$obTxtMotivo->setInteiro                  ( true                                              );
$obTxtMotivo->setNull                     ( false                                             );

$obCmbMotivo = new Select;
$obCmbMotivo->setRotulo                   ( "Motivo"                                          );
$obCmbMotivo->setTitle                    ( "Informe o motivo do assentamento."               );
$obCmbMotivo->setName                     ( "inCodMotivo"                                     );
$obCmbMotivo->setValue                    ( $inCodMotivo                                      );
$obCmbMotivo->setStyle                    ( "width: 200px"                                    );
$obCmbMotivo->setCampoID                  ( "cod_motivo"                                      );
$obCmbMotivo->setCampoDesc                ( "descricao"                                       );
$obCmbMotivo->addOption                   ( "", "Selecione"                                   );
$obCmbMotivo->setNull                     ( false                                             );
$obCmbMotivo->preencheCombo               ( $rsMotivo                                         );
$obCmbMotivo->obEvento->setOnChange("buscaValor('gerarSpanMotivo');");

$obLblMotivo = new Label;
$obLblMotivo->setRotulo                   ( 'Motivo'                                          );
$obLblMotivo->setName                     ( 'stMotivo'                                        );
$obLblMotivo->setValue                    ( $stMotivo                                         );

$obLblDtPublicacao = new Label;
$obLblDtPublicacao->setRotulo                           ( 'Data de publicação'                              );
$obLblDtPublicacao->setName                             ( 'lbldtPublicacao'                                 );
$obLblDtPublicacao->setId                               ( 'lbldtPublicacao'                                 );
$obLblDtPublicacao->setValue                            ( $dtPublicacao                                     );

$obHdnDtPublicacao =  new Hidden;
$obHdnDtPublicacao->setName                             ( "hdndtPublicacao"                                 );
$obHdnDtPublicacao->setValue                            ( $hdndtPublicacao                                  );

$obRdnAssentamentoAutomaticoTrue = new Radio();
$obRdnAssentamentoAutomaticoTrue->setName               ( "boAssentamentoAutomatico"                        );
$obRdnAssentamentoAutomaticoTrue->setRotulo             ( "Gerar Assentamento Automático"                   );
$obRdnAssentamentoAutomaticoTrue->setTitle              ( "Informe se o assentamento será gerado automaticamente para o servidor." );
$obRdnAssentamentoAutomaticoTrue->setLabel              ( "Sim"                                             );
$obRdnAssentamentoAutomaticoTrue->setValue              ( "t"                                               );
if ($boAssentamentoAutomatico == "t") {
    $obRdnAssentamentoAutomaticoTrue->setChecked        ( true                                              );
}

$obRdnAssentamentoAutomaticoFalse = new Radio();
$obRdnAssentamentoAutomaticoFalse->setName              ( "boAssentamentoAutomatico"                        );
$obRdnAssentamentoAutomaticoFalse->setRotulo            ( "Gerar Assentamento Automático"                   );
$obRdnAssentamentoAutomaticoFalse->setTitle             ( "Informe se o assentamento será gerado automaticamento para o servidor." );
$obRdnAssentamentoAutomaticoFalse->setLabel             ( "Não"                                             );
$obRdnAssentamentoAutomaticoFalse->setValue             ( "f"                                               );
if ( $boAssentamentoAutomatico == "f" or !isset($boAssentamentoAutomatico) ) {
    $obRdnAssentamentoAutomaticoFalse->setChecked       ( true                                              );
}

$obSpnMotivo = new Span();
$obSpnMotivo->setId("spnMotivo");

$obHdnMotido = new hiddenEval();
$obHdnMotido->setName("hdnMotivo");
