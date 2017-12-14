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
    * Página de Processamento para Migrar o Organograma
    * Data de criação : 13/04/2009

    * @author Analista:     Gelson Wolowski <gelson.goncalves@cnm.org.br>
    * @author Programador:  Diogo Zarpelon  <diogo.zarpelon@cnm.org.br>

    * @ignore

    $Id:$

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GA_ORGAN_MAPEAMENTO.'TConfigurarMigracaoOrganogramaDinamico.class.php';
include_once CAM_GA_ORGAN_MAPEAMENTO.'TOrganogramaOrganograma.class.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';

$stPrograma = "ConfigurarMigracaoOrganogramaDinamico";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$arOrgao = Sessao::read('arOrgao');
$inCodOrganogramaNovo = sessao::read('inCodOrganogramaNovo');
$inCodOrganogramaAntigo = sessao::read('inCodOrganogramaAntigo');

if (count($arOrgao) > 0) {

    Sessao::setTrataExcecao(true);

    $obTConfigurarMigracaoOrganogramaDinamico = new TConfigurarMigracaoOrganogramaDinamico;
    $obTAdministracaoConfiguracao             = new TAdministracaoConfiguracao;
    $obTOrganogramaOrganograma                = new TOrganogramaOrganograma;

    foreach ($arOrgao as $key => $value) {

        list($stNomeVar, $inCodOrgao) = explode('_', $key);

        # Seta as chaves da tabela para futura atualização.
        $obTConfigurarMigracaoOrganogramaDinamico->setDado('cod_orgao'     , $inCodOrgao  );
        $obTConfigurarMigracaoOrganogramaDinamico->setDado('cod_orgao_new' , $value       );
        $obTConfigurarMigracaoOrganogramaDinamico->recuperaPorChave($rsOrgao);

        if ($rsOrgao->getCampo('cod_orgao') == null) {
            $obTConfigurarMigracaoOrganogramaDinamico->setDado('cod_organograma' , $inCodOrganogramaAntigo );
            $obTConfigurarMigracaoOrganogramaDinamico->inclusao();
        } else {
            $obTConfigurarMigracaoOrganogramaDinamico->alteracao();
        }

    }

    # Recupera o status da Migração, se está finalizado ou não.
    $obTConfigurarMigracaoOrganogramaDinamico->recuperaMigraTotalidade($rsTotalidade);
    $boMigraCompleto = $rsTotalidade->getCampo('finalizado');

    # Caso a migração esteja completa, atualiza o parâmetro para 'true'.
    $obTAdministracaoConfiguracao->setDado('cod_modulo', 19);
    $obTAdministracaoConfiguracao->setDado('exercicio' , Sessao::getExercicio());
    $obTAdministracaoConfiguracao->setDado('parametro' , 'migra_orgao');
    $obTAdministracaoConfiguracao->setDado('valor'     , (($boMigraCompleto == 'true') ? 'true' : 'false'));
    $obTAdministracaoConfiguracao->alteracao();

    SistemaLegado::exibeAviso('Configuração da Migração do Organograma Dinâmico finalizada com sucesso!', 'aviso', '');

    Sessao::encerraExcecao();
}

?>
