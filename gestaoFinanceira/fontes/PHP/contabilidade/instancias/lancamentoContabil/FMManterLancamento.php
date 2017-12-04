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
    * Página de Formulario de Inclusao/Alteracao de Lancamento
    * Data de Criação   : 15/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    * $Id: FMManterLancamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterLancamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

$stFiltro = "&pos=".Sessao::read('pos');
$stFiltro .= "&pg=".Sessao::read('pg');
$stFiltro .= "&paginando=".Sessao::read('paginando');
$filtro = Sessao::read('filtro');
if (isset($filtro)) {
    foreach ($filtro as $stCampo => $stValor) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                if (is_array($stValor2) ) {
                    $stFiltro .= "&".$stCampo2."=".implode( ',' , $stValor2 );
                } else {
                    $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
                }
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

if ( $rsEntidade->getNumLinhas() == 1) {
    $inCodEntidade = $rsEntidade->getCampo( "cod_entidade" );

    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( 'M' );
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->listar( $rsLote, 'cod_lote DESC LIMIT 1' );

    $inCodLote = $rsLote->getCampo('cod_lote')+1;
}

$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo();
$obRContabilidadeLancamentoValor->getMesProcessamento( $inMesProcessamento );
if (!$_GET['inCodLote']) {
//    $inCodLote =  $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote();
} else {
    $inCodEntidade  = $_REQUEST['inCodEntidade'];
    $inCodLote      = $_REQUEST['inCodLote'];
    $stNomLote      = $_REQUEST['stNomLote'];
    $stDtLote       = $_REQUEST['stDtLote'];
}

if ($stAcao == 'alterar') {

    $inSequencia    = $_GET['inSequencia'];
    $inCodLote      = $_GET['inCodLote'];
    $inCodHistorico = $_GET['inCodHistorico'];
    $inCodEntidade  = $_GET['inCodEntidade'];
    $stTipoValor    = $_GET['stTipoValor'];
    $stTipo         = $_GET['stTipo'];

    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->setSequencia( $inSequencia );
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $inCodLote );
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( $inCodHistorico );
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
    $obRContabilidadeLancamentoValor->setTipoValor( $stTipoValor );
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( $stTipo );
    $obRContabilidadeLancamentoValor->consultar();

    $nuValor = str_replace('-','',$obRContabilidadeLancamentoValor->getValor());
    $nuValor = number_format($nuValor,2,',','.');
    $stComplemento = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->getComplemento();
    $stNomHistorico = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getNomHistorico();
    $stNomLote = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote();
    $stDtLote = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote();
    $stNomEntidade = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getNomeEntidade();
    $inCodContaCredito = $obRContabilidadeLancamentoValor->getContaCredito();
    $inCodContaDebito =  $obRContabilidadeLancamentoValor->getContaDebito();
    $stNomHistorico = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getNomHistorico();

    $obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodContaDebito );
    $obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->consultar();
    $stContaDebito = $obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->getNomConta();
    if ($inCodContaCredito) {
        $obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodContaCredito );
        $obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->consultar();
        $stContaCredito = $obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->getNomConta();
    }

}

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

if ($stAcao == 'alterar') {

    //Define o objeto Hidden para Sequencia
    $obHdnSequencia = new Hidden;
    $obHdnSequencia->setName ( "inSequencia" );
    $obHdnSequencia->setValue( $inSequencia );

    //Define Objeto Hiddem para Codigo do Lote
    $obHdnCodLote = new Hidden;
    $obHdnCodLote->setName ( "inCodLote" );
    $obHdnCodLote->setValue( $inCodLote );

    //Define Objeto Hiddem para Codigo da Entidade
    $obHdnCodEntidade = new Hidden;
    $obHdnCodEntidade->setName ( "inCodEntidade" );
    $obHdnCodEntidade->setValue( $inCodEntidade );

    // Define Label para Codigo da Entidade
    $obLblCodEntidade = new Label;
    $obLblCodEntidade->setRotulo( "Selecione a Entidade" );
    $obLblCodEntidade->setValue( "$inCodEntidade - $stNomEntidade" );

    // Define Objeto Label para Codigo do Lote
    $obLblCodLote = new Label;
    $obLblCodLote->setRotulo( "Número do Lote" );
    $obLblCodLote->setValue( $inCodLote );

    // Define Objeto Label para Nome do Lote
    $obLblNomLote = new Label;
    $obLblNomLote->setRotulo( "Nome do Lote" );
    $obLblNomLote->setValue( $stNomLote );

    // Define Objeto Label para Data do Lote
    $obLblNomEntidade = new Label;
    $obLblNomEntidade->setRotulo( "Data" );
    $obLblNomEntidade->setValue( $stDtLote );

} else {
    // Define Objeto TextBox para Codigo da Entidade
    $obTxtCodEntidade = new TextBox;
    $obTxtCodEntidade->setName   ( "inCodEntidade" );
    $obTxtCodEntidade->setId     ( "inCodEntidade" );
    $obTxtCodEntidade->setValue  ( $inCodEntidade  );
    $obTxtCodEntidade->setRotulo ( "Entidade"      );
    $obTxtCodEntidade->setTitle  ( "Selecione a Entidade" );
    $obTxtCodEntidade->setInteiro( true  );
    $obTxtCodEntidade->setNull   ( false );
    $obTxtCodEntidade->obEvento->setOnChange( "buscaDado('buscaProxLote');" );

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
        $obCmbNomEntidade->obEvento->setOnChange( "buscaDado('buscaProxLote');" );
    } else $jsSL = "buscaDado('buscaProxLote');";
    $obCmbNomEntidade->preencheCombo( $rsEntidade    );

    // Define Objeto TextBox para Codigo do Lote
    $obTxtCodLote = new TextBox;
    $obTxtCodLote->setName   ( "inCodLote" );
    $obTxtCodLote->setId     ( "inCodLote" );
    $obTxtCodLote->setValue  ( $inCodLote  );
    $obTxtCodLote->setRotulo ( "Número do Lote" );
    $obTxtCodLote->setTitle  ( "Informe o Nro do Lote" );
    $obTxtCodLote->setInteiro( true        );
    $obTxtCodLote->obEvento->setOnBlur("buscaDado('buscaLote');");
    $obTxtCodLote->setNull   ( false );

    // Define Objeto TextBox para Descricao do Lote
    $obTxtNomLote = new TextBox;
    $obTxtNomLote->setName     ( "stNomLote" );
    $obTxtNomLote->setId       ( "stNomLote" );
    $obTxtNomLote->setValue    ( $stNomLote  );
    $obTxtNomLote->setRotulo   ( "Nome do Lote" );
    $obTxtNomLote->setTitle    ( "Informe o Nome do Lote" );
    $obTxtNomLote->setNull     ( false );
    $obTxtNomLote->setSize     ( 80 );
    $obTxtNomLote->setMaxLength( 80 );

    // Define Objeto Data para Data do Lote
    $obDtLote = new Data;
    $obDtLote->setName   ( "stDtLote" );
    $obDtLote->setId     ( "stDtLote" );
    $obDtLote->setValue  ( $stDtLote  );
    $obDtLote->setRotulo ( "Data" );
    $obDtLote->setTitle  ( "Informe a Data" );
    $obDtLote->setNull   ( false );
    $obDtLote->obEvento->setOnChange( "validaMes( this, '$inMesProcessamento' );" );
}

// Define Objeto BuscaInner para Conta a Débito
$obBscContaDebito = new BuscaInner;
$obBscContaDebito->setRotulo ( "Conta a Débito" );
$obBscContaDebito->setTitle ( "Informe a Conta de Débito" );
$obBscContaDebito->setId ( "stContaDebito" );
$obBscContaDebito->setValue ( $stContaDebito );
$obBscContaDebito->obCampoCod->setName ( "inCodContaDebito" );
$obBscContaDebito->obCampoCod->setSize ( 10 );
$obBscContaDebito->obCampoCod->setMaxLength( 5 );
$obBscContaDebito->obCampoCod->setValue ( $inCodContaDebito );
$obBscContaDebito->obCampoCod->setAlign ("left");
$obBscContaDebito->obCampoCod->obEvento->setOnBlur("buscaDado('buscaContaDebito');");
$obBscContaDebito->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaDebito','stContaDebito','','".Sessao::getId()."','800','550');");

$obHdnEval = new HiddenEval;
$obHdnEval->setName ( 'hdnEval');
$obHdnEval->setValue ("
                if ((document.frm.inCodContaDebito.value == '') && (document.frm.inCodContaCredito.value == '')) {
                    erro = true;
                    mensagem = 'Informe uma conta a Crédito ou a Débito.';
                }");

// Define Objeto Busca Inner para Conta a Crédito
$obBscContaCredito = new BuscaInner;
$obBscContaCredito->setRotulo ( "Conta a Crédito" );
$obBscContaCredito->setTitle ( "Informe a Conta de Crédito" );
$obBscContaCredito->setId ( "stContaCredito" );
$obBscContaCredito->setValue ( $stContaCredito );
$obBscContaCredito->obCampoCod->setName ( "inCodContaCredito" );
$obBscContaCredito->obCampoCod->setSize ( 10 );
$obBscContaCredito->obCampoCod->setMaxLength( 5 );
$obBscContaCredito->obCampoCod->setValue ( $inCodContaCredito );
$obBscContaCredito->obCampoCod->setAlign ("left");
$obBscContaCredito->obCampoCod->obEvento->setOnBlur("buscaDado('buscaContaCredito');");
$obBscContaCredito->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaCredito','stContaCredito','','".Sessao::getId()."','800','550');");

// Define Objeto TextBox para Codigo do Historico Padrao
$obBscHistorico = new BuscaInner;
$obBscHistorico->setRotulo ( "Histórico" );
$obBscHistorico->setTitle ( "Informe o Histórico Contábil" );
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
$obTxtComplemento->setTitle  ( "Informe o Complemento" );
$obTxtComplemento->setNull   ( true );
$obTxtComplemento->setRows   ( 3 );
$obTxtComplemento->setMaxCaracteres( 400 );

// Define Objeto Moeda para Valor
$obTxtValor = new Moeda;
$obTxtValor->setName     ( "nuValor" );
$obTxtValor->setId       ( "nuValor" );
$obTxtValor->setValue    ( $nuValor  );
$obTxtValor->setRotulo   ( "Valor"   );
$obTxtValor->setTitle    ( "Informe o Valor" );
$obTxtValor->setNull     ( false );
$obTxtValor->setMaxLength( 15 );
$obTxtValor->setSize     ( 22 );
$obTxtValor->setNegativo ( false );
$obTxtValor->obEvento->setOnChange("if (this.value == '0,00') { this.value = ''; alertaAviso('@Valor deve ser maior que zero.','form','erro','".Sessao::getId()."'); \n } ");

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-02.02.04');
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados para Lançamento Contábil" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnBoComplemento );

if ($stAcao == 'alterar') {
    $obFormulario->addHidden( $obHdnSequencia );
    $obFormulario->addHidden( $obHdnCodLote );
    $obFormulario->addHidden( $obHdnCodEntidade );
    $obFormulario->addComponente( $obLblCodEntidade );
    $obFormulario->addComponente( $obLblCodLote );
    $obFormulario->addComponente( $obLblNomLote );
    $obFormulario->addComponente( $obLblNomEntidade );
} else {
    $obFormulario->addComponenteComposto( $obTxtCodEntidade, $obCmbNomEntidade );
    $obFormulario->addComponente( $obTxtCodLote );
    $obFormulario->addComponente( $obTxtNomLote );
    $obFormulario->addComponente( $obDtLote );
}

$obFormulario->addComponente( $obBscContaDebito );
$obFormulario->addComponente( $obBscContaCredito   );
$obFormulario->addHidden    ( $obHdnEval, true );

$obFormulario->addComponente( $obBscHistorico );
$obFormulario->addComponente( $obTxtComplemento );
$obFormulario->addComponente( $obTxtValor );

if ($stAcao == 'alterar') {
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
    $obFormulario->Cancelar( $stLocation );
} else {
    $obBtnOk = new Ok;
    $obBtnCancelar = new Button;
    $obBtnCancelar->setValue( "Limpar" );
    $obBtnCancelar->obEvento->setOnClick( "Limpar();" );

    $obFormulario->defineBarra( array($obBtnOk, $obBtnCancelar) );
}
$obFormulario->show();

if ($jsSL) SistemaLegado::executaFrameOculto($jsSL);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
