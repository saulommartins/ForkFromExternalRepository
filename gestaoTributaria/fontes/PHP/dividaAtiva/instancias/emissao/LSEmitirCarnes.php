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
  * Página de Lista de Emissão de Carnês
  * Data de criação : 16/04/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * @ignore

    * $Id: LSEmitirCarnes.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.04.04
**/

/*
$Log$
Revision 1.7  2007/09/04 14:16:44  cercato
correcao no filtro da lista.

Revision 1.6  2007/08/02 21:01:52  cercato
adicionando observacao.

Revision 1.5  2007/07/31 19:05:19  cercato
correcao na sql de emissao de carnes.

Revision 1.4  2007/07/31 14:48:39  cercato
correcao na sql de emissao de carnes.

Revision 1.3  2007/07/13 21:44:57  cercato
adicionando ordem na lista de emissao de carnes.

Revision 1.2  2007/07/02 20:45:16  cercato
retirando filtro por exercicio.

Revision 1.1  2007/04/16 18:11:29  cercato
adicionando funcoes para emitir carne pela divida.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );

//Definicao dos nomes de arquivos
$stPrograma = "EmitirCarnes";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$stCaminho = CAM_GT_ARR_INSTANCIAS."documentos/";

//Define arquivos PHP para cada acao
$pgProx = $pgFormVinculo;

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( 'link' );
$stLink .= "&stAcao=".$_REQUEST['stAcao'];

if ( isset($_REQUEST['pg']) and isset($_REQUEST['pos']) ) {
    $stLink.= "&pg=".$_REQUEST['pg']."&pos=".$_REQUEST['pos'];
    $link["pg"]  = $_REQUEST['pg'];
    $link["pos"] = $_REQUEST['pos'];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link'  , $link);
Sessao::write('stLink', $stLink);

$stFiltro = "";
if ($_REQUEST['inNum']) {
    $stFiltro .= " AND acne.numeracao = ".$_REQUEST["inNum"];
}

if ($_REQUEST['inCGM']) {
    $stFiltro .= " AND ddc.numcgm = ".$_REQUEST["inCGM"];
}

if ($_REQUEST["inCodImovel"]) {
    $stFiltro .= " AND ddi.inscricao_municipal = ".$_REQUEST["inCodImovel"];
}

if ($_REQUEST["inInscricaoEconomica"]) {
    $stFiltro .= " AND dde.inscricao_economica = ".$_REQUEST["inInscricaoEconomica"];
}

if ($_REQUEST["inCodInscricao"]) {
    $arInscricao = explode( "/", $_REQUEST["inCodInscricao"] );
    $stFiltro .= " AND dda.cod_inscricao = ".$arInscricao[0];
}

if ($_REQUEST["inNrParcelamento"]) {
    $arInscricao = explode( "/", $_REQUEST["inNrParcelamento"] );
    $stFiltro .= " AND ddp.numero_parcelamento = ".$arInscricao[0];
    $stFiltro .= " AND ddp.exercicio = '".$arInscricao[1]."'";
}

$stFiltro .= " ORDER BY ddp.numero_parcelamento, ap.cod_parcela, ddpar.num_parcela";
$obTDATDividaAtiva = new TDATDividaAtiva;
$obTDATDividaAtiva->recuperaListaCarnesDivida( $rsCarne, $stFiltro );

foreach ($rsCarne->arElementos as $dados) {
    if ($dados["judicial"] == 't') {
        $boJudicial = 1;
    } else {
        $boJudicial = 2;
    }
}

if ($boJudicial == 1) {
    Sessao::write('cobrancaJudicial', true);
} else {
    Sessao::write('cobrancaJudicial', false);
}
$obRARRConfiguracao = new RARRConfiguracao;
$obRARRConfiguracao->consultar();

// Separar Vencidas
$hoje = date('Ymd');
$arTmp = $rsCarne->arElementos;
$arVencidas = array();
$arNormais  = array();

if ($arTmp) {
    $arTmp2 = array();
    for ( $inX=0; $inX<count( $arTmp ); $inX++ ) {
        $boEncontrou = false;
        for ( $inY=0; $inY<count( $arTmp2 ); $inY++ ) {
            if (  (  $arTmp[$inX]["numeracao"] == $arTmp2[$inY]["numeracao"] ) &&
                  (  $arTmp[$inX]["num_parcelamento"] == $arTmp2[$inY]["num_parcelamento"] ) &&
                  (  $arTmp[$inX]["num_parcela"] == $arTmp2[$inY]["num_parcela"] )
               ) {
               $boEncontrou = true;
               break;
            }
        }

        if (!$boEncontrou) {
            $arTmp2[] = $arTmp[$inX];
        }
    }

    foreach ($arTmp2 as $linha) {
        $arDtCorrente = explode('-',$linha['dt_vencimento_parcela']);
        $dtCorrente = $arDtCorrente[0].$arDtCorrente[1].$arDtCorrente[2];
        if ($dtCorrente < $hoje) {
            if (( $linha["info_parcela"] != "Única" ) || (( $linha["info_parcela"] == "Única" ) && ( $obRARRConfiguracao->getBaixaManualUnica() == "sim" )) ) {
                $arVencidas[] = $linha;
            }
        } else {
            $arNormais[]  = $linha;
        }
    }
}

$rsNormais  = new Recordset;
$rsVencidas = new Recordset;
$rsNormais->preenche    ( $arNormais    );
$rsVencidas->preenche   ( $arVencidas   );
$boInscricaoN = $boInscricaoV = false;

while ( !$rsNormais->eof() ) {
    if ( $rsNormais->getCampo('inscricao') ) {
        $boInscricaoN = true;
        break;
    }
    $rsNormais->proximo();
}
while ( !$rsVencidas->eof() ) {
    if ( $rsVencidas->getCampo('inscricao') ) {
        $boInscricaoV = true;
        break;
    }
    $rsVencidas->proximo();
}
$rsNormais->setPrimeiroElemento();
$rsVencidas->setPrimeiroElemento();

$rsNormais->addFormatacao('valor_parcela','NUMERIC_BR');
$rsVencidas->addFormatacao('valor_parcela','NUMERIC_BR');

unset($arNormais,$arVencidas,$rsCarne);

$obLista = new Lista;
$obLista->setTitulo    ("Parcelas a Vencer");
$obLista->setRecordSet( $rsNormais  );
$obLista->setMostraPaginacao(false);
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Numeração");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Parcela");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Contribuinte");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Cobrança");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth( 5  );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Vencimento");
$obLista->ultimoCabecalho->setWidth( 6 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Reemitir");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numeracao]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "info_parcela" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->ultimoDado->setTitle("CGM: [numcgm] <br> [nom_cgm]");
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numero_parcelamento]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor_parcela" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vencimento_parcela_br" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obChkReemitir = new Checkbox;
$obChkReemitir->setName                        ( "nboReemitir"                                );
$obChkReemitir->setValue                       ( "[cod_lancamento]§[cod_parcela]§[cod_convenio]§[cod_carteira]§[exercicio]§[convenio_atual]§[carteira_atual]§[numeracao]§[vencimento_parcela_br]§[valor_parcela]§[info_parcela]§[numcgm]§[impresso]§[chave_vinculo]§[id_vinculo]§[inscricao]" );

$obLista->addDadoComponente                    ( $obChkReemitir                              );
$obLista->ultimoDado->setAlinhamento           ( 'CENTRO'                                    );
$obLista->ultimoDado->setCampo                 ( "reemitir"                                  );
$obLista->commitDadoComponente                 (                                             );

// checks
$obChkTodosN = new Checkbox;
$obChkTodosN->setName                        ( "boTodos" );
$obChkTodosN->setId                          ( "boTodos" );
$obChkTodosN->setRotulo                      ( "Selecionar Todas" );
$obChkTodosN->obEvento->setOnChange          ( "selecionarTodos('n');" );
$obChkTodosN->montaHTML();

$obTabelaCheckboxN = new Tabela;
$obTabelaCheckboxN->addLinha();
$obTabelaCheckboxN->ultimaLinha->addCelula();
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setColSpan ( 2 );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Selecionar Todos".$obChkTodosN->getHTML()."&nbsp;</div>");
$obTabelaCheckboxN->ultimaLinha->commitCelula();
$obTabelaCheckboxN->commitLinha();

$obTabelaCheckboxN->montaHTML();
$obLista->montaHTML();

$stHtmlN  = $obLista->getHTML();
$stHtmlN .= $obTabelaCheckboxN->getHTML();

// lista de vencidas
$obListaNVencidas = new Lista;
$obListaNVencidas->setTitulo    ("Parcelas Vencidas");
$obListaNVencidas->setRecordSet( $rsVencidas );
$obListaNVencidas->setMostraPaginacao(false);
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("&nbsp;");
$obListaNVencidas->ultimoCabecalho->setWidth( 5 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Numeração");
$obListaNVencidas->ultimoCabecalho->setWidth( 5 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Parcela");
$obListaNVencidas->ultimoCabecalho->setWidth( 5 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Contribuinte");
$obListaNVencidas->ultimoCabecalho->setWidth( 25 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Cobrança");
$obListaNVencidas->ultimoCabecalho->setWidth( 20 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Valor");
$obListaNVencidas->ultimoCabecalho->setWidth( 5  );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Vencimento");
$obListaNVencidas->ultimoCabecalho->setWidth( 6 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Reemitir");
$obListaNVencidas->ultimoCabecalho->setWidth( 5 );
$obListaNVencidas->commitCabecalho();

$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "[numeracao]/[exercicio]" );
$obListaNVencidas->ultimoDado->setAlinhamento( 'CENTRO' );
$obListaNVencidas->commitDado();
$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "info_parcela" );
$obListaNVencidas->ultimoDado->setAlinhamento( 'DIREITA' );
$obListaNVencidas->commitDado();
$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "nom_cgm" );
$obListaNVencidas->ultimoDado->setTitle("CGM: [numcgm] <br> [nom_cgm]");
$obListaNVencidas->ultimoDado->setAlinhamento( 'CENTRO' );
$obListaNVencidas->commitDado();
$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "[numero_parcelamento]/[exercicio]" );
$obListaNVencidas->ultimoDado->setAlinhamento( 'DIREITA' );
$obListaNVencidas->commitDado();
$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "valor_parcela" );
$obListaNVencidas->ultimoDado->setAlinhamento( 'DIREITA');
$obListaNVencidas->commitDado();

$txtNovoVencimento = new Data;
$txtNovoVencimento->setName     ( "dtNovoVencimento"    );
$txtNovoVencimento->setRotulo   ( "Novo Vencimento"     );
$txtNovoVencimento->setTitle    ( "Novo vencimento para a parcela" );
$txtNovoVencimento->setStyle    ( "width:100%;");

$obChkReemitir = new Checkbox;
$obChkReemitir->setName                        ( "vboReemitir"                                );
$obChkReemitir->setValue                       ( "[cod_lancamento]§[cod_parcela]§[cod_convenio]§[cod_carteira]§[exercicio]§[convenio_atual]§[carteira_atual]§[numeracao]§[vencimento_parcela_br]§[valor_parcela]§[info_parcela]§[numcgm]§[impresso]§[chave_vinculo]§[id_vinculo]§[inscricao]" );

$obListaNVencidas->addDadoComponente                    ( $txtNovoVencimento                          );
$obListaNVencidas->ultimoDado->setAlinhamento           ( 'CENTRO'                                    );
$obListaNVencidas->ultimoDado->setCampo                 ( "novo_vencimento"                           );
$obListaNVencidas->commitDadoComponente                 (                                             );

$obListaNVencidas->addDadoComponente                    ( $obChkReemitir                              );
$obListaNVencidas->ultimoDado->setAlinhamento           ( 'CENTRO'                                    );
$obListaNVencidas->ultimoDado->setCampo                 ( "reemitir"                                  );
$obListaNVencidas->commitDadoComponente                 (                                             );

// checks
$obChkTodos = new Checkbox;
$obChkTodos->setName                        ( "boTodos" );
$obChkTodos->setId                          ( "boTodos" );
$obChkTodos->setRotulo                      ( "Selecionar Todas" );
$obChkTodos->obEvento->setOnChange          ( "selecionarTodos('v');" );
$obChkTodos->montaHTML();

$obTabelaCheckbox = new Tabela;
$obTabelaCheckbox->addLinha();
$obTabelaCheckbox->ultimaLinha->addCelula();
$obTabelaCheckbox->ultimaLinha->ultimaCelula->setColSpan ( 2 );
$obTabelaCheckbox->ultimaLinha->ultimaCelula->setClass   ( $obListaNVencidas->getClassPaginacao() );
$obTabelaCheckbox->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Selecionar Todos".$obChkTodos->getHTML()."&nbsp;</div>");
$obTabelaCheckbox->ultimaLinha->commitCelula();
$obTabelaCheckbox->commitLinha();

$obTabelaCheckbox->montaHTML();
$obListaNVencidas->montaHTML();

$stHtmlTmp  = $obListaNVencidas->getHTML();
$stHtmlTmp .= $obTabelaCheckbox->getHTML();

$obSpanNormais = new Span;
$obSpanNormais->setId       ( 'spnListaNormais' );
$obSpanNormais->setValue    ( $stHtmlN );

$obSpanVencidas = new Span;
$obSpanVencidas->setId      ( "spnLista"      );
$obSpanVencidas->setValue   ( $stHtmlTmp );

$obTxtObservacao = new TextArea;
$obTxtObservacao->setName ( "stObservacao" );
$obTxtObservacao->setRotulo ( "Observações p/ Boleto" );
$obTxtObservacao->setTitle ( "Observações para o contribuinte." );
$obTxtObservacao->setValue ( "" );
$obTxtObservacao->setNull  ( true );
$obTxtObservacao->setCols ( 30 );
$obTxtObservacao->setRows ( 5 );
$obTxtObservacao->setMaxCaracteres(300);

$rsModelos = new RecordSet;

include_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );

$obRARRCarne = new RARRCarne;
$obRARRCarne->listarModeloDeCarne( $rsModelos, Sessao::read('acao') );

$obCmbModelo = new Select;
$obCmbModelo->setRotulo       ( "Modelo"    );
$obCmbModelo->setTitle        ( "Modelo de carne"    );
$obCmbModelo->setName         ( "cmbModelo" );
$obCmbModelo->addOption       ( "", "Selecione" );
$obCmbModelo->setCampoId      ( "[nom_arquivo]§[cod_modelo]" );
$obCmbModelo->setCampoDesc    ( "nom_modelo" );
$obCmbModelo->preencheCombo    ( $rsModelos );
$obCmbModelo->setStyle        ( "width: 100%;" );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST['stCtrl']  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao']  );

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

$obFormulario = new FormularioAbas;
$obFormulario->addForm  ( $obForm       );
//$obFormulario->addTitulo( 'Reemitir Carnês' );
$obFormulario->addHidden( $obHdnCtrl    );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addAba   ( "A Vencer"    );
$obFormulario->addSpan  ( $obSpanNormais);
$obFormulario->addAba   ( "Vencidas" );
$obFormulario->addSpan  ( $obSpanVencidas);

$obFormulario->addDiv( 4, "componente" );
$obFormulario->addComponente( $obTxtObservacao );
$obFormulario->addComponente( $obCmbModelo );
$obFormulario->fechaDiv();

$obFormulario->Cancelar();
$obFormulario->show();
