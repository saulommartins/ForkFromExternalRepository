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
    * Data de Criação: 26/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: FMManterManutencao.php 46943 2012-06-29 12:10:50Z tonismar $

    * Casos de uso: uc-03.02.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaInfracao.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaMotivoInfracao.class.php' );
include_once( CAM_GP_FRO_COMPONENTES.'IPopUpVeiculo.class.php' );
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );

$stPrograma = "ManterInfracao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//cria um novo formulario
$obForm = new Form;
$obForm->setAction($pgList);
$obForm->setTarget("telaPrincipal");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//cria um hidden para o id do item
$obHdnId = new Hidden();
$obHdnId->setId('hdnId');

//instancia o componente IPopUpVeiculo
$obIPopUpVeiculo = new IPopUpVeiculo($obForm);

//instancia o componente IPopUpCGMVinculado para o responsavel
$obIPopUpMotorista = new IPopUpCGMVinculado( $obForm );
$obIPopUpMotorista->setTabelaVinculo    ( 'frota.motorista' );
$obIPopUpMotorista->setCampoVinculo     ( 'cgm_motorista'        );
$obIPopUpMotorista->setNomeVinculo      ( 'Motorista'            );
$obIPopUpMotorista->setRotulo           ( 'Motorista'            );
$obIPopUpMotorista->setTitle            ( 'Informe o motorista do veículo.' );
$obIPopUpMotorista->setName             ( 'stNomMotorista'       );
$obIPopUpMotorista->setId               ( 'stNomMotorista'       );
$obIPopUpMotorista->obCampoCod->setName ( 'inCodMotorista'       );
$obIPopUpMotorista->obCampoCod->setId   ( 'inCodMotorista'       );
$obIPopUpMotorista->setNull             ( true                   );

$obTxtAutoInfracao = new TextBox();
$obTxtAutoInfracao->setRotulo   ( 'Auto de infração' );
$obTxtAutoInfracao->setName     ( 'stAutoInfracao'   );
$obTxtAutoInfracao->setId       ( 'stAutoInfracao'   );
$obTxtAutoInfracao->setStyle    ( 'width: 200px'     );
$obTxtAutoInfracao->setMaxLength( 15                 );
$obTxtAutoInfracao->setValue    ( ''                 );

$obDtInfracao = new Data();
$obDtInfracao->setName  ( 'dtInfracao' );
$obDtInfracao->setId    ( 'dtInfracao' );
$obDtInfracao->setRotulo( 'Data da Infração' );
$obDtInfracao->setValue ( '' );

//Carrega itens
$obTFrotaMotivoInfracao = new TFrotaMotivoInfracao();
$obTFrotaMotivoInfracao->recuperaTodos( $rsMotivoInfracao );

$obSelectMotivoInfracao = new Select();
$obSelectMotivoInfracao->setName      ( 'inCodInfracao'   );
$obSelectMotivoInfracao->setId        ( 'inCodInfracao'   );
$obSelectMotivoInfracao->setRotulo    ( 'Motivo'          );
$obSelectMotivoInfracao->setTitle     ( 'Selecione o motivo da infração.' );
$obSelectMotivoInfracao->setCampoId   ( 'cod_infracao'    );
$obSelectMotivoInfracao->setCampoDesc ( 'descricao'       );
$obSelectMotivoInfracao->addOption    ( '', 'Selecione'   );
$obSelectMotivoInfracao->preencheCombo( $rsMotivoInfracao );
$obSelectMotivoInfracao->obEvento->setOnChange( "montaParametrosGET('montaDados','inCodInfracao');" );
$obSelectMotivoInfracao->setStyle     ( 'width: 400px'    );

$obTxtBaseLegal = new TextBox();
$obTxtBaseLegal->setName    ( 'stBaseLegal'  );
$obTxtBaseLegal->setId      ( 'stBaseLegal'  );
$obTxtBaseLegal->setRotulo  ( 'Base Legal'   );
$obTxtBaseLegal->setTitle   ( 'Base Legal'   );
$obTxtBaseLegal->setReadOnly( true           );
$obTxtBaseLegal->setStyle   ( 'width: 400px' );

$obTxtGravidade = new TextBox();
$obTxtGravidade->setName    ( 'stGravidade'  );
$obTxtGravidade->setId      ( 'stGravidade'  );
$obTxtGravidade->setRotulo  ( 'Gravidade'    );
$obTxtGravidade->setTitle   ( 'Gravidade'    );
$obTxtGravidade->setReadOnly( true           );
$obTxtGravidade->setStyle   ( 'width: 400px' );

$obTxtPontos = new TextBox();
$obTxtPontos->setName    ( 'stPontos'     );
$obTxtPontos->setId      ( 'stPontos'     );
$obTxtPontos->setRotulo  ( 'Pontos'       );
$obTxtPontos->setTitle   ( 'Pontos'       );
$obTxtPontos->setReadOnly( true           );
$obTxtPontos->setStyle   ( 'width: 400px' );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addTitulo    ( 'Dados da Filtro' );
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnId );
$obFormulario->addComponente( $obIPopUpVeiculo );
$obFormulario->addComponente( $obIPopUpMotorista );

$obFormulario->addComponente( $obTxtAutoInfracao      );
$obFormulario->addComponente( $obDtInfracao           );
$obFormulario->addComponente( $obSelectMotivoInfracao );
$obFormulario->addComponente( $obTxtBaseLegal         );
$obFormulario->addComponente( $obTxtGravidade         );
$obFormulario->addComponente( $obTxtPontos            );

if ($stAcao == 'incluir') {
    $obFormulario->OK(true);
} else {
    $obFormulario->Cancelar(  $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
