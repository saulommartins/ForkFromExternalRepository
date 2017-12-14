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
 * Página de Include Oculta - Exportação Arquivos Execucao - OcorrenciasFuncionais.xml
 *
 * Data de Criação: 3/07/2014
 *
 * @author: Arthur Cruz.
 *
 */
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALOcorrenciasFuncionais.class.php';

$obTTCEALOcorrenciasFuncionais = new TTCEALOcorrenciasFuncionais();
$codEntidadePrefeitura         = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());
$stNomeArquivo                 = 'OcorrenciasFuncionais';
$arEsquemasEntidades           = array();

foreach (explode(',',$stEntidades) as $inCodEntidade) {
    $obTEntidade = new TEntidade();
    $stFiltro = " WHERE nspname = 'pessoal_".$inCodEntidade."'";
    $obTEntidade->recuperaEsquemasCriados($rsEsquema,$stFiltro);

    if ( $rsEsquema->getNumLinhas() > 0 ) {
        $arEsquemasEntidades[] = $inCodEntidade;
    }
}
  
foreach ($arEsquemasEntidades as $inCodEntidade) {
        
    if ($codEntidadePrefeitura != $inCodEntidade) {
        $stEntidade = '_'.$inCodEntidade;
        $entidade = $inCodEntidade;
    } else {
        $stEntidade = '';
    }
    
    if ($inCodEntidade ==  $codEntidadePrefeitura) {
        $inCodEntidade='';
        $entidade = $codEntidadePrefeitura;
    }

    $obTTCEALOcorrenciasFuncionais->setDado('stExercicio'  , Sessao::getExercicio());
    $obTTCEALOcorrenciasFuncionais->setDado('stEntidade'   , $stEntidade  );
    $obTTCEALOcorrenciasFuncionais->setDado('inCodEntidade', $inCodEntidade );
    $obTTCEALOcorrenciasFuncionais->setDado('entidade'     , $entidade    );
    $obTTCEALOcorrenciasFuncionais->setDado('bimestre'     , $request->get('bimestre') );
    $obTTCEALOcorrenciasFuncionais->setDado('dtInicial'    , $dtInicial   );
    $obTTCEALOcorrenciasFuncionais->setDado('dtFinal'      , $dtFinal     );
    
    $rsRecordSet = "rsOcorrencias";
    $rsRecordSet .= $stEntidade;
    $$rsRecordSet = new RecordSet();
    
    $obTTCEALOcorrenciasFuncionais->recuperaOcorrenciasFuncionais($rsRecordSet);
    
    $idCount = 0;
    $arResult = array();
        
    while (!$rsRecordSet->eof()) {
        $arResult[$idCount]['CodUndGestora']          = $rsRecordSet->getCampo('cod_und_gestora');
        $arResult[$idCount]['CodigoUA']               = ($rsRecordSet->getCampo('codigo_ua') == "" ? '0000' : $rsRecordSet->getCampo('codigo_ua'));
        $arResult[$idCount]['Bimestre']               = $rsRecordSet->getCampo('bimestre');
        $arResult[$idCount]['Exercicio']              = $rsRecordSet->getCampo('exercicio');    
        $arResult[$idCount]['Cpf']                    = $rsRecordSet->getCampo('cpf');
        $arResult[$idCount]['Matricula']              = $rsRecordSet->getCampo('matricula');
        $arResult[$idCount]['CodOcorrencia']          = $rsRecordSet->getCampo('cod_ocorrencia');
        $arResult[$idCount]['DataInicioOcorrencia']   = $rsRecordSet->getCampo('data_inicio_ocorrencia');
        $arResult[$idCount]['InformacaoComplementar'] = $rsRecordSet->getCampo('informacao_complementar');
    
        $idCount++;
        
        $rsRecordSet->proximo();
    }
}

unset($UndGestora, $inCodUniGestora, $obTOrcamentoEntidade, $obTTCEALOcorrenciasFuncionais);

?>
