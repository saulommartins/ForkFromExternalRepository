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
    * Página de Filtro do Conferência SEFIP
    * Data de Criação: 26/03/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30829 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-30 12:18:39 -0200 (Ter, 30 Out 2007) $

    * Casos de uso: uc-04.08.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                           );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ConferenciaSEFIP";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

Sessao::write('link', '');

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);
$obIFiltroCompetencia->obCmbMes->obEvento->setOnChange("montaParametrosGET('gerarSpanCompetencia13','inCodMes,inAno');");
$obIFiltroCompetencia->obTxtAno->obEvento->setOnChange($obIFiltroCompetencia->obTxtAno->obEvento->getOnChange()."montaParametrosGET('processarCompetencia','inCodMes,inAno');");

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setAtributoServidor();

$obSpnCompetencia13 = new Span();
$obSpnCompetencia13->setId("spnCompetencia13");

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMARecolhimento.class.php");
$obTIMARecolhimento = new TIMARecolhimento();
$obTIMARecolhimento->recuperaTodos($rsRecolhimento);

$obTxtCodRecolhimento = new TextBox;
$obTxtCodRecolhimento->setRotulo              ( "Código de Recolhimento"                        );
$obTxtCodRecolhimento->setTitle               ( "Selecione o código de recolhimento da sefip."  );
$obTxtCodRecolhimento->setName                ( "inCodRecolhimentoTxt"                          );
$obTxtCodRecolhimento->setSize                ( 6                                               );
$obTxtCodRecolhimento->setMaxLength           ( 3                                               );
$obTxtCodRecolhimento->setInteiro             ( true                                            );
$obTxtCodRecolhimento->setSize                ( 10                                              );
$obTxtCodRecolhimento->setNull                ( false                                           );

$obCmbCodRecolhimento = new Select;
$obCmbCodRecolhimento->setRotulo                ( "Código de Recolhimento"      );
$obCmbCodRecolhimento->setName                  ( "inCodRecolhimento"           );
$obCmbCodRecolhimento->addOption                ( "","Selecione"                );
$obCmbCodRecolhimento->setStyle                 ( "width: 450px"                );
$obCmbCodRecolhimento->setCampoID               ( "cod_recolhimento"            );
$obCmbCodRecolhimento->setCampoDesc             ( "descricao"                   );
$obCmbCodRecolhimento->preencheCombo            ( $rsRecolhimento               );
$obCmbCodRecolhimento->setNull                  ( false                         );

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAModalidadeRecolhimento.class.php");
$obTIMAModalidadeRecolhimento = new TIMAModalidadeRecolhimento();
$obTIMAModalidadeRecolhimento->recuperaTodos($rsModalidadeRecolhimento);

$obTxtCodModalidadeRecolhimento = new TextBox;
$obTxtCodModalidadeRecolhimento->setRotulo              ( "Modalidade do Recolhimento"                      );
$obTxtCodModalidadeRecolhimento->setTitle               ( "Informe a modalidade do recolhimento da sefip. Para competências anteriores à 10/1998, deve ser igual a 0,1,7 ou 8. Para competência 13, deve ser igual a 1 ou 9." );
$obTxtCodModalidadeRecolhimento->setName                ( "inCodModalidadeRecolhimentoTxt"                  );
$obTxtCodModalidadeRecolhimento->setSize                ( 6                                                 );
$obTxtCodModalidadeRecolhimento->setMaxLength           ( 3                                                 );
$obTxtCodModalidadeRecolhimento->setInteiro             ( true                                              );
$obTxtCodModalidadeRecolhimento->setSize                ( 10                                                );
$obTxtCodModalidadeRecolhimento->setNull                ( false                                             );

$obCmbCodModalidadeRecolhimento = new Select;
$obCmbCodModalidadeRecolhimento->setRotulo              ( "Modalidade do Recolhimento"                      );
$obCmbCodModalidadeRecolhimento->setName                ( "inCodModalidadeRecolhimento"                     );
$obCmbCodModalidadeRecolhimento->setStyle               ( "width: 450px"                                    );
$obCmbCodModalidadeRecolhimento->addOption              ( "","Selecione"                                    );
$obCmbCodModalidadeRecolhimento->setCampoID             ( "sefip"                                           );
$obCmbCodModalidadeRecolhimento->setCampoDesc           ( "descricao"                                       );
$obCmbCodModalidadeRecolhimento->preencheCombo          ( $rsModalidadeRecolhimento                         );
$obCmbCodModalidadeRecolhimento->setNull                ( false                                             );

$obBtnOk = new Ok;
$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter','',true);");

$obBtnLimpar = new Limpar();

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setTarget("telaPrincipal");
$obForm->setAction                              ( $pgProc                                                               );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addTitulo                        ( "Seleção do Filtro"                                                   );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obIFiltroCompetencia->geraFormulario($obFormulario);
$obFormulario->addSpan($obSpnCompetencia13);
$obFormulario->addComponenteComposto($obTxtCodRecolhimento,$obCmbCodRecolhimento);
$obFormulario->addComponenteComposto($obTxtCodModalidadeRecolhimento,$obCmbCodModalidadeRecolhimento);
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar)                                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
