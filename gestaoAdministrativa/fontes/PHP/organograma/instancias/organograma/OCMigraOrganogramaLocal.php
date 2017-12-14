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
    * Página de Oculto para Migrar Organograma
    * Data de criação : 08/12/2008

    * @author Analista: Gelson Wolowski
    * @author Programador: Diogo Zarpelon

    * @ignore

    $Id:$

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

include_once( CAM_GA_ORGAN_NEGOCIO.'ROrganogramaLocal.class.php' );
include_once( CAM_GA_ORGAN_MAPEAMENTO.'TMigraOrganogramaLocal.class.php' );

$stPrograma = "MigraOrganogramaLocal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function montaTabelaMigracao()
{
    # Monta o RecordSet com os Locais do Organograma Padrão (antigo).
    $obTMigraOrganogramaLocal = new TMigraOrganogramaLocal;
    $obTMigraOrganogramaLocal->recuperaOrganogramaLocalPadrao($rsOrganogramaPadrao);

    # Monta o RecordSet com os locais cadastrados.
    $obROrganogramaLocal = new ROrganogramaLocal;
    $obErro = $obROrganogramaLocal->listarLocal($rsListaLocal);

    $obCmbOrganograma = new Select;
    $obCmbOrganograma->setName     ('inCodOrgao_[ano_exercicio]_[cod_orgao]_[cod_unidade]_[cod_departamento]_[cod_setor]_[cod_local]');
    $obCmbOrganograma->setValue    ('[cod_local_organograma]');
    $obCmbOrganograma->addOption   ('', 'Selecione');
    $obCmbOrganograma->setCampoId  ('[cod_local]');
    $obCmbOrganograma->setCampoDesc('[cod_local] - [descricao]');
    $obCmbOrganograma->setStyle    ('width:250px; height: 25px;');
    $obCmbOrganograma->preencheCombo($rsListaLocal);

    $table = new Table;

    $table->setRecordset($rsOrganogramaPadrao);
    $table->setSummary('Migrar Organograma');
    //$table->setConditional(true);
    $table->Head->addCabecalho( 'Organograma Antigo (Local)' , 65 );
    $table->Head->addCabecalho( 'Organograma Atual (Local)'  , 35 );

    $table->Body->addCampo ('[cod_orgao].[cod_unidade].[cod_departamento].[cod_setor].[cod_local] - [nom_local]' , 'L');

    # Adiciona o componente Select com todos os órgãos disponíveis.
    $table->Body->addComponente ( $obCmbOrganograma, 'ok' );

    $table->montaHTML(true);
    $stHTML = $table->getHtml();

    # Monta a tabela para a migração do Organograma.
    $stJs = "jQuery('#spnTable').html('".$stHTML."'); ";

    return $stJs;
}

switch ($stCtrl) {
    case 'montaTabelaMigracao':
        $stJs = montaTabelaMigracao();
    break;
}

echo $stJs;
