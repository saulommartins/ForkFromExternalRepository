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
    * Página de Oculto para Requisição
    * Data de criação : 02/12/2008

    * @author Analista: Diego Victoria
    * @author Programador: Diego Victoria

    * @ignore

    $Id: .php 35831 2008-11-20 20:35:21Z luiz $

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoItemMarca.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoEstoqueItem.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoContaDespesaItem.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoDiversa";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

$obRItemMarca   = new RAlmoxarifadoItemMarca;
$obREstoqueItem = new RAlmoxarifadoEstoqueItem;

$rsMarcas = new RecordSet;

function montaListaItens($arRecordSet , $stAcao = "")
{
    $pgOcul = "OCMovimentacaoDiversa.php";

    $rsItens = new RecordSet;
    $rsItens->preenche( $arRecordSet );

    $table = new TableTree();
    $table->setRecordset( $rsItens );

    $table->addCondicionalTree("detalhar", "true");
    $table->setArquivo( $pgOcul );
    $table->setParametros( array( "id") );
    $table->setComplementoParametros ( "stCtrl=detalhaItem&stAcao=".$stAcao );

    $table->setSummary('Itens');

    $table->Head->addCabecalho( 'Item'  , 20 );
    $table->Head->addCabecalho( 'Marca' , 15 );
    $table->Head->addCabecalho( 'Centro de Custo' , 20 );
    if (( $stAcao != "anular" ) && ( $stAcao != "consultar" )) {
        $table->Head->addCabecalho( 'Saldo' , 5 );
        $table->Head->addCabecalho( 'Quantidade' , 5 );
        $table->Head->addCabecalho( 'Desdobramento para lançamento' , 5 );
    } else {
        $table->Head->addCabecalho( 'Requisitada' , 5 );
        $table->Head->addCabecalho( 'Atendida' , 5 );
        if ($stAcao == "consultar") {
            $table->Head->addCabecalho( 'Devolvida' , 5 );
        }
        $table->Head->addCabecalho( 'Anulada' , 5 );
        if ($stAcao == "consultar") {
            $table->Head->addCabecalho( 'Saldo Pendente' , 5 );
        } else {
            $table->Head->addCabecalho( 'A Anular' , 5 );
        }
    }

    $table->Body->addCampo( '[cod_item]-[descricao_item]', 'E' );
    $table->Body->addCampo( '[cod_marca]-[descricao_marca]', 'E' );
    $table->Body->addCampo( '[cod_centro]-[descricao_centro]', 'E' );
    if (( $stAcao != "anular" ) && ( $stAcao != "consultar" )) {
        $table->Body->addCampo( '[saldo_formatado]', 'E' );

        $obQuantidade = new Quantidade();
        $obQuantidade->setValue( "quantidade" );
        $obQuantidade->setName ( "nuQuantidadeLista" );
        $obQuantidade->setNull ( false );
        $obQuantidade->setReadOnly ( "[detalhar]" );
        $obQuantidade->setSize ( 10 );
        $obQuantidade->setId   ( "" );
        $obQuantidade->setValue( "[quantidade]" );
        $obQuantidade->obEvento->setOnChange("JavaScript:executaFuncaoAjax('validaValorItem', '&nomeCampo='+this.name+'&novaQuantidade='+this.value);");

        $table->Body->addComponente( $obQuantidade );

        $table->Body->addAcao( 'excluir' ,  'excluirItem(%s)' , array( 'id' ) );

        //monta combo para o lançamento contábil do item
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

          $table->Body->addComponente( $obCmbDesdobramento );
          // $table->Body->ultimoDado->setCampo( '[cod_conta_despesa]' );
          // $table->Body->ultimoDado->setAlinhamento( "CENTRO" );
          // $table->Body->commitDadoComponente();
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
               $stJsLancamento .= "d.getElementById('spnItens').appendChild(input);";
             }
               $rsItens->proximo();
               $inQtd++;
          }
          $rsItens->setPrimeiroElemento();

    } else {
        $table->Body->addCampo( '[requisitada]', 'E' );
        $table->Body->addCampo( '[atendida]', 'E' );
        if ($stAcao == "consultar") {
            $table->Body->addCampo( '[devolvida]', 'E' );
        }
        $table->Body->addCampo( '[anulada]', 'E' );
        if ($stAcao == "consultar") {
            $table->Body->addCampo( '[pendente]', 'E' );
        } else {
            $obAnular = new Quantidade();
            $obAnular->setValue( "anular" );
            $obAnular->setName ( "nuAnular" );
            $obAnular->setNull ( false );
            $obAnular->setSize ( 10 );
            $obAnular->setId   ( "" );
            $obAnular->setValue("[anular]");

            $table->Body->addComponente( $obAnular );
        }
        $table->Body->addAcao( 'excluir' ,  'excluirItemAnulacao(%s)' , array( 'id' ) );
    }
    $table->montaHTML(true);
    $stJs .= "jq('#spnItens').html('".$table->getHtml()."');";
    $stJs .= Resetar(1);
    $stJs .= $stJsLancamento;

    if ($stAcao != "consultar") {
        $stJs .= bloqueiaQuantidadeLista($stAcao);
    }
    echo $stJs;
}

function bloqueiaQuantidadeLista($stAcao)
{
    $arItens = Sessao::read('arItens');
    for ($i=0; $i < count($arItens); $i++) {
        if ( count($arItens[$i]['valores_atributos']) > 0 ) {
            $id = $i + 1;
            if ($stAcao == 'anular') {
                $js .= "document.getElementById('nuAnular_".$id."').readOnly = true;";
            } else {
                $js .= "document.getElementById('nuQuantidadeLista_".$id."').readOnly = true;";
            }
        }
    }

    return $js;
}

function Resetar($inVar)
{
    switch ($inVar) {
        case 1:
            $stJs .= "d.getElementById('inSaldo').innerHTML = '&nbsp;';\n";
            $stJs .= "d.getElementById('stUnidadeMedida').innerHTML = '&nbsp;';\n";
            $stJs .= "d.getElementById('stNomItem').innerHTML = '&nbsp;';\n";
            $stJs .= "f.inCodItem.value = '';\n";
            $stJs .= "limpaSelect(f.inCodMarca, 1 );\n";
            $stJs .= "limpaSelect(f.inCodCentroCusto, 1 );\n";
            $stJs .= "f.nuQuantidade.value = '';\n";
            $stJs .= "document.getElementById('nuQuantidade').readOnly = false;\n";
            $stJs .= "document.getElementById('spnAtributos').innerHTML = '';";
            $stJs .= "jq('#spnListaLotes').html('&nbsp;');";
            $stJs .= "jq('#spnDadosFrota').html('&nbsp;');";
            break;
        case 2:
            $stJs .= "d.getElementById('inSaldo').innerHTML = '&nbsp;';\n";
            $stJs .= "f.nuQuantidade.value = '';\n";
            break;
        case 3:
            $stJs .= "d.getElementById('inSaldo').innerHTML = '&nbsp;';\n";
            $stJs .= "d.getElementById('stUnidadeMedida').innerHTML = '&nbsp;';\n";
            $stJs .= "d.getElementById('stNomItem').innerHTML = '&nbsp;';\n";
            $stJs .= "d.getElementById('spnItens').innerHTML = '&nbsp;';\n";
            $stJs .= "jq('#spnDadosFrota').html('&nbsp;');";
            $stJs .= "f.inCodItem.value = '';\n";
            $stJs .= "limpaSelect(f.inCodMarca, 1 );\n";
            $stJs .= "limpaSelect(f.inCodCentroCusto, 1 );\n";
            $stJs .= "f.nuQuantidade.value = '';\n";
            $stJs .= "f.stObservacao.value = '';\n";
        break;
    }

    return $stJs;
}

function detalhaItem()
{
    global $pgOcul;
    $arItens = Sessao::read('arItens');
    $inId    = $_GET['id'];

    $arItem = $arItens[$_GET['id']-1];

    if ($arItem['possui_atributos'] == "true") {
        $rsAtributosValores = new RecordSet;
        $rsAtributosValores->preenche( $arItens[$_GET['id']-1]['valores_atributos'] );
        $indice = 0;

        while (!$rsAtributosValores->eof()) {

            $arAtributos = $rsAtributosValores->getCampo('atributo');

            foreach ($arAtributos as $chave=> $dados) {
                if ($dados['valor']!="") {
                    $inCodTipo = SistemaLegado::pegaDado('cod_tipo','administracao.atributo_dinamico'," where cod_atributo =".$dados['cod_atributo']);

                    if ($inCodTipo==4) {
                        if (!($dados['valor'])=='') {
                            $stValorAtributoLista .= "<b>".$dados['nom_atributo']."</b>"." : ";
                        }

                        $arValorAtributoSeparado = explode(",",trim($dados['valor']));
                        foreach ($arValorAtributoSeparado as $chaveSeparado => $dadosSeparados) {
                            if ($dadosSeparados != '') {
                                $stValorAtributoLista .= SistemaLegado::pegaDado('valor_padrao','administracao.atributo_valor_padrao'," where cod_valor IN (".$dadosSeparados.") and cod_atributo = ".$dados['cod_atributo'])." ";
                            }
                            if ((count($arValorAtributoSeparado)-1)>$chaveSeparado) {
                                $stValorAtributoLista .= ",";
                            }
                        }
                    } elseif ($inCodTipo==3) {
                        if (!($dados['valor'])=='') {
                            $stValorAtributoLista .= "<b>".$dados['nom_atributo']."</b>"." : ".SistemaLegado::pegaDado('valor_padrao','administracao.atributo_valor_padrao'," where cod_valor =".$dados['valor']." and cod_atributo = ".$dados['cod_atributo']);
                        }
                    } else {
                        if (!($dados['valor'])=='') {

                            $stValorAtributoLista .= "<b>".$dados['nom_atributo']."</b>"." : ".$dados['valor'];
                        }
                    }

                    if ((count($arAtributos)-1)>$chave) {
                        $stValorAtributoLista .= ", ";
                    }

                }
            }
            $arItens[$_GET['id']-1]['valores_atributos'][$indice]['stValoresAtributosLista'] = $stValorAtributoLista;
            unset($stValorAtributoLista);
            $rsAtributosValores->proximo();
            $indice++;
        }

        $rsAtributosValores = new RecordSet;
        $rsAtributosValores->preenche( $arItens[$_GET['id']-1]['valores_atributos'] );

        $rsAtributosValores->setPrimeiroElemento();

        $table = new Table();
        $table->setRecordset( $rsAtributosValores );

        $table->Head->addCabecalho( "Atributos Dinâmicos" , 60 );
        $table->Head->addCabecalho( "Saldo" , 20 );
        $table->Head->addCabecalho( "Quantidade" , 20 );

        $obTxtQuantidadeAtributo = new Quantidade;
        $obTxtQuantidadeAtributo->setRotulo  ( "Quantidade" );
        $obTxtQuantidadeAtributo->setName    ( "nuQuantidadeAtributoLista" );
        $obTxtQuantidadeAtributo->setId      ( "nuQuantidadeAtributoLista" );
        $obTxtQuantidadeAtributo->setInteiro ( false );
        $obTxtQuantidadeAtributo->setFloat   ( true  );
        $obTxtQuantidadeAtributo->setValue( "[quantidade]" );
        $obTxtQuantidadeAtributo->obEvento->setOnChange("JavaScript:executaFuncaoAjax('validaValorAtributo', '&stAcao=".$_REQUEST['stAcao']."&idItem=".$_GET['id']."&nomeCampo='+this.name+'&novaQuantidade='+this.value);");

        $table->Body->addCampo( '[stValoresAtributosLista]', 'E' );
        $table->Body->addCampo( '[saldo_atributo]', 'E' );
        if ($_REQUEST['stAcao'] != "consultar") {
            $table->Body->addComponente( $obTxtQuantidadeAtributo );
        } else {
            $table->Body->addCampo( '[quantidade]', 'E' );
        }

        $table->montaHTML();
        $stHTML = $table->getHtml();

        echo $stHTML;
    } elseif ($arItem['possui_lotes'] == "true") {

        $rsItens = new RecordSet();
        $rsItens->preenche( $arItem['lotes'] );
        $rsItens->addFormatacao('saldo', 'NUMERIC_BR_4');
        $rsItens->addFormatacao('quantidade', 'NUMERIC_BR_4');

        $table = new Table();
        $table->setRecordset( $rsItens );

        $table->Head->addCabecalho( "Lote" , 5 );
        $table->Head->addCabecalho( "Data de Fabricação" , 5 );
        $table->Head->addCabecalho( "Data de Validade" , 5 );
        $table->Head->addCabecalho( "Saldo do Lote" , 5 );
        $table->Head->addCabecalho( "Quantidade" , 5 );

        $table->Body->addCampo( '[lote]', 'E' );
        $table->Body->addCampo( '[dt_fabricacao]', 'C' );
        $table->Body->addCampo( '[dt_validade]', 'C' );
        $table->Body->addCampo( '[saldo]', 'D' );

        $obTxtQtdLote = new Numerico;
        $obTxtQtdLote->setId       ("nmQtdLoteLista_$inId");
        $obTxtQtdLote->setName     ("nmQtdLoteLista_$inId");
        $obTxtQtdLote->setSize     ( 14 );
        $obTxtQtdLote->setMaxLength( 14 );
        $obTxtQtdLote->setDecimais ( 4 );
        $obTxtQtdLote->setValue    ( "[quantidade]" );
        $obTxtQtdLote->obEvento->setOnChange("JavaScript:executaFuncaoAjax('alterarQuantidadeLote', '&nomeCampo='+this.name+'&novaQuantidade='+this.value);");

        $table->Body->addComponente( $obTxtQtdLote );
        $table->montaHTML();
        $stHTML = $table->getHtml();
        echo $stHTML;
    }
}

function montaItens()
{
    $arItens = Sessao::read('arItens');
    $inCodItem        = $_REQUEST['inCodItem'];
    $inCodCentroCusto = $_REQUEST['inCodCentroCusto'];
    $inCodMarca       = $_REQUEST['inCodMarca'];

    if (count($arItens) > 0) {
        foreach ($arItens as $arTEMP) {
            if ($arTEMP['cod_item'] == $inCodItem && $arTEMP['cod_centro'] == $inCodCentroCusto && $arTEMP['cod_marca'] == $inCodMarca) {
                $boExecuta   = true;
                $stJsAlerta  = Resetar(1);
                $stJsAlerta .= "jq('#inCodItem').focus();";
                $stJsAlerta .= "alertaAviso('Item (".$inCodItem.") com a mesma configuração já incluso na lista.','form','erro','".Sessao::getId()."');";
            }
            if ($arTEMP['boFrota'] == true) {
                $boItensDoFrota = true;
            }
        }
        if ($boItensDoFrota == true) {
           $stMsg     = 'Este item não pertence aos itens de manutenção do frota. Se você deseja que este item pertença a manutenção do frota, ';
           $stMsg    .= 'execute a ação  "Gestão Patrimonial :: Frota :: Itens de Manutenção :: Incluir Item" antes de efetuar a Saídas Diversas';
           $stJsPopUp = "alertPopUp('Saída por Requisição','".$stMsg."','');";
        }
    }
    if (!$boExecuta) {
        if ($boItensDoFrota == true) {
            echo $stJsPopUp;
        }
        $stJs .= montaArrayLista($arItens);
    } else {
        if ($boItensDoFrota == true && $boExecuta = true) {
            echo $stJsAlerta;
        } else {
            if ($boItensDoFrota == true) {
                echo $stJsPopUp;
            }
            if ($boExecuta == true) {
                echo $stJsAlerta;
            }
        }
    }
}

function montaItensFrota($boConfirmacao = false)
{
    $arItens = Sessao::read('arItens');
    $inCodItem        = $_REQUEST['inCodItem'];
    $inCodCentroCusto = $_REQUEST['inCodCentroCusto'];
    $inCodMarca       = $_REQUEST['inCodMarca'];
    $boFrota          = $_REQUEST['boFrota'];
    $inCodVeiculo     = $_REQUEST['inCodVeiculo'];
    $nmKm             = $_REQUEST['nmKm'];

    if (!$boConfirmacao) {
        if (count($arItens) > 0) {
            foreach ($arItens as $arTEMP) {
                if ($arTEMP['cod_item'] == $inCodItem && $arTEMP['cod_centro'] == $inCodCentroCusto && $arTEMP['cod_marca'] == $inCodMarca && $arTEMP['inCodVeiculo'] == $inCodVeiculo) {
                    $boExecuta = true;
                    $stJs .= Resetar(1);
                    $stJs .= "jq('#inCodItem').focus();";
                    $stJs .= "alertaAviso('Item (".$inCodItem.") com a mesma configuração já incluso na lista.','form','erro','".Sessao::getId()."');";
                }
                if ($inCodVeiculo == $arTEMP['inCodVeiculo'] && $nmKm != $arTEMP['nmKm']) {
                     $boExecuta = true;
                     $stMsg  = 'Já possui itens na lista para este veículo com quilometragem \"'.$arTEMP['nmKm'].'\". Deseja alterar ';
                     $stMsg .= 'os demais itens da lista que utilizam este veículo para a quilometragem \"'.$_REQUEST['nmKm'].'\" informada neste item?';
                     $stJs  .= "confirmPopUp('Saída por Requisição','".$stMsg."','montaParametrosGET(\'confirmaItemFrota\');');";
                }
            }
        }
        if (!$boExecuta) {
            $stJs .= montaArrayLista($arItens);
        } else {
            echo $stJs;
        }
    } else {
        foreach ($arItens as $chave =>$arTEMP) {
            if ($arTEMP['inCodVeiculo'] == $inCodVeiculo) {
                $arItens[$chave]['nmKm'] = $nmKm;
            }
        }
        Sessao::write('arItens', $arItens);
        $stJs .= montaArrayLista($arItens);
    }
}

function montaArrayLista($arItens)
{
    $inCount = count($arItens);

    $arItens[$inCount]['id']               = $inCount+1;
    $arItens[$inCount]['cod_item']         = $_REQUEST['inCodItem'];
    $arItens[$inCount]['descricao_item']   = str_replace("'","&#39;",$_REQUEST['stNomItem']);
    $arItens[$inCount]['descricao_marca']  = str_replace("'","&#39;",$_REQUEST['stMarca']);
    $arItens[$inCount]['cod_centro']       = $_REQUEST['inCodCentroCusto'];
    $arItens[$inCount]['descricao_centro'] = str_replace("'","&#39;",$_REQUEST['stCentroCusto']);
    $arItens[$inCount]['saldo']            = $_REQUEST['stSaldo'];
    $arItens[$inCount]['saldo_formatado']  = $_REQUEST['stSaldo'];
    $arItens[$inCount]['quantidade']       = $_REQUEST['nuQuantidade'];
    $arItens[$inCount]['cod_marca']        = $_REQUEST['inCodMarca'];
    $arItens[$inCount]['cod_almoxarifado'] = $_REQUEST['inCodAlmoxarifado'];
    $arItens[$inCount]['boFrota']          = $_REQUEST['boFrota'];
    $arItens[$inCount]['nmKm']             = $_REQUEST['nmKm'];
    $arItens[$inCount]['inCodVeiculo']     = $_REQUEST['inCodVeiculo'];

    $obIMontaItemQuantidadeValoresAtributo = Sessao::read('obIMontaItemQuantidadeValoresAtributo');
    if (count($obIMontaItemQuantidadeValoresAtributo) > 0) {
        $arItens[$inCount]['detalhar']         = "true";
        $arItens[$inCount]['possui_atributos'] = "true";
        foreach ($obIMontaItemQuantidadeValoresAtributo as $chave =>$dados) {
            $dadosValoresAtributosSeparados = explode(' - ',$dados['stValoresAtributos']);
            $dadosNomeAtributosSeparados = explode(' - ',$dados['NomeAtributos']);
            foreach ($dadosValoresAtributosSeparados as $chaveAtributo =>$dadosAtributo) {
                if ($valorAtributo != '') {
                    $valorAtributo .= ' , ';
                }
                $valorAtributo .= "<b>".$dadosNomeAtributosSeparados[$chaveAtributo].'</b> : '.$dadosAtributo;
            }
            $arItens[$inCount]['valores_atributos'][$chave]['stValoresAtributosLista'] = $valorAtributo;
            $arItens[$inCount]['valores_atributos'][$chave]['stValoresAtributos'] = $dados['stValoresAtributos'];
            $arItens[$inCount]['valores_atributos'][$chave]['NomeAtributos'] = $dados['NomeAtributos'];
            $arItens[$inCount]['valores_atributos'][$chave]['quantidade'] = $dados['quantidade'];
            $arItens[$inCount]['valores_atributos'][$chave]['saldo_atributo'] = $dados['saldo_atributo'];
            $arItens[$inCount]['valores_atributos'][$chave]['atributo'] = $dados['atributo'];
            unset($valorAtributo);
        }
        $arItens[$inCount]['possui_atributos'] = "false";
        $arItens[$inCount]['detalhar']         = "false";
    }

    $obIMontaItemQuantidadeLotes = Sessao::read('obIMontaItemQuantidadeLotes');
    if (count($obIMontaItemQuantidadeLotes) > 0) {
        $arItens[$inCount]['possui_lotes'] = "true";
        $arItens[$inCount]['detalhar']     = "true";
        foreach ($obIMontaItemQuantidadeLotes as $chave =>$dados) {
            $arItens[$inCount]['lotes'][$chave]['inId']         = $dados['inId'];
            $arItens[$inCount]['lotes'][$chave]['lote']         = $dados['lote'];
            $arItens[$inCount]['lotes'][$chave]['dt_validade']  = $dados['dt_validade'];
            $arItens[$inCount]['lotes'][$chave]['dt_fabricacao']= $dados['dt_fabricacao'];
            $arItens[$inCount]['lotes'][$chave]['saldo']        = $dados['saldo'];
            $arItens[$inCount]['lotes'][$chave]['quantidade']   = $dados['quantidade'];
        }
    } else {
        $arItens[$inCount]['possui_lotes'] = "false";
        $arItens[$inCount]['detalhar']     = "false";
    }
    Sessao::write('arItens', $arItens);
    $stJs .= montaListaItens(Sessao::read('arItens'));
}

switch ($stCtrl) {

    case 'verificaItemFrota':

        if ($_REQUEST['inCodAlmoxarifado']) {
            if ($_REQUEST['inCodItem']) {
                include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php";
                $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;
                $stFiltro = " AND aci.cod_item = ".$_REQUEST['inCodItem'];
                $obTAlmoxarifadoCatalogoItem->recuperaRelacionamentoComSaldo($rsRecordSet, $stFiltro);
                if ($rsRecordSet->getNumLinhas() > 0) {
                    $inCodItem = $_REQUEST['inCodItem'];
                    include_once CAM_GP_FRO_MAPEAMENTO."TFrotaItem.class.php";
                    $obTFrotaItem = new TFrotaItem;
                    $obTFrotaItem->setDado('cod_item', $inCodItem);
                    $obTFrotaItem->recuperaPorChave($rsRecordSet);
                    if ($rsRecordSet->getNumLinhas() > 0) {
                        $obForm = new Form;
                        $obForm->setAction( "FMMovimentacaoDiversa.php" );

                        $obTxtSaldoAtual = new Label();
                        $obTxtSaldoAtual->setRotulo   ( 'Saldo Atual'  );
                        $obTxtSaldoAtual->setTitle    ( 'Saldo atualizado incluindo os itens da lista.' );
                        $obTxtSaldoAtual->setName     ( 'stSaldoAtual'   );
                        $obTxtSaldoAtual->setID       ( 'stSaldoAtual'   );

                        include_once( CAM_GP_FRO_COMPONENTES."IPopUpVeiculo.class.php" );
                        $obIPopUpVeiculo = new IPopUpVeiculo($obForm);
                        $obIPopUpVeiculo->setObrigatorioBarra( true );
                        $obIPopUpVeiculo->setNull( false );

                        $obNumKm = new Numerico();
                        $obNumKm->setRotulo   ( '**Quilometragem'                     );
                        $obNumKm->setTitle    ( 'Informe a quilometragem do veículo.' );
                        $obNumKm->setName     ( 'nmKm'                                );
                        $obNumKm->setId       ( 'nmKm'                                );
                        $obNumKm->setValue    ( $nmKm                                 );
                        $obNumKm->setDecimais ( 1                                     );
                        $obNumKm->setNegativo ( false                                 );

                        $obHdnFrota = new Hidden;
                        $obHdnFrota->setName  ( 'boFrota' );
                        $obHdnFrota->setId    ( 'boFrota' );
                        $obHdnFrota->setValue ( true      );

                        $obFormulario = new Formulario();
                        $obFormulario->addTitulo    ( 'Atualizações de Saldo ');
                        $obFormulario->addComponente( $obTxtSaldoAtual        );
                        $obFormulario->addTitulo    ( 'Vínculo do Frota');
                        $obFormulario->addComponente( $obIPopUpVeiculo  );
                        $obFormulario->addComponente( $obNumKm          );
                        $obFormulario->addHidden    ( $obHdnFrota       );

                        $obFormulario->montaInnerHtml();
                        $stJs .= "jq('#spnDadosFrota').html('".$obFormulario->getHtml()."');";
                    }
                }
            }
        } else {
            $stJs .= "alertaAviso('@Informe o Almoxarifado.','form','erro','".Sessao::getId()."');";
            $stJs .= "jq('#inCodAlmoxarifado').focus();";
        }
        if (!$stJs) {
            $stJs .= "jq('#spnDadosFrota').html('&nbsp;');";
        }

    break;

    case 'buscaSolicitante':
        if ( !empty($_REQUEST[ 'inCGMSolicitante' ]) ) {
            $obREstoqueItem->obRAlmoxarifado->obRCGMResponsavel->setNumCGM( $_REQUEST[ 'inCGMSolicitante' ] );
            $obREstoqueItem->obRAlmoxarifado->obRCGMResponsavel->consultar( $rsCGM );

            $inNumLinhas = $rsCGM->getNumLinhas();
            if ($inNumLinhas <= 0) {
                $stJs .= 'f.inNumCGM.value = "";';
                $stJs .= 'f.inNumCGM.focus();';
                $stJs .= 'd.getElementById("stNomCGMSolicitante").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@CGM do solicitante não encontrado. (".$_REQUEST["inCGMSolicitante"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgmSolicitante = $rsCGM->getCampo("nom_cgm");
                $stJs .= 'd.getElementById("stNomCGMSolicitante").innerHTML = "'.$stNomCgmSolicitante.'";';
            }
        }
    break;

    case 'incluirItem':

        $quantidadeFormatada = str_replace('.','',$_REQUEST['nuQuantidade']);
        $stSaldoFormatado = str_replace('.','',$_REQUEST['stSaldo']);
        $quantidade = str_replace(',','.',$quantidadeFormatada);
        $saldo = str_replace(',','.',$stSaldoFormatado);

        if (!$_REQUEST['inCodAlmoxarifado']) {
            $stJs = "alertaAviso('É necessário escolher um almoxarifado antes de incluir itens.','form','erro','".Sessao::getId()."');";
        }
        if ($quantidade > $saldo) {
            $stJs = "alertaAviso('Quantidade ".$_REQUEST['nuQuantidade']." deve ser menor ou igual ao Saldo em Estoque ".$_REQUEST['stSaldo']."','form','erro','".Sessao::getId()."');";
        }
        if (!$quantidade || ($quantidade == "0.0000" )) {
            $stJs = "alertaAviso('Quantidade não pode ser nula.','form','erro','".Sessao::getId()."');";
        }
        if ($_REQUEST['boFrota'] == true) {
            if (!$_REQUEST['inCodVeiculo']) {
                $stJs = "alertaAviso('Informe o veículo.','form','erro','".Sessao::getId()."');";
            }
            if (!$_REQUEST['nmKm']) {
                $stJs = "alertaAviso('Informe a quilometragem do veículo.','form','erro','".Sessao::getId()."');";
            }
            if (!$stJs) {
                $stJs = montaItensFrota();
            }
        } else {
            if (!$stJs) {
                $stJs = montaItens();
            }
        }

    break;

    case 'atualizaSaldo':
        if ($_REQUEST['stSaldo'] && $_REQUEST['inCodCentroCusto']) {

            $arItens = Sessao::read('arItens');

            $nuQuantidade = str_replace('.','',$_REQUEST['nuQuantidade']);
            $nuQuantidade = str_replace(',','.',$nuQuantidade);
            $nuSaldo      = str_replace('.','',$_REQUEST['stSaldo']);
            $nuSaldo      = str_replace(',','.',$nuSaldo);

            if (count($arItens > 0)) {
                foreach ($arItens as $arDados) {
                    if ($arDados['cod_item'] == $_REQUEST['inCodItem']) {
                        $nuQnt = str_replace('.','',$arDados['quantidade']);
                        $nuQnt = str_replace(',','.',$nuQnt);
                        $nuSomaQnt = $nuSomaQnt + $nuQnt;
                    }
                }
            }
            if ($nuSomaQnt) {
                $nuQuantidade = $nuQuantidade + $nuSomaQnt;
            }
            if ($nuSaldo >= $nuQuantidade) {
                $nuSaldoAtual = $nuSaldo - $nuQuantidade;
                $nuSaldoAtual = number_format($nuSaldoAtual,4,',','.');
                $stJs  = "jq('#btIncluirItem').removeAttr('disabled');";
                $stJs .= "jq('#stSaldoAtual').html('".$nuSaldoAtual."');";
            } else {
                $stJs  = "jq('#btIncluirItem').attr('disabled','disabled');";
                $stJs .= "jq('#nuQuantidade').focus();";
                $stJs .= "alertaAviso('A soma da quantidade dos itens não pode ultrapassar o saldo em estoque.','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs  = Resetar(2);
            $stJs .= "jq('#nuQuantidade').value = '';";
        }
    break;

    case 'confirmaItemFrota':
        $stJs .= montaItensFrota(true);
    break;

    case 'excluirItemAnulacao':
    case 'excluirItem':
        $arTEMP  = array();
        $inCount = 0;

        $arItens = Sessao::read('arItens');

        foreach ($arItens as $key => $value) {
            if ( ($key+1) != $_REQUEST['id'] ) {
                $value['id'] = $inCount+1;
                $arTEMP[$inCount] = $value;
                $inCount++;
            }
        }

        Sessao::remove('arItens');
        Sessao::write('arItens', $arTEMP);

        if ($stCtrl == 'excluirItemAnulacao') {
            $stJs .= montaListaItens( $arTEMP , 'anular');
        } else {
            $stJs .= montaListaItens( $arTEMP );
        }

        if ( count($arTEMP) == 0 ) {
            $stJs .= "f.inCodAlmoxarifado.disabled = false;";
        }

    break;

    case 'limpaItem':
        $stJs .= Resetar(1);
    break;

    case 'alteraItem':
        $stJs .= montaListaItens( Sessao::read('arItens') );
    break;

    case 'anularRequisicao':
        $stJs .= montaListaItens( Sessao::read('arItens'), 'anular' );
    break;

    case 'consultarRequisicao':
        $stJs .= montaListaItens( Sessao::read('arItens'), 'consultar' );
    break;

    case 'limpaTotal':
        $stJs .= Resetar(3);
        $stJs .= "f.inCodAlmoxarifado.disabled = false;\n";
        $stJs .= "f.inCodAlmoxarifado.options[0].selected = true;\n";
        $stJs .= "document.getElementById('spnAtributos').innerHTML = '&nbsp;';";
        Sessao::remove('arItens');
    break;

    case 'detalhaItem':
        detalhaItem();
    break;

    case 'validaValorAtributo':
        $arItens = Sessao::read('arItens');
        $arNomeCampo = explode( "_", $_REQUEST['nomeCampo'] );
        $idAtributo = ((int) $arNomeCampo[1] - 1);
        $nuValorAntigo = (float) str_replace( ',', '.' , $arItens[($_GET['idItem']-1)]['valores_atributos'][$idAtributo]['quantidade'] );
        $nuTotalAntigo = (float) str_replace( ',', '.' , $arItens[($_GET['idItem']-1)]['quantidade'] );
        $nuNovoValor   = (float) str_replace( ',', '.' , $_REQUEST['novaQuantidade'] );
        $nuSaldoAtributo = (float) str_replace( ',', '.' , $arItens[($_GET['idItem']-1)]['valores_atributos'][$idAtributo]['saldo_atributo'] );

        if ($nuSaldoAtributo >= $nuNovoValor) {
            $nuNovoTotal   = number_format( ($nuTotalAntigo + ( $nuNovoValor - $nuValorAntigo )), 4, ',', '.');
            $nuNovoValor   = number_format( $nuNovoValor, 4, ',', '.');

            $arItens[($_GET['idItem']-1)]['quantidade'] = $nuNovoTotal;
            $arItens[($_GET['idItem']-1)]['valores_atributos'][$idAtributo]['quantidade'] = $nuNovoValor;

            if ($_REQUEST['stAcao'] == 'anular') {
                $stJs .= "document.getElementById('nuAnular_".$_GET['idItem']."').value = '".$nuNovoTotal."';";
            } else {
                $stJs .= "document.getElementById('nuQuantidadeLista_".$_GET['idItem']."').value = '".$nuNovoTotal."';";
            }
        } else {
            $nuValorAntigo = number_format( $nuValorAntigo, 4, ',', '.');
            $stJs .= "document.getElementById('".$_REQUEST['nomeCampo']."').value = '".$nuValorAntigo."';";
            $stJs .= "document.getElementById('".$_REQUEST['nomeCampo']."').focus();";
            $stJs .= "alertaAviso('@A quantidade requisitada não pode ser maior que o saldo disponível.','form','erro','".Sessao::getId()."');";
        }
        Sessao::write('arItens', $arItens);
    break;

    case 'validaValorItem':

        $arNomeCampo = explode( "_", $_REQUEST['nomeCampo'] );
        $inIdItem = $arNomeCampo[1] - 1;
        $arItens = Sessao::read('arItens');

        $nuValorAntigo = (float) str_replace( ',', '.' , $arItens[$inIdItem]['quantidade'] );

        $nuNovoValor   = str_replace( ',', '.' , $_REQUEST['novaQuantidade'] );
        $nuSaldoItem   = str_replace( ',', '.' , $arItens[$inIdItem]['saldo_formatado'] );
        $nuNovoValor   = str_replace( '.', '' , $nuNovoValor );
        $nuSaldoItem   = str_replace( '.', '' , $nuSaldoItem );

        if ($nuSaldoItem >= $nuNovoValor) {
            $arItens[$inIdItem]['quantidade'] = $_REQUEST['novaQuantidade'];
        } else {
            $stJs .= "document.getElementById('".$_REQUEST['nomeCampo']."').value = '".$arItens[$inIdItem]['quantidade']."';";
            $stJs .= "document.getElementById('".$_REQUEST['nomeCampo']."').focus();";
            $stJs .= "alertaAviso('@A quantidade requisitada não pode ser maior que o saldo disponível.','form','erro','".Sessao::getId()."');";
        }
        Sessao::write('arItens', $arItens);
    break;

    case 'alterarQuantidadeLote':

        $arNomeCampo = explode( "_", $_REQUEST['nomeCampo'] );
        $inIdItem = $arNomeCampo[1] - 1;
        $inIdLote = $arNomeCampo[2] - 1;
        $arItens = Sessao::read('arItens');

        $nuValorAntigo = $arItens[$inIdItem]['lotes'][$inIdLote]['quantidade'];

        $nuNovoValor   = str_replace(',','.',str_replace('.','',$_REQUEST['novaQuantidade'] ) );
        $nuSaldoLote   = str_replace(',','.',str_replace('.','',$arItens[$inIdItem]['lotes'][$inIdLote]['saldo'] ) );

        if ($nuSaldoLote >= $nuNovoValor) {
            $arItens[$inIdItem]['lotes'][$inIdLote]['quantidade'] = str_replace(',','.',str_replace('.','',$_REQUEST['novaQuantidade'] ) );
            $nuTotalLote = 0;
            for ($inLote=0; $inLote<count($arItens[$inIdItem]['lotes']); $inLote++) {
                $nuTotalLote+= $arItens[$inIdItem]['lotes'][$inLote]['quantidade'];
            }
            $stJs .= "document.getElementById('nuQuantidadeLista_".($inIdItem+1)."').value = '".number_format($nuTotalLote,4,',','.')."';";
        } else {
            $stJs .= "document.getElementById('".$_REQUEST['nomeCampo']."').value = '".$arItens[$inIdItem]['lotes'][$inIdLote]['quantidade']."';";
            $stJs .= "document.getElementById('".$_REQUEST['nomeCampo']."').focus();";
            $stJs .= "alertaAviso('@A quantidade informada não pode ser maior que o saldo disponível.','form','erro','".Sessao::getId()."');";
        }
        Sessao::write('arItens', $arItens);

    break;
}

echo $stJs;
