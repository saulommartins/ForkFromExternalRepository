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
* Tela de filtro de solicitações
* Data de Criação: 27/09/2006

* @author Analista     : Cleisson
* @author Desenvolvedor: Bruce Cruz de Sena

  $Id: FMManterHomologacaoSolicitacaoCompra.php 63367 2015-08-20 21:27:34Z michel $

* Casos de uso: uc-03.04.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES.'ILabelEntidade.class.php';
include_once CAM_GP_ALM_COMPONENTES.'ILabelAlmoxarifado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/CGM/classes/componentes/ILabelCGM.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasSolicitacao.class.php';
include_once CAM_GP_COM_COMPONENTES.'ILabelEditObjeto.class.php';
include_once TCOM.'TComprasConfiguracao.class.php';

SistemaLegado::LiberaFrames(true,true);

$stPrograma = "ManterHomologacaoSolicitacaoCompra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTComprasSolicitacao = new TComprasSolicitacao;
$obTComprasSolicitacao->setDado( 'cod_solicitacao', $_REQUEST['cod_solicitacao'] );
$obTComprasSolicitacao->setDado( 'exercicio'      , $_REQUEST['exercicio']       );
$obTComprasSolicitacao->setDado( 'cod_entidade'   , $_REQUEST['cod_entidade']    );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ( "stExercicio" );
$obHdnExercicio->setValue( $_REQUEST['exercicio'] );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "stCodEntidade" );
$obHdnCodEntidade->setValue( $_REQUEST['cod_entidade'] );

$obHdnCodSolicitacao = new Hidden;
$obHdnCodSolicitacao->setName ( "stCodSolicitacao" );
$obHdnCodSolicitacao->setValue( $_REQUEST['cod_solicitacao'] );

$obTComprasSolicitacao->consultar();

$obTConfiguracao = new TComprasConfiguracao();
$obTConfiguracao->setDado("parametro","reserva_rigida");
$obTConfiguracao->recuperaPorChave($rsConfiguracao);

$boReservaRigida = $rsConfiguracao->getCampo('valor') == 'true' ? true : false;

$obTConfiguracao->setDado("parametro","reserva_autorizacao");
$obTConfiguracao->recuperaPorChave($rsConfiguracao);
$boReservaAutorizacao = $rsConfiguracao->getCampo('valor') == 'true' ? true : false;

if(!$boReservaRigida && !$boReservaAutorizacao){
    $stMsg = "Obrigatório Configurar o Tipo de Reserva em: Gestão Patrimonial :: Compras :: Configuração :: Alterar Configuração";
    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$_REQUEST["stAcao"],$stMsg,"unica","aviso", Sessao::getId(), "../");
}else
    $stReserva = ($boReservaRigida) ? 'reserva_rigida' : 'reserva_autorizacao';

include_once ( $pgOcul );

$stAcao = $_REQUEST["stAcao"];

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//Registro de Preço
$obLblRegistroPreco = new Label;
$obLblRegistroPreco->setId     ( 'stRegistroPreco' );
$obLblRegistroPreco->setrotulo ( 'Registro de Preço' );
$obLblRegistroPreco->setValue  ( ($obTComprasSolicitacao->getDado('registro_precos') == 't') ? 'Sim' : 'Não' );

//Exercicio
$obLblExercicio = new Label;
$obLblExercicio->setId     ( 'stExercicio' );
$obLblExercicio->setrotulo ( 'Exercício'   );
$obLblExercicio->setValue  ( $obTComprasSolicitacao->getDado('exercicio') );

//Entidade
$obILabelEntidade = new ILabelEntidade( $obForm );
$obILabelEntidade->setMostraCodigo( true           );
$obILabelEntidade->setCodEntidade ( $obTComprasSolicitacao->getDado( 'cod_entidade' ) );

// Solicitação
$obLblSolicitacao = new Label;
$obLblSolicitacao->setId     ( 'stSolicitacao' );
$obLblSolicitacao->setrotulo ( 'Solicitação'   );
$obLblSolicitacao->setValue  ( $_REQUEST['cod_solicitacao'] );

// almoxarifado
$obLblAlmoxarifado = new ILabelAlmoxarifado($obForm);
$obLblAlmoxarifado->setCodAlmoxarifado( $obTComprasSolicitacao->getDado( 'cod_almoxarifado' ) );

// Data da solicitação
$arDataSolicitacao = substr( $obTComprasSolicitacao->getDado( 'timestamp' ),0,10);
$arDataSolicitacao = explode ("-", $arDataSolicitacao);
$arDataSolicitacao = $arDataSolicitacao[2]."/".$arDataSolicitacao[1].'/'.$arDataSolicitacao[0];

$obLblDataSolicitacao = new Label;
$obLblDataSolicitacao->setId     ( 'stDataSolicitacao' );
$obLblDataSolicitacao->setrotulo ( 'Data Solicitação'  );
$obLblDataSolicitacao->setValue  ( $arDataSolicitacao  );

// Objeto
$obILabelEditObjeto = new ILabelEditObjeto;
$obILabelEditObjeto->setRotulo   ( 'Objeto' );
$obILabelEditObjeto->setCodObjeto( stripslashes($obTComprasSolicitacao->getDado ( 'cod_objeto' )) );

//Requisitante CGM
$obLblRequisitante = new ILabelCGM();
$obLblRequisitante->setRotulo ( "Requisitante" );
$obLblRequisitante->setNumCGM ( $obTComprasSolicitacao->getDado('cgm_requisitante') );

// Solicitante CGM
$obLblSolicitante = new ILabelCGM();
$obLblSolicitante->setRotulo ( "Solicitante" );
$obLblSolicitante->setNumCGM ( $obTComprasSolicitacao->getDado( 'cgm_solicitante' ) );

$obSpnItens = new Span;
$obSpnItens->setId ( 'spnItens' );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;

$obBtnCancelar = new Button;
$obBtnCancelar->setName                    ( "btnClean"                        );
$obBtnCancelar->setValue                   ( "Cancelar"                        );
$obBtnCancelar->setTipo                    ( "button"                          );
$obBtnCancelar->setDisabled                ( false                             );
$obBtnCancelar->obEvento->setOnClick       ( "Cancelar('".$stLocation."');"    );

$obBtnOK = new Ok(true);

$botoesForm = array ( $obBtnOK , $obBtnCancelar );

$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-03.04.02');
$obFormulario->addForm       ( $obForm                );
$obFormulario->addHidden     ( $obHdnAcao             );
$obFormulario->addHidden     ( $obHdnCtrl             );
$obFormulario->addHidden     ( $obHdnExercicio        );
$obFormulario->addHidden     ( $obHdnCodEntidade      );
$obFormulario->addHidden     ( $obHdnCodSolicitacao   );
$obFormulario->addTitulo     ( 'Dados da Solicitação' );
$obFormulario->addComponente ( $obLblRegistroPreco    );
$obFormulario->addComponente ( $obLblExercicio        );
$obFormulario->addComponente ( $obLblDataSolicitacao  );
$obFormulario->addComponente ( $obILabelEntidade      );
$obFormulario->addComponente ( $obLblSolicitacao      );
$obFormulario->addComponente ( $obLblAlmoxarifado     );
$obFormulario->addComponente ( $obILabelEditObjeto    );
$obFormulario->addComponente ( $obLblRequisitante     );
$obFormulario->addComponente ( $obLblSolicitante      );
$obFormulario->addSpan       ( $obSpnItens            );
$obFormulario->defineBarra   ( $botoesForm            );
$obFormulario->show();

sistemaLegado::executaFrameOculto(  montaSpanItens ($obTComprasSolicitacao->getDado('exercicio'),
                                                    $obTComprasSolicitacao->getDado( 'cod_entidade' ),
                                                    $_REQUEST['cod_solicitacao'],
                                                    $obTComprasSolicitacao->getDado('registro_precos'),
                                                    $stReserva)
                                 );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
