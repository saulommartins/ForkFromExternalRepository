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
<?PHP
/**
    * Pagine de Oculta do Movimentação SEFIP
    * Data de Criação: 06/02/2006

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.04.40

    $Id: OCManterMovimentacaoSEFIP.php 59612 2014-09-02 12:00:51Z gelson $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//###############################
function gerarSpanMovimentacaoAlterar()
{
    Sessao::write("stAcao","alterar");
    $inCodSefip = Sessao::read("inCodSefip");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalSefip.class.php");
    $obTPessoalSefip = new TPessoalSefip();
    $stFiltro = " WHERE cod_sefip = ".$inCodSefip;
    $obTPessoalSefip->recuperaTodos($rsSefip,$stFiltro);
    $stCodigoSEFIP = $rsSefip->getCampo("num_sefip");
    $stDescricao = $rsSefip->getCampo("descricao");

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalMovSefipSaida.class.php");
    $obTPessoalMovSefipSaida = new TPessoalMovSefipSaida();
    $obTPessoalMovSefipSaida->setDado("cod_sefip_saida",$inCodSefip);
    $obTPessoalMovSefipSaida->recuperaPorChave($rsSefipSaida);

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalMovimentoSefipRetorno.class.php");
    $obTPessoalMovimentoSefipRetorno = new TPessoalMovimentoSefipRetorno();
    $obTPessoalMovimentoSefipRetorno->setDado("cod_sefip_retorno",$inCodSefip);
    $obTPessoalMovimentoSefipRetorno->recuperaPorChave($rsSefipRetorno);

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalMovSefipSaidaMovSefipRetorno.class.php");
    $obTPessoalMovSefipSaidaMovSefipRetorno = new TPessoalMovSefipSaidaMovSefipRetorno();
    $stFiltro = " AND cod_sefip_saida = ".$inCodSefip;
    $obTPessoalMovSefipSaidaMovSefipRetorno->recuperaMovSefipRetorno($rsMovSaidaRetorno,$stFiltro);

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCategoriaMovimento.class.php");
    $obTPessoalCategoriaMovimento = new TPessoalCategoriaMovimento();

    if ($rsSefipSaida->getNumLinhas() > 0) {
        $stMovimentacao = "A";
        $stJs = "f.stMovimentacao[0].checked = true;\n";
    } else {
        $stMovimentacao = "R";
        $stJs = "f.stMovimentacao[1].checked = true;\n";
    }

    Sessao::write("stMovimentacao",$stMovimentacao);
    $stJs .= "$('stCodigoSEFIP').value = '".$stCodigoSEFIP."';\n";
    $stJs .= "$('stDescricao').value = '".$stDescricao."';\n";
    $stJs .= gerarSpanMovimentacao();
    if ($stMovimentacao == "A") {
        if ($rsMovSaidaRetorno->getNumLinhas() == 1) {
            Sessao::write("boRetorno","S");
        } else {
            Sessao::write("boRetorno","N");
            $stFiltro = " AND cod_sefip_saida = ".$inCodSefip;
            $obTPessoalCategoriaMovimento->recuperaRelacionamento($rsCategorias, $stFiltro);
            $arCategorias = array();
            while (!$rsCategorias->eof()) {
                $arElementos['stIndicativo'] = $rsCategorias->getCampo("indicativo");
                $arElementos['inCodCategoria'] = $rsCategorias->getCampo("cod_categoria");
                $arElementos['categoria'] = $rsCategorias->getCampo("descricao");
                $arCategorias[] = $arElementos;
                $rsCategorias->proximo();
            }
            Sessao::write("aCategorias",$arCategorias);
        }
        $stJs .= gerarSpanMovimentacao();

        if ($rsMovSaidaRetorno->getNumLinhas() == 1) {
            $stJs .= "f.boRetorno[0].checked = true;\n";
        } else {
            $stJs .= "f.boRetorno[1].checked = true;\n";
            $stJs .= montaListaRecolhimento();
        }
        if ($rsSefip->getCampo("repetir_mensal") == "t") {
            $stJs .= "f.stRepetir[0].checked = true;\n";
        } else {
            $stJs .= "f.stRepetir[1].checked = true;\n";
        }
        if ($rsMovSaidaRetorno->getNumLinhas() == 1) {
            $stJs .= "$('stNumSefipRetorno').value ='". $rsMovSaidaRetorno->getCampo('num_sefip')   ."';             \n";
            $stJs .= "$('stSefipRetorno').innerHTML = '".$rsMovSaidaRetorno->getCampo('descricao') ."';";
        }
    }

    return $stJs;
}

function gerarSpanMovimentacao()
{
    $stMovimentacao = ($_GET["stMovimentacao"] != "") ? $_GET["stMovimentacao"] : Sessao::read("stMovimentacao");
    $stAcao = Sessao::read("stAcao");
    if ($stMovimentacao == "A" or $stMovimentacao == "") {
        $obSNRepetirMensal = new SimNao;
        $obSNRepetirMensal->setname    ( "stRepetir"                                                 );
        $obSNRepetirMensal->setId      ( "stRepetir"                                                 );
        $obSNRepetirMensal->setRotulo  ( "Repetir Mensalmente"                                       );
        $obSNRepetirMensal->setTitle   ( "Informe se a movimentação deverá ser repetida mensalmente." );
        $obSNRepetirMensal->setChecked(true);

        $obSNRetorno = new SimNao;
        $obSNRetorno->setname                           ( 'boRetorno'                                                        );
        $obSNRetorno->setID                             ( 'boRetorno'                                                        );
        $obSNRetorno->setRotulo                         ( 'Movimentação com Retorno'                                         );
        $obSNRetorno->setTitle                          ( 'Indique se a movimentação da SEFIP de afastamento possui retorno' );
        $obSNRetorno->setChecked(true);
        $obSNRetorno->obRadioSim->obEvento->setOnChange ( "montaParametrosGET('gerarSpanMovimentacaoRetorno','boRetorno');"                                   );
        $obSNRetorno->obRadioNao->obEvento->setOnChange ( "montaParametrosGET('gerarSpanMovimentacaoRetorno','boRetorno');"                                   );
//         if ($stAcao == "alterar") {
//             $obSNRetorno->obRadioSim->setDisabled(true);
//             $obSNRetorno->obRadioNao->setDisabled(true);
//         }

        $obSpanMovimentacaoRetorno = new Span();
        $obSpanMovimentacaoRetorno->setId("spnMovimentacaoRetorno");

        $obFormulario = new Formulario;
        $obFormulario->addComponente ( $obSNRepetirMensal);
        $obFormulario->addComponente ( $obSNRetorno);
        $obFormulario->addSpan($obSpanMovimentacaoRetorno);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        $stJs  = "$('spnSpanMovimentacao').innerHTML = '".$stHtml. "';";
        $stJs .= gerarSpanMovimentacaoRetorno();
    } else {
        $stJs  = "$('spnSpanMovimentacao').innerHTML = '".$stHtml. "';";
    }

    return $stJs;
}

function gerarSpanMovimentacaoRetorno()
{
    $boRetorno = ($_GET["boRetorno"] != "") ? $_GET["boRetorno"] : Sessao::read("boRetorno");

    if ($boRetorno == "S") {
        $obinnBuscaSefip = new BuscaInner;
        $obinnBuscaSefip->setRotulo                         ( 'SEFIP Retorno'                      );
        $obinnBuscaSefip->setTitle                          ( 'escolha a Sefip de retorno'         );
        $obinnBuscaSefip->setID                             ( 'stSefipRetorno'                     );
        $obinnBuscaSefip->setNull                           ( false                                );

        $obinnBuscaSefip->obCampoCod->setName               ( 'stNumSefipRetorno'                  );
        $obinnBuscaSefip->obCampoCod->setId                 ( 'stNumSefipRetorno'                  );
        $obinnBuscaSefip->obCampoCod->setValue              ( $inCodSefipRetorno                   );
        $obinnBuscaSefip->obCampoCod->setAlign              ( "right"                              );
        $obinnBuscaSefip->obCampoCod->setInteiro            ( false                                );
        $obinnBuscaSefip->obCampoCod->setMaxLength          ( 3                                    );
        $obinnBuscaSefip->obCampoCod->setNull               ( false                                );
        $obinnBuscaSefip->obCampoCod->obEvento->setOnChange ( "montaParametrosGET('buscaSefipRetorno','stNumSefipRetorno');" );

        $obinnBuscaSefip->setFuncaoBusca( "abrePopUp('FLManterMovimentacaoSEFIP.php',
                                            'frm', 'stNumSefipRetorno', 'stSefipRetorno',
                                            'retorno', '".Sessao::getId()."&stAcao=SELECIONAR','800','550' );" );

        $obFormulario = new Formulario;
        $obFormulario->addComponente ( $obinnBuscaSefip      );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    } else {
        $obSelIndicativo = new Select;
        $obSelIndicativo->setName   ('stIndicativo');
        $obSelIndicativo->setId     ('stIndicativo');
        $obSelIndicativo->setRotulo ( 'Indicativo' );
        $obSelIndicativo->setTitle  ( "Informe o indicativo para recolhimento de FGTS. 'S' ou 'N'.".
                                        "para indicar se o empregador já efetuou arrecadação FGTS. ".
                                        "'C' para indicar se é recolhimento complementar do FGTS   ");
        $obSelIndicativo->setValue  ( $stInticativo       );
        $obSelIndicativo->setStyle  ( "width: 110px"      );
//         $obSelIndicativo->setNullBarra   ( false               );
        $obSelIndicativo->addOption ( "", "Nulo"         );
        $obSelIndicativo->addOption ( "S", "Sim"          );
        $obSelIndicativo->addOption ( "N", "Não"          );
        $obSelIndicativo->addOption ( "C", 'Complementar' );

        $obinCategoria = new BuscaInner;
        $obinCategoria->setRotulo            ( 'Categoria Sefip'     );
        $obinCategoria->setID                ( 'stCategoria'         );
        $obinCategoria->setTitle             ( 'Informe a categoria' );
        $obinCategoria->setName              ( 'stCategoria'         );
        $obinCategoria->setNullBarra              ( false                 );
        $obinCategoria->obCampoDescrHidden->setId("HdninCodCategoria");
        $obinCategoria->obCampoCod->setName  ( 'inCodCategoria'      );
        $obinCategoria->obCampoCod->setId    ( 'inCodCategoria'      );
        $obinCategoria->obCampoCod->setAlign ( 'right'               );
        $obinCategoria->obCampoCod->obEvento->setOnChange ( "montaParametrosGET('buscaCategoria','inCodCategoria');" );
        $obinCategoria->setFuncaoBusca( "abrePopUp('". CAM_GRH_PES_POPUPS. "assentamento/LSCategoria.php',
                                        'frm', 'inCodCategoria', 'stCategoria',
                                        'retorno', '".Sessao::getId()."&stAcao=SELECIONAR','800','550' );" );

        $obSpnCategorias = new Span;
        $obSpnCategorias->setId("spnCategorias");

        $obFormulario = new formulario;
        $obFormulario->addTitulo("Informações sobre o recolhimento do FGTS");
        $obFormulario->addComponente( $obSelIndicativo );
        $obFormulario->addComponente( $obinCategoria );
        $obFormulario->incluir("Recolhimento",array($obSelIndicativo,$obinCategoria),true);
        $obFormulario->addSpan($obSpnCategorias);
        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs  = "$('spnMovimentacaoRetorno').innerHTML = '".$stHtml. "';";
    $stJs .= "$('hdnMovimentacao').value = '".$obFormulario->getInnerJavaScript()."';";
    $stJs .= $obFormulario->getInnerJavascriptBarra();

    return $stJs;
}

function buscaSefipRetorno()
{
    $rsSefip = new recordset;
    if ($_GET["stNumSefipRetorno"]) {
        include_once ( CAM_GRH_PES_MAPEAMENTO. "TPessoalMovimentoSefipRetorno.class.php" );
        $obTPessoalMovimentoSefipRetorno = new TPessoalMovimentoSefipRetorno;
        $stFiltro = " WHERE num_sefip = '".$_GET["stNumSefipRetorno"]."'";
        $obTPessoalMovimentoSefipRetorno->recuperaRelacionamentoSefip ( $rsSefip,$stFiltro );
    }
    if ( $rsSefip->getNumLinhas() > 0 ) {
        $stJs .= "$('stNumSefipRetorno').value ='". $rsSefip->getCampo('num_sefip')   ."';             \n";
        $stJs .= "$('stSefipRetorno').innerHTML = '".$rsSefip->getCampo('descricao') ."';";
    } else {
        $stJs .= "$('stNumSefipRetorno').value ='';             \n";
        $stJs .= "$('stSefipRetorno').innerHTML = '&nbsp';";
    }

    return $stJs;
}

function buscaCategoria()
{
    $inCodCategoria = $_GET['inCodCategoria'];
    $rsCategoria = new recordset;
    if ($inCodCategoria) {
        include_once ( CAM_GRH_PES_MAPEAMENTO. "TPessoalCategoria.class.php" );

        $obTPessoalCategoria = new TPessoalCategoria;
        $stFiltro = " WHERE cod_categoria = ".$inCodCategoria;
        $obTPessoalCategoria->recuperaTodos($rsCategoria,$stFiltro);

    }
    if ( $rsCategoria->getNumLinhas() > 0 ) {
        $stJs .= "$('inCodCategoria').value ='". $inCodCategoria  ."';             \n";
        $stJs .= "$('stCategoria').innerHTML = '".$rsCategoria->getCampo('descricao') ."';";
        $stJs .= "$('HdninCodCategoria').value ='".$rsCategoria->getCampo('descricao') ."';      \n";
    } else {
        $stJs .= "$('inCodCategoria').value ='';             \n";
        $stJs .= "$('stCategoria').innerHTML = '&nbsp';";
        $stJs .= "$('HdninCodCategoria').value ='';             \n";
    }

    return $stJs;
}

function incluirRecolhimento()
{
    $arCategorias = Sessao::read('aCategorias');
    $obErro = new erro();
    if (is_array($arCategorias)) {
        foreach ($arCategorias as $arCategoria) {
            if ($arCategoria['inCodCategoria'] == $_GET['inCodCategoria']) {
                $obErro->setDescricao("Esta categoria já foi cadastrada");
                break;
            }
        }
    }
    if (!$obErro->ocorreu()) {
        include_once ( CAM_GRH_PES_MAPEAMENTO. "TPessoalCategoria.class.php" );

        $obTPessoalCategoria = new TPessoalCategoria;
        $stFiltro = " WHERE cod_categoria = ".$_GET['inCodCategoria'];
        $obTPessoalCategoria->recuperaTodos($rsCategoria,$stFiltro);

        $arElementos['stIndicativo'] = $_GET['stIndicativo'  ];
        $arElementos['inCodCategoria'] = $_GET['inCodCategoria'];
        $arElementos['categoria'] = $rsCategoria->getCampo("descricao");
        $arCategorias[] = $arElementos;

        Sessao::write('aCategorias',$arCategorias);
        $stJs = montaListaRecolhimento();
    } else {
        $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
    }

    return $stJs;
}

function excluirRecolhimento()
{
    $arCategorias = Sessao::read('aCategorias');
    $arTemp = array();
    if (is_array($arCategorias)) {
        foreach ($arCategorias as $arCategoria) {
            if ($arCategoria['inCodCategoria'] != $_GET['inCodCategoria']) {
                    $arTemp[] = $arCategoria;
            }
        }
    }
    Sessao::write('aCategorias',$arTemp);
    $stJs = montaListaRecolhimento();

    return $stJs;
}

function montaListaRecolhimento()
{
    $rsRecordSet = new recordset;
    $rsRecordSet->preenche(Sessao::read('aCategorias'));

    // Montagem Lista
    $obLstCategorias = new Lista;
    $obLstCategorias->setTitulo          ("Lista de Indicativos Relacionados à Categoria");
    $obLstCategorias->setMostraPaginacao ( false );

    //criação da fonte de dados para a lista
    $obLstCategorias->setRecordset($rsRecordSet);
    //Fim Fonte

    // Cabeçalho da lista
    $obLstCategorias->addCabecalho();
    $obLstCategorias->ultimoCabecalho->addConteudo    ( "&nbsp;"     );
    $obLstCategorias->ultimoCabecalho->setWidth       ( 5            );
    $obLstCategorias->commitCabecalho();

    $obLstCategorias->addCabecalho();
    $obLstCategorias->ultimoCabecalho->addConteudo    ( "Indicativo" );
    $obLstCategorias->ultimoCabecalho->setWidth       ( 10           );
    $obLstCategorias->commitCabecalho();

    $obLstCategorias->addCabecalho();
    $obLstCategorias->ultimoCabecalho->addConteudo    ( "Codigo");
    $obLstCategorias->ultimoCabecalho->setWidth       ( 10      );
    $obLstCategorias->commitCabecalho();

    $obLstCategorias->addCabecalho();
    $obLstCategorias->ultimoCabecalho->addConteudo    ( 'Categoria');
    $obLstCategorias->ultimoCabecalho->setWidth       ( 80      );
    $obLstCategorias->commitCabecalho();

    $obLstCategorias->addCabecalho();
    $obLstCategorias->ultimoCabecalho->addConteudo    ( 'Ação');
    $obLstCategorias->ultimoCabecalho->setWidth       ( 20    );
    $obLstCategorias->commitCabecalho();
    // fim cabeçalho

    //dados da Lista
    $obLstCategorias->addDado();
    $obLstCategorias->ultimoDado->setCampo( "stIndicativo" );
    $obLstCategorias->commitDado();

    $obLstCategorias->addDado();
    $obLstCategorias->ultimoDado->setCampo( "inCodCategoria" );
    $obLstCategorias->commitDado();

    $obLstCategorias->addDado();
    $obLstCategorias->ultimoDado->setCampo( "categoria" );
    $obLstCategorias->commitDado();
    // FIM dados da Lista

    // Adicionando ação excluir a lista
    $obLstCategorias->addAcao();
    $obLstCategorias->ultimaAcao->setAcao( "EXCLUIR" );
    $obLstCategorias->ultimaAcao->setFuncaoAjax( true );
    $obLstCategorias->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirRecolhimento');" );
    $obLstCategorias->ultimaAcao->addCampo("1","inCodCategoria");
    $obLstCategorias->commitAcao();

    //fim Ação

    // Fim montagem Lista

    $obLstCategorias->montaHTML();
    $stHtml = $obLstCategorias->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs  = "$('spnCategorias').innerHTML = '".$stHtml. "';";

    return $stJs;
}

function limpaForm()
{
    $stJs .= gerarSpanMovimentacao();

    return $stJs;
}

//###############################

switch ($_GET["stCtrl"]) {
    case "gerarSpanMovimentacao":
        $stJs = gerarSpanMovimentacao();
        break;
    case "gerarSpanMovimentacaoRetorno":
        $stJs = gerarSpanMovimentacaoRetorno();
        break;
    case "buscaSefipRetorno":
        $stJs = buscaSefipRetorno();
        break;
    case "buscaCategoria":
        $stJs = buscaCategoria();
        break;
    case "incluirRecolhimento":
        $stJs = incluirRecolhimento();
        break;
    case "excluirRecolhimento":
        $stJs = excluirRecolhimento();
        break;
    case "limpaForm":
        $stJs = limpaForm();
        break;
    case "gerarSpanMovimentacaoAlterar":
        $stJs = gerarSpanMovimentacaoAlterar();
        break;
}

if ($stJs) {
    echo $stJs;
}
