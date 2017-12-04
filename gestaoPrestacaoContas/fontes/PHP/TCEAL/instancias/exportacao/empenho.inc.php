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
    * @author: Michel Teixeira
    *
    $Id: credor.inc.php 58365 2014-05-27 18:43:38Z michel $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALEmpenho.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$obTTCEALEmpenho = new TTCEALEmpenho();

$obTTCEALEmpenho->setDado('exercicio'     , Sessao::getExercicio());
$obTTCEALEmpenho->setDado('cod_entidade'  , $stEntidades          );
$obTTCEALEmpenho->setDado('und_gestora'   , $stEntidades        );
$obTTCEALEmpenho->setDado('dt_inicial'    , $dtInicial            );
$obTTCEALEmpenho->setDado('dt_final'      , $dtFinal              );
$obTTCEALEmpenho->setDado('bimestre'      , $inBimestre           );
$obTTCEALEmpenho->recuperaEmpenho($rsRecordSet);

$idCount = 0;
$inSequencial = 1;
$stNomeArquivo = 'Empenho';
$arResult = array();

while (!$rsRecordSet->eof()) {        
    $arResult[$idCount]['CodUndGestora']        = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']             = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre']             = $rsRecordSet->getCampo('bimestre');;
    $arResult[$idCount]['Exercicio']            = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['NumEmpenho']           = $rsRecordSet->getCampo('num_empenho');
    $arResult[$idCount]['DataEmpenho']          = $rsRecordSet->getCampo('dt_empenho');
    $arResult[$idCount]['Valor']                = $rsRecordSet->getCampo('vl_empenho');
    $arResult[$idCount]['Sinal']                = $rsRecordSet->getCampo('sinal');
    $arResult[$idCount]['Tipo']                 = $rsRecordSet->getCampo('tipo');
    $arResult[$idCount]['CodOrgao']             = $rsRecordSet->getCampo('num_orgao');
    $arResult[$idCount]['CodUndOrcamentaria']   = $rsRecordSet->getCampo('num_unidade');
    $arResult[$idCount]['CodFuncao']            = $rsRecordSet->getCampo('cod_funcao');
    $arResult[$idCount]['CodSubFuncao']         = $rsRecordSet->getCampo('cod_subfuncao');
    $arResult[$idCount]['CodPrograma']          = $rsRecordSet->getCampo('cod_programa');
    $arResult[$idCount]['CodProjAtividade']     = $rsRecordSet->getCampo('num_pao');
    $arResult[$idCount]['CodContaDespesa']      = $rsRecordSet->getCampo('cod_estrutural');
    $arResult[$idCount]['CodContaContabil']     = $rsRecordSet->getCampo('cod_conta_contabil');
    $arResult[$idCount]['CodRecVinculado']      = $rsRecordSet->getCampo('cod_recurso');
    $arResult[$idCount]['ContraPartida']        = $rsRecordSet->getCampo('contra_partida');
    $arResult[$idCount]['CodCredor']            = $rsRecordSet->getCampo('cod_credor');
    $arResult[$idCount]['ModalLicita']          = $rsRecordSet->getCampo('modal_licita');
    $arResult[$idCount]['RegistroDePreco']      = $rsRecordSet->getCampo('registro_preco');
    $arResult[$idCount]['ReferenciaLegal']      = $rsRecordSet->getCampo('referencia_legal');
    $arResult[$idCount]['NumProcesso']          = $rsRecordSet->getCampo('num_processo');
    $arResult[$idCount]['DataProcesso']         = $rsRecordSet->getCampo('dt_processo');
    $arResult[$idCount]['NumContrato']          = $rsRecordSet->getCampo('num_contrato');
    $arResult[$idCount]['DataContrato']         = $rsRecordSet->getCampo('dt_contrato');
    $arResult[$idCount]['NumConvenio']          = $rsRecordSet->getCampo('num_convenio');
    $arResult[$idCount]['DataConvenio']         = $rsRecordSet->getCampo('dt_convenio');
    $arResult[$idCount]['NumObra']              = $rsRecordSet->getCampo('num_obra');
    $arResult[$idCount]['CaracPeculiar']        = $rsRecordSet->getCampo('carac_peculiar');
    $arResult[$idCount]['Historico']            = $rsRecordSet->getCampo('historico');
    
    $idCount++;
    $inSequencial++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade, $obTTCEALEmpenho);

?>