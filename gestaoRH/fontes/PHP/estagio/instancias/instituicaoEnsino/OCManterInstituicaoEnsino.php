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
    * Página de Oculto do Instituição de Ensino
    * Data de Criação: 02/10/2006

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

//Define o nome dos arquivos PHP
$stPrograma = "ManterInstituicaoEnsino";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function preencherCurso()
{
    include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioCurso.class.php");
    $obTEstagioCurso = new TEstagioCurso();
    $stFiltro = " WHERE cod_grau = ".$_GET['inCodGrau'];
    $obTEstagioCurso->recuperaTodos($rsCurso,$stFiltro);
    $stJs .= "limpaSelect(f.inCodCurso,0);                              \n";
    $stJs .= "f.inCodCurso[0] = new Option('Selecione','','selected');  \n";
    $inIndex = 1;
    while (!$rsCurso->eof()) {
        $stJs .= "f.inCodCurso[$inIndex] = new Option('".$rsCurso->getCampo("nom_curso")."','".$rsCurso->getCampo("cod_curso")."','');  \n";
        $rsCurso->proximo();
        $inIndex++;
    }

    return $stJs;
}

function montaListaCursos($arCursos=array())
{
    $arCursos = ( count($arCursos) ) ? $arCursos : Sessao::read('arCursos');
    $rsCursos = new RecordSet();
    $rsCursos->preenche($arCursos);
    $obLista = new Lista;
    $obLista->setTitulo("Cursos/Área de Conhecimento Vinculadas à Instituição");
    $obLista->setRecordSet( $rsCursos );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Curso");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Grau Curso");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Bolsa");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Avaliação");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "stCurso" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "stGrau" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "nuValorBolsa" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "stMes" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('montaAlterarCurso');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirCurso');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnCursos').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function incluirCurso()
{
    $arCursos = ( is_array(Sessao::read('arCursos')) ? Sessao::read('arCursos') : array());
    $obErro    = new erro;
    if ( !$obErro->ocorreu() ) {
        if ($_GET['inCGM'] == "") {
            $obErro->setDescricao("Campo Instituição inválido!()");
        }
    }
    if ( !$obErro->ocorreu() and count($arCursos)>0 ) {
        foreach ($arCursos as $arCurso) {
            if ($arCurso['inCodCurso'] == $_GET['inCodCurso']) {
                $obErro->setDescricao("O Curso ".$arCurso['stCurso']." já está incluído na lista.");
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioCursoInstituicaoEnsino.class.php");
        $obTEstagioCursoInstituicaoEnsino = new TEstagioCursoInstituicaoEnsino();
        $stFiltro  = " WHERE numcgm = ".$_GET['inCGM'];
        $stFiltro .= "   AND cod_curso = ".$_GET['inCodCurso'];
        $obTEstagioCursoInstituicaoEnsino->recuperaTodos($rsInstituicao,$stFiltro);
        if ( $rsInstituicao->getNumLinhas() > 0 ) {
            include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioCurso.class.php");
            $obTEstagioCurso = new TEstagioCurso();
            $obTEstagioCurso->setDado("cod_curso",$_GET['inCodCurso']);
            $obTEstagioCurso->recuperaPorChave($rsCurso);
            $obErro->setDescricao("O curso ".$rsCurso->getCampo("nom_curso")." já está cadastrado para a instituição ".$_GET['stNomCGM']);
        }
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioCurso.class.php");
        include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioGrau.class.php");
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoMes.class.php");
        $obTEstagioCurso = new TEstagioCurso();
        $obTEstagioCurso->setDado("cod_curso",$_GET['inCodCurso']);
        $obTEstagioCurso->recuperaPorChave($rsCurso);
        $obTEstagioGrau = new TEstagioGrau();
        $obTEstagioGrau->setDado("cod_grau",$_GET['inCodGrau']);
        $obTEstagioGrau->recuperaPorChave($rsGrau);
        $obTAdministracaoMes = new TAdministracaoMes();
        $obTAdministracaoMes->setDado("cod_mes",$_GET['inCodMes']);
        $obTAdministracaoMes->recuperaPorChave($rsMes);

        $arCurso                               = array();
        $arCurso['inId']                       = count($arSessaoCursos);
        $arCurso['inCodCurso']                 = $_GET['inCodCurso'];
        $arCurso['stCurso']                    = $rsCurso->getCampo("nom_curso");
        $arCurso['inCodGrau']                  = $_GET['inCodGrau'];
        $arCurso['stGrau']                     = $rsGrau->getCampo("descricao");
        $arCurso['nuValorBolsa']               = $_GET['nuValorBolsa'];
        $arCurso['inCodMes']                   = $_GET['inCodMes'];
        $arCurso['stMes']                      = trim($rsMes->getCampo("descricao"));

        $arCursos[]                            = $arCurso;
        Sessao::write('arCursos', $arCursos);
        $stJs .= montaListaCursos($arCursos);
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function alterarCurso()
{
    $obErro = new erro;
    $arCursos = ( is_array(Sessao::read('arCursos')) ? Sessao::read('arCursos') : array());
    foreach ($arCursos as $arCurso) {
        if ($arCurso['inCodCurso'] == $_GET['inCodCurso'] and $arCurso['inId'] != Sessao::read('inIdEditar')) {
            $obErro->setDescricao("O Curso ".$arCurso['stCurso']." já está incluído na lista.");
        }
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioCurso.class.php");
        include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioGrau.class.php");
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoMes.class.php");
        $obTEstagioCurso = new TEstagioCurso();
        $obTEstagioCurso->setDado("cod_curso",$_GET['inCodCurso']);
        $obTEstagioCurso->recuperaPorChave($rsCurso);
        $obTEstagioGrau = new TEstagioGrau();
        $obTEstagioGrau->setDado("cod_grau",$_GET['inCodGrau']);
        $obTEstagioGrau->recuperaPorChave($rsGrau);
        $obTAdministracaoMes = new TAdministracaoMes();
        $obTAdministracaoMes->setDado("cod_mes",$_GET['inCodMes']);
        $obTAdministracaoMes->recuperaPorChave($rsMes);

        $arSessaoCursos                        = Sessao::read('arCursos');

        $arCurso                               = array();
        $arCurso['inId']                       = Sessao::read('inIdEditar');
        $arCurso['inCodCurso']                 = $_GET['inCodCurso'];
        $arCurso['stCurso']                    = $rsCurso->getCampo("nom_curso");
        $arCurso['inCodGrau']                  = $_GET['inCodGrau'];
        $arCurso['stGrau']                     = $rsGrau->getCampo("descricao");
        $arCurso['nuValorBolsa']               = $_GET['nuValorBolsa'];
        $arCurso['inCodMes']                   = $_GET['inCodMes'];
        $arCurso['stMes']                      = trim($rsMes->getCampo("descricao"));

        $arSessaoCursos[Sessao::read('inIdEditar')] = $arCurso;
        $stJs .= montaListaCursos($arSessaoCursos);
        Sessao::write('inIdEditar', "");
        $stJs .= "f.btAlterarCurso.disabled = true;     \n";
        $stJs .= "f.btIncluirCurso.disabled = false;    \n";
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirCurso()
{
    include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagio.class.php");
    $obTEstagioEstagiarioEstagio = new TEstagioEstagiarioEstagio();
    $arCursos = ( is_array(Sessao::read('arCursos')) ? Sessao::read('arCursos') : array());
    $arSessaoCursos = array();
    foreach ($arCursos as $arCurso) {
        if ($arCurso['inId'] != $_GET['inId']) {
            $inId = sizeof($arSessaoCursos);
            $arCurso['inId'] = $inId;
            $arSessaoCursos[] = $arCurso;
        } else {
            $stFiltro = " WHERE cod_curso = ".$arCurso['inCodCurso'];
            $obTEstagioEstagiarioEstagio->recuperaTodos($rsEstagio,$stFiltro);
            if ( $rsEstagio->getNumLinhas() > 0 ) {
                $inId = sizeof($arSessaoCursos);
                $arCurso['inId'] = $inId;
                $arSessaoCursos[] = $arCurso;
                $stMensagem = "Existe um estagiário vinculado ao curso ".$arCurso['stCurso']." por esse motivo o mesmo não pode ser excluído.";
                $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
            }
        }
    }
    Sessao::write('arCursos', $arSessaoCursos);
    $stJs .= montaListaCursos($arSessaoCursos);

    return $stJs;
}

function montaAlterarCurso()
{
    Sessao::write('inIdEditar', $_GET['inId']);
    $arCursos = ( is_array(Sessao::read('arCursos')) ? Sessao::read('arCursos') : array());
    $arCurso  = $arCursos[$_GET['inId']];
    if ( is_array($arCurso) ) {
        $_GET['inCodGrau'] = $arCurso['inCodGrau'];
        $stJs .= preencherCurso();
        $stJs .= "f.inCodGrau.value = '".$arCurso['inCodGrau']."';                                  \n";
        $stJs .= "f.inCodCurso.value = '".$arCurso['inCodCurso']."';                                \n";
        $stJs .= "f.nuValorBolsa.value = '".$arCurso['nuValorBolsa']."';                            \n";
        $stJs .= "f.inCodMes.value = '".$arCurso['inCodMes']."';                                    \n";
        $stJs .= "f.btAlterarCurso.disabled = false;                                               \n";
        $stJs .= "f.btIncluirCurso.disabled = true;                                                \n";
    }

    return $stJs;
}

function preencherDados()
{
    $inNumCGM       = "";
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
    $stJs .= montaListaCursos();

    return $stJs;
}

function _Salvar()
{
    if ( count(Sessao::read('arCursos')) ) {
        $stJs .= "parent.frames[2].Salvar();\n";
    } else {
        $stMensagem = "A lista de Cursos/Área de Conhecimento Vinculadas à Instituição não deve estar vazia, insira pelo menos um Cursos/Área de Conhecimento.";
        $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "preencherCurso":
        $stJs .= preencherCurso();
    break;
    case "incluirCurso":
        $stJs .= incluirCurso();
    break;
    case "alterarCurso":
        $stJs .= alterarCurso();
    break;
    case "excluirCurso":
        $stJs .= excluirCurso();
    break;
    case "montaAlterarCurso":
        $stJs .= montaAlterarCurso();
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
