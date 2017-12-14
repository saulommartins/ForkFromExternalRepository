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
    * Página de Oculto de Manter Inventario
    * Data de Criação: 02/10/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Id:$

    * Casos de uso: uc-03.03.15

    */

$stCtrl = $_REQUEST['stCtrl'];

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

function carregaInventario()
{
    include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoItem.class.php");
    include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoInventarioItemValor.class.php");
    $arInventario = Sessao::read('inventario');

    $obTInventarioItens = new TAlmoxarifadoInventarioItens();
    $obTInventarioItens->setDado( 'exercicio'       , $_GET['stExercicio'] );
    $obTInventarioItens->setDado( 'cod_almoxarifado', $_GET['inCodAlmoxarifado'] );
    $obTInventarioItens->setDado( 'cod_inventario'  , $_GET['inCodInventario'] );
    $obTInventarioItens->recuperaItensInventario( $rsItensInventario );

    $rsItensInventario->addFormatacao( 'saldo', 'NUMERIC_BR_4' );
    $rsItensInventario->addFormatacao( 'quantidade', 'NUMERIC_BR_4' );

    $arInventario['stExercicio']       = $_GET['stExercicio'];
    $arInventario['inCodInventario']   = $_GET['inCodInventario'];
    $arInventario['inCodAlmoxarifado'] = $rsItensInventario->getCampo('cod_almoxarifado');
    $arInventario['stNomAlmoxarifado'] = $rsItensInventario->getCampo('desc_almoxarifado');
    $arInventario['inCodCatalogoTxt']  = $rsItensInventario->getCampo('cod_catalogo');
    $arInventario['stNomCatalogoTxt']  = $rsItensInventario->getCampo('desc_catalogo');
    $arInventario['stObservacao']      = $rsItensInventario->getCampo('observacao');

    $inIdClassificacao = -1;
    $inCodClassificacaoAnterior = '';

    $inIdItem = -1;
    $inCodItem = '';

    $inIdCentro = -1;

    while ( !$rsItensInventario->eof() ) {

        if ( $inCodClassificacaoAnterior != $rsItensInventario->getCampo('cod_classificacao') ) {
            $inIdClassificacao++;
            $inIdItem = -1;

            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['inIdClassificacao']       = $inIdClassificacao;
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['cod_classificacao']       = $rsItensInventario->getCampo("cod_classificacao");
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['cod_catalogo']            = $rsItensInventario->getCampo("cod_catalogo");
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['cod_estrutural']          = $rsItensInventario->getCampo("cod_estrutural");
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['cod_estrutural_reduzido'] = $rsItensInventario->getCampo("cod_estrutural");
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['descricao']               = $rsItensInventario->getCampo("desc_classificacao");

            $inCodClassificacaoAnterior = $rsItensInventario->getCampo("cod_classificacao");
        }

        $boItemDifirenteAnterior = $inCodItem != $rsItensInventario->getCampo('cod_item') || $inCodMarca != $rsItensInventario->getCampo('cod_marca');

        if ($boItemDifirenteAnterior) {
            $inIdItem++;
            $inCodItem  = $rsItensInventario->getCampo('cod_item');
            $inCodMarca = $rsItensInventario->getCampo('cod_marca');
        }

        if ($_REQUEST['stAcao'] == "alterar" or $_REQUEST['stAcao'] == "processar") {

            if ($boItemDifirenteAnterior) {
                $inIdCentro = -1;

                $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['inIdItem']           = $inIdItem;
                $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_estrutural']     = $rsItensInventario->getCampo('cod_estrutural');
                $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_item']           = $rsItensInventario->getCampo('cod_item');
                $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['descricao_resumida'] = $rsItensInventario->getCampo('descricao_resumida');
                $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['descricao_item']     = $rsItensInventario->getCampo('descricao');
                $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['nom_unidade']        = $rsItensInventario->getCampo('nom_unidade');
                $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_marca']          = $rsItensInventario->getCampo('cod_marca');
                $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['desc_marca']         = $rsItensInventario->getCampo('desc_marca');

                //carrega os atributos
                $obTAtributoCatalogoItem = new TAlmoxarifadoAtributoCatalogoItem();
                $obTAtributoCatalogoItem->recuperaAtributoDinamicoItem( $rsAtributos, ' AND cod_item = '.$rsItensInventario->getCampo('cod_item') );

                $arElementos = $rsAtributos->getElementos();
                $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['atributos'] = (count($arElementos)>0) ? $arElementos : array();

            }

            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['quantidade_apurada'] = number_format( str_replace(",", ".", str_replace(".", "", $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['quantidade_apurada'] )) + str_replace(",", ".", str_replace(".", "", $rsItensInventario->getCampo('quantidade') ) ) , 4, ",", ".");
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldo'] = number_format( str_replace(",", ".", str_replace(".", "", $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldo'] )) + str_replace(",", ".", str_replace(".", "", $rsItensInventario->getCampo('saldo') ) ) , 4, ",", ".");

            if ($arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldo'] > 0) {
                $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['novo_item'] = false;
            } else {
                $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['novo_item'] = true;
            }

            $inIdCentro++;

            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['inIdCentro']         = $inIdCentro;
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['cod_centro']         = $rsItensInventario->getCampo('cod_centro');
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['descricao_centro']   = $rsItensInventario->getCampo('desc_centro_custo');
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['saldo']              = $rsItensInventario->getCampo('saldo');
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['quantidade_apurada'] = $rsItensInventario->getCampo('quantidade');
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['justificativa']      = $rsItensInventario->getCampo('justificativa');

            //carrega valores dos atributos
            $obTAtributoItemValor = new TAlmoxarifadoAtributoInventarioItemValor();
            $stFiltro  = " WHERE 1=1 ";
            $stFiltro .= " AND exercicio='".$arInventario['stExercicio']."'";
            $stFiltro .= " AND cod_almoxarifado=".$arInventario['inCodAlmoxarifado'];
            $stFiltro .= " AND cod_inventario=".$arInventario['inCodInventario'];
            $stFiltro .= " AND cod_item=".$rsItensInventario->getCampo('cod_item');
            $stFiltro .= " AND cod_marca=".$rsItensInventario->getCampo('cod_marca');
            $stFiltro .= " AND cod_centro=".  $rsItensInventario->getCampo('cod_centro');
            $stFiltro .= " AND cod_modulo=".  $obTAtributoItemValor->getDado('cod_modulo');
            $stFiltro .= " ORDER BY cod_cadastro, cod_atributo";
            $obTAtributoItemValor->recuperaTodos($rsAtributosValor,$stFiltro);

            while (!$rsAtributosValor->eof()) {
                $inCodAtributo = $rsAtributosValor->getCampo('cod_atributo');
                $inCodCadastro = $rsAtributosValor->getCampo('cod_cadastro');

                $inCodTipo = SistemaLegado::pegaDado('cod_tipo','administracao.atributo_dinamico'," where cod_atributo =".$rsAtributosValor->getCampo('cod_atributo'));
                if (trim($inCodTipo) == '4') {
                    $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['atributos_valores'][$inCodAtributo.'_'.$inCodCadastro."_Selecionados"] = explode(',',$rsAtributosValor->getCampo('valor'));

                } else {
                    $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['atributos_valores'][$inCodAtributo.'_'.$inCodCadastro] = $rsAtributosValor->getCampo('valor');
                }
                $rsAtributosValor->proximo();
            }

        }
        if ($_REQUEST['stAcao'] == "processar" or $_REQUEST['stAcao'] == "anular") {
            if ($boItemDifirenteAnterior) {

                $arInventario['itens_inventariados'][$inIdClassificacao][$inIdItem]['inIdItem']           = $inIdItem;
                $arInventario['itens_inventariados'][$inIdClassificacao][$inIdItem]['cod_estrutural']     = $rsItensInventario->getCampo('cod_estrutural');
                $arInventario['itens_inventariados'][$inIdClassificacao][$inIdItem]['cod_item']           = $rsItensInventario->getCampo('cod_item');
                $arInventario['itens_inventariados'][$inIdClassificacao][$inIdItem]['descricao_resumida'] = $rsItensInventario->getCampo('descricao_resumida');
                $arInventario['itens_inventariados'][$inIdClassificacao][$inIdItem]['descricao_item']     = $rsItensInventario->getCampo('descricao');
                $arInventario['itens_inventariados'][$inIdClassificacao][$inIdItem]['nom_unidade']        = $rsItensInventario->getCampo('nom_unidade');
                $arInventario['itens_inventariados'][$inIdClassificacao][$inIdItem]['cod_marca']          = $rsItensInventario->getCampo('cod_marca');
                $arInventario['itens_inventariados'][$inIdClassificacao][$inIdItem]['desc_marca']         = $rsItensInventario->getCampo('desc_marca');

            }

            $arInventario['itens_inventariados'][$inIdClassificacao][$inIdItem]['quantidade_apurada'] = number_format( str_replace(",", ".", str_replace(".", "", $arInventario['itens_inventariados'][$inIdClassificacao][$inIdItem]['quantidade_apurada'] )) + str_replace(",", ".", str_replace(".", "", $rsItensInventario->getCampo('quantidade') ) ) , 4, ",", ".");
            $arInventario['itens_inventariados'][$inIdClassificacao][$inIdItem]['saldo'] = number_format( str_replace(",", ".", str_replace(".", "", $arInventario['itens_inventariados'][$inIdClassificacao][$inIdItem]['saldo'] )) + str_replace(",", ".", str_replace(".", "", $rsItensInventario->getCampo('saldo') ) ) , 4, ",", ".");

        }

        $rsItensInventario->proximo();
    }

    Sessao::write('inventario',array());
    Sessao::write('inventario', $arInventario);

    if ($_REQUEST['stAcao'] == "incluir" or $_REQUEST['stAcao'] == "alterar") {
        $stJs .= montaSpnDadosClassificacao( $arInventario['inCodAlmoxarifado'], $arInventario['inCodCatalogoTxt'] );
    }
    $stJs .= montaSpnListaClassificacoesBloqueadas();
    if($_REQUEST['stAcao'] != "incluir" and $_REQUEST['stAcao'] != "alterar")
       $stJs .= montaSpnListaItensInventariados();

    return $stJs;

}

function montaClassificacaoFiltro()
{
    include_once ( CAM_GP_ALM_COMPONENTES."IMontaClassificacao.class.php" );
    $stHtml = "";
    if ($_REQUEST['inCodCatalogo']) {
        $obIMontaClassificacao = new IMontaClassificacao();
        $obIMontaClassificacao->setCodigoCatalogo( $_GET['inCodCatalogoTxt'] );

        $obFormulario = new Formulario;
        $obIMontaClassificacao->geraFormulario( $obFormulario );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }

    $stJs  = "document.getElementById('spnClassificacao').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function validaItem()
{
    $stErro = false;
    foreach ($_GET as $key=>$value) {
        if (strpos($key,'nuQuantidadeApurada_')!==false) {
            $nuQuantidade = str_replace(",", ".", str_replace(".", "", $value) );
            if ($nuQuantidade < 0) {
                $stErro = "A quantidade apurada deve ser maior que zero.";
                break;
            }
        }
    }
    if( !$stErro )
        $stErro = validaAtributos(false);
    if ($stErro) {
        return "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
    }
}

function validaAtributos($boImprime=true)
{
    $stErro = false;
    $arInventario = Sessao::read('inventario');
    $inIdClassificacao = $arInventario['classificacao_selecionada'];
    $inIdItem = $arInventario['item_selecionado'];

    $arElementos = $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['atributos'] ;

    for ($inCount=0; $inCount<count($arElementos); $inCount++) {
        $stObrigatorio = $arElementos[$inCount]['nao_nulo'];
        if ($stObrigatorio=='f') {
            $inCodAtributo = $arElementos[$inCount]['cod_atributo'];
            $inCodCadastro = $arElementos[$inCount]['cod_cadastro'];
            $stNomAtributo = $arElementos[$inCount]['nom_atributo'];

            if (!$boImprime) {
                $arCentros = $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'];
                for ($inIdCentro=0; $inIdCentro<count($arCentros); $inIdCentro++) {

                    $inCodTipo = SistemaLegado::pegaDado('cod_tipo','administracao.atributo_dinamico'," where cod_atributo =".$inCodAtributo);

                    if (trim($inCodTipo) == '4') {
                        if ( trim($arCentros[$inIdCentro]['atributos_valores'][$inCodAtributo.'_'.$inCodCadastro.'_Selecionados']) == '' ) {
                            $stErro = "Atributo $stNomAtributo obrigatório no centro de custo ".$arCentros[$inIdCentro]['cod_centro'];
                            break 2;
                        }
                    } else {
                        if ( trim($arCentros[$inIdCentro]['atributos_valores'][$inCodAtributo.'_'.$inCodCadastro]) == '' ) {
                            $stErro = "Atributo $stNomAtributo obrigatório no centro de custo ".$arCentros[$inIdCentro]['cod_centro'];
                            break 2;
                        }
                    }
                }
            } else {

                $inCodTipo = SistemaLegado::pegaDado('cod_tipo','administracao.atributo_dinamico'," where cod_atributo =".$inCodAtributo);

                if (trim($inCodTipo) == '4') {
                    if ( (trim($_REQUEST['Atributos_'.$inCodAtributo.'_'.$inCodCadastro.'_Selecionados']) == '')   ) {
                        $stErro = "Atributo $stNomAtributo obrigatório ";
                        break;
                    }
                } else {
                    if ( (trim($_REQUEST['Atributos_'.$inCodAtributo.'_'.$inCodCadastro]) == '')   ) {
                        $stErro = "Atributo $stNomAtributo obrigatório ";
                        break;
                    }
                }
            }
        }
    }
    if ($stErro) {
        if ($boImprime) {
            return "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
        } else {
            return $stErro;
        }
    }

}

function limparTela()
{
    //$stJs  = "document.getElementById('spnDadosClassificacao').innerHTML = '';\n";
    //$stJs .= "document.getElementById('spnDetalhesClassificacaoBloqueada').innerHTML = '';\n";
    //$stJs .= "document.getElementById('spnListaClassificacoesBloqueadas').innerHTML = '';\n";
    //$stJs .= "document.getElementById('stObservacao').value = '';\n";
    if ($_REQUEST['stAcao'] != 'incluir') {
        $stCompl  = "&stExercicio=".$_REQUEST['stExercicio'];
        $stCompl .= "&inCodInventario=".$_REQUEST['inCodInventario'];
        $stCompl .= "&inCodAlmoxarifado=".$_REQUEST['inCodAlmoxarifado'];
    }
    $stJs  = "window.location='FMManterInventario.php?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao']."$stCompl';";

    return $stJs;
}

function limparItem()
{
    $stJs  = "if( document.frm.inCodItem )  document.frm.inCodItem.value='';";
    $stJs .= "if( $('stNomItem') )          $('stNomItem').innerHTML='&nbsp;';";
    $stJs .= "if( $('spnInformacoesItem') ) $('spnInformacoesItem').innerHTML='';";
    $stJs .= "if( $('inCodMarca') )         $('inCodMarca').value='';";
    $stJs .= "if( $('stNomMarca') )         $('stNomMarca').innerHTML='&nbsp;';";
    $stJs .= "if( $('inCodCentroCusto') )   $('inCodCentroCusto').value='';";
    $stJs .= "if( $('stNomCentroCusto') )   $('stNomCentroCusto').innerHTML='&nbsp;';";

    return $stJs;
}

function limparDetalhesAlterarItem()
{
    $arInventario = Sessao::read('inventario');

    $stJs  = "document.getElementById('spnDetalhesItensClassificacao').innerHTML = '';\n";
    $arInventario['item_selecionado'] = '';

    Sessao::write('inventario',array());
    Sessao::write('inventario', $arInventario);

    return $stJs;
}

function limparDetalhesIncluirItem()
{
    $arInventario = Sessao::read('inventario');

    $stJs  = "document.getElementById('spnIncluirItem').innerHTML = '';\n";
    $arInventario['item_selecionado'] = '';

    Sessao::write('inventario',array());
    Sessao::write('inventario', $arInventario);

    return $stJs;
}

function limparDetalhesItem()
{
    $arInventario = Sessao::read('inventario');

    $stJs  = "document.getElementById('spnDetalhesItensClassificacao').innerHTML = '';\n";
    //diego - comentado
    //$stJs .= "document.getElementById('spnIncluirItem').innerHTML = '';\n";
    $stJs .= limparItem();
    $stJs .= "document.getElementById('spnSaldoCentroCusto').innerHTML = '';\n";
    $arInventario['item_selecionado'] = '';

    Sessao::write('inventario',array());
    Sessao::write('inventario', $arInventario);

    return $stJs;
}

function limparDetalhes()
{
    $arInventario = Sessao::read('inventario');

    $stJs .= "document.getElementById('spnDetalhesClassificacaoBloqueada').innerHTML = '';\n";
    $arInventario['classificacao_selecionada'] = '';
    $arInventario['item_selecionado'] = '';

    Sessao::write('inventario',array());
    Sessao::write('inventario', $arInventario);

    return $stJs;
}

function incluirItem()
{
    $arInventario = Sessao::read('inventario');
    include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php" );
    include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoClassificacao.class.php" );

    // verificar se os 3 campos foram informados
    $inCodItem        = $_GET['inCodItem'];
    $inCodMarca       = $_GET['inCodMarca'];
    $inCodCentroCusto = $_GET['inCodCentroCusto'];

    if ($inCodItem && $inCodMarca && $inCodCentroCusto) {

        $inIdClassificacaoSelecionada = $arInventario['classificacao_selecionada'];
        $inProxIdItem = count($arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens']);
        $stErro = '';
        $inIdItemIncluirCentro = '';
        $inProxIdCentroCusto = '';

        for ($inIdItem=0; $inIdItem<$inProxIdItem; $inIdItem++) {
            if ( ($inCodItem == $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inIdItem]['cod_item']) && ($inCodMarca == $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inIdItem]['cod_marca']) ) {
                // Já existe o item/marca na sessão
                $inIdItemIncluirCentro = $inIdItem;
                $inProxIdCentroCusto = count( $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inIdItem]['saldos_centro_custo'] );
                for ($inIdCentroCusto=0; $inIdCentroCusto<$inProxIdCentroCusto; $inIdCentroCusto++) {
                    if ($inCodCentroCusto == $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentroCusto]['cod_centro']) {
                        $stErro = 'Já existe um centro de custo para o item e a marca informados.';
                        break;
                    }
                }
                break;
            }
        }
    } else {
        if (!$inCodItem) {
            $stErro = "Campo Item inválido!($inCodItem).";
        } elseif (!$inCodMarca) {
            $stErro = "Campo Marca inválido!($inCodMarca).";
        } elseif (!$inCodCentroCusto) {
            $stErro = "Campo Centro de Custo inválido!($inCodCentroCusto).";
        }
    }

    if ($stErro == '') {
        // o === é para diferenciar 0 de ''
        if ($inIdItemIncluirCentro === '') {
            $obTCatalogoItem = new TAlmoxarifadoCatalogoItem();
            $obTCatalogoItem->setDado('cod_item', $inCodItem);
            $obTCatalogoItem->recuperaPorChave( $rsItem );

            $obTCatalogoClassificacao = new TAlmoxarifadoCatalogoClassificacao();
            $obTCatalogoClassificacao->setDado('cod_catalogo'     , $rsItem->getCampo('cod_catalogo'));
            $obTCatalogoClassificacao->setDado('cod_classificacao', $rsItem->getCampo('cod_classificacao'));
            $obTCatalogoClassificacao->recuperaPorChave( $rsClassificacao );

            $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['inIdItem']           = $inProxIdItem;
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['cod_estrutural']     = $rsClassificacao->getCampo('cod_estrutural');
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['cod_item']           = $rsItem->getCampo('cod_item');
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['descricao_resumida'] = $rsItem->getCampo('descricao_resumida');
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['descricao_item']     = $rsItem->getCampo('descricao');
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['nom_unidade']        = $_GET['stNomUnidade'];
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['cod_marca']          = $_GET['inCodMarca'];
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['desc_marca']         = $_GET['stNomMarca'];
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['saldo']              = '0,0000';
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['quantidade_apurada'] = '0,0000';
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['novo_item']          = true;

            $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['saldos_centro_custo'] = array();
        } else {
           $inProxIdItem = $inIdItemIncluirCentro;
        }

        if ($inProxIdCentroCusto === '') {
            $inProxIdCentroCusto = 0;
        }

        $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['saldos_centro_custo'][$inProxIdCentroCusto]['inIdCentro']         = $inProxIdCentroCusto;
        $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['saldos_centro_custo'][$inProxIdCentroCusto]['cod_centro']         = $_GET['inCodCentroCusto'];
        $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['saldos_centro_custo'][$inProxIdCentroCusto]['descricao_centro']   = $_GET['stNomCentroCusto'];
        $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['saldos_centro_custo'][$inProxIdCentroCusto]['saldo']              = '0,0000';
        $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['saldos_centro_custo'][$inProxIdCentroCusto]['quantidade_apurada'] = '0,0000';
        $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['itens'][$inProxIdItem]['saldos_centro_custo'][$inProxIdCentroCusto]['justificativa']      = '';

        $arInventario['item_selecionado'] = $inProxIdItem;

        Sessao::write('inventario',array());
        Sessao::write('inventario', $arInventario);

        //diego - comentado
        //$stJs .= montaSpnItensClassificacao();
        $stJs .= montaSpnItensClassificacaoSaldoCentroCusto( 'Incluir' );
        $stJs .= "document.getElementById('inCodCentroCusto').value = '';";
        $stJs .= "document.getElementById('stNomCentroCusto').innerHTML = '&nbsp;';";

    }

    $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";

    return $stJs;
}

function montaIncluirItem($inIdItem='')
{
    $arInventario = Sessao::read('inventario');

    include_once CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php";
    include_once CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php";
    include_once CAM_GP_ALM_COMPONENTES."IPopUpCentroCustoUsuario.class.php";

    $obForm = new Form;
    $obForm->setAction( $pgProc );
    $obForm->setTarget( "oculto" );

    $inIdClassificacaoSelecionada = $arInventario['classificacao_selecionada'];

    $obIPopUpItem = new IPopUpItem($obForm);
    $obIPopUpItem->setRetornaUnidade( true );
    $obIPopUpItem->setObrigatorio(false);
    $obIPopUpItem->setObrigatorioBarra(true);
    $obIPopUpItem->setCodClassificacao( $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['cod_classificacao'] );
    $obIPopUpItem->setCodEstruturalReduzido( $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoSelecionada]['cod_estrutural_reduzido'] );
    $obIPopUpItem->setCodCatalogo( $arInventario['inCodCatalogoTxt'] );
    $obIPopUpItem->setNomCampoUnidade('stUnidadeMedida');
    $obIPopUpItem->setServico(false);
    $obIPopUpItem->setFiltroBusca('SomenteComMovimentacao');
    $obIPopUpItem->setMsgComplementarSemSaldo('Efetue Implantação.');

    //Para mostrar a Unidade de Medida
    $obSpnInformacoesItem = new Span();
    $obSpnInformacoesItem->setId( 'spnInformacoesItem' );

    $obHdnUnidadeMedida = new Hidden;
    $obHdnUnidadeMedida->setName( "hdnUnidadeMedidaValida" );
    $obHdnUnidadeMedida->setId( "hdnUnidadeMedidaValida" );

    $obMarca = new IPopUpMarca($obForm);
    $obMarca->setTitle("Informe a marca do item.");
    $obMarca->obCampoCod->setId('inCodMarca');
    $obMarca->setNull (true);
    $obMarca->setObrigatorioBarra(true);

    $obCentroCusto = new IPopUpCentroCustoUsuario($obForm);
    $obCentroCusto->setNull(true);
    $obCentroCusto->obCampoCod->setId('inCodCentroCusto');
    $obCentroCusto->setObrigatorioBarra(true);

    $obBtnIncluirItem = new Button;
    $obBtnIncluirItem->setName ( "btnIncluirItem" );
    $obBtnIncluirItem->setValue( "Incluir" );
    $obBtnIncluirItem->setTipo ( "button" );
    $obBtnIncluirItem->obEvento->setOnClick ( "montaParametrosGET('incluirItem')" );

    $obBtnLimparIncluirItem = new Button;
    $obBtnLimparIncluirItem->setName ( "btnLimparIncluirItem" );
    $obBtnLimparIncluirItem->setValue( "Limpar" );
    $obBtnLimparIncluirItem->setTipo ( "button" );
    #$obBtnLimparIncluirItem->obEvento->setOnClick ( "executaFuncaoAjax('montaIncluirItem');" );
    $stJsLimpa = limparItem();
    $obBtnLimparIncluirItem->obEvento->setOnClick ( $stJsLimpa );

    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );
    $obFormulario->addTitulo( "Dados do Item" );
    $obFormulario->addComponente( $obIPopUpItem );
    $obFormulario->addSpan($obSpnInformacoesItem);
    $obFormulario->addHidden( $obHdnUnidadeMedida );
    $obFormulario->addComponente( $obMarca );
    $obFormulario->addComponente( $obCentroCusto );
    $obFormulario->defineBarra( array($obBtnIncluirItem, $obBtnLimparIncluirItem), "left", "<b>**Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" );
    $obFormulario->montaInnerHTML();
    $stHtml .= $obFormulario->getHTML();

    $stJs  = "document.getElementById('spnIncluirItem').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function alterarItem()
{
    $arInventario = Sessao::read('inventario');

    $inIdClassificacao = $arInventario['classificacao_selecionada'];
    $inIdItem = $arInventario['item_selecionado'];

    $nuQuantidadeApuradaTotalItem = 0;
    $inNumSaldosCentroCusto = count($arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo']);

    for ($i=0; $i<$inNumSaldosCentroCusto; $i++) {
        $i_mais_um = $i+1;
        $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$i]['quantidade_apurada'] = $_GET["nuQuantidadeApurada_$i_mais_um"];
        $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$i]['justificativa'] = $_GET["stJustificativa_$i_mais_um"];
        $nuQuantidadeApuradaTotalItem = $nuQuantidadeApuradaTotalItem + str_replace(",", ".", str_replace(".", "", $_GET["nuQuantidadeApurada_$i_mais_um"]) );

    }

    $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['quantidade_apurada'] = number_format( $nuQuantidadeApuradaTotalItem, 4, ",", ".");

    Sessao::write('inventario',array());
    Sessao::write('inventario', $arInventario);

    return $stJs;
}

function verificaHabilitaJustificativa()
{
    $arInventario = Sessao::read('inventario');

    $inIdClassificacao = $arInventario['classificacao_selecionada'];
    $inIdItem = $arInventario['item_selecionado'];

    $inNumCountCentro = count( $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'] );

    for ($inIdCentro=0; $inIdCentro<$inNumCountCentro; $inIdCentro++) {
        if ($arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['saldo'] != $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['quantidade_apurada']) {
            $inIdJustificativa = $inIdCentro+1;
            $stJs .= "document.frm.stJustificativa_".$inIdJustificativa.".readOnly = false;";
        }
    }

    return $stJs;
}

function habilitaJustificativa($inIdCentro, $valor)
{
    $arInventario = Sessao::read('inventario');

    $inIdClassificacao = $arInventario['classificacao_selecionada'];
    $inIdItem = $arInventario['item_selecionado'];
    $posInIdCentro = $inIdCentro + 1;

    if ($arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['saldo'] != $valor) {
        $stJs = "document.frm.stJustificativa_".$posInIdCentro.".readOnly = false;";
    } else {
        $stJs  = "document.frm.stJustificativa_".$posInIdCentro.".readOnly = true;";
        $stJs .= "document.frm.stJustificativa_".$posInIdCentro.".value = '';";
    }

    return $stJs;
}

function montaSpnItensClassificacaoSaldoCentroCusto($modo='Alterar')
{
    $arInventario = Sessao::read('inventario');

    $inIdClassificacaoBloqueada = $arInventario['classificacao_selecionada'];
    $inIdItem = $arInventario['item_selecionado'];

    $rsSaldoCentroCusto = new RecordSet();

    if($inIdClassificacaoBloqueada!=='' && $inIdItem!=='')
        $rsSaldoCentroCusto->preenche( $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens'][$inIdItem]['saldos_centro_custo'] );

    $pgOcul = "OCManterInventario.php";

    $table = new TableTree();
    //$table->setLineNumber(false);

    $table->setRecordset( $rsSaldoCentroCusto );

    //$table->addCondicionalTree("possui_atributos", "true");
    $table->setArquivo( $pgOcul );
    $table->setParametros( array( "inIdCentro") );
    $table->setComplementoParametros ( "stCtrl=detalharItem&stAcao=".$stAcao );

    $table->setSummary('Itens');

    $table->Head->addCabecalho( 'Centro de Custo'  , 40 );
    $table->Head->addCabecalho( 'Marca'  , 20 );
    $table->Head->addCabecalho( 'Saldo' , 15 );
    $table->Head->addCabecalho( 'Quantidade Apurada' , 15 );
    $table->Head->addCabecalho( 'Justificativa' , 27 );

    $table->Body->addCampo( '[cod_centro]-[descricao_centro]', 'E' );
    $table->Body->addCampo( '[desc_marca]', 'C' );
    $table->Body->addCampo( 'saldo', 'D' );

    $obTxtQuantidadeApurada = new TextBox;
    $obTxtQuantidadeApurada->setName      ( "nuQuantidadeApurada" );
    $obTxtQuantidadeApurada->setId        ( "nuQuantidadeApurada" );
    $obTxtQuantidadeApurada->setSize      ( 20        );
    $obTxtQuantidadeApurada->setMaxLength ( 23        );
    $obTxtQuantidadeApurada->setValue     ( "[quantidade_apurada]" );
    $obTxtQuantidadeApurada->setFloat( true );
    $obTxtQuantidadeApurada->setDecimais( 4 );
    $obTxtQuantidadeApurada->obEvento->setOnChange("habilitaJustificativa(this);");

    $obTxtJustificativa = new TextArea;
    $obTxtJustificativa->setName  ( "stJustificativa" );
    $obTxtJustificativa->setId    ( "stJustificativa" );
    $obTxtJustificativa->setValue ( "[justificativa]" );
    $obTxtJustificativa->setRows  ( 1 );
    $obTxtJustificativa->setMaxCaracteres( 150 );
    $obTxtJustificativa->setStyle( "width: 250px"  );
    $obTxtJustificativa->setReadOnly( true );

    $table->Body->addComponente( $obTxtQuantidadeApurada );
    $table->Body->addComponente( $obTxtJustificativa );

    $table->montaHTML(true);
    $stHtml = $table->getHtml();

/*

    $obLista = new Lista;
    $obLista->setTitulo( "Saldos do Item Por Centro de Custo" );
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsSaldoCentroCusto );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Centro de Custo" );
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Saldo" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Quantidade Apurada" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Justificativa" );
    $obLista->ultimoCabecalho->setWidth( 27 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_centro]-[descricao_centro]" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "saldo" );
    $obLista->ultimoDado->setAlinhamento( "DIREITA" );
    $obLista->commitDado();

    $obTxtQuantidadeApurada = new TextBox;
    $obTxtQuantidadeApurada->setName      ( "nuQuantidadeApurada_[inIdCentro]" );
    $obTxtQuantidadeApurada->setId        ( "nuQuantidadeApurada_[inIdCentro]" );
    $obTxtQuantidadeApurada->setSize      ( 20        );
    $obTxtQuantidadeApurada->setMaxLength ( 23        );
    $obTxtQuantidadeApurada->setValue     ( "quantidade_apurada" );
    $obTxtQuantidadeApurada->setFloat( true );
    $obTxtQuantidadeApurada->setDecimais( 4 );
    $obTxtQuantidadeApurada->obEvento->setOnChange("habilitaJustificativa(this);");

    $obTxtJustificativa = new TextArea;
    $obTxtJustificativa->setName  ( "stJustificativa_[inIdCentro]" );
    $obTxtJustificativa->setId    ( "stJustificativa_[inIdCentro]" );
    $obTxtJustificativa->setValue ( "justificativa" );
    $obTxtJustificativa->setRows  ( 1 );
    $obTxtJustificativa->setMaxCaracteres( 150 );
    $obTxtJustificativa->setStyle( "width: 250px"  );
    $obTxtJustificativa->setReadOnly( true );

    $obLista->addDadoComponente( $obTxtQuantidadeApurada );
    $obLista->ultimoDado->setCampo( "quantidade_apurada" );
    $obLista->commitDadoComponente();

    $obLista->addDadoComponente( $obTxtJustificativa );
    $obLista->ultimoDado->setCampo( "justificativa" );
    $obLista->commitDadoComponente();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace( "\n", "", $stHtml);
    $stHtml = str_replace( "  ", "", $stHtml);
    $stHtml = str_replace( "'" , "\\'", $stHtml);

*/

    $obBtnAlterarItem = new Button;
    $obBtnAlterarItem->setName ( "btnAlterarItem" );
    $obBtnAlterarItem->setValue( $modo );
    $obBtnAlterarItem->setTipo ( "button" );
    $obBtnAlterarItem->obEvento->setOnClick ( "montaParametrosGET('alterarItem')" );

    $obBtnLimparItem = new Button;
    $obBtnLimparItem->setName ( "btnLimparItem" );
    $obBtnLimparItem->setValue( "Limpar" );
    $obBtnLimparItem->setTipo ( "button" );
    $obBtnLimparItem->obEvento->setOnClick ( "executaFuncaoAjax( 'limparSaldos', '&modo=$modo' );" );

    $obFormulario = new Formulario;
    $obFormulario->defineBarra( array($obBtnAlterarItem, $obBtnLimparItem), "left", "<b>**Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" );
    $obFormulario->montaInnerHTML();
    $stHtml .= $obFormulario->getHTML();

    if( $rsSaldoCentroCusto->getNumLinhas()>0 )
        $stJs = "document.getElementById('spnSaldoCentroCusto').innerHTML = '".$stHtml."';\n";
    else
        $stJs = "document.getElementById('spnSaldoCentroCusto').innerHTML = '';\n";

    return $stJs;
}

function montaSpnDetalhesItemClassificacaoBloqueada($inIdItem)
{
    $arInventario = Sessao::read('inventario');

    $inIdClassificacaoBloqueada = $arInventario['classificacao_selecionada'];
    $arInventario['item_selecionado'] = $inIdItem;

    $stItem = $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens'][$inIdItem]['cod_item']."-".$arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens'][$inIdItem]['descricao_item'];

    include_once ( CAM_GP_ALM_COMPONENTES."IPopUpCentroCustoUsuario.class.php" );
    $obForm = new Form;
    $obForm->setAction( $pgProc );
    $obForm->setTarget( "oculto" );

    $obHdnCodItem = new Hidden;
    $obHdnCodItem->setName( "inCodItem" );
    $obHdnCodItem->setId  ( "inCodItem" );
    $obHdnCodItem->setValue( $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens'][$inIdItem]['cod_item'] );

    $obHdnCodMarca = new Hidden;
    $obHdnCodMarca->setName( "inCodMarca" );
    $obHdnCodMarca->setId  ( "inCodMarca" );
    $obHdnCodMarca->setValue( $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens'][$inIdItem]['cod_marca'] );

    $obHdnIdItem = new Hidden;
    $obHdnIdItem->setName( "inIdItem" );
    $obHdnIdItem->setId( "inIdItem" );
    $obHdnIdItem->setValue( $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens'][$inIdItem]['inIdItem'] );

    $obLabelItem = new Label();
    $obLabelItem->setName("stItem");
    $obLabelItem->setRotulo("Item");
    $obLabelItem->setValue( $stItem );

    $obLabelUnidadeMedida = new Label();
    $obLabelUnidadeMedida->setName("stUnidadeMedida");
    $obLabelUnidadeMedida->setRotulo("Unidade de Medida");
    $obLabelUnidadeMedida->setValue( $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens'][$inIdItem]['nom_unidade'] );

    $obLabelMarca = new Label();
    $obLabelMarca->setName("stMarca");
    $obLabelMarca->setRotulo("Marca");
    $obLabelMarca->setValue( $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens'][$inIdItem]['desc_marca'] );

    $obLabelSaldo = new Label();
    $obLabelSaldo->setName("stSaldo");
    $obLabelSaldo->setRotulo("Saldo");
    $obLabelSaldo->setValue( $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens'][$inIdItem]['saldo'] );

    $obCentroCusto = new IPopUpCentroCustoUsuario($obForm);
    $obCentroCusto->setNull(true);
    $obCentroCusto->obCampoCod->setId('inCodCentroCusto');
    $obCentroCusto->setObrigatorioBarra(true);

    $obBtnIncluirItem = new Button;
    $obBtnIncluirItem->setName ( "btnIncluirItem" );
    $obBtnIncluirItem->setValue( "Incluir" );
    $obBtnIncluirItem->setTipo ( "button" );
    $obBtnIncluirItem->obEvento->setOnClick ( "montaParametrosGET('incluirItem')" );

    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );
    $obFormulario->addTitulo("Dados do Item");
    $obFormulario->addComponente($obLabelItem);
    $obFormulario->addComponente($obLabelUnidadeMedida);
    $obFormulario->addComponente($obLabelMarca);
    $obFormulario->addComponente($obLabelSaldo);
    $obFormulario->addComponente($obCentroCusto);
    $obFormulario->addHidden($obHdnIdItem);
    $obFormulario->addHidden($obHdnCodItem);
    $obFormulario->addHidden($obHdnCodMarca);
    $obFormulario->defineBarra( array($obBtnIncluirItem), "left", "<b>**Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" );
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    $stJs .= "document.getElementById('spnDetalhesItensClassificacao').innerHTML = '".$stHtml."';\n";

    Sessao::write('inventario',array());
    Sessao::write('inventario', $arInventario);

    return $stJs;
}

function montaSpnDetalhesClassificacaoBloqueada($inIdClassificacao)
{
    $arInventario = Sessao::read('inventario');

    $arInventario['classificacao_selecionada'] = $inIdClassificacao;

    $stCodEstrutural = $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['cod_estrutural'];
    $stDescricaoClassificacao = $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['descricao'];
    $stClassificacaoBloqueada = $stCodEstrutural." - ".$stDescricaoClassificacao;

    $obLblClassificacaoBloqueada = new Label();
    $obLblClassificacaoBloqueada->setName("stClassificacaoBloqueada");
    $obLblClassificacaoBloqueada->setRotulo("Classificação");
    $obLblClassificacaoBloqueada->setValue($stClassificacaoBloqueada);

    $obSpnDetalhesItensClassificacao = new Span();
    $obSpnDetalhesItensClassificacao->setId ( "spnDetalhesItensClassificacao" );

    $obSpnIncluirItem = new Span();
    $obSpnIncluirItem->setId ( "spnIncluirItem" );

    $obSpnSaldoCentroCusto = new Span();
    $obSpnSaldoCentroCusto->setId ( "spnSaldoCentroCusto" );

    $obSpnItensClassificacao = new Span();
    $obSpnItensClassificacao->setId ( "spnItensClassificacao" );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Detalhes da Classificação Bloqueada para Inventário");
    $obFormulario->addComponente($obLblClassificacaoBloqueada);
    $obFormulario->addSpan( $obSpnDetalhesItensClassificacao );
    $obFormulario->addSpan( $obSpnIncluirItem );
    $obFormulario->addSpan($obSpnSaldoCentroCusto);
    $obFormulario->addSpan( $obSpnItensClassificacao );
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    $stJs  = "document.getElementById('spnDetalhesClassificacaoBloqueada').innerHTML = '".$stHtml."';\n";

    Sessao::write('inventario',array());
    Sessao::write('inventario', $arInventario);

    $stJs .= montaSpnItensClassificacao();

    return $stJs;
}

function excluirClassificacoes()
{
    $arInventario = Sessao::read('inventario');

    $inIdClassificacao = $arInventario['classificacao_selecionada'];

    $arNewArray = array();
    if( is_array($arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens']) )
    for ( $inCount=0; $inCount<=count($arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens']); $inCount++) {
        //Não mostra na interface itens com quantidade apurada menor ou igual a zero.
        if( str_replace(",", ".", str_replace(".", "", $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inCount]['quantidade_apurada'] ) )
            > 0
          )
            $arNewArray[] = $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inCount];
    }
    $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'] = $arNewArray;

    Sessao::write('inventario',array());
    Sessao::write('inventario', $arInventario);
}

function montaSpnItensClassificacao()
{
    excluirClassificacoes();

    $arInventario = Sessao::read('inventario');

    $inIdClassificacao = $arInventario['classificacao_selecionada'];

    $rsItensClassificacao = new RecordSet();
    if( is_array($arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens']) )
        $rsItensClassificacao->preenche($arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens']);

    $stHtml = montaSpnListaItens($rsItensClassificacao);

/*
    $obBtnIncluirItem = new Button;
    $obBtnIncluirItem->setName ( "btnIncluirItem" );
    $obBtnIncluirItem->setValue( "Incluir" );
    $obBtnIncluirItem->setTipo ( "button" );
    $obBtnIncluirItem->setRotulo( "Item" );
    $obBtnIncluirItem->obEvento->setOnClick ( "executaFuncaoAjax('montaIncluirItem')" );

    $obFormulario = new Formulario;
    $obFormulario->addComponente($obBtnIncluirItem);
    $obFormulario->montaInnerHTML();
    $stHtml .= $obFormulario->getHTML();
*/

    if( $rsItensClassificacao->getNumLinhas()>0 )
        $stJs = "document.getElementById('spnItensClassificacao').innerHTML = '".$stHtml."';\n";
    else
        $stJs = "document.getElementById('spnItensClassificacao').innerHTML = '';\n";

    return $stJs;
}

function montaSpnListaItensInventariados()
{
    $arInventario = Sessao::read('inventario');

    $novaChave = 0;
    foreach ($arInventario['itens_inventariados'] as $classificacao => $itens) {
        foreach ($itens as $chaveAntiga => $dados) {
            $arItensInventariados[$novaChave] = $dados;
            unset($arInventario[$chaveAntiga]);
            $novaChave = $novaChave + 1;
        }
    }

    $rsItensInventariados = new RecordSet();
    $rsItensInventariados->preenche($arItensInventariados);
    $stHtml = montaSpnListaItens($rsItensInventariados);
    $stJs = "document.getElementById('spnListaItensInventariados').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function montaSpnListaItens(&$rsItens)
{
    $table = new Table;
    $table->setRecordset( $rsItens );
     if ($_REQUEST['stAcao'] == "incluir" or $_REQUEST['stAcao'] == "alterar") {
        $table->setSummary( "Itens da Classificação Bloqueada para Inventário" );
    } else {
        $table->setSummary( "Itens Inventariados" );
    }
    //$table->setConditional( true , "#d0e4f2" );

    $table->Head->addCabecalho('&nbsp;'                 ,  3);
    $table->Head->addCabecalho('Classificação'          , 10);
    $table->Head->addCabecalho('Item'                   , 37);
    $table->Head->addCabecalho('Unidade de Medida'      , 10);
    $table->Head->addCabecalho('Marca'                  , 10);
    $table->Head->addCabecalho('centro de Custo'        , 10);
    $table->Head->addCabecalho('Saldo'                  , 10);
    $table->Head->addCabecalho('Unidade de Medida'      , 10);
    $table->Head->addCabecalho('Quantidade Apurada'     , 10);

    $stTitle = "[stTitle]";

    $table->Body->addCampo('cod_estrutural'                  , "E"          );
    $table->Body->addCampo('[cod_item]-[descricao_resumida]' , "E"          );
    $table->Body->addCampo('nom_unidade'                     , "C"          );
    $table->Body->addCampo('desc_marca'                      , "C"          );
    $table->Body->addCampo('cod_centro'                      , "C", $stTitle);
    $table->Body->addCampo('saldo'                           , "D"          );
    $table->Body->addCampo('quantidade_apurada'              , "D"          );
    $inIdItem = "inIdItem";

    if ($_REQUEST["stAcao"] == "alterar" or $_REQUEST["stAcao"] == "incluir") {
        $table->Body->addAcao( 'ALTERAR'   ,  'montaParametrosGET(alterarItem(%s), \'\', true)'      , array( 'inIdItem') );
        $table->Body->addAcao( 'EXCLUIR' ,  'excluirItem(%s)', array('inIdItem'));

    }
    $table->montaHTML();
    $stHtml = $table->getHTML();
    $stHtml = str_replace( "\n", "", $stHtml);
    $stHtml = str_replace( "  ", "", $stHtml);
    $stHtml = str_replace( "'" , "\\'", $stHtml);

    return $stHtml;

    // termina o novo

//    $obLista = new Lista;
//    if ($_REQUEST['stAcao'] == "incluir" or $_REQUEST['stAcao'] == "alterar") {
//        $obLista->setTitulo( "Itens da Classificação Bloqueada para Inventário" );
//    } else {
//        $obLista->setTitulo( "Itens Inventariados" );
//    }
//    $obLista->setMostraPaginacao( false );
//    $obLista->setRecordSet( $rsItens );
//
//    $obLista->addCabecalho();
//    $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
//    $obLista->ultimoCabecalho->setWidth( 3 );
//    $obLista->commitCabecalho();
//
//    $obLista->addCabecalho();
//    $obLista->ultimoCabecalho->addConteudo( "Classificação" );
//    $obLista->ultimoCabecalho->setWidth( 10 );
//    $obLista->commitCabecalho();
//
//    $obLista->addCabecalho();
//    $obLista->ultimoCabecalho->addConteudo( "Item" );
//    $obLista->ultimoCabecalho->setWidth( 37 );
//    $obLista->commitCabecalho();
//
//    $obLista->addCabecalho();
//    $obLista->ultimoCabecalho->addConteudo( "Unidade de Medida" );
//    $obLista->ultimoCabecalho->setWidth( 10 );
//    $obLista->commitCabecalho();
//
//    $obLista->addCabecalho();
//    $obLista->ultimoCabecalho->addConteudo( "Marca" );
//    $obLista->ultimoCabecalho->setWidth( 10 );
//    $obLista->commitCabecalho();
//
//    $obLista->addCabecalho();
//    $obLista->ultimoCabecalho->addConteudo( "Centro de Custo" );
//    $obLista->ultimoCabecalho->setWidth( 10 );
//    $obLista->commitCabecalho();
//
//    $obLista->addCabecalho();
//    $obLista->ultimoCabecalho->addConteudo( "Saldo" );
//    $obLista->ultimoCabecalho->setWidth( 10 );
//    $obLista->commitCabecalho();
//
//    $obLista->addCabecalho();
//    $obLista->ultimoCabecalho->addConteudo( "Quantidade Apurada" );
//    $obLista->ultimoCabecalho->setWidth( 10 );
//    $obLista->commitCabecalho();
//    if ($_REQUEST["stAcao"] == "alterar" or $_REQUEST["stAcao"] == "incluir") {
//        $obLista->addCabecalho();
//        $obLista->ultimoCabecalho->addConteudo( "Ação" );
//        $obLista->ultimoCabecalho->setWidth( 10 );
//        $obLista->commitCabecalho();
//    }
//
//    $obLista->addDado();
//    $obLista->ultimoDado->setCampo( "cod_estrutural" );
//    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
//    $obLista->commitDado();
//
//    $obLista->addDado();
//    $obLista->ultimoDado->setCampo( "[cod_item]-[descricao_resumida]" );
//    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
//    $obLista->commitDado();
//
//    $obLista->addDado();
//    $obLista->ultimoDado->setCampo( "nom_unidade" );
//    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
//    $obLista->commitDado();
//
//    $obLista->addDado();
//    $obLista->ultimoDado->setCampo( "desc_marca" );
//    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
//    $obLista->commitDado();
//
//    $obLista->addDado();
//    $obLista->ultimoDado->setCampo( "cod_centro" );
//    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
//    $obLista->commitDado();
//
//    $obLista->addDado();
//    $obLista->ultimoDado->setCampo( "saldo" );
//    $obLista->ultimoDado->setAlinhamento( "DIREITA" );
//    $obLista->commitDado();
//
//    $obLista->addDado();
//    $obLista->ultimoDado->setCampo( "quantidade_apurada" );
//    $obLista->ultimoDado->setAlinhamento( "DIREITA" );
//    $obLista->commitDado();
//    if ($_REQUEST["stAcao"] == "alterar" or $_REQUEST["stAcao"] == "incluir") {
//        $obLista->addAcao();
//        $obLista->ultimaAcao->setAcao( "ALTERAR" );
//        $obLista->ultimaAcao->setFuncao( true );
//        $obLista->ultimaAcao->setLink( "JavaScript:alterarItem();" );
//        $obLista->ultimaAcao->addCampo("0","inIdItem");
//        $obLista->commitAcao();
//
//        $obLista->addAcao();
//        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
//        $obLista->ultimaAcao->setFuncao( true );
//        $obLista->ultimaAcao->setLink( "JavaScript:excluirItem();" );
//        $obLista->ultimaAcao->addCampo("0","inIdItem");
//        $obLista->commitAcao();
//    }
//
//    $obLista->montaHTML();
//    $stHtml = $obLista->getHTML();
//    $stHtml = str_replace( "\n", "", $stHtml);
//    $stHtml = str_replace( "  ", "", $stHtml);
//    $stHtml = str_replace( "'" , "\\'", $stHtml);
//
//    return $stHtml;

}

function excluirItem($inIdItem)
{
    $arInventario = Sessao::read('inventario');

    $inIdClassificacaoBloqueada = $arInventario['classificacao_selecionada'];

    if ($arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens'][$inIdItem]['novo_item']) {
        $arNovo = array();
        $i = 0;
        for ( $inItem=0; $inItem<count($arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens']); $inItem++ ) {
            if ($arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens'][$inItem]['inIdItem'] != $inIdItem) {
                $arNovo[$i] = $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens'][$inItem];
                $arNovo[$i]['inIdItem'] = $i;
                $i++;
            } else {
                $arInventario['itens_excluidos'][] = $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens'][$inItem];
            }
        }
        $arInventario['classificacoes_bloqueadas'][$inIdClassificacaoBloqueada]['itens'] = $arNovo;

        Sessao::write('inventario',array());
        Sessao::write('inventario', $arInventario);

        $stJs = montaSpnItensClassificacao();
    } else {
        $stJs = "alertaAviso('Este item existe em estoque e não pode ser excluído. Você deve alterar as Quantidades Apuradas.','form','erro','".Sessao::getId()."');\n";
    }

    Sessao::write('inventario',array());
    Sessao::write('inventario', $arInventario);

    return $stJs;

}

function excluirClassificao($inIdExcluirClassificacao)
{
    $arInventario = Sessao::read('inventario');

    $arNovo = array();
    $i = 0;

    for ( $inItem=0; $inItem < count($arInventario['classificacoes_bloqueadas'][$inIdExcluirClassificacao]['itens']); $inItem++ ) {
        $arInventario['itens_excluidos'][] = $arInventario['classificacoes_bloqueadas'][$inIdExcluirClassificacao]['itens'][$inItem];
    }

    unset($arInventario['classificacoes_bloqueadas'][$inIdExcluirClassificacao]);

    Sessao::write('inventario', $arInventario);

    return $stJs;
}

function montaSpnListaClassificacoesBloqueadas()
{
    $arInventario = Sessao::read('inventario');

    $rsListaClassificacoesBloqueadas = new RecordSet();
    if( is_array($arInventario['classificacoes_bloqueadas']) )
        $rsListaClassificacoesBloqueadas->preenche($arInventario['classificacoes_bloqueadas']);

    $obLista = new Lista;
    $obLista->setTitulo( "Lista de Classificações Bloqueadas" );
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsListaClassificacoesBloqueadas );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Classificação" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->ultimoCabecalho->setWidth( 67 );
    $obLista->commitCabecalho();
    if ($_REQUEST['stAcao'] == "incluir" or $_REQUEST['stAcao'] == "alterar") {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Detalhar" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Ação" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
    }

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_estrutural" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "descricao" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();

    if ($_REQUEST['stAcao'] == "incluir" or $_REQUEST['stAcao'] == "alterar") {
        $obRdnClassificacaoBloqueada = new Radio();
        $obRdnClassificacaoBloqueada->setName( "inIdClassificacaoBloqueada" );
        $obRdnClassificacaoBloqueada->setId( "inIdClassificacaoBloqueada" );
        $obRdnClassificacaoBloqueada->setValue( "inIdClassificacao" );
        $obRdnClassificacaoBloqueada->setNull( false );
        $obRdnClassificacaoBloqueada->obEvento->setOnClick("montaParametrosGET( 'detalharClassificacao'  );");

        $obLista->addDadoComponente( $obRdnClassificacaoBloqueada, false );
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
        $obLista->commitDadoComponente();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirClassificacao');" );
        $obLista->ultimaAcao->addCampo("1","inIdClassificacao");
        $obLista->ultimaAcao->addCampo("2","stAcao=".$_REQUEST['stAcao']."&x");
        $obLista->commitAcao();
    }

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace( "\n", "", $stHtml);
    $stHtml = str_replace( "  ", "", $stHtml);
    $stHtml = str_replace( "'" , "\\'", $stHtml);

    $stJs = "document.getElementById('spnListaClassificacoesBloqueadas').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function incluirClassificacao()
{
    //global $sessao;

    include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAlmoxarifado.class.php" );
    include_once(CAM_GP_ALM_MAPEAMENTO ."TAlmoxarifadoCatalogo.class.php");
    include_once(CAM_GP_ALM_MAPEAMENTO ."TAlmoxarifadoCatalogoItem.class.php");
    include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoClassificacao.class.php" );
    include_once(CAM_GP_ALM_MAPEAMENTO ."TAlmoxarifadoInventarioItens.class.php");

    $stErro = "";

    if (!$_GET['stChaveClassificacao']) {
        $stErro = "Nenhuma classificação foi informada.";
    }

    $arInventario = Sessao::read('inventario');

    if ($stErro == "") {
        // Adiciona na sessão as informações sobre almoxarifado
        if (!$arInventario['inCodAlmoxarifado']) {
            $obTAlmoxarifado = new TAlmoxarifadoAlmoxarifado();
            $obTAlmoxarifado->recuperaAlmoxarifados( $rsAlmoxarifado, " and almoxarifado.cod_almoxarifado = ".$_GET['inCodAlmoxarifado']." " );
            $arInventario['inCodAlmoxarifado'] = $_GET['inCodAlmoxarifado'];
            $arInventario['stNomAlmoxarifado'] = $rsAlmoxarifado->getCampo('nom_cgm');
        }
        // Adiciona na sessão as informações sobre o catalogo
        //if (!$sessao->transf['inventario']['inCodCatalogoTxt']) {
        if (!$arInventario['inCodCatalogoTxt']) {
            $obCatalogo = new TAlmoxarifadoCatalogo();
            $obCatalogo->setDado( 'cod_catalogo', $_GET['inCodCatalogoTxt'] );
            $obCatalogo->recuperaPorChave( $rsCatalogo );
            $arInventario['inCodCatalogoTxt'] = $_GET['inCodCatalogoTxt'];
            $arInventario['stNomCatalogoTxt'] = $rsCatalogo->getCampo('descricao');
        }

        // Descobre qual o código do último nível de classificação informado
        for ($i = 1; $i < $_GET["inNumNiveisClassificacao"]; $i++) {
            if ($_GET["inCodClassificacao_$i"]) {
                $arCodClassificacao = explode('-', $_GET["inCodClassificacao_$i"] );
                $inCodClassificacao = $arCodClassificacao[1];
            }
        }

        $obTInventarioItens = new TAlmoxarifadoInventarioItens;
        $obTInventarioItens->setDado('cod_estrutural', $_GET['stChaveClassificacao'] );
        $obTInventarioItens->recuperaItensInventarioPorClassificacao( $rsInventarioItens );

        if ( $rsInventarioItens->getNumLinhas() > 0 ) {
            $stErro = "Uma classficação mãe está presente em outro inventário.";
        }

        // Recupera informações sobre a classificação
        $obTCatalogoClassificacao = new TAlmoxarifadoCatalogoClassificacao();
        $obTCatalogoClassificacao->setDado('cod_classificacao', $inCodClassificacao);
        $obTCatalogoClassificacao->setDado('cod_catalogo', $_GET["inCodCatalogo"]);
        $obTCatalogoClassificacao->recuperaPorChave( $rsClassificacoes );

        // Verifica se a classificação já foi incluída, ou alguma classificação mãe , ou alguma filha
        // Passa por todas as classificações incluídas
        $inCountClassificacao = count( $arInventario['classificacoes_bloqueadas'] );
        for ($inIdClassificacao=0; $inIdClassificacao<$inCountClassificacao; $inIdClassificacao++) {
            $arCodEstruturalExistente = explode( ".", $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['cod_estrutural'] );
            $arCodEstruturalNovo = explode( ".", $rsClassificacoes->getCampo("cod_estrutural") );
            // Passa por todos os níveis da classificação
            $inCountEstruturalExistente = count($arCodEstruturalExistente);
            for ($inNivelEstrutural=0; $inNivelEstrutural<$inCountEstruturalExistente; $inNivelEstrutural++) {
                if ($arCodEstruturalExistente[$inNivelEstrutural] != $arCodEstruturalNovo[$inNivelEstrutural]) {
                    if ( (int) $arCodEstruturalExistente[$inNivelEstrutural] == 0 ) {
                        $stErro = "Uma classficação mãe está presente neste inventário.";
                    } elseif ( (int) $arCodEstruturalNovo[$inNivelEstrutural] == 0 ) {
                        $stErro = "Uma classficação filho está presente neste inventário.";
                    }
                    break;
                } elseif ($inNivelEstrutural == $inCountEstruturalExistente - 1) {
                    $stErro = "Esta classificação já foi informada para este inventário.";
                }
            }
        }

        // Monta o código estrutural sem os zeros finais
        $arCodEstruturalNovo = explode( ".", $rsClassificacoes->getCampo("cod_estrutural") );
        for ($inNivelEstrutural=0; $inNivelEstrutural<count($arCodEstruturalNovo); $inNivelEstrutural++) {
            if ( (int) $arCodEstruturalNovo[$inNivelEstrutural] != 0 ) {
                $stCodEstruralSemZerosFinais .= ".".$arCodEstruturalNovo[$inNivelEstrutural];
            }
        }
        $stCodEstruralSemZerosFinais = substr( $stCodEstruralSemZerosFinais, 1);
    }
    if ($stErro == "") {
        // Adiciona a classificação na sessão
        $novaClassificacao = $inCountClassificacao;

        $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['inIdClassificacao'] = $novaClassificacao;
        $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['cod_classificacao'] = $rsClassificacoes->getCampo("cod_classificacao");
        $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['cod_catalogo']      = $rsClassificacoes->getCampo("cod_catalogo");
        $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['cod_estrutural']    = $rsClassificacoes->getCampo("cod_estrutural");
        $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['cod_estrutural_reduzido'] = $_GET['stChaveClassificacao'];
        $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['descricao']         = $rsClassificacoes->getCampo("descricao");

        $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'] = array();

        $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem();
        $obTAlmoxarifadoCatalogoItem->setDado('cod_almoxarifado', $arInventario['inCodAlmoxarifado'] );
        $obTAlmoxarifadoCatalogoItem->setDado('cod_estrutural', $stCodEstruralSemZerosFinais );
        $obTAlmoxarifadoCatalogoItem->setDado('cod_catalogo', $_REQUEST['inCodCatalogo']);
        $stFiltro = '';
        $stOrder = 'ORDER BY catalogo_classificacao.cod_estrutural, catalogo_item.descricao_resumida, marca.descricao';
        $obTAlmoxarifadoCatalogoItem->recuperaItensComSaldoPorAlmoxarifado( $rsItensClassificacao, $stFiltro, $stOrder );

        $rsItensClassificacao->addFormatacao( 'saldo', 'NUMERIC_BR_4' );

        // Adiciona os itens da classificação na sessão
        for ($item=0; $item<$rsItensClassificacao->getNumLinhas(); $item++) {
            $inCodItem = $rsItensClassificacao->getCampo('cod_item');

            $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['inIdItem']     = $item;
            $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['cod_estrutural']     = $rsItensClassificacao->getCampo('cod_estrutural');
            $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['cod_item']           = $inCodItem;
            $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['descricao_resumida'] = $rsItensClassificacao->getCampo('descricao_resumida');
            $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['descricao_item']     = $rsItensClassificacao->getCampo('descricao_item');
            $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['nom_unidade']        = $rsItensClassificacao->getCampo('nom_unidade');
            $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['cod_marca']          = $rsItensClassificacao->getCampo('cod_marca');
            $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['desc_marca']         = $rsItensClassificacao->getCampo('descricao_marca');
            $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['cod_centro']         = $rsItensClassificacao->getCampo('cod_centro');
            $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['saldo']              = $rsItensClassificacao->getCampo('saldo');
            $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['quantidade_apurada'] = $rsItensClassificacao->getCampo('saldo');
            $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['novo_item']          = false;
            $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['descricao_centro']   = $rsItensClassificacao->getCampo('descricao_centro');

            # Recupera as descrições do Centro de Custo, por produto .
            if ($rsItensClassificacao->getCampo('cod_centro')) {

                $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['stTitle']  = $rsItensClassificacao->getCampo('cod_centro').' - '.$rsItensClassificacao->getCampo('descricao_centro');
            } else {
                $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['stTitle'] = '&nbsp;';
            }
            // Adiciona o saldo por centro de custo para cada item da classificação
            $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem();
            $obTAlmoxarifadoCatalogoItem->setDado('cod_almoxarifado', $arInventario['inCodAlmoxarifado'] );
            $obTAlmoxarifadoCatalogoItem->setDado('cod_item', $inCodItem );
            $obTAlmoxarifadoCatalogoItem->setDado('cod_marca', $rsItensClassificacao->getCampo('cod_marca') );
            $obTAlmoxarifadoCatalogoItem->recuperaItensComSaldoPorCentroCustoInventario( $rsSaldoItemPorCentroCusto );

            $rsSaldoItemPorCentroCusto->addFormatacao( 'saldo', 'NUMERIC_BR_4' );
            $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['saldos_centro_custo'] = array();
            for ($CentroCusto=0; $CentroCusto<$rsSaldoItemPorCentroCusto->getNumLinhas(); $CentroCusto++) {

                $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['saldos_centro_custo'][$CentroCusto]['inIdCentro']         = $CentroCusto;
                $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['saldos_centro_custo'][$CentroCusto]['cod_centro']         = $rsSaldoItemPorCentroCusto->getCampo('cod_centro');
                $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['saldos_centro_custo'][$CentroCusto]['descricao_centro']   = $rsSaldoItemPorCentroCusto->getCampo('descricao_centro');
                $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['saldos_centro_custo'][$CentroCusto]['desc_marca']         = $rsSaldoItemPorCentroCusto->getCampo('desc_marca');
                $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['saldos_centro_custo'][$CentroCusto]['saldo']              = $rsSaldoItemPorCentroCusto->getCampo('saldo');
                $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['saldos_centro_custo'][$CentroCusto]['quantidade_apurada'] = $rsSaldoItemPorCentroCusto->getCampo('saldo');
                $arInventario['classificacoes_bloqueadas'][$novaClassificacao]['itens'][$item]['saldos_centro_custo'][$CentroCusto]['justificativa']      = '';

                if ( !$rsSaldoItemPorCentroCusto->eof() ) {
                    $rsSaldoItemPorCentroCusto->proximo();
                }
            }

            if ( !$rsItensClassificacao->eof() ) {
                $rsItensClassificacao->proximo();
            }
        }

        Sessao::write('inventario',array());
        Sessao::write('inventario', $arInventario);

        $stJs .= montaSpnListaClassificacoesBloqueadas();
        $stJs .= "document.getElementById('spnDetalhesClassificacaoBloqueada').innerHTML = '';\n";
        $stJs .= montaSpnAlmoxarifadoCatalogo();
    } else {

        Sessao::write('inventario',array());
        Sessao::write('inventario', $arInventario);

        $stJs = "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;

}

function montaSpnDadosClassificacao($inCodAlmoxarifado, $inCodCatalogo)
{
    include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php" );
    include_once ( CAM_GP_ALM_COMPONENTES."IMontaClassificacao.class.php" );

    // $_GET['inCodAlmoxarifado']
    // $_GET['inCodCatalogo']

    if ($inCodAlmoxarifado && $inCodCatalogo) {
        $obIMontaClassificacao = new IMontaClassificacao();
        $obIMontaClassificacao->setCodigoCatalogo( $inCodCatalogo );

        $obBtnIncluirClassificacao = new Button;
        $obBtnIncluirClassificacao->setName ( "btnIncluirClassicacao" );
        $obBtnIncluirClassificacao->setValue( "Incluir" );
        $obBtnIncluirClassificacao->setTipo ( "button" );
        $obBtnIncluirClassificacao->obEvento->setOnClick ( "montaParametrosGET('incluirClassificacao')" );

        $obBtnLimparClassificacao = new Button;
        $obBtnLimparClassificacao->setName ( "btnLimparClassicacao" );
        $obBtnLimparClassificacao->setValue( "Limpar" );
        $obBtnLimparClassificacao->setTipo ( "button" );
        $obBtnLimparClassificacao->obEvento->setOnClick ( "montaParametrosGET('montaSpnDadosClassificacao', 'inCodAlmoxarifado, inCodCatalogo');" );

        $obFormulario = new Formulario;
        $obFormulario->addTitulo("Dados da Classificação");
        $obIMontaClassificacao->geraFormulario( $obFormulario );
        $obFormulario->defineBarra( array($obBtnIncluirClassificacao, $obBtnLimparClassificacao), "left", "<b>**Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    } else {
        $stHtml = "";
    }

    $stJs  = "document.getElementById('spnDadosClassificacao').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function montaSpnAlmoxarifadoCatalogo()
{
    //global $sessao;
    $arInventario = Sessao::read('inventario');

    include_once( CAM_GP_ALM_COMPONENTES."ISelectAlmoxarifadoAlmoxarife.class.php" );
    include_once (CAM_GP_ALM_COMPONENTES."ITextBoxSelectCatalogo.class.php" );

    $jsMontaSpanDadosClassificacao = "montaParametrosGET('montaSpnDadosClassificacao', 'inCodAlmoxarifado, inCodCatalogo');";

    if ($arInventario['inCodAlmoxarifado']) {
        $obLblAlmoxarifado = new Label();
        $obLblAlmoxarifado->setName('stAlmoxarifado');
        $obLblAlmoxarifado->setRotulo('Almoxarifado');
        $obLblAlmoxarifado->setValue( $arInventario['inCodAlmoxarifado']." - ".$arInventario['stNomAlmoxarifado'] );

        $obHdnAlmoxarifado = new Hidden;
        $obHdnAlmoxarifado->setName( "inCodAlmoxarifado" );
        $obHdnAlmoxarifado->setId( "inCodAlmoxarifado" );
        $obHdnAlmoxarifado->setValue( $arInventario['inCodAlmoxarifado'] );
    } else {
        $obSelectAlmoxarifado = new ISelectAlmoxarifadoAlmoxarife();
        $obSelectAlmoxarifado->obEvento->setOnChange($jsMontaSpanDadosClassificacao);
        $obSelectAlmoxarifado->setObrigatorio(true);
    }

    if ($arInventario['inCodCatalogoTxt']) {
        $obLblCatalogo = new Label();
        $obLblCatalogo->setName('inCodCatalogoTxt');
        $obLblCatalogo->setRotulo('Catálogo');
        $obLblCatalogo->setValue( $arInventario['inCodCatalogoTxt']." - ".$arInventario['stNomCatalogoTxt'] );

        $obHdnCatalogo = new Hidden;
        $obHdnCatalogo->setName( "inCodCatalogo" );
        $obHdnCatalogo->setId( "inCodCatalogo" );
        $obHdnCatalogo->setValue( $arInventario['inCodCatalogoTxt'] );
    } else {
        $obSelectCatalogo = new ITextBoxSelectCatalogo();
        $obSelectCatalogo->setNaoPermiteManutencao(true);
        $obSelectCatalogo->obTextBox->obEvento->setOnChange($jsMontaSpanDadosClassificacao);
        $obSelectCatalogo->obSelect->obEvento->setOnChange($jsMontaSpanDadosClassificacao);
        $obSelectCatalogo->setObrigatorio(true);
    }

    $obFormulario = new Formulario;
    if ($arInventario['inCodAlmoxarifado']) {
        $obFormulario->addComponente( $obLblAlmoxarifado );
        $obFormulario->addHidden( $obHdnAlmoxarifado );
    } else {
        $obFormulario->addComponente( $obSelectAlmoxarifado );
    }
    if ($arInventario['inCodCatalogoTxt']) {
        $obFormulario->addComponente( $obLblCatalogo );
        $obFormulario->addHidden( $obHdnCatalogo );
    } else {
        $obFormulario->addComponente( $obSelectCatalogo );
    }
    if ($_REQUEST['stAcao'] == 'incluir' or $_REQUEST['stAcao'] == 'alterar') {
       $obTxtAreaObservacao = new TextArea();
       $obTxtAreaObservacao->setName('stObservacao');
       $obTxtAreaObservacao->setId('stObservacao');
       $obTxtAreaObservacao->setRotulo('Observação');
       $obTxtAreaObservacao->setTitle('Informe a observação.');

       $obFormulario->addComponente ( $obTxtAreaObservacao );
    } else {
       $obLblObservacao = new Label;
       $obLblObservacao->setName ('stObservacao');
       $obLblObservacao->setId('stObservacao');
       $obLblObservacao->setRotulo("Observação");
       $obLblObservacao->setValue($arInventario['stObservacao']);

       $obFormulario->addComponente ( $obLblObservacao );
    }
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    $stJs  = "document.getElementById('spnAlmoxarifadoCatalogo').innerHTML = '".$stHtml."';\n";

    if ($_REQUEST['stAcao'] == 'incluir' or $_REQUEST['stAcao'] == 'alterar') {
       $stObservacao = str_replace( "\r\n", "\\n", $arInventario['stObservacao']);
       $stJs .= "document.getElementById('stObservacao').value = '". $stObservacao."';\n";
    }

    return $stJs;
}

function operacoesIniciais()
{
    include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoInventarioItens.class.php" );

    if ($_GET['stAcao'] != 'incluir') {
        $stJs .= carregaInventario();
    }

    $stJs .= montaSpnAlmoxarifadoCatalogo();

    if ($_GET['stAcao'] == 'incluir') {
        $stJs .= "document.getElementById('inCodCatalogoTxt').focus();";
    }

    return $stJs;
}

function carregaAtributos()
{
    include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoCatalogoItem.class.php");
    $arInventario = Sessao::read('inventario');

    $inIdClassificacao = $arInventario['classificacao_selecionada'];
    $inIdItem = $arInventario['item_selecionado'];

    $inCodItem = (int) $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_item'];

    if (is_int($inCodItem) && $inCodItem >= 0) {
        $obTAtributoCatalogoItem = new TAlmoxarifadoAtributoCatalogoItem();
        $obTAtributoCatalogoItem->recuperaAtributoDinamicoItem( $rsAtributos, ' AND cod_item = '.$inCodItem );

        if ( $rsAtributos->getNumLinhas() > 0 ) {
            $arElementos = $rsAtributos->getElementos();
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['atributos'] = $arElementos;
        } else {
            $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['atributos'] = array();
        }

        Sessao::write('inventario',array());
        Sessao::write('inventario', $arInventario);
    }
}

function montaAtributosItem()
{
    $arInventario = Sessao::read('inventario');

    $inIdClassificacao = $arInventario['classificacao_selecionada'];
    $inIdItem = $arInventario['item_selecionado'];
    $inIdCentro = $_GET['inIdCentro'];

    $inCodItem  = $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['cod_item'];
    $inCodCentro= $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['cod_centro'];
    $arElementos= $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['atributos'];

    if ( count($arElementos) > 0 ) {
        $rsAtributosValores = new RecordSet;

        for ($inCount=0; $inCount<count($arElementos); $inCount++) {

            $inCodAtributo = $arElementos[$inCount]['cod_atributo'];
            $inCodCadastro = $arElementos[$inCount]['cod_cadastro'];

            $arAuxAtributos = $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['atributos_valores'][$inCodAtributo.'_'.$inCodCadastro.'_Selecionados'];

            if (is_array($arAuxAtributos)) {
                if (!empty($arAuxAtributos[0])) {
                    $arValor = $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['atributos_valores'][$inCodAtributo.'_'.$inCodCadastro.'_Selecionados'];
                    foreach ($arValor as $keyValor => $ValorDados) {
                        $arElementos[$inCount]['valor'] .= $ValorDados;

                        $stDescricao = SistemaLegado::pegaDado('valor_padrao','administracao.atributo_valor_padrao'," where cod_valor IN (".$ValorDados.") and cod_atributo = ".$inCodAtributo);
                        $arElementos[$inCount]['valor_desc'] .= $stDescricao;

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
                        if (($keyValor < count($arValor)-1)) {
                            $arElementos[$inCount]['valor'] .= ",";
                            $arElementos[$inCount]['valor_desc'] .= "[][][]";
                        }
                    }
                }
            } elseif ($arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['atributos_valores'][$inCodAtributo.'_'.$inCodCadastro]) {
                $arElementos[$inCount]['valor'] = $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['atributos_valores'][$inCodAtributo.'_'.$inCodCadastro];
            }
        }

        $rsAtributosValores->preenche($arElementos);

        $obHdnTipoAtributos = new Hidden;
        $obHdnTipoAtributos->setName     ( "hdnTipoAtributos" );

        $obFormulario = new Formulario;
        $obFormulario->addHidden      ( $obHdnTipoAtributos    );

        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "Atributos_" );
        $obMontaAtributos->setRecordSet  ( $rsAtributosValores );
        $obMontaAtributos->recuperaValores();

        $obMontaAtributos->geraFormulario ( $obFormulario );

        $obBtnSalvar = new Button;
        $obBtnSalvar->setName  ( "Salvar" );
        $obBtnSalvar->setValue ( "Salvar" );
        $obBtnSalvar->obEvento->setOnClick("montaParametrosGET('salvarAtributo&inIdCentro=$inIdCentro&linha_table_tree=".$_REQUEST['linha_table_tree']."', '');");

        $obFormulario->defineBarra( array($obBtnSalvar), "left", "<b>*Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" );

        $obFormulario->montaHTML();
        $stHTML = $obFormulario->getHTML();

        echo $stHTML;
    }
}

function salvarAtributo()
{
    $arInventario = Sessao::read('inventario');

    $inIdClassificacao = $arInventario['classificacao_selecionada'];
    $inIdItem = $arInventario['item_selecionado'];
    $inIdCentro = $_GET['inIdCentro'];

    unset($arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['atributos_valores']);

    foreach ($_REQUEST as $key=>$value) {
        if (strpos($key,'Atributos_')!==false) {
            $stAtrVal = substr($key,strpos($key,'_')+1,strlen($key));
            if (isset($_REQUEST['Atributos_'.$stAtrVal]) || $_REQUEST[$stAtrVal]) {
                $arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens'][$inIdItem]['saldos_centro_custo'][$inIdCentro]['atributos_valores'][ $stAtrVal ] = $_REQUEST['Atributos_'.$stAtrVal];
            }
        }
    }

    $stJs = "TableTreeLineControl( '".$_REQUEST['linha_table_tree']."' , 'none', '', 'none');";

    Sessao::write('inventario', array());
    Sessao::write('inventario', $arInventario);

    return $stJs;
}

switch ($stCtrl) {
    case "limpartela":
        $js  = limparTela();
    break;
    case "operacoesIniciais":
        $js .= operacoesIniciais();

        // Caso a ação for para processar o Inventário, libera o Botão OK após o término da listagem de itens.
        if ($_REQUEST['stAcao'] == "processar")
            $js .= "jQuery('#Ok').removeAttr('disabled'); \n";
    break;
    case "montaSpnDadosClassificacao":
        $js = montaSpnDadosClassificacao( $_GET['inCodAlmoxarifado'], $_GET['inCodCatalogo'] );
    break;
    case "incluirClassificacao":
        $js  = incluirClassificacao();
    break;
    case "detalharItem":
        $js  = montaAtributosItem();
    break;
    case "salvarAtributo":
        $stValida = validaAtributos();
        if ($stValida) {
            $js = $stValida;
        } else {
            $js  = salvarAtributo();
        }
    break;
    case "detalharClassificacao":
        $js = montaSpnDetalhesClassificacaoBloqueada($_GET["inIdClassificacaoBloqueada"]);

        //diego - novo
        $js .= limparDetalhesAlterarItem();
        $js .= montaIncluirItem();
        $js .= montaSpnItensClassificacaoSaldoCentroCusto( 'Incluir' );
    break;
    case "excluirClassificacao":
        $js  = limparDetalhes();
        $js .= excluirClassificao($_GET["inIdClassificacao"]);
        $js .= montaSpnListaClassificacoesBloqueadas();
    break;
    case "montaAlterarItem":
        $js  = limparDetalhesIncluirItem();
        $js .= montaSpnDetalhesItemClassificacaoBloqueada( $_GET["inIdItem"] );
        //diego - novo
        ##$js .= montaIncluirItem( $_GET["inIdItem"] );

        $js .= montaSpnItensClassificacaoSaldoCentroCusto( 'Alterar' );
        $js .= verificaHabilitaJustificativa();
    break;
    case "excluirItem":
        $js  = limparDetalhesItem();
        $js .= excluirItem( $_GET["inIdItem"] );
    break;
    case "alterarItem":
        $stValida = validaItem();

        if (!empty($stValida)) {
            $js = $stValida;

        } else {
            $js  = alterarItem();
            $js .= limparDetalhesItem();
            $js .= montaIncluirItem();
            $arInventario = Sessao::read('inventario');

            $inIdClassificacao = $arInventario['classificacao_selecionada'];

            $rsItensClassificacao = new RecordSet();
            if( is_array($arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens']) )
                $rsItensClassificacao->preenche($arInventario['classificacoes_bloqueadas'][$inIdClassificacao]['itens']);

            $stHtml = montaSpnListaItens($rsItensClassificacao);

            if( $rsItensClassificacao->getNumLinhas()>0 )
                $js .= "document.getElementById('spnItensClassificacao').innerHTML = '".$stHtml."';\n";
            else
                $js .= "document.getElementById('spnItensClassificacao').innerHTML = '';\n";
        }

    break;
    case "habilitaJustificativa":
        $js = habilitaJustificativa( $_GET['inIdCentro'], $_GET['valor_apurado'] );
    break;
    case "montaIncluirItem":
        $js  = limparDetalhesAlterarItem();
        $js .= montaIncluirItem();
        $js .= montaSpnItensClassificacaoSaldoCentroCusto( 'Incluir' );
    break;
    case "incluirItem":
        $js = incluirItem();
        carregaAtributos();
    break;
    case "limparSaldos":
        $js = montaSpnItensClassificacaoSaldoCentroCusto( $_GET['modo'] );
    break;
    case "montaClassificacaoFiltro":
        $js = montaClassificacaoFiltro();
    break;
}

if ($js) {
    echo $js;
}

?>
