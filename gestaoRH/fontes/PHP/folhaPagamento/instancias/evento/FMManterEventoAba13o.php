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
* Página de Formulario de Evento - Aba 13o Salário
* Data de Criação   : 29/08/2005

* @author Analista: Leandro Oliveira
* @author Programador: Eduardo Antunez

* @ignore

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-11-20 15:48:41 -0200 (Ter, 20 Nov 2007) $

Caso de uso: uc-04.05.06
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$obBscRubricaDespesa13o = new BuscaInner;
$obBscRubricaDespesa13o->setRotulo                         ( "Rubrica de despesa"                                          );
$obBscRubricaDespesa13o->setTitle                          ( "Informe uma rubrica de despesa para o evento de 13o salário" );
$obBscRubricaDespesa13o->setId                             ( "stRubricaDespesa13o"                                         );
$obBscRubricaDespesa13o->obCampoCod->setName               ( "stMascClassificacao13o"                                      );
$obBscRubricaDespesa13o->obCampoCod->setSize               ( 18                                                            );
$obBscRubricaDespesa13o->obCampoCod->setMaxLength          ( 22                                                            );
$obBscRubricaDespesa13o->obCampoCod->setValue              ( $stRubricaDespesa13o                                          );
$obBscRubricaDespesa13o->obCampoCod->setAlign              ( "LEFT"                                                        );
$obBscRubricaDespesa13o->obCampoCod->setPreencheComZeros   ( "D" );
$obBscRubricaDespesa13o->obCampoCod->obEvento->setOnChange ( "buscaValor('preencheMascClassificacao13o','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."');" );
$obBscRubricaDespesa13o->setFuncaoBusca                    ( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','stMascClassificacao13o','stRubricaDespesa13o','&mascClassificacao=$stMascaraElementoDespesa','".Sessao::getId()."','800','550');" );

$obTxtDescricao13o = new TextBox;
$obTxtDescricao13o->setRotulo              ( "*Descrição"                                                                    );
$obTxtDescricao13o->setTitle               ( "Informe a descrição auxiliar para a particularidade do evento de 13o salário" );
$obTxtDescricao13o->setName                ( "stDescricao13o"                                                               );
$obTxtDescricao13o->setValue               ( $stDescricao13o                                                                );
$obTxtDescricao13o->setSize                ( 40                                                                             );
$obTxtDescricao13o->setMaxLength           ( 80                                                                             );
$obTxtDescricao13o->setCaracteresAceitos   ( "[0-9a-zA-Z áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ%--/*+%]"                               );
$obTxtDescricao13o->setEspacosExtras       ( false                                                                          );

$obTxtTipoMedia13o = new TextBox;
$obTxtTipoMedia13o->setRotulo                         ( "Tipo de Média"                                           );
$obTxtTipoMedia13o->setName                           ( "inCodigoTipoMedia13o"                                    );
$obTxtTipoMedia13o->setValue                          ( $inCodigoTipoMedia13o                                     );
$obTxtTipoMedia13o->setTitle                          ( "Selecione o tipo de média para cálculo do salário."      );
$obTxtTipoMedia13o->setSize                           ( 3                                                         );
$obTxtTipoMedia13o->setMaxLength                      ( 3                                                         );
$obTxtTipoMedia13o->setInteiro                        ( true                                                      );
$obTxtTipoMedia13o->setPreencheComZeros               ( "E"                                                       );
$obTxtTipoMedia13o->setMascara                        ( "999"                                                     );
$obTxtTipoMedia13o->obEvento->setOnChange             ( "buscaValor('preencherObservacao13o');"                   );

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoMedia.class.php");
$obTFolhaPagamentoTipoMedia = new TFolhaPagamentoTipoMedia;
$stFiltro = " WHERE desdobramento = 'D'";
$stOrdem = " ORDER BY descricao";
$obTFolhaPagamentoTipoMedia->recuperaTodos($rsTipoMedia,$stFiltro,$stOrdem);

$obCmbTipoMedia13o = new Select;
$obCmbTipoMedia13o->setName                           ( "stCodigoTipoMedia13o"                                    );
$obCmbTipoMedia13o->setValue                          ( $inCodigoTipoMedia13o                                     );
$obCmbTipoMedia13o->setRotulo                         ( "Tipo de Média"                                           );
$obCmbTipoMedia13o->setTitle                          ( "Selecione o tipo de média para cálculo do salário."      );
$obCmbTipoMedia13o->setCampoId                        ( "[codigo]"                                                );
$obCmbTipoMedia13o->setCampoDesc                      ( "descricao"                                               );
$obCmbTipoMedia13o->addOption                         ( "", "Selecione"                                           );
$obCmbTipoMedia13o->preencheCombo                     ( $rsTipoMedia                                              );
$obCmbTipoMedia13o->setStyle                          ( "width: 250px"                                            );
$obCmbTipoMedia13o->obEvento->setOnChange             ( "buscaValor('preencherObservacao13o');"                   );

$obLblObservacao13o = new Label;
$obLblObservacao13o->setValue                          ( "&nbsp;" );
$obLblObservacao13o->setRotulo                         ( "Observação" );
$obLblObservacao13o->setName                           ( "stObservacao13o" );
$obLblObservacao13o->setId                             ( "stObservacao13o" );

//$obCmbRegimeSubDivisao13o = new SelectMultiplo();
//$obCmbRegimeSubDivisao13o->setName         ( 'inCodSubDivisao13o'                                                           );
//$obCmbRegimeSubDivisao13o->setRotulo       ( "*Regime/subdivisões"                                                           );
//$obCmbRegimeSubDivisao13o->setTitle        ( "Selecione os regimes/subdivisões associados ao evento de 13o salário"         );
//$obCmbRegimeSubDivisao13o->SetNomeLista1   ( 'inCodSubDivisaoDisponiveis13o'                                                );
//$obCmbRegimeSubDivisao13o->setCampoId1     ( '[cod_sub_divisao]/[nom_regime]/[nom_sub_divisao]'                             );
//$obCmbRegimeSubDivisao13o->setCampoDesc1   ( '[nom_regime]/[nom_sub_divisao]'                                               );
//$obCmbRegimeSubDivisao13o->setStyle1       ( "width: 300px"                                                                 );
//$obCmbRegimeSubDivisao13o->SetRecord1      ( $rsSubDivisaoDisponiveis13o                                                    );
//$obCmbRegimeSubDivisao13o->SetNomeLista2   ( 'inCodSubDivisaoSelecionados13o'                                               );
//$obCmbRegimeSubDivisao13o->setCampoId2     ( '[cod_sub_divisao]/[nom_regime]/[nom_sub_divisao]'                             );
//$obCmbRegimeSubDivisao13o->setCampoDesc2   ( '[nom_regime]/[nom_sub_divisao]'                                               );
//$obCmbRegimeSubDivisao13o->setStyle2       ( "width: 300px"                                                                 );
//$obCmbRegimeSubDivisao13o->SetRecord2      ( $rsSubDivisaoSelecionados13o                                                   );
//$stOnClick = "selecionaSubDivisao('13o',true);buscaValor('preencheCargoEspecialidade13o');selecionaSubDivisao('13o',false);";
//$obCmbRegimeSubDivisao13o->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
//$obCmbRegimeSubDivisao13o->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
//$obCmbRegimeSubDivisao13o->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
//$obCmbRegimeSubDivisao13o->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
//$obCmbRegimeSubDivisao13o->obSelect1->obEvento->setOnDblClick( $stOnClick );
//$obCmbRegimeSubDivisao13o->obSelect2->obEvento->setOnDblClick( $stOnClick );
//
//$obCmbCargo13o = new SelectMultiplo();
//$obCmbCargo13o->setName                    ( 'inCodCargo13o'                                                                );
//$obCmbCargo13o->setRotulo                  ( "*Cargos vinculados"                                                            );
//$obCmbCargo13o->setTitle                   ( "Selecione os cargos/especialidades associados ao evento de 13o salário"       );
//$obCmbCargo13o->SetNomeLista1              ( 'inCodCargoDisponiveis13o'                                                     );
//$obCmbCargo13o->setCampoId1                ( '[cod_cargo]'                                                                  );
//$obCmbCargo13o->setCampoDesc1              ( '[descr_cargo]/[descr_espec]'                                                  );
//$obCmbCargo13o->setStyle1                  ( "width: 300px"                                                                 );
//$obCmbCargo13o->SetRecord1                 ( $rsCargoDisponiveis13o                                                         );
//$obCmbCargo13o->SetNomeLista2              ( 'inCodCargoSelecionados13o'                                                    );
//$obCmbCargo13o->setCampoId2                ( '[cod_cargo]/[descr_cargo]/[cod_especialidade]/[descr_espec]'                  );
//$obCmbCargo13o->setCampoDesc2              ( '[descr_cargo]/[descr_espec]'                                                  );
//$obCmbCargo13o->setStyle2                  ( "width: 300px"                                                                 );
//$obCmbCargo13o->SetRecord2                 ( $rsCargoSelecionados13o                                                        );

$obBscFuncao13o = new BuscaInner;
$obBscFuncao13o->setRotulo ( "*Função"                                                                   );
$obBscFuncao13o->setTitle  ( "Selecione uma função para esta particularidade de evento de 13o salário"  );
$obBscFuncao13o->setId     ( "stFuncao13o"                                                              );
$obBscFuncao13o->obCampoCod->setName   ( "inCodFuncao13o" );
$obBscFuncao13o->obCampoCod->setValue  ( $inCodFuncao13o  );
$obBscFuncao13o->obCampoCod->obEvento->setOnChange("buscaValor('buscaFuncao13o');");
$obBscFuncao13o->obCampoCod->obEvento->setOnBlur  ("buscaValor('buscaFuncao13o');");
$obBscFuncao13o->obCampoCod->setMascara("99.99.999");
$obBscFuncao13o->setFuncaoBusca( "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncao13o','stFuncao13o','','".Sessao::getId()."','800','550');" );

$obBtnIncluir13o = new Button;
$obBtnIncluir13o->setName                  ( "btIncluir13o"                                                                 );
$obBtnIncluir13o->setValue                 ( "Incluir"                                                                      );
$obBtnIncluir13o->obEvento->setOnClick     ( "selecionaSubDivisao('13o',true);
                                              selecionaCargo('13o',true);
                                              buscaValor('incluiCaso13o');
                                              selecionaSubDivisao('13o',false);
                                              selecionaCargo('13o',false);"                                                 );

$obBtnAlterar13o = new Button;
$obBtnAlterar13o->setName                  ( "btAlterar13o"                                                                 );
$obBtnAlterar13o->setValue                 ( "Alterar"                                                                      );
$obBtnAlterar13o->obEvento->setOnClick     ( "selecionaSubDivisao('13o',true);
                                              selecionaCargo('13o',true);
                                              buscaValor('alteraCaso13o');
                                              selecionaSubDivisao('13o',false);
                                              selecionaCargo('13o',false);"                                                 );

$obBtnLimpar13o = new Button;
$obBtnLimpar13o->setName                   ( "btLimpar13o"                                                                  );
$obBtnLimpar13o->setValue                  ( "Limpar"                                                                       );
$obBtnLimpar13o->obEvento->setOnClick      ( "buscaValor('limpaCamposCaso13o')"                                             );

$obSpnLista13o = new Span;
$obSpnLista13o->setId ( "spnLista13o" );

$obHdn13o = new Hidden;
$obHdn13o->setName("hdn13o");

$obSpnEventoBase13o = new Span;
$obSpnEventoBase13o->setId ( "spnEventoBase13o" );

$obSpnSpan3 = new Span;
$obSpnSpan3->setId ( "spnSpan3" );

$obCkbConsPorporcaoAdiantamento = new CheckBox();
$obCkbConsPorporcaoAdiantamento->setRotulo("Considerar Proporção do Adiantamento");
$obCkbConsPorporcaoAdiantamento->setName("boConsProporcaoAdiantamento");
$obCkbConsPorporcaoAdiantamento->setValue("true");
$obCkbConsPorporcaoAdiantamento->setChecked(true);
$obCkbConsPorporcaoAdiantamento->setTitle("Marque para proporcionalizar o evento na concessão do adiantamento do décimo terceiro.");

?>
