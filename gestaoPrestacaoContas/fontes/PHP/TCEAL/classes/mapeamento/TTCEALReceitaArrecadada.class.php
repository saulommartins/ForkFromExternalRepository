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
    * Extensão da Classe de Mapeamento TTCEALReceitaArrecadada
    *
    * Data de Criação: 04/07/2014
    *
    * @author: Evandro Melos
    *
    $Id: TTCEALReceitaArrecadada.class.php 65563 2016-05-31 20:36:59Z michel $
    *
    * @ignore
    *
*/
class TTCEALReceitaArrecadada extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALReceitaArrecadada()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    public function recuperaReceitaArrecadada(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaReceitaArrecadada().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaReceitaArrecadada()
    {
        $stSql ="
                SELECT
                         codigo_ua
                        ,cod_und_gestora
                        ,cod_orgao
                        ,cod_unid_orcamentaria
                        ,CASE WHEN cod_conta_contabil ilike '621310100000000' THEN 
                                RPAD(REPLACE(cod_estrutural, '.', ''),16,'0')
                        ELSE
                                cod_conta_receita
                        END as cod_conta_receita
                        ,cod_banco
                        ,cod_agencia
                        ,num_conta
                        ,data_arrecadacao
                        ,CASE WHEN cod_conta_contabil ilike '621310100000000' THEN
                                deducao_liquida
                            ELSE 
                                valor_arrecadado_liquido
                         END as valor
                        , LPAD(cod_rec_vinculado::VARCHAR,9,'0') as cod_rec_vinculado
                        , RPAD(cod_conta_contabil,17,'0') AS cod_conta_contabil
                        , cod_conta_ativo
                        , forma_arrecadacao
                        , data_registro
                FROM (
                    SELECT
                        (SELECT CASE WHEN valor = '' THEN '0000' ELSE valor END as valor
                                FROM administracao.configuracao_entidade
                                WHERE exercicio    = '".$this->getDado('exercicio')."'
                                AND cod_entidade = ".$this->getDado('cod_entidade')."
                                AND cod_modulo   = 62
                                AND parametro    = 'tceal_configuracao_unidade_autonoma'
                        ) AS codigo_ua
                        , (SELECT PJ.cnpj
                                FROM orcamento.entidade
                                JOIN sw_cgm
                                    ON sw_cgm.numcgm = entidade.numcgm
                                JOIN sw_cgm_pessoa_juridica AS PJ
                                    ON sw_cgm.numcgm = PJ.numcgm
                                WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                AND entidade.cod_entidade   = ".$this->getDado('cod_entidade')."
                        ) AS cod_und_gestora
                        , LPAD(tceal.recupera_codigo_orgao('".$this->getDado('exercicio')."', ".$this->getDado('cod_entidade').", 'orgao')::VARCHAR, 2, '0') AS cod_orgao
                        , LPAD(tceal.recupera_codigo_orgao('".$this->getDado('exercicio')."', ".$this->getDado('cod_entidade').", 'unidade')::VARCHAR, 4, '0') AS cod_unid_orcamentaria
                        , RPAD(REPLACE(conta_receita.cod_estrutural,'.',''), 16, '0') as cod_conta_receita
                        , plano_banco.cod_banco
                        , plano_banco.cod_agencia
                        , plano_banco.conta_corrente as num_conta
                        , TO_CHAR(arrecadacao.timestamp_arrecadacao,'dd/mm/yyyy') as data_arrecadacao
                        , COALESCE(SUM(arrecadacao_receita.vl_arrecadacao),'0.00') - COALESCE(SUM(arrecadacao_estornada_receita.vl_estornado),'0.00') as valor_arrecadado_liquido
                        , recurso.cod_recurso as cod_rec_vinculado
                        , CASE WHEN receita_dedutora.cod_estrutural ilike '9.1.7%' THEN
                                    '621310100000000'
                                 ELSE
                                    '621200000000000'
                           END as cod_conta_contabil
                        , receita_dedutora.cod_estrutural as cod_estrutural
                        , ABS(COALESCE(SUM(receita_dedutora.vl_deducao),'0.00') - COALESCE(SUM(receita_dedutora.vl_estornado),'0.00')) as deducao_liquida
                        , RPAD(REPLACE(plano_conta.cod_estrutural,'.',''),17,'0') AS cod_conta_ativo
                        , CASE WHEN REPLACE(plano_conta.cod_estrutural, '.', '') ILIKE '1111101%'
                           THEN 1
                           ELSE 2
                          END AS forma_arrecadacao
                        , TO_CHAR( arrecadacao.timestamp_terminal, 'DD/MM/YYYY') AS data_registro

                FROM orcamento.receita

                JOIN orcamento.conta_receita
                     ON receita.exercicio = conta_receita.exercicio
                    AND receita.cod_conta = conta_receita.cod_conta

                JOIN tesouraria.arrecadacao_receita
                     ON receita.cod_receita = arrecadacao_receita.cod_receita
                    AND receita.exercicio   = arrecadacao_receita.exercicio

                LEFT JOIN tesouraria.arrecadacao_estornada_receita
                     ON arrecadacao_estornada_receita.cod_arrecadacao       = arrecadacao_receita.cod_arrecadacao
                    AND arrecadacao_estornada_receita.cod_receita           = arrecadacao_receita.cod_receita
                    AND arrecadacao_estornada_receita.exercicio             = arrecadacao_receita.exercicio
                    AND arrecadacao_estornada_receita.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao

                LEFT JOIN tesouraria.arrecadacao
                     ON arrecadacao_receita.cod_arrecadacao        = arrecadacao.cod_arrecadacao
                    AND arrecadacao_receita.exercicio              = arrecadacao.exercicio
                    AND arrecadacao_receita.timestamp_arrecadacao  = arrecadacao.timestamp_arrecadacao

                LEFT JOIN contabilidade.plano_analitica
                     ON arrecadacao.cod_plano = plano_analitica.cod_plano
                    AND arrecadacao.exercicio = plano_analitica.exercicio

            LEFT JOIN contabilidade.plano_conta
                   ON plano_analitica.cod_conta = plano_conta.cod_conta 
                  AND plano_analitica.exercicio = plano_conta.exercicio

             LEFT JOIN contabilidade.lancamento_receita
                    ON lancamento_receita.cod_receita = receita.cod_receita 
                   AND lancamento_receita.exercicio   = receita.exercicio

                LEFT JOIN contabilidade.plano_banco
                     ON plano_analitica.cod_plano = plano_banco.cod_plano
                    AND plano_analitica.exercicio = plano_banco.exercicio
                    AND plano_banco.cod_entidade =  receita.cod_entidade

                LEFT JOIN orcamento.recurso
                     ON recurso.exercicio   = receita.exercicio
                    AND recurso.cod_recurso = receita.cod_recurso

                LEFT JOIN (SELECT
                                      AR.cod_arrecadacao
                                    , dedutoras.cod_receita_dedutora
                                    , plano_banco.cod_banco
                                    , plano_banco.cod_agencia 
                                    , plano_banco.conta_corrente
                                    , pab.cod_plano
                                    , rec.cod_receita
                                    , rec.exercicio
                                    , crec.cod_conta
                                    , AR.timestamp_arrecadacao
                                    , dedutoras.vl_deducao
                                    , dedutoras.vl_estornado
                                    , crec.cod_estrutural
                                FROM(  SELECT
                                                ard.cod_receita_dedutora
                                                ,sum(ard.vl_deducao) as vl_deducao
                                                ,0.00 as vl_estornado
                                                ,ard.cod_arrecadacao
                                                ,ard.timestamp_arrecadacao
                                                ,ard.exercicio
                                                ,false as bo_devolucao
                                        FROM tesouraria.arrecadacao_receita_dedutora as ard
                                        GROUP BY ard.cod_receita_dedutora, ard.cod_arrecadacao, ard.timestamp_arrecadacao, ard.exercicio

                                        UNION ALL 

                                        SELECT   arrecadacao_receita.cod_receita as cod_receita_dedutora
                                                ,sum(vl_arrecadacao) as vl_deducao
                                                ,0.00 as vl_estornado
                                                ,arrecadacao_receita.cod_arrecadacao
                                                ,arrecadacao_receita.timestamp_arrecadacao
                                                ,arrecadacao_receita.exercicio
                                                ,true as bo_devolucao
                                        FROM tesouraria.arrecadacao_receita

                                        JOIN tesouraria.arrecadacao
                                          ON arrecadacao.cod_arrecadacao = arrecadacao_receita.cod_arrecadacao
                                         AND arrecadacao.exercicio = arrecadacao_receita.exercicio
                                         AND arrecadacao.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao

                                        JOIN tesouraria.arrecadacao_receita_dedutora as ard
                                          ON ard.cod_arrecadacao = arrecadacao_receita.cod_arrecadacao
                                         AND ard.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao
                                         AND ard.cod_receita = arrecadacao_receita.cod_receita
                                         AND ard.exercicio = arrecadacao_receita.exercicio
                                         
                                        WHERE ard.cod_receita is not null
                                          AND arrecadacao.devolucao = 't'

                                        GROUP BY arrecadacao_receita.cod_receita, arrecadacao_receita.cod_arrecadacao, arrecadacao_receita.timestamp_arrecadacao, arrecadacao_receita.exercicio

                                        UNION ALL

                                        SELECT arde.cod_receita_dedutora
                                               ,0.00 as vl_deducao
                                               ,sum(arde.vl_estornado) as vl_estornado
                                               ,arde.cod_arrecadacao
                                               ,arde.timestamp_arrecadacao
                                               ,arde.exercicio
                                               ,false as bo_devolucao
                                        FROM tesouraria.arrecadacao_receita_dedutora_estornada arde

                                        JOIN tesouraria.arrecadacao_receita_dedutora as ard
                                             ON ard.cod_arrecadacao       = arde.cod_arrecadacao
                                            AND ard.cod_receita           = arde.cod_receita
                                            AND ard.exercicio             = arde.exercicio
                                            AND ard.timestamp_arrecadacao = arde.timestamp_arrecadacao
                                            AND ard.cod_receita_dedutora  = arde.cod_receita_dedutora

                                        WHERE arde.cod_receita is not null                     

                                        GROUP BY arde.cod_receita_dedutora, arde.cod_arrecadacao, arde.timestamp_arrecadacao, arde.exercicio
                                    ) as dedutoras

                                JOIN tesouraria.arrecadacao_receita as AR
                                     ON ar.cod_arrecadacao       = dedutoras.cod_arrecadacao
                                    AND ar.timestamp_arrecadacao = dedutoras.timestamp_arrecadacao

                                JOIN tesouraria.arrecadacao as ta
                                     ON ta.cod_arrecadacao       = ar.cod_arrecadacao
                                    AND ta.timestamp_arrecadacao = ar.timestamp_arrecadacao
                                    AND ta.devolucao             = dedutoras.bo_devolucao

                                --- Conta de Caixa/Banco que arrecadou com dedução
                                JOIN contabilidade.plano_analitica as pab
                                     ON ta.cod_plano = pab.cod_plano
                                    AND ta.exercicio = pab.exercicio 

                                JOIN contabilidade.plano_banco
                                     ON plano_banco.exercicio   = pab.exercicio
                                    AND plano_banco.cod_plano   = pab.cod_plano

                                JOIN orcamento.receita as rec
                                     ON rec.cod_receita = dedutoras.cod_receita_dedutora
                                    AND rec.exercicio   = dedutoras.exercicio 

                                JOIN orcamento.conta_receita crec
                                     ON rec.cod_conta = crec.cod_conta
                                    AND rec.exercicio = crec.exercicio 

                        WHERE crec.exercicio = '".$this->getDado('exercicio')."'
                        AND ta.cod_entidade IN (".$this->getDado('cod_entidade').")
                )AS receita_dedutora
                     ON receita_dedutora.exercicio       = arrecadacao_receita.exercicio
                    AND receita_dedutora.cod_arrecadacao = arrecadacao_receita.cod_arrecadacao  

                WHERE receita.exercicio = '".$this->getDado('exercicio')."'
                  AND arrecadacao_receita.timestamp_arrecadacao BETWEEN TO_DATE('".$this->getDado('dtInicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."','dd/mm/yyyy')
                 AND receita.cod_entidade = ".$this->getDado('cod_entidade')."
                GROUP BY codigo_ua
                        ,cod_und_gestora
                        ,cod_orgao
                        ,cod_unid_orcamentaria
                        ,cod_conta_receita
                        ,plano_banco.cod_banco
                        ,plano_banco.cod_agencia
                        ,num_conta
                        ,data_arrecadacao
                        ,cod_rec_vinculado
                        ,cod_conta_contabil
                        ,receita_dedutora.cod_estrutural
                        , cod_conta_ativo
                        , forma_arrecadacao
                        , data_registro

                ORDER BY data_arrecadacao, cod_conta_receita
            ) as retorno
            WHERE valor_arrecadado_liquido > 0
            ";
        
        return $stSql;
    }
}
?>