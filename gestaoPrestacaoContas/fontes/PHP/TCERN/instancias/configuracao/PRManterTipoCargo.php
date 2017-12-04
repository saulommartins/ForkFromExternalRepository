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
    * Titulo do arquivo : Arquivo de processamento do vinculo do tipo de cargo
    * Data de Criação: 18/07/2013

    * @author Analista:
    * @author Desenvolvedor Tallis

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCERN_MAPEAMENTO.'TTCERNSubdivisaoDescricaoSIAI.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ManterTipoCargo';
$pgFilt    = 'FL'.$stPrograma.'.php';
$pgList    = 'LS'.$stPrograma.'.php';
$pgForm    = 'FM'.$stPrograma.'.php';
$pgProc    = 'PR'.$stPrograma.'.php';
$pgOcul    = 'OC'.$stPrograma.'.php';

$stAcao = $request->get('stAcao');
$inCodEntidade = Sessao::getCodEntidade($boTransacao);
Sessao::setTrataExcecao(true);

switch ($stAcao) {
case 'configurar' :
    $obErro = new Erro;
    $obTransacao = new Transacao;
    $obTransacao->abreTransacao($boFlag, $boTransacao);

    $obTTCERNSubdivisaoDescricaoSIAI = new TTCERNSubdivisaoDescricaoSIAI();
    $obTTCERNSubdivisaoDescricaoSIAI->setDado('exercicio', Sessao::getExercicio());
    $obTTCERNSubdivisaoDescricaoSIAI->setDado('cod_entidade', $inCodEntidade);
    $obTTCERNSubdivisaoDescricaoSIAI->recuperaEntidade($rsSubdivisaoDescricaoSIAI, "", "", $boTransacao);

    while (!$rsSubdivisaoDescricaoSIAI->EOF()) {
        $obTTCERNSubdivisaoDescricaoSIAI->setDado('exercicio', Sessao::getExercicio());
        $obTTCERNSubdivisaoDescricaoSIAI->setDado('cod_entidade', $inCodEntidade);
        $obTTCERNSubdivisaoDescricaoSIAI->setDado('cod_sub_divisao', $rsSubdivisaoDescricaoSIAI->getCampo('cod_sub_divisao'));
        $obTTCERNSubdivisaoDescricaoSIAI->setDado('cod_siai', $rsSubdivisaoDescricaoSIAI->getCampo('cod_siai'));
        $obTTCERNSubdivisaoDescricaoSIAI->exclusao($boTransacao);
        $rsSubdivisaoDescricaoSIAI->proximo();
    }

    if (!$obErro->ocorreu()) {
        foreach ($_REQUEST as $stKey => $stValue) {
            if (strpos($stKey,'cmbCargo') !== false AND $stValue != '') {
                $arRetencao = explode('_',$stKey);

                $obTTCERNSubdivisaoDescricaoSIAI->setDado('exercicio', Sessao::getExercicio());
                $obTTCERNSubdivisaoDescricaoSIAI->setDado('cod_entidade', $inCodEntidade);
                $obTTCERNSubdivisaoDescricaoSIAI->setDado('cod_sub_divisao',$arRetencao[1]);
                $obTTCERNSubdivisaoDescricaoSIAI->setDado('cod_siai',$stValue);
                $obErro = $obTTCERNSubdivisaoDescricaoSIAI->inclusao($boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
            }
        }
    }

    if (!$obErro->ocorreu) {
        $obTransacao->commitAndClose();
        SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");

    } else {
        $obTransacao->rollbackAndClose();
        sistemaLegado::exibeAviso($obErro->getDescricao() ,"n_incluir","erro");
    }
}

Sessao::encerraExcecao();
