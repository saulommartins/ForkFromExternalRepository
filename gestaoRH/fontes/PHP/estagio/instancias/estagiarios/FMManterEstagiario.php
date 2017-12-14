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
    * Página de Formulário do Estagiário
    * Data de Criação: 04/10/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.07.01

    $Id: FMManterEstagiario.php 61562 2015-02-05 13:03:54Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalGradeHorario.class.php"                                     );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php"                                             );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                                         );
include_once ( CAM_GT_MON_COMPONENTES."IMontaAgencia.class.php"                                    );
include_once ( CAM_GRH_PES_COMPONENTES."IBuscaInnerLocal.class.php"                                     );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioGrau.class.php"                                          );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagio.class.php"                             );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagioLocal.class.php"                        );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiario.class.php"                                    );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagioConta.class.php"                        );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEntidadeIntermediadoraEstagio.class.php"                 );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioCurso.class.php"                                         );
include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php"                );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEstagiario";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$stAcao = $request->get('stAcao');
$obRCadastroDinamico = new RCadastroDinamico();
$obRCadastroDinamico->setCodCadastro(1);
$obRCadastroDinamico->obRModulo->setCodModulo(39);
if ($stAcao == "alterar") {
    $obTEstagioEstagiarioEstagio             = new TEstagioEstagiarioEstagio();
    $obTEstagioEstagiarioEstagioConta        = new TEstagioEstagiarioEstagioConta();
    $obTEstagioEstagiarioEstagioLocal        = new TEstagioEstagiarioEstagioLocal();
    $obTEstagioEstagiario                    = new TEstagioEstagiario();
    $obTEstagioEntidadeIntermediadoraEstagio = new TEstagioEntidadeIntermediadoraEstagio();
    $obTEstagioCurso                         = new TEstagioCurso();

    $stFiltro  = " AND cod_estagio = ".$request->get('inCodEstagio');
    $stFiltro .= " AND cod_curso = ".$request->get('inCodCurso');
    $stFiltro .= " AND cgm_estagiario = ".$request->get('inNumCGMEstagiario');
    $stFiltro .= " AND cgm_instituicao_ensino = ".$request->get('inNumCGMInstituicao');
    $obTEstagioEstagiarioEstagio->recuperaRelacionamento($rsEstagiario,$stFiltro);
    $obTEstagioEstagiario->setDado("numcgm",$rsEstagiario->getCampo("numcgm"));
    $obTEstagioEstagiario->recuperaPorChave($rsEstagiarioMaePai);
    $obTEstagioEntidadeIntermediadoraEstagio->setDado("cgm_estagiario",$rsEstagiario->getCampo("cgm_estagiario"));
    $obTEstagioEntidadeIntermediadoraEstagio->setDado("cod_estagio",$rsEstagiario->getCampo("cod_estagio"));
    $obTEstagioEntidadeIntermediadoraEstagio->setDado("cod_curso",$rsEstagiario->getCampo("cod_curso"));
    $obTEstagioEntidadeIntermediadoraEstagio->setDado("cgm_instituicao_ensino",$rsEstagiario->getCampo("cgm_instituicao_ensino"));
    $obTEstagioEntidadeIntermediadoraEstagio->recuperaPorChave($rsEntidade);
    $stFiltro = " AND cod_curso = ".$rsEstagiario->getCampo("cod_curso");
    $obTEstagioCurso->recuperaRelacionamento($rsCurso,$stFiltro);
    $stFiltro  = " AND numcgm = ".$rsEstagiario->getCampo("cgm_estagiario");
    $stFiltro .= " AND cod_estagio = ".$rsEstagiario->getCampo("cod_estagio");
    $stFiltro .= " AND cod_curso = ".$rsEstagiario->getCampo("cod_curso");
    $stFiltro .= " AND cgm_instituicao_ensino = ".$rsEstagiario->getCampo("cgm_instituicao_ensino");
    $obTEstagioEstagiarioEstagioConta->recuperaRelacionamento($rsConta,$stFiltro);
    $obTEstagioEstagiarioEstagioLocal->recuperaRelacionamento($rsLocal,$stFiltro);

    $arChaveAtributoCandidato =  array( "cod_estagio" => $rsEstagiario->getCampo("cod_estagio"),
                                             "numcgm" => $rsEstagiario->getCampo("cgm_estagiario"),
                                          "cod_curso" => $rsEstagiario->getCampo("cod_curso"),
                             "cgm_instituicao_ensino" => $rsEstagiario->getCampo("cgm_instituicao_ensino"));
    $obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
    $obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
    $inCodEstagio    = $rsEstagiario->getCampo("cod_estagio");
    $inCodigoEstagio = $rsEstagiario->getCampo("numero_estagio");
    $inNumCGMEstagiario = $rsEstagiario->getCampo("cgm_estagiario");
    $stNomCGM        = $rsEstagiario->getCampo("nom_cgm");
    $stCGM           = $rsEstagiario->getCampo("cgm_estagiario")."-".$rsEstagiario->getCampo("nom_cgm");
    $stNomePai       = $rsEstagiarioMaePai->getCampo("nom_pai");
    $stNomeMae       = $rsEstagiarioMaePai->getCampo("nom_mae");
    $stVinculo       = $rsEstagiario->getCampo("vinculo_estagio");
    $stVinculoTxt    = ($rsEstagiario->getCampo("vinculo_estagio") == "i")?"Instituição de Ensino" : "Entidade Intermediadora";
    $stAnoSemestre   = $rsEstagiario->getCampo("ano_semestre");
    $dtInicioEstagio = $rsEstagiario->getCampo("data_inicio");
    $dtFimEstagio    = $rsEstagiario->getCampo("data_final");
    $dtRenovacaoEstagio = $rsEstagiario->getCampo("data_renovacao");
    $stFuncao        = $rsEstagiario->getCampo("funcao");
    $stObjetivo      = $rsEstagiario->getCampo("objetivos");
    $inCodGradeHorario = $rsEstagiario->getCampo("cod_grade");
    $stGrauTxt       = $rsCurso->getCampo('descricao');
    $inCodGrau       = $rsCurso->getCampo('cod_grau');
    $inCodCurso      = $rsCurso->getCampo('cod_curso');
    $stCursoTxt      = $rsCurso->getCampo('nom_curso');
    $inCodOrgao      = $rsEstagiario->getCampo("cod_orgao");

    $jsOnload  = "executaFuncaoAjax('preencherFormAlterar','&inNumCGMEntidade=".$rsEntidade->getCampo("cgm_entidade");
    $jsOnload .= "&inNumCGMInstituicao=".$rsEstagiario->getCampo("cgm_instituicao_ensino");
    $jsOnload .= "&inCodGradeHorario=".$rsEstagiario->getCampo("cod_grade");
    $jsOnload .= "&stNumBanco=".$rsConta->getCampo("num_banco");
    $jsOnload .= "&stNumAgencia=".$rsConta->getCampo("num_agencia");
    $jsOnload .= "&stContaCorrente=".$rsConta->getCampo("num_conta");
    $jsOnload .= "&inCodLocal=".$rsLocal->getCampo("cod_local");
    $jsOnload .= "&stLocal=".$rsLocal->getCampo("descricao");
    $jsOnload .= "&inCodCurso=".$rsCurso->getCampo("cod_curso");
    $jsOnload .= "&inCodEstagio=".$rsEstagiario->getCampo("cod_estagio");
    $jsOnload .= "&stVinculo=".$rsEstagiario->getCampo("vinculo_estagio");
    $jsOnload .= "&inCGM=".$rsEstagiario->getCampo("cgm_estagiario")."');";
} else {
    $obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

    $jsOnload = "executaFuncaoAjax('preencherForm');";
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );
$obForm->setTarget                              ( "oculto"                                                              );

$obIPopCGM = new IPopUpCGM($obForm);
$obIPopCGM->setTipo("fisica");
$obIPopCGM->obCampoCod->obEvento->setOnChange("montaParametrosGET('preencherDadosEstagiario','inCGM');");
$obIPopCGM->obCampoCod->obEvento->setOnBlur("montaParametrosGET('preencherDadosEstagiario','inCGM');");
$obIPopCGM->setTitle("Informe o CGM do estagiário.");

$obLblCGM = new Label();
$obLblCGM->setRotulo("CGM");
$obLblCGM->setId("stCGM");
$obLblCGM->setValue($stCGM);

$obHdnCGM = new hidden();
$obHdnCGM->setName("inNumCGMEstagiario");
$obHdnCGM->setValue($inNumCGMEstagiario);

$obHdnNomCGM = new hidden();
$obHdnNomCGM->setName("stNomCGM");
$obHdnNomCGM->setValue($stNomCGM);

$obTxtCodigoEstagio = new TextBox();
$obTxtCodigoEstagio->setRotulo("Código do Estágio");
$obTxtCodigoEstagio->setTitle("Informe o código do estágio para referência no município.");
$obTxtCodigoEstagio->setName("inCodEstagio");
$obTxtCodigoEstagio->setId("inCodEstagio");
$obTxtCodigoEstagio->setValue($inCodEstagio);
$obTxtCodigoEstagio->setNull(false);
$obTxtCodigoEstagio->setInteiro(true);
$obTxtCodigoEstagio->setSize(10);
$obTxtCodigoEstagio->obEvento->setOnChange("montaParametrosGET('limpaZerosAEsquerda','inCodEstagio');montaParametrosGET('verificaCodigoEstagio','inCodEstagio');");

$obHdnCodEstagio =  new Hidden;
$obHdnCodEstagio->setName                             ( "inCodEstagio");
$obHdnCodEstagio->setValue                            ( $inCodEstagio );

$obHdnCodigoEstagio =  new Hidden;
$obHdnCodigoEstagio->setName                          ( "inCodigoEstagio" );
$obHdnCodigoEstagio->setValue                         ( $inCodigoEstagio  );

$obLblCodigoEstagio = new Label();
$obLblCodigoEstagio->setRotulo("Código do Estágio");
$obLblCodigoEstagio->setId("inCodigoEstagio");
$obLblCodigoEstagio->setValue($inCodigoEstagio);

$obLblEndereco = new Label();
$obLblEndereco->setRotulo("Endereço");
$obLblEndereco->setId("stEndereco");

$obLblRG = new Label();
$obLblRG->setRotulo("RG");
$obLblRG->setId("stRG");

$obLblCPF = new Label();
$obLblCPF->setRotulo("CPF");
$obLblCPF->setId("stCPF");

$obLblTelefone = new Label();
$obLblTelefone->setRotulo("Telefone Fixo");
$obLblTelefone->setId("stTelefone");

$obLblCelular = new Label();
$obLblCelular->setRotulo("Telefone Celular");
$obLblCelular->setId("stCelular");

$obTxtNomePai = new TextBox();
$obTxtNomePai->setRotulo("Nome do Pai");
$obTxtNomePai->setTitle("Informe o nome do pai do estagiário.");
$obTxtNomePai->setName("stNomePai");
$obTxtNomePai->setValue($stNomePai);
$obTxtNomePai->setSize(50);
$obTxtNomePai->setMaxLength(100);

$obTxtNomeMae = new TextBox();
$obTxtNomeMae->setRotulo("Nome da Mãe");
$obTxtNomeMae->setTitle("Informe o nome da mãe do estagiário.");
$obTxtNomeMae->setName("stNomeMae");
$obTxtNomeMae->setValue($stNomeMae);
$obTxtNomeMae->setSize(50);
$obTxtNomeMae->setMaxLength(100);

$obRdoInstituicaoEnsino = new Radio();
$obRdoInstituicaoEnsino->setRotulo("Vínculo do Estágio");
$obRdoInstituicaoEnsino->setTitle("Selecione o tipo de vínculo do estágio.");
$obRdoInstituicaoEnsino->setName("stVinculo");
$obRdoInstituicaoEnsino->setValue("i");
$obRdoInstituicaoEnsino->setLabel("Instituição de Ensino");
$obRdoInstituicaoEnsino->setChecked($stVinculo == "i" || !$stVinculo);
$obRdoInstituicaoEnsino->obEvento->setOnChange("montaParametrosGET('preencherSpanInstituicaoEntidade','stVinculo,stAcao');");

$obRdoEntidadeIntermediadora = new Radio();
$obRdoEntidadeIntermediadora->setRotulo("Vínculo do Estágio");
$obRdoEntidadeIntermediadora->setTitle("Selecione o tipo de vínculo do estágio.");
$obRdoEntidadeIntermediadora->setName("stVinculo");
$obRdoEntidadeIntermediadora->setValue("e");
$obRdoEntidadeIntermediadora->setLabel("Entidade Intermediadora");
$obRdoEntidadeIntermediadora->setChecked($stVinculo == "e");
$obRdoEntidadeIntermediadora->obEvento->setOnChange("montaParametrosGET('preencherSpanInstituicaoEntidade','stVinculo,stAcao');");

$obLblVinculo = new Label();
$obLblVinculo->setRotulo("Vínculo do Estágio");
$obLblVinculo->setValue($stVinculoTxt);

$obHdnVinculo = new Hidden();
$obHdnVinculo->setName("stVinculo");
$obHdnVinculo->setValue($stVinculo);

$obSpnInstituicaoEntidade = new Span();
$obSpnInstituicaoEntidade->setId("spnInstituicaoEntidade");

//$obTEstagioGrau = new TEstagioGrau();
//$obTEstagioGrau->recuperaTodos($rsGrauCurso);
$obCmbGrauCursos = new Select();
$obCmbGrauCursos->setRotulo("Grau do Curso");
$obCmbGrauCursos->setTitle("Informe o grau do curso, se superior, 2º grau, 1º grau, técnico, etc...");
$obCmbGrauCursos->setName("inCodGrau");
$obCmbGrauCursos->setValue($inCodGrau);
$obCmbGrauCursos->addOption("","Selecione");
$obCmbGrauCursos->setStyle( "width: 250px" );
//$obCmbGrauCursos->setCampoId("cod_grau");
//$obCmbGrauCursos->setCampoDesc("descricao");
//$obCmbGrauCursos->preencheCombo($rsGrauCurso);
$obCmbGrauCursos->obEvento->setOnChange("montaParametrosGET('preencherCurso','inCodGrau,inNumCGMInstituicao,stAcao');");
$obCmbGrauCursos->setNull(false);

$obLblGrauCursos = new Label();
$obLblGrauCursos->setRotulo("Grau do Curso");
$obLblGrauCursos->setValue($stGrauTxt);

$obHdnGrauCursos = new Hidden();
$obHdnGrauCursos->setName("inCodGrau");
$obHdnGrauCursos->setValue($inCodGrau);

$obCmbCursos = new Select();
$obCmbCursos->setRotulo("Curso");
$obCmbCursos->setTitle("Informe o nome do curso ou área de conhecimento.");
$obCmbCursos->setName("inCodCurso");
$obCmbCursos->setNullBarra(false);
$obCmbCursos->addOption("","Selecione");
$obCmbCursos->setStyle( "width: 250px" );
$obCmbCursos->setNull(false);
$obCmbCursos->obEvento->setOnChange("montaParametrosGET('preencherMesValorBolsa','inNumCGMInstituicao,inCodCurso,inCodEstagio');");

$obLblCursos = new Label();
$obLblCursos->setRotulo("Curso");
$obLblCursos->setValue($stCursoTxt);

$obHdnCursos = new Hidden();
$obHdnCursos->setName("inCodCurso");
$obHdnCursos->setValue($inCodCurso);

$obTxtValorBolsa = new Moeda();
$obTxtValorBolsa->setRotulo("Valor da Bolsa");
$obTxtValorBolsa->setName("nuValorBolsa");
$obTxtValorBolsa->setValue($nuValorBolsa);
$obTxtValorBolsa->setId("nuValorBolsa");
$obTxtValorBolsa->setTitle("Altere o valor caso seja diferente do sugerido pelo sistema.");
$obTxtValorBolsa->obEvento->setOnChange("montaParametrosGET('preencherNovoValorBolsa','nuValorBolsa,inDiasFaltas,boVR,boVT')");
//$obTxtValorBolsa->setTitle("Informe o valor da bolsa auxílio, caso seja diferente do sugerido pelo sistema.");

$obIntQuantDiasFalta = new Inteiro();
$obIntQuantDiasFalta->setRotulo("Dias de Faltas");
$obIntQuantDiasFalta->setName("inDiasFaltas");
$obIntQuantDiasFalta->setValue($inDiasFaltas);
$obIntQuantDiasFalta->setId("inDiasFaltas");
$obIntQuantDiasFalta->setSize(4);
$obIntQuantDiasFalta->setMaxLength(2);
$obIntQuantDiasFalta->setTitle("Informe os dias de faltas do estagiário na competência.");
$obIntQuantDiasFalta->obEvento->setOnChange("montaParametrosGET('preencherNovoValorBolsa','nuValorBolsa,inDiasFaltas,boVR,boVT')");

$obSpnNovoValorBolsa = new Span();
$obSpnNovoValorBolsa->setId("spnNovoValorBolsa");

$obRdoVRSim = new Radio();
$obRdoVRSim->setRotulo("Vale-Refeição");
$obRdoVRSim->setTitle("Marque sim, no caso de utilização do vale-refeição.");
$obRdoVRSim->setName("boVR");
$obRdoVRSim->setId("boVRSim");
$obRdoVRSim->setValue("true");
$obRdoVRSim->setLabel("Sim");
$obRdoVRSim->obEvento->setOnChange("montaParametrosGET('preencherSpanValeRefeicao','boVR');");
$obRdoVRSim->setDisabled(true);

$obRdoVRNao = new Radio();
$obRdoVRNao->setRotulo("Vale-Refeição");
$obRdoVRNao->setTitle("Marque sim, no caso de utilização do vale-refeição.");
$obRdoVRNao->setName("boVR");
$obRdoVRNao->setId("boVRNao");
$obRdoVRNao->setValue("false");
$obRdoVRNao->setLabel("Não");
$obRdoVRNao->setChecked(true);
$obRdoVRNao->obEvento->setOnChange("montaParametrosGET('preencherSpanValeRefeicao','boVR');");
$obRdoVRNao->setDisabled(true);

$obSpnVR = new Span();
$obSpnVR->setId("spnVR");

$obHdnVR = new Hiddeneval();
$obHdnVR->setName("hdnVR");
$obHdnVR->setId("hdnVR");

$obRdoVTSim = new Radio();
$obRdoVTSim->setRotulo("Vale-Transporte");
$obRdoVTSim->setTitle("Marque sim, no caso de utilização do Vale-Transporte.");
$obRdoVTSim->setName("boVT");
$obRdoVTSim->setId("boVTSim");
$obRdoVTSim->setValue("true");
$obRdoVTSim->setLabel("Sim");
$obRdoVTSim->obEvento->setOnChange("montaParametrosGET('preencherSpanValeTransporte','boVT');");
$obRdoVTSim->setDisabled(true);

$obRdoVTNao = new Radio();
$obRdoVTNao->setRotulo("Vale-Transporte");
$obRdoVTNao->setTitle("Marque sim, no caso de utilização do Vale-Transporte.");
$obRdoVTNao->setName("boVT");
$obRdoVTNao->setId("boVTNao");
$obRdoVTNao->setValue("false");
$obRdoVTNao->setLabel("Não");
$obRdoVTNao->setChecked(true);
$obRdoVTNao->obEvento->setOnChange("montaParametrosGET('preencherSpanValeTransporte','boVT');");
$obRdoVTNao->setDisabled(true);

$obSpnVT = new Span();
$obSpnVT->setId("spnVT");

$obSpnCalendario = new Span();
$obSpnCalendario->setId("spnCalendario");

$obHdnVT = new Hiddeneval();
$obHdnVT->setName("hdnVT");
$obHdnVT->setId("hdnVT");

$obLblMesAvaliacao = new Label();
$obLblMesAvaliacao->setId("nuMesAvaliacao");
$obLblMesAvaliacao->setRotulo("Mês de Avaliação Estágio");
$obLblMesAvaliacao->setValue($nuMesAvaliacao);

$obTxtAnoSemestre = new TextBox();
$obTxtAnoSemestre->setRotulo("Ano/Semestre");
$obTxtAnoSemestre->setName("stAnoSemestre");
$obTxtAnoSemestre->setValue($stAnoSemestre);
$obTxtAnoSemestre->setTitle("Informe o ano ou semestre em que o estagiário encontra-se no curso.");
$obTxtAnoSemestre->setSize(4);
$obTxtAnoSemestre->setMaxLength(4);

$obDtInicioEstagio = new Data();
$obDtInicioEstagio->setRotulo("Data de Início do Estágio");
$obDtInicioEstagio->setTitle("Informe a data de início do estágio.");
$obDtInicioEstagio->setName("dtInicioEstagio");
$obDtInicioEstagio->setValue($dtInicioEstagio);
$obDtInicioEstagio->setNull(false);

$obDtFimEstagio = new Data();
$obDtFimEstagio->setRotulo("Data do Fim do Estágio");
$obDtFimEstagio->setTitle("Informe a data fim do estágio.");
$obDtFimEstagio->setName("dtFimEstagio");
$obDtFimEstagio->setValue($dtFimEstagio);
$obDtFimEstagio->obEvento->setOnChange("montaParametrosGET('validarDataFim','dtInicioEstagio,dtFimEstagio');");

$obDtRenovacaoEstagio = new Data();
$obDtRenovacaoEstagio->setRotulo("Data da Renovação do Estágio");
$obDtRenovacaoEstagio->setTitle("Informe a data de renovação do estágio.");
$obDtRenovacaoEstagio->setName("dtRenovacaoEstagio");
$obDtRenovacaoEstagio->setValue($dtRenovacaoEstagio);
$obDtRenovacaoEstagio->obEvento->setOnChange("montaParametrosGET('validarDataRenovacao','dtFimEstagio,dtRenovacaoEstagio');");

$obTxtFuncao = new TextBox();
$obTxtFuncao->setRotulo("Função Desempenhada");
$obTxtFuncao->setName("stFuncao");
$obTxtFuncao->setValue($stFuncao);
$obTxtFuncao->setTitle("Informe a função a ser desempenhada pelo estagiário no município.");
$obTxtFuncao->setNull(false);
$obTxtFuncao->setSize(20);
$obTxtFuncao->setMaxLength(20);

$obTxtObjetivo = new TextArea();
$obTxtObjetivo->setRotulo("Objetivo do Estágio");
$obTxtObjetivo->setName("stObjetivo");
$obTxtObjetivo->setValue($stObjetivo);
$obTxtObjetivo->setTitle("Informe os objetivos do estágio.");

$obIMontaAgencia = new IMontaAgencia();
$obIMontaAgencia->obTextBoxSelectAgencia->setNull(true);
$obIMontaAgencia->obITextBoxSelectBanco->setNull(true);

$obTxtContaCorrente = new TextBox();
$obTxtContaCorrente->setRotulo("Conta Corrente");
$obTxtContaCorrente->setTitle("Digite o número da conta corrente.");
$obTxtContaCorrente->setName("stContaCorrente");
$obTxtContaCorrente->setId("stContaCorrente");
$obTxtContaCorrente->setSize(15);
$obTxtContaCorrente->setMaxLength(15);

$obIBuscaInnerLocal = new IBuscaInnerLocal();

$obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
$obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);

include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
$obIMontaOrganograma = new IMontaOrganograma();
$obIMontaOrganograma->setCodOrgao($inCodOrgao);
$obIMontaOrganograma->setNivelObrigatorio(1);
$obIMontaOrganograma->obROrganograma->setCodOrganograma($rsOrganogramaVigente->getCampo('cod_organograma'));

$obRPessoalGradeHorario = new RPessoalGradeHorario();
$obRPessoalGradeHorario->listarGrade( $rsGradeHorario,"" );
$obCmbGradeHorario = new Select;
$obCmbGradeHorario->setName                    ( "inCodGradeHorario"                      );
$obCmbGradeHorario->setValue                   ( $inCodGradeHorario                    );
$obCmbGradeHorario->setRotulo                  ( "Tipo"                                );
$obCmbGradeHorario->setTitle                   ( "Selecione o gradro de horários para o estagiário."        );
$obCmbGradeHorario->addOption                  ( "", "Selecione"                       );
$obCmbGradeHorario->setCampoId                 ( "cod_grade"                         );
$obCmbGradeHorario->setCampoDesc               ( "descricao"                           );
$obCmbGradeHorario->preencheCombo              ( $rsGradeHorario                       );
$obCmbGradeHorario->setStyle                   ( "width: 250px"                        );
$obCmbGradeHorario->setNull(false);
$obCmbGradeHorario->obEvento->setOnChange      ( "montaParametrosGET('preencherTurnos','inCodGradeHorario');"        );

$obSpnTurnos = new Span;
$obSpnTurnos->setId ('spnTurnos' );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("montaParametrosGET('_Salvar','',true);");

$obBtnLimpar = new Limpar();

$obBtnCancelar = new Cancelar();
$obBtnCancelar->obEvento->setOnClick("Cancelar('".$pgList.'?'.Sessao::getId().'&HdninCGM='.$request->get('inNumCGM').'&stAcao='.$stAcao."','telaPrincipal');");

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas();
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addAba("Estagiário");
$obFormulario->addTitulo("Dados do Estagiário");
if ($stAcao == "incluir") {
    $obFormulario->addComponente($obIPopCGM);
    $obFormulario->addComponente($obTxtCodigoEstagio);
} else {
    $obFormulario->addHidden($obHdnCodEstagio);
    $obFormulario->addHidden($obHdnCodigoEstagio);
    $obFormulario->addHidden($obHdnCGM);
    $obFormulario->addHidden($obHdnNomCGM);
    $obFormulario->addComponente($obLblCGM);
    $obFormulario->addComponente($obLblCodigoEstagio);
}
$obFormulario->addComponente($obLblRG);
$obFormulario->addComponente($obLblCPF);
$obFormulario->addComponente($obLblEndereco);
$obFormulario->addComponente($obLblTelefone);
$obFormulario->addComponente($obLblCelular);
$obFormulario->addComponente($obTxtNomePai);
$obFormulario->addComponente($obTxtNomeMae);
$obFormulario->addTitulo("Dados do Contrato do Estágio");
if ($stAcao == "incluir") {
    $obFormulario->agrupaComponentes(array($obRdoInstituicaoEnsino,$obRdoEntidadeIntermediadora));
    $obFormulario->addSpan($obSpnInstituicaoEntidade);
    $obFormulario->addComponente($obCmbGrauCursos);
    $obFormulario->addComponente($obCmbCursos);
} else {
    $obFormulario->addComponente($obLblVinculo);
    $obFormulario->addHidden($obHdnVinculo);
    $obFormulario->addSpan($obSpnInstituicaoEntidade);
    $obFormulario->addComponente($obLblGrauCursos);
    $obFormulario->addHidden($obHdnGrauCursos);
    $obFormulario->addComponente($obLblCursos);
    $obFormulario->addHidden($obHdnCursos);
}
$obFormulario->addComponente($obTxtValorBolsa);
$obFormulario->addComponente($obIntQuantDiasFalta);
$obFormulario->addSpan($obSpnNovoValorBolsa);
$obFormulario->addComponente($obLblMesAvaliacao);
$obFormulario->addComponente($obTxtAnoSemestre);
$obFormulario->addComponente($obDtInicioEstagio);
$obFormulario->addComponente($obDtFimEstagio);
$obFormulario->addComponente($obDtRenovacaoEstagio);
$obFormulario->addComponente($obTxtFuncao);
$obFormulario->addComponente($obTxtObjetivo);
$obFormulario->addTitulo("Informações Bancárias");
$obIMontaAgencia->geraFormulario($obFormulario);
$obFormulario->addComponente($obTxtContaCorrente);
$obFormulario->addTitulo("Informações da Lotação");
$obIMontaOrganograma->geraFormulario($obFormulario);
$obIBuscaInnerLocal->geraFormulario($obFormulario);
$obFormulario->addTitulo("Quadro de Horários");
$obFormulario->addComponente($obCmbGradeHorario);
$obFormulario->addSpan($obSpnTurnos);

$obFormulario->addAba("Dados de Benefícios");
$obFormulario->addTitulo("Vale-Transporte");
$obFormulario->agrupaComponentes(array($obRdoVTSim,$obRdoVTNao));
$obFormulario->addSpan($obSpnVT);
$obFormulario->addHidden($obHdnVT,true);
$obFormulario->addSpan($obSpnCalendario);

$obFormulario->addTitulo("Vale-Refeição");
$obFormulario->agrupaComponentes(array($obRdoVRSim,$obRdoVRNao));
$obFormulario->addSpan($obSpnVR);
$obFormulario->addHidden($obHdnVR,true);

$obFormulario->addAba("Atributos");
$obMontaAtributos->geraFormulario($obFormulario);
if ($stAcao == "incluir") {
    $obFormulario->defineBarra(array($obBtnOk,$obBtnLimpar));
} else {
    $obFormulario->defineBarra(array($obBtnOk,$obBtnCancelar));
}
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
