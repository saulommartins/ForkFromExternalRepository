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
 * Página Formulário 
 * Data de Criação: 07/10/2014
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>
 * @ignore
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_COMPONENTES.'/Table/Table.class.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO ."TTCEPETipoNorma.class.php";
include_once CAM_GPC_TCEPE_MAPEAMENTO ."TTCEPEVinculoTipoNorma.class.php";

$stPrograma = "ManterTipoNorma";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao', 'incluir');

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

# Recupera Tipo Normas do TCEPE
$obTTipoNorma = new TTCEPETipoNorma();
$obTTipoNorma->recuperaRelacionamento($rsTipoNorma);

# Recupera Vinculo de Tipo Normas do Urbem e TCEPE
$obTTCEPEVinculoTipoNorma = new TTCEPEVinculoTipoNorma();
$obTTCEPEVinculoTipoNorma->recuperaVinculo($rsTipoNormaUrbem,' ORDER BY cod_tipo_norma');

# Select com os tipos de norma do tribunal
$obCmbTipoNorma = new Select  ();
$obCmbTipoNorma->setId        ('inTipo_[cod_tipo_norma]');
$obCmbTipoNorma->setName      ('inTipo_[cod_tipo_norma]');
$obCmbTipoNorma->setCampoId   ('[cod_tipo]');
$obCmbTipoNorma->setCampoDesc ('[cod_tipo] - [descricao]');
$obCmbTipoNorma->addOption    ('','Selecione');
$obCmbTipoNorma->preencheCombo($rsTipoNorma);
$obCmbTipoNorma->setValue     ('[cod_tipo_norma_tcepe]');

$obTable = new Table;
$obTable->setRecordset($rsTipoNormaUrbem);

$obTable->Head->addCabecalho('Tipo Norma - Urbem', 30);
$obTable->Head->addCabecalho('Tipo Norma - TCEPE', 10);

$obTable->Body->addCampo('[cod_tipo_norma] - [nom_tipo_norma]', 'E');
$obTable->Body->addCampo($obCmbTipoNorma, 'C');

$obTable->montaHTML(true);
$stHTML = $obTable->getHtml();

$obSpnLista = new Span();
$obSpnLista->setId('spnLista');
$obSpnLista->setValue($stHTML);

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addSpan( $obSpnLista );

$obFormulario->defineBarra(array(new Ok()));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';