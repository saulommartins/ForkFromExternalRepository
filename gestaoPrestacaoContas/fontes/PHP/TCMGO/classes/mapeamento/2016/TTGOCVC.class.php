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
    * Classe de mapeamento do arquivo CVC.inc.php
    * Data de Criação:  19/12/2008

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGOCVC.class.php 65190 2016-04-29 19:36:51Z michel $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTGOCVC extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */

    public function __construct()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaVeiculos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaVeiculos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaVeiculos()
    {
    $stSql = "
    SELECT tipo_registro
         , COALESCE(orcamento.recuperaNumOrgao(cod_orgao, exercicio), 0) AS cod_orgao
         , COALESCE(orcamento.recuperaNumUnidade(cod_orgao, exercicio), 0) AS cod_unidade
         , cod_veiculo
         , descricao
         , tipo_veiculo
         , subtipo_veiculo
         , modelo
         , ano_fabricacao
         , placa
         , chassi
         , numero_serie
         , situacao_veiculo
         , tipo_deslocamento
         , km_inicial
         , km_final
         , atestado_controle
         , numero_sequencial
      FROM (
        SELECT 10 AS tipo_registro
             , CASE WHEN (veiculo_propriedade.proprio = true) THEN
                    orgao_bem.cod_orgao
               ELSE
                    orgao_terceiro.cod_orgao
               END AS cod_orgao
             , veiculo.cod_veiculo
             , CASE WHEN (veiculo_propriedade.proprio = true) THEN
                    descricao_veiculo.descricao
               ELSE
                    modelo.nom_modelo ||
                    ' ' || veiculo.ano_fabricacao ||
                    ' cor ' || veiculo.cor ||
                    ' placa ' || veiculo.placa
               END AS descricao
             , CASE WHEN (veiculo_propriedade.proprio = true) THEN
                    orgao_bem.exercicio
               ELSE
                    orgao_terceiro.exercicio
               END AS exercicio
             , tipo_veiculo_vinculo.cod_tipo_tcm AS tipo_veiculo
             , tipo_veiculo_vinculo.cod_subtipo_tcm AS subtipo_veiculo
             , modelo.nom_modelo AS modelo
             , veiculo.ano_fabricacao
             , SUBSTR(veiculo.placa, 1, 3) || ' ' || SUBSTR(veiculo.placa, 4, 4) AS placa
             , veiculo.chassi
             , '' AS numero_serie
             , CASE WHEN (veiculo_propriedade.proprio = true) THEN
                    01
               ELSE
                    02
               END AS situacao_veiculo
             , 01 AS tipo_deslocamento
             , COALESCE(km_inicial, 0) AS km_inicial
             , COALESCE(kilometragem.km_final, 0) AS km_final
             , 1 AS atestado_controle
             , 0 AS numero_sequencial
          FROM frota.veiculo
          JOIN frota.modelo
            ON modelo.cod_modelo = veiculo.cod_modelo
           AND modelo.cod_marca = veiculo.cod_marca
          JOIN frota.veiculo_propriedade
            ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
           AND veiculo_propriedade.\"timestamp\" = ( SELECT MAX(vp.\"timestamp\")
                                                     FROM frota.veiculo_propriedade as vp
                                                    WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo
                                                 )
     LEFT JOIN ( SELECT bem.descricao
                      , bem.cod_bem
                      , veiculo_propriedade.cod_veiculo
                      , MAX(veiculo_propriedade.\"timestamp\")
                   FROM frota.veiculo
                   JOIN frota.veiculo_propriedade
                     ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                    AND veiculo_propriedade.proprio = true
                   JOIN frota.proprio
                     ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                    AND proprio.\"timestamp\" = veiculo_propriedade.\"timestamp\"
                   JOIN patrimonio.bem
                     ON bem.cod_bem = proprio.cod_bem
               GROUP BY bem.descricao
                      , veiculo_propriedade.cod_veiculo
                      , bem.cod_bem
             ) AS descricao_veiculo
            ON descricao_veiculo.cod_veiculo = veiculo.cod_veiculo
    LEFT JOIN ( SELECT * FROM(
                SELECT historico_bem.cod_bem
                     , historico_bem.cod_orgao
                     , CAST(EXTRACT(YEAR FROM MAX(historico_bem.timestamp)) AS VARCHAR) AS exercicio
                     , MAX(\"timestamp\")
                  FROM patrimonio.historico_bem
              GROUP BY historico_bem.cod_bem
                     , historico_bem.cod_orgao
                ) as tabela WHERE exercicio <= '".Sessao::getExercicio()."'
            ) orgao_bem
           ON orgao_bem.cod_bem = descricao_veiculo.cod_bem
    LEFT JOIN (SELECT CASE WHEN (utilizacao_retorno.virada_odometro = true) THEN
                                           COALESCE(utilizacao_retorno.km_retorno, 0) + 999999
                                   ELSE
                                           COALESCE(utilizacao_retorno.km_retorno, 0)
                                   END AS km_final
                                 , utilizacao_retorno.cod_veiculo
                              FROM frota.utilizacao_retorno
                 JOIN ( SELECT tabela_saida.cod_veiculo
                             , MAX(hr_saida) AS hr_saida
                             , tabela_saida.dt_saida
                          FROM frota.utilizacao_retorno
                          JOIN ( SELECT MAX(dt_saida) AS dt_saida
                                      , cod_veiculo
                                   FROM frota.utilizacao_retorno
                               GROUP BY cod_veiculo
                               ORDER BY cod_veiculo
                                      , dt_saida
                             ) AS tabela_saida
                            ON tabela_saida.cod_veiculo = utilizacao_retorno.cod_veiculo
                           AND tabela_saida.dt_saida = utilizacao_retorno.dt_saida
                      GROUP BY tabela_saida.dt_saida
                             , tabela_saida.cod_veiculo
                    ) AS saida
                   ON saida.cod_veiculo = utilizacao_retorno.cod_veiculo
                  AND saida.dt_saida = utilizacao_retorno.dt_saida
                  AND saida.hr_saida = utilizacao_retorno.hr_saida
             ) AS kilometragem
            ON kilometragem.cod_veiculo = veiculo.cod_veiculo
     LEFT JOIN tcmgo.tipo_veiculo_vinculo
            ON tipo_veiculo_vinculo.cod_tipo = veiculo.cod_tipo_veiculo
     LEFT JOIN ( SELECT * FROM(
                    SELECT terceiros_historico.cod_veiculo
                         , terceiros_historico.cod_orgao
                         , CAST(EXTRACT(YEAR FROM MAX(terceiros_historico.timestamp)) AS VARCHAR) AS exercicio
                         , MAX(terceiros_historico.\"timestamp\")
                      FROM frota.terceiros_historico
                  GROUP BY terceiros_historico.cod_veiculo
                         , terceiros_historico.cod_orgao
                ) as tabela WHERE exercicio <= '".Sessao::getExercicio()."'
             ) orgao_terceiro
            ON orgao_terceiro.cod_veiculo = veiculo.cod_veiculo
      ORDER BY veiculo.cod_veiculo
    ) AS veiculos
    ";

        return $stSql;
    }

    public function recuperaConsumoCombustivelVeiculo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaConsumoCombustivelVeiculo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaConsumoCombustivelVeiculo()
    {
    $stSql = "
    SELECT tipo_registro
         , COALESCE(orcamento.recuperaNumOrgao(cod_orgao, exercicio), 0) AS cod_orgao
         , COALESCE(orcamento.recuperaNumUnidade(cod_orgao, exercicio), 0) AS cod_unidade
         , cod_veiculo
         , tipo_gasto
         , tipo_combustivel
         , quantidade
         , origem_combustivel
         , espaco_branco
         , nro_sequencial
      FROM (
          SELECT 11 as tipo_registro
               , CASE WHEN (veiculo_propriedade.proprio = true) THEN
                      orgao_bem.cod_orgao
                 ELSE
                      orgao_terceiro.cod_orgao
                 END AS cod_orgao
               , CASE WHEN (veiculo_propriedade.proprio = true) THEN
                      orgao_bem.exercicio
                 ELSE
                      orgao_terceiro.exercicio
                 END AS exercicio
               , manutencao.cod_veiculo
               , combustivel_vinculo.cod_tipo AS tipo_gasto
               , combustivel_vinculo.cod_combustivel AS tipo_combustivel
               , ROUND(SUM(COALESCE(manutencao_item.quantidade, 0)), 0) AS quantidade
               , 2 AS origem_combustivel
               , '' AS espaco_branco
               , 0 AS nro_sequencial
            FROM frota.manutencao
            JOIN frota.veiculo_propriedade
              ON veiculo_propriedade.cod_veiculo = manutencao.cod_veiculo
             AND veiculo_propriedade.\"timestamp\" = ( SELECT MAX(vp.\"timestamp\")
                                                       FROM frota.veiculo_propriedade as vp
                                                      WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo
                                                   )
         LEFT JOIN ( SELECT bem.descricao
                          , bem.cod_bem
                          , veiculo_propriedade.cod_veiculo
                          , MAX(veiculo_propriedade.\"timestamp\")
                       FROM frota.veiculo
                       JOIN frota.veiculo_propriedade
                         ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                        AND veiculo_propriedade.proprio = true
                       JOIN frota.proprio
                         ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                        AND proprio.\"timestamp\" = veiculo_propriedade.\"timestamp\"
                       JOIN patrimonio.bem
                         ON bem.cod_bem = proprio.cod_bem
                   GROUP BY bem.descricao
                          , veiculo_propriedade.cod_veiculo
                          , bem.cod_bem
                 ) AS descricao_veiculo
                ON descricao_veiculo.cod_veiculo = manutencao.cod_veiculo
        LEFT JOIN ( SELECT * FROM (
                    SELECT historico_bem.cod_bem
                         , historico_bem.cod_orgao
                         , CAST(EXTRACT(YEAR FROM MAX(historico_bem.timestamp)) AS VARCHAR) AS exercicio
                         , MAX(\"timestamp\")
                      FROM patrimonio.historico_bem
                  GROUP BY historico_bem.cod_bem
                         , historico_bem.cod_orgao
                ) as tabela WHERE exercicio <= '".Sessao::getExercicio()."'
                ) orgao_bem
               ON orgao_bem.cod_bem = descricao_veiculo.cod_bem
         LEFT JOIN ( SELECT * FROM (
                     SELECT terceiros_historico.cod_veiculo
                          , terceiros_historico.cod_orgao
                          , CAST(EXTRACT(YEAR FROM MAX(terceiros_historico.timestamp)) AS VARCHAR) AS exercicio
                          , MAX(terceiros_historico.\"timestamp\")
                       FROM frota.terceiros_historico
                   GROUP BY terceiros_historico.cod_veiculo
                          , terceiros_historico.cod_orgao
                ) as tabela WHERE exercicio <= '".Sessao::getExercicio()."'
                 ) orgao_terceiro
                ON orgao_terceiro.cod_veiculo = manutencao.cod_veiculo


            JOIN frota.efetivacao
              ON efetivacao.cod_manutencao = manutencao.cod_manutencao
             AND efetivacao.exercicio_manutencao = manutencao.exercicio
            JOIN frota.autorizacao
              ON autorizacao.cod_autorizacao = efetivacao.cod_autorizacao
             AND autorizacao.exercicio = efetivacao.exercicio_autorizacao
            JOIN frota.manutencao_item
              ON manutencao_item.cod_manutencao = manutencao.cod_manutencao
             AND manutencao_item.exercicio = manutencao.exercicio
            JOIN tcmgo.combustivel_vinculo
              ON combustivel_vinculo.cod_item = manutencao_item.cod_item ";
            if ($this->getDado('dtInicio')) {
                $stSql .= " WHERE manutencao.dt_manutencao >= to_char(to_date('".$this->getDado('dtInicio')."', 'dd/mm/yyyy'), 'yyyy-mm-dd')::date \n";
            }
            if ($this->getDado('dtFim')) {
                $stSql .= " AND manutencao.dt_manutencao <= to_char(to_date('".$this->getDado('dtFim')."', 'dd/mm/yyyy'), 'yyyy-mm-dd')::date \n";
            }
        $stSql .= "
        GROUP BY manutencao.cod_veiculo
               , combustivel_vinculo.cod_tipo
               , combustivel_vinculo.cod_combustivel
               , veiculo_propriedade.proprio
               , orgao_bem.cod_orgao
               , orgao_terceiro.cod_orgao
               , orgao_bem.exercicio
               , orgao_terceiro.exercicio
        ORDER BY manutencao.cod_veiculo
        ) AS tabela

        ";

        return $stSql;
    }

    public function recuperaDetalhamentoEmpenhos(&$rsRecordSet, $stFiltro = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDetalhamentoEmpenhos().$stFiltro;
        $this->setDebug( $stSql );

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

        return $obErro;
    }

    public function montaRecuperaDetalhamentoEmpenhos()
    {
        $stSql  = " SELECT 12 as tipo_registro                                   \n";
        $stSql .= "      , despesa.cod_programa                                  \n";
        $stSql .= "      , despesa.num_orgao                                     \n";
        $stSql .= "      , despesa.num_unidade                                   \n";
        $stSql .= "      , despesa.cod_funcao                                    \n";
        $stSql .= "      , despesa.cod_subfuncao                                 \n";
        $stSql .= "      , substr(TO_CHAR(despesa.num_pao, '9999'), 2, 1) as natureza_acao \n";
        $stSql .= "      , substr(TO_CHAR(despesa.num_pao, '9999'), 3, 3) as nro_proj_ativ \n";
        $stSql .= "      , orcamento.recuperaEstruturalDespesa(despesa.cod_conta, despesa.exercicio, 6, FALSE, FALSE) AS elemento_despesa \n";
        $stSql .= "      , orcamento.recuperaEstruturalDespesa(despesa.cod_conta, despesa.exercicio, 2, TRUE, FALSE) AS subelemento_despesa \n";
        $stSql .= "      , empenho.cod_empenho                                   \n";
        $stSql .= "      , TO_CHAR(empenho.dt_empenho,'ddmmyyyy') AS dt_empenho  \n";
        $stSql .= "      , '' AS espaco_branco                                   \n";
        $stSql .= "      , 0 AS nro_sequencial                                   \n";
        $stSql .= "   FROM empenho.item_pre_empenho_julgamento                   \n";
        $stSql .= "   JOIN frota.item                                            \n";
        $stSql .= "     ON item_pre_empenho_julgamento.cod_item = item.cod_item  \n";
        $stSql .= "   JOIN empenho.item_pre_empenho                              \n";
        $stSql .= "     ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho \n";
        $stSql .= "    AND item_pre_empenho.exercicio = item_pre_empenho_julgamento.exercicio \n";
        $stSql .= "    AND item_pre_empenho.num_item = item_pre_empenho_julgamento.num_item \n";
        $stSql .= "   JOIN empenho.pre_empenho                                   \n";
        $stSql .= "     ON pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho \n";
        $stSql .= "    AND pre_empenho.exercicio = item_pre_empenho.exercicio    \n";
        $stSql .= "   JOIN empenho.empenho                                       \n";
        $stSql .= "     ON empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho \n";
        $stSql .= "    AND empenho.exercicio = pre_empenho.exercicio             \n";
        $stSql .= "   JOIN empenho.pre_empenho_despesa                           \n";
        $stSql .= "     ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho \n";
        $stSql .= "    AND pre_empenho_despesa.exercicio = pre_empenho.exercicio \n";
        $stSql .= "   JOIN orcamento.despesa                                     \n";
        $stSql .= "     ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa \n";
        $stSql .= "    AND despesa.exercicio = pre_empenho_despesa.exercicio     \n";
        $stSql .= "  WHERE item.cod_tipo = 1                                     \n";
        if ($this->getDado('dtInicio')) {
            $stSql .= " AND empenho.dt_empenho >= to_char(to_date('".$this->getDado('dtInicio')."', 'dd/mm/yyyy'), 'yyyy-mm-dd')::date \n";
        }
        if ($this->getDado('dtFim')) {
            $stSql .= " AND empenho.dt_empenho <= to_char(to_date('".$this->getDado('dtFim')."', 'dd/mm/yyyy'), 'yyyy-mm-dd')::date \n";
        }

        return $stSql;

    }

}
