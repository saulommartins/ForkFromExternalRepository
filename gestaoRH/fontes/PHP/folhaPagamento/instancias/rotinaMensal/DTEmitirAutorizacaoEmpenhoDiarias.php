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
    * Lista
    * Data de Criação: 18/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 31094 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-24 16:43:58 -0300 (Ter, 24 Jul 2007) $

    * Casos de uso: uc-04.05.62
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

$arDetalhes[0]["rubrica_despesa"]   = $_GET["rubrica_despesa"];
$arDetalhes[0]["descricao_despesa"] = $_GET["descricao_despesa"];
$arDetalhes[0]["red_dotacao"]       = $_GET["red_dotacao"];
$arDetalhes[0]["cargo"]             = $_GET["cargo"];
$rsDetalhes = new RecordSet();
$rsDetalhes->preenche($arDetalhes);

$obTableTree = new Table();
$obTableTree->setRecordset($rsDetalhes);
$obTableTree->setSummary("Detalhes");

$obTableTree->Head->addCabecalho( 'Dotação' , 30  );
$obTableTree->Head->addCabecalho( 'Reduz Dotação' , 10  );
$obTableTree->Head->addCabecalho( 'Cargo' , 30  );

$obTableTree->Body->addCampo( '[rubrica_despesa]-[descricao_despesa]', 'E' );
$obTableTree->Body->addCampo( 'red_dotacao', 'C' );
$obTableTree->Body->addCampo( 'cargo', 'E' );

$obTableTree->montaHTML();
echo $obTableTree->getHtml();

?>
