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
* Tela do formulário para filtro de Solicitação de compra
* Data de Criação: 24/09/2006

* @author Analista     : Cleisson
* @author Desenvolvedor: Bruce Cruz de Sena

* Casos de uso: uc-03.04.02
*/

/*$Log$
 *Revision 1.8  2007/08/15 16:03:28  hboaventura
 *Bug#9925#
 *
/*Revision 1.7  2007/05/31 14:25:36  hboaventura
/*Bug #9179#
/*
/*Revision 1.6  2006/12/20 12:31:53  bruce
/*corrigidios filtros da homologação
/*
/*Revision 1.5  2006/11/10 13:06:40  hboaventura
/*bug #7419#
/*
/*Revision 1.4  2006/11/09 18:43:12  hboaventura
/*bug #7422#
/*
/*Revision 1.3  2006/10/03 09:21:55  bruce
/*Colocado número de UC e tag de log
/*
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeGeral.class.php"                                     );
include_once(CAM_GP_COM_COMPONENTES."IPopUpEditObjeto.class.php"                                                 );
include_once( CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php"                                                      );
include_once( CAM_GF_ORC_COMPONENTES."IPopUpDotacaoFiltroClassificacao.class.php"                                                   );
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php"                                   );
include_once(CAM_GP_ALM_COMPONENTES."IPopUpCentroCustoUsuario.class.php"                                         );

$stPrograma = "ManterHomologacaoSolicitacaoCompra";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::remove('filtro');

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

$stAcao = $request->get('stAcao');

#sessao->transf3['arValores'] = array();

Sessao::write('arValores', array());

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnStCtrl = new Hidden;
$obHdnStCtrl->setName ( "stCtrl" );
$obHdnStCtrl->setValue( $stCtrl );

$obISelectEntidade = new ISelectMultiploEntidadeGeral();
$obISelectEntidade->setNull( true );

// lista de entidades disponiveis
$obISelectEntidade->SetNomeLista1 ('inCodEntidadeDisponivel');
$obISelectEntidade->setCampoId1   ( 'cod_entidade' );
$obISelectEntidade->setCampoDesc1 ( 'nom_cgm' );

// lista de entidades selecionados
$obISelectEntidade->SetNomeLista2 ('inCodEntidade');
$obISelectEntidade->setCampoId2   ('cod_entidade');
$obISelectEntidade->setCampoDesc2 ('nom_cgm');

$obTxtSolicitacao = new TextBox;
$obTxtSolicitacao->setName      ( "stSolicitacao"              );
$obTxtSolicitacao->setValue     ( ""                           );
$obTxtSolicitacao->setRotulo    ( "Solicitação"                );
$obTxtSolicitacao->setTitle     ( "Informe a Solicitação."      );
$obTxtSolicitacao->setNull      ( true                         );
$obTxtSolicitacao->setMaxLength ( 4                            );
$obTxtSolicitacao->setSize      ( 5                            );

$obObjeto = new IPopUpEditObjeto($obForm);
$obObjeto->setNull( true        );
$obObjeto->setName( 'stObjeto'  );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setValidaExercicio( true              );
$obPeriodicidade->setNull           ( true              );
$obPeriodicidade->setValue          ( 4                 );

$obBscItem = new IPopUpItem($obForm);
$obBscItem->setNull( true );
$obBscItem->setRetornaUnidade( false );

$obPopUpDotacao = new IPopUpDotacaoFiltroClassificacao ($obISelectEntidade);

$obBscCentroCusto = new IPopUpCentroCustoUsuario($obForm);
$obBscCentroCusto->setNull(true);

$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-03.04.02');
$obFormulario->addForm($obForm);
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnStCtrl );
$obFormulario->addTitulo( "Dados para o filtro" );
$obFormulario->addComponente($obISelectEntidade );
$obFormulario->addComponente( $obTxtSolicitacao );
$obFormulario->addComponente( $obObjeto         );
$obFormulario->addComponente( $obPeriodicidade  );
$obFormulario->addComponente( $obBscItem        );
$obFormulario->addComponente( $obPopUpDotacao   );
$obFormulario->addComponente( $obBscCentroCusto );
$obFormulario->Ok();
$obFormulario->show()

?>
