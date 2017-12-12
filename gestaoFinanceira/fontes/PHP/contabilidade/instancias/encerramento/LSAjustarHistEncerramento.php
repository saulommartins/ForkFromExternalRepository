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
    * Página de Listagem de Itens
    * Data de Criação   : 21/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    * $Id: LSAjustarHistEncerramento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AjustarHistEncerramento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$_REQUEST['stEval'] = "";
$filtro = Sessao::read('filtro');
//seta elementos do filtro para ENTIDADE
if ($filtro['inCodEntidade']) {
    $_REQUEST['inCodEntidade'] = $filtro['inCodEntidade'];
}

if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        if (is_array($stValor)) {
            $stCodEntidade = "";
            foreach ($stValor as $key => $valor) {
                $stCodEntidade .= $valor." , ";
            }
            $stValor = substr( $stCodEntidade, 0, strlen($stCodEntidade) - 2 );
        }
        if ($stCampo != "stEval") {
            $filtro[$stCampo] = $stValor;
        }
    }
    Sessao::write('filtro', $filtro);
    Sessao::write('pg', $_GET['pg'] ? $_GET['pg'] : 0);
    Sessao::write('pos', $_GET['pos']? $_GET['pos'] : 0);
    Sessao::write('paginando', true);
} else {
    Sessao::write('pg', $_GET['pg']);
    Sessao::write('pos', $_GET['pos']);
}

$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $filtro['stCodEntidade'] );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( $filtro['inCodHistorico'] );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo("");
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLoteInicial( $filtro['stDtLancamento'] );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLoteTermino( $filtro['stDtLancamento'] );

$stOrdem = "cod_entidade, cod_lote, dt_lote, sequencia";
$obRContabilidadeLancamentoValor->listar( $rsLista , $stOrdem );
$rsLista->addFormatacao( "vl_lancamento", "NUMERIC_BR" );

$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
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

$stEval = "
    var i=0;
    var cont=0;

    for (i=0; i<document.forms['frm'].elements.length; i++) {
        if (document.forms['frm'].elements[i].type=='checkbox') {
            if (document.forms['frm'].elements[i].checked==true) {
                cont++;
            }
        }
    }
    if (cont == 0) {
        erro = true;
        mensagem += '@Escolha pelo menos um Lançamento!()';
    }
";

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval"            );
$obHdnEval->setValue ( $stEval             );

$obLista = new Lista;
$obLista->setMostraPaginacao( false );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Lote");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Tipo ");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Histórico ");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor ");
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_lote]"."/".Sessao::getExercicio() );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "tipo" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_lote" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_historico] - [nom_historico]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_lancamento" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obChkLote = new CheckBox;
$obChkLote->setName ( "boLancamento|[cod_lote]|[tipo]|[cod_entidade]|[sequencia]|".Sessao::getExercicio());
$obChkLote->setId   ( "boLancamento" );
$obChkLote->setValue( true );
$obChkLote->setChecked( true );

$obLista->addDadoComponente( $obChkLote );
$obLista->ultimoDado->setCampo( "[cod_lote]|[tipo]|[cod_entidade]|[sequencia]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDadoComponente();

$obLista->montaHTML();
$stHTML = $obLista->getHTML();

$stHTML = str_replace( "\n" ,"" ,$stHTML );
$stHTML = str_replace( "  " ,"" ,$stHTML );
$stHTML = str_replace( "'","\\'",$stHTML );
$stHTML = str_replace( "\\\'","\\'",$stHTML );

// Define Objeto Span Lancamentos
$obSpanLancamentos = new Span;
$obSpanLancamentos->setId( "spnLancamentos" );
$obSpanLancamentos->setValue( $stHTML );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden ( $obHdnCtrl          );
$obFormulario->addHidden ( $obHdnAcao          );
//$obFormulario->addHidden ( $obHdnEval, true    );
$obFormulario->addSpan   ( $obSpanLancamentos  );

$stLocation = $pgFilt.'?'.Sessao::getId().'&stAcao'.$stAcao;

$obFormulario->Cancelar( $stLocation );
$obFormulario->show();

?>
