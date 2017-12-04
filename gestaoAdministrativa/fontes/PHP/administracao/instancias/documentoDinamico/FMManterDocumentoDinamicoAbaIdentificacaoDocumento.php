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
* Arquivo de instância para manutenção de documentos dinâmicos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3075 $
$Name$
$Author: pablo $
$Date: 2005-11-29 14:45:45 -0200 (Ter, 29 Nov 2005) $

Casos de uso: uc-01.03.99
*/

$rsRecordSet = new RecordSet;

$obRDocumentoDinamico->obRModulo->listar($rsRecordSet);

while (!$rsRecordSet->eof()) {
  if ($rsRecordSet->getCampo('cod_modulo') == $_REQUEST['stModulo']) {
     $stModulo = $rsRecordSet->getCampo('nom_modulo');
  }
  $rsRecordSet->proximo();
}

$obLblModulo = new Label;
$obLblModulo->setRotulo ("Módulo");
$obLblModulo->setName   ("stModulo");
$obLblModulo->setValue  ($stModulo);

$obHdnModulo = new Hidden;
$obHdnModulo->setName( "hdnModulo" );
$obHdnModulo->setValue( $stModulo);

$obTxtDocumento = new TextBox;
$obTxtDocumento->setRotulo    ( "Nome do Documento" );
$obTxtDocumento->setName      ( "stDocumento"     );
$obTxtDocumento->setValue     ( $stDocumento      );
$obTxtDocumento->setSize      ( 80 );
$obTxtDocumento->setMaxLength ( 80 );
$obTxtDocumento->setNull      ( false );

$obTxtTitulo = new TextBox;
$obTxtTitulo->setRotulo    ( "Título" );
$obTxtTitulo->setName      ( "stTitulo" );
$obTxtTitulo->setValue      ( $stTitulo );
$obTxtTitulo->setSize      ( 80 );
$obTxtTitulo->setMaxLength ( 80 );
$obTxtTitulo->setNull      ( false );

?>
