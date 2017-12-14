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
	* Formulário de Cadastro de Contratos TCEMG
	* Data de Criação   : 21/02/2014

	* @author Analista      Sergio Luiz dos Santos
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: FMManterContrato.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php'        );
include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'  );
include_once ( '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php'	);
include_once ( '../../../../../../gestaoFinanceira/fontes/PHP/empenho/classes/componentes/IPopUpEmpenho.class.php'               		);
include_once ( CAM_GF_PPA_COMPONENTES.'ITextBoxSelectOrgao.class.php'                                   );
include_once ( CAM_GA_CGM_COMPONENTES.'IPopUpCGMVinculado.class.php'                                    );
include_once ( CAM_GP_COM_COMPONENTES.'IPopUpFornecedor.class.php'                                      );

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

include_once($pgJs);

Sessao::remove( 'arEmpenhos'    );
Sessao::remove( 'arFornecedores');
Sessao::remove( 'arAditivo'     );
Sessao::remove( 'arApostila'    );

//*****************************************************//
// Busca componentes Modalidade, Objeto e Instrumento
//*****************************************************//
$stOrder = " ORDER BY descricao ";

include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoModalidadeLicitacao.class.php'	);
$obTTCEMGContratoModalidadeLicitacao = new TTCEMGContratoModalidadeLicitacao;
$obTTCEMGContratoModalidadeLicitacao->recuperaTodos($rsModLic, "", $stOrder);

include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoObjeto.class.php' );
$obTTCEMGContratoObjeto = new TTCEMGContratoObjeto;
$obTTCEMGContratoObjeto->recuperaTodos($rsObjeto, "", $stOrder);

include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoInstrumento.class.php' );
$obTTCEMGContratoInstrumento = new TTCEMGContratoInstrumento;
$obTTCEMGContratoInstrumento->recuperaTodos($rsInstrumento, "", $stOrder);

include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoAditivoTipo.class.php' );
$obTTCEMGContratoAditivoTipo = new TTCEMGContratoAditivoTipo;
$obTTCEMGContratoAditivoTipo->recuperaTodos($rsTermoAditivo, "", $stOrder);

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obHdnCodContrato = new Hidden;
$obHdnCodContrato->setName  ( "inCodContrato"           );
$obHdnCodContrato->setValue ( $_REQUEST['inCodContrato']);

$stExercicioContrato= (isset($_REQUEST['stExercicioContrato']))? $_REQUEST['stExercicioContrato'] : Sessao::getExercicio();
$obHdnExercicioContrato = new Hidden;
$obHdnExercicioContrato->setName  ( "exercicio_contrato" );
$obHdnExercicioContrato->setValue ( $stExercicioContrato );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName  ( "cod_entidade"          	 );
$obHdnCodEntidade->setValue ( $_REQUEST['inCodEntidade'] );

$obTxtContrato = new TextBox;
$obTxtContrato->setName   ( "inNumContrato"            );
$obTxtContrato->setId     ( "inNumContrato"            );
$obTxtContrato->setValue  ( $_REQUEST['inNumContrato'] );
$obTxtContrato->setRotulo ( "Número do Contrato"       );
$obTxtContrato->setTitle  ( "Informe o contrato."      );
$obTxtContrato->setNull   ( false                      );
$obTxtContrato->setInteiro( true                       );

if ($stAcao == 'alterar') {
    $obTxtContrato->setDisabled (true);
    
    $obHdnNumContrato = new Hidden;
    $obHdnNumContrato->setName  ( "hdnNumContrato"          );
    $obHdnNumContrato->setValue ( $_REQUEST['inNumContrato']);
}

$obTxtExercicioContrato = new TextBox;
$obTxtExercicioContrato->setName     ( "stExercicioContrato"			);
$obTxtExercicioContrato->setValue    ( $stExercicioContrato			);
$obTxtExercicioContrato->setRotulo   ( "*Exercício do Contrato"			);
$obTxtExercicioContrato->setTitle    ( "Informe o exercício do contrato."	);
$obTxtExercicioContrato->setInteiro  ( false                              	);
$obTxtExercicioContrato->setNull     ( true                               	);
$obTxtExercicioContrato->setMaxLength( 4                                   	);
$obTxtExercicioContrato->setSize     ( 5                                	);

$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuario->setNull( false  );
$obEntidadeUsuario->obTextBox->setSize		( 3 );
$obEntidadeUsuario->obTextBox->setMaxLength	( 1 );
$obEntidadeUsuario->obTextBox->obEvento->setOnChange("montaParametrosGET('carregaDados','inNumContrato, stExercicioContrato, inCodEntidade, stAcao');");
$obEntidadeUsuario->obSelect->obEvento->setOnChange ("montaParametrosGET('carregaDados','inNumContrato, stExercicioContrato, inCodEntidade, stAcao');");

$obSelectOrgao = new ITextBoxSelectOrgao;
$obSelectOrgao->setNull ( false );
$obSelectOrgao->setRotulo('Órgão Responsável');
$obSelectOrgao->obTextBox->setSize       ( 3 );
$obSelectOrgao->obTextBox->setMaxLength  ( 2 );
$obSelectOrgao->obSelect->setStyle       ( "width: 520"                               );
$obSelectOrgao->obTextBox->obEvento->setOnChange("montaParametrosGET('MontaUnidade');");
$obSelectOrgao->obSelect->obEvento->setOnChange ("montaParametrosGET('MontaUnidade');");

//Unidade
$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo    	( "Unidade"                       	);
$obTxtUnidade->setTitle      	( "Selecione a unidade para filtro."    );
$obTxtUnidade->setName       	( "inCodUnidadeTxt"        	        );
$obTxtUnidade->setId         	( "inCodUnidadeTxt"               	);
$obTxtUnidade->setValue       	( $inCodUnidadeTxt                	);
$obTxtUnidade->setSize		( 6                               	);
$obTxtUnidade->setMaxLength	( 3                               	);
$obTxtUnidade->setInteiro   	( true                            	);
$obTxtUnidade->setObrigatorio	( true                            	);

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo      	( "Unidade"		);
$obCmbUnidade->setName        	( "inCodUnidade"	);
$obCmbUnidade->setId          	( "inCodUnidade" 	);
$obCmbUnidade->setValue       	( $inCodUnidade		);
$obCmbUnidade->setStyle       	( "width: 200px"	);
$obCmbUnidade->setCampoID     	( "cod_unidade"		);
$obCmbUnidade->setCampoDesc   	( "descricao"		);
$obCmbUnidade->addOption  	( "", "Selecione"	);
$obCmbUnidade->setObrigatorio	( true );
//Fim Unidade

$obCmbModLicitacao = new Select;
$obCmbModLicitacao->setName      ( "cod_modalidade"            );
$obCmbModLicitacao->setRotulo    ( "Modalidade de Licitação"   );
$obCmbModLicitacao->setId        ( "stNomModLic"               );
$obCmbModLicitacao->setCampoId   ( "cod_modalidade_licitacao"  );
$obCmbModLicitacao->setCampoDesc ( "descricao"                 );
$obCmbModLicitacao->addOption    ( '','Selecione'              );
$obCmbModLicitacao->preencheCombo( $rsModLic                   );
$obCmbModLicitacao->setNull      ( false                       );
$obCmbModLicitacao->setValue     ( ''                          );
$obCmbModLicitacao->obEvento->setOnChange("montaParametrosGET('MontaModalidade');");

$SpnEntidadeLicitacao = new Span;
$SpnEntidadeLicitacao->SetId('spnEntidadeLicitacao');

$obCmbObjeto = new Select;
$obCmbObjeto->setName      ( "cod_objeto"          );
$obCmbObjeto->setRotulo    ( "Natureza do Objeto"  );
$obCmbObjeto->setId        ( "stObjeto"            );
$obCmbObjeto->setCampoId   ( "cod_objeto"          );
$obCmbObjeto->setCampoDesc ( "descricao"           );
$obCmbObjeto->addOption    ( '','Selecione'        );
$obCmbObjeto->preencheCombo( $rsObjeto             );
$obCmbObjeto->setNull      ( false                 );
$obCmbObjeto->setValue     ( ''                    );
$obCmbObjeto->obEvento->setOnChange("montaParametrosGET('MontaFormaNatureza');");

$obCmbInstrumento = new Select;
$obCmbInstrumento->setName      ( "cod_instrumento"     );
$obCmbInstrumento->setRotulo    ( "Tipo de Instrumento" );
$obCmbInstrumento->setId        ( "stInstrumento"       );
$obCmbInstrumento->setCampoId   ( "cod_instrumento"     );
$obCmbInstrumento->setCampoDesc ( "descricao"           );
$obCmbInstrumento->addOption    ( '','Selecione'        );
$obCmbInstrumento->preencheCombo( $rsInstrumento        );
$obCmbInstrumento->setNull      ( false                 );
$obCmbInstrumento->setValue     ( ''                    );

$obTxtObjContrato = new TextArea;
$obTxtObjContrato->setName	    ( "stObjContrato"   	);
$obTxtObjContrato->setId	    ( "stObjContrato"		);
$obTxtObjContrato->setRotulo	    ( "Objeto do Contrato"	);
$obTxtObjContrato->setNull	    ( false          		);
$obTxtObjContrato->setRows	    ( 5                		);
$obTxtObjContrato->setCols	    ( 100              		);
$obTxtObjContrato->setMaxCaracteres ( 500       		);

$obDtPublicacao = new Data;
$obDtPublicacao->setName   ( "dtPublicacao"                 	); 
$obDtPublicacao->setRotulo ( "Data de Publicação"           	);
$obDtPublicacao->setTitle  ( 'Informe a data de publicação.'	);
$obDtPublicacao->setNull   ( false                         	);
$obDtPublicacao->obEvento->setOnChange("montaParametrosGET('comparaData','dtPublicacao, dtInicial, dtFinal');");

$obDtInicial = new Data;
$obDtInicial->setName     ( "dtInicial"                      );
$obDtInicial->setId       ( "dtInicial"                      );
$obDtInicial->setRotulo   ( "Período do Contrato"            );
$obDtInicial->setTitle    ( 'Informe o período do contrato.' );
$obDtInicial->setNull     ( false                            );
$obDtInicial->obEvento->setOnChange ( "montaParametrosGET('comparaData','dtPublicacao, dtInicial, dtFinal');" );
$obDtInicial->obEvento->setOnClick  ( "montaParametrosGET('limpaCampoEmpenho');"                              );

$obDtAssinatura = new Data;
$obDtAssinatura->setName     ( "dtAssinatura"                      		);
$obDtAssinatura->setRotulo   ( "*Data da Assinatura"            		);
$obDtAssinatura->setTitle    ( 'Informe a Data da Assinatura do Processo.'	);
$obDtAssinatura->setNull     ( false                           			);

$obLabel = new Label;
$obLabel->setValue( " até " );

$obDtFinal = new Data;
$obDtFinal->setName     ( "dtFinal"   );
$obDtFinal->setRotulo   ( "Período"   );
$obDtFinal->setTitle    ( ''          );
$obDtFinal->setNull     ( false       );
$obDtFinal->obEvento->setOnChange("montaParametrosGET('comparaData','dtPublicacao, dtInicial, dtFinal');");

$obTxtVlContrato = new Moeda;
$obTxtVlContrato->setName     ( "nuVlContrato"      );
$obTxtVlContrato->setRotulo   ( "Valor do Contrato" );
$obTxtVlContrato->setAlign    ( 'RIGHT'             );
$obTxtVlContrato->setTitle    ( ""                  );
$obTxtVlContrato->setMaxLength( 19                  );
$obTxtVlContrato->setSize     ( 21                  );
$obTxtVlContrato->setValue    ( ''                  );
$obTxtVlContrato->setNull     ( false               );

$SpnContratante = new Span;
$SpnContratante->SetId('spnContratante');

$SpnFormaNatureza = new Span;
$SpnFormaNatureza->SetId('spnFormaNatureza');

//cgm do signatário da contratante
$obCGMSignatario = new IPopUpCGM($obForm);
$obCGMSignatario->setName              ( 'stNomSignatario'              );
$obCGMSignatario->setId                ( 'stNomSignatario'              );
$obCGMSignatario->obCampoCod->setName  ( 'cgmSignatario'                );
$obCGMSignatario->obCampoCod->setId    ( 'cgmSignatario'                );
$obCGMSignatario->setRotulo( 'CGM do signatário da contratante'         );
$obCGMSignatario->setTitle( 'Informe o CGM do signatário da contratante');
$obCGMSignatario->setTipo              ('fisica');
$obCGMSignatario->setNull              ( false  );

//Painel veiculos de publicidade 
$obVeiculoPublicidade = new IPopUpCGMVinculado  ( $obForm );
$obVeiculoPublicidade->setTabelaVinculo  	( 'licitacao.veiculos_publicidade' 		);
$obVeiculoPublicidade->setCampoVinculo  	( 'numcgm'                         		);
$obVeiculoPublicidade->setNomeVinculo     	( 'Veículo de Publicação'          		);
$obVeiculoPublicidade->setRotulo      		( '*Veículo de Publicação'			);
$obVeiculoPublicidade->setTitle        		( 'Informe o Veículo de Publicidade.'	        );
$obVeiculoPublicidade->setName         		( 'stNomCgmVeiculoPublicadade'     		);
$obVeiculoPublicidade->setId           		( 'stNomCgmVeiculoPublicadade'     		);
$obVeiculoPublicidade->obCampoCod->setName	( 'inVeiculo'                      		);
$obVeiculoPublicidade->obCampoCod->setId	( 'inVeiculo'                      		);
$obVeiculoPublicidade->setNull			( true 						);
$obVeiculoPublicidade->obCampoCod->setNull	( true 						);

$obTxtExercicio = new TextBox;
$obTxtExercicio->setName     ( "stExercicioEmpenho"	);
$obTxtExercicio->setValue    ( Sessao::getExercicio()	);
$obTxtExercicio->setRotulo   ( "*Exercício"          	);
$obTxtExercicio->setTitle    ( "Informe o exercício."	);
$obTxtExercicio->setInteiro  ( false                 	);
$obTxtExercicio->setNull     ( false        		);
$obTxtExercicio->setMaxLength( 4                      	);
$obTxtExercicio->setSize     ( 5                     	);
$obTxtExercicio->obEvento->setOnClick("montaParametrosGET('limpaCampoEmpenho')");

$obBscEmpenho = new BuscaInner;
$obBscEmpenho->setTitle               ( "Informe o número do empenho.");
$obBscEmpenho->setRotulo              ( "**Número do Empenho"         );
$obBscEmpenho->setId                  ( "stEmpenho"                   );
$obBscEmpenho->setValue               ( $stEmpenho                    ); 
$obBscEmpenho->setMostrarDescricao    ( true                          );
$obBscEmpenho->obCampoCod->setName    ( "numEmpenho"                  );
$obBscEmpenho->obCampoCod->setValue   (  $numEmpenho                  );
$obBscEmpenho->obCampoCod->obEvento->setOnChange("montaParametrosGET('preencheInner','numEmpenho, inCodEntidade, stExercicioEmpenho');");
$obBscEmpenho->setFuncaoBusca         ( "abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLProcurarEmpenho.php','frm','numEmpenho','stEmpenho','empenhoComplementar&inCodigoEntidade='+document.frm.inCodEntidade.value + '&dtInicial='+document.frm.dtInicial.value + '&stExercicioEmpenho='+document.frm.stExercicioEmpenho.value,'".Sessao::getId()."','800','550');");

$obBtnIncluir = new Button;
$obBtnIncluir->setValue             ( "Incluir"			);
$obBtnIncluir->setName              ( "btnIncluirEmp"	);
$obBtnIncluir->setId                ( "btnIncluirEmp"	);
$obBtnIncluir->setDisabled          ( true          	);
$obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET('incluirEmpenhoLista','numEmpenho, stExercicioEmpenho, dtInicial, inCodEntidade');" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimparEmp"	);
$obBtnLimpar->setId                ( "limparEmp" 	);
$obBtnLimpar->setValue             ( "Limpar" 		);
$obBtnLimpar->setDisabled          ( true     		);
$obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limpar');" );

$spnLista = new Span;
$spnLista->setId( 'spnLista' );

$obFornecedor = new IPopUpFornecedor($obForm);
$obFornecedor->setTitle	( "Selecione o Fornecedor que deseja pesquisar.");
$obFornecedor->setRotulo( "*Fornecedor" 				);
$obFornecedor->setName	( "stNomCGM"					);
$obFornecedor->setNull	( true 						);
$obFornecedor->obCampoCod->setId( "inCodFornecedor"		        );

//cgm do representante legal
$obCGMRep = new IPopUpCGM($obForm);
$obCGMRep->setName              ( 'stNomRepLegal'           );
$obCGMRep->setId                ( 'stNomRepLegal'           );
$obCGMRep->obCampoCod->setName  ( 'cgmRepLegal'             );
$obCGMRep->obCampoCod->setId    ( 'cgmRepLegal'             );
$obCGMRep->setRotulo( 'CGM do Representante Legal'          );
$obCGMRep->setTitle( 'Informe o CGM do representante legal' );
$obCGMRep->setTipo              ( 'fisica'                  );
$obCGMRep->setNull              ( true );
$obCGMRep->setObrigatorioBarra  ( true );

$spnFornecedor = new Span;
$spnFornecedor->setId( 'spnFornecedor' );

$obBtnFornIncluir = new Button;
$obBtnFornIncluir->setValue             ( "Incluir"     );
$obBtnFornIncluir->setName              ( "btnIncluir"  );
$obBtnFornIncluir->setId                ( "btnIncluir"  );
$obBtnFornIncluir->obEvento->setOnClick ( "montaParametrosGET('incluirFornecedorLista','inCodFornecedor, stNomCGM, cgmRepLegal, stNomRepLegal');" );

$obBtnFornLimpar = new Button;
$obBtnFornLimpar->setName              ( "btnLimpar");
$obBtnFornLimpar->setId                ( "limpar" 	);
$obBtnFornLimpar->setValue             ( "Limpar" 	);
$obBtnFornLimpar->obEvento->setOnClick ( "montaParametrosGET('limparFornecedor');" );
 
//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden            ( $obHdnAcao                                    );
$obFormulario->addHidden            ( $obHdnCtrl                                    );
$obFormulario->addHidden            ( $obHdnCodContrato                             );
if ($stAcao == 'alterar') { $obFormulario->addHidden            ( $obHdnNumContrato                             ); }
$obFormulario->addHidden            ( $obHdnCodEntidade                             );
$obFormulario->addHidden            ( $obHdnExercicioContrato                       );
$obFormulario->addComponente        ( $obTxtContrato                                );
$obFormulario->addComponente        ( $obTxtExercicioContrato                       );
$obFormulario->addComponente        ( $obDtAssinatura                               );
$obFormulario->addComponente        ( $obEntidadeUsuario                            );
$obFormulario->addComponente        ( $obSelectOrgao                                );
$obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade                  );
$obFormulario->addComponente        ( $obCmbModLicitacao                            );
$obFormulario->addSpan              ( $SpnEntidadeLicitacao                         );
$obFormulario->addComponente        ( $obCmbObjeto                                  );
$obFormulario->addComponente        ( $obTxtObjContrato                             );
$obFormulario->addComponente        ( $obCmbInstrumento                             );
$obFormulario->agrupaComponentes    ( array( $obDtInicial,$obLabel, $obDtFinal )	);
$obFormulario->addComponente        ( $obTxtVlContrato                              );
$obFormulario->addSpan              ( $SpnFormaNatureza                             );
$obFormulario->addComponente        ( $obCGMSignatario                              );
$obFormulario->addTitulo            ( 'Veículo de Publicação'                       );
$obFormulario->addComponente        ( $obVeiculoPublicidade                         );
$obFormulario->addComponente        ( $obDtPublicacao                               );

$obFormulario->addTitulo            ( "Dados dos empenhos do contrato"              );
$obFormulario->addComponente        ( $obTxtExercicio                               );
$obFormulario->addComponente        ( $obBscEmpenho                                 );
$obFormulario->agrupaComponentes    ( array( $obBtnIncluir, $obBtnLimpar ),"",""    );
$obFormulario->addSpan              ( $spnLista                                     );

$obFormulario->addTitulo            ( "Empresa(s) Consorciada(s) / Contratado(s)."  );
$obFormulario->addComponente        ( $obFornecedor                                 );
$obFormulario->addComponente        ( $obCGMRep                                     );
$obFormulario->agrupaComponentes    ( array( $obBtnFornIncluir, $obBtnFornLimpar ),"","" );
$obFormulario->addSpan              ( $spnFornecedor                                );

$obOk  = new Ok();
$obOk->obEvento->setOnClick("ValidaContrato();");

if ($stAcao == 'alterar') {
    $stFiltro  = "&pg=".Sessao::read('pg');
    $stFiltro .= "&pos=".Sessao::read('pos');
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
    
    $obCancelarLimpar  = new Cancelar;
    $obCancelarLimpar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");
} else {
    $obCancelarLimpar  = new Limpar;
}

$obFormulario->defineBarra( array( $obOk, $obCancelarLimpar ) );

$obFormulario->show();

$jsOnload = "montaParametrosGET('carregaDados','inNumContrato, stExercicioContrato, inCodEntidade, stAcao, cod_entidade, exercicio_contrato, inCodContrato');";

include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php');
?>