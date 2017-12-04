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
    * Formulário
    * Data de Criação: 10/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 31094 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-17 10:02:38 -0300 (Ter, 17 Jul 2007) $

    * Casos de uso: uc-04.05.29
*/

include_once(CAM_GRH_FOL_COMPONENTES."ISelectMultiploEvento.class.php");
$obISelectMultiploEvento = new ISelectMultiploEvento();
$obISelectMultiploEvento->SetRecord1(new recordset);
$obISelectMultiploEvento->setTitle("Selecione a Opções de Configuração (Lotação/Local/Atributo) e a Configuração para preencher os campos de Eventos.");

$obCmbConfiguracao = new Select;
$obCmbConfiguracao->setRotulo       ( "Configuração"                                         );
$obCmbConfiguracao->setName         ( "stConfiguracao"                                       );
$obCmbConfiguracao->setId           ( "stConfiguracao"                                       );
$obCmbConfiguracao->setStyle        ( "width: 200px"                                         );
$obCmbConfiguracao->setTitle        ( "Selecione o tipo de configuração da folha que sofrerá alteração.");
$obCmbConfiguracao->addOption       ( "", "Selecione"                                        );
$obCmbConfiguracao->addOption       ( "1","Salário"                                          );
$obCmbConfiguracao->addOption       ( "2","Férias"                                           );
$obCmbConfiguracao->addOption       ( "3","13° Salário"                                      );
$obCmbConfiguracao->addOption       ( "4","Rescisão"                                         );
$obCmbConfiguracao->addOption       ( "10","Todas Folhas"                                    );
$obCmbConfiguracao->obEvento->setOnChange("BloqueiaFrames(true,false);montaParametrosGET('preencherEventos','stOpcoesConfiguracaoEvento,stConfiguracao,HdninCodLotacaoEvento,inCodLocalEvento');");

include_once(CAM_GRH_PES_COMPONENTES."ISelectMultiploRegSubCarEsp.class.php");
$obISelectMultiploRegSubCarEsp = new ISelectMultiploRegSubCarEsp();
$obISelectMultiploRegSubCarEsp->setDisabledEspecialidade(true);

$obCkbSituacao1 = new Checkbox();
$obCkbSituacao1->setRotulo("Situação");
$obCkbSituacao1->setName("stSituacao1");
$obCkbSituacao1->setTitle("Marque as opções de cadastro dos servidores para configuração: Ativos, Aposentados, Pensionistas.");
$obCkbSituacao1->setValue("a");
$obCkbSituacao1->setLabel("Ativos");

$obCkbSituacao2 = new Checkbox();
$obCkbSituacao2->setRotulo("Situação");
$obCkbSituacao2->setName("stSituacao2");
$obCkbSituacao2->setTitle("Marque as opções de cadastro dos servidores para configuração: Ativos, Aposentados, Pensionistas.");
$obCkbSituacao2->setValue("o");
$obCkbSituacao2->setLabel("Aposentados");

$obCkbSituacao3 = new Checkbox();
$obCkbSituacao3->setRotulo("Situação");
$obCkbSituacao3->setName("stSituacao3");
$obCkbSituacao3->setTitle("Marque as opções de cadastro dos servidores para configuração: Ativos, Aposentados, Pensionistas.");
$obCkbSituacao3->setValue("p");
$obCkbSituacao3->setLabel("Pensionistas");

$obSpnComboOpcoesConfiguracaoEvento = new Span();
$obSpnComboOpcoesConfiguracaoEvento->setId("spnComboOpcoesConfiguracaoEvento");

$obSpnOpcoesConfiguracaoEventos = new Span();
$obSpnOpcoesConfiguracaoEventos->setId("spnOpcoesConfiguracaoEventos");

$obHdnOpcoesConfiguracaoEventos = new hidden();
$obHdnOpcoesConfiguracaoEventos->setId("hdnOpcoesConfiguracaoEventos");
$obHdnOpcoesConfiguracaoEventos->setValue("selecionaTodosSelect(inCodEventoSelecionados);selecionaTodosSelect(".$obISelectMultiploRegSubCarEsp->obCmbRegime->getName()."Selecionados);selecionaTodosSelect(".$obISelectMultiploRegSubCarEsp->obCmbSubDivisao->getName()."Selecionados);selecionaTodosSelect(".$obISelectMultiploRegSubCarEsp->obCmbCargo->getName()."Selecionados);");

$stJs  = "var url = '".CAM_GRH_FOL_INSTANCIAS."configuracao/OCManterAutorizacaoEmpenho.php?".Sessao::getId()."';\n";

//adicionado bloqueiaframes no botão de incluir e alterar
$onBtnIncluirEvento = new Button();
$onBtnIncluirEvento->setName("obBtnIncluirEvento");
$onBtnIncluirEvento->setID  ("obBtnIncluirEvento");
$onBtnIncluirEvento->setValue("Incluir");
$onBtnIncluirEvento->setTipo("button");
$onBtnIncluirEvento->setDisabled(false);
$onBtnIncluirEvento->obEvento->setOnClick("
                                           eval(BloqueiaFrames(true,false));
                                           eval(document.frm.hdnOpcoesConfiguracaoEventos.value);
                                           $stJs
                                           jQuery('#stCtrl').val('incluirEvento');
                                           jQuery.post(url, jQuery('#frm').serialize(),function (data) {executaJavaScript(data);},'html');
                                           ");

$onBtnAlterarEvento = new Button();
$onBtnAlterarEvento->setName("obBtnAlterarEvento");
$onBtnAlterarEvento->setID  ("obBtnAlterarEvento");
$onBtnAlterarEvento->setValue("Alterar");
$onBtnAlterarEvento->setTipo("button");
$onBtnAlterarEvento->setDisabled(true);
$onBtnAlterarEvento->obEvento->setOnClick("
                                           eval(BloqueiaFrames(true,false));
                                           eval(document.frm.hdnOpcoesConfiguracaoEventos.value);
                                           $stJs
                                           jQuery('#stCtrl').val('alterarEvento');
                                           jQuery.post(url, jQuery('#frm').serialize(),function (data) {executaJavaScript(data);},'html');
                                           ");

$onBtnLimparEvento = new Button();
$onBtnLimparEvento->setName("obBtnLimparEvento");
$onBtnLimparEvento->setId  ("obBtnLimparEvento");
$onBtnLimparEvento->setValue("Limpar");
$onBtnLimparEvento->setTipo("button");
$onBtnLimparEvento->setDisabled(false);
$onBtnLimparEvento->obEvento->setOnClick("
                                           $stJs
                                           jQuery('#stCtrl').val('limparEvento');
                                           jQuery.post(url, jQuery('#frm').serialize(),function (data) {executaJavaScript(data);},'html');
                                           ");

$arBotoesEventos = array($onBtnIncluirEvento,$onBtnAlterarEvento,$onBtnLimparEvento);

$obSpnConfiguracoesEventos = new Span();
$obSpnConfiguracoesEventos->setId("spnConfiguracoesEventos");

$obHdnNumPAOEvento = new Hidden();
$obHdnNumPAOEvento->setId('inHdnNumPAOEvento');
$obHdnNumPAOEvento->setName('inHdnNumPAOEvento');
$obHdnNumPAOEvento->obEvento->setOnChange("montaParametrosGET('preencherDotacao','stMascClassificacao,inNumPAOEvento');");

$obHdnDotacaoEvento = new Hidden();
$obHdnDotacaoEvento->setId('stHdnDotacaoEvento');
$obHdnDotacaoEvento->setName('stHdnDotacaoEvento');

include_once ( CAM_GF_ORC_COMPONENTES.'IPopUpPAO.class.php' );
$obIPopUpPAO = new IPopUpPAO(array('extensao'=>'Evento'));
$obIPopUpPAO->obCampoCod->obEvento->setOnBlur(" setTimeout(function(){jQuery('#inHdnNumPAOEvento').change()},500);");

include_once(CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php");
$obROrcamentoDespesa = new ROrcamentoDespesa();
$stMascaraRubrica    = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();

isset($stRubricaDespesa) ? $stRubricaDespesa : $stRubricaDespesa = "";
isset($stMascaraRubrica) ? $stMascaraRubrica : $stMascaraRubrica = "";
$obBscRubricaDespesaSal = new BuscaInner;
$obBscRubricaDespesaSal->setRotulo                         ( "Rubrica de Despesa"                                            );
$obBscRubricaDespesaSal->setTitle                          ( "Selecione a rubrica de despesa para a configuração do evento." );
$obBscRubricaDespesaSal->setId                             ( "stRubricaDespesa"                                              );
$obBscRubricaDespesaSal->obCampoCod->setName               ( "stMascClassificacao"                                  );
$obBscRubricaDespesaSal->obCampoCod->setId                 ( "stMascClassificacao"                                  );
$obBscRubricaDespesaSal->obCampoCod->setSize               ( 18                                                     );
$obBscRubricaDespesaSal->obCampoCod->setMaxLength          ( 22                                                     );
$obBscRubricaDespesaSal->obCampoCod->setValue              ( $stRubricaDespesa                                      );
$obBscRubricaDespesaSal->obCampoCod->setAlign              ( "LEFT"                                                 );
$obBscRubricaDespesaSal->obCampoCod->setPreencheComZeros   ( "D"                                                    );
$obBscRubricaDespesaSal->obCampoCod->setMascara            ( $stMascaraRubrica                                      );
$obBscRubricaDespesaSal->obCampoCod->obEvento->setOnBlur   ( " montaParametrosGET('preencheMascClassificacao','stMascClassificacao,inNumPAOEvento'); if (this.value == '') { jQuery('#HdnstMascClassificacao').val(); jQuery('#hdnStRubricaDespesa').val(''); jQuery('#stRubricaDespesa').html('&nbsp;'); jQuery('#inCodDespesa').removeOption(/./); jQuery('#inCodDespesa').addOption('', 'Selecione'); }" );
$obBscRubricaDespesaSal->obCampoCodHidden->setId           ( "HdnstMascClassificacao"                               );
$obBscRubricaDespesaSal->obCampoDescrHidden->setId         ( "stRubricaDespesa"                                     );
$obBscRubricaDespesaSal->setFuncaoBusca                    ( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','stMascClassificacao','stRubricaDespesa','','".Sessao::getId()."&mascClassificacao=".$stMascaraRubrica."','800','550');" );

$obCmbDotacao = new Select;
$obCmbDotacao->setRotulo       ( "Dotação"                               );
$obCmbDotacao->setName         ( "inCodDespesa"                          );
$obCmbDotacao->setId           ( "inCodDespesa"                          );
$obCmbDotacao->setStyle        ( "width: 600px"                          );
$obCmbDotacao->setTitle        ( "Selecione a dotação para configuração do evento." );
$obCmbDotacao->addOption       ( "", "Selecione"                         );

?>
