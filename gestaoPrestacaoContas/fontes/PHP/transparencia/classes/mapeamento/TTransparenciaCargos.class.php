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
 * Data de Criação: 16/11/2012

 * @author Analista: Gelson
 * @author Desenvolvedor: Carolina

 * @package URBEM
 * @subpackage Mapeamento

 $Id: TTransparenciaCargos.class.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTransparenciaCargos extends Persistente
{
    public function recuperaUltimoTimesTampPeriodoMovimentacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaUltimoTimesTampPeriodoMovimentacao().$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaUltimoTimesTampPeriodoMovimentacao()
    {
        $stSql = " select ultimotimestampperiodomovimentacao(".$this->getDado('inCodPeriodoMovimentacao') .",'".$this->getDado('stEntidade') ."') ";

        return $stSql;
    }

     function recuperaCargos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
     {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaCargos().$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

   public function montaRecuperaCargos()
   {
        $stSql = "
            SELECT (select cod_entidade from orcamento.entidade where cod_entidade = ".$this->getDado('codEntidade') ." and exercicio = '".$this->getDado('exercicio') ."' ) as numero_entidade
                        , to_char((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCodPeriodoMovimentacao') .")::date), 'mm/yyyy') as mes_ano
                        , cargo.cod_cargo as codigo
                        , trim(cargo.descricao) as descricao_cargo
                        , (CASE WHEN cargo.cargo_cc  THEN 'COMISSIONADO'
                                    WHEN cargo.funcao_gratificada THEN 'FUNÇÃO GRATIFICADA'
                                      ELSE 'QUADRO GERAL'
                            END) as tipo_cargo
                         , (norma.num_norma|| '/' || norma.exercicio) as lei
                         , trim(padrao.descricao) as descricao_padrao
                         , padrao.horas_mensais as cargahoraria_mensal
                         , padrao.horas_semanais as cargahoraria_semanal
                         , padrao_padrao.valor
                         , to_char(padrao_padrao.vigencia, 'ddmmyyyy') as vigencia
                         , sub_divisao.descricao as regime_subdivisao
                         , COALESCE(vagas_cadastradas.nro_vaga_criada,0) as vagas_criadas
                         , COALESCE(contador.contador, 0) + COALESCE(contador_principal.contador,0) as vagas_ocupadas
                         , (COALESCE(vagas_cadastradas.nro_vaga_criada,0) - COALESCE(contador.contador,0) + COALESCE(contador_principal.contador,0) ) as vagas_disponiveis

              FROM pessoal".$this->getDado('stEntidade') .".cargo

         INNER JOIN (
                         SELECT cargo_padrao.cod_padrao
                                   , cargo_padrao.cod_cargo
                            FROM pessoal".$this->getDado('stEntidade') .".cargo_padrao
                                   , (  SELECT cod_cargo
                                                  , max(timestamp) as timestamp
                                           FROM pessoal".$this->getDado('stEntidade') .".cargo_padrao
                                    GROUP BY cod_cargo
                                     ) as max_cargo_padrao
                           WHERE max_cargo_padrao.cod_cargo = cargo_padrao.cod_cargo
                               AND max_cargo_padrao.timestamp = cargo_padrao.timestamp
                        ) AS cargo_padrao
                 ON cargo_padrao.cod_cargo = cargo.cod_cargo

        INNER JOIN folhapagamento".$this->getDado('stEntidade') .".padrao
                ON cargo_padrao.cod_padrao = padrao.cod_padrao

        INNER JOIN (SELECT padrao_padrao.cod_padrao
                         , padrao_padrao.valor
                         , padrao_padrao.vigencia
                      FROM folhapagamento".$this->getDado('stEntidade') .".padrao_padrao
                         , (  SELECT cod_padrao
                                   , max(timestamp) as timestamp
                                FROM folhapagamento".$this->getDado('stEntidade') .".padrao_padrao
                               WHERE to_char(vigencia, 'yyyy-mm-dd') <= '".$this->getDado('dtTimesTamp') ."'
                            GROUP BY cod_padrao) as max_padrao_padrao
                     WHERE max_padrao_padrao.cod_padrao = padrao_padrao.cod_padrao
                       AND max_padrao_padrao.timestamp = padrao_padrao.timestamp
                   ) as padrao_padrao
                ON padrao_padrao.cod_padrao = padrao.cod_padrao

                        INNER JOIN pessoal".$this->getDado('stEntidade') .".cargo_sub_divisao
                                ON cargo_sub_divisao.cod_cargo = cargo.cod_cargo
                               AND cargo_sub_divisao.nro_vaga_criada > 0

                        INNER JOIN (SELECT cod_cargo
                                         , cod_sub_divisao
                                         , max(timestamp) as timestamp
                                      FROM pessoal".$this->getDado('stEntidade') .".cargo_sub_divisao
                                  GROUP BY cod_cargo
                                         , cod_sub_divisao
                                 ) as max_cargo_sub_divisao
                                ON max_cargo_sub_divisao.cod_cargo = cargo_sub_divisao.cod_cargo
                               AND max_cargo_sub_divisao.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao
                               AND max_cargo_sub_divisao.timestamp = cargo_sub_divisao.timestamp

                        INNER JOIN pessoal".$this->getDado('stEntidade') .".sub_divisao
                                ON sub_divisao.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao

--inicia
--primeira funcao parte 1
                         LEFT JOIN (
                                        SELECT cargo_sub_divisao.nro_vaga_criada
                                                , cargo_sub_divisao.cod_cargo
                                             , cargo_sub_divisao.cod_sub_divisao
                                              , sub_divisao.cod_regime
                                          FROM pessoal".$this->getDado('stEntidade') .".cargo_sub_divisao
                                    INNER JOIN (
                                                        SELECT cargo_sub_divisao.cod_cargo
                                                               , cargo_sub_divisao.cod_sub_divisao
                                                               , max(timestamp) as timestamp
                                                             FROM pessoal".$this->getDado('stEntidade') .".cargo_sub_divisao
                                                         WHERE timestamp <=  '".$this->getDado('dtTimesTamp') ."'
                                                        GROUP BY cod_cargo, cod_sub_divisao
                                               ) as max_cargo_sub_divisao
                                            ON cargo_sub_divisao.cod_cargo= max_cargo_sub_divisao.cod_cargo
                                           AND cargo_sub_divisao.cod_sub_divisao = max_cargo_sub_divisao.cod_sub_divisao
                                           AND cargo_sub_divisao.timestamp= max_cargo_sub_divisao.timestamp
                                    INNER JOIN pessoal".$this->getDado('stEntidade') .".sub_divisao
                                            ON cargo_sub_divisao.cod_sub_divisao = sub_divisao.cod_sub_divisao
                                    INNER JOIN pessoal".$this->getDado('stEntidade') .".regime
                                            ON sub_divisao.cod_regime= regime.cod_regime
                                   ) as vagas_cadastradas
                                ON vagas_cadastradas.cod_cargo = cargo.cod_cargo
                               AND vagas_cadastradas.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao
                               AND vagas_cadastradas.cod_regime = sub_divisao.cod_regime
--segunda funcao parte 1
                         LEFT JOIN (
                                        SELECT count(1) as contador
                                              , contrato_servidor.cod_cargo
                                              , contrato_servidor.cod_sub_divisao
                                              , contrato_servidor.cod_regime
                                            FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor

                                    INNER JOIN (
                                                 SELECT contrato.cod_contrato
                                                      , CASE
                                                           WHEN pensionista.total = 1     AND aposentado.total IS NULL AND rescindido.total IS NULL THEN
                                                               'E'
                                                           WHEN pensionista.total IS NULL AND aposentado.total > 0     AND rescindido.total IS NULL THEN
                                                               'P'
                                                           WHEN pensionista.total IS NULL AND aposentado.total IS NULL AND rescindido.total > 0     THEN
                                                               'R'
                                                           WHEN pensionista.total IS NULL AND aposentado.total IS NULL AND rescindido.total IS NULL THEN
                                                               'A'
                                                       END AS status
                                                  FROM pessoal".$this->getDado('stEntidade') .".contrato
                                             LEFT JOIN (
                                                           SELECT contrato_pensionista.cod_contrato
                                                                , count(*) as total
                                                             FROM pessoal".$this->getDado('stEntidade') .".contrato_pensionista
                                                         GROUP BY contrato_pensionista.cod_contrato
                                                        ) AS pensionista
                                                    ON pensionista.cod_contrato = contrato.cod_contrato
                                            LEFT JOIN (
                                                           SELECT interna.cod_contrato
                                                                , count(*) as total
                                                             FROM (
                                                                       SELECT aposentadoria.cod_contrato
                                                                            , max(aposentadoria.timestamp)
                                                                         FROM pessoal".$this->getDado('stEntidade') .".aposentadoria
                                                                    LEFT JOIN pessoal".$this->getDado('stEntidade') .".aposentadoria_excluida
                                                                           ON aposentadoria_excluida.cod_contrato = aposentadoria.cod_contrato
                                                                          AND aposentadoria_excluida.timestamp    = aposentadoria.timestamp
                                                                        WHERE aposentadoria.dt_concessao <= (
                                                                                                             SELECT periodo_movimentacao.dt_final
                                                                                                               FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                                              WHERE cod_periodo_movimentacao = (
                                                                                                                                                 SELECT MAX(cod_periodo_movimentacao)
                                                                                                                                                   FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                                                                                )
                                                                                                              )
                                                                          AND aposentadoria_excluida.cod_contrato IS NULL
                                                                     GROUP BY aposentadoria.cod_contrato
                                                                  ) AS interna
                                                           GROUP BY interna.cod_contrato
                                                        ) AS aposentado
                                                     ON aposentado.cod_contrato = contrato.cod_contrato
                                              LEFT JOIN (
                                                           SELECT contrato_servidor_caso_causa.cod_contrato
                                                                , count(*) as total
                                                             FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor_caso_causa
                                                            WHERE dt_rescisao <= (
                                                                                   SELECT periodo_movimentacao.dt_final
                                                                                     FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                    WHERE cod_periodo_movimentacao = (
                                                                                                                        SELECT MAX(cod_periodo_movimentacao)
                                                                                                                          FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                                                      )
                                                                                   )
                                                         GROUP BY contrato_servidor_caso_causa.cod_contrato
                                                        ) AS rescindido
                                                     ON rescindido.cod_contrato = contrato.cod_contrato
                                       ) AS situacao_contrato
                                    ON situacao_contrato.cod_contrato = contrato_servidor.cod_contrato
                                 WHERE situacao_contrato.status = 'A'
                              GROUP BY contrato_servidor.cod_cargo
                                      , contrato_servidor.cod_sub_divisao
                                      , contrato_servidor.cod_regime
                                ) as contador
                            ON contador.cod_cargo = cargo.cod_cargo
                           AND contador.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao
                           AND contador.cod_regime = sub_divisao.cod_regime
--  segunda funcao parte 2
                     LEFT JOIN (
                                            SELECT count(1) as contador
                                                    , contrato_servidor_funcao.cod_cargo
                                                   , contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                                 , contrato_servidor_regime_funcao.cod_regime
                                                 FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor
                                        INNER JOIN pessoal".$this->getDado('stEntidade') .".contrato_servidor_funcao
                                                ON contrato_servidor.cod_contrato = contrato_servidor_funcao.cod_contrato
                                        INNER JOIN (  SELECT cod_contrato
                                                           , max(timestamp) as timestamp
                                                        FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor_funcao
                                                       WHERE timestamp <= '".$this->getDado('dtTimesTamp') ."'
                                                    GROUP BY cod_contrato
                                                    ) as max_contrato_servidor_funcao
                                                ON contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato
                                               AND contrato_servidor_funcao.timestamp    = max_contrato_servidor_funcao.timestamp
                                        INNER JOIN pessoal".$this->getDado('stEntidade') .".contrato_servidor_sub_divisao_funcao
                                                ON contrato_servidor.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                                        INNER JOIN (  SELECT cod_contrato
                                                           , max(timestamp) as timestamp
                                                        FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor_sub_divisao_funcao
                                                       WHERE timestamp <=  '".$this->getDado('dtTimesTamp') ."'
                                                    GROUP BY cod_contrato
                                                    ) as max_contrato_servidor_sub_divisao_funcao
                                                  ON contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                                               AND contrato_servidor_sub_divisao_funcao.timestamp    = max_contrato_servidor_sub_divisao_funcao.timestamp
                                        INNER JOIN pessoal".$this->getDado('stEntidade') .".contrato_servidor_regime_funcao
                                                ON contrato_servidor.cod_contrato = contrato_servidor_regime_funcao.cod_contrato
                                        INNER JOIN (
                                                             SELECT cod_contrato
                                                                 , max(timestamp) as timestamp
                                                              FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor_regime_funcao
                                                             WHERE timestamp <=  '".$this->getDado('dtTimesTamp') ."'
                                                          GROUP BY cod_contrato
                                                  ) as max_contrato_servidor_regime_funcao
                                                    ON contrato_servidor_regime_funcao.cod_contrato         = max_contrato_servidor_regime_funcao.cod_contrato
                                                AND contrato_servidor_regime_funcao.timestamp            = max_contrato_servidor_regime_funcao.timestamp
                                         INNER JOIN (
                                                             SELECT contrato.cod_contrato
                                                                  , CASE
                                                                       WHEN pensionista.total = 1     AND aposentado.total IS NULL AND rescindido.total IS NULL THEN
                                                                    'E'
                                                                       WHEN pensionista.total IS NULL AND aposentado.total > 0     AND rescindido.total IS NULL THEN
                                                                    'P'
                                                                    WHEN pensionista.total IS NULL AND aposentado.total IS NULL AND rescindido.total > 0     THEN
                                                                    'R'
                                                                    WHEN pensionista.total IS NULL AND aposentado.total IS NULL AND rescindido.total IS NULL THEN
                                                                    'A'
                                                                    END AS status
                                                                  FROM pessoal".$this->getDado('stEntidade') .".contrato
                                                            LEFT JOIN (
                                                                               SELECT contrato_pensionista.cod_contrato
                                                                                 , count(*) as total
                                                                               FROM pessoal".$this->getDado('stEntidade') .".contrato_pensionista
                                                                           GROUP BY contrato_pensionista.cod_contrato
                                                                       ) AS pensionista
                                                                  ON pensionista.cod_contrato = contrato.cod_contrato
                                                           LEFT JOIN (
                                                                               SELECT interna.cod_contrato
                                                                                 , count(*) as total
                                                                                 FROM (
                                                                                               SELECT aposentadoria.cod_contrato
                                                                                                  , max(aposentadoria.timestamp)
                                                                                                FROM pessoal".$this->getDado('stEntidade') .".aposentadoria
                                                                                            LEFT JOIN pessoal".$this->getDado('stEntidade') .".aposentadoria_excluida
                                                                                                   ON aposentadoria_excluida.cod_contrato = aposentadoria.cod_contrato
                                                                                                 AND aposentadoria_excluida.timestamp    = aposentadoria.timestamp
                                                                                               WHERE aposentadoria.dt_concessao <= (
                                                                                                                                      SELECT periodo_movimentacao.dt_final
                                                                                                                                      FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                                                                       WHERE cod_periodo_movimentacao = (
                                                                                                                                                                          SELECT MAX(cod_periodo_movimentacao)
                                                                                                                                                                             FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                                                                                                       )
                                                                                                                                  )
                                                                                                AND aposentadoria_excluida.cod_contrato IS NULL
                                                                                         GROUP BY aposentadoria.cod_contrato
                                                                                     ) AS interna
                                                                               GROUP BY interna.cod_contrato
                                                                       ) AS aposentado
                                                                   ON aposentado.cod_contrato = contrato.cod_contrato
                                                               LEFT JOIN (
                                                                            SELECT contrato_servidor_caso_causa.cod_contrato
                                                                                  , count(*) as total
                                                                                FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor_caso_causa
                                                                             WHERE dt_rescisao <= (
                                                                                                           SELECT periodo_movimentacao.dt_final
                                                                                                           FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                                         WHERE cod_periodo_movimentacao = (
                                                                                                                                                   SELECT MAX(cod_periodo_movimentacao)
                                                                                                                                                   FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                                                                            )
                                                                                                  )
                                                                            GROUP BY contrato_servidor_caso_causa.cod_contrato
                                                                       ) AS rescindido
                                                                    ON rescindido.cod_contrato = contrato.cod_contrato
                                                 ) AS situacao_contrato
                                             ON situacao_contrato.cod_contrato = contrato_servidor.cod_contrato
                                             WHERE (
                                                     contrato_servidor.cod_cargo       != contrato_servidor_funcao.cod_cargo
                                                  OR contrato_servidor.cod_sub_divisao != contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                                  OR contrato_servidor.cod_regime      != contrato_servidor_regime_funcao.cod_regime
                                                 )
                                            AND situacao_contrato.status = 'A'
                                          GROUP BY contrato_servidor_funcao.cod_cargo
                                                , contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                              , contrato_servidor_regime_funcao.cod_regime
                                       ) as contador_principal
                                      ON contador_principal.cod_cargo = cargo.cod_cargo
                                   AND contador_principal.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao
                                     AND contador_principal.cod_regime = sub_divisao.cod_regime


                        INNER JOIN normas.norma
                                ON norma.cod_norma = cargo_sub_divisao.cod_norma

                            UNION

                            SELECT (select cod_entidade from orcamento.entidade where  cod_entidade = ".$this->getDado('codEntidade') ." and exercicio = '".$this->getDado('exercicio') ."' ) as numero_entidade
                                      , to_char((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCodPeriodoMovimentacao') .")::date), 'mm/yyyy') as mes_ano
                                      , especialidade.cod_cargo as codigo
                                      , (SELECT trim(descricao) FROM pessoal".$this->getDado('stEntidade') .".cargo WHERE cod_cargo = especialidade.cod_cargo)||' / '||especialidade.descricao as descricao_cargo
                                      , (CASE WHEN (SELECT cargo_cc FROM pessoal".$this->getDado('stEntidade') .".cargo WHERE cod_cargo = especialidade.cod_cargo)  THEN 'COMISSIONADO'
                                         WHEN (SELECT funcao_gratificada FROM pessoal".$this->getDado('stEntidade') .".cargo WHERE cod_cargo = especialidade.cod_cargo) THEN 'FUNÇÃO GRATIFICADA'
                                         ELSE 'QUADRO GERAL'
                                         END
                                       ) as tipo_cargo
                                      ,( norma.num_norma|| '/' || norma.exercicio) as lei
                                      , trim(padrao.descricao) as descricao_padrao
                                      , padrao.horas_mensais as cargahoraria_mensal
                                      , padrao.horas_semanais as cargahoraria_semanal
                                      , padrao_padrao.valor
                                      , to_char(padrao_padrao.vigencia, 'ddmmyyyy') as vigencia
                                      , sub_divisao.descricao as regime_subdivisao
                                      , COALESCE(vagas_cadastradas.nro_vaga_criada, 0) as vagas_criadas
                                      , COALESCE(contador_principal_especialidade.contador, 0) + COALESCE(contador_principal_especialidade.contador, 0) as vagas_ocupadas
                                      , (COALESCE(vagas_cadastradas.nro_vaga_criada, 0)) - (COALESCE(contador_especialidade.contador, 0) + COALESCE(contador_principal_especialidade.contador,0) ) as vagas_disponiveis

                              FROM pessoal".$this->getDado('stEntidade') .".especialidade

                        INNER JOIN (SELECT especialidade_padrao.cod_padrao
                                         , especialidade_padrao.cod_especialidade
                                      FROM pessoal".$this->getDado('stEntidade') .".especialidade_padrao
                                         , (  SELECT cod_especialidade
                                                   , max(timestamp) as timestamp
                                                FROM pessoal".$this->getDado('stEntidade') .".especialidade_padrao
                                            GROUP BY cod_especialidade) as max_especialidade_padrao
                                     WHERE max_especialidade_padrao.cod_especialidade = especialidade_padrao.cod_especialidade
                                       AND max_especialidade_padrao.timestamp = especialidade_padrao.timestamp
                                   ) AS especialidade_padrao
                                ON especialidade_padrao.cod_especialidade = especialidade.cod_especialidade
                        INNER JOIN folhapagamento".$this->getDado('stEntidade') .".padrao
                                ON especialidade_padrao.cod_padrao = padrao.cod_padrao

                        INNER JOIN (SELECT padrao_padrao.cod_padrao
                                         , padrao_padrao.valor
                                         , padrao_padrao.vigencia
                                      FROM folhapagamento".$this->getDado('stEntidade') .".padrao_padrao
                                         , (  SELECT cod_padrao
                                                   , max(timestamp) as timestamp
                                                FROM folhapagamento".$this->getDado('stEntidade') .".padrao_padrao
                                               WHERE to_char(vigencia, 'yyyy-mm-dd') <= '".$this->getDado('dtTimesTamp') ."'
                                            GROUP BY cod_padrao) as max_padrao_padrao
                                     WHERE max_padrao_padrao.cod_padrao = padrao_padrao.cod_padrao
                                       AND max_padrao_padrao.timestamp = padrao_padrao.timestamp
                                   ) as padrao_padrao
                                ON padrao_padrao.cod_padrao = padrao.cod_padrao

                        INNER JOIN pessoal".$this->getDado('stEntidade') .".cargo_sub_divisao
                                ON cargo_sub_divisao.cod_cargo = especialidade.cod_cargo
                               AND cargo_sub_divisao.nro_vaga_criada > 0

                        INNER JOIN (SELECT cod_cargo
                                         , cod_sub_divisao
                                         , max(timestamp) as timestamp
                                      FROM pessoal".$this->getDado('stEntidade') .".cargo_sub_divisao
                                  GROUP BY cod_cargo
                                         , cod_sub_divisao
                                 ) as max_cargo_sub_divisao
                                ON max_cargo_sub_divisao.cod_cargo = cargo_sub_divisao.cod_cargo
                               AND max_cargo_sub_divisao.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao
                               AND max_cargo_sub_divisao.timestamp = cargo_sub_divisao.timestamp

                        INNER JOIN pessoal".$this->getDado('stEntidade') .".sub_divisao
                                ON sub_divisao.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao


                        INNER JOIN normas.norma
                                ON norma.cod_norma = cargo_sub_divisao.cod_norma

-- inicia funcao
                        LEFT JOIN (
                                           SELECT nro_vaga_criada
                                                , especialidade_sub_divisao.cod_especialidade
                                             , especialidade_sub_divisao.cod_sub_divisao
                                             , regime.cod_regime
                                             FROM pessoal".$this->getDado('stEntidade') .".especialidade_sub_divisao
                                    INNER JOIN (
                                                     SELECT cod_especialidade
                                                          , cod_sub_divisao
                                                          , max(timestamp) as timestamp
                                                      FROM pessoal".$this->getDado('stEntidade') .".especialidade_sub_divisao
                                                     WHERE timestamp <=  '".$this->getDado('dtTimesTamp') ."'
                                                  GROUP BY cod_especialidade
                                                         , cod_sub_divisao
                                                   ) as max_especialidade_sub_divisao
                                             ON especialidade_sub_divisao.cod_especialidade = max_especialidade_sub_divisao.cod_especialidade
                                            AND especialidade_sub_divisao.cod_sub_divisao   = max_especialidade_sub_divisao.cod_sub_divisao
                                            AND especialidade_sub_divisao.timestamp         = max_especialidade_sub_divisao.timestamp
                                     INNER JOIN pessoal".$this->getDado('stEntidade') .".sub_divisao
                                             ON especialidade_sub_divisao.cod_sub_divisao   = sub_divisao.cod_sub_divisao
                                     INNER JOIN pessoal".$this->getDado('stEntidade') .".regime
                                             ON sub_divisao.cod_regime                      = regime.cod_regime
                                  ) as vagas_cadastradas
                               ON vagas_cadastradas.cod_especialidade =  especialidade.cod_especialidade
                              AND vagas_cadastradas.cod_sub_divisao = sub_divisao.cod_sub_divisao
                              AND vagas_cadastradas.cod_regime = sub_divisao.cod_regime

-- funcao 1
                        LEFT JOIN (
                                        SELECT count(1) as contador
                                             , contrato_servidor_especialidade_cargo.cod_especialidade
                                             , contrato_servidor.cod_sub_divisao
                                             , contrato_servidor.cod_regime
                                          FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor_especialidade_cargo
                                    INNER JOIN pessoal".$this->getDado('stEntidade') .".contrato_servidor
                                            ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato
                                    INNER JOIN (
                                                        SELECT contrato.cod_contrato
                                                             , CASE
                                                                 WHEN pensionista.total = 1     AND aposentado.total IS NULL AND rescindido.total IS NULL THEN
                                                              'E'
                                                              WHEN pensionista.total IS NULL AND aposentado.total > 0     AND rescindido.total IS NULL THEN
                                                              'P'
                                                              WHEN pensionista.total IS NULL AND aposentado.total IS NULL AND rescindido.total > 0     THEN
                                                              'R'
                                                              WHEN pensionista.total IS NULL AND aposentado.total IS NULL AND rescindido.total IS NULL THEN
                                                              'A'
                                                              END AS status
                                                            FROM pessoal".$this->getDado('stEntidade') .".contrato
                                                      LEFT JOIN (
                                                                    SELECT contrato_pensionista.cod_contrato
                                                                          , count(*) as total
                                                                        FROM pessoal".$this->getDado('stEntidade') .".contrato_pensionista
                                                                    GROUP BY contrato_pensionista.cod_contrato
                                                              ) AS pensionista
                                                            ON pensionista.cod_contrato = contrato.cod_contrato
                                                      LEFT JOIN (
                                                                    SELECT interna.cod_contrato
                                                                          , count(*) as total
                                                                       FROM (
                                                                                    SELECT aposentadoria.cod_contrato
                                                                                          , max(aposentadoria.timestamp)
                                                                                       FROM pessoal".$this->getDado('stEntidade') .".aposentadoria
                                                                                   LEFT JOIN pessoal".$this->getDado('stEntidade') .".aposentadoria_excluida
                                                                                        ON aposentadoria_excluida.cod_contrato = aposentadoria.cod_contrato
                                                                                          AND aposentadoria_excluida.timestamp    = aposentadoria.timestamp
                                                                                        WHERE aposentadoria.dt_concessao <= (
                                                                                                                                 SELECT periodo_movimentacao.dt_final
                                                                                                                                     FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                                                                   WHERE cod_periodo_movimentacao = (
                                                                                                                                                                     SELECT MAX(cod_periodo_movimentacao)
                                                                                                                                                                         FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                                                                                                   )
                                                                                                                            )
                                                                                         AND aposentadoria_excluida.cod_contrato IS NULL
                                                                                   GROUP BY aposentadoria.cod_contrato
                                                                              ) AS interna
                                                                  GROUP BY interna.cod_contrato
                                                                ) AS aposentado
                                                              ON aposentado.cod_contrato = contrato.cod_contrato
                                                     LEFT JOIN (
                                                                        SELECT contrato_servidor_caso_causa.cod_contrato
                                                                              , count(*) as total
                                                                           FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor_caso_causa
                                                                         WHERE dt_rescisao <= (
                                                                                                    SELECT periodo_movimentacao.dt_final
                                                                                                       FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                                     WHERE cod_periodo_movimentacao = (
                                                                                                                                           SELECT MAX(cod_periodo_movimentacao)
                                                                                                                                           FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                                                                       )
                                                                                                 )
                                                                        GROUP BY contrato_servidor_caso_causa.cod_contrato
                                                                ) AS rescindido
                                                            ON rescindido.cod_contrato = contrato.cod_contrato
                                               ) AS situacao_contrato
                                              ON situacao_contrato.cod_contrato = contrato_servidor.cod_contrato
                                            WHERE situacao_contrato.status = 'A'
                                      GROUP BY contrato_servidor_especialidade_cargo.cod_especialidade
                                             , contrato_servidor.cod_sub_divisao
                                               , contrato_servidor.cod_regime
                                     ) as contador_especialidade
                               ON contador_especialidade.cod_especialidade =  especialidade.cod_especialidade
                              AND contador_especialidade.cod_sub_divisao = sub_divisao.cod_sub_divisao
                              AND contador_especialidade.cod_regime = sub_divisao.cod_regime
-- funcao 2
                        LEFT JOIN (
                                        SELECT count(1) as contador
                                              , contrato_servidor_especialidade_funcao.cod_especialidade
                                             , contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                             , contrato_servidor_regime_funcao.cod_regime
                                          FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor
                                     LEFT JOIN pessoal".$this->getDado('stEntidade') .".contrato_servidor_especialidade_funcao
                                            ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato
                                     LEFT JOIN pessoal".$this->getDado('stEntidade') .".contrato_servidor_especialidade_cargo
                                            ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato
                                     LEFT JOIN (
                                                    SELECT cod_contrato
                                                         , max(timestamp) as timestamp
                                                      FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor_especialidade_funcao
                                                     WHERE timestamp <= '".$this->getDado('dtTimesTamp') ."'
                                                  GROUP BY cod_contrato
                                                ) as max_contrato_servidor_especialidade_funcao
                                            ON contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato
                                           AND contrato_servidor_especialidade_funcao.timestamp    = max_contrato_servidor_especialidade_funcao.timestamp
                                    INNER JOIN pessoal".$this->getDado('stEntidade') .".contrato_servidor_sub_divisao_funcao
                                            ON contrato_servidor.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                                    INNER JOIN (
                                                        SELECT cod_contrato
                                                             , max(timestamp) as timestamp
                                                          FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor_sub_divisao_funcao
                                                         WHERE timestamp <= '".$this->getDado('dtTimesTamp') ."'
                                                      GROUP BY cod_contrato
                                                ) as max_contrato_servidor_sub_divisao_funcao
                                            ON contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                                           AND contrato_servidor_sub_divisao_funcao.timestamp    = max_contrato_servidor_sub_divisao_funcao.timestamp
                                    INNER JOIN pessoal".$this->getDado('stEntidade') .".contrato_servidor_regime_funcao
                                            ON contrato_servidor.cod_contrato = contrato_servidor_regime_funcao.cod_contrato
                                    INNER JOIN (
                                                        SELECT cod_contrato
                                                             , max(timestamp) as timestamp
                                                          FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor_regime_funcao
                                                          WHERE timestamp <= '".$this->getDado('dtTimesTamp') ."'
                                                      GROUP BY cod_contrato
                                               ) as max_contrato_servidor_regime_funcao
                                            ON contrato_servidor_regime_funcao.cod_contrato             = max_contrato_servidor_regime_funcao.cod_contrato
                                           AND contrato_servidor_regime_funcao.timestamp                = max_contrato_servidor_regime_funcao.timestamp
                                    INNER JOIN (
                                                      SELECT contrato.cod_contrato
                                                           , CASE
                                                             WHEN pensionista.total = 1     AND aposentado.total IS NULL AND rescindido.total IS NULL THEN
                                                             'E'
                                                             WHEN pensionista.total IS NULL AND aposentado.total > 0     AND rescindido.total IS NULL THEN
                                                             'P'
                                                             WHEN pensionista.total IS NULL AND aposentado.total IS NULL AND rescindido.total > 0     THEN
                                                             'R'
                                                             WHEN pensionista.total IS NULL AND aposentado.total IS NULL AND rescindido.total IS NULL THEN
                                                             'A'
                                                             END AS status
                                                        FROM pessoal".$this->getDado('stEntidade') .".contrato
                                                   LEFT JOIN (
                                                                   SELECT contrato_pensionista.cod_contrato
                                                                        , count(*) as total
                                                                     FROM pessoal".$this->getDado('stEntidade') .".contrato_pensionista
                                                                 GROUP BY contrato_pensionista.cod_contrato
                                                               ) AS pensionista
                                                            ON pensionista.cod_contrato = contrato.cod_contrato
                                                     LEFT JOIN (
                                                                   SELECT interna.cod_contrato
                                                                        , count(*) as total
                                                                     FROM (
                                                                               SELECT aposentadoria.cod_contrato
                                                                                    , max(aposentadoria.timestamp)
                                                                                 FROM pessoal".$this->getDado('stEntidade') .".aposentadoria
                                                                            LEFT JOIN pessoal".$this->getDado('stEntidade') .".aposentadoria_excluida
                                                                                   ON aposentadoria_excluida.cod_contrato = aposentadoria.cod_contrato
                                                                                  AND aposentadoria_excluida.timestamp    = aposentadoria.timestamp
                                                                                WHERE aposentadoria.dt_concessao <= (
                                                                                                                     SELECT periodo_movimentacao.dt_final
                                                                                                                       FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                                                      WHERE cod_periodo_movimentacao = (
                                                                                                                                                         SELECT MAX(cod_periodo_movimentacao)
                                                                                                                                                           FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                                                                                        )
                                                                                                                    )
                                                                                 AND aposentadoria_excluida.cod_contrato IS NULL
                                                                            GROUP BY aposentadoria.cod_contrato
                                                                              ) AS interna
                                                                       GROUP BY interna.cod_contrato
                                                              ) AS aposentado
                                                           ON aposentado.cod_contrato = contrato.cod_contrato
                                                    LEFT JOIN (
                                                                   SELECT contrato_servidor_caso_causa.cod_contrato
                                                                        , count(*) as total
                                                                     FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor_caso_causa
                                                                    WHERE dt_rescisao <= (
                                                                                           SELECT periodo_movimentacao.dt_final
                                                                                             FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                            WHERE cod_periodo_movimentacao = (
                                                                                                                               SELECT MAX(cod_periodo_movimentacao)
                                                                                                                                 FROM folhapagamento".$this->getDado('stEntidade') .".periodo_movimentacao
                                                                                                                              )
                                                                                          )
                                                                 GROUP BY contrato_servidor_caso_causa.cod_contrato
                                                                ) AS rescindido
                                                            ON rescindido.cod_contrato = contrato.cod_contrato
                                                ) AS situacao_contrato
                                             ON situacao_contrato.cod_contrato = contrato_servidor.cod_contrato
                                          WHERE situacao_contrato.status = 'A'
                                            AND (
                                                     ( contrato_servidor_especialidade_cargo.cod_especialidade != contrato_servidor_especialidade_funcao.cod_especialidade
                                                      OR contrato_servidor_especialidade_cargo.cod_especialidade IS NULL
                                                     )
                                                       OR contrato_servidor.cod_sub_divisao != contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                                    OR contrato_servidor.cod_regime      != contrato_servidor_regime_funcao.cod_regime
                                                )
                                  GROUP BY contrato_servidor_especialidade_funcao.cod_especialidade
                                         , contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                         , contrato_servidor_regime_funcao.cod_regime
                                  ) as contador_principal_especialidade
                             ON contador_principal_especialidade.cod_especialidade = especialidade.cod_especialidade
                            AND contador_principal_especialidade.cod_sub_divisao   = sub_divisao.cod_sub_divisao
                            AND contador_principal_especialidade.cod_regime        = sub_divisao.cod_regime
    --termina funcao

                       ORDER BY codigo
                 ";

        return $stSql;
    }

}

?>
