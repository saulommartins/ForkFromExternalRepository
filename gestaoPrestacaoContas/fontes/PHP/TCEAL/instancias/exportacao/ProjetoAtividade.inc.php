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
    * Página de Include Oculta - Exportação Arquivos Relacionais - Credor.xml
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Franver Sarmento de Moraes
    *
    $Id: ProjetoAtividade.inc.php 60534 2014-10-27 18:04:24Z carlos.silva $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALProjetoAtividade.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$inCodUniGestora = explode(',', $stEntidades);

if (count($inCodUniGestora) > 1){
    $obTOrcamentoEntidade = new TOrcamentoEntidade;
    
    foreach ($inCodUniGestora as $cod_entidade) {
        $obTOrcamentoEntidade->setDado('exercicio'    , Sessao::getExercicio() );
        $obTOrcamentoEntidade->setDado('cod_entidade' , $cod_entidade );
        
        $stCondicao = " AND LOWER(CGM.nom_cgm) LIKE '%prefeitura%' ";
        
        $obTOrcamentoEntidade->recuperaRelacionamentoNomes( $rsEntidade, $stCondicao );
        
        if ($rsEntidade->inNumLinhas > 0) {
            $inCodUniGestora = $cod_entidade;
        }
    }

    if (!$inCodUniGestora) {
        $inCodUniGestora = $UndGestora[0];
    }
    
} else {
    $inCodUniGestora = $stEntidades;
}

$obTTCEALProjetoAtividade = new TTCEALProjetoAtividade();
$obTTCEALProjetoAtividade->setDado('exercicio'    , Sessao::getExercicio()    );
$obTTCEALProjetoAtividade->setDado('cod_entidade' , $stEntidades              );
$obTTCEALProjetoAtividade->setDado('bimestre'     , $request->get('bimestre') );
$obTTCEALProjetoAtividade->setDado('und_gestora'  , $inCodUniGestora          );

$obTTCEALProjetoAtividade->recuperaProjetoAtividade($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'ProjAtividade';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora']    = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']         = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre']         = $rsRecordSet->getCampo('bimestre');
    $arResult[$idCount]['Exercicio']        = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['CodProjAtividade'] = $rsRecordSet->getCampo('cod_proj_atividade');
    $arResult[$idCount]['Identificador']    = $rsRecordSet->getCampo('identificador');
    $arResult[$idCount]['Nome']             = $rsRecordSet->getCampo('nome');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $inCodUniGestora, $obTOrcamentoEntidade, $obTTCEALProjetoAtividade); 

?>