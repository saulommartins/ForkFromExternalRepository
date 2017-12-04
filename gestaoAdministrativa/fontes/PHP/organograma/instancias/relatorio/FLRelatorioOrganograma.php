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
    * Arquivo de instância para Relatorio.
    * Data de Criação: 08/10/2013

    * @author Arthur Cruz

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrganograma.class.php";

$pgGera = 'OCGeraRelatorioOrganograma.php';

$rsOrganograma = new RecordSet;
$obRegra       = new ROrganogramaOrganograma;

$obRegra->listar( $rsOrganograma );

$obForm = new Form;
$obForm->setAction ( $pgGera );
$obForm->setTarget ( 'telaPrincipal' );

$obCmbOrganograma = new Select;
$obCmbOrganograma->setRotulo        ( "Organograma" );
$obCmbOrganograma->setTitle         ( "Selecione o Organograma" );
$obCmbOrganograma->setName          ( "inCodOrganograma" );
$obCmbOrganograma->setStyle         ( "width: 200px");
$obCmbOrganograma->setCampoID       ( "cod_organograma" );
$obCmbOrganograma->setCampoDesc     ( "implantacao" );
$obCmbOrganograma->addOption        ( "", "Selecione" );
$obCmbOrganograma->setValue         ( isset($inCodOrganograma) ? $inCodOrganograma : null );
$obCmbOrganograma->setNull          ( false );
$obCmbOrganograma->preencheCombo    ( $rsOrganograma );

$obFormulario = new Formulario();
$obFormulario->addTitulo("Dados para filtro");
$obFormulario->addForm($obForm);
$obFormulario->addComponente ( $obCmbOrganograma     );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
