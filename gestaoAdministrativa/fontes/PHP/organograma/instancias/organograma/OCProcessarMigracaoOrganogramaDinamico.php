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
  * Página Oculta para Processar Migraçao Organograma
  * Data de criação: 14/04/2009

  * @author Analista: Gelson Wolowski   <gelson.goncalves@cnm.org.br>
  * @author Programador: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>

  * @package     Gestao Administrativa
  * @subpackage  Organograma

  $Id:$

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {

    case 'verificaStatus':

        include_once CAM_GA_ORGAN_MAPEAMENTO."TConfigurarMigracaoOrganogramaDinamico.class.php";
        $obTConfigurarMigracaoOrganogramaDinamico = new TConfigurarMigracaoOrganogramaDinamico;

        # Verifica se a configuração da migração está completa.
        $obTConfigurarMigracaoOrganogramaDinamico->recuperaMigraTotalidade($rsConfiguracao);

        include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
        $obTAdministracaoConfiguracao->setDado("exercicio"  , Sessao::getExercicio());
        $obTAdministracaoConfiguracao->setDado("cod_modulo" , '19');
        $obTAdministracaoConfiguracao->setDado("parametro"  , 'migra_orgao');
        $obTAdministracaoConfiguracao->recuperaPorChave($rsRecordSet);

        if ($rsConfiguracao->getCampo('finalizado') == "false" || $rsRecordSet->getCampo('valor') == "false") {
            $stJs .= "jQuery('#stStatusConfiguracao').html('Não Configurado'); \n";
            $stJs .= "jQuery('#Ok').attr('disabled', 'disabled');              \n";
        } else {
            $stJs .= "jQuery('#stStatusConfiguracao').html('Configurado');     \n";
        }

    break;
}

echo (!empty($stJs) ? $stJs : '');

?>
