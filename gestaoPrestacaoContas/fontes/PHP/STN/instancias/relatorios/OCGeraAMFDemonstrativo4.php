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
    * Página Oculto : AMF Demonstrativo 4
    * Data de Criação   : 17/09/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDOIndicadores.class.php';

$preview = new PreviewBirt(6, 36, 40);
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);

$stAcao = $request->get('stAcao');

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado('exercicio'   , Sessao::getExercicio());
$obTOrcamentoEntidade->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
$obTOrcamentoEntidade->recuperaRelacionamentoNomes($rsEntidade);

$preview->addParametro('ano_referencia', $_REQUEST['stExercicio']);
$preview->addParametro('cod_ppa'       , $_REQUEST['inCodPPA']);
$preview->addParametro('cod_pib'       , $_REQUEST['inCodPIB']);
$preview->addParametro('cod_inflacao'  , $_REQUEST['inCodInflacao']);

$preview->addParametro('poder'       , 'Executivo');
$preview->addParametro('nom_entidade', $rsEntidade->getCampo('entidade'));

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
