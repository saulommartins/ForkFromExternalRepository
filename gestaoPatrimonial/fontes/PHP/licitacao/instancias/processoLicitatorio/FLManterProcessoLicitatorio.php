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
    * Página de Filtro de fornecedor
    * Data de Criação   : 06/10/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    * Casos de uso: uc-03.05.15
*/
/*
$Log$
Revision 1.3  2007/01/29 17:50:39  hboaventura
Mudança da tabela tipo_objeto de licitação para compras

Revision 1.2  2006/10/30 17:05:05  fernando
Filtro para alterar e anular processo licitatorio

Revision 1.1  2006/10/06 17:47:23  fernando
inclusão do uc-03.05.15

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php");
include_once(CAM_GP_COM_COMPONENTES."IPopUpObjeto.class.php");
//include_once(CAM_GP_LIC_COMPONENTES."ISelectModalidadeLicitacao.class.php");
include_once(CAM_GP_LIC_COMPONENTES."ISelectTipoLicitacao.class.php");
include_once(CAM_GP_LIC_COMPONENTES."ISelectCriterioJulgamento.class.php");
include_once(CAM_GP_COM_COMPONENTES."ISelectTipoObjeto.class.php");
include_once( CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php'                       );
include_once(CAM_GP_COM_COMPONENTES."IPopUpMapaCompras.class.php" );
include_once(CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php");
include_once(CAM_GP_LIC_COMPONENTES."IMontaNumeroLicitacaoMultiploEntidadeUsuario.class.php");

$stPrograma = "ManterProcessoLicitatorio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//include ($pgJs);
$jsOnload = '';


//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obForm = new Form;
$obForm->setAction                  ( $pgList );

/// Entidade
$obEntidadeUsuario = new ISelectMultiploEntidadeUsuario;
$obEntidadeUsuario->setNull(true);

//Define objeto de select critério julgamento
$obISelectCriterioJulgamento = new ISelectCriterioJulgamento();

//Define objeto de select tipo Objeto
$obISelectTipoObjeto = new ISelectTipoObjeto();

//Define objeto de popup objeto
$obPopUpObjeto = new IPopUpObjeto($obForm);

//Define objeto de select modalidade licitacao
//$obISelectModalidadeLicitacao = new ISelectModalidadeLicitacao();

//Define objeto de select tipo licitacao
$obISelectTipoLicitacao = new ISelectTipoLicitacao();

$obPopUpMapa = new IPopUpMapaCompras($obForm);

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio() );
if ($stAcao!='consultar' OR $stAcao!='alterar' or $stAcao!= 'anular') {
    $obPeriodicidade->setValidaExercicio( true               );
}
$obPeriodicidade->setNull           ( true               );
$obPeriodicidade->setValue          ( 4                  );

$obPopUpProcesso = new IPopUpProcesso($obForm);

$obMontaLicitacao = new IMontaNumeroLicitacaoMultiploEntidadeUsuario($obForm);

$obHomologada = new Select();
$obHomologada->setRotulo('Homologadas');
$obHomologada->setName("inCodHomologada");
$obHomologada->setCampoId	("cod_homologada");
$obHomologada->addOption("1", "Todas");
$obHomologada->addOption("2", "Sim");
$obHomologada->addOption("3", "Não");

//Definição do Formulário
$obFormulario = new Formulario;
$obFormulario->addTitulo                 ( "Dados Para o Filtro" );
$obFormulario->addForm                   ( $obForm               );
$obFormulario->setAjuda                  ("UC-03.05.15");
$obFormulario->addHidden     ( $obHdnAcao              );
$obFormulario->addHidden     ( $obHdnCtrl              );
$obMontaLicitacao->geraFormulario( $obFormulario );
if ($stAcao!='consultar')
    $obMontaLicitacao->obExercicio->setReadOnly(true);
$obFormulario->addComponente    ( $obHomologada);
$obFormulario->addComponente    ( $obPopUpProcesso                 );
$obFormulario->addComponente    ( $obPopUpMapa                     );
$obFormulario ->addComponente       ($obPeriodicidade               );
//$obFormulario->addComponente    ( $obISelectModalidadeLicitacao    );
$obFormulario->addComponente    ( $obISelectTipoLicitacao          );
$obFormulario->addComponente    ( $obISelectCriterioJulgamento     );
$obFormulario->addComponente    ( $obISelectTipoObjeto             );
$obFormulario->addComponente    ( $obPopUpObjeto                   );
$obFormulario->OK();
$obFormulario->show();

//$jsOnload .="jq('#stExercicioLicitacao').attr('readonly',true);";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
