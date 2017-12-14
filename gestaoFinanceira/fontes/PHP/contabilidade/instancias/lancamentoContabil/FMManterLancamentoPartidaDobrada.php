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
    * Página de Formulario de Inclusao/Alteracao de Lancamento Partida Dobrada
    * Data de Criação   : 19/10/2006

    * @author Analista      : Gelson Gonçalves
    * @author Desenvolvedor : Rodrigo Soares

    * @ignore

    * $Id: FMManterLancamentoPartidaDobrada.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.33
*/

/* Este arquivo já havia sido implementado, apenas fiz algumas adequeções para nao ser utilizado
   tanto o JS.

                                                                        Alexandre Melo           */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php"                         );
include_once ( CAM_GF_CONT_COMPONENTES.'IPopUpContaAnalitica.class.php'                                 );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php"                            );

//Define o nome dos arquivos PHP
$stPrograma = "ManterLancamentoPartidaDobrada";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($rsUltimoMesEncerrado->getCampo('mes') >= $mesAtual AND $boUtilizarEncerramentoMes == 'true' AND $_REQUEST['stAcao'] == 'incluir') {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois o mês atual está encerrado!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    $obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

    $obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo();
    $obRContabilidadeLancamentoValor->getMesProcessamento( $inMesProcessamento );

    $obForm = new Form;
    $obForm->setAction( $pgProc  );
    $obForm->setTarget( "oculto" );

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ( "stAcao" );
    $obHdnAcao->setValue( $_REQUEST['stAcao'] );

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName ( "stCtrl" );
    $obHdnCtrl->setValue( ""       );

    $obHdnSequencia = new Hidden;
    $obHdnSequencia->setName ( "sequencia" );
    $obHdnSequencia->setValue( $sequencia );

    $obHdnDescDebito = new Hidden;
    $obHdnDescDebito->setName ( "stDescDebito" );
    $obHdnDescDebito->setValue( $stDescDebito );

    $obHdnDescCredito = new Hidden;
    $obHdnDescCredito->setName ( "stDescCredito" );
    $obHdnDescCredito->setValue( $stDescCredito );

    $obHdnInMesProcessamento = new Hidden;
    $obHdnInMesProcessamento->setName  ( "inMesProcessamento"            );
    $obHdnInMesProcessamento->setId    ( "inMesProcessamento"            );
    $obHdnInMesProcessamento->setValue ( $inMesProcessamento             );

    $obHdnBoComplementoDebito = new Hidden;
    $obHdnBoComplementoDebito->setName  ( "boComplementoDebito" );
    $obHdnBoComplementoDebito->setId    ( "boComplementoDebito" );
    $obHdnBoComplementoDebito->setValue ( "true"                );

    $obHdnBoComplementoCredito = new Hidden;
    $obHdnBoComplementoCredito->setName  ( "boComplementoCredito" );
    $obHdnBoComplementoCredito->setId    ( "boComplementoCredito" );
    $obHdnBoComplementoCredito->setValue ( "false"                );

    $obHdnHistoricoCredito = new Hidden;
    $obHdnHistoricoCredito->setName ( "stNomHistoricoCredito" );
    $obHdnHistoricoCredito->setId   ( "stNomHistoricoCredito" );

    $obHdnDebitos = new Hidden;
    $obHdnDebitos->setName ( "stDebitos" );
    $obHdnDebitos->setId   ( "stDebitos" );
    $obHdnDebitos->setValue( $_REQUEST['stDebitos'] );

    $obHdnCreditos = new Hidden;
    $obHdnCreditos->setName  ( "stCreditos" );
    $obHdnCreditos->setId    ( "stCreditos" );
    $obHdnCreditos->setValue ( $_REQUEST['stCreditos'] );

    $obHdnExercicio = new Hidden;
    $obHdnExercicio->setName  ( "exercicio"            );
    $obHdnExercicio->setId    ( "exercicio"            );
    $obHdnExercicio->setValue ( $_REQUEST['exercicio'] );

    $obHdnCodEntidade = new Hidden;
    $obHdnCodEntidade->setName  ( "cod_entidade"            );
    $obHdnCodEntidade->setId    ( "cod_entidade"            );
    $obHdnCodEntidade->setValue ( $_REQUEST['cod_entidade'] );

    $obHdnTipo = new Hidden;
    $obHdnTipo->setName ( "tipo"            );
    $obHdnTipo->setId   ( "tipo"            );
    $obHdnTipo->setValue( $_REQUEST['tipo'] );

    $obHdnCodLote = new Hidden;
    $obHdnCodLote->setName  ( "cod_lote"            );
    $obHdnCodLote->setId    ( "cod_lote"            );
    $obHdnCodLote->setValue ( $_REQUEST['cod_lote'] );

    $obTxtCodEntidade = new TextBox;
    $obTxtCodEntidade->setName   ( "inCodEntidade"            );
    $obTxtCodEntidade->setId     ( "inCodEntidade"            );
    $obTxtCodEntidade->setValue  ( $_REQUEST['cod_entidade']  );
    $obTxtCodEntidade->setRotulo ( "Entidade"                 );
    $obTxtCodEntidade->setTitle  ( "Selecione a Entidade"     );
    $obTxtCodEntidade->setInteiro( true                       );
    $obTxtCodEntidade->setNull   ( false                      );
    $obTxtCodEntidade->obEvento->setOnChange( "montaParametrosGET('buscaProxLote', 'inCodEntidade');" );
    if ($_REQUEST['stAcao'] == "alterar") {
        $obTxtCodEntidade->setDisabled( true );
    }

    $obCmbNomEntidade = new Select;
    $obCmbNomEntidade->setName        ( "stNomEntidade"             );
    $obCmbNomEntidade->setId          ( "stNomEntidade"             );
    $obCmbNomEntidade->setValue       ( $_REQUEST['cod_entidade']   );
    $obCmbNomEntidade->setCampoId     ( "cod_entidade"              );
    $obCmbNomEntidade->setCampoDesc   ( "nom_cgm"                   );
    $obCmbNomEntidade->setNull        ( false                       );
    $obCmbNomEntidade->setStyle       ( "width: 500px;"             );
    $obCmbNomEntidade->addOption      ( ""            ,"Selecione"  );
    $obCmbNomEntidade->obEvento->setOnChange( "montaParametrosGET('buscaProxLote', 'inCodEntidade');" );
    $obCmbNomEntidade->preencheCombo  ( $rsEntidade                 );
    if ($_REQUEST['stAcao'] == "alterar") {
        $obCmbNomEntidade->setDisabled( true );
    }

    $obTxtCodLoteLancamento = new TextBox;
    $obTxtCodLoteLancamento->setName            ( "inCodLote"             );
    $obTxtCodLoteLancamento->setId              ( "inCodLote"             );
    $obTxtCodLoteLancamento->setValue           ( $_REQUEST['cod_lote']   );
    $obTxtCodLoteLancamento->setRotulo          ( "Número do Lote"        );
    $obTxtCodLoteLancamento->setTitle           ( "Informe o Nro do Lote" );
    $obTxtCodLoteLancamento->setInteiro         ( true                    );
    $obTxtCodLoteLancamento->setNull            ( false                   );
    $obTxtCodLoteLancamento->obEvento->setOnBlur("montaParametrosGET('validaLote', 'inCodLote, cod_lote, inCodEntidade' );");
    if ($_REQUEST['stAcao'] == "alterar") {
        $obTxtCodLoteLancamento->setDisabled( true );
    }

    $obTxtNomLoteLancamento = new TextBox;
    $obTxtNomLoteLancamento->setName     ( "stNomLote"              );
    $obTxtNomLoteLancamento->setId       ( "stNomLote"              );
    $obTxtNomLoteLancamento->setValue    ( $_REQUEST['stNomLote']   );
    $obTxtNomLoteLancamento->setRotulo   ( "Nome do Lote"           );
    $obTxtNomLoteLancamento->setTitle    ( "Informe o Nome do Lote" );
    $obTxtNomLoteLancamento->setNull     ( false                    );
    $obTxtNomLoteLancamento->setSize     ( 80                       );
    $obTxtNomLoteLancamento->setMaxLength( 80                       );
    if ($_REQUEST['stAcao'] == "alterar") {
        $obTxtNomLoteLancamento->setDisabled( true );
    }

    $obDtLote  = new Data();
    $obDtLote->setName   ( "stDtLote"             );
    $obDtLote->setId     ( "stDtLote"             );
    $obDtLote->setValue  ( $_REQUEST['dtLote']    );
    $obDtLote->setRotulo ( "Data"                 );
    $obDtLote->setTitle  ( "Informe a Data"       );
    $obDtLote->setNull   ( false                  );
    $obDtLote->obEvento->setOnChange( "montaParametrosGET('validaMes', 'inMesProcessamento, stDtLote');" );
    if ($_REQUEST['stAcao'] == "alterar") {
        $obDtLote->setDisabled( true );
    }

    $obPopUpContaDebito = new BuscaInner;
    $obPopUpContaDebito->setRotulo ( "Conta a Débito" );
    $obPopUpContaDebito->setTitle ( "Informe a Conta de Débito" );
    $obPopUpContaDebito->setId ( "stContaDebito" );
    $obPopUpContaDebito->setValue ( $stContaDebito );
    $obPopUpContaDebito->obCampoCod->setName ( "inCodContaDebito" );
    $obPopUpContaDebito->obCampoCod->setSize ( 10 );
    $obPopUpContaDebito->obCampoCod->setMaxLength( 5 );
    $obPopUpContaDebito->obCampoCod->setValue ( $inCodContaDebito );
    $obPopUpContaDebito->obCampoCod->setAlign ("left");
    $obPopUpContaDebito->obCampoCod->obEvento->setOnBlur("montaParametrosGET('buscaContaDebito', 'inCodContaDebito');");
    $obPopUpContaDebito->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaDebito','stContaDebito','','".Sessao::getId()."','800','550');");

    $obValorDebito = new Moeda;
    $obValorDebito->setValue   ( "0,00"       );
    $obValorDebito->setNull    ( true         );
    $obValorDebito->setId      ( "inVlDebito" );
    $obValorDebito->setSize    ( 20           );
    $obValorDebito->setRotulo  ( "*Valor"     );
    $obValorDebito->setName    ( "nuVlDebito" );
    $obValorDebito->setNegativo( "false"      );

    $obBscHistoricoDebito = new BuscaInner;
    $obBscHistoricoDebito->setRotulo                        ( "*Histórico"                    );
    $obBscHistoricoDebito->setTitle                         ( "Informe o histórico contábil." );
    $obBscHistoricoDebito->setNull                          ( true                            );
    $obBscHistoricoDebito->setId                            ( "stNomHistoricoDebito"          );
    $obBscHistoricoDebito->setValue                         ($stNomHistoricoDebito);
    $obBscHistoricoDebito->obCampoCod->setName              ( "inCodHistoricoDebito"          );

    $obBscHistoricoDebito->obCampoCod->setSize              ( 10                              );
    $obBscHistoricoDebito->obCampoCod->setMaxLength         ( 5                               );
    $obBscHistoricoDebito->obCampoCod->setValue             ( $inCodHistoricoDebito           );
    $obBscHistoricoDebito->obCampoCod->setAlign             ("left"                           );
    $obBscHistoricoDebito->obCampoCod->obEvento->setOnChange("montaParametrosGET('buscaHistoricoDebito', 'inCodHistoricoDebito');");
    $obBscHistoricoDebito->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."historicoPadrao/FLHistoricoPadrao.php','frm','inCodHistoricoDebito','stNomHistoricoDebito','','".Sessao::getId()."','800','550');");

    $obTxtComplementoDebito = new TextArea;
    $obTxtComplementoDebito->setName   ( "stComplementoDebito"   );
    $obTxtComplementoDebito->setId     ( "stComplementoDebito"   );
    $obTxtComplementoDebito->setValue  ( $stComplemento          );
    $obTxtComplementoDebito->setRotulo ( "Complemento"         	 );
    $obTxtComplementoDebito->setTitle  ( "Informe o Complemento" );
    $obTxtComplementoDebito->setNull   ( true                    );
    $obTxtComplementoDebito->setRows   ( 3                       );
    $obTxtComplementoDebito->setMaxCaracteres ( 200                 );

     $obBtnIncluirDebito = new Button;
     $obBtnIncluirDebito->setName              ( "btnIncluirDebito");
     $obBtnIncluirDebito->setId                ( "incluir" );
     $obBtnIncluirDebito->setValue             ( "Incluir" );
     $obBtnIncluirDebito->obEvento->setOnClick ( "montaParametrosGET('incluirListaDebito', 'inCodContaDebito, nuVlDebito, inCodHistoricoDebito, stComplementoDebito, stContaDebito, stDescDebito, sequencia' );" );

     $obBtnLimparDebito = new Button;
     $obBtnLimparDebito->setName             ( "btnLimparDebito"                     );
     $obBtnLimparDebito->setValue            ( "Limpar"                              );
     $obBtnLimparDebito->obEvento->setOnClick( "montaParametrosGET('limparDebito');" );

     $obSpnListaDebito = new Span;
     $obSpnListaDebito->setID("spnListaDebito");

     $obPopUpContaCredito = new BuscaInner;
     $obPopUpContaCredito->setRotulo ( "Conta a Crédito" );
     $obPopUpContaCredito->setTitle ( "Informe a Conta de Crédito" );
     $obPopUpContaCredito->setId ( "stContaCredito" );
     $obPopUpContaCredito->setValue ( $stContaCredito );
     $obPopUpContaCredito->obCampoCod->setName ( "inCodContaCredito" );
     $obPopUpContaCredito->obCampoCod->setSize ( 10 );
     $obPopUpContaCredito->obCampoCod->setMaxLength( 5 );
     $obPopUpContaCredito->obCampoCod->setValue ( $inCodContaCredito );
     $obPopUpContaCredito->obCampoCod->setAlign ("left");
     $obPopUpContaCredito->obCampoCod->obEvento->setOnBlur("montaParametrosGET('buscaContaCredito', 'inCodContaCredito');");
     $obPopUpContaCredito->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaCredito','stContaCredito','','".Sessao::getId()."','800','550');");

     $obValorCredito = new Moeda;
     $obValorCredito->setValue   ( "0,00"        );
     $obValorCredito->setNull    ( true          );
     $obValorCredito->setId      ( "inVlCredito" );
     $obValorCredito->setSize    ( 20            );
     $obValorCredito->setRotulo  ( "*Valor"      );
     $obValorCredito->setName    ( "nuVlCredito" );
     $obValorCredito->setNegativo( "false"       );

     $obBscHistoricoCredito = new BuscaInner;
     $obBscHistoricoCredito->setRotulo                        ( "*Histórico"                    );
     $obBscHistoricoCredito->setTitle                         ( "Informe o histórico contábil." );
     $obBscHistoricoCredito->setName                          ( "stNomHistoricoCredito"         );
     $obBscHistoricoCredito->setId                            ( "stNomHistoricoCredito"         );
     $obBscHistoricoCredito->setValue                         ( $stNomHistoricoCredito          );
     $obBscHistoricoCredito->obCampoCod->setName              ( "inCodHistoricoCredito"         );
     $obBscHistoricoCredito->obCampoCod->setSize              ( 10                              );
     $obBscHistoricoCredito->obCampoCod->setMaxLength         ( 5                               );
     $obBscHistoricoCredito->obCampoCod->setValue             ( $inCodHistoricoCredito          );
     $obBscHistoricoCredito->obCampoCod->setAlign             ("left"                           );
     $obBscHistoricoCredito->obCampoCod->obEvento->setOnChange("montaParametrosGET('buscaHistoricoCredito', 'inCodHistoricoCredito');");
     $obBscHistoricoCredito->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."historicoPadrao/FLHistoricoPadrao.php','frm','inCodHistoricoCredito','stNomHistoricoCredito','','".Sessao::getId()."','800','550');");

    $obTxtComplementoCredito = new TextArea;
    $obTxtComplementoCredito->setName   ( "stComplementoCredito" );
    $obTxtComplementoCredito->setId     ( "stComplementoCredito" );
    $obTxtComplementoCredito->setValue  ( $stComplemento         );
    $obTxtComplementoCredito->setRotulo ( "Complemento"          );
    $obTxtComplementoCredito->setTitle  ( ""                     );
    $obTxtComplementoCredito->setNull   ( true                   );
    $obTxtComplementoCredito->setRows   ( 3                      );
    $obTxtComplementoCredito->setMaxCaracteres ( 200                 );

    $obBtnIncluirCredito = new Button;
    $obBtnIncluirCredito->setName             ( "btnIncluirCredito");
    $obBtnIncluirCredito->setValue            ( "Incluir"          );
    $obBtnIncluirCredito->obEvento->setOnClick( "montaParametrosGET('incluirListaCredito','stComplementoCredito, inCodContaCredito, nuVlCredito, inCodHistoricoCredito, stContaCredito, stDescCredito, sequencia' );" );

    $obBtnLimparCredito = new Button;
    $obBtnLimparCredito->setName             ( "btnLimparCredito" );
    $obBtnLimparCredito->setValue            ( "Limpar"           );
    $obBtnLimparCredito->obEvento->setOnClick( "montaParametrosGET('limparCredito');" );

    $obSpnListaCredito = new Span;
    $obSpnListaCredito->setID("spnListaCredito");

    $obLblTotalDebito = new Label;
    $obLblTotalDebito->setRotulo ( "Total Débito"  );
    $obLblTotalDebito->setValue  ( $nuTotalDebito  );
    $obLblTotalDebito->setId     ( "nuTotalDebito" );

    $obLblTotalCredito = new Label;
    $obLblTotalCredito->setRotulo ( "Total Crédito"  );
    $obLblTotalCredito->setValue  ( $nuTotalCredito  );
    $obLblTotalCredito->setId     ( "nuTotalCredito" );

    $obLblDiferenca = new Label;
    $obLblDiferenca->setRotulo ( "Diferença"   );
    $obLblDiferenca->setValue  ( $nuDiferenca  );
    $obLblDiferenca->setId     ( "nuDiferenca" );

    $obOk  = new Ok;
    $obOk->setId ("Ok");
    $obOk->obEvento->setOnClick( "verificaDebitoCreditoInclusao();");

    if ($_REQUEST['stAcao'] == 'incluir') {
        $obLimpar = new Button;
        $obLimpar->setValue( "Limpar" );
        $obLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparCampos');");
    } else {
        $stLocation = $pgList.'?'.Sessao::getId()."&stAcao=".$_REQUEST['stAcao'];
        $obLimpar = new Button;
        $obLimpar->setValue( "Voltar" );
        $obLimpar->obEvento->setOnClick("Cancelar('".$stLocation."');");
    }

    //*****************************************************//
    /* FORMULÁRIO                                          */
    //*****************************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );

    if ($_REQUEST['stAcao'] == 'incluir') {
        // Ações disparadas por eventos
        $onChange = $obTxtCodEntidade->obEvento->getOnChange();
        $obTxtCodEntidade->obEvento->setOnChange($onChange.'getIMontaAssinaturas();');
        $onChange = $obCmbNomEntidade->obEvento->getOnChange();
        $obCmbNomEntidade->obEvento->setOnChange($onChange.'getIMontaAssinaturas();');
    }

    /* Lançamentos Contábeis */
    $obFormulario->addTitulo    ( "Dados para Lançamento Contábeis" );
    $obFormulario->addHidden    ( $obHdnCtrl                        );
    $obFormulario->addHidden    ( $obHdnAcao                        );
    $obFormulario->addHidden    ( $obHdnBoComplementoDebito         );
    $obFormulario->addHidden    ( $obHdnBoComplementoCredito        );
    $obFormulario->addHidden    ( $obHdnInMesProcessamento          );
    $obFormulario->addHidden    ( $obHdnCodLote                     );
    $obFormulario->addHidden    ( $obHdnDescDebito                  );
    $obFormulario->addHidden    ( $obHdnDescCredito                 );
    $obFormulario->addHidden    ( $obHdnDebitos                     );
    $obFormulario->addHidden    ( $obHdnCreditos                    );
    $obFormulario->addHidden    ( $obHdnExercicio                   );
    $obFormulario->addHidden    ( $obHdnCodEntidade                 );
    $obFormulario->addHidden    ( $obHdnTipo                        );
    $obFormulario->addHidden    ( $obHdnSequencia                   );
    $obFormulario->addComponenteComposto( $obTxtCodEntidade, $obCmbNomEntidade );
    $obFormulario->addComponente( $obTxtCodLoteLancamento           );
    $obFormulario->addComponente( $obTxtNomLoteLancamento           );
    $obFormulario->addComponente( $obDtLote                         );

    /* Dados de Débitos */
    $obFormulario->addTitulo         ( "Dados de Débitos"                                       );
    $obFormulario->addComponente     ( $obPopUpContaDebito                                      );
    $obFormulario->addComponente     ( $obValorDebito                                           );
    $obFormulario->addComponente     ( $obBscHistoricoDebito                                    );
    $obFormulario->addComponente     ( $obTxtComplementoDebito                                  );
    $obFormulario->agrupaComponentes ( array( $obBtnIncluirDebito, $obBtnLimparDebito ), "", "" );
    $obFormulario->addSpan           ( $obSpnListaDebito                                        );

    /* Dados de Créditos                */
    $obFormulario->addTitulo         ( "Dados de Créditos"                                      );
    $obFormulario->addComponente     ( $obPopUpContaCredito                                     );
    $obFormulario->addComponente     ( $obValorCredito                                          );
    $obFormulario->addComponente     ( $obBscHistoricoCredito                                   );
    $obFormulario->addComponente     ( $obTxtComplementoCredito                                 );
    $obFormulario->agrupaComponentes ( array( $obBtnIncluirCredito, $obBtnLimparCredito )       );
    /* Dados da Listagem de Créditos      */
    $obFormulario->addSpan           ( $obSpnListaCredito                                       );

    $obChkEmitirNota = new Checkbox;
    $obChkEmitirNota->setName  ( "boEmitirNota" );
    $obChkEmitirNota->setRotulo( "Emitir nota"  );
    $obChkEmitirNota->setLabel ( "Emitir nota após efetuar o lançamento" );

    /* Dados do total de débitos/créditos e diferença*/
    $obFormulario->addTitulo         ( "Totais"                                );
    $obFormulario->addComponente     ( $obLblTotalDebito                       );
    $obFormulario->addComponente     ( $obLblTotalCredito                      );
    $obFormulario->addComponente     ( $obLblDiferenca                         );

    if ($_REQUEST['stAcao'] == 'incluir') {
        $obFormulario->addComponente     ( $obChkEmitirNota                        );

        include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
        $obMontaAssinaturas = new IMontaAssinaturas;

        // Injeção de código no formulário
        $obMontaAssinaturas->geraFormulario( $obFormulario );
    }

    $obFormulario->defineBarra( array($obOk, $obLimpar) );
    $obFormulario->show();

    $jsOnload = "montaParametrosGET('carregaDados','exercicio, cod_entidade, tipo, cod_lote, stAcao');";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
