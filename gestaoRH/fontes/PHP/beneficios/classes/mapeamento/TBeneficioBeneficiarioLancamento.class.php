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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * @author Analista: Carlos Adriano
  * @author Desenvolvedor: Carlos Adriano

*/
class TBeneficioBeneficiarioLancamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TBeneficioBeneficiarioLancamento()
{
    parent::Persistente();
    $this->setTabela("beneficio.beneficiario_lancamento");

    $this->setCampoCod('cod_contrato, cgm_fornecedor, cod_modalidade, cod_tipo_convenio, codigo_usuario, timestamp, timestamp_lancamento');

    $this->AddCampo('cod_contrato'             , 'integer'       , true,  ''     , true  , true);
    $this->AddCampo('cgm_fornecedor'           , 'integer'       , true,  ''     , true  , true);
    $this->AddCampo('cod_modalidade'           , 'integer'       , true,  ''     , true  , true);
    $this->AddCampo('cod_tipo_convenio'        , 'integer'       , true,  ''     , true  , true);
    $this->AddCampo('codigo_usuario'           , 'integer'       , true,  ''   , true  , true);
    $this->AddCampo('timestamp'                , 'timestamp'     , true,  ''     , true  , true);
    $this->AddCampo('timestamp_lancamento'     , 'timestamp_now' , true,  ''     , true  , false);
    $this->AddCampo('valor'                    , 'numeric'       , true,  '14,2' , false , false);
    $this->AddCampo('cod_periodo_movimentacao' , 'integer'       , true,  ''     , false , true);
}

function verificaPeriodoMovimentacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaVerificaPeriodoMovimentacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificaPeriodoMovimentacao()
{
    $stSql .= "    SELECT *                                                                                                                                                                       \n";
    $stSql .= "      FROM folhapagamento.periodo_movimentacao                                                                                                                                     \n";
    $stSql .= "INNER JOIN ( SELECT * FROM folhapagamento.periodo_movimentacao_situacao WHERE situacao = 'a' ORDER BY cod_periodo_movimentacao DESC LIMIT 1 ) AS max_periodo_movimentacao_situacao \n";
    $stSql .= "        ON max_periodo_movimentacao_situacao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao                                                              \n";

    return $stSql;
}

}
?>
