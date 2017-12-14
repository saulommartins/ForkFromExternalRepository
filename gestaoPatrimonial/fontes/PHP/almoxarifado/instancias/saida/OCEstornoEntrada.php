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
    * Página Oculta
    * Data de Criação   : 08/06/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * Casos de uso: uc-03.03.17

    $Id: OCMovimentacaoDiversa.php 34628 2008-10-18 12:22:59Z luiz $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterialEstorno.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoMarca.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCusto.class.php";

$stCtrl = $_REQUEST['stCtrl'];

function montaDetalheItem($inIdItem,$boAlterar=false)
{
    $pgOcul = "OCEstornoEntrada.php";
    $arSessao = Sessao::read("sessao");

    if ($boAlterar) {

        //$arSessao['selecionado']['inIdItem']        = null;
        $arSessao['selecionado']['inIdItemEstorno'] = $inIdItem;
        $arSessao['selecionado']['inCodItem']       = $arSessao['itensEstorno']['item'][$inIdItem]['cod_item'];
        $arSessao['selecionado']['inCodMarca']      = $arSessao['itensEstorno']['item'][$inIdItem]['cod_marca'];
        $arSessao['selecionado']['inCodCentro']     = $arSessao['itensEstorno']['item'][$inIdItem]['cod_centro'];
        $arSessao['selecionado']['boPerecivel']     = $arSessao['itensEstorno']['item'][$inIdItem]['perecivel'];

        $obLbLMarca = new Label();
        $obLbLMarca->setRotulo ( 'Marca');
        $obLbLMarca->setId ('inCodMarca');
        $obLbLMarca->setValue( $arSessao['itensEstorno']['item'][$inIdItem]['cod_marca']."-".$arSessao['itensEstorno']['item'][$inIdItem]['desc_marca'] );

        $obLbLCentro = new Label();
        $obLbLCentro->setRotulo ( 'Centro');
        $obLbLCentro->setId ('inCodCentro');
        $obLbLCentro->setValue( $arSessao['itensEstorno']['item'][$inIdItem]['cod_centro']."-".$arSessao['itensEstorno']['item'][$inIdItem]['desc_centro'] );

        $stJustificativa    = $arSessao['itensEstorno']['item'][$inIdItem]['justificativa'];
        $nuQuantidade       = $arSessao['itensEstorno']['item'][$inIdItem]['quantidade'];

        $stJsComplemento  = "montaParametrosGET('carregaDetalhesCentro&inCodCentro=".$arSessao['selecionado']['inCodCentro']."&alteracao=true' );";
        //$stJsComplemento .= preencheSpanListaLotes( $arSessao['itensEstorno']['item'][$inIdItem]['pereciveis'] );

    } else {

        $inCodItem = $arSessao['itensDisponiveis'][$inIdItem]['cod_item'];
        $arSessao['selecionado']['inIdItem']        = $inIdItem;
        $arSessao['selecionado']['inIdItemEstorno'] = null;
        $arSessao['selecionado']['inCodItem']       = $inCodItem;
        $arSessao['selecionado']['inCodMarca']      = null;
        $arSessao['selecionado']['inCodCentro']     = null;
        $arSessao['selecionado']['boPerecivel']     = $arSessao['itensDisponiveis'][$inIdItem]['perecivel'];

        $obTLancamentoEstorno = new TAlmoxarifadoLancamentoMaterialEstorno();
        $obTLancamentoEstorno->setDado('stExercicio'       , $arSessao['selecionado']['stExercicioLancamento'] );
        $obTLancamentoEstorno->setDado('inCodAlmoxarifado' , $arSessao['selecionado']['inCodAlmoxarifado']     );
        $obTLancamentoEstorno->setDado('inNumLancamento'   , $arSessao['selecionado']['inNumLancamento']       );
        $obTLancamentoEstorno->setDado('inCodNatureza'     , $arSessao['selecionado']['inCodNatureza']         );
        $obTLancamentoEstorno->setDado('stTipoNatureza'    , $arSessao['selecionado']['stTipoNatureza']        );
        $obTLancamentoEstorno->setDado('inCodItem'         , $arSessao['selecionado']['inCodItem']             );
        $obTLancamentoEstorno->listarMarcaCentro($rsMarca);

        # Caso só tenha uma marca e um centro de custo vinculados ao item, sugere ao usuário.
        if (count($rsMarca->arElementos) == 1) {
            $inCodMarca = $rsMarca->arElementos[0]['cod_marca'];
            $stJs .= "montaParametrosGET('carregaCentroCusto&inCodMarca=$inCodMarca'); ";
        }

        $obCmbMarca = new Select;
        $obCmbMarca->setRotulo    ( 'Marca' );
        $obCmbMarca->setTitle     ( 'Selecione a marca do item.');
        $obCmbMarca->setName      ( 'inCodMarca' );
        $obCmbMarca->setID        ( 'inCodMarca' );
        $obCmbMarca->setValue     ( $inCodMarca  );
        $obCmbMarca->setCampoID   ( 'cod_marca'  );
        $obCmbMarca->setCampoDesc ( '[cod_marca]-[desc_marca]'  );
        $obCmbMarca->addOption    ( "", "Selecione" );
        $obCmbMarca->obEvento->setOnChange( "montaParametrosGET('carregaCentroCusto&'+this.name+'='+this.value);" );
        $obCmbMarca->preencheCombo( $rsMarca );

        $obCmbCentroCusto = new Select;
        $obCmbCentroCusto->setRotulo    ( 'Centro de Custo' );
        $obCmbCentroCusto->setTitle     ( 'Selecione o centro de custo do item.');
        $obCmbCentroCusto->setName      ( 'inCodCentro' );
        $obCmbCentroCusto->setID        ( 'inCodCentro' );
        $obCmbCentroCusto->setValue     ( $inCodCentro  );
        $obCmbCentroCusto->setCampoID   ( 'cod_centro'  );
        $obCmbCentroCusto->setCampoDesc ( '[cod_centro]-[desc_centro]'  );
        $obCmbCentroCusto->addOption    ( "", "Selecione" );
        $obCmbCentroCusto->obEvento->setOnChange( "montaParametrosGET('carregaDetalhesCentro&'+this.name+'='+this.value".$stParam." );" );
    }

    $obTxtJustificativa = new TextArea;
    $obTxtJustificativa->setId    ('stJustificativa');
    $obTxtJustificativa->setName  ('stJustificativa' );
    $obTxtJustificativa->setRotulo('Justificativa');
    $obTxtJustificativa->setTitle ('Informe a justificativa.');
    $obTxtJustificativa->setValue ($stJustificativa );
    $obTxtJustificativa->setNull  (false);

    $obSpnAtributos = new Span;
    $obSpnAtributos->setId("spnAtributos");

    $obSpnListaLotes = new Span;
    $obSpnListaLotes->setId("spnListaLotes");

    $obLbLSaldoEstornado = new Label;
    $obLbLSaldoEstornado->setRotulo ('Quantidade Estornada');
    $obLbLSaldoEstornado->setId     ('nuSaldoEstornado');

    $obLbLSaldo = new Label;
    $obLbLSaldo->setRotulo ('Saldo em Estoque');
    $obLbLSaldo->setId     ('nuSaldo');

    $obTxtQtd = new Numerico;
    $obTxtQtd->setId('nuQuantidade');
    $obTxtQtd->setName('nuQuantidade');
    $obTxtQtd->setRotulo ( '* Quantidade');
    $obTxtQtd->setTitle ( 'Informe a quantidade');
    $obTxtQtd->setDecimais(4);
    $obTxtQtd->setValue( $nuQuantidade );
    $obTxtQtd->setReadOnly( ($arSessao['selecionado']['boPerecivel'] == 't') );

    if ($boAlterar) {
        $obBtnIncluirAlterar = new Button;
        $obBtnIncluirAlterar->setName ( "btnAlterar" );
        $obBtnIncluirAlterar->setValue( "Alterar" );
        $obBtnIncluirAlterar->setTipo ( "button" );
        $obBtnIncluirAlterar->obEvento->setOnClick( "AlterarItem(".$inIdItem.");" );

        $obBtnLimpar = new Button;
        $obBtnLimpar->setName( "btnLimpar" );
        $obBtnLimpar->setValue( "Limpar" );
        $obBtnLimpar->setTipo( "button" );
        #$obBtnLimpar->obEvento->setOnClick( "montaParametrosGET( 'limparItemSpn','' );" );
        $stJsLimpar  = "$('stJustificativa').value='';";
        $stJsLimpar .= "$('nuQuantidade').value='';";
        $obBtnLimpar->obEvento->setOnClick( $stJsLimpar );
    } else {
        $obBtnIncluirAlterar = new Button;
        $obBtnIncluirAlterar->setName ( "btnIncluir" );
        $obBtnIncluirAlterar->setValue( "Incluir" );
        $obBtnIncluirAlterar->setTipo ( "button" );
        $obBtnIncluirAlterar->obEvento->setOnClick( "IncluirItem();" );

        $obBtnLimpar = new Button;
        $obBtnLimpar->setName( "btnLimpar" );
        $obBtnLimpar->setValue( "Limpar" );
        $obBtnLimpar->setTipo( "button" );
        $obBtnLimpar->obEvento->setOnClick( "montaParametrosGET( 'detalharItem&inIdItem=".$inIdItem."');" );
    }

    $obFormulario = new Formulario;
    $obFormulario->addTitulo      ( "Detalhes do Item" );

    if ($boAlterar) {
        $obFormulario->addComponente( $obLbLMarca );
        $obFormulario->addComponente( $obLbLCentro );
    } else {
        $obFormulario->addComponente( $obCmbMarca );
        $obFormulario->addComponente( $obCmbCentroCusto );
    }

    $obFormulario->addComponente( $obTxtJustificativa );
    if ($arSessao['selecionado']['boPerecivel'] == 't') {
        $obFormulario->addSpan 	    ( $obSpnListaLotes    );
    }
    $obFormulario->addSpan 	    ( $obSpnAtributos      );
    $obFormulario->addComponente( $obLbLSaldoEstornado );
    $obFormulario->addComponente( $obLbLSaldo          );
    $obFormulario->addComponente( $obTxtQtd            );
    $obFormulario->defineBarra(array($obBtnIncluirAlterar, $obBtnLimpar), "left", "");

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= " d.getElementById('spnDetalhes').innerHTML = '". $stHtml. "';\n";
    $stJs .= $stJsComplemento;

    Sessao::write('sessao',array());
    Sessao::write("sessao",$arSessao);

    return $stJs;
}

function montaCentroCusto($inCodMarca)
{
    if (!empty($inCodMarca)) {
        $pgOcul = "OCEstornoEntrada.php";
        $arSessao = Sessao::read("sessao");

        $arSessao['selecionado']['inCodMarca']  = $inCodMarca;
        $arSessao['selecionado']['inCodCentro'] = null;

        # Atualiza o array na sessão.
        Sessao::write("sessao", $arSessao);

        $obTLancamentoEstorno = new TAlmoxarifadoLancamentoMaterialEstorno();
        $obTLancamentoEstorno->setDado('stExercicio'       , $arSessao['selecionado']['stExercicioLancamento'] );
        $obTLancamentoEstorno->setDado('inCodAlmoxarifado' , $arSessao['selecionado']['inCodAlmoxarifado'] );
        $obTLancamentoEstorno->setDado('inNumLancamento'   , $arSessao['selecionado']['inNumLancamento'] );
        $obTLancamentoEstorno->setDado('inCodNatureza'     , $arSessao['selecionado']['inCodNatureza'] );
        $obTLancamentoEstorno->setDado('stTipoNatureza'    , $arSessao['selecionado']['stTipoNatureza'] );
        $obTLancamentoEstorno->setDado('inCodItem'         , $arSessao['selecionado']['inCodItem'] );
        $obTLancamentoEstorno->setDado('inCodMarca'        , $arSessao['selecionado']['inCodMarca'] );
        $obTLancamentoEstorno->listarMarcaCentro($rsCentro);

        # Caso só tenha um Centro de Custo, sugere ao usuário.
        if (count($rsCentro->arElementos[0]['cod_centro']) == 1) {
            $boSelected = 'true';
            # Busca as informações de Saldo por Centro de Custo.
            $stJs .= montaDetalhesCentro($rsCentro->arElementos[0]['cod_centro'], 'false');
            $inCodCentro = $rsCentro->getCampo('cod_centro');
        }

        $stCombo = "inCodCentro";
        $stJs .= "limpaSelect(f.$stCombo,1);";

        while ( !$rsCentro->eof() ) {
            $stJs .= "f.".$stCombo."[".$rsCentro->getCorrente()."] = new Option('".$rsCentro->getCampo('cod_centro')."-".$rsCentro->getCampo('desc_centro')."','".$rsCentro->getCampo('cod_centro')."', '".$boSelected."');\n";
            $rsCentro->proximo();
        }
         $stJs .= "f.".$stCombo.".value = '".$inCodCentro."'";
    } else {
        $stCombo = "inCodCentro";
        $stJs .= "limpaSelect(f.$stCombo,1);";
        # Limpa os spans de saldo.
        $stJs .= "jQuery('#nuSaldoEstornado').html('&nbsp;'); ";
        $stJs .= "jQuery('#nuSaldo').html('&nbsp;'); ";
    }

    return $stJs;
}

function montaDetalhesCentro($inCodCentro, $stAlteracao)
{
    $arSessao = Sessao::read("sessao");
    $arSessao['selecionado']['inCodCentro'] = $inCodCentro;

    $obTLancamentoEstorno = new TAlmoxarifadoLancamentoMaterialEstorno;
    $obTLancamentoEstorno->setDado('inCodAlmoxarifado' , $arSessao['selecionado']['inCodAlmoxarifado'] );
    $obTLancamentoEstorno->setDado('stExercicio'       , $arSessao['selecionado']['stExercicioLancamento'] );
    $obTLancamentoEstorno->setDado('inCodNatureza'     , $arSessao['selecionado']['inCodNatureza'] );
    $obTLancamentoEstorno->setDado('stTipoNatureza'    , $arSessao['selecionado']['stTipoNatureza'] );
    $obTLancamentoEstorno->setDado('inCodItem'         , $arSessao['selecionado']['inCodItem'] );
    $obTLancamentoEstorno->setDado('inCodMarca'        , $arSessao['selecionado']['inCodMarca'] );
    $obTLancamentoEstorno->setDado('inCodCentro'       , $arSessao['selecionado']['inCodCentro'] );
    $obTLancamentoEstorno->listarMarcaCentro($rsQuantidade);

    $obTLancamentoEstorno->setDado('inNumLancamento'   , $arSessao['selecionado']['inNumLancamento'] );
    $obTLancamentoEstorno->listarSaldoEstornoLancamento($rsSaldoEstorno);

    $nuQuantidade = $rsQuantidade->getCampo('quantidade');
    $stQuantidade = number_format ( $nuQuantidade, 4, ",", ".");

    $nuQuantidadeEstornado = $rsSaldoEstorno->getCampo('saldo_estornado');
    $stQuantidadeEstornado = number_format ( $nuQuantidadeEstornado, 4, ",", ".");

    $arSessao['selecionado']['saldo'] = $nuQuantidade;

    $obTLancamentoEstorno->listarPereciveis($rsPereciveis);

    $arSessao['pereciveisTMP'] = $rsPereciveis->getElementos();

    Sessao::write('sessao',array());
    Sessao::write("sessao",$arSessao);

    # Preenche o Label com o Saldo Estornado (Quantidade de Entrada - Quantidade Estornada)
    $stJs .= "jq('#nuSaldoEstornado').html('$stQuantidadeEstornado'); ";

    # Preenche o Label com o Saldo que pode ser estornado.
    $stJs .= "jq('#nuSaldo').html('$stQuantidade'); ";

    if ($stAlteracao == 'true') {
        $stJs .= preencheSpanListaLotes( $arSessao['itensEstorno']['item'][ $arSessao['selecionado']['inIdItemEstorno'] ]['pereciveis'] );
    } else {
        $stJs .= preencheSpanListaLotes( $arSessao['pereciveisTMP'] );
    }

    return $stJs;
}

function preencheSpanListaLotes($arRecordSet)
{
   $obLista = new Lista;
   $obLista->setAlternado(true);
   $obLista->setMostraPaginacao( false );
   $obLista->setTitulo ( '');

   $rsRecordSet = new RecordSet();
   $rsRecordSet->preenche( $arRecordSet );

   $rsRecordSet->addFormatacao('quantidade', 'NUMERIC_BR_4');
   $rsRecordSet->addFormatacao('saldo', 'NUMERIC_BR_4');

   $obLista->setRecordSet( $rsRecordSet );

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
   $obLista->ultimoCabecalho->addConteudo( 'Quantidade Entrada' );
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

   $obLista->addDado();
   $obLista->ultimoDado->setAlinhamento("DIREITA");
   $obLista->ultimoDado->setCampo( "quantidade" );
   $obLista->commitDado();

   $obTxtQtdLote = new Numerico;
   $obTxtQtdLote->setId       ("nmQtdLoteLista_[cod_lancamento]");
   $obTxtQtdLote->setName     ("nmQtdLoteLista_[cod_lancamento]");
   $obTxtQtdLote->setSize     ( 14 );
   $obTxtQtdLote->setMaxLength( 14 );
   $obTxtQtdLote->setDecimais ( 4 );
   $obTxtQtdLote->setValue    ( "quantidade_informada" );

   $obTxtQtdLote->obEvento->setOnBlur("buscaDado('AlterarQuantidadeLote');");

   $obLista->addDadoComponente( $obTxtQtdLote );
   $obLista->ultimoDado->setCampo( "nmQtdLote" );
   $obLista->commitDadoComponente();

   $obLista->montaHTML();
   $stHTML = $obLista->getHTML();
   $stHTML = str_replace( "\n" ,"" ,$stHTML );
   $stHTML = str_replace( "  " ,"" ,$stHTML );
   $stHTML = str_replace( "'","\\'",$stHTML );

   $stJs  = "jq('#spnListaLotes').html('".$stHTML."'); ";

   return $stJs;
}

function montaAtributosItem()
{
    include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterialEstorno.class.php");
    $arSessao = Sessao::read('sessao');
    $arElementos = array();

    $inIdItem = $arSessao['selecionado']['inIdItem'];

    $inCodItem  = $arSessao['selecionado']['inCodItem'];
    $inCodMarca = $arSessao['selecionado']['inCodMarca'];
    $inCodCentro= $arSessao['selecionado']['inCodCentro'];

    if ($inCodCentro) {

        $obTLancamentoMaterialEstorno = new TAlmoxarifadoLancamentoMaterialEstorno();
        $obTLancamentoMaterialEstorno->setDado('inCodAlmoxarifado',$arSessao['selecionado']['inCodAlmoxarifado']);
        $obTLancamentoMaterialEstorno->setDado('inCodCentro'    , $inCodCentro);
        $obTLancamentoMaterialEstorno->setDado('inCodMarca'     , $inCodMarca);
        $obTLancamentoMaterialEstorno->setDado('inCodItem'      , $inCodItem);
        $obTLancamentoMaterialEstorno->setDado('stExercicio'    , $arSessao['selecionado']['stExercicioLancamento'] );
        $obTLancamentoMaterialEstorno->setDado('inNumLancamento', $arSessao['selecionado']['inNumLancamento'] );
        $obTLancamentoMaterialEstorno->setDado('inCodNatureza'  , $arSessao['selecionado']['inCodNatureza'] );
        $obTLancamentoMaterialEstorno->setDado('stTipoNatureza' , $arSessao['selecionado']['stTipoNatureza'] );

        $obTLancamentoMaterialEstorno->listarAtributosValores( $rsAtributosValores );
        //$arSessao['itensEstorno']['item'][$inIdItem]['atributos'] = $rsAtributosValores->getElementos();

        $arElementos= $rsAtributosValores->getElementos();
    }

    if ( count($arElementos) > 0 ) {
        $rsAtributosValores = new RecordSet;
        for ($inCount=0; $inCount<count($arElementos); $inCount++) {

            $inCodAtributo = $arElementos[$inCount]['cod_atributo'];
            $inCodCadastro = $arElementos[$inCount]['cod_cadastro'];

            //if (is_array($arSessao['itensEstorno']['item'][$inIdItem]['detalhes']["$inCodMarca-$inCodCentro"]['atributos_valores'][$inCodAtributo.'_'.$inCodCadastro.'_Selecionados'])) {
            if (true) {
                $arValor = $arSessao['itensEstorno']['item'][$inIdItem]['detalhes']["$inCodMarca-$inCodCentro"]['atributos_valores'][$inCodAtributo.'_'.$inCodCadastro.'_Selecionados'];
                //foreach ($arValor as $keyValor => $ValorDados) {

                    //$arElementos[$inCount]['valor'] .= $ValorDados;
                    $ValorDados = $arElementos[$inCount]['valor'];
                    $stDescricao = SistemaLegado::pegaDado('valor_padrao','administracao.atributo_valor_padrao'," where cod_valor IN (".$ValorDados.") and cod_atributo = ".$inCodAtributo);
                    $arElementos[$inCount]['valor_desc'] = $stDescricao;

                    if (($stDescricao) || ($ValorDados)) {
                        $arValor_padrao = explode(',',$arElementos[$inCount]['valor_padrao']);
                        $arValor_padrao_desc = explode('[][][]',$arElementos[$inCount]['valor_padrao_desc']);
                        foreach ($arValor_padrao as $chaveValor => $DadosVal) {

                            if ($arValor_padrao_desc[$chaveValor] == $stDescricao) {
                                unset($arValor_padrao_desc[$chaveValor]);
                                unset($arValor_padrao[$chaveValor]);
                            }

                        }
                        $stValor_padrao = implode(',',$arValor_padrao);
                        $stValor_padrao_desc = implode('[][][]',$arValor_padrao_desc);

                        $arElementos[$inCount]['valor_padrao'] = $stValor_padrao;
                        $arElementos[$inCount]['valor_padrao_desc'] = $stValor_padrao_desc;
                    }
                    //if (($keyValor < count($arValor)-1)) {
                    //    $arElementos[$inCount]['valor'] .= ",";
                    //    $arElementos[$inCount]['valor_desc'] .= "[][][]";
                    //}
                //}
            } elseif ($arSessao['itensEstorno']['item'][$inIdItem]['detalhes']["$inCodMarca-$inCodCentro"]['atributos_valores'][$inCodAtributo.'_'.$inCodCadastro]) {
                $arElementos[$inCount]['valor'] = $arSessao['itensEstorno']['item'][$inIdItem]['detalhes']["$inCodMarca-$inCodCentro"]['atributos_valores'][$inCodAtributo.'_'.$inCodCadastro];
            }

        }

        $rsAtributosValores->preenche($arElementos);

        $obHdnTipoAtributos = new Hidden;
        $obHdnTipoAtributos->setName     ( "hdnTipoAtributos" );

        $obFormulario = new Formulario;
        $obFormulario->addHidden      ( $obHdnTipoAtributos    );

        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setLabel      (true);
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "Atributos_" );
        $obMontaAtributos->setRecordSet  ( $rsAtributosValores );
        $obMontaAtributos->recuperaValores();

        $obMontaAtributos->geraFormulario ( $obFormulario );

        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();

        $arSessao['atributosTMP'] = $arElementos;
        Sessao::write('sessao',array());
        Sessao::write('sessao', $arSessao);

        $stJs = "$('spnAtributos').innerHTML='$stHTML';";

        return $stJs;
    } else {
        $arSessao['atributosTMP'] = $arElementos;
        Sessao::write('sessao',array());
        Sessao::write('sessao', $arSessao);

        $stJs = "$('spnAtributos').innerHTML='&nbsp;';";
    }

}

function montaSpnItensEstorno()
{
    $arSessao = Sessao::read("sessao");

    $arItens = $arSessao['itensEstorno']['item'];

    $rsItens = new RecordSet();
    if (is_array($arItens)) {
        $rsItens->preenche($arItens);
        $rsItens->addFormatacao('valor_unitario', 'NUMERIC_BR_4');
    }

    $obLista = new Lista;
    $obLista->setAlternado(true);
    $obLista->setTitulo( "Itens a serem estornados" );
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsItens );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Item" );
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Unidade de Medida" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Marca" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Centro Custo" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Qtde. Estorno" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor Unitário" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Ação" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_item]-[descricao_resumida]" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_unidade" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_marca]-[desc_marca]" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_centro]-[desc_centro]" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "quantidade" );
    $obLista->ultimoDado->setAlinhamento( "DIREITA" );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "valor_unitario" );
    $obLista->ultimoDado->setAlinhamento( "DIREITA" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:montaAlterarItem();" );
    $obLista->ultimaAcao->addCampo("0","inIdItem");
    $obLista->commitAcao();
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:excluirItem();" );
    $obLista->ultimaAcao->addCampo("0","inIdItem");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace( "\n", "", $stHtml);
    $stHtml = str_replace( "  ", "", $stHtml);
    $stHtml = str_replace( "'" , "\\'", $stHtml);

    return $stHtml;
}

function montaAlterarItem($inIdItem)
{
    $stJs = montaDetalheItem ($inIdItem, true);

    return $stJs;
}

function alterarQuantidadeLote()
{
    $nuTotal = 0;
    foreach ($_POST as $key=>$value) {
        if ( strstr($key,'nmQtdLoteLista_') ) {
            $stQtdLote = $value;
            $nuQtdLote = str_replace(",", ".", str_replace(".", "", $stQtdLote) );

            $nuTotal += $nuQtdLote;
        }
    }
    $stTotal = number_format ( $nuTotal, 4, ",", ".");

    return "d.getElementById('nuQuantidade').value = '$stTotal';";
}

function validaPereciveis()
{
    $arSessao = Sessao::read("sessao");
    $stErro = null;

    //inclusao
    $inIdItem           = $arSessao['selecionado']['inIdItem'];
    //alteracao
    $inIdItemEstorno    = $arSessao['selecionado']['inIdItemEstorno'];

    if ($inIdItemEstorno == null )
        $arPereciveis = $arSessao['pereciveisTMP'];
    else
        $arPereciveis = $arSessao['itensEstorno']['item'][$inIdItemEstorno]['pereciveis'];

    for ( $inCount=0; $inCount<count($arPereciveis); $inCount++) {
        $seqLista = $inCount+1;
        $stQtdLote = $_REQUEST[ 'nmQtdLoteLista_'.$seqLista];
        $nuQtdLote = str_replace(",", ".", str_replace(".", "", $stQtdLote) );

        if ($arPereciveis[$inCount]['saldo'] < $nuQtdLote) {
            $stErro = "Lote (".$arPereciveis[$inCount]['lote'].") não possui saldo disponível.";
            break;
        }
        if ($arPereciveis[$inCount]['quantidade'] < $nuQtdLote) {
            $stErro = "Quantidade do lote (".$arPereciveis[$inCount]['lote'].") deve ser menor que a quantidade de entrada.";
            break;
        }
        if ($inIdItemEstorno == null )
            $arSessao['pereciveisTMP'][$inCount]['quantidade_informada'] = $nuQtdLote;
        else
            $arSessao['itensEstorno']['item'][$inIdItemEstorno]['pereciveis'][$inCount]['quantidade_informada'] = $nuQtdLote;
    }
    if ($stErro==null) {
        if ($nuQtdLote <= 0) {
            $stErro = "Quantidade deve ser maior que zero.";
        }
    }

    Sessao::write('sessao',array());
    Sessao::write("sessao",$arSessao);

    return $stErro;
}

function validaItem($inCodItem, $inCodMarca, $inCodCentro, $nuQuantidade, $boAlterar=false, $stJustificativa="")
{
    $arSessao = Sessao::read("sessao");
    $stErro = null;

    if ($arSessao['selecionado']['boPerecivel'] == 't') {
        $stErro = validaPereciveis();
    }
    $arSessao = Sessao::read("sessao");

    if (!$boAlterar && $stErro==null) {
        for ($inCount=0; $inCount<count($arSessao['itensEstorno']['item']); $inCount++) {
            if ($arSessao['itensEstorno']['item'][$inCount]['cod_item']   == $inCodItem
                &&  $arSessao['itensEstorno']['item'][$inCount]['cod_marca']  == $inCodMarca
                &&  $arSessao['itensEstorno']['item'][$inCount]['cod_centro'] == $inCodCentro
            ) {
                $stErro = "Item ($inCodItem), Centro ($inCodCentro), Marca ($inCodMarca) já existe na lista.";
                break;
            }
        }
    }

    $inIdItem = $arSessao['selecionado']['inIdItem'];

    if ($stErro == null) {
        $nuQuantidade = str_replace(",", ".", str_replace(".", "", $nuQuantidade) );

        if ($nuQuantidade <= 0) {
            $stErro = "A quantidade informada deve ser maior que zero.";
        } elseif ($nuQuantidade > $arSessao['selecionado']['saldo']) {
            $stErro = "A quantidade informada não pode ser superior ao saldo.";
        } elseif (($nuQuantidade + $arSessao['itensDisponiveis'][$inIdItem]['qtde_estornada']) > ($arSessao['itensDisponiveis'][$inIdItem]['quantidade'])) {
            $stErro = "A soma da quantidade informada mais o que já foi estornado não pode ser superior a quantidade de entrada.";
        } elseif (empty($stJustificativa)) {
            $stErro = "Informe o campo Justificativa.";
        }
    }

    return $stErro;
}

function limparItemSpn()
{
    $stJs .= "if (f.inCodMarca ) f.inCodMarca.options[0].selected  = 'true';\n";
    $stJs .= "if (f.inCodCentro ) limpaSelect(f.inCodCentro,1);\n";
    $stJs .= "if (jq_('#stJustificativa'))  jq_('#stJustificativa').val('');\n";
    $stJs .= "if (jq_('#nuQuantidade'))     jq_('#nuQuantidade').val('');\n";
    $stJs .= "if (jq_('#nuSaldo'))          jq_('#nuSaldo').html('&nbsp;');\n";
    $stJs .= "if (jq_('#nuSaldoEstornado')) jq_('#nuSaldoEstornado').html('&nbsp;');\n";
    $stJs .= "if (jq_('#spnAtributos'))     jq_('#spnAtributos').html('');\n";
    $stJs .= "if (jq_('#spnListaLotes'))    jq_('#spnListaLotes').html('');\n";
    $stJs .= "if (jq_('#spnDetalhes'))      jq_('#spnDetalhes').html('');\n";

    return $stJs;
}

function limparSessao()
{
    $arSessao = Sessao::read("sessao");

    $arSessao['atributosTMP']   = array();
    $arSessao['pereciveisTMP']  = array();
    Sessao::write('sessao',array());
    Sessao::write("sessao",$arSessao);
}

function incluirItem($inCodCentro, $nuQuantidade, $stJustificativa)
{
    $pgOcul = "OCEstornoEntrada.php";
    $arSessao = Sessao::read("sessao");

    $inIdItem = $arSessao['selecionado']['inIdItem'];
    $arSessao['selecionado']['inCodCentro'] = $inCodCentro;

    $stErro = validaItem ( $arSessao['selecionado']['inCodItem'], $arSessao['selecionado']['inCodMarca'], $arSessao['selecionado']['inCodCentro'], $nuQuantidade, false, $stJustificativa );
    if (!$stErro) {

        //Le novamente o arSessao pois foi alterado nos metodos anteriores.
        $arSessao = Sessao::read("sessao");

        $obTMarca = new TAlmoxarifadoMarca();
        $obTMarca->setDado('cod_marca', $arSessao['selecionado']['inCodMarca'] );
        $obTMarca->consultar();

        $obTCentro = new TAlmoxarifadoCentroCusto();
        $obTCentro->setDado('cod_centro', $arSessao['selecionado']['inCodCentro'] );
        $obTCentro->consultar();
        $inIdProxItem = count( $arSessao['itensEstorno']['item'] );
        $arSessao['itensEstorno']['item'][$inIdProxItem]['inIdItem']            = $inIdProxItem;
        $arSessao['itensEstorno']['item'][$inIdProxItem]['perecivel']           = $arSessao['selecionado']['boPerecivel'];
        $arSessao['itensEstorno']['item'][$inIdProxItem]['cod_item']            = $arSessao['selecionado']['inCodItem'];
        $arSessao['itensEstorno']['item'][$inIdProxItem]['descricao_resumida']  = $arSessao['itensDisponiveis'][$inIdItem]['descricao_resumida'];
        $arSessao['itensEstorno']['item'][$inIdProxItem]['nom_unidade']         = $arSessao['itensDisponiveis'][$inIdItem]['nom_unidade'];
        $arSessao['itensEstorno']['item'][$inIdProxItem]['cod_marca']           = $arSessao['selecionado']['inCodMarca'];
        $arSessao['itensEstorno']['item'][$inIdProxItem]['desc_marca']          = $obTMarca->getDado('descricao');
        $arSessao['itensEstorno']['item'][$inIdProxItem]['cod_centro']          = $arSessao['selecionado']['inCodCentro'];
        $arSessao['itensEstorno']['item'][$inIdProxItem]['desc_centro']         = $obTCentro->getDado('descricao');
        $arSessao['itensEstorno']['item'][$inIdProxItem]['quantidade']          = $nuQuantidade;
        $arSessao['itensEstorno']['item'][$inIdProxItem]['justificativa']       = $stJustificativa;
        $arSessao['itensEstorno']['item'][$inIdProxItem]['valor_unitario']      = $arSessao['itensDisponiveis'][$inIdItem]['valor_unitario'];

        $arSessao['itensEstorno']['item'][$inIdProxItem]['saldo']               = $arSessao['selecionado']['saldo'];
        $arSessao['itensEstorno']['item'][$inIdProxItem]['atributos']           = $arSessao['atributosTMP'];
        $arSessao['itensEstorno']['item'][$inIdProxItem]['pereciveis']          = $arSessao['pereciveisTMP'];

    }

    Sessao::write('sessao',array());
    Sessao::write("sessao",$arSessao);

    return $stErro;
}

function alterarItem($inIdItemEstorno, $nuQuantidade, $stJustificativa)
{
    $arSessao = Sessao::read("sessao");

    $stErro = validaItem ( $arSessao['selecionado']['inCodItem'], $arSessao['selecionado']['inCodMarca'], $arSessao['selecionado']['inCodCentro'], $nuQuantidade, true, $stJustificativa );
    if (!$stErro) {
        //Le novamente o arSessao pois foi alterado nos metodos anteriores.
        $arSessao = Sessao::read("sessao");

        $arSessao['itensEstorno']['item'][$inIdItemEstorno]['quantidade']          = $nuQuantidade;
        $arSessao['itensEstorno']['item'][$inIdItemEstorno]['justificativa']       = $stJustificativa;
    }

    Sessao::write('sessao',array());
    Sessao::write("sessao",$arSessao);

    return $stErro;
}

function excluirItem($inIdItem)
{
    $arSessao = Sessao::read("sessao");

    $arNew = array();
    $inNew = 0;
    for ($inCount=0; $inCount<count($arSessao['itensEstorno']['item']); $inCount++) {
        if ($inCount != $inIdItem) {
            $arNew[$inNew] = $arSessao['itensEstorno']['item'][$inCount];
            $arNew[$inNew]['inIdItem'] = $inNew;
            $inNew++;
        }
    }

    $arSessao['itensEstorno']['item'] = array();
    $arSessao['itensEstorno']['item'] = $arNew;

    Sessao::write('sessao',array());
    Sessao::write("sessao",$arSessao);
}

switch ($stCtrl) {

    case 'detalharItem':
        $stJs = montaDetalheItem($_REQUEST['inIdItem']);
        //carregaAtributosItem();
    break;

    case 'carregaCentroCusto':
        $stJs = montaCentroCusto($_REQUEST['inCodMarca']);
    break;

    case 'carregaDetalhesCentro':
        if (!empty($_REQUEST['inCodCentro'])) {
            $stJs  = montaDetalhesCentro($_REQUEST['inCodCentro'], $_REQUEST['alteracao']);
            $stJs .= montaAtributosItem();
        } else {
            $stJs .= "jQuery('#nuSaldoEstornado').html('&nbsp;'); ";
            $stJs .= "jQuery('#nuSaldo').html('&nbsp;'); ";
        }
    break;

    case 'incluirItem':
        $stErro = incluirItem( $_REQUEST['inCodCentro'], $_REQUEST['nuQuantidade'], $_REQUEST['stJustificativa']);

        if ($stErro) {
            $js = "alertaAviso('".$stErro."','frm','erro','".Sessao::getId()."');";
        } else {
            $stHtml = montaSpnItensEstorno();
            $js  = "d.getElementById('spnItensEstorno').innerHTML = '".$stHtml."';\n";
            $js .= limparItemSpn();
            limparSessao();
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'montaAlterarItem':
        $stJs = montaAlterarItem($_REQUEST['inIdItem']);
    break;

    case 'alterarItem':
        $stErro = alterarItem( $_REQUEST['inIdItemEstorno'], $_REQUEST['nuQuantidade'], $_REQUEST['stJustificativa']);
        if ($stErro) {
            $js = "alertaAviso('".$stErro."','frm','erro','".Sessao::getId()."');";
        } else {
            $stHtml = montaSpnItensEstorno();
            $js  = "d.getElementById('spnItensEstorno').innerHTML = '".$stHtml."';\n";
            $js .= "d.getElementById('spnDetalhes').innerHTML = '';\n";
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'excluirItem':
        excluirItem( $_REQUEST['inIdItem'] );
        $stHtml = montaSpnItensEstorno();
        $stJs = "document.getElementById('spnItensEstorno').innerHTML = '".$stHtml."';\n";
    break;

    case 'limparItemSpn':
        $js = limparItemSpn();
    break;

    case 'AlterarQuantidadeLote':
        $js = alterarQuantidadeLote();
        SistemaLegado::executaFrameOculto($js);
    break;
}

echo $stJs;
