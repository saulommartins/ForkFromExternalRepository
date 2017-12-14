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
    * Página de Filtro de Publicacao de compra direta
    * Data de Criação   : 03/08/2015

    * @author Analista: Gelson Goncalves
    * @author Desenvolvedor: Lisiane Morais

    * @ignore
    * Casos de uso : uc-03.05.17
     $Id:$
   

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_LIC_COMPONENTES."IMontaNumeroLicitacaoMultiploEntidadeUsuario.class.php" );
include_once ( CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php" );
include_once ( CAM_GP_COM_COMPONENTES."IPopUpMapaCompras.class.php" );
include_once ( CAM_GP_LIC_COMPONENTES."IPopUpNumeroEdital.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeGeral.class.php" );


//Define o nome dos arquivos PHP
$stPrograma = "ManterPublicacaoCompraDireta";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LSManterCompraDireta.php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js" ;
//$pgFormAnular = "FMAnularEdital.php";

Sessao::write('link', '');

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = 'publicar';

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obForm = new Form();
$obForm->setAction ( $pgList  );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden();
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obLbExercicio = new Label();
$obLbExercicio->setName  ( 'stExercicio' );
$obLbExercicio->setRotulo( 'Exercício' );
$obLbExercicio->setTitle ( 'Exercício' );
$obLbExercicio->setValue ( Sessao::getExercicio());

$obPopUpMapa = new IPopUpMapaCompras($obForm);
$obPopUpProcesso = new IPopUpProcesso($obForm);

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

$obISelectModalidade = new Select();
$obISelectModalidade->setRotulo            ("Modalidade"                            );
$obISelectModalidade->setTitle             ("Selecione a modalidade."               );
$obISelectModalidade->setName              ("inCodModalidade"                       );
$obISelectModalidade->setCampoID           ("cod_modalidade"                        );
$obISelectModalidade->addOption            ("","Selecione"                          );
$obISelectModalidade->addOption            ("8","8 - Dispensa de Licitação"         );
$obISelectModalidade->addOption            ("9","9 - Inexibilidade"                 );

/// codigo da solicitação
$obTextCompraDireta = new TextBox;
$obTextCompraDireta->setName  ( 'inCompraDireta'                );
$obTextCompraDireta->setID    ( 'inCompraDireta'                );
$obTextCompraDireta->setRotulo( 'Compra Direta'            );
$obTextCompraDireta->setTitle ( 'Informe o código da Compra Direta.' );
$obTextCompraDireta->setInteiro ( true );

//$obComissao = new ISelectComissao();

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addComponente ( $obLbExercicio );
$obFormulario->addComponente ( $obISelectEntidadeGeral );
$obFormulario->addComponente ( $obISelectModalidade );
$obFormulario->addComponente ( $obTextCompraDireta   );
$obFormulario->addComponente( $obPopUpProcesso );
$obFormulario->addComponente( $obPopUpMapa );
$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
