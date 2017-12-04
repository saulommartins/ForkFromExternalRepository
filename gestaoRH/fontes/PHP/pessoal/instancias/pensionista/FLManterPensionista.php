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
    * Página de Filtro do Manter Cadastro de Pensionista
    * Data de Criação: 14/08/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30860 $
    $Name$
    $Author: alex $
    $Date: 2007-09-26 18:29:24 -0300 (Qua, 26 Set 2007) $

    * Casos de uso: uc-04.04.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                           );
//include_once ( CAM_GRH_PES_COMPONENTES."IPopUpCGMServidor.class.php"                           );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPensionista";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::remove('link');
$stAcao = $request->get('stAcao');

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

//DEFINICAO DO FORM
$obForm = new Form;
if ($stAcao == "incluir") {
    $obForm->setAction( $pgForm );
    $obIPopUpCGM = new IPopUpCGM($obForm);
    $obIPopUpCGM->setTipo( "fisica" );
} else {
    $obForm->setAction( $pgList );

    $obIPopUpCGMPensionista = new IPopUpCGM($obForm);
    $obIPopUpCGMPensionista->setTipo                          ( "pensionista" );
    $obIPopUpCGMPensionista->setRotulo                        ( "CGM do Pensionista" );
    $obIPopUpCGMPensionista->setTitle                         ( "Informe o CGM do pensionista." );
    $obIPopUpCGMPensionista->obCampoCod->setName              ( "inCGM" );
    $obIPopUpCGMPensionista->obCampoCod->setId                ( "inCGM" );
    $obIPopUpCGMPensionista->setNull                          ( true );
    $obIPopUpCGMPensionista->setId                            ( "inCampoInnerCGM" );
    $obIPopUpCGMPensionista->setFuncaoBusca                   ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarCgm.php','frm','inCGM','inCampoInnerCGM','fisica','".Sessao::getId()."&inFiltro=4','800','550')" );

    $obIContratoDigitoVerificadorPensionista = new IContratoDigitoVerificador;
    $obIContratoDigitoVerificadorPensionista->setExtender                     ( "Pensionista" );
    $obIContratoDigitoVerificadorPensionista->setPagFiltro                    ( true );
    $obIContratoDigitoVerificadorPensionista->setRotulo                       ( "Matrícula do Pensionista");
    $obIContratoDigitoVerificadorPensionista->obTxtRegistroContrato->setRotulo( "Matrícula do Pensionista" );
    $obIContratoDigitoVerificadorPensionista->obTxtRegistroContrato->setTitle ( "Informe o contrato do pensionista." );
    $obIContratoDigitoVerificadorPensionista->setPensionista();

    $obIPopUpCGMServidor = new IPopUpCGM($obForm);
    $obIPopUpCGMServidor->setTipo                          ( "vigente" );
    $obIPopUpCGMServidor->setRotulo                        ( "CGM do Servidor" );
    $obIPopUpCGMServidor->setTitle                         ( "Informe o CGM do servidor." );
    $obIPopUpCGMServidor->obCampoCod->setName              ( "inNumCGMServidor" );
    $obIPopUpCGMServidor->obCampoCod->setId                ( "inNumCGMServidor" );
    $obIPopUpCGMServidor->obCampoCod->setValue             ( $inNumCGMServidor );
    $obIPopUpCGMServidor->setNull                          ( true );
    $obIPopUpCGMServidor->setId                            ( "inNomCGMServidor" );
    $obIPopUpCGMServidor->setFuncaoBusca                   ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarCgm.php','frm','inNumCGMServidor','inNomCGMServidor','fisica','".Sessao::getId()."&inFiltro=2','800','550')" );

    $obIContratoDigitoVerificadorServidor = new IContratoDigitoVerificador;
    $obIContratoDigitoVerificadorServidor->setExtender                     ( "Servidor" );
    $obIContratoDigitoVerificadorServidor->setPagFiltro                    ( true );
    $obIContratoDigitoVerificadorServidor->setRotulo                       ( "Matrícula do Servidor" );
    $obIContratoDigitoVerificadorServidor->obTxtRegistroContrato->setRotulo( "Matrícula do Servidor" );
    $obIContratoDigitoVerificadorServidor->obTxtRegistroContrato->setTitle ( "Informe o contrato do servidor." );

}

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addTitulo                        ( "Filtro para Pensionista"                                             );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );

if ($stAcao == "incluir") {
    $obFormulario->addComponente                ( $obIPopUpCGM                                                          );
} else {
    $obFormulario->addComponente                ( $obIPopUpCGMPensionista                                               );
    $obIContratoDigitoVerificadorPensionista->geraFormulario( $obFormulario                                             );

    $obFormulario->addComponente                ( $obIPopUpCGMServidor                                                           );
    $obIContratoDigitoVerificadorServidor->geraFormulario( $obFormulario                                                );
}

$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
