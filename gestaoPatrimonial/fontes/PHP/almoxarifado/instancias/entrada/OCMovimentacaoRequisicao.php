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
   * Página de Formulario de Inclusao/Alteracao do CID
   * Data de Criação: 04/01/2006

   * @author Analista: Diego Victoria
   * @author Desenvolvedor: Leandro André Zis

   * Casos de uso: uc-03.03.11

   $Id: OCMovimentacaoRequisicao.php 59612 2014-09-02 12:00:51Z gelson $
   */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoRequisicao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoRequisicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

function preencheSpanDadosItem($arDadosItem,$inCodRequisicao,$inCodAlmoxarifado)
{
   global $stAcao;

   $nmSaldoAtendido    = $arDadosItem['saldo_atend'];
   $nmSaldoRequisitado = $arDadosItem['saldo_req'];
   $nmSaldoAtual       = $arDadosItem['saldo_atual'];
   $nmQuantidade       = $arDadosItem['quantidade'];
   $stComplemento      = $arDadosItem['complemento'];
   $inCodVeiculo       = $arDadosItem['inCodVeiculo'];
   $nmKm               = $arDadosItem['nmKm'];
   $boItemFrota        = $arDadosItem['boItemFrota'];

   $boPerecivel = $arDadosItem['perecivel'];
   $inId = $arDadosItem['inId'];

   $rsAtributos = new RecordSet;

   $obFormulario = new Formulario();

   $obHdnInIDPos = new Hidden;
   $obHdnInIDPos->setName  ('inIDPos');
   $obHdnInIDPos->setValue ($inId);

   $obHdnSaldoAtendido = new Hidden;
   $obHdnSaldoAtendido->setName ('nmSaldoAtendido');
   $obHdnSaldoAtendido->setValue ( $nmSaldoAtendido );

   $obHdnSaldoRequisitado = new Hidden;
   $obHdnSaldoRequisitado->setName ('nmSaldoRequisitado');
   $obHdnSaldoRequisitado->setValue ( $nmSaldoRequisitado );

   $obHdnSaldoAtual = new Hidden;
   $obHdnSaldoAtual->setName ('nmSaldoAtual');
   $obHdnSaldoAtual->setValue ( $nmSaldoAtual);

   $obRAlmoxarifadoRequisicao = new RAlmoxarifadoRequisicao();
   $obRAlmoxarifadoRequisicao->setExercicio( Sessao::getExercicio() );
   $obRAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->setCodigo($inCodAlmoxarifado);
   $obRAlmoxarifadoRequisicao->setCodigo($inCodRequisicao);
   $obRAlmoxarifadoRequisicao->addRequisicaoItem();
   $obRAlmoxarifadoRequisicao->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo($arDadosItem['cod_item']);
   $obRAlmoxarifadoRequisicao->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->setCodigo($arDadosItem['cod_marca']);
   $obRAlmoxarifadoRequisicao->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->setCodigo($arDadosItem['cod_centro']);
   $obRAlmoxarifadoRequisicao->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->obRCadastroDinamico->setChavePersistenteValores(
        array("cod_item"         => $arDadosItem['cod_item'          ],
              "cod_centro"       => $arDadosItem['cod_centro'        ],
              "cod_marca"        => $arDadosItem['cod_marca'         ],
              "cod_almoxarifado" => $inCodAlmoxarifado,
              "cod_catalogo"     => $arDadosItem['inCodCatalogo'     ],
              "cod_classificacao"=> $arDadosItem['inCodClassificacao'] ) );

   $obRAlmoxarifadoRequisicao->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->obRCadastroDinamico->recuperaAtributosSelecionadosValores($rsAtributos);
   $obMontaAtributos = new MontaAtributos();
   $obMontaAtributos->setTitulo('Atributos do Item no Almoxarifado');
   $obMontaAtributos->setName  ('Atributos_');
   $obMontaAtributos->setLabel(true);
   $obMontaAtributos->setRecordSet( $rsAtributos );

   if ($boPerecivel) {
      if ($stAcao == 'entrada') {
         $obSpanListaLotes = new Span();
         $obSpanListaLotes->setId("spnListaLotes");
      } else {
         $obSpanAlteraLotes = new Span();
         $obSpanAlteraLotes->setId("spnAlteraLotes");
      }
   }
   $obLblSaldoAtual = new Label();
   $obLblSaldoAtual->setRotulo('Saldo Atual');
   $obLblSaldoAtual->setValue( $nmSaldoAtual );

   $obLblSaldoRequisitado = new Label();
   $obLblSaldoRequisitado->setRotulo ( 'Saldo Requisitado');
   $obLblSaldoRequisitado->setId     ( 'nmSaldoRequisitado');
   $obLblSaldoRequisitado->setValue  ( $nmSaldoRequisitado );

   $obLblSaldoAtentido = new Label();
   $obLblSaldoAtentido->setRotulo ( 'Saldo Atendido' );
   $obLblSaldoAtentido->setValue ( $nmSaldoAtendido );

   $obTxtQuantidade= new Numerico();
   $obTxtQuantidade->setId('nmQuantidade');
   $obTxtQuantidade->setName('nmQuantidade');
   if ($stAcao == 'entrada') {
      $obTxtQuantidade->setRotulo ( 'Quantidade a Devolver');
      $obTxtQuantidade->setTitle ( 'Informe a quantidade a devolver.');
   } else {
      $obTxtQuantidade->setRotulo ( 'Quantidade de Saída');
      $obTxtQuantidade->setTitle ( 'Informe a quantidade de saída.');
   }
   $obTxtQuantidade->setDecimais(4);
   $obTxtQuantidade->setNull(false);
   $obTxtQuantidade->setValue($nmQuantidade);

   $obTxtComplemento = new TextArea();
   $obTxtComplemento->setId('stComplemento');
   $obTxtComplemento->setName('stComplemento_'.$inId );
   $obTxtComplemento->setRotulo ( 'Complemento do Item');
   $obTxtComplemento->setTitle  ( 'Informe o complemento do item.');
   $obTxtComplemento->setValue  ( $stComplemento );

   if ($boItemFrota == true) {
        $obForm = new Form;
        $obForm->setAction( "FMMovimentacaoRequisicao.php" );

        include_once( CAM_GP_FRO_COMPONENTES."IPopUpVeiculo.class.php" );
        $obIPopUpVeiculo = new IPopUpVeiculo($obForm);
        $obIPopUpVeiculo->setObrigatorioBarra( true );
        $obIPopUpVeiculo->setNull( false );
        $obIPopUpVeiculo->obCampoCod->setValue( $inCodVeiculo );

        if ($inCodVeiculo) {
            include_once CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculo.class.php";
            $obTFrotaVeiculo = new TFrotaVeiculo;
            $obTFrotaVeiculo->setDado('cod_veiculo', $inCodVeiculo);
            $obTFrotaVeiculo->recuperaVeiculoSintetico($rsVeiculo);
            $stNomVeiculo =  $rsVeiculo->getCampo('nom_modelo');
        }

        $obNumKm = new Numerico();
        $obNumKm->setRotulo   ( '**Quilometragem'                     );
        $obNumKm->setTitle    ( 'Informe a quilometragem do veículo.' );
        $obNumKm->setName     ( 'nmKm'                                );
        $obNumKm->setId       ( 'nmKm'                                );
        $obNumKm->setValue    ( $nmKm                                 );
        $obNumKm->setDecimais ( 1                                     );
        $obNumKm->setNegativo ( false                                 );

        $obHdnBoItemFrota = new Hidden;
        $obHdnBoItemFrota->setName  ( 'boItemFrota' );
        $obHdnBoItemFrota->setValue ( $boItemFrota  );

        $obHdnStAltera = new Hidden;
        $obHdnStAltera->setName     ( 'stAltera'    );
        $obHdnStAltera->setValue    (  true         );
   }

   $obBtnAlterar = new Button;
   $obBtnAlterar->setName ( "btnAlterar" );
   $obBtnAlterar->setValue( "Alterar" );
   $obBtnAlterar->setTipo ( "button" );
   $obBtnAlterar->obEvento->setOnClick( "document.frm.Ok.disabled = false; AlterarItemFrota();" );

   $obBtnLimpar = new Button;
   $obBtnLimpar->setName( "btnLimpar" );
   $obBtnLimpar->setValue( "Limpar" );
   $obBtnLimpar->setTipo( "button" );
   $obBtnLimpar->obEvento->setOnClick( "document.frm.Ok.disabled = false; buscaDado('limpaDadosItem');" );

   $obFormulario->addHidden($obHdnInIDPos);
   $obFormulario->addHidden($obHdnSaldoAtendido);
   $obFormulario->addHidden($obHdnSaldoRequisitado);
   $obFormulario->addHidden($obHdnSaldoAtual);
   $obMontaAtributos->geraFormulario($obFormulario);
   if ($boPerecivel) {
      if ($stAcao == 'entrada') {
         $obFormulario->addSpan($obSpanListaLotes);
      } else {
         $obFormulario->addSpan($obSpanAlteraLotes);
      }
   }

   $obSpnListaLote = new Span();
   $obSpnListaLote->setId("spnListaLotes");

   $obFormulario->addTitulo("Quantidades");
   $obFormulario->addComponente( $obLblSaldoAtual       );
   $obFormulario->addComponente( $obLblSaldoRequisitado );
   $obFormulario->addComponente( $obLblSaldoAtentido    );
   $obFormulario->addSpan      ( $obSpnListaLote        );

   if ($stAcao == 'entrada') {
      $obFormulario->addTitulo('Dados de Entrada do Item');
   } else {
      $obFormulario->addTitulo('Dados de Saída do Item');
   }
   $obFormulario->addComponente( $obTxtComplemento );
   if ($boItemFrota == true) {
      $obFormulario->addTitulo('Dados do Veículo');
      $obFormulario->addComponente( $obIPopUpVeiculo  );
      $obFormulario->addComponente( $obNumKm          );
      $obFormulario->addHidden    ( $obHdnBoItemFrota );
      $obFormulario->addHidden    ( $obHdnStAltera    );
   }
   $obFormulario->defineBarra(array($obBtnAlterar, $obBtnLimpar), "left", "");
   $obFormulario->montaInnerHtml();

   $stJs  = "d.getElementById('spnDadosItem').innerHTML = '".$obFormulario->getHtml()."';";
   if ($boItemFrota == true) {
      $stJs .= "d.getElementById('".$obIPopUpVeiculo->getId()."').innerHTML = '".$stNomVeiculo."'";
   }
   if ($boPerecivel) {
      $arrayValores = Sessao::read('Valores');
     if ($arrayValores) {
         $stJs .= preencheSpanListaLotes($arrayValores[$inId]['ValoresLotes']);
     }
   }

   return $stJs;
}

function preencheSpanListaItens($arRecordSet)
{
   global $stAcao;

   $obLista = new Lista;
   $obLista->setMostraPaginacao( false );
   $obLista->setTitulo ( '');

   $rsItens = new RecordSet();
   $rsItens->preenche( $arRecordSet );

   $rsItens->addFormatacao('desc_marca', 'STRIPSLASHES');
   $rsItens->addFormatacao('desc_centro', 'STRIPSLASHES');
   $rsItens->addFormatacao('desc_unidade', 'STRIPSLASHES');
   $rsItens->addFormatacao('desc_item', 'STRIPSLASHES');
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
   $obLista->ultimoCabecalho->addConteudo( 'Centro de Custo' );
   $obLista->ultimoCabecalho->setWidth( 5 );
   $obLista->commitCabecalho();

   $obLista->addCabecalho();
   $obLista->ultimoCabecalho->addConteudo( 'Saldo Atual' );
   $obLista->ultimoCabecalho->setWidth( 5 );
   $obLista->commitCabecalho();

   $obLista->addCabecalho();
   $obLista->ultimoCabecalho->addConteudo( 'Saldo Requisitado' );
   $obLista->ultimoCabecalho->setWidth( 5 );
   $obLista->commitCabecalho();

   $obLista->addCabecalho();
   $obLista->ultimoCabecalho->addConteudo( 'Saldo Atendido' );
   $obLista->ultimoCabecalho->setWidth( 5 );
   $obLista->commitCabecalho();

   $obLista->addCabecalho();
   if ($stAcao == 'entrada') {
       $obLista->ultimoCabecalho->addConteudo( 'Quantidade a Devolver' );
   } else {
       $obLista->ultimoCabecalho->addConteudo( 'Quantidade de Saída' );
   }
   $obLista->ultimoCabecalho->setWidth( 5 );
   $obLista->commitCabecalho();

   // if ($stAcao == "saida") {
       $obLista->addCabecalho();
       $obLista->ultimoCabecalho->addConteudo( 'Desdobramento para lançamento' );
       $obLista->ultimoCabecalho->setWidth( 10 );
       $obLista->commitCabecalho();
   // }

   $obLista->addCabecalho();
   if ($stAcao == 'entrada') {
      $obLista->ultimoCabecalho->addConteudo( 'Devolver' );
   } else {
      $obLista->ultimoCabecalho->addConteudo( 'Detalhar' );
   }
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
   $obLista->ultimoDado->setCampo( "[cod_centro]-[desc_centro]" );
   $obLista->commitDado();

   $obLista->addDado();
   $obLista->ultimoDado->setAlinhamento("DIREITA");
   $obLista->ultimoDado->setCampo( "saldo_atual" );
   $obLista->commitDado();

   $obLista->addDado();
   $obLista->ultimoDado->setAlinhamento("DIREITA");
   $obLista->ultimoDado->setCampo( "saldo_req" );
   $obLista->commitDado();

   $obLista->addDado();
   $obLista->ultimoDado->setAlinhamento("DIREITA");
   $obLista->ultimoDado->setCampo( "saldo_atend" );
   $obLista->commitDado();

   $obQuantidade = new Quantidade();
   $obQuantidade->setValue( "quantidade"   );
   $obQuantidade->setName ( "nuQuantidade" );
   $obQuantidade->setNull ( false );
   $obQuantidade->setSize ( 10    );
   $obQuantidade->setId   ( "" );

   $obLista->addDadoComponente( $obQuantidade );
   $obLista->ultimoDado->setCampo( '[quantidade]' );
   $obLista->ultimoDado->setAlinhamento( "CENTRO" );
   $obLista->commitDadoComponente();
   $inQtd=1;

   while ( !$rsItens->eof() ) {
        if( $rsItens->getCampo('perecivel') )
            $stJsQtd .= "f.".$obQuantidade->getName()."_".($inQtd++).".readOnly='readonly';";
               $rsItens->proximo();
   }
   $rsItens->setPrimeiroElemento();

   //monta combo para o lançamento contábil do item
   // if ($stAcao == "saida") {
     include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoContaDespesaItem.class.php";
     include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";
     $obROrcamentoDespesa = new ROrcamentoDespesa;
     $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao('3.3.9.0.30');
     $obROrcamentoDespesa->listarCodEstruturalDespesa($rsContaDespesa, " AND conta_despesa.cod_estrutural <> '3.3.9.0.30.00.00.00.00' ORDER BY conta_despesa.cod_estrutural");

     $obCmbDesdobramento = new Select;
     $obCmbDesdobramento->setName( "inCodContaDespesa" );
     $obCmbDesdobramento->setCampoID( "cod_conta" );
     $obCmbDesdobramento->setCampoDesc( "cod_estrutural" );
     $obCmbDesdobramento->setValue( "cod_conta_despesa" );
     $obCmbDesdobramento->addOption( "", "Selecione" );
     $obCmbDesdobramento->preencheCombo($rsContaDespesa);

     $obLista->addDadoComponente( $obCmbDesdobramento );
     $obLista->ultimoDado->setCampo( '[cod_conta_despesa]' );
     $obLista->ultimoDado->setAlinhamento( "CENTRO" );
     $obLista->commitDadoComponente();
     $inQtd=1;

     while ( !$rsItens->eof() ) {
        $boOk = true;
        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem = new TContabilidadeConfiguracaoLancamentoContaDespesaItem;
        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado('cod_item', $rsItens->getCampo('cod_item'));
        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado('exercicio', Sessao::getExercicio());
        $boOk = $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->consultarItem();
        if ($boOk) {
          $stJsLancamento .= "f.".$obCmbDesdobramento->getName()."_".($inQtd).".disabled='disabled';";
          $stJsLancamento .= "f.".$obCmbDesdobramento->getName()."_".($inQtd).".value=".$obTContabilidadeConfiguracaoLancamentoContaDespesaItem->getDado('cod_conta_despesa').";";
          $stJsLancamento .= "var input = d.createElement('input');";
          $stJsLancamento .= "input.setAttribute('type', 'hidden');";
          $stJsLancamento .= "input.setAttribute('name', '".$obCmbDesdobramento->getName()."_".($inQtd)."_hidden');";
          $stJsLancamento .= "input.setAttribute('id', '".$obCmbDesdobramento->getName()."_".($inQtd)."_hidden');";
          $stJsLancamento .= "input.setAttribute('value', '".$obTContabilidadeConfiguracaoLancamentoContaDespesaItem->getDado('cod_conta_despesa')."');";
          $stJsLancamento .= "d.getElementById('spnListaItens').appendChild(input);";
        }

        $inQtd++;
        $rsItens->proximo();
     }
     $rsItens->setPrimeiroElemento();
   // }

   $obRdnDetalharItem = new Radio();
   $obRdnDetalharItem->setValue( "[cod_item]-[cod_marca]-[cod_centro]" );
   $obRdnDetalharItem->setName( "boDetalharItem" );
   $obRdnDetalharItem->setNull( false );
   $obRdnDetalharItem->obEvento->setOnClick("DetalharItem();");

   $obLista->addDadoComponente( $obRdnDetalharItem, false );
   $obLista->ultimoDado->setAlinhamento( "CENTRO" );
   $obLista->commitDadoComponente();

   $obLista->montaHTML();
   $stHTML = $obLista->getHTML();
   $stHTML = str_replace( "\n" ,"" ,$stHTML );
   $stHTML = str_replace( "  " ,"" ,$stHTML );
   $stHTML = str_replace( "'","\\'",$stHTML );

   $stJs = "d.getElementById('spnListaItens').innerHTML = '".$stHTML."';";
   $stJs .= "d.getElementById('spnDadosItem').innerHTML = '&nbsp';";
   $stJs .= $stJsQtd;
   $stJs .= $stJsLancamento;

   return $stJs;
}

function preencheSpanAlteraLotes($arRecordSet)
{
   if (count($arRecordSet) > 0) {
       global $pgOcul;
       global $pgProc;
       $obFormulario = new Formulario();

       $rsLotes = new RecordSet;
       $rsLotes->preenche($arRecordSet);

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
       $obCmbLote->obEvento->setOnChange( 'PreencheDadosLote();' );

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

       $obBtnIncluir = new Button;
       $obBtnIncluir->setName ( "btnIncluirLote" );
       $obBtnIncluir->setValue( "Incluir" );
       $obBtnIncluir->setTipo ( "button" );
       $obBtnIncluir->obEvento->setOnClick( "IncluirLote();" );

       $obBtnLimpar = new Button;
       $obBtnLimpar->setName( "btnLimparLote" );
       $obBtnLimpar->setValue( "Limpar" );
       $obBtnLimpar->setTipo( "button" );
       $obBtnLimpar->obEvento->setOnClick( "LimparLote()" );

       $obFormulario->addComponente($obCmbLote);
       $obFormulario->addComponente($obLblDataFabricacao);
       $obFormulario->addComponente($obLblDataValidade);
       $obFormulario->addComponente($obLblSaldoLote);
       $obFormulario->addComponente($obTxtQtdLote);
       $obFormulario->defineBarra(array($obBtnIncluir, $obBtnLimpar), "left", "");
       $obFormulario->addSpan($obSpnListaLote);
       $obFormulario->montaInnerHtml();
       $stHTML = $obFormulario->getHTML();
       $stJs.= "d.getElementById('spnAlteraLotes').innerHTML = '".$stHTML."';";
       $stJs.= "d.frm.stCtrl.value = 'preencheSpanListaLotes';";
       $stJs.= "d.frm.action = '".$pgOcul."?".Sessao::getId()."&inId=".$inId."';";
       $stJs.= "d.frm.target = 'oculto';";
       $stJs.= "d.frm.submit();";
       $stJs.= "d.frm.action = '".$pgProc."?".Sessao::getId()."&inId=".$inId."';";
   }

   return $stJs;
}

function preencheSpanListaLotes($arRecordSet)
{
   if (count($arRecordSet) > 0) {
       $arRecordSetLotes = array();
       if (count($arRecordSet) > 0) {
          foreach ($arRecordSet as $chave =>$dados) {
             $saldo = str_replace(',','.',str_replace('.','',$dados["saldo"]));
             if ($saldo > 0) {
                $arRecordSetLotes[] = $arRecordSet[$chave];
             }
          }
       }

       $obLista = new Lista;
       $obLista->setMostraPaginacao( false );
       $obLista->setTitulo ( '');

       $rsItens = new RecordSet();
       $rsItens->preenche( $arRecordSetLotes );
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

       $obTxtQtdLote = new Numerico;
       $obTxtQtdLote->setId       ("nmQtdLoteLista");
       $obTxtQtdLote->setName     ("nmQtdLoteLista");
       $obTxtQtdLote->setSize     ( 14 );
       $obTxtQtdLote->setMaxLength( 14 );
       $obTxtQtdLote->setDecimais ( 4 );
       $obTxtQtdLote->setValue    ( "quantidade" );

       $obTxtQtdLote->obEvento->setOnBlur("buscaDado('AlterarQuantidadeLote');");

       $obLista->addDadoComponente( $obTxtQtdLote );
       $obLista->ultimoDado->setCampo( "nmQtdLote" );
       $obLista->commitDadoComponente();

       $obLista->montaHTML();
       $stHTML = $obLista->getHTML();
       $stHTML = str_replace( "\n" ,"" ,$stHTML );
       $stHTML = str_replace( "  " ,"" ,$stHTML );
       $stHTML = str_replace( "'","\\'",$stHTML );

       $stJs  = "d.getElementById('spnListaLotes').innerHTML = '".$stHTML."';";
       $stJs .= "d.getElementById('Ok').disabled = false;";
       $stJs .= "f.stComplemento.value = '';";
       $stJs .= "f.nmKm.value = '';";
       $stJs .= "f.inCodVeiculo.value = '';";
       $stJs .= "d.getElementById('stNomVeiculo').innerHTML = '';";
   }

   return $stJs;
}

function limpaDadosItem()
{
   $stJs  = "f.stComplemento.value = '';";
   $stJs .= "f.nmKm.value = '';";
   $stJs .= "f.inCodVeiculo.value = '';";
   $stJs .= "d.getElementById('stNomVeiculo').innerHTML = '';";

   return $stJs;
}

function validaQuantidadeLote($nuVlrSoma=0)
{
   $arValores = Sessao::read('Valores');

   if ($arValores[$_REQUEST['inIDPos']]['ValoresLotes']) {

       $nuValor = 0;
       $inCount = 0;

       $arAux = array();

       $i = 1;

       foreach ($arValores[$_REQUEST['inIDPos']]['ValoresLotes'] as $chave => $valor) {
          if ($valor['saldo'] > 0) {
             $arAux[$i] = $valor;
             $i++;
          }
       }
       foreach ($_POST as $key=>$value) {

          if ( strstr($key,'nmQtdLoteLista_') ) {

             $indice = explode('_',$key);

             if ($arAux[$indice[1]]['saldo'] > 0) {
                $inLote = $arAux[$indice[1]]['lote'];

                $nuValorInformado = str_replace(",", ".", str_replace(".", "" ,$value) );
                $nuValor += $nuValorInformado;

                $nuSaldoLote = $arAux[$indice[1]]['saldo'];
                $nuSaldoLote = str_replace(",", ".", str_replace(".", "", $nuSaldoLote));

                if( $nuValorInformado > $nuSaldoLote )

                   return "alertaAviso('@Quantidade informada (".number_format($nuValorInformado, 4, ',', '.').") ultrapassou o saldo (".number_format($nuSaldoLote, 4, ',', '.').").','form','erro','".Sessao::getId()."', '../');";
             }
             $inCount++;
          }
       }

       $nuSaldoReq = $arValores[ $_POST['inIDPos'] ] ['saldo_req'];
       $nuSaldoReq = str_replace(",", ".", str_replace(".", "" ,$nuSaldoReq ) );

       $nuSaldoAte = $arValores[ $_POST['inIDPos'] ] ['saldo_atend'];
       $nuSaldoAte = str_replace(",", ".", str_replace(".", "" ,$nuSaldoAte ) );

       $devolucao = Sessao::read('devolucao');

       if ($devolucao) {
           $nuSaldoReqAte = $nuSaldoAte;
       } else {
           $nuSaldoReqAte = $nuSaldoReq - $nuSaldoAte;
       }

       if ($nuValor > $nuSaldoReqAte) {
           if ($devolucao) {
               return "alertaAviso('@Quantidade informada (".number_format($nuValor, 4, ',', '.').") ultrapassou o saldo atendido (".number_format($nuSaldoReqAte, 4, ',', '.').").','form','erro','".Sessao::getId()."', '../');";
           } else {
               return "alertaAviso('@Quantidade informada (".number_format($nuValor, 4, ',', '.').") ultrapassou o saldo requisitado-atendido (".number_format($nuSaldoReqAte, 4, ',', '.').").','form','erro','".Sessao::getId()."', '../');";
           }
       } else {

           $Valores = $arValores[ $_POST['inIDPos'] ] ['saldo_atual'];
           $nuSaldoAtual = str_replace(",", ".", str_replace(".", "", $Valores) );
           if ($nuValor > $nuSaldoAtual) {
               if (!$devolucao) {
                   return "alertaAviso('@Quantidade informada (".number_format($nuValor, 4, ',', '.').") ultrapassou o saldo do item  (".number_format($nuSaldoAtual, 4, ',', '.').").','form','erro','".Sessao::getId()."', '../');";
               } else {
                   return;
               }
           } else {
               return;
           }
       }
   }
}

$arrayValores = Sessao::read('Valores');

switch ($stCtrl) {
    case "preencheSpanDadosItem":
      if (is_array($arrayValores) ) {
         foreach ($arrayValores as $campo => $valor) {
            if ($arrayValores[$campo]['cod_item'] == $_REQUEST['inCodItem']) {
               $arDadosItem = $arrayValores[$campo];
               }
         }
      } else {
         $arrayValores = array();
      }
      $stJs .= preencheSpanDadosItem($arDadosItem, $inCodRequisicao, $inCodAlmoxarifado);
    break;

    case "preencheSpanListaItens":
        $stJs .= preencheSpanListaItens($arrayValores);
    break;

    case "limpaDadosItem":
        $stJs .= limpaDadosItem();

    case "preencheSpanListaLotes":
        $inId = $_POST['inIDPos'];
        $stJs .= preencheSpanListaLotes($arrayValores[$inId]['ValoresLotes']);
    break;
    case "preencheSpanAlteraLotes":
        $inId = $_POST['inIDPos'];
        $stJs .= preencheSpanAlteraLotes($arrayValores[$inId]['ValoresLotes']);
    break;
    case "incluirLote":
       $boRepetido = false;
       $inIdItem = $_POST['inIDPos'];
       for ($i=0;$i<count($arrayValores[$inIdItem]['ValoresLotes']);$i++) {
            if ($arrayValores[$inIdItem]['ValoresLotes'][$i]['lote'] == $stLote) {
               $boRepetido = true;
            }
       }
       if ($boRepetido) {
           $js = "alertaAviso('Não pode existir mais de um lote igual.','form','erro','".Sessao::getId()."');";
           sistemaLegado::executaFrameOculto($js);
       } else {
            $stRet = validaQuantidadeLote( $_REQUEST['nmQtdLote'] );

            if ($stRet != null) {
                $stJs .= $stRet;
            } else {

                $inIdC = count($arrayValores[$inIdItem]['ValoresLotes']);

                $arLote["inId"]         = $inIdC;
                $arLote["lote"]         = $_POST['stLote'];
                $arLote["quantidade"]   = $_POST['nmQtdLote'];
                $arLote["dt_validade"]  = $_REQUEST['dtValidade'];
                $arLote["dt_fabricacao"]= $_REQUEST['dtFabricacao'];
                $arLote["saldo"]        = $_REQUEST['nmSaldoLote'];

                $arrayValores[$inIdItem]['ValoresLotes'][] = $arLote;

                $stJs .= preencheSpanListaLotes($arrayValores[$inIdItem]['ValoresLotes']);
                $stJs .= "f.stLote.value = '';\n";
                $stJs .= "d.getElementById('dtFabricacao').innerHTML = '&nbsp';\n";
                $stJs .= "d.getElementById('dtValidade').innerHTML = '&nbsp';\n";
                $stJs .= "d.getElementById('nmSaldoLote').innerHTML = '&nbsp';\n";
                $stJs .= "f.nmQtdLote.value = '';\n";
            }
        }
    break;
    case "preencheDadosLote":

        if ($stLote != "") {
            $inCodItem  = $arrayValores[ $_POST['inIDPos'] ]["cod_item"];
            $inCodMarca = $arrayValores[ $_POST['inIDPos'] ]["cod_marca"];
            $inCodCentro= $arrayValores[ $_POST['inIDPos'] ]["cod_centro"];
            $obRAlmoxarifadoEstoqueItem = new RAlmoxarifadoEstoqueItem();
            $obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo($inCodItem);
            $obRAlmoxarifadoEstoqueItem->obRMarca->setCodigo($inCodMarca);
            $obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->setCodigo($inCodCentro);
            $obRAlmoxarifadoEstoqueItem->obRAlmoxarifado->setCodigo($inCodAlmoxarifado);
            $obRAlmoxarifadoEstoqueItem->addPerecivel();
            $obRAlmoxarifadoEstoqueItem->roUltimoPerecivel->setLote($stLote);
            $obRAlmoxarifadoEstoqueItem->roUltimoPerecivel->consultar();
            $obRAlmoxarifadoEstoqueItem->roUltimoPerecivel->retornaSaldoLote($inSaldoLote);
            $stJs = "d.getElementById('dtValidade').innerHTML = '".$obRAlmoxarifadoEstoqueItem->roUltimoPerecivel->getDataValidade()."';";
            $stJs .= "d.getElementById('dtFabricacao').innerHTML = '".$obRAlmoxarifadoEstoqueItem->roUltimoPerecivel->getDataFabricacao()."';";
            $stJs .= "d.getElementById('nmSaldoLote').innerHTML = '".$inSaldoLote."';";

            sistemaLegado::ExecutaFrameOculto($stJs);
        }
    break;
    case "excluirLote":
        $arTemp = array();
        $arElementos = array();
        $inIdItem = $_POST['inIDPos'];
        $inCount=0;

         foreach ($arrayValores[$inIdItem]['ValoresLotes'] as $campo => $valor) {
            if ($arrayValores[$inIdItem]['ValoresLotes'][$campo]["inId"] != $_REQUEST['inId']) {
                foreach ($arrayValores[$inIdItem]['ValoresLotes'][$campo] as $key=>$value) {
                    $arElementos[$inCount][ $key ]    = $value;
                    Sessao::getExercicio();
                }
                $arElementos[$inCount][ 'inId' ]  = $inCount;
                $inCount++;
            }
         }
        $arrayValores[$inIdItem]['ValoresLotes'] = $arElementos;
        $stJs .= preencheSpanListaLotes($arrayValores[$inIdItem]['ValoresLotes']);
    break;
    case "AlterarQuantidadeLote":
        $stRet = validaQuantidadeLote();
        if ($stRet != null) {
            $stJs .= $stRet;
        } else {
            $inCount = 1;
            foreach ($arrayValores[ $_POST['inIDPos'] ]['ValoresLotes'] as $campo => $valor) {
                if ($arrayValores[ $_POST['inIDPos'] ]['ValoresLotes'][$campo][ 'saldo' ] > 0) {
                  $nuQtdLote = $_POST[ 'nmQtdLoteLista_'.($inCount) ];
                  $nuQtdLote = str_replace(",", ".", str_replace(".", "" ,$nuQtdLote ) );
                  $nuQtdLote = number_format($nuQtdLote, 4, ',', '.');
                  $arrayValores[ $_POST['inIDPos'] ]['ValoresLotes'][$campo][ 'quantidade' ] = $nuQtdLote;
                  $inCount++;
                }
            }
            $stJs .= "d.getElementById('Ok').disabled = true;";
        }
    break;

    case "AlterarLote":

         $inIdItem = $_POST['inIDPos'];
         foreach ($arrayValores[$inIdItem]['ValoresLotes'] as $campo => $valor) {
            if ($arrayValores[$inIdItem]['ValoresLotes'][$campo]["inId"] == $idLote) {
                  $arrayValores[$inIdItem]['ValoresLotes'][$campo]["lote"]  = $_POST['stLote'];
                  $arrayValores[$inIdItem]['ValoresLotes'][$campo]["quantidade"] = $_POST['nmQtdLote'];
            }
         }
        $stJs .= preencheSpanListaLotes($arrayValores[$inIdItem]['ValoresLotes']);
        $stJs .= 'document.btnIncluirLote.value = "Incluir"';
        $stJs .= 'document.btnLimparLote.value = "Cancelar"';
    break;

    case "AlterarItemFrota":
        if ($_REQUEST['boItemFrota']) {
            if ($_REQUEST['inCodVeiculo']) {
                if ($_REQUEST['nmKm']) {
                    if ($_REQUEST['boItemFrota'] == true && $_REQUEST['stAltera'] == true) {
                        if (count($arrayValores) > 1) {
                            foreach ($arrayValores as $chave =>$dados) {
                                if ($_REQUEST['inCodVeiculo'] == $dados['inCodVeiculo'] && $_REQUEST['nmKm'] != $dados['nmKm']) {
                                    $boExecuta = true;
                                    $_REQUEST['stAltera'] == false;
                                    $stMsg  = 'Já possui itens na lista para este veículo com quilometragem \"'.$dados['nmKm'].'\". Deseja alterar ';
                                    $stMsg .= 'os demais itens da lista que utilizam este veículo para a quilometragem \"'.$_REQUEST['nmKm'].'\" informada neste item?';
                                    $stJs  .= "confirmPopUp('Saída por Requisição','".$stMsg."','AlterarItem()');";
                                }
                            }
                        }
                    }
                } else {
                    $boExecuta = true;
                    $stJs = "alertaAviso('Informe a quilometragem do veículo.','form','erro','".Sessao::getId()."', '../');";
                }
            } else {
                 $boExecuta = true;
                 $stJs = "alertaAviso('Informe o veículo.','form','erro','".Sessao::getId()."', '../');";
            }
        }
        if (!$boExecuta) {
            $stJs  = "jq_('#stCtrl').val('AlterarItem');";
            $stJs .= "jq_('#frm').attr('action','".$pgOcul."?".Sessao::getId()."&inId=".$inId."');";
            $stJs .= "jq_('#frm').submit();";
            $stJs .= "jq_('#frm').attr('action','".$pgProc."?".Sessao::getId()."&inId=".$inId."');";
        }
    break;

    case "AlterarItem":

            $stRet = validaQuantidadeLote();
            if ($stRet != null) {
                $stJs = $stRet;
            } else {
                $ind = 0;

                while ( $ind < count( $arrayValores ) ) {
                    if ($arrayValores[$ind]['inCodVeiculo'] == $_REQUEST['inCodVeiculo']) {
                        $arrayValores[$ind]['nmKm'] = $_REQUEST['nmKm'];
                    }
                    if ($_REQUEST['inIDPos'] == $ind) {
                        $nuQuantidade = 0;
                        if (count( $arrayValores[$ind]['ValoresLotes'])>0 ) {
                            foreach ($arrayValores[$ind]['ValoresLotes'] as $key=>$value) {
                                $nuQuantidade = bcadd($nuQuantidade ,str_replace(',','.',str_replace('.','',$_REQUEST['nmQtdLoteLista_'.($key+1)])),4);
                            }
                            $nuQuantidade = number_format($nuQuantidade,4,',','.');
                        } else {
                            $nuQuantidade = str_replace(',','.',str_replace('.','',$_REQUEST['nuQuantidade_'.($ind+1)]));
                            $saldoAtual = str_replace(',','.',str_replace('.','',$arrayValores[$ind]['saldo_atual']));
                            if ($_REQUEST['stAcao'] == "saida") {
                               $stRet = validaQuantidadeRequisicao($nuQuantidade, $saldoAtual, $ind+1);
                            }
                            $nuQuantidade = number_format($nuQuantidade,4,',','.');
                        }
                        if ($stRet != null) {
                            $stJs = $stRet;
                        } else {
                            $arrayValores[$ind]['quantidade'] = $nuQuantidade;
                            if ($_REQUEST['inIDPos'] == $ind) {
                                $arrayValores[$ind]['complemento']  = $_REQUEST['stComplemento_'.$ind];
                                $arrayValores[$ind]['inCodVeiculo'] = $_REQUEST['inCodVeiculo'];
                                $arrayValores[$ind]['nmKm']         = $_REQUEST['nmKm'];
                            }
                        }
                    }
                    $ind++;
                }
                $stJs .= "d.getElementById('Ok').disabled = false;";
                $stJs .= "d.getElementById('spnDadosItem').innerHTML = '&nbsp';";
            }
    break;
}

Sessao::write('Valores', $arrayValores);

function validaQuantidadeRequisicao($quantidade, $saldoAtual, $indice)
{

   $valorQuantidade = str_replace('.','',$quantidade);
   $valorQuantidade = str_replace(',','.',$valorQuantidade);
   $valorSaldoAtual = str_replace('.','',$saldoAtual);
   $valorSaldoAtual = str_replace(',','.',$valorSaldoAtual);

   if ($valorQuantidade  > $valorSaldoAtual) {
     return "alertaAviso('@Quantidade informada no item ".$indice." (".number_format($quantidade, 4, ',', '.').") ultrapassou o saldo do item  (".number_format($saldoAtual, 4, ',', '.').").','form','erro','".Sessao::getId()."', '../');";
   } else {
      return;
   }
}

if ( $stJs )
   sistemaLegado::executaFrameOculto($stJs);
?>
