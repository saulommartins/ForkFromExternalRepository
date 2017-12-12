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

    * Página de Filtro para Imprimir Etiquetas
    * Data de Criação   : 15/10/2007

    * @author Analista: Lucas
    * @author Desenvolvedor: Rodrigo Soares Rodrigues

    * @ignore

    * Casos de uso : 01.06.98

    $Id: FLImprimirEtiquetas.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_PROT_CLASSES."componentes/ITextChaveProcesso.class.php" );
include_once( CAM_GA_PROT_CLASSES."componentes/ISelectClassificacaoAssunto.class.php");

$pgGera = 'etiquetas.php';

Sessao::remove('filtro');

$obForm = new Form;
$obForm->setAction ( $pgGera );
$obForm->setTarget ( 'telaPrincipal' );

$obITextCodProcessoInicial = new ITextChaveProcesso();
$obITextCodProcessoInicial->setName("codProcessoInicial");
$obITextCodProcessoInicial->setTitle("Número do processo");

// Define Objeto Label
$obLabelProcesso = new Label;
$obLabelProcesso->setValue( " até " );

$obITextCodProcessoFinal = new ITextChaveProcesso();
$obITextCodProcessoFinal->setName("codProcessoFinal");

//BUSCA O INTERESSADO(POPUP CGM)
$obBuscaCGM = new IPopUpCGM( $obForm );
$obBuscaCGM->setRotulo               ( 'Interessado' );
$obBuscaCGM->obCampoCod->setName     ( 'numCgm'      );
$obBuscaCGM->setNull                 ( true          );
$obBuscaCGM->setTitle                ( 'Interessado pelo processo' );

//COMPONENTES PARA O PERIODO DE BUSCA
$obDataInicial = new Data;
$obDataInicial->setName ( 'dataInicial'  );
$obDataInicial->setTitle( 'Data de inclusão do processo' );;
$obDataFinal = new Data;
$obDataFinal->setName   ( 'dataFinal' );

//INFORMA O FORMATO DA ETIQUETA
$obCmbTipoEtiqueta = new Select;
$obCmbTipoEtiqueta->setRotulo    ('Selecione o formato'             );
$obCmbTipoEtiqueta->setTitle     ("Selecione o formato da etiqueta.");
$obCmbTipoEtiqueta->setName      ('stFormatoEtiqueta'               );
$obCmbTipoEtiqueta->setNull      (false                             );
$obCmbTipoEtiqueta->addOption    ("A4", "Modelo A4 (2x8)"           );
$obCmbTipoEtiqueta->addOption    ("termica", "Modelo Impressora Térmica" );

$obFormulario = new Formulario();
$obFormulario->addTitulo("Dados para filtro");
$obFormulario->addForm($obForm);
$obFormulario->agrupaComponentes ( array($obITextCodProcessoInicial, $obLabelProcesso, $obITextCodProcessoFinal)   );
$obFormulario->addComponente( $obBuscaCGM );
$obFormulario->periodo($obDataInicial, $obDataFinal);
$obFormulario->addTitulo ( 'Etiqueta'             );
$obFormulario->addComponente ( $obCmbTipoEtiqueta     );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
