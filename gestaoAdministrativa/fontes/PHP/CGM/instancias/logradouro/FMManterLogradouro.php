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
    
    * Página de formulário para o cadastro de logradouro
    * Data de Criação   : 13/07/2015

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Evandro Melos
    * $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterLogradouro";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgBairro   = CAM_GT_CIM_POPUPS."/bairro/FMManterBairro.php?".Sessao::getId();;

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ($stAcao == "") {
    $stAcao = "incluir";
}

$arBairrosSessao = Sessao::read('bairros');
$arCepSessao     = Sessao::read('cep');

if ( !is_array( $arBairrosSessao ) and !is_array( $arCepSessao ) ) {
    Sessao::remove('sessao_transf6');
    Sessao::write('bairros', array());
    Sessao::write('cep'    , array());
}

//DEFINICAO DOS COMPONENTES DE FORMULARIO
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setId  ( "stCtrl" );
$obHdnCtrl->setValue ( $request->get('stCtrl') );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setId  ( "stAcao" );
$obHdnAcao->setValue ( $request->get('stAcao') );

$obHdnCodLogradouro = new Hidden;
$obHdnCodLogradouro->setName  ( "inCodigoLogradouro"            );
$obHdnCodLogradouro->setId    ( "inCodigoLogradouro"            );
$obHdnCodLogradouro->setValue ( $request->get("inCodigoLogradouro") );

$obHdnCampoNome = new Hidden;
$obHdnCampoNome->setName  ( "campoNom"       );
$obHdnCampoNome->setId    ( "campoNom"       );
$obHdnCampoNome->setValue ( $request->get("campoNom") );

$obHdnCampoNum  = new Hidden;
$obHdnCampoNum->setName  ( "campoNum"       );
$obHdnCampoNum->setId    ( "campoNum"       );
$obHdnCampoNum->setValue ( $request->get("campoNum") );

$obHdnPais = new Hidden;
$obHdnPais->setName  ( "inCodPais" );
$obHdnPais->setId    ( "inCodPais" );
$obHdnPais->setValue ( $request->get("inCodPais") );

$obHdnCadastro = new Hidden;
$obHdnCadastro->setName  ( "stCadastro"            );
$obHdnCadastro->setId    ( "stCadastro"            );
$obHdnCadastro->setValue ( $request->get("stCadastro") );

$obHdnCodUF = new Hidden;
$obHdnCodUF->setName  ( "inCodUF"               );
$obHdnCodUF->setId    ( "inCodUF"               );
$obHdnCodUF->setValue ( $request->get("inCodigoUF") );

$obHdnCodMunicipio = new Hidden;
$obHdnCodMunicipio->setName  ( "inCodMunicipio"               );
$obHdnCodMunicipio->setId    ( "inCodMunicipio"               );
$obHdnCodMunicipio->setValue ( $request->get("inCodigoMunicipio") );

$obHdnNomeUF = new Hidden;
$obHdnNomeUF->setName  ( "stNomeUF"               );
$obHdnNomeUF->setId    ( "stNomeUF"               );
$obHdnNomeUF->setValue ( $request->get("stNomeUF") );

$obHdnNomeMunicipio = new Hidden;
$obHdnNomeMunicipio->setName  ( "stNomeMunicipio"               );
$obHdnNomeMunicipio->setId    ( "stNomeMunicipio"               );
$obHdnNomeMunicipio->setValue ( $request->get("stNomeMunicipio") );

$obHdninId = new Hidden;
$obHdninId->setName ( "inId" );
$obHdninId->setId   ( "inId" );

//Hidden para atribuir o valor do campo html
$obHdnDescricaoNormaHistorico = new Hidden;
$obHdnDescricaoNormaHistorico->setName ( "stDescricaoNormaHistorico" );
$obHdnDescricaoNormaHistorico->setId   ( "stDescricaoNormaHistorico" );

$obHdnDescricaoNorma = new Hidden;
$obHdnDescricaoNorma->setName ( "stDescricaoNorma" );
$obHdnDescricaoNorma->setId   ( "stDescricaoNorma" );

$obLblTipoLogradouro = new Label();
$obLblTipoLogradouro->setRotulo("Tipo");
$obLblTipoLogradouro->setName("inCodigoTipo");
$obLblTipoLogradouro->setId("inCodigoTipo");
$obLblTipoLogradouro->setValue( $request->get("inCodigoTipo") );

$obLblNomeLogradouro = new Label();
$obLblNomeLogradouro->setRotulo("Nome do logradouro");
$obLblNomeLogradouro->setName("stNomeLogradouro");
$obLblNomeLogradouro->setId("stNomeLogradouro");
$obLblNomeLogradouro->setValue($request->get('stNomeLogradouro'));

$obLblCodLogradouro = new Label;
$obLblCodLogradouro->setRotulo ( "Código do Logradouro" );
$obLblCodLogradouro->setName   ( "inCodigoLogradouro"   );
$obLblCodLogradouro->setId     ( "inCodigoLogradouro"   );
$obLblCodLogradouro->setValue  ( $request->get("inCodigoLogradouro") );

$obLblNomeUF = new Label;
$obLblNomeUF->setRotulo ( "Estado"                 );
$obLblNomeUF->setName   ( "stNomeUF"               );
$obLblNomeUF->setId     ( "stNomeUF"               );
$obLblNomeUF->setValue  ( $request->get("stNomeUF") );

$obLblNomeMunicipio = new Label;
$obLblNomeMunicipio->setRotulo ( "Município" );
$obLblNomeMunicipio->setName   ( "stNomeMunicipio"  );
$obLblNomeMunicipio->setId     ( "stNomeMunicipio"  );
$obLblNomeMunicipio->setValue  ( $request->get("stNomeMunicipio")  );

$obIPopUpNorma = new IPopUpNorma();
$obIPopUpNorma->obInnerNorma->setRotulo          ( "Norma"     );
$obIPopUpNorma->obInnerNorma->setTitle           ( "Informe a Norma que determinou o Nome do Logradouro."    );
$obIPopUpNorma->obInnerNorma->obCampoCod->setId  ( "inCodNorma"  );
$obIPopUpNorma->obInnerNorma->obCampoCod->setName( "inCodNorma"  );
$obIPopUpNorma->obInnerNorma->setNull            ( false );

$obDtInicial = new Data();
$obDtInicial->setRotulo    ( "Data Inicial" );
$obDtInicial->setTitle     ( "Informe a Data Inicial do Nome do Logradouro." );
$obDtInicial->setName      ( "stDataInicial" );
$obDtInicial->setId        ( "stDataInicial" );
$obDtInicial->setMaxLength ( 10 );
$obDtInicial->setSize      ( 10 );
$obDtInicial->setNull      ( false );
    
$obDtFinal = new Data();
$obDtFinal->setRotulo    ( "Data Final" );
$obDtFinal->setTitle     ( "Informe a Data Final do Nome do Logradouro." );
$obDtFinal->setName      ( "stDataFinal" );
$obDtFinal->setId        ( "stDataFinal" );
$obDtFinal->setMaxLength ( 10 );
$obDtFinal->setSize      ( 10 );
$obDtFinal->setNull      ( true );

// Mostrar endereço.
$obRadHistoricoSim = new Radio();
$obRadHistoricoSim->setId      ('boMostraHistorico');
$obRadHistoricoSim->setName    ('boMostraHistorico');
$obRadHistoricoSim->setValue   ('S');
$obRadHistoricoSim->setRotulo  ('Histórico do Logradouro');
$obRadHistoricoSim->setLabel   ('Sim');
$obRadHistoricoSim->obEvento->setOnChange(" jQuery('#spanListarHistorico').show(); ");

$obRadHistoricoNao = new Radio();
$obRadHistoricoNao->setId      ('boMostraHistorico');
$obRadHistoricoNao->setName    ('boMostraHistorico');
$obRadHistoricoNao->setValue   ('N');
$obRadHistoricoNao->setRotulo  ('Histórico do Logradouro');
$obRadHistoricoNao->setLabel   ('Não');
$obRadHistoricoNao->setChecked (true);
$obRadHistoricoNao->obEvento->setOnChange(" jQuery('#spanListarHistorico').hide(); ");

$arRadHistorico = array($obRadHistoricoSim, $obRadHistoricoNao);

$obTxtCodigoLogradouro = new TextBox;
$obTxtCodigoLogradouro->setRotulo    ( "Código do Logradouro"  );
$obTxtCodigoLogradouro->setName      ( "inCodLogradouro"       );
$obTxtCodigoLogradouro->setId        ( "inCodLogradouro"       );
$obTxtCodigoLogradouro->setSize      ( 8                       );
$obTxtCodigoLogradouro->setMaxLength ( 8                       );
$obTxtCodigoLogradouro->setInteiro   ( true                    );
$obTxtCodigoLogradouro->setNull      ( false                   );
$obTxtCodigoLogradouro->setValue     ( $request->get("inCodLogradouro") );

$obTxtCodTipo = new TextBox;
$obTxtCodTipo->setRotulo    ( "Tipo"                    );
$obTxtCodTipo->setId        ( "inCodigoTipo"            );
$obTxtCodTipo->setName      ( "inCodigoTipo"            );
$obTxtCodTipo->setValue     ( $request->get("inCodigoTipo") );
$obTxtCodTipo->setSize      ( 8                         );
$obTxtCodTipo->setMaxLength ( 8                         );
$obTxtCodTipo->setNull      ( false                     );
$obTxtCodTipo->setValue     ( $request->get("inCodigoTipo") );

$obTxtNome = new TextBox;
$obTxtNome->setRotulo    ( "Nome Atual"                  );
$obTxtNome->setTitle     ( "Nome do logradouro"          );
$obTxtNome->setName      ( "stNomeLogradouro"            );
$obTxtNome->setId        ( "stNomeLogradouro"            );
$obTxtNome->setSize      ( 70                            );
$obTxtNome->setMaxLength ( 60                            );
$obTxtNome->setNull      ( false                         );
$obTxtNome->setValue     ( str_replace('\\', '', $request->get("stNomeLogradouro")));

$obBtnIncluirNovoBairro = new Button;
$obBtnIncluirNovoBairro->setName              ( "btnIncluirNovoBairro"   );
$obBtnIncluirNovoBairro->setValue             ( "Incluir Novo Bairro"    );
$obBtnIncluirNovoBairro->setTipo              ( "button"                 );
$obBtnIncluirNovoBairro->obEvento->setOnClick ( "incluirNovoBairro();"   );

$obTxtNovoBairro = new TextBox;
$obTxtNovoBairro->setRotulo    ( "Novo Bairro"  );
$obTxtNovoBairro->setName      ( "stNovoBairro" );
$obTxtNovoBairro->setId        ( "stNovoBairro" );
$obTxtNovoBairro->setSize      ( 60 );
$obTxtNovoBairro->setMaxLength ( 120 );
$obTxtNovoBairro->setNull      ( true );

$obTxtCodBairro = new TextBox;
$obTxtCodBairro->setRotulo    ( "*Bairro"                   );
$obTxtCodBairro->setName      ( "inCodigoBairro"            );
$obTxtCodBairro->setId        ( "inCodigoBairro"            );
$obTxtCodBairro->setValue     ( $request->get("inCodigoBairro") );
$obTxtCodBairro->setSize      ( 8                           );
$obTxtCodBairro->setMaxLength ( 8                           );
$obTxtCodBairro->setInteiro   ( true                        );

$obTxtCEP = new CEP;
$obTxtCEP->setRotulo ( "*CEP"           );
$obTxtCEP->setName   ( "inCEP"          );
$obTxtCEP->setId     ( "inCEP"          );
$obTxtCEP->setValue  ( $request->get("inCEP") );

$obTxtInicial = new TextBox;
$obTxtInicial->setRotulo    ( "Número Inicial" );
$obTxtInicial->setName      ( "inInicial"             );
$obTxtInicial->setId        ( "inInicial"             );
$obTxtInicial->setSize      ( 8                       );
$obTxtInicial->setMaxLength ( 6                       );
$obTxtInicial->setInteiro   ( true                    );

$obTxtFinal = new TextBox;
$obTxtFinal->setRotulo    ( "Número Final" );
$obTxtFinal->setName      ( "inFinal"             );
$obTxtFinal->setId        ( "inFinal"             );
$obTxtFinal->setSize      ( 8                     );
$obTxtFinal->setMaxLength ( 6                     );
$obTxtFinal->setInteiro   ( true                  );

$obTxtCodUF = new TextBox;
$obTxtCodUF->setRotulo             ( "Estado"                 );
$obTxtCodUF->setName               ( "inCodigoUF"             );
$obTxtCodUF->setId                 ( "inCodigoUF"             );
$obTxtCodUF->setValue              ( $request->get("inCodigoUF")  );
$obTxtCodUF->setSize               ( 8                        );
$obTxtCodUF->setMaxLength          ( 8                        );
$obTxtCodUF->setNull               ( false                    );
$obTxtCodUF->setInteiro            ( true                     );
$obTxtCodUF->obEvento->setOnChange ( "preencheMunicipio('');" );

$obTxtCodMunicipio = new TextBox;
$obTxtCodMunicipio->setRotulo             ( "Município" );
$obTxtCodMunicipio->setName               ( "inCodigoMunicipio"            );
$obTxtCodMunicipio->setId                 ( "inCodigoMunicipio"            );
$obTxtCodMunicipio->setValue              ( $request->get("inCodigoMunicipio") );
$obTxtCodMunicipio->setSize               ( 8                              );
$obTxtCodMunicipio->setMaxLength          ( 8                              );
$obTxtCodMunicipio->setNull               ( false                          );
$obTxtCodMunicipio->setInteiro            ( true                           );
$obTxtCodMunicipio->obEvento->setOnChange ( "preencheBairro();"            );

$obTxtCodLogradouro = new Label;
$obTxtCodLogradouro->setRotulo ( "Código"                        );
$obTxtCodLogradouro->setName   ( "inCodigoLogradouro"            );
$obTxtCodLogradouro->setValue  ( $request->get("inCodigoLogradouro") );

$obLblExtensao = new Label;
$obLblExtensao->setRotulo ( "Extensao" );
$obLblExtensao->setName   ( "stExtensao"  );
$obLblExtensao->setId     ( "stExtensao"  );
    
$obCmbUF = new Select;
$obCmbUF->setName               ( "inCodUF"                 );
$obCmbUF->setId                 ( "inCodUF"                 );
$obCmbUF->addOption             ( "", "Selecione"           );
$obCmbUF->setCampoId            ( "cod_uf"                  );
$obCmbUF->setCampoDesc          ( "nom_uf"                  );
$obCmbUF->setValue              ( $request->get("inCodigoUF")   );
$obCmbUF->setNull               ( false                     );
$obCmbUF->setStyle              ( "width: 220px"        );
$obCmbUF->obEvento->setOnChange ( "preencheMunicipio('');"  );

$obCmbMunicipio = new Select;
$obCmbMunicipio->setName                ( "inCodMunicipio"               );
$obCmbMunicipio->setId                  ( "inCodMunicipio"               );
$obCmbMunicipio->addOption              ( "", "Selecione"                );
$obCmbMunicipio->setCampoId             ( "cod_municipio"                );
$obCmbMunicipio->setCampoDesc           ( "nom_municipio"                );
$obCmbMunicipio->setValue               ( $request->get("inCodigoMunicipio") );
$obCmbMunicipio->setNull                ( false                          );
$obCmbMunicipio->setStyle               ( "width: 220px"             );
$obCmbMunicipio->obEvento->setOnChange  ( "preencheBairro();"            );

$obCmbTipo = new Select;
$obCmbTipo->setName       ( "inCodTipo"               );
$obCmbTipo->setId         ( "inCodTipo"               );
$obCmbTipo->setValue      ( $request->get("inCodigoTipo") );
$obCmbTipo->addOption     ( "", "Selecione"           );
$obCmbTipo->setCampoId    ( "cod_tipo"                );
$obCmbTipo->setCampoDesc  ( "nom_tipo"                );
$obCmbTipo->setNull       ( false                     );

$obCmbBairro = new Select;
$obCmbBairro->setName       ( "inCodBairro"               );
$obCmbBairro->setId         ( "inCodBairro"               );
$obCmbBairro->addOption     ( "", "Selecione"             );
$obCmbBairro->setCampoId    ( "cod_bairro"                );
$obCmbBairro->setCampoDesc  ( "nom_bairro"                );
$obCmbBairro->setValue      ( $request->get("inCodigoBairro") );
$obCmbBairro->setStyle      ( "width: 220px"             );

$obBtnIncluirBairro = new Button;
$obBtnIncluirBairro->setName              ( "btnIncluirBairro"       );
$obBtnIncluirBairro->setId                ( "btnIncluirBairro"       );
$obBtnIncluirBairro->setValue             ( "Incluir"                );
$obBtnIncluirBairro->setTipo              ( "button"                 );
$obBtnIncluirBairro->obEvento->setOnClick ( "incluirBairro();"       );

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimparBairro"        );
$obBtnLimpar->setId                ( "btnLimparBairro"        );
$obBtnLimpar->setValue             ( "Limpar"                 );
$obBtnLimpar->obEvento->setOnClick ( "limparBairro();"        );

$arBotoesBairro = array ($obBtnIncluirBairro, $obBtnLimpar );

$obBtnIncluirCEP = new Button;
$obBtnIncluirCEP->setName              ( "btnIncluirCEP" );
$obBtnIncluirCEP->setId                ( "btnIncluirCEP" );
$obBtnIncluirCEP->setValue             ( "Incluir"       );
$obBtnIncluirCEP->setTipo              ( "button"        );
$obBtnIncluirCEP->obEvento->setOnClick ( "incluirCEP();" );

$obBtnLimparCEP = new Button;
$obBtnLimparCEP->setName              ( "btnLimparCEP" );
$obBtnLimparCEP->setId                ( "btnLimparCEP" );
$obBtnLimparCEP->setValue             ( "Limpar"       );
$obBtnLimparCEP->obEvento->setOnClick ( "limparCEP();" );

$obRdnTodos = new Radio;
$obRdnTodos->setRotulo  ( "Numeração" );
$obRdnTodos->setName    ( "boNumeracao"      );
$obRdnTodos->setId      ( "boNumeracao"      );
$obRdnTodos->setLabel   ( "Todos"            );
$obRdnTodos->setValue   ( "Todos"            );
$obRdnTodos->setChecked ( true               );

$obRdnPares = new Radio;
$obRdnPares->setRotulo  ( "Numeração" );
$obRdnPares->setName    ( "boNumeracao"      );
$obRdnPares->setId      ( "boNumeracao"      );
$obRdnPares->setLabel   ( "Pares"            );
$obRdnPares->setValue   ( "Pares"            );
$obRdnPares->setChecked ( false              );

$obRdnImpares = new Radio;
$obRdnImpares->setRotulo  ( "Numeração"   );
$obRdnImpares->setName    ( "boNumeracao" );
$obRdnImpares->setId      ( "boNumeracao" );
$obRdnImpares->setLabel   ( "Ímpares"     );
$obRdnImpares->setValue   ( "Ímpares"     );
$obRdnImpares->setChecked ( false         );

$arBotoesCEP = array ( $obBtnIncluirCEP , $obBtnLimparCEP );

$ArRdnCEP = array ($obRdnTodos,$obRdnPares,$obRdnImpares);

$obSpnListarBairro = New Span;
$obSpnListarBairro->setId ( "spanListarBairro" );

$obSpnListarCEP = New Span;
$obSpnListarCEP->setId ( "spanListarCEP" );

$obSpnListarHistorico = New Span;
$obSpnListarHistorico->setId ( "spanListarHistorico" );

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto'  );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo ( "Dados para Logradouro" );

$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCodLogradouro );
$obFormulario->addHidden ( $obHdnCampoNome );
$obFormulario->addHidden ( $obHdnCampoNum );
$obFormulario->addHidden ( $obHdnPais );
$obFormulario->addHidden ( $obHdnCadastro );
$obFormulario->addHidden ( $obHdninId );
$obFormulario->addHidden ( $obHdnDescricaoNorma );
$obFormulario->addHidden ( $obHdnDescricaoNormaHistorico );

switch ($stAcao) {
    case 'incluir':
        $obFormulario->addComponente         ( $obTxtCodigoLogradouro              );
        $obFormulario->addComponenteComposto ( $obTxtCodTipo, $obCmbTipo           );
        $obFormulario->addComponente         ( $obTxtNome                          );
        $obFormulario->addComponenteComposto ( $obTxtCodUF, $obCmbUF               );
        $obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio );
        $obIPopUpNorma->geraFormulario       ( $obFormulario                       );
        $obFormulario->addComponente         ( $obDtInicial                        );
        $obFormulario->addComponente         ( $obDtFinal                          );
        $obFormulario->agrupaComponentes     ( $arRadHistorico                     );        
        $obFormulario->addSpan               ( $obSpnListarHistorico               );
        $obFormulario->addTitulo             ( "Bairro"                            );
        $obFormulario->agrupaComponentes     ( array( $obTxtNovoBairro, $obBtnIncluirNovoBairro ));
        $obFormulario->addComponenteComposto ( $obTxtCodBairro, $obCmbBairro       );
        $obFormulario->defineBarra           ( $arBotoesBairro,'center',''         );
        $obFormulario->addSpan               ( $obSpnListarBairro                  );
        $obFormulario->addTitulo             ( "CEP"                               );
        $obFormulario->addComponente         ( $obTxtCEP                           );
        $obFormulario->addComponente         ( $obTxtInicial                       );
        $obFormulario->addComponente         ( $obTxtFinal                         );
        $obFormulario->agrupaComponentes     ( $ArRdnCEP                           );
        $obFormulario->defineBarra           ( $arBotoesCEP,'center',''            );    
        $obFormulario->addSpan               ( $obSpnListarCEP                     );
    break;

    case 'alterar':
        $obFormulario->addHidden             ( $obHdnCodUF                   );
        $obFormulario->addHidden             ( $obHdnCodMunicipio            );
        $obFormulario->addHidden             ( $obHdnNomeUF                  );
        $obFormulario->addHidden             ( $obHdnNomeMunicipio           );        
        $obFormulario->addComponente         ( $obLblCodLogradouro           );
        $obFormulario->addComponenteComposto ( $obTxtCodTipo, $obCmbTipo     );
        $obFormulario->addComponente         ( $obTxtNome                    );
        $obFormulario->addComponente         ( $obLblNomeUF                  );
        $obFormulario->addComponente         ( $obLblNomeMunicipio           );
        $obIPopUpNorma->geraFormulario       ( $obFormulario                 );
        $obFormulario->addComponente         ( $obDtInicial                  );
        $obFormulario->addComponente         ( $obDtFinal                    );
        $obFormulario->addComponente         ( $obLblExtensao                );
        $obFormulario->agrupaComponentes     ( $arRadHistorico               );
        $obFormulario->addSpan               ( $obSpnListarHistorico         );
        $obFormulario->addTitulo             ( "Bairro"                      );
        $obFormulario->agrupaComponentes     ( array( $obTxtNovoBairro, $obBtnIncluirNovoBairro ));
        $obFormulario->addComponenteComposto ( $obTxtCodBairro, $obCmbBairro );
        $obFormulario->defineBarra           ( $arBotoesBairro,'center',''   );
        $obFormulario->addSpan               ( $obSpnListarBairro            );
        $obFormulario->addTitulo             ( "CEP"                         );
        $obFormulario->addComponente         ( $obTxtCEP                     );
        $obFormulario->addComponente         ( $obTxtInicial                 );
        $obFormulario->addComponente         ( $obTxtFinal                   );
        $obFormulario->agrupaComponentes     ( $ArRdnCEP                     );
        $obFormulario->defineBarra           ( $arBotoesCEP,'center',''      );    
        $obFormulario->addSpan               ( $obSpnListarCEP               );
    break;
    
    case 'consultar':
        $obFormulario->addHidden        ( $obHdnCodUF           );
        $obFormulario->addHidden        ( $obHdnCodMunicipio    );
        $obFormulario->addHidden        ( $obHdnNomeUF          );
        $obFormulario->addHidden        ( $obHdnNomeMunicipio   );
        $obFormulario->addComponente    ( $obLblCodLogradouro   );
        $obFormulario->addComponente    ( $obLblTipoLogradouro  );
        $obFormulario->addComponente    ( $obLblNomeLogradouro  );
        $obFormulario->addComponente    ( $obLblNomeUF          );
        $obFormulario->addComponente    ( $obLblNomeMunicipio   );
        $obFormulario->addComponente    ( $obLblExtensao        );
        $obFormulario->addSpan          ( $obSpnListarHistorico );
        $obFormulario->addTitulo        ( "Bairro"              );
        $obFormulario->addSpan          ( $obSpnListarBairro    );
        $obFormulario->addTitulo        ( "CEP"                 );
        $obFormulario->addSpan          ( $obSpnListarCEP       );
    break;
}

$obBtnOk = new OK;
$obBtnOk->obEvento->setOnClick("verificaCodigoLogradouro();");

$obBtnLimpar = new Limpar;
$obBtnLimpar->obEvento->setOnClick( "limparListas();" );

$obVoltar = new Voltar;

if ($stAcao == "incluir") {
    $arBotaoAcao = array( $obBtnOk, $obBtnLimpar );
}elseif($stAcao == "consultar"){    
    $arBotaoAcao = array( $obVoltar );
}else{
    $arBotaoAcao = array( $obBtnOk, $obVoltar );
}

$obFormulario->defineBarra ( $arBotaoAcao );
$obFormulario->show();

if ($stAcao == 'alterar' || $stAcao == 'consultar') {
    if ($stAcao == 'consultar'){
        sistemalegado::executaFrameOculto("preencheInnerConsultar();");
    }else{
        sistemalegado::executaFrameOculto("jQuery('#spanListarHistorico').hide(); preencheInner();");
    }
} else {
    sistemalegado::executaFrameOculto(" jQuery('#spanListarHistorico').hide(); IniciaSessions();");
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
