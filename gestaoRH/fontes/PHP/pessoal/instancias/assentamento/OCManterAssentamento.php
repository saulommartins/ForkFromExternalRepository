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
    * Página de Formulario de Oculto de Assentamento
    * Data de Criação   : 08/06/2005

    * @author Vandré Miguel Ramos

    * @ignore
    $Id: OCManterAssentamento.php 66448 2016-08-30 18:15:38Z michel $

    Caso de uso: uc-04.04.08
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_NEGOCIO."RPessoalVantagem.class.php";
include_once CAM_GRH_PES_NEGOCIO."RPessoalAssentamento.class.php";
include_once CAM_GRH_PES_NEGOCIO."RPessoalCausaRescisao.class.php";
include_once 'JSManterAssentamento.js';

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');
$obRPessoalVantagem             = new RPessoalVantagem;
$obRPessoalAssentamento         = new RPessoalAssentamento($obRPessoalVantagem);
$rsFaixas = new RecordSet;

function listarRescisao()
{
    $rsLista = new RecordSet;
    $obRPessoalVantagem     = new RPessoalVantagem;
    $obRPessoalAssentamento = new RPessoalAssentamento($obRPessoalVantagem);
    $obRPessoalAssentamento->addPessoalCausaRescisao();
    $obRPessoalAssentamento->setCodAssentamento($_REQUEST['inCodAssentamento']);
    $obRPessoalAssentamento->roUltimoPessoalCausaRescisao->listarCausa($rsLista," descricao",$boTransacao);

    $obLista = new Lista;
    $obLista->setRecordSet( $rsLista );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Código" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->ultimoCabecalho->setWidth( 70 );
    $obLista->commitCabecalho();

    $obChkRescisao = new CheckBox;
    $obChkRescisao->setName           ( "inCodAbaRescisao_[cod_causa_rescisao]_"  );
    $obChkRescisao->setValue          ( "true");

    $obLista->addDadoComponente( $obChkRescisao );
    $obLista->ultimoDado->setAlinhamento('CENTRO');
    $obLista->ultimoDado->setCampo( "booleano" );
    $obLista->commitDadoComponente();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "cod_causa_rescisao" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "descricao" );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    // preenche a lista com innerHTML

    $stJs .= "d.getElementById('spnRescisao').innerHTML = '".$stHtml."';";

    $inCount = 0;
    if (($_REQUEST['stAcao'])=='alterar') {
        $stJs .= 'desabilitaNorma();';
        $obRPessoalAssentamento->setCodAssentamento($_REQUEST['inCodAssentamento']);
        $obRPessoalAssentamento->listarAssentamentoCausaRescisao($rsCausa,"","","");
        while (!$rsCausa->eof()) {
                $inCodCausa = $rsCausa->getCampo('cod_causa_rescisao');
                $stJs .='marcaRescisao("'.$inCodCausa.'");';
                $inCount++;
                $rsCausa->proximo();
        }
        if (($inCount > 0)&&($_REQUEST['inCodSefip']=='')) {
            $stJs .= 'desabilitaAfastamento();';
        } elseif (($inCount == '0') && ($_REQUEST['inCodSefip']!='')) {
            $stJs .= 'desabilitaRescisao();';
        }
    }

    return $stJs;

}

function listarFaixas($arRecordSet, $boExecuta=true)
{
    global $obRegra;
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Intervalos Cadastrados" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "De" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Até" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Desconto" );
        $obLista->ultimoCabecalho->setWidth( 80 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inInicioIntervalo" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inFimIntervalo" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "flPercentualDesc" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alteraDado('alteraFaixa');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->ultimaAcao->addCampo("2","inInicioIntervalo");
        $obLista->ultimaAcao->addCampo("3","inFimIntervalo");
        $obLista->ultimaAcao->addCampo("4","flPercentualDesc");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('excluiFaixa');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnFaixas').innerHTML = '".$stHtml."';";
    $stJs .= "f.inIdIntervalo.value = '';";
    $stJs .= "f.inInicioIntervalo.value = '';";
    $stJs .= "f.inFimIntervalo.value = '';";
    $stJs .= "f.flPercentualDesc.value = '';";
    if ($boExecuta==true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function MontaNorma($stSelecionado = "")
{
    global $_POST;
    global $obRPessoalAssentamento;

    $stCombo  = "inCodNorma";
    $stFiltro = "inCodTipoNorma";
    if ($_REQUEST['stAcao'] == 'incluir') {
        $stJs .="d.getElementById('lbldtPublicacao').innerHTML = ''; \n";
        $stJs .="f.dtDataInicioAssentamento.value = ''; \n";
        $stJs .="f.dtDataInicio.value = ''; \n";
    }
    $stJs .= "limpaSelect(f.$stCombo,0); \n";
    $stJs .= "f.$stCombo.options[0] = new Option('Selecione','');\n";
    $stJs .= "f.".$stCombo."Txt.value='$stSelecionado';\n";

    if ($_POST[ $stFiltro ] != "") {
        $inCodTipoNorma = $_POST[ $stFiltro ];
        $obRPessoalAssentamento->obRNorma->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
        $obRPessoalAssentamento->obRNorma->listar( $rsCombo );
        $inCount = 1;
        while (!$rsCombo->eof()) {
            $inId               = str_replace(' ','',$rsCombo->getCampo("num_norma"));
            $stDesc             = $rsCombo->getCampo("nom_norma");
            if ($stSelecionado == $inId) {
                $stSelected = 'selected';
            } else {
                $stSelected = '';
            }
            $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
            $inCount++;
            $rsCombo->proximo();
        }
        $stJs .= "f.".$stCombo.".value = '".$stSelecionado."'; \n";
    }

    return $stJs;
}

function MostraEvento()
{
    global $obRPessoalAssentamento;
    $rsEventosDisponiveis = $rsEventosSelecionados = new RecordSet;
    if ($_REQUEST['stAcao'] == 'alterar') {
        $obRPessoalAssentamento->setCodAssentamento( $_REQUEST['inCodAssentamento'] );
        $obRPessoalAssentamento->listarEventosSelecionados($rsEventosSelecionados,$stFiltro,"",$boTransacao);
    }
    $obRPessoalAssentamento->addEvento();
    $obRPessoalAssentamento->listarEventosDisponiveis($rsEventosDisponiveis,$stOrdem,$boTransacao);

    $obCmbEvento = new SelectMultiplo();
    $obCmbEvento->setName   ('inCodEvento'                          );
    $obCmbEvento->setRotulo ( "Eventos"                             );
    $obCmbEvento->setNull   ( false                                 );
    $obCmbEvento->setTitle  ( "Selecione os eventos relacionados."   );

    // lista disponiveis
    $obCmbEvento->SetNomeLista1 ( 'inCodEventoDisponiveis'      );
    $obCmbEvento->setCampoId1   ( 'cod_evento'                  );
    $obCmbEvento->setCampoDesc1 ( '[codigo]/[descricao]'    );
    $obCmbEvento->setStyle1     ( "width: 300px"                );
    $obCmbEvento->SetRecord1    ( $rsEventosDisponiveis         );

    // lista selecionados
    $obCmbEvento->SetNomeLista2 ( 'inCodEventoSelecionados'     );
    $obCmbEvento->setCampoId2   ( 'cod_evento'                  );
    $obCmbEvento->setCampoDesc2 ( '[codigo]/[descricao]'    );
    $obCmbEvento->setStyle2     ( "width: 300px"                );
    $obCmbEvento->SetRecord2    ( $rsEventosSelecionados        );

    $obFormulario = new Formulario;
    $obFormulario->addComponente ( $obCmbEvento );

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $obFormulario->montaInnerHtml();

    $stJs  = "if (f.boEventoAutomatico.checked == true) {";
    $stJs .= "window.parent.frames['telaPrincipal'].document.getElementById('spnEvento').innerHTML = '".$obFormulario->getHTML()."';" ;
    $stJs .= "f.stEventoEval.value  = '".$stEval."';";
    $stJs .= "} else {";
    $stJs .= "window.parent.frames['telaPrincipal'].document.getElementById('spnEvento').innerHTML = '';" ;
    $stJs .= "f.stEventoEval.value  = '';";
    $stJs .= "}";

    return $stJs;
}

function gerarSpanEventos2()
{
    global $obRPessoalAssentamento;
    $rsEventosDisponiveis  = new RecordSet;
    $rsEventosSelecionados = new RecordSet;

    if ($_REQUEST['stAcao'] == 'alterar') {
        $obRPessoalAssentamento->setCodAssentamento( $_REQUEST['inCodAssentamento'] );
        $obRPessoalAssentamento->listarEventosProporcionaisSelecionados($rsEventosSelecionados,$stFiltro,"",$boTransacao);
        if ( $rsEventosSelecionados->getNumLinhas() > 0 ) {
            $stJs .= "f.boInformarEventosProporcionalizacao.checked = true; ";
        }
    }

    $obRPessoalAssentamento->addEvento();
    $stFiltro = " AND FPE.tipo != 'V'";
    $obRPessoalAssentamento->listarEventosDisponiveisProporcional($rsEventosDisponiveis,$stFiltro,"",$boTransacao);

    $obCmbEvento = new SelectMultiplo();
    $obCmbEvento->setName   ('inCodEvento'                          );
    $obCmbEvento->setRotulo ( "Eventos"                             );
    $obCmbEvento->setNull   ( false                                 );
    $obCmbEvento->setTitle  ( "Selecione os eventos relacionados."   );

    // lista disponiveis
    $obCmbEvento->SetNomeLista1 ( 'inCodEventoProporcionalizacaoDisponiveis'      );
    $obCmbEvento->setCampoId1   ( 'cod_evento'                  );
    $obCmbEvento->setCampoDesc1 ( '[codigo]/[descricao]'    );
    $obCmbEvento->setStyle1     ( "width: 300px"                );
    $obCmbEvento->SetRecord1    ( $rsEventosDisponiveis         );

    // lista selecionados
    $obCmbEvento->SetNomeLista2 ( 'inCodEventoProporcionalizacaoSelecionados'     );
    $obCmbEvento->setCampoId2   ( 'cod_evento'                  );
    $obCmbEvento->setCampoDesc2 ( '[codigo]/[descricao]'    );
    $obCmbEvento->setStyle2     ( "width: 300px"                );
    $obCmbEvento->SetRecord2    ( $rsEventosSelecionados        );

    $obFormulario = new Formulario;
    $obFormulario->addComponente ( $obCmbEvento );

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval  = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval  = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "if (f.boInformarEventosProporcionalizacao.checked == true) {";
    $stJs .= "window.parent.frames['telaPrincipal'].document.getElementById('spnEvento2').innerHTML = '".$obFormulario->getHTML()."';" ;
    $stJs .= "f.stEventoEval2.value  = '".$stEval."';";
    $stJs .= "} else {";
    $stJs .= "window.parent.frames['telaPrincipal'].document.getElementById('spnEvento2').innerHTML = '';" ;
    $stJs .= "f.stEventoEval2.value  = '';";
    $stJs .= "}";

    return $stJs;
}

function desbloqueiaAbas($boExecuta = false)
{
    $boDesbloqueia = true;
    if ($_REQUEST['incluir']) {
        //Verificação do preenchimento dos campos da aba Afastamento Temporário
        $arCamposAfastamentoTemporario = array('inCodSefipTxt','inInicioIntervalo','inFimIntervalo','flPercentualDesc','inQuantidadeDias');
        foreach ($arCamposAfastamentoTemporario as $stNomeCampo) {
            if ($_REQUEST[$stNomeCampo] != "") {
                $boDesbloqueia = false;
                $stAba = "Afastamento Temporário";
            }
        }
        if ( count(Sessao::read('Faixas')) ) {
            $boDesbloqueia = false;
            $stAba = "Afastamento Temporário";
        }
        //Verificação do preenchimento dos campos da aba Afastamento Permanente
        foreach ($_REQUEST as $stKey=>$stValue) {
            if (strstr($stKey,"inCodAbaRescisao_")) {
                $boDesbloqueia = false;
                $stAba = "Afastamento Permanente";
            }
        }
        //Verificação do preenchimento dos campos da aba Vantagem
        $arCamposVantagem = array('dtDataInicio','dtDataEncerramento','inQuantidadeMeses','nuPercentualCorrecao');
        foreach ($arCamposVantagem as $stNomeCampo) {
            if ($_REQUEST[$stNomeCampo] != "") {
                $boDesbloqueia = false;
                $stAba = "Vantagem";
            }
        }
    }
    if ($boDesbloqueia) {
        global $obRPessoalAssentamento;
        $rsClassificacao = new Recordset;

        $inCodClassificacaoTxt = isset($_REQUEST['inCodClassificacaoTxt']) ? $_REQUEST['inCodClassificacaoTxt'] : $_REQUEST['hdnCodClassificacao'];
        $obRPessoalAssentamento->obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento( $inCodClassificacaoTxt );
        $obRPessoalAssentamento->obRPessoalClassificacaoAssentamento->listarClassificacao( $rsClassificacao );
        $stJs .= "f.hdnCodTipo.value = ".$rsClassificacao->getCampo('cod_tipo').";\n";
        $stJs .= "window.parent.frames['telaPrincipal'].document.links['id_layer_1'].href = \"javascript:buscaValor('habilitaLayer1');\";";
        switch ( $rsClassificacao->getCampo('cod_tipo') ) {
            case 2:
                $stJs .= "window.parent.frames['telaPrincipal'].document.links['id_layer_2'].href = \"javascript:buscaValor('habilitaLayer2');\";";
                $stJs .= "window.parent.frames['telaPrincipal'].document.links['id_layer_3'].href = \"javascript:buscaValor('exibeAviso');\";\n";
                $stJs .= "window.parent.frames['telaPrincipal'].document.links['id_layer_4'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            break;
            case 3:
                $stJs .= "window.parent.frames['telaPrincipal'].document.links['id_layer_3'].href = \"javascript:buscaValor('habilitaLayer3');\";";
                $stJs .= "window.parent.frames['telaPrincipal'].document.links['id_layer_2'].href = \"javascript:buscaValor('exibeAviso');\";\n";
                $stJs .= "window.parent.frames['telaPrincipal'].document.links['id_layer_4'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            break;
            case 4:
                $stJs .= "window.parent.frames['telaPrincipal'].document.links['id_layer_4'].href = \"javascript:buscaValor('habilitaLayer4');\";";
                $stJs .= "window.parent.frames['telaPrincipal'].document.links['id_layer_2'].href = \"javascript:buscaValor('exibeAviso');\";\n";
                $stJs .= "window.parent.frames['telaPrincipal'].document.links['id_layer_3'].href = \"javascript:buscaValor('exibeAviso');\";\n";
            break;
        }
        if ($boExecuta) {
            sistemaLegado::executaFrameOculto( $stJs );
        } else {
            return $stJs;
        }
    } else {
        sistemaLegado::exibeAviso("Para alterar a classificação é necessário limpar os campos da Aba $stAba."," "," ");

        return $stJs;
    }
}

function preencheDataNormaSelecionada($boExecuta=false)
{
    global $obRPessoalAssentamento;
    $rsNorma = new Recordset;

    if (!isset($_REQUEST['inCodNorma']) && $_REQUEST['inCodNorma'] != '') {
        $inCodNorma = $_REQUEST['inCodNorma'];
    } else {
        $inCodNorma = $_REQUEST['inCodNormaTxt'];
    }

    $obRPessoalAssentamento->obRNorma->setNumNorma( $inCodNorma );
    $obRPessoalAssentamento->obRNorma->obRTipoNorma->setCodTipoNorma($_REQUEST['inCodTipoNorma']);
    $obRPessoalAssentamento->obRNorma->listar( $rsNorma );

    /* so deve preencher a data de publicação quando achar apenas uma norma (não pq isso de uma norma ), e quando a norma for diferente de ZERO*/
    if (( $rsNorma->getNumLinhas() == 1 ) and ($rsNorma->getCampo('cod_norma') >= 0) ) {
        $stJs .="d.getElementById('lbldtPublicacao').innerHTML = '".$rsNorma->getCampo('dt_publicacao')."'; \n";
    } else {
        $stJs .="d.getElementById('lbldtPublicacao').innerHTML = ''; \n";
    }

    $stJs .="f.hdndtPublicacao.value = '".$rsNorma->getCampo('dt_publicacao')."'; \n";

    if ($_REQUEST['stAcao'] != 'alterar') {
        if ( $rsNorma->getNumLinhas() == 1 ) {
            $dtPublicacao = $rsNorma->getCampo('dt_publicacao');
            $data = DateTime::createFromFormat('d/m/Y', $dtPublicacao);
            $data->add(new DateInterval('P1D')); // 1 dia
            $dtPublicacao = $data->format('d/m/Y'); 

            $stJs .= "f.dtDataInicioAssentamento.value = '".$dtPublicacao."'; \n";
            $stJs .= "f.dtDataInicio.value = '".$dtPublicacao."'; \n";
        } else {
            $stJs .= "f.dtDataInicioAssentamento.value = ''; \n";
            $stJs .= "f.dtDataInicio.value = ''; \n";
        }
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function listarCorrecoes($arRecordSet, $boExecuta=false)
{
    global $obRegra;
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Correções Cadastrados" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Quantidade de Meses" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Percentual de Correção" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inQuantidadeMeses" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nuPercentualCorrecao" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarDado('montaAlterarCorrecao');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarDado('excluirCorrecao');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnCorrecoes').innerHTML = '".$stHtml."';";
    $stJs .= "f.inQuantidadeMeses.value = '';";
    $stJs .= "f.nuPercentualCorrecao.value = '';";
    if ($boExecuta==true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function incluirCorrecao($boExecuta=false)
{
    $stMensagem = false;
    $arElementos = array ();
    $arCorrecoes = array ();
    $rsRecordSet = new Recordset;

    if ( ($_POST['inQuantidadeMeses'] != "") and  ($_POST['nuPercentualCorrecao'] != "") and ($_POST['nuPercentualCorrecao'] != "0,00" ) ) {
        if ( is_array(Sessao::read('Correcoes')) ) {
            $rsRecordSet->preenche( Sessao::read('Correcoes') );
        }
        $rsRecordSet->setUltimoElemento();
        $inUltimoId = $rsRecordSet->getCampo("inId");
        if (!$inUltimoId) {
            $inProxId = 1;
        } else {
            $inProxId = $inUltimoId + 1;
        }
        $ultimoQuantMeses           = $rsRecordSet->getCampo("inQuantidadeMeses");
        $inQuantMeses               = $_POST['inQuantidadeMeses'];
        $ultimoPercentualCorrecao   = $rsRecordSet->getCampo("nuPercentualCorrecao");
        $ultimoPercentualCorrecao   = str_replace(",","",$ultimoPercentualCorrecao);
        $ultimoPercentualCorrecao   = str_replace(".","",$ultimoPercentualCorrecao);
        $nuPercentualCorrecao       = $_POST['nuPercentualCorrecao'];
        $nuPercentualCorrecao       = str_replace(",","",$nuPercentualCorrecao);
        $nuPercentualCorrecao       = str_replace(".","",$nuPercentualCorrecao);

        if ( ($ultimoPercentualCorrecao >= $nuPercentualCorrecao) && (count (Sessao::read("Correcoes")) > 0 )) {
            if ($ultimoPercentualCorrecao == "10000") {
                sistemaLegado::exibeAviso("Impossível incluir mais intervalos de dias, pois o desconto já está em 100,00."," "," ");
            } else {
                sistemaLegado::exibeAviso("O valor referente ao desconto informado deve ser maior que o da última correção cadastrada."," "," ");
            }
        } elseif ( ($ultimoQuantMeses >= $inQuantMeses) && (count (Sessao::read("Correcoes")) > 0 )) {
            sistemaLegado::exibeAviso("O valor informado para a quantidade de meses deve ser maior que o da última correção cadastrada."," "," ");
        } else {
            $arElementos['inId']                    = $inProxId;
            $arElementos['inQuantidadeMeses']       = $_POST['inQuantidadeMeses'];
            $arElementos['nuPercentualCorrecao']    = $_POST['nuPercentualCorrecao'];
            $arCorrecoes[]                          = $arElementos;
            Sessao::write('Correcoes', $arCorrecoes);
            $stJs .= listarCorrecoes( $arCorrecoes );
        }
        if ($boExecuta) {
            sistemaLegado::executaFrameOculto( $stJs );
        } else {
            return $stJs;
        }
    } else {
        if ($_POST['inQuantidadeMeses'] == "") {
            sistemaLegado::exibeAviso("Campo Quantidade de Meses inválido!()"," "," ");
        } else {
            sistemaLegado::exibeAviso("Campo Percentual de Correção inválido!()"," "," ");
        }
    }
}

function montaAlterarCorrecao($boExecuta=false)
{
    $id = $_GET['inId'];
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( Sessao::read('Correcoes') );
    $rsRecordSet->setPrimeiroElemento();
    while (!$rsRecordSet->eof()) {
        if ($rsRecordSet->getCampo('inId') == $id) {
            $stJs .= "f.inQuantidadeMeses.value = ".$rsRecordSet->getCampo('inQuantidadeMeses')."\n;";
            $stJs .= "f.nuPercentualCorrecao.value = '".$rsRecordSet->getCampo('nuPercentualCorrecao')."'\n;";
            $stJs .= "f.inCorrecaoId.value  = $id;\n";
        }
        $rsRecordSet->proximo();
    }
    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function alterarCorrecao($boExecuta=false)
{
    $id                   = $_POST['inCorrecaoId'];
    $inQuantidadeMeses    = $_POST['inQuantidadeMeses'];
    $nuPercentualCorrecao = $_POST['nuPercentualCorrecao'];

    $arCorrecoes = Sessao::read('Correcoes');
    reset($arCorrecoes);
    $arTMP = array();
    $boErro = false;

    $rsCorrecoes = new RecordSet;
    $rsCorrecoes->preenche( $arCorrecoes );
    $rsCorrecoes->setPrimeiroElemento();

    while ( !$rsCorrecoes->eof() ) {
        if ( $rsCorrecoes->getCampo("inId") == $id ) {
            $rsCorrecoes->anterior();
            $inQuantidadeMesesAnterior    = $rsCorrecoes->getCampo("inQuantidadeMeses");
            $nuPercentualCorrecaoAnterior = str_replace(",", "", $rsCorrecoes->getCampo("nuPercentualCorrecao") );
            $rsCorrecoes->proximo();
            $rsCorrecoes->proximo();
            $inQuantidadeMesesProximo    = $rsCorrecoes->getCampo("inQuantidadeMeses");
            $nuPercentualCorrecaoProximo = str_replace(",", "", $rsCorrecoes->getCampo("nuPercentualCorrecao") );
            $rsCorrecoes->anterior();

            if ( ( $nuPercentualCorrecaoProximo != "" ) && ( $nuPercentualCorrecaoProximo <= str_replace(",", "", $nuPercentualCorrecao ) ) ) {
                $boErro = true;
                sistemaLegado::exibeAviso("O percentual de correção deve ser menor que a da correção seguinte."," "," ");
            }
            if ( ( $nuPercentualCorrecaoAnterior != "" ) && ( $nuPercentualCorrecaoAnterior >= str_replace(",", "", $nuPercentualCorrecao ) ) ) {
                $boErro = true;
                sistemaLegado::exibeAviso("O percentual de correção deve ser maior que a da correção anterior."," "," ");
            }
            if ( ($inQuantidadeMesesProximo != "") && ( $inQuantidadeMesesProximo < $inQuantidadeMeses ) ) {
                $boErro = true;
                sistemaLegado::exibeAviso("A quantidade de meses deve ser menor que a da correção seguinte."," "," ");
            }
            if ( ( $inQuantidadeMesesAnterior != "" ) && ( $inQuantidadeMesesAnterior >= $inQuantidadeMeses ) ) {
                $boErro = true;
                sistemaLegado::exibeAviso("A quantidade de meses deve ser maior que a da correção anterior."," "," ");
            }
            $rsCorrecoes->proximo();
        }
        $rsCorrecoes->proximo();
    }

    if (!$boErro) {
        while ( list( $arId ) = each( $arCorrecoes ) ) {
            if ($arCorrecoes[$arId]["inId"] == $id) {
                $arElementos['inId']                            = $id;
                $arElementos['inQuantidadeMeses']               = $_POST["inQuantidadeMeses"];
                $arElementos['nuPercentualCorrecao']            = $_POST["nuPercentualCorrecao"];
                $arTMP[] = $arElementos;
            } else {
                $arTMP[] = $arCorrecoes[$arId];
            }
        }

        Sessao::write('Correcoes', $arTMP);
        $stJs .= listarCorrecoes( $arCorrecoes );
        if ($boExecuta == true) {
            sistemaLegado::executaFrameOculto($stJs);
        } else {
            return $stJs;
        }
    }
}

function excluirCorrecao($boExecuta=false)
{
    $id = $_REQUEST['inId'];
    $inCount = 0;
    $arCorrecoes = Sessao::read('Correcoes');
    reset($arCorrecoes);
    $arTMP = array();

    while ( list( $arId ) = each( $arCorrecoes ) ) {
        if ($arCorrecoes[$arId]["inId"] != $id) {
            $inCount= $inCount + 1;
            $arElementos['inId']                            = $inCount;
            $arElementos['inQuantidadeMeses']               = $arCorrecoes[$arId]["inQuantidadeMeses"];
            $arElementos['nuPercentualCorrecao']            = $arCorrecoes[$arId]["nuPercentualCorrecao"];
            $arTMP[] = $arElementos;
        }
    }
    Sessao::write('Correcoes', $arTMP);
    $stJs .= listarCorrecoes( $arTMP );
    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function limpaSessao($boExecuta=false)
{
    Sessao::write('Faixas', array());
    Sessao::write('Correcoes', array());
}

function limparAba($boExecuta=false)
{
    switch (Sessao::read('stAba')) {
        case 'assentamento':
            $obRPessoalSubDivisao = new RPessoalSubDivisao(new RPessoalRegime);
            $obRPessoalSubDivisao->listarSubDivisao($rsSubDivisao);
            $stJs .= "f.inCodClassificacaoTxt.value = '';                   \n";
            $stJs .= "f.inCodClassificacao.options[0].selected = true;      \n";
            $stJs .= "f.stDescricao.value = '';                             \n";
            $stJs .= "f.stSigla.value = '';                                 \n";
            $stJs .= "f.inCodRegimePrevidencia[0].checked = true;           \n";
            $stJs .= "f.boAssentamentoInicio.checked = false;               \n";
            $stJs .= "f.boGradeEfetividade.checked = false;                 \n";
            $stJs .= "f.boRelFuncaoGratificada.checked = false;             \n";
            $stJs .= "f.inCodTipoNormaTxt.value = '';                       \n";
            $stJs .= "f.inCodTipoNorma.options[0].selected = true;          \n";
            $stJs .= "f.inCodNormaTxt.value = '';                           \n";
            $stJs .= "f.inCodNorma.options[0].selected = true;              \n";
            $stJs .= "f.boEventoAutomatico.checked = false;                 \n";
            $stJs .= "d.getElementById('spnEvento').innerHTML = '';         \n";
            $stJs .= "limpaSelect(f.inCodRegimeDisponiveis,0);              \n";
            $inIndex = 0;
            while ( !$rsSubDivisao->eof() ) {
                $stValue = $rsSubDivisao->getCampo('nom_regime')."/".$rsSubDivisao->getCampo('nom_sub_divisao');
                $stOption= $rsSubDivisao->getCampo('cod_sub_divisao')."/".$rsSubDivisao->getCampo('nom_regime')."/".$rsSubDivisao->getCampo('nom_sub_divisao');
                $stJs .= "f.inCodRegimeDisponiveis.options[".$inIndex."] = new Option('$stValue','$stOption', '');\n";
                $inIndex++;
                $rsSubDivisao->proximo();
            }
            $stJs .= "limpaSelect(f.inCodRegimeSelecionados,0);             \n";
            $stJs .= "f.inCodEsferaTxt.value = '';                          \n";
            $stJs .= "f.inCodEsfera.options[0].selected = true;             \n";
            $stJs .= "f.inCodOperadorTxt.value = '';                        \n";
            $stJs .= "f.inCodOperador.options[0].selected = true;           \n";
            $stJs .= "f.dtDataInicioAssentamento.value = '';                \n";
            $stJs .= "f.dtDataFinalAssentamento.value = '';                 \n";
            $stJs .= "f.boCancelarDireito.checked = false;                  \n";
            $stJs .= "f.boInformarEventosProporcionalizacao.checked = false;\n";
            $stJs .= "d.getElementById('spnEvento2').innerHTML = '';        \n";
            $stJs .= "f.inCodMotivoTxt.value = '';                          \n";
            $stJs .= "f.inCodMotivo.options[0].selected = true;             \n";
            $stJs .= "f.boAssentamentoAutomatico[1].checked = true;         \n";
        break;
        case 'temporario':
            $stJs .= "f.inCodSefipTxt.value = '';                           \n";
            $stJs .= "f.inCodSefip.options[0].selected = true;              \n";
            $stJs .= "f.inQuantidadeDias.value = '';                        \n";
            $stJs .= "f.inInicioIntervalo.value = '';                       \n";
            $stJs .= "f.inFimIntervalo.value = '';                          \n";
            $stJs .= "f.flPercentualDesc.value = '';                        \n";
            $stJs .= "d.getElementById('spnFaixas').innerHTML = '';         \n";
        break;
        case 'permanente':
            $obRPessoalCausaRescisao = new RPessoalCausaRescisao;
            $obRPessoalCausaRescisao->listarCausa($rsCausaRescisao );
            $inIndex = 1;
            while (!$rsCausaRescisao->eof()) {
                $stJs .= "f.inCodAbaRescisao_".$rsCausaRescisao->getCampo('cod_causa_rescisao')."_$inIndex.checked = false; \n";
                $inIndex++;
                $rsCausaRescisao->proximo();
            }
        break;
        case 'vantagem':
            $stJs .= "f.dtDataInicio.value = '';                            \n";
            $stJs .= "f.dtDataEncerramento.value = '';                      \n";
            $stJs .= "f.inQuantidadeMeses.value = '';                       \n";
            $stJs .= "f.nuPercentualCorrecao.value = '';                    \n";
            $stJs .= "d.getElementById('spnCorrecoes').innerHTML = '';      \n";
        break;
    }
    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function habilitaLayer1($boExecuta=false)
{
    Sessao::write('stAba', "assentamento");
    $stJs .= "parent.frames[2].HabilitaLayer('layer_1');";
    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function habilitaLayer2($boExecuta=false)
{
    Sessao::write('stAba', "temporario");
    $stJs .= "parent.frames[2].HabilitaLayer('layer_2');";
    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function habilitaLayer3($boExecuta=false)
{
    Sessao::write('stAba', "permanente");
    $stJs .= "parent.frames[2].HabilitaLayer('layer_3');";
    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function habilitaLayer4($boExecuta=false)
{
    Sessao::write('stAba', "vantagem");
    $stJs .= "parent.frames[2].HabilitaLayer('layer_4');";
    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpanMotivo()
{
    $stHtml = "";
    $stEval = "";
    if($_POST["inCodMotivo"] == 5 OR $_POST["inCodMotivo"] == 6 OR
       Sessao::read("inCodMotivo") == 5 OR Sessao::read("inCodMotivo") == 6){
        $inQuantDiasOnusEmpregador = (Sessao::read("inQuantDiasOnusEmpregador") != "") ? Sessao::read("inQuantDiasOnusEmpregador") : 15;

        $obTxtQuantDiasOnusEmpregador = new Inteiro();
        $obTxtQuantDiasOnusEmpregador->setRotulo("Quantidade de Dias de Afastamento Ônus Empregador");
        $obTxtQuantDiasOnusEmpregador->setTitle("Digite a quantidade de dias de afastamento em que o ônus será do empregador. Exemplo: Afastamento Doença/Acidente Trabalho RGPS (primeiros 15 dias, ônus da empresa).");
        $obTxtQuantDiasOnusEmpregador->setName("inQuantDiasOnusEmpregador");
        $obTxtQuantDiasOnusEmpregador->setValue($inQuantDiasOnusEmpregador);
        $obTxtQuantDiasOnusEmpregador->setSize(3);
        $obTxtQuantDiasOnusEmpregador->setMaxLength(30);
        $obTxtQuantDiasOnusEmpregador->setNull(false);

        $obFormulario = new Formulario;
        $obFormulario->addComponente($obTxtQuantDiasOnusEmpregador);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $obFormulario->obJavaScript->montaJavaScript();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);
    }
    if ($_POST["inCodMotivo"] == 9 or Sessao::read("inCodMotivo") == 9) {
        $inQuantDiasLicencaPremio = (Sessao::read("inQuantDiasLicencaPremio") != "") ? Sessao::read("inQuantDiasLicencaPremio") : "";

        $obTxtQuantDiasLicencaPremio = new Inteiro();
        $obTxtQuantDiasLicencaPremio->setRotulo("Tempo Para Concessão da Licença");
        $obTxtQuantDiasLicencaPremio->setTitle("Informe o tempo para concessão da licença prêmio, em quantidade de dias.");
        $obTxtQuantDiasLicencaPremio->setName("inQuantDiasLicencaPremio");
        $obTxtQuantDiasLicencaPremio->setValue($inQuantDiasLicencaPremio);
        $obTxtQuantDiasLicencaPremio->setSize(5);
        $obTxtQuantDiasLicencaPremio->setNull(false);

        $obFormulario = new Formulario;
        $obFormulario->addComponente($obTxtQuantDiasLicencaPremio);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $obFormulario->obJavaScript->montaJavaScript();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);
    }
    $stJs  = "d.getElementById('spnMotivo').innerHTML = '$stHtml';";
    $stJs .= "f.hdnMotivo.value = '$stEval';";

    return $stJs;
}

function processarOperador()
{
    if ($_POST["inCodEsfera"] == 3) {
        $stJs .= "f.inCodOperadorTxt.readOnly = false;    \n";
        $stJs .= "f.inCodOperador.disabled = false;       \n";
    } else {
        $stJs .= "f.inCodOperadorTxt.value = '1';         \n";
        $stJs .= "f.inCodOperador.value = '1';            \n";
        $stJs .= "f.inCodOperadorTxt.readOnly = true;     \n";
        $stJs .= "f.inCodOperador.disabled = true;        \n";
    }

    return $stJs;
}

// Acoes por pagina
$arFaixas = Sessao::read('Faixas');
$arCorrecoes = Sessao::read('Correcoes');
switch ($stCtrl) {
    case "MostraEvento":
        $stJs = MostraEvento();
    break;

    case "MontaNorma":
        $stJs = MontaNorma();
    break;

    case "MontaFaixa":
        $stMensagem = false;
        $arElementos = array ();
        $rsRecordSet = new Recordset;

        if ( is_array($arFaixas) ) {
            $rsRecordSet->preenche( $arFaixas );
        }
        $rsRecordSet->setUltimoElemento();
        $inUltimoId = $rsRecordSet->getCampo("inId");
        if (!$inUltimoId) {
            $inProxId = 1;
        } else {
            $inProxId = $inUltimoId + 1;
        }
        $ultimoValorIncluido = $rsRecordSet->getCampo("inFimIntervalo");
        $ValorIncluido = $_POST['inInicioIntervalo'];

        $ultimoPercentualIncluido = $rsRecordSet->getCampo("flPercentualDesc");
        $ultimoPercentualIncluido = str_replace(",","",$ultimoPercentualIncluido);
        $ultimoPercentualIncluido = str_replace(".","",$ultimoPercentualIncluido);
        $PercentualIncluido = $_POST['flPercentualDesc'];
        $PercentualIncluido = str_replace(",","",$PercentualIncluido);
        $PercentualIncluido = str_replace(".","",$PercentualIncluido);

        if ( ($ultimoPercentualIncluido >= $PercentualIncluido) && (count(Sessao::read("Faixas")) > 0 )) {
            if($ultimoPercentualIncluido == "10000")
                sistemaLegado::exibeAviso("Impossível incluir mais intervalos de dias, pois o desconto já está em 100,00."," "," ");
            else
                sistemaLegado::exibeAviso("O intervalo de dias informado deve ser maior que o da última faixa cadastrada."," "," ");
        } elseif ( ($ultimoValorIncluido >= $ValorIncluido) && (count (Sessao::read("Faixas")) > 0 )) {
            sistemaLegado::exibeAviso("A quantidade de dias informada no início deste intervalo deve ser maior que o intervalo final da faixa anterior."," "," ");
        } else {
            $arElementos['inId']             = $inProxId;
            $arElementos['inInicioIntervalo']= $_POST['inInicioIntervalo'];
            $arElementos['inFimIntervalo']   = $_POST['inFimIntervalo'];
            $arElementos['flPercentualDesc'] = $_POST['flPercentualDesc'];
            $arFaixas[]      = $arElementos;
            listarFaixas( $arFaixas );
        }
    break;

    case "MontaFaixaAlteracao":
        $id = $_POST['inIdIntervalo'];

        $stMensagem = false;
        $arElementos = array ();
        $rsRecordSet = new Recordset;
        $rsRecordSet->preenche( $arFaixas );

        $ValorInicioIncluido = $_POST['inInicioIntervalo'];
        $ultimoValorIncluido = $arFaixas[($id-2)]["inFimIntervalo"];
        if($ultimoValorIncluido=="") $ultimoValorIncluido = $ValorInicioIncluido - 1;

        $PercentualIncluido         = $_POST['flPercentualDesc'];
        $PercentualIncluido         = str_replace(",","",$PercentualIncluido);
        $PercentualIncluido         = str_replace(".","",$PercentualIncluido);
        $ultimoPercentualIncluido   = $arFaixas[($id-2)]["flPercentualDesc"];
        $ultimoPercentualIncluido   = str_replace(",","",$ultimoPercentualIncluido);
        $ultimoPercentualIncluido   = str_replace(".","",$ultimoPercentualIncluido);
        if($ultimoPercentualIncluido=="") $ultimoPercentualIncluido = $PercentualIncluido - 1;
        $proximoPercentualIncluido  = $arFaixas[($id)]["flPercentualDesc"];
        $proximoPercentualIncluido  = str_replace(",","",$proximoPercentualIncluido);
        $proximoPercentualIncluido  = str_replace(".","",$proximoPercentualIncluido);
        if($proximoPercentualIncluido=="") $proximoPercentualIncluido = $PercentualIncluido + 1;

        $ValorFimIncluido = $_POST['inFimIntervalo'];
        $proximoValorIncluido = $arFaixas[($id)]["inInicioIntervalo"];
        if($proximoValorIncluido=="") $proximoValorIncluido = $ValorFimIncluido + 1;

        if ( ($ultimoPercentualIncluido >= $PercentualIncluido) && (count($arFaixas) > 0 )) {
           if($ultimoPercentualIncluido == "10000")
               sistemaLegado::exibeAviso("Impossível incluir mais intervalos de dias, pois o desconto já está em 100,00."," "," ");
           else
                sistemaLegado::exibeAviso("O valor referente ao desconto informado deve ser maior que o da faixa anterior."," "," ");
        } elseif ( ($proximoPercentualIncluido <= $PercentualIncluido) && (count ($arFaixas) > 0 )) {
            sistemaLegado::exibeAviso("O valor referente ao desconto informado deve ser menor que o da faixa seguinte."," "," ");
        } elseif ( ($ultimoValorIncluido >= $ValorInicioIncluido) && (count ($arFaixas) > 0 )) {
            sistemaLegado::exibeAviso("A quantidade de dias informada no início deste intervalo deve ser maior que o intervalo final da faixa anterior."," "," ");
        } elseif ( ($proximoValorIncluido <= $ValorFimIncluido) && (count ($arFaixas) > 0 )) {
            sistemaLegado::exibeAviso("O valor informado para o salário final deve ser menor que o salário inicial da faixa seguinte."," "," ");
        } else {
            $idMaior = $arFaixas[($id)]["inInicioIntervalo"];
            reset($arFaixas);
            while ( list( $arId ) = each( $arFaixas ) ) {
                if ($arFaixas[$arId]["inId"] != $id) {
                    $arElementos['inId']                = $arFaixas[$arId]["inId"];
                    $arElementos['inInicioIntervalo']   = $arFaixas[$arId]["inInicioIntervalo"];
                    $arElementos['inFimIntervalo']      = $arFaixas[$arId]["inFimIntervalo"];
                    $arElementos['flPercentualDesc']    = $arFaixas[$arId]["flPercentualDesc"];
                    $arTMP[] = $arElementos;
                } else {
                    $arElementos['inId']                = $arFaixas[$arId]["inId"];
                    $arElementos['inInicioIntervalo']   = $_POST['inInicioIntervalo'];
                    $arElementos['inFimIntervalo']      = $_POST['inFimIntervalo'];
                    $arElementos['flPercentualDesc']    = $_POST['flPercentualDesc'];
                    $arTMP[] = $arElementos;
                }
            }
            Sessao::write('Faixas', $arTMP);
            listarFaixas( $arTMP );
        }
    break;

    case "excluiFaixa":
        $id = $_GET['inId'];
        $stMensagem = false;
        if ($_REQUEST['stAcao']=='alterar') {
            reset($arFaixas);
            while ( list( $arId ) = each( $arFaixas ) ) {
                if ($arFaixas["inId"] == $id) {
                    $obRegra->setCodAssentamento    ( $_POST['inCodAssentamento'] );
                    $obRegra->obRFaixa->setCodFaixa ( $arFaixas["inCodFaixa"] );
                }
            }
        }

        if ($stMensagem==false) {
            reset($arFaixas);
            $cont=0;
            while ( list( $arId ) = each( $arFaixas ) ) {
                if ($arFaixas[$arId]["inId"] != $id) {

                    //código para deixar os registro com ordem sequencial
                    $cont++;
                    $arFaixas[$arId]["inId"] = $cont;

                    $arElementos['inId']                = $arFaixas[$arId]["inId"];
                    $arElementos['inInicioIntervalo']   = $arFaixas[$arId]["inInicioIntervalo"];
                    $arElementos['inFimIntervalo']      = $arFaixas[$arId]["inFimIntervalo"];
                    $arElementos['flPercentualDesc']    = $arFaixas[$arId]["flPercentualDesc"];
                    $arTMP[] = $arElementos;
                }
            }
            $arFaixas = $arTMP;
            listarFaixas( $arTMP );
        } else {
            $stJs = "sistemaLegado::alertaAviso('@ ($stMensagem)','form','erro','".Sessao::getId()."');";
        }
    break;

    case "excluiTodaFaixa":
        if (count($arFaixas) > 0) {
            reset($arFaixas);
            while ( list( $arId ) = each( $arFaixas ) ) { }
            Sessao::write('Faixas', $arTMP);
            listarFaixas( $arTMP );
        }
    break;

    case 'preencheInner':
        if ( count( $arFaixas ) ) {
            $stJs = listarFaixas( $$arFaixas, false );
        }
        $stJs .= MontaNorma(Sessao::read('inCodNormaTxt'));
        $stJs .= listarRescisao();
        $stJs .= MostraEvento();
        $stJs .= gerarSpanEventos2();
        $stJs .= desbloqueiaAbas();
        $stJs .= preencheDataNormaSelecionada();
        $stJs .= listarCorrecoes($arCorrecoes);
        $stJs .= gerarSpanMotivo();
        $stJs .= processarOperador();
    break;

    case 'listarRescisao':
        $stJs .= listarRescisao();
    break;

    case 'exibeAviso':
        sistemaLegado::exibeAviso("Esta aba não está relacionada à classificação selecionada."," "," ");
    break;

    case 'desbloqueiaAbas':
        $stJs .= desbloqueiaAbas();
    break;

    case 'preencheDataNormaSelecionada':
        $stJs .= preencheDataNormaSelecionada();
    break;

    case 'incluirCorrecao':
        $stJs .= incluirCorrecao();
    break;

    case 'montaAlterarCorrecao':
        $stJs .= montaAlterarCorrecao();
    break;

    case 'alterarCorrecao':
        $stJs .= alterarCorrecao();
    break;

    case 'excluirCorrecao':
        $stJs .= excluirCorrecao();
    break;
    case 'limpaSessao':
        $stJs .= limpaSessao();
    break;
    case 'gerarSpanEventos2':
        $_REQUEST["stAcao"] = "";
        $stJs .= gerarSpanEventos2();
    break;
    case 'limparAba':
        $stJs .= limparAba();
    break;
    case 'habilitaLayer1':
        $stJs .= habilitaLayer1();
    break;
    case 'habilitaLayer2':
        $stJs .= habilitaLayer2();
    break;
    case 'habilitaLayer3':
        $stJs .= habilitaLayer3();
    break;
    case 'habilitaLayer4':
        $stJs .= habilitaLayer4();
    break;
    case "gerarSpanMotivo":
        $stJs .= gerarSpanMotivo();
        break;
    case "processarOperador":
        $stJs = processarOperador();
        break;
}
if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

if ($_REQUEST['stAcao'] == 'alterar') {
    SistemaLegado::LiberaFrames();
}
?>
