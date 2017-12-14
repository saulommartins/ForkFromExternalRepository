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
    * Página de Formulário para Configuração de Tipos de Diárias
    * Data de Criação: 05/08/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: FMTipoDiarias.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.09.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );
include_once( CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php"                                    );

$stPrograma = "TipoDiarias";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$jsOnload   = "montaParametrosGET('gerarListaTipoDiaria','');";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obHdnAcao = new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                  );
$obHdnAcao->setValue                            ( $stAcao                                                   );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                  );
$obHdnCtrl->setValue                            ( ""                                                        );

$obHdnId = new Hidden;
$obHdnId->setName                               ( "inId"                                                    );
$obHdnId->setValue                              ( ""                                                        );

$obHdnInCodTipo = new Hidden;
$obHdnInCodTipo->setName                        ( "inCodTipo"                                               );
$obHdnInCodTipo->setValue                       ( ""                                                        );

$obTxtNomeDiaria = new TextBox;
$obTxtNomeDiaria->setRotulo                     ( "Nome do Tipo de Diária"                                  );
$obTxtNomeDiaria->setTitle                      ( "Informe o nome da diária"                                );
$obTxtNomeDiaria->setName                       ( "stNomeTipoDiaria"                                        );
$obTxtNomeDiaria->setId                         ( "stNomeTipoDiaria"                                        );
$obTxtNomeDiaria->setValue                      ( $stNomeTipoDiaria                                         );
$obTxtNomeDiaria->setMaxLength                  ( 50                                                        );
$obTxtNomeDiaria->setSize                       ( 50                                                        );
$obTxtNomeDiaria->setNull                       ( false                                                     );

$obTipoNormaNorma = new IBuscaInnerNorma        ( false, false );
$obTipoNormaNorma->obITextBoxSelectTipoNorma->obSelect->setDisabled( true );
$obTipoNormaNorma->obITextBoxSelectTipoNorma->obTextBox->setReadOnly( true );
$obTipoNormaNorma->obBscNorma->setRotulo        ("Lei/Decreto" );
$obTipoNormaNorma->obBscNorma->setTitle         ("Selecione a lei ou decreto que regulamenta o pagamento das diárias.");
$obTipoNormaNorma->setNull                      ( false );

$obTxtValorDiaria = new Moeda;
$obTxtValorDiaria->setRotulo                    ( "Valor da Diária"                                         );
$obTxtValorDiaria->setName                      ( "flValorDiaria"                                           );
$obTxtValorDiaria->setTitle                     ( "Informe o valor de uma diária"                           );
$obTxtValorDiaria->setSize                      ( 6                                                         );
$obTxtValorDiaria->setMaxLength                 ( 6                                                         );
$obTxtValorDiaria->setNull                      ( false                                                     );

$obBscRubricaDespesa = new BuscaInner;
$obBscRubricaDespesa->setRotulo                 ( "Rubrica de Despesa"                                      );
$obBscRubricaDespesa->setTitle                  ( "Selecione a rubrica de despesa referente ao pagamento de diárias, para emissão da autorização de empenho." );
$obBscRubricaDespesa->setId                     ( "stRubricaDespesa"                                        );
$obBscRubricaDespesa->obCampoCod->setName       ( "stMascClassificacao"                                     );
$obBscRubricaDespesa->obCampoCod->setSize       ( 18                                                        );
$obBscRubricaDespesa->obCampoCod->setMaxLength  ( 22                                                        );
$obBscRubricaDespesa->obCampoCod->setValue      ( $stRubricaDespesa                                         );
$obBscRubricaDespesa->obCampoCod->setAlign      ( "LEFT"                                                    );
$obBscRubricaDespesa->obCampoCod->setPreencheComZeros ( "D" );
$obBscRubricaDespesa->obCampoCod->obEvento->setOnChange( "executaFuncaoAjax('preencheMascClassificacao','&stMascClassificacao='+this.value);" );
$obBscRubricaDespesa->setFuncaoBusca            ( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','stMascClassificacao','stRubricaDespesa','','".Sessao::getId()."','800','550');" );

$obTxtVigencia = new Data;
$obTxtVigencia->setRotulo        ( "Vigência" 							);
$obTxtVigencia->setTitle         ( "Informe a data iní­cio de vigência." );
$obTxtVigencia->setName          ( "dtDataVigencia" 					);
$obTxtVigencia->setId            ( "dtDataVigencia" 					);
$obTxtVigencia->setValue         ( $dtDataVigencia  					);
$obTxtVigencia->setNull			 ( false 								);

$obSpnListaTiposDiarias = new Span;
$obSpnListaTiposDiarias->setId                  ( "spnListaTiposDiarias"                                    );

$obBtnIncluirTipoDiaria = new Button;
$obBtnIncluirTipoDiaria->setName                ( "btnIncluirTipoDiaria"                                    );
$obBtnIncluirTipoDiaria->setValue               ( "Incluir"                                                 );
$obBtnIncluirTipoDiaria->setTipo                ( "button"                                                  );
$obBtnIncluirTipoDiaria->obEvento->setOnClick   ( "if ( Valida() ) { buscaValor('incluirTipoDiaria', '".$pgOcul."', '".$pgProc."', '', '".Sessao::getId()."'); }");

$obBtnAlterarTipoDiaria = new Button;
$obBtnAlterarTipoDiaria->setName                ( "btnAlterarTipoDiaria"                                    );
$obBtnAlterarTipoDiaria->setValue               ( "Alterar"                                                 );
$obBtnAlterarTipoDiaria->setTipo                ( "button"                                                  );
$obBtnAlterarTipoDiaria->obEvento->setOnClick    ( "if ( Valida() ) { buscaValor('alterarTipoDiaria', '".$pgOcul."', '".$pgProc."', '', '".Sessao::getId()."'); }");
$obBtnAlterarTipoDiaria->setDisabled            ( true                                                      );

$obBtnLimparTipoDiaria = new Button;
$obBtnLimparTipoDiaria->setName                 ( "btnLimparTipoDiaria"                                     );
$obBtnLimparTipoDiaria->setValue                ( "Limpar"                                                  );
$obBtnLimparTipoDiaria->setTipo                 ( "button"                                                  );
$obBtnLimparTipoDiaria->obEvento->setOnClick    ( "buscaValor('limparTipoDiaria', '".$pgOcul."', '".$pgProc."', '', '".Sessao::getId()."');");

$obBtnOk = new Ok;
$obBtnOk->obEvento->setOnClick                  ( " montaParametrosGET('submeter', '', true); ");

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimpar"                                               );
$obBtnLimpar->setValue                          ( "Limpar"                                                  );
$obBtnLimpar->setTipo                           ( "button"                                                  );
$obBtnLimpar->obEvento->setOnClick              ( "buscaValor('limparFormulario', '".$pgOcul."', '".$pgProc."', '', '".Sessao::getId()."');");

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                   );
$obForm->setTarget                              ( "oculto"                                                  );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                   );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );

$obFormulario->addHidden                        ( $obHdnAcao                                                );
$obFormulario->addHidden                        ( $obHdnCtrl                                                );
$obFormulario->addHidden                        ( $obHdnId                                                  );
$obFormulario->addHidden                        ( $obHdnInCodTipo                                           );
$obFormulario->addTitulo                        ( "Configuração de Tipos de Diárias"                        );
$obFormulario->addComponente                    ( $obTxtNomeDiaria                                          );
$obTipoNormaNorma->geraFormulario($obFormulario);
$obFormulario->addComponente                    ( $obTxtValorDiaria                                         );
$obFormulario->addComponente                    ( $obBscRubricaDespesa                                      );
$obFormulario->addComponente                    ( $obTxtVigencia 											);
$obFormulario->defineBarra                      ( array($obBtnIncluirTipoDiaria,$obBtnAlterarTipoDiaria,$obBtnLimparTipoDiaria), "", "" );
$obFormulario->addSpan                          ( $obSpnListaTiposDiarias                                   );
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar)                              );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
