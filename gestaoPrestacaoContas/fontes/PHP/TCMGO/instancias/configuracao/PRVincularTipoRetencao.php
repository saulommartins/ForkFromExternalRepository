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
    * Titulo do arquivo : Arquivo de processamento do vinculo do tipo de retencao
    * Data de Criação   : 06/04/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TTGO . 'TTCMGODeParaTipoRetencao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "VincularTipoRetencao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao( true );

switch ($stAcao) {
    case 'configurar' :
        $obTTCMGODeParaTipoRetencao = new TTCMGODeParaTipoRetencao();
        $stChave = $obTTCMGODeParaTipoRetencao->getComplementoChave();
        $obTTCMGODeParaTipoRetencao->setComplementoChave('exercicio');
        $obTTCMGODeParaTipoRetencao->setDado('exercicio',Sessao::getExercicio());
        $obTTCMGODeParaTipoRetencao->exclusao();
        $obTTCMGODeParaTipoRetencao->setComplementoChave($stChave);

        foreach ($_REQUEST as $stKey => $stValue) {
            if (strpos($stKey,'slRetencao') !== false AND $stValue != '') {
                $arRetencao = explode('_',$stKey);
                $arTipo = explode('_',$stValue);

                $obTTCMGODeParaTipoRetencao->setDado('exercicio_tipo',$arTipo[1]);
                $obTTCMGODeParaTipoRetencao->setDado('cod_tipo',$arTipo[0]);
                $obTTCMGODeParaTipoRetencao->setDado('exercicio',$arRetencao[2]);
                $obTTCMGODeParaTipoRetencao->setDado('cod_plano',$arRetencao[1]);
                $obErro = $obTTCMGODeParaTipoRetencao->inclusao();
            }
        }
        if (!$obErro->ocorreu) {
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso('',"n_incluir","erro");
        }
}

Sessao::encerraExcecao();
