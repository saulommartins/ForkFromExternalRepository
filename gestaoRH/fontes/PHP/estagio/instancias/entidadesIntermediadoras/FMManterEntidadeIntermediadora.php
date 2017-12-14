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
    * Página de Formulário do Entidade Intermediadora
    * Data de Criação: 03/10/2006

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
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioInstituicaoEnsino.class.php"                             );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEntidadeIntermediadora.class.php"                        );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEntidadeIntermediadora";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$stAcao      = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

Sessao::write('arInstituicoes', array());
$nuPorcentagem = '0,00';

$boLabel = false;
if ($stAcao == "alterar") {
    $boLabel = true;
    $obTEstagioEntidadeIntermediadora = new TEstagioEntidadeIntermediadora();
    $stFiltro = " WHERE entidade_intermediadora.numcgm = ".$_GET['inNumCGM'];
    $obTEstagioEntidadeIntermediadora->recuperaTodos($rsEntidadeIntermediadora,$stFiltro);
    $rsEntidadeIntermediadora->addFormatacao('percentual_atual','NUMERIC_BR');

    $stFiltro = " AND entidade_intermediadora.numcgm = ".$_GET['inNumCGM'];
    $obTEstagioEntidadeIntermediadora->recuperaRelacionamento($rsInstituicao,$stFiltro);
    $arSessaoInstituicoes = array();
    while (!$rsInstituicao->eof()) {
        $arInstituicao                               = array();
        $arInstituicao['inId']                       = count($arSessaoInstituicoes);
        $arInstituicao['inNumCGMInstituicao']        = $rsInstituicao->getCampo("numcgm_instituicao");
        $arInstituicao['stNomCGM']                   = $rsInstituicao->getCampo("nom_cgm_instituicao");

        $arSessaoInstituicoes[]                      = $arInstituicao;
        $rsInstituicao->proximo();
    }
    $nuPorcentagem = $rsEntidadeIntermediadora->getCampo('percentual_atual');
    Sessao::write('arInstituicoes', $arSessaoInstituicoes);
    $jsOnload = "executaFuncaoAjax('preencheFormAlterar','&stAcao=".$stAcao."&inCGM=".$_GET['inNumCGM']."');";
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

$obIPopUpInstituicaoEntidade = new IPopUpInstituicaoEntidade(false);
$obIPopUpInstituicaoEntidade->setLabel($boLabel);

$obTEstagioInstituicaoEnsino = new TEstagioInstituicaoEnsino();
$obTEstagioInstituicaoEnsino->recuperaRelacionamento($rsInstituicao);

$obCmbInstituicao = new Select;
$obCmbInstituicao->setName                    ( "inNumCGMInstituicao"                                                  );
$obCmbInstituicao->setId                      ( "inNumCGMInstituicao"                                                  );
$obCmbInstituicao->setValue                   ( $inNumCGMInstituicao                                                   );
$obCmbInstituicao->setRotulo                  ( "Instituição"                                                       );
$obCmbInstituicao->setTitle                   ( "Selecione a instituição de ensino conveniada."                                          );
$obCmbInstituicao->setNullBarra               ( false                                                                 );
$obCmbInstituicao->addOption                  ( "", "Selecione"                                                       );
$obCmbInstituicao->setCampoId("numcgm");
$obCmbInstituicao->setCampoDesc("nom_cgm");
$obCmbInstituicao->setStyle("width: 250px");
$obCmbInstituicao->preencheCombo($rsInstituicao);

$arComponentes = array($obCmbInstituicao);

$obSpnInstituicoes = new Span();
$obSpnInstituicoes->setId("spnInstituicoes");

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
$obFormulario->addTitulo("Entidade Intermediária do Estágio");
$obIPopUpInstituicaoEntidade->geraFormulario($obFormulario,$obForm);

$obFormulario->addTitulo("Contribuição Patronal à Entidade");
$obPercentualContribuicao = new Porcentagem;
$obPercentualContribuicao->setRotulo                  ("Percentual");
$obPercentualContribuicao->setName                    ("nuPercentualContribuicao");
$obPercentualContribuicao->setId                      ("nuPercentualContribuicao");
$obPercentualContribuicao->setValue                   ($nuPorcentagem);
$obPercentualContribuicao->setTitle                   ("Informe o percentual patronal (% do valor da bolsa) para repasse a entidade intermediadora do estágio.");
$obPercentualContribuicao->setSize                    (5);
$obPercentualContribuicao->setMaxLength               (5);
$obFormulario->addComponente($obPercentualContribuicao);

$obFormulario->addTitulo("Instituição de Ensino");
$obFormulario->addComponente($obCmbInstituicao);
$obFormulario->Incluir("Instituicao",$arComponentes,true);
$obFormulario->addSpan($obSpnInstituicoes);
if ($stAcao == "incluir") {
    $obFormulario->defineBarra(array($obBtnOk,$obBtnLimpar));
} else {
    $obFormulario->defineBarra(array($obBtnOk,$obBtnCancelar));
}
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
