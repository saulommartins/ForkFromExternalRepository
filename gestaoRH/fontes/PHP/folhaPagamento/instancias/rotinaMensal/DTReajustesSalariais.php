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
    * Data de Criação: 27/09/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 31094 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-02 14:35:36 -0300 (Ter, 02 Out 2007) $

    * Casos de uso: uc-04.05.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

$arDetalhes[0]["regime"]            = $_GET["regime"];
$arDetalhes[0]["sub_divisao"]       = $_GET["sub_divisao"];
$arDetalhes[0]["funcao"]            = $_GET["funcao"];
$arDetalhes[0]["especialidade"]     = $_GET["especialidade"];
$arDetalhes[0]["orgao"]             = $_GET["orgao"];
$rsDetalhes = new RecordSet();
$rsDetalhes->preenche($arDetalhes);

$obTableTree = new Table();
$obTableTree->setRecordset($rsDetalhes);
$obTableTree->setSummary("Detalhes");

$obTableTree->Head->addCabecalho( 'Regime' , 10  );
$obTableTree->Head->addCabecalho( 'Subdivisão' , 20  );
$obTableTree->Head->addCabecalho( 'Função' , 20  );
$obTableTree->Head->addCabecalho( 'Especialidade' , 20  );
$obTableTree->Head->addCabecalho( 'Lotação' , 30  );

$obTableTree->Body->addCampo( 'regime', 'E' );
$obTableTree->Body->addCampo( 'sub_divisao', 'E' );
$obTableTree->Body->addCampo( 'funcao', 'E' );
$obTableTree->Body->addCampo( 'especialidade', 'E' );
$obTableTree->Body->addCampo( 'orgao', 'E' );

$obTableTree->montaHTML();
echo $obTableTree->getHtml();
?>
