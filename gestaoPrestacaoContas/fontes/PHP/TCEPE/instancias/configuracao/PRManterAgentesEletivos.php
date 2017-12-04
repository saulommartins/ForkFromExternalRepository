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
    * Pacote de configuração do TCEPE
    * Data de Criação   : 30/09/2014

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Michel Teixeira
    *
    $Id: PRManterAgentesEletivos.php 60109 2014-09-30 18:14:20Z michel $
    *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPERelacionarAgenteEletivo.class.php';

$stPrograma = "ManterAgentesEletivos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao ( true );
$obTTCEPERelacionarAgenteEletivo = new TTCEPERelacionarAgenteEletivo();
Sessao::getTransacao()->setMapeamento( $obTTCEPERelacionarAgenteEletivo );

$obTTCEPERelacionarAgenteEletivo->setDado('exercicio', Sessao::getExercicio());
$obTTCEPERelacionarAgenteEletivo->setDado('cod_entidade', Sessao::read('cod_entidade'));
$obTTCEPERelacionarAgenteEletivo->exclusao();

$arAgentesSessao = Sessao::read('arAgentes');

foreach ($arAgentesSessao as $arAgentesSessaoTmp) {
    foreach ($arAgentesSessaoTmp["cargos"] as $arCargoSelecionado) {
        $obTTCEPERelacionarAgenteEletivo->setDado('exercicio'           , Sessao::getExercicio()                        );
        $obTTCEPERelacionarAgenteEletivo->setDado('cod_entidade'        , Sessao::read('cod_entidade')                  );
        $obTTCEPERelacionarAgenteEletivo->setDado('cod_tipo_remuneracao', $arAgentesSessaoTmp["cod_tipo_remuneracao"]   );
        $obTTCEPERelacionarAgenteEletivo->setDado('cod_tipo_norma'      , $arAgentesSessaoTmp["cod_tipo_norma"]         );
        $obTTCEPERelacionarAgenteEletivo->setDado('cod_norma'           , $arAgentesSessaoTmp["cod_norma"]              );
        $obTTCEPERelacionarAgenteEletivo->setDado('cod_cargo'           , $arCargoSelecionado                           );

        $obTTCEPERelacionarAgenteEletivo->inclusao();
    }
}

SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao,"Configuração ","incluir","incluir_n", Sessao::getId(), "../");
Sessao::encerraExcecao();

?>