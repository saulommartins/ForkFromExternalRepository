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
* Manutneção de relatórios
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 27427 $
$Name$
$Author: rodrigosoares $
$Date: 2008-01-08 16:47:11 -0200 (Ter, 08 Jan 2008) $

Casos de uso: uc-01.03.94
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrganograma.class.php");

$obRegra       = new ROrganogramaOrganograma;
$obRegra->listar( $rsOrganograma );

$obCmbOrganograma = new Select;
$obCmbOrganograma->setRotulo        ( "Organograma" );
$obCmbOrganograma->setName          ( "inCodOrganograma" );
$obCmbOrganograma->setStyle         ( "width: 200px");
$obCmbOrganograma->setCampoID       ( "cod_organograma" );
$obCmbOrganograma->setCampoDesc     ( "implantacao" );
$obCmbOrganograma->addOption        ( "", "Selecione" );
$obCmbOrganograma->setValue         ( $inCodOrganograma );
$obCmbOrganograma->setNull          ( false );
$obCmbOrganograma->preencheCombo    ( $rsOrganograma );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ('LSOrganograma.php');
$obForm->setTarget ("telaPrincipal");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );

$obFormulario->addTitulo            ( "Dados para filtro" );
$obFormulario->addComponente        ( $obCmbOrganograma );

$obFormulario->OK  ();
$obFormulario->show();

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
