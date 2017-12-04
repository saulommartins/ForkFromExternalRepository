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
    * Página de Include Oculta - Exportação Arquivos Orcamento - eventosContabeis.xml
    *
    * Data de Criação: 31/03/2015
    *
    * @author: Arthur Cruz
    *
    $Id: $
    *
*/
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALEventosContabeis.class.php';

$stNomeArquivo             = 'EventosContabeis';
$codEntidadePrefeitura     = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());
$obTTCEALEventosContabeis  = new TTCEALEventosContabeis();

foreach (explode(',',$stEntidades) as $inCodEntidade) {
    $arEsquemasEntidades[] = $inCodEntidade;
}

foreach ($arEsquemasEntidades as $inCodEntidade) {
    
    $rsRecordSet = "rsEventosContabeis";
    $rsRecordSet .= $stEntidade;
    $$rsRecordSet = new RecordSet();
    
    $obTTCEALEventosContabeis->setDado('stExercicio'  , Sessao::getExercicio());
    $obTTCEALEventosContabeis->setDado('inCodEntidade', $inCodEntidade );
    $obTTCEALEventosContabeis->setDado('bimestre'     , $inBimestre  );
    $obTTCEALEventosContabeis->setDado('dt_inicial'   , $dtInicial );
    $obTTCEALEventosContabeis->setDado('dt_final'     , $dtFinal );
    $obTTCEALEventosContabeis->recuperaEventosContabeis ($rsRecordSet);

    $idCount = 0;
    $inNumEvento = 1;
    $arResult = array();
    
    while (!$rsRecordSet->eof()) {
        $arResult[$idCount]['CodUndGestora']        = $rsRecordSet->getCampo('cod_und_gestora');
        $arResult[$idCount]['CodigoUA']             = $rsRecordSet->getCampo('codigo_ua');
        $arResult[$idCount]['Exercicio']            = $rsRecordSet->getCampo('exercicio');
        $arResult[$idCount]['Bimestre']             = $rsRecordSet->getCampo('bimestre');
        $arResult[$idCount]['NumEvento']            = str_pad($inNumEvento, 6, "0", STR_PAD_LEFT);
        $arResult[$idCount]['CodEvento']            = $rsRecordSet->getCampo('cod_evento');
        $arResult[$idCount]['Historico']            = $rsRecordSet->getCampo('historico');
        $arResult[$idCount]['DataLancamento']       = $rsRecordSet->getCampo('dt_lancamento');
        $arResult[$idCount]['IdentificadorDebCred'] = $rsRecordSet->getCampo('id_debcred');
        $arResult[$idCount]['CodContaContabil']     = $rsRecordSet->getCampo('cod_conta_contabil');
        $arResult[$idCount]['ValorLancamento']      = $rsRecordSet->getCampo('vl_lancamento');
        
        $idCount++;
        $inNumEvento++;
        
        $rsRecordSet->proximo();
    }
}
    
unset($UndGestora, $CodUndGestora, $obTTCEALEventosContabeis, $stPeriodoMovimentacao, $obTEntidade);

?>