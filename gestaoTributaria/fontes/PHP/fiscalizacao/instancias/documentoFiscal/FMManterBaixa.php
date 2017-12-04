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
    * Página de Formulario de Inclusao de Baixa de Notas Fiscais

    * Data de Criação   : 31/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: FMManterBaixa.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.04
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php"                              );
include_once( CAM_GT_FIS_MAPEAMENTO."TFISTipoInutilizacao.class.php"                                     );

$stAcao = $request->get('stAcao');
Sessao::write( 'arValores', array() );

if ( empty( $stAcao ) ) { $stAcao = "incluir"; }

//Define o nome dos arquivos PHP
$stPrograma = "ManterBaixa";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once( $pgJs );

$arInscricaoEconomica = explode("-", $_REQUEST["inscricao_economica"]);
$inCodInscricao = trim($arInscricaoEconomica[0]);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

$obHdnNotaInicial =  new Hidden;
$obHdnNotaInicial->setName ( "inNotaInicial"           );
$obHdnNotaInicial->setValue( $_REQUEST['nota_inicial'] );

$obHdnNotaFinal =  new Hidden;
$obHdnNotaFinal->setName ( "inNotaFinal"           );
$obHdnNotaFinal->setValue( $_REQUEST['nota_final'] );

$obHdnTipoInutilizacao =  new Hidden;
$obHdnTipoInutilizacao->setName( "stTipoInutilizacao" );

$obHdnInscricaoEconomica =  new Hidden;
$obHdnInscricaoEconomica->setName ( "inInscricaoEconomica" );
$obHdnInscricaoEconomica->setValue( $inCodInscricao );

$obHdnAutorizacao =  new Hidden;
$obHdnAutorizacao->setName ( "cod_autorizacao"            );
$obHdnAutorizacao->setValue( $_REQUEST['cod_autorizacao'] );

$obLblAutorizacao = new Label;
$obLblAutorizacao->setName  ( "stAutorizacao"              );
$obLblAutorizacao->setId    ( "stAutorizacao"              );
$obLblAutorizacao->setRotulo( "Autorização"                );
$obLblAutorizacao->setValue ( $_REQUEST["cod_autorizacao"] );

$obLblInscricaoEconomica = new Label;
$obLblInscricaoEconomica->setName  ( "stIncricao"                     );
$obLblInscricaoEconomica->setId    ( "stIncricao"                     );
$obLblInscricaoEconomica->setRotulo( "Inscrição Econômica"            );
$obLblInscricaoEconomica->setValue ( $_REQUEST["inscricao_economica"] );

$obLblSerie = new Label;
$obLblSerie->setName  ( "stSerie"          );
$obLblSerie->setId    ( "stSerie"          );
$obLblSerie->setRotulo( "Série"            );
$obLblSerie->setValue ( $_REQUEST["serie"] );

$obHdnSerie =  new Hidden;
$obHdnSerie->setName ( "stSerie"           );
$obHdnSerie->setValue( $_REQUEST['serie'] );

$obTxtObservacoes = new TextArea;
$obTxtObservacoes->setName  ( "stObservacoes" );
$obTxtObservacoes->setId    ( "stObservacoes" );
$obTxtObservacoes->setTitle ( "Observações"   );
$obTxtObservacoes->setRotulo( "Observação"    );

//DOCUMENTOS
$obITextBoxSelectDocumento = new ITextBoxSelectDocumento;
$obITextBoxSelectDocumento->setCodAcao( Sessao::read('acao') );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setRotulo( "Documento" );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setNULL  ( false       );

$obTxtCodInutilizacao = new TextBox;
$obTxtCodInutilizacao->setRotulo   ( '*Tipo de Inutilização'            );
$obTxtCodInutilizacao->setTitle    ( 'Selecione o tipo de inutilização' );
$obTxtCodInutilizacao->setName     ( 'inCodInutilizacao'                );
$obTxtCodInutilizacao->setInteiro  ( true                               );
$obTxtCodInutilizacao->setSize     ( 10                                 );
$obTxtCodInutilizacao->setMaxLength( 10                                 );

$rsInutilizacao      = new RecordSet();
$obTTipoInutilizacao = new TFISTipoInutilizacao();
$obTTipoInutilizacao->recuperaTodos( $rsInutilizacao );
$obCmbInutilizacao = new Select;
$obCmbInutilizacao->setName       ( "cmbCodInutilizacao" );
$obCmbInutilizacao->addOption     ( "", "Selecione"      );
$obCmbInutilizacao->setCampoId    ( "cod_tipo"           );
$obCmbInutilizacao->setCampoDesc  ( "descricao"          );
$obCmbInutilizacao->preencheCombo ( $rsInutilizacao      );
$obCmbInutilizacao->setStyle      ( "width: 220px"       );

$obTxtNotaFiscal = new TextBox;
$obTxtNotaFiscal->setRotulo   ( '*Nota Fiscal '                          );
$obTxtNotaFiscal->setTitle    ( 'Informe o número da nota fiscal inútil' );
$obTxtNotaFiscal->setName     ( 'inCodNotaFiscal'                        );
$obTxtNotaFiscal->setInteiro  ( true                                     );
$obTxtNotaFiscal->setSize     ( 10                                       );
$obTxtNotaFiscal->setMaxLength( 10                                       );

$obBtnIncluirInutilizacao = new Button;
$obBtnIncluirInutilizacao->setName             ( "btnIncluir"             );
$obBtnIncluirInutilizacao->setValue            ( "Incluir"                );
$obBtnIncluirInutilizacao->setTipo             ( "button"                 );
$obBtnIncluirInutilizacao->obEvento->setOnClick( "incluirInutilizacao();" );
$obBtnIncluirInutilizacao->setDisabled         ( false                    );

$obBtnLimparInutilizacao = new Button;
$obBtnLimparInutilizacao->setName             ( "btnLimpar" );
$obBtnLimparInutilizacao->setValue            ( "Limpar"    );
$obBtnLimparInutilizacao->setTipo             ( "button"    );
$obBtnLimparInutilizacao->obEvento->setOnClick( "Limpar();" );
$obBtnLimparInutilizacao->setDisabled         ( false       );

$botoesSpanInutilizacao = array( $obBtnIncluirInutilizacao,$obBtnLimparInutilizacao );

$obSpnListaInutilizacao = new Span;
$obSpnListaInutilizacao->setID( "spnListaInutilizacao" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->settarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                    ( $obForm                                   );
$obFormulario->addHidden                  ( $obHdnAcao                                );
$obFormulario->addHidden                  ( $obHdnCtrl                                );
$obFormulario->addHidden                  ( $obHdnNotaInicial                         );
$obFormulario->addHidden                  ( $obHdnNotaFinal                           );
$obFormulario->addHidden                  ( $obHdnTipoInutilizacao                    );
$obFormulario->addHidden                  ( $obHdnAutorizacao                         );
$obFormulario->addHidden                  ( $obHdnInscricaoEconomica                  );
$obFormulario->addHidden                  ( $obHdnSerie                               );
$obFormulario->addTitulo                  ( "Dados para Baixa"                        );
$obFormulario->addComponente              ( $obLblAutorizacao                         );
$obFormulario->addComponente              ( $obLblInscricaoEconomica                  );
$obFormulario->addComponente              ( $obLblSerie                               );
$obFormulario->addComponente              ( $obTxtObservacoes                         );
$obITextBoxSelectDocumento->geraFormulario( $obFormulario                             );
$obFormulario->addTitulo                  ( "Dados para Nota"                         );
$obFormulario->addComponenteComposto      ( $obTxtCodInutilizacao, $obCmbInutilizacao );
$obFormulario->addComponente              ( $obTxtNotaFiscal                          );
$obFormulario->defineBarra                ( $botoesSpanInutilizacao,'left',''         );
$obFormulario->addSpan                    ( $obSpnListaInutilizacao                   );

$obFormulario->Cancelar();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
