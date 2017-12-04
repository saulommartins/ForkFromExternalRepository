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
    * Página de Oculto do Entidade Intermediadora
    * Data de Criação: 03/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30843 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEntidadeIntermediadora.class.php"                        );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEntidadeIntermediadora";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function montaListaInstituicoes($arInstituicoes=array())
{
    $arInstituicoes = ( count($arInstituicoes) ) ? $arInstituicoes : Sessao::read('arInstituicoes');
    $rsInstituicoes = new RecordSet();
    $rsInstituicoes->preenche($arInstituicoes);
    $rsInstituicoes->setCampo('stAcao', $_REQUEST['stAcao'], true);
    $rsInstituicoes->setCampo('inCGM', $_REQUEST['inCGM'], true);

    $obLista = new Lista;
    $obLista->setTitulo("Instituições de Ensino Vinculadas à Entidade Intermediadora");
    $obLista->setRecordSet( $rsInstituicoes );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Instituição de Ensino");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "stNomCGM" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirInstituicao');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->ultimaAcao->addCampo("2","inCGM");
    $obLista->ultimaAcao->addCampo("3","stAcao");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnInstituicoes').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function incluirInstituicao()
{
    $arInstituicoes = ( is_array(Sessao::read('arInstituicoes')) ? Sessao::read('arInstituicoes') : array());
    $obErro    = new erro;
    if ( !$obErro->ocorreu() ) {
        if ($_GET['inCGM'] == "") {
            $obErro->setDescricao("Campo Entidade Intermediária inválido!()");
        }
    }
    if ( !$obErro->ocorreu() and count($arInstituicoes)>0 ) {
        foreach ($arInstituicoes as $arInstituicao) {
            if ($arInstituicao['inNumCGMInstituicao'] == $_GET['inNumCGMInstituicao']) {
                $obErro->setDescricao("A Instituição ".$arInstituicao['stNomCGM']." já está incluído na lista.");
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioInstituicaoEntidade.class.php");
        $obTEstagioInstituicaoEntidade = new TEstagioInstituicaoEntidade();
        $stFiltro  = " WHERE cgm_entidade    = ".$_GET['inCGM'];
        $stFiltro .= "   AND cgm_instituicao = ".$_GET['inNumCGMInstituicao'];
        $obTEstagioInstituicaoEntidade->recuperaTodos($rsEntidade,$stFiltro);
        if ( $rsEntidade->getNumLinhas() > 0 ) {
            include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioInstituicaoEnsino.class.php");
            $obTEstagioInstituicaoEnsino = new TEstagioInstituicaoEnsino();
            $stFiltro = " AND instituicao_ensino.numcgm = ".$_GET['inNumCGMInstituicao'];
            $obTEstagioInstituicaoEnsino->recuperaRelacionamento($rsInstituicao,$stFiltro);
            $obErro->setDescricao("A instituição de ensino ".$rsInstituicao->getCampo("nom_cgm")." já está cadastrada para a entidade intermeriaria ".$_GET['stNomCGM']);
        }
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GA_CGM_MAPEAMENTO."TCGMCGM.class.php");
        $obTCGMCGM = new TCGMCGM();
        $obTCGMCGM->setDado("numcgm",$_GET['inNumCGMInstituicao']);
        $obTCGMCGM->recuperaPorChave($rsCGM);

        $arInstituicao                          = array();
        $arInstituicao['inId']                  = count($arInstituicoes);
        $arInstituicao['inNumCGMInstituicao']   = $_GET['inNumCGMInstituicao'];
        $arInstituicao['stNomCGM']              = $rsCGM->getCampo("nom_cgm");

        $arInstituicoes[]                       = $arInstituicao;
        Sessao::write('arInstituicoes', $arInstituicoes);
        $stJs .= montaListaInstituicoes($arInstituicoes);
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirInstituicao()
{
    $arInstituicoes = ( is_array(Sessao::read('arInstituicoes')) ? Sessao::read('arInstituicoes') : array());
    $arSessaoInstituicoes = array();
    $obErro = new Erro();
    foreach ($arInstituicoes as $arInstituicao) {
        if ($arInstituicao['inId'] != $_GET['inId']) {
            $inId = sizeof($arSessaoInstituicoes);
            $arInstituicao['inId']  = $inId;
            $arSessaoInstituicoes[] = $arInstituicao;
        } elseif ($_REQUEST['stAcao'] == 'alterar') {
            $obTEstagioEntidadeIntermediadora = new TEstagioEntidadeIntermediadora();
            $stFiltro = " AND entidade_intermediadora.numcgm = ".$_REQUEST['inCGM']." AND entidade_intermediadora_estagio.cgm_instituicao_ensino = ".$arInstituicao['inNumCGMInstituicao'];
            $obTEstagioEntidadeIntermediadora->recuperaEstagiariosDaEntidade($rsEstagiarios,$stFiltro);

            if ($rsEstagiarios->getNumLinhas() > 0) {
                $obErro->setDescricao("Não foi possível excluir a Instituição de Ensino da Entidade Intermediadora, pois existem estagiários relacionados.");
                $arSessaoInstituicoes = $arInstituicoes;
                break;
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        Sessao::write('arInstituicoes', $arSessaoInstituicoes);
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    $stJs .= montaListaInstituicoes($arSessaoInstituicoes);

    return $stJs;
}

function preencherDados()
{
    $inNumCGMInstituicao = "";
    $stNomCGM       = "";
    $stCGM          = "";
    $stCNPJ         = "";
    $stEndereco     = "";
    $stBairro       = "";
    $stCidade       = "";
    $stTelefone     = "";
    if ($_GET['inCGM'] != "") {
        $rsCGM = new RecordSet();
        $rsMunicipio = new RecordSet();
        include_once(CAM_GA_CGM_MAPEAMENTO."TCGMPessoaJuridica.class.php");
        $obTCGMPessoaJuridica = new TCGMPessoaJuridica();
        $stFiltro = " AND sw_cgm.numcgm = ".$_GET['inCGM'];
        $obTCGMPessoaJuridica->recuperaDadosPessoaJuridica($rsCGM,$stFiltro);
        $inNumCGM       = $rsCGM->getCampo("numcgm");
        $stNomCGM       = $rsCGM->getCampo("nom_cgm");
        $stCGM          = $rsCGM->getCampo("numcgm")."-".$rsCGM->getCampo("nom_cgm");
        $stCNPJ         = $rsCGM->getCampo("cnpj");
        $stEndereco     = $rsCGM->getCampo("endereco");
        $stBairro       = $rsCGM->getCampo("bairro");
        $stCidade       = $rsCGM->getCampo("nom_municipio");
        $stTelefone     = $rsCGM->getCampo("fone_comercial");
    }
    $stJs .= "d.getElementById('stCGM').innerHTML = '$stCGM';             \n";
    $stJs .= "f.inCGM.value = '$inNumCGM';             \n";
    $stJs .= "f.stNomCGM.value = '$stNomCGM';             \n";
    $stJs .= "d.getElementById('stCNPJ').innerHTML = '$stCNPJ';             \n";
    $stJs .= "d.getElementById('stEndereco').innerHTML = '$stEndereco';     \n";
    $stJs .= "d.getElementById('stBairro').innerHTML = '$stBairro';         \n";
    $stJs .= "d.getElementById('stCidade').innerHTML = '$stCidade';         \n";
    $stJs .= "d.getElementById('stTelefone').innerHTML = '$stTelefone';     \n";

    return $stJs;
}

function preencheFormAlterar()
{
    $stJs .= preencherDados();
    $stJs .= montaListaInstituicoes();

    return $stJs;
}

function _Salvar()
{
    if ( count(Sessao::read('arInstituicoes')) ) {
        $stJs .= "parent.frames[2].Salvar();\n";
    } else {
        $stMensagem = "A lista de Instituições de Ensino Vinculadas à Entidade Intermediadora não deve estar vazia, insira pelo menos uma Instituição de Ensino.";
        $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "incluirInstituicao":
        $stJs .= incluirInstituicao();
    break;
    case "excluirInstituicao":
        $stJs .= excluirInstituicao();
    break;
    case "preencheFormAlterar":
        $stJs .= preencheFormAlterar();
    break;
    case "_Salvar":
        $stJs .= _Salvar();
    break;
}

if ($stJs) {
    echo $stJs;
}

?>
