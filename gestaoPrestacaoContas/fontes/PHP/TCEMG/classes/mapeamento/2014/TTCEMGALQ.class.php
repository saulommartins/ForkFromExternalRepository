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
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 56934 $
    $Name$
    $Author: gelson $
    $Date: 2014-01-08 17:46:44 -0200 (Wed, 08 Jan 2014) $

    * Casos de uso: uc-06.04.00
*/

/*
$Log$
Revision 1.7  2007/10/10 23:35:33  hboaventura
correção dos arquivos

Revision 1.6  2007/10/05 18:35:14  hboaventura
Correção dos desdobramentos

Revision 1.5  2007/06/14 19:31:03  hboaventura
Correção de bugs

Revision 1.4  2007/06/12 20:44:11  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.3  2007/06/12 18:34:05  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.2  2007/06/07 15:36:31  hboaventura
Inclusão do filtro por periodicidade

Revision 1.1  2007/04/21 20:22:43  hboaventura
Arquivos para geração do TCMGO

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGALQ extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/

    public function recuperaExportacaoALQ10(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaExportacaoALQ10", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaExportacaoALQ10()
    {
        $stSql = "
                SELECT  10 AS tipo_registro
		    ,   tcemg.seq_cod_red_alq(empenho.exercicio, empenho.cod_entidade,empenho.cod_empenho) as cod_reduzido
                    ,   orgao_sicom.valor AS cod_orgao
                    ,   CASE WHEN pre_empenho.implantado = 't' THEN
                                CASE WHEN ( uniorcam.num_orgao_atual IS NOT NULL) THEN
					LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')
				     ELSE LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')
				    END
                                ELSE LPAD((LPAD(''||despesa_empenho.num_orgao,2, '0')||LPAD(''||despesa_empenho.num_unidade,2, '0')), 5, '0') 
                            END AS codunidadesub 
                    ,   empenho.cod_empenho AS num_empenho
                    ,   to_char(empenho.dt_empenho,'ddmmyyyy') as dt_empenho
                    ,   to_char(nota_liquidacao.dt_liquidacao,'ddmmyyyy') as dt_liquidacao
                    ,   TCEMG.numero_nota_liquidacao('".$this->getDado('exercicio')."',
                                                                 empenho.cod_entidade,
                                                                 nota_liquidacao.cod_nota,
                                                                 nota_liquidacao.exercicio_empenho,
                                                                 empenho.cod_empenho
                                                    ) AS num_liquidacao
                    ,   to_char(nota_liquidacao_item_anulado.timestamp,'ddmmyyyy') as dt_anulacao
                    ,   TCEMG.numero_anulacao_liquidacao('".Sessao::getExercicio()."',pre_empenho.exercicio,empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao_item_anulado.timestamp)  AS  num_anulacao
                    ,   CASE WHEN empenho.exercicio = '".Sessao::getExercicio()."'
                                 THEN 1
                                 ELSE 2
                           END AS tipo_liquidacao
                    ,   'Anulacao Liquidacao' AS justificativa_anulacao
                    ,   SUM(nota_liquidacao_item_anulado.vl_anulado) as vl_anulado

                FROM empenho.empenho

                JOIN empenho.nota_liquidacao
                  ON nota_liquidacao.exercicio_empenho = empenho.exercicio
                 AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                 AND nota_liquidacao.cod_empenho = empenho.cod_empenho

                JOIN empenho.nota_liquidacao_item_anulado
                  ON nota_liquidacao_item_anulado.exercicio = nota_liquidacao.exercicio
                 AND nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao.cod_entidade
                 AND nota_liquidacao_item_anulado.cod_nota = nota_liquidacao.cod_nota

           LEFT JOIN empenho.pre_empenho
                  ON pre_empenho.exercicio = empenho.exercicio
                 AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

           LEFT JOIN empenho.restos_pre_empenho
                  ON restos_pre_empenho.exercicio = pre_empenho.exercicio
                 AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                JOIN (SELECT valor::integer 
                             , configuracao_entidade.exercicio
                             , configuracao_entidade.cod_entidade
                          FROM tcemg.orgao 
                    INNER JOIN administracao.configuracao_entidade
                            ON configuracao_entidade.valor::integer = orgao.num_orgao   
                         WHERE configuracao_entidade.cod_entidade IN (1,2,3)  AND parametro = 'tcemg_codigo_orgao_entidade_sicom'
                      )  AS orgao_sicom
                  ON orgao_sicom.exercicio = '".Sessao::getExercicio()."'
                 AND orgao_sicom.cod_entidade = empenho.cod_entidade
		 
	   LEFT JOIN tcemg.uniorcam
		  ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
		 AND uniorcam.exercicio = restos_pre_empenho.exercicio
		 AND uniorcam.num_orgao_atual IS NOT NULL
		 
           LEFT JOIN (SELECT  pre_empenho_despesa.cod_despesa--, *--despesa.*
                            , pre_empenho_despesa.cod_pre_empenho
                            , pre_empenho_despesa.exercicio
                            , despesa.num_orgao
                            , despesa.num_unidade
                        FROM empenho.pre_empenho_despesa
                        JOIN orcamento.despesa
                          ON despesa.exercicio = pre_empenho_despesa.exercicio
                         AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
		    ) AS despesa_empenho
		  ON  pre_empenho.exercicio = despesa_empenho.exercicio
		 AND pre_empenho.cod_pre_empenho = despesa_empenho.cod_pre_empenho                     
                     
               WHERE TO_DATE(nota_liquidacao_item_anulado.timestamp::varchar, 'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                 AND nota_liquidacao_item_anulado.cod_entidade IN (".$this->getDado('entidades').")

            GROUP BY pre_empenho.implantado
                   , pre_empenho.exercicio
                   , restos_pre_empenho.num_orgao
                   , despesa_empenho.num_orgao
                   , orgao_sicom.valor
                   , restos_pre_empenho.num_unidade
                   , despesa_empenho.num_unidade                       
                   , empenho.cod_empenho
                   , empenho.dt_empenho
                   , nota_liquidacao.dt_liquidacao
                   , empenho.cod_entidade
                   , nota_liquidacao.cod_nota
                   , nota_liquidacao.exercicio_empenho
                   , nota_liquidacao_item_anulado.timestamp
                   , empenho.exercicio
		   , codunidadesub
        ";
        return $stSql;
    }

    public function recuperaExportacaoALQ11(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaExportacaoALQ11", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaExportacaoALQ11()
    {
        $stSql = "
             SELECT  11 AS tipo_registro
		    , tcemg.seq_cod_red_alq(empenho.exercicio, empenho.cod_entidade,empenho.cod_empenho) as cod_reduzido
                    , CASE WHEN restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
					       THEN restos_pre_empenho.recurso::VARCHAR
						   ELSE COALESCE(recurso.cod_fonte, '100')::VARCHAR
					   END AS cod_fonte_recurso
                    , empenho.cod_empenho as num_empenho
                    , empenho.exercicio, empenho.cod_entidade,empenho.cod_empenho
                    , SUM(nota_liquidacao_item_anulado.vl_anulado) as vl_anulado_fonte
					, TCEMG.numero_anulacao_liquidacao('".$this->getDado('exercicio')."',pre_empenho.exercicio,empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao_item_anulado.timestamp)  AS  num_anulacao
		    , TCEMG.numero_nota_liquidacao('".$this->getDado('exercicio')."',
                                                                 empenho.cod_entidade,
                                                                 nota_liquidacao.cod_nota,
                                                                 nota_liquidacao.exercicio_empenho,
                                                                 empenho.cod_empenho
                                                    ) AS num_liquidacao
                          
                FROM  empenho.empenho

                JOIN  empenho.nota_liquidacao
                  ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
                 AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
                 AND  nota_liquidacao.cod_empenho = empenho.cod_empenho

                JOIN  empenho.nota_liquidacao_item_anulado
                  ON  nota_liquidacao_item_anulado.exercicio = nota_liquidacao.exercicio
                 AND  nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao.cod_entidade
                 AND  nota_liquidacao_item_anulado.cod_nota = nota_liquidacao.cod_nota

                JOIN  empenho.pre_empenho
                  ON  pre_empenho.exercicio = empenho.exercicio
                 AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

           LEFT JOIN  empenho.restos_pre_empenho
                  ON  restos_pre_empenho.exercicio = pre_empenho.exercicio
                 AND  restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

           LEFT JOIN (SELECT  despesa.cod_entidade
                            , despesa.exercicio
                            , despesa.cod_recurso
                            , conta_despesa.cod_estrutural
                            , pre_empenho_despesa.cod_pre_empenho
                        FROM empenho.pre_empenho_despesa
                        JOIN orcamento.despesa
                          ON despesa.exercicio = pre_empenho_despesa.exercicio
                         AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                        JOIN orcamento.conta_despesa
                          ON conta_despesa.exercicio = despesa.exercicio
                         AND conta_despesa.cod_conta = despesa.cod_conta
                     ) AS despesa
                  ON despesa.exercicio = pre_empenho.exercicio
                 AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                 AND despesa.cod_entidade = empenho.cod_entidade
                  OR  despesa.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho

           LEFT JOIN  orcamento.recurso
                  ON  recurso.exercicio = despesa.exercicio
                 AND  recurso.cod_recurso = despesa.cod_recurso

               WHERE  TO_DATE(nota_liquidacao_item_anulado.timestamp::varchar, 'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                 AND  nota_liquidacao_item_anulado.cod_entidade IN (".$this->getDado('entidades').")

                GROUP BY 1,2,3,4,5,6,7,num_liquidacao, num_anulacao
        ";
        return $stSql;
    }

    public function recuperaExportacaoALQ12(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaExportacaoALQ12", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaExportacaoALQ12()
    {
        $stSql = "
                    SELECT
                            12 AS tipo_registro
                          , tcemg.seq_cod_red_alq(empenho.exercicio, empenho.cod_entidade,empenho.cod_empenho) as cod_reduzido
                          , SUBSTR(TO_CHAR(nota_liquidacao_item_anulado.timestamp,'ddmmyyyy'),3,2) AS mes_competencia
                          , SUBSTR(TO_CHAR(nota_liquidacao_item_anulado.timestamp,'ddmmyyyy'),5,8) AS exercicio_competencia
                          , SUM(nota_liquidacao_item_anulado.vl_anulado) AS vl_anulado_dsp_exercicio_ant
                          , empenho.cod_empenho as num_empenho
						  , TCEMG.numero_nota_liquidacao('".$this->getDado('exercicio')."', empenho.cod_entidade, nota_liquidacao.cod_nota, nota_liquidacao.exercicio_empenho, empenho.cod_empenho ) AS num_liquidacao
						  , TCEMG.numero_anulacao_liquidacao('".$this->getDado('exercicio')."',pre_empenho.exercicio,empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao_item_anulado.timestamp)  AS  num_anulacao

                    FROM  empenho.empenho

                    JOIN  empenho.nota_liquidacao
                      ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
                     AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
                     AND  nota_liquidacao.cod_empenho = empenho.cod_empenho

                    JOIN  empenho.nota_liquidacao_item_anulado
                      ON  nota_liquidacao_item_anulado.exercicio = nota_liquidacao.exercicio
                     AND  nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao.cod_entidade
                     AND  nota_liquidacao_item_anulado.cod_nota = nota_liquidacao.cod_nota

                    JOIN  empenho.pre_empenho
                      ON  pre_empenho.exercicio = empenho.exercicio
                     AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                     AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

               LEFT JOIN  empenho.restos_pre_empenho
                      ON  restos_pre_empenho.exercicio = pre_empenho.exercicio
                     AND  restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    JOIN (SELECT  despesa.*
                                , conta_despesa.cod_estrutural
                                , pre_empenho_despesa.cod_pre_empenho
                            FROM empenho.pre_empenho_despesa
                            JOIN orcamento.despesa
                              ON despesa.exercicio = pre_empenho_despesa.exercicio
                             AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                            JOIN orcamento.conta_despesa
                              ON conta_despesa.exercicio = despesa.exercicio
                             AND conta_despesa.cod_conta = despesa.cod_conta
                            JOIN tcemg.orgao
                              ON orgao.num_orgao = despesa.num_orgao
                             AND orgao.exercicio = despesa.exercicio
                            JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.valor::integer = orgao.num_orgao
                             AND configuracao_entidade.exercicio = orgao.exercicio
                           WHERE configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                             AND configuracao_entidade.cod_modulo = 55
                        ) AS despesa
                      ON despesa.exercicio = pre_empenho.exercicio
                     AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND despesa.cod_entidade = empenho.cod_entidade

               LEFT JOIN  (SELECT  conta_despesa.cod_estrutural
                                 , conta_despesa.cod_conta
                                 , conta_despesa.exercicio
                                 , pre_empenho_despesa.cod_pre_empenho
                             FROM empenho.pre_empenho_despesa
                       INNER JOIN orcamento.conta_despesa
                               ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                              AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                        ) AS despesa_sub_elemento
                      ON  despesa_sub_elemento.exercicio = pre_empenho.exercicio
                     AND  despesa_sub_elemento.cod_pre_empenho = pre_empenho.cod_pre_empenho

               LEFT JOIN  orcamento.recurso
                      ON  recurso.exercicio = despesa.exercicio
                     AND  recurso.cod_recurso = despesa.cod_recurso

                   --WHERE TO_DATE(nota_liquidacao_item_anulado.timestamp::varchar, 'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
				   WHERE TO_DATE(nota_liquidacao_item_anulado.timestamp::varchar, 'yyyy-mm-dd') BETWEEN TO_DATE('01/01/".($this->getDado('exercicio')-1)."','dd/mm/yyyy') AND TO_DATE('31/12/".($this->getDado('exercicio')-1)."','dd/mm/yyyy')
                     AND nota_liquidacao_item_anulado.cod_entidade IN (".$this->getDado('entidades').")

                GROUP BY pre_empenho.exercicio
                       , empenho.cod_entidade
                       , nota_liquidacao.cod_nota
                       , nota_liquidacao_item_anulado.timestamp
                       , empenho.cod_empenho
                       , empenho.exercicio
					   , num_liquidacao
					   , num_anulacao
                       
        ";
        return $stSql;
    }

	public function __destruct(){}

}
