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
    * Data de Criação: 28/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: FLManterTransferirBem.php 61462 2015-01-20 13:17:23Z diogo.zarpelon $

    * Casos de uso: uc-03.01.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_COMPONENTES."IPopUpBem.class.php");
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php";
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );

$stPrograma = "ManterTransferirBem";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

include_once( $pgJs );

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgForm);

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//instancia o componente IPopUpCGMVinculado para o responsavel anterior
$obIPopUpCGMVinculadoResponsavelAnterior = new IPopUpCGMVinculado( $obForm );
$obIPopUpCGMVinculadoResponsavelAnterior->setTabelaVinculo    ( 'patrimonio.bem_responsavel'        );
$obIPopUpCGMVinculadoResponsavelAnterior->setCampoVinculo     ( 'numcgm'                            );
$obIPopUpCGMVinculadoResponsavelAnterior->setNomeVinculo      ( 'ResponsavelAnterior'               );
$obIPopUpCGMVinculadoResponsavelAnterior->setRotulo           ( 'Responsável Atual'              );
$obIPopUpCGMVinculadoResponsavelAnterior->setTitle            ( 'Selecione o Responsável Atual.' );
$obIPopUpCGMVinculadoResponsavelAnterior->setName             ( 'stNomResponsavelAtual'          );
$obIPopUpCGMVinculadoResponsavelAnterior->setId               ( 'stNomResponsavelAtual'          );
$obIPopUpCGMVinculadoResponsavelAnterior->obCampoCod->setName ( 'inNumResponsavelAtual'          );
$obIPopUpCGMVinculadoResponsavelAnterior->obCampoCod->setId   ( 'inNumResponsavelAtual'          );
$obIPopUpCGMVinculadoResponsavelAnterior->setNull             ( true                               );
$obIPopUpCGMVinculadoResponsavelAnterior->obCampoCod->obEvento->setOnFocus  ( "montaParametrosGET( 'verificaResponsavelBem'); montaParametrosGET( 'verificaResponsavelDif');" );
$obIPopUpCGMVinculadoResponsavelAnterior->obCampoCod->obEvento->setOnChange ( "montaParametrosGET( 'verificaResponsavelBem'); montaParametrosGET( 'verificaResponsavelDif');" );

//instancia o componenete IMontaOrganograma
$obIMontaOrganograma = new IMontaOrganograma(true);
$obIMontaOrganograma->setCodOrgao($codOrgao);
$obIMontaOrganograma->setCadastroOrganograma(true);
$obIMontaOrganograma->setNivelObrigatorio(1);

$obIMontaOrganogramaLocal = new IMontaOrganogramaLocal;
$obIMontaOrganogramaLocal->setValue($codLocal);
$obIMontaOrganogramaLocal->setStyle('width:350;');
$obIMontaOrganogramaLocal->setNull(false);

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-03.01.06');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo( 'Transferir Bem' );
$obFormulario->addComponente( $obIPopUpCGMVinculadoResponsavelAnterior );
$obIMontaOrganograma->geraFormulario( $obFormulario );
$obIMontaOrganogramaLocal->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
