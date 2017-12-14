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
    * Página de Filtro para Imprimir Etiquetas
    * Data de Criação   : 15/10/2007

    * @author Analista: Lucas
    * @author Desenvolvedor: Rodrigo Soares Rodrigues

    * @ignore

    * Casos de uso : 01.06.98

    $Id: imprimirEtiqueta.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$codProcesso = $_REQUEST['codProcesso'];
$anoExercicio = $_REQUEST['anoExercicio'];

$pgGera = 'etiquetas.php';

Sessao::remove('filtro');

$obForm = new Form;
$obForm->setAction ( $pgGera );
$obForm->setTarget ( 'telaPrincipal'     );

$obHdnCodProcesso = new Hidden;
$obHdnCodProcesso->setName( "codProcesso" );
$obHdnCodProcesso->setValue( $codProcesso);

$obHdnAnoExercicio = new Hidden;
$obHdnAnoExercicio->setName( "anoExercicio" );
$obHdnAnoExercicio->setValue( $anoExercicio);

//INFORMA O FORMATO DA ETIQUETA
$obCmbTipoEtiqueta = new Select;
$obCmbTipoEtiqueta->setRotulo    ('Selecione o formato'             );
$obCmbTipoEtiqueta->setTitle     ("Selecione o formato da etiqueta.");
$obCmbTipoEtiqueta->setName      ('stFormatoEtiqueta'               );
$obCmbTipoEtiqueta->setNull      (false                             );
$obCmbTipoEtiqueta->addOption    ("A4", "Modelo A4 (2x8)"           );
$obCmbTipoEtiqueta->addOption    ("termica", "Modelo Impressora Térmica" );

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addHidden ( $obHdnCodProcesso );
$obFormulario->addHidden ( $obHdnAnoExercicio );
$obFormulario->addTitulo ( 'Etiqueta'             );
$obFormulario->addComponente ( $obCmbTipoEtiqueta     );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
