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
    * Página de Formulario de Inclusao/Alteracao de Arrecadação de Receita
    * Data de Criação   : 20/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-08-15 11:15:55 -0300 (Qua, 15 Ago 2007) $

    * Casos de uso: uc-02.02.05
*/

/*
$Log$
Revision 1.16  2007/08/15 14:15:25  hboaventura
Bug#9914#

Revision 1.15  2007/06/20 13:23:22  vitor
Bug#9412#, Bug#9413#

Revision 1.14  2007/06/18 21:03:15  vitor
#9412# #9413#

Revision 1.13  2007/06/12 19:47:49  cako
Bug #9349#

Revision 1.12  2007/06/04 22:17:07  cako
Bug #9349#

Revision 1.11  2006/07/25 16:51:10  cako
Bug #6479#

Revision 1.10  2006/07/05 20:50:39  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceita.class.php" );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ArrecadarReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRContabilidadeLancamentoReceita = new RContabilidadeLancamentoReceita;
$obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

if ( $rsEntidade->getNumLinhas() == 1) {
    $inCodEntidade = $rsEntidade->getCampo( "cod_entidade" );

    $obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( 'A' );
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->listar( $rsLote, 'cod_lote DESC LIMIT 1' );

    $inCodigoLote = $rsLote->getCampo('cod_lote')+1;
}

if (!$_GET['inCodLote']) {
    $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
    $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( "A" );
    $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo();
    $inCodLote =  $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote();
} else {
    $inCodEntidade  = $_REQUEST['inCodEntidade'];
    $inCodLote      = $_REQUEST['inCodLote'];
    $stNomLote      = $_REQUEST['stNomLote'];
    $stDtLote       = $_REQUEST['stDtLote'];
}

$obRContabilidadeLancamentoReceita->getMesProcessamento( $inMesProcessamento );

//if ($stAcao == 'alterar') {
//
//    $inSequencia    = $_GET['inSequencia'];
//    $inCodLote      = $_GET['inCodLote'];
//    $inCodHistorico = $_GET['inCodHistorico'];
//    $inCodEntidade  = $_GET['inCodEntidade'];
//    $stTipoValor    = $_GET['stTipoValor'];
//    $stTipo         = $_GET['stTipo'];
//    $inCodReceita   = $_GET['inCodReceita'];
//
//    $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->setSequencia( $inSequencia );
//    $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $inCodLote );
//    $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( $inCodHistorico );
//    $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
//    $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
//    $obRContabilidadeLancamentoReceita->setTipoValor( $stTipoValor );
//    $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( $stTipo );
//    $obRContabilidadeLancamentoReceita->obROrcamentoReceita->setCodReceita( $inCodReceita );
//    $obRContabilidadeLancamentoReceita->consultar();
//
//    $nuValor = str_replace('-','',$obRContabilidadeLancamentoReceita->getValor());
//    $nuValor = number_format($nuValor,2,',','.');
//    $stComplemento = $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->getComplemento();
//    $stNomHistorico = $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getNomHistorico();
//    $stNomLote = $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote();
//    $stDtLote = $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote();
//    $stNomEntidade = $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getNomeEntidade();
//    $inCodContaCredito = $obRContabilidadeLancamentoReceita->getContaCredito();
//    $inCodContaDebito =  $obRContabilidadeLancamentoReceita->getContaDebito();
//    $stNomHistorico = $obRContabilidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getNomHistorico();
//    $stNomReceita = $obRContabilidadeLancamentoReceita->obROrcamentoReceita->obROrcamentoClassificacaoReceita->getDescricao();
//
//    if ($inCodContaDebito) {
//        $obRContabilidadeLancamentoReceita->obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodContaDebito );
//        $obRContabilidadeLancamentoReceita->obRContabilidadePlanoContaAnalitica->consultar();
//        $stContaDebito = $obRContabilidadeLancamentoReceita->obRContabilidadePlanoContaAnalitica->getNomConta();
//    }
//    if ($inCodContaCredito) {
//        $obRContabilidadeLancamentoReceita->obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodContaCredito );
//        $obRContabilidadeLancamentoReceita->obRContabilidadePlanoContaAnalitica->consultar();
//        $stContaCredito = $obRContabilidadeLancamentoReceita->obRContabilidadePlanoContaAnalitica->getNomConta();
//    }
//
//
//
//}

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

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define o objeto de Hidden para Complemento
$obHdnBoComplemento = new Hidden;
$obHdnBoComplemento->setName ( "boComplemento" );
$obHdnBoComplemento->setId( "boComplemento" );
$obHdnBoComplemento->setValue( "true" );

//if ($stAcao == 'alterar') {
//
//    //Define o objeto Hidden para Sequencia
//    $obHdnSequencia = new Hidden;
//    $obHdnSequencia->setName ( "inSequencia" );
//    $obHdnSequencia->setValue( $inSequencia );
//
//    //Define Objeto Hiddem para Codigo do Lote
//    $obHdnCodLote = new Hidden;
//    $obHdnCodLote->setName ( "inCodLote" );
//    $obHdnCodLote->setValue( $inCodLote );
//
//    // Define Objeto Hidden para Data do Lote
//    $obHdnDtLote = new Hidden;
//    $obHdnDtLote->setName( "stDtLote" );
//    $obHdnDtLote->setValue( $stDtLote );
//
//
//    //Define Objeto Hiddem para Codigo da Entidade
//    $obHdnCodEntidade = new Hidden;
//    $obHdnCodEntidade->setName ( "inCodEntidade" );
//    $obHdnCodEntidade->setValue( $inCodEntidade );
//
//    //Define Objeto Hiddem para Codigo da Receita
//    $obHdnCodReceita = new Hidden;
//    $obHdnCodReceita->setName ( "inCodReceita" );
//    $obHdnCodReceita->setValue( $inCodReceita );
//
//    //Define Objeto Hiddem para Codigo da Conta Credito
//    $obHdnCodContaDebito = new Hidden;
//    $obHdnCodContaDebito->setName ( "inCodContaDebito" );
//    $obHdnCodContaDebito->setValue( $inCodContaDebito  );
//
//    // Define Label para Codigo da Entidade
//    $obLblCodEntidade = new Label;
//    $obLblCodEntidade->setRotulo( "Selecione a Entidade" );
//    $obLblCodEntidade->setValue( "$inCodEntidade - $stNomEntidade" );
//
//    // Define Objeto Label para Codigo do Lote
//    $obLblCodLote = new Label;
//    $obLblCodLote->setRotulo( "Número do Lote" );
//    $obLblCodLote->setValue( $inCodLote );
//
//    // Define Objeto Label para Nome do Lote
//    $obLblNomLote = new Label;
//    $obLblNomLote->setRotulo( "Nome do Lote" );
//    $obLblNomLote->setValue( $stNomLote );
//
//    // Define Objeto Label para Data do Lote
//    $obLblNomEntidade = new Label;
//    $obLblNomEntidade->setRotulo( "Data" );
//    $obLblNomEntidade->setValue( $stDtLote );
//
//    // Define Objeto Label para Codigo da Receita
//    $obLblCodReceita = new Label;
//    $obLblCodReceita->setRotulo( "Receita" );
//    $obLblCodReceita->setValue( $inCodReceita . ' - ' . $stNomReceita );
//
//    // Define Objeto Label para Codigo Conta Debito
//    $obLblCodContaDebito = new Label;
//    $obLblCodContaDebito->setRotulo( "Contra Partida" );
//    $obLblCodContaDebito->setValue( $inCodContaDebito . ' - ' . $stContaDebito);

//} else {
    // Define Objeto TextBox para Codigo da Entidade
/*    $obTxtCodEntidade = new TextBox;
    $obTxtCodEntidade->setName   ( "inCodEntidade" );
    $obTxtCodEntidade->setId     ( "inCodEntidade" );
    $obTxtCodEntidade->setValue  ( $inCodEntidade  );
    $obTxtCodEntidade->setRotulo ( "Entidade"      );
    $obTxtCodEntidade->setTitle  ( "Selecione a Entidade" );
    $obTxtCodEntidade->setInteiro( true  );
    $obTxtCodEntidade->setNull   ( false );
    $obTxtCodEntidade->obEvento->setOnChange( "buscaDado('buscaLote');" );

    // Define Objeto Select para Nome da Enteidade
    $obCmbNomEntidade = new Select;
    $obCmbNomEntidade->setName      ( "stNomEntidade"  );
    $obCmbNomEntidade->setId        ( "stNomEntidade"  );
    $obCmbNomEntidade->setValue     ( $inCodEntidade   );
    $obCmbNomEntidade->setCampoId   ( "cod_entidade" );
    $obCmbNomEntidade->setCampoDesc ( "nom_cgm" );
    $obCmbNomEntidade->setNull   ( false );
    $obCmbNomEntidade->setStyle  ( "width: 500px;" );
    if ($rsEntidade->getNumLinhas() > 1) {
        $obCmbNomEntidade->addOption    ( ""            ,"Selecione" );
        $obCmbNomEntidade->obEvento->setOnChange( "buscaDado('buscaLote');" );
    } else $jsSL = "buscaDado('buscaLote');";
    $obCmbNomEntidade->preencheCombo( $rsEntidade    ); */

    include_once ( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php");
    $obISelectEntidade = new ITextBoxSelectEntidadeUsuario();
    $obISelectEntidade->obSelect->obEvento->setOnChange( "buscaDado('buscaLote');");
    $jsOnload = "if (document.frm.inCodEntidade.value) buscaDado('buscaLote'); ";

    // Define Objeto TextBox para Codigo do Lote
    $obTxtCodLote = new TextBox;
    $obTxtCodLote->setName   ( "inCodLote"    );
    $obTxtCodLote->setId     ( "inCodLote"    );
    $obTxtCodLote->setValue  ( $inCodigoLote  );
    $obTxtCodLote->setRotulo ( "Número do Lote" );
    $obTxtCodLote->setTitle  ( "" );
    $obTxtCodLote->setInteiro( true        );
    $obTxtCodLote->setNull   ( false );
    $obTxtCodLote->setReadOnly( true );
    $obTxtCodLote->obEvento->setOnBlur( "buscaDado('validaLote');" );

    // Define Objeto TextBox para Descricao do Lote
    $obTxtNomLote = new TextBox;
    $obTxtNomLote->setName     ( "stNomLote" );
    $obTxtNomLote->setId       ( "stNomLote" );
    $obTxtNomLote->setValue    ( $stNomLote  );
    $obTxtNomLote->setRotulo   ( "Nome do Lote" );
    $obTxtNomLote->setTitle    ( "Tipo Lote=A" );
    $obTxtNomLote->setNull     ( false );
    $obTxtNomLote->setSize     ( 80 );
    $obTxtNomLote->setMaxLength( 80 );

    // Define Objeto Data para Data do Lote
    $obDtLote = new Data;
    $obDtLote->setName   ( "stDtLote" );
    $obDtLote->setId     ( "stDtLote" );
    $obDtLote->setValue  ( $stDtLote  );
    $obDtLote->setRotulo ( "Data" );
    $obDtLote->setTitle  ( "" );
    $obDtLote->setNull   ( false );
    $obDtLote->obEvento->setOnChange( "validaMes( this, '".$inMesProcessamento."' );" );

    // Define Objeto BuscaInner para Receita
    include_once ( CAM_GF_ORC_COMPONENTES."IPopUpReceita.class.php" );
    $obBscReceita = new IPopUpReceita ( $obISelectEntidade );
    $obBscReceita->setId( "stNomReceita" );
    if($stAcao == 'incluir')
        $obBscReceita->setTipoBusca ( 'contArrec' );
    $obBscReceita->setUsaFiltro ( true );
    $obBscReceita->setNull ( false );

/*
    $obBscReceita = new BuscaInner;
    $obBscReceita->setRotulo ( "Receita" );
    $obBscReceita->setTitle  ( "Digite o Reduzido da Receita");
    $obBscReceita->setNulL ( false );
    $obBscReceita->setId ( "stNomReceita" );
    $obBscReceita->setValue ( $stNomReceita );
    $obBscReceita->obCampoCod->setName ( "inCodReceita" );
    $obBscReceita->obCampoCod->setSize ( 10 );
    $obBscReceita->obCampoCod->setMaxLength( 5 );
    $obBscReceita->obCampoCod->setValue ( $inCodReceita );
    $obBscReceita->obCampoCod->setAlign ("left");
    $obBscReceita->obCampoCod->obEvento->setOnBlur("buscaDado('buscaReceita');");
    $obBscReceita->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."receita/LSReceita.php','frm','inCodReceita','stNomReceita','','".Sessao::getId()."','800','550');");
    $obBscReceita->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."receita/FLReceita.php','frm','inCodReceita','stNomReceita','receitaArrec&inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");
*/
    // Define Objeto BuscaInner para Conta a Débito
    $obBscContaDebito = new BuscaInner;
    $obBscContaDebito->setRotulo ( "Contra Partida" );
    $obBscContaDebito->setTitle ( "" );
    $obBscContaDebito->setNulL ( false );
    $obBscContaDebito->setId ( "stContaDebito" );
    $obBscContaDebito->setValue ( $stContaDebito );
    $obBscContaDebito->obCampoCod->setName ( "inCodContaDebito" );
    $obBscContaDebito->obCampoCod->setSize ( 10 );
    $obBscContaDebito->obCampoCod->setMaxLength( 5 );
    $obBscContaDebito->obCampoCod->setValue ( $inCodContaDebito );
    $obBscContaDebito->obCampoCod->setAlign ("left");
    $obBscContaDebito->obCampoCod->obEvento->setOnBlur("buscaDado('buscaContaDebito');");
    $obBscContaDebito->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaDebito','stContaDebito','','".Sessao::getId()."','800','550');");

//}

// Define Objeto TextBox para Codigo do Historico Padrao
$obBscHistorico = new BuscaInner;
$obBscHistorico->setRotulo ( "Histórico" );
$obBscHistorico->setTitle ( "" );
$obBscHistorico->setNulL ( false );
$obBscHistorico->setId ( "stNomHistorico" );
$obBscHistorico->setValue( $stNomHistorico );
$obBscHistorico->obCampoCod->setName ( "inCodHistorico" );
$obBscHistorico->obCampoCod->setSize ( 10 );
$obBscHistorico->obCampoCod->setMaxLength( 5 );
$obBscHistorico->obCampoCod->setValue ( $inCodHistorico );
$obBscHistorico->obCampoCod->setAlign ("left");
$obBscHistorico->obCampoCod->obEvento->setOnBlur("buscaDado('buscaHistorico');");
$obBscHistorico->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."historicoPadrao/FLHistoricoPadrao.php','frm','inCodHistorico','stNomHistorico','','".Sessao::getId()."','800','550');");

// Define Objeto TextBox para Complemento
$obTxtComplemento = new TextArea;
$obTxtComplemento->setName   ( "stComplemento" );
$obTxtComplemento->setId     ( "stComplemento" );
$obTxtComplemento->setValue  ( $stComplemento  );
$obTxtComplemento->setRotulo ( "Complemento" );
$obTxtComplemento->setTitle  ( "" );
$obTxtComplemento->setNull   ( true );
$obTxtComplemento->setRows   ( 3 );

// Define Objeto Moeda para Valor
$obTxtValor = new moeda;
$obTxtValor->setName     ( "nuValor" );
$obTxtValor->setId       ( "nuValor" );
$obTxtValor->setValue    ( $nuValor  );
$obTxtValor->setRotulo   ( "Valor"   );
$obTxtValor->setTitle    ( "" );
$obTxtValor->setNull     ( false );
$obTxtValor->setMaxLength( 18 );
$obTxtValor->setSize     ( 15 );
$obTxtValor->obEvento->setOnChange( "validaValor( this );" );
//$obTxtValor->setNegativo ( true ); // estava sendo inserido na base de maneira errada.

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados para lançamento contábil" );
$obFormulario->setAjuda('UC-02.02.05');
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnBoComplemento );

//if ($stAcao == 'alterar') {
//    $obFormulario->addHidden( $obHdnSequencia );
//    $obFormulario->addHidden( $obHdnCodLote );
//    $obFormulario->addHidden( $obHdnDtLote );
//    $obFormulario->addHidden( $obHdnCodEntidade );
//    $obFormulario->addHidden( $obHdnCodReceita );
//    $obFormulario->addHidden( $obHdnCodContaDebito );
//    $obFormulario->addComponente( $obLblCodEntidade );
//    $obFormulario->addComponente( $obLblCodLote );
//    $obFormulario->addComponente( $obLblNomLote );
//    $obFormulario->addComponente( $obLblNomEntidade );
//    $obFormulario->addComponente( $obLblCodReceita );
//    $obFormulario->addComponente( $obLblCodContaDebito );
//} else {
 // $obFormulario->addComponenteComposto( $obTxtCodEntidade, $obCmbNomEntidade );
    $obFormulario->addComponente( $obISelectEntidade );
    $obFormulario->addComponente( $obTxtCodLote );
    $obFormulario->addComponente( $obTxtNomLote );
    $obFormulario->addComponente( $obDtLote );
    $obFormulario->addComponente( $obBscReceita );
    $obFormulario->addComponente( $obBscContaDebito );
//}

$obFormulario->addComponente( $obBscHistorico );
$obFormulario->addComponente( $obTxtComplemento );
$obFormulario->addComponente( $obTxtValor );

$obFormulario->OK();
$obFormulario->show();

if ($jsSL) SistemaLegado::executaFrameOculto($jsSL);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
