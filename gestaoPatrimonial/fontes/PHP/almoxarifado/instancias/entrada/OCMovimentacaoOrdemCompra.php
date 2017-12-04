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
    * Arquivo Oculto da Entrada por Ordem de Compra
    * Data de Criação: 12/07/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    $Id: OCMovimentacaoOrdemCompra.php 65631 2016-06-03 21:06:49Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

require_once CAM_GP_ALM_COMPONENTES."ISelectAlmoxarifadoAlmoxarife.class.php";
require_once CAM_GP_COM_MAPEAMENTO."TComprasOrdem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioSituacaoBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioBem.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoOrdemCompra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');

// função que monta a lista de item da ordem de compra
function montaListaItens($arItens)
{
    $stPrograma = "MovimentacaoOrdemCompra";
    $pgOcul = "OC".$stPrograma.".php";

    $rsListaItens = new RecordSet;
    $rsListaItens->preenche( $arItens );

    $rsListaItens->setPrimeiroElemento();

    $table = new TableTree();
    $table->setArquivo( 'OCMovimentacaoOrdemCompra.php' );
    $table->setParametros( array('cod_pre_empenho','num_item','exercicio_empenho') );
    $table->setComplementoParametros( 'stCtrl=detalharItem' );
    $table->setRecordset( $rsListaItens );
    $table->setSummary('Itens a serem Atendidos');

    $table->Head->addCabecalho( 'Item'              , 25 );
    $table->Head->addCabecalho( 'Unidade de Medida' , 10 );
    $table->Head->addCabecalho( 'Centro de Custo'   , 25 );
    $table->Head->addCabecalho( 'Solicitado OC'     , 10 );
    $table->Head->addCabecalho( 'Atendido OC'       , 10 );

    $stTitle = "";

    $table->Body->addCampo( 'nom_item'      , "E" );
    $table->Body->addCampo( 'nom_unidade'   , "C" );
    $table->Body->addCampo( 'centro_custo'  , "E" );
    $table->Body->addCampo( 'solicitado_oc' , "D" );
    $table->Body->addCampo( 'atendido_oc'   , "D" );

    $stMensagem = "";

    $table->Head->addCabecalho( 'Detalhar', 10 );
    $obRadioDetalhar = new Radio();
    $obRadioDetalhar->setName( 'item' );
    $obRadioDetalhar->obEvento->setOnChange( "selecionaItem(this);" );
    $obRadioDetalhar->obEvento->setOnClick( "executaFuncaoAjax( 'montaDadosItem', '&item='+this.id, false );" );
    $table->Body->addComponente( $obRadioDetalhar, "C" 	);

    $table->montaHTML( true );
    $stHTML = $table->getHtml();

    $stRetorno = "$('spnItens').innerHTML = '".$stHTML."';";
    $stRetorno .= (empty($stMensagem) ? "" : $stMensagem);

    return $stRetorno;
}

function montaListaItensAtendidos($arItens)
{
    $stPrograma = "MovimentacaoOrdemCompra";
    $pgOcul = "OC".$stPrograma.".php";

    $rsListaItens = new RecordSet;
    $rsListaItens->preenche( $arItens );

    $rsListaItens->setPrimeiroElemento();

    $table = new TableTree();
    $table->setArquivo( 'OCMovimentacaoOrdemCompra.php' );
    $table->setParametros( array('cod_pre_empenho','num_item','exercicio_empenho') );
    $table->setComplementoParametros( 'stCtrl=detalharItem' );
    $table->setRecordset( $rsListaItens );
    $table->setSummary('Itens Atendidos');

    $table->Head->addCabecalho( 'Item'              , 25 );
    $table->Head->addCabecalho( 'Unidade de Medida' , 10 );
    $table->Head->addCabecalho( 'Centro de Custo'   , 25 );
    $table->Head->addCabecalho( 'Solicitado OC'     , 10 );
    $table->Head->addCabecalho( 'Atendido OC'       , 10 );

    $stTitle = "";

    $table->Body->addCampo( 'nom_item'      , "E" );
    $table->Body->addCampo( 'nom_unidade'   , "C" );
    $table->Body->addCampo( 'centro_custo'  , "E" );
    $table->Body->addCampo( 'solicitado_oc' , "D" );
    $table->Body->addCampo( 'atendido_oc'   , "D" );

    $stMensagem = "";

    $table->montaHTML( true );
    $stHTML = $table->getHtml();

    $stRetorno = "$('spnItensAtendidos').innerHTML = '".$stHTML."';";
    $stRetorno .= (empty($stMensagem) ? "" : $stMensagem);

    return $stRetorno;
}

// Função que monta a listagem do perecíveis
function montaListaItensPerecivel($arItensPerecivel)
{
    global $request;

    $rsListaItensPerecivel = new RecordSet;
    $rsListaItensPerecivel->preenche( $arItensPerecivel );

    $table = new Table();
    $table->setRecordset( $rsListaItensPerecivel );
    $table->setSummary('Listagem Perecível');

    $table->Head->addCabecalho( 'Lote'               , 25 );
    $table->Head->addCabecalho( 'Data de Fabricação' , 15 );
    $table->Head->addCabecalho( 'Data de Validade'   , 15 );
    $table->Head->addCabecalho( 'Quantidade'         , 10 );

    $table->Body->addCampo( 'inNumLotePerecivel'    , "E" );
    $table->Body->addCampo( 'dtFabricacaoPerecivel' , "C" );
    $table->Body->addCampo( 'dtValidadePerecivel'   , "C" );
    $table->Body->addCampo( 'inQtdePerecivel'       , "E" );

    $table->Body->addAcao( "ALTERAR", 'alterarPerecivel(%d,%s,%s,%s,%d)', array('inNumLotePerecivel', 'dtFabricacaoPerecivel', 'dtValidadePerecivel', 'inQtdePerecivel', 'inNumLinhaListaPerecivel') );
    $table->Body->addAcao( "EXCLUIR", 'excluirPerecivel(%d,%d)', array('inNumLinhaListaPerecivel', $request->get('inCodItem') ) );

    $table->montaHTML( true );
    $stHTML = $table->getHtml();

    return "$('spnItensPereciveis').innerHTML = '".$stHTML."';";
}

// Função que limpa os dados da parte perecivel do item
function limpaDadosPerecivel()
{
    $stJs  = "\n $('inNumLotePerecivel').value = '';";
    $stJs .= "\n $('dtFabricacaoPerecivel').value = '';";
    $stJs .= "\n $('dtValidadePerecivel').value = '';";
    $stJs .= "\n $('inQtdePerecivel').value = '';";
    $stJs .= "\n $('inNumLinhaListaPerecivel').value = '';";
    $stJs .= "\n $('acaoPerecivel').value = 'incluir';";
    $stJs .= "\n $('Incluir').value = 'Incluir Lotes';";

    return $stJs;
}

// Função que valida os dados para inserir na listagem dos perecíveis
function validaListaPerecivel(Request $request)
{
    $stMensagem = '';

    // valida o numero do lote
    $inNumLotePerecivel = $request->get('inNumLotePerecivel');
    if (empty($inNumLotePerecivel))
        $stMensagem = "O Número do Lote precisa ser preenchido.";
    else {
        // faz um somatório para guardar o total da quantidade da listagem
        $inTotQtde = 0;

        // faz a verificação se o lote já está na listagem, verificando se a linha da lista é diferente da alterada
        $arItensPerecivel = Sessao::read('arItensPerecivel');
        if (isset($arItensPerecivel[$request->get('inCodItem')])) {
            foreach ($arItensPerecivel[$request->get('inCodItem')] as $chave => $valor) {
                if( $inNumLotePerecivel != $valor['inNumLotePerecivel'] )
                    $inTotQtde += str_replace(",",  ".", str_replace(".", "", $valor['inQtdePerecivel']));
                if ( ($valor['inNumLotePerecivel'] == $inNumLotePerecivel) && ($request->get('inNumLinhaListaPerecivel') != $valor['inNumLinhaListaPerecivel']) ) {
                    $stMensagem = "O item de Lote ".$inNumLotePerecivel." já está na lista.";
                    break;
                }
            }
        }
    }

    if(empty($stMensagem)){
        // valida a data de fabricação
        $dtFabricacaoPerecivel = $request->get('dtFabricacaoPerecivel');
        if (empty($dtFabricacaoPerecivel))
            $stMensagem = "A data de fabricação precisa ser preenchida.";
        elseif (SistemaLegado::ComparaDatas($dtFabricacaoPerecivel, date("d/m/Y")))
            $stMensagem = "A data de fabricação não pode ser posterior a data de hoje.";

        if(empty($stMensagem)){
            // valida a data de validade
            $dtValidadePerecivel = $request->get('dtValidadePerecivel');
            if ($dtValidadePerecivel == '')
                $stMensagem = "A data de validade precisa ser preenchida.";
            elseif (SistemaLegado::ComparaDatas($dtFabricacaoPerecivel, $dtValidadePerecivel))
                $stMensagem = "A data de validade não pode ser anterior a data de fabricação.";
            elseif (SistemaLegado::ComparaDatas(date('d/m/Y'), $dtValidadePerecivel))
                $stMensagem = "A data de validade não pode ser anterior a data de hoje.";

            if(empty($stMensagem)){
                //valida a quantidade
                $inQtdePerecivel = $request->get('inQtdePerecivel');
                if (empty($inQtdePerecivel))
                    $stMensagem = "A quantidade precisa ser preenchida.";
                else {
                    $inQtdePerecivel  = str_replace(",", ".", str_replace(".", "", $inQtdePerecivel));
                    $inQtdeDisponivel = str_replace(",", ".", str_replace(".", "", $request->get('inQtdeDisponivel')));

                    if ($inQtdePerecivel <= 0)
                        $stMensagem = "A quantidade precisa ser um valor maior que zero.";
                    elseif (($inQtdePerecivel+$inTotQtde) > $inQtdeDisponivel)
                        $stMensagem = "A quantidade informada ultrapassa a quantidade do item selecionado na Ordem de Compra.";
                }
            }
        }
    }

    if(!empty($stMensagem))
        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
}

// Monta a listagem dos itens de entrada
function montaListaItensEntrada($arItensEntrada)
{
    foreach( $arItensEntrada AS $key => $value){
        if($value['boCodItem'] == TRUE)
            $arItensEntrada[$key]['stItem'] = $arItensEntrada[$key]['stItem']." (".$value['inCodItem']." - ".$value['stNomItem'].")";
    }

    $rsListaItensEntrada = new RecordSet;
    $rsListaItensEntrada->preenche( $arItensEntrada );

    $table = new Table();
    $table->setRecordset( $rsListaItensEntrada );
    $table->setSummary('Itens da Ordem de Compra para Entrada');

    $table->Head->addCabecalho( 'Item'              , 20 );
    $table->Head->addCabecalho( 'Unidade de Medida' , 7  );
    $table->Head->addCabecalho( 'Marca'             , 7  );
    $table->Head->addCabecalho( 'Centro de Custo'   , 20 );
    $table->Head->addCabecalho( 'Qtde.'             , 7  );
    $table->Head->addCabecalho( 'Valor Total'       , 10 );

    $table->Body->addCampo( 'stItem'          , "E" );
    $table->Body->addCampo( 'stUnidadeMedida' , "C" );
    $table->Body->addCampo( 'stMarca'         , "C" );
    $table->Body->addCampo( 'stCentroCusto'   , "E" );
    $table->Body->addCampo( 'inQtdeEntrada'   , "D" );
    $table->Body->addCampo( 'vlTotalItem'     , "D" );

    $table->Body->addAcao( "ALTERAR", 'alterarEntrada(%d)', array( 'inCheckBoxId' ) );
    $table->Body->addAcao( "EXCLUIR", 'excluirEntrada(%d)', array( 'inCodItem' ) );

    $table->Foot->addSoma( 'vlTotalItem', "D" );

    $table->montaHTML( true );
    $stHTML = $table->getHtml();

    return "$('spnItensEntrada').innerHTML = '".$stHTML."';";
}

// Função que valida os dados para inserir na listagem de entrada os itens
function validaListaEntrada(Request $request)
{
    $stMensagem = '';

    $inCodItem = $request->get('inCodItem');
    $boCodItem = $request->get('boCodItem');

    if (empty($inCodItem)) {
        if($boCodItem)
            $stMensagem = "Informe o campo Código do Item.";
        else
            $stMensagem = "Selecione um Item na listagem.";
    }else{
        $boAlterarItem = $request->get('boAlterarItem');

        if($boCodItem && !$boAlterarItem){
            $arItensEntrada = Sessao::read('arItensEntrada');
            if(is_array($arItensEntrada)){
                foreach ($arItensEntrada as $chave => $valor) {
                    if ($valor['inCodItem'] == $inCodItem) {
                        $stMensagem = "Código do Item(".$inCodItem.") já consta na lista de Itens da Ordem de Compra para Entrada.";
                        break;
                    }
                }
            }

            if(empty($stMensagem)){
                $arItensAtendido = Sessao::read('arItensAtendido');
                if(is_array($arItensAtendido)){
                    foreach ($arItensAtendido as $chave =>$valor) {
                        if($valor['cod_item'] == $inCodItem) {
                            $stMensagem = "Código do Item(".$inCodItem.") já consta na lista de Itens Atendidos.";
                            break;
                        }
                    }
                }
            }

            if(empty($stMensagem)){
                $arItens = Sessao::read('arItens');
                if(is_array($arItens)){
                    foreach ($arItens as $chave =>$valor) {
                        if($valor['cod_item'] == $inCodItem) {
                            $stMensagem = "Código do Item(".$inCodItem.") já consta na lista de Itens a serem Atendidos.";
                            break;
                        }
                    }
                }
            }
        }
    }

    if ( Sessao::read('boPerecivel') && empty($stMensagem) ) {
        $arItensPerecivel = Sessao::read('arItensPerecivel');
        if ( count($arItensPerecivel[$inCodItem]) < 1 )
            $stMensagem = "Deve ser incluído os dados perecíveis do item.";
    }

    if ($request->get('inCodAlmoxarifado','') == '' && empty($stMensagem) )
        $stMensagem = "O Almoxarifado precisa ser selecionado.";

    if ($request->get('inCodCentroCusto','') == '' && empty($stMensagem) )
        $stMensagem = "O Centro de Custo precisa ser selecionado.";

    if ($request->get('inCodMarca','') == '' && empty($stMensagem) )
        $stMensagem = "A Marca precisa ser selecionada.";

    $inQtdeDisponivel = str_replace(",", ".", str_replace(".", "", $request->get('inQtdeDisponivel')));
    $inQtdeEntrada = str_replace(",", ".", str_replace(".", "", $request->get('inQtdeEntrada')));

    if(empty($stMensagem)){
        if ($inQtdeEntrada == '')
            $stMensagem = "A Quantidade precisa ser preenchida.";
        elseif (!($inQtdeEntrada > 0))
            $stMensagem = "A Quantidade deve ser maior que zero.";
        elseif ($inQtdeEntrada > $inQtdeDisponivel)
            $stMensagem = "A quantidade informada ultrapassa a quantidade disponivel do item selecionado.";
    }

    if(empty($stMensagem)){
        if ($request->get('flValorTotalMercado','') == '')
            $stMensagem = "O Valor Total de Mercado precisa ser preenchido.";
        else {
            $flValorTotalMercado = str_replace(",", ".", str_replace(".", "", $request->get('flValorTotalMercado')));
            if ($flValorTotalMercado <= 0)
                $stMensagem = "O Valor Total de Mercado precisa ser maior que zero.";
        }
    }

    if(!empty($stMensagem))
        return "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
}

// Função que limpa os dados quando o item vai para a listagem de entrada
function limpaDadosEntrada($inCheckBoxId)
{
    $stJs  = "\n frm.inCodAlmoxarifado.value = '';";
    $stJs .= "\n $('inCodMarca').value = '';";
    $stJs .= "\n $('stMarca').value = '&nbsp;';";
    $stJs .= "\n $('inCodCentroCusto').value = '';";
    $stJs .= "\n $('inQtdeEntrada').value = '';";
    $stJs .= "\n $('flValorTotalMercado').value = '';";
    $stJs .= "\n $('spnDetalheItem').innerHTML = '';";

    // desmarca a radio do item selecionado
    $stJs .= "\n $('item_".$inCheckBoxId."').checked = false;";

    return $stJs;
}

function verificaItemPerecivel($inCodItem){
    Sessao::write('boPerecivel', false);
    Sessao::write('boBemPatrimonial', false);

    $rsCatalogoItem = new RecordSet();

    if(!empty($inCodItem)){
        require_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php";

        $obTAlmoxarifadoCatalagoItem = new TAlmoxarifadoCatalogoItem;
        $obTAlmoxarifadoCatalagoItem->setDado( "cod_item", (!empty($inCodItemAjustado)) ? $inCodItemAjustado : $inCodItem );
        $obTAlmoxarifadoCatalagoItem->recuperaPorChave($rsCatalogoItem);

        if ($rsCatalogoItem->getCampo('cod_tipo') == 2 )
            Sessao::write('boPerecivel', true);

        if ($rsCatalogoItem->getCampo('cod_tipo') == 4 )
            Sessao::write('boBemPatrimonial', true);
    }

    return $rsCatalogoItem;
}

function verificaItemAtributo($inCodItem){
    Sessao::write('boAtributo', false);

    $rsAtributos = new RecordSet();

    if(!empty($inCodItem)){
        require_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoItem.class.php";

        $obTAtributoCatalogoItem = new TAlmoxarifadoAtributoCatalogoItem();
        $obTAtributoCatalogoItem->setDado("cod_item", $inCodItem );
        $obTAtributoCatalogoItem->recuperaAtributoCatalogoItem( $rsAtributos );
        if ( $rsAtributos->getNumLinhas() > 0 ){
            Sessao::write('boAtributo', true);

            $inCont = 0;
            $arItensAtributo = Sessao::read('arItensAtributo');
            if(isset($arItensAtributo[$inCodItem])){
                while ( !$rsAtributos->eof() ) {
                    $rsAtributos->arElementos[$inCont]['valor_padrao'] = $arItensAtributo[$inCodItem][$inCont]['stValor'];
                    $rsAtributos->proximo();
                    $inCont++;
                }
            }
            $rsAtributos->setPrimeiroElemento();
        }
    }

    return $rsAtributos;
}

function montaPerecivel($inCodItem = ''){
    $obTxtNumLotePerecivel = new Inteiro();
    $obTxtNumLotePerecivel->setRotulo    ( 'Número do Lote' );
    $obTxtNumLotePerecivel->setName      ( "inNumLotePerecivel" );
    $obTxtNumLotePerecivel->setId        ( "inNumLotePerecivel" );
    $obTxtNumLotePerecivel->setTitle     ( "Informe o número do lote.");
    $obTxtNumLotePerecivel->setNull      ( true );
    $obTxtNumLotePerecivel->setObrigatorioBarra( true );

    // data de fabricação do perecivel
    $obDtFabricacaoPerecivel = new Data();
    $obDtFabricacaoPerecivel->setRotulo    ( 'Data de Fabricação' );
    $obDtFabricacaoPerecivel->setName      ( "dtFabricacaoPerecivel" );
    $obDtFabricacaoPerecivel->setId        ( "dtFabricacaoPerecivel" );
    $obDtFabricacaoPerecivel->setTitle     ( "Informe a data de fabricação.");
    $obDtFabricacaoPerecivel->setNull      ( true );
    $obDtFabricacaoPerecivel->setObrigatorioBarra( true );

    // data de validade do perecivel
    $obDtValidadePerecivel = new Data();
    $obDtValidadePerecivel->setRotulo    ( 'Data de Validade' );
    $obDtValidadePerecivel->setName      ( "dtValidadePerecivel" );
    $obDtValidadePerecivel->setId        ( "dtValidadePerecivel" );
    $obDtValidadePerecivel->setTitle     ( "Informe a data de validade.");
    $obDtValidadePerecivel->setNull      ( true );
    $obDtValidadePerecivel->setObrigatorioBarra( true );

    // campo quantidade do perecível
    $obQtdePerecivel = new Quantidade();
    $obQtdePerecivel->setName( 'inQtdePerecivel' );
    $obQtdePerecivel->setId( 'inQtdePerecivel' );
    $obQtdePerecivel->setNull(false);
    $obQtdePerecivel->setObrigatorioBarra( false );

    $obHdnInNumLinhaListaPerecivel = new Hidden();
    $obHdnInNumLinhaListaPerecivel->setName( 'inNumLinhaListaPerecivel' );
    $obHdnInNumLinhaListaPerecivel->setId( 'inNumLinhaListaPerecivel' );

    $obSpnItensPereciveis = new Span();
    $obSpnItensPereciveis->setId( 'spnItensPereciveis' );

    $obFormulario = new Formulario();

    $obFormulario->addTitulo    ( 'Perecível'                       );
    $obFormulario->addComponente( $obTxtNumLotePerecivel            );
    $obFormulario->addComponente( $obDtFabricacaoPerecivel          );
    $obFormulario->addComponente( $obDtValidadePerecivel            );
    $obFormulario->addComponente( $obQtdePerecivel                  );
    $obFormulario->addHidden    ( $obHdnInNumLinhaListaPerecivel    );

    $obIncluir = new Button;
    $obIncluir->setValue            ( 'Incluir Lotes');
    $obIncluir->setName             ( 'Incluir');
    $obIncluir->setId               ( 'Incluir');
    $obIncluir->obEvento->setOnClick( "montaParametrosGET('montaListaItensPerecivel', 'inCodItem, inNumLotePerecivel, dtFabricacaoPerecivel, dtValidadePerecivel, inQtdePerecivel, inNumLinhaListaPerecivel, inQtdeEntrada, inQtdeDisponivel, Incluir, acaoPerecivel, inQtdeUltimoPerecivel'); jQuery('#Incluir').attr('disabled', 'disabled');");

    $obLimpar = new Button;
    $obLimpar->setValue             ( 'Limpar');
    $obLimpar->setName              ( 'Limpar');
    $obLimpar->setId                ( 'Limpar');
    $obLimpar->obEvento->setOnClick ( "executaFuncaoAjax('limpaDadosPerecivel', '')");

    $obFormulario->defineBarra( array($obIncluir, $obLimpar) );
    $obFormulario->addSpan( $obSpnItensPereciveis );

    $obFormulario->montaInnerHTML();

    $stJs  = "jQuery('#spnPerecivel').html('".$obFormulario->getHTML()."');";
    $stJs .= "jQuery('#inQtdeEntrada').val('');";
    $stJs .= "jQuery('#inQtdeEntrada').attr('disabled', 'disabled');";

    if(!empty($inCodItem)){
        $arItensPerecivel = Sessao::read('arItensPerecivel');
        $inQtdeEntrada = 0;
        if(is_array($arItensPerecivel[$inCodItem])){
            foreach ($arItensPerecivel[$inCodItem] as $chave => $valor) {
                $inQtdeEntrada += str_replace(",",  ".", str_replace(".", "", $valor['inQtdePerecivel']));
            }
        }

        $inQtdeEntrada = number_format($inQtdeEntrada, 4, ",", ".");
        $stJs .= "jQuery('#inQtdeEntrada').val('".$inQtdeEntrada."');";
    }

    return $stJs;
}

function montaAtributos($rsAtributos){
    $obMontaAtributos = new MontaAtributos;
    $obMontaAtributos->setTitulo     ( "Atributos do Item no Estoque"  );
    $obMontaAtributos->setName       ( "Atributos_" );
    $obMontaAtributos->setRecordSet  ( $rsAtributos );
    $obMontaAtributos->recuperaValores();

    $obFormulario = new Formulario();
    $obMontaAtributos->geraFormulario ( $obFormulario );
    $obFormulario->montaInnerHTML();

    $stJs = "jQuery('#spnAtributo').html('".$obFormulario->getHTML()."');";

    return $stJs;
}

function montaPatrimonial($inNumLinhaEntrada = ''){
    $arItensEntrada = Sessao::read('arItensEntrada');

    $obSpnNumeroPlaca = new Span();
    $obSpnNumeroPlaca->setId( 'spnNumeroPlaca' );

    //instancio o componente TextBoxSelect para a situacao do bem
    $obITextBoxSelectSituacao = new TextBoxSelect();
    $obITextBoxSelectSituacao->setRotulo( 'Situação'                   );
    $obITextBoxSelectSituacao->setTitle ( 'Informe a situação do bem.' );
    $obITextBoxSelectSituacao->setName  ( 'inCodTxtSituacao'           );
    $obITextBoxSelectSituacao->setNull  ( false                        );

    $obITextBoxSelectSituacao->obTextBox->setName      ( "inCodTxtSituacao" );
    $obITextBoxSelectSituacao->obTextBox->setId        ( "inCodTxtSituacao" );
    $obITextBoxSelectSituacao->obTextBox->setSize      ( 6                  );
    $obITextBoxSelectSituacao->obTextBox->setMaxLength ( 3                  );
    $obITextBoxSelectSituacao->obTextBox->setInteiro   ( true               );
    $obITextBoxSelectSituacao->obTextBox->setValue     ( $arItensEntrada[$inNumLinhaEntrada]['inCodSituacao'] );

    $obITextBoxSelectSituacao->obSelect->setName       ( "inCodSituacao" );
    $obITextBoxSelectSituacao->obSelect->setId         ( "inCodSituacao" );
    $obITextBoxSelectSituacao->obSelect->setStyle      ( "width: 200px"  );
    $obITextBoxSelectSituacao->obSelect->setCampoID    ( "cod_situacao"  );
    $obITextBoxSelectSituacao->obSelect->setCampoDesc  ( "nom_situacao"  );
    $obITextBoxSelectSituacao->obSelect->addOption     ( "", "Selecione" );

    //recupero todos os registros da table patrimonio.situacao_bem e preencho o componenete ITextBoxSelect
    $obTPatrimonioSituacaoBem = new TPatrimonioSituacaoBem();
    $obTPatrimonioSituacaoBem->recuperaTodos( $rsSituacaoBem );

    $obITextBoxSelectSituacao->obSelect->preencheCombo( $rsSituacaoBem );
    $obITextBoxSelectSituacao->obSelect->setValue( $arItensEntrada[$inNumLinhaEntrada]['inCodSituacao'] );

    $obRdPlacaIdentificacaoSim = new Radio();
    $obRdPlacaIdentificacaoSim->setRotulo ( 'Placa de Identificação' );
    $obRdPlacaIdentificacaoSim->setName   ( 'stPlacaIdentificacao'   );
    $obRdPlacaIdentificacaoSim->setValue  ( 'sim'                    );
    $obRdPlacaIdentificacaoSim->setLabel  ( 'Sim'                    );
    $obRdPlacaIdentificacaoSim->setTitle  ( 'Informe se o item possui placa de identificação.' );
    $obRdPlacaIdentificacaoSim->obEvento->setOnClick( "montaParametrosGET( 'montaPlacaIdentificacao', 'stPlacaIdentificacao,inNumLinhaEntrada' );" );

    $obRdPlacaIdentificacaoNao = new Radio();
    $obRdPlacaIdentificacaoNao->setRotulo ( 'Placa de Identificação' );
    $obRdPlacaIdentificacaoNao->setName   ( 'stPlacaIdentificacao'   );
    $obRdPlacaIdentificacaoNao->setValue  ( 'nao'                    );
    $obRdPlacaIdentificacaoNao->setLabel  ( 'Não'                    );
    $obRdPlacaIdentificacaoNao->setTitle  ( 'Informe se o item possui placa de identificação' );
    $obRdPlacaIdentificacaoNao->obEvento->setOnClick( "montaParametrosGET( 'montaPlacaIdentificacao', 'stPlacaIdentificacao,inNumLinhaEntrada' );" );

    if ( ($arItensEntrada[$inNumLinhaEntrada]['stPlacaIdentificacao'] == 'sim') || ($arItensEntrada[$inNumLinhaEntrada]['stPlacaIdentificacao'] == '' )) {
        $obRdPlacaIdentificacaoSim->setChecked( true );
        $montaPlaca = true;
    } else {
        $obRdPlacaIdentificacaoNao->setChecked( true );
        $montaPlaca = false;
    }

    $obFormulario = new Formulario();
    $obFormulario->addTitulo( 'Detalhes Bem Patrimonial' );
    $obFormulario->addComponente( $obITextBoxSelectSituacao );
    $obFormulario->agrupaComponentes( array( $obRdPlacaIdentificacaoSim, $obRdPlacaIdentificacaoNao ) );
    $obFormulario->addSpan( $obSpnNumeroPlaca );
    $obFormulario->montaInnerHTML();

    $stJs  = "jQuery('#spnPatrimonial').html('".$obFormulario->getHTML()."');";
    $stJs .= montaPlacaIdentificacao($inNumLinhaEntrada, $montaPlaca);

    return $stJs;
}

// Monta a tela de detalhes do item selecionado
function montaFormDadosItem($inItemId)
{
    // require dos arquivos necessários
    require_once CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php";
    require_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoItem.class.php";
    require_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php";
    require_once CAM_FW_HTML."MontaAtributos.class.php";
    require_once CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php";
    require_once CAM_GP_ALM_COMPONENTES."IPopUpCentroCustoUsuario.class.php";

    // inicializa os valores para saber se o item tem atributo e/ou se é perecível
    Sessao::write('boAtributo'      , false);
    Sessao::write('boPerecivel'     , false);
    Sessao::write('boBemPatrimonial', false);

    /*********************************************************
    *   Span de listagem de itens, caso o item seja perecível
    **********************************************************/
    $obSpnPerecivel = new Span();
    $obSpnPerecivel->setId( 'spnPerecivel' );

    /**********************************************************
    *                       Monta os campo
    **********************************************************/

    // $inItemId guarda o id do item selecionado. $arItemId[1] vai guardar o numero da linha.
    $arItemId = explode("_", $inItemId );

    // guarda o numero da linha do array de itens
    $inNumLinha = $arItemId[1]-1;

    $arItens        = Sessao::read('arItens');
    $arItensEntrada = Sessao::read('arItensEntrada');
    $arItemLinha    = Sessao::read('arItemLinha');

    // Faz um explode to nome do item, separando id da descrição
    $arItem = explode(" - ", $arItens[$inNumLinha]["nom_item"]);
    $inCodItem = $arItem[0];
    $stItem    = $arItem[1];

    $inNumItem = $arItens[$inNumLinha]["num_item"];

    // recebe a unidade de medida do item
    $stUnidadeMedida = $arItens[$inNumLinha]["nom_unidade"];

    // atribui o valor a variável para poder fazer as verificações para saber se é para alterar o item ou não
    $boAlterarItem = false;

    $inCodItemAjustado = '';

    if(!empty($stItem)){
        $stDescricaoItem = $inCodItem.' - '.$stItem;

        if ( isset( $arItemLinha[$inCodItem] ) ) {
            $inNumLinhaEntrada = $arItemLinha[$inCodItem];

            foreach ($arItensEntrada as $chave =>$dados) {
                if ($dados['inNumItem'] == $inNumItem)
                    $boAlterarItem = true;
            }
        }
    }else{
        $stDescricaoItem = $inCodItem;

        foreach ($arItensEntrada as $chave =>$dados) {
            if ($dados['inNumItem'] == $inNumItem) {
                $boAlterarItem = true;
                $inNumLinhaEntrada = $arItemLinha[$dados['inCodItem']];
                $inCodItemAjustado = $dados['inCodItem'];
                $stNomItemAjustado = SistemaLegado::pegaDado('descricao', 'almoxarifado.catalogo_item', 'WHERE cod_item = '.$inCodItemAjustado);
            }
        }

        if(empty($inCodItemAjustado) && !empty($arItens[$inNumLinha]["cod_item"])){
            $inCodItemAjustado = $arItens[$inNumLinha]["cod_item"];
            $stNomItemAjustado = SistemaLegado::pegaDado('descricao', 'almoxarifado.catalogo_item', 'WHERE cod_item = '.$inCodItemAjustado);
            list($inCodCentroCustoAjustado, $stNomCentroCustoAjustado) = explode(" - ", $arItens[$inNumLinha]["centro_custo"]);
        }
    }

    //--------------------------------------
    // Monta atributos do item (caso tenha)
    //--------------------------------------

    // se não for, faz uma pesquisa para ver se o item tem algum atributo
    $rsAtributos = new RecordSet();
    if(!empty($stItem) || !empty($inCodItemAjustado))
        $rsAtributos = verificaItemAtributo((!empty($inCodItemAjustado)) ? $inCodItemAjustado : $inCodItem);

    // caso o item tenha atributo, gera os campos no formulario
    $obSpnAtributo = new Span();
    $obSpnAtributo->setId( 'spnAtributo' );

    // ---------------------------------------
    // monta dados Item Perecível (caso tenha)
    // ---------------------------------------

    // faz uma busca verificando se o cod_tipo = 2 (perecível) ou cod_tipo = 4 (patrimonial)
    $rsCatalogoItem = new RecordSet();
    if(!empty($stItem) || !empty($inCodItemAjustado))
        $rsCatalogoItem = verificaItemPerecivel((!empty($inCodItemAjustado)) ? $inCodItemAjustado : $inCodItem);

    //Item do tipo Bem Patrimonial
    $obSpnPatrimonial = new Span();
    $obSpnPatrimonial->setId( 'spnPatrimonial' );

    // ---------------------------------------
    // monta os campos obrigatórios do item
    // ---------------------------------------

    // cria o objeto do form
    $obForm = new Form();

    // Combo de Almoxarifado
    $obSelectAlmoxarifado = new ISelectAlmoxarifadoAlmoxarife();
    $obSelectAlmoxarifado->setObrigatorioBarra(true);
    if ($boAlterarItem)
        $obSelectAlmoxarifado->setCodAlmoxarifado( $arItensEntrada[$inNumLinhaEntrada]['inCodAlmoxarifado'] );

    $obTComprasOrdemCompra = new TComprasOrdem();
    $arItens = Sessao::read('arItens');
    $obTComprasOrdemCompra->setDado( "cod_ordem", $arItens[$inNumLinha]["cod_ordem"] );
    $obTComprasOrdemCompra->setDado( "tipo", "C");
    if(!empty($stItem))
        $stFiltro = " \n and centro_custo.cod_item = ".$inCodItem;
    else
        $stFiltro = " \n and centro_custo.num_item = ".$inNumItem;
    // Faz a busca dos possiveis centros de custo em relação a ordem de compra
    $obTComprasOrdemCompra->recuperaCentroCustoPorOrdemCompra($rsOrdemCompraCentroCusto, $stFiltro);

    if (!$boAlterarItem) {
        // Faz a busca da marca para 'forçar' o valor setado
        $obTComprasOrdemCompra->recuperaMarcaPorOrdemCompra( $rsOrdemCompraMarca, $stFiltro );
    }

    $obIPopUpCatalogoItem = new IPopUpItem($obForm);
    $obIPopUpCatalogoItem->setRotulo            ( 'Código do Item' );
    $obIPopUpCatalogoItem->setNull              ( true        );
    $obIPopUpCatalogoItem->setRetornaUnidade    ( false       );
    $obIPopUpCatalogoItem->setObrigatorioBarra  ( true        );
    $obIPopUpCatalogoItem->setId                ( 'stNomItem' );
    $obIPopUpCatalogoItem->obCampoCod->setName  ( 'inCodItem' );
    $obIPopUpCatalogoItem->obCampoCod->setId    ( 'inCodItem' );
    $obIPopUpCatalogoItem->obCampoCod->setValue ( $inCodItemAjustado );
    $obIPopUpCatalogoItem->setValue             ( $stNomItemAjustado );
    $obIPopUpCatalogoItem->obCampoCod->obEvento->setOnChange( "montaParametrosGET( 'verificaItemPerecivel', 'inCodItem' );" );
    $obIPopUpCatalogoItem->obCampoCod->obEvento->setOnBlur  ( "if(this.value==''){ montaParametrosGET( 'verificaItemPerecivel', 'inCodItem' ); }" );
    if($inCodItemAjustado != '')
        $obIPopUpCatalogoItem->setLabel ( TRUE );

    $obCentroCustoUsuario = new IPopUpCentroCustoUsuario($obForm);
    $obCentroCustoUsuario->setNull              ( true               );
    $obCentroCustoUsuario->setObrigatorioBarra  ( true               );
    $obCentroCustoUsuario->setRotulo            ( 'Centro de Custo'  );
    $obCentroCustoUsuario->obCampoCod->setId    ( 'inCodCentroCusto' );
    $obCentroCustoUsuario->obCampoCod->setName  ( 'inCodCentroCusto' );
    $obCentroCustoUsuario->setId                ( 'stNomCentroCusto' );
    if ($boAlterarItem) {
        $obCentroCustoUsuario->obCampoCod->setValue ( $arItensEntrada[$inNumLinhaEntrada]['inCodCentroCusto'] );
        $obCentroCustoUsuario->setValue             ( $arItensEntrada[$inNumLinhaEntrada]['stCentroCusto']    );
    }elseif(isset($inCodCentroCustoAjustado)){
        $obCentroCustoUsuario->obCampoCod->setValue ( $inCodCentroCustoAjustado );
        $obCentroCustoUsuario->setValue             ( $stNomCentroCustoAjustado );
        $obCentroCustoUsuario->setLabel             ( TRUE );
    }

    if ($boAlterarItem && !empty($stItem))
        $inCodCentroCusto = $arItensEntrada[$inNumLinhaEntrada]['inCodCentroCusto'];
    else
        $inCodCentroCusto = $rsOrdemCompraCentroCusto->getCampo('cod_centro');

    $obCentroCusto = new Select();
    $obCentroCusto->setTitle     ( "Informe o centro de custo."    );
    $obCentroCusto->setName      ( 'inCodCentroCusto'              );
    $obCentroCusto->setId        ( 'inCodCentroCusto'              );
    $obCentroCusto->setCampoId   ( 'cod_centro'                    );
    $obCentroCusto->addOption    ( '', 'Selecione'                 );
    $obCentroCusto->setCampoDesc ( '[cod_centro] - [centro_custo]' );
    $obCentroCusto->setRotulo    ( 'Centro de Custo'               );
    $obCentroCusto->obEvento->setOnChange( ""                      );
    $obCentroCusto->preencheCombo( $rsOrdemCompraCentroCusto       );
    $obCentroCusto->setNull      ( true                            );
    $obCentroCusto->setObrigatorioBarra( true                      );
    $obCentroCusto->setValue     ( $inCodCentroCusto               );

    // Campo Marca
    $obMarca = new IPopUpMarca($obForm);
    $obMarca->setTitle ( "Informe a marca do item." );
    $obMarca->obCampoCod->setId   ( 'inCodMarca'    );
    $obMarca->obCampoCod->setName ( 'inCodMarca'    );
    $obMarca->setId               ( 'stMarca'       );
    $obMarca->setName             ( 'stMarca'       );
    $obMarca->setNull             ( true            );
    $obMarca->setObrigatorioBarra ( true            );
    if ($boAlterarItem) {
        $obMarca->obCampoCod->setValue( $arItensEntrada[$inNumLinhaEntrada]['inCodMarca'] );
        $obMarca->setValue            ( $arItensEntrada[$inNumLinhaEntrada]['stMarca']    );
    } else {
        $obMarca->obCampoCod->setValue( $rsOrdemCompraMarca->getCampo('cod_marca') );
        $obMarca->setValue            ( $rsOrdemCompraMarca->getCampo('marca')     );
    }

    // Campo Quantidade
    $obQtdeEntrada = new Quantidade();
    $obQtdeEntrada->setName( 'inQtdeEntrada' );
    $obQtdeEntrada->setId  ( 'inQtdeEntrada' );
    $obQtdeEntrada->setNull( false           );
    if ($boAlterarItem)
        $obQtdeEntrada->setValue( $arItensEntrada[$inNumLinhaEntrada]['inQtdeEntrada'] );
    elseif ( !Sessao::read('boPerecivel') && !empty($stItem) ) {
        $inQtdeItem = ($arItens[$inNumLinha]['qtde_disponivel_oc']);
        $obQtdeEntrada->setValue( number_format($inQtdeItem, 4, ",", "." ) );
    }

    if ( Sessao::read('boPerecivel') )
        $obQtdeEntrada->setDisabled( true );

    // Campo Valor Total de Mercado
    $obVlTotalMercado = new TextBox();
    $obVlTotalMercado->setName    ( 'flValorTotalMercado'                );
    $obVlTotalMercado->setId      ( 'flValorTotalMercado'                );
    $obVlTotalMercado->setRotulo  ( 'Valor Unitário do Item'             );
    $obVlTotalMercado->setTitle   ( 'Informe o valor empenhado do item.' );
    $obVlTotalMercado->setNull    ( true                                 );
    $obVlTotalMercado->setReadOnly( true                                 );
    $obVlTotalMercado->setObrigatorioBarra( true                         );
    if ($boAlterarItem)
        $obVlTotalMercado->setValue( $arItensEntrada[$inNumLinhaEntrada]['flValorTotalMercado'] );
    else
        $obVlTotalMercado->setValue( $arItens[$inNumLinha]['vl_empenhado'] );

    $obTxtComplemento = new TextBox;
    $obTxtComplemento->setName      ( "stComplemento" );
    $obTxtComplemento->setId        ( "stComplemento" );
    $obTxtComplemento->setRotulo    ( "Complemento" );
    $obTxtComplemento->setTitle     ( "Informe um complemento para o item.");
    $obTxtComplemento->setNull      ( true );
    $obTxtComplemento->setMaxLength ( 160 );
    $obTxtComplemento->setSize      ( 100 );
    if ($boAlterarItem)
        $obTxtComplemento->setValue( $arItensEntrada[$inNumLinhaEntrada]['stComplemento'] );

    // ---------------------------------------
    // monta os hiddens do formulario
    // ---------------------------------------

    // Campo Hidden com o código do item
    $obHdnCodItem = new Hidden();
    $obHdnCodItem->setId( 'inCodItem' );
    $obHdnCodItem->setName( 'inCodItem' );
    $obHdnCodItem->setValue( $inCodItem );

    $obHdnBoCodItem = new Hidden();
    $obHdnBoCodItem->setId( 'boCodItem' );
    $obHdnBoCodItem->setName( 'boCodItem' );
    $obHdnBoCodItem->setValue( TRUE );

    // Hidden com o nro do checkbox do item.
    $obHdnCheckBoxId = new Hidden();
    $obHdnCheckBoxId->setId( 'inCheckBoxId' );
    $obHdnCheckBoxId->setName( 'inCheckBoxId' );
    $obHdnCheckBoxId->setValue( $inItemId );

    // Campo Hidden com o código do item
    $obHdnItem = new Hidden();
    $obHdnItem->setId( 'stItem' );
    $obHdnItem->setName( 'stItem' );
    if(empty($stItem))
        $obHdnItem->setValue( $inCodItem );
    else
        $obHdnItem->setValue( $stItem );

    // Campo Hidden da unidade de medida do item
    $obHdnUnidadeMedida = new Hidden();
    $obHdnUnidadeMedida->setId( 'stUnidadeMedida' );
    $obHdnUnidadeMedida->setName( 'stUnidadeMedida' );
    $obHdnUnidadeMedida->setValue( $stUnidadeMedida );

    // hidden que recebe o id do item selecionado
    $obHdnNumItem = new Hidden();
    $obHdnNumItem->setId( 'inNumItem' );
    $obHdnNumItem->setName( 'inNumItem' );
    $obHdnNumItem->setValue( $arItens[$inNumLinha]['num_item'] );

    // hidden que recebe o codigo do tipo do item
    $obHdnCodTipoItem = new Hidden();
    $obHdnCodTipoItem->setId( 'inCodTipoItem' );
    $obHdnCodTipoItem->setName( 'inCodTipoItem' );
    $obHdnCodTipoItem->setValue( ($rsCatalogoItem->getCampo('cod_tipo')) );

    // hidden com a linha do array da lista de entrada
    $obHdnNumLinhaEntrada = new Hidden();
    $obHdnNumLinhaEntrada->setId( 'inNumLinhaEntrada' );
    $obHdnNumLinhaEntrada->setName( 'inNumLinhaEntrada' );
    if ($boAlterarItem)
        $obHdnNumLinhaEntrada->setValue( $inNumLinhaEntrada );

    $obHdnBoAlterarItem = new Hidden();
    $obHdnBoAlterarItem->setId( 'boAlterarItem' );
    $obHdnBoAlterarItem->setName( 'boAlterarItem' );
    $obHdnBoAlterarItem->setValue( $boAlterarItem );

    // hidden com a diferença disponível de quantidade para o item selecionado
    $obHdnQtdeDisponivel = new Hidden();
    $obHdnQtdeDisponivel->setId( 'inQtdeDisponivel' );
    $obHdnQtdeDisponivel->setName( 'inQtdeDisponivel' );
    $obHdnQtdeDisponivel->setValue( number_format($arItens[$inNumLinha]["qtde_disponivel_oc"], 4, ",", "." ) );

    $obHdnQtdeUltimoPerecivel = new Hidden;
    $obHdnQtdeUltimoPerecivel->setId  ('inQtdeUltimoPerecivel');
    $obHdnQtdeUltimoPerecivel->setName('inQtdeUltimoPerecivel');

    $obHdnAcaoPerecivel = new Hidden;
    $obHdnAcaoPerecivel->setId   ('acaoPerecivel');
    $obHdnAcaoPerecivel->setName ('acaoPerecivel');
    $obHdnAcaoPerecivel->setValue('incluir');

    /**********************************************************
    *                       Monta o formulario
    **********************************************************/
    $obFormulario = new Formulario();
    $obFormulario->addForm( $obForm );

    // se for um atributo, adiciona os atributos ao relatório
    $obFormulario->addSpan( $obSpnAtributo );

    $obFormulario->addTitulo( 'Detalhes do Item '.$stDescricaoItem );

    // adiciona os campos hidden necessários no formulario
    if(!empty($stItem))
        $obFormulario->addHidden( $obHdnCodItem    );
    else
        $obFormulario->addHidden( $obHdnBoCodItem  );

    $obFormulario->addHidden( $obHdnItem           );
    $obFormulario->addHidden( $obHdnUnidadeMedida  );
    $obFormulario->addHidden( $obHdnQtdeDisponivel );
    $obFormulario->addHidden( $obHdnNumItem        );
    $obFormulario->addHidden( $obHdnCheckBoxId     );
    $obFormulario->addHidden( $obHdnCodTipoItem    );
    $obFormulario->addHidden( $obHdnAcaoPerecivel  );
    $obFormulario->addHidden( $obHdnQtdeUltimoPerecivel );
    $obFormulario->addHidden( $obHdnNumLinhaEntrada     );
    $obFormulario->addHidden( $obHdnBoAlterarItem  );

    // adiciona os campos obrigatórios do item
    $obFormulario->addTitulo    ( 'Dados do Item'       );
    $obFormulario->addComponente( $obSelectAlmoxarifado );

    if(empty($stItem))
        $obFormulario->addComponente( $obIPopUpCatalogoItem );

    if(empty($inCodCentroCusto))
        $obFormulario->addComponente( $obCentroCustoUsuario );
    else
        $obFormulario->addComponente( $obCentroCusto    );

    $obFormulario->addComponente( $obMarca              );

    $obFormulario->addComponente( $obQtdeEntrada        );
    $obFormulario->addComponente( $obVlTotalMercado     );
    $obFormulario->addComponente( $obTxtComplemento     );

    $obFormulario->addSpan( $obSpnPatrimonial );

    $obFormulario->addSpan( $obSpnPerecivel );

    $inNumeroTotalItens = count(Sessao::read('arItens'));

    $arInfoChecks = explode("_", $inItemId);
    $inCheckBoxId = $arInfoChecks[1];

    $obCheckBoxProxProduto = new CheckBox;
    $obCheckBoxProxProduto->setRotulo('Ir para o próximo item');
    $obCheckBoxProxProduto->setValue('1');
    $obCheckBoxProxProduto->setChecked(Sessao::read('irProximoItem') == ""?false:true);
    $obCheckBoxProxProduto->setName('inProxItem');
    $obCheckBoxProxProduto->setId('inProxItem');
    $obCheckBoxProxProduto->obEvento->setOnClick('setarCheckBox();');

    if ($inNumeroTotalItens <= $inCheckBoxId)
        $obCheckBoxProxProduto->setDisabled(true);

    $obFormulario->addComponente( $obCheckBoxProxProduto );

    // monta os botões do formulario
    $obIncluir = new Button();
    $obIncluir->setValue            ( 'Salvar'                                   );
    $obIncluir->setName             ( 'incluirEntrada'                           );
    $obIncluir->setId               ( 'incluirEntrada'                           );
    $obIncluir->obEvento->setOnClick( "montaParametrosGET('incluirItemEntrada')" );

    $obLimpar = new Button();
    $obLimpar->setValue            ( 'Limpar'                                                  );
    $obLimpar->setName             ( 'limparEntrada'                                           );
    $obLimpar->setId               ( 'limparEntrada'                                           );
    $obLimpar->obEvento->setOnClick( "montaParametrosGET('limparDadosItens', 'inOrdemCompra')" );

    $obFormulario->defineBarra( array($obIncluir, $obLimpar) );
    $obFormulario->montaInnerHTML();

    $stJs .= "$('spnDetalheItem').innerHTML = '".$obFormulario->getHTML()."';";

    // se for um atributo, adiciona os atributos ao relatório
    if (Sessao::read('boAtributo'))
        $stJs .= montaAtributos($rsAtributos);

    // se for perecivel, adiciona os campos de perecível
    if ( Sessao::read('boPerecivel') )
        $stJs .= montaPerecivel((!empty($inCodItemAjustado)) ? $inCodItemAjustado : $inCodItem);

    //monta o text da placa de identificação por padrão
    if (Sessao::read('boBemPatrimonial'))
        $stJs .= montaPatrimonial($inNumLinhaEntrada);

    return $stJs;
}

function montaPlacaIdentificacao($inNumLinhaEntrada = "", $boMontaPlaca = false)
{
    include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
    $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
    $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
    $obTAdministracaoConfiguracao->pegaConfiguracao( $boPlacaAlfa, 'placa_alfanumerica' );

    $stJs = "$('spnNumeroPlaca').innerHTML = '';";

    if ($boMontaPlaca == true) {
        $obTxtNumeroPlaca = new TextBox();
        $obTxtNumeroPlaca->setRotulo( 'Número da Placa' );
        $obTxtNumeroPlaca->setTitle( 'Informe o número da placa do bem.' );
        $obTxtNumeroPlaca->setName( 'stNumeroPlaca' );
        $obTxtNumeroPlaca->setId( 'stNumeroPlaca' );
        $obTxtNumeroPlaca->setNull( false );

        if ($boPlacaAlfa == 'false')
            $obTxtNumeroPlaca->setInteiro (true);
        else
            $obTxtNumeroPlaca->setCaracteresAceitos( "[a-zA-Z0-9\-]" );

        $obTPatrimonioBem = new TPatrimonioBem();

        if ($boPlacaAlfa == 'true') {
            $obTPatrimonioBem->recuperaMaxNumPlacaAlfanumerico($rsNumPlaca);
            $maxNumeroPlaca = $rsNumPlaca->getCampo('num_placa');
        } else {
            $obTPatrimonioBem->recuperaMaxNumPlacaNumerico($rsNumPlaca);

            if ( $rsNumPlaca->getNumLinhas() <=0 )
                $inMaiorNumeroPlaca = 0;
            else
                $inMaiorNumeroPlaca = $rsNumPlaca->getCampo('num_placa');

            $maxNumeroPlaca = $inMaiorNumeroPlaca;
        }

        $arItensEntrada = Sessao::read('arItensEntrada');
        # Adiciona o total de quantidades na lista para sugerir a numeração da placa.
        if (is_array($arItensEntrada)) {
            foreach ($arItensEntrada as $key => $value) {
                if ($inNumLinhaEntrada !== $key && $value['stNumeroPlaca'] > $maxNumeroPlaca)
                    $maxNumeroPlaca = $value['stNumeroPlaca'];
            }
        }

        # Incrementa a sugestão do num_placa considerando as quantidades selecionadas no itens anteriores.
        $maxNumeroPlaca++;

        if($inNumLinhaEntrada!=='' && !empty($arItensEntrada[$inNumLinhaEntrada]['stNumeroPlaca']))
            $maxNumeroPlaca = $arItensEntrada[$inNumLinhaEntrada]['stNumeroPlaca'];

        $obTxtNumeroPlaca->setValue( $maxNumeroPlaca );
        $obTxtNumeroPlaca->obEvento->setOnChange( "montaParametrosGET( 'verificaIntervalo' );" );

        $obFormulario = new Formulario();
        $obFormulario->addComponente( $obTxtNumeroPlaca );
        $obFormulario->montaInnerHTML();

        $stJs = "$('spnNumeroPlaca').innerHTML = '".$obFormulario->getHTML()."';";
    }

    return $stJs;
}

switch ($stCtrl) {

// monta o span com os itens da ordem de compra
case 'montaItens':
    include_once TCOM.'TComprasNotaFiscalFornecedor.class.php';
    $obTComprasNotaFiscalFornecedor = new TComprasNotaFiscalFornecedor();
    $obTComprasNotaFiscalFornecedor->setDado( 'exercicio'   , $request->get('exercicio') );
    $obTComprasNotaFiscalFornecedor->setDado( 'cod_entidade', $request->get('cod_entidade') );
    $obTComprasNotaFiscalFornecedor->setDado( 'cod_ordem'   , $request->get('cod_ordem') );
    $obTComprasNotaFiscalFornecedor->setDado( 'tipo'        , 'C' );
    $obTComprasNotaFiscalFornecedor->recuperaItensNotaOrdemCompra( $rsItens );

    $inCount = 0;
    $inCountAtendido = 0;
    $arItens = null;
    $arItensAtendido = null;

    while ( !$rsItens->eof() ) {
        if ($rsItens->getCampo('ativo') == true) {
            $inCodItem = $rsItens->getCampo('cod_item');
            $stNomItem = $rsItens->getCampo('nom_item');
            if(!empty($inCodItem) && $rsItens->getCampo('bo_item') == 't')
                $stNomItem = $inCodItem.' - '.$stNomItem;

            if ($rsItens->getCampo('qtde_disponivel_oc') > 0) {
                $arItens[$inCount]['cod_ordem'         ] = $request->get('cod_ordem');
                $arItens[$inCount]['num_item'          ] = $rsItens->getCampo('num_item');
                $arItens[$inCount]['nom_item'          ] = $stNomItem;
                $arItens[$inCount]['cod_item'          ] = $inCodItem;
                $arItens[$inCount]['cod_pre_empenho'   ] = $rsItens->getCampo('cod_pre_empenho');
                $arItens[$inCount]['exercicio_empenho' ] = $rsItens->getCampo('exercicio_empenho');
                $arItens[$inCount]['nom_unidade'       ] = $rsItens->getCampo('nom_unidade');
                $arItens[$inCount]['centro_custo'      ] = ( $rsItens->getCampo('cod_centro') ) ? $rsItens->getCampo('cod_centro').' - '.$rsItens->getCampo('nom_centro') : null;
                $arItens[$inCount]['solicitado_oc'     ] = number_format($rsItens->getCampo('solicitado_oc'), 4, ',', '.');
                $arItens[$inCount]['atendido_oc'       ] = number_format($rsItens->getCampo('atendido_oc'), 4, ',', '.');
                $arItens[$inCount]['qtde_disponivel_oc'] = $rsItens->getCampo('qtde_disponivel_oc');
                $arItens[$inCount]['vl_empenhado'      ] = number_format($rsItens->getCampo('vl_empenhado'), 2, ',', '.');
                $inCount++;
            } else {
                $arItensAtendido[$inCountAtendido]['cod_ordem'         ] = $request->get('cod_ordem');
                $arItensAtendido[$inCountAtendido]['num_item'          ] = $rsItens->getCampo('num_item');
                $arItensAtendido[$inCountAtendido]['nom_item'          ] = $stNomItem;
                $arItensAtendido[$inCountAtendido]['cod_item'          ] = $inCodItem;
                $arItensAtendido[$inCountAtendido]['cod_pre_empenho'   ] = $rsItens->getCampo('cod_pre_empenho');
                $arItensAtendido[$inCountAtendido]['exercicio_empenho' ] = $rsItens->getCampo('exercicio_empenho');
                $arItensAtendido[$inCountAtendido]['nom_unidade'       ] = $rsItens->getCampo('nom_unidade');
                $arItensAtendido[$inCountAtendido]['centro_custo'      ] = ( $rsItens->getCampo('cod_centro') ) ? $rsItens->getCampo('cod_centro').' - '.$rsItens->getCampo('nom_centro') : null;
                $arItensAtendido[$inCountAtendido]['solicitado_oc'     ] = number_format($rsItens->getCampo('solicitado_oc'), 4, ',', '.');
                $arItensAtendido[$inCountAtendido]['atendido_oc'       ] = number_format($rsItens->getCampo('atendido_oc'), 4, ',', '.');
                $arItensAtendido[$inCountAtendido]['qtde_disponivel_oc'] = $rsItens->getCampo('qtde_disponivel_oc');
                $arItensAtendido[$inCountAtendido]['vl_empenhado'      ] = number_format($rsItens->getCampo('vl_empenhado'), 2, ',', '.');
                $inCountAtendido++;
            }
        }
        $rsItens->proximo();
    }

    // Caso não tenha nenhum item a ser atendido, não monta a lista
    if (count($arItens) > 0)
        $stJs.= montaListaItens( $arItens );

    // Caso não tenha nenhum item atendido, não monta a lista
    if (count($arItensAtendido) > 0)
        $stJs.= montaListaItensAtendidos( $arItensAtendido );

    Sessao::write('arItens', $arItens);
    Sessao::write('arItensAtendido', $arItensAtendido);

break;

// monta os delalhes dos itens da listagem dos itens da ordem de compra
case 'detalharItem' :
    include_once TCOM."TComprasOrdem.class.php";
    $obTComprasOrdemCompra = new TComprasOrdem();
    $obTComprasOrdemCompra->setDado( 'exercicio'        , $request->get('exercicio_empenho') );
    $obTComprasOrdemCompra->setDado( 'cod_pre_empenho'  , $request->get('cod_pre_empenho')   );
    $obTComprasOrdemCompra->setDado( 'num_item'         , $request->get('num_item')          );
    $obTComprasOrdemCompra->setDado( 'tipo'             , 'C' );
    $obTComprasOrdemCompra->recuperaDetalheItem( $rsDetalheItem );

    $obForm = new Form();
    $obForm->setName('detalharItem');
    $obForm->setId('detalharItem');

    if (!is_null($rsDetalheItem->getCampo('cod_item')) || !is_null($rsDetalheItem->getCampo('cod_item_ordem'))) {
        $obLblCodItem = new Label();
        $obLblCodItem->setRotulo( 'Código do Item' );
        $codItem = (!is_null($rsDetalheItem->getCampo('cod_item'))) ? $rsDetalheItem->getCampo('cod_item') : $rsDetalheItem->getCampo('cod_item_ordem');
        $obLblCodItem->setValue( $codItem );
    }else{
        $arItens         = Sessao::read('arItens');
        $arItensAtendido = Sessao::read('arItensAtendido');

        if(is_array($arItens)){
            foreach ($arItens as $chave =>$dados) {
                if(  $dados['exercicio_empenho'] == $request->get('exercicio_empenho')
                  && $dados['cod_pre_empenho'] == $request->get('cod_pre_empenho')
                  && $dados['num_item'] == $request->get('num_item')
                  && !empty($dados['cod_item'])
                ){
                    $inCodItemEntrada = $dados['cod_item'];
                    $stNomItemEntrada = SistemaLegado::pegaDado('descricao', 'almoxarifado.catalogo_item', 'WHERE cod_item = '.$inCodItemEntrada);
                }
            }
        }

        if(is_array($arItensAtendido) && !isset($inCodItemEntrada)){
            foreach ($arItensAtendido as $chave =>$dados) {
                if(  $dados['exercicio_empenho'] == $request->get('exercicio_empenho')
                  && $dados['cod_pre_empenho'] == $request->get('cod_pre_empenho')
                  && $dados['num_item'] == $request->get('num_item')
                  && !empty($dados['cod_item'])
                ){
                    $inCodItemEntrada = $dados['cod_item'];
                    $stNomItemEntrada = SistemaLegado::pegaDado('descricao', 'almoxarifado.catalogo_item', 'WHERE cod_item = '.$inCodItemEntrada);
                }
            }
        }

        if(isset($inCodItemEntrada)){
            $obLblItemEntrada = new Label();
            $obLblItemEntrada->setRotulo( 'Item de Entrada' );
            $obLblItemEntrada->setValue( $inCodItemEntrada.' - '.$stNomItemEntrada );
        }
    }

    $obLblItem = new Label();
    $obLblItem->setRotulo( 'Descrição' );
    $obLblItem->setValue( $rsDetalheItem->getCampo('descricao') );

    $obLblGrandeza = new Label();
    $obLblGrandeza->setRotulo( 'Grandeza' );
    $obLblGrandeza->setValue( $rsDetalheItem->getCampo('nom_grandeza') );

    $obLblUnidade = new Label();
    $obLblUnidade->setRotulo( 'Unidade' );
    $obLblUnidade->setValue( $rsDetalheItem->getCampo('nom_unidade') );

    $obFormulario = new Formulario();
    $obFormulario->addForm( $obForm );
    $obFormulario->addTitulo( 'Detalhe do Item' );
    if (!is_null($rsDetalheItem->getCampo('cod_item')) || !is_null($rsDetalheItem->getCampo('cod_item_ordem')))
        $obFormulario->addComponente( $obLblCodItem );
    $obFormulario->addComponente( $obLblItem );
    $obFormulario->addComponente( $obLblGrandeza );
    $obFormulario->addComponente( $obLblUnidade );
    if(isset($obLblItemEntrada))
        $obFormulario->addComponente( $obLblItemEntrada );
    $obFormulario->show();

    break;

// monta o span com os dados a serem preenchidos de cada item da ordem de compra
case 'montaDadosItem':
    // monta o formulario
    $stJs .= montaFormDadosItem( $request->get('item') );

    /* faz o mesmo processo que na montagem do formulario para poder pegar a linha
        pega item, que é o id do item, passado pelo onclick da listagem de itens*/
    $arItemId = explode("_", $request->get('item') );

    // pega a parte do numero da linha
    $inNumLinha = $arItemId[1]-1;

    $arItens          = Sessao::read('arItens');
    $arItemLinha      = Sessao::read('arItemLinha');
    $arItensEntrada   = Sessao::read('arItensEntrada');
    $arItensPerecivel = Sessao::read('arItensPerecivel');

    // Faz um explode to nome do item, separando id da descrição
    $arItem = explode(" - ", $arItens[$inNumLinha]["nom_item"]);
    $inCodItem = $arItem[0];
    $stItem    = $arItem[1];

    $inNumItem = $arItens[$inNumLinha]["num_item"];
    $inCodItemAjustado = '';

    // atribui o valor a variável para poder fazer as verificações para saber se é para alterar o item ou não
    if(!empty($stItem)){
        if ( isset( $arItemLinha[$inCodItem] ) ) {
            if (count($arItensEntrada[$inNumLinha]) > 0 )
                $stJs .= "$('incluirEntrada').value = 'Alterar';";
        }
    }else{
        foreach ($arItensEntrada as $chave =>$dados) {
            if ($dados['inNumItem'] == $inNumItem) {
                $inCodItemAjustado = $dados['inCodItem'];
                $stJs .= "$('incluirEntrada').value = 'Alterar';";
            }
        }
    }

    $arItemPerecivel = $arItensPerecivel[ (!empty($inCodItemAjustado)) ? $inCodItemAjustado : $inCodItem ];
    if ( count($arItemPerecivel) > 0 ){
        $request->set('inCodItem', (!empty($inCodItemAjustado)) ? $inCodItemAjustado : $inCodItem);
        $stJs .= montaListaItensPerecivel( $arItemPerecivel );
    }

    break;

// monta o span com a listagem dos dados dos itens que são perecíveis
case 'montaListaItensPerecivel':
    // valida os dados pereciveis do item, caso retorne algo é porque há algum erro para ser exibido na tela
    $retorno = validaListaPerecivel($request);

    // se não tiver erro entra e monta os arrays, caso contrario mostra o aviso na tela
    if ($retorno == "") {
        $arItensPerecivel = Sessao::read('arItensPerecivel');

        //verifica se existe algum valor no hidden, caso tenha, é porque está sendo alterado algum valor da listagem.
        if ($request->get('inNumLinhaListaPerecivel') != '')
            $inCont = $request->get('inNumLinhaListaPerecivel');
        else {
            $inCont = count($arItensPerecivel[$request->get('inCodItem')]);
            $arItensPerecivel[$request->get('inCodItem')][$inCont]['inNumLinhaListaPerecivel'] = $inCont;
            Sessao::write('arItensPerecivel', $arItensPerecivel);
        }

        // atribui os valores dos campos do transf3 para a montagem da listagem
        $arItensPerecivel[$request->get('inCodItem')][$inCont]['inNumLotePerecivel']    = $request->get('inNumLotePerecivel');
        $arItensPerecivel[$request->get('inCodItem')][$inCont]['dtFabricacaoPerecivel'] = $request->get('dtFabricacaoPerecivel');
        $arItensPerecivel[$request->get('inCodItem')][$inCont]['dtValidadePerecivel']   = $request->get('dtValidadePerecivel');
        $arItensPerecivel[$request->get('inCodItem')][$inCont]['inQtdePerecivel']       = $request->get('inQtdePerecivel');

        Sessao::write('arItensPerecivel', $arItensPerecivel);

        $inQtdeEntrada = 0;
        foreach ($arItensPerecivel[$request->get('inCodItem')] as $chave => $valor) {
            $inQtdeEntrada += str_replace(",",  ".", str_replace(".", "", $valor['inQtdePerecivel']));
        }

        $inQtdeEntrada = number_format($inQtdeEntrada, 4, ",", ".");
        $stJs .=  "$('inQtdeEntrada').value = '".$inQtdeEntrada."';";

        // remonta a lista perecivel do item
        $stJs .= montaListaItensPerecivel( $arItensPerecivel[$request->get('inCodItem')] );

        // limpa os campos perecivel do item
        $stJs .= limpaDadosPerecivel();
    } else
        $stJs .= $retorno;

    # Habilita o botão de incluir/alterar.
    $stJs .= "jQuery('#Incluir').removeProp('disabled');";
    break;

// limpa os dados dos campos específicos dos itens perecível
case 'limpaDadosPerecivel':
    $stJs = limpaDadosPerecivel();
    break;

// exclui a linha da listagem dos itens
case 'excluirPerecivel':
    $arItensPerecivel = Sessao::read('arItensPerecivel');
    // é decrescido a quantidade de entrada
    $inQtdePerecivel = $arItensPerecivel[$request->get('inCodItem')][$request->get('inNumLinhaListaPerecivel')]['inQtdePerecivel'];

    $inQtdeEntrada = $request->get('inQtdeEntrada');
    $inQtdeEntrada = str_replace(",", ".", str_replace(".", "", $inQtdeEntrada));
    $valor = $inQtdeEntrada - number_format( $inQtdePerecivel, 4, ".", "," );
    $valor = number_format( $valor, 4, ",", "." );

    $stJs .=  "$('inQtdeEntrada').value = '".$valor."';";

    $arTemp = array();
    $inCont = 0;

    // remonta os itens na lista
    foreach ($arItensPerecivel[$request->get('inCodItem')] as $chave => $valor) {
        if ($chave != $request->get('inNumLinhaListaPerecivel')) {
            $arTemp[$inCont]['inNumLotePerecivel']       = $valor['inNumLotePerecivel'];
            $arTemp[$inCont]['dtValidadePerecivel']      = $valor['dtValidadePerecivel'];
            $arTemp[$inCont]['dtFabricacaoPerecivel']    = $valor['dtFabricacaoPerecivel'];
            $arTemp[$inCont]['inQtdePerecivel']          = $valor['inQtdePerecivel'];
            $arTemp[$inCont]['inNumLinhaListaPerecivel'] = $inCont;
            $inCont++;
        }
    }
    $arItensPerecivel[$request->get('inCodItem')] = $arTemp;
    Sessao::write('arItensPerecivel' , $arItensPerecivel);

    // caso tenha algum item na listagem, mostra a lista, caso contrario limpa o span
    if ( count($arItensPerecivel[$request->get('inCodItem')]) > 0 )
        $stJs .= montaListaItensPerecivel( $arItensPerecivel[$request->get('inCodItem')] );
    else
        $stJs .= "$('spnItensPereciveis').innerHTML = '';";

    $inQtdeEntrada = 0;
    foreach ($arItensPerecivel[$request->get('inCodItem')] as $chave => $valor) {
        $inQtdeEntrada += str_replace(",",  ".", str_replace(".", "", $valor['inQtdePerecivel']));
    }

    $inQtdeEntrada = number_format($inQtdeEntrada, 4, ",", ".");
    $stJs .= "jQuery('#inQtdeEntrada').val('".$inQtdeEntrada."');";
    break;

// monta a listagens dos itens de entrada
case 'incluirItemEntrada':
    $arInfoChecks = explode("_", $request->get('inCheckBoxId'));
    $inCheckBoxId = $arInfoChecks[1];

    Sessao::write('irProximoItem',$request->get('inProxItem'));

    $retorno = validaListaEntrada($request);
    if ($retorno == '') {
        $boAlterarItem = $request->get('boAlterarItem');

        // monta a lista de atributo e verifica os campos
        if ( Sessao::read('boAtributo') ) {
            require_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoItem.class.php";
            $obTAtributoCatalogoItem = new TAlmoxarifadoAtributoCatalogoItem();
            $obTAtributoCatalogoItem->setDado("cod_item", $request->get('inCodItem') );
            $obTAtributoCatalogoItem->recuperaAtributoCatalogoItem( $rsAtributos );

            $inCont = 0;
            $boSair = false;
            // faz a montagem do array dos atributos do item. Caso algum dos atributos não nulos estejam em branco,
            // para o processo e acusa o erro na tela

            $arItensAtributo = Sessao::read('arItensAtributo');

            while ((!$rsAtributos->eof()) && (!$boSair)) {
                if (($request->get('Atributos_'.$rsAtributos->getCampo('cod_atributo').'_2') != '' ) && ( $rsAtributos->getCampo('nao_nulo') == 't')) {
                    $arItensAtributo[$request->get('inCodItem')][$inCont]['stValor']       = $request->get('Atributos_'.$rsAtributos->getCampo('cod_atributo').'_2');
                    $arItensAtributo[$request->get('inCodItem')][$inCont]['inCodAtributo'] = $rsAtributos->getCampo('cod_atributo');
                    $arItensAtributo[$request->get('inCodItem')][$inCont]['inCodModulo']   = $rsAtributos->getCampo('cod_modulo');
                    $arItensAtributo[$request->get('inCodItem')][$inCont]['inCodCadastro'] = $rsAtributos->getCampo('cod_cadastro');
                    $inCont++;
                    $rsAtributos->proximo();
                } else {
                    $stMensagem = 'O atributo '.$rsAtributos->getCampo('nom_atributo').' deve ser preenchido.';
                    $retorno = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";

                    // destroi a criação do array para que não fique nada dela em sessão, caso não seja alteração do item, verificado
                    // pela variavel 'inNumLinhaEntrada' que só recebe valor caso esteja alterando os dados de determinado item
                    if (!$boAlterarItem)
                        unset($arItensAtributo[$request->get('inCodItem')]);
                    $boSair = true;
                }
            }
            Sessao::write('arItensAtributo', $arItensAtributo);
        }

        // caso não tenha encontrado nenhum erro na crição do array dos atributos, vai para a criação do array dos itens de entrada
        if ($retorno == "") {
            require_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCusto.class.php";
            $obTAlmoxarifadoCentroCusto = new TAlmoxarifadoCentroCusto();
            $obTAlmoxarifadoCentroCusto->setDado( "cod_centro", $request->get('inCodCentroCusto'));
            $obTAlmoxarifadoCentroCusto->recuperaPorChave( $rsCentroCusto );

            $arItensEntrada = Sessao::read('arItensEntrada');

            $inCont = 0;
            if ($boAlterarItem){
                unset($arItensEntrada[$request->get('inNumLinhaEntrada')]);
                $inCont = $request->get('inNumLinhaEntrada');
            }else{
                if ( is_array($arItensEntrada) ) {
                    $inCont = count($arItensEntrada);
                }
            }

            $arItensEntrada[$inCont]['inCodItem'          ] = $request->get('inCodItem');
            $arItensEntrada[$inCont]['stItem'             ] = $request->get('stItem');
            $arItensEntrada[$inCont]['inCodFornecedor'    ] = $request->get('inCodFornecedor');
            $arItensEntrada[$inCont]['stDtOrdem'          ] = $request->get('stDtOrdem');
            $arItensEntrada[$inCont]['inCodEntidade'      ] = $request->get('inCodEntidade');
            $arItensEntrada[$inCont]['inOrdemCompra'      ] = $request->get('inOrdemCompra');
            $arItensEntrada[$inCont]['stExercicio'        ] = $request->get('stExercicio');
            $arItensEntrada[$inCont]['inCodAlmoxarifado'  ] = $request->get('inCodAlmoxarifado');
            $arItensEntrada[$inCont]['inCodCentroCusto'   ] = $request->get('inCodCentroCusto');
            $arItensEntrada[$inCont]['stUnidadeMedida'    ] = $request->get('stUnidadeMedida');
            $arItensEntrada[$inCont]['inCodMarca'         ] = $request->get('inCodMarca');
            $arItensEntrada[$inCont]['stMarca'            ] = $request->get('stMarca');
            $arItensEntrada[$inCont]['inQtdeEntrada'      ] = $request->get('inQtdeEntrada');
            $arItensEntrada[$inCont]['flValorTotalMercado'] = $request->get('flValorTotalMercado');

            //Informações de Bem Patrimonial
            $arItensEntrada[$inCont]['stPlacaIdentificacao'] = $request->get('stPlacaIdentificacao');
            $arItensEntrada[$inCont]['inCodSituacao'] = $request->get('inCodSituacao');
            $arItensEntrada[$inCont]['stNumeroPlaca'] = $request->get('stNumeroPlaca');
            $arItensEntrada[$inCont]['inCodTipoItem'] = $request->get('inCodTipoItem');

            // Utilizado para multiplicar e gerar os totais.
            $qtdItemFormatado = str_replace('.','',$request->get('inQtdeEntrada'));
            $qtdItemFormatado = str_replace(',','.',$qtdItemFormatado);
            $inQtdeItem     = number_format($qtdItemFormatado, 2, ".", "");

            $vlUnitarioItem = str_replace('.','',$request->get('flValorTotalMercado'));
            $vlUnitarioItem = str_replace(",", ".",$vlUnitarioItem );
            $vlUnitarioItem = number_format($vlUnitarioItem, 2, ".", "");

            $vlTotalItem    = number_format(($vlUnitarioItem * $inQtdeItem), 2, ",", ".");

            $arItensEntrada[$inCont]['vlTotalItem'   ] = $vlTotalItem;
            $arItensEntrada[$inCont]['stComplemento' ] = $request->get('stComplemento');
            $arItensEntrada[$inCont]['inNumItem'     ] = $request->get('inNumItem');
            $arItensEntrada[$inCont]['stCentroCusto' ] = $rsCentroCusto->getCampo( 'descricao' );
            $arItensEntrada[$inCont]['inCheckBoxId'  ] = $inCheckBoxId;
            $arItensEntrada[$inCont]['boCodItem'     ] = $request->get('boCodItem', FALSE);
            $arItensEntrada[$inCont]['stNomItem'     ] = $request->get('stNomItem');

            if (!$boAlterarItem) {
                // adiciona ao item inserido a posição na lista, para poder fazer a alteração dos dados posteriormente
                $arItemLinha = Sessao::read('arItemLinha');
                Sessao::remove('arItemLinha');
                $arItemLinha[$request->get('inCodItem')] = $inCont;
                Sessao::write('arItemLinha', $arItemLinha);
            }

            $arItensEntradaC = $arItensEntrada;
            Sessao::write('arItensEntrada', $arItensEntrada);

            if ( Sessao::read('boPerecivel') ) {
                $stJs .= limpaDadosPerecivel();
                $stJs .= "\n $('spnPerecivel').innerHTML = '';";
            }

            $stJs .= limpaDadosEntrada($inCheckBoxId);

            // monta a lista de itens de entrada
            $stJs .= montaListaItensEntrada( $arItensEntradaC );

            $stJs .= "$('item_".$inCheckBoxId."').checked = false;";

            $inNumeroTotalItens = count(Sessao::read('arItens'));

            $inProxItem = $inCheckBoxId+1;
            if ( ($request->get('inProxItem') == '1') && ($inNumeroTotalItens >= $inProxItem)) {
                $stJs .= "$('item_".$inProxItem."').click();";
            }
        } else {
            $stJs .= $retorno;
        }

    } else {
        $stJs .= $retorno;
    }

    break;

case 'limparDadosItens':
    $obSelectAlmoxarifado = new ISelectAlmoxarifadoAlmoxarife();

    $obTComprasOrdemCompra = new TComprasOrdem();
    $obTComprasOrdemCompra->setDado( "cod_ordem", Sessao::read('inOrdemCompra') );
    $obTComprasOrdemCompra->setDado( "tipo"     , "C" );
    $obTComprasOrdemCompra->recuperaMarcaPorOrdemCompra( $rsOrdemCompraMarca );

    $stJs .= "frm.inCodAlmoxarifado.value = '".$obSelectAlmoxarifado->getCodAlmoxarifado()."';";
    $stJs .= "$('inCodCentroCusto').value = '';";
    $stJs .= "$('inCodMarca').value = '".$rsOrdemCompraMarca->getCampo('cod_marca')."';";
    $stJs .= "$('stMarca').innerHTML = '".$rsOrdemCompraMarca->getCampo('marca')."';";
    $stJs .= "$('inQtdeEntrada').value = '".number_format(0, 4, ",", ".")."';";
    $stJs .= "$('flValorTotalMercado').value = '';";
    $stJs .= "$('stComplemento').value = '';";
    $stJs .= "if($('stNumeroPlaca'))$('stNumeroPlaca').value = '';";
    $stJs .= "if($('inCodSituacao'))$('inCodSituacao').value = '';";
    $stJs .= "if($('inCodTxtSituacao'))$('inCodTxtSituacao').value = '';";
    $stJs .= "if($('spnDetalheItem'))$('spnDetalheItem').innerHTML = '';";
    break;

case 'montaPlacaIdentificacao':
    if ($request->get('stPlacaIdentificacao') == 'sim')
        $montaPlaca = true;
    else
        $montaPlaca = false;

    $stJs = montaPlacaIdentificacao($request->get('inNumLinhaEntrada'), $montaPlaca);
    break;

case 'verificaIntervalo':
    $stNumeroPlaca = $request->get('stNumeroPlaca');
    $nuQuantidade = $request->get('nuQuantidade');
    if ($stNumeroPlaca != '' && $nuQuantidade != '' && $nuQuantidade > 0) {
        $arNumPlaca = array();
        // monta um array com os números das placas possíveis de acordo com a
        // quantidade informada
        for ($i=0; $i < $nuQuantidade; $i++)
            $arNumPlaca[] = "'".($stNumeroPlaca++)."'";

        $stFiltro = " WHERE num_placa IN (".implode("," ,$arNumPlaca).")";
        $obTPatrimonioBem = new TPatrimonioBem();
        $obTPatrimonioBem->recuperaTodos( $rsBem, $stFiltro );

        if ( $rsBem->getNumLinhas() >= 0 ) {
            $inQuantidade = str_replace('.','', $nuQuantidade);
            $inQuantidade = str_replace(',','.', $inQuantidade);
            $inQuantidade = (int) $inQuantidade;
            $intervalo = ($inQuantidade) + $stNumeroPlaca;

            $stJs.= "alertaAviso('Já existem bens com placas no intervalo selecionado (".$stNumeroPlaca." - ".$intervalo.")!','form','erro','".Sessao::getId()."');";
        }
    }
    break;

case 'excluirEntrada':
    // contador para a lista nova
    $inCont = 0;

    // copia o array da lista de entrada para um array novo
    $arItensEntrada     = array();
    $arItensEntradaNovo = array();
    $arItensEntrada     = Sessao::read('arItensEntrada');

    $inCodItem = $request->get('inCodItem');

    // limpa o array para remontá-lo sem o item excluido
    Sessao::remove('arItensEntrada');

    // reordena a o array da lista de entrada, agora sem o item excluído
    foreach ($arItensEntrada as $chave => $valor) {
        // Caso o item não for o excluido, adiciona no array dos itens de entrada.
        if ($valor['inCodItem'] != $inCodItem) {
            $arItensEntradaNovo[$inCont] = $valor;
            $inCont++;
        }
    }

    Sessao::write('arItensEntrada', $arItensEntradaNovo);

    $arItensPerecivel = Sessao::read('arItensPerecivel');
    $arItensAtributo  = Sessao::read('arItensAtributo');
    $arItemLinha      = Sessao::read('arItemLinha');

    // destroi as listas relacionadas a esse item que está sendo excluído
    unset( $arItensPerecivel[$inCodItem] );
    unset( $arItensAtributo [$inCodItem] );
    unset( $arItemLinha     [$inCodItem] );

    $inContItens = 0;
    foreach ($arItemLinha as $chave =>$dados) {
        $arItemLinha[$chave] = $inContItens;
        $inContItens++;
    }

    Sessao::write('arItensPerecivel', $arItensPerecivel);
    Sessao::write('arItensAtributo' , $arItensAtributo);
    Sessao::write('arItemLinha'     , $arItemLinha);

    // se a lista não estiver vazia, monta a lista novamenete, caso contrario limpa o span
    if (count($arItensEntradaNovo) > 0) {
        $stJs .= montaListaItensEntrada( $arItensEntradaNovo );
        $stJs .= "$('spnDetalheItem').innerHTML = '';";
    } else {
        $stJs .= "$('spnItensEntrada').innerHTML = '';";
    }

    // destroi a variável que não é mais útil
    unset( $arItensEntrada );
    unset( $arItensEntradaNovo );

    break;

    case 'verificaItemPerecivel':
        $rsCatalogoItem = verificaItemPerecivel($request->get('inCodItem'));
        if ( Sessao::read('boPerecivel') )
            $stJs = montaPerecivel();
        else{
            $stJs  = "jQuery('#spnPerecivel').html('');";
            $stJs .= "jQuery('#inQtdeEntrada').removeProp('disabled');";
        }

        $rsAtributos = verificaItemAtributo($request->get('inCodItem'));
        if (Sessao::read('boAtributo'))
            $stJs .= montaAtributos($rsAtributos);
        else
            $stJs .= "jQuery('#spnAtributo').html('');";

        if (Sessao::read('boBemPatrimonial'))
            $stJs .= montaPatrimonial($request->get('inNumLinhaEntrada'));
        else
            $stJs .= "jQuery('#spnPatrimonial').html('');";
    break;
}

echo $stJs;
?>
