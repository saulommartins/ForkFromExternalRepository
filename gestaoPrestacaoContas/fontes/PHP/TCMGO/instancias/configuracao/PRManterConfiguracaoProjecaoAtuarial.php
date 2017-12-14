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
    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOProjecaoAtuarial.class.php" );

$j = count($_REQUEST['exercicio']);

for ($i=0; $i<$j; $i++) {
    $obTTCMGOProjecaoAtuarial = new TTCMGOProjecaoAtuarial();
    $obTTCMGOProjecaoAtuarial->setDado('exercicio'   , $_REQUEST['exercicio'][$i]);
    $obTTCMGOProjecaoAtuarial->setDado('num_orgao'   , $_REQUEST['inOrgao']);
    $obTTCMGOProjecaoAtuarial->limpaProjecao();
}

for ($i=0; $i<$j; $i++) {
    $obTTCMGOProjecaoAtuarial = new TTCMGOProjecaoAtuarial();
    $vlReceita = str_replace(',', '.', str_replace('.', '', $_REQUEST['vlReceitaPrevidenciaria'][$i]));
    $vlDespesa = str_replace(',', '.', str_replace('.', '', $_REQUEST['vlDespesaPrevidenciaria'][$i]));
    $vlSaldo   = str_replace(',', '.', str_replace('.', '', $_REQUEST['vlSaldoFinanceiroExercicio'][$i]));

    $obTTCMGOProjecaoAtuarial->setDado('exercicio'       , $_REQUEST['exercicio'][$i]);
    $obTTCMGOProjecaoAtuarial->setDado('num_orgao'       , $_REQUEST['inOrgao']);
    $obTTCMGOProjecaoAtuarial->setDado('exercicio_orgao' , Sessao::getExercicio());
    $obTTCMGOProjecaoAtuarial->setDado('vl_receita'      , $vlReceita != '' ? $vlReceita : 'NULL');
    $obTTCMGOProjecaoAtuarial->setDado('vl_despesa'      , $vlDespesa != '' ? $vlDespesa : 'NULL');
    $obTTCMGOProjecaoAtuarial->setDado('vl_saldo'        , $vlSaldo   != '' ? $vlSaldo   : 'NULL');
    $obTTCMGOProjecaoAtuarial->inclusao();
}

SistemaLegado::exibeAviso("Configuração salva","incluir","incluir_n");
