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
    * Data de criação : 05/12/2008

    * @author Analista:     Gelson Wolowski
    * @author Programador:  Diogo Zarpelon

    * @ignore

    $Id:$

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ORGAN_MAPEAMENTO.'TMigraOrganograma.class.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';

$stPrograma = "MigraOrganograma";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

Sessao::setTrataExcecao(true);

$obTMigraOrganograma          = new TMigraOrganograma;
$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;

    foreach ($_REQUEST as $key => $value) {

        list($stNomeVar, $stAnoExercicio, $inCodOrgao, $inCodUnidade, $inCodDepartamento, $inCodSetor) = explode("_", $key);

        if ($stNomeVar == "inCodOrgao") {
            # Seta as chaves da tabela para futura atualização.
            $obTMigraOrganograma->setDado('ano_exercicio'		  , $stAnoExercicio    );
            $obTMigraOrganograma->setDado('cod_orgao'             , $inCodOrgao        );
            $obTMigraOrganograma->setDado('cod_unidade'           , $inCodUnidade      );
            $obTMigraOrganograma->setDado('cod_departamento'      , $inCodDepartamento );
            $obTMigraOrganograma->setDado('cod_setor'             , $inCodSetor        );

            # Seta o novo valor correspondente ao organograma antigo.
            $obTMigraOrganograma->setDado('cod_orgao_organograma' , (!empty($value) ? $value : 'null'));
            $obTMigraOrganograma->alteracao();
        }
    }

    # Recupera o status da Migração, se está completa ou parcial.
    $obTMigraOrganograma->recuperaMigraTotalidade($rsTotalidade);
    $boMigraCompleto = $rsTotalidade->getCampo('finalizado');

    # Caso a migração esteja completa, atualiza o parâmetro para 'true'.
    $obTAdministracaoConfiguracao->setDado('cod_modulo', 19);
    $obTAdministracaoConfiguracao->setDado('exercicio' , Sessao::getExercicio());
    $obTAdministracaoConfiguracao->setDado('parametro' , 'migra_setor');
    $obTAdministracaoConfiguracao->setDado('valor'     , (($boMigraCompleto == 'true') ? 'true' : 'false'));
    $obTAdministracaoConfiguracao->alteracao();

    SistemaLegado::exibeAviso('Migração do Setor do Organograma finalizada com sucesso!', 'aviso', '');

    Sessao::encerraExcecao();
