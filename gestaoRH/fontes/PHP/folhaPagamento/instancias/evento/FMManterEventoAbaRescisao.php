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
* Página de Formulario de Evento - Aba Rescisão
* Data de Criação   : 29/08/2005

* @author Analista: Leandro Oliveira
* @author Programador: Eduardo Antunez

* @ignore

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2006-10-27 09:42:51 -0300 (Sex, 27 Out 2006) $

Caso de uso: uc-04.05.06
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$obBscRubricaDespesaRes = new BuscaInner;
$obBscRubricaDespesaRes->setRotulo                         ( "Rubrica de despesa"                                      );
$obBscRubricaDespesaRes->setTitle                          ( "Informe uma rubrica de despesa para o evento de rescisão");
$obBscRubricaDespesaRes->setId                             ( "stRubricaDespesaRes"                                     );
$obBscRubricaDespesaRes->obCampoCod->setName               ( "stMascClassificacaoRes"                                  );
$obBscRubricaDespesaRes->obCampoCod->setSize               ( 18                                                        );
$obBscRubricaDespesaRes->obCampoCod->setMaxLength          ( 22                                                        );
$obBscRubricaDespesaRes->obCampoCod->setValue              ( $stRubricaDespesaRes                                      );
$obBscRubricaDespesaRes->obCampoCod->setAlign              ( "LEFT"                                                    );
$obBscRubricaDespesaRes->obCampoCod->setPreencheComZeros   ( "D" );
$obBscRubricaDespesaRes->obCampoCod->obEvento->setOnChange ( "buscaValor('preencheMascClassificacaoRes','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."');" );
$obBscRubricaDespesaRes->setFuncaoBusca                    ( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','stMascClassificacaoRes','stRubricaDespesaRes','&mascClassificacao=$stMascaraElementoDespesa','".Sessao::getId()."','800','550');" );

$obTxtDescricaoRes = new TextBox;
$obTxtDescricaoRes->setRotulo              ( "*Descrição"                                                                );
$obTxtDescricaoRes->setTitle               ( "Informe a descrição auxiliar para a particularidade do evento de rescisão");
$obTxtDescricaoRes->setName                ( "stDescricaoRes"                                                           );
$obTxtDescricaoRes->setValue               ( $stDescricaoRes                                                            );
$obTxtDescricaoRes->setSize                ( 40                                                                         );
$obTxtDescricaoRes->setMaxLength           ( 80                                                                         );
$obTxtDescricaoRes->setCaracteresAceitos   ( "[0-9a-zA-Z áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ%--/*+%]"                           );
$obTxtDescricaoRes->setEspacosExtras       ( false                                                                      );

$obTxtTipoMediaRes = new TextBox;
$obTxtTipoMediaRes->setRotulo                         ( "Tipo de Média"                                           );
$obTxtTipoMediaRes->setName                           ( "inCodigoTipoMediaRes"                                    );
$obTxtTipoMediaRes->setValue                          ( $inCodigoTipoMediaRes                                     );
$obTxtTipoMediaRes->setTitle                          ( "Selecione o tipo de média para cálculo do salário."      );
$obTxtTipoMediaRes->setSize                           ( 3                                                         );
$obTxtTipoMediaRes->setMaxLength                      ( 3                                                         );
$obTxtTipoMediaRes->setInteiro                        ( true                                                      );
$obTxtTipoMediaRes->setPreencheComZeros               ( "E"                                                       );
$obTxtTipoMediaRes->setMascara                        ( "999"                                                     );
$obTxtTipoMediaRes->obEvento->setOnChange             ( "buscaValor('preencherObservacaoRes');"                   );

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoMedia.class.php");
$obTFolhaPagamentoTipoMedia = new TFolhaPagamentoTipoMedia;
$stFiltro = " WHERE desdobramento = 'R'";
$stOrdem = " ORDER BY descricao";
$obTFolhaPagamentoTipoMedia->recuperaTodos($rsTipoMedia,$stFiltro,$stOrdem);

$obCmbTipoMediaRes = new Select;
$obCmbTipoMediaRes->setName                           ( "stCodigoTipoMediaRes"                                    );
$obCmbTipoMediaRes->setValue                          ( $inCodigoTipoMediaRes                                     );
$obCmbTipoMediaRes->setRotulo                         ( "Tipo de Média"                                           );
$obCmbTipoMediaRes->setTitle                          ( "Selecione o tipo de média para cálculo do salário."      );
$obCmbTipoMediaRes->setCampoId                        ( "[codigo]"                                                );
$obCmbTipoMediaRes->setCampoDesc                      ( "descricao"                                               );
$obCmbTipoMediaRes->addOption                         ( "", "Selecione"                                           );
$obCmbTipoMediaRes->preencheCombo                     ( $rsTipoMedia                                              );
$obCmbTipoMediaRes->setStyle                          ( "width: 250px"                                            );
$obCmbTipoMediaRes->obEvento->setOnChange             ( "buscaValor('preencherObservacaoRes');"                   );

$obLblObservacaoRes = new Label;
$obLblObservacaoRes->setValue                          ( "&nbsp;" );
$obLblObservacaoRes->setRotulo                         ( "Observação" );
$obLblObservacaoRes->setName                           ( "stObservacaoRes" );
$obLblObservacaoRes->setId                             ( "stObservacaoRes" );

//$obCmbRegimeSubDivisaoRes = new SelectMultiplo();
//$obCmbRegimeSubDivisaoRes->setName         ( 'inCodSubDivisaoRes'                                                       );
//$obCmbRegimeSubDivisaoRes->setRotulo       ( "*Regime/subdivisões"                                                       );
//$obCmbRegimeSubDivisaoRes->setTitle        ( "Selecione os regimes/subdivisões associados ao evento de rescisão"        );
//$obCmbRegimeSubDivisaoRes->SetNomeLista1   ( 'inCodSubDivisaoDisponiveisRes'                                            );
//$obCmbRegimeSubDivisaoRes->setCampoId1     ( '[cod_sub_divisao]/[nom_regime]/[nom_sub_divisao]'                         );
//$obCmbRegimeSubDivisaoRes->setCampoDesc1   ( '[nom_regime]/[nom_sub_divisao]'                                           );
//$obCmbRegimeSubDivisaoRes->setStyle1       ( "width: 300px"                                                             );
//$obCmbRegimeSubDivisaoRes->SetRecord1      ( $rsSubDivisaoDisponiveisRes                                                );
//$obCmbRegimeSubDivisaoRes->SetNomeLista2   ( 'inCodSubDivisaoSelecionadosRes'                                           );
//$obCmbRegimeSubDivisaoRes->setCampoId2     ( '[cod_sub_divisao]/[nom_regime]/[nom_sub_divisao]'                         );
//$obCmbRegimeSubDivisaoRes->setCampoDesc2   ( '[nom_regime]/[nom_sub_divisao]'                                           );
//$obCmbRegimeSubDivisaoRes->setStyle2       ( "width: 300px"                                                             );
//$obCmbRegimeSubDivisaoRes->SetRecord2      ( $rsSubDivisaoSelecionadosRes                                               );
//$stOnClick = "selecionaSubDivisao('Res',true);buscaValor('preencheCargoEspecialidadeRes');selecionaSubDivisao('Res',false);";
//$obCmbRegimeSubDivisaoRes->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
//$obCmbRegimeSubDivisaoRes->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
//$obCmbRegimeSubDivisaoRes->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
//$obCmbRegimeSubDivisaoRes->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
//$obCmbRegimeSubDivisaoRes->obSelect1->obEvento->setOnDblClick( $stOnClick );
//$obCmbRegimeSubDivisaoRes->obSelect2->obEvento->setOnDblClick( $stOnClick );
//
//
//$obCmbCargoRes = new SelectMultiplo();
//$obCmbCargoRes->setName                    ( 'inCodCargoRes'                                                            );
//$obCmbCargoRes->setRotulo                  ( "*Cargos vinculados"                                                        );
//$obCmbCargoRes->setTitle                   ( "Selecione os cargos/especialidades associados ao evento de rescisão"      );
//$obCmbCargoRes->SetNomeLista1              ( 'inCodCargoDisponiveisRes'                                                 );
//$obCmbCargoRes->setCampoId1                ( '[cod_cargo]'                                                              );
//$obCmbCargoRes->setCampoDesc1              ( '[descr_cargo]/[descr_espec]'                                              );
//$obCmbCargoRes->setStyle1                  ( "width: 300px"                                                             );
//$obCmbCargoRes->SetRecord1                 ( $rsCargoDisponiveisRes                                                     );
//$obCmbCargoRes->SetNomeLista2              ( 'inCodCargoSelecionadosRes'                                                );
//$obCmbCargoRes->setCampoId2                ( '[cod_cargo]/[descr_cargo]/[cod_especialidade]/[descr_espec]'              );
//$obCmbCargoRes->setCampoDesc2              ( '[descr_cargo]/[descr_espec]'                                              );
//$obCmbCargoRes->setStyle2                  ( "width: 300px"                                                             );
//$obCmbCargoRes->SetRecord2                 ( $rsCargoSelecionadosRes                                                    );

$obBscFuncaoRes = new BuscaInner;
$obBscFuncaoRes->setRotulo ( "*Função"                                                                );
$obBscFuncaoRes->setTitle  ( "Selecione uma função para esta particularidade de evento de rescisão"  );
$obBscFuncaoRes->setId     ( "stFuncaoRes"                                                           );
$obBscFuncaoRes->obCampoCod->setName   ( "inCodFuncaoRes" );
$obBscFuncaoRes->obCampoCod->setValue  ( $inCodFuncaoRes  );
$obBscFuncaoRes->obCampoCod->obEvento->setOnChange("buscaValor('buscaFuncaoRes');");
$obBscFuncaoRes->obCampoCod->obEvento->setOnBlur  ("buscaValor('buscaFuncaoRes');");
$obBscFuncaoRes->obCampoCod->setMascara("99.99.999");
$obBscFuncaoRes->setFuncaoBusca( "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncaoRes','stFuncaoRes','','".Sessao::getId()."','800','550');" );

$obBtnIncluirRes = new Button;
$obBtnIncluirRes->setName                  ( "btIncluirRes"                                                             );
$obBtnIncluirRes->setValue                 ( "Incluir"                                                                  );
$obBtnIncluirRes->obEvento->setOnClick     ( "selecionaSubDivisao('Res',true);
                                              selecionaCargo('Res',true);
                                              buscaValor('incluiCasoRes');
                                              selecionaSubDivisao('Res',false);
                                              selecionaCargo('Res',false);"                                             );

$obBtnAlterarRes = new Button;
$obBtnAlterarRes->setName                  ( "btAlterarRes"                                                             );
$obBtnAlterarRes->setValue                 ( "Alterar"                                                                  );
$obBtnAlterarRes->obEvento->setOnClick     ( "selecionaSubDivisao('Res',true);
                                              selecionaCargo('Res',true);
                                              buscaValor('alteraCasoRes');
                                              selecionaSubDivisao('Res',false);
                                              selecionaCargo('Res',false);"                                             );

$obBtnLimparRes = new Button;
$obBtnLimparRes->setName                   ( "btLimparRes"                                                              );
$obBtnLimparRes->setValue                  ( "Limpar"                                                                   );
$obBtnLimparRes->obEvento->setOnClick      ( "buscaValor('limpaCamposCasoRes')"                                         );

$obSpnListaRes = new Span;
$obSpnListaRes->setId ( "spnListaRes" );

$obHdnRes = new Hidden;
$obHdnRes->setName("hdnRes");

$obSpnEventoBaseRes = new Span;
$obSpnEventoBaseRes->setId ( "spnEventoBaseRes" );

$obSpnSpan4 = new Span;
$obSpnSpan4->setId ( "spnSpan4" );

?>
