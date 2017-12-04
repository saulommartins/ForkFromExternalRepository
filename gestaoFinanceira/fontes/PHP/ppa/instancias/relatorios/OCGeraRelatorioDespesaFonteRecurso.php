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
    * Página do oculto filtra de Relatórios de Estimativa de Receitas PPA
    * Data de Criação: 11/05/2008

    * @author Analista: Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor: Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * @package URBEM
    * @subpackage PPA

    * $Id: $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
require_once CAM_GF_PPA_MAPEAMENTO.'TPPA.class.php';;

$obTPPA = new TPPA;
$stCondicao = "\n WHERE ppa.cod_ppa = ".$_POST['inCodPPA'];
$obTPPA->recuperaTodos($rsPPA, $stCondicao);
$arPPA = $rsPPA->getElementos();

$preview = new PreviewBirt(2, 43,6);
$preview->setVersaoBirt  ('2.5.0');
$preview->setExportaExcel(true);

$preview->addParametro('cod_ppa'    , $_POST['inCodPPA']);
$preview->addParametro('ano_inicial', $arPPA[0]['ano_inicio']);
$preview->addParametro('exercicio1' , $arPPA[0]['ano_inicio']);
$preview->addParametro('exercicio2' , $arPPA[0]['ano_inicio'] + 1);
$preview->addParametro('exercicio3' , $arPPA[0]['ano_inicio'] + 2);
$preview->addParametro('exercicio4' , $arPPA[0]['ano_inicio'] + 3);
$preview->preview();
