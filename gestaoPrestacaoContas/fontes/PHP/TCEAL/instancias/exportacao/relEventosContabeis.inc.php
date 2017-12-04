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
    * Página de Include Oculta - Exportação Arquivos Relacionais - BemVinculado.xml
    *
    * Data de Criação: 05/08/2014
    *
    * @author: Carlos Adriano
    *
    $Id: bemVinculado.inc.php 59756 2014-09-09 19:50:47Z carlos.silva $
    *
    * @ignore
    *
*/
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALRelEventosContabeis.class.php';

$obTTCEALRelEventosContabeis  = new TTCEALRelEventosContabeis();
$codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());
$stNomeArquivo         = 'RelEventosContabeis';

foreach (explode(',',$stEntidades) as $inCodEntidade) {
    $arEsquemasEntidades[] = $inCodEntidade;
}
    
foreach ($arEsquemasEntidades as $inCodEntidade) {
    
    $rsRecordSet = "rsBemVinculado";
    $rsRecordSet .= $stEntidade;
    $$rsRecordSet = new RecordSet();
    
    $obTTCEALRelEventosContabeis->setDado('stExercicio'  , Sessao::getExercicio());
    $obTTCEALRelEventosContabeis->setDado('inCodEntidade', $inCodEntidade );
    $obTTCEALRelEventosContabeis->setDado('bimestre'     , $inBimestre  );
    $obTTCEALRelEventosContabeis->setDado('dt_inicial'   , $dtInicial );
    $obTTCEALRelEventosContabeis->setDado('dt_final'     , $dtFinal );
    $obTTCEALRelEventosContabeis->recuperaRelacionamento ($rsRecordSet);
        
    $idCount=0;
    $arResult = array();
    
    while (!$rsRecordSet->eof()) {
        $arResult[$idCount]['CodUndGestora']        = $rsRecordSet->getCampo('cod_und_gestora');
        $arResult[$idCount]['CodigoUA']             = $rsRecordSet->getCampo('codigo_ua');
        $arResult[$idCount]['Exercicio']            = $rsRecordSet->getCampo('exercicio');
        $arResult[$idCount]['Bimestre']             = $rsRecordSet->getCampo('bimestre');
        $arResult[$idCount]['CodEvento']            = $rsRecordSet->getCampo('cod_evento');
        $arResult[$idCount]['TituloEvento']         = $rsRecordSet->getCampo('titulo_evento');
        $arResult[$idCount]['IdentificadorDebCred'] = $rsRecordSet->getCampo('id_debcred');
        $arResult[$idCount]['CodContaContabil']     = $rsRecordSet->getCampo('cod_conta_contabil');
        
        $idCount++;
        
        $rsRecordSet->proximo();
    }
}
    
unset($UndGestora, $CodUndGestora, $obTTCEALRelEventosContabeis, $stPeriodoMovimentacao, $obTEntidade);

?>