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
/*
    * Formulário de Cadastro de Contratos
    * Data de Criação   : 01/09/2008

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/empenho/classes/componentes/IPopUpEmpenho.class.php';

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContratoModalidadeLicitacao.class.php" );
$obTTCMGOContratoModalidadeLicitacao = new TTCMGOContratoModalidadeLicitacao;
$stOrder = " ORDER BY descricao ";
$obTTCMGOContratoModalidadeLicitacao->recuperaTodos($rsModLic, "", $stOrder);

include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContratoAssunto.class.php" );
$obTTCMGOContratoAssunto = new TTCMGOContratoAssunto;
$stOrder = " ORDER BY descricao ";
$obTTCMGOContratoAssunto->recuperaTodos($rsAssunto, "", $stOrder);

include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContratoSubAssunto.class.php" );
$obTTCMGOContratoSubAssunto = new TTCMGOContratoSubAssunto;
$stOrder = " ORDER BY cod_sub_assunto ";
$obTTCMGOContratoSubAssunto->recuperaTodos($rsSubAssunto, "", $stOrder);

include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContratoTipo.class.php" );
$obTTCMGOContratoTipo = new TTCMGOContratoTipo;
$stOrder = " ORDER BY descricao ";
$obTTCMGOContratoTipo->recuperaTodos($rsTipo, "", $stOrder);

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obHdnCodContrato = new Hidden;
$obHdnCodContrato->setName  ( "inCodContrato" );
$obHdnCodContrato->setValue ( $inCodContrato  );

$obHdnExercicioContrato = new Hidden;
$obHdnExercicioContrato->setName  ( "stExercicioContrato"            );
$obHdnExercicioContrato->setValue ( $_REQUEST['stExercicioContrato'] );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName  ( "cod_entidade"             );
$obHdnCodEntidade->setValue ( $_REQUEST['cod_entidade']  );

$obTxtContrato = new TextBox;
$obTxtContrato->setName   ( "inNumContrato"            );
$obTxtContrato->setId     ( "inNumContrato"            );
$obTxtContrato->setValue  ( $_REQUEST['inNumContrato'] );
$obTxtContrato->setRotulo ( "Número do Contrato"       );
$obTxtContrato->setTitle  ( "Informe o contrato."      );
$obTxtContrato->setNull   ( false                      );
$obTxtContrato->setInteiro( true                       );

$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuario->setNull ( false );

$obCmbModLicitacao = new Select;
$obCmbModLicitacao->setName      ( "cod_modalidade"            );
$obCmbModLicitacao->setRotulo    ( "Modalidade de Licitação"   );
$obCmbModLicitacao->setId        ( "stNomModLic"               );
$obCmbModLicitacao->setCampoId   ( "cod_modalidade"            );
$obCmbModLicitacao->setCampoDesc ( "descricao"                 );
$obCmbModLicitacao->addOption    ( '','Selecione'              );
$obCmbModLicitacao->preencheCombo( $rsModLic                   );
$obCmbModLicitacao->setNull      ( false                       );
$obCmbModLicitacao->setValue     ( ''                          );

$obCmbAssunto = new Select;
$obCmbAssunto->setName      ( "cod_assunto"   );
$obCmbAssunto->setRotulo    ( "Assunto"       );
$obCmbAssunto->setId        ( "stAssunto"     );
$obCmbAssunto->setCampoId   ( "cod_assunto"   );
$obCmbAssunto->setCampoDesc ( "descricao"     );
$obCmbAssunto->addOption    ( '','Selecione'  );
$obCmbAssunto->preencheCombo( $rsAssunto      );
$obCmbAssunto->setNull      ( false           );
$obCmbAssunto->setValue     ( ''              );

$obCmbSubAssunto = new Select;
$obCmbSubAssunto->setName      ( "subAssunto"    );
$obCmbSubAssunto->setRotulo    ( "Sub-Assunto"   );
$obCmbSubAssunto->setId        ( "stSubAssunto"  );
$obCmbSubAssunto->setCampoId   ( "cod_sub_assunto"    );
$obCmbSubAssunto->setCampoDesc ( "descricao"    );
$obCmbSubAssunto->addOption    ( '','Selecione'  );
$obCmbSubAssunto->preencheCombo( $rsSubAssunto   );
$obCmbSubAssunto->setNull      ( true            );
$obCmbSubAssunto->setValue     ( ''              );
$obCmbSubAssunto->obEvento->setOnChange("montaParametrosGET('subAssunto')");

$SpnSubAssunto = new Span;
$SpnSubAssunto->SetId('spnSubAssunto');

$obCmbTipoContrato = new Select;
$obCmbTipoContrato->setName      ( "cod_tipo"            );
$obCmbTipoContrato->setRotulo    ( "Tipo de Contrato"    );
$obCmbTipoContrato->setId        ( "stTipoContrato"      );
$obCmbTipoContrato->setCampoId   ( "cod_tipo"            );
$obCmbTipoContrato->setCampoDesc ( "descricao"           );
$obCmbTipoContrato->addOption    ( '','Selecione'        );
$obCmbTipoContrato->preencheCombo( $rsTipo               );
$obCmbTipoContrato->setNull      ( false                 );
$obCmbTipoContrato->setValue     ( ''                    );
$obCmbTipoContrato->obEvento->setOnChange("montaParametrosGET('tipoContrato')");

$SpnTermoAditivo = new Span;
$SpnTermoAditivo->SetId('spnTermoAditivo');

$obTxtObjContrato = new TextArea;
$obTxtObjContrato->setName   ( "stObjContrato"              );
$obTxtObjContrato->setId     ( "stObjContrato"              );
$obTxtObjContrato->setRotulo ( "Objeto do Contrato"         );
$obTxtObjContrato->setNull   ( false                        );
$obTxtObjContrato->setRows   ( 6                            );
$obTxtObjContrato->setCols   ( 100                          );
$obTxtObjContrato->setMaxCaracteres( 200                    );

$obDtPublicacao = new Data;
$obDtPublicacao->setName   ( "dtPublicacao"                         );
$obDtPublicacao->setRotulo ( "Data de Publicação"                   );
$obDtPublicacao->setValue  ( $dtPublicacao                          );
$obDtPublicacao->setTitle  ( 'Informe a data de publicação.'        );
$obDtPublicacao->setNull   ( true                                  );
$obDtPublicacao->obEvento->setOnChange ( "montaParametrosGET('comparaData','dtPublicacao, dtInicial, dtFinal');" );

$obDtInicial = new Data;
$obDtInicial->setName     ( "dtInicial"                      );
$obDtInicial->setRotulo   ( "Período do Contrato"            );
$obDtInicial->setTitle    ( 'Informe o período do contrato.' );
$obDtInicial->setNull     ( true                            );
$obDtInicial->obEvento->setOnChange ( "montaParametrosGET('comparaData','dtPublicacao, dtInicial, dtFinal');" );
$obDtInicial->obEvento->setOnClick  ( "montaParametrosGET('limpaCampoEmpenho');"                              );

$obDtFirmatura = new Data;
$obDtFirmatura->setName     ( "dtFirmatura"                      );
$obDtFirmatura->setRotulo   ( "Data da Firmatura"            );
$obDtFirmatura->setTitle    ( 'Informe a Data da Firmatura do Termo Aditivo.' );
$obDtFirmatura->setNull     ( true                            );

$obTxtPrazo = new TextBox;
$obTxtPrazo->setName   ( "inPrazo"            );
$obTxtPrazo->setId     ( "inPrazo"            );
$obTxtPrazo->setValue  ( $_REQUEST['inPrazo'] );
$obTxtPrazo->setRotulo ( "Número de Dias"       );
$obTxtPrazo->setTitle  ( "Informe o Número de Dias."      );
$obTxtPrazo->setNull   ( true                      );
$obTxtPrazo->setInteiro( true                       );

$obDtLancamento = new Data;
$obDtLancamento->setName     ( "dtLancamento"                      );
$obDtLancamento->setRotulo   ( "Data de Lançamento"            );
$obDtLancamento->setTitle    ( 'Informe a Data de Lançamento do Acréscimo e/ou Decréscimo.' );
$obDtLancamento->setNull     ( true                            );

$obTxtVlAcrescimo = new Numerico;
$obTxtVlAcrescimo->setName     ( "nuVlAcrescimo"      );
$obTxtVlAcrescimo->setRotulo   ( "Valor do Acréscimo" );
$obTxtVlAcrescimo->setAlign    ( 'RIGHT'             );
$obTxtVlAcrescimo->setTitle    ( ""                  );
$obTxtVlAcrescimo->setMaxLength( 19                  );
$obTxtVlAcrescimo->setSize     ( 21                  );
$obTxtVlAcrescimo->setValue    ( ''                  );
$obTxtVlAcrescimo->setNull     ( true               );

$obTxtVlDecrescimo = new Numerico;
$obTxtVlDecrescimo->setName     ( "nuVlDecrescimo"      );
$obTxtVlDecrescimo->setRotulo   ( "Valor do Decréscimo" );
$obTxtVlDecrescimo->setAlign    ( 'RIGHT'             );
$obTxtVlDecrescimo->setTitle    ( ""                  );
$obTxtVlDecrescimo->setMaxLength( 19                  );
$obTxtVlDecrescimo->setSize     ( 21                  );
$obTxtVlDecrescimo->setValue    ( ''                  );
$obTxtVlDecrescimo->setNull     ( true               );

$obTxtVlContratual = new Numerico;
$obTxtVlContratual->setName     ( "nuVlContratual"      );
$obTxtVlContratual->setRotulo   ( "Valor Contratual" );
$obTxtVlContratual->setAlign    ( 'RIGHT'             );
$obTxtVlContratual->setTitle    ( ""                  );
$obTxtVlContratual->setMaxLength( 19                  );
$obTxtVlContratual->setSize     ( 21                  );
$obTxtVlContratual->setValue    ( ''                  );
$obTxtVlContratual->setNull     ( true               );

$obDtRescisao = new Data;
$obDtRescisao->setName     ( "dtRescisao"                      );
$obDtRescisao->setRotulo   ( "Data de Rescisão"            );
$obDtRescisao->setTitle    ( 'Informe a Data de Rescisão do Contrato.' );
$obDtRescisao->setNull     ( true                            );

$obTxtVlFinalContrato = new Numerico;
$obTxtVlFinalContrato->setName     ( "nuVlFinalContrato"      );
$obTxtVlFinalContrato->setRotulo   ( "Valor Final Contrato" );
$obTxtVlFinalContrato->setAlign    ( 'RIGHT'             );
$obTxtVlFinalContrato->setTitle    ( ""                  );
$obTxtVlFinalContrato->setMaxLength( 19                  );
$obTxtVlFinalContrato->setSize     ( 21                  );
$obTxtVlFinalContrato->setValue    ( ''                  );
$obTxtVlFinalContrato->setNull     ( true               );

$obLabel = new Label;
$obLabel->setValue( " até " );

$obDtFinal = new Data;
$obDtFinal->setName     ( "dtFinal"   );
$obDtFinal->setRotulo   ( "Período"   );
$obDtFinal->setTitle    ( ''          );
$obDtFinal->setNull     ( false       );
$obDtFinal->obEvento->setOnChange ( "montaParametrosGET('comparaData','dtPublicacao, dtInicial, dtFinal');" );

$obTxtVlContrato = new Moeda;
$obTxtVlContrato->setName     ( "nuVlContrato"      );
$obTxtVlContrato->setRotulo   ( "Valor do Contrato" );
$obTxtVlContrato->setAlign    ( 'RIGHT'             );
$obTxtVlContrato->setTitle    ( ""                  );
$obTxtVlContrato->setMaxLength( 19                  );
$obTxtVlContrato->setSize     ( 21                  );
$obTxtVlContrato->setValue    ( ''                  );
$obTxtVlContrato->setNull     ( false               );

$obTxtNroProcesso = new TextBox;
$obTxtNroProcesso->setName   ( "inNumProcesso"            );
$obTxtNroProcesso->setId     ( "inNumProcesso"            );
$obTxtNroProcesso->setValue  ( $_REQUEST['inNumProcesso'] );
$obTxtNroProcesso->setRotulo ( "Número do Processo"       );
$obTxtNroProcesso->setTitle  ( "Informe o número."      );
$obTxtNroProcesso->setNull   ( true                      );
$obTxtNroProcesso->setInteiro( true                       );

$obTxtAnoProcesso = new TextBox;
$obTxtAnoProcesso->setName     ( "stAnoProcesso"                      );
$obTxtAnoProcesso->setValue    (  $stAnoExercicio                       );
$obTxtAnoProcesso->setRotulo   ( "Ano Processo"                              );
$obTxtAnoProcesso->setTitle    ( "Informe o Ano Processo."                    );
$obTxtAnoProcesso->setInteiro  ( false                                     );
$obTxtAnoProcesso->setNull     ( true                                     );
$obTxtAnoProcesso->setMaxLength( 4                                         );
$obTxtAnoProcesso->setSize     ( 5                                         );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setName     ( "stExercicioEmpenho"                      );
$obTxtExercicio->setValue    ( Sessao::getExercicio()                    );
$obTxtExercicio->setRotulo   ( "*Exercício"                              );
$obTxtExercicio->setTitle    ( "Informe o exercício."                    );
$obTxtExercicio->setInteiro  ( false                                     );
$obTxtExercicio->setNull     ( false                                     );
$obTxtExercicio->setMaxLength( 4                                         );
$obTxtExercicio->setSize     ( 5                                         );
$obTxtExercicio->obEvento->setOnClick  ( "montaParametrosGET('limpaCampoEmpenho')" );

$obBscEmpenho = new BuscaInner;
$obBscEmpenho->setTitle               ( "Informe o número do empenho.");
$obBscEmpenho->setRotulo              ( "**Número do Empenho"         );
$obBscEmpenho->setId                  ( "stEmpenho"                   );
$obBscEmpenho->setValue               ( $stEmpenho                    );
$obBscEmpenho->setMostrarDescricao    ( true                          );
$obBscEmpenho->obCampoCod->setName    ( "numEmpenho"                  );
$obBscEmpenho->obCampoCod->setValue   (  $numEmpenho                  );
$obBscEmpenho->obCampoCod->obEvento->setOnChange( "montaParametrosGET('preencheInner','numEmpenho, inCodEntidade, stExercicioEmpenho');" );
$obBscEmpenho->setFuncaoBusca         ( "abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLProcurarEmpenho.php','frm','numEmpenho','stEmpenho','empenhoComplementar&inCodigoEntidade='+document.frm.inCodEntidade.value + '&dtInicial='+document.frm.dtInicial.value + '&stExercicioEmpenho='+document.frm.stExercicioEmpenho.value,'".Sessao::getId()."','800','550');");

$obBtnIncluir = new Button;
$obBtnIncluir->setValue             ( "Incluir"     );
$obBtnIncluir->setName              ( "btnIncluir"  );
$obBtnIncluir->setId                ( "btnIncluir"  );
$obBtnIncluir->setDisabled          ( true          );
$obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET('incluirEmpenhoLista','numEmpenho, stExercicioEmpenho, dtInicial, inCodEntidade');" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimpar");
$obBtnLimpar->setId                ( "limpar" );
$obBtnLimpar->setValue             ( "Limpar" );
$obBtnLimpar->setDisabled          ( true     );
$obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limpar');" );

$spnLista = new Span;
$spnLista->setId  ( 'spnLista' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden          ( $obHdnAcao                                    );
$obFormulario->addHidden          ( $obHdnCtrl                                    );
$obFormulario->addHidden          ( $obHdnCodContrato                             );
$obFormulario->addHidden          ( $obHdnCodEntidade                             );
$obFormulario->addHidden          ( $obHdnExercicioContrato                       );
$obFormulario->addComponente      ( $obTxtContrato                                );
$obFormulario->addComponente      ( $obEntidadeUsuario                            );
$obFormulario->addComponente      ( $obCmbModLicitacao                            );
$obFormulario->addComponente      ( $obCmbAssunto                                 );
$obFormulario->addComponente      ( $obCmbSubAssunto                              );
$obFormulario->addSpan            ( $SpnSubAssunto                                );
$obFormulario->addComponente      ( $obCmbTipoContrato                            );
$obFormulario->addSpan            ( $SpnTermoAditivo                              );
$obFormulario->addComponente      ( $obTxtObjContrato                             );
$obFormulario->addComponente      ( $obDtPublicacao                               );
$obFormulario->agrupaComponentes  ( array( $obDtInicial,$obLabel, $obDtFinal )    );
$obFormulario->addComponente      ( $obTxtVlContrato                              );
$obFormulario->addComponente      ( $obTxtNroProcesso );
$obFormulario->addComponente      ( $obTxtAnoProcesso );

$obFormulario->addComponente($obDtFirmatura);
$obFormulario->addComponente($obTxtPrazo);
$obFormulario->addComponente($obDtLancamento);
$obFormulario->addComponente($obTxtVlAcrescimo);
$obFormulario->addComponente($obTxtVlDecrescimo);
$obFormulario->addComponente($obTxtVlContratual);
$obFormulario->addComponente($obDtRescisao);
$obFormulario->addComponente($obTxtVlFinalContrato);

$obFormulario->addTitulo          ( "Dados dos empenhos do contrato"              );
$obFormulario->addComponente      ( $obTxtExercicio                               );
$obFormulario->addComponente      ( $obBscEmpenho                                 );
$obFormulario->agrupaComponentes  ( array( $obBtnIncluir, $obBtnLimpar ),"",""    );
$obFormulario->addSpan            ( $spnLista                                     );

$obFormulario->OK();
$obFormulario->show();

$jsOnload = "montaParametrosGET('carregaDados','inNumContrato, stExercicioContrato, inCodEntidade, stAcao');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
