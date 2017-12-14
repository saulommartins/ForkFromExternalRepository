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

class TTCMBAConsIngExtOrc extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMBAConsIngExtOrc()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
}

function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosTribunal()
{
    $stSql .= " SELECT tipo_registro
                      ,unidade_gestora
                      ,REPLACE(cod_contabil,'.','') AS cod_contabil
                      ,".$this->getDado('exercicio')."::VARCHAR||".$this->getDado('mes')."::VARCHAR AS competencia
                      ,COALESCE(SUM(vl_ext_mes),0.00) AS vl_ext_mes
                      ,COALESCE(SUM(vl_ext_ate_mes),0.00) AS vl_ext_ate_mes
                  FROM (
                        SELECT 1 AS tipo_registro
                              ,".$this->getDado('unidade_gestora')." AS unidade_gestora
                              ,plano_conta.cod_estrutural AS cod_contabil
                              ,transferencia.valor AS vl_ext_mes
                              ,(SELECT transferencia.valor
                                  FROM tesouraria.transferencia AS tt
                                  WHERE tt.cod_tipo = transferencia.cod_tipo
                                    AND tt.tipo = transferencia.tipo
                                    AND tt.cod_entidade = transferencia.cod_entidade
                                    AND tt.cod_lote = transferencia.cod_lote
                                    AND tt.exercicio = transferencia.exercicio
                                    AND TO_DATE(tt.timestamp_transferencia::VARCHAR,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial_ano')."'::VARCHAR,'dd/mm/yyyy') and TO_DATE('".$this->getDado('dt_final')."'::VARCHAR,'dd/mm/yyyy')
                              ) AS vl_ext_ate_mes

                          FROM tesouraria.transferencia

                    INNER JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_plano = transferencia.cod_plano_credito
                           AND plano_analitica.exercicio = transferencia.exercicio

                    INNER JOIN contabilidade.plano_conta
                            ON plano_conta.cod_conta = plano_analitica.cod_conta
                           AND plano_conta.exercicio = plano_analitica.exercicio

                         WHERE transferencia.cod_tipo = 2
                           AND transferencia.tipo = 'T'
                           AND TO_DATE(transferencia.timestamp_transferencia::VARCHAR,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."'::VARCHAR,'dd/mm/yyyy') and TO_DATE('".$this->getDado('dt_final')."'::VARCHAR,'dd/mm/yyyy')
                           AND transferencia.cod_entidade IN (".$this->getDado('entidades').")
                           AND transferencia.exercicio = '".$this->getDado('exercicio')."'
                           AND ((plano_conta.cod_estrutural ilike '1%') OR (plano_conta.cod_estrutural ilike '2%'))

                      GROUP BY plano_conta.cod_estrutural
                              ,transferencia.cod_tipo
                              ,transferencia.tipo
                              ,transferencia.exercicio
                              ,transferencia.cod_lote
                              ,transferencia.cod_entidade
                        ) AS retorno

                GROUP BY cod_contabil
                        ,tipo_registro
                        ,unidade_gestora
            ";
            
    return $stSql;
}

}