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
    * Extensão da Classe de Mapeamento
    * Data de Criação: 28/03/2011
    *
    *
    * @author: Eduardo Paculski Schitz
    *
    * @package URBEM
    *
*/
class TTCEAMLicitacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMLicitacao()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
            SELECT 0 AS reservado_tc
                 , processo_licitatorio
                 , diario_oficial
                 , REPLACE(data_publicacao, '-', '') AS data_publicacao
                 , modalidade
                 , '' AS reservado_caracter_tc
                 , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = (SELECT valor FROM administracao.configuracao WHERE parametro = 'CGMPrefeito' AND exercicio = '".$this->getDado('exercicio')."')::integer) AS responsavel_juridico
                 , objeto_licitacao
                 , total_previsto
                 , processo_licitatorio||'.pdf' AS arquivo_texto
                 , num_edital
                 , (SELECT array_to_string( ARRAY( SELECT despesa.exercicio
                                                        ||LPAD(despesa.num_orgao::varchar||LPAD(despesa.num_unidade::varchar,2,'0')::varchar, 6, '0')
                                                        ||SUBSTR(despesa.num_pao::varchar, 1, 1)
                                                        ||LPAD(despesa.num_pao::varchar, 7, '0')
                                                        ||SUBSTR(REPLACE(cod_estrutural::varchar, '.', ''), 1, 6)
                                                        ||LPAD(despesa.cod_programa::varchar, 4, '0')
                                                        ||LPAD(despesa.cod_subfuncao::varchar, 3, '0')
                                                        ||LPAD(recurso.cod_fonte::varchar, 10, '0')
                                                     FROM orcamento.despesa
                                                     JOIN orcamento.conta_despesa
                                                       ON conta_despesa.cod_conta = despesa.cod_conta
                                                      AND conta_despesa.exercicio = despesa.exercicio
                                                     JOIN orcamento.recurso
                                                       ON recurso.cod_recurso = despesa.cod_recurso
                                                      AND recurso.exercicio   = despesa.exercicio
                                                    WHERE despesa.exercicio = registros.exercicio
                                                      AND despesa.cod_despesa IN (SELECT DISTINCT cod_despesa FROM compras.mapa_item_dotacao WHERE exercicio = registros.exercicio AND cod_mapa = registros.cod_mapa)
                                                    LIMIT 6 ), '' ) ) AS dotacao_ate_6
                 , 0 AS tipo_licitacao
                 , (SELECT array_to_string( ARRAY( SELECT despesa.exercicio
                                                      ||LPAD(despesa.num_orgao::varchar||LPAD(despesa.num_unidade::varchar,2,'0')::varchar, 6, '0')
                                                      ||SUBSTR(despesa.num_pao::varchar, 1, 1)
                                                      ||LPAD(despesa.num_pao::varchar, 7, '0')
                                                      ||SUBSTR(REPLACE(cod_estrutural::varchar, '.', ''), 1, 6)
                                                      ||LPAD(despesa.cod_programa::varchar, 4, '0')
                                                      ||LPAD(despesa.cod_subfuncao::varchar, 3, '0')
                                                      ||LPAD(recurso.cod_fonte::varchar, 10, '0')
                                                     FROM orcamento.despesa
                                                     JOIN orcamento.conta_despesa
                                                       ON conta_despesa.cod_conta = despesa.cod_conta
                                                      AND conta_despesa.exercicio = despesa.exercicio
                                                     JOIN orcamento.recurso
                                                       ON recurso.cod_recurso = despesa.cod_recurso
                                                      AND recurso.exercicio   = despesa.exercicio
                                                    WHERE despesa.exercicio = registros.exercicio
                                                      AND despesa.cod_despesa IN (SELECT DISTINCT cod_despesa FROM compras.mapa_item_dotacao WHERE exercicio = registros.exercicio AND cod_mapa = registros.cod_mapa)
                                                   OFFSET 6 ), '' ) ) AS dotacao_depois_6

              FROM (
               SELECT CASE WHEN licitacao.cod_modalidade = 1  THEN 'CC'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                           WHEN licitacao.cod_modalidade = 2  THEN 'TP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                           WHEN licitacao.cod_modalidade = 3  THEN 'CO'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                           WHEN licitacao.cod_modalidade = 4  THEN 'LE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                           WHEN licitacao.cod_modalidade = 5  THEN 'CP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                           WHEN licitacao.cod_modalidade = 6  THEN 'PR'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                           WHEN licitacao.cod_modalidade = 7  THEN 'PE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                           WHEN licitacao.cod_modalidade = 8  THEN 'DL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                           WHEN licitacao.cod_modalidade = 9  THEN 'IL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                           WHEN licitacao.cod_modalidade = 10 THEN 'OT'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                           WHEN licitacao.cod_modalidade = 11 THEN 'RP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                      END AS processo_licitatorio
                    , mapa.cod_mapa
                    , mapa.exercicio
                    , configuracao_arquivo_licitacao.diario_oficial
                    , CAST(publicacao_edital.data_publicacao AS VARCHAR) AS data_publicacao
                    , tceam.fn_depara_modalidade_licitacao(licitacao.cod_modalidade, licitacao.cod_tipo_objeto) AS modalidade
                    , objeto.descricao AS objeto_licitacao
                    , licitacao.vl_cotado AS total_previsto
                    , CAST(edital.num_edital AS VARCHAR) AS num_edital
                 FROM compras.mapa
                 JOIN licitacao.licitacao
                   ON licitacao.cod_mapa       = mapa.cod_mapa
                  AND licitacao.exercicio_mapa = mapa.exercicio
                 JOIN licitacao.edital
                   ON edital.cod_licitacao       = licitacao.cod_licitacao
                  AND edital.cod_modalidade      = licitacao.cod_modalidade
                  AND edital.cod_entidade        = licitacao.cod_entidade
                  AND edital.exercicio_licitacao = licitacao.exercicio
                 JOIN compras.objeto
                   ON objeto.cod_objeto = licitacao.cod_objeto
            LEFT JOIN licitacao.publicacao_edital
                   ON publicacao_edital.num_edital = edital.num_edital
                  AND publicacao_edital.exercicio  = edital.exercicio
            LEFT JOIN tceam.configuracao_arquivo_licitacao
                   ON configuracao_arquivo_licitacao.cod_mapa  = mapa.cod_mapa
                  AND configuracao_arquivo_licitacao.exercicio = mapa.exercicio
                WHERE mapa.exercicio = '".$this->getDado('exercicio')."'
                  AND to_char(licitacao.timestamp,'mm') = '".$this->getDado('mes')."'
                  AND licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")

            UNION ALL

               SELECT CASE WHEN compra_direta.cod_modalidade = 1  THEN 'CC'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                           WHEN compra_direta.cod_modalidade = 2  THEN 'TP'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                           WHEN compra_direta.cod_modalidade = 3  THEN 'CO'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                           WHEN compra_direta.cod_modalidade = 4  THEN 'LE'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                           WHEN compra_direta.cod_modalidade = 5  THEN 'CP'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                           WHEN compra_direta.cod_modalidade = 6  THEN 'PR'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                           WHEN compra_direta.cod_modalidade = 7  THEN 'PE'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                           WHEN compra_direta.cod_modalidade = 8  THEN 'DL'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                           WHEN compra_direta.cod_modalidade = 9  THEN 'IL'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                           WHEN compra_direta.cod_modalidade = 10 THEN 'OT'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                           WHEN compra_direta.cod_modalidade = 11 THEN 'RP'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade

                      END AS processo_licitatorio
                    , mapa.cod_mapa
                    , mapa.exercicio
                    , configuracao_arquivo_licitacao.diario_oficial
                    , TO_CHAR(compra_direta.timestamp, 'yyyymmdd') AS data_publicacao
                    , tceam.fn_depara_modalidade_licitacao(compra_direta.cod_modalidade, compra_direta.cod_tipo_objeto) AS modalidade
                    , objeto.descricao AS objeto_licitacao
                    , vl_compra_direta.vl_total AS total_previsto
                    , '' AS num_edital
                 FROM compras.mapa
                 JOIN compras.compra_direta
                   ON compra_direta.cod_mapa       = mapa.cod_mapa
                  AND compra_direta.exercicio_mapa = mapa.exercicio
                 JOIN compras.objeto
                   ON objeto.cod_objeto = compra_direta.cod_objeto
                 JOIN ( SELECT SUM(vl_total) AS vl_total
                             , cod_mapa
                             , exercicio
                          FROM compras.mapa_item
                      GROUP BY cod_mapa
                             , exercicio
                    ) AS vl_compra_direta
                   ON vl_compra_direta.cod_mapa  = mapa.cod_mapa
                  AND vl_compra_direta.exercicio = mapa.exercicio
            LEFT JOIN tceam.configuracao_arquivo_licitacao
                   ON configuracao_arquivo_licitacao.cod_mapa  = mapa.cod_mapa
                  AND configuracao_arquivo_licitacao.exercicio = mapa.exercicio
                WHERE mapa.exercicio = '".$this->getDado('exercicio')."'
                  AND to_char(compra_direta.timestamp,'mm') = '".$this->getDado('mes')."'
                  AND compra_direta.cod_entidade IN (".$this->getDado('cod_entidade').")

                ) AS registros
        ";

        return $stSql;
    }
    
    public function recuperaLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = ""){
        $obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaLicitacao().$stFiltro.$stOrdem;
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
	return $obErro;
    }

    public function montaRecuperaLicitacao(){
        $stSql = "
            SELECT processo_licitatorio
                 , num_diario_oficial  
                 , dt_publicacao_edital 
                 , cod_modalidade AS compras_modalidade 
                 , descricao_objeto
                 , total_previsto
                 , numero_edital
                 , tipo_licitacao
            FROM ( SELECT CASE WHEN licitacao.cod_modalidade = 1  THEN 'CC'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 2  THEN 'TP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 3  THEN 'CO'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 4  THEN 'LE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 5  THEN 'CP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 6  THEN 'PR'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 7  THEN 'PE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 8  THEN 'DL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 9  THEN 'IL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 10 THEN 'OT'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 11 THEN 'RP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                         END AS processo_licitatorio
                       , publicacao_edital.num_publicacao AS num_diario_oficial  
                       , TO_CHAR(publicacao_edital.data_publicacao,'YYYYMMDD') as dt_publicacao_edital
                       , CASE WHEN licitacao.cod_modalidade = 1 AND licitacao.cod_objeto = 1 THEN 1
                              WHEN licitacao.cod_modalidade = 1 AND licitacao.cod_objeto = 2 THEN 2
                              WHEN licitacao.cod_modalidade = 2 AND licitacao.cod_objeto = 1 THEN 3
                              WHEN licitacao.cod_modalidade = 2 AND licitacao.cod_objeto = 2 THEN 4
                              WHEN licitacao.cod_modalidade = 3 AND licitacao.cod_objeto = 1 THEN 5
                              WHEN licitacao.cod_modalidade = 3 AND licitacao.cod_objeto = 2 THEN 6
                              WHEN licitacao.cod_modalidade = 4 THEN 7
                              WHEN licitacao.cod_modalidade = 8 THEN 8
                              WHEN licitacao.cod_modalidade = 9 THEN 9		
                              WHEN licitacao.cod_modalidade = 5 THEN 10
                              WHEN licitacao.cod_modalidade = 7 THEN 11
                              WHEN licitacao.cod_modalidade = 6 THEN 12
                              WHEN licitacao.cod_modalidade = 3 AND licitacao.cod_objeto = 3 THEN 13
                            --WHEN licitacao.cod_modalidade = 3 AND licitacao.cod_objeto = ?? THEN 14
                              WHEN licitacao.cod_modalidade = 10 THEN 00
                         END AS cod_modalidade
                       , objeto.descricao as descricao_objeto
                       , licitacao.vl_cotado as total_previsto
                       , edital.exercicio||edital.num_edital AS numero_edital
                       , CASE WHEN licitacao.cod_tipo_licitacao = 1 THEN 'I'
                              WHEN licitacao.cod_tipo_licitacao = 2 OR licitacao.cod_tipo_licitacao = 3 THEN 'L' 
                         END AS tipo_licitacao 
                    FROM licitacao.licitacao
    
              INNER JOIN licitacao.edital
                      ON licitacao.cod_licitacao  = edital.cod_licitacao
                     AND licitacao.cod_modalidade = edital.cod_modalidade
                     AND licitacao.cod_entidade   = edital.cod_entidade
                     AND licitacao.exercicio      = edital.exercicio_licitacao
          
              INNER JOIN licitacao.publicacao_edital
                      ON edital.num_edital = publicacao_edital.num_edital
                     AND edital.exercicio  = publicacao_edital.exercicio
                      
              INNER JOIN compras.modalidade
                      ON licitacao.cod_modalidade = modalidade.cod_modalidade
              
              INNER JOIN compras.objeto
                      ON licitacao.cod_objeto = objeto.cod_objeto
            
             INNER JOIN compras.tipo_licitacao               
                     ON licitacao.cod_tipo_licitacao = tipo_licitacao.cod_tipo_licitacao

                  WHERE licitacao.exercicio = '".$this->getDado('exercicio')."'
                    AND to_char(licitacao.timestamp,'mm') = '".$this->getDado('mes')."'
                    AND licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
                     
            UNION ALL
            
            SELECT CASE WHEN licitacao.cod_modalidade = 1  THEN 'CC'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 2  THEN 'TP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 3  THEN 'CO'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 4  THEN 'LE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 5  THEN 'CP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 6  THEN 'PR'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 7  THEN 'PE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 8  THEN 'DL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 9  THEN 'IL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 10 THEN 'OT'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                               WHEN licitacao.cod_modalidade = 11 THEN 'RP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                         END AS processo_licitatorio
                       , publicacao_edital.num_publicacao AS num_diario_oficial  
                       , TO_CHAR(publicacao_edital.data_publicacao,'YYYYMMDD') as dt_publicacao_edital
		       , CASE WHEN EXISTS (
				   SELECT licitacao_anulada.cod_modalidade
				     FROM licitacao.licitacao_anulada
				    WHERE licitacao_anulada.deserta    = FALSE
				      AND licitacao_anulada.fracassada = FALSE
			    )THEN 15
			     WHEN EXISTS (
				   SELECT licitacao_anulada.cod_modalidade			
				     FROM licitacao.licitacao_anulada
				    WHERE licitacao_anulada.deserta = TRUE
			   ) THEN 16
			     ELSE 17
			 END AS cod_modalidade
                       , objeto.descricao as descricao_objeto
                       , licitacao.vl_cotado as total_previsto
                       , edital.exercicio||edital.num_edital AS numero_edital
                       , CASE WHEN licitacao.cod_tipo_licitacao = 1 THEN 'I'
                              WHEN licitacao.cod_tipo_licitacao = 2 OR licitacao.cod_tipo_licitacao = 3 THEN 'L' 
                         END AS tipo_licitacao 
                    FROM licitacao.licitacao
    
              INNER JOIN licitacao.edital
                      ON licitacao.cod_licitacao  = edital.cod_licitacao
                     AND licitacao.cod_modalidade = edital.cod_modalidade
                     AND licitacao.cod_entidade   = edital.cod_entidade
                     AND licitacao.exercicio      = edital.exercicio_licitacao
          
              INNER JOIN licitacao.publicacao_edital
                      ON edital.num_edital = publicacao_edital.num_edital
                     AND edital.exercicio  = publicacao_edital.exercicio
                      
              INNER JOIN compras.modalidade
                      ON licitacao.cod_modalidade = modalidade.cod_modalidade
              
              INNER JOIN compras.objeto
                      ON licitacao.cod_objeto = objeto.cod_objeto
            
             INNER JOIN compras.tipo_licitacao               
                     ON licitacao.cod_tipo_licitacao = tipo_licitacao.cod_tipo_licitacao

             INNER JOIN licitacao.licitacao_anulada
                     ON licitacao.cod_licitacao  = licitacao_anulada.cod_licitacao
                    AND licitacao.cod_modalidade = licitacao_anulada.cod_modalidade
                    AND licitacao.cod_entidade   = licitacao_anulada.cod_entidade
                    AND licitacao.exercicio      = licitacao_anulada.exercicio

                  WHERE licitacao.exercicio = '".$this->getDado('exercicio')."'
                    AND to_char(licitacao.timestamp,'mm') = '".$this->getDado('mes')."'
                    AND licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
            
            ) AS registros ";
            
            return $stSql;
    }
}
?>
