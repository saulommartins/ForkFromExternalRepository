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
    * Página de Formulário do Instituição de Ensino
    * Data de Criação: 02/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30843 $
    $Name$
    $Author: souzadl $
    $Date: 2006-10-30 13:04:04 -0300 (Seg, 30 Out 2006) $

    * Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_EST_COMPONENTES."IPopUpInstituicaoEntidade.class.php"                            );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoMes.class.php"                                      );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioGrau.class.php"                                          );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioCursoInstituicaoEnsino.class.php"                        );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioCurso.class.php"                                         );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioGrau.class.php"                                          );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInstituicaoEnsino";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

Sessao::write('arCursos', array());

$boLabel = false;
if ($stAcao == "alterar") {
    $boLabel = true;
    $obTEstagioCursoInstituicaoEnsino = new TEstagioCursoInstituicaoEnsino();
    $obTEstagioCurso             = new TEstagioCurso();
    $obTEstagioGrau              = new TEstagioGrau();
    $obTAdministracaoMes         = new TAdministracaoMes();
    $stFiltro = " AND curso_instituicao_ensino.numcgm = ".$_GET['inNumCGM'];
    $obTEstagioCursoInstituicaoEnsino->recuperaCursosDeInstituicao($rsInstituicao,$stFiltro);
    $arSessaoCursos = array();
    while (!$rsInstituicao->eof()) {
        $obTEstagioCurso->setDado("cod_curso",$rsInstituicao->getCampo("cod_curso"));
        $obTEstagioCurso->recuperaPorChave($rsCurso);
        $obTEstagioGrau->setDado("cod_grau",$rsCurso->getCampo("cod_grau"));
        $obTEstagioGrau->recuperaPorChave($rsGrau);
        $obTAdministracaoMes->setDado("cod_mes",$rsInstituicao->getCampo("cod_mes"));
        $obTAdministracaoMes->recuperaPorChave($rsMes);
        $arCurso                               = array();
        $arCurso['inId']                       = count($arSessaoCursos);
        $arCurso['inCodCurso']                 = $rsInstituicao->getCampo("cod_curso");
        $arCurso['stCurso']                    = $rsCurso->getCampo("nom_curso");
        $arCurso['inCodGrau']                  = $rsGrau->getCampo("cod_grau");
        $arCurso['stGrau']                     = $rsGrau->getCampo("descricao");
        $arCurso['nuValorBolsa']               = number_format($rsInstituicao->getCampo("vl_bolsa"),2,',','.');
        $arCurso['inCodMes']                   = $rsInstituicao->getCampo("cod_mes");
        $arCurso['stMes']                      = trim($rsMes->getCampo("descricao"));

        $arSessaoCursos[]                      = $arCurso;
        $rsInstituicao->proximo();
    }
    Sessao::write('arCursos', $arSessaoCursos);
    $jsOnload = "executaFuncaoAjax('preencheFormAlterar','&inCGM=".$_GET['inNumCGM']."');";
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

$obIPopUpInstituicaoEntidade = new IPopUpInstituicaoEntidade();
$obIPopUpInstituicaoEntidade->setLabel($boLabel);

$obTEstagioGrau = new TEstagioGrau();
$obTEstagioGrau->recuperaTodos($rsGrauCurso);
$obCmbGrauCursos = new Select();
$obCmbGrauCursos->setRotulo("Grau do Curso");
$obCmbGrauCursos->setTitle("Informe o grau do curso, se superior, 2º grau, 1º grau, técnico, etc...");
$obCmbGrauCursos->setName("inCodGrau");
$obCmbGrauCursos->setId("inCodGrau");
$obCmbGrauCursos->setNullBarra(false);
$obCmbGrauCursos->addOption("","Selecione");
$obCmbGrauCursos->setStyle( "width: 250px" );
$obCmbGrauCursos->setCampoId("cod_grau");
$obCmbGrauCursos->setCampoDesc("descricao");
$obCmbGrauCursos->preencheCombo($rsGrauCurso);
$obCmbGrauCursos->obEvento->setOnChange(" if (jQuery('#inCodGrau').val() != ''){ montaParametrosGET('preencherCurso','inCodGrau'); } else { if(jQuery('#inCodCurso')) {limpaSelect(document.getElementById('inCodCurso'),1);} }");

$obCmbCursos = new Select();
$obCmbCursos->setRotulo("Curso");
$obCmbCursos->setTitle("Informe o nome do curso ou área de conhecimento.");
$obCmbCursos->setName("inCodCurso");
$obCmbCursos->setId("inCodCurso");
$obCmbCursos->setNullBarra(false);
$obCmbCursos->addOption("","Selecione");
$obCmbCursos->setStyle( "width: 250px" );

$obMoeValorBolsa = new Moeda();
$obMoeValorBolsa->setRotulo("Valor da Bolsa");
$obMoeValorBolsa->setTitle("Informe o valor da bolsa auxílio.");
$obMoeValorBolsa->setName("nuValorBolsa");
$obMoeValorBolsa->setValue($nuValorBolsa);

$obTAdministracaoMes = new TAdministracaoMes();
$obTAdministracaoMes->recuperaTodos($rsMes);
$obCmbMeses = new Select();
$obCmbMeses->setRotulo("Período Avaliação do Estágio");
$obCmbMeses->setTitle("Selecione o mês para avaliação do estágio.");
$obCmbMeses->setName("inCodMes");
$obCmbMeses->setValue($inCodMes);;
$obCmbMeses->addOption("","Selecione");
$obCmbMeses->setCampoId("cod_mes");
$obCmbMeses->setCampoDesc("descricao");
$obCmbMeses->setStyle( "width: 150px" );
$obCmbMeses->preencheCombo($rsMes);

$arComponentes = array($obCmbGrauCursos,$obCmbCursos,$obMoeValorBolsa,$obCmbMeses);

$obSpnCursos = new Span();
$obSpnCursos->setId("spnCursos");

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("executaFuncaoAjax('_Salvar','',true);");

$obBtnLimpar = new Limpar();

$obBtnCancelar = new Cancelar();
$obBtnCancelar->obEvento->setOnClick("Cancelar('".$pgList.'?'.Sessao::getId().'&HdninCGM='.$_GET['inNumCGM'].'&stAcao='.$stAcao."','telaPrincipal');");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addTitulo("Instituição de Ensino");
$obIPopUpInstituicaoEntidade->geraFormulario($obFormulario,$obForm);
$obFormulario->addTitulo("Cursos/Área de Conhecimento");
$obFormulario->addComponente($obCmbGrauCursos);
$obFormulario->addComponente($obCmbCursos);
$obFormulario->addComponente($obMoeValorBolsa);
$obFormulario->addComponente($obCmbMeses);
$obFormulario->IncluirAlterar("Curso",$arComponentes,true);
$obFormulario->addSpan($obSpnCursos);
if ($stAcao == "incluir") {
    $obFormulario->defineBarra(array($obBtnOk,$obBtnLimpar));
} else {
    $obFormulario->defineBarra(array($obBtnOk,$obBtnCancelar));
}
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
