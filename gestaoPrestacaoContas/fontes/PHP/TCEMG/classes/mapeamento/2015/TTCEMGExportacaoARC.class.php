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
	* Classe de mapeamento do arquivo e exportação ARC
	* Data de Criação   : 29/05/2015

	* @author Analista      Ane Pereira
	* @author Desenvolvedor Arthur Cruz

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: TTCEMGExportacaoARC.class.php 62302 2015-04-20 17:54:18Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGExportacaoARC extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function TTCEMGExportacaoARC()
    {
        parent::Persistente();
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaCorrecoesReceitas10.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    function recuperaCorrecoesReceitas10(&$rsRecordSet, $boTransacao = "")
    {
	$obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaCorrecoesReceitas10();
	$this->setDebug( $stSql);
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
	return $obErro;
    }

    function montaRecuperaCorrecoesReceitas10()
    {
	$stSql = "
               SELECT tipo_registro
		    , cod_correcao
		    , cod_orgao
		    , deducao_receita
		    , indentificador_deducao_reduzida
		    , natureza_receita_reduzida
		    , especificacao_reduzida
		    , identificador_acrescida
		    , natureza_receita_acrescida
		    , especificacao_acrescida
		    , SUM(vl_reduzido_acrescido) AS vl_reduzido_acrescido
		    , cod_receita
                FROM (
                        SELECT
                        10 AS tipo_registro
                        , receita.cod_receita
                        , receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8) AS cod_correcao
                        , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                        , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9' THEN
                                1
                        ELSE
                                2
                        END AS deducao_receita
                        , valores_identificadores.cod_identificador AS indentificador_deducao_reduzida
                        , CASE WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8) = '17240101'
                               THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
                               ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
                           END AS natureza_receita_reduzida
                        , TRIM(conta_receita.descricao) AS especificacao_reduzida
                        , '' AS identificador_acrescida
                        , '' AS natureza_receita_acrescida
                        , '' AS especificacao_acrescida
                        , SUM(arrecadacao_receita.vl_arrecadacao) AS vl_reduzido_acrescido
                
                        FROM orcamento.receita
                
                        JOIN tesouraria.arrecadacao_receita
                        ON arrecadacao_receita.cod_receita=receita.cod_receita
                        AND arrecadacao_receita.exercicio=receita.exercicio
                        AND arrecadacao_receita.timestamp_arrecadacao::date BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                
                        JOIN tesouraria.arrecadacao
                        ON arrecadacao.cod_arrecadacao=arrecadacao_receita.cod_arrecadacao
                        AND arrecadacao.exercicio=arrecadacao_receita.exercicio
                        AND arrecadacao.timestamp_arrecadacao=arrecadacao_receita.timestamp_arrecadacao
                        AND arrecadacao.devolucao=true
                
                        JOIN administracao.configuracao_entidade
                        ON configuracao_entidade.cod_entidade = receita.cod_entidade
                        AND configuracao_entidade.exercicio = receita.exercicio
                
                        JOIN orcamento.conta_receita
                        ON conta_receita.cod_conta = receita.cod_conta
                        AND conta_receita.exercicio = receita.exercicio        
                
                        LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
                        ON receita_indentificadores_peculiar_receita.exercicio = receita.exercicio
                        AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
                
                        LEFT JOIN tcemg.valores_identificadores
                        ON valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador
                
                        WHERE receita.exercicio = '". $this->getDado('exercicio')."'
                        AND receita.cod_entidade IN (".$this->getDado('entidades').")
                        AND configuracao_entidade.cod_modulo = 55
                        AND configuracao_entidade.parametro = 'tcemg_tipo_orgao_entidade_sicom'
                        
                        GROUP BY receita.cod_receita
                        , receita.exercicio
                        , cod_orgao
                        , conta_receita.cod_estrutural
                        , conta_receita.descricao
                        , indentificador_deducao_reduzida
                        , natureza_receita_reduzida
                        , especificacao_reduzida
                        , identificador_acrescida
                        , natureza_receita_acrescida
                        , especificacao_acrescida	
                
                        UNION
                
                        SELECT
                        10 AS tipo_registro
                        , receita.cod_receita
                        , receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8) AS cod_correcao
                        , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                        , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9' THEN
                                1
                        ELSE
                                2
                        END AS deducao_receita
                        , valores_identificadores.cod_identificador AS indentificador_deducao_reduzida
                        , CASE WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8) = '17240101'
                               THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
                               ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
                           END AS natureza_receita_reduzida
                        , TRIM(conta_receita.descricao) AS especificacao_reduzida
                        , '' AS identificador_acrescida
                        , '' AS natureza_receita_acrescida
                        , '' AS especificacao_acrescida
                        , SUM(arrecadacao_estornada_receita.vl_estornado) AS vl_reduzido_acrescido
                
                        FROM orcamento.receita
                
                        JOIN tesouraria.arrecadacao_receita
                        ON arrecadacao_receita.cod_receita=receita.cod_receita
                        AND arrecadacao_receita.exercicio=receita.exercicio
                
                        JOIN tesouraria.arrecadacao_estornada_receita
                        ON arrecadacao_estornada_receita.cod_arrecadacao=arrecadacao_receita.cod_arrecadacao
                        AND arrecadacao_estornada_receita.cod_receita=arrecadacao_receita.cod_receita
                        AND arrecadacao_estornada_receita.exercicio=arrecadacao_receita.exercicio
                        AND arrecadacao_estornada_receita.timestamp_arrecadacao=arrecadacao_receita.timestamp_arrecadacao
                        AND arrecadacao_estornada_receita.timestamp_estornada::date BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                
                        JOIN administracao.configuracao_entidade
                        ON configuracao_entidade.cod_entidade = receita.cod_entidade
                        AND configuracao_entidade.exercicio = receita.exercicio
                
                        JOIN orcamento.conta_receita
                        ON conta_receita.cod_conta = receita.cod_conta
                        AND conta_receita.exercicio = receita.exercicio        
                
                        LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
                        ON receita_indentificadores_peculiar_receita.exercicio = receita.exercicio
                        AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
                
                        LEFT JOIN tcemg.valores_identificadores
                        ON valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador
                
                        WHERE receita.exercicio = '". $this->getDado('exercicio')."'
                        AND receita.cod_entidade IN (".$this->getDado('entidades').")
                        AND configuracao_entidade.cod_modulo = 55
                        AND configuracao_entidade.parametro = 'tcemg_tipo_orgao_entidade_sicom'
                
                        GROUP BY receita.cod_receita
                        , receita.exercicio
                        , cod_orgao
                        , conta_receita.cod_estrutural
                        , conta_receita.descricao
                        , indentificador_deducao_reduzida
                        , natureza_receita_reduzida
                        , especificacao_reduzida
                        , identificador_acrescida
                        , natureza_receita_acrescida
                        , especificacao_acrescida
                
                        UNION
                
                        SELECT
                        10 AS tipo_registro
                        , receita.cod_receita
                        , receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9) AS cod_correcao
                        , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                        , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9' THEN
                                1
                        ELSE
                                2
                        END AS deducao_receita
                        , valores_identificadores.cod_identificador AS indentificador_deducao_reduzida
                        , CASE WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8) = '17240101'
                               THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
                               ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
                           END AS natureza_receita_reduzida
                        , TRIM(conta_receita.descricao) AS especificacao_reduzida
                        , '' AS identificador_acrescida
                        , '' AS natureza_receita_acrescida
                        , '' AS especificacao_acrescida
                        , SUM(redutora.vl_deducao) AS vl_reduzido_acrescido
                
                        FROM orcamento.receita
                
                        JOIN (SELECT tabela.cod_receita_dedutora
                                , tabela.exercicio
                                , SUM(tabela.vl_arrecadacao) AS vl_deducao
                                FROM(
                                        SELECT arrecadacao_receita.cod_arrecadacao
                                        , arrecadacao_receita.cod_receita AS cod_receita_dedutora
                                        , arrecadacao_receita.exercicio
                                        , arrecadacao_receita.vl_arrecadacao
                                        FROM tesouraria.arrecadacao_receita
                                        JOIN tesouraria.arrecadacao
                                        ON arrecadacao.cod_arrecadacao=arrecadacao_receita.cod_arrecadacao
                                        AND arrecadacao.exercicio=arrecadacao_receita.exercicio
                                        AND arrecadacao.timestamp_arrecadacao=arrecadacao_receita.timestamp_arrecadacao
                                        AND arrecadacao.devolucao=false
                                        WHERE arrecadacao_receita.timestamp_arrecadacao::date BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))

                                        UNION 

                                        SELECT arrecadacao_receita_dedutora_estornada.cod_arrecadacao
                                        , arrecadacao_receita_dedutora_estornada.cod_receita_dedutora
                                        , arrecadacao_receita_dedutora_estornada.exercicio
                                        , arrecadacao_receita_dedutora_estornada.vl_estornado AS vl_arrecadacao               
                                        FROM tesouraria.arrecadacao_receita_dedutora_estornada
                                        WHERE arrecadacao_receita_dedutora_estornada.timestamp_dedutora_estornada::date BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                                ) AS tabela
                                GROUP BY tabela.cod_receita_dedutora
                                , tabela.exercicio
                                , tabela.vl_arrecadacao
                        ) AS redutora
                        ON redutora.cod_receita_dedutora=receita.cod_receita
                        AND redutora.exercicio=receita.exercicio
                        
                        JOIN administracao.configuracao_entidade
                        ON configuracao_entidade.cod_entidade = receita.cod_entidade
                        AND configuracao_entidade.exercicio = receita.exercicio
                
                        JOIN orcamento.conta_receita
                        ON conta_receita.cod_conta = receita.cod_conta
                        AND conta_receita.exercicio = receita.exercicio
                        AND SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'    
                
                        LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
                        ON receita_indentificadores_peculiar_receita.exercicio = receita.exercicio
                        AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
                
                    LEFT JOIN tcemg.valores_identificadores
                        ON valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador
                
                      WHERE receita.exercicio = '". $this->getDado('exercicio')."'
                        AND receita.cod_entidade IN (".$this->getDado('entidades').")
                        AND configuracao_entidade.cod_modulo = 55
                        AND configuracao_entidade.parametro = 'tcemg_tipo_orgao_entidade_sicom'
                
                 GROUP BY receita.cod_receita
                        , receita.exercicio
                        , cod_orgao
                        , conta_receita.cod_estrutural
                        , conta_receita.descricao
                        , indentificador_deducao_reduzida
                        , natureza_receita_reduzida
                        , especificacao_reduzida
                        , identificador_acrescida
                        , natureza_receita_acrescida
                        , especificacao_acrescida
                
                ) AS consulta
                
	     GROUP BY tipo_registro
                    , cod_receita
                    , cod_correcao
                    , cod_orgao
                    , deducao_receita
                    , indentificador_deducao_reduzida
                    , natureza_receita_reduzida
                    , especificacao_reduzida
                    , identificador_acrescida
                    , natureza_receita_acrescida
                    , especificacao_acrescida
                
             ORDER BY consulta.cod_receita ";

        return $stSql;
    }
        
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaCorrecoesReceitas11.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
	
    function recuperaCorrecoesReceitas11(&$rsRecordSet, $boTransacao = "")
    {
	$obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaCorrecoesReceitas11();
	$this->setDebug( $stSql);
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
	return $obErro;
    }
    
    function montaRecuperaCorrecoesReceitas11()
    {
	$stSql = "
	    SELECT 
	    tipo_registro
	    , cod_correcao
	    , cod_fonte_reduzida
	    , SUM(vl_reduzido_acrescido) AS vl_reduzido_fonte
	    , cod_receita
	    FROM (
		    SELECT 11 AS tipo_registro
		    , receita.cod_receita
		    , receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8) AS cod_correcao
		    , receita.cod_recurso AS cod_fonte_reduzida
		    , SUM(valor_lancamento.vl_lancamento) AS vl_reduzido_acrescido
	
		    FROM contabilidade.lancamento_receita
	
		    JOIN contabilidade.lancamento
		    ON lancamento.exercicio=lancamento_receita.exercicio
		    AND lancamento.cod_entidade=lancamento_receita.cod_entidade
		    AND lancamento.tipo=lancamento_receita.tipo
		    AND lancamento.cod_lote=lancamento_receita.cod_lote
		    AND lancamento.sequencia=lancamento_receita.sequencia
	
		    JOIN contabilidade.lote
		    ON lancamento.exercicio=lote.exercicio
		    AND lancamento.cod_entidade=lote.cod_entidade
		    AND lancamento.tipo=lote.tipo
		    AND lancamento.cod_lote=lote.cod_lote
	
		    JOIN contabilidade.valor_lancamento
		    ON lancamento.exercicio=valor_lancamento.exercicio
		    AND lancamento.cod_entidade=valor_lancamento.cod_entidade
		    AND lancamento.tipo=valor_lancamento.tipo
		    AND lancamento.cod_lote=valor_lancamento.cod_lote
		    AND lancamento.sequencia=valor_lancamento.sequencia
		    AND valor_lancamento.tipo_valor='D'
	
		    JOIN orcamento.receita
		    ON receita.cod_receita=lancamento_receita.cod_receita
		    AND receita.exercicio=lancamento_receita.exercicio
	
		    JOIN orcamento.conta_receita
		    ON receita.cod_conta=conta_receita.cod_conta
		    AND receita.exercicio=conta_receita.exercicio
		    AND SUBSTR(conta_receita.cod_estrutural, 1, 1) != '9'  	
	
		    LEFT JOIN orcamento.recurso('". $this->getDado('exercicio')."') as rec
		    ON rec.cod_recurso=receita.cod_recurso
		    AND rec.exercicio=receita.exercicio
	
		    WHERE estorno=true
		    AND lote.dt_lote BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
		    AND receita.cod_entidade IN (".$this->getDado('entidades').")
		    
		    GROUP BY tipo_registro
	
		    , receita.cod_receita
		    , receita.exercicio
		    , conta_receita.cod_estrutural
		    , receita.cod_recurso
	    
		    UNION
	    
		    SELECT
		    11 AS tipo_registro
		    , receita.cod_receita
		    , receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9) AS cod_correcao
		    , receita.cod_recurso AS cod_fonte_reduzida
		    , SUM(redutora.vl_deducao) AS vl_reduzido_acrescido
	    
		    FROM orcamento.receita
	    
		    JOIN (SELECT tabela.cod_receita_dedutora
			    , tabela.exercicio
			    , SUM(tabela.vl_arrecadacao) AS vl_deducao
			    FROM(
				    SELECT arrecadacao_receita.cod_arrecadacao
				    , arrecadacao_receita.cod_receita AS cod_receita_dedutora
				    , arrecadacao_receita.exercicio
				    , arrecadacao_receita.vl_arrecadacao
				    FROM tesouraria.arrecadacao_receita
				    JOIN tesouraria.arrecadacao
				    ON arrecadacao.cod_arrecadacao=arrecadacao_receita.cod_arrecadacao
				    AND arrecadacao.exercicio=arrecadacao_receita.exercicio
				    AND arrecadacao.timestamp_arrecadacao=arrecadacao_receita.timestamp_arrecadacao
				    AND arrecadacao.devolucao=false
				    WHERE arrecadacao_receita.timestamp_arrecadacao::date BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
	
				    UNION 
	
				    SELECT arrecadacao_receita_dedutora_estornada.cod_arrecadacao
				    , arrecadacao_receita_dedutora_estornada.cod_receita_dedutora
				    , arrecadacao_receita_dedutora_estornada.exercicio
				    , arrecadacao_receita_dedutora_estornada.vl_estornado AS vl_arrecadacao               
				    FROM tesouraria.arrecadacao_receita_dedutora_estornada
				    WHERE arrecadacao_receita_dedutora_estornada.timestamp_dedutora_estornada::date BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
			    ) AS tabela
			    GROUP BY tabela.cod_receita_dedutora
			    , tabela.exercicio
			    , tabela.vl_arrecadacao
		    ) AS redutora
		    ON redutora.cod_receita_dedutora=receita.cod_receita
		    AND redutora.exercicio=receita.exercicio
		    
		    JOIN administracao.configuracao_entidade
		    ON configuracao_entidade.cod_entidade = receita.cod_entidade
		    AND configuracao_entidade.exercicio = receita.exercicio
	    
		    JOIN orcamento.conta_receita
		    ON conta_receita.cod_conta = receita.cod_conta
		    AND conta_receita.exercicio = receita.exercicio
		    AND SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'  
	    
		    LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
		    ON receita_indentificadores_peculiar_receita.exercicio = receita.exercicio
		    AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
	    
		    LEFT JOIN tcemg.valores_identificadores
		    ON valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador
	    
		    WHERE receita.exercicio = '". $this->getDado('exercicio')."'
		    AND receita.cod_entidade IN (".$this->getDado('entidades').")
		    AND configuracao_entidade.cod_modulo = 55
		    AND configuracao_entidade.parametro = 'tcemg_tipo_orgao_entidade_sicom'
	    
		    GROUP BY receita.cod_receita
		    , receita.exercicio
		    , conta_receita.cod_estrutural
	    
	    ) AS consulta
	    GROUP BY
	    tipo_registro
	    , cod_receita
	    , cod_correcao
	    , cod_fonte_reduzida   
	    
	    ORDER BY consulta.cod_receita
	";
	return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaCorrecoesReceitas12.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    function recuperaCorrecoesReceitas12(&$rsRecordSet, $boTransacao = "")
    {
	$obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaCorrecoesReceitas12();
	$this->setDebug( $stSql);
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
	return $obErro;
    }
    
    function montaRecuperaCorrecoesReceitas12()
    {
	$stSql = "
	SELECT 12 AS tipo_registro
	     , receita.cod_receita||''||receita.cod_recurso||receita.cod_entidade||receita.exercicio AS cod_correcao
	     , 0 AS cod_fonte_acrescida
	     , 0 AS vl_acrescido_fonte
	  FROM orcamento.receita
	  JOIN orcamento.previsao_receita
	    ON previsao_receita.cod_receita = receita.cod_receita
	   AND previsao_receita.exercicio   = receita.exercicio
	 WHERE receita.exercicio = '".Sessao::getExercicio()."'
	   AND receita.cod_entidade IN (".$this->getDado('entidades').")
      GROUP BY receita.cod_receita
	     , receita.cod_recurso
	     , receita.cod_entidade
	     , receita.exercicio
	";
    
	return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaCorrecoesReceitas20.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    function recuperaCorrecoesReceitas20(&$rsRecordSet, $boTransacao = "")
    {
	$obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaCorrecoesReceitas20();
	$this->setDebug( $stSql);
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
	return $obErro;
    }
    
    function montaRecuperaCorrecoesReceitas20()
    {
	$stSql = "
	
	SELECT tipo_registro
		 , '20'||cod_correcao AS cod_estorno
		 , cod_orgao
		 , deducao_receita
		 , indentificador_deducao_reduzida AS identificador_deducao
		 , natureza_receita_reduzida AS natureza_receita_estornada
		 , ( SELECT sem_acentos(descricao) as descricao
				FROM orcamento.conta_receita
			   WHERE REPLACE(conta_receita.cod_estrutural, '.', '')::TEXT = CASE 
										WHEN SUBSTR(natureza_receita_reduzida::TEXT, 1, 1) = '9' THEN RPAD(natureza_receita_reduzida::TEXT, 15, '0')
										ELSE RPAD(natureza_receita_reduzida::TEXT, 14, '0') END
				  AND exercicio = '".$this->getDado('exercicio')."'
	        ) AS especificacao_estornada
	     , SUM(vl_reduzido_acrescido) AS vl_estornado
	     , cod_correcao
	 FROM (
		SELECT 20 AS tipo_registro
		    , receita.cod_receita
		    , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
		    , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9' THEN 1
				   ELSE 2
		      END AS deducao_receita
		    , valores_identificadores.cod_identificador AS indentificador_deducao_reduzida
			, CASE
				WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
					THEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer
                ELSE
					CASE
						WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
							OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
							OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17219903
							OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 19319902
								THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
						WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 24210100
							THEN '24210101'
                    ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
                    END
              END AS natureza_receita_reduzida   
		    , SUM(arrecadacao_receita.vl_arrecadacao) AS vl_reduzido_acrescido
		    , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
						THEN receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer 
					ELSE CASE
							WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17219903
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 19319902
									THEN receita.exercicio||RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
							WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 24210100
								THEN receita.exercicio||'24210101'
							ELSE receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
							END
               END AS cod_correcao
	
		 FROM orcamento.receita
	
		INNER JOIN tesouraria.arrecadacao_receita
			ON arrecadacao_receita.cod_receita = receita.cod_receita
		       AND arrecadacao_receita.exercicio   = receita.exercicio
		       AND arrecadacao_receita.timestamp_arrecadacao::date BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
	
		INNER JOIN tesouraria.arrecadacao
			ON arrecadacao.cod_arrecadacao       = arrecadacao_receita.cod_arrecadacao
		       AND arrecadacao.exercicio             = arrecadacao_receita.exercicio
		       AND arrecadacao.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao
		       AND arrecadacao.devolucao             = true
	
		INNER JOIN administracao.configuracao_entidade
			ON configuracao_entidade.cod_entidade = receita.cod_entidade
		       AND configuracao_entidade.exercicio    = receita.exercicio
	
		INNER JOIN orcamento.conta_receita
			ON conta_receita.cod_conta = receita.cod_conta
		       AND conta_receita.exercicio = receita.exercicio        
	
		LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
		       ON receita_indentificadores_peculiar_receita.exercicio   = receita.exercicio
		      AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
	
		LEFT JOIN tcemg.valores_identificadores
		       ON valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador
	
		    WHERE receita.exercicio     = '". $this->getDado('exercicio')."'
		      AND receita.cod_entidade IN (".$this->getDado('entidades').")
		      AND configuracao_entidade.cod_modulo = 55
		      AND configuracao_entidade.parametro  = 'tcemg_tipo_orgao_entidade_sicom'
		
		 GROUP BY receita.cod_receita
			, receita.exercicio
			, cod_orgao
			, deducao_receita
			, indentificador_deducao_reduzida
			, natureza_receita_reduzida
			, cod_correcao
	
		UNION
	
		  SELECT 20 AS tipo_registro
		       , receita.cod_receita
		       , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
		       , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9' THEN 1
					  ELSE 2
				 END AS deducao_receita
		       , valores_identificadores.cod_identificador AS indentificador_deducao_reduzida
		       , CASE
					WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
						THEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer
					ELSE
						CASE
							WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17219903
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 19319902
									THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
							WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 24210100
								THEN '24210101'
						ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
						END
				 END AS natureza_receita_reduzida  
		       , SUM(arrecadacao_estornada_receita.vl_estornado) AS vl_reduzido_acrescido
		       , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
						THEN receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer 
					ELSE CASE
							WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17219903
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 19319902
									THEN receita.exercicio||RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
							WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 24210100
								THEN receita.exercicio||'24210101'
							ELSE receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
							END
				 END AS cod_correcao
	
		    FROM orcamento.receita
	
	      INNER JOIN tesouraria.arrecadacao_receita
		      ON arrecadacao_receita.cod_receita = receita.cod_receita
		     AND arrecadacao_receita.exercicio   = receita.exercicio
	
	      INNER JOIN tesouraria.arrecadacao_estornada_receita
		      ON arrecadacao_estornada_receita.cod_arrecadacao       = arrecadacao_receita.cod_arrecadacao
		     AND arrecadacao_estornada_receita.cod_receita           = arrecadacao_receita.cod_receita
		     AND arrecadacao_estornada_receita.exercicio             = arrecadacao_receita.exercicio
		     AND arrecadacao_estornada_receita.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao
		     AND arrecadacao_estornada_receita.timestamp_estornada::date BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
	
	      INNER JOIN administracao.configuracao_entidade
		      ON configuracao_entidade.cod_entidade = receita.cod_entidade
		     AND configuracao_entidade.exercicio    = receita.exercicio
	
	      INNER JOIN orcamento.conta_receita
		      ON conta_receita.cod_conta = receita.cod_conta
		     AND conta_receita.exercicio = receita.exercicio
		     AND SUBSTR(conta_receita.cod_estrutural, 1, 1) != '9'
	
	       LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
		      ON receita_indentificadores_peculiar_receita.exercicio   = receita.exercicio
		     AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
	
	       LEFT JOIN tcemg.valores_identificadores
		      ON valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador
	
		   WHERE receita.exercicio    = '". $this->getDado('exercicio')."'
		     AND receita.cod_entidade IN (".$this->getDado('entidades').")
		     AND configuracao_entidade.cod_modulo = 55
		     AND configuracao_entidade.parametro  = 'tcemg_tipo_orgao_entidade_sicom'
		   --and receita.vl_original > 0
	
	      GROUP BY receita.cod_receita
		      , receita.exercicio
		      , cod_orgao
		      , deducao_receita
		      , indentificador_deducao_reduzida
		      , natureza_receita_reduzida
		      , cod_correcao
		
		UNION
	
		 SELECT 20 AS tipo_registro
		      , receita.cod_receita
		      , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
		      , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9' THEN 1
					 ELSE 2
				END AS deducao_receita
		      , valores_identificadores.cod_identificador AS indentificador_deducao_reduzida
		      , CASE
					WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
						THEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer
					ELSE
						CASE
							WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17219903
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 19319902
									THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
							WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 24210100
							THEN '24210101'
						ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
						END
				END AS natureza_receita_reduzida  
		      , SUM(redutora.vl_deducao) AS vl_reduzido_acrescido
		      , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
						THEN receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer 
					ELSE CASE
							WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17219903
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 19319902
									THEN receita.exercicio||RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
							WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 24210100
								THEN receita.exercicio||'24210101'
							ELSE receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
							END
               END AS cod_correcao
		   FROM orcamento.receita
		INNER JOIN (
					SELECT tabela.cod_receita_dedutora
						 , tabela.exercicio
						 , SUM(tabela.vl_arrecadacao) AS vl_deducao
					 FROM (
						SELECT arrecadacao_receita_dedutora_estornada.cod_arrecadacao
							 , arrecadacao_receita_dedutora_estornada.cod_receita_dedutora
							 , arrecadacao_receita_dedutora_estornada.exercicio
							 , arrecadacao_receita_dedutora_estornada.vl_estornado AS vl_arrecadacao               
						 FROM tesouraria.arrecadacao_receita_dedutora_estornada
				        WHERE arrecadacao_receita_dedutora_estornada.timestamp_dedutora_estornada::date BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
							AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
					    ) AS tabela
			    GROUP BY tabela.cod_receita_dedutora
						, tabela.exercicio
						, tabela.vl_arrecadacao
		      ) AS redutora
			ON redutora.cod_receita_dedutora = receita.cod_receita
		       AND redutora.exercicio            = receita.exercicio
      
		INNER JOIN administracao.configuracao_entidade
			ON configuracao_entidade.cod_entidade = receita.cod_entidade
		       AND configuracao_entidade.exercicio    = receita.exercicio
	
		INNER JOIN orcamento.conta_receita
			ON conta_receita.cod_conta = receita.cod_conta
		       AND conta_receita.exercicio = receita.exercicio
		       AND SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'   
	
		LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
		       ON receita_indentificadores_peculiar_receita.exercicio   = receita.exercicio
		      AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
	
		LEFT JOIN tcemg.valores_identificadores
		       ON valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador
	
		    WHERE receita.exercicio    = '". $this->getDado('exercicio')."'
		      AND receita.cod_entidade IN (".$this->getDado('entidades').")
		      AND configuracao_entidade.cod_modulo = 55
		      AND configuracao_entidade.parametro  = 'tcemg_tipo_orgao_entidade_sicom'
	
		 GROUP BY tipo_registro
			, receita.cod_receita
			, cod_orgao
			, deducao_receita
			, indentificador_deducao_reduzida
			, natureza_receita_reduzida
			, cod_correcao
		  
	     ) AS consulta
      
      GROUP BY tipo_registro
	     , cod_orgao
	     , deducao_receita
	     , indentificador_deducao_reduzida
	     , natureza_receita_reduzida
	     , especificacao_estornada
	     , cod_correcao
	
      ORDER BY consulta.natureza_receita_reduzida ";
    
	return $stSql;
    }
	
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaCorrecoesReceitas21.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    function recuperaCorrecoesReceitas21(&$rsRecordSet, $boTransacao = "")
    {
	$obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaCorrecoesReceitas21();
	$this->setDebug( $stSql);
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
	return $obErro;
    }
    
    function montaRecuperaCorrecoesReceitas21()
    {
	$stSql = "
	SELECT tipo_registro
	     , '20'||cod_correcao AS cod_estorno
	     , SUM(vl_reduzido_acrescido) AS vl_estornado_fonte
	     , cod_receita
	     , cod_fonte_reduzida AS cod_fonte_estornada
	     , cod_correcao
	FROM (
		SELECT 21 AS tipo_registro
		    , receita.cod_receita
		    , receita.cod_recurso AS cod_fonte_reduzida
		    , SUM(valor_lancamento.vl_lancamento) AS vl_reduzido_acrescido
		    , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
						THEN receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer 
					ELSE CASE
							WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17219903
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 19319902
									THEN receita.exercicio||RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
							WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 24210100
								THEN receita.exercicio||'24210101'
							ELSE receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
							END
               END AS cod_correcao
		       
		 FROM contabilidade.lancamento_receita
    
	   INNER JOIN contabilidade.lancamento
		   ON lancamento.exercicio    = lancamento_receita.exercicio
		  AND lancamento.cod_entidade = lancamento_receita.cod_entidade
		  AND lancamento.tipo         = lancamento_receita.tipo
		  AND lancamento.cod_lote     = lancamento_receita.cod_lote
		  AND lancamento.sequencia    = lancamento_receita.sequencia
    
	   INNER JOIN contabilidade.lote
		   ON lancamento.exercicio    = lote.exercicio
		  AND lancamento.cod_entidade = lote.cod_entidade
		  AND lancamento.tipo         = lote.tipo
		  AND lancamento.cod_lote     = lote.cod_lote
    
	   INNER JOIN contabilidade.valor_lancamento
		   ON lancamento.exercicio        = valor_lancamento.exercicio
		  AND lancamento.cod_entidade     = valor_lancamento.cod_entidade
		  AND lancamento.tipo             = valor_lancamento.tipo
		  AND lancamento.cod_lote         = valor_lancamento.cod_lote
		  AND lancamento.sequencia        = valor_lancamento.sequencia
		  AND valor_lancamento.tipo_valor = 'D'
    
	   INNER JOIN orcamento.receita
		   ON receita.cod_receita = lancamento_receita.cod_receita
		  AND receita.exercicio   = lancamento_receita.exercicio
    
	   INNER JOIN orcamento.conta_receita
		   ON receita.cod_conta = conta_receita.cod_conta
		  AND receita.exercicio = conta_receita.exercicio
		  AND SUBSTR(conta_receita.cod_estrutural, 1, 1) != '9'  	
    
	    LEFT JOIN orcamento.recurso('". $this->getDado('exercicio')."') as rec
		   ON rec.cod_recurso = receita.cod_recurso
		  AND rec.exercicio   = receita.exercicio
    
	        WHERE estorno = true
		  AND lote.dt_lote BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
		  AND receita.cod_entidade IN (".$this->getDado('entidades').")
		
	     GROUP BY tipo_registro
		    , receita.cod_receita
		    , receita.exercicio
		    , conta_receita.cod_estrutural
		    , receita.cod_recurso
		    , cod_correcao
	
	    UNION
	
		SELECT 21 AS tipo_registro
		     , receita.cod_receita
		     , receita.cod_recurso AS cod_fonte_reduzida
		     , SUM(redutora.vl_deducao) AS vl_reduzido_acrescido
		     , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
						THEN receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer 
					ELSE CASE
							WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17219903
								OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 19319902
									THEN receita.exercicio||RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
							WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 24210100
								THEN receita.exercicio||'24210101'
							ELSE receita.exercicio||SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
							END
               END AS cod_correcao
		FROM orcamento.receita
	
	  INNER JOIN (
				SELECT tabela.cod_receita_dedutora
					 , tabela.exercicio
					 , SUM(tabela.vl_arrecadacao) AS vl_deducao
      			 FROM (
					SELECT arrecadacao_receita_dedutora_estornada.cod_arrecadacao
						, arrecadacao_receita_dedutora_estornada.cod_receita_dedutora
						, arrecadacao_receita_dedutora_estornada.exercicio
						, arrecadacao_receita_dedutora_estornada.vl_estornado AS vl_arrecadacao               
				    FROM tesouraria.arrecadacao_receita_dedutora_estornada
				   WHERE arrecadacao_receita_dedutora_estornada.timestamp_dedutora_estornada::date BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
						AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
			        ) AS tabela
			 GROUP BY tabela.cod_receita_dedutora
					, tabela.exercicio
					, tabela.vl_arrecadacao
		     ) AS redutora
		    ON redutora.cod_receita_dedutora=receita.cod_receita
		   AND redutora.exercicio=receita.exercicio
		
	  INNER JOIN administracao.configuracao_entidade
		  ON configuracao_entidade.cod_entidade = receita.cod_entidade
		 AND configuracao_entidade.exercicio    = receita.exercicio
	
	  INNER JOIN orcamento.conta_receita
		  ON conta_receita.cod_conta = receita.cod_conta
		 AND conta_receita.exercicio = receita.exercicio
		 AND SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'  
	
	   LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
		  ON receita_indentificadores_peculiar_receita.exercicio = receita.exercicio
		 AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
	
	   LEFT JOIN tcemg.valores_identificadores
		  ON valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador
	
	       WHERE receita.exercicio    = '". $this->getDado('exercicio')."'
		 AND receita.cod_entidade IN (".$this->getDado('entidades').")
		 AND configuracao_entidade.cod_modulo = 55
		 AND configuracao_entidade.parametro = 'tcemg_tipo_orgao_entidade_sicom'
	
	    GROUP BY receita.cod_receita
		   , receita.exercicio
		   , conta_receita.cod_estrutural
	
	    ) AS consulta
    
    GROUP BY tipo_registro
	   , cod_receita
	   , cod_correcao
	   , cod_fonte_reduzida
	
    ORDER BY consulta.cod_receita ";
    
	return $stSql;
    }
    
    public function __destruct(){}
    
}

?>