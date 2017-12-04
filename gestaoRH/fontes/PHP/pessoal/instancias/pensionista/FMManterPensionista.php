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
    * Página de Manter Cadastro de Pensionista
    * Data de Criação: 14/08/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-16 11:06:40 -0200 (Ter, 16 Out 2007) $

    * Casos de uso: uc-04.04.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionista.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalCID.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalPensionistaCid.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionistaProcesso.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionistaOrgao.class.php";
include_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterPensionista";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
if ($stAcao == "alterar") {
    $obTPessoalContratoPensionista = new TPessoalContratoPensionista;
    $stFiltro  = " AND pensionista.cod_pensionista = ".$_REQUEST['inCodPensionista'];
    $stFiltro .= " AND contrato_pensionista.cod_contrato = ".$_REQUEST['inCodContratoPensionista'];
    $obTPessoalContratoPensionista->recuperaPensionistas($rsContratoPensionista,$stFiltro);
    $obTPessoalPensionistaCid = new TPessoalPensionistaCid;
    $obTPessoalPensionistaCid->setDado("cod_pensionista",$_REQUEST['inCodPensionista']);
    $obTPessoalPensionistaCid->setDado("cod_contrato_cedente",$_REQUEST['inCodContratoServidor']);
    $obTPessoalPensionistaCid->recuperaPorChave($rsPensionistaCid);
    $obTPessoalCID = new TPessoalCID;
    $obTPessoalCID->setDado("cod_cid",$rsPensionistaCid->getCampo("cod_cid"));
    $obTPessoalCID->recuperaPorChave($rsCID);
    $obTPessoalContratoPensionistaProcesso = new TPessoalContratoPensionistaProcesso;
    $obTPessoalContratoPensionistaProcesso->setDado("cod_contrato",$rsContratoPensionista->getCampo("cod_contrato"));
    $obTPessoalContratoPensionistaProcesso->recuperaPorChave($rsProcesso);
    $obTPessoalContratoPensionistaOrgao = new TPessoalContratoPensionistaOrgao;
    $stFiltro = " AND contrato_pensionista_orgao.cod_contrato = ".$rsContratoPensionista->getCampo("cod_contrato");
    $obTPessoalContratoPensionistaOrgao->recuperaRelacionamento($rsOrgao,$stFiltro);
    $arChaveAtributoCandidato =  array( "cod_contrato" => $rsContratoPensionista->getCampo("cod_contrato") );
    $obRCadastroDinamico = new RCadastroDinamico;
    $obRCadastroDinamico->setCodCadastro(7);
    $obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
    $obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
    $inCodProfissao                 = $rsContratoPensionista->getCampo("cod_profissao");
    $inCodPensionista               = $rsContratoPensionista->getCampo("cod_pensionista");
    $inCodGrauParentesco            = $rsContratoPensionista->getCampo("cod_grau");
    $inNumBeneficio                 = $rsContratoPensionista->getCampo("num_beneficio");
    $inCodTipoDependencia           = $rsContratoPensionista->getCampo("cod_dependencia");
    $nuPercentualPagamentoPensao    = str_replace(".",",",$rsContratoPensionista->getCampo("percentual_pagamento"));
    $dtInicioBeneficio              = $rsContratoPensionista->getCampo("dt_inicio_beneficio");
    $dtEncerramentoBeneficio        = $rsContratoPensionista->getCampo("dt_encerramento");
    $stMotivoEncerramento           = $rsContratoPensionista->getCampo("motivo_encerramento");
    $stChaveProcesso                = $rsProcesso->getCampo("cod_processo")."/".$rsProcesso->getCampo("ano_exercicio");
    $inCodCID                       = $rsPensionistaCid->getCampo("cod_cid");
    $inSiglaCID                     = $rsCID->getCampo('sigla');
    $stDescricaoCID                 = $rsCID->getCampo('descricao');
    $inCodLotacao                   = $rsOrgao->getCampo("cod_orgao");
    $dtDataLaudo                    = $rsPensionistaCid->getCampo('data_laudo');
    $jsOnload   = "executaFuncaoAjax('processarForm','&inCGM=".$_REQUEST['inCGM']."&stAcao=".$stAcao."&stChaveProcesso=".$stChaveProcesso."&inCodContrato=".$rsContratoPensionista->getCampo("cod_contrato")."');";
} else {
    $obRCadastroDinamico = new RCadastroDinamico;
    $obRCadastroDinamico->setCodCadastro(7);
    $obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
    $jsOnload   = "executaFuncaoAjax('processarForm','&inCGM=".$_REQUEST['inCGM']."&stAcao=".$stAcao."');";
}

include_once 'FMManterPensionistaAbaInformacoes.php';
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo    ( "Atributos"  );
$obMontaAtributos->setName      ( "Atributo_"  );
$obMontaAtributos->setRecordSet ( $rsAtributos );

$obBtnIncluir = new Button;
$obBtnIncluir->setName              ( "btnIncluirCampos" );
$obBtnIncluir->setValue             ( "    Ok    " );
$obBtnIncluir->setTipo              ( "button" );
$obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET('salvarForm','',true);" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName ( "btnLimparCampos" );
$obBtnLimpar->setValue ( "Limpar"         );
$obBtnLimpar->setTipo ( "button"          );
$obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limparForm');" );

$obDtDataLaudo  = new Data;
$obDtDataLaudo->setName      ( "dtDataLaudo" );
$obDtDataLaudo->setId        ( "dtDataLaudo" );
$obDtDataLaudo->setValue     ( $dtDataLaudo );
$obDtDataLaudo->setRotulo    ("Data do Laudo ");
$obDtDataLaudo->setTitle     ("Informe a data do laudo");
$obDtDataLaudo->setNull      ( true );
$obDtDataLaudo->setDisabled  ( true );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm       ( $obForm  );
$obFormulario->addTitulo     ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addAba        ( "Informações do Pensionista" );
$obFormulario->addHidden     ( $obHdnAcao              );
$obFormulario->addHidden     ( $obHdnCtrl              );
$obFormulario->addHidden     ( $obHdnCGM               );
$obFormulario->addHidden     ( $obHdnNomCGMRescisao    );
$obFormulario->addHidden     ( $obHdnRegistroRescisao  );
$obFormulario->addHidden     ( $obHdnPensionista       );
$obFormulario->addComponente ( $obLblCGM               );
$obFormulario->addComponente ( $obLblNascimento        );
$obFormulario->addComponente ( $obLblSexo              );
$obFormulario->addComponente ( $obLblRG                );
$obFormulario->addComponente ( $obLblCPF               );
$obFormulario->addComponente ( $obLblEndereco          );
$obFormulario->addComponente ( $obLblTelefone          );
$obFormulario->addComponente ( $obLblCelular           );
$obFormulario->addComponente ( $obCmbOcupacao          );
$obFormulario->addComponente ( $obBscCID               );
$obFormulario->addComponente ( $obDtDataLaudo          );
$obFormulario->addComponenteComposto ( $obTxtCodParentesco,$obCmbCodParentesco                               );
$obFormulario->addTitulo ( "Informações Bancárias"                                               );
$obIMontaAgencia->geraFormulario ( $obFormulario                                                         );
$obFormulario->addComponente ( $obTxtContaCorrente );
if ($stAcao == "incluir") {
    $obIFiltroContrato->geraFormulario ( $obFormulario                                                         );
} else {
    $obFormulario->addTitulo     ( "Dados da Matrícula do Gerador do Benefício" );
    $obFormulario->addComponente ( $obLblCGMServidor      );
    $obFormulario->addComponente ( $obLblContratoServidor );
    $obFormulario->addHidden     ( $obHdnContratoServidor );
    $obFormulario->addHidden     ( $obHdnRegistroServidor );
}
$obFormulario->addTitulo                        ( "Dados da Matrícula do Pensionista"  );
if ($stAcao == "incluir") {
    $stValue = $obIContratoDigitoVerificador->obLblRegistroContrato->getValue('stValue');
    $obLblContratoPensionista->setValue($stValue);
    $inCodPensionista = $stValue;

    $obHdnContratoPensionista->setValue($stValue);
    $_REQUEST['inRegistroPensionista'] = $stValue;

    $_GET['inRegistroPensionista'] = $stValue;
    $_POST['inRegistroPensionista'] = $stValue;
    $obIContratoDigitoVerificador->setRegistroContratoLabel($obLblContratoPensionista);
    $obIContratoDigitoVerificador->setRegistroContratoHidden($obHdnContratoPensionista)    ;
    $obFormulario->inCodPensionista = $stValue;

    $obIContratoDigitoVerificador->geraFormulario( $obFormulario );
} else {
    $obFormulario->addComponente ( $obLblContratoPensionista );
    $obFormulario->addHidden     ( $obHdnContratoPensionista );
}

$obFormulario->addHidden  ( $obHdnCodCID );

$obFormulario->addComponente ( $obTxtNumBeneficio              );
$obFormulario->addComponente ( $obIPopUpProcesso               );
$obFormulario->addComponente ( $obLblDataInclusaoProcessao     );
$obFormulario->addComponente ( $obCmbTipoDependencia           );
$obFormulario->addComponente ( $obNumPercentualPagamentoPensao );
$obFormulario->addComponente ( $obDtaInicioBeneficio           );
$obFormulario->addComponente ( $obDtaEncerramentoBeneficio     );
$obFormulario->addSpan       ( $obSpnCalculoPensao             );
$obFormulario->addComponente ( $obTxtMotivoEncerramento        );
$obIMontaOrganograma->geraFormulario  ( $obFormulario );
$obFormulario->addSpan ( $obSpanPrevidencia );
$obFormulario->addAba ( "Atributos" );
$obMontaAtributos->geraFormulario ( $obFormulario  );

if ($stAcao == "incluir") {
    $obFormulario->defineBarra ( array( $obBtnIncluir, $obBtnLimpar ) );
} else {
    $obFormulario->Cancelar ( $pgList."?".Sessao::getId()."&stAcao=".$stAcao );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
