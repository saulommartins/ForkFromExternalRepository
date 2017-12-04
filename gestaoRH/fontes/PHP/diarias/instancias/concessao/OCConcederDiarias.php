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
    * Página de Oculto para Concessão de Diárias
    * Data de Criação: 05/08/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: OCConcederDiarias.php 63836 2015-10-22 14:06:51Z franver $

    * Casos de uso: uc-04.09.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                     );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                             );
include_once ( CAM_GRH_DIA_MAPEAMENTO."TDiariasDiaria.class.php"                                  );
include_once ( CAM_GRH_DIA_MAPEAMENTO."TDiariasTipoDiaria.class.php"                              );
include_once ( CAM_GRH_DIA_MAPEAMENTO."TDiariasDiariaEmpenho.class.php"                           );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"                                        );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php"                                    );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoPais.class.php"                               );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php"                                 );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php"                          );

$stAcao = $request->get('stAcao');
$stCtrl = $_REQUEST['stCtrl'];

$stPrograma = "ConcederDiarias";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

function atualizaSpanFiltro()
{
    $obFormulario = new Formulario;

    if ($_GET["rdoOpcao"] == 1) {
        $obIFiltroCGMContrato = new IFiltroCGMContrato(true);
        $obIFiltroCGMContrato->obBscCGM->obCampoCod->setId('inNumCGM');
        $obIFiltroCGMContrato->geraFormulario($obFormulario);
    } elseif ($_GET["rdoOpcao"] == 2) {

        if ($_GET['stAcao'] == 'conceder') {
            $boRescindido = false;
        } else {
            $boRescindido = true;
        }

        $obIContratoDigitoVerificador = new IContratoDigitoVerificador("",$boRescindido);
        $obIContratoDigitoVerificador->setTitle( "Informe a matrícula do servidor, ou clique no ícone de busca." );
        $obIContratoDigitoVerificador->setPagFiltro(true);
        $obIContratoDigitoVerificador->obTxtRegistroContrato->setNull(false);
        $obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnChange("");
        $obIContratoDigitoVerificador->geraFormulario($obFormulario);
    }
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavascript();
    $stTmp = $obFormulario->obJavaScript->getInnerJavascript();
    $stTmp = str_replace("\n","",$stTmp);

    $stJs  .= "f.stEval.value = '".trim($stTmp)."'; \n";
    $stJs  .= "d.getElementById('spnOpcao').innerHTML = '".$obFormulario->getHTML()."';\n";

    $stJs  .= "if (d.getElementById('inCodTipoDiaria')) {\n";
    $stJs  .= "    d.getElementById('inCodTipoDiaria').options[0].selected = true;\n";
    $stJs  .= "}\n";

    $stJs  .= "if (d.getElementById('dtPagamentoInicial')) {\n";
    $stJs  .= "    d.getElementById('dtPagamentoInicial').value = '';\n";
    $stJs  .= "    d.getElementById('dtPagamentoFinal').value = '';\n";
    $stJs  .= "}\n";

    if ($_GET["rdoOpcao"] == 1) {
        $stJs  .= "d.getElementById('rdoOpcao1').checked = true; \n";
        $stJs  .= "d.getElementById('inNumCGM').focus(); \n";
    } else {
        $stJs  .= "d.getElementById('rdoOpcao2').checked = true; \n";
        $stJs  .= "d.getElementById('inContrato').focus(); \n";
    }

    return $stJs;
}

function preencherConcessoes($inCodContrato)
{
    $rsDiaria = new RecordSet();
    $obTDiariasDiaria = new TDiariasDiaria();
    $stFiltroDiaria = " AND contrato.cod_contrato = ".$inCodContrato;
    $obTDiariasDiaria->recuperaRelacionamento($rsDiaria, $stFiltroDiaria);

    $arSessaoConcessoes = array();
    if ($rsDiaria->getNumLinhas() > 0) {
        $inCountConcessoes=1;
        while (!$rsDiaria->eof()) {

            $arConcessao                             = array();
            $arConcessao['inId']                     = $inCountConcessoes;
            $arConcessao['inCodDiaria']              = $rsDiaria->getCampo('cod_diaria');
            $arConcessao['stTimestamp']              = $rsDiaria->getCampo('timestamp');
            $arConcessao['inCodContrato']            = $rsDiaria->getCampo('cod_contrato');
            $arConcessao['nuNormaExercicio']         = $rsDiaria->getCampo('num_norma_exercicio');
            $arConcessao['dtInicio']                 = $rsDiaria->getCampo('dt_inicio');
            $arConcessao['dtTermino']                = $rsDiaria->getCampo('dt_termino');
            $arConcessao['hrInicio']                 = $rsDiaria->getCampo('hr_inicio');
            $arConcessao['hrTermino']                = $rsDiaria->getCampo('hr_termino');
            $arConcessao['inCodEstado']              = $rsDiaria->getCampo('cod_uf');
            $arConcessao['inCodMunicipio']           = $rsDiaria->getCampo('cod_municipio');
            $arConcessao['stMotivo']                 = $rsDiaria->getCampo('motivo');
            $arConcessao['inCodTipo']                = $rsDiaria->getCampo('cod_tipo');
            $arConcessao['nuQuantidade']             = $rsDiaria->getCampo('quantidade');
            $arConcessao['nuValorTotal']             = $rsDiaria->getCampo('vl_total');
            $arConcessao['nuValorUnitario']          = $rsDiaria->getCampo('vl_unitario');
            $arConcessao['stAutorizacaoEmpenho']     = $rsDiaria->getCampo('autorizacao_empenho');
            $arConcessao['dtAutorizacaoEmpenho']     = $rsDiaria->getCampo('dt_autorizacao_empenho');
            $arConcessao['stTimestampTipo']          = $rsDiaria->getCampo('timestamp_tipo');
            //$arConcessao['dtPagamento']              = $rsDiaria->getCampo('dt_pagamento');

            $stOrdenacao = explode("/",$rsDiaria->getCampo('dt_inicio'));
            $arConcessao['stOrdenacao']              = $stOrdenacao[2].$stOrdenacao[1].$stOrdenacao[0];

            $arConcessao['stAssinatura']             = serialize($arConcessao);

            $arSessaoConcessoes[] = $arConcessao;

            $rsDiaria->proximo();
            $inCountConcessoes++;
        }//
    }//
    Sessao::write('arConcessoes', $arSessaoConcessoes);
    $stJs = montaListaConcessoes($arSessaoConcessoes);

    return $stJs;
}

function montaListaConcessoes($arConcessoes)
{
    global $pgOcul;
    $rsConcessoes = new Recordset;
    $rsConcessoes->preenche($arConcessoes);

    if ($rsConcessoes->getNumLinhas() > 0) {

        while (!$rsConcessoes->eof()) {
            //Verifica Norma
            $rsNorma    = new RecordSet();
            $arCodNorma = ltrim($rsConcessoes->getCampo('nuNormaExercicio'), "0");
            if($arCodNorma[0]=="")
                $arCodNorma = "0".$arCodNorma;
            $arCodNorma = explode("/",$arCodNorma);
            if (count($arCodNorma)>0) {
                $rsNorma  = new RecordSet();
                $stFiltroNorma = " WHERE num_norma='".$arCodNorma[0]."' and exercicio='".$arCodNorma[1]."'";
                $obTNorma = new TNorma();
                $obTNorma->recuperaTodos($rsNorma, $stFiltroNorma);
                if ($rsNorma->getNumLinhas() > 0) {
                    $stFiltroTipoNorma = " WHERE cod_tipo_norma = ".$rsNorma->getCampo('cod_tipo_norma');
                    $obTTipoNorma = new TTipoNorma();
                    $obTTipoNorma->recuperaTodos($rsTipoNorma, $stFiltroTipoNorma);
                    $rsConcessoes->setCampo('dtAto', $rsNorma->getCampo('dt_publicacao'));
                    $rsConcessoes->setCampo('stDescNorma', $rsTipoNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma')."/".$rsNorma->getCampo('exercicio')." - ".$rsNorma->getCampo('nom_norma')  );
                }
            }

            $stCodNorma = ltrim($rsConcessoes->getCampo('nuNormaExercicio'), "0");
            if($stCodNorma[0] == "/")
                $stCodNorma = "0".$stCodNorma;

            $rsConcessoes->setCampo('nuNormaExercicio', $stCodNorma);
            $rsConcessoes->proximo();
        }

        $rsConcessoes->addFormatacao('nuValorTotal', 'NUMERIC_BR');
        $rsConcessoes->ordena('stOrdenacao');
        $rsConcessoes->setPrimeiroElemento();

        $obLista = new Lista;
        $obLista->setTitulo("Lista de Concess&otilde;es de Di&aacute;rias");
        $obLista->setRecordSet($rsConcessoes);
        $obLista->setMostraPaginacao( false );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Período da Viagem");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Descri&ccedil;&atilde;o da Lei/Decreto");
        $obLista->ultimoCabecalho->setWidth( 35 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Data Lei/Decreto");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor Total");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "[dtInicio] à [dtTermino] " );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[stDescNorma]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "[dtAto]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "R$ [nuValorTotal]" );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('preencherAlteraConcessao');");
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirConcessao');");
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }//

    $stJs .= "d.getElementById('spnConcessoes').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function incluirConcessao()
{
    $obErro     = new erro;
    $stCodNorma = ltrim($_REQUEST['stCodNorma'], "0");
    if($stCodNorma[0]=="/")
        $stCodNorma = "0".$stCodNorma;

    $nuValorTotal = str_replace(",", ".", str_replace(".", "", $_REQUEST['nuValorTotal']))*1;
    if ($nuValorTotal <= 0) {
        $obErro->setDescricao("Campo Valor Total inválido()");
    }

    if ( !$obErro->ocorreu() ) {
        $arConcessoes = ( is_array(Sessao::read('arConcessoes')) ) ? Sessao::read('arConcessoes') : array();
        foreach ($arConcessoes as $arConcessao) {
            if (addslashes($arConcessao['stAssinatura']) != $_REQUEST['stAssinatura']) {

                if(
                             dataContida($arConcessao['dtInicio']."#".$arConcessao['hrInicio'], $arConcessao['dtTermino']."#".$arConcessao['hrTermino'], $_REQUEST['dtInicio']."#".$_REQUEST['hrInicio']) ||
                             dataContida($arConcessao['dtInicio']."#".$arConcessao['hrInicio'], $arConcessao['dtTermino']."#".$arConcessao['hrTermino'], $_REQUEST['dtTermino']."#".$_REQUEST['hrTermino']) ||

                             dataContida($_REQUEST['dtInicio']."#".$_REQUEST['hrInicio'], $_REQUEST['dtTermino']."#".$_REQUEST['hrTermino'], $arConcessao['dtInicio']."#".$arConcessao['hrInicio']) ||
                             dataContida($_REQUEST['dtInicio']."#".$_REQUEST['hrInicio'], $_REQUEST['dtTermino']."#".$_REQUEST['hrTermino'], $arConcessao['dtTermino']."#".$arConcessao['hrTermino']) ||

                             ($_REQUEST['dtInicio']."#".$_REQUEST['hrInicio'] == $arConcessao['dtInicio']."#".$arConcessao['hrInicio'] &&
                              $_REQUEST['dtTermino']."#".$_REQUEST['hrTermino'] == $arConcessao['dtTermino']."#".$arConcessao['hrTermino'])
                        ){

                    $obErro->setDescricao("O Período da Viagem selecionado está em conflito com a diária concedida em ".$arConcessao['dtInicio']." (Saída ".$arConcessao['hrInicio'].") Lei/Decreto ".$arConcessao['nuNormaExercicio']);

                    if ($arConcessao['nuNormaExercicio'] == $stCodNorma) {
                        $obErro->setDescricao("O Período da Viagem e Lei/Decreto informados já foram atribuidos à outra diária: Período ".$arConcessao['dtInicio']." (Saída ".$arConcessao['hrInicio'].") à ".$arConcessao['dtTermino']." (Retorno ".$arConcessao['hrTermino'].") - Lei/Decreto ".$arConcessao['nuNormaExercicio']." - Valor Total R$".number_format($arConcessao['nuValorTotal'], 2, ",", "."));
                    }
                    break;
                }
            } else {
                if (!comparaDatas($_REQUEST['dtInicio']."#".$_REQUEST['hrInicio'],$_REQUEST['dtTermino']."#".$_REQUEST['hrTermino'])) {
                    $obErro->setDescricao("No campo Período da Viagem, a Data Inicial deve ser maior que a Data de Término");
                    break;
                }
            }
        }//end foreach
    }

    //busca timestamp_tipo
    $stFiltroTipoDiaria = " AND cod_tipo = ".$_REQUEST['inCodTipo'];
    $obTDiariasTipoDiaria = new TDiariasTipoDiaria;
    $obTDiariasTipoDiaria->recuperaTipoDiariaEmVigencia($rsTipoDiaria, $stFiltroTipoDiaria);

    if ( !$obErro->ocorreu() ) {
        $arConcessoes                            = Sessao::read('arConcessoes');

        $arConcessao                             = array();
        $arConcessao['inId']                     = count($arConcessoes)+1;
        $arConcessao['inCodDiaria']              = "";
        $arConcessao['stTimestamp']              = "";
        $arConcessao['inCodContrato']            = $_REQUEST['inCodContrato'];
        $arConcessao['nuNormaExercicio']         = $_REQUEST['stCodNorma'];
        $arConcessao['dtInicio']                 = $_REQUEST['dtInicio'];
        $arConcessao['dtTermino']                = $_REQUEST['dtTermino'];
        $arConcessao['hrInicio']                 = $_REQUEST['hrInicio'];
        $arConcessao['hrTermino']                = $_REQUEST['hrTermino'];
        $arConcessao['inCodEstado']              = $_REQUEST['inCodEstado'];
        $arConcessao['inCodMunicipio']           = $_REQUEST['inCodMunicipio'];
        $arConcessao['stMotivo']                 = $_REQUEST['stMotivo'];
        $arConcessao['inCodTipo']                = $_REQUEST['inCodTipo'];
        $arConcessao['nuQuantidade']             = str_replace(",", ".", str_replace(".", "", $_REQUEST['nuQuantidade']));
        $arConcessao['nuValorTotal']             = str_replace(",", ".", str_replace(".", "", $_REQUEST['nuValorTotal']));
        $arConcessao['nuValorUnitario']          = $_REQUEST['nuValorDiaria'];
        $arConcessao['stTimestampTipo']          = $rsTipoDiaria->getCampo('timestamp');
        //$arConcessao['dtPagamento']              = $_REQUEST['dtPagamento'];

        $stOrdenacao = explode("/",$_REQUEST['dtInicio']);
        $arConcessao['stOrdenacao']              = $stOrdenacao[2].$stOrdenacao[1].$stOrdenacao[0];

        $arConcessao['stAssinatura']             = serialize($arConcessao);

        $arConcessoes[]                          = $arConcessao;
        Sessao::write('arConcessoes', $arConcessoes);

        $stJs .= "parent.frames[2].limpaFormularioConcessao();";
        $stJs .= montaListaConcessoes($arConcessoes);
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function alterarConcessao()
{
    $obErro     = new erro;
    $stCodNorma = ltrim($_REQUEST['stCodNorma'], "0");
    if($stCodNorma[0]=="/")
        $stCodNorma = "0".$stCodNorma;

    $nuValorTotal = str_replace(",", ".", str_replace(".", "", $_REQUEST['nuValorTotal']))*1;
    if ($nuValorTotal <= 0) {
        $obErro->setDescricao("Campo Valor Total inválido()");
    }

    if ( !$obErro->ocorreu() ) {
        $arConcessoes = ( is_array(Sessao::read('arConcessoes')) ) ? Sessao::read('arConcessoes') : array();

        foreach ($arConcessoes as $arConcessao) {
            if ($arConcessao['inCodDiaria'] != $_REQUEST['inCodDiariaChave'] || $arConcessao['inCodContrato'] != $_REQUEST['inCodContratoChave'] || $arConcessao['stTimestamp'] != $_REQUEST['stTimestampChave']) {
                if(
                             dataContida($arConcessao['dtInicio']."#".$arConcessao['hrInicio'], $arConcessao['dtTermino']."#".$arConcessao['hrTermino'], $_REQUEST['dtInicio']."#".$_REQUEST['hrInicio']) ||
                             dataContida($arConcessao['dtInicio']."#".$arConcessao['hrInicio'], $arConcessao['dtTermino']."#".$arConcessao['hrTermino'], $_REQUEST['dtTermino']."#".$_REQUEST['hrTermino']) ||

                             dataContida($_REQUEST['dtInicio']."#".$_REQUEST['hrInicio'], $_REQUEST['dtTermino']."#".$_REQUEST['hrTermino'], $arConcessao['dtInicio']."#".$arConcessao['hrInicio']) ||
                             dataContida($_REQUEST['dtInicio']."#".$_REQUEST['hrInicio'], $_REQUEST['dtTermino']."#".$_REQUEST['hrTermino'], $arConcessao['dtTermino']."#".$arConcessao['hrTermino']) ||

                             ($_REQUEST['dtInicio']."#".$_REQUEST['hrInicio'] == $arConcessao['dtInicio']."#".$arConcessao['hrInicio'] &&
                              $_REQUEST['dtTermino']."#".$_REQUEST['hrTermino'] == $arConcessao['dtTermino']."#".$arConcessao['hrTermino'])
                        ){

                    $obErro->setDescricao("O Período da Viagem selecionado está em conflito com a diária concedida em ".$arConcessao['dtInicio']." (Saída ".$arConcessao['hrInicio'].") Lei/Decreto ".$arConcessao['nuNormaExercicio']);

                    if ($arConcessao['nuNormaExercicio'] == $stCodNorma) {
                        $obErro->setDescricao("O Período da Viagem e Lei/Decreto informados já foram atribuidos à outra diária: Período ".$arConcessao['dtInicio']." (Saída ".$arConcessao['hrInicio'].") à ".$arConcessao['dtTermino']." (Retorno ".$arConcessao['hrTermino'].") - Lei/Decreto ".$arConcessao['nuNormaExercicio']." - Valor Total R$".number_format($arConcessao['nuValorTotal'], 2, ",", "."));
                    }
                    break;
                }
            } else {
                if (!comparaDatas($_REQUEST['dtInicio']."#".$_REQUEST['hrInicio'],$_REQUEST['dtTermino']."#".$_REQUEST['hrTermino'])) {
                    $obErro->setDescricao("No campo Período da Viagem, a Data de Término deve ser maior que a Data Inicial");
                    break;
                } elseif ($arConcessao['stAutorizacaoEmpenho']!="") {
                    $obErro->setDescricao("A diária selecionada não pode ser alterada pois o pagamento da mesma já foi autorizado.");
                    break;
                }
            }
        }//end foreach
    }

    //busca timestamp_tipo
    $stFiltroTipoDiaria = " AND cod_tipo = ".$_REQUEST['inCodTipo'];
    $obTDiariasTipoDiaria = new TDiariasTipoDiaria;
    $obTDiariasTipoDiaria->recuperaTipoDiariaEmVigencia($rsTipoDiaria, $stFiltroTipoDiaria);

    if ( !$obErro->ocorreu() ) {
        foreach ($arConcessoes as $arConcessaoKey => $arConcessao) {

           if ($arConcessao['inCodDiaria'] == $_REQUEST['inCodDiariaChave'] && $arConcessao['inCodContrato'] == $_REQUEST['inCodContratoChave'] && $arConcessao['stTimestamp'] == $_REQUEST['stTimestampChave']) {

                $arConcessao['inCodDiaria']              = $_REQUEST['inCodDiariaChave'];
                $arConcessao['stTimestamp']              = $_REQUEST['stTimestampChave'];
                $arConcessao['inCodContrato']            = $_REQUEST['inCodContratoChave'];
                $arConcessao['nuNormaExercicio']         = $_REQUEST['stCodNorma'];
                $arConcessao['dtInicio']                 = $_REQUEST['dtInicio'];
                $arConcessao['dtTermino']                = $_REQUEST['dtTermino'];
                $arConcessao['hrInicio']                 = $_REQUEST['hrInicio'];
                $arConcessao['hrTermino']                = $_REQUEST['hrTermino'];
                $arConcessao['inCodEstado']              = $_REQUEST['inCodEstado'];
                $arConcessao['inCodMunicipio']           = $_REQUEST['inCodMunicipio'];
                $arConcessao['stMotivo']                 = $_REQUEST['stMotivo'];
                $arConcessao['inCodTipo']                = $_REQUEST['inCodTipo'];
                $arConcessao['nuQuantidade']             = str_replace(",", ".", str_replace(".", "", $_REQUEST['nuQuantidade']));
                $arConcessao['nuValorTotal']             = str_replace(",", ".", str_replace(".", "", $_REQUEST['nuValorTotal']));
                $arConcessao['nuValorUnitario']          = $_REQUEST['nuValorDiaria'];
                $arConcessao['stTimestampTipo']          = $rsTipoDiaria->getCampo('timestamp');
                //$arConcessao['dtPagamento']              = $_REQUEST['dtPagamento'];

                $stOrdenacao = explode("/",$_REQUEST['dtInicio']);
                $arConcessao['stOrdenacao']              = $stOrdenacao[2].$stOrdenacao[1].$stOrdenacao[0];

                $arConcessao['stAssinatura']             = serialize($arConcessao);

                $arConcessoes[$arConcessaoKey]           = $arConcessao;
                Sessao::write('arConcessoes', $arConcessoes);

                $stJs .= "parent.frames[2].limpaFormularioConcessao();";
                $stJs .= montaListaConcessoes($arConcessoes);
                break;
            }
        }
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirConcessao()
{
    $obErro = new Erro();
    $arConcessoes = ( is_array(Sessao::read('arConcessoes')) ? Sessao::read('arConcessoes') : array());
    $arSessaoConcessoes = array();
    foreach ($arConcessoes as $arConcessao) {
        if ($arConcessao['inId'] != $_REQUEST['inId']) {
            $arConcessao['inId'] = sizeof($arSessaoConcessoes)+1;
            $arSessaoConcessoes[] = $arConcessao;
        } else {
            if ($arConcessao['stAutorizacaoEmpenho']!="") {
                $obErro->setDescricao("A diária selecionada não pode ser removida pois o pagamento da mesma já foi autorizado.");
                break;
            }
        }
    }

    if (!$obErro->ocorreu()) {
        Sessao::write('arConcessoes', $arSessaoConcessoes);
        $stJs .= "parent.frames[2].limpaFormularioConcessao(1);";
        $stJs .= montaListaConcessoes($arSessaoConcessoes);
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function preencherAlteraConcessao()
{
    $arSessaoConcessoes = Sessao::read('arConcessoes');

    if (is_array($arSessaoConcessoes)) {

        foreach ($arSessaoConcessoes as $arConcessao) {

            if ($arConcessao['inId'] == $_REQUEST['inId']) {

                $stJs .= "limpaFormularioConcessao();";

                if ($arConcessao['inCodDiaria'] != "" && $arConcessao['stAutorizacaoEmpenho']!="") {
                    $stJs .= preencherEmpenho($arConcessao['stAutorizacaoEmpenho'], $arConcessao['dtAutorizacaoEmpenho']);
                }

                $stJs .= preencherPais($arConcessao['inCodEstado'], $arConcessao['inCodMunicipio']);

                $stJs .= "f.stCodNorma.value         = '".$arConcessao['nuNormaExercicio']."';  \n";
                $stJs .= preencherDetalhesNorma($arConcessao['nuNormaExercicio']);

                $stJs .= "d.getElementById('nuValorDiariaFormatado').innerHTML = 'R$ ".number_format($arConcessao['nuValorUnitario'], 2, ",", ".")."'; \n";
                $stJs .= "f.nuValorDiaria.value = '".number_format($arConcessao['nuValorUnitario'], 2, ".", "")."'; \n";
                $stJs .= "f.inCodTipo.value          = '".$arConcessao['inCodTipo']."';         \n";

                $stJs .= "f.inCodDiariaChave.value   = '".$arConcessao['inCodDiaria']."';       \n";
                $stJs .= "f.stTimestampChave.value   = '".$arConcessao['stTimestamp']."';       \n";
                $stJs .= "f.inCodContratoChave.value = '".$arConcessao['inCodContrato']."';     \n";

                $stJs .= "f.dtInicio.value           = '".$arConcessao['dtInicio']."';          \n";
                $stJs .= "f.dtTermino.value          = '".$arConcessao['dtTermino']."';         \n";
                $stJs .= "f.hrInicio.value           = '".$arConcessao['hrInicio']."';          \n";
                $stJs .= "f.hrTermino.value          = '".$arConcessao['hrTermino']."';         \n";
                $stJs .= "f.stMotivo.value           = '".addslashes($arConcessao['stMotivo'])."';\n";
                $stJs .= "f.nuQuantidade.value       = '".number_format($arConcessao['nuQuantidade'], 2, ",", ".")."';      \n";
                $stJs .= "f.nuValorTotal.value       = '".number_format($arConcessao['nuValorTotal'], 2, ",", ".")."';      \n";
                //$stJs .= "f.dtPagamento.value        = '".$arConcessao['dtPagamento']."';       \n";
                $stJs .= "f.stAssinatura.value       = '".addslashes($arConcessao['stAssinatura'])."';";
                $stJs .= "f.btAlterarConcessao.disabled = false;";
                $stJs .= "f.btIncluirConcessao.disabled = true;";
            }
        }//

        return $stJs;
    }
}

function preencherDetalhesNorma($nuNormaExercicio, $preencheDescricao = true)
{
    //Verifica Norma
    $arCodNorma = explode("/",$nuNormaExercicio);
    $stNumNorma = ltrim($arCodNorma[0], "0");
    if ($stNumNorma=="") {
        $stNumNorma = "0";
    }
    $stFiltroNorma = " WHERE num_norma='".$stNumNorma."' and exercicio='".$arCodNorma[1]."'";
    $obTNorma = new TNorma();
    $obTNorma->recuperaTodos($rsNorma, $stFiltroNorma);
    if ($rsNorma->getNumLinhas() > 0) {
        $stCodNorma = $nuNormaExercicio;
        $stDataPublicacao = $rsNorma->getCampo('dt_publicacao');
        if ($preencheDescricao) {
            $stFiltroTipoNorma = " WHERE cod_tipo_norma = ".$rsNorma->getCampo('cod_tipo_norma');
            $obTTipoNorma = new TTipoNorma();
            $obTTipoNorma->recuperaTodos($rsTipoNorma, $stFiltroTipoNorma);
            $stNorma = $rsTipoNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma')."/".$rsNorma->getCampo('exercicio')." - ".$rsNorma->getCampo('nom_norma');
        }
    } else {
        $stDataPublicacao = "&nbsp;";
        $stNorma = "&nbsp;";
        $nuExercicioNorma = "";
    }
    $stJs  = "d.getElementById('dtAto').innerHTML = '".$stDataPublicacao."';\n";
    $stJs .= "d.getElementById('stNorma').innerHTML = '".addslashes($stNorma)."';\n";
    $stJs .= "f.stCodNorma.value = '".$stCodNorma."';\n";

    return $stJs;
}

function preencherValorDiaria($inCodTipo, $nuValorQuantidade = '', $boPreencherValorTotal = false)
{
    if ($inCodTipo != "") {
        $stFiltroTipoDiaria = " AND cod_tipo = ".$inCodTipo;
        $obTDiariasTipoDiaria = new TDiariasTipoDiaria;
        $obTDiariasTipoDiaria->recuperaTipoDiariaEmVigencia($rsTipoDiaria, $stFiltroTipoDiaria);

        if ($rsTipoDiaria->getNumLinhas()>0) {
            $nuValor = number_format($rsTipoDiaria->getCampo('valor'), 2, ",", ".");
            $stJs .= "d.getElementById('nuValorDiariaFormatado').innerHTML = 'R$ ".$nuValor."'; \n";
            $stJs .= "f.nuValorDiaria.value = '".number_format($rsTipoDiaria->getCampo('valor'), 2, ".", "")."'; \n";
            $stJs .= "f.inCodTipo.value = '".$rsTipoDiaria->getCampo('cod_tipo')."'; \n";

            if ($boPreencherValorTotal) {
                $stJs .= preencherValorTotal($nuValorQuantidade, $nuValor);
            }
        }
    }

    return $stJs;
}

function preencherValorTotal($nuQuantidade, $nuValorDiaria)
{
    $nuQuantidade = str_replace(",", ".", str_replace(".", "", $nuQuantidade));
    $nuQuantidade = $nuQuantidade*1;

    $nuValorDiaria = $nuValorDiaria*1;

    if ($nuQuantidade > 0) {
        $nuValorTotal = $nuQuantidade*$nuValorDiaria;
        $stJs .= "f.nuValorTotal.value = '".number_format($nuValorTotal, 2, ",", ".")."';\n";
    }
    $stJs .= "f.nuValorTotal.value = '".number_format($nuValorTotal, 2, ",", ".")."';\n";

    return $stJs;
}

function limparForm()
{
    Sessao::write('arConcessoes', array());
    $stJs .= montaListaConcessoes(array());

    return $stJs;
}

function preencherPais($inCodEstado = "", $inCodMunicipio = "")
{
    if ($inCodEstado == "") {
        $inCodEstado = Sessao::read('inCodEstadoConfiguracao');
    }

    if ($inCodEstado) {
        $obTUF = new TUF();
        $stFiltroPais = " WHERE cod_uf = ".$inCodEstado;
        $obTUF->recuperaTodos($rsUF, $stFiltroPais);
        if ($rsUF->getNumLinhas()>0) {
            $inCodPais = $rsUF->getCampo('cod_pais');

            $stJs .= "limpaSelect( document.getElementById('inCodEstado'),1);\n";
            $stJs .= "limpaSelect( document.getElementById('inCodMunicipio'),1);\n";

            $obUF = new TUF();
            $stFiltro = " WHERE cod_pais = ".$inCodPais;
            $obUF->recuperaTodos( $rsLista, $stFiltro );
            $inContador = 0;
            while (!$rsLista->eof()) {
                $inId        = $rsLista->getCampo("cod_uf");
                $stDescricao = $rsLista->getCampo("nom_uf");
                $stJs .= "document.getElementById('inCodEstado').options[".++$inContador."] = new Option('".addslashes($stDescricao)."','$inId');\n";
                $rsLista->proximo();
            }

            $obMunicipio = new TMunicipio();
            $stFiltro = " WHERE cod_uf = ".$inCodEstado;
            $obMunicipio->recuperaTodos( $rsLista, $stFiltro );
            $inContador = 0;
            while (!$rsLista->eof()) {
                $inId        = $rsLista->getCampo("cod_municipio");
                $stDescricao = $rsLista->getCampo("nom_municipio");
                $stJs .= "document.getElementById('inCodMunicipio').options[".++$inContador."] = new Option('".addslashes($stDescricao)."','$inId');\n";
                $rsLista->proximo();
            }

            $stJs .= "f.inCodPais.value = '".$inCodPais."';";
            $stJs .= "f.inCodEstado.value = '".$inCodEstado."';";
            $stJs .= "f.inCodMunicipio.value = '".$inCodMunicipio."';";
        }
    }

    return $stJs;
}

function preencherEmpenho($stAutorizacaoEmpenho, $dtAutorizacaoEmpenho)
{
    $obLblAutorizacaoEmpenho = new Label();
    $obLblAutorizacaoEmpenho->setRotulo( "Autorização Empenho" );
    $obLblAutorizacaoEmpenho->setValue($stAutorizacaoEmpenho);

    $obLblDataPagamentoAutorizacaoEmpenho = new Label();
    $obLblDataPagamentoAutorizacaoEmpenho->setRotulo( "Data do Pagamento" );
    $obLblDataPagamentoAutorizacaoEmpenho->setValue($dtAutorizacaoEmpenho);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo( "Informações de Pagamento das Diárias");
    $obFormulario->addComponente( $obLblAutorizacaoEmpenho );
    $obFormulario->addComponente( $obLblDataPagamentoAutorizacaoEmpenho );
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stHtml = str_replace("\n","",$stHtml);

    $stJs .= "jQuery('#spnEmpenho').html('".$stHtml."');\n";

    return $stJs;
}

function submeter()
{
    $obErro = new Erro();

    /*if ($_REQUEST['dt_pagamento']) {
        $obErro->setDescricao($obErro->getDescricao()."@Data de Pagamento deve ser igual ou superior à data atual!");
    }*/

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','aviso','".Sessao::getId()."');";
    } else {
        $stJs .= "f.submit(); //BloqueiaFrames(true,false);\n";
    }

    return $stJs;
}

/*
function dataContida($dtInicioPeriodo, $dtFimPeriodo, $dtComparacao)
{
    if (SistemaLegado::comparaDatas($dtComparacao, $dtInicioPeriodo) && SistemaLegado::comparaDatas($dtFimPeriodo, $dtComparacao)) {
        return true;
    }

    return false;
}
*/

function dataContida($stInicioPeriodo, $stFimPeriodo, $stComparacao)
{
    $arPartesInicioPeriodo  = explode("#", $stInicioPeriodo);
    $arPartesTerminoPeriodo = explode("#", $stFimPeriodo);
    $arPartesComparacao     = explode("#", $stComparacao);

    $arDataInicioPeriodo  = explode("/", $arPartesInicioPeriodo[0]);
    $arDataTerminoPeriodo = explode("/", $arPartesTerminoPeriodo[0]);
    $arDataComparacao     = explode("/", $arPartesComparacao[0]);

    $arTimeInicioPeriodo  = explode(":", $arPartesInicioPeriodo[1]);
    $arTimeTerminoPeriodo = explode(":", $arPartesTerminoPeriodo[1]);
    $arTimeComparacao     = explode(":", $arPartesComparacao[1]);

    $inTimestampInicioPeriodo  = mktime((int) $arTimeInicioPeriodo[0] , (int) $arTimeInicioPeriodo[1] , 0, (int) $arDataInicioPeriodo[1] , (int) $arDataInicioPeriodo[0] , (int) $arDataInicioPeriodo[2] );
    $inTimestampTerminoPeriodo = mktime((int) $arTimeTerminoPeriodo[0], (int) $arTimeTerminoPeriodo[1], 0, (int) $arDataTerminoPeriodo[1], (int) $arDataTerminoPeriodo[0], (int) $arDataTerminoPeriodo[2]);
    $inTimestampComparacao     = mktime((int) $arTimeComparacao[0]    , (int) $arTimeComparacao[1]    , 0, (int) $arDataComparacao[1]    , (int) $arDataComparacao[0]    , (int) $arDataComparacao[2]  );

    if ($inTimestampComparacao >= $inTimestampInicioPeriodo && $inTimestampTerminoPeriodo >= $inTimestampComparacao) {
        return true;
    }

    return false;
}

function comparaDatas($stInicioPeriodo, $stFimPeriodo)
{
    $arPartesInicioPeriodo  = explode("#", $stInicioPeriodo);
    $arPartesTerminoPeriodo = explode("#", $stFimPeriodo);

    $arDataInicioPeriodo  = explode("/", $arPartesInicioPeriodo[0]);
    $arDataTerminoPeriodo = explode("/", $arPartesTerminoPeriodo[0]);

    $arTimeInicioPeriodo  = explode(":", $arPartesInicioPeriodo[1]);
    $arTimeTerminoPeriodo = explode(":", $arPartesTerminoPeriodo[1]);

    $inTimestampInicioPeriodo  = mktime((int) $arTimeInicioPeriodo[0] , (int) $arTimeInicioPeriodo[1] , 0, (int) $arDataInicioPeriodo[1] , (int) $arDataInicioPeriodo[0] , (int) $arDataInicioPeriodo[2] );
    $inTimestampTerminoPeriodo = mktime((int) $arTimeTerminoPeriodo[0], (int) $arTimeTerminoPeriodo[1], 0, (int) $arDataTerminoPeriodo[1], (int) $arDataTerminoPeriodo[0], (int) $arDataTerminoPeriodo[2]);

    if ($inTimestampTerminoPeriodo > $inTimestampInicioPeriodo) {
        return true;
    }

    return false;
}

function preencherQuantidadeValorDiarias()
{
    $obErro = new Erro();

    if ($_REQUEST['dtInicio']  != "" &&
       $_REQUEST['dtTermino'] != "") {

        $arConcessoes = ( is_array(Sessao::read('arConcessoes')) ) ? Sessao::read('arConcessoes') : array();
        foreach ($arConcessoes as $arConcessao) {
            if (addslashes($arConcessao['stAssinatura']) != $_REQUEST['stAssinatura']) {
                if(
                             dataContida($arConcessao['dtInicio']."#".$arConcessao['hrInicio'], $arConcessao['dtTermino']."#".$arConcessao['hrTermino'], $_REQUEST['dtInicio']."#".$_REQUEST['hrInicio']) ||
                             dataContida($arConcessao['dtInicio']."#".$arConcessao['hrInicio'], $arConcessao['dtTermino']."#".$arConcessao['hrTermino'], $_REQUEST['dtTermino']."#".$_REQUEST['hrTermino']) ||

                             dataContida($_REQUEST['dtInicio']."#".$_REQUEST['hrInicio'], $_REQUEST['dtTermino']."#".$_REQUEST['hrTermino'], $arConcessao['dtInicio']."#".$arConcessao['hrInicio']) ||
                             dataContida($_REQUEST['dtInicio']."#".$_REQUEST['hrInicio'], $_REQUEST['dtTermino']."#".$_REQUEST['hrTermino'], $arConcessao['dtTermino']."#".$arConcessao['hrTermino']) ||

                             ($_REQUEST['dtInicio']."#".$_REQUEST['hrInicio'] == $arConcessao['dtInicio']."#".$arConcessao['hrInicio'] &&
                              $_REQUEST['dtTermino']."#".$_REQUEST['hrTermino'] == $arConcessao['dtTermino']."#".$arConcessao['hrTermino'])
                        ){

                    $obErro->setDescricao("O Período da Viagem selecionado está em conflito com a diária concedida em ".$arConcessao['dtInicio']." (Saída ".$arConcessao['hrInicio'].") Lei/Decreto ".$arConcessao['nuNormaExercicio']);
                    break;
                }
            } else {
                if (!comparaDatas($_REQUEST['dtInicio']."#".$_REQUEST['hrInicio'],$_REQUEST['dtTermino']."#".$_REQUEST['hrTermino'])) {
                    $obErro->setDescricao("No campo Período da Viagem, a Data Inicial deve ser maior que a Data de Término");
                    break;
                }
            }
        }//end foreach

        if (!$obErro->ocorreu()) {

            $dtInicio  = substr($_REQUEST['dtInicio'],6)."-".substr($_REQUEST['dtInicio'],3,2)."-".substr($_REQUEST['dtInicio'],0,2);
            $dtTermino = substr($_REQUEST['dtTermino'],6)."-".substr($_REQUEST['dtTermino'],3,2)."-".substr($_REQUEST['dtTermino'],0,2);

            $nuQuantidade = SistemaLegado::datediff('d', $dtInicio, $dtTermino);
            $nuQuantidade = ($nuQuantidade > 0)?$nuQuantidade-1:0;

            if ($_REQUEST['hrInicio']  != "" &&
               $_REQUEST['hrTermino'] != "") {

               if ($dtInicio != $dtTermino) {
                   $inDiferencaMinutosSaida   = 1440 - (substr($_REQUEST['hrInicio'],0,2)*60 + substr($_REQUEST['hrInicio'],3,2)*1);
                   $inDiferencaMinutosRetorno = substr($_REQUEST['hrTermino'],0,2)*60 + substr($_REQUEST['hrTermino'],3,2)*1;
               } else {
                    if ((int) str_replace(":", "", $_REQUEST['hrTermino']) > (int) str_replace(":", "", $_REQUEST['hrInicio'])) {
                       $inDiferencaMinutosSaida   = 0;
                       $inDiferencaMinutosRetorno = (substr($_REQUEST['hrTermino'],0,2)*60 + substr($_REQUEST['hrTermino'],3,2)*1) - (substr($_REQUEST['hrInicio'],0,2)*60 + substr($_REQUEST['hrInicio'],3,2)*1);
                    }
               }

               if ($inDiferencaMinutosSaida >= 360 && $inDiferencaMinutosSaida <= 719) {
                    $nuQuantidade += 0.5;
               } elseif ($inDiferencaMinutosSaida > 719) {
                    $nuQuantidade += 1;
               }

               if ($inDiferencaMinutosRetorno >= 360 && $inDiferencaMinutosRetorno <= 719) {
                    $nuQuantidade += 0.5;
               } elseif ($inDiferencaMinutosRetorno > 719) {
                    $nuQuantidade += 1;
               }
            }
            $stJs .= "jQuery('#nuQuantidade').val('".number_format($nuQuantidade,2,",",".")."');";
            $stJs .= "if ( jQuery('#nuValorDiaria').val() !='' ) { jQuery('#nuValorTotal').val('".number_format($_REQUEST['nuValorDiaria']*$nuQuantidade, 2, ",", ".")."'); }";
        }
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','aviso','".Sessao::getId()."');";
    }

    return $stJs;
}

switch ($stCtrl) {
    case 'atualizaSpanFiltro':
        $stJs .= atualizaSpanFiltro();
        break;
    case "incluirConcessao":
        $stJs = incluirConcessao();
        break;
    case "excluirConcessao":
        $stJs = excluirConcessao();
        break;
    case "alterarConcessao":
        $stJs = alterarConcessao();
        break;
    case "preencherConcessoes":
        $stJs = preencherConcessoes($_REQUEST['inCodContrato']);
        break;
    case "preencherAlteraConcessao":
        $stJs = preencherAlteraConcessao();
        break;
    case "preencherQuantidadeValorDiarias":
        $stJs = preencherQuantidadeValorDiarias();
        break;
    case "submeter":
        $stJs = submeter();
        break;
    case "limparForm":
        $stJs = limparForm();
        break;
    case "preencherValorDiaria":
        $stJs = preencherValorDiaria($_REQUEST['inCodTipo'], $_REQUEST['nuQuantidade'], $_REQUEST['boPreencherValorTotal']);
        break;
    case "preencherDetalhesNorma":
        $stJs = preencherDetalhesNorma($_REQUEST['nuExercicioNorma'], true);
        break;
    case "preencherValorTotal":
        $stJs = preencherValorTotal($_REQUEST['nuQuantidade'], $_REQUEST['nuValorDiaria']);
        break;
    case "preencherPais":
        $stJs = preencherPais();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
