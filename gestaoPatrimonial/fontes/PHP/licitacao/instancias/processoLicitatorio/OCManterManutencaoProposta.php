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
    * Pagina de Oculto
    * Data de Criação   : 20/09/2007

    * @author Desenvolvedor: Andre Almeida
    * @author Analista: Anelise Schwengber

    * @ignore

    * Casos de uso: uc-03.05.25

    $Id: OCManterManutencaoProposta.php 64149 2015-12-09 16:55:40Z michel $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/request/Request.class.php';
include_once CAM_FW_COMPONENTES."Table/TableTree.class.php";
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
include_once TCOM."TComprasConfiguracao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterManutencaoProposta";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;

unset($stJs);

# Função para validar o valor de referência do Item.
function validaVlrRefItem($vlItemUnitario, $vlItemReferencia)
{

    # Busca o tipo do valor de referência.
    $obTConfiguracaoVlReferencia = new TComprasConfiguracao;
    $obTConfiguracaoVlReferencia->setDado( 'cod_modulo' , 35                    );
    $obTConfiguracaoVlReferencia->setDado( 'exercicio'  , Sessao::getExercicio());
    $obTConfiguracaoVlReferencia->setDado( 'parametro' , "tipo_valor_referencia");
    $obTConfiguracaoVlReferencia->recuperaPorChave($rsConfiguracao);
    $stValorReferencia = $rsConfiguracao->getCampo('valor');

    # Conversão para cálculos.
    $vlItemUnitario   = str_replace(",", ".", str_replace(".", "", $vlItemUnitario));
    $vlItemReferencia = str_replace(",", ".", str_replace(".", "", $vlItemReferencia));

    $stErro = "";

    switch ($stValorReferencia) {

        case '10%' :

            # Incrementa 10% no valor de referência.
            $vlItemReferencia = $vlItemReferencia + (($vlItemReferencia * 10) / 100);

            if ($vlItemUnitario > $vlItemReferencia)
                $stErro = "O valor unitário deve ser menor ou igual ao valor de referência mais 10% (R$ ".number_format($vlItemReferencia, 2, ",", ".").")";
        break;

        case 'solicitado' :
            if ($vlItemUnitario > $vlItemReferencia)
                $stErro = "O valor unitário deve ser menor ou igual ao valor de referência (R$ ".number_format($vlItemReferencia, 2, ",", ".").")";
        break;

        case 'livre' :
            $stErro = "";
        break;

    }

    return $stErro;
}

/**
 * Verifica se existe item e participante selecionado,
 * se existir monta form para o usuario entrar com as
 * informações.
 */
function montaDadosItem()
{
    $arManterPropostas = Sessao::read('arManterPropostas');

    $inCodMapa         = $arManterPropostas["cod_mapa"];
    $stExercicioMapa   = $arManterPropostas["exercicio"];
    $inCgmParticipante = $arManterPropostas["participante"];
    $inCodItem         = $arManterPropostas["item"];
    $inLote            = $arManterPropostas["lote"];

    if ( $inCodMapa && $stExercicioMapa && $inCgmParticipante && $inCodItem && ( $inLote || $inLote == '0') ) {
        // ir até o participante
        $rsTmp = $arManterPropostas["rsParticipantes"];
        $rsTmp->setPrimeiroElemento();

        while ( !$rsTmp->eof() ) {
            if ( $rsTmp->getCampo( 'cgm_fornecedor' ) == $arManterPropostas["participante"] ) {
                $rsItens =$rsTmp->getCampo( 'rsItens' );
                $rsItens->setPrimeiroElemento();
                while ( !$rsItens->eof() ) {
                    if ( $rsItens->getCampo('lote') == $inLote && $rsItens->getCampo('cod_item') == $inCodItem ) {
                        $arDadosItem = $rsItens->arElementos[$rsItens->getCorrente()-1];

                        return $stJs.montaFormDadosItem($arDadosItem);
                    }
                    $rsItens->proximo();
                }
            }
            $rsTmp->proximo();
        }
    }
}

function montaFormDadosItem($arDadosItem)
{
    require_once( CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php"        );
    $arManterPropostas = Sessao::read('arManterPropostas');
    $boProximoItem = Sessao::read('boProximoItem');

    // monta componentes
    $obLblItem = new Label;
    $obLblItem->setRotulo( 'Item' );
    $obLblItem->setValue( $arDadosItem[ 'cod_item' ].' - '.$arDadosItem[ 'descricao' ] );

    $obLblLote = new Label;
    $obLblLote->setRotulo( 'Lote' );
    $obLblLote->setValue( $arDadosItem[ 'lote' ] );

    $obLblQtd = new Label;
    $obLblQtd->setRotulo( 'Quantidade' );
    $obLblQtd->setValue( number_format($arDadosItem[ 'quantidade' ], 4, ",", ".") );

    $obHdnQtd = new Hidden;
    $obHdnQtd->setId   ( 'qtd' );
    $obHdnQtd->setName ( 'qtd' );
    $obHdnQtd->setValue( number_format($arDadosItem[ 'quantidade' ], 4, ",", ".") );

    $obLblVUR = new Label;
    $obLblVUR->setRotulo( 'Valor Referência' );
    $obLblVUR->setValue( number_format($arDadosItem['valor_referencia'], 2, ",", ".") );

    $obHdnVlrReferencia = new Hidden;
    $obHdnVlrReferencia->setId   ( 'vlReferencia' );
    $obHdnVlrReferencia->setName ( 'vlReferencia' );
    $obHdnVlrReferencia->setValue( number_format($arDadosItem['valor_referencia'], 2, ",", ".") );

    // buscar data de validade de algum item ja preenchido
    if (!$arDadosItem[ 'data_validade' ]) {
        $rsParticipantes = $arManterPropostas['rsParticipantes'];
        $rsParticipantes->setPrimeiroElemento();
        while (  !$rsParticipantes->eof() ) {
            if ( $rsParticipantes->getCampo('cgm_fornecedor') == $arManterPropostas["participante"] ) {
                $rsItens = $rsParticipantes->getCampo('rsItens');
                $rsItens->setPrimeiroElemento();
                while (  !$rsItens->eof() ) {
                    if ( $rsItens->getCampo('data_validade') )
                        $arDadosItem['data_validade'] = $rsItens->getCampo('data_validade');
                    $rsItens->proximo();
                }
            }
            $rsParticipantes->proximo();
        }
    }
    $obTxtDataValidade = new Data;
    $obTxtDataValidade->setName ( 'dtValidade' );
    $obTxtDataValidade->setId ( 'dtValidade' );
    $obTxtDataValidade->setRotulo( '**Data Validade' );
    $obTxtDataValidade->setValue( $arDadosItem[ 'data_validade' ] );
    $obTxtDataValidade->obEvento->setOnChange("montaParametrosGET('validarData' , 'dtValidade',false);");

    $obMarca = new IPopUpMarca( new Form);
    $obMarca->setRotulo( '*Marca');
    $arManterPropostas["marca"] = $arDadosItem[ 'desc_marca' ];
    $obMarca->obCampoCod->setValue( $arDadosItem[ 'cod_marca' ] );
    $obMarca->obCampoCod->setId( $obMarca->obCampoCod->getName() );

    $obHdnDescMarca = new Hidden;
    $obHdnDescMarca->setId   ( 'descMarca' );
    $obHdnDescMarca->setName ( 'descMarca' );
    $obHdnDescMarca->setValue( $arManterPropostas["marca"]);

    $obValorUnitario = new TextBox;
    $obValorUnitario->setName ( 'stValorUnitario' );
    $obValorUnitario->setId ( 'stValorUnitario' );
    $obValorUnitario->setRotulo( '**Valor Unitário' );
    $obValorUnitario->obEvento->setOnKeyPress("mascaraMoeda(this, 4, event, false);");
    $obValorUnitario->obEvento->setOnChange( "montaParametrosGET('atualizaSomaItem' , 'qtd,stValorUnitario',true);" );
    $obValorUnitario->obEvento->setOnClick ("this.value = '';");
    $stValor = strstr( $arDadosItem[ 'valor_unitario' ] , ',' ) ? $arDadosItem[ 'valor_unitario' ] : number_format($arDadosItem[ 'valor_unitario' ], 4 , ',' , '.' );
    $obValorUnitario->setValue( $stValor );

    $obValorTotal = new Moeda;
    $obValorTotal->setName ( 'stValorTotal' );
    $obValorTotal->setId ( 'stValorTotal' );
    $obValorTotal->setRotulo( '**Valor Total' );
    $obValorTotal->obEvento->setOnChange( "montaParametrosGET('atualizaTotalItem' , 'qtd,stValorTotal',true);" );
    $obValorTotal->obEvento->setOnClick ("this.value = '';");
    $stValor = strstr( $arDadosItem[ 'valor_total' ] , ',' ) ? $arDadosItem[ 'valor_total' ] : number_format($arDadosItem[ 'valor_total' ], 2 , ',' , '.' );
    $obValorTotal->setValue( $stValor );

    //Para saber se é o último ítem, caso for o check IrProximoItem tem q ficar desabilitado
    $boUltimo = false;
    $rsItensValidaUltimo = $arManterPropostas["rsItens"];
    $rsItensValidaUltimo->setUltimoElemento();
    if ( ($rsItensValidaUltimo->getCampo("cod_item") == $arDadosItem[ 'cod_item' ]) && ($rsItensValidaUltimo->getCampo("lote") == $arDadosItem[ 'lote' ])  ) {
        $boUltimo = true;
    }

    $obChkIrProximo = new CheckBox;
    $obChkIrProximo->setRotulo('Ir para o próximo item');
    $obChkIrProximo->setTitle('Ir para o próximo item após salvar.');
    $obChkIrProximo->setName('boProximoItem');
    $obChkIrProximo->setId  ('boProximoItem');
    if ($boProximoItem==true) {
        $obChkIrProximo->setValue('on');
    } else {
        $obChkIrProximo->setValue('off');
    }
    $obChkIrProximo->setChecked( $boProximoItem );
    $obChkIrProximo->obEvento->setOnChange( "montaParametrosGET('atualizaProxItem' , '',true);" );

    if ($boUltimo === true) {
        $obChkIrProximo->setDisabled( true );
        $obChkIrProximo->setChecked( false );
        $obChkIrProximo->setValue('off');
    }

    $obBtnSalvar = new Button;
    $obBtnSalvar->setValue             ( "Salvar" );
    $obBtnSalvar->setId                ( "btnSalvar" );
    $obBtnSalvar->obEvento->setOnClick ( "montaParametrosGET('proxItem','dtValidade,inCodMarca,stValorUnitario,stValorTotal,boProximoItem,vlReferencia,descMarca',true);" );

    //Define Objeto Button para Limpar Veiculo da Publicação
    $obBtnLimparItem = new Button;
    $obBtnLimparItem->setValue             ( "Limpar"       );
    $obBtnLimparItem->obEvento->setOnClick ( "limparItem()" );

    $obFormulario = new Formulario();
    $obFormulario->addHidden    ( $obHdnQtd  );
    $obFormulario->addHidden    ( $obHdnVlrReferencia );
    $obFormulario->addHidden    ( $obHdnDescMarca );
    $obFormulario->addComponente( $obLblItem );
    if ( $arManterPropostas["tipo_mapa"] == 2)
        $obFormulario->addComponente( $obLblLote );
    $obFormulario->addComponente( $obLblQtd );
    $obFormulario->addComponente( $obLblVUR );
    $obFormulario->addComponente( $obTxtDataValidade );
    $obFormulario->addComponente( $obMarca );
    $obFormulario->addComponente( $obValorUnitario );
    $obFormulario->addComponente( $obValorTotal );
    $obFormulario->addComponente( $obChkIrProximo );
    $obFormulario->agrupaComponentes( array( $obBtnSalvar , $obBtnLimparItem ) );
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

function atualizaItens()
{

    include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php" );
    $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;

    $arManterPropostas = Sessao::read('arManterPropostas');

    if ($arManterPropostas["participante"]) {

        // buscar participante
        $rsTmp = $arManterPropostas["rsParticipantes"];

        $rsTmp->setPrimeiroElemento();

        while ( !$rsTmp->eof() ) {
            if ( $rsTmp->getCampo( 'cgm_fornecedor' ) == $arManterPropostas["participante"] ) {
                $rsItens = $rsTmp->getCampo( 'rsItens' );

            }
            $rsTmp->proximo();
        }

        return montaListaItens($rsItens);

    } else {

        $inCodMapa = $arManterPropostas["cod_mapa"];
        $stExercicioMapa = $arManterPropostas["exercicio"];

        include_once ( CAM_GP_COM_MAPEAMENTO . 'TComprasMapaItem.class.php' );
        $obTComprasMapaItem = new TComprasMapaItem();

        $obTComprasMapaItem->setDado( 'exercicio', $stExercicioMapa );
        $obTComprasMapaItem->setDado( 'cod_mapa', $inCodMapa );

        /* verificar tipo do mapa */
        $inTIpo = SistemaLegado::pegaDado( "cod_tipo_licitacao", "compras.mapa" , " where cod_mapa = " . $inCodMapa  . " and exercicio = '" . $stExercicioMapa . "' " );

        $obTComprasMapaItem->recuperaItensPropostaAgrupado( $rsMapaItens );

        $rsMapaItens->addFormatacao('valor_unitario'   ,'NUMERIC_BR');
        $rsMapaItens->addFormatacao('valor_total'      ,'NUMERIC_BR');
        $rsMapaItens->addFormatacao('valor_referencia' ,'NUMERIC_BR');

        while ( !$rsMapaItens->eof() ) {
            $itemComplemento = "";
            $obTComprasMapaItem->setDado('cod_mapa'  , $rsMapaItens->getCampo('cod_mapa'));
            $obTComprasMapaItem->setDado('cod_item'  , $rsMapaItens->getCampo('cod_item'));
            $obTComprasMapaItem->setDado('exercicio' , $rsMapaItens->getCampo('exercicio'));
            $obTComprasMapaItem->recuperaComplementoItemMapa( $rsItemComplemento );

            $rsItemComplemento->setPrimeiroElemento();
            While (!$rsItemComplemento->eof()) {
                if ($itemComplemento == "") {
                    $itemComplemento= $rsItemComplemento->getCampo('complemento');
                } else {
                    $itemComplemento= $itemComplemento ." <br\>".$rsItemComplemento->getCampo('complemento');
                }
                $rsItemComplemento->proximo();
            }
            $rsMapaItens->setCampo( 'complemento', $itemComplemento);

            $obTComprasMapaItem->setDado('cod_item'  , $rsMapaItens->getCampo('cod_item'));
            $obTComprasMapaItem->setDado('exercicio' , $rsMapaItens->getCampo('exercicio'));
            $obTComprasMapaItem->recuperaValorReferenciaItem( $rsItemEmpenhado );

            $obTAlmoxarifadoCatalogoItem->setDado('cod_item'  , $rsMapaItens->getCampo('cod_item'));
            $obTAlmoxarifadoCatalogoItem->setDado('exercicio' , $rsMapaItens->getCampo('exercicio'));
            $obTAlmoxarifadoCatalogoItem->recuperaValorItemUltimaCompra($rsItemUltimaCompra);

            if ($rsItemEmpenhado->getNumLinhas() > 0 ) {
                $valorReferencia = $rsItemEmpenhado->getCampo('vl_referencia');
            } else {
                $valorTotalFormatado = str_replace(',','',str_replace('.','',$rsMapaItens->getCampo( 'vl_total' )));
                $quantidadeFormatado = str_replace(',','',str_replace('.','',$rsMapaItens->getCampo( 'quantidade' )));

                $valorReferencia = $valorTotalFormatado / $quantidadeFormatado;
                $valorReferencia = number_format($valorReferencia,4,',','.');
            }

            $vlItemUltimaCompra = $rsItemUltimaCompra->getCampo('vl_unitario_ultima_compra');

            $rsMapaItens->setCampo( 'valor_referencia', $valorReferencia);
            $rsMapaItens->setCampo( 'valor_ultima_compra', $vlItemUltimaCompra);

            $rsMapaItens->proximo();
        }
        $rsMapaItens->setPrimeiroElemento();

        require_once(TCOM."TComprasCotacaoFornecedorItem.class.php");
        $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem;

        $rsParticipantes = $arManterPropostas["rsParticipantes"];
        if ( is_object( $rsParticipantes ) ) {
            $rsParticipantes->setPrimeiroElemento();
            while ( !$rsParticipantes->eof() ) {

                // as partes comentadas partir dessa linha nessa função foram comentadas para corrigir um bug de repetição de dados nas listas
                // em testes
                $inCount=0;

                $arItensFornecedor = $rsMapaItens->arElementos;

                if ($arManterPropostas["cod_cotacao"]) { // se tem cotacao, buscar itens ja incluidos

                    foreach ($arItensFornecedor as $chave =>$dados) {
                        $obTComprasCotacaoFornecedorItem->setDado('cod_cotacao', $arManterPropostas["cod_cotacao"] );
                        $obTComprasCotacaoFornecedorItem->setDado('exercicio', $arManterPropostas["exercicio_cotacao"] );

                        $obTComprasCotacaoFornecedorItem->setDado('cod_item', $arItensFornecedor[$chave]['cod_item']);

                        $obTComprasCotacaoFornecedorItem->setDado('lote', $arItensFornecedor[$chave]['lote']);

                        $obTComprasCotacaoFornecedorItem->setDado('cgm_fornecedor', $rsParticipantes->getCampo('cgm_fornecedor') );
                        $obTComprasCotacaoFornecedorItem->recuperaItensFornecedor( $rsItem );

                        if ( $rsItem->getNumLinhas() == 1) {
                            list( $a , $m , $d ) = explode('-', $rsItem->getCampo('dt_validade'));

                            $quantidadeItemFornecedor = $arItensFornecedor[$inCount]['quantidade'];

                            $arItensFornecedor[$inCount]['data_validade']  = $d . '/' . $m . '/' . $a;
                            $arItensFornecedor[$inCount]['valor_unitario'] = number_format(($rsItem->getCampo('vl_cotacao') / $quantidadeItemFornecedor), 4, ",", ".");
                            $arItensFornecedor[$inCount]['valor_total']    = number_format($rsItem->getCampo('vl_cotacao'), 2, ",", ".");
                            $arItensFornecedor[$inCount]['cod_marca']      = $rsItem->getCampo('cod_marca');
                            $arItensFornecedor[$inCount]['desc_marca']     = $rsItem->getCampo('descricao');

                        }
                        $inCount++;
                    }
                }

                $rsItensFornecedor = new RecordSet;
                $rsItensFornecedor->preenche($arItensFornecedor);

                $rsParticipantes->setCampo('rsItens' , $rsItensFornecedor );
                $rsParticipantes->proximo();
            }

            $rsParticipantes->setPrimeiroElemento();
            $arManterPropostas["rsParticipantes"] = $rsParticipantes;
        }

        $arManterPropostas["rsItens"] = $rsMapaItens;
        Sessao::write('arManterPropostas', $arManterPropostas);

        return montaListaItens( $rsMapaItens );
    }
}

function montaListaItens($rsItens , $stHtm = "")
{
    $arManterPropostas = Sessao::read('arManterPropostas');
    $rsItens->setPrimeiroElemento();
    
    $rsItens->addFormatacao('quantidade'         ,'NUMERIC_BR_4');
    $rsItens->addFormatacao('valor_referencia'   ,'NUMERIC_BR');
    $rsItens->addFormatacao('valor_ultima_compra','NUMERIC_BR');

    $table = new Table;
    $table->setRecordset($rsItens);

    $table->setSummary('Itens');
    $table->Head->addCabecalho('Item' 		    , 30);
    if ($arManterPropostas["tipo_mapa"] == 2) {
        $table->Head->addCabecalho('Lote'           , 5);
    }
    $table->Head->addCabecalho('Qtde'               , 10);
    $table->Head->addCabecalho('Valor Referência'   , 10);
    $table->Head->addCabecalho('Valor Última Compra', 10);
    $table->Head->addCabecalho('Valor Unitário'	    , 10);
    $table->Head->addCabecalho('Valor Total'	    , 10);
    $table->Head->addCabecalho('Selecione'	        , 5);

    $stTitle = "[stTitle]";

    $table->Body->addCampo("[cod_item] - [descricao]<br/>[complemento]","E", $stTitle);
    if ($arManterPropostas["tipo_mapa"] == 2) {
        $table->Body->addCampo("lote", "E", $stTitle);
    }
    $table->Body->addCampo('quantidade'         , "D", $stTitle);
    $table->Body->addCampo('valor_referencia'   , "D", $stTitle);
    $table->Body->addCampo('valor_ultima_compra', "D", $stTitle);
    $table->Body->addCampo('valor_unitario'     , "D", $stTitle);
    $table->Body->addCampo('valor_total'        , "D", $stTitle);

    $obRdSelecione = new Radio;
    $obRdSelecione->setName               ( "rd_item" );
    $obRdSelecione->setId                 ( "" );
    $obRdSelecione->obEvento->setOnChange ( "selecionaItem(this)" );
    $obRdSelecione->setValue              ( "[lote],[cod_item]" );

    $table->Body->addComponente($obRdSelecione);
    $table->Foot->addSoma('valor_total', "D");

    $table->montaHTML();
    $stHTML = $table->getHtml();
    $stHTML = str_replace("\n" ,"" ,$stHTML);
    $stHTML = str_replace("  " ,"" ,$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "d.getElementById('spnItens').innerHTML = '".$stHtm." ".$stHTML."';";

    return $stJs;
}

/**
 * Busca Participantes da Licitação na Base
 */
function buscaParticipantesBase()
{
    $request = new Request($_REQUEST);
    $arManterPropostas = Sessao::read('arManterPropostas');

    $inCodMapa       = $arManterPropostas["cod_mapa"];
    $stExercicioMapa = $arManterPropostas["exercicio"];
    $inCodLicitacao  = $arManterPropostas['licitacao']['cod_licitacao'];
    $inCodModalidade = $arManterPropostas['licitacao']['cod_modalidade'];

    include_once ( CAM_GP_COM_MAPEAMENTO . 'TComprasMapa.class.php' );
    $obTComprasMapa = new TComprasMapa();

    $obTComprasMapa->setDado( 'exercicio', $stExercicioMapa );
    $obTComprasMapa->setDado( 'cod_mapa', $inCodMapa );

    if ( $request->get('stAcao') != "dispensaLicitacao" && $request->get('stAcao') !=NULL) {
        $stFiltroMapa = " AND licitacao.cod_licitacao=".$inCodLicitacao;
        $stFiltroMapa .= " AND licitacao.cod_modalidade=".$inCodModalidade;
        $obTComprasMapa->recuperaMapaLicitacaoProposta( $rsMapa , $stFiltroMapa);

        if ( $rsMapa->getNumLinhas() > 0 and $rsMapa->getCampo('cod_licitacao') ) {
            $inCodLicitacao = $rsMapa->getCampo('cod_licitacao');
            $inCodModalidade = $rsMapa->getCampo('cod_modalidade');
            $inCodEntidade= $rsMapa->getCampo('cod_entidade');
            $inExercicioLic = $rsMapa->getCampo('exercicio');

            require_once ( CAM_GP_LIC_MAPEAMENTO . 'TLicitacaoParticipante.class.php' );
            $obLicParticipantes = new TLicitacaoParticipante();
            $obLicParticipantes->setDado('cod_licitacao', $inCodLicitacao);
            $obLicParticipantes->setDado('cod_modalidade', $inCodModalidade);
            $obLicParticipantes->setDado('cod_entidade', $inCodEntidade);
            $obLicParticipantes->setDado('exercicio', $inExercicioLic);
            $obLicParticipantes->recuperaParticipanteLicitacaoManutencaoPropostas( $rsParticipantes);

            if ( $rsParticipantes->getNumLinhas() > 0 ) {
                // busca se mapa ja possui cotacao
                require_once( TCOM . "TComprasMapa.class.php" );
                $obTComprasMapa = new TComprasMapa;
                $obTComprasMapa->setDado('cod_mapa',$inCodMapa);
                $obTComprasMapa->setDado('exercicio_mapa',$stExercicioMapa);
                $obTComprasMapa->recuperaMapaCotacaoValida( $rsCotacao );

                if ( $rsCotacao->getNumLinhas() > 0 ) {
                    $arManterPropostas["cod_cotacao"] = $rsCotacao->getCampo('cod_cotacao');
                    $arManterPropostas["exercicio_cotacao"] = $rsCotacao->getCampo('exercicio_cotacao');
                }

                // recordset com valores dos itens dos participantes
                $rsItensPart = new Recordset;

                $arNovo = array();
                $i = 0;
                while ( $i < count($rsParticipantes->arElementos)) {
                    $tmp = $rsParticipantes->arElementos[$i];
                    $tmp['rsItens'] = $rsItensPart;
                    $arNovo[] = $tmp;
                    $i++;
                }

                $rsParticipantes->preenche( $arNovo );

                if ( $rsParticipantes->getNumLinhas() < 1 )
                    $rsParticipantes = new Recordset;

                $arManterPropostas["rsParticipantes"] = $rsParticipantes;
                Sessao::write('arManterPropostas', $arManterPropostas);

                return montaListaParticipantes( $rsParticipantes );
            }
        }
    } else {
        // busca se mapa ja possui cotacao
        require_once( TCOM . "TComprasMapa.class.php" );
        $obTComprasMapa = new TComprasMapa;
        $obTComprasMapa->setDado('cod_mapa',$inCodMapa);
        $obTComprasMapa->setDado('exercicio_mapa',$stExercicioMapa);
        $obTComprasMapa->recuperaMapaCotacaoValida( $rsCotacao );

        if ( $rsCotacao->getNumLinhas() > 0 ) {
            $arManterPropostas["cod_cotacao"] = $rsCotacao->getCampo('cod_cotacao');
            $arManterPropostas["exercicio_cotacao"] = $rsCotacao->getCampo('exercicio_cotacao');

            require_once( TCOM . "TComprasCotacaoFornecedorItem.class.php" );
            $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem;
            $obTComprasCotacaoFornecedorItem->setDado('cod_cotacao',$rsCotacao->getCampo('cod_cotacao'));
            $obTComprasCotacaoFornecedorItem->setDado('exercicio_cotacao',$rsCotacao->getCampo('exercicio_cotacao'));
            $obTComprasCotacaoFornecedorItem->recuperaFornecedoresCotacao( $rsParticipantes );
        } else {
            $rsParticipantes = new Recordset();
        }

        $arManterPropostas["rsParticipantes"] = $rsParticipantes;

        Sessao::write('arManterPropostas', $arManterPropostas);
        $stJs = montaListaParticipantes( $rsParticipantes , true , montaFormIncluirParticipante() );

        return $stJs;
    }

}

function montaFormIncluirParticipante()
{
    $obFormulario = new Formulario();
    $obForm = new Form;

    $obParticipante = new IPopUpCGMVinculado( $obForm );
    $obParticipante->setTabelaVinculo ( ' (select fornecedor.*
                                             from compras.fornecedor where not exists ( select 1 from compras.fornecedor_inativacao where fornecedor_inativacao.cgm_fornecedor = fornecedor.cgm_fornecedor and ( fornecedor_inativacao.timestamp_fim::date > now()::date or fornecedor_inativacao.timestamp_fim is null ) ) )   ' );
    $obParticipante->setCampoVinculo ( 'cgm_fornecedor' );
    $obParticipante->setNomeVinculo ( 'Participante' );
    $obParticipante->setRotulo ( '*CGM do Participante' );
    $obParticipante->setName   ( 'stNomParticipante');
    $obParticipante->setId     ( 'stNomParticipante');
    $obParticipante->obCampoCod->setName ( 'inCgmFornecedor' );
    $obParticipante->obCampoCod->setId   ( 'inCgmFornecedor' );
    $obParticipante->obCampoCod->setNull ( true                      );
    $obParticipante->setNull ( true                      );

    //botoes
    $obBtnIncluirParticipante= new Button;
    $obBtnIncluirParticipante->setName              ( "btnIncluirParticipante" );
    $obBtnIncluirParticipante->setValue             ( "Incluir" );
    $obBtnIncluirParticipante->setTipo              ( "button" );
    $obBtnIncluirParticipante->obEvento->setOnClick ( "montaParametrosGET('IncluirParticipante', 'inCgmFornecedor',true);" );
    $obBtnIncluirParticipante->setDisabled          ( false );

    $obBtnLimparParticipante = new Button;
    $obBtnLimparParticipante->setName               ( "btnLimparParticipante" );
    $obBtnLimparParticipante->setValue              ( "Limpar" );
    $obBtnLimparParticipante->setTipo               ( "button" );
    $obBtnLimparParticipante->obEvento->setOnClick  ( "limparParticipante();" );
    $obBtnLimparParticipante->setDisabled           ( false );

    $botoesParticipante = array ( $obBtnIncluirParticipante, $obBtnLimparParticipante );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo (" Incluir Participante");
    $obFormulario->addComponente( $obParticipante   );
    $obFormulario->defineBarra  ( $botoesParticipante, 'left', '' );
    $obFormulario->montaInnerHTML();
    $stHTML = $obFormulario->getHTML();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);

    return $stHTML;

}

function montaListaParticipantes($rsParticipantes , $boExcluir = false, $stHtm = "")
{
    $rsParticipantes->setPrimeiroElemento();
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsParticipantes );
    $obLista->setTitulo ("Participantes");
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Participante");
    $obLista->ultimoCabecalho->setWidth( 90 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Selecione");
    $obLista->ultimoCabecalho->setWidth( 7 );
    $obLista->commitCabecalho();
    if ($boExcluir) {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Ação");
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();
    }
    $obLista->addDado();

    $obLista->ultimoDado->setCampo("[cgm_fornecedor] - [fornecedor]");
    $obLista->commitDado();

    $obRdSelecione = new Radio;
    $obRdSelecione->setId                          ( "rd_participante_"                   );
    $obRdSelecione->setName                        ( "rd_participante_"                   );
    $obRdSelecione->obEvento->setOnChange          ( "selecionaParticipante(this)"        );
    $obRdSelecione->setValue                       ( "cgm_fornecedor"                     );

    $obLista->addDadoComponente                    ( $obRdSelecione    );
    $obLista->ultimoDado->setAlinhamento           ( 'centro'          );
    $obLista->ultimoDado->setCampo                 ( "selecionar"      );
    $obLista->commitDadoComponente                 (                   );
    if ($boExcluir) {
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "javascript: excluirParticipante();" );
        $obLista->ultimaAcao->addCampo( "1","cgm_fornecedor" );
        $obLista->commitAcao();
    }

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "d.getElementById('spnParticipantes').innerHTML = '".$stHtm." ".$stHTML."';";

    return $stJs;
}

function incluirParticipante($inCgmParticipante)
{

    $inCgmPartPendente = Sessao::read('inCgmParticipante');

    $arPropostas = array();
    $arPropostas = Sessao::read('arManterPropostas');
    $rsParticipantes = $arPropostas['rsParticipantes'];
    $rsItens = $arPropostas['rsItens'];
    $rsParticipantes->setPrimeiroElemento();

    if (!$inCgmPartPendente) {
        if ($inCgmParticipante) {
            foreach ($arPropostas['rsParticipantes'] as $arItens) {
                if ($arItens['cgm_fornecedor'] == incluirParticipante) {
                    $boErro = true;
                    $stJs .= "alertaAviso('Participante já incluído.','form','erro','".Sessao::getId()."','../');\n";
                }
            }

            include_once ( CAM_GP_COM_MAPEAMENTO."TComprasFornecedor.class.php");
            $boTemDebitoTributario = false;
            $obTComprasFornecedor = new TComprasFornecedor;
            $stFiltroSQL .= "WHERE calculo_cgm.numcgm = ".$inCgmParticipante." \n";
            $obTComprasFornecedor->recuperaFornecedorDebito ( $rsRecordSet, $stFiltroSQL );

            if ($rsRecordSet->getNumLinhas() > 0) {
                $boExecuta = true;
                Sessao::write('inCgmParticipante',$inCgmParticipante);
                $stJs .= "confirmPopUp('Manutenção de Participantes','Esse participante possui débitos tributários, deseja realmente inseri-lo na lista de participantes?','montaParametrosGET(\'IncluirParticipante\')');";
            }
        }
    } else {
        $inCgmParticipante = $inCgmPartPendente;
        Sessao::remove('inCgmParticipante');
    }

    if (!$boExecuta) {

        $arArray = array();
        $arArray['cod_licitacao']        = '';
        $arArray['cgm_fornecedor']       = $inCgmParticipante;
        $arArray['cod_modalidade']       = '';
        $arArray['cod_entidade']         = '';
        $arArray['exercicio']            = Sessao::getExercicio();
        $arArray['numcgm_representante'] = '';
        $arArray['dt_inclusao']          = '';
        $arArray['fornecedor']           = SistemaLegado::pegaDado( 'nom_cgm' , 'sw_cgm' , ' where numcgm = '.$inCgmParticipante );
        $arArray['representante']        = '';
        $arArray['rsItens']              = $rsItens;

        $rsParticipantes->add($arArray);

        $arPropostas["rsParticipantes"] = $rsParticipantes;

        Sessao::write('arManterPropostas', $arPropostas);
        $stJs .= montaListaParticipantes($rsParticipantes, TRUE, montaFormIncluirParticipante());

    }

    return $stJs;
}

function validaValoresItemRequest(Request $request)
{
    $stErro = false;
    $arManterPropostas = Sessao::read('arManterPropostas');
    $boProximoItem = Sessao::read('boProximoItem');
    //validações
    $rsParticipantes = $arManterPropostas["rsParticipantes"];

    if ($rsParticipantes->getNumLinhas() > 0 ) {
        # Validação para o campo Data de Validade.
        if ($request->get('dtValidade')==NULL)
            $stErro = 'Campo Data de Validade deve ser preenchido';

        list($d,$m,$a) = explode('/' , $request->get('dtValidade'));
        $data = (integer) ($a.$m.$d);

        if ($data < date('Ymd') && !$stErro)
            $stErro = 'Campo Data de Validade deve ser maior que a Data de Hoje!';

        if (($request->get('inCodMarca')==""||$request->get('inCodMarca')==NULL) && !$stErro && ($request->get('descMarca') == ""||$request->get('descMarca')==NULL))
            $stErro = 'Campo Marca deve ser preenchido';

        if ((($request->get('stValorUnitario')==NULL) || $request->get('stValorUnitario') == '0,00') && !$stErro)
            $stErro = 'Campo Valor Unitário deve ser maior que zero';

        # Validação quando o objeto for Alienação de Bens Móveis/Imóveis
        # para obrigar o usuário setar o valor igual ou maior o valor de referência.
        # cod_tipo_objeto 4 = Alienação de Bens Móveis/Imóveis

        if ($arManterPropostas['licitacao']['cod_tipo_objeto'] == 4 && !$stErro) {
            $stValorUnitario   = str_replace(",", ".", str_replace(".", "", $request->get('stValorUnitario')));
            $stValorReferencia = str_replace(",", ".", str_replace(".", "", $request->get('vlReferencia')));

            if ($stValorUnitario < $stValorReferencia)
                $stErro = 'Campo Valor Unitário deve ser maior ou igual ao Valor de Referência';

        } elseif (!$stErro) {
            # Validação do valor unitário, conforme regra sobre valor de referência.
            $stErro = validaVlrRefItem($request->get('stValorUnitario'), $request->get('vlReferencia'));
        }

        if ((($request->get('stValorTotal')==NULL) || $request->get('stValorTotal') == '0,00') && !$stErro)
            $stErro = 'Campo Valor Total deve ser maior que zero';

        if (!$stErro) {

            if ($request->get('inCodMarca') !=NULL) {
                $stDescMarca = SistemaLegado::pegaDado('descricao','almoxarifado.marca',' where cod_marca = '.$request->get('inCodMarca').' ');
            } else {
                $stDescMarca = $request->get('descMarca');
            }

            $inProx = salvarValoresItem( $arManterPropostas["participante"], $arManterPropostas["lote"], $arManterPropostas["item"], $request->get('inCodMarca'), $stDescMarca, $request->get('stValorUnitario'), $request->get('stValorTotal'), $request->get('dtValidade') );
            $stJs .= atualizaItens();

            if ( $request->get('boProximoItem') == "on" ) {
                $boProximoItem = true;
                $stJs .= "\n document.getElementById('rd_item_" . $inProx . "').click();";
                $stJs .= "\n document.getElementById('dtValidade').focus();";
            } else {
                $boProximoItem = false;
                $stJs .= "d.getElementById('spnDadosItem').innerHTML = '';\n";
            }

            Sessao::write('boProximoItem', $boProximoItem);

        } else {
            $stJs = "alertaAviso( '" . $stErro . "' , 'form', 'erro' , '" . Sessao::getId() . "' );\n";
        }
    }

    return $stJs;
}

function salvarValoresItem($inCgmFornecedor, $inLoteItem, $inCodItem, $inCodMarca, $stDescMarca, $stValorUnitario, $stValorTotal, $dtValidade)
{
    $arManterPropostas = Sessao::read('arManterPropostas');
    $rsParticipantes = $arManterPropostas["rsParticipantes"];

    //localiza o participante e pega os itens do participante
    $rsParticipantes->setPrimeiroElemento();
    while ( !$rsParticipantes->eof() ) {
        if ( $rsParticipantes->getCampo("cgm_fornecedor") == $inCgmFornecedor ) {
            $rsItens = $rsParticipantes->getCampo("rsItens");
            break;
        }
        $rsParticipantes->proximo();
    }

    $arItens = $rsItens->arElementos;

    /* Localiza o item do participante e atualiza os valores
    para saber o proximo item */
    $inProx = 1;

    foreach ($arItens as $chave =>$dados) {
        $inProx = $inProx+1;
        if ($inLoteItem == $dados['lote'] && $inCodItem == $dados['cod_item']) {
            $arItens[$chave]['cod_marca'] = $inCodMarca;
            $arItens[$chave]['desc_marca'] = $stDescMarca;
            $arItens[$chave]['valor_unitario'] = $stValorUnitario;
            $arItens[$chave]['valor_total'] = $stValorTotal;
            $arItens[$chave]['data_validade'] = $dtValidade;
            break;
        }
    }

    $rsItensModificados = new RecordSet;
    $rsItensModificados->preenche($arItens);

    $rsParticipantes->setPrimeiroElemento();
    while ( !$rsParticipantes->eof() ) {
        if ( $rsParticipantes->getCampo("cgm_fornecedor") == $inCgmFornecedor ) {
            $rsParticipantes->setCampo("rsItens",$rsItensModificados );
        }
        $rsParticipantes->proximo();
    }

    Sessao::write('arManterPropostas', $arManterPropostas);
    Sessao::write('manutencaoPropostas', true);

    $stJS = "var stCtrl = document.frm.stCtrl.value;
             var stAcao      = document.frm.action;
             var stTarget    = document.frm.target;
             document.frm.target = 'oculto';
             document.frm.action = 'PRManterManutencaoProposta.php?Sessao::getId()';
             document.frm.submit();
             document.frm.target = stTarget;
             document.frm.action = stAcao;";

    echo $stJS;

    return $inProx;
}

function importarArquivoFornecedor()
{
    include_once( CAM_GP_COM_MAPEAMENTO.'TComprasCompraDireta.class.php' );
    include_once( CAM_GP_COM_MAPEAMENTO."TComprasFornecedor.class.php" );
    include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoMarca.class.php" );
    include_once(CAM_FW_XML."domxml.php");

    $stErro = "";
    $arManterPropostas = Sessao::read('arManterPropostas');
    $stLocalArquivoImportacao = $request->get('arquivoCotacao');

    if (!is_readable($stLocalArquivoImportacao)) {
        $stErro = "Arquivo Sem Permissões para Leitura.";
    } else {
        if (!$stLocalArquivoImportacao) {
            $stErro = "Nenhum Arquivo de Importação foi informado.";
        } else {
            if ( !$domDocument = domxml_open_file( $stLocalArquivoImportacao,"",$erroXMLLoader ) ) {
                $stErro = "O arquivo de importação não é um XML válido.";
            }
        }
    }

    if ($stErro == "") {
        $eleCompra = $domDocument->get_elements_by_tagname("compra");
        if ( count( $eleCompra ) > 0 ) {
            $eleCodCompra = $eleCompra[0]->get_elements_by_tagname('codigo');
            if ( count( $eleCodCompra ) > 0 ) {
                $inCodCompra = $eleCodCompra[0]->get_content();
                $eleEntidade = $domDocument->get_elements_by_tagname("entidade");
                if ( count( $eleEntidade ) > 0 ) {
                    $eleCodEntidade = $eleEntidade[0]->get_elements_by_tagname('codigo');
                    if ( count( $eleCodEntidade ) > 0 ) {
                        $inCodEntidade = $eleCodEntidade[0]->get_content();
                        $eleExercicioEntidade = $eleEntidade[0]->get_elements_by_tagname('exercicio');
                        if ( count( $eleExercicioEntidade ) > 0 ) {
                            $inExercicioEntidade = $eleExercicioEntidade[0]->get_content();
                            $eleModalidade = $domDocument->get_elements_by_tagname("modalidade");
                            if ( count( $eleModalidade ) > 0 ) {
                                $eleCodModalidade = $eleModalidade[0]->get_elements_by_tagname('codigo');
                                if ( count( $eleCodModalidade ) > 0 ) {
                                    $inCodModalidade = $eleCodModalidade[0]->get_content();
                                    $eleFornecedor = $domDocument->get_elements_by_tagname("fornecedor");
                                    if ( count( $eleFornecedor ) > 0 ) {
                                        $eleCnpjFornecedor = $eleFornecedor[0]->get_elements_by_tagname('cnpj');
                                        if ( count( $eleCnpjFornecedor ) > 0 ) {
                                            $stCnpjFornecedor = $eleCnpjFornecedor[0]->get_content();
                                        } else {
                                            $stErro = "O arquivo de importação não é um XML válido. [ cnpj ]";
                                        }
                                    } else {
                                        $stErro = "O arquivo de importação não é um XML válido. [ fornecedor ]";
                                    }
                                } else {
                                    $stErro = "O arquivo de importação não é um XML válido. [ codigo modalidade ]";
                                }
                            } else {
                                $stErro = "O arquivo de importação não é um XML válido. [ modalidade ]";
                            }
                        } else {
                            $stErro = "O arquivo de importação não é um XML válido. [ exercicio ]";
                        }
                    } else {
                        $stErro = "O arquivo de importação não é um XML válido. [ codigo entidade ]";
                    }
                } else {
                    $stErro = "O arquivo de importação não é um XML válido. [ entidade ]";
                }
            } else {
                $stErro = "O arquivo de importação não é um XML válido. [ codigo compra ]";
            }
        } else {
            $stErro = "O arquivo de importação não é um XML válido. [ compra ]";
        }

        if ($stErro == "") {

            $inCnpjFornecedor = str_replace( ".", "", $stCnpjFornecedor );
            $inCnpjFornecedor = str_replace( "-", "", $inCnpjFornecedor );
            $inCnpjFornecedor = str_replace( "/", "", $inCnpjFornecedor );

            //Consulta o banco de dados para trazer os valores para conferir com os do XML
            $obTComprasCompraDireta = new TComprasCompraDireta();
            $obTComprasCompraDireta->setDado('cod_mapa',       $arManterPropostas['cod_mapa']);
            $obTComprasCompraDireta->setDado('exercicio_mapa', $arManterPropostas['exercicio']);
            $obTComprasCompraDireta->recuperaCompraDiretaPorMapa( $rsCompraDireta );

            // Verifica se o Fornecedor esta correto
            $obTComprasFornecedor = new TComprasFornecedor();
            $obTComprasFornecedor->setDado("cnpj", $inCnpjFornecedor );
            $obTComprasFornecedor->recuperaFornecedorCnpj( $rsFornecedor );

            //Validações
            if ( $inCodCompra != $rsCompraDireta->getCampo( "cod_compra_direta" ) ) {
                $stErro = "O arquivo de importação informado não se refere a esta compra.";
            }

            if ( $inCodModalidade != $rsCompraDireta->getCampo( "cod_modalidade" ) ) {
                $stErro = "A modalidade do arquivo de importação não equivale a modalidade desta compra.";
            }

            if ( ($inCodEntidade != $rsCompraDireta->getCampo( "cod_entidade" ) ) || ( $inExercicioEntidade != $rsCompraDireta->getCampo( "exercicio_entidade" ) )  ) {
                $stErro = "A entidade do arquivo de importação não equivale a entidade desta compra.";
            }

            if ( $rsFornecedor->getNumLinhas() <= 0 ) {
                $stErro = "O fornecedor informado no arquivo de importação não está cadastrado no Urbem. Cnpj: $stCnpjFornecedor ";
            }

            if ($stErro == "") {

                $stJs .= incluirParticipante( $rsFornecedor->getCampo('numcgm')  );

                //busca todos os itens da proposta
                $eleItem = $domDocument->get_elements_by_tagname("item");

                for ( $i=0; $i<count($eleItem); $i++ ) {

                    // Pega informações referente ao lote
                    if ($eleItem[$i]->get_elements_by_tagname('lote')) {
                        $eleLoteItem = $eleItem[$i]->get_elements_by_tagname('lote');
                        $inLoteItem = $eleLoteItem[0]->get_content();
                    }

                    // Pega o codigo do Item
                    $eleCodItem = $eleItem[$i]->get_elements_by_tagname('codigo');
                    $inCodItem = $eleCodItem[0]->get_content();

                    //Pega a marca do Item
                    $eleMarcaItem = $eleItem[$i]->get_elements_by_tagname('marca');
                    $stMarcaItem = $eleMarcaItem[0]->get_content();
                    $stMarcaItem = trim( $stMarcaItem );

                    // Pega a data de validade da proposta
                    $eleDtValidadeItem = $eleItem[$i]->get_elements_by_tagname('data_validade_proposta');
                    $stDtValidadeItem = $eleDtValidadeItem[0]->get_content();

                    // Pega a quantidade do item
                    $eleQuantidadeItem = $eleItem[$i]->get_elements_by_tagname('quantidade');
                    $stQuantidadeItem = $eleQuantidadeItem[0]->get_content();

                    //Pega o valor da proposta do item
                    $eleValorTotalItem = $eleItem[$i]->get_elements_by_tagname('valor');
                    $stValorTotalItem = $eleValorTotalItem[0]->get_content();

                    // Calcula o valor unitario do item
                    $nuValorTotalItem = number_format($stValorTotalItem,3,'.',',');
                    $nuQuantidadeItem = number_format($stQuantidadeItem,3,'.',',');

                    $nuValorUnitario = $nuValorTotalItem / $nuQuantidadeItem;

                    $nuValorUnitario = number_format($nuValorUnitario,2,',','.');
                    $stValorTotalItem = number_format($stValorTotalItem,2,',','.');

                    //Consulta o código da marca se não tiver marca define como cod_marca "" para ser incluído no PR desta funcionalidade
                    $obTAlmoxarifadoMarca = new TAlmoxarifadoMarca();
                    $obTAlmoxarifadoMarca->setDado( "descricao", $stMarcaItem );
                    $obTAlmoxarifadoMarca->recuperaMarca( $rsMarca );
                    $inCodMarca = ($rsMarca->getCampo("cod_marca") != '') ? $rsMarca->getCampo("cod_marca") : "";
                    $stMarcaItem = ($rsMarca->getCampo("descricao") != '') ? $rsMarca->getCampo("descricao") : $stMarcaItem;

                    //salva o item
                    if ( ($rsFornecedor->getCampo('numcgm') != "") && ($inLoteItem != "") && ($inCodItem != "") && ($stMarcaItem != "") && ($nuValorUnitario != "") && ($stValorTotalItem != "") && ($stDtValidadeItem != "") ) {
                        salvarValoresItem( $rsFornecedor->getCampo('numcgm'), $inLoteItem, $inCodItem, $inCodMarca, $stMarcaItem, $nuValorUnitario, $stValorTotalItem, $stDtValidadeItem );
                    }

                    unset($eleLoteItem);
                    unset($eleCodItem);
                    unset($eleMarcaItem);
                    unset($stMarcaItem);
                    unset($obTAlmoxarifadoMarca);
                    unset($rsMarca);
                }
            }

            if ($stErro == "") {
                $stJs .= "document.getElementById('arquivoCotacao').value = '';";
                $stJs .= "alertaAviso('Arquivo importado com sucesso.' , 'form' , 'aviso' , '" . Sessao::getId() . "' , '../');\n";
            }
        }
    }

    if ($stErro != "") {
        $stJs = "alertaAviso('".$stErro."' , 'form' , 'erro' , '" . Sessao::getId() . "' , '../');\n";
    }

    return $stJs;
}

/*Se não existir nenhuma cotação para o mapa de compras
esta função insere os dados da ultima cotação anulada deste mapa.*/
function incluirUltimaCotacaoAnulada($cod,$mapa,$exercicio)
{
    $codUltCotacaoAnulada = $cod;
    $codMapa = $mapa;
    $stExercicioMapa = $exercicio;
    $stExercicioCotacao = Sessao::getExercicio();

    require_once( TCOM . "TComprasCotacao.class.php" );
    $obTComprasNovaCotacao = new TComprasCotacao;
    $stFiltro = " AND exercicio = '" . $stExercicioCotacao . "'";
    $obTComprasNovaCotacao->verificaUltimoCodCotacao ( $rsUltimoCodCotacao, $stFiltro );
    $codNovo = $rsUltimoCodCotacao->getCampo('cod_cotacao');
    $codNovaCotacao = $codNovo + 1;

    //Inclui uma nova cotação com os dados da ultima cotação anulada
    $obTComprasNovaCotacao->setDado('exercicio', $stExercicioCotacao);
    $obTComprasNovaCotacao->setDado('cod_cotacao', $codNovaCotacao);
    $obTComprasNovaCotacao->inclusaoNoDebug();

    require_once( TCOM . "TComprasMapaCotacao.class.php" );
    $obTComprasNovoMapa = new TComprasMapaCotacao;
    $obTComprasNovoMapa->setDado('cod_cotacao',$codNovaCotacao );
    $obTComprasNovoMapa->setDado('cod_mapa',$codMapa);
    $obTComprasNovoMapa->setDado('exercicio_cotacao',$stExercicioCotacao);
    $obTComprasNovoMapa->setDado('exercicio_mapa',$stExercicioMapa);
    $obTComprasNovoMapa->inclusaoNoDebug();

    //Inclui os itens da ultima cotação anulada para a nova cotação
    require_once( TCOM . "TComprasCotacaoItem.class.php" );
    $obTComprasNovoItemCotacao = new TComprasCotacaoItem;
    $stFiltro = " AND cod_cotacao = ". $codUltCotacaoAnulada ." AND exercicio = '". $stExercicioCotacao."'";
    $obTComprasNovoItemCotacao->recuperaUltimosItensCotacao ( $rsUltimosItensCotacao, $stFiltro );

    while (!$rsUltimosItensCotacao->eof()) {
        $obTComprasNovoItemCotacao->setDado('exercicio',$rsUltimosItensCotacao->getCampo('exercicio'));
        $obTComprasNovoItemCotacao->setDado('cod_cotacao',$codNovaCotacao );
        $obTComprasNovoItemCotacao->setDado('lote',$rsUltimosItensCotacao->getCampo('lote'));
        $obTComprasNovoItemCotacao->setDado('cod_item',$rsUltimosItensCotacao->getCampo('cod_item'));
        $obTComprasNovoItemCotacao->setDado('quantidade',$rsUltimosItensCotacao->getCampo('quantidade'));

        $obTComprasNovoItemCotacao->inclusaoNoDebug();
        $rsUltimosItensCotacao->proximo();
    }

    require_once( TCOM . "TComprasCotacaoFornecedorItem.class.php" );
    $obTComprasNovoItem = new TComprasCotacaoFornecedorItem;
    $stFiltro = " AND cod_cotacao = ". $codUltCotacaoAnulada ." AND exercicio = '". $stExercicioCotacao."'";
    $obTComprasNovoItem->recuperaUltimosItens ( $rsUltimosItens, $stFiltro );

    while (!$rsUltimosItens->eof()) {
        $obTComprasNovoItem->setDado('exercicio',$rsUltimosItens->getCampo('exercicio'));
        $obTComprasNovoItem->setDado('cod_cotacao',$codNovaCotacao );
        $obTComprasNovoItem->setDado('cod_item',$rsUltimosItens->getCampo('cod_item'));
        $obTComprasNovoItem->setDado('lote',$rsUltimosItens->getCampo('lote'));
        $obTComprasNovoItem->setDado('cgm_fornecedor',$rsUltimosItens->getCampo('cgm_fornecedor'));
        $obTComprasNovoItem->setDado('cod_marca',$rsUltimosItens->getCampo('cod_marca'));
        $obTComprasNovoItem->setDado('dt_validade',"'".$rsUltimosItens->getCampo('dt_validade')."'");
        $obTComprasNovoItem->setDado('vl_cotacao',$rsUltimosItens->getCampo('vl_cotacao'));

        $obTComprasNovoItem->inclusaoNoDebug();
        $rsUltimosItens->proximo();
    }

    return $stJs;
}

switch ( $request->get('stCtrl') ) {

    case 'montaLabelsDispensaLicitacao':
    case 'montaClusterLabels':

        $stErro = FALSE;
        $stJs = '';
        $rsDataLicitacao='';
        
        list ( $inCodMapa , $stExercicioMapa ) = explode ( '/' , $request->get('stMapaCompras'));
        list ( $inCodLicitacaoBusca , $stExercicioLicitacao ) = explode ( '/' , $request->get('inCodLicitacao'));
        $stExercicioMapa = ($stExercicioMapa == '') ? Sessao::getExercicio() : $stExercicioMapa;
        
        if ($inCodMapa && $stExercicioMapa) {

            $rsParticipantes = new Recordset();
            $rsItens = new Recordset();
            $arManterPropostas = array();

            $arManterPropostas["rsParticipantes"] = $rsParticipantes;
            $arManterPropostas["rsItens"] = $rsItens;
            $arManterPropostas["participante"] = null;
            $arManterPropostas["item"] = null;
            $arManterPropostas["lote"] = null;
            $arManterPropostas["cod_mapa"] = null;
            $arManterPropostas["exercicio"] = null;
            $arManterPropostas["tipo_mapa"] = null;
            $arManterPropostas["licitacao"] = null;

            /* seta na sessao o mapa de compras que esta usando*/
            $arManterPropostas["cod_mapa"] = $inCodMapa;
            $arManterPropostas["exercicio"] = $stExercicioMapa;

            $obForm = new Form;
            $obFormulario = new Formulario();

            $boExisteMapa = true;

            include_once ( CAM_GP_LIC_MAPEAMENTO.'TLicitacaoLicitacao.class.php' );
            $obTLicitacaoLicitacao = new TLicitacaoLicitacao;

            if ( $request->get('stCtrl')  != 'montaLabelsDispensaLicitacao' ) {
                
                if (!$stErro) {
                    $obTLicitacaoLicitacao->setDado( 'exercicio', $stExercicioMapa );
                    $obTLicitacaoLicitacao->setDado( 'cod_mapa' , $inCodMapa       );

                    if ( $request->get('stAcao') != "dispensaLicitacao") {
                        $obTLicitacaoLicitacao->setDado( 'cod_licitacao' , $inCodLicitacaoBusca );
                        $obTLicitacaoLicitacao->setDado( 'cod_modalidade' , $request->get('inCodModalidade') );
                    }
                    $obTLicitacaoLicitacao->recuperaLicitacao( $rsLicitacao );

                    $inCodTipoObjeto = $rsLicitacao->getCampo('cod_tipo_objeto');

                    if ($rsLicitacao->getNumLinhas() == -1) {
                        $stErro = 'Mapa de Compras não está em processo licitatório';
                    }
                }

                if (!$stErro) {
                    
                    $obTLicitacaoLicitacao->recuperaLicitacaoCompleta($rsDataLicitacao);
                    
                    if ( $request->get('stAcao') != "dispensaLicitacao") {
                        $stFiltroLicitacao = " AND licitacao.cod_licitacao =".$inCodLicitacaoBusca;
                        $stFiltroLicitacao .= " AND licitacao.cod_modalidade =".$request->get('inCodModalidade');
                    }

                    require_once ( CAM_GP_LIC_COMPONENTES . 'IClusterLabelsMapa.class.php' );
                    $obCluster = new IClusterLabelMapa ( $obForm , $inCodMapa , $stExercicioMapa );
                    $obCluster->setFiltro($stFiltroLicitacao);
                    $obCluster->geraFormulario ( $obFormulario );

                    if ($inCodMapa == '') {
                        $stMapaCompras = explode('/', $request->get('stMapaCompras'));
                        $inCodMapa     = $stMapaCompras[0];
                    }

                    if ($stExercicioMapa == '') {
                        $stMapaCompras   = explode('/', $request->get('stMapaCompras'));
                        $stExercicioMapa = $stMapaCompras[1];
                    }
                    
                    require_once( TCOM . "TComprasCotacao.class.php" );
                    $obTComprasCotacao = new TComprasCotacao;
                    $stFiltro          = "  WHERE mapa_cotacao.cod_mapa =  ".$inCodMapa." AND mapa_cotacao.exercicio_mapa = '".$stExercicioMapa."' ";
                    $obTComprasCotacao->recuperaCotacaoNaoAnulada($rsCotacao, $stFiltro);
                    
                    if ($rsCotacao->inNumLinhas <= 0) {
                        if ($rsDataLicitacao!='') {
                            $stDataManutencao = SistemaLegado::dataToBr($rsDataLicitacao->getCampo('timestamp'));
                        }
                    } else {
                        $stDataManutencao = SistemaLegado::dataToBr($rsCotacao->getCampo('timestamp'));
                    }
                    if ($stDataManutencao==null||$stDataManutencao=='') {
                        $stDataManutencao = date("d/m/Y");
                    }
                    $obTxtDataEmissao = new Data();
                    $obTxtDataEmissao->setRotulo('Data da Manutenção');
                    $obTxtDataEmissao->setTitle ('Informe a Data de Manutenção.');
                    $obTxtDataEmissao->setId    ('stDataManutencao');
                    $obTxtDataEmissao->setName  ('stDataManutencao');
                    $obTxtDataEmissao->setNull  (false);
                    $obTxtDataEmissao->setValue ($stDataManutencao);
                    $obTxtDataEmissao->obEvento->setOnChange( "montaParametrosGET( 'validaDataManutencao', 'stDataManutencao' ); " );

                    $obFormulario->addComponente( $obTxtDataEmissao );

                    $obFormulario->montaInnerHTML();
                    $stHtml = $obFormulario->getHTML();
                }

            } else {
                
                //Verificar se existe cotações anuladas para o mapa de compras
                require_once( TCOM . "TComprasMapaCotacao.class.php" );
                $obTComprasUltimoMapaCotacao = new TComprasMapaCotacao;
                $stFiltro = " AND cod_mapa = ". $inCodMapa ." AND exercicio_mapa = '" . $stExercicioMapa . "'";
                $obTComprasUltimoMapaCotacao->recuperaUltimaCotacaoMapa ( $rsUltimaCotacao, $stFiltro );
                $codUltimaCotacao = $rsUltimaCotacao->getCampo('cod_cotacao');

                if ($codUltimaCotacao != '') {
                    require_once( TCOM . "TComprasCotacaoAnulada.class.php" );
                    $obTComprasUltimaCotacao = new TComprasCotacaoAnulada;
                    $stFiltro = " AND cotacao_anulada.cod_cotacao = ". $codUltimaCotacao ." AND cotacao_anulada.exercicio = '" . $stExercicioMapa . "'";
                    $obTComprasUltimaCotacao->recuperaUltimaCotacaoAnulada ( $rsUltimaCotacaoAnulada, $stFiltro );
                    $codUltimaCotacaoAnulada = $rsUltimaCotacaoAnulada->getCampo('cod_cotacao');

                    if ($codUltimaCotacaoAnulada != '') {
                        $stJs .= "jQuery('#nuUltCodCotacaoAnulada').val('".$codUltimaCotacaoAnulada."');";
                        $stJs .= "jQuery('#nuCodMapa').val('".$inCodMapa."');";
                        $stJs .= "jQuery('#nuExercicioMapa').val('".$stExercicioMapa."');";
                        $stJs .= "confirmPopUp('Recuperar Última Cotação Anulada', 'Existem cotações anuladas para este mapa de compras. Deseja recuperar a última cotação anulada?', 'montaParametrosGET(\'RecuperaUltimaCotacaoAnulada\',\'\');');";
                    }
                }

                ///verificação pra quando a compra dispensa licitação ai não pode ter processo licitatório pro mapa escolhido
                /// verificando se o mapa está em processo licitatório
                $stFiltro = " where licitacao.cod_mapa = $inCodMapa
                                and licitacao.exercicio_mapa = '$stExercicioMapa'
                                and not exists ( select 1
                                                   from licitacao.licitacao_anulada
                                                  where licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                                                    and licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                                    and licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                                                    and licitacao_anulada.exercicio      = licitacao.exercicio      )
                               " ;
                if ( $request->get('stAcao') != "dispensaLicitacao") {
                    $obTLicitacaoLicitacao->setDado( 'cod_licitacao' , $inCodLicitacaoBusca );
                    $obTLicitacaoLicitacao->setDado( 'cod_modalidade' , $request->get('inCodModalidade') );
                }
                $obTLicitacaoLicitacao->recuperaTodos( $rsLicitacoes, $stFiltro );

                if ( $rsLicitacoes->getNumLinhas() >0 ) {
                    $stErro = "Este mapa já está em processo licitatório.";
                } else {

                    include_once( CAM_GP_COM_MAPEAMENTO.'TComprasCompraDireta.class.php' );
                    $obTComprasCompraDireta = new TComprasCompraDireta();
                    $obTComprasCompraDireta->setDado('cod_mapa',$inCodMapa);
                    $obTComprasCompraDireta->setDado('exercicio_mapa',$stExercicioMapa);
                    $obTComprasCompraDireta->recuperaCompraDiretaPorMapa( $rsCompraDireta );

                    if ( $rsCompraDireta->getNumLinhas() < 1 ) {
                        $stErro = "O mapa deve possuir uma compra direta";
                    } else {

                        $obLblObjeto = new Label();
                        $obLblObjeto->setRotulo('Objeto');
                        $obLblObjeto->setValue( $rsCompraDireta->getCampo( 'cod_objeto' ) . ' - ' . stripslashes(nl2br(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ', $rsCompraDireta->getCampo('objeto'))))) );

                        $obLblMapaCompra = new Label();
                        $obLblMapaCompra->setRotulo( 'Mapa de Compras' );
                        $obLblMapaCompra->setValue( $rsCompraDireta->getCampo( 'cod_mapa' ).'/'.$rsCompraDireta->getCampo( 'exercicio_mapa' ) );

                        $obLblModalidade = new Label();
                        $obLblModalidade->setRotulo( 'Modalidade' );
                        $obLblModalidade->setValue( $rsCompraDireta->getCampo( 'cod_modalidade' ).' - '.$rsCompraDireta->getCampo( 'modalidade' ) );

                        $obLblEntidade = new Label();
                        $obLblEntidade->setRotulo( 'Entidade' );
                        $obLblEntidade->setValue( $rsCompraDireta->getCampo( 'cod_entidade' ).' - '.$rsCompraDireta->getCampo( 'entidade' ) );

                        $obLblCompraDireta = new Label();
                        $obLblCompraDireta->setRotulo( 'Compra Direta' );
                        $obLblCompraDireta->setValue( $rsCompraDireta->getCampo( 'cod_compra_direta' ) );

                        $stFiltroData  = " WHERE compra_direta.exercicio_mapa = '" . $rsCompraDireta->getCampo( 'exercicio_mapa' )."'";
                        $stFiltroData .= "   AND compra_direta.cod_mapa        = ". $rsCompraDireta->getCampo( 'cod_mapa' )  ;
                        $obTComprasCompraDireta->recuperaCompraDireta( $rsCompraDiretaData, $stFiltroData );

                        if ($inCodMapa == '') {
                            $stMapaCompras = explode('/', $request->get('stMapaCompras'));
                            $inCodMapa     = $stMapaCompras[0];
                        }

                        if ($stExercicioMapa == '') {
                            $stMapaCompras   = explode('/', $request->get('stMapaCompras'));
                            $stExercicioMapa = $stMapaCompras[1];
                        }

                        $obTComprasCotacao = new TComprasCotacao;
                        $stFiltro          = "  WHERE mapa_cotacao.cod_mapa =  ".$inCodMapa." AND mapa_cotacao.exercicio_mapa = '".$stExercicioMapa."'";
                        $obTComprasCotacao->recuperaCotacaoNaoAnulada($rsCotacao, $stFiltro);

                        if ($rsCotacao->inNumLinhas <= 0) {
                            $stDataManutencao = SistemaLegado::dataToBr($rsCompraDiretaData->getCampo( 'timestamp' ));
                        } else {
                            $stDataManutencao = SistemaLegado::dataToBr($rsCotacao->getCampo( 'timestamp' ));
                        }
                        if ($stDataManutencao==''||$stDataManutencao==null) {
                            $stDataManutencao = date("d/m/Y");
                        }
                        $inCodCotacao       = SistemaLegado::pegaDado("cod_cotacao"       , "compras.mapa_cotacao", "WHERE cod_mapa = ".$rsCompraDireta->getCampo( 'cod_mapa' )." AND exercicio_mapa = '".$rsCompraDireta->getCampo( 'exercicio_mapa' )."'");
                        $stExercicioCotacao = SistemaLegado::pegaDado("exercicio_cotacao" , "compras.mapa_cotacao", "WHERE cod_mapa = ".$rsCompraDireta->getCampo( 'cod_mapa' )." AND exercicio_mapa = '".$rsCompraDireta->getCampo( 'exercicio_mapa' )."'");

                        Sessao::write('stDtManutencao', $stDataManutencao );

                        $obTxtDataEmissao = new Data();
                        $obTxtDataEmissao->setRotulo    ( "Data da Manutenção" );
                        $obTxtDataEmissao->setTitle     ( "Informe a Data de Manutenção." );
                        $obTxtDataEmissao->setId        ( "stDataManutencao" );
                        $obTxtDataEmissao->setName      ( "stDataManutencao" );
                        $obTxtDataEmissao->setNull      ( false );
                        $obTxtDataEmissao->setValue     ( $stDataManutencao );
                        $obTxtDataEmissao->obEvento->setOnChange( "montaParametrosGET( 'validaDataManutencao', 'stDataManutencao' ); " );

                        $obFormulario->addComponente( $obLblEntidade    );
                        $obFormulario->addComponente( $obLblMapaCompra  );
                        $obFormulario->addComponente( $obLblCompraDireta );
                        $obFormulario->addComponente( $obLblObjeto      );
                        $obFormulario->addComponente( $obLblModalidade  );
                        $obFormulario->addComponente( $obTxtDataEmissao );
                        $obFormulario->montaInnerHTML();
                        $stHtml = $obFormulario->getHTML();
                    }
                }
            }

            if (!$stErro) {
                // licitacao possui participantes ?
                include_once ( CAM_GP_COM_MAPEAMENTO . 'TComprasMapa.class.php' );
                $obTComprasMapa = new TComprasMapa();
            
                $obTComprasMapa->setDado( 'exercicio', $stExercicioMapa );
                $obTComprasMapa->setDado( 'cod_mapa', $inCodMapa );

                if ( $request->get('stAcao') != "dispensaLicitacao") {
                    $stFiltroMapa = " AND licitacao.cod_licitacao = ".$inCodLicitacaoBusca;
                    $stFiltroMapa .= " AND licitacao.cod_modalidade = ".$request->get('inCodModalidade');
                }

                $obTComprasMapa->recuperaMapaLicitacaoProposta( $rsMapa,$stFiltroMapa );

                $arManterPropostas["tipo_mapa"] = $rsMapa->getCampo('cod_tipo_licitacao');

                if ( $rsMapa->getNumLinhas() > 0 and $rsMapa->getCampo('cod_licitacao') ) {
                    
                    require_once ( CAM_GP_LIC_MAPEAMENTO . 'TLicitacaoParticipante.class.php' );
                    $obLicParticipantes = new TLicitacaoParticipante;
                    $obLicParticipantes->setDado('cod_licitacao', $rsMapa->getCampo('cod_licitacao'));
                    $obLicParticipantes->setDado('cod_modalidade', $rsMapa->getCampo('cod_modalidade'));
                    $obLicParticipantes->setDado('cod_entidade', $rsMapa->getCampo('cod_entidade'));
                    $obLicParticipantes->setDado('exercicio', $rsMapa->getCampo('exercicio'));
                    $stOrdem = "";
                    $arManterPropostas['licitacao'] = array
                                                      (
                                                        'cod_licitacao'   => $rsMapa->getCampo('cod_licitacao') ,
                                                        'cod_modalidade'  => $rsMapa->getCampo('cod_modalidade'),
                                                        'cod_entidade'    => $rsMapa->getCampo('cod_entidade') ,
                                                        'exercicio'       => $rsMapa->getCampo('exercicio') ,
                                                        'cod_tipo_objeto' => $inCodTipoObjeto
                                                      );
                    $obLicParticipantes->recuperaParticipanteLicitacaoManutencaoPropostas( $rsParticipantes);
                    
                    if ( $rsParticipantes->getNumLinhas() < 1 )
                        $stErro = 'Licitação deve possuir participantes';
                }
                // validar edital, se tiver
                if ( $rsMapa->getCampo('num_edital') && $rsMapa->getCampo('exercicio_edital') ) {
                    // tem, agora verifica se é valido
                    require_once (TLIC.'TLicitacaoEditalImpugnado.class.php');
                    $obTLicitacaoEditalImpugnado = new TLicitacaoEditalImpugnado;
                    $obTLicitacaoEditalImpugnado->setDado( 'num_edital' , $rsMapa->getCampo('num_edital') );
                    $obTLicitacaoEditalImpugnado->setDado( 'exercicio' , $rsMapa->getCampo('exercicio_edital') );
                    $obTLicitacaoEditalImpugnado->recuperaProcessos( $rsProcessosEdital );
                    if ( $rsProcessosEdital->getNumLinhas() > 0 and !$rsProcessosEdital->getCampo('parecer_juridico') )
                        $stErro = "Mapa de Compras inválido, Edital esta impugnado!";
                }
                /* validar cotacao, se esta julgada*/
                $obTComprasMapa = null;
                $obTComprasMapa = new TComprasMapa;
                $obTComprasMapa->setDado( 'exercicio_mapa', $stExercicioMapa );
                $obTComprasMapa->setDado( 'cod_mapa', $inCodMapa );
                $obTComprasMapa->recuperaMapaCotacaoValida( $rsCotaca );

                if ( $rsCotaca->getNumLinhas() > 0 ) {
                    $arManterPropostas["cod_cotacao"] = $rsCotaca->getCampo('cod_cotacao');
                    $arManterPropostas["exercicio_cotacao"] = $rsCotaca->getCampo('exercicio_cotacao');

                    /* verifica julgamento*/
                    $inCodCotacaoJulgada =  SistemaLegado::pegaDado("cod_cotacao","compras.julgamento","where cod_cotacao = ". $rsCotaca->getCampo('cod_cotacao') ." and exercicio = '" . $rsCotaca->getCampo('exercicio_cotacao')."' " );
                    if ($inCodCotacaoJulgada) {
                        $stErro = "Proposta do Mapa de Compras ".$inCodMapa."/".$stExercicioMapa." está em Julgamento";
                    }
                }

            }

            Sessao::write('arManterPropostas', $arManterPropostas);

            /* FIM VALIDAÇÕES */
            if (!$stErro) {
                $stJs  .= "d.getElementById('spnLabels').innerHTML = '" . $stHtml . "';\n";
                /* busca participantes e monta lista */
                $stJs .= buscaParticipantesBase();
                /* atualiza itens e monta lista*/
                $stJs .= atualizaItens();
                $stJs .= "d.getElementById('Ok').disabled = false\n;";
            } else {
                $stJs  .= "d.getElementById('spnLabels').innerHTML = '';\n";
                $stJs .= "d.getElementById('spnParticipantes').innerHTML = ''\n;";
                $stJs .= "d.getElementById('spnItens').innerHTML = ''\n;";
                $stJs .= "d.getElementById('spnDadosItem').innerHTML = ''\n;";
                $stJs .= "d.getElementById('Ok').disabled = true\n;";
                $stJs .= "alertaAviso('" . $stErro . "','n_erro','erro','".Sessao::getId()."');\n";
            }

        } else {
            $stJs .= "d.getElementById('spnLabels').innerHTML = '';\n";
            $stJs .= "d.getElementById('spnParticipantes').innerHTML = ''\n;";
            $stJs .= "d.getElementById('spnItens').innerHTML = ''\n;";
            $stJs .= "d.getElementById('spnDadosItem').innerHTML = ''\n;";
            $stJs .= "d.getElementById('Ok').disabled = true\n;";
        }
    break;
    case 'validaDataManutencao':
        if ($request->get('stDataManutencao')!=NULL&&$request->get('stDataManutencao')!="") {
            $stDtManutencao = Sessao::read('stDtManutencao');
            $arDataManutencao = explode('/',$request->get('stDataManutencao'));
                    $arDataManutencaoSolicitacao = explode('/',$stDtManutencao );

                    $inDataManutencao  = $arDataManutencao[2] . $arDataManutencao[1] . $arDataManutencao[0];
                    $inDataCompraSolicitacao = $arDataManutencaoSolicitacao[2] . $arDataManutencaoSolicitacao[1] . $arDataManutencaoSolicitacao[0];

            // Não pode ser menor que a data da solicitacao.
            if ($inDataManutencao < $inDataCompraSolicitacao) {
                $stJs  = "jQuery('#stDataManutencao').val('".$stDtManutencao."');";
                $stJs .= "jQuery('#stDataManutencao').focus();";
                $stJs .=  "alertaAviso('A data da manutenção deve ser maior ou igual a data da solicitação.','n_erro','erro','".Sessao::getId()."');\n";
            }
            // Não pode ser maior que a data corrente.
            elseif (!SistemaLegado::comparaDatas(date('d/m/Y'), $request->get('stDataManutencao'), true)) {
                $stJs  = "jQuery('#stDataManutencao').val('".$stDtManutencao."');";
                $stJs .= "jQuery('#stDataManutencao').focus();";
                $stJs .=  "alertaAviso('A data do Julgamento deve ser menor ou igual a data atual.','n_erro','erro','".Sessao::getId()."');\n";
            } else {
                $stJs = "d.getElementById('Ok').disabled = false\n;";
            }
        } else {
            $data = date("d/m/Y");
            $stJs  = "jQuery('#stDataManutencao').val('".$data."');";
            $stJs .= "jQuery('#stDataManutencao').focus();";
            $stJs .= "d.getElementById('Ok').disabled = true\n;";
            $stJs .=  "alertaAviso('O campo Data da Manutenção deve ser preenchido.','n_erro','erro','".Sessao::getId()."');\n";
        }
    break;
    case 'IncluirParticipante':
        $stJs = incluirParticipante( $request->get('inCgmFornecedor'));
    break;

    case 'excluirParticipante':
        if ( $request->get('cgm_fornecedor')) {
            $arManterPropostas = Sessao::read('arManterPropostas');
            $rsParticipantes = $arManterPropostas["rsParticipantes"];
            $rsParticipantes->setPrimeiroElemento();

            $arNovo = array();
            $inCgmFornecedorSelecionado = "";
            foreach ($rsParticipantes->arElementos as $arRegistro) {
                if ( $arRegistro['cgm_fornecedor'] != $request->get('cgm_fornecedor') ) {
                    $arNovo[] = $arRegistro;
                    if( $inCgmFornecedorSelecionado == "" )
                        $inCgmFornecedorSelecionado = $arRegistro['cgm_fornecedor'];
                }
            }

            $rsParticipantes->preenche( $arNovo );
            $rsParticipantes->setPrimeiroElemento();

            $arManterPropostas["participante"] = $inCgmFornecedorSelecionado;
            $arManterPropostas["rsParticipantes"] = $rsParticipantes;
            Sessao::write('arManterPropostas', $arManterPropostas);

            $stJs .= montaListaParticipantes( $rsParticipantes , TRUE , montaFormIncluirParticipante() );
            $stJs .= atualizaItens();
            $stJs .= "d.getElementById('spnDadosItem').innerHTML = '';";
            $stJs .= "if( document.getElementById('rd_participante_1') ) document.getElementById('rd_participante_1').checked = true;";

        }
    break;

    case 'setaParticipante':
        /* seta participante atual na sessao */
        if ( $request->get('participante')) {
            $arManterPropostas = Sessao::read('arManterPropostas');
            $arManterPropostas["participante"] = $request->get('participante');
            Sessao::write('arManterPropostas', $arManterPropostas);
            $stJs = atualizaItens();
        }
    break;

    case 'setaItem':
        /* seta item atual na sessao */
        $arManterPropostas = Sessao::read('arManterPropostas');
        if ( $request->get('item') AND ( count($arManterPropostas['participante']) > 0 ) ) {
            $inCount = 0;
            $arFornecedores = array();
            $inCodCgmFornecedor = $arManterPropostas['participante'];

            include_once CAM_GP_COM_MAPEAMENTO.'TComprasFornecedor.class.php';
            $obTComprasFornecedor = new TComprasFornecedor();
            $obTComprasFornecedor->setDado("cgm_fornecedor", $inCodCgmFornecedor);
            $obTComprasFornecedor->recuperaListaFornecedor( $rsFornecedor );

            if ($rsFornecedor->getCampo('status') == 'Inativo') {
                $arFornecedores[$inCount]['cgm_fornecedor'] = $inCodCgmFornecedor;
                $arFornecedores[$inCount]['nom_cgm'] = $rsFornecedor->getCampo('nom_cgm');
                $inCount++;
            }

            if (count($arFornecedores) > 0) {
                if (count($arFornecedores) == 1) {
                    $stMensagemErro = 'O Participante ('.$arFornecedores[0]['cgm_fornecedor'].' - '.$arFornecedores[0]['nom_cgm'].') está inativo! Efetue a Manutenção de Participantes para retirar este Participante.';
                } elseif (count($arFornecedores) > 1) {
                    foreach ($arFornecedores as $arFornecedoresAux) {
                        $stCodNomFornecedores .= $arFornecedoresAux['cgm_fornecedor'].' - '.$arFornecedoresAux['nom_cgm'].', ';
                    }
                    $stCodNomFornecedores = substr($stCodNomFornecedores, 0, strlen($stCodNomFornecedores)-2);
                    $stMensagemErro = 'Os Participantes ('.$stCodNomFornecedores.') estão inativos! Efetue a Manutenção de Participantes para retirar estes Participantes.';
                }
            }

            if (!$stMensagemErro) {
                list( $inLote , $inCodItem ) = explode( ',' , $request->get('item') );
                $arManterPropostas["item"] = $inCodItem;
                $arManterPropostas["lote"] = $inLote;
                Sessao::write('arManterPropostas', $arManterPropostas);

                $elementosParticipantes = $arManterPropostas['rsParticipantes']->arElementos;

                foreach ($elementosParticipantes as $chave => $dadosParticipante) {
                    if ($dadosParticipante['cgm_fornecedor'] == $arManterPropostas['participante']) {
                        $rsItensFornecedor = $dadosParticipante['rsItens']->arElementos;
                        foreach ($rsItensFornecedor as $chaveProdutos =>$dadosProdutos) {
                            if ($dadosProdutos['cod_item'] == $arManterPropostas['item']) {
                                $marcaDesc = $dadosProdutos['desc_marca'];
                            }
                        }
                    }
                }
                /* verificar se há participante e item selecionado */
                $stJs  = "d.getElementById('spnDadosItem').innerHTML = '" . montaDadosItem() . "';\n";
                $stJs .= "if($('stNomMarca'))$('stNomMarca').innerHTML = '".addslashes($marcaDesc)."' \n";
            } else {
                $stJs = "alertaAviso( '".$stMensagemErro."' , 'form', 'erro' , '" . Sessao::getId() . "' );\n";
            }
        }
    break;

    case 'proxItem':
        $stJs .= validaValoresItemRequest($request);
    break;

    case 'atualizaProxItem':
    $stJs .= "var proxItem = d.getElementById('boProximoItem').checked;\n";
    $stJs .= "if (proxItem==true) { document.getElementById('boProximoItem').value = 'on' }";
    $stJs .= "else{ document.getElementById('boProximoItem').value = 'off' }";
    break;

    case 'atualizaSomaItem':
        $nuQtd = str_replace(',','.',str_replace('.','',$request->get('qtd')));
        $nuVlr = str_replace(',','.',str_replace('.','',$request->get('stValorUnitario')));

        // Adicionado teste para evitar erro como divisão por 0.
        if (!empty($nuQtd) && (!empty($nuVlr) && $nuVlr > 0)) {
            $stTotal = $nuVlr * $nuQtd;
            $stJs  = "document.getElementById('stValorUnitario').value = '".number_format($nuVlr,4,',','.')."';";
            $stJs .= "document.getElementById('stValorTotal').value = '".number_format($stTotal,2,',','.')."';";
        } else {
            $stJs .= "document.getElementById('stValorTotal').value = '';";
        }
    break;

    case 'atualizaTotalItem':
        $nuQtd = $request->get('qtd');
        $nuVlr = str_replace(',','.',str_replace('.','',$request->get('stValorTotal')));
        $stValor = $nuVlr / $nuQtd;

        $stJs  = "document.getElementById('stValorUnitario').value = '".number_format($stValor,2,',','.')."';";
        $stJs .= "document.getElementById('stValorTotal').value = '".number_format($nuVlr,2,',','.')."';";
    break;

    case 'limpar':
        $arManterPropostas = Sessao::read('arManterPropostas');
        $arManterPropostas["rsParticipantes"] = $rsParticipantes;
        $arManterPropostas["rsItens"] = $rsItens;
        $arManterPropostas["participante"] = null;
        $arManterPropostas["item"] = null;
        $arManterPropostas["lote"] = null;
        $arManterPropostas["cod_mapa"] = null;
        $arManterPropostas["exercicio"] = null;
        $arManterPropostas["licitacao"] = null;
        Sessao::write('arManterPropostas', $arManterPropostas);
        $stJs  = "d.getElementById('spnLabels').innerHTML = '';\n";
        $stJs .= "d.getElementById('spnParticipantes').innerHTML = ''\n;";
        $stJs .= "d.getElementById('spnItens').innerHTML = ''\n;";
        $stJs .= "d.getElementById('spnDadosItem').innerHTML = ''\n;";
        $stJs .= "d.getElementById('Ok').disabled = true\n;";
    break;

    case 'validarData':
        list($d,$m,$a) = explode('/' , $request->get('dtValidade'));
        $data = (integer) ($a.$m.$d);
        if ( $data < date('Ymd') && !$stErro ) { $stErro = '!';
            $stJs = "alertaAviso( 'Campo Data de Validade deve ser maior que a Data de Hoje' , 'form', 'erro' , '" . Sessao::getId() . "' );\n";
        }
    break;

    case 'importarArquivoFornecedor':
        $stJs = importarArquivoFornecedor();
    break;

    case 'cancelaInsercaoAutomaticaProposta':
        $arManterPropostas = Sessao::read('arManterPropostas');
        $arManterPropostas['CadastrarMarcas'] = array();
        Sessao::write('arManterPropostas',$arManterPropostas);
        Sessao::remove('manutencaoPropostas');
    break;

    case 'confirmaInclusao':
        $stJs = incluirParticipante($request->get('inCgmParticipante'), 'false');
    break;

    case 'RecuperaUltimaCotacaoAnulada':
        incluirUltimaCotacaoAnulada($request->get('nuUltCodCotacaoAnulada'),$request->get('nuCodMapa'),$request->get('nuExercicioMapa'),$request->get('nuCgmParticipante'));
        $stJs .= "window.location.reload();";
    break;
}

echo $stJs;
