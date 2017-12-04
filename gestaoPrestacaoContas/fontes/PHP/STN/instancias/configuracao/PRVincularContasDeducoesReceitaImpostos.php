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
    * Processamento de Configuração do Anexo 4
    * Data de Criação   : 05/04/2013

    * @author Desenvolvedor: Davi Ritter Aroldi

    * @package URBEM
    * @subpackage Configuração

    $Id: PRVincularContasDeducoesReceitaImpostos.php 66446 2016-08-29 21:18:46Z michel $

    * Casos de uso: uc-02.08.07
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GPC_STN_MAPEAMENTO."TSTNTributoAnexo8.class.php";
require_once CAM_GPC_STN_MAPEAMENTO."TSTNContaDedutoraTributos.class.php";

$stPrograma = "VincularContasDeducoesReceitaImpostos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obErro = new Erro;
$obTransacao = new Transacao;
$obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

if (!$obErro->ocorreu()) {
    $obTSTNTributoAnexo8 = new TSTNTributoAnexo8;

    $obErro = $obTSTNTributoAnexo8->listarTributoAnexo8($rsTributos, "", "", $boTransacao);

    if (!$obErro->ocorreu()) {
        $obTSTNContaDedutoraTributos = new TSTNContaDedutoraTributos;
        $obTSTNContaDedutoraTributos->setDado('exercicio', Sessao::getExercicio());
        $obErro = $obTSTNContaDedutoraTributos->exclusao($boTransacao);

        if (!$obErro->ocorreu()) {
            while (!$rsTributos->eof()) {
                $arReceitaTributo = Sessao::read("arReceitaTributo_".$rsTributos->getCampo('cod_tributo'));

                foreach ($arReceitaTributo as $receitaTributo) {
                    $obTSTNContaDedutoraTributos->setDado('cod_tributo', $receitaTributo['cod_tributo']);
                    $obTSTNContaDedutoraTributos->setDado('cod_receita', $receitaTributo['cod_receita']);
                    $obTSTNContaDedutoraTributos->setDado('exercicio', Sessao::getExercicio());

                    $obErro = $obTSTNContaDedutoraTributos->inclusao($boTransacao);

                    if ($obErro->ocorreu()) {
                        break 2;
                    }
                }

                $rsTributos->proximo();
            }
        }
    }
}

if ($obErro->ocorreu()) {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
} else {
    SistemaLegado::alertaAviso($pgForm, "Configuração realizada com sucesso!", 'incluir', 'aviso');
}

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTSTNContaDedutoraTributos );
?>
