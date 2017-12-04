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
* Página de Aba de Identificação
* Data de Criação   : ???

* @author Analista: ???
* @author Desenvolvedor: ???

* @ignore

$Revision: 30963 $
$Name$
$Author: souzadl $
$Date: 2008-03-24 11:59:05 -0300 (Seg, 24 Mar 2008) $

* Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once($pgOculDependentes);

$obHdnCodDependente = new Hidden;
$obHdnCodDependente->setName("inCodDependente");
$obHdnCodDependente->setValue( isset($inCodDependente) ? $inCodDependente : null );

$obBscCGMDependente = new IPopUpCGM($obForm);
$obBscCGMDependente->setId                   ( 'stNomDependente');
$obBscCGMDependente->setRotulo               ( '*CGM do Dependente'       );
$obBscCGMDependente->setTipo                 ('fisica'           );
$obBscCGMDependente->setTitle                ( 'Informe o CGM do dependente do servidor.');
$obBscCGMDependente->setValue                ( isset($stNomDependente) ? $stNomDependente : null );
$obBscCGMDependente->setNull                 ( true             );
$obBscCGMDependente->obCampoCod->setName     ( 'inCGMDependente' );
$obBscCGMDependente->obCampoCod->setSize     (10);
$obBscCGMDependente->obCampoCod->setValue    ( isset($inCGMDependente) ? $inCGMDependente : null );
$obBscCGMDependente->obCampoCod->obEvento->setOnBlur("ajaxJavaScript('OCManterServidorAbaDependentes.php?".
                                                       Sessao::getId()."&inCGMDependente='+this.value,
                                                       'preencheDadosCGMDependente' );");

$obBscCGMDependente->obCampoCod->obEvento->setOnChange("ajaxJavaScript('OCManterServidorAbaDependentes.php?".
                                                       Sessao::getId()."&inCGMDependente='+this.value,
                                                       'preencheDadosCGMDependente' );");

$obSpnDataNascimentoDependente = new Span;
$obSpnDataNascimentoDependente->setId("spnDataNascimentoDependente");
$obSpnDataNascimentoDependente->setValue("");

$obLblSexoDependente = new Label;
$obLblSexoDependente->setRotulo ( "Sexo"             );
$obLblSexoDependente->setValue  ( isset($stSexoDependente) ? $stSexoDependente : null );
$obLblSexoDependente->setId     ( "stSexoDependente" );

$obHdnSexoDependente = new Hidden;
$obHdnSexoDependente->setName("stSexoDependente");
$obHdnSexoDependente->setValue( isset($stSexoDependente) ? $stSexoDependente : null );

$obRdoDependenteSalarioFamiliaSim = new Radio;
$obRdoDependenteSalarioFamiliaSim->setName    ( "boDependenteSalarioFamilia"                );
$obRdoDependenteSalarioFamiliaSim->setId      ( "boDependenteSalarioFamiliaSim"             );
$obRdoDependenteSalarioFamiliaSim->setRotulo  ( "Dependente Salário Família"                );
$obRdoDependenteSalarioFamiliaSim->setTitle   ( "Informe se dependente do salário família." );
$obRdoDependenteSalarioFamiliaSim->setLabel   ( "Sim"                                       );
$obRdoDependenteSalarioFamiliaSim->setDisabled( true                                        );
$obRdoDependenteSalarioFamiliaSim->setChecked ( true                                        );
$obRdoDependenteSalarioFamiliaSim->setValue   ( "t"     		 							);
$obRdoDependenteSalarioFamiliaSim->obEvento->setOnChange( "buscaValor('geraSpnDependenteSalarioFamilia',5)" );

$obRdoDependenteSalarioFamiliaNao = new Radio;
$obRdoDependenteSalarioFamiliaNao->setName    ( "boDependenteSalarioFamilia"               );
$obRdoDependenteSalarioFamiliaNao->setId      ( "boDependenteSalarioFamiliaNao"            );
$obRdoDependenteSalarioFamiliaNao->setTitle   ( "Dependente Salário Família."               );
$obRdoDependenteSalarioFamiliaNao->setRotulo  ( "Informe se dependente do salário família" );
$obRdoDependenteSalarioFamiliaNao->setLabel   ( "Não"                                      );
$obRdoDependenteSalarioFamiliaNao->setChecked ( true );
$obRdoDependenteSalarioFamiliaNao->setValue   ( "f"                                        );
$obRdoDependenteSalarioFamiliaNao->obEvento->setOnChange( "buscaValor('limpaSpnDependenteSalarioFamilia',5)" );

$obHdnEvalDependenteSalarioFamilia = new HiddenEval;
$obHdnEvalDependenteSalarioFamilia->setName   ( "stEvalDependenteSalarioFamilia"           );
$obHdnEvalDependenteSalarioFamilia->setValue  ( ""                                         );

$obSpnDependenteSalarioFamilia = new Span;
$obSpnDependenteSalarioFamilia->setId         ( "spnDependenteSalarioFamilia"              );

$obTxtCodParentesco = new TextBox;
$obTxtCodParentesco->setRotulo    ( "*Grau Parentesco"   );
$obTxtCodParentesco->setTitle     ( "Informe o grau de parentesco." );
$obTxtCodParentesco->setName      ( "inCodGrauParentesco" );
$obTxtCodParentesco->setValue     ( isset($inCodGrauParentesco) ? $inCodGrauParentesco : null  );
$obTxtCodParentesco->setMaxLength ( 10  );
$obTxtCodParentesco->setSize      ( 10 );
$obTxtCodParentesco->setNull      ( true );
$obTxtCodParentesco->obEvento->setOnChange("buscaValor('habilitaDependenteSalarioFamilia',5);");

$obRPessoalServidor->addDependente();
$obRPessoalServidor->roUltimoDependente->obRPessoalGrauParentesco->listarGrauParentesco( $rsGrauParentesco );
$obCmbCodParentesco = new Select;
$obCmbCodParentesco->setName       ( "stGrauParentesco" );
$obCmbCodParentesco->setStyle      ( "width: 250px" );
$obCmbCodParentesco->setRotulo    ( "*Grau Parentesco" );
$obCmbCodParentesco->setNull       ( true );
$obCmbCodParentesco->addOption     ( "", "Selecione" );
$obCmbCodParentesco->setCampoID    ( "[cod_grau]" );
$obCmbCodParentesco->setCampoDesc  ( "[nom_grau]" );
$obCmbCodParentesco->preencheCombo( $rsGrauParentesco );
$obCmbCodParentesco->obEvento->setOnChange("buscaValor('habilitaDependenteSalarioFamilia',5);");

$obChkSalarioFamilia = new CheckBox;
$obChkSalarioFamilia->setRotulo         ( "Dependente Salário Família"   );
$obChkSalarioFamilia->setTitle          ( "Informe se dependente do salário família."   );
$obChkSalarioFamilia->setName           ( "boDependenteSalarioFamilia"  );
$obChkSalarioFamilia->setValue          ( 't' );
$obChkSalarioFamilia->setChecked        ( ($boTitulacao == 't') );

$obTxtCodDependenteIR = new TextBox;
$obTxtCodDependenteIR->setRotulo    ( "*Dependente IR"   );
$obTxtCodDependenteIR->setTitle     ( "Informe grau de depenência do IR." );
$obTxtCodDependenteIR->setName      ( "inCodDependenteIR" );
$obTxtCodDependenteIR->setId        ( "inCodDependenteIR" );
$obTxtCodDependenteIR->setValue     ( isset($inCodDependenteIR) ? $inCodDependenteIR : null);
$obTxtCodDependenteIR->setMaxLength ( 10  );
$obTxtCodDependenteIR->setSize      ( 10 );

$obCmbCodDependenteIR = new Select;
$obCmbCodDependenteIR->setName       ( "stDependenteIR" );
$obCmbCodDependenteIR->setId         ( "stDependenteIR" );
$obCmbCodDependenteIR->setStyle      ( "width: 250px" );
$obCmbCodDependenteIR->addOption     ( "", "Selecione" );
$obCmbCodDependenteIR->addOption     ("0","Não dependente.");
$obCmbCodDependenteIR->addOption     ("1","Cônjuge.");
$obCmbCodDependenteIR->addOption     ("2","O companheiro ou a companheira, desde que haja vida em comum por mais de cinco anos, ou por período menor se da união resultou filho;");
$obCmbCodDependenteIR->addOption     ("3", "Filho(a) até vinte e um anos.");
$obCmbCodDependenteIR->addOption     ("4", "Enteado(a) até vinte e um anos.");
$obCmbCodDependenteIR->addOption     ("5", "Filho(a) de qualquer idade quando incapacitado física ou mentalmente para o trabalho;");
$obCmbCodDependenteIR->addOption     ("6", "O menor pobre, até vinte e um anos, que o contribuinte crie e eduque e do qual detenha a guarda judicial;");
$obCmbCodDependenteIR->addOption     ("7", "O irmão, o neto ou o bisneto, sem arrimo dos pais, até vinte e um anos, desde que o contribuinte detenha a guarda judicial.");
$obCmbCodDependenteIR->addOption     ("8", "O irmão, o neto ou o bisneto, sem arrimo dos pais de qualquer idade quando incapacitado física ou mentalmente para o trabalho;");
$obCmbCodDependenteIR->addOption     ("9", "Filho(a) até 24 anos de idade, se cursando estabelecimento de ensino superior, ou escola técnica de segundo grau.");
$obCmbCodDependenteIR->addOption     ("10", "Enteado até 24 anos de idade, se cursando estabelecimento de ensino superior, ou escola técnica de segundo grau.");
$obCmbCodDependenteIR->addOption     ("11", "Os pais, os avós ou bisavós, desde que não aufiram rendimentos, tributáveis ou não, superiores ao limite de isenção mensal;");
$obCmbCodDependenteIR->addOption     ("12", "O absolutamente incapaz, do qual o contribuinte seja tutor ou curador. Obs: A incapacitação física ou mental");
$obCmbCodDependenteIR->setValue      ( isset($inCodDependenteIR) ? $inCodDependenteIR : null );

$obChkCarteiraVacinacao = new CheckBox;
$obChkCarteiraVacinacao->setRotulo         ( "Apresentar Comprovante de Vacinação"   );
$obChkCarteiraVacinacao->setTitle          ( "Informe se deve ser apresentada carteira de vacinação.");
$obChkCarteiraVacinacao->setName           ( "boCarteiraVacinacao"  );
$obChkCarteiraVacinacao->setId             ( "boCarteiraVacinacao"  );
$obChkCarteiraVacinacao->setValue          ( 't' );
$obChkCarteiraVacinacao->setChecked        ( ($boCarteiraVacinacao == 't') );
$obChkCarteiraVacinacao->obEvento->setOnClick( "buscaValor('habilitaCarteiraVacinacao',5);" );

$obChkComprovanteMatricula = new CheckBox;
$obChkComprovanteMatricula->setRotulo         ( "Apresentar Comprovante de Matrícula/Frequência"   );
$obChkComprovanteMatricula->setTitle          ( "Informe se dever apresentar comprovante de matrícula."   );
$obChkComprovanteMatricula->setName           ( "boComprovanteMatricula"  );
$obChkComprovanteMatricula->setId             ( "boComprovanteMatricula"  );
$obChkComprovanteMatricula->setValue          ( 't' );
$obChkComprovanteMatricula->setChecked        ( ($boComprovanteMatricula == 't') );
$obChkComprovanteMatricula->obEvento->setOnClick( "buscaValor('habilitaComprovanteMatricula',5);" );

$obRdoDependentePrevSim = new Radio;
$obRdoDependentePrevSim->setName    ( "boDependentePrev"                		);
$obRdoDependentePrevSim->setId      ( "boDependentePrevSim"             		);
$obRdoDependentePrevSim->setRotulo  ( "Dependente da previdência"               );
$obRdoDependentePrevSim->setTitle   ( "Informe se dependente da previdência." 	);
$obRdoDependentePrevSim->setLabel   ( "Sim"                                     );
$obRdoDependentePrevSim->setDisabled( false                                     );
$obRdoDependentePrevSim->setChecked ( false                                     );
$obRdoDependentePrevSim->setValue   ( "t"     		 							);

$obRdoDependentePrevNao = new Radio;
$obRdoDependentePrevNao->setName    ( "boDependentePrev"               			 );
$obRdoDependentePrevNao->setId      ( "boDependentePrevNao"            			 );
$obRdoDependentePrevNao->setTitle   ( "Dependente da previdência."               );
$obRdoDependentePrevNao->setRotulo  ( "Informe se dependente da previdência" 	 );
$obRdoDependentePrevNao->setLabel   ( "Não"                                      );
$obRdoDependentePrevNao->setChecked ( true 										 );
$obRdoDependentePrevNao->setValue   ( "f"                                        );

//*****************************
//
//Carteira de vacinacao
//
//*****************************

$obTxtApresentacaoCarteiraVacinacao =  new Data;
$obTxtApresentacaoCarteiraVacinacao->setName        ( "dtApresentacaoCarteiraVacinacao"         );
$obTxtApresentacaoCarteiraVacinacao->setNull        ( true                                      );
$obTxtApresentacaoCarteiraVacinacao->setRotulo      ( "Data de Apresentação"                    );
$obTxtApresentacaoCarteiraVacinacao->setTitle       ( "Informe a data prevista de apresentação." );
$obTxtApresentacaoCarteiraVacinacao->setValue       ( isset($dtApresentacaoCarteiraVacinacao) ? $dtApresentacaoCarteiraVacinacao : null );
$obTxtApresentacaoCarteiraVacinacao->setDisabled    ( true                                      );
$obTxtApresentacaoCarteiraVacinacao->obEvento->setOnChange("buscaValor('validarDataCarteiraVacinacao',5);");

$obBtnIncluirVacinacao = new Button;
$obBtnIncluirVacinacao->setName                     ( "btnIncluir"                              );
$obBtnIncluirVacinacao->setValue                    ( "Incluir"                                 );
$obBtnIncluirVacinacao->setTipo                     ( "button"                                  );
$obBtnIncluirVacinacao->obEvento->setOnClick        ( "buscaValor('incluirVacinacao',5);"       );

$obBtnLimparVacinacao = new Button;
$obBtnLimparVacinacao->setName                      ( "btnLimpar"                               );
$obBtnLimparVacinacao->setValue                     ( "Limpar"                                  );
$obBtnLimparVacinacao->setTipo                      ( "button"                                  );
$obBtnLimparVacinacao->obEvento->setOnClick         ( "buscaValor('limparVacinacao',5);"        );

$obSpnVacinacao = new Span;
$obSpnVacinacao->setId                              ( "spnVacinacao"                            );

//*****************************
//
// Comprovante de matriculo
//
//*****************************

$obTxtApresentacaoComprovanteMatricula =  new Data;
$obTxtApresentacaoComprovanteMatricula->setName     ( "dtApresentacaoComprovanteMatricula"      );
$obTxtApresentacaoComprovanteMatricula->setNull     ( true                                      );
$obTxtApresentacaoComprovanteMatricula->setRotulo   ( " Data de Apresentação"                   );
$obTxtApresentacaoComprovanteMatricula->setTitle    ( "Informe a data prevista de apresentação." );
$obTxtApresentacaoComprovanteMatricula->setValue    ( isset($dtApresentacaoComprovanteMatricula) ? $dtApresentacaoComprovanteMatricula : null );
$obTxtApresentacaoComprovanteMatricula->setDisabled ( true                                      );
$obTxtApresentacaoComprovanteMatricula->obEvento->setOnChange("buscaValor('validarDataComprovanteMatricula',5);");

$obBtnIncluirMatricula = new Button;
$obBtnIncluirMatricula->setName ( "btnIncluir" );
$obBtnIncluirMatricula->setValue( "Incluir" );
$obBtnIncluirMatricula->setTipo ( "button" );
$obBtnIncluirMatricula->obEvento->setOnClick ( "buscaValor('incluirMatricula',5);" );

$obBtnLimparMatricula = new Button;
$obBtnLimparMatricula->setName( "btnLimpar" );
$obBtnLimparMatricula->setValue( "Limpar" );
$obBtnLimparMatricula->setTipo( "button" );
$obBtnLimparMatricula->obEvento->setOnClick ( "buscaValor('limparMatricula',5);" );

$obSpnMatricula = new Span;
$obSpnMatricula->setId ( "spnMatricula" );

//**************************
//
// botao incluir dependente
//
//**************************

$obBtnIncluirDependente = new Button;
$obBtnIncluirDependente->setName ( "btnIncluirDependente" );
$obBtnIncluirDependente->setId   ( "btnIncluirDependente" );
$obBtnIncluirDependente->setValue( "Incluir" );
$obBtnIncluirDependente->obEvento->setOnClick ( "buscaValor('incluirDependente',5);" );

$obBtnAlterarDependente = new Button;
$obBtnAlterarDependente->setName              ( "btnAlterarDependente" );
$obBtnAlterarDependente->setId                ( "btnAlterarDependente" );
$obBtnAlterarDependente->setValue             ( "Alterar" );
$obBtnAlterarDependente->obEvento->setOnClick ( "buscaValor('alterarDependente',5);" );

$obBtnLimparDependente = new Button;
$obBtnLimparDependente->setName( "btnLimpar" );
$obBtnLimparDependente->setValue( "Limpar" );
$obBtnLimparDependente->setTipo( "button" );
$obBtnLimparDependente->obEvento->setOnClick ( "buscaValor('limparDependente',5);" );

$obSpnDependente = new Span;
$obSpnDependente->setId ( "spnDependente" );

//Define o objeto da Hidden para armazenar o timestamp na hora da edição do dependente
$obHdnTimestamp = new Hidden;
$obHdnTimestamp->setName     ( "inTimestamp" );
$obHdnTimestamp->setValue    ( isset($inTimestamp) ? $inTimestamp : null );
