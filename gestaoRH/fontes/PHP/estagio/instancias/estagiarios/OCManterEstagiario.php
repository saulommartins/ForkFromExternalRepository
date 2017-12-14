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
    * Página de Oculto do Estagiário
    * Data de Criação: 04/10/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.07.01

    $Id: OCManterEstagiario.php 65967 2016-07-04 19:59:54Z diogo.zarpelon $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterEstagiario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function preencherDadosEstagiario()
{
    $stRG           = "";
    $stCPF          = "";
    $stEndereco     = "";
    $stCelular      = "";
    $stTelefone     = "";
    $stNomePai      = "";
    $stNomeMae      = "";
    if ($_GET['inCGM'] != "") {
        $rsCGM = new RecordSet();
        $rsMunicipio = new RecordSet();
        include_once(CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php");
        $obTCGMPessoaFisica = new TCGMPessoaFisica();
        $stFiltro = " AND CGM.numcgm = ".$_GET['inCGM'];
        $obTCGMPessoaFisica->recuperaRelacionamento($rsCGM,$stFiltro);
        if ( $rsCGM->getNumLinhas() > 0 ) {
            $stRG           = $rsCGM->getCampo("rg");
            $stCPF          = $rsCGM->getCampo("cpf");
            $stEndereco     = addslashes($rsCGM->getCampo("logradouro"))." ".$rsCGM->getCampo("numero").", ".addslashes($rsCGM->getCampo("bairro")).", ".addslashes($rsCGM->getCampo("nom_municipio")).", ".$rsCGM->getCampo("sigla_uf");
            $stCelular      = $rsCGM->getCampo("fone_celular");
            $stTelefone     = $rsCGM->getCampo("fone_residencial");
        }
        include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiario.class.php");
        $obTEstagioEstagiario = new TEstagioEstagiario();
        $obTEstagioEstagiario->setDado("numcgm",$_GET['inCGM']);
        $obTEstagioEstagiario->recuperaPorChave($rsPaiMae);
        $stNomeMae = $rsPaiMae->getCampo("nom_mae");
        $stNomePai = $rsPaiMae->getCampo("nom_pai");
    }
    $stJs .= "d.getElementById('stRG').innerHTML = '$stRG';                 \n";
    $stJs .= "d.getElementById('stCPF').innerHTML = '$stCPF';               \n";
    $stJs .= "d.getElementById('stEndereco').innerHTML = '$stEndereco';     \n";
    $stJs .= "d.getElementById('stCelular').innerHTML = '$stCelular';       \n";
    $stJs .= "d.getElementById('stTelefone').innerHTML = '$stTelefone';     \n";
    $stJs .= "f.stNomePai.value = '$stNomePai';                             \n";
    $stJs .= "f.stNomeMae.value = '$stNomeMae';                             \n";

    return $stJs;
}

function preencherSpanInstituicaoEntidade()
{
    $stHTML = "";
    if ($_GET['stVinculo'] == "i") {
        include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioInstituicaoEnsino.class.php"                             );
        $obTEstagioInstituicaoEnsino = new TEstagioInstituicaoEnsino();
        $stFiltro = ( $_GET['stAcao'] == "alterar" ) ? " AND instituicao_ensino.numcgm = ".$_GET['inNumCGMInstituicao'] : "";
        $obTEstagioInstituicaoEnsino->recuperaRelacionamento($rsInstituicao,$stFiltro);
        $rsInstituicao2 = clone $rsInstituicao;

        $obCmbInstituicao = new Select;
        $obCmbInstituicao->setName                    ( "inNumCGMInstituicao"                                                  );
        $obCmbInstituicao->setId                      ( "inNumCGMInstituicao"                                                  );
        $obCmbInstituicao->setValue                   ( $_GET['inNumCGMInstituicao']                                                   );
        $obCmbInstituicao->setRotulo                  ( "Instituição de Ensino"                                                       );
        $obCmbInstituicao->setTitle                   ( "Selecione a instituição de ensino do estágio."                                          );
        $obCmbInstituicao->setNull                    ( false                                                                 );
        $obCmbInstituicao->addOption                  ( "", "Selecione"                                                       );
        $obCmbInstituicao->setCampoId("numcgm");
        $obCmbInstituicao->setCampoDesc("nom_cgm");
        $obCmbInstituicao->setStyle("width: 250px");
        $obCmbInstituicao->preencheCombo($rsInstituicao);
        $obCmbInstituicao->obEvento->setOnChange("montaParametrosGET('preencherGrauCurso','inNumCGMInstituicao');montaParametrosGET('inNumCGMInstituicao,inCodCurso');");

        $obLblInstituicao = new Label();
        $obLblInstituicao->setRotulo("Instituição de Ensino");
        $obLblInstituicao->setValue($rsInstituicao2->getCampo("nom_cgm"));

        $obHdnInstituicao = new Hidden();
        $obHdnInstituicao->setName("inNumCGMInstituicao");
        $obHdnInstituicao->setValue($rsInstituicao2->getCampo("numcgm"));

        $obFormulario = new Formulario();
        if ($_GET['stAcao'] == "incluir") {
            $obFormulario->addComponente($obCmbInstituicao);
        } else {
            $obFormulario->addComponente($obLblInstituicao);
            $obFormulario->addHidden($obHdnInstituicao);
        }
        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();
    } else {
        include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEntidadeIntermediadora.class.php"                             );
        $obTEstagioEntidadeIntermediadora = new TEstagioEntidadeIntermediadora();
        if ($_GET['stAcao'] == "alterar") {
            $obTEstagioEntidadeIntermediadora->setDado("numcgm",$_GET['inNumCGMEntidade']);
        }
        $obTEstagioEntidadeIntermediadora->recuperaEntidadesIntermediarias($rsEntidades);

        $obCmbEntidade = new Select;
        $obCmbEntidade->setName                    ( "inNumCGMEntidade"                                                  );
        $obCmbEntidade->setId                      ( "inNumCGMEntidade"                                                  );
        $obCmbEntidade->setValue                   ( $_GET['inNumCGMEntidade']                                           );
        $obCmbEntidade->setRotulo                  ( "Entidade Intermediadora"                                                       );
        $obCmbEntidade->setTitle                   ( "Selecione a entidade intermediadora do estágio."                                          );
        $obCmbEntidade->setNull                    ( false                                                                 );
        $obCmbEntidade->addOption                  ( "", "Selecione"                                                       );
        $obCmbEntidade->setCampoId("numcgm");
        $obCmbEntidade->setCampoDesc("nom_cgm");
        $obCmbEntidade->preencheCombo($rsEntidades);
        $obCmbEntidade->setStyle("width: 250px");
        $obCmbEntidade->obEvento->setOnChange("montaParametrosGET('preencherInstituicao','inNumCGMEntidade');");

        $obLblEntidade = new Label();
        $obLblEntidade->setRotulo("Entidade Intermediadora");
        $obLblEntidade->setValue($rsEntidades->getCampo("nom_cgm"));

        $obHdnEntidade = new Hidden();
        $obHdnEntidade->setName("inNumCGMEntidade");
        $obHdnEntidade->setValue($rsEntidades->getCampo("numcgm"));

        $obCmbInstituicao = new Select;
        $obCmbInstituicao->setName                    ( "inNumCGMInstituicao"                                                  );
        $obCmbInstituicao->setId                      ( "inNumCGMInstituicao"                                                  );
        $obCmbInstituicao->setValue                   ( $inNumCGMInstituicao                                                   );
        $obCmbInstituicao->setRotulo                  ( "Instituição de Ensino"                                                       );
        $obCmbInstituicao->setTitle                   ( "Selecione a instituição de ensino do estágio."                                          );
        $obCmbInstituicao->setNull                    ( false                                                                 );
        $obCmbInstituicao->addOption                  ( "", "Selecione"                                                       );
        $obCmbInstituicao->setStyle("width: 250px");
        $obCmbInstituicao->obEvento->setOnChange("montaParametrosGET('preencherGrauCurso','inNumCGMInstituicao');montaParametrosGET('preencherMesValorBolsa','inNumCGMInstituicao,inCodCurso');");

        $rsInstituicao = new RecordSet();
        if ($_GET['stAcao'] == "alterar") {
            include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEntidadeIntermediadora.class.php"                             );
            $obTEstagioEntidadeIntermediadora = new TEstagioEntidadeIntermediadora();
            $stFiltro  = " AND entidade_intermediadora.numcgm = ".$_GET['inNumCGMEntidade'];
            $stFiltro .= " AND instituicao_entidade.cgm_instituicao = ".$_GET['inNumCGMInstituicao'];
            $obTEstagioEntidadeIntermediadora->recuperaInstituicoesDaEntidade($rsInstituicao,$stFiltro);
        }
        $obLblInstituicao = new Label();
        $obLblInstituicao->setRotulo("Instituição de Ensino");
        $obLblInstituicao->setValue($rsInstituicao->getCampo("nom_cgm"));

        $obHdnInstituicao = new Hidden();
        $obHdnInstituicao->setName("inNumCGMInstituicao");
        $obHdnInstituicao->setValue($rsInstituicao->getCampo("numcgm"));

        $obFormulario = new Formulario();
        if ($_GET['stAcao'] == "incluir") {
            $obFormulario->addComponente($obCmbEntidade);
            $obFormulario->addComponente($obCmbInstituicao);
        } else {
            $obFormulario->addComponente($obLblEntidade);
            $obFormulario->addHidden($obHdnEntidade);
            $obFormulario->addComponente($obLblInstituicao);
            $obFormulario->addHidden($obHdnInstituicao);
        }
        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnInstituicaoEntidade').innerHTML = '".$stHTML."';     \n";
    $stJs .= "d.getElementById('nuValorBolsa').innerHTML = '&nbsp;';     \n";
    $stJs .= "d.getElementById('nuMesAvaliacao').innerHTML = '&nbsp;';     \n";
    $stJs .= "f.inCodGrau.value = '';\n";
    $stJs .= preencherCurso();

    return $stJs;
}

function preencherForm()
{
    $_GET['stVinculo'] = "i";
    $_GET['stAcao']    = "incluir";
    $stJs .= preencherSpanInstituicaoEntidade();

    return $stJs;
}

function preencherInstituicao()
{
    include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEntidadeIntermediadora.class.php"                             );
    $obTEstagioEntidadeIntermediadora = new TEstagioEntidadeIntermediadora();
    $rsInstituicoes = new RecordSet();
    if ($_GET['inNumCGMEntidade'] != "") {
        $stFiltro = " AND entidade_intermediadora.numcgm = ".$_GET['inNumCGMEntidade'];
        $obTEstagioEntidadeIntermediadora->recuperaInstituicoesDaEntidade($rsInstituicoes,$stFiltro);
    }
    $stJs.= "limpaSelect(f.inNumCGMInstituicao,0);                          \n";
    $stJs.= "f.inNumCGMInstituicao[0] = new Option('Selecione','','selected');\n";
    $inIndex = 1;
    while (!$rsInstituicoes->eof()) {
        $stSelected = ( $rsInstituicoes->getCampo("numcgm") == $_GET['inNumCGMInstituicao'] ) ? "selected" : "";
        $stJs.= "f.inNumCGMInstituicao[".$inIndex."] = new Option('".$rsInstituicoes->getCampo("nom_cgm")."','".$rsInstituicoes->getCampo("numcgm")."','$stSelected');\n";
        $inIndex++;
        $rsInstituicoes->proximo();
    }

    return $stJs;
}

function preencherCurso()
{
    if ($_GET['stAcao'] == "incluir") {
        $stJs .= "limpaSelect(f.inCodCurso,0);                              \n";
        $stJs .= "f.inCodCurso[0] = new Option('Selecione','','selected');  \n";
        if ($_GET['inCodGrau'] != "" and $_GET['inNumCGMInstituicao'] != "") {
            include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioCursoInstituicaoEnsino.class.php");
            $obTEstagioCursoInstituicaoEnsino = new TEstagioCursoInstituicaoEnsino();
            $stFiltro  = " AND cod_grau = ".$_GET['inCodGrau'];
            $stFiltro .= " AND curso_instituicao_ensino.numcgm = ".$_GET['inNumCGMInstituicao'];
            $obTEstagioCursoInstituicaoEnsino->recuperaCursosDeInstituicao($rsCurso,$stFiltro);
            $inIndex = 1;
            while (!$rsCurso->eof()) {
                $stJs .= "f.inCodCurso[$inIndex] = new Option('".$rsCurso->getCampo("nom_curso")."','".$rsCurso->getCampo("cod_curso")."','');  \n";
                $rsCurso->proximo();
                $inIndex++;
            }
        }
    }

    return $stJs;
}

function preencherTurnos()
{
    $stHtml = "";
    if ($_GET['inCodGradeHorario'] != "") {
        include_once ( CAM_GRH_PES_NEGOCIO."RPessoalGradeHorario.class.php" );
        $obRPessoalGradeHorario = new RPessoalGradeHorario();
        $rsFaixaTurno = new RecordSet();
        $obRPessoalGradeHorario->setCodGrade( $_GET['inCodGradeHorario'] );
        $obRPessoalGradeHorario->addFaixaTurno();
        $obRPessoalGradeHorario->roRPessoalFaixaTurno->listarFaixaTurno( $rsFaixaTurno,"" );

        $obLista = new Lista;
        $obLista->setTitulo( "Turnos" );
        $obLista->setRecordSet( $rsFaixaTurno );
        $obLista->setMostraPaginacao( false );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Dia" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Horário de Entrada" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Horário de Saída" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Horário de Entrada2" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Horário de Saída2" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nom_dia");
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "hora_entrada" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "hora_saida" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "hora_entrada_2" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "hora_saida_2" );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    $stJs .= "d.getElementById('spnTurnos').innerHTML = '".$stHtml."';";

    return $stJs;
}
function preencherMesValorBolsa()
{
    $nuValorBolsa = "";
    $nuMesAvaliacao = "&nbsp;";
    $inDiasFaltas = 0;
    if ($_GET['inNumCGMInstituicao'] != "" and $_GET['inCodCurso'] != "") {
        include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioCursoInstituicaoEnsino.class.php");
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoMes.class.php");
        $obTEstagioCursoInstituicaoEnsino = new TEstagioCursoInstituicaoEnsino();
        $obTAdministracaoMes = new TAdministracaoMes();
        $stFiltro  = " AND curso_instituicao_ensino.cod_curso = ".$_GET['inCodCurso'];
        $stFiltro .= " AND curso_instituicao_ensino.numcgm = ".$_GET['inNumCGMInstituicao'];
        $obTEstagioCursoInstituicaoEnsino->recuperaCursosDeInstituicao($rsCurso,$stFiltro);

        $obTAdministracaoMes->setDado("cod_mes",$rsCurso->getCampo("cod_mes"));
        $obTAdministracaoMes->recuperaPorChave($rsMes);
        if ( $rsCurso->getNumLinhas() > 0 ) {
            $rsBolsa = new recordset();
            if ($_GET['inCGM'] != "" and $_GET['inCodEstagio'] != "") {
                include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagioBolsa.class.php");
                $obTEstagioEstagiarioEstagioBolsa = new TEstagioEstagiarioEstagioBolsa();
                $stFiltro  = " AND estagiario_estagio_bolsa.cgm_estagiario = ".$_GET['inCGM'];
                $stFiltro .= " AND estagiario_estagio_bolsa.cod_curso = ".$rsCurso->getCampo("cod_curso");
                $stFiltro .= " AND estagiario_estagio_bolsa.cgm_instituicao_ensino = ".$rsCurso->getCampo("numcgm");
                $stFiltro .= " AND estagiario_estagio_bolsa.cod_estagio = ".$_GET['inCodEstagio'];
                $obTEstagioEstagiarioEstagioBolsa->recuperaRelacionamento($rsBolsa,$stFiltro,'timestamp desc');
                $inDiasFaltas = $rsBolsa->getCampo("faltas");
            }
            if ($_GET['stAcao'] != "alterar") {
                $nuValorBolsa = number_format($rsCurso->getCampo("vl_bolsa"),2,',','.');
            } else {
                $nuValorBolsa = number_format($rsBolsa->getCampo("vl_bolsa"),2,',','.');
            }
            $nuMesAvaliacao = $rsMes->getCampo("descricao");
        }
    }
    $stJs .= "d.getElementById('nuValorBolsa').value = '".$nuValorBolsa."';";
    $stJs .= liberaValeRefeicao($nuValorBolsa);
    $stJs .= liberaValeTransporte($nuValorBolsa);
    $stJs .= "f.inDiasFaltas.value = '".$inDiasFaltas."';\n";
    if ($inDiasFaltas > 0) {
        $_GET["inDiasFaltas"] = $inDiasFaltas;
        $_GET["nuValorBolsa"] = $nuValorBolsa;
        $stJs .= preencherNovoValorBolsa();
    }
    $stJs .= "d.getElementById('nuMesAvaliacao').innerHTML = '".$nuMesAvaliacao."';";

    return $stJs;
}

function _Salvar()
{    
    $obErro = new Erro();
    if (!$obErro->ocorreu()) {
        if ($_GET['inCodBancoTxt'] != "" and $_GET['stNumAgenciaTxt'] == "") {
            $obErro->setDescricao("A agência do banco deve ser informada!");
        }
    }
    if (!$obErro->ocorreu()) {
        $stJs .= "parent.frames[2].Salvar();\n";
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function verificaCodigoEstagio()
{
    $obErro = new Erro();

    if ( !$obErro->ocorreu() and !((int) $_GET['inCodEstagio'] > 0) ) {
        $obErro->setDescricao("O código do estágio deve ser maior de zero.");
    }
    if ( !$obErro->ocorreu() and $_GET['inCodEstagio'] != "" ) {
        include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagio.class.php" );
        $obTEstagioEstagiarioEstagio = new TEstagioEstagiarioEstagio();
        $stFiltro = " WHERE numero_estagio = '".$_GET['inCodEstagio']."'";
        $obTEstagioEstagiarioEstagio->recuperaTodos($rsEstagio,$stFiltro);
        if ( $rsEstagio->getNumLinhas() != -1 ) {
            $obErro->setDescricao("O código ".$_GET['inCodEstagio']." já está sendo utilizado.");
        }
    }
    if ( $obErro->ocorreu() ) {
        $stJs .= "f.inCodEstagio.value = '';\n";
        $stJs .= "f.inCodEstagio.focus();   \n";
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function preencherFormAlterar(Request $request)
{
    include_once(CAM_GT_MON_INSTANCIAS."agenciaBancaria/OCMontaAgencia.php");
    $request->set('stAcao', 'alterar');
    $stJs .= preencherDadosEstagiario();
    $stJs .= preencherSpanInstituicaoEntidade();
    $stJs .= preencherMesValorBolsa();
    $stJs .= preencherTurnos();
    $stJs .= PreencheAgencia($request);

    $stJs .= "f.inCodBanco.value = '".$request->get('stNumBanco')."';\n";
    $stJs .= "f.inCodBancoTxt.value = '".$request->get('stNumBanco')."';\n";
    $stJs .= "f.stNumAgencia.value = '".$request->get('stNumAgencia')."';\n";
    $stJs .= "f.stNumAgenciaTxt.value = '".$request->get('stNumAgencia')."';\n";
    $stJs .= "f.stContaCorrente.value = '".$request->get('stContaCorrente')."';\n";
    $stJs .= "f.inCodLocal.value = '".$request->get('inCodLocal')."';\n";
    $stLocal = ( $request->get('stLocal') != "" ) ? $request->get('stLocal') : "&nbsp;";
    $stJs .= "d.getElementById('stLocal').innerHTML = '".$stLocal."';\n";

    return $stJs;
}

function preencherGrauCurso()
{
    $stJs .= "limpaSelect(f.inCodGrau,0);                              \n";
    $stJs .= "f.inCodGrau[0] = new Option('Selecione','','selected');  \n";
    $stJs .= "limpaSelect(f.inCodCurso,0);                              \n";
    $stJs .= "f.inCodCurso[0] = new Option('Selecione','','selected');  \n";
    if ($_GET['inNumCGMInstituicao'] != "") {
        include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioInstituicaoEnsino.class.php");
        $obTEstagioInstituicaoEnsino = new TEstagioInstituicaoEnsino();
        $obTEstagioInstituicaoEnsino->setDado("numcgm",$_GET['inNumCGMInstituicao']);
        $obTEstagioInstituicaoEnsino->recuperaGrausDeInstituicaoEnsino($rsGraus);
        $inIndex = 1;
        while (!$rsGraus->eof()) {
            $stJs .= "f.inCodGrau[$inIndex] = new Option('".$rsGraus->getCampo("descricao")."','".$rsGraus->getCampo("cod_grau")."','');  \n";
            $inIndex++;
            $rsGraus->proximo();
        }
    }

    return $stJs;
}

function validarDataFim()
{
    if (trim($_GET['dtInicioEstagio']) != "" and trim($_GET['dtFimEstagio']) != "") {
        if ( SistemaLegado::comparaDatas($_GET['dtInicioEstagio'],$_GET['dtFimEstagio']) ) {
            $stJs .= "f.dtFimEstagio.value = '';\n";
            $stJs .= "f.dtFimEstagio.focus();\n";
            $stJs .= "alertaAviso('Data Fim do Estágio anterior a Data Início do Estágio.','form','erro','".Sessao::getId()."');\n";
        }
    }

    return $stJs;
}

function validarDataRenovacao()
{
    if ( SistemaLegado::comparaDatas($_GET['dtFimEstagio'],$_GET['dtRenovacaoEstagio']) ) {
        $stJs .= "f.dtRenovacaoEstagio.value = '';\n";
        $stJs .= "f.dtRenovacaoEstagio.focus();\n";
        $stJs .= "alertaAviso('Data da Renovação do Estágio anterior a Data Fim do Estágio.','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function limpaZerosAEsquerda()
{
    ;
    $stJs .= "stValor = limpaZerosAEsquerda('".trim($_GET['inCodEstagio'])."');  									          \n";
    $stJs .= "f.inCodEstagio.value = stValor; 																		          \n";

    return $stJs;
}

function preencherNovoValorBolsa()
{
    $stHTML = "";
    if ($_GET["boVR"]=="false") {
        $stJs = liberaValeRefeicao($_GET["nuValorBolsa"]);
    }
    if ($_GET["boVT"]=="false") {
        $stJs = liberaValeTransporte($_GET["nuValorBolsa"]);
    }
    if ($_GET["inDiasFaltas"] != "") {
        if ($_GET["inDiasFaltas"] > 30) {
            $inDiasFaltas = 30;
            $stJs = "f.inDiasFaltas.value = 30;\n";
        } else {
            $inDiasFaltas = $_GET["inDiasFaltas"];
        }

        $nuValorBolsa = str_replace('.','',$_GET["nuValorBolsa"]);
        $nuValorBolsa = str_replace(',','.',$nuValorBolsa);
        $nuValorBolsa = (float) $nuValorBolsa;

        $stNovoValorBolsa = number_format($nuValorBolsa - (($nuValorBolsa/30)*$inDiasFaltas),2,',','.');

        $obLblNovoValorBolsa = new label;
        $obLblNovoValorBolsa->setRotulo("Novo Valor da Bolsa");
        $obLblNovoValorBolsa->setValue($stNovoValorBolsa);

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obLblNovoValorBolsa);
        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnNovoValorBolsa').innerHTML = '".$stHTML."';";

    return $stJs;
}

function liberaValeRefeicao($nuValorBolsa)
{
    $rsVR = new RecordSet();
    if ($nuValorBolsa != "") {
        $stJs .= "d.getElementById('boVRSim').disabled = false;\n";
        $stJs .= "d.getElementById('boVRNao').disabled = false;\n";
        if ($_GET["stAcao"] == "alterar") {
            include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioCursoInstituicaoEnsino.class.php");
            $obTEstagioCursoInstituicaoEnsino = new TEstagioCursoInstituicaoEnsino();
            $stFiltro  = " AND curso_instituicao_ensino.cod_curso = ".$_GET['inCodCurso'];
            $stFiltro .= " AND curso_instituicao_ensino.numcgm = ".$_GET['inNumCGMInstituicao'];
            $obTEstagioCursoInstituicaoEnsino->recuperaCursosDeInstituicao($rsCurso,$stFiltro);

            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

            include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioValeRefeicao.class.php");
            $obTEstagioEstagiarioValeRefeicao = new TEstagioEstagiarioValeRefeicao();
            $stFiltro  = " AND estagiario_vale_refeicao.cgm_estagiario = ".$_GET['inCGM'];
            $stFiltro .= " AND estagiario_vale_refeicao.cod_curso = ".$rsCurso->getCampo("cod_curso");
            $stFiltro .= " AND estagiario_vale_refeicao.cgm_instituicao_ensino = ".$rsCurso->getCampo("numcgm");
            $stFiltro .= " AND estagiario_vale_refeicao.cod_estagio = ".$_GET['inCodEstagio'];
            $stFiltro .= " AND estagiario_estagio_bolsa.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
            $obTEstagioEstagiarioValeRefeicao->recuperaRelacionamento($rsVR,$stFiltro);

            $obTEstagioEstagiarioEstagioBolsa = new TEstagioEstagiarioEstagioBolsa();
            $stFiltro  = " AND estagiario_estagio_bolsa.cgm_estagiario = ".$_GET['inCGM'];
            $stFiltro .= " AND estagiario_estagio_bolsa.cod_curso = ".$rsCurso->getCampo("cod_curso");
            $stFiltro .= " AND estagiario_estagio_bolsa.cgm_instituicao_ensino = ".$rsCurso->getCampo("numcgm");
            $stFiltro .= " AND estagiario_estagio_bolsa.cod_estagio = ".$_GET['inCodEstagio'];
            $obTEstagioEstagiarioEstagioBolsa->recuperaRelacionamento($rsBolsa,$stFiltro,'timestamp desc');

            if ($rsVR->getNumLinhas() == 1 && $rsBolsa->getCampo("vale_refeicao")=='t') {
                $_GET["boVR"] = "true";
                $stJs .= "d.getElementById('boVRSim').checked = true;\n";
            }
        }
    } else {
        $stJs .= "d.getElementById('boVRNao').checked = true;\n";
        $stJs .= "d.getElementById('boVRSim').disabled = true;\n";
        $stJs .= "d.getElementById('boVRNao').disabled = true;\n";
    }
    $stJs .= preencherSpanValeRefeicao();
    if ($_GET["boVR"] == "true") {
        $stJs .= "f.inQuantVR.value = '".$rsVR->getCampo("quantidade")."';\n";
        $stJs .= "f.nuValorVR.value = '".$rsVR->getCampo("vl_vale")."';\n";
        $stJs .= "f.nuValorDescontoVR.value = '".$rsVR->getCampo("vl_desconto")."';\n";
    }

    return $stJs;
}

function preencherSpanValeRefeicao()
{
    if ($_GET["boVR"] == "true") {
        $obIntQuantVR = new Inteiro();
        $obIntQuantVR->setRotulo("Quantidade do Vale-Refeição");
        $obIntQuantVR->setName("inQuantVR");
        $obIntQuantVR->setId("inQuantVR");
        $obIntQuantVR->setSize(4);
        $obIntQuantVR->setMaxLength(2);
        $obIntQuantVR->setValue(30);
        $obIntQuantVR->setNull(false);
        $obIntQuantVR->setTitle("Informe a quantidade de vales refeição.");

        $obTxtValorVR = new Moeda();
        $obTxtValorVR->setRotulo("Valor do Vale-Refeição a Pagar");
        $obTxtValorVR->setName("nuValorVR");
        $obTxtValorVR->setId("nuValorVR");
        $obTxtValorVR->setTitle("Informe o valor total a pagar de vale-refeição, caso seja pago o valor em pecúnio.");
        $obTxtValorVR->setNull(false);

        $obTxtValorDescontoVR = new Moeda();
        $obTxtValorDescontoVR->setRotulo("Valor do Desconto de Vale-Refeição");
        $obTxtValorDescontoVR->setName("nuValorDescontoVR");
        $obTxtValorDescontoVR->setId("nuValorDescontoVR");
        $obTxtValorDescontoVR->setTitle("Informe o valor do desconto do vale-refeição, caso algum valor seja descontado.");

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obIntQuantVR);
        $obFormulario->addComponente($obTxtValorVR);
        $obFormulario->addComponente($obTxtValorDescontoVR);

        $obFormulario->obJavaScript->montaJavaScript();

        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);

        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnVR').innerHTML = '".$stHTML."';";
    $stJs .= "$('hdnVR').value = '".$stEval."';";

    return $stJs;
}

function liberaValeTransporte($nuValorBolsa)
{
    $rsVT = new RecordSet();
    if ($nuValorBolsa != "") {

        $stJs .= "jQuery('#boVTSim').removeAttr('disabled');\n";
        $stJs .= "jQuery('#boVTNao').removeAttr('disabled');\n";
        $stJs .= "jQuery('#inTipoDiaria').removeAttr('disabled');\n";
        $stJs .= "jQuery('#inTipoMensalFixo').removeAttr('disabled');\n";

        if ($_GET["stAcao"] == "alterar") {

            include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioCursoInstituicaoEnsino.class.php");
            $obTEstagioCursoInstituicaoEnsino = new TEstagioCursoInstituicaoEnsino();
            $stFiltro  = " AND curso_instituicao_ensino.cod_curso = ".$_GET['inCodCurso'];
            $stFiltro .= " AND curso_instituicao_ensino.numcgm = ".$_GET['inNumCGMInstituicao'];
            $obTEstagioCursoInstituicaoEnsino->recuperaCursosDeInstituicao($rsCurso,$stFiltro);

            include_once( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagioBolsa.class.php");
            $obTEstagioEstagiarioEstagioBolsa = new TEstagioEstagiarioEstagioBolsa();
            $stFiltro  = " WHERE estagiario_estagio_bolsa.cgm_estagiario = ".$_GET['inCGM'];
            $stFiltro .= " AND estagiario_estagio_bolsa.cod_curso = ".$rsCurso->getCampo("cod_curso");
            $stFiltro .= " AND estagiario_estagio_bolsa.cgm_instituicao_ensino = ".$rsCurso->getCampo("numcgm");
            $stFiltro .= " AND estagiario_estagio_bolsa.cod_estagio = ".$_GET['inCodEstagio'];

            $obTEstagioEstagiarioEstagioBolsa->recuperaTodos($rsBolsa, $stFiltro,'timestamp desc');

            include_once( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioValeTransporte.class.php");
            $obTEstagioEstagiarioValeTransporte = new TEstagioEstagiarioValeTransporte();
            $stFiltro  = " WHERE estagiario_vale_transporte.cgm_estagiario = ".$_GET['inCGM'];
            $stFiltro .= " AND estagiario_vale_transporte.cod_curso = ".$rsCurso->getCampo("cod_curso");
            $stFiltro .= " AND estagiario_vale_transporte.cgm_instituicao_ensino = ".$rsCurso->getCampo("numcgm");
            $stFiltro .= " AND TO_CHAR(estagiario_vale_transporte.timestamp,'yyyy-mm-dd hh24:mi:ss.us') = '".$rsBolsa->getCampo("timestamp")."'";
            $stFiltro .= " AND estagiario_vale_transporte.cod_estagio = ".$_GET['inCodEstagio'];

            $obTEstagioEstagiarioValeTransporte->recuperaTodos($rsVT,$stFiltro,'timestamp desc');
            $rsVT->addFormatacao('valor_unitario','NUMERIC_BR');

            if ($rsBolsa->getCampo("vale_transporte") == 't') {
                $_GET["boVT"] = "true";
                $stJs .= "jQuery('#boVTSim').attr('checked', 'checked');\n";
            }
        } else {
            $_GET['inTipoContagem'] = '1';
            $stJs .= preencherSpanCalendario();
        }
    } else {
        $stJs .= "jQuery('#boVTNao').attr('checked', 'checked');\n";
        $stJs .= "jQuery('#boVTSim').attr('disabled', 'disabled');\n";
        $stJs .= "jQuery('#boVTNao').attr('disabled', 'disabled');\n";

        $stJs .= "jQuery('#inTipoDiaria').attr('checked', 'checked');\n";
        $stJs .= "jQuery('#inTipoDiaria').attr('disabled', 'disabled');\n";
        $stJs .= "jQuery('#inTipoMensalFixo').attr('disabled', 'disabled');\n";
    }

    $stJs .= preencherSpanValeTransporte();

    if ($_GET["boVT"] == "true") {
        $stJs .= "jQuery('#inQuantVT').val('".$rsVT->getCampo("quantidade")."');\n";
        $stJs .= "jQuery('#nuValorVT').val('".$rsVT->getCampo("valor_unitario")."');\n";
            $_GET['inTipoContagem'] = $rsVT->getCampo("cod_tipo");
        switch ($rsVT->getCampo("cod_tipo")) {
        case '1':
            $stJs .= "jQuery('#inTipoDiaria').attr('checked', 'checked');\n";
            $stJs .= preencherSpanCalendario();
            $stJs .= "jQuery('#inCodCalendario').val('".$rsVT->getCampo("cod_calendar")."');\n";
            $stJs .= "jQuery('#stCalendario').val('".$rsVT->getCampo('cod_calendar')."');\n";
            break;
        case '2':
            $stJs .= "jQuery('#inTipoMensalFixo').attr('checked', 'checked');\n";
            $stJs .= preencherSpanCalendario();
            break;
        }
    }

    return $stJs;
}

function preencherSpanValeTransporte()
{
    if ($_GET["boVT"] == "true") {

        include_once( CAM_GRH_EST_MAPEAMENTO."TEstagioTipoContagemVale.class.php");

        $obTipos = new TEstagioTipoContagemVale();
        $obTipos->recuperaTodos($rsTipos, "", 'cod_tipo');

        $stTextoHint = 'Informe se a contagem dos vales deve ser diária (quantidade de vales por dia, considerando o mês calendário e grade de horários) ou mensalmente (fixa).';
        $stRotulo = "Contagem dos vales";
        $stNomeTipoContagem = 'inTipoContagem';

        $obRdoTipoDiaria = new Radio();
        $obRdoTipoDiaria->setRotulo($stRotulo);
        $obRdoTipoDiaria->setTitle($stTextoHint);
        $obRdoTipoDiaria->setName($stNomeTipoContagem);
        $obRdoTipoDiaria->setId("inTipoDiaria");
        $obRdoTipoDiaria->setValue($rsTipos->getCampo('cod_tipo'));
        $obRdoTipoDiaria->setLabel($rsTipos->getCampo('descricao'));
        $obRdoTipoDiaria->setChecked(true);
        $obRdoTipoDiaria->obEvento->setOnChange("montaParametrosGET('preencherSpanCalendario','".$stNomeTipoContagem.",boVT');");

        $rsTipos->proximo();
        $obRdoTipoMensalFixo = new Radio();
        $obRdoTipoMensalFixo->setRotulo($stRotulo);
        $obRdoTipoMensalFixo->setTitle($stTextoHint);
        $obRdoTipoMensalFixo->setName($stNomeTipoContagem);
        $obRdoTipoMensalFixo->setId("inTipoMensalFixo");
        $obRdoTipoMensalFixo->setValue($rsTipos->getCampo('cod_tipo'));
        $obRdoTipoMensalFixo->setLabel($rsTipos->getCampo('descricao'));
        $obRdoTipoMensalFixo->obEvento->setOnChange("montaParametrosGET('preencherSpanCalendario','".$stNomeTipoContagem.",boVT');");

        $obHdnCalendario = new Hiddeneval();
        $obHdnCalendario->setName("hdnCalendario");
        $obHdnCalendario->setId("hdnCalendario");

        $obIntQuantVT = new Inteiro();
        $obIntQuantVT->setRotulo("Quantidade de Vales-Transporte");
        $obIntQuantVT->setName("inQuantVT");
        $obIntQuantVT->setId("inQuantVT");
        $obIntQuantVT->setSize(4);
        $obIntQuantVT->setMaxLength(3);
        $obIntQuantVT->setValue(2);
        $obIntQuantVT->setNull(false);
        $obIntQuantVT->setTitle("Informe a quantidade de Vales-Transporte conforme a forma de contagem (diária ou mensal).");

        $obTxtValorVT = new Moeda();
        $obTxtValorVT->setRotulo("Valor diário/mensal do Vale-Transporte");
        $obTxtValorVT->setName("nuValorVT");
        $obTxtValorVT->setId("nuValorVT");
        $obTxtValorVT->setTitle("Valor do Vale-Transporte (valor unitário) ou valor mensal (fixo) quando deve ser pago ao estagiário.");
        $obTxtValorVT->setNull(false);

        $obFormulario = new Formulario();
        $obFormulario->agrupaComponentes(array($obRdoTipoDiaria,$obRdoTipoMensalFixo));
        $obFormulario->addHidden($obHdnCalendario,true);
        $obFormulario->addComponente($obIntQuantVT);
        $obFormulario->addComponente($obTxtValorVT);

        $obFormulario->obJavaScript->montaJavaScript();

        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();

        $stEval .= "
                     stCampo = document.frm.inQuantVT;
                     if (stCampo) {
                        var numQtdVT = new Number(stCampo.value);
                        if (numQtdVT <= 0) {
                            erro = true; mensagem += \"@Campo Quantidade de Vale-Transporte inválido!\"
                        }
                     }";

        $stEval = str_replace("\n","",$stEval);

        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();
    }
    
    $stJs .= "d.getElementById('spnVT').innerHTML = '".$stHTML."';";
    $stJs .= "$('hdnVT').value = '".$stEval."';";

    if (!isset($_GET['inTipoContagem'])) {
        $_GET['inTipoContagem'] = '1';
        $stJs .= preencherSpanCalendario();
    }

    return $stJs;
}

function preencherSpanCalendario()
{
    if (($_GET['inTipoContagem'] == '1') && ($_GET["boVT"] == "true")) {
        include_once CAM_GRH_CAL_MAPEAMENTO.'TCalendarioCalendarioCadastro.class.php';

        $obCalendario = new TCalendarioCalendarioCadastro();
        $obCalendario->recuperaTodos($rsCalendario);
        $inCodCalendario = $_GET['inCodCalendario'];

        $obTxtCodCalendario = new TextBox;
        $obTxtCodCalendario->setRotulo  ( "Calendário"                      );
        $obTxtCodCalendario->setId      ( "inCodCalendario"                 );
        $obTxtCodCalendario->setName    ( "inCodCalendario"                 );
        $obTxtCodCalendario->setValue   ( $inCodCalendario                  );
        $obTxtCodCalendario->setTitle   ( "Selecione o calendário, para avaliação dos dias úteis"            );
        $obTxtCodCalendario->setMaxLength( 10                               );
        $obTxtCodCalendario->setSize    ( 10                                );
        $obTxtCodCalendario->setNull    ( false                             );
        $obTxtCodCalendario->setInteiro ( true                              );

        $obCmbCalendario = new Select;
        $obCmbCalendario->setName       ( "stCalendario"                    );
        $obCmbCalendario->setId         ( "stCalendario"                    );
        $obCmbCalendario->setStyle      ( "width: 250px"                    );
        $obCmbCalendario->setRotulo     ( "Tipo"                            );
        $obCmbCalendario->setValue      ( $inCodCalendario                  );
        $obCmbCalendario->setNull       ( false                             );
        $obCmbCalendario->addOption     ( "", "Selecione"                   );
        $obCmbCalendario->setCampoID    ( "[cod_calendar]"                  );
        $obCmbCalendario->setCampoDesc  ( "[descricao]"                     );
        $obCmbCalendario->preencheCombo ( $rsCalendario                     );

        $obFormulario = new Formulario;
        $obFormulario->addComponenteComposto( $obTxtCodCalendario   , $obCmbCalendario  );
        $obFormulario->montaInnerHtml();
        $stHTML = $obFormulario->getHTML();

        $stHTML = str_replace("\n","",$stHTML           );
        $stHTML = str_replace("\\","\\\\",$stHTML       );
        $stHTML = str_replace("  ","",$stHTML           );
        $stHTML = str_replace("'","\\'",$stHTML         );

        $stJs .= "
            jQuery('#spnCalendario').html('".$stHTML."');                       \n

            jQuery('#inCodCalendario').live('change', function () {               \n
                jQuery('#stCalendario').val(jQuery('#inCodCalendario').val());  \n
            });                                                                 \n

            jQuery('#stCalendario').live('change', function () {                 \n
                jQuery('#inCodCalendario').val(jQuery('#stCalendario').val());  \n
            });                                                                 \n
        ";
    } else {
        $stJs .= "jQuery('#stCalendario').val('');\n";
        $stJs .= "jQuery('#inCodCalendario').val('');\n";
        $stJs .= "jQuery('#spnCalendario').html('');\n";
    }

    return $stJs;
}

$stCtrl = $request->get('stCtrl');

switch ($stCtrl) {
    case "preencherDadosEstagiario":
        $stJs .= preencherDadosEstagiario();
        break;
    case "preencherSpanInstituicaoEntidade":
        $stJs .= preencherSpanInstituicaoEntidade();
        break;
    case "preencherForm":
        $stJs .= preencherForm();
        break;
    case "preencherFormAlterar":
        $stJs .= preencherFormAlterar($request);
        break;
    case "preencherInstituicao":
        $stJs .= preencherInstituicao();
        break;
    case "preencherCurso":
        $stJs .= preencherCurso();
        break;
    case "preencherTurnos":
        $stJs .= preencherTurnos();
        break;
    case "preencherMesValorBolsa":
        $stJs .= preencherMesValorBolsa();
        break;
    case "_Salvar":
        $stJs .= _Salvar();
        break;
    case "verificaCodigoEstagio":
        $stJs .= verificaCodigoEstagio();
        break;
    case "preencherGrauCurso":
        $stJs .= preencherGrauCurso();
        break;
    case "validarDataFim":
        $stJs .= validarDataFim();
        break;
    case "validarDataRenovacao":
        $stJs .= validarDataRenovacao();
        break;
    case "limpaZerosAEsquerda":
        $stJs .= limpaZerosAEsquerda();
        break;
     case "preencherNovoValorBolsa":
        $stJs = preencherNovoValorBolsa();
        break;
    case "preencherSpanValeRefeicao":
        $stJs = preencherSpanValeRefeicao();
        break;
    case "preencherSpanValeTransporte":
        $stJs = preencherSpanValeTransporte();
        break;
    case 'preencherSpanCalendario':
        $stJs = preencherSpanCalendario();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
