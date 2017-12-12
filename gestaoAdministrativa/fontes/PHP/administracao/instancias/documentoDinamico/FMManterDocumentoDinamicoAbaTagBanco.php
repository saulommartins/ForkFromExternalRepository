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

$Revision: 3347 $
$Name$
$Author: pablo $
$Date: 2005-12-05 11:05:04 -0200 (Seg, 05 Dez 2005) $

Casos de uso: uc-01.03.99
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$rsRecordSet = new RecordSet;

$obRDocumentoDinamico->setCodDocumento($_REQUEST['inCodDocumento']);
$obRDocumentoDinamico->obRModulo->setCodModulo($_REQUEST['stModulo']);
$obRDocumentoDinamico->listarDocumentoTagBase($rsRecordSet);

$obCmbTagRS = new Select;
$obCmbTagRS->setRotulo     ( "TAGS Variáveis" );
$obCmbTagRS->setName       ( "tags_banco" );
//$obCmbTagRS->setCampoId    ( "&#91;[stNomeCampo]&#93;" );

$obCmbTagRS->setCampoId    ( "coluna" );
$obCmbTagRS->setCampoDesc  ( "descricao"    );
$obCmbTagRS->addOption     ( "", "Selecione"  );
$obCmbTagRS->preencheCombo ( $rsRecordSet     );
$obCmbTagRS->obEvento->setOnChange("insereTagsBanco(document.frm.stControleTextArea.value,document.frm.tags_banco.value);");

$obTxtCombo = new TextBox;
$obTxtCombo->setRotulo ("TAGS Variáveis" );
$obTxtCombo->setSize (30);
$obTxtCombo->setMaxLength (30);
$obTxtCombo->setReadOnly (true);

?>
