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
    * Oculto do Formulário para consulta de PPA
    * Data de Criação   : 22/05/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once CAM_GF_PPA_MAPEAMENTO.'TPPAAcao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarPPA";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {
case 'montaAcoes':

    $stFiltro  = " programa.cod_programa = ".$_REQUEST['cod_programa'];
    $stFiltro .= " AND ppa.cod_ppa = ".$_REQUEST['cod_ppa'];
    $stOrder = " acao.num_acao ";

    $obTPPAAcao = new TPPAAcao;
    $obTPPAAcao->recuperaDados($rsAcoes, $stFiltro, $stOrder);
    $rsAcoes->addFormatacao('valor_acao', 'NUMERIC_BR');

    //Instancia uma Table para demonstrar as ações
    $obTable = new Table     ();
    $obTable->setRecordset    ($rsAcoes);
    $obTable->setSummary      ('Lista de Ações');

    $obTable->Head->addCabecalho('Código',10);
    $obTable->Head->addCabecalho('Tipo',20);
    $obTable->Head->addCabecalho('Descrição',55);
    $obTable->Head->addCabecalho('Valor',15);

    $obTable->Body->addCampo('num_acao','C');
    $obTable->Body->addCampo('nom_tipo_acao','C');
    $obTable->Body->addCampo('descricao','E');
    $obTable->Body->addCampo('valor_acao','D');

    $obTable->montaHTML();
    echo $obTable->getHTML();

    break;
}

?>
