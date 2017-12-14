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

    * @author Analista: Dagiane
    * @author Desenvolvedor: Lisiane Morais

    * @ignore

    $Revision: $
    $Name$
    $Author: $
    $Date: $
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
$stAcao = "alterar" ;

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
if ($stAcao == "alterar") {
    $obTPessoalContratoPensionista = new TPessoalContratoPensionista;
    $stFiltro .= " AND registro_pensionista.registro = ".$_REQUEST['inRegistro'];
    $obTPessoalContratoPensionista->recuperaPensionistas($rsContratoPensionista,$stFiltro);
    $obTPessoalPensionistaCid = new TPessoalPensionistaCid;
    $obTPessoalPensionistaCid->setDado("cod_pensionista",$rsContratoPensionista->getCampo('cod_pensionista'));
    $obTPessoalPensionistaCid->setDado("cod_contrato_cedente",$rsContratoPensionista->getCampo('cod_contrato_cedente'));
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
    $inCodProfissao                 = $rsContratoPensionista->getCampo("cod_profissao");
    $inCodPensionista               = $rsContratoPensionista->getCampo("cod_pensionista");
    $inCodGrauParentesco            = $rsContratoPensionista->getCampo("cod_grau");
    $inNumBeneficio                 = $rsContratoPensionista->getCampo("num_beneficio");
    $inCodTipoDependencia           = $rsContratoPensionista->getCampo("cod_dependencia");
    $nuPercentualPagamentoPensao    = str_replace(".",",",$rsContratoPensionista->getCampo("percentual_pagamento"));
    $dtInicioBeneficio              = $rsContratoPensionista->getCampo("dt_inicio_beneficio");
    $dtEncerramentoBeneficio        = $rsContratoPensionista->getCampo("dt_encerramento");
    $stMotivoEncerramento           = $rsContratoPensionista->getCampo("motivo_encerramento");
    $inCodCID                       = $rsPensionistaCid->getCampo("cod_cid");
    $inSiglaCID                     = $rsCID->getCampo('sigla');
    $stDescricaoCID                 = $rsCID->getCampo('descricao');
    $inCodLotacao                   = $rsOrgao->getCampo("cod_orgao");
    $dtDataLaudo                    = $rsPensionistaCid->getCampo('data_laudo');
    if($rsProcesso->getCampo("cod_processo")){
        $stChaveProcesso                = $rsProcesso->getCampo("cod_processo")."/".$rsProcesso->getCampo("ano_exercicio");
    }
    $jsOnload   = "executaFuncaoAjax('processarForm','&inCGM=".$_REQUEST['inCGM']."&stAcao=".$stAcao."&stChaveProcesso=".$stChaveProcesso."&inCodContrato=".$rsContratoPensionista->getCampo("cod_contrato")."');";
}

if($rsContratoPensionista->getNumLinhas() > 0){
    foreach($rsContratoPensionista->getElementos() as $Key){
        $_REQUEST = $Key;
    }
}
$_REQUEST['cod_cid'] = $rsPensionistaCid->getCampo('cod_cid');
$_REQUEST['data_laudo'] = $rsPensionistaCid->getCampo('data_laudo');

include_once CAM_GRH_PES_POPUPS.'servidor/FMConsultarPensionistaAbaInformacao.php';

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

$obLblCID = new Label;
$obLblCID->setRotulo ( "CID" );
$obLblCID->setValue ( $_REQUEST['cod_cid']  );
$obLblCID->setId ( "stCID"        );

$obLblDataLaudo  = new Label;
$obLblDataLaudo->setName      ( "dtDataLaudo" );
$obLblDataLaudo->setId        ( "dtDataLaudo" );
$obLblDataLaudo->setValue     ( $_REQUEST['data_laudo'] );
$obLblDataLaudo->setRotulo    ("Data do Laudo ");

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
$obFormulario->addTitulo     ( "Informações do Pensionista" );
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
$obFormulario->addComponente ( $obLblOcupacao          );
$obFormulario->addComponente ( $obLblCID               );
$obFormulario->addComponente ( $obLblDataLaudo         );
$obFormulario->addComponente ( $obLblGrauParentesco    );
$obFormulario->addTitulo     ( "Informações Bancárias" );
$obFormulario->addComponente ( $obLblBanco             );
$obFormulario->addComponente ( $obLblAgencia           );
$obFormulario->addComponente ( $obLblContaCorrente     );
$obFormulario->addTitulo     ( "Dados da Matrícula do Gerador do Benefício" );
$obFormulario->addComponente ( $obLblCGMServidor       );
$obFormulario->addComponente ( $obLblContratoServidor  );
$obFormulario->addHidden     ( $obHdnContratoServidor  );
$obFormulario->addHidden     ( $obHdnRegistroServidor  );
$obFormulario->addTitulo     ( "Dados da Matrícula do Pensionista"  );
$obFormulario->addComponente ( $obLblContratoPensionista );
$obFormulario->addHidden     ( $obHdnContratoPensionista );
$obFormulario->addHidden     ( $obHdnCodCID              );
$obFormulario->addComponente ( $obLblNumBeneficio              );
$obFormulario->addComponente ( $obLblProcesso                  );
$obFormulario->addComponente ( $obLblDataInclusaoProcessao     );
$obFormulario->addComponente ( $obLblTipoDependencia           );
$obFormulario->addComponente ( $obLblPercentualPagamentoPensao );
$obFormulario->addComponente ( $obDtaInicioBeneficio           );
$obFormulario->addComponente ( $obDtaEncerramentoBeneficio     );
$obFormulario->addSpan       ( $obSpnCalculoPensao             );
$obFormulario->addComponente ( $obTxtMotivoEncerramento        );

$obIMontaOrganograma->geraFormulario  ( $obFormulario );
$obFormulario->addSpan ( $obSpanPrevidencia );
$obBtnFechar = new Button;
$obBtnFechar->setName                     ( "btnFecharCampos"             );
$obBtnFechar->setValue                    ( "    Fechar    "              );
$obBtnFechar->setTipo                     ( "button"                      );
$obBtnFechar->obEvento->setOnClick        ( "javascript:window.close();"  );

$obFormulario->defineBarra(array($obBtnFechar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
