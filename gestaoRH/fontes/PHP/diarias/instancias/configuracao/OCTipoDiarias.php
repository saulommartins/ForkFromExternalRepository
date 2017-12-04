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
    * Página de Oculto para Configuração de Tipos de Diárias
    * Data de Criação: 05/08/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: OCTipoDiarias.php 63836 2015-10-22 14:06:51Z franver $

    * Casos de uso: uc-04.09.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_DIA_MAPEAMENTO."TDiariasTipoDiaria.class.php"                                    );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php"                            );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"                                              );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php"                                          );

//Define o nome dos arquivos PHP
$stPrograma = "TipoDiarias";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgDeta = "DT".$stPrograma.".php";

function gerarListaTipoDiaria()
{
    $rsTipoDiaria = new RecordSet();

    $stFiltroTipoDiaria = " WHERE timestamp = (SELECT max(timestamp)
                                                 FROM diarias.tipo_diaria td
                                                WHERE td.cod_tipo = tipo_diaria.cod_tipo) ";
    $obTDiariasTipoDiaria = new TDiariasTipoDiaria();
    $obTDiariasTipoDiaria->recuperaRelacionamento($rsTipoDiaria, "", " nom_tipo ");

    $rsTipoDiaria->addFormatacao('vigencia', 'DATA_BR');
    $arTipoDiaria = array();
    while (!$rsTipoDiaria->eof()) {
        $arTemp = array();
        $arTemp['inId']                = count($arTipoDiaria)+1;
        $arTemp['inCodTipo']           = $rsTipoDiaria->getCampo('cod_tipo');
        $arTemp['stNomeTipoDiaria']    = $rsTipoDiaria->getCampo('nom_tipo');
        $arTemp['stCodNorma']          = $rsTipoDiaria->getCampo('num_norma_exercicio');
        $arTemp['dtPublicacao']        = $rsTipoDiaria->getCampo('dt_publicacao_norma');
        $arTemp['flValorDiaria']       = $rsTipoDiaria->getCampo('valor');
        $arTemp['stMascClassificacao'] = $rsTipoDiaria->getCampo('mascara_classificacao');
        $arTemp['dtDataVigencia']      = $rsTipoDiaria->getCampo('vigencia');

        $arTipoDiaria[] = $arTemp;
        $rsTipoDiaria->proximo();
    }
    Sessao::write("arTipoDiaria",$arTipoDiaria);

    return listarTipoDiaria();
}

function listarTipoDiaria()
{
    global $pgDeta;

    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array(Sessao::read('arTipoDiaria')) ? Sessao::read('arTipoDiaria') : array() );

    if ($rsRecordSet->getNumLinhas() > 0) {

        while (!$rsRecordSet->eof()) {
            if ($rsRecordSet->getCampo('dtPublicacao') == '') {
                //Verifica Norma
                $rsNorma    = new RecordSet();
                $arCodNorma = ltrim($rsRecordSet->getCampo('stCodNorma'), "0");
                if($arCodNorma[0] == "/")
                    $arCodNorma = "0".$arCodNorma;
                $arCodNorma = explode("/",$arCodNorma);
                if (count($arCodNorma)>0) {
                    $stFiltroNorma = " WHERE num_norma='".$arCodNorma[0]."' and exercicio='".$arCodNorma[1]."'";
                    $obTNorma = new TNorma();
                    $obTNorma->recuperaTodos($rsNorma, $stFiltroNorma);
                    if ($rsNorma->getNumLinhas() > 0) {
                        $rsRecordSet->setCampo('dtPublicacao', $rsNorma->getCampo('dt_publicacao'));
                    }
                }
            }

            $stCodNorma = ltrim($rsRecordSet->getCampo('stCodNorma'), "0");
            if($stCodNorma[0] == "/")
                $stCodNorma = "0".$stCodNorma;

            $rsRecordSet->setCampo('stCodNorma', $stCodNorma);
            $rsRecordSet->proximo();
        }

        $rsRecordSet->setPrimeiroElemento();
        $rsRecordSet->addFormatacao('flValorDiaria', 'NUMERIC_BR');

        $obLista = new Lista();
        $obLista->setTitulo("Lista de Tipos de Diárias");
        $obLista->setRecordSet($rsRecordSet);
        $obLista->setMostraPaginacao( false );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Nome");
        $obLista->ultimoCabecalho->setWidth( 25 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Lei/Decreto");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Data Publicação");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Rubrica de Despesa");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Vigência");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "stNomeTipoDiaria" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "stCodNorma" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "dtPublicacao" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "R$ [flValorDiaria]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( " [stMascClassificacao] " );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "dtDataVigencia" );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('montaAlterarTipoDiaria');");
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirTipoDiaria');");
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();

        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs = "d.getElementById('spnListaTiposDiarias').innerHTML = '".$stHtml."';";

    return $stJs;
}

function incluirTipoDiaria()
{
    $arTemp = array();
    $arTipoDiaria = Sessao::read("arTipoDiaria");

    $obErro = new Erro;
    $flValorDiaria = str_replace(",", ".", str_replace(".", "", $_REQUEST['flValorDiaria']))*1;
    if ($flValorDiaria <= 0) {
        $obErro->setDescricao("Campo Valor da Diária inválido()");
    }

    if ( !$obErro->ocorreu() ) {
        foreach ($arTipoDiaria as $obTipoDiaria) {
                if(strtoupper(trim($obTipoDiaria['stNomeTipoDiaria'])) == strtoupper(trim($_REQUEST['stNomeTipoDiaria'])) &&
                  $obTipoDiaria['inId'] != $_REQUEST['inId']){
                        $obErro->setDescricao('Não foi possível inserir o Tipo de Diária. Tipo de Diária com mesmo Nome já existe na lista.');
                        break;
                  }
        }
    }

    if ( !$obErro->ocorreu() ) {
        $inId = ( is_array($arTipoDiaria) ) ? $arTipoDiaria[count($arTipoDiaria)-1]['inId'] +1 : 1;
        $arTemp['inId']                = $inId;
        $arTemp['stNomeTipoDiaria']    = trim($_REQUEST['stNomeTipoDiaria']);
        $arTemp['stCodNorma']          = $_REQUEST['stCodNorma'];
        $arTemp['stMascClassificacao'] = $_REQUEST['stMascClassificacao'];
        $arTemp['flValorDiaria']       = str_replace(",", ".", str_replace(".", "", $_REQUEST['flValorDiaria']));
        $arTemp['inCodTipo']           = "";
        $arTemp['dtDataVigencia']      = $_REQUEST['dtDataVigencia'];

        $arTipoDiaria[]= $arTemp;
        Sessao::write("arTipoDiaria",$arTipoDiaria);
        $stJs .= limparTipoDiaria();
        $stJs .= listarTipoDiaria();
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    SistemaLegado::executaFrameOculto($stJs);
}

function alterarTipoDiaria($boExecuta=false)
{
    $arTemp = array();
    $arTipoDiaria = Sessao::read('arTipoDiaria');

    $obErro = new Erro;
    $flValorDiaria = str_replace(",", ".", str_replace(".", "", $_REQUEST['flValorDiaria']))*1;
    if ($flValorDiaria <= 0) {
        $obErro->setDescricao("Campo Valor da Diária inválido()");
    }

    if ( !$obErro->ocorreu() ) {
        foreach ($arTipoDiaria as $obTipoDiaria) {
                if(strtoupper(trim($obTipoDiaria['stNomeTipoDiaria'])) == strtoupper(trim($_REQUEST['stNomeTipoDiaria'])) &&
                  $obTipoDiaria['inId'] != $_REQUEST['inId']){
                        $obErro->setDescricao('Não foi possível inserir o Tipo de Diária. Tipo de Diária com mesmo Nome já existe na lista.');
                        break;
                  }
        }
    }

    if ( !$obErro->ocorreu() ) {
        $arTemp['inId']                = $_REQUEST['inId'];
        $arTemp['stNomeTipoDiaria']    = trim($_REQUEST['stNomeTipoDiaria']);
        $arTemp['stCodNorma']          = $_REQUEST['stCodNorma'];
        $arTemp['stMascClassificacao'] = $_REQUEST['stMascClassificacao'];
        $arTemp['flValorDiaria']       = str_replace(",", ".", str_replace(".", "", $_REQUEST['flValorDiaria']));
        $arTemp['inCodTipo']           = $_REQUEST['inCodTipo'];
        $arTemp['dtDataVigencia']      = $_REQUEST['dtDataVigencia'];

        $arTipoDiaria[$_REQUEST['inId']-1] = $arTemp;
        Sessao::write("arTipoDiaria",$arTipoDiaria);

        $stJs .= limparTipoDiaria();
        $stJs .= listarTipoDiaria();
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    SistemaLegado::executaFrameOculto($stJs);
}

function excluirTipoDiaria()
{
    $arTemp = array();
    $arTipoDiaria = Sessao::read("arTipoDiaria");
    foreach ($arTipoDiaria as $arTipoDiariaTemp) {
        if ($arTipoDiariaTemp['inId'] != $_GET['inId']) {
            $arTipoDiariaTemp['inId'] = sizeof($arTemp)+1;
            $arTemp[] = $arTipoDiariaTemp;
        }
    }
    Sessao::write('arTipoDiaria',$arTemp);
    $stJs .= listarTipoDiaria();
    $stJs .= limparTipoDiaria(false);

    return $stJs;
}

function montaAlterarTipoDiaria()
{
    $arTipoDiaria = Sessao::read("arTipoDiaria");
    foreach ($arTipoDiaria as $arTipoDiariaTemp) {
        if ($arTipoDiariaTemp['inId'] == $_REQUEST['inId']) {

            //Verifica Norma
            $rsNorma = new RecordSet();

            $arCodNorma = ltrim($arTipoDiariaTemp['stCodNorma'], "0");
            if($arCodNorma[0]=="")
                $arCodNorma = "0".$arCodNorma;
            $arCodNorma = explode("/",$arCodNorma);
            if (count($arCodNorma)>0) {
                $rsNorma  = new RecordSet();
                $stFiltroNorma = " WHERE num_norma='".$arCodNorma[0]."' and exercicio='".$arCodNorma[1]."'";
                $obTNorma = new TNorma();
                $obTNorma->recuperaTodos($rsNorma, $stFiltroNorma);
                if ($rsNorma->getNumLinhas() > 0) {
                    $rsTipoNorma = new RecordSet();
                    $stFiltroTipoNorma = " WHERE cod_tipo_norma = ".$rsNorma->getCampo('cod_tipo_norma');
                    $obTTipoNorma = new TTipoNorma();
                    $obTTipoNorma->recuperaTodos($rsTipoNorma, $stFiltroTipoNorma);
                    $stJs .= "d.getElementById('stNorma').innerHTML = '".$rsTipoNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma')."/".$rsNorma->getCampo('exercicio')." - ".$rsNorma->getCampo('nom_norma')."';\n";
                }
            }

            //Verifica Mascara Classificação - Rubrica Despesa
            if ($arTipoDiariaTemp['stMascClassificacao'] != "") {
                $obROrcamentoClassificacaoDespesa = new ROrcamentoClassificacaoDespesa;
                $obROrcamentoClassificacaoDespesa->setMascClassificacao( $arTipoDiariaTemp['stMascClassificacao'] );
                $obROrcamentoClassificacaoDespesa->listar($rsClassificacaoDespesa);

                if ( $rsClassificacaoDespesa->getNumLinhas() > 0 ) {
                    $stJs .= "f.stMascClassificacao.value = '".$arTipoDiariaTemp['stMascClassificacao']."';\n";
                    $stJs .= 'd.getElementById("stRubricaDespesa").innerHTML = "'.$rsClassificacaoDespesa->getCampo("descricao").'";';
                } else {
                    $stJs .= 'd.getElementById("stRubricaDespesa").innerHTML = "&nbsp;";';
                }
            } else {
                $stJs .= 'd.getElementById("stRubricaDespesa").innerHTML = "&nbsp;";';
            }

            $stJs .= "f.inId.value                = '".$arTipoDiariaTemp['inId']."';            \n";
            $stJs .= "f.inCodTipo.value           = '".$arTipoDiariaTemp['inCodTipo']."';       \n";
            $stJs .= "f.stNomeTipoDiaria.value    = '".$arTipoDiariaTemp['stNomeTipoDiaria']."';\n";
            $stJs .= "f.stCodNorma.value          = '".$arTipoDiariaTemp['stCodNorma']."';      \n";
            $stJs .= "f.dtDataVigencia.value      = '".$arTipoDiariaTemp['dtDataVigencia']."';  \n";
            $stJs .= "f.flValorDiaria.value       = '".number_format($arTipoDiariaTemp['flValorDiaria'], 2, ",", "")."';   \n";
            $stJs .= "f.btnIncluirTipoDiaria.disabled = true;\n";
            $stJs .= "f.btnAlterarTipoDiaria.disabled = false;\n";

            break;
        }
    }

    return $stJs;
}

function limparTipoDiaria($boExecuta = true)
{
    $stJs .= "f.inId.value = '';\n";
    $stJs .= "f.btnIncluirTipoDiaria.disabled = false;\n";
    $stJs .= "f.btnAlterarTipoDiaria.disabled = true;\n";
    $stJs .= "parent.frames[2].limpaFormulario();\n";
    $stJs .= listarTipoDiaria();
    if($boExecuta)
        SistemaLegado::executaFrameOculto($stJs);
    else
        return $stJs;
}

function limparFormulario()
{
    Sessao::write('arTipoDiaria',"");
    $stJs .= "d.getElementById('spnListaTiposDiarias').innerHTML = '&nbsp;';\n";
    $stJs .= limparTipoDiaria();
}

function preencheMascClassificacao($stMascClassificacao)
{
    if ($stMascClassificacao != "") {
        $obROrcamentoClassificacaoDespesa = new ROrcamentoClassificacaoDespesa;
        $obROrcamentoClassificacaoDespesa->setMascClassificacao( $stMascClassificacao );
        $obROrcamentoClassificacaoDespesa->listar($rsClassificacaoDespesa);
        $inNumLinhas = $rsClassificacaoDespesa->getNumLinhas();
        if ($inNumLinhas > 0) {
            $stDescricaoDespesa = $rsClassificacaoDespesa->getCampo("descricao");
            $js .= 'd.getElementById("stRubricaDespesa").innerHTML = "'.$stDescricaoDespesa.'";';
        }
    }

    if ( $stMascClassificacao == "" || ($stMascClassificacao != "" && $inNumLinhas <= 0) ) {
        $js .= 'f.stMascClassificacao'.$stAba.'.value = "";';
        $js .= 'f.stMascClassificacao'.$stAba.'.focus();';
        $js .= 'd.getElementById("stRubricaDespesa'.$stAba.'").innerHTML = "&nbsp;";';
        if($stMascClassificacao != "" && $inNumLinhas <= 0)
            $js .= "alertaAviso('@Valor inválido. (".$stMascClassificacao.").','form','aviso','".Sessao::getId()."');";
    }

    return $js;
}

function submeter()
{
    $obErro = new Erro();

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','aviso','".Sessao::getId()."');";
    } else {
        $stJs .= "f.submit();// BloqueiaFrames(true,false);\n";
    }

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case "gerarListaTipoDiaria":
        $stJs .= gerarListaTipoDiaria();
        break;
    case "incluirTipoDiaria":
        $stJs .= incluirTipoDiaria();
        break;
    case "montaAlterarTipoDiaria":
        $stJs .= montaAlterarTipoDiaria();
        break;
    case "alterarTipoDiaria":
        $stJs .= alterarTipoDiaria();
        break;
    case "excluirTipoDiaria":
        $stJs .= excluirTipoDiaria();
        break;
    case "limparTipoDiaria":
        $stJs .= limparTipoDiaria();
        break;
    case "limparFormulario":
        $stJs .= limparFormulario();
        break;
    case "preencheMascClassificacao":
        $stJs .= preencheMascClassificacao($_REQUEST["stMascClassificacao"]);
        break;
    case "submeter":
        $stJs .= submeter();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
