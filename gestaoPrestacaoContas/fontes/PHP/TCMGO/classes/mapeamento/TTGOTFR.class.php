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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Jean Silva

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 46038 $
    $Name$
    $Author: jean $
    $Date: 2012-01-04 15:39:23 -0200 (Qua, 04 Jan 2012) $

    * Casos de uso: uc-06.04.00
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOTFR extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/

    public function recuperaFonteRecursoOrigem(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaFonteRecursoOrigem",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaFonteRecursoOrigem()
    {
    $inMes = explode('/',$this->getDado('dtInicio'));
        $inMes = $inMes[1];

    $stSql = "
        SELECT c.tipo_registro
               , c.num_orgao
               , '01' AS num_unidade
               , c.num_banco
               , c.num_agencia
               , c.num_conta_corrente
               , c.conta_digito
               , c.tipo_conta
               , c.fonte_origem
               , SUM(c.vl_decrescido) AS vl_decrescido

        FROM (
                SELECT '10'::int                                                            AS tipo_registro
                    , orgao_plano_banco.num_orgao                                           AS num_orgao
                    , CASE WHEN (substr(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
               THEN '999'
               ELSE num_banco
                END 							    AS num_banco
                    , CASE WHEN (substr(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
               THEN '999999'
               ELSE ltrim(replace(num_agencia,'-',''),'0')
                END  							    AS num_agencia
                    , CASE WHEN (substr(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
               THEN '999999999999'
               ELSE ltrim(split_part(conta_corrente,'-',1),'0')
                END   							    AS num_conta_corrente
                    , ltrim(split_part(conta_corrente,'-',2),'0') 			    AS conta_digito
                    , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.1')
                           THEN '03'
               WHEN ((substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.3')
                 OR (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.5')
                 OR (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.4'))
                           THEN '02'
               ELSE '01'
                END                                                             AS tipo_conta
                    , recurso_origem.cod_fonte                                              AS fonte_origem
            , ( SELECT SUM(
                    ( SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) AS vl_total
                    FROM contabilidade.conta_credito
                  INNER JOIN contabilidade.valor_lancamento
                      ON valor_lancamento.exercicio = conta_credito.exercicio
                     AND valor_lancamento.cod_entidade = conta_credito.cod_entidade
                     AND valor_lancamento.tipo = conta_credito.tipo
                     AND valor_lancamento.cod_lote = conta_credito.cod_lote
                     AND valor_lancamento.sequencia = conta_credito.sequencia
                     AND valor_lancamento.tipo_valor = conta_credito.tipo_valor
                  INNER JOIN  contabilidade.lancamento
                                          ON  lancamento.sequencia = valor_lancamento.sequencia
                                         AND  lancamento.cod_lote = valor_lancamento.cod_lote
                                         AND  lancamento.tipo = valor_lancamento.tipo
                                         AND  lancamento.exercicio = valor_lancamento.exercicio
                                         AND  lancamento.cod_entidade = valor_lancamento.cod_entidade
                                  INNER JOIN  contabilidade.lote
                                          ON  lote.cod_lote = lancamento.cod_lote
                                         AND  lote.exercicio = lancamento.exercicio
                                         AND  lote.tipo = lancamento.tipo
                                         AND  lote.cod_entidade = lancamento.cod_entidade
                                         AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                         AND  lote.exercicio = '".$this->getDado('exercicio')."'
                     AND  lote.tipo != 'I'
                                       WHERE  conta_credito.exercicio = pa.exercicio
                                         AND  conta_credito.cod_plano = pa.cod_plano
                    )
                  ) 							    AS vl_total
                FROM contabilidade.plano_analitica AS pa
               WHERE pa.cod_plano = plano_analitica.cod_plano
                 AND pa.exercicio = plano_analitica.exercicio
            ) * -1 AS vl_decrescido
                    , ''                                                                    AS brancos
                    , '0'                                                                   AS numero_sequencial

                    FROM tcmgo.orgao_plano_banco
                    JOIN contabilidade.plano_banco
                      ON plano_banco.exercicio = orgao_plano_banco.exercicio
                     AND plano_banco.cod_plano = orgao_plano_banco.cod_plano
                    JOIN monetario.agencia
                      ON agencia.cod_banco = plano_banco.cod_banco
                     AND agencia.cod_agencia = plano_banco.cod_agencia
                    JOIN monetario.banco
                      ON banco.cod_banco = plano_banco.cod_banco
                    JOIN contabilidade.plano_analitica
                      ON plano_analitica.cod_plano = plano_banco.cod_plano
                     AND plano_analitica.exercicio = plano_banco.exercicio
            JOIN contabilidade.plano_conta
                      ON plano_conta.cod_conta = plano_analitica.cod_conta
                     AND plano_conta.exercicio = plano_analitica.exercicio

                    JOIN contabilidade.conta_credito
                      ON conta_credito.exercicio = plano_analitica.exercicio
                     AND conta_credito.cod_plano = plano_analitica.cod_plano

                    JOIN contabilidade.plano_recurso AS plano_recurso_origem
                      ON plano_recurso_origem.exercicio = conta_credito.exercicio
                     AND plano_recurso_origem.cod_plano = conta_credito.cod_plano
                    JOIN orcamento.recurso AS recurso_origem
                      ON recurso_origem.exercicio = plano_recurso_origem.exercicio
                     AND recurso_origem.cod_recurso = plano_recurso_origem.cod_recurso

                    JOIN contabilidade.valor_lancamento AS decrescido
                      ON decrescido.exercicio = conta_credito.exercicio
                     AND decrescido.cod_entidade = conta_credito.cod_entidade
                     AND decrescido.tipo = conta_credito.tipo
                     AND decrescido.cod_lote = conta_credito.cod_lote
                     AND decrescido.sequencia = conta_credito.sequencia
                     AND decrescido.tipo_valor = conta_credito.tipo_valor

           WHERE  plano_banco.exercicio = '".$this->getDado('exercicio')."'
                     AND  plano_banco.cod_entidade IN (".$this->getDado('cod_entidade').")
        GROUP BY  num_orgao
            , num_conta_corrente
            , banco.num_banco
            , agencia.num_agencia
            , conta_digito
            , plano_analitica.cod_plano
            , plano_analitica.exercicio
            , plano_conta.cod_estrutural
            , recurso_origem.cod_fonte
            ) AS c

        GROUP BY    c.tipo_registro
              , c.num_orgao
              , c.num_conta_corrente
              , c.num_banco
              , c.num_agencia
              , c.conta_digito
              , c.tipo_conta
              , c.fonte_origem
        ";

        return $stSql;
    }

    public function recuperaFonteRecursoDestino(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaFonteRecursoDestino",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaFonteRecursoDestino()
    {
    $inMes = explode('/',$this->getDado('dtInicio'));
        $inMes = $inMes[2];

    $stSql .="
        SELECT c.tipo_registro
               , c.num_orgao
               , '01' AS num_unidade
               , c.num_banco
               , c.num_agencia
               , c.num_conta_corrente
               , c.conta_digito
               , c.tipo_conta
               , c.fonte_origem
               , c.fonte_destino
               , SUM(c.vl_acrescido) AS vl_acrescido

        FROM (
                SELECT
                    '11'::int                                                               AS tipo_registro
                    , orgao_plano_banco.num_orgao                                           AS num_orgao
                    , CASE WHEN (substr(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
               THEN '999'
               ELSE num_banco
                END 							    AS num_banco
                    , CASE WHEN (substr(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
               THEN '999999'
               ELSE ltrim(replace(num_agencia,'-',''),'0')
                END  							    AS num_agencia
                    , CASE WHEN (substr(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
               THEN '999999999999'
                   ELSE ltrim(split_part(conta_corrente,'-',1),'0')
                END   							    AS num_conta_corrente
                    , ltrim(split_part(conta_corrente,'-',2),'0')                           AS conta_digito
                    , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.1')
                           THEN '03'
               WHEN ((substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.3')
                 OR (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.5')
                 OR (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.4'))
                           THEN '02'
               ELSE '01'
                END                                                             AS tipo_conta
                    , recurso_origem.cod_fonte                                              AS fonte_origem
                    , recurso_destino.cod_fonte                                             AS fonte_destino

            , ( SELECT  SUM(
                    ( SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) AS vl_total
                    FROM  contabilidade.conta_debito
                      INNER JOIN  contabilidade.valor_lancamento
                      ON  valor_lancamento.cod_lote = conta_debito.cod_lote
                             AND  valor_lancamento.tipo = conta_debito.tipo
                             AND  valor_lancamento.sequencia = conta_debito.sequencia
                             AND  valor_lancamento.exercicio = conta_debito.exercicio
                             AND  valor_lancamento.tipo_valor = conta_debito.tipo_valor
                             AND  valor_lancamento.cod_entidade = conta_debito.cod_entidade
                      INNER JOIN  contabilidade.lancamento
                              ON  lancamento.sequencia = valor_lancamento.sequencia
                             AND  lancamento.cod_lote = valor_lancamento.cod_lote
                             AND  lancamento.tipo = valor_lancamento.tipo
                             AND  lancamento.exercicio = valor_lancamento.exercicio
                             AND  lancamento.cod_entidade = valor_lancamento.cod_entidade
                      INNER JOIN  contabilidade.lote
                              ON  lote.cod_lote = lancamento.cod_lote
                             AND  lote.exercicio = lancamento.exercicio
                             AND  lote.tipo = lancamento.tipo
                             AND  lote.cod_entidade = lancamento.cod_entidade
                             AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                             AND  lote.exercicio = '".$this->getDado('exercicio')."'
                             AND  lote.tipo != 'I'
                           WHERE  conta_debito.exercicio = pa.exercicio
                             AND  conta_debito.cod_plano = pa.cod_plano
                        )
                   )							    AS vl_total
                FROM  contabilidade.plano_analitica AS pa
               WHERE  pa.cod_plano = plano_analitica.cod_plano
                 AND  pa.exercicio = plano_analitica.exercicio
              ) AS vl_acrescido
                    , '0'                                                                   AS numero_sequencial
                    FROM tcmgo.orgao_plano_banco
                    JOIN contabilidade.plano_banco
                      ON plano_banco.exercicio = orgao_plano_banco.exercicio
                     AND plano_banco.cod_plano = orgao_plano_banco.cod_plano
                    JOIN monetario.agencia
                      ON agencia.cod_banco = plano_banco.cod_banco
                     AND agencia.cod_agencia = plano_banco.cod_agencia
                    JOIN monetario.banco
                      ON banco.cod_banco = plano_banco.cod_banco
                    JOIN contabilidade.plano_analitica
                      ON plano_analitica.cod_plano = plano_banco.cod_plano
                     AND plano_analitica.exercicio = plano_banco.exercicio
            JOIN contabilidade.plano_conta
                      ON plano_conta.cod_conta = plano_analitica.cod_conta
                     AND plano_conta.exercicio = plano_analitica.exercicio

                    JOIN contabilidade.conta_credito
                      ON conta_credito.exercicio = plano_analitica.exercicio
                     AND conta_credito.cod_plano = plano_analitica.cod_plano
            JOIN contabilidade.plano_recurso AS plano_recurso_origem
                      ON plano_recurso_origem.exercicio = plano_analitica.exercicio
                     AND plano_recurso_origem.cod_plano = plano_analitica.cod_plano
                    JOIN orcamento.recurso AS recurso_origem
                      ON recurso_origem.exercicio = plano_recurso_origem.exercicio
                     AND recurso_origem.cod_recurso = plano_recurso_origem.cod_recurso

                    JOIN contabilidade.conta_debito
                      ON conta_debito.exercicio = plano_analitica.exercicio
                     AND conta_debito.cod_plano = plano_analitica.cod_plano
             JOIN contabilidade.plano_recurso AS plano_recurso_destino
                      ON plano_recurso_destino.exercicio = plano_analitica.exercicio
                     AND plano_recurso_destino.cod_plano = plano_analitica.cod_plano
                    JOIN orcamento.recurso AS recurso_destino
                      ON recurso_destino.exercicio = plano_recurso_destino.exercicio
                     AND recurso_destino.cod_recurso = plano_recurso_destino.cod_recurso

                    JOIN contabilidade.valor_lancamento AS acrescido
                      ON acrescido.exercicio = conta_debito.exercicio
                     AND acrescido.cod_entidade = conta_debito.cod_entidade
                     AND acrescido.tipo = conta_debito.tipo
                     AND acrescido.cod_lote = conta_debito.cod_lote
                     AND acrescido.sequencia = conta_debito.sequencia
                     AND acrescido.tipo_valor = conta_debito.tipo_valor
           WHERE plano_banco.exercicio = '".$this->getDado('exercicio')."'
             AND plano_banco.cod_entidade IN (".$this->getDado('cod_entidade').")

        GROUP BY num_orgao
            , num_conta_corrente
            , banco.num_banco
            , agencia.num_agencia
            , conta_digito
            , plano_analitica.cod_plano
            , plano_analitica.exercicio
            , plano_conta.cod_estrutural
            , recurso_origem.cod_fonte
            , recurso_destino.cod_fonte
            ) AS c

        GROUP BY c.tipo_registro
             , c. num_orgao
             , c. num_conta_corrente
             , c. num_banco
             , c. num_agencia
             , c. conta_digito
             , c. fonte_origem
             , c. fonte_destino
             , c. tipo_conta
        ";

        return $stSql;
    }
}
