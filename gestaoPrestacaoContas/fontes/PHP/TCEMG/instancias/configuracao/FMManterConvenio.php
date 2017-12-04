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
	* Formulario de Convenio
	* Data de Criação   : 10/03/2014

	* @author Analista: Sergio Luiz dos Santos
	* @author Desenvolvedor: Michel Teixeira
	* @ignore

	$Id: FMManterConvenio.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

	*Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once ( CAM_GP_COM_COMPONENTES.'IPopUpEditObjeto.class.php'              );
include_once ( CAM_GA_CGM_COMPONENTES.'IPopUpCGMVinculado.class.php'            );
include_once ( CAM_GA_ADM_COMPONENTES.'ITextBoxSelectDocumento.class.php'       );
include_once ( CAM_GP_LIC_MAPEAMENTO.'TLicitacaoPublicacaoConvenio.class.php'	);
include_once ( CAM_GF_PPA_COMPONENTES.'ITextBoxSelectOrgao.class.php'           );

$stAcao = $request->get('stAcao');
if (empty($stAcao)) {
    $stAcao = "incluir";
}

Sessao::remove('participantes');
Sessao::remove('arEmpenhos');
Sessao::remove('arAditivo');

//Define o nome dos arquivos PHP
$stPrograma = "ManterConvenio";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once $pgJs;
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/ajax.php';

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( 'oculto');

$obHdnCodConvenio = new Hidden;
$obHdnCodConvenio->setName  ( "cod_convenio"          	 );
$obHdnCodConvenio->setId    ( "cod_convenio"          	 );
$obHdnCodConvenio->setValue ( $_REQUEST['inCodConvenio'] );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName  ( "cod_entidade"          	 );
$obHdnCodEntidade->setId    ( "cod_entidade"          	 );
$obHdnCodEntidade->setValue ( $_REQUEST['inCodEntidade'] );

/* Entidade do Convênio */
$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuario->setNull( false  );
$obEntidadeUsuario->obTextBox->setSize( 3 );
$obEntidadeUsuario->obTextBox->setMaxLength( 1 );
$obEntidadeUsuario->obTextBox->obEvento->setOnChange("montaParametrosGET('carregaEntidade','inCodEntidade');");
$obEntidadeUsuario->obSelect->obEvento->setOnChange ("montaParametrosGET('carregaEntidade','inCodEntidade');");

/* Exercício */
$stExercicioConvenio= (isset($rsConvenio))? $rsConvenio->getCampo('exercicio') : Sessao::getExercicio();

$obHdnStExercicio =  new Hidden;
$obHdnStExercicio->setName  ( "stExercicio"        );
$obHdnStExercicio->setValue ( $stExercicioConvenio );

$obLblExercicio = new Inteiro;
$obLblExercicio->setName    ( "inExercicio"             );
$obLblExercicio->setRotulo  ( "Exercício do Convênio"   );
$obLblExercicio->setValue   ( $stExercicioConvenio      );
$obLblExercicio->setDisabled( true );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );

/* NUMERO DO CONVENIO*/
$obIntNumConvenio = new Inteiro;
$obIntNumConvenio->setName  ( "inNumConvenio"               );
$obIntNumConvenio->setId    ( "inNumConvenio"               );
$obIntNumConvenio->setRotulo( "Número do Convênio"          );
$obIntNumConvenio->setTitle ( "Informe o número do convênio");
$obIntNumConvenio->setNull  ( false );

if ($_REQUEST['stAcao'] == 'alterar') {
    $obIntNumConvenio->setDisabled( true );
    
    $obHdnNumConvenio =  new Hidden;
    $obHdnNumConvenio->setName  ( "hdnNumConvenio" );
    $obHdnNumConvenio->setValue ( $_REQUEST['inNumConvenio'] );
}

/* OBJETO */
$obObjeto = new IPopUpEditObjeto($obForm);
$obObjeto->setNull  ( false );
$obObjeto->setName  ( "stObjeto" );
$obObjeto->setId    ( "stObjeto" );

/* Data da Assinatura */
$obDtAssinatura = new Data;
$obDtAssinatura->setName    ( 'dtAssinatura'        );
$obDtAssinatura->setId      ( 'dtAssinatura'        );
$obDtAssinatura->setRotulo  ( 'Data da Assinatura'  );
$obDtAssinatura->setNull    ( false                 );
$obDtAssinatura->obEvento->setOnChange  ( 'validaDatasAssinatura( this );');
$obDtAssinatura->obEvento->setOnBlur    ( 'validaDatasAssinatura( this );');
$obDtAssinatura->setTitle   ( 'Informe a Data de Assinatura do Convênio.' );

/* Data do Final de Vigencia */
$obDtFinalVigencia = new Data;
$obDtFinalVigencia->setName     ( 'dtFinalVigencia'              );
$obDtFinalVigencia->setId       ( 'dtFinalVigencia'              );
$obDtFinalVigencia->setRotulo   ( 'Data do Final da Vigência'    );
$obDtFinalVigencia->setNull     ( false                          );
$obDtFinalVigencia->obEvento->setOnChange   ( 'validaDatasAssinatura( this );'  );
$obDtFinalVigencia->obEvento->setOnBlur     ( 'validaDatasAssinatura( this );'  );
$obDtFinalVigencia->setTitle    ( 'Informe a Data do Final da Vigência.'        );

/* Valor */
$obValorConvenio = new Numerico;
$obValorConvenio->setName       ( 'nuValorConvenio'               );
$obValorConvenio->setId         ( 'nuValorConvenio'               );
$obValorConvenio->setMaxLength  ( 14                              );
$obValorConvenio->setSize       ( 18                              );
$obValorConvenio->setRotulo     ( 'Valor do Convênio'             );
$obValorConvenio->setTitle      ( 'Informe o Valor do Convênio'   );
$obValorConvenio->setNull       ( false                           );

/* Valor Contra-Partida */
$obValorContra = new Numerico;
$obValorContra->setName     ( 'nuValorContra'           );
$obValorContra->setId       ( 'nuValorContra'           );
$obValorContra->setMaxLength( 14                        );
$obValorContra->setSize     ( 18                        );
$obValorContra->setRotulo   ( 'Valor de Contra-Partida' );
$obValorContra->setNull     ( false                     );
$obValorContra->setTitle    ( 'Informe o Valor de Contra-Partida do Convênio' );

/* Data Início Execução*/
$obDtInicioExecucao = new Data;
$obDtInicioExecucao->setName    ( 'dtInicioExecucao'            );
$obDtInicioExecucao->setId      ( 'dtInicioExecucao'            );
$obDtInicioExecucao->setRotulo  ( 'Data de Início de Execução'  );
$obDtInicioExecucao->setNull    ( false                         );
$obDtInicioExecucao->setTitle   ( 'Informe a data de início de execução do convênio.'   );
$obDtInicioExecucao->obEvento->setOnChange  ( 'validaDatasAssinatura( this );'          );
$obDtInicioExecucao->obEvento->setOnBlur    ( 'validaDatasAssinatura( this );'          );

// DADOS DOS PARTICIPANTES
/* CGM */
$obCgmParticipante =  new IPopUpCGMVinculado($obForm);
$obCgmParticipante->setTabelaVinculo    ( 'compras.fornecedor'                  );
$obCgmParticipante->setCampoVinculo     ( 'cgm_fornecedor'                      );
$obCgmParticipante->setNomeVinculo      ( 'Participante'                        );
$obCgmParticipante->setRotulo           ( 'CGM'                                 );
$obCgmParticipante->setTitle            ( 'Selecione o CGM do participante'     );
$obCgmParticipante->setObrigatorioBarra ( true                                  );
$obCgmParticipante->setName             ( 'stNomCgmParticipante'                );
$obCgmParticipante->setId               ( 'stNomCgmParticipante'                );
$obCgmParticipante->obCampoCod->setName ( 'inCgmParticipante'                   );
$obCgmParticipante->obCampoCod->setId   ( 'inCgmParticipante'                   );
$obCgmParticipante->setNull             ( true                                  );

/* Tipo de Participante */
require_once ( CAM_GP_LIC_MAPEAMENTO.'TLicitacaoTipoParticipante.class.php' );
$obTLicitacaoTipoParticipante = new TLicitacaoTipoParticipante;
$obTLicitacaoTipoParticipante->recuperaTodos($rsTiposParticipante);

for($i=0;$i<(count($rsTiposParticipante->arElementos));$i++){
    if($rsTiposParticipante->arElementos[$i]['descricao']=="Concedente"){
        $idParticipante     = $rsTiposParticipante->arElementos[$i][ 'cod_tipo_participante'];
        $descParticipante   = $rsTiposParticipante->arElementos[$i][ 'descricao'            ];
    } 
}

$obCmbTiposParticipante = new Select;
$obCmbTiposParticipante->setTitle       ( "Selecione o tipo de participação");
$obCmbTiposParticipante->setName        ( "inCodTipoParticipante"           );
$obCmbTiposParticipante->setId          ( "inCodTipoParticipante"           );
$obCmbTiposParticipante->setRotulo      ( "Tipo de Participação"            );
$obCmbTiposParticipante->addOption      ( $idParticipante, $descParticipante);
$obCmbTiposParticipante->setCampoId     ( "cod_tipo_participante"           );
$obCmbTiposParticipante->setCampoDesc   ( "descricao"                       );
$obCmbTiposParticipante->setObrigatorioBarra( true                          );

/* Valor Participacao */
$obValorParticipacao = new Numerico;
$obValorParticipacao->setName           ( 'nuValorParticipacao'             );
$obValorParticipacao->setId             ( 'nuValorParticipacao'             );
$obValorParticipacao->setMaxLength      ( 18                                );
$obValorParticipacao->setSize           ( 18                                );
$obValorParticipacao->setRotulo         ( 'Valor de Participação'           );
$obValorParticipacao->setTitle          ( 'Informe o Valor de Participação' );
$obValorParticipacao->setObrigatorioBarra   ( true                          );
$obValorParticipacao->obEvento->setOnChange ( "montaParametrosGET('atualizaParticipacao', 'nuValorConvenio,nuValorParticipacao,hdnPercentualParticipacao', true);" );

$arrayEsfera = array(1=>'Federal', 2=>'Estadual', 3=>'Municipal');

$obCmbEsfera = new Select;
$obCmbEsfera->setTitle       ( "Selecione a Esfera do Concedente"   );
$obCmbEsfera->setName        ( "stEsfera"               );
$obCmbEsfera->setId          ( "stEsfera"               );
$obCmbEsfera->setRotulo      ( "Esfera do Concedente"   );
$obCmbEsfera->addOption      ( '', 'Selecione'          );
$obCmbEsfera->addOption      ( $arrayEsfera[1], $arrayEsfera[1]     );
$obCmbEsfera->addOption      ( $arrayEsfera[2], $arrayEsfera[2]     );
$obCmbEsfera->addOption      ( $arrayEsfera[3], $arrayEsfera[3]     );
$obCmbEsfera->setObrigatorioBarra( true  );

/* Percentual de Participação */
$obHdnPercentualParticipacao = new Hidden;
$obHdnPercentualParticipacao->setId     ( 'hdnPercentualParticipacao' );
$obHdnPercentualParticipacao->setName   ( 'hdnPercentualParticipacao' );
$obHdnPercentualParticipacao->setValue  ( '' );

$obPercentualParticipacao = new Label;
$obPercentualParticipacao->setId    	( 'nuPercentualParticipacao'	);
$obPercentualParticipacao->setName  	( 'nuPercentualParticipacao'	);
$obPercentualParticipacao->setRotulo	( 'Percentual de Participação'	);
$obPercentualParticipacao->setValue 	( '0,00 %' );

//BOTÕES DO PARTICIPANTE
/* Botão Incluir */
$obBtnIncluirParticipante = new Button;
$obBtnIncluirParticipante->setName              ( "btnIncluirParticipante" );
$obBtnIncluirParticipante->setId                ( "btnIncluirParticipante" );
$obBtnIncluirParticipante->setValue             ( "Incluir" );
$obBtnIncluirParticipante->setTipo              ( "button"  );
$obBtnIncluirParticipante->setDisabled          ( false     );
$obBtnIncluirParticipante->obEvento->setOnClick ( "montaParametrosGET('incluirParticipante', '', true);" );

/* Botão Limpar */
$obBtnLimparParticipante = new Button;
$obBtnLimparParticipante->setName               ( "btnLimparParticipante" );
$obBtnLimparParticipante->setValue              ( "Limpar"	);
$obBtnLimparParticipante->setTipo               ( "button"	);
$obBtnLimparParticipante->setDisabled           ( false 	);
$obBtnLimparParticipante->obEvento->setOnClick  ( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."', 'limpaParticipante');" );

/* Span Participantes */
$obSpanParticipantes = new Span;
$obSpanParticipantes->setId ( 'spnParticipantes' );

//DADOS DO EMPENHO
/* Exercício Empenho */
$obTxtExercicioEmpenho = new TextBox;
$obTxtExercicioEmpenho->setName     ( "stExercicioEmpenho"      );
$obTxtExercicioEmpenho->setValue    ( Sessao::getExercicio()    );
$obTxtExercicioEmpenho->setRotulo   ( "*Exercício"              );
$obTxtExercicioEmpenho->setTitle    ( "Informe o exercício."	);
$obTxtExercicioEmpenho->setInteiro  ( false                     );
$obTxtExercicioEmpenho->setNull     ( false                     );
$obTxtExercicioEmpenho->setMaxLength( 4                         );
$obTxtExercicioEmpenho->setSize     ( 5                         );
$obTxtExercicioEmpenho->obEvento->setOnClick("montaParametrosGET('limpaCampoEmpenho')");

/* Número Empenho */
$obBscEmpenho = new BuscaInner;
$obBscEmpenho->setTitle               ( "Informe o número do empenho.");
$obBscEmpenho->setRotulo              ( "**Número do Empenho"         );
$obBscEmpenho->setId                  ( "stEmpenho"                   );
$obBscEmpenho->setValue               ( $stEmpenho                    ); 
$obBscEmpenho->setMostrarDescricao    ( true                          );
$obBscEmpenho->obCampoCod->setName    ( "numEmpenho"                  );
$obBscEmpenho->obCampoCod->setValue   (  $numEmpenho                  );
$obBscEmpenho->obCampoCod->obEvento->setOnChange("montaParametrosGET('preencheInner','numEmpenho, inCodEntidade, stExercicioEmpenho');");
$obBscEmpenho->setFuncaoBusca         ("abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLEmpenho.php','frm','numEmpenho','stEmpenho','empenhoNotaFiscal&inCodEntidade='+document.frm.inCodEntidade.value + '&dtFinal='+document.frm.dtFinalVigencia.value + '&dtEmissao='+document.frm.dtInicioExecucao.value+'&stCampoExercicio=stExercicioEmpenho&stExercicioEmpenho='+document.frm.stExercicioEmpenho.value,'".Sessao::getId()."','800','550');");

//BOTÕES EMPENHO
/* Botão Incluir */
$obBtnIncluir = new Button;
$obBtnIncluir->setValue             ( "Incluir"         );
$obBtnIncluir->setName              ( "btnIncluirEmp"   );
$obBtnIncluir->setId                ( "btnIncluirEmp"   );
$obBtnIncluir->setDisabled          ( false             );
$obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET('incluirEmpenhoLista','numEmpenho, stExercicioEmpenho, dtInicioExecucao, inCodEntidade');" );

/* Botão Limpar */
$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimparEmp"	);
$obBtnLimpar->setId                ( "limparEmp" 	);
$obBtnLimpar->setValue             ( "Limpar" 		);
$obBtnLimpar->setDisabled          ( false     		);
$obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limpar');" );

/* Lista Empenho(s) */
$spnLista = new Span;
$spnLista->setId( 'spnLista' );

/* Aditivo */
$inCodAditivo = (isset($inCodAditivo)) ? $inCodAditivo : 1;
$obTxtCodAditivo = new TextBox;
$obTxtCodAditivo->setId       ( "inCodAditivo"       );
$obTxtCodAditivo->setName     ( "inCodAditivo"       );
$obTxtCodAditivo->setValue    ( $inCodAditivo        );
$obTxtCodAditivo->setRotulo   ( "*Número do Aditivo" );
$obTxtCodAditivo->setTitle    ( "Informe o número do aditivo do convênio." );
$obTxtCodAditivo->setInteiro  ( true                 );
$obTxtCodAditivo->setNull     ( true                 );
$obTxtCodAditivo->setMaxLength( 2                    );
$obTxtCodAditivo->setSize     ( 3                    );

$obTxtDescAditivo = new TextArea;
$obTxtDescAditivo->setName          ( "stDescAditivo"   );
$obTxtDescAditivo->setId            ( "stDescAditivo"   );
$obTxtDescAditivo->setRotulo        ( "*Descrição da Alteração do Aditivo" );
$obTxtDescAditivo->setNull          ( true              );
$obTxtDescAditivo->setRows          ( 5                 );
$obTxtDescAditivo->setCols          ( 100               );
$obTxtDescAditivo->setMaxCaracteres ( 500               );

$obDtAssinaturaAditivo = new Data;
$obDtAssinaturaAditivo->setId       ( "dtAssinaturaAditivo"                     );
$obDtAssinaturaAditivo->setName     ( "dtAssinaturaAditivo"                     );
$obDtAssinaturaAditivo->setRotulo   ( "*Data da Assinatura"                     );
$obDtAssinaturaAditivo->setTitle    ( 'Informe a Data da Assinatura do Aditivo.');
$obDtAssinaturaAditivo->setNull     ( true                                      );

$obDtFinalAditivo = new Data;
$obDtFinalAditivo->setName     ( 'dtFinalAditivo'                   );
$obDtFinalAditivo->setId       ( 'dtFinalAditivo'                   );
$obDtFinalAditivo->setRotulo   ( 'Nova Data do Final da Vigência'   );
$obDtFinalAditivo->setValue    ( ''     );
$obDtFinalAditivo->setNull     ( true   );
$obDtFinalAditivo->setTitle    ( 'Informe a Nova Data do Final da Vigência do Convênio.' );

$obValorAditivo = new Numerico;
$obValorAditivo->setName        ( 'nuValorAditivo'                               );
$obValorAditivo->setId          ( 'nuValorAditivo'                               );
$obValorAditivo->setRotulo      ( 'Novo Valor do Convênio'                       );
$obValorAditivo->setTitle       ( 'Informe o Novo Valor Atualizado do Convênio.' );
$obValorAditivo->setNull        ( true  );
$obValorAditivo->setMaxLength   ( 14    );
$obValorAditivo->setSize        ( 18    );

$obValorContraAditivo = new Numerico;
$obValorContraAditivo->setMaxLength ( 14    );
$obValorContraAditivo->setSize      ( 18    );
$obValorContraAditivo->setNull      ( true  );
$obValorContraAditivo->setName      ( 'nuValorContraAditivo'         );
$obValorContraAditivo->setId        ( 'nuValorContraAditivo'         );
$obValorContraAditivo->setRotulo    ( 'Novo Valor de Contra-Partida' );
$obValorContraAditivo->setTitle     ( 'Informe o Novo Valor Atualizado de Contra-Partida do Convênio' );

$obBtnIncluirAditivo = new Button;
$obBtnIncluirAditivo->setValue              ( "Incluir"          );
$obBtnIncluirAditivo->setName               ( "btnIncluirAditivo");
$obBtnIncluirAditivo->setId                 ( "btnIncluirAditivo");
$obBtnIncluirAditivo->obEvento->setOnClick  ( "montaParametrosGET('incluirAditivoLista');" );

$obBtnLimparAditivo = new Button;
$obBtnLimparAditivo->setName                ( "btnLimparAditivo");
$obBtnLimparAditivo->setId                  ( "limparAditivo"   );
$obBtnLimparAditivo->setValue               ( "Limpar"          );
$obBtnLimparAditivo->obEvento->setOnClick   ( "montaParametrosGET('limparAditivo');" );

$SpnListaAditivo= new Span;
$SpnListaAditivo->SetId('spnListaAditivos');
/* Fim Aditivo */

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->setAjuda     ( "UC-03.05.14" );
$obFormulario->addHidden    ( $obHdnStExercicio         );
if ($stAcao == 'alterar') { $obFormulario->addHidden    ( $obHdnNumConvenio         ); }
$obFormulario->addHidden    ( $obHdnCodEntidade         );
$obFormulario->addHidden    ( $obHdnCodConvenio         );
$obFormulario->addHidden    ( $obHdnAcao                );
$obFormulario->addHidden    ( $obHdnCtrl                );
$obFormulario->addTitulo    ( "Dados do Convênio"       );
$obFormulario->addComponente( $obLblExercicio           );
$obFormulario->addComponente( $obIntNumConvenio         );
$obFormulario->addComponente( $obEntidadeUsuario        );
$obFormulario->addComponente( $obObjeto                 );
$obFormulario->addComponente( $obDtAssinatura           );
$obFormulario->addComponente( $obDtInicioExecucao       );
$obFormulario->addComponente( $obDtFinalVigencia        );
$obFormulario->addComponente( $obValorConvenio          );
$obFormulario->addComponente( $obValorContra            );
$obFormulario->addTitulo        ( "Dados dos Participantes do Convênio" );
$obFormulario->addComponente    ( $obCgmParticipante                                                );
$obFormulario->addComponente    ( $obCmbTiposParticipante                                           );
$obFormulario->addComponente    ( $obValorParticipacao                                              );
$obFormulario->addComponente    ( $obCmbEsfera                                                      );
$obFormulario->addHidden        ( $obHdnPercentualParticipacao                                      );
$obFormulario->addComponente    ( $obPercentualParticipacao                                         );
$obFormulario->agrupaComponentes( array( $obBtnIncluirParticipante, $obBtnLimparParticipante ),"","");
$obFormulario->addSpan          ( $obSpanParticipantes                                              );
$obFormulario->addTitulo        ( "Dados dos Empenhos do Convênio"                                  );
$obFormulario->addComponente    ( $obTxtExercicioEmpenho                                            );
$obFormulario->addComponente    ( $obBscEmpenho                                                     );
$obFormulario->agrupaComponentes( array( $obBtnIncluir, $obBtnLimpar ),"",""                        );
$obFormulario->addSpan          ( $spnLista                                                         );

$obFormulario->addTitulo        ( "Dados dos aditivos do Convênio." );
$obFormulario->addComponente    ( $obTxtCodAditivo                  );
$obFormulario->addComponente    ( $obTxtDescAditivo                 );
$obFormulario->addComponente    ( $obDtAssinaturaAditivo            );
$obFormulario->addComponente    ( $obDtFinalAditivo                 );
$obFormulario->addComponente    ( $obValorAditivo                   );
$obFormulario->addComponente    ( $obValorContraAditivo             );
$obFormulario->agrupaComponentes( array( $obBtnIncluirAditivo,  $obBtnLimparAditivo ),"","" );
$obFormulario->addSpan          ( $SpnListaAditivo                  );

if ($stAcao == 'alterar') {
    $stFiltro  = "&pg=".Sessao::read('pg');
    $stFiltro .= "&pos=".Sessao::read('pos');
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
    
    $obFormulario->Cancelar($stLocation);
}else{
    $obFormulario->Ok();
}
$obFormulario->show();

if ($stAcao == 'alterar') {
    echo "<script type=\"text/javascript\">             \r\n";
    echo "    ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumConvenio=".$_REQUEST['inNumConvenio']."&inCodConvenio=".$_REQUEST['inCodConvenio']."&stExercicio=".$_REQUEST['inExercicio']."', 'montaListas');     \r\n";
    echo "</script>                                                             \r\n";
}
?>