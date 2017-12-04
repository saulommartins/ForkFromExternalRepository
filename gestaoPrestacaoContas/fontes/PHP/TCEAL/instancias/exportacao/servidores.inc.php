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
    * Página de Include Oculta - Exportação Arquivos Relacionais - Servidores.xml
    *
    * Data de Criação: 06/06/2014
    *
    * @author: Jean Silva
    *
    $Id: servidores.inc.php 64853 2016-04-07 18:27:30Z carlos.silva $
    *
    * @ignore
    *
*/
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALServidores.class.php';

$obTTCEALServidores = new TTCEALServidores();

$codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());
$stNomeArquivo         = 'Servidores';
$arEsquemasEntidades   = array();

foreach (explode(',',$stEntidades) as $inCodEntidade) {
    $obTEntidade = new TEntidade();
    $stFiltro = " WHERE nspname = 'pessoal_".$inCodEntidade."'";
    $obTEntidade->recuperaEsquemasCriados($rsEsquema,$stFiltro);
    if ($rsEsquema->getNumLinhas() > 0 || $codEntidadePrefeitura ==$inCodEntidade ) {
        $arEsquemasEntidades[] = $inCodEntidade;
    }
}
    
    foreach ($arEsquemasEntidades as $inCodEntidade) {
        
        if ($codEntidadePrefeitura !=$inCodEntidade) {
            $stEntidade = '_'.$inCodEntidade;
            $entidade = $inCodEntidade;
        } else {
            $stEntidade = '';
        }
        
        if ($inCodEntidade ==  $codEntidadePrefeitura) {
            $inCodEntidade='';
            $entidade = $codEntidadePrefeitura;
        }
        
        $stPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $stPeriodoMovimentacao->setDado('dtInicial', $dtInicial);
        $stPeriodoMovimentacao->setDado('dtFinal', $dtFinal);
        $stPeriodoMovimentacao->recuperaPeriodoMovimentacaoTCEAL($rsPeriodoMovimentacao);
        
        if($rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao') != '') {
            $rsRecordSet = "rsServidores";
            $rsRecordSet .= $stEntidade;
            $$rsRecordSet = new RecordSet();
            
            $obTTCEALServidores->setDado('dt_inicial', $dtInicial);
            $obTTCEALServidores->setDado('dt_final', $dtFinal);
            $obTTCEALServidores->setDado('cod_periodo_movimentacao', $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
            $obTTCEALServidores->setDado('exercicio', Sessao::getExercicio());
            $obTTCEALServidores->setDado('entidade', $stEntidade  );
            $obTTCEALServidores->setDado('cod_entidade', $entidade );
            $obTTCEALServidores->recuperaServidores ($rsRecordSet);
            
            $idCount=0;
            $arResult = array();
            
            while (!$rsRecordSet->eof()) {
                $arResult[$idCount]['CodUndGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
                $arResult[$idCount]['CodigoUA'] = $rsRecordSet->getCampo('codigo_ua_verdadeiro');
                $arResult[$idCount]['Bimestre'] = $inBimestre;
                $arResult[$idCount]['Exercicio'] = Sessao::getExercicio();
                $arResult[$idCount]['Cpf'] = $rsRecordSet->getCampo('cpf');
                $arResult[$idCount]['Nome'] = $rsRecordSet->getCampo('nome');
                $arResult[$idCount]['DataNascimento'] = $rsRecordSet->getCampo('data_nascimento');
                $arResult[$idCount]['NomeMae'] = $rsRecordSet->getCampo('nome_mae');
                $arResult[$idCount]['NomePai'] = $rsRecordSet->getCampo('nome_pai');
                $arResult[$idCount]['PisPasep'] = $rsRecordSet->getCampo('pis_pasep');
                $arResult[$idCount]['TituloEleitoral'] = $rsRecordSet->getCampo('titulo_eleitoral');
                $arResult[$idCount]['DataAdmissao'] = $rsRecordSet->getCampo('dt_admissao');
                $arResult[$idCount]['CodVinculoEmpregaticio'] = $rsRecordSet->getCampo('cod_vinculo_empregaticio');
                $arResult[$idCount]['CodRegimePrevidenciario'] = $rsRecordSet->getCampo('cod_regime_previdenciario');
                $arResult[$idCount]['CodEscolaridade'] = $rsRecordSet->getCampo('cod_escolaridade');
                $arResult[$idCount]['SobCessao'] = $rsRecordSet->getCampo('sob_cessao');
                $arResult[$idCount]['CnpjEntidade'] = $rsRecordSet->getCampo('cnpj_entidade');
                $arResult[$idCount]['NomeEntidade'] = $rsRecordSet->getCampo('nome_entidade');
                $arResult[$idCount]['DataCessao'] = $rsRecordSet->getCampo('data_cessao');
                $arResult[$idCount]['DataRetornoCessao'] = $rsRecordSet->getCampo('data_retorno_cessao');
                $arResult[$idCount]['SalarioBruto'] = $rsRecordSet->getCampo('salario_bruto');
                $arResult[$idCount]['SalarioLiquido'] = $rsRecordSet->getCampo('salario_liquido');
                $arResult[$idCount]['MargemConsignada'] = $rsRecordSet->getCampo('margem_consignada');
                $arResult[$idCount]['CBO'] = $rsRecordSet->getCampo('cbo');
                $arResult[$idCount]['CodCargo'] = $rsRecordSet->getCampo('cod_cargo');
                $arResult[$idCount]['CodLotacao'] = $rsRecordSet->getCampo('cod_lotacao');
                $arResult[$idCount]['CodFuncao'] = $rsRecordSet->getCampo('cod_funcao');
                $arResult[$idCount]['Matricula'] = $rsRecordSet->getCampo('matricula');
                
                $idCount++;
                
                $rsRecordSet->proximo();
            }
        }
    }
    
unset($UndGestora, $CodUndGestora, $obTTCEALServidor, $stPeriodoMovimentacao, $obTEntidade);
?>