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
    * Página de Filtro de Mapa de Compras
    * Data de Criação   :06/10/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Revision: 25471 $
    $Name$
    $Author: bruce $
    $Date: 2007-09-13 18:42:35 -0300 (Qui, 13 Set 2007) $

    * Casos de uso: uc-03.04.05
*/

/**
$Log$
Revision 1.4  2007/09/13 21:42:35  bruce
Ticket#9925#

Revision 1.3  2007/04/23 14:45:19  bruce
Bug #9181#

Revision 1.2  2006/11/30 16:54:18  hboaventura
bug #7457"

Revision 1.1  2006/10/06 16:53:45  bruce
filtro de mapas

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_COM_COMPONENTES."IMontaSolicitacao.class.php"                                    );
include_once ( CAM_GP_COM_COMPONENTES."IPopUpObjeto.class.php"                                         );
include_once ( CAM_GF_ORC_COMPONENTES."IPopUpDotacaoFiltroClassificacao.class.php"                                        );
include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeGeral.class.php"                         );
include_once ( CAM_GP_ALM_COMPONENTES."IPopUpCentroCustoUsuario.class.php"                             );
include_once ( CAM_GP_ALM_COMPONENTES."IMontaItemUnidade.class.php"                                    );

$stPrograma = "ManterMapaCompras";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$stAcao = $request->get("stAcao");

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obForm = new Form;
$obForm->setAction ( $pgList  );

//// SelectMultiploNetidadesGeral
$obISelectEntidadeGeral = new ISelectMultiploEntidadeGeral();
$obISelectEntidadeGeral->setName   ('inCodEntidade');
$obISelectEntidadeGeral->setNull ( true );

// lista de entidades disponiveis
$obISelectEntidadeGeral->SetNomeLista1 ('inCodEntidadeDisponivel');
$obISelectEntidadeGeral->setCampoId1   ( 'cod_entidade' );
$obISelectEntidadeGeral->setCampoDesc1 ( 'nom_cgm' );

// lista de entidades selecionados
$obISelectEntidadeGeral->SetNomeLista2 ('inCodEntidade');
$obISelectEntidadeGeral->setCampoId2   ('cod_entidade');
$obISelectEntidadeGeral->setCampoDesc2 ('nom_cgm');

/// codigo da solicitação
$obTextCodSolicitacao = new TextBox;
$obTextCodSolicitacao->setName  ( 'txtCodSolicitacao'                );
$obTextCodSolicitacao->setID    ( 'txtCodSolicitacao'                );
$obTextCodSolicitacao->setRotulo( 'Código da Solicitação'            );
$obTextCodSolicitacao->setTitle ( 'Informe o código da solicitação.' );
$obTextCodSolicitacao->setInteiro ( true );

/// Numero do mapa
$obTextCodMapa = new TextBox;
$obTextCodMapa->setName  ( 'txtCodMapa'                );
$obTextCodMapa->setID    ( 'txtCodMapa'                );
$obTextCodMapa->setRotulo( 'Código do Mapa'            );
$obTextCodMapa->setTitle ( 'Informe o código do Mapa.' );
$obTextCodMapa->setInteiro ( true );

////objeto
$obObjeto = new IPopUpObjeto($obForm);
$obObjeto->setRotulo ("Objeto");

/// Periodicidade
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setValue          ( 4                 );
$obPeriodicidade->obDataInicial->setName    ( "stDtInicial" );
$obPeriodicidade->obDataFinal->setName      ( "stDtFinal" );

/// item
$obMontaItemUnidade = new IMontaItemUnidade($obForm);
$obMontaItemUnidade->obIPopUpCatalogoItem->setRotulo("Item");
$obMontaItemUnidade->obIPopUpCatalogoItem->setNull(true);
$obMontaItemUnidade->obIPopUpCatalogoItem->setNull ( true );

/// Dotacao
$obPopUpDotacao = new IPopUpDotacaoFiltroClassificacao($obISelectEntidadeGeral );

//centro de custo
$obBscCentroCusto = new IPopUpCentroCustoUsuario($obForm);
$obBscCentroCusto->setNull ( true );

$obFormulario = new Formulario;
$obFormulario->setAjuda( 'UC-03.04.05' );
$obFormulario->addForm($obForm);
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Dados para Filtro" );

$obFormulario->addComponente ( $obISelectEntidadeGeral );
$obFormulario->addComponente ( $obTextCodSolicitacao   );
$obFormulario->addComponente ( $obTextCodMapa          );
$obFormulario->addComponente ( $obObjeto               );
$obFormulario->addComponente ( $obPeriodicidade        );
$obMontaItemUnidade->geraFormulario( $obFormulario  );
$obFormulario->addComponente ( $obPopUpDotacao         );
$obFormulario->addComponente ( $obBscCentroCusto       );

$obFormulario->ok();
$obFormulario->show();

?>
