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
    * Página de Include Oculta - Exportação Arquivos Execução - ComprovanteLiquidacao.xml
    *
    * Data de Criação: 02/06/2014
    *
    * @author: Michel Teixeira
    *
    $Id: comprovanteLiquidacao.inc.php 59693 2014-09-05 12:39:50Z carlos.silva $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALComprovanteLiquidacao.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$obTTCEALComprovanteLiquidacao = new TTCEALComprovanteLiquidacao();

$UndGestora = explode(',', $stEntidades);
if(count($UndGestora)>1){
    $obTOrcamentoEntidade = new TOrcamentoEntidade;
    foreach ($UndGestora as $cod_entidade) {
        $obTOrcamentoEntidade->setDado( 'exercicio', Sessao::getExercicio() );
        $obTOrcamentoEntidade->setDado( 'cod_entidade', $cod_entidade );
        $stCondicao = " AND CGM.nom_cgm ILIKE 'prefeitura%' ";
        $obTOrcamentoEntidade->recuperaRelacionamentoNomes( $rsEntidade, $stCondicao );
        
        if($rsEntidade->inNumLinhas>0)
            $CodUndGestora = $cod_entidade;
    }
    if(!$CodUndGestora)
        $CodUndGestora = $UndGestora[0];
} else {
    $CodUndGestora = $stEntidades;
}

$obTTCEALComprovanteLiquidacao->setDado('exercicio'     , Sessao::getExercicio());
$obTTCEALComprovanteLiquidacao->setDado('cod_entidade'  , $stEntidades          );
$obTTCEALComprovanteLiquidacao->setDado('und_gestora'   , $CodUndGestora        );
$obTTCEALComprovanteLiquidacao->setDado('dtInicial'     , $dtInicial            );
$obTTCEALComprovanteLiquidacao->setDado('dtFinal'       , $dtFinal              );
$obTTCEALComprovanteLiquidacao->setDado('bimestre'      , $inBimestre           );

$obTTCEALComprovanteLiquidacao->recuperaComprovanteLiquidacao($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'ComprovanteLiquidacao';
$stVersao = "2.1";
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora']         = $rsRecordSet->getCampo( 'cod_und_gestora' );
    $arResult[$idCount]['CodigoUA']              = $rsRecordSet->getCampo( 'codigo_ua'       );
    $arResult[$idCount]['Bimestre']              = $rsRecordSet->getCampo( 'bimestre'        );
    $arResult[$idCount]['Exercicio']             = $rsRecordSet->getCampo( 'exercicio'       );
    $arResult[$idCount]['NumEmpenho']            = $rsRecordSet->getCampo( 'num_empenho'     );
    $arResult[$idCount]['NumLiquidacao']         = $rsRecordSet->getCampo( 'num_liquidacao'  );
    $arResult[$idCount]['TipoDocumento']         = $rsRecordSet->getCampo( 'tipo_documento'  );
    $arResult[$idCount]['NumDocumento']          = $rsRecordSet->getCampo( 'num_documento'   );
    $arResult[$idCount]['DataDocumento']         = $rsRecordSet->getCampo( 'data_documento'  );
    $arResult[$idCount]['Descricao']             = $rsRecordSet->getCampo( 'descricao'       );
    $arResult[$idCount]['AutorizacaoNotaFiscal'] = $rsRecordSet->getCampo( 'autorizacao'     );
    $arResult[$idCount]['ModeloNotaFiscal']      = $rsRecordSet->getCampo( 'modelo'          );
    $arResult[$idCount]['Valor']                 = $rsRecordSet->getCampo( 'valor'           );
    $arResult[$idCount]['NumXmlNFe']             = $rsRecordSet->getCampo( 'nro_xml_nfe'     );
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade, $obTTCEALComprovanteLiquidacao);
?>