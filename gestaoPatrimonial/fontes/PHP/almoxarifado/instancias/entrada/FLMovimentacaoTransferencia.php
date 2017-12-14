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
    * Página de Formulario de Inclusao de Sequência de Cálculo
    * Data de Criação: 05/01/2006

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-03.03.09

    $Id:$

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_COMPONENTES."ISelectMultiploAlmoxarifadoAlmoxarife.class.php");
include_once(CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php");
include_once(CAM_GP_ALM_COMPONENTES."IPopUpCentroCusto.class.php");
include_once(CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php");

//Define a função do arquivo, ex: incluir ou alterar
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write('link', '');

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoTransferencia";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$obForm = new Form;
$obForm->setAction( $pgList );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obExercicio = new Exercicio;

$obSelectAlmoxarifadoOrigem = new ISelectMultiploAlmoxarifadoAlmoxarife;
$obSelectAlmoxarifadoOrigem->setRotulo("Almoxarifados de Origem");
$obSelectAlmoxarifadoOrigem->setName ( "stAlmoxarifadosOrigem" );
$obSelectAlmoxarifadoOrigem->SetNomeLista1 ('inCodAlmoxarifadoDispOrigem');
$obSelectAlmoxarifadoOrigem->SetNomeLista2 ('inCodAlmoxarifadoOrigem');

$obSelectAlmoxarifadoDestino = new ISelectMultiploAlmoxarifadoAlmoxarife;
$obSelectAlmoxarifadoDestino->setRotulo("Almoxarifados de Destino");
$obSelectAlmoxarifadoDestino->SetNomeLista1 ('inCodAlmoxarifadoDispDestino');
$obSelectAlmoxarifadoDestino->SetNomeLista2 ('inCodAlmoxarifadoDestino');

$obTxtCodTransferencia = new TextBox;
$obTxtCodTransferencia->setRotulo        ( "Código da Transferência"       );
$obTxtCodTransferencia->setTitle         ( "Informe o código da transferência." );
$obTxtCodTransferencia->setName          ( "inCodTransferencia" );
$obTxtCodTransferencia->setId            ( "inCodTransferencia" );
$obTxtCodTransferencia->setValue         ( $inCodTransferencia  );
$obTxtCodTransferencia->setSize          ( 5                            );
$obTxtCodTransferencia->setMaxLength     ( 10                            );
$obTxtCodTransferencia->setInteiro       ( true                         );

$obTxtObservacao = new TextBox;
$obTxtObservacao->setRotulo        ( "Observação"                      );
$obTxtObservacao->setTitle         ( "Informe a descrição"        );
$obTxtObservacao->setName          ( "stObservacao"                );
$obTxtObservacao->setId            ( "stObservacao"                );
$obTxtObservacao->setValue         ( $stObservacao);
$obTxtObservacao->setSize          ( 50                            );
$obTxtObservacao->setMaxLength     ( 160                            );

$obCmbTipoBusca = new TipoBusca ( $obTxtObservacao );

$obBscItem = new IPopUpItem($obForm);
$obBscItem->setNull(true);

$obBscMarca = new IPopUpMarca($obForm);
$obBscMarca->setTitle("Informe a marca do item.");
$obBscMarca->setNull(true);

$obBscCentroCusto = new IPopUpCentroCusto($obForm);
$obBscCentroCusto->setNull(true);

$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm               );
$obFormulario->addHidden        ( $obHdnAcao            );
$obFormulario->addHidden        ( $obHdnCtrl            );
$obFormulario->addTitulo        ( "Dados para o Filtro" );
$obFormulario->addComponente    ( $obExercicio );
$obFormulario->addComponente    ( $obSelectAlmoxarifadoOrigem );
$obFormulario->addComponente    ( $obSelectAlmoxarifadoDestino );
$obFormulario->addComponente    ( $obTxtCodTransferencia );
$obFormulario->addComponente    ( $obCmbTipoBusca );
$obFormulario->addComponente    ( $obBscItem );
$obFormulario->addComponente    ( $obBscMarca );
$obFormulario->addComponente    ( $obBscCentroCusto );

$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
