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
    * Data de Criação: 26/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: OCManterManutencao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaVeiculo.class.php' );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaAutorizacao.class.php' );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaEfetivacao.class.php' );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaItem.class.php' );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaUtilizacaoRetorno.class.php' );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaManutencao.class.php' );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterManutencao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function montaListaItens($arItens, $boReadOnly = false)
{

    $pgOcul = "OCManterManutencao.php";

    if ( !is_array($arItens) ) {
        $arItens = array();
    }

    $rsItens = new RecordSet();
    $rsItens->preenche( $arItens );

    //formata os campos
    $rsItens->addFormatacao('valor','NUMERIC_BR');
    $rsItens->addFormatacao('quantidade','NUMERIC_BR_4');

    $obTable = new Table();
    $obTable->setRecordset( $rsItens );
    $obTable->setSummary( 'Lista de Itens da Manutenção' );

    $obTable->Head->addCabecalho( 'Item', 60 );
    $obTable->Head->addCabecalho( 'Tipo',10 );
    $obTable->Head->addCabecalho( 'Quantidade', 10 );
    $obTable->Head->addCabecalho( 'Valor', 10 );

    $obTable->Body->addCampo( '[cod_item] - [descricao]', 'E' );
    $obTable->Body->addCampo( 'tipo', 'C' );
    $obTable->Body->addCampo( 'quantidade','C' );
    $obTable->Body->addCampo( 'valor', 'C' );

    if (!$boReadOnly) {
        $obTable->Body->addAcao( 'alterar', "JavaScript:ajaxJavaScript(  '".CAM_GP_FRO_INSTANCIAS."manutencao/".$pgOcul."?".Sessao::getId()."&id=%s', 'montaAlterarListaItem' );", array( 'id' ) );
        $obTable->Body->addAcao( 'excluir', "JavaScript:ajaxJavaScript(  '".CAM_GP_FRO_INSTANCIAS."manutencao/".$pgOcul."?".Sessao::getId()."&inCodItem=%s', 'excluirListaItem' );", array( 'cod_item' ) );
    }

    $obTable->montaHTML( true );
    if ( $rsItens->getNumLinhas() > 0 ) {
        return "$('spnItens').innerHTML = '".$obTable->getHtml()."';";
    } else {
        return "$('spnItens').innerHTML = '&nbsp;';";
    }
}

function montaQuilometragem($inCodVeiculo)
{
    global $request;
    $kmManutencao = 0;
    //recupera a quilometragem do veiculo
    $obTFrotaUtilizacaoRetorno = new TFrotaUtilizacaoRetorno();
    $obTFrotaUtilizacaoRetorno->setDado('cod_veiculo',$inCodVeiculo);
    $obTFrotaUtilizacaoRetorno->recuperaRetornoVeiculo( $rsRetorno );

    if ($rsRetorno->getNumLinhas() > 0) {
        $kmManutencao = $rsRetorno->getCampo('km_inicial');
    }

    if ($request->get('stAcao') != 'incluir') {

        $obTFrotaManutencao = new TFrotaManutencao;
        $obTFrotaManutencao->setDado('cod_manutencao', $request->get('inCodManutencao'));
        $obTFrotaManutencao->setDado('exercicio', Sessao::getExercicio());
        $obTFrotaManutencao->recuperaManutencaoAnalitica($rsKmManutencao);

        if ($rsKmManutencao->getNumLinhas() > 0) {
            $kmManutencao = $rsKmManutencao->getCampo('km');
        }
    }

    //cria um textbox para a quilometragem
    $obInQuilometragem = new TextBox();
    $obInQuilometragem->setName( 'inQuilometragem' );
    $obInQuilometragem->setRotulo( 'Quilometragem' );

    //quilometragem do veiculo, quando utilizado, a de retorno, caso contrario, a km_inicial do veiculo
    $obInQuilometragem->setTitle( 'Quilometragem na manutenção.' );
    $obInQuilometragem->setValue( $kmManutencao );

    //instancia um formulario para a quilometragem
    $obFormulario = new Formulario();
    $obFormulario->addComponente( $obInQuilometragem );
    $obFormulario->montaInnerHTML();

    return "$('spnQuilometragem').innerHTML = '".$obFormulario->getHTML()."';";
}

switch ($stCtrl) {
    case 'montaVeiculo' :

        $arItensAutorizacao = Sessao::read('arItensAutorizacao');
        $stJs = isset($stJs) ? $stJs : null;

        if ( ($request->get('inCodVeiculo') != '' AND $request->get('inCodVeiculo') > 0) OR $request->get('stPrefixo') != '' OR $request->get('stNumPlaca') != '' ) {

            //recupera os dados do veículo
            $obTFrotaVeiculo = new TFrotaVeiculo();
            $obTFrotaVeiculo->setDado( 'cod_veiculo', $request->get('inCodVeiculo') );
            $obTFrotaVeiculo->setDado( 'prefixo', $request->get('stPrefixo') );
            $obTFrotaVeiculo->setDado( 'placa', str_replace('-','',$request->get('stNumPlaca')) );
            $obTFrotaVeiculo->recuperaVeiculoSintetico( $rsVeiculo );

            //recupera dados da autorizacao
            $arAutorizacao = explode('/',$_REQUEST['inCodAutorizacao']);
            $obTFrotaAutorizacao = new TFrotaAutorizacao();
            $obTFrotaAutorizacao->setDado('cod_autorizacao', $arAutorizacao[0] );
            $obTFrotaAutorizacao->setDado('exercicio', isset($arAutorizacao[1]) ? $arAutorizacao[1] : null);
            $obTFrotaAutorizacao->recuperaPorChave( $rsAutorizacao );

            if ( $rsAutorizacao->getCampo('cod_veiculo') != $rsVeiculo->getCampo('cod_veiculo') ) {
                Sessao::write('arItensAutorizacao' , array());
                $stJs .= "$('inCodAutorizacao').value = ''; ";
                $stJs .= "$('inCodVeiculo').value = '".$rsVeiculo->getCampo('cod_veiculo')."';";
                $stJs .= "$('stNomVeiculo').innerHTML = '".$rsVeiculo->getCampo('nom_modelo')."';";
                $stJs .= "$('stPrefixo').value = '".$rsVeiculo->getCampo('prefixo')."';";
                if ($rsVeiculo->getCampo('placa_masc') != '-') {
                    $stJs .= "$('stNumPlaca').value = '".$rsVeiculo->getCampo('placa_masc')."';";
                } else {
                    $stJs .= "$('stNumPlaca').value = '';";
                }
                if ($rsVeiculo->getCampo('cod_veiculo')) {
                    $stJs .= montaQuilometragem( $rsVeiculo->getCampo('cod_veiculo') );
                } else {
                    $stJs .= "$('spnQuilometragem').innerHTML = '';";
                    $stJs .= "$('inCodItem').value = '';";
                    $stJs .= "$('stNomItem').innerHTML = '&nbsp;';";
                    $stJs .= "$('inQuantidade').value = '0,0000';";
                    $stJs .= "$('inValor').value = '0,00';";
                    $stJs .= "$('incluiItem').setAttribute('onclick','montaParametrosGET(\'incluirListaItem\',\'inCodItem,inQuantidade,inValor\');');";
                    $stJs .= "$('incluiItem').value = 'Incluir';";
                    $stJs .= "$('inCodItem').disabled = false;";
                    Sessao::remove('arItensAutorizacao');
                    Sessao::write('arItensAutorizacao' , array());
                    $arItensAutorizacao = Sessao::read('arItensAutorizacao');
                }
            }
        } else {
            $stJs .= "$('inCodAutorizacao').value = ''; ";
            $stJs .= "$('inCodVeiculo').value = '';";
            $stJs .= "$('stNomVeiculo').innerHTML = '&nbsp;';";
            $stJs .= "$('stPrefixo').value = '';";
            $stJs .= "$('stNumPlaca').value = '';";
        }
        //monta a lista de itens
        $stJs .= montaListaItens( $arItensAutorizacao );

        break;

    case 'preencheAutorizacao' :
        //limpa todos os campos
        $stJs= isset($stJs) ? $stJs : null;
        $stJs .= "$('inCodAutorizacao').value = '';";
        $stJs .= "$('inCodVeiculo').value = '';";
        $stJs .= "$('stNomVeiculo').innerHTML = '&nbsp;';";
        $stJs .= "$('stPrefixo').value = '';";
        $stJs .= "$('stNumPlaca').value = '';";
        Sessao::write('arItensAutorizacao' , array());
        if ($_REQUEST['inCodAutorizacao'] != '') {
            $arAutorizacao = explode( '/', $_REQUEST['inCodAutorizacao'] );
            if ( strlen($arAutorizacao[1]) != 4 ) {
                $arAutorizacao[1] = Sessao::getExercicio();
            }
            //recupera a autorizacao
            $obTFrotaAutorizacao = new TFrotaAutorizacao();
            $obTFrotaAutorizacao->setDado( 'cod_autorizacao', $arAutorizacao[0] );
            $obTFrotaAutorizacao->setDado( 'exercicio', $arAutorizacao[1] );
            $stFiltro = " AND NOT EXISTS ( SELECT 1
                                             FROM frota.efetivacao
                                            WHERE efetivacao.cod_autorizacao = autorizacao.cod_autorizacao
                                              AND efetivacao.exercicio_autorizacao = autorizacao.exercicio
                                              AND NOT EXISTS ( SELECT 1
                                                                 FROM frota.manutencao_anulacao
                                                                WHERE manutencao_anulacao.exercicio = efetivacao.exercicio_manutencao
                                                                  AND manutencao_anulacao.cod_manutencao = efetivacao.cod_manutencao
                                                             )
                                          ) ";
            $obTFrotaAutorizacao->recuperaAutorizacao( $rsAutorizacao, $stFiltro );
            if ( $rsAutorizacao->getNumLinhas() <= 0 ) {
                $obTFrotaEfetivacao = new TFrotaEfetivacao;
                $obTFrotaEfetivacao->recuperaTodos( $rsEfetivacao, " WHERE cod_autorizacao = ".$arAutorizacao[0]." AND exercicio_autorizacao = '".$arAutorizacao[1]."' " );
                if ( $rsEfetivacao->getNumLinhas() > 0 ) {
                    $stMensagem = 'Este código de autorização já foi efetivado';
                } else {
                    $stMensagem = 'Código da Autorização inválido';
                }
            }

            if ( !isset($stMensagem) ) {
                //preenche os campos do formulario
                $stJs .= "$('inCodAutorizacao').value = '".$rsAutorizacao->getCampo('cod_autorizacao').'/'.$rsAutorizacao->getCampo('exercicio')."';";
                $stJs .= "$('inCodVeiculo').value = '".$rsAutorizacao->getCampo('cod_veiculo')."';";
                $stJs .= "$('stNomVeiculo').innerHTML = '".$rsAutorizacao->getCampo('nom_modelo')."';";
                $stJs .= "$('stNumPlaca').value = '".$rsAutorizacao->getCampo('placa_masc')."';";
                $stJs .= "$('stPrefixo').value = '".$rsAutorizacao->getCampo('prefixo')."';";

                $stJs .= montaQuilometragem( $rsAutorizacao->getCampo('cod_veiculo') );

                //coloca na sessao os dados do item
                $arItensAutorizacao[0]['id'         ] = 0;
                $arItensAutorizacao[0]['cod_item'   ] = $rsAutorizacao->getCampo('cod_item');
                $arItensAutorizacao[0]['descricao'  ] = $rsAutorizacao->getCampo('descricao');
                $arItensAutorizacao[0]['quantidade' ] = $rsAutorizacao->getCampo('quantidade');
                $arItensAutorizacao[0]['valor'      ] = $rsAutorizacao->getCampo('valor');
                $arItensAutorizacao[0]['tipo'       ] = 'Combustível';
                $arItensAutorizacao[0]['combustivel'] = true;

                if ( $rsAutorizacao->getCampo('quantidade') == '0.0000' OR $rsAutorizacao->getCampo('valor') == '0.00' ) {
                    $arItensAutorizacao[0]['alteravel'  ] = true;
                } else {
                    $arItensAutorizacao[0]['alteravel'  ] = false;
                }
            } else {
                $stJs .= "alertaAviso('".$stMensagem."!','frm','aviso','".Sessao::getId()."');";
            }
        }

        Sessao::write('arItensAutorizacao' , $arItensAutorizacao);
        //monta a lista de itens
        $stJs .= montaListaItens( $arItensAutorizacao );

    break;

    case 'buscaEmpenho':
        $stJs = isset($stJs) ? $stJs : null;
        if ($_REQUEST["inCodigoEmpenho"] != '' && $_REQUEST["inCodEntidade"] != '' && $_REQUEST['stExercicioEmpenho'] != '') {

            $obREmpenhoEmpenho = new REmpenhoEmpenho;

            $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade ( $_REQUEST["inCodEntidade"]  );
            $obREmpenhoEmpenho->setExercicio  ( $_REQUEST['stExercicioEmpenho'] );
            $obREmpenhoEmpenho->setCodEmpenhoInicial ( $_REQUEST["inCodigoEmpenho"] );
            $obREmpenhoEmpenho->setCodEmpenhoFinal ( $_REQUEST["inCodigoEmpenho"] );
            $obREmpenhoEmpenho->setSituacao ( 5 );
            $obREmpenhoEmpenho->listar($rsLista);

            if ($rsLista->getNumLinhas() > 0 ) {
                $obREmpenhoEmpenho->setCodEmpenho ( $_REQUEST["inCodigoEmpenho"] );
                $obREmpenhoEmpenho->consultar();
                $stNomFornecedor = ( $rsLista->getCampo( 'nom_fornecedor' ) ) ? str_replace( "'","\'",$rsLista->getCampo( "nom_fornecedor" )):'&nbsp;';
                $stJs .= "d.getElementById('stNomFornecedor').innerHTML='".$stNomFornecedor."';";
            } else {
                $stJs .= "$('inCodigoEmpenho').value='';";
                $stJs .= "$('stNomFornecedor').innerHTML = '&nbsp;';";
                $stJs .= "alertaAviso('Empenho informado está anulado ou não existe.','frm','erro','".Sessao::getId()."'); \n";
            }
        } else {
            $stJs .= "$('inCodigoEmpenho').value='';";
            $stJs .= "$('stNomFornecedor').innerHTML = '&nbsp;';";
            $stJs .= "alertaAviso('Empenho informado está anulado ou não existe.','frm','erro','".Sessao::getId()."'); \n";
        }
        break;

    case 'incluirListaItem' :
        $stJs = isset($stJs) ? $stJs : null;
        if ($_REQUEST['inCodItem'] == '') {
            $stMensagem = 'Preencha o campo Item';
        } elseif ($_REQUEST['inQuantidade'] == '0,0000') {
            $stMensagem = 'Quantidade inválida';
        } else {

            $arItensAutorizacao = Sessao::read('arItensAutorizacao');
            Sessao::remove('arItensAutorizacao');

            if ( count( $arItensAutorizacao ) > 0 ) {
                foreach ($arItensAutorizacao AS $arTemp) {
                    if ($arTemp['cod_item'] == $_REQUEST['inCodItem']) {
                        $stMensagem = 'Este item já está na lista';
                        break;
                    }
                }
            }
        }

        if (!$stMensagem) {
            //recupera os dados do item
            $obTFrotaItem = new TFrotaItem();
            $obTFrotaItem->setDado('cod_item', $_REQUEST['inCodItem']);
            $obTFrotaItem->recuperaRelacionamento( $rsItem );

            if ( $rsItem->getNumLinhas() > 0 ) {
                $inCount = count( $arItensAutorizacao );
                //coloca na sessao os dados do item
                $arItensAutorizacao[$inCount]['id'         ] = $inCount;
                $arItensAutorizacao[$inCount]['cod_item'   ] = $rsItem->getCampo('cod_item');
                $arItensAutorizacao[$inCount]['descricao'  ] = $rsItem->getCampo('descricao');
                $arItensAutorizacao[$inCount]['quantidade' ] = str_replace(',','.',str_replace('.','',$_REQUEST['inQuantidade']));
                $arItensAutorizacao[$inCount]['valor'      ] = str_replace(',','.',str_replace('.','',$_REQUEST['inValor']));
                $arItensAutorizacao[$inCount]['tipo'       ] = $rsItem->getCampo('nom_tipo');
                $arItensAutorizacao[$inCount]['combustivel'] = false;
                $arItensAutorizacao[$inCount]['alteravel'  ] = true;

                $stJs .= "$('inCodItem').value = '';";
                $stJs .= "$('stNomItem').innerHTML = '&nbsp;';";
                $stJs .= "$('inQuantidade').value = '0,0000';";
                $stJs .= "$('inValor').value = '0,00';";

                Sessao::write('arItensAutorizacao' , $arItensAutorizacao);

                $stJs .= montaListaItens( $arItensAutorizacao );
            }
        } else {
            $stJs .= "alertaAviso('".$stMensagem."!','frm','erro','".Sessao::getId()."'); \n";
        }
    break;

    case 'montaAlterarListaItem' :

        $arItensAutorizacao = Sessao::read('arItensAutorizacao');
        $stJs = isset($stJs) ? $stJs : null;
        if ($arItensAutorizacao[$_REQUEST['id']]['alteravel'] == true) {
            $stJs .= "jQuery('#inQuantidade').focus();";
            $stJs .= "$('inCodItem').value = '".$arItensAutorizacao[$_REQUEST['id']]['cod_item']."';";
            $stJs .= "$('stNomItem').innerHTML = '".$arItensAutorizacao[$_REQUEST['id']]['descricao']."';";
            $stJs .= "$('hdnId').value = '".$_REQUEST['id']."';";
            $stJs .= "$('inCodItem').disabled = true;";
            $stJs .= "$('imgItem').style.display = 'none';";
            $stJs .= "$('inQuantidade').value = '".number_format($arItensAutorizacao[$_REQUEST['id']]['quantidade'],4,',','.')."';";
            $stJs .= "$('inValor').value = '".number_format($arItensAutorizacao[$_REQUEST['id']]['valor'],2,',','.')."';";
            $stJs .= "$('incluiItem').setAttribute('onclick','montaParametrosGET(\'alterarListaItem\',\'hdnId,inQuantidade,inValor\');');";
            $stJs .= "$('incluiItem').value = 'Alterar';";
        } else {
            $stJs .= "alertaAviso('Este item não pode ser alterado!','frm','erro','".Sessao::getId()."'); \n";
        }
    break;

    case 'alterarListaItem' :
        $stJs = isset($stJs) ? $stJs : null;
        $arItensAutorizacao = Sessao::read('arItensAutorizacao');

        $itemId = $_REQUEST['hidden'];

        if ($_REQUEST['inQuantidade'] == '0,0000') {
            $stMensagem = 'Quantidade inválida';
        }

        if ($_REQUEST['inValor'] == '0,00') {
            $stMensagem = 'Valor inválido';
        }

        if (!$stMensagem) {

            $arItensAutorizacao = Sessao::read('arItensAutorizacao');
            Sessao::remove('arItensAutorizacao');

            $arItensAutorizacao[$itemId]['quantidade' ] = str_replace(',','.',str_replace('.','',$_REQUEST['inQuantidade']));
            $arItensAutorizacao[$itemId]['valor'      ] = str_replace(',','.',str_replace('.','',$_REQUEST['inValor']));
            $stJs .= "$('incluiItem').setAttribute('onclick','montaParametrosGET(\'incluirListaItem\',\'inCodItem,inQuantidade,inValor\');');";
            $stJs .= "$('incluiItem').value = 'Incluir';";
            $stJs .= "$('inCodItem').value = '';";
            $stJs .= "$('stNomItem').innerHTML = '&nbsp;';";
            $stJs .= "$('inQuantidade').value = '0,0000';";
            $stJs .= "$('inValor').value = '0,00';";
            $stJs .= "$('inCodItem').disabled = false;";
            $stJs .= "$('imgItem').style.display = 'inline';";

            Sessao::write('arItensAutorizacao', $arItensAutorizacao);
            $stJs .= montaListaItens( $arItensAutorizacao );

        } else {
            $stJs .= "alertaAviso('".$stMensagem."!','frm','erro','".Sessao::getId()."'); \n";
        }

    break;

    case 'excluirListaItem' :
        $stJs = isset($stJs) ? $stJs : null;
        $arItensAutorizacao = Sessao::read('arItensAutorizacao');

        foreach ($arItensAutorizacao AS $arTemp) {
            if ($arTemp['cod_item'] != $_REQUEST['inCodItem']) {
                $arAux[] = $arTemp;
            } elseif ($arTemp['combustivel']) {
                $stJs .= "$('inCodAutorizacao').value = '';";
            }
        }
        Sessao::write('arItensAutorizacao' , $arAux);
        $stJs .= montaListaItens( Sessao::read('arItensAutorizacao') );
    break;

    case 'montaListaItens' :
        $stJs .= montaListaItens( Sessao::read('arItensAutorizacao'), ($_REQUEST['boReadOnly'] == 'true' ) ? true : false );
    break;

    case 'montaQuilometragem' :
        $stJs .= montaQuilometragem( $_REQUEST['inCodVeiculo'] );
    break;

    case 'montaMascara' :
        $arCodigo = ( $_REQUEST['inCodAutorizacao'] ) ? explode('/',$_REQUEST['inCodAutorizacao']) : explode('/',$_REQUEST['inCodManutencao']) ;
        $stId = ( $_REQUEST['inCodAutorizacao'] ) ? 'inCodAutorizacao' : 'inCodManutencao';
        if ($arCodigo[0] != '' AND $arCodigo[0] > 0) {
            if ( strlen($arCodigo[1]) < 4 ) {
                $arCodigo[1] = Sessao::getExercicio();
            }
            $stJs .= "$('".$stId."').value = '".$arCodigo[0].'/'.$arCodigo[1]."';";
        } else {
            $stJs .= "$('".$stId."').value = '';";
        }
    break;

    case 'limparItem' :
        $stJs = isset($stJs) ? $stJs : null;
        $stJs .= "$('incluiItem').setAttribute('onclick','montaParametrosGET(\'incluirListaItem\',\'inCodItem,inQuantidade,inValor\');');";
        $stJs .= "$('incluiItem').value = 'Incluir';";
        $stJs .= "$('inCodItem').value = '';";
        $stJs .= "$('stNomItem').innerHTML = '&nbsp;';";
        $stJs .= "$('inQuantidade').value = '0,0000';";
        $stJs .= "$('inValor').value = '0,00';";
        $stJs .= "$('inCodItem').disabled = false;";
        $stJs .= "$('imgItem').style.display = 'inline';";
    break;
}

echo $stJs;
