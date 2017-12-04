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
* Listagens dos Criados pelo modulo Exportacao
* Data de Criação   : 11/02/2005

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Lucas Texeira Stephanou

* @ignore

$Revision: 25574 $
$Name$
$Autor: $
$Date: 2007-09-20 11:47:06 -0300 (Qui, 20 Set 2007) $

* Casos de uso : uc-03.04.33
*/

/*
$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$obLblDownload = new Link;
$obLblDownload->setName( 'aviso' );
$obLblDownload->setRotulo( 'Arquivo XML' );
$obLblDownload->setValue( "Download" );
$obLblDownload->setHref( $_GET["stCaminhoArquivoXML"] );
$obLblDownload->setLinkTitle( "Para salvar o arquivo clique com o botão direito sobre 'Download' e escolha 'Salvar link como...'" );

$obFormulario = new Formulario;
$obFormulario->addTitulo( "Arquivo para Cotação" );
$obFormulario->addComponente( $obLblDownload );
$obFormulario->show();

?>
