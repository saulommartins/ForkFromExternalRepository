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
    * Página de Processamento de Vinculo do Plano de Contas ao TCE-MG
    * Data de Criação   : 13/07/2016

    * @author: Michel Teixeira

    * @ignore
    * $Id: PRVincularPlanoContas.php 66067 2016-07-14 17:27:32Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGPlanoContas.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "VincularPlanoContas";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $request->get("stAcao");

switch ($stAcao) {
    default:
        $obErro = new Erro();
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $obTTCEMGPlanoContas = new TTCEMGPlanoContas;

        if(!$obErro->ocorreu()){
            $inCodUF = $request->get('inCodUF');
            $inCodPlano = $request->get('inCodPlano');
            $stExercicio = $request->get('stExercicio');
            $inCodGrupo = $request->get('inCodGrupo');

            if(empty($inCodGrupo))
                $obErro->setDescricao("Grupo de Contas inválido()!");
            if(empty($stExercicio))
                $obErro->setDescricao("Exercício inválido()!");
            if(empty($inCodPlano))
                $obErro->setDescricao("Versão de Plano inválido()!");
            if(empty($inCodUF))
                $obErro->setDescricao("UF inválido()!");

            if(!$obErro->ocorreu()){
                $inCount = 0;
                $arContas = array();
                $arContasGrupo = array();
                foreach( $request->getAll() AS $key => $value ){
                    if (strpos($key,'slPlano') !== false) {
                        list($stChave, $inConta, $inLinha) = explode('_', $key);

                        if(!empty($value)){
                            $arContas[$inCount]['inConta'] = $inConta;
                            $arContas[$inCount]['stEstrutural'] = $value;
                            $inCount++;
                        }

                        $arContasGrupo[]['inConta'] = $inConta;
                    }
                }

                $obTTCEMGPlanoContas->setDado('exercicio' , $stExercicio);
                $obTTCEMGPlanoContas->setDado('cod_uf'    , $inCodUF);
                $obTTCEMGPlanoContas->setDado('cod_plano' , $inCodPlano);

                foreach( $arContasGrupo AS $chave => $contaGrupo ){
                    $obTTCEMGPlanoContas->setDado('cod_conta' , $contaGrupo['inConta']);
                    $obErro = $obTTCEMGPlanoContas->exclusao($boTransacao);
                }

                if(!$obErro->ocorreu()){
                    foreach( $arContas AS $chave => $conta ){
                        $obTTCEMGPlanoContas->setDado('cod_conta'         , $conta['inConta']);
                        $obTTCEMGPlanoContas->setDado('codigo_estrutural' , $conta['stEstrutural']);

                        $obErro = $obTTCEMGPlanoContas->inclusao($boTransacao);

                        if($obErro->ocorreu())
                            break;
                    }
                }
            }
        }

        if(!$obErro->ocorreu())
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao, 'Exercicio: '.$stExercicio.' e Grupo de Contas: '.$inCodGrupo, "incluir","incluir_n", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

        $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGPlanoContas);
    break;
}

SistemaLegado::LiberaFrames();
?>
