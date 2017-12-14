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
    * Filtro para Alterar Assentamento Gerado
    * Data de Criação   : 09/05/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Programador: Diego Lemos de Souza

    * @ignore

    $Revision: 30877 $
    $Name$
    $Author: souzadl $
    $Date: 2006-08-23 16:57:24 -0300 (Qua, 23 Ago 2006) $

    Caso de uso: uc-04.04.14
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalClassificacaoAssentamento.class.php"                        );

$stPrograma = "ManterGeracaoAssentamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );

include_once ($pgOcul);
include_once ($pgJs);
$stAcao = $request->get('stAcao');

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                                  ( $pgList                                                           );
$obForm->setTarget                                  ( "telaPrincipal"                                                   );

//Define o objeto de controle
$obHdnAcao = new Hidden;
$obHdnAcao->setName                                 ( "stAcao"                                                          );
$obHdnAcao->setValue                                ( $stAcao                                                           );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                                 ( "stCtrl"                                                          );
$obHdnCtrl->setValue                                ( ""                                                                );

$obHdnEval = new HiddenEval;
$obHdnEval->setName                                 ( "stEval"                                                          );
$obHdnEval->setValue                                ( ""                                                                );

$obCmbModoGeracao = new Select;
$obCmbModoGeracao->setRotulo                        ( "Assentamento"                                                    );
$obCmbModoGeracao->setName                          ( "stModoGeracao"                                                   );
$obCmbModoGeracao->setId                            ( "stModoGeracao"                                                   );
$obCmbModoGeracao->setTitle                         ( "Informe o modo que o assentamento será filtrado."                );
$obCmbModoGeracao->addOption                        ( "contrato"    , "Matrícula"                                        );
$obCmbModoGeracao->addOption                        ( "cgm/contrato", "CGM/Matrícula"                                    );
$obCmbModoGeracao->addOption                        ( "cargo"       , "Cargo"                                           );
$obCmbModoGeracao->addOption                        ( "lotacao"     , "Lotação"                                         );
$obCmbModoGeracao->setValue                         ( "contrato"                                                        );
$obCmbModoGeracao->obEvento->setOnChange            ( "buscaValor('gerarAssentamentoFiltro');"                          );

$obSpan1 = new Span;
$obSpan1->setId                                     ( "spnSpan1"                                                        );

$obRPessoalClassificacaoAssentamento = new RPessoalClassificacaoAssentamento();
$obRPessoalClassificacaoAssentamento->listarClassificacao( $rsClassificacao );

$obTxtCodClassificao = new TextBox;
$obTxtCodClassificao->setRotulo                     ( "Classificação"                                                   );
$obTxtCodClassificao->setTitle                      ( "Informe a classificação do assentamento."                        );
$obTxtCodClassificao->setName                       ( "inCodClassificacaoTxt"                                           );
$obTxtCodClassificao->setId                         ( "inCodClassificacaoTxt"                                           );
$obTxtCodClassificao->setSize                       ( 10                                                                );
$obTxtCodClassificao->setMaxLength                  ( 10                                                                );
$obTxtCodClassificao->setInteiro                    ( true                                                              );
$obTxtCodClassificao->obEvento->setOnChange         ( "buscaValor('preencherAssentamento');"                            );

//Define objeto SELECT para listar a DESCRIÇÂO da classificação
$obCmbClassificao = new Select;
$obCmbClassificao->setRotulo                        ( "Classificação"                                                   );
$obCmbClassificao->setTitle                         ( "Informe a classificação do assentamento."                        );
$obCmbClassificao->setName                          ( "inCodClassificacao"                                              );
$obCmbClassificao->setId                            ( "inCodClassificacao"                                              );
$obCmbClassificao->setStyle                         ( "width: 200px"                                                    );
$obCmbClassificao->addOption                        ( "", "Selecione"                                                   );
$obCmbClassificao->setValue                         ( ""                                                                );
$obCmbClassificao->setCampoID                       ( "cod_classificacao"                                               );
$obCmbClassificao->setCampoDesc                     ( "descricao"                                                       );
$obCmbClassificao->preencheCombo                    ( $rsClassificacao                                                  );
$obCmbClassificao->obEvento->setOnChange            ( "buscaValor('preencherAssentamento');"                            );

//Define objeto TEXTBOX para informar o CODIGO da classificação
$obTxtCodAssentamento = new TextBox;
$obTxtCodAssentamento->setRotulo                    ( "Assentamento"                                                    );
$obTxtCodAssentamento->setTitle                     ( "Informe o assentamento para o filtro."                           );
$obTxtCodAssentamento->setName                      ( "inCodAssentamentoTxt"                                            );
$obTxtCodAssentamento->setId                        ( "inCodAssentamentoTxt"                                            );
$obTxtCodAssentamento->setSize                      ( 10                                                                );
$obTxtCodAssentamento->setMaxLength                 ( 10                                                                );
$obTxtCodAssentamento->setInteiro                   ( true                                                              );

//Define objeto SELECT para listar a DESCRIÇÂO do motivo
$obCmbAssentamento = new Select;
$obCmbAssentamento->setRotulo                       ( "Assentamento"                                                    );
$obCmbAssentamento->setTitle                        ( "Informe o assentamento para o filtro."                           );
$obCmbAssentamento->setName                         ( "inCodAssentamento"                                               );
$obCmbAssentamento->setId                           ( "inCodAssentamento"                                               );
$obCmbAssentamento->setStyle                        ( "width: 200px"                                                    );
$obCmbAssentamento->addOption                       ( "", "Selecione"                                                   );
$obCmbAssentamento->setValue                        ( ""                                                                );

$obDtPeriodicidade = new Periodicidade();
$obDtPeriodicidade->setExercicio                    ( Sessao::getExercicio()                                                );
$obDtPeriodicidade->setId                           ( "stPeriodicidade"                                                 );

$obBtnLimparCampos = new Button;
$obBtnLimparCampos->setName                         ( "btnLimparCampos"                                                 );
$obBtnLimparCampos->setValue                        ( "Limpar"                                                          );
$obBtnLimparCampos->setTipo                         ( "button"                                                          );
$obBtnLimparCampos->obEvento->setOnClick            ( "buscaValor('limparFiltro');"                                     );
$obBtnLimparCampos->setDisabled                     ( false                                                             );

$obBtnOK = new Ok;

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                              ( $obForm                                                           );
$obFormulario->addHidden                            ( $obHdnAcao                                                        );
$obFormulario->addHidden                            ( $obHdnCtrl                                                        );
$obFormulario->addHidden                            ( $obHdnEval , true                                                 );
$obFormulario->addTitulo                            ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addTitulo                            ( "Dados para o Filtro"                                             );
$obFormulario->addComponente                        ( $obCmbModoGeracao                                                 );
$obFormulario->addSpan                              ( $obSpan1                                                          );
$obFormulario->addTitulo                            ( "Informações do Assentamento"                                     );
$obFormulario->addComponenteComposto                ( $obTxtCodClassificao , $obCmbClassificao                          );
$obFormulario->addComponenteComposto                ( $obTxtCodAssentamento, $obCmbAssentamento                         );
$obFormulario->addComponente                        ( $obDtPeriodicidade                                                );
$obFormulario->setFormFocus                         ( $obCmbModoGeracao->getId()                                        );
$obFormulario->defineBarra                          ( array($obBtnOK,$obBtnLimparCampos)                                );
$obFormulario->show                 ();

processarForm(true,"filtro");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
