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
    * Página Oculto : AMF Demonstrativo 7
    * Data de Criação : 15/06/2009

    * @author Analista      Tonismar Régis Bernardo     <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * $Id: $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
require_once CAM_GF_LDO_MAPEAMENTO.'TLDO.class.php';

$obTLDO = new TLDO;
$obTLDO->setDado('exercicio', $_POST['stExercicio']);
$obTLDO->recuperaDadosLDOPorExercicio($rsLDO);

$preview = new PreviewBirt(6, 36, 37);
$preview->setTitulo('Estimativa e Compensação da Renúncia de Receita');
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);
$preview->addParametro('ano_ldo', $_POST['stExercicio']);
if ($rsLDO->getNumLinhas() > 0) {
    $preview->addParametro('ano' , $rsLDO->getCampo('ano'));
    $preview->addParametro('cod_ppa', $rsLDO->getCampo('cod_ppa'));
} else {
    $preview->addParametro('ano' , 0);
    $preview->addParametro('cod_ppa', 0);
}
$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
