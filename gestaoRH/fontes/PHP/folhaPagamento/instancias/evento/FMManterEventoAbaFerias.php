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
* Página de Formulario de Evento - Aba Ferias
* Data de Criação   : 29/08/2005

* @author Analista: Leandro Oliveira
* @author Programador: Eduardo Antunez

* @ignore

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2006-09-12 06:43:46 -0300 (Ter, 12 Set 2006) $

Caso de uso: uc-04.05.06
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$obBscRubricaDespesaFer = new BuscaInner;
$obBscRubricaDespesaFer->setRotulo                         ( "Rubrica de despesa"                                      );
$obBscRubricaDespesaFer->setTitle                          ( "Informe uma rubrica de despesa para o evento de férias"  );
$obBscRubricaDespesaFer->setId                             ( "stRubricaDespesaFer"                                     );
$obBscRubricaDespesaFer->obCampoCod->setName               ( "stMascClassificacaoFer"                                  );
$obBscRubricaDespesaFer->obCampoCod->setSize               ( 18                                                        );
$obBscRubricaDespesaFer->obCampoCod->setMaxLength          ( 22                                                        );
$obBscRubricaDespesaFer->obCampoCod->setValue              ( $stRubricaDespesaFer                                      );
$obBscRubricaDespesaFer->obCampoCod->setAlign              ( "LEFT"                                                    );
$obBscRubricaDespesaFer->obCampoCod->setPreencheComZeros   ( "D" );
$obBscRubricaDespesaFer->obCampoCod->obEvento->setOnChange ( "buscaValor('preencheMascClassificacaoFer','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."');" );
$obBscRubricaDespesaFer->setFuncaoBusca                    ( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','stMascClassificacaoFer','stRubricaDespesaFer','&mascClassificacao=$stMascaraElementoDespesa','".Sessao::getId()."','800','550');" );

$obTxtDescricaoFer = new TextBox;
$obTxtDescricaoFer->setRotulo              ( "*Descrição"                                                                );
$obTxtDescricaoFer->setTitle               ( "Informe a descrição auxiliar para a particularidade do evento de férias"  );
$obTxtDescricaoFer->setName                ( "stDescricaoFer"                                                           );
$obTxtDescricaoFer->setValue               ( $stDescricaoFer                                                            );
$obTxtDescricaoFer->setSize                ( 40                                                                         );
$obTxtDescricaoFer->setMaxLength           ( 80                                                                         );
$obTxtDescricaoFer->setCaracteresAceitos ( "[0-9a-zA-Z áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ%--+*/%]"                             );
$obTxtDescricaoFer->setEspacosExtras       ( false                                                                      );

$obTxtTipoMedia = new TextBox;
$obTxtTipoMedia->setRotulo                         ( "Tipo de Média"                                           );
$obTxtTipoMedia->setName                           ( "inCodigoTipoMediaFer"                                    );
$obTxtTipoMedia->setValue                          ( $inCodigoTipoMediaFer                                     );
$obTxtTipoMedia->setTitle                          ( "Selecione o tipo de média para cálculo do salário."      );
$obTxtTipoMedia->setSize                           ( 3                                                         );
$obTxtTipoMedia->setMaxLength                      ( 3                                                         );
$obTxtTipoMedia->setInteiro                        ( true                                                      );
$obTxtTipoMedia->setPreencheComZeros               ( "E"                                                       );
$obTxtTipoMedia->setMascara                        ( "999"                                                     );
$obTxtTipoMedia->obEvento->setOnChange             ( "buscaValor('preencherObservacaoFer');"                   );

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoMedia.class.php");
$obTFolhaPagamentoTipoMedia = new TFolhaPagamentoTipoMedia;
$stFiltro = " WHERE desdobramento = 'F'";
if ($boApresentaParcela == "S") {
    $stFiltro .= " AND cod_tipo = 18";
}
$stOrdem = " ORDER BY descricao";

$obTFolhaPagamentoTipoMedia->recuperaTodos($rsTipoMedia,$stFiltro,$stOrdem);

$obCmbTipoMedia = new Select;
$obCmbTipoMedia->setName                           ( "stCodigoTipoMediaFer"                                    );
$obCmbTipoMedia->setValue                          ( $inCodigoTipoMediaFer                                     );
$obCmbTipoMedia->setRotulo                         ( "Tipo de Média"                                           );
$obCmbTipoMedia->setTitle                          ( "Selecione o tipo de média para cálculo do salário."      );
$obCmbTipoMedia->setCampoId                        ( "[codigo]"                                                );
$obCmbTipoMedia->setCampoDesc                      ( "descricao"                                               );
$obCmbTipoMedia->addOption                         ( "", "Selecione"                                           );
$obCmbTipoMedia->preencheCombo                     ( $rsTipoMedia                                              );
$obCmbTipoMedia->setStyle                          ( "width: 250px"                                            );
$obCmbTipoMedia->obEvento->setOnChange             ( "buscaValor('preencherObservacaoFer');"                   );

$obLblObservacao = new Label;
$obLblObservacao->setValue                          ( "&nbsp;" );
$obLblObservacao->setRotulo                         ( "Observação" );
$obLblObservacao->setName                           ( "stObservacaoFer" );
$obLblObservacao->setId                             ( "stObservacaoFer" );

$obChkProporcionalizarAbono = new Checkbox;
$obChkProporcionalizarAbono->setRotulo("Considerar Proporção do Abono");
$obChkProporcionalizarAbono->setName("boProporcionalizarAbono");
$obChkProporcionalizarAbono->setId("boProporcionazarAbono");
$obChkProporcionalizarAbono->setTitle("Marque para proporcionalizar o evento no pagamento de abono das férias.");
$obChkProporcionalizarAbono->setChecked(true);
$obChkProporcionalizarAbono->setValue(true);

$obBscFuncaoFer = new BuscaInner;
$obBscFuncaoFer->setRotulo ( "*Função"                                                              );
$obBscFuncaoFer->setTitle  ( "Selecione uma função para esta particularidade de evento de férias"  );
$obBscFuncaoFer->setId     ( "stFuncaoFer"                                                         );
$obBscFuncaoFer->obCampoCod->setName   ( "inCodFuncaoFer" );
$obBscFuncaoFer->obCampoCod->setValue  ( $inCodFuncaoFer  );
$obBscFuncaoFer->obCampoCod->obEvento->setOnChange("buscaValor('buscaFuncaoFer');");
$obBscFuncaoFer->obCampoCod->obEvento->setOnBlur  ("buscaValor('buscaFuncaoFer');");
$obBscFuncaoFer->obCampoCod->setMascara("99.99.999");
$obBscFuncaoFer->setFuncaoBusca( "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncaoFer','stFuncaoFer','','".Sessao::getId()."','800','550');" );

$obBtnIncluirFer = new Button;
$obBtnIncluirFer->setName                  ( "btIncluirFer"                                                             );
$obBtnIncluirFer->setValue                 ( "Incluir"                                                                  );
$obBtnIncluirFer->obEvento->setOnClick     ( "selecionaSubDivisao('Fer',true);
                                              selecionaCargo('Fer',true);
                                              buscaValor('incluiCasoFer');
                                              selecionaSubDivisao('Fer',false);
                                              selecionaCargo('Fer',false);"                                             );

$obBtnAlterarFer = new Button;
$obBtnAlterarFer->setName                  ( "btAlterarFer"                                                             );
$obBtnAlterarFer->setValue                 ( "Alterar"                                                                  );
$obBtnAlterarFer->obEvento->setOnClick     ( "selecionaSubDivisao('Fer',true);
                                              selecionaCargo('Fer',true);
                                              buscaValor('alteraCasoFer');
                                              selecionaSubDivisao('Fer',false);
                                              selecionaCargo('Fer',false);"                                             );

$obBtnLimparFer = new Button;
$obBtnLimparFer->setName                   ( "btLimparFer"                                                              );
$obBtnLimparFer->setValue                  ( "Limpar"                                                                   );
$obBtnLimparFer->obEvento->setOnClick      ( "buscaValor('limpaCamposCasoFer')"                                         );

$obSpnListaFer = new Span;
$obSpnListaFer->setId ( "spnListaFer" );

$obHdnFer = new Hidden;
$obHdnFer->setName("hdnFer");

$obSpnEventoBaseFer = new Span;
$obSpnEventoBaseFer->setId ( "spnEventoBaseFer" );

$obSpnSpan2 = new Span;
$obSpnSpan2->setId ( "spnSpan2" );
?>
