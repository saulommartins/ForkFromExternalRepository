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
  * Página Oculta da Configuração de Leis do LDO
  * Data de Criação: 15/01/2014

  * @author Analista: Ane Pereira
  * @author Desenvolvedor: Arthur Cruz

  * @ignore
  *
  * $Id: OCManterConfiguracaoLeisLDO.php 61768 2015-03-03 13:08:43Z michel $

  * $Revision: $
  * $Name: $
  * $Author: $
  * $Date: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function processarForm($boExecuta = false, $stArquivo = "Form", $stAcao = "incluir")
{
    switch ($stAcao) {
        case "incluir":
            $stJs .= preencheLeisConsultaLDO();
            $stJs .= buscaNormas();
            $stJs .= montaListaNorma();
        break;
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function incluirNorma()
{
    include_once CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php";
    include_once CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php";
    include_once CAM_GA_NORMAS_MAPEAMENTO."TNormaDataTermino.class.php";

    $obErro = new Erro();
    $obTNorma = new TNorma();
    $obTTipoNorma = new TTipoNorma();
    $obTNormaDataTermino = new TNormaDataTermino();

    if ($_REQUEST['hdnCodTipoNorma'] == "" || $_REQUEST['stCodNorma'] == "") {
        $obErro->setDescricao("Informe o Tipo de Norma e a Norma!");
    } else {

        $arCodNorma = explode("/",$_REQUEST["stCodNorma"]);
        $stFiltroTipoNorma = " WHERE cod_tipo_norma = ".$_REQUEST['hdnCodTipoNorma'];

        $stFiltroNorma  = " WHERE cod_tipo_norma = ".$_REQUEST['hdnCodTipoNorma'];
        $stFiltroNorma .= "   AND num_norma = '".(int) $arCodNorma[0]."'";

        $obTNorma->recuperaNormas($rsRecordSetNorma, $stFiltroNorma);
        $obTTipoNorma->recuperaTodos($rsRecordSetTipoNorma, $stFiltroTipoNorma);

        $stFiltroDataTermino = " WHERE cod_norma = ".$rsRecordSetNorma->getCampo('cod_norma');
        $obTNormaDataTermino->recuperaTodos($rsRecordSetDataTermino, $stFiltroDataTermino);
        $arNormas = Sessao::read('arNormas');

        $arNorma = array();
        $arNorma['stNomTipoNorma']          =   $rsRecordSetTipoNorma->getCampo('nom_tipo_norma');
        $arNorma['stNorma']                 =   $rsRecordSetNorma->getCampo('num_norma_exercicio')." - ".$rsRecordSetNorma->getCampo('nom_norma');
        $arNorma['dtAssinatura']            =   $rsRecordSetNorma->getCampo('dt_assinatura_formatado');
        $arNorma['dtTermino']               =   $rsRecordSetDataTermino->getCampo('dt_termino');
        $arNorma['dtPublicacao']            =   $rsRecordSetNorma->getCampo('dt_publicacao');
        $arNorma['inCodNorma']              =   $rsRecordSetNorma->getCampo('cod_norma');
        $arNorma['inCodTipoNorma']          =   $rsRecordSetNorma->getCampo('cod_tipo_norma');
        $arNorma['stNomNorma']              =   $rsRecordSetNorma->getCampo('nom_norma');
        $arNorma['stDescricao']             =   $rsRecordSetNorma->getCampo('descricao');
        $arNorma['stExercicio']             =   $rsRecordSetNorma->getCampo('exercicio');
        $arNorma['inNumNorma']              =   $rsRecordSetNorma->getCampo('num_norma');
        $arNorma['inId']                    =   count($arNormas);

        if ($arNormas != "") {
            foreach ($arNormas as $arrNorma) {
                if ($arrNorma['stTipoNorma'] == $arNorma['stTipoNorma'] && $arrNorma['stNorma'] == $arNorma['stNorma']) {
                    $obErro->setDescricao("Esta norma já está na lista!");
                }
            }
        }
    }

    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $arNormas[] = $arNorma;
        Sessao::write('arNormas',$arNormas);
        $stJs .= montaListaNorma();
    }

    $stJs .= "f.hdnCodTipoNorma.value               = '';\n";
    $stJs .= "f.stCodNorma.value                    = '';\n";
    $stJs .= "d.getElementById('stNorma').innerHTML = '&nbsp;';\n";

    return $stJs;
}

function buscaNormas()
{
    include_once CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php";
    include_once CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php";
    include_once CAM_GA_NORMAS_MAPEAMENTO."TNormaDataTermino.class.php";

    $obErro = new Erro();
    $obTNorma = new TNorma();
    $obTTipoNorma = new TTipoNorma();
    $obTNormaDataTermino = new TNormaDataTermino();

    $arCodNormas = Sessao::read('arCodNorma');
    if ($arCodNormas != "") {
        foreach ($arCodNormas as $norma) {
            if ($norma['inCodTipoNorma'] == "" || $norma['inCodNorma'] == "") {
                $obErro->setDescricao("Informe o Tipo de Norma e a Norma!");
        }

        $stFiltroTipoNorma = " WHERE cod_tipo_norma = ".$norma['inCodTipoNorma'];

        $stFiltroNorma  = " WHERE cod_tipo_norma = ".$norma['inCodTipoNorma'];
        $stFiltroNorma .= "   AND cod_norma = ".$norma['inCodNorma'];

        $stFiltroDataTermino = " WHERE cod_norma = ".$norma['inCodNorma'];

        $obTNorma->recuperaNormas($rsRecordSetNorma, $stFiltroNorma);
        $obTTipoNorma->recuperaTodos($rsRecordSetTipoNorma, $stFiltroTipoNorma);
        $obTNormaDataTermino->recuperaTodos($rsRecordSetDataTermino, $stFiltroDataTermino);

        $arNorma = array();
        $arNorma['stNomTipoNorma']          =   $rsRecordSetTipoNorma->getCampo('nom_tipo_norma');
        $arNorma['stNorma']                 =   $rsRecordSetNorma->getCampo('num_norma_exercicio')." - ".$rsRecordSetNorma->getCampo('nom_norma');
        $arNorma['dtAssinatura']            =   $rsRecordSetNorma->getCampo('dt_assinatura_formatado');
        $arNorma['dtTermino']               =   $rsRecordSetDataTermino->getCampo('dt_termino');
        $arNorma['dtPublicacao']            =   $rsRecordSetNorma->getCampo('dt_publicacao');
        $arNorma['inCodNorma']              =   $rsRecordSetNorma->getCampo('cod_norma');
        $arNorma['inCodTipoNorma']          =   $rsRecordSetNorma->getCampo('cod_tipo_norma');
        $arNorma['stNomNorma']              =   $rsRecordSetNorma->getCampo('nom_norma');
        $arNorma['stDescricao']             =   $rsRecordSetNorma->getCampo('descricao');
        $arNorma['stExercicio']             =   $rsRecordSetNorma->getCampo('exercicio');
        $arNorma['inNumNorma']              =   $rsRecordSetNorma->getCampo('num_norma');
        $arNorma['inId']                    =   count($arNormas);

        $arNormas[] = $arNorma;
    }
        Sessao::write('arNormas',$arNormas);
    } else {
        if (Sessao::read('arNormas') != '') {
            $stJs .= montaListaNorma();
            sistemaLegado::executaFrameOculto($stJs);
        }
    }
}

function preencheLeisConsultaLDO()
{
    include_once CAM_GF_LDO_MAPEAMENTO.'TLDOHomologacao.class.php';
    $obTLDOHomologacao = new TLDOHomologacao();
    $obTLDOHomologacao->recuperaLDOPorAnoPPANorma($rsLDO);
    
    $stNomeLeiLDO = '&nbsp';
    while (!$rsLDO->eof()) {
        $stNomeLeiLDO = $rsLDO->getCampo("descricao_norma_ldo");
        
        $rsLDO->proximo();
    }
    
    $stJs = "d.getElementById('stNomeLeiLDO').innerHTML = '".$stNomeLeiLDO."';\n";

    return $stJs;
}

function montaListaNorma()
{
    $rsRecordSet = new RecordSet();
    if (Sessao::read('arNormas') != "") {
        $rsRecordSet->preenche(Sessao::read('arNormas'));
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Normas/Fundamentação Legal" );

    $obLista->setRecordSet( $rsRecordSet );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo Norma" );
    $obLista->ultimoCabecalho->setWidth( 17 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Norma" );
    $obLista->ultimoCabecalho->setWidth( 37 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Assinatura" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Publicação" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Término" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNomTipoNorma" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNorma" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtAssinatura" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtPublicacao" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtTermino" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirNorma');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnFundamentacaoLegal').innerHTML = '".$stHtml."';";

    return $stJs;
}

function excluirNorma()
{
    $arTemp       = array();
    $arTempRemovido = array();

    $arNormas     = Sessao::read('arNormas');
    $arNormasRemovidos = Sessao::read('arNormasRemovido');

    foreach ($arNormas as $arNorma) {
        if ($arNorma['inId'] != $_GET['inId']) {
            $arTemp[] = $arNorma;
        } else {
            $arNormasRemovidos[] = $arNorma;
        }
    }

    $arNormas = $arTemp;

    Sessao::write('arNormasRemovido', $arNormasRemovidos);
    Sessao::write('arNormas', $arNormas);

    $stJs .= montaListaNorma();

    return $stJs;
}

switch ($request->get('stCtrl')) {
    case "incluirNorma":
        $stJs .= incluirNorma();
    break;
    case "excluirNorma":
        $stJs .= excluirNorma();
    break;
}

if (isset($stJs)) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>
