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
    * Página de Include Oculta - Exportação Arquivos Execucao- AtivoPermanente.xml
    *
    * Data de Criação: 28/10/2014
    *
    * @author: Lisiane Morais
    *
    $Id: ativoPermanente.inc.php 61572 2015-02-09 16:23:27Z michel $
    *
    * @ignore
    *
*/
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALAtivoPermanente.class.php';

$obTTCEALAtivoPermanente= new TTCEALAtivoPermanente();
$codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());
$stNomeArquivo         = 'AtivoPermanente';

foreach (explode(',',$stEntidades) as $inCodEntidade) {
    $arEsquemasEntidades[] = $inCodEntidade;
}
    
foreach ($arEsquemasEntidades as $inCodEntidade) {
    
    $rsRecordSet = "rsAtivoPermanente";
    $rsRecordSet .= $stEntidade;
    $$rsRecordSet = new RecordSet();
    
    $obTTCEALAtivoPermanente->setDado('stExercicio'  , Sessao::getExercicio());
    $obTTCEALAtivoPermanente->setDado('inCodEntidade', $inCodEntidade        );
    $obTTCEALAtivoPermanente->setDado('bimestre'     , $inBimestre           );
    $obTTCEALAtivoPermanente->setDado('dt_inicial'    , $dtInicial           );
    $obTTCEALAtivoPermanente->setDado('dt_final'     , $dtFinal              );
    $obTTCEALAtivoPermanente->recuperaAtivoPermanente($rsRecordSet           );

    $idCount=0;
    $arResult = array();
    
    while (!$rsRecordSet->eof()) {
        $arResult[$idCount]['CodUndGestora']                = $rsRecordSet->getCampo('cod_und_gestora');
        $arResult[$idCount]['CodigoUA']                     = $rsRecordSet->getCampo('codigo_ua');
        $arResult[$idCount]['Bimestre']                     = $rsRecordSet->getCampo('bimestre');
        $arResult[$idCount]['Exercicio']                    = $rsRecordSet->getCampo('exercicio');
        $arResult[$idCount]['CodOrgao']                     = $rsRecordSet->getCampo('cod_orgao');
        $arResult[$idCount]['CodUndOrcamentaria']           = $rsRecordSet->getCampo('cod_und_orcamentaria');
        $arResult[$idCount]['NumBem']                       = $rsRecordSet->getCampo('num_bem');
        $arResult[$idCount]['Descricao']                    = $rsRecordSet->getCampo('descricao');
        $arResult[$idCount]['DataInscricao']                = $rsRecordSet->getCampo('data_inscricao');
        $arResult[$idCount]['NumEmpenho']                   = $rsRecordSet->getCampo('num_empenho');
        $arResult[$idCount]['NumDocumentoFiscal']           = $rsRecordSet->getCampo('numero_documento_fiscal'); 
        $arResult[$idCount]['DataDocumentoFiscal']          = $rsRecordSet->getCampo('data_doc_fiscal');
        $arResult[$idCount]['TipoDocumentoFiscal']          = $rsRecordSet->getCampo('tipo_documento_fiscal');
        $arResult[$idCount]['ValorBem']                     = $rsRecordSet->getCampo('valor_bem');
        $arResult[$idCount]['Quantidade']                   = $rsRecordSet->getCampo('quantidade');
        $arResult[$idCount]['Setor']                        = $rsRecordSet->getCampo('setor');
        $arResult[$idCount]['NumTombamento']                = $rsRecordSet->getCampo('num_tombamento');
        $arResult[$idCount]['CodContaContabil']             = $rsRecordSet->getCampo('cod_estrutural');
        $arResult[$idCount]['EstadoBem']                    = $rsRecordSet->getCampo('estado_bem');
        $arResult[$idCount]['AlteracaoBemAtivoPermanente']  = $rsRecordSet->getCampo('alteracao_bem');
        $arResult[$idCount]['DataAlteracao']                = $rsRecordSet->getCampo('dt_alteracao');
        $arResult[$idCount]['ValorAlteracao']               = $rsRecordSet->getCampo('vl_alteracao');
        $arResult[$idCount]['Percentual']                   = $rsRecordSet->getCampo('percentual');
        
        $idCount++;
        
        $rsRecordSet->proximo();
    }
}
    
unset($UndGestora, $CodUndGestora, $obTTCEALServidor, $stPeriodoMovimentacao, $obTEntidade);
?>