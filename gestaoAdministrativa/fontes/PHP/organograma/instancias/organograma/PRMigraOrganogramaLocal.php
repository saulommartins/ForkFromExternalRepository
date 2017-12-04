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
    * Data de criação : 08/12/2008

    * @author Analista: Gelson Wolowski
    * @author Programador: Diogo Zarpelon

    * @ignore

    $Id:$

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_ORGAN_MAPEAMENTO.'TMigraOrganogramaLocal.class.php' );
include_once( CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php' );

$stPrograma = "MigraOrganogramaLocal";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );

$obTMigraOrganogramaLocal     = new TMigraOrganogramaLocal;
$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;

    foreach ($_REQUEST as $key => $value) {

        list($stNomeVar, $stAnoExercicio, $inCodOrgao, $inCodUnidade, $inCodDepartamento, $inCodSetor, $inCodLocal) = explode("_", $key);

        if ($stNomeVar == "inCodOrgao") {
            # Seta as chaves da tabela para futura atualização.
            $obTMigraOrganogramaLocal->setDado('ano_exercicio'		  , $stAnoExercicio    );
            $obTMigraOrganogramaLocal->setDado('cod_orgao'             , $inCodOrgao        );
            $obTMigraOrganogramaLocal->setDado('cod_unidade'           , $inCodUnidade      );
            $obTMigraOrganogramaLocal->setDado('cod_departamento'      , $inCodDepartamento );
            $obTMigraOrganogramaLocal->setDado('cod_setor'             , $inCodSetor        );
            $obTMigraOrganogramaLocal->setDado('cod_local'             , $inCodLocal        );

            # Seta o novo valor correspondente ao organograma antigo.
            $obTMigraOrganogramaLocal->setDado('cod_local_organograma' , (!empty($value) ? $value : 'null'));
            $obTMigraOrganogramaLocal->alteracao();
        }
    }

    # Recupera o status da Migração, se está completa ou parcial.
    $obTMigraOrganogramaLocal->recuperaMigraTotalidade($rsTotalidade);
    $boMigraCompleto = $rsTotalidade->getCampo('finalizado');

    # Caso a migração esteja completa, atualiza o parâmetro para 'true'.
    $obTAdministracaoConfiguracao->setDado('cod_modulo', 19);
    $obTAdministracaoConfiguracao->setDado('exercicio', Sessao::getExercicio());
    $obTAdministracaoConfiguracao->setDado('parametro' , 'migra_local');
    $obTAdministracaoConfiguracao->setDado('valor'     , (($boMigraCompleto == 'true') ? 'true' : 'false'));
    $obTAdministracaoConfiguracao->alteracao();

    SistemaLegado::exibeAviso('Migração do Local do Organograma finalizada com sucesso!', 'aviso', '');

    Sessao::encerraExcecao();
