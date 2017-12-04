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
    * Data de criação : 02/02/2006

    * @author Analista: Diego Victoria
    * @author Programador: Tonismar R. Bernardo

    * @ignore

    $Id: OCManterRequisicao.php 59612 2014-09-02 12:00:51Z gelson $
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoItemMarca.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoEstoqueItem.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterRequisicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

$obRItemMarca = new RAlmoxarifadoItemMarca;
$obREstoqueItem = new RAlmoxarifadoEstoqueItem;

$rsMarcas = new RecordSet;

function montaListaItens($arRecordSet , $stAcao = "")
{
    $pgOcul = "OCManterRequisicao.php";

    $rsItens = new RecordSet;
    $rsItens->preenche( $arRecordSet );

    $table = new TableTree();
    $table->setRecordset( $rsItens );

    $table->addCondicionalTree("possui_atributos", "true");
    $table->setArquivo( $pgOcul );
    $table->setParametros( array( "id") );
    $table->setComplementoParametros ( "stCtrl=detalhaItem&stAcao=".$stAcao );

    $table->setSummary('Itens');

    $table->Head->addCabecalho( 'Item'  , 20 );
    $table->Head->addCabecalho( 'Marca' , 15 );
    $table->Head->addCabecalho( 'Centro de Custo' , 20 );

    $boMostraSaldo = sistemalegado::pegaConfiguracao( 'demonstrar_saldo_estoque',29 ); // 29 = modulo almoxarifado

    if (($stAcao != "anular") && ($stAcao != "consultar") && ($stAcao != "homologar") && ($stAcao != "anular_homolog") ) {
        if (strtolower($boMostraSaldo)=='true') {
            $table->Head->addCabecalho('Saldo'      , 5);
        }

        $table->Head->addCabecalho('Quantidade' , 5);
    } else {
        $table->Head->addCabecalho('Requisitada' , 5);
        $table->Head->addCabecalho('Atendida'    , 5);

        if (($stAcao == "consultar") || ($stAcao == "homologar") || ($stAcao == "anular_homolog") ) {
            $table->Head->addCabecalho('Devolvida' , 5);
        }

        $table->Head->addCabecalho('Anulada' , 5);

        if (($stAcao == "consultar") || ($stAcao == "homologar") || ($stAcao == "anular_homolog")) {
            $table->Head->addCabecalho('Saldo Pendente' , 5);
        } else {
            $table->Head->addCabecalho('A Anular' , 5);
        }
    }

    $table->Body->addCampo('[cod_item]-[descricao_item]'     , 'E');
    $table->Body->addCampo('[cod_marca]-[descricao_marca]'   , 'E');
    $table->Body->addCampo('[cod_centro]-[descricao_centro]' , 'E');

    if (($stAcao != "anular") && ($stAcao != "consultar") && ($stAcao != "homologar") && ($stAcao != "anular_homolog")) {
        if (strtolower($boMostraSaldo)=='true') {
            $table->Body->addCampo( '[saldo_formatado]', 'E' );
        }

        $obQuantidade = new Quantidade();
        $obQuantidade->setValue( "quantidade" );
        $obQuantidade->setName ( "nuQuantidadeLista" );
        $obQuantidade->setNull ( false );
        $obQuantidade->setSize ( 10 );
        $obQuantidade->setId   ( "" );
        $obQuantidade->setValue( "[quantidade]" );
        $obQuantidade->obEvento->setOnChange("JavaScript:executaFuncaoAjax('validaValorItem', '&nomeCampo='+this.name+'&novaQuantidade='+this.value);");

        $table->Body->addComponente( $obQuantidade );

        $table->Body->addAcao( 'excluir' ,  'excluirItem(%s)' , array( 'id' ) );

    } else {
        $table->Body->addCampo( '[requisitada]', 'E' );
        $table->Body->addCampo( '[atendida]', 'E' );
        if (($stAcao == "consultar") || ($stAcao == "homologar") || ($stAcao == "anular_homolog")) {
            $table->Body->addCampo( '[devolvida]', 'E' );
        }

        $table->Body->addCampo( '[anulada]', 'E' );
        if (($stAcao == "consultar" ) || ($stAcao == "homologar") || ($stAcao == "anular_homolog")) {
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

        if ($stAcao != "consultar" && $stAcao != "anular" && $stAcao != "homologar" && $stAcao != "anular_homolog") {
            $table->Body->addAcao('excluir', 'excluirItemAnulacao(%s)', array('id'));
        }
    }

    $table->montaHTML(true);
    $stHTML = $table->getHtml();

    $stJs = "document.getElementById('spnItens').innerHTML = '".$stHTML."';";

    if (($stAcao != "consultar") && ($stAcao != "homologar") && ($stAcao != "anular_homolog")) {
        $stJs .= bloqueiaQuantidadeLista($stAcao);
    }

    return $stJs;
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
            $stJs .= "jQuery('#inSaldo').html('&nbsp;');\n";
            $stJs .= "d.getElementById('stUnidadeMedida').innerHTML = '&nbsp;';\n";
            $stJs .= "d.getElementById('stNomItem').innerHTML = '&nbsp;';\n";
            $stJs .= "f.inCodItem.value = '';\n";
            $stJs .= "limpaSelect(f.inCodMarca, 1 );\n";
            $stJs .= "limpaSelect(f.inCodCentroCusto, 1 );\n";
            $stJs .= "f.nuQuantidade.value = '';\n";
            $stJs .= "document.getElementById('nuQuantidade').readOnly = false;\n";
            $stJs .= "document.getElementById('spnAtributos').innerHTML = '&nbsp;';";

            break;
        case 2:
            $stJs .= "jQuery('#inSaldo').html('&nbsp;');\n";
            $stJs .= "f.nuQuantidade.value = '';\n";
            break;
        case 3:
            $stJs .= "jQuery('#inSaldo').html('&nbsp;');\n";
            $stJs .= "d.getElementById('stUnidadeMedida').innerHTML = '&nbsp;';\n";
            $stJs .= "d.getElementById('stNomItem').innerHTML = '&nbsp;';\n";
            $stJs .= "d.getElementById('spnItens').innerHTML = '&nbsp;';\n";
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
    $arItens = Sessao::read('arItens');
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
    if (( $_REQUEST['stAcao'] != "consultar" ) && ( $_REQUEST['stAcao'] != "homologar" ) && ( $_REQUEST['stAcao'] != "anular_homolog" )) {
        $table->Body->addComponente( $obTxtQuantidadeAtributo );
    } else {
        $table->Body->addCampo( '[quantidade]', 'E' );
    }

    $table->montaHTML();
    $stHTML = $table->getHtml();

    echo $stHTML;
}

switch ($stCtrl) {
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

        $arTEMP = array();

        $arItens = Sessao::read('arItens');

        if (!$_REQUEST['inCodAlmoxarifado']) {
            $stMensagem = "É necessário escolher um almoxarifado antes de incluir itens.";
        }

        $quantidadeFormatada = str_replace('.','',$_REQUEST['nuQuantidade']);
        $stSaldoFormatado = str_replace('.','',$_REQUEST['stSaldo']);

        $quantidade = str_replace(',','.',$quantidadeFormatada);
        $saldo = str_replace(',','.',$stSaldoFormatado);

        if ($quantidade > $saldo) {
            $boMostraSaldo = sistemalegado::pegaConfiguracao( 'demonstrar_saldo_estoque',29 ); // 29 = modulo almoxarifado

            if (strtolower($boMostraSaldo) == 'true') {
                $stMensagem = 'Quantidade '.$_REQUEST['nuQuantidade'].' deve ser menor ou igual ao Saldo em Estoque '.$_REQUEST['stSaldo'].'.';
            } else {
                $stMensagem = 'Quantidade indisponível no estoque.';
            }
        }

        if ( !$quantidade || ($quantidade == 0) ) {
            $stMensagem = 'Quantidade não pode ser nula.';
        }

        // Validação para identificar se o item/marca/centro de custo já está na lista.
        if ( count($arItens) > 0 ) {
            foreach ($arItens as $arTEMP) {
                if (( $arTEMP['cod_item'] == $_REQUEST['inCodItem']  ) && ( $arTEMP['cod_centro'] == $_REQUEST['inCodCentroCusto'] ) && ( $arTEMP['cod_marca'] == $_REQUEST['inCodMarca'] )) {
                    $stMensagem = "Este item já está na lista, efetue a alteração." ;
                    break;
                }
            }
        }

        if ( empty($stMensagem) ) {

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

            $obIMontaItemQuantidadeValoresAtributo = Sessao::read('obIMontaItemQuantidadeValoresAtributo');

            if ( count($obIMontaItemQuantidadeValoresAtributo) > 0 ) {

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

                    //Dados para preencher a lista ja formatados
                    $arItens[$inCount]['valores_atributos'][$chave]['stValoresAtributosLista'] = $valorAtributo;
                    $arItens[$inCount]['valores_atributos'][$chave]['stValoresAtributos'] = $dados['stValoresAtributos'];
                    $arItens[$inCount]['valores_atributos'][$chave]['NomeAtributos'] = $dados['NomeAtributos'];
                    $arItens[$inCount]['valores_atributos'][$chave]['quantidade'] = $dados['quantidade'];
                    $arItens[$inCount]['valores_atributos'][$chave]['saldo_atributo'] = $dados['saldo_atributo'];

                    // formatação para poder inserir no banco segundo a rotina da regra (RAlmoxarifadoRequisicaoItem)
                    $arItens[$inCount]['valores_atributos'][$chave]['atributo'] = $dados['atributo'];
                    unset($valorAtributo);
                }
            } else {
                $arItens[$inCount]['possui_atributos'] = "false";
            }

            Sessao::write('arItens',array());
            Sessao::write('arItens', $arItens);
            $stJs .= montaListaItens( $arItens);

            $stJs .= Resetar(1);
            $stJs .= "f.inCodAlmoxarifado.disabled = true;";
        } else {
            $stJs .= "alertaAviso('@Valor inválido. (".$stMensagem.")','form','erro','".Sessao::getId()."');";
        }
        $stMensagem = "";
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

}

echo $stJs;
