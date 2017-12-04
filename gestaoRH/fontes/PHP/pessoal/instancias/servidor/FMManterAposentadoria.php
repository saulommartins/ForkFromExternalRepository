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
    * Página de Form do Aposentadoria
    * Data de Criação: 21/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30930 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-19 10:03:42 -0300 (Ter, 19 Jun 2007) $

    * Casos de uso: uc-04.04.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                       );
include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalClassificacao.class.php"                                  );
include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalAposentadoria.class.php"                                  );
include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalAposentadoriaEncerramento.class.php"                      );
include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                                       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAposentadoria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

if ($stAcao == "alterar" or $stAcao == "excluir"  or $stAcao == "consultar") {
    $obTPessoalContrato = new TPessoalContrato();
    $stFiltro = " AND registro = ".$_POST['inContrato'];
    $obTPessoalContrato->recuperaCgmDoRegistro($rsContrato,$stFiltro);
    $obTPessoalAposentadoria = new TPessoalAposentadoria();
    $stFiltro = " AND aposentadoria.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
    $obTPessoalAposentadoria->recuperaRelacionamento($rsAposentadoria,$stFiltro);
    if ( $rsAposentadoria->getNumLinhas() == 1 ) {
        $obTPessoalAposentadoriaEncerramento = new TPessoalAposentadoriaEncerramento();
        $stFiltro  = " WHERE cod_contrato = ".$rsContrato->getCampo("cod_contrato");
        $stFiltro .= "   AND timestamp = '".$rsAposentadoria->getCampo("timestamp")."'";
        $obTPessoalAposentadoriaEncerramento->recuperaTodos($rsAposentadoriaEncerramento,$stFiltro);

        $inRegistro                  = $rsContrato->getCampo("registro");
        $inCodContrato               = $rsContrato->getCampo("cod_contrato");
        $inNumCGM                    = $rsContrato->getCampo("numcgm");
        $stNomCGM                    = $rsContrato->getCampo("nom_cgm");
        $dtRequerimentoAposentadoria = $rsAposentadoria->getCampo("data_requirimento");
        $dtConcessao                 = $rsAposentadoria->getCampo("data_concessao");
        $dtPublicacao                = $rsAposentadoria->getCampo("data_publicacao");
        $stNrProcesso                = $rsAposentadoria->getCampo("num_processo_tce");
        $nuPercentual                = str_replace(".",",",$rsAposentadoria->getCampo("percentual"));
        $inCodClassificacao          = $rsAposentadoria->getCampo("cod_classificacao");
        $dtEncerramento              = $rsAposentadoriaEncerramento->getCampo("dt_encerramento");
        $stMotivo                    = $rsAposentadoriaEncerramento->getCampo("motivo");
        if ($stAcao == "alterar") {

            $jsOnload = "executaFuncaoAjax('preencherAlterar','"."&inCodClassificacao=".$rsAposentadoria->getCampo('cod_classificacao')."&inCodEnquadramento=".$rsAposentadoria->getCampo('cod_enquadramento')."&inCodContrato=".$rsContrato->getCampo('cod_contrato')."');";

        }
        if ($stAcao == "excluir" or $stAcao == "consultar") {
            $jsOnload = "executaFuncaoAjax('preencherExcluir','"."&inCodClassificacao=".$rsAposentadoria->getCampo('cod_classificacao')."&inCodEnquadramento=".$rsAposentadoria->getCampo('cod_enquadramento')."&inCodContrato=".$rsContrato->getCampo('cod_contrato')."');";
        }
    }
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obIFiltroContrato = new IFiltroContrato;
$obIFiltroContrato->setTituloFormulario("Dados da Aposentadoria");
$obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->setValue($inRegistro);
$obIFiltroContrato->obIContratoDigitoVerificador->setTipo('servidor');
$obIFiltroContrato->obIContratoDigitoVerificador->setNull(false);
if ($stAcao == "alterar" or $stAcao == "excluir" or $stAcao == "consultar") {
    $obIFiltroContrato->obIContratoDigitoVerificador->setPagFiltro(false);
    $obIFiltroContrato->obIContratoDigitoVerificador->setAutomatico(true);
    $obIFiltroContrato->obIContratoDigitoVerificador->obLblRegistroContrato->setValue($inRegistro);
    $obIFiltroContrato->obIContratoDigitoVerificador->obHdnRegistroContrato->setValue($inCodContrato);
    $obIFiltroContrato->obLblCGM->setValue($inNumCGM."-".$stNomCGM);
    $obIFiltroContrato->obHdnCGM->setValue($inNumCGM."-".$stNomCGM);
}

$obDataRequerimento = new Data();
$obDataRequerimento->setName                    ( "dtRequerimentoAposentadoria"                                         );
$obDataRequerimento->setValue                   ( $dtRequerimentoAposentadoria                                          );
$obDataRequerimento->setRotulo                  ( "Data de Requerimento da Aposentadoria"                               );
$obDataRequerimento->setTitle                   ( "Informe a data de requerimento da aposentadoria."                    );
$obDataRequerimento->setNull                    ( false                                                                 );
$obDataRequerimento->obEvento->setOnChange      ( "montaParametrosGET('comparaDatas','dtEncerramento,dtRequerimentoAposentadoria,dtConcessao,dtPublicacao');");

$obLblDataRequerimento = new Label();
$obLblDataRequerimento->setRotulo               ( "Data de Requerimento da Aposentadoria"                               );
$obLblDataRequerimento->setId                   ( "dtRequerimentoAposentadoria"                                         );
$obLblDataRequerimento->setValue                ( $dtRequerimentoAposentadoria                                          );

$obDataConcessao = new Data();
$obDataConcessao->setName                       ( "dtConcessao"                                                         );
$obDataConcessao->setValue                      ( $dtConcessao                                                          );
$obDataConcessao->setRotulo                     ( "Data de Concessão do Benefício"                                      );
$obDataConcessao->setTitle                      ( "Informe a data de concessão do benefício."                           );
$obDataConcessao->setNull                       ( false                                                                 );
$obDataConcessao->obEvento->setOnChange         ( "montaParametrosGET('comparaDatas','dtEncerramento,dtRequerimentoAposentadoria,dtConcessao,dtPublicacao');");

$obLblDataConcessao = new Label();
$obLblDataConcessao->setRotulo                  ( "Data de Concessão do Benefício"                                      );
$obLblDataConcessao->setId                      ( "dtConcessao"                                                         );
$obLblDataConcessao->setValue                   ( $dtConcessao                                                          );

$obDataPublicacao = new Data();
$obDataPublicacao->setName                      ( "dtPublicacao"                                                        );
$obDataPublicacao->setValue                     ( $dtPublicacao                                                         );
$obDataPublicacao->setRotulo                    ( "Data da Publicação"                                                  );
$obDataPublicacao->setTitle                     ( "Informe a data da publicação da aposentadoria."                      );
$obDataPublicacao->setNull                      ( false                                                                 );
$obDataPublicacao->obEvento->setOnChange        ( "montaParametrosGET('comparaDatas','dtEncerramento,dtRequerimentoAposentadoria,dtConcessao,dtPublicacao');");

$obLblDataPublicacao = new Label();
$obLblDataPublicacao->setRotulo                 ( "Data de Publicação"                                                  );
$obLblDataPublicacao->setId                     ( "dtPublicacao"                                                         );
$obLblDataPublicacao->setValue                  ( $dtPublicacao                                                          );

$obDataEncerramento = new Data();
$obDataEncerramento->setName                    ( "dtEncerramento"                                                       );
$obDataEncerramento->setValue                   ( $dtEncerramento                                                        );
$obDataEncerramento->setRotulo                  ( "Data de Encerramento"                                                 );
$obDataEncerramento->setTitle                   ( "Informe a data de encerramento da concessão do benefício."            );
$obDataEncerramento->obEvento->setOnChange      ( "montaParametrosGET('comparaDatas','dtEncerramento,dtRequerimentoAposentadoria,dtConcessao,dtPublicacao');");

$obLblDataEncerramento = new Label();
$obLblDataEncerramento->setRotulo               ( "Data de Encerramento"                                                 );
$obLblDataEncerramento->setId                   ( "dtEncerramento"                                                       );
$obLblDataEncerramento->setValue                ( $dtEncerramento                                                        );

$obTPessoalClassificacao = new TPessoalClassificacao;
$obTPessoalClassificacao->recuperaTodos($rsClassificacao);
$obCmbClassificacao = new Select;
$obCmbClassificacao->setRotulo                  ( "Classificação Regra Aposentadoria"                                   );
$obCmbClassificacao->setTitle                   ( "Selecione a classificação da regra de aposentadoria."                );
$obCmbClassificacao->setName                    ( "inCodClassificacao"                                                  );
$obCmbClassificacao->setId                      ( "inCodClassificacao"                                                  );
$obCmbClassificacao->setValue                   ( $inCodClassificacao                                                   );
//$obCmbClassificacao->setStyle                   ( "width: 200px"                                                        );
$obCmbClassificacao->addOption                  ( "", "Selecione"                                                       );
$obCmbClassificacao->setCampoId                 ( "cod_classificacao"                                                   );
$obCmbClassificacao->setCampoDesc               ( "nome_classificacao"                                                  );
$obCmbClassificacao->preencheCombo              ( $rsClassificacao                                                      );
$obCmbClassificacao->setNull                    ( false                                                                 );
$obCmbClassificacao->obEvento->setOnChange      ( "montaParametrosGET('preencherEnquadramento','inCodClassificacao');"  );

$obLblClassificacao = new Label();
$obLblClassificacao->setRotulo                  ( "Classificação Regra Aposentadoria"                                   );
$obLblClassificacao->setId                      ( "stClassificacao"                                                     );
$obLblClassificacao->setValue                   ( $stClassificacao                                                      );

$obCmbEnquadramento = new Select;
$obCmbEnquadramento->setRotulo                  ( "Enquadramento da Aposentadoria"                                      );
$obCmbEnquadramento->setTitle                   ( "Selecione o enquadramento da aposentadoria."                         );
$obCmbEnquadramento->setName                    ( "inCodEnquadramento"                                                  );
$obCmbEnquadramento->setId                      ( "inCodEnquadramento"                                                  );
$obCmbEnquadramento->setValue                   ( $inCodEnquadramento                                                   );
//$obCmbEnquadramento->setStyle                   ( "width: 200px"                                                        );
$obCmbEnquadramento->addOption                  ( "", "Selecione"                                                       );
$obCmbEnquadramento->setNull                    ( false                                                                 );
$obCmbEnquadramento->obEvento->setOnChange      ( "montaParametrosGET('preencherReajuste','inCodEnquadramento,inCodClassificacao');"       );

$obLblEnquadramento = new Label();
$obLblEnquadramento->setRotulo                  ( "Enquadramento da Aposentadoria"                                      );
$obLblEnquadramento->setId                      ( "stEnquadramento"                                                     );
$obLblEnquadramento->setValue                   ( $stEnquadramento                                                      );

$obTxtNrProcesso = new TextBox();
$obTxtNrProcesso->setName                       ( "stNrProcesso"                                                        );
$obTxtNrProcesso->setRotulo                     ( "Nr. do Processo TCE"                                                 );
$obTxtNrProcesso->setValue                      ( $stNrProcesso                                                         );
$obTxtNrProcesso->setTitle                      ( "Informe o número do processo de aposentadoria no TCE."               );
$obTxtNrProcesso->setNull                       ( false                                                                 );
$obTxtNrProcesso->setSize                       ( 10                                                                    );
$obTxtNrProcesso->setMaxLength                  ( 10                                                                    );

$obLblNrProcesso = new Label();
$obLblNrProcesso->setRotulo                     ( "Nr. do Processo TCE"                                                 );
$obLblNrProcesso->setId                         ( "stNrProcesso"                                                        );
$obLblNrProcesso->setValue                      ( $stNrProcesso                                                         );

$obLblTipoReajuste = new Label();
$obLblTipoReajuste->setRotulo                   ( "Tipo de Reajuste"                                                    );
$obLblTipoReajuste->setId                       ( "stTipoReajuste"                                                      );
$obLblTipoReajuste->setValue                    ( $stTipoReajuste                                                       );

$obPercentual = new Porcentagem();
$obPercentual->setName                          ( "nuPercentual"                                                        );
$obPercentual->setValue                         ( $nuPercentual                                                         );
$obPercentual->setRotulo                        ( "Percentual do Benefício Recebido em Folha"                           );
$obPercentual->setTitle                         ( "Informe o percentual do benefício que será recebido em folha."       );
$obPercentual->setNull                          ( false                                                                 );

$obLblPercentual = new Label();
$obLblPercentual->setRotulo                     ( "Percentual do Benefício Recebido em Folha"                           );
$obLblPercentual->setId                         ( "nuPercentual"                                                        );
$obLblPercentual->setValue                      ( $nuPercentual                                                         );

$obTxtMotivo = new TextArea();
$obTxtMotivo->setName                           ( "stMotivo"                                                            );
$obTxtMotivo->setValue                          ( $stMotivo                                                             );
$obTxtMotivo->setRotulo                         ( "Motivo do Encerramento"                                              );
$obTxtMotivo->setTitle                          ( "Informe o motivo do encerramento da concessão do benefício."         );
$obTxtMotivo->setMaxCaracteres                  ( 160                                                                   );

$obLblMotivo = new Label();
$obLblMotivo->setRotulo                         ( "Motivo do Encerramento"                                              );
$obLblMotivo->setId                             ( "stMotivo"                                                            );
$obLblMotivo->setValue                          ( $stMotivo                                                             );

$obBtnExcluir = new Ok();
$obBtnExcluir->setValue("Excluir");
$obBtnExcluir->obEvento->setOnClick("montaParametrosGET('excluir');");

$obBtnVoltar = new Ok();
$obBtnVoltar->setValue("Voltar");
$obBtnVoltar->obEvento->setOnClick("back();");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );
$obForm->setTarget                              ( "oculto"                                                              );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obIFiltroContrato->geraFormulario              ( $obFormulario                                                         );
if ($stAcao == "incluir" or $stAcao == "alterar") {
    $obFormulario->addComponente                    ( $obDataRequerimento                                                   );
    $obFormulario->addComponente                    ( $obTxtNrProcesso                                                      );
    $obFormulario->addComponente                    ( $obDataConcessao                                                      );
    $obFormulario->addComponente                    ( $obCmbClassificacao                                                   );
    $obFormulario->addComponente                    ( $obCmbEnquadramento                                                   );
    $obFormulario->addComponente                    ( $obLblTipoReajuste                                                    );
    $obFormulario->addComponente                    ( $obPercentual                                                         );
    $obFormulario->addComponente                    ( $obDataPublicacao                                                     );
    if ($stAcao == "alterar") {
        $obFormulario->addComponente                ( $obDataEncerramento                                                   );
        $obFormulario->addComponente                ( $obTxtMotivo                                                          );
    }
    $obFormulario->Cancelar( $pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao );
}
if ($stAcao == "excluir" or $stAcao == "consultar") {
    $obFormulario->addComponente                    ( $obLblDataRequerimento                                                );
    $obFormulario->addComponente                    ( $obLblNrProcesso                                                      );
    $obFormulario->addComponente                    ( $obLblDataConcessao                                                   );
    $obFormulario->addComponente                    ( $obLblClassificacao                                                   );
    $obFormulario->addComponente                    ( $obLblEnquadramento                                                   );
    $obFormulario->addComponente                    ( $obLblTipoReajuste                                                    );
    $obFormulario->addComponente                    ( $obLblPercentual                                                      );
    $obFormulario->addComponente                    ( $obLblDataPublicacao                                                  );
    $obFormulario->addComponente                    ( $obLblDataEncerramento                                                );
    $obFormulario->addComponente                    ( $obLblMotivo                                                          );
    if ($stAcao == "excluir") {
        $obFormulario->defineBarra                  ( array($obBtnExcluir)                                                  );
    } else {
        $obFormulario->defineBarra                  ( array($obBtnVoltar)                                                   );
    }
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
