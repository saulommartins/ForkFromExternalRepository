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
    * Página de Formulario de Transferência
    * Data de Criação: 04/01/2006

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * Casos de uso: uc-03.03.11

    $Id:$

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoItem.class.php" );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoClassificacao.class.php" );
include_once(CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoPerecivel.class.php');

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function preencheSpanDadosItem($arDadosItem, $inCodAlmoxarifado, $stAcao)
{
    $nmSaldoAtual = $arDadosItem['saldo_atual'];
    $nmQuantidade = $arDadosItem['quantidade'];
    $boPerecivel  = $arDadosItem['perecivel'];
    $inCodItem    = $arDadosItem['cod_item'];
    $inId         = $arDadosItem['inId'];

    $obFormulario = new Formulario();

    $obHdnInIDPos = new Hidden;
    $obHdnInIDPos->setName  ('inIDPos');
    $obHdnInIDPos->setId    ('inIDPos');
    $obHdnInIDPos->setValue ($inId);

    $obHdnSaldoAtual = new Hidden;
    $obHdnSaldoAtual->setName ('nmSaldoAtual');
    $obHdnSaldoAtual->setId   ('nmSaldoAtual');
    $obHdnSaldoAtual->setValue ( $nmSaldoAtual);

    $rsAtributos = unserialize($arDadosItem['ValoresAtributos']);
    $boTemAtributos = $rsAtributos->getNumLinhas() > 0;

    if ($boPerecivel) {
       if ($stAcao == 'entrada') {
          $obSpanListaLotes = new Span();
          $obSpanListaLotes->setId("spnListaLotes");
       } else {
          $obSpanAlteraLotes = new Span();
          $obSpanAlteraLotes->setId("spnAlteraLotes");
       }
    } elseif ($boTemAtributos) {
       $obSpanListaAtributos = new Span();
       $obSpanListaAtributos->setId('spnListaAtributos');
    }

    $obBtnAlterar = new Button;
    $obBtnAlterar->setName ( "btnAlterar" );
    $obBtnAlterar->setValue( "Alterar" );
    $obBtnAlterar->setTipo ( "button" );
    $obBtnAlterar->obEvento->setOnClick( "montaParametrosGET('AlterarItem');" );

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName( "btnLimpar" );
    $obBtnLimpar->setValue( "Limpar" );
    $obBtnLimpar->setTipo( "button" );
    $obBtnLimpar->obEvento->setOnClick( "montaParametrosGET('limpaItem');" );

    $obFormulario->addHidden($obHdnInIDPos);
    $obFormulario->addHidden($obHdnSaldoAtual);
    if ($boPerecivel) {
       if ($stAcao == 'entrada') {
          $obFormulario->addSpan($obSpanListaLotes);
       } else {
          $obFormulario->addSpan($obSpanAlteraLotes);
       }
    } elseif ($boTemAtributos) {
       $obFormulario->addSpan($obSpanListaAtributos);
    }

    if($stAcao == "saida")
       $obFormulario->defineBarra(array($obBtnAlterar), "left", "");

    $obFormulario->montaInnerHTML();

    if ($boPerecivel) {
        if ($arDadosItem['ValoresLotes']) {
            if ($stAcao == "entrada") {
              $stJs .= preencheSpanListaLotes($arDadosItem['ValoresLotes'], $_REQUEST['stAcao']);
            } else {
              $stJs .= preencheSpanAlteraLotes($arDadosItem['ValoresLotes'], $arDadosItem, $inCodAlmoxarifado);
            }
        }
    } else {
       $stJs .= "d.getElementById('spnDadosItem').innerHTML = '&nbsp';";
//       $stJs .= preencheSpanListaAtributos($rsAtributos, $arDadosItem['stAtributos']);
    }

    return $stJs;
}

function preencheSpanListaItens($arRecordSet, $stAcao)
{
    # global $stAcao;

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo ( '');

    $rsItens = new RecordSet();
    $rsItens->preenche( $arRecordSet );
    $rsItens->addFormatacao('desc_marca'   , 'STRIPSLASHES');
    $rsItens->addFormatacao('desc_centro'  , 'STRIPSLASHES');
    $rsItens->addFormatacao('desc_unidade' , 'STRIPSLASHES');
    $rsItens->addFormatacao('desc_item'    , 'STRIPSLASHES');

    $obLista->setRecordSet( $rsItens);

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( '&nbsp' );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Item' );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Unidade de Medida' );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Marca' );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    if ($_REQUEST['stAcao'] == "entrada")
        $obLista->ultimoCabecalho->addConteudo( "Centro de Custo de Destino");
    else
       $obLista->ultimoCabecalho->addConteudo( "Centro de Custo de Origem");

    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    if ($_REQUEST['stAcao'] == "entrada")
      $obLista->ultimoCabecalho->addConteudo( 'Saldo Atual no Destino' );
    else
      $obLista->ultimoCabecalho->addConteudo( 'Saldo Atual na Origem' );

    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Quantidade' );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Detalhar' );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_item]-[desc_item]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "desc_unidade" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "[cod_marca]-[desc_marca]" );
    $obLista->commitDado();

    $obLista->addDado();
    if ($_REQUEST['stAcao'] == "entrada")
        $obLista->ultimoDado->setCampo( "[cod_centro_destino]-[desc_centro_destino]" );
    else
        $obLista->ultimoDado->setCampo( "[cod_centro]-[desc_centro]" );

    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "saldo_atual" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "quantidade" );
    $obLista->commitDado();

    $obRdnDetalharItem = new Radio();
    $obRdnDetalharItem->setValue( "[cod_item]-[cod_marca]-[cod_centro]" );
    $obRdnDetalharItem->setName( "boDetalharItem" );
    $obRdnDetalharItem->setId( "boDetalharItem" );
    $obRdnDetalharItem->setNull( false );
    $obRdnDetalharItem->obEvento->setOnClick("DetalharItem();");

    $obLista->addDadoComponente( $obRdnDetalharItem, false );

    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    // $obLista->ultimoDado->setDesabilitaComponente( "boDesabilitaDetalhar" );
    $obLista->commitDadoComponente();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "d.getElementById('spnListaItens').innerHTML = '".$stHTML."';";
    $stJs .= "d.getElementById('spnDadosItem').innerHTML = '&nbsp';";
    $stJs .= $stJsQtd;

    return $stJs;
}

function preencheSpanAlteraLotes($arRecordSet, $arDadosItem, $inCodAlmoxarifadoOrigem)
{
   global $pgOcul;
   global $pgProc;

   $obFormulario = new Formulario();

   $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel;
   $obTAlmoxarifadoPerecivel->setDado('cod_item', $arDadosItem['cod_item']);
   $obTAlmoxarifadoPerecivel->setDado('cod_marca', $arDadosItem['cod_marca']);
   $obTAlmoxarifadoPerecivel->setDado('cod_centro', $arDadosItem['cod_centro']);
   $obTAlmoxarifadoPerecivel->setDado('cod_almoxarifado', $inCodAlmoxarifadoOrigem);
   $obTAlmoxarifadoPerecivel->recuperaPereciveis($rsLotes);

   $obCmbLote = new Select;
   $obCmbLote->setRotulo    ( '*Lote' );
   $obCmbLote->setTitle     ( 'Selecione o lote');
   $obCmbLote->setName      ( 'stLote' );
   $obCmbLote->setId        ( 'stLote' );
   $obCmbLote->setValue     ( $stLote );
   $obCmbLote->setCampoID   ( 'lote' );
   $obCmbLote->setCampoDesc ( 'lote' );
   $obCmbLote->addOption    ( "", "Selecione" );
   $obCmbLote->preencheCombo( $rsLotes );
   $obCmbLote->obEvento->setOnChange( 'montaParametrosGET(\'preencheDadosLote\'); desabilitaIncluirLote();' );

   $obHdnDataFabricacao = new Hidden();
   $obHdnDataFabricacao->setId('hdndtFabricacao');
   $obHdnDataFabricacao->setName('hdndtFabricacao');
   $obHdnDataFabricacao->setRotulo ( 'Data de Fabricação' );
   $obHdnDataFabricacao->setValue ( $dtFabricacao );

   $obHdnDataValidade = new Hidden();
   $obHdnDataValidade->setId('hdndtValidade');
   $obHdnDataValidade->setName('hdndtValidade');
   $obHdnDataValidade->setRotulo ( 'Data de Validade' );
   $obHdnDataValidade->setValue ( $dtValidade );

   $obHdnSaldoLote = new Hidden();
   $obHdnSaldoLote->setId('hdnnmSaldoLote');
   $obHdnSaldoLote->setName('hdnnmSaldoLote');
   $obHdnSaldoLote->setRotulo ( 'Saldo do Lote' );
   $obHdnSaldoLote->setValue ( $nmSaldoLote );

   $obLblDataFabricacao = new Label();
   $obLblDataFabricacao->setId('dtFabricacao');
   $obLblDataFabricacao->setRotulo ( 'Data de Fabricação' );
   $obLblDataFabricacao->setValue ( $dtFabricacao );

   $obLblDataValidade = new Label();
   $obLblDataValidade->setId('dtValidade');
   $obLblDataValidade->setRotulo ( 'Data de Validade' );
   $obLblDataValidade->setValue ( $dtValidade );

   $obLblSaldoLote = new Label();
   $obLblSaldoLote->setId('nmSaldoLote');
   $obLblSaldoLote->setRotulo ( 'Saldo do Lote' );
   $obLblSaldoLote->setValue ( $nmSaldoLote );

   $obTxtQtdLote = new Numerico();
   $obTxtQtdLote->setId('nmQtdLote');
   $obTxtQtdLote->setName('nmQtdLote');
   $obTxtQtdLote->setRotulo ( '*Quantidade');
   $obTxtQtdLote->setTitle ( 'Informe a quantidade');
   $obTxtQtdLote->setDecimais(4);
   $obTxtQtdLote->setValue( $nmQtdLote );
   $obTxtQtdLote->obEvento->setOnChange("desabilitaIncluirLote();");

   $obBtnIncluir = new Button;
   $obBtnIncluir->setName ( "btnIncluirLote" );
   $obBtnIncluir->setId ( "btnIncluirLote" );
   $obBtnIncluir->setValue( "Incluir" );
   $obBtnIncluir->setTipo ( "button" );
   $obBtnIncluir->obEvento->setOnClick( "montaParametrosGET('incluirLote');" );
   $obBtnIncluir->setDisabled(true);

   $obBtnLimpar = new Button;
   $obBtnLimpar->setName( "btnLimparLote" );
   $obBtnLimpar->setValue( "Limpar" );
   $obBtnLimpar->setTipo( "button" );
   $obBtnLimpar->obEvento->setOnClick( "LimparLote()" );

   $obSpnListaLote = new Span;
   $obSpnListaLote->setId("spnListaLotes");

   $obFormulario->addTitulo("Perecível");
   $obFormulario->addComponente($obCmbLote);
   $obFormulario->addComponente($obLblDataFabricacao);
   $obFormulario->addComponente($obLblDataValidade);
   $obFormulario->addComponente($obLblSaldoLote);
   $obFormulario->addHidden    ($obHdnDataFabricacao);
   $obFormulario->addHidden    ($obHdnDataValidade);
   $obFormulario->addHidden    ($obHdnSaldoLote);
   $obFormulario->addComponente($obTxtQtdLote);
   $obFormulario->defineBarra(array($obBtnIncluir, $obBtnLimpar), "left", "");
   $obFormulario->addSpan($obSpnListaLote);
   $obFormulario->montaInnerHtml();
   $stHTML = $obFormulario->getHTML();

   $stJs.= "d.getElementById('spnDadosItem').innerHTML = '".$stHTML."';";
   $stJs.= preencheSpanListaLotes($arRecordSet, $_REQUEST['stAcao']);

   return $stJs;
}

function preencheSpanListaAtributos($rsAtributos, $stAtributos)
{
   global $stAcao;

   $rsAtributos->addFormatacao("quantidade","NUMERIC_BR");
   $rsAtributos->addFormatacao("saldo","NUMERIC_BR");
   $obLista = new Lista;
   $obLista->setMostraPaginacao( false );
   if($stAcao == "saida")
      $obLista->setTitulo ( 'Atributos de Saída do Item');
   else
      $obLista->setTitulo ( 'Atributos de Entrada do Item');

   foreach ($rsAtributos->arElementos[0] as $atributos) {
       $inValorAtributo = $atributos['valor_atributos'];
       $rsAtributos->arElementos[0]['valor_atributos'] = $inValorAtributo." - ".$stAtributos;

   }

   reset($rsAtributos->arElementos);

   $obLista->setRecordSet( $rsAtributos );

   $obLista->addCabecalho();
   $obLista->ultimoCabecalho->addConteudo( '&nbsp' );
   $obLista->ultimoCabecalho->setWidth( 5 );
   $obLista->commitCabecalho();

   $obLista->addCabecalho();
   //   $obLista->ultimoCabecalho->addConteudo( $stAtributos );
   $obLista->ultimoCabecalho->addConteudo( 'Atributos Dinâmicos' );
   $obLista->ultimoCabecalho->setWidth( 50 );
   $obLista->commitCabecalho();

   $obLista->addCabecalho();
   $obLista->ultimoCabecalho->addConteudo( "Saldo" );
   $obLista->ultimoCabecalho->setWidth( 10 );
   $obLista->commitCabecalho();

   $obLista->addCabecalho();
   $obLista->ultimoCabecalho->addConteudo( "Quantidade" );
   $obLista->ultimoCabecalho->setWidth( 10 );
   $obLista->commitCabecalho();

   $obLista->addDado();
   $obLista->ultimoDado->setCampo( "valor_atributos" );
   $obLista->commitDado();

   $obLista->addDado();
   $obLista->ultimoDado->setAlinhamento("DIREITA");
   $obLista->ultimoDado->setCampo( "saldo" );
   $obLista->commitDado();
   if ($stAcao == "saida") {
      $obTxtQtdLote = new Numerico;
      $obTxtQtdLote->setId       ("nmQtdAtributo");
      $obTxtQtdLote->setName     ("nmQtdAtributo");
      $obTxtQtdLote->setSize     ( 14 );
      $obTxtQtdLote->setMaxLength( 14 );
      $obTxtQtdLote->setDecimais ( 4 );
      $obTxtQtdLote->setValue    ( "quantidade" );
      $obTxtQtdLote->obEvento->setOnBlur("montaParametrosGET('AlterarQuantidadeAtributo');");

      $obLista->addDadoComponente( $obTxtQtdLote );
      $obLista->ultimoDado->setCampo( "nmQtdLote" );
      $obLista->commitDadoComponente();
   } else {
      $obLista->addDado();
      $obLista->ultimoDado->setAlinhamento("DIREITA");
      $obLista->ultimoDado->setCampo( "quantidade" );
      $obLista->commitDado();
   }

   $obLista->montaHTML();
   $stHTML = $obLista->getHTML();
   $stHTML = str_replace( "\n" ,"" ,$stHTML );
   $stHTML = str_replace( "  " ,"" ,$stHTML );
   $stHTML = str_replace( "'","\\'",$stHTML );

   $stJs  = "jq('#spnDadosItem').html('$stHTML');";

   return $stJs;
}

function preencheSpanListaLotes($arRecordSet, $stAcao)
{
//   global $stAcao;

    if ($stAcao == 'entrada') {
        foreach ($arRecordSet as $chave=>$arItem) {

            $saldo      = str_replace('.','',$arItem['saldo']);
            $saldo      = str_replace(',','.',$saldo);
            $quantidade = str_replace('.','',$arItem['quantidade']);
            $quantidade = str_replace(',','.',$quantidade);
            $saldo      = $saldo-$quantidade;
            $arRecordSet[$chave]['saldo'] = number_format($saldo,4,',','.');
        }
    }

   $obLista = new Lista;
   $obLista->setMostraPaginacao( false );
   $obLista->setTitulo ( '');
   $rsItens = new RecordSet();
   $rsItens->preenche( $arRecordSet );
   $obLista->setRecordSet( $rsItens );

   $obLista->addCabecalho();
   $obLista->ultimoCabecalho->addConteudo( '&nbsp' );
   $obLista->ultimoCabecalho->setWidth( 5 );
   $obLista->commitCabecalho();

   $obLista->addCabecalho();
   $obLista->ultimoCabecalho->addConteudo( 'Lote' );
   $obLista->ultimoCabecalho->setWidth( 5 );
   $obLista->commitCabecalho();

   $obLista->addCabecalho();
   $obLista->ultimoCabecalho->addConteudo( 'Data de Fabricação' );
   $obLista->ultimoCabecalho->setWidth( 5 );
   $obLista->commitCabecalho();

   $obLista->addCabecalho();
   $obLista->ultimoCabecalho->addConteudo( 'Data de Validade' );
   $obLista->ultimoCabecalho->setWidth( 5 );
   $obLista->commitCabecalho();

   $obLista->addCabecalho();
   $obLista->ultimoCabecalho->addConteudo( 'Saldo do Lote' );
   $obLista->ultimoCabecalho->setWidth( 5 );
   $obLista->commitCabecalho();

   $obLista->addCabecalho();
   $obLista->ultimoCabecalho->addConteudo( 'Quantidade' );
   $obLista->ultimoCabecalho->setWidth( 5 );
   $obLista->commitCabecalho();

  if ($stAcao != 'entrada') {
      $obLista->addCabecalho();
      $obLista->ultimoCabecalho->addConteudo( '&nbsp' );
      $obLista->ultimoCabecalho->setWidth( 5 );
      $obLista->commitCabecalho();

   }

   $obLista->addDado();
   $obLista->ultimoDado->setCampo( "lote" );
   $obLista->commitDado();

   $obLista->addDado();
   $obLista->ultimoDado->setAlinhamento("CENTRO");
   $obLista->ultimoDado->setCampo( "dt_fabricacao" );
   $obLista->commitDado();

   $obLista->addDado();
   $obLista->ultimoDado->setAlinhamento("CENTRO");
   $obLista->ultimoDado->setCampo( "dt_validade" );
   $obLista->commitDado();

   $obLista->addDado();
   $obLista->ultimoDado->setAlinhamento("DIREITA");
   $obLista->ultimoDado->setCampo( "saldo" );
   $obLista->commitDado();
/*   if ($stAcao == "entrada") {

      $obTxtQtdLote = new Numerico;
      $obTxtQtdLote->setId       ("nmQtdLoteLista");
      $obTxtQtdLote->setName     ("nmQtdLoteLista");
      $obTxtQtdLote->setSize     ( 14 );
      $obTxtQtdLote->setMaxLength( 14 );
      $obTxtQtdLote->setDecimais ( 4 );
      $obTxtQtdLote->setValue    ( "quantidade" );
      $obTxtQtdLote->obEvento->setOnBlur("montaParametrosGET('AlterarQuantidadeLote');");

      $obLista->addDadoComponente( $obTxtQtdLote );
      $obLista->ultimoDado->setCampo( "nmQtdLote" );
      $obLista->commitDadoComponente();

   } else {*/

   $obLista->addDado();
   $obLista->ultimoDado->setAlinhamento("DIREITA");
   $obLista->ultimoDado->setCampo( "quantidade" );
   $obLista->commitDado();

   if ($stAcao == "saida") {

      $obLista->addAcao();
      $obLista->ultimaAcao->setAcao( 'Alterar' );
      $obLista->ultimaAcao->setFuncao( true );
      $obLista->ultimaAcao->setLink(  "JavaScript:AlterarLote();"  );
      $obLista->ultimaAcao->addCampo("1","inId");
      $obLista->commitAcao();

      $obLista->addAcao();
      $obLista->ultimaAcao->setAcao( 'Excluir' );
      $obLista->ultimaAcao->setFuncao( true );
      $obLista->ultimaAcao->setLink(  "JavaScript:ExcluirLote();"  );
      $obLista->ultimaAcao->addCampo("1","inId");
      $obLista->commitAcao();
   }
   $obLista->montaHTML();
   $stHTML = $obLista->getHTML();
   $stHTML = str_replace( "\n" ,"" ,$stHTML );
   $stHTML = str_replace( "  " ,"" ,$stHTML );
   $stHTML = str_replace( "'","\\'",$stHTML );

   $stJs  = "d.getElementById('spnListaLotes').innerHTML = '".$stHTML."';";
   $stJs .= "d.getElementById('Ok').disabled = false;";

   return $stJs;
}

function validaQuantidadeLote()
{
    global $stAcao;

    $arValoresSessao = Sessao::read('Valores');

    $nuValor = 0;
    $boTemLotes = false;
    if ($stAcao == 'entrada') {
       foreach ($_REQUEST as $key=>$value) {
           if ( strstr($key,'nmQtdLoteLista_') ) {
               $boTemLotes = true;
               $nuQuantidadeLote = str_replace(",", ".", str_replace(".", "" ,$value) );
               list($stIgnora, $inIdLote) = explode('_', $key);
               $nuSaldoLote = $arValoresSessao[$_REQUEST['inIDPos']]['ValoresLotes'][$inIdLote-1]['saldo'];
               $nuSaldoLote = str_replace(",", ".", str_replace(".", "" ,$nuSaldoLote) );
               if ($nuQuantidadeLote > $nuSaldoLote) {
                   return "alertaAviso('@Quantidade informada (".number_format($nuQuantidadeLote, 4, ',', '.').") ultrapassou o saldo do lote (".number_format($nuSaldoLote, 4, ',', '.').").','form','erro','".Sessao::getIt()."', '../');";
               }
               $nuValor += $nuQuantidadeLote ;
           }
       }
   } else {
      foreach ($arValoresSessao[$_REQUEST['inIDPos']]['ValoresLotes'] as $arLote) {
           $boTemLotes = true;
           $nuQuantidadeLote = str_replace(",", ".", str_replace(".", "" ,$arLote['quantidade']) );
           $nuValor += $nuQuantidadeLote;
      }
   }

    $nuQuantidade = $arValoresSessao[$_REQUEST['inIDPos']]['quantidade'];
    $nuQuantidade = str_replace(",", ".", str_replace(".", "" ,$nuQuantidade) );
    if ($boTemLotes) {
       if ($nuValor != $nuQuantidade) {
           return "alertaAviso('@Quantidade informada (".number_format($nuValor, 4, ',', '.').") esta diferente da quantidade (".number_format($nuQuantidade, 4, ',', '.').").','form','erro','".Sessao::getId()."', '../');";
       }
    }
}

function validaQuantidadeAtributos()
{
    global $stAcao;

    $arValoresSessao = Sessao::read('Valores');

    $nuValor = 0;
    $boTemAtributos = false;
    if ($stAcao == 'saida') {
      foreach ($_REQUEST as $key=>$value) {
           if ( strstr($key,'nmQtdAtributo_') ) {
               $boTemAtributos = true;
               $nuQuantidadeAtributo = str_replace(",", ".", str_replace(".", "" ,$value) );
               list($stIgnora, $inIdAtributo) = explode('_', $key);
               $rsAtributos = unserialize($arValoresSessao[$_REQUEST['inIDPos']]['ValoresAtributos']);
               $rsAtributos->setPrimeiroElemento();
               while (!$rsAtributos->eof()) {
                   if ($rsAtributos->getCampo('cod_sequencial') == $inIdAtributo) {
                       $nuSaldoAtributo = $rsAtributos->getCampo('saldo');
                   }
                   $rsAtributos->proximo();
               }
               if ($nuQuantidadeAtributo > $nuSaldoAtributo) {
                   return "alertaAviso('@Quantidade informada (".number_format($nuQuantidadeAtributo, 4, ',', '.').") ultrapassou o saldo do atributo (".number_format($nuSaldoAtributo, 4, ',', '.').").','form','erro','".Sessao::getId()."', '../');";
               }
               $nuValor += $nuQuantidadeAtributo;
           }
       }
    }

    $nuQuantidade = $arValoresSessao[$_REQUEST['inIDPos']]['quantidade'];
    $nuQuantidade = str_replace(",", ".", str_replace(".", "" ,$nuQuantidade) );
    if ($boTemAtributos) {
       if ($nuValor != $nuQuantidade) {
           return "alertaAviso('@Quantidade informada (".number_format($nuValor, 4, ',', '.').") esta diferente da quantidade (".number_format($nuQuantidade, 4, ',', '.').").','form','erro','".Sessao::getId()."', '../');";
       }
    }
}

$arValoresSessao = Sessao::read('Valores');

switch ($stCtrl) {
    case "preencheSpanDadosItem":
         list($inCodItem, $inCodMarca, $inCodCentro) = explode('-',$_REQUEST['boDetalharItem']);

         foreach ($arValoresSessao as $campo => $valor) {
            if ($arValoresSessao[$campo]["cod_item"] == $inCodItem &&
                 $arValoresSessao[$campo]["cod_marca"] == $inCodMarca &&
                 $arValoresSessao[$campo]["cod_centro"] == $inCodCentro) {
                 $arDadosItem = $arValoresSessao[$campo];
                 $stJs .= 'document.frm.inIDPos.value = '.$campo.';';
            }
         }

         $stJs .= preencheSpanDadosItem($arDadosItem, $_REQUEST['inAlmoxarifadoOrigem'], $_REQUEST['stAcao']);
    break;
    case "preencheSpanListaItens":
        $stJs .= preencheSpanListaItens($arValoresSessao, $_REQUEST['stAcao']);
    break;
    case "preencheSpanListaLotes":
        $inId = $_POST['inIDPos'];
        $stJs .= preencheSpanListaLotes($arValoresSessao[$inId]['ValoresLotes'], $_REQUEST['stAcao']);
    break;
    case "preencheSpanAlteraLotes":
        $inId = $_POST['inIDPos'];
        $stJs .= preencheSpanAlteraLotes($arValoresSessao[$inId]['ValoresLotes']);
    break;
    case "incluirLote":
       $inIdItem = $_REQUEST['inIDPos'];
       $stLote   = $_REQUEST['stLote'];

       for ($i=0;$i<count($arValoresSessao[$inIdItem]['ValoresLotes']);$i++) {
            if ($arValoresSessao[$inIdItem]['ValoresLotes'][$i]['lote'] == $stLote) {
               $boRepetido = true;
            }
       }
       if ($_REQUEST['btnIncluirLote'] == 'Alterar') {
            foreach ($arValoresSessao[$inIdItem]['ValoresLotes'] as $campo => $valor) {

               if ($arValoresSessao[$inIdItem]['ValoresLotes'][$campo]["lote"] == $_REQUEST['stLote']) {
                    $arValoresSessao[$inIdItem]['ValoresLotes'][$campo]["lote"]  = $_REQUEST['stLote'];
                    $arValoresSessao[$inIdItem]['ValoresLotes'][$campo]["quantidade"] = $_REQUEST['nmQtdLote'];
               }
            }

           $stJs .= 'document.frm.btnIncluirLote.value = "Incluir";';
           $stJs .= 'document.frm.btnLimparLote.value = "Limpar";';
       } else {
          if ($boRepetido) {
              $js = "alertaAviso('Não é possível inserir um lote já existente na lista.','form','erro','".Sessao::getId()."');";
              echo $js;
          } else {
               $nuQuantidadeLote =  str_replace(",", ".", str_replace(".", "" ,$_REQUEST['nmQtdLote']));
               $nuSaldoLote =  str_replace(",", ".", str_replace(".", "" ,$_REQUEST['hdnnmSaldoLote']));

               if ($nuSaldoLote < $nuQuantidadeLote)
                   $stRet = "alertaAviso('@Quantidade informada (".number_format($nuQuantidadeLote, 4, ',', '.').") ultrapassou o saldo do lote (".number_format($nuSaldoLote, 4, ',', '.').").','form','erro','".Sessao::getId()."', '../');";
               if ($stRet != null) {
                   $stJs .= $stRet;
               } else {
                   $arValores = Sessao::read('Valores') ;
                   $inIdC = count($arValores[$inIdItem]['ValoresLotes']);

                   $arLote["inId"]         = $inIdC;
                   $arLote["lote"]         = $_REQUEST['stLote'];
                   $arLote["quantidade"]   = $_REQUEST['nmQtdLote'];
                   $arLote["dt_validade"]  = $_REQUEST['hdndtValidade'];
                   $arLote["dt_fabricacao"]= $_REQUEST['hdndtFabricacao'];
                   $arLote["saldo"]        = $_REQUEST['hdnnmSaldoLote'];

                   $arValoresSessao[$inIdItem]['ValoresLotes'][] = $arLote;
               }
           }
        }

        Sessao::write('Valores', $arValoresSessao);
        $stJs .= preencheSpanListaLotes($arValoresSessao[$inIdItem]['ValoresLotes'], $_REQUEST['stAcao']);
        $stJs .= "LimparLote();";
    break;
    case "preencheDadosLote":
        $stLote = $_REQUEST['stLote'];
        if ($stLote != "") {
            $inCodItem  = $arValoresSessao[ $_REQUEST['inIDPos'] ]["cod_item"];
            $inCodMarca = $arValoresSessao[ $_REQUEST['inIDPos'] ]["cod_marca"];
            $inCodCentro= $arValoresSessao[ $_REQUEST['inIDPos'] ]["cod_centro"];

            $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel;
            $obTAlmoxarifadoPerecivel->setDado('lote', $stLote);
            $obTAlmoxarifadoPerecivel->setDado('cod_item', $inCodItem);
            $obTAlmoxarifadoPerecivel->setDado('cod_marca', $inCodMarca);
            $obTAlmoxarifadoPerecivel->setDado('cod_centro', $inCodCentro);
            $obTAlmoxarifadoPerecivel->setDado('cod_almoxarifado', $_REQUEST['inAlmoxarifadoOrigem']);
            $obTAlmoxarifadoPerecivel->recuperaPorChave($rsPerecivel);

            $stJs = "d.getElementById('dtValidade').innerHTML = '".$rsPerecivel->getCampo('dt_validade')."';";
            $stJs .= "d.getElementById('dtFabricacao').innerHTML = '".$rsPerecivel->getCampo('dt_fabricacao')."';";
            $stJs .= "d.getElementById('hdndtValidade').value = '".$rsPerecivel->getCampo('dt_validade')."';";
            $stJs .= "d.getElementById('hdndtFabricacao').value = '".$rsPerecivel->getCampo('dt_fabricacao')."';";
            $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel;
            $obTAlmoxarifadoPerecivel->setDado('lote', $stLote);
            $obTAlmoxarifadoPerecivel->setDado('cod_item'  ,       $rsPerecivel->getCampo('cod_item'));
            $obTAlmoxarifadoPerecivel->setDado('cod_marca' ,       $rsPerecivel->getCampo('cod_marca'));
            $obTAlmoxarifadoPerecivel->setDado('cod_centro',       $rsPerecivel->getCampo('cod_centro'));
            $obTAlmoxarifadoPerecivel->setDado('cod_almoxarifado', $rsPerecivel->getCampo('cod_almoxarifado'));
            $obTAlmoxarifadoPerecivel->recuperaSaldoLote($rsSaldoLote);

            $stJs .= "d.getElementById('nmSaldoLote').innerHTML = '".number_format($rsSaldoLote->getCampo('saldo_lote'), 4, ',', '.')."';";
            $stJs .= "d.getElementById('hdnnmSaldoLote').value = '".number_format($rsSaldoLote->getCampo('saldo_lote'), 4, ',', '.')."';";

        }
    break;
    case "ExcluirLote":
        $arTemp = array();
        $arElementos = array();
        $inIdItem = $_REQUEST['inIDPos'];

        $inCount=0;
         foreach ($arValoresSessao[$inIdItem]['ValoresLotes'] as $campo => $valor) {
            if ($arValoresSessao[$inIdItem]['ValoresLotes'][$campo]["inId"] != $_REQUEST['inId']) {
                foreach ($arValoresSessao[$inIdItem]['ValoresLotes'][$campo] as $key=>$value) {
                    $arElementos[$inCount][ $key ]    = $value;
                }
                $arElementos[$inCount][ 'inId' ]  = $inCount;
                $inCount++;
            }
         }
        $arValoresSessao[$inIdItem]['ValoresLotes'] = $arElementos;
        $stJs .= preencheSpanListaLotes($arValoresSessao[$inIdItem]['ValoresLotes'], $_REQUEST['stAcao']);
    break;
    case "AlterarQuantidadeLote":
        $stRet = validaQuantidadeLote();
        if ($stRet != null) {
            $stJs .= $stRet;
        } else {
            foreach ($arValoresSessao[ $_REQUEST['inIDPos'] ]['ValoresLotes'] as $campo => $valor) {
                $nuQtdLote = $_REQUEST[ 'nmQtdLoteLista_'.($campo+1) ];
                $nuQtdLote = str_replace(",", ".", str_replace(".", "" ,$nuQtdLote ) );
                $nuQtdLote = number_format($nuQtdLote, 4, ',', '.');
                $arValoresSessao[ $_REQUEST['inIDPos'] ]['ValoresLotes'][$campo][ 'quantidade' ] = $nuQtdLote;
            }
            $stJs .= "d.getElementById('Ok').disabled = true;";
        }
    break;
    case "AlteraLote":
         $arTemp = array();
         $arElementos = array();

         $inIdItem = $_REQUEST['inIDPos'];

         foreach ($arValoresSessao[$inIdItem]['ValoresLotes'] as $campo => $valor) {
            if ($arValoresSessao[$inIdItem]['ValoresLotes'][$campo]["inId"] == $_REQUEST['inId']) {
                 $stLote = $arValoresSessao[$inIdItem]['ValoresLotes'][$campo]["lote"];
                 $nmSaldo = $arValoresSessao[$inIdItem]['ValoresLotes'][$campo]["saldo"];
                 $nmQuantidade = $arValoresSessao[$inIdItem]['ValoresLotes'][$campo]["quantidade"];
                 $dtValidade= $arValoresSessao[$inIdItem]['ValoresLotes'][$campo]["dt_validade"];
                 $dtFabricacao= $arValoresSessao[$inIdItem]['ValoresLotes'][$campo]["dt_fabricacao"];
            }
         }

         $stJs = "d.getElementById('stLote').value = '".$stLote."';";
         $stJs .= "d.getElementById('dtValidade').innerHTML = '".$dtValidade."';";
         $stJs .= "d.getElementById('dtFabricacao').innerHTML = '".$dtFabricacao."';";
         $stJs .= "d.getElementById('hdndtValidade').value = '".$dtValidade."';";
         $stJs .= "d.getElementById('hdndtFabricacao').value = '".$dtFabricacao."';";
         $stJs .= "d.getElementById('nmSaldoLote').innerHTML = '".$nmSaldo."';";
         $stJs .= "d.getElementById('hdnnmSaldoLote').value = '".$nmSaldo."';";
         $stJs .= "d.getElementById('nmQtdLote').value = '".$nmQuantidade."';";

         $stJs .= "desabilitaIncluirLote();";
         $stJs .= 'document.frm.btnIncluirLote.value = "Alterar";';
         $stJs .= 'document.frm.btnLimparLote.value = "Cancelar";';
    break;

    case "AlterarItem":

        $id = $_REQUEST['inIDPos'];
        $stRet = validaQuantidadeLote();
        $stRet = validaQuantidadeAtributos();
        if ($stRet != null) {
            $stJs = $stRet;
        } else {
            if ($arValoresSessao) {
                foreach ($arValoresSessao as $campo => $valor) {
                    if ($arValoresSessao[$campo]["inId"] == $id) {

                        $nuQuantidade = 0.0000;
                        for ($inCount=0;$inCount<count($arValoresSessao[$campo]['ValoresLotes']);$inCount++) {
                            $nuQuantidade    += str_replace(",", ".", str_replace(".", "" ,$arValoresSessao[$campo]['ValoresLotes'][$inCount]["quantidade"] ) );
                        }
                        $rsAtributos = unserialize($arValoresSessao[$campo]["ValoresAtributos"]);
                       foreach ($_REQUEST as $key=>$value) {
                           if (strstr($key, 'nmQtdAtributo_')) {
                           list($stIgnore, $inCodSequencial) = explode('_', $key);
                           for ($i=0;$i<count($rsAtributos->arElementos);$i++) {
                                 if($rsAtributos->arElementos[$i]['cod_sequencial'] == $inCodSequencial)
                                    $rsAtributos->arElementos[$i]['quantidade'] = str_replace(',', '.', str_replace('.', '', $value));
                           }
                           }
                       }
                        $Valores[$campo]["ValoresAtributos"] = $rsAtributos;
                    }
                }
            }
//print_r($sessao->transf['Valores']);

            Sessao::write('Valores', $Valores);
            $stJs .= preencheSpanListaItens($Valores, $_REQUEST['stAcao']);
            $stJs .= "d.getElementById('Ok').disabled = false;";
        }
    break;
}

if ( $stJs )
   echo $stJs;

?>
