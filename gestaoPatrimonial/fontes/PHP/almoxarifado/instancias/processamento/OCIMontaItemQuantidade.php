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
    * Oculto de Processamento do Componente IMontaItemQuantidade
    * Data de Criação: 04/07/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * Casos de uso: uc-03.03.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once (  CAM_GP_ALM_COMPONENTES."IMontaItemQuantidade.class.php" );
include_once (  CAM_GP_ALM_COMPONENTES."ISelectAlmoxarifado.class.php"  );
include_once (  CAM_GP_ALM_COMPONENTES."IMontaItemUnidade.class.php"    );
include_once (  CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php"   );

if (isset($_REQUEST['inCodAlmoxarifadoOrigem'])) {
    $_REQUEST['inCodAlmoxarifado'] =  $_REQUEST['inCodAlmoxarifadoOrigem'];
}

$pgOc = CAM_GP_ALM_PROCESSAMENTO.'OCIMontaItemQuantidade.php?'.Sessao::getId();

$obIMontaItemQuantidade = Sessao::read("obIMontaItemQuantidade");
$obIMontaItemQuantidadeValoresAtributo = Sessao::read('obIMontaItemQuantidadeValoresAtributo');

$stNomeAlmoxarifado = $obIMontaItemQuantidade->obISelectAlmoxarifado->getName();
$stNomeItem = $obIMontaItemQuantidade->obIMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->getName();
$stNomeMarca = $obIMontaItemQuantidade->obCmbMarca->getName();
$stNomeCentroCusto = $obIMontaItemQuantidade->obCmbCentroCusto->getName();
$stNomeSaldo = $obIMontaItemQuantidade->obLblSaldo->getId();

function montaHtmlListaAtributos()
{
    $pgOc = CAM_GP_ALM_PROCESSAMENTO.'OCIMontaItemQuantidade.php?'.Sessao::getId();
    $obIMontaItemQuantidade = Sessao::read("obIMontaItemQuantidade");

    $obIMontaItemQuantidadeValoresAtributo = Sessao::read('obIMontaItemQuantidadeValoresAtributo');

    foreach ($obIMontaItemQuantidadeValoresAtributo as $chave =>$dados) {
        foreach ($dados['atributo'] as $chave => $arAtributos) {
            $inCodTipo = SistemaLegado::pegaDado('cod_tipo','administracao.atributo_dinamico'," where cod_atributo =".$arAtributos['cod_atributo']);
            $arFlagTipo[$chave] = $inCodTipo;
        }
    }

    foreach ($obIMontaItemQuantidadeValoresAtributo as $chave =>$dados) {
        $dadosValoresAtributosSeparados = explode(' - ',$dados['stValoresAtributos']);
        $dadosNomeAtributosSeparados = explode(' - ',$dados['NomeAtributos']);

        foreach ($dadosValoresAtributosSeparados as $chaveAtributo =>$dadosAtributo) {

            if ($valorAtributo != '') {
                $valorAtributo .= ' , ';
            }

            if ($arFlagTipo[$chaveAtributo]==4) {

                $arValorAtributoSeparado = explode(",",trim($dadosAtributo));

                foreach ($arValorAtributoSeparado as $chaveSeparado => $dadosSeparados) {
                    if ($dadosSeparados != '') {
                        $stDescricaoValores[] = SistemaLegado::pegaDado('valor_padrao','administracao.atributo_valor_padrao'," where cod_valor IN (".$dadosSeparados.") and cod_atributo = ".$dados['atributo'][$chaveAtributo]['cod_atributo']);
                    }
                }
                if ($stDescricaoValores) {
                    $stDescricao = implode(', ',$stDescricaoValores);
                    $dadosAtributo = $stDescricao;
                }
                $stDescricaoValores = '';

            } elseif ($arFlagTipo[$chaveAtributo]==3) {
                $stDescricao = SistemaLegado::pegaDado('valor_padrao','administracao.atributo_valor_padrao'," where cod_valor =".$dadosAtributo." and cod_atributo = ".$dados['atributo'][$chaveAtributo]['cod_atributo']);
                $dadosAtributo = $stDescricao;
                $stDescricao = '';
            }

            if ($dadosAtributo) {
                $valorAtributo .= "<b>".$dados['atributo'][$chaveAtributo]['nom_atributo'].'</b> : '.$dadosAtributo;
            }

        }

        $obIMontaItemQuantidadeValoresAtributo[$chave]['stValoresAtributos'] = $valorAtributo;
        $obIMontaItemQuantidadeValoresAtributo[$chave]['quantidade'] = $dados['quantidade'];
        $obIMontaItemQuantidadeValoresAtributo[$chave]['saldo_atributo'] = $dados['saldo_atributo'];
        unset($valorAtributo);
    }

    $rsAtributos = new RecordSet;
    $rsAtributos->preenche( $obIMontaItemQuantidadeValoresAtributo );

    $obLista = new Lista;
    $obLista->setTitulo( "Lista de Atributos de Entrada" );
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsAtributos );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Seq" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Atributos Dinâmicos' );
    $obLista->ultimoCabecalho->setWidth( 45 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Saldo" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Quantidade" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Ação" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "stValoresAtributos" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "saldo_atributo" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "quantidade" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:ajaxJavaScript('".$pgOc."','excluirQuantidadeAtributo');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtmlLista = $obLista->getHTML();
    $stHtmlLista = str_replace( "\n", "", $stHtmlLista);
    $stHtmlLista = str_replace( "  ", "", $stHtmlLista);
    $stHtmlLista = str_replace( "'" , "\\'", $stHtmlLista);

    return $stHtmlLista;
}

function montaListaAtributos()
{
    $stHtmlLista = montaHtmlListaAtributos();
    $js = "document.getElementById('spnListaAtributos').innerHTML = '".$stHtmlLista."';";

    return $js;
}

function mostraAtributos($boNaoReseta = true)
{
    global $stNomeItem;
    global $pgOc;
    global $obIMontaItemQuantidade;
    global $stNomeAlmoxarifado;
    include_once ( TALM."TAlmoxarifadoAtributoCatalogoItem.class.php" );

    if ($boNaoReseta) {
        $obIMontaItemQuantidadeValoresAtributo = array();
        Sessao::write('obIMontaItemQuantidadeValoresAtributo', $obIMontaItemQuantidadeValoresAtributo);
    }

    $obTAlmoxarifadoAtributoCatalogoItem = new TAlmoxarifadoAtributoCatalogoItem;
    $obTAlmoxarifadoAtributoCatalogoItem->setDado( 'cod_item'  , $_REQUEST['inCodItem' ]);
    $obTAlmoxarifadoAtributoCatalogoItem->setDado( 'cod_marca' , $_REQUEST['inCodMarca']);
    $obTAlmoxarifadoAtributoCatalogoItem->setDado( 'cod_centro', $_REQUEST['inCodCentroCusto']);
    $obTAlmoxarifadoAtributoCatalogoItem->recuperaAtributoValoresCatalogoItem( $rsAtributos );

    $rsAtributos->ordena('nom_atributo', 'ASC', SORT_STRING);
    $arAtributos = $rsAtributos->arElementos;

    $i = 0;
    foreach ($arAtributos as $chave => $dados) {
        $arConjuntoAtributos[$dados['cod_atributo']]['valor'][$i] = $dados['valor'];
        $arConjuntoAtributos[$dados['cod_atributo']]['nom_atributo'] = $dados['nom_atributo'];
        $arConjuntoAtributos[$dados['cod_atributo']]['cod_atributo'] = $dados['cod_atributo'];
        $arConjuntoAtributos[$dados['cod_atributo']]['cod_cadastro'] = $dados['cod_cadastro'];
        $arConjuntoAtributos[$dados['cod_atributo']]['cod_modulo'] = $dados['cod_modulo'];
        $arConjuntoAtributos[$dados['cod_atributo']]['cod_tipo'] = $dados['cod_tipo'];
        $arConjuntoAtributos[$dados['cod_atributo']]['nao_nulo'] = $dados['nao_nulo'];
        $arConjuntoAtributos[$dados['cod_atributo']]['mascara'] = $dados['mascara'];
        $arConjuntoAtributos[$dados['cod_atributo']]['ajuda'] = $dados['ajuda'];
        $arConjuntoAtributos[$dados['cod_atributo']]['ativo'] = $dados['ativo'];
        $arConjuntoAtributos[$dados['cod_atributo']]['interno'] = $dados['interno'];
        $arConjuntoAtributos[$dados['cod_atributo']]['indexavel'] = $dados['indexavel'];
        $i++;
    }

    Sessao::write('arConjuntoAtributos', $arConjuntoAtributos);

    if (is_array($arConjuntoAtributos) == true ) {
        foreach ($arConjuntoAtributos as $chave => $dadosAtributos) {
            $arAtributosFormatados[] = $dadosAtributos;
        }
    }

    if ( count($arAtributosFormatados) > 0 ) {

        $obFormulario = new Formulario;
        $obFormulario->addTitulo('Atributos de Entrada');

        $i = 0;
        $numAtributos = count( $arAtributosFormatados );
        $stAtributosPai = "";

        for ($i=0; $i<=$numAtributos-1 ; $i++) {
            $atributo = $arAtributosFormatados[$i];

            $proximo_atributo = $arAtributosFormatados[$i+1];

            $_REQUEST['stAtributo'.$atributo['nom_atributo']] = "";

            //Lista dos atributos / Cod do Atributo / Valor do Atributo
            $stAtributosPai .= "&stAtributoPai[".$i."]=".$atributo['nom_atributo']."&inCodAtributoPai".$atributo['nom_atributo']."=".$atributo['cod_atributo']."&stAtributoValorPai".$atributo['nom_atributo']."='+document.getElementById('stAtributo".$atributo['nom_atributo']."').value+'";

            $obCmbAtributo[$i] = new Select;
            $obCmbAtributo[$i]->setRotulo    ( $atributo['nom_atributo']              );
            $obCmbAtributo[$i]->setName      ( 'stAtributo'.$atributo['nom_atributo'] );
            $obCmbAtributo[$i]->setId        ( 'stAtributo'.$atributo['nom_atributo'] );
            $obCmbAtributo[$i]->setCampoID   ( 'valor_desc'                           );
            $obCmbAtributo[$i]->setCampoDesc ( 'valor'                                );
            $obCmbAtributo[$i]->addOption    ( "", "Selecione"                        );
            $obCmbAtributo[$i]->setObrigatorioBarra(true);

            $obCmbAtributo[$i]->obEvento->setOnChange("ajaxJavaScript('".$pgOc."&inCodItem=".$_REQUEST[$stNomeItem]."&stValorAtributo='+this.value+'&inCodAtributo=".$atributo['cod_atributo']."&inCodMarca=".$_REQUEST['inCodMarca']."&inCodCentroCusto=".$_REQUEST['inCodCentroCusto']."&inCodAlmoxarifado=".$_REQUEST['inCodAlmoxarifado']."','preencheComboAtributo'); ajaxJavaScript('".$pgOc."&".$stNomeAlmoxarifado."='+document.frm.".$obIMontaItemQuantidade->obISelectAlmoxarifado->getName().".value+'&inCodItem='+document.frm.inCodItem.value+'&inCodCentroCusto='+document.frm.inCodCentroCusto.value+'&inCodMarca='+document.frm.inCodMarca.value+'".$stAtributosPai."','mostraSaldoAtributo');");

            $obFormulario->addComponente( $obCmbAtributo[$i] );
            $stNomAtributos .= " - ".$atributo['nom_atributo'];
        }

        $obIMontaItemQuantidade->stNomAtributos = substr($stNomAtributos, 3);

        $obIMontaItemQuantidade = $obIMontaItemQuantidade;
        Sessao::write('obIMontaItemQuantidade', $obIMontaItemQuantidade);

        $obHdnSaldoAtributo = new Hidden;
        $obHdnSaldoAtributo->setName  ( "hdnNuSaldoAtributo" );
        $obHdnSaldoAtributo->setId  ( "hdnNuSaldoAtributo" );

        $obLblSaldo = new Label;
        $obLblSaldo->setRotulo( 'Saldo por Atributo' );
        $obLblSaldo->setName( 'nuSaldoAtributo' );
        $obLblSaldo->setId  ( 'nuSaldoAtributo' );
        $obLblSaldo->setValue( '&nbsp;' );

        $obTxtQuantidade = new Quantidade;
        $obTxtQuantidade->setRotulo  ( "Quantidade" );
        $obTxtQuantidade->setName    ( "nuQuantidadeAtributo" );
        $obTxtQuantidade->setId      ( "nuQuantidadeAtributo" );
        $obTxtQuantidade->setInteiro ( false );
        $obTxtQuantidade->setFloat   ( true  );
        $obTxtQuantidade->setObrigatorioBarra( true );
        $obTxtQuantidade->setValue("0,0000");

        $stHtmlLista = montaHtmlListaAtributos();

        $obSpanListaAtributos = new Span;
        $obSpanListaAtributos->setId( 'spnListaAtributos' );
        $obSpanListaAtributos->setValue( $stHtmlLista );

        $obBtnIncluir = new Button;
        $obBtnIncluir->setName              ( "btIncluirAtributos"    );
        $obBtnIncluir->setId                ( "btIncluirAtributos"    );
        $obBtnIncluir->setValue             ( "Incluir"             );
        $obBtnIncluir->setDisabled          (true);
        $obBtnIncluir->obEvento->setOnClick("ajaxJavaScript('".$pgOc."&nuQuantidadeAtributo='+document.getElementById('nuQuantidadeAtributo').value+'&hdnNuSaldoAtributo='+document.getElementById('hdnNuSaldoAtributo').value+'".$stAtributosPai."'+'&inCodAlmoxarifado='+jQuery('#inCodAlmoxarifado').val() +'&inCodCentroCusto='+jQuery('#inCodCentroCusto').val()+'&inCodMarca='+jQuery('#inCodMarca').val()+'&inCodItem='+jQuery('#inCodItem').val(),'incluirQuantidadeAtributo');");

        $obBtnLimpar = new Button;
        $obBtnLimpar->setName              ( "btLimparAtributos"          );
        $obBtnLimpar->setValue             ( "Limpar"                   );
        $obBtnLimpar->obEvento->setOnClick ( "ajaxJavaScript('".$pgOc.$stAtributosPai."','limparCamposAtributo');");

        $obFormulario->addHidden    ( $obHdnSaldoAtributo );
        $obFormulario->addComponente( $obLblSaldo );
        $obFormulario->addComponente( $obTxtQuantidade );
        $obFormulario->defineBarra( array($obBtnIncluir, $obBtnLimpar), "left", "<b>**Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" );

        $obFormulario->addSpan($obSpanListaAtributos);

        $obFormulario->montaInnerHTML();

        $stHtmlFormulario = $obFormulario->getHTML();

        $stJs .= "document.getElementById('spnAtributos').innerHTML = '".$stHtmlFormulario."';\n";

        $stJs .= "document.getElementById('nuQuantidade').readOnly = true;\n";
        $stJs .= "document.getElementById('nuQuantidade').value = '';";
        $stJs .= preencheComboAtributo( 'stAtributo'.$rsAtributos->arElementos[0]['nom_atributo'], $_REQUEST[$stNomeItem], $rsAtributos->arElementos[0]['cod_atributo']);
    } else {
        $stJs .= "document.getElementById('spnAtributos').innerHTML = '';\n";
    }

    return $stJs;
}

function preencheComboAtributo($idCmbAtributo, $inCodItem, $inCodAtributo)
{
    include_once ( TALM."TAlmoxarifadoAtributoEstoqueMaterialValor.class.php" );

    $arConjuntoAtributos = Sessao::read('arConjuntoAtributos');

    // Remove todos os valores dos selects (atributos dinâmicos) para recriá-los abaixo.
    $stJs .= "jQuery(\"select[id^='stAtributo']\").removeOption(/./); \n";

    // Cria um novo índice no array que guarda os atributos para gravar o valor selecionado na tela.
    if (isset($_REQUEST['inCodAtributo']) && !empty($_REQUEST['inCodAtributo'])) {
        foreach ($arConjuntoAtributos as $arChave => $arValor) {
            if ($_REQUEST['inCodAtributo'] == $arChave) {
                $arConjuntoAtributos[$arChave]['vl_atributo'] = (!empty($_REQUEST['stValorAtributo']) ? $_REQUEST['stValorAtributo'] : "");
            }
        }
    }

    Sessao::write('arConjuntoAtributos', $arConjuntoAtributos);

    $stFiltroAux .= " AND (";

    // Monta o filtro para ser usado na query conforme os atributos selecionados.
    foreach ($arConjuntoAtributos as $arChave => $arValor) {
        if (!empty($arValor['vl_atributo'])) {
            $stFiltro .= " (cod_atributo = ".$arValor['cod_atributo']." AND valor = '".$arValor['vl_atributo']."') OR ";
        }
    }

    // Remove o caracter da variável que não será mais utilizado ( OR ).
    $stFiltro = substr($stFiltro, 0, strlen($inCodAtributos)-4);

    if (!empty($stFiltro)) {
        $stFiltro = $stFiltroAux.$stFiltro." ) ";
    }

    $inCodModulo   = 29;
    $inCodCadastro = 2;

    // Faz a consulta para retornar todos os lançamentos que tenham o(s) atributo(s) selecionados.
    $obTAlmoxarifadoAtributoEstoqueMaterialValor = new TAlmoxarifadoAtributoEstoqueMaterialValor;
    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_item'         , $_REQUEST['inCodItem']);
    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_marca'        , $_REQUEST['inCodMarca']);
    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_centro'       , $_REQUEST['inCodCentroCusto']);
    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_almoxarifado' , $_REQUEST['inCodAlmoxarifado']);
    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_modulo'       , $inCodModulo  );
    $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_cadastro'     , $inCodCadastro);

    $obTAlmoxarifadoAtributoEstoqueMaterialValor->recuperaLancamentoValoresAtributo( $rsValorAtributo, $stFiltro );

    foreach ($rsValorAtributo->arElementos as $chave => $valor) {
        $inCodLancamento .= $valor['cod_lancamento'].",";
    }

    $inCodLancamento = substr($inCodLancamento, 0, strlen($inCodLancamento)-1);

    // Busca todos os valores dos atributos que podem ser selecionados.
    $obTAlmoxarifadoIrmaosAtributoEstoqueMaterialValor = new TAlmoxarifadoAtributoEstoqueMaterialValor;
    $obTAlmoxarifadoIrmaosAtributoEstoqueMaterialValor->setDado('cod_item'         , $_REQUEST['inCodItem']);
    $obTAlmoxarifadoIrmaosAtributoEstoqueMaterialValor->setDado('cod_marca'        , $_REQUEST['inCodMarca']);
    $obTAlmoxarifadoIrmaosAtributoEstoqueMaterialValor->setDado('cod_centro'       , $_REQUEST['inCodCentroCusto']);
    $obTAlmoxarifadoIrmaosAtributoEstoqueMaterialValor->setDado('cod_lancamento'   , $inCodLancamento);
    $obTAlmoxarifadoIrmaosAtributoEstoqueMaterialValor->setDado('cod_almoxarifado' , $_REQUEST['inCodAlmoxarifado']);
    $obTAlmoxarifadoIrmaosAtributoEstoqueMaterialValor->setDado('cod_modulo'       , $inCodModulo  );
    $obTAlmoxarifadoIrmaosAtributoEstoqueMaterialValor->setDado('cod_cadastro'     , $inCodCadastro);

    $obTAlmoxarifadoIrmaosAtributoEstoqueMaterialValor->recuperaValoresAtributo( $rsIrmaosAtributo );

    $arConjuntoAtributos = Sessao::read('arConjuntoAtributos');

    // Percorre o RecordSet para popular os selects com os valores que podem ser selecionados.
    foreach ($rsIrmaosAtributo->arElementos as $chave => $valor) {
        foreach ($arConjuntoAtributos as $arChave => $arValor) {
            $idIdAtributo = "stAtributo".$arValor['nom_atributo'];
            if ($valor['cod_atributo'] == $arChave) {
                $inCodTipo = SistemaLegado::pegaDado('cod_tipo','administracao.atributo_dinamico'," where cod_atributo = ".$valor['cod_atributo']);

                if ($inCodTipo == 3) {
                    $stDescricao = SistemaLegado::pegaDado('valor_padrao','administracao.atributo_valor_padrao'," WHERE cod_modulo = ".$inCodModulo." AND cod_cadastro = ".$inCodCadastro." AND cod_valor =".$valor['valor']." and cod_atributo = ".$valor['cod_atributo']);

                } elseif (($inCodTipo == 4)) {
                    $stDescricaoValores = array();
                    $arValorAtributoSeparado = explode(",",trim($valor['valor']));

                    foreach ($arValorAtributoSeparado as $chave => $dados) {

                        $stDescricaoValores[] = SistemaLegado::pegaDado('valor_padrao','administracao.atributo_valor_padrao'," WHERE cod_modulo = ".$inCodModulo." AND cod_cadastro = ".$inCodCadastro." AND cod_valor IN (".$dados.") and cod_atributo = ".$valor['cod_atributo']);
                    }
                    $stDescricao = implode(', ',$stDescricaoValores);
                } else {
                    $stDescricao = $valor['valor'];
                }

                if (!empty($arConjuntoAtributos[$valor['cod_atributo']]['vl_atributo'])) {
                    if ($arConjuntoAtributos[$valor['cod_atributo']]['vl_atributo'] == $valor['valor'])
                        $selected = "selected";
                    else
                        $selected = "";
                } else {
                    $selected = "";
                }

                $stJs .= "document.getElementById('".$idIdAtributo."').options.add(  new Option('".$stDescricao."','".$valor['valor']."', '$selected') ); \n";

                break;
            }
        }
    }

    return $stJs;
}

function montaArrayAtributoValoresPai()
{
    $arValorAtributo = array();

    for ($i=0; $i <= count( $_REQUEST['stAtributoPai'] ) -1; $i++ ) {
        $arValorAtributo[$i]['cod_atributo']   = $_REQUEST[str_replace(' ','_','inCodAtributoPai'.$_REQUEST['stAtributoPai'][$i])];
        $arValorAtributo[$i]['valor']          = $_REQUEST[str_replace(' ','_','stAtributoValorPai'.$_REQUEST['stAtributoPai'][$i])];
    }

    return $arValorAtributo;
}

function mostraSaldoAtributo($inCodAlmoxarifado, $inCodItem, $inCodCentro, $inCodMarca, $arValorAtributos)
{
    include_once ( TALM."TAlmoxarifadoAtributoEstoqueMaterialValor.class.php" );

    $arConjuntoAtributos = Sessao::read('arConjuntoAtributos', $arConjuntoAtributos);

    if (($arConjuntoAtributos) && ($inCodCentro)) {

        $js .= "jQuery('#btIncluirAtributos').attr('disabled', 'true');";

        $rsSaldo = new RecordSet;
        $stFiltroAux .= " AND (";

        // Monta o filtro para ser usado na query conforme os atributos selecionados.
        foreach ($arConjuntoAtributos as $arChave => $arValor) {
            if (!empty($arValor['vl_atributo'])) {
                $stFiltro .= " (cod_atributo = ".$arValor['cod_atributo']." AND valor = '".$arValor['vl_atributo']."') OR ";
                $stCodAtributos .= ", ".$arValor['cod_atributo'];
                $stValorAtributos .= ", '".$arValor['vl_atributo']."'";
            }
        }

        $stCodAtributos   = substr($stCodAtributos, 2);
        $stValorAtributos = substr($stValorAtributos, 2);

        // Remove o caracter da variável que não será mais utilizado ( OR ).
        $stFiltro = substr($stFiltro, 0, strlen($inCodAtributos)-4);
        if (!empty($stFiltro)) {
            $stFiltro = $stFiltroAux.$stFiltro." ) ";
        }
        $inCodModulo   = 29;
        $inCodCadastro = 2;

        // Faz a consulta para retornar todos os lançamentos que tenham o(s) atributo(s) selecionados.
        $obTAlmoxarifadoAtributoEstoqueMaterialValor = new TAlmoxarifadoAtributoEstoqueMaterialValor;
        $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_item'         , $_REQUEST['inCodItem']);
        $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_marca'        , $_REQUEST['inCodMarca']);
        $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_centro'       , $_REQUEST['inCodCentroCusto']);
        $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_almoxarifado' , $_REQUEST['inCodAlmoxarifado']);
        $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_modulo'       , $inCodModulo  );
        $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_cadastro'     , $inCodCadastro);

        $obTAlmoxarifadoAtributoEstoqueMaterialValor->recuperaLancamentoValoresAtributo( $rsValorAtributo, $stFiltro );

        $arValorAtributos = explode('\', ',$stValorAtributos);
        $arCodAtributos = explode(',',trim($stCodAtributos));

        foreach ($arValorAtributos as $chave => $inValorAtributo) {
            if ($inValorAtributo == "'") {
                unset($arValorAtributos[$chave]);
                unset($arCodAtributos[$chave]);
            }
        }

        $stValorAtributos = implode('\', ',$arValorAtributos);
        $stCodAtributos = implode(', ',$arCodAtributos);

        //Faz a verificação para abilitar ou deixar desabilitado o botão de incluirt
        if (($stCodAtributos)&&($stValorAtributos)) {
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado( 'cod_atributos' , $stCodAtributos );
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado( 'valor_atributos' , $stValorAtributos );
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->recuperaSaldo( $rsSaldo1 );
            if ($rsSaldo1->getCampo('qtd')> 0) {
                $js .= "jQuery('#btIncluirAtributos').attr('disabled', '');";
            }
        }

        foreach ($rsValorAtributo->arElementos as $chave => $valor) {
            $inCodLancamento .= $valor['cod_lancamento'].",";
        }

        $inCodLancamento = substr($inCodLancamento, 0, strlen($inCodLancamento)-1);
        $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_lancamento' , $inCodLancamento);
        $obTAlmoxarifadoAtributoEstoqueMaterialValor->recuperaSaldoDinamico( $rsSaldo );

        $nuSaldo = 0;
        if ($rsSaldo->getCampo('qtd')) {
            foreach($rsSaldo->arElementos as $chave => $valor)
            $nuSaldo = $nuSaldo + $valor['qtd'];
        }

        $nuSaldo = number_format( $nuSaldo, 4, ',', '.');

        $js .= "document.getElementById('nuSaldoAtributo').innerHTML = '".$nuSaldo."';";
        $js .= "document.getElementById('hdnNuSaldoAtributo').value = '".$nuSaldo."';";

        return $js;
    }
}

function incluirQuantidadeAtributo()
{
    //define o id do novo atributo/valor
    $obIMontaItemQuantidadeValoresAtributo = Sessao::read('obIMontaItemQuantidadeValoresAtributo');
    $inId = count($obIMontaItemQuantidadeValoresAtributo);

    //Cria um novo array para se passar na validacao incluir na sessao
    $arNovo = array();
    $arNovo['inId'] = $inId;

    $stMensagem = "";

    $quantidadeAtributo = str_replace('.','',$_REQUEST['nuQuantidadeAtributo']);
    $saldoAtributo = str_replace('.','',$_REQUEST['hdnNuSaldoAtributo']);
    $quantidadeAtributo = str_replace(',','',$quantidadeAtributo);
    $saldoAtributo = str_replace(',','',$saldoAtributo);

    if ($quantidadeAtributo > $saldoAtributo) {
               $stMensagem = "A Quantidade não pode ser maior que o Saldo";
    }

    for ($i=0; $i < count($_REQUEST['stAtributoPai']); $i++) {
        $arNovo['atributo'][$i]['cod_atributo'] = $_REQUEST[str_replace(' ','_','inCodAtributoPai'.$_REQUEST['stAtributoPai'][$i])];
        $arNovo['atributo'][$i]['nom_atributo'] = $_REQUEST['stAtributoPai'][$i];
        $arNovo['atributo'][$i]['valor'] = $_REQUEST[str_replace(' ','_','stAtributoValorPai'.$_REQUEST['stAtributoPai'][$i])];

        //usado na validacao
        $stValoresAtributos .= " - ".$_REQUEST[str_replace(' ','_','stAtributoValorPai'.$_REQUEST['stAtributoPai'][$i])];
        $stNomeAtributos .= " - ".str_replace(' ','_',$_REQUEST['stAtributoPai'][$i]);

    }

    $arNovo['NomeAtributos'] = substr($stNomeAtributos, 3);
    $arNovo['stValoresAtributos'] = substr($stValoresAtributos, 3);
    $arNovo['quantidade'] = $_REQUEST['nuQuantidadeAtributo'];
    $arNovo['saldo_atributo'] = $_REQUEST['hdnNuSaldoAtributo'];

    //validacao
    for ( $i=0; $i<count( $obIMontaItemQuantidadeValoresAtributo ); $i++) {
        if ($arNovo['stValoresAtributos'] == $obIMontaItemQuantidadeValoresAtributo[$i]['stValoresAtributos']) {
            $stMensagem = "Já existe quantidade para estes atributos.";
        }
        //calculo da quantidade total
        $stQuantidadeAtributo = str_replace(".", "", $obIMontaItemQuantidadeValoresAtributo[$i]['quantidade'] );
        $stQuantidadeAtributo = str_replace(",", ".", $stQuantidadeAtributo );
        $nuQuantidadeTotal = $nuQuantidadeTotal + (float) $stQuantidadeAtributo;
    }

    if ($stMensagem == "") {
        $obIMontaItemQuantidadeValoresAtributo[$inId] = $arNovo;
        //quantidade total existente + o novo item
        $stQuantidadeAtributo = str_replace(".", "", $arNovo['quantidade'] );
        $stQuantidadeAtributo = str_replace(",", ".", $stQuantidadeAtributo );
        $nuQuantidadeTotal = $nuQuantidadeTotal + (float) $stQuantidadeAtributo;
        $nuQuantidadeTotal = number_format( $nuQuantidadeTotal, 4, ',', '.');

        Sessao::write('obIMontaItemQuantidadeValoresAtributo', $obIMontaItemQuantidadeValoresAtributo);

        $js .= montaListaAtributos();
        $js .= "document.getElementById('nuQuantidade').value = '".$nuQuantidadeTotal."';";
        //$js .= limparCamposAtributo();
    } else {
        $js = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    }

    return $js;
}

function excluirQuantidadeAtributo($inId)
{
    $arNovo = array();
    $inIdNovo = 0;
    $nuQuantidadeTotal = 0;
    $obIMontaItemQuantidadeValoresAtributo = Sessao::read('obIMontaItemQuantidadeValoresAtributo');
    for ($i=0; $i < count($obIMontaItemQuantidadeValoresAtributo); $i++) {
        if ($inId != $obIMontaItemQuantidadeValoresAtributo[$i]['inId']) {
            $arNovo[$inIdNovo]['inId'] = $inIdNovo;
            $arNovo[$inIdNovo]['atributo'] = $obIMontaItemQuantidadeValoresAtributo[$i]['atributo'];
            $arNovo[$inIdNovo]['stValoresAtributos'] = $obIMontaItemQuantidadeValoresAtributo[$i]['stValoresAtributos'];
            $arNovo[$inIdNovo]['quantidade'] = $obIMontaItemQuantidadeValoresAtributo[$i]['quantidade'];
            $nuQuantidadeAtributo = str_replace('.', '', $obIMontaItemQuantidadeValoresAtributo[$i]['quantidade']);
            $nuQuantidadeAtributo = str_replace(',', '.', $nuQuantidadeAtributo);
            $nuQuantidadeTotal += $nuQuantidadeAtributo;

            $inIdNovo++;
        }
    }

    $obIMontaItemQuantidadeValoresAtributo = $arNovo;
    Sessao::write('obIMontaItemQuantidadeValoresAtributo', $obIMontaItemQuantidadeValoresAtributo);
    $js = "document.getElementById('nuQuantidade').value = '".number_format($nuQuantidadeTotal, 4, ',', '.')."';";

    return $js;
}

function limpaSpanAtributos()
{
    $js  = "document.getElementById('spnAtributos').innerHTML = '';\n";
    $js .= "document.getElementById('nuQuantidade').value = '';";
    $js .= "document.getElementById('nuQuantidade').readOnly = false;\n";

    return $js;
}

function limparCamposAtributo()
{
    for ($i=0; $i < count($_REQUEST['stAtributoPai']); $i++) {
        $js .= "document.getElementById('stAtributo".$_REQUEST['stAtributoPai'][$i]."').value = '';";
    }
    $js .= "document.getElementById('nuQuantidadeAtributo').value = '0,0000';";
    $js .= "document.getElementById('nuSaldoAtributo').innerHTML = '&nbsp;';";
    //$js .= "document.getElementById('nuQuantidade').readOnly = false;\n";
    return $js;
}

function preencheSpanLotes()
{
    include_once ( TALM."TAlmoxarifadoPerecivel.class.php" );
    global $pgOc, $stNomeItem, $stNomeAlmoxarifado, $stNomeMarca, $stNomeCentroCusto;
    $obIMontaItemQuantidadeLotes = Sessao::read('obIMontaItemQuantidadeLotes');

    if (!$_REQUEST[$stNomeCentroCusto])
        return;

    $stFiltro  = " where cod_item = "       . $_REQUEST[$stNomeItem];
    $stFiltro .= " and cod_almoxarifado = " . $_REQUEST[$stNomeAlmoxarifado];
    $stFiltro .= " and cod_marca = "        . $_REQUEST[$stNomeMarca];
    $stFiltro .= " and cod_centro = "       . $_REQUEST[$stNomeCentroCusto];

    $obTAlmoxarifadoPerecivel = new TAlmoxarifadoPerecivel;
    $obTAlmoxarifadoPerecivel->setDado('cod_item'   , $_REQUEST[$stNomeItem]);
    $obTAlmoxarifadoPerecivel->setDado('cod_marca'  , $_REQUEST[$stNomeMarca]);
    $obTAlmoxarifadoPerecivel->setDado('cod_centro' , $_REQUEST[$stNomeCentroCusto]);
    $obTAlmoxarifadoPerecivel->setDado('cod_almoxarifado', $_REQUEST[$stNomeAlmoxarifado]);
    $obTAlmoxarifadoPerecivel->recuperaTodos($rsPerecivel, $stFiltro, "ORDER BY TO_CHAR(dt_validade,'yyyymmdd') ASC");

    if ($rsPerecivel->eof())
        return;

    //$arRecordSetLotes = is_array($obIMontaItemQuantidadeLotes) ? $obIMontaItemQuantidadeLotes : array();
    $arRecordSetLotes = array();
    $inPosLotes = 0;
    $disp = $inSaldoDisponivel;
    while (!$rsPerecivel->eof()) {
        $quantidadeLote = 0;
        $arLotes['inId']          = $inPosLotes++;
        $arLotes['lote']          = $rsPerecivel->getCampo('lote');
        $arLotes['dt_validade']   = $rsPerecivel->getCampo('dt_validade');
        $arLotes['dt_fabricacao'] = $rsPerecivel->getCampo('dt_fabricacao');

        $obTAlmoxarifadoPerecivel->setDado('lote', $arLotes['lote'] );
        $obTAlmoxarifadoPerecivel->recuperaSaldoLote( $rsSaldoLote );
        $nuSaldoLote = $rsSaldoLote->getCampo( 'saldo_lote' );
        $arLotes['saldo'] = number_format($nuSaldoLote, 4, ',', '.');

        if ($disp > 0) {
            if ($nuSaldoLote >= $disp) {
                 $quantidadeLote = $disp ;
                 $disp = 0;
            } else {
                 $quantidadeLote = $nuSaldoLote;
                 $disp = $disp - $nuSaldoLote;
            }
        }
        $arLotes['quantidade'] = number_format($quantidadeLote, 4, ',', '.');
        if ( str_replace(',','.',str_replace('.','',$arLotes['saldo'])) > 0 ) {
            $arRecordSetLotes[] = $arLotes;
        }
        //$arElementos['ValoresLotes'][] = $arLotes;
        $rsPerecivel->proximo();
    }
    Sessao::write('obIMontaItemQuantidadeLotes',$arRecordSetLotes);

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
    //$obTxtQtdLote->obEvento->setOnBlur("buscaDado('alterarQuantidadeLote');");
    $obTxtQtdLote->obEvento->setOnBlur("ajaxJavaScript('".$pgOc."&'+this.name+'='+this.value+'&inCodAlmoxarifado='+jQuery('#inCodAlmoxarifado').val() +'&inCodCentroCusto='+jQuery('#inCodCentroCusto').val()+'&inCodMarca='+jQuery('#inCodMarca').val()+'&inCodItem='+jQuery('#inCodItem').val(),'alterarQuantidadeLote');");

    $obLista->addDadoComponente( $obTxtQtdLote );
    $obLista->ultimoDado->setCampo( "nmQtdLote" );
    $obLista->commitDadoComponente();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs  = "document.getElementById('spnAtributos').innerHTML = '';";
    $stJs .= "document.getElementById('spnListaLotes').innerHTML = '".$stHTML."';";
    $stJs .= "document.getElementById('nuQuantidade').readOnly = true;";

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case "limpaCampos":
        $stJs  = "f.".$stNomeItem.".value = '';";
        $stJs .= "d.getElementById('".$obIMontaItemQuantidade->obIMontaItemUnidade->obIPopUpCatalogoItem->getId()."').innerHTML = '&nbsp;';";
        $stJs .= "d.getElementById('".$obIMontaItemQuantidade->obIMontaItemUnidade->obLabelUnidadeMedida->getId()."').innerHTML = '&nbsp;';";
        $stJs .= "limpaSelect(f.".$stNomeMarca.",1);";
        $stJs .= "limpaSelect(f.".$stNomeCentroCusto.",1);";
        $stJs .= "jQuery('#".$obIMontaItemQuantidade->obLblSaldo->getId()."').html('&nbsp;');";
        $stJs .= "f.".$obIMontaItemQuantidade->obTxtQuantidade->getName().".value = '';";
        $stJs .= limpaSpanAtributos();
    break;

    case "carregaMarca":
        $stJs .= limpaSpanAtributos();
        if ($_GET[$stNomeItem]) {
            include_once ( TALM."TAlmoxarifadoCatalogoItemMarca.class.php" );
            $obTAlmoxarifadoCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca;
            $stFiltro = " and acim.cod_item = ".$_REQUEST[$stNomeItem];

            if ($_REQUEST[$stNomeAlmoxarifado]) {
                $stFiltro .= " and spfc.cod_almoxarifado = ".$_REQUEST[$stNomeAlmoxarifado];
            }

            $obTAlmoxarifadoCatalogoItemMarca->recuperaItemMarcaComSaldo( $rsMarcas, $stFiltro );

            $stJs .= "limpaSelect(f.".$obIMontaItemQuantidade->obCmbMarca->getName().", 1);";
            $stJs .= "limpaSelect(f.".$stNomeCentroCusto.", 1);";
            $stJs .= "jQuery('#".$stNomeSaldo."').html('&nbsp;');";
            $rsMarcas->addFormatacao( 'descricao' , 'SLASHES' );

            $rsMarcas->setPrimeiroElemento();
            while ( !$rsMarcas->eof() ) {
                  $stJs .= "f.".$stNomeMarca."[".$rsMarcas->getCorrente()."] = new Option('".$rsMarcas->getCampo('descricao')."','".$rsMarcas->getCampo('cod_marca')."');\n";
                $rsMarcas->proximo();
            }
        } else {
            $stJs .= "limpaSelect(f.".$obIMontaItemQuantidade->obCmbMarca->getName().", 1);";
            $stJs .= "limpaSelect(f.".$stNomeCentroCusto.", 1);";
            $stJs .= "d.getElementById('".$stNomeSaldo."').innerHTML = '&nbsp;';";
        }
    break;
    case 'carregaCentroCusto':
        include_once ( TALM."TAlmoxarifadoEstoqueMaterial.class.php"   );
        $obTAlmoxarifadoEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
        $stJs .= "limpaSelect(f.".$stNomeCentroCusto.", 1);";
        $stJs .= "jQuery('#".$stNomeSaldo."').html('&nbsp;');";
        $stJs .= limpaSpanAtributos();

        if (isset( $_REQUEST[$stNomeMarca] ) && isset( $_REQUEST[$stNomeItem] ) && trim($_REQUEST[$stNomeMarca])<>"") {
            $stFiltro = " and am.cod_marca = ".$_REQUEST[$stNomeMarca];
            $stFiltro .= " and aem.cod_item = ".$_REQUEST[$stNomeItem];
            if ($_REQUEST[$stNomeAlmoxarifado]) {
                $stFiltro .= " and aem.cod_almoxarifado = ".$_REQUEST[$stNomeAlmoxarifado];
            }

            $stAcaoTela = Sessao::read('stAcaoTela');
            if ($stAcaoTela != 'IncluirNotaTransferencia' and $stAcaoTela !='') {
                $stFiltro .= " and apa.cgm_almoxarife = ".Sessao::read('numCgm');
            }

            if (!$obIMontaItemQuantidade->getCentroCustoPermissao())
                $stFiltro .= " and accp.numcgm  = ".Sessao::read('numCgm');

            $obTAlmoxarifadoEstoqueMaterial->recuperaEstoqueCentroDeCustoComSaldo( $rsCentro, $stFiltro );

            $rsCentro->setPrimeiroElemento();
            while ( !$rsCentro->eof() ) {
                $stJs .= "f.".$stNomeCentroCusto."[".$rsCentro->getCorrente()."] = new Option('".$rsCentro->getCampo('descricao')."','".$rsCentro->getCampo('cod_centro')."');\n";
                $rsCentro->proximo();
            }
            $stJs .= "f.".$obIMontaItemQuantidade->obHdnMarca->getName().".value = f.".$obIMontaItemQuantidade->obCmbMarca->getName().".options[f.".$obIMontaItemQuantidade->obCmbMarca->getName().".selectedIndex].text;";
        }
    break;
    case 'mostraSaldo':
       if (isset( $_REQUEST[$stNomeCentroCusto] ) && isset( $_REQUEST[$stNomeMarca] ) && isset( $_REQUEST[$stNomeItem] )) {
               $boUsarMarca = true;
            if ($_GET[$stNomeItem]) {
                include_once ( TALM."TAlmoxarifadoCatalogoItemMarca.class.php" );
                $obTAlmoxarifadoCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca;
                $stFiltro = " and acim.cod_item = ".$_REQUEST[$stNomeItem];

                if ($_REQUEST[$stNomeAlmoxarifado]) {
                    $stFiltro .= " and spfc.cod_almoxarifado = ".$_REQUEST[$stNomeAlmoxarifado];
                }

                $obTAlmoxarifadoCatalogoItemMarca->recuperaItemMarcaComSaldo( $rsMarcas, $stFiltro );
                //$boUsarMarca = count($rsMarcas)-1; // retirado, pois trazia o saldo errado na requisição
            }

            include_once ( TALM."TAlmoxarifadoEstoqueMaterial.class.php"   );
            $obTAlxamoxarifadoEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;
            $stFiltro  = " and  alm.cod_centro = ".$_REQUEST[$stNomeCentroCusto];
            if($boUsarMarca)
                $stFiltro .= " and alm.cod_marca = ".$_REQUEST[$stNomeMarca];
            $stFiltro .= " and alm.cod_item = ".$_REQUEST[$stNomeItem];
            if ($_REQUEST[$stNomeAlmoxarifado]) {
                $stFiltro .= " and alm.cod_almoxarifado = ".$_REQUEST[$stNomeAlmoxarifado];
            }
            $obTAlxamoxarifadoEstoqueMaterial->recuperaSaldoEstoque( $rsSaldo, $stFiltro );

            $boMostraSaldo = strtolower(sistemalegado::pegaConfiguracao('demonstrar_saldo_estoque' , 29));

            if ( $rsSaldo->getCampo('saldo_estoque')) {
                $inSaldo = number_format($rsSaldo->getCampo('saldo_estoque'),4,',','.');
                if ($boMostraSaldo == 'true') {                   
                    $stJs .= "jQuery('#".$stNomeSaldo."').html('".$inSaldo."');";
                    $stJs .= "f.".$obIMontaItemQuantidade->obHdnSaldo->getName().".value = '".$inSaldo."';";
                    $stJs .= "f.".$obIMontaItemQuantidade->obHdnCentroCusto->getName().".value = f.".$stNomeCentroCusto.".options[f.".$stNomeCentroCusto.".selectedIndex].text;";

                    //Se o item possuir atributos de entrada deve ser mostrado o span dos atributos
                    if ($_REQUEST['boFlag']) {
                        $stJs .= mostraAtributos(false);
                    } else {
                        $stJs .= mostraAtributos();
                    }
                } else {
                    $stJs .= "d.getElementById('".$stNomeSaldo."').innerHTML = 'Configuração não permite a exibição do saldo de estoque.';";
                    $stJs .= "f.".$obIMontaItemQuantidade->obHdnSaldo->getName().".value = '".$inSaldo."';";
                    //$stJs .= limpaSpanAtributos();
                }
            } else {
                $stJs .= "d.getElementById('".$stNomeSaldo."').innerHTML = '&nbsp;';";
                $stJs .= limpaSpanAtributos();
            }
        } else {
            $stJs .= "d.getElementById('".$stNomeSaldo."').innerHTML = '&nbsp;';";
            $stJs .= limpaSpanAtributos();
        }
    break;
    case 'preencheComboAtributo':
        $stJs .= preencheComboAtributo( $_REQUEST['idCmbAtributo'], $_REQUEST['inCodItem'], $_REQUEST['inCodAtributo'] );
        $stJs .= "document.getElementById('nuSaldoAtributo').innerHTML = '&nbsp;';";

    break;
    case 'mostraSaldoAtributo':
        $arValorAtributo = montaArrayAtributoValoresPai();
        if ($_REQUEST[$stNomeAlmoxarifado] != '') {
            $stJs .= mostraSaldoAtributo( $_REQUEST[$stNomeAlmoxarifado], $_REQUEST['inCodItem'], $_REQUEST['inCodCentroCusto'], $_REQUEST['inCodMarca'], $arValorAtributo );
        }
    break;
    case 'incluirQuantidadeAtributo':
        if (!$_REQUEST['nuQuantidadeAtributo'] || $_REQUEST['nuQuantidadeAtributo'] == '0,0000') {
            $mensagem = "Campo Quantidade inválido!";
        }
        foreach ($_REQUEST['stAtributoPai'] as $atributo) {

            $stAtributoNull = $_REQUEST[str_replace(' ','_',"inCodAtributoPai".$atributo)];

            //$boNull = SistemaLegado::pegaDado('nao_nulo','administracao.atributo_dinamico'," where cod_atributo ='".$stAtributoNull."'");
            //if ( ( (!$_REQUEST[str_replace(' ','_',"stAtributoValorPai".$atributo)]) && ($boNull == 't' ))) {
            //    $mensagem = "Campo $atributo inválido!";
            //    break;
            //}
        }
        if ($mensagem) {
            $stJs = "alertaAviso('".$mensagem."','form','erro','".Sessao::getId()."');";
        } else {

            /*$obIMontaItemQuantidade = Sessao::read("obIMontaItemQuantidade");

            $stNomeAlmoxarifado = $obIMontaItemQuantidade->obISelectAlmoxarifado->getName();
            $stNomeItem = $obIMontaItemQuantidade->obIMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->getName();
            $stNomeMarca = $obIMontaItemQuantidade->obCmbMarca->getName();
            $stNomeCentroCusto = $obIMontaItemQuantidade->obCmbCentroCusto->getName();
            $stNomeSaldo = $obIMontaItemQuantidade->obLblSaldo->getId();*/

            $stJs .= incluirQuantidadeAtributo();

            $arConjuntoAtributos = Sessao::read('arConjuntoAtributos');

            // Monta o filtro para ser usado na query conforme os atributos selecionados.
            foreach ($arConjuntoAtributos as $arChave => $arValor) {
                unset($arConjuntoAtributos[$arChave]['vl_atributo']);
            }

            Sessao::write('arConjuntoAtributos', $arConjuntoAtributos);

            $pgOculto = CAM_GP_ALM_PROCESSAMENTO.'OCIMontaItemQuantidade.php?'.Sessao::getId();
            $stJs .= "ajaxJavaScript('".$pgOculto."&inCodItem=".$_REQUEST[$stNomeItem]."&stValorAtributo='+this.value+'&inCodAtributo=".$atributo['cod_atributo']."&inCodMarca=".$_REQUEST['inCodMarca']."&inCodCentroCusto=".$_REQUEST['inCodCentroCusto']."&inCodAlmoxarifado=".$_REQUEST['inCodAlmoxarifado']."','preencheComboAtributo'); ajaxJavaScript('".$pgOc."&".$stNomeAlmoxarifado."='+document.frm.".$obIMontaItemQuantidade->obISelectAlmoxarifado->getName().".value+'&inCodItem='+document.frm.inCodItem.value+'&inCodCentroCusto='+document.frm.inCodCentroCusto.value+'&inCodMarca='+document.frm.inCodMarca.value+'".$stAtributosPai."','mostraSaldoAtributo');";
        }
    break;
    case 'excluirQuantidadeAtributo':
         $stJs = excluirQuantidadeAtributo( $_REQUEST['inId'] ) ;
         $stJs .= montaListaAtributos();
    break;
    case 'limparCamposAtributo':
        $stJs = limparCamposAtributo();
    break;
    case 'mostraSaldoPereciveis':
        $stJs .= preencheSpanLotes();
    break;
    case 'alterarQuantidadeLote':
        $obIMontaItemQuantidadeLotes = Sessao::read('obIMontaItemQuantidadeLotes');
        foreach ($_REQUEST as $key=>$value) {
            if (strpos($key,'nmQtdLoteLista')!==false) {
                $arKey          = explode("_",$key);
                $inIndice       = $arKey[1]-1;
                $arTMP          = $obIMontaItemQuantidadeLotes[ $inIndice ];
                $nuSaldo        = str_replace(',','.',str_replace('.','', $arTMP['saldo'] ));
                $nuQuantidade   = str_replace(',','.',str_replace('.','', $value ));
                $obIMontaItemQuantidadeLotes[ $inIndice ]['quantidade'] = $value;
            }
        }
        for ($inCount=0; $inCount<count($obIMontaItemQuantidadeLotes); $inCount++) {
            $nuTotal += $obIMontaItemQuantidadeLotes[$inCount]['quantidade'];
        }
        Sessao::write('obIMontaItemQuantidadeLotes',$obIMontaItemQuantidadeLotes);
        $stJs .= "document.getElementById('nuQuantidade').value = '".number_format($nuTotal,4,',','.')."';";
    break;
}

echo $stJs;
?>
