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
    * Página de Filtro para Relatório de MODELOS
    * Data de Criação   : 24/01/2006

    * @author Fernando Zank Correa Evangelista

    * @ignore

    * Casos de uso : uc-06.02.11
                     uc-06.02.12
                     uc-06.02.13
                     uc-06.02.15
                     uc-06.02.17
                     uc-06.02.18
*/

/*
$Log$
Revision 1.9  2006/07/18 16:25:51  rodrigo
Caso de Uso #06.02.12

Revision 1.8  2006/07/06 13:52:24  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:42:06  diego
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeGeral.class.php");

$stPrograma = ucfirst($_REQUEST['stAcao']);

$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';
$pgGera = 'OCGera'.$stPrograma.'.php';

Sessao::remove('filtro');

$obForm = new Form;
$obForm->setAction ( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget ( 'oculto' );

//Definição dos componentes
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnStCtrl = new Hidden;
$obHdnStCtrl->setName ( "stCtrl" );
$obHdnStCtrl->setValue( $stCtrl );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GPC_TCERJ_INSTANCIAS."relatorios/OCModelos.php?pgGera=".$pgGera."&".Sessao::getId());

$obISelectEntidade = new ISelectMultiploEntidadeGeral();

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnStCtrl );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addTitulo( "Dados para o filtro" );
$obFormulario->addComponente ($obISelectEntidade);
$obFormulario->Ok();
$obFormulario->show()
