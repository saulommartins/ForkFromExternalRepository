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
    * Extensão da Classe de Mapeamento TTCETOEmpenho
    *
    * Data de Criação: 11/11/2014
    *
    * @author: Lisiane Morais
    *
    $Id:$
    *
    * @ignore
    *
*/
class TTCETOEmpenho extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCETOEmpenho()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaCredor.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaEmpenho(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaEmpenho().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaEmpenho()
    {       
        $stSql = "  SELECT  Cod_Und_Gestora
                            , bimestre
                            , exercicio
	                        , num_orgao
                            , num_unidade
                            , cod_funcao
                            , cod_subfuncao
                            , cod_programa
                            , num_acao AS projeto_atividade
                            , cod_conta_contabil
                            , cod_estrutural AS rubrica_despesa
                            , cod_recurso AS recurso_vinculado
                            , cod_credor
                            , num_empenho
                            , dt_empenho
                            , vl_empenho
                            , sinal
                            , historico
                            , contra_partida
                            , modal_licita
                            , carac_peculiar
                            , num_processo
                            , num_contrato
                            , dt_contrato
                            , num_convenio
                            , num_obra
                            , tipo
                    FROM ( SELECT empenho.cod_empenho
                                , (SELECT PJ.cnpj
                                     FROM orcamento.entidade
                                     JOIN sw_cgm
                                       ON sw_cgm.numcgm = entidade.numcgm
                                     JOIN sw_cgm_pessoa_juridica AS PJ
                                       ON sw_cgm.numcgm = PJ.numcgm
                                    WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                      AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                                  ) AS Cod_Und_Gestora
                                , ".$this->getDado('bimestre')." AS bimestre
                                , '".$this->getDado('exercicio')."' AS exercicio
                                , (empenho.exercicio::varchar ||  LPAD(empenho.cod_empenho::VARCHAR, 9, '0')) AS num_empenho
                                , TO_CHAR(empenho.dt_empenho,'yyyy-mm-dd') AS dt_empenho
                                , (SELECT SUM(vl_total)::VARCHAR
                                     FROM empenho.item_pre_empenho
                                    WHERE exercicio = pre_empenho.exercicio
                                      AND cod_pre_empenho = empenho.cod_pre_empenho
                                  ) AS vl_empenho
                                , '+' AS sinal
                                , CASE WHEN tipo_empenho.cod_tipo = 2 THEN 1
                                       WHEN tipo_empenho.cod_tipo = 3 THEN 2
                                       WHEN tipo_empenho.cod_tipo = 1 THEN 3
                                  END AS tipo
                                , LPAD(despesa.num_orgao::VARCHAR, 2, '0') AS num_orgao
                                , LPAD(despesa.num_unidade::VARCHAR, 4 ,'0') AS num_unidade
                                , LPAD(funcao.cod_funcao::VARCHAR, 2, '0') AS cod_funcao
                                , LPAD(subfuncao.cod_subfuncao::VARCHAR, 3, '0') AS cod_subfuncao
                                , LPAD(ppa_programa.num_programa::VARCHAR, 4, '0') AS cod_programa
                                , REPLACE(conta_despesa.cod_estrutural, '.', '') AS cod_estrutural
                                , '6221301000000' AS cod_conta_contabil
                                , recurso.cod_recurso AS cod_recurso
                                , '0' AS contra_partida
                                , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL
                                       THEN sw_cgm_pessoa_fisica.cpf
                                       ELSE sw_cgm_pessoa_juridica.cnpj
                                  END AS cod_credor
                                , CASE WHEN atributo_empenho_valor.valor = '1'  THEN '11'
                                       WHEN atributo_empenho_valor.valor = '2'  THEN '03'
                                       WHEN atributo_empenho_valor.valor = '3'  THEN '04'
                                       WHEN atributo_empenho_valor.valor = '4'  THEN '05'
                                       WHEN atributo_empenho_valor.valor = '5'  THEN '01'
                                       WHEN atributo_empenho_valor.valor = '6'  THEN '02'
                                       WHEN atributo_empenho_valor.valor = '7'  THEN '99'
                                       WHEN atributo_empenho_valor.valor = '11' THEN '07'
                                       WHEN atributo_empenho_valor.valor = '12' THEN '08'
                                       WHEN atributo_empenho_valor.valor = '14' THEN '06'
                                  END AS modal_licita
                                , '0' AS num_processo
                                , '0' AS num_contrato
                                , '0' AS dt_contrato
                                , '0' AS num_convenio
                                , '0' AS num_obra
                                , CASE WHEN atributo_peculiar.cod_valor = 1 THEN '000' ELSE '000' END AS carac_peculiar
                                , TRIM(pre_empenho.descricao) AS historico
                                , acao.num_acao
                                  
                            FROM empenho.empenho
                              
                            JOIN empenho.pre_empenho
                                 ON pre_empenho.exercicio = empenho.exercicio
                                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                            
                            JOIN empenho.item_pre_empenho
                                 ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                AND item_pre_empenho.exercicio = pre_empenho.exercicio
                            
                            LEFT JOIN empenho.empenho_anulado
                                 ON empenho_anulado.exercicio = empenho.exercicio
                                AND empenho_anulado.cod_entidade = empenho.cod_entidade
                                AND empenho_anulado.cod_empenho = empenho.cod_empenho
                            
                            LEFT JOIN empenho.empenho_anulado_item
                                 ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                                AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                                AND empenho_anulado_item.num_item = item_pre_empenho.num_item
                            
                            JOIN empenho.pre_empenho_despesa
                                 ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                            
                               JOIN orcamento.despesa
                                 ON despesa.exercicio = pre_empenho_despesa.exercicio
                                AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                            
                               JOIN orcamento.recurso
                                 ON recurso.exercicio = despesa.exercicio
                                AND recurso.cod_recurso = despesa.cod_recurso
                            
                            JOIN orcamento.conta_despesa
                                 ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                                AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                            
                            JOIN empenho.tipo_empenho
                                 ON tipo_empenho.cod_tipo = pre_empenho.cod_tipo
                             
                            JOIN orcamento.funcao
                                 ON funcao.cod_funcao = despesa.cod_funcao
                                AND funcao.exercicio = despesa.exercicio
                            
                            JOIN orcamento.subfuncao
                                 ON subfuncao.exercicio = despesa.exercicio
                                AND subfuncao.cod_subfuncao = despesa.cod_subfuncao
                            
                            JOIN empenho.atributo_empenho_valor
                                 ON atributo_empenho_valor.exercicio = pre_empenho.exercicio
                                AND atributo_empenho_valor.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                AND atributo_empenho_valor.cod_atributo = 101
                                AND atributo_empenho_valor.timestamp = (SELECT MAX(timestamp)
                                                                       FROM empenho.atributo_empenho_valor AS aev
								      WHERE aev.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho
									AND aev.exercicio = atributo_empenho_valor.exercicio )
                            JOIN administracao.atributo_valor_padrao
                                 ON atributo_valor_padrao.cod_modulo = atributo_empenho_valor.cod_modulo
                                AND atributo_valor_padrao.cod_cadastro = atributo_empenho_valor.cod_cadastro
                                AND atributo_valor_padrao.cod_atributo = atributo_empenho_valor.cod_atributo
                                AND atributo_valor_padrao.cod_valor::VARCHAR = atributo_empenho_valor.valor

                            LEFT JOIN (SELECT atributo_empenho_valor.exercicio
                                            , atributo_empenho_valor.cod_pre_empenho
                                            , atributo_empenho_valor.cod_cadastro
                                            , atributo_empenho_valor.cod_modulo
                                            , COALESCE(atributo_valor_padrao.cod_valor, 0) AS cod_valor
                                    FROM administracao.atributo_valor_padrao
                                    JOIN empenho.atributo_empenho_valor
                                         ON atributo_valor_padrao.cod_modulo   = atributo_empenho_valor.cod_modulo
                                        AND atributo_valor_padrao.cod_cadastro = atributo_empenho_valor.cod_cadastro
                                        AND atributo_valor_padrao.cod_atributo = atributo_empenho_valor.cod_atributo
                                        AND atributo_valor_padrao.cod_valor::VARCHAR = atributo_empenho_valor.valor
                                    WHERE atributo_valor_padrao.cod_atributo = 101
                            ) AS atributo_peculiar
                                 ON atributo_peculiar.cod_cadastro    = atributo_empenho_valor.cod_cadastro
                                AND atributo_peculiar.cod_modulo      = atributo_empenho_valor.cod_modulo
                                AND atributo_peculiar.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho
                                AND atributo_peculiar.exercicio       = atributo_empenho_valor.exercicio
                          
                            JOIN sw_cgm
                                 ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario
                                 
                            LEFT JOIN sw_cgm_pessoa_fisica                                                                             
                                 ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
                               
                            LEFT JOIN sw_cgm_pessoa_juridica                                                                            
                                 ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm
                                 
                            JOIN orcamento.programa AS orcamento_programa
                                 ON orcamento_programa.exercicio = despesa.exercicio
                                AND orcamento_programa.cod_programa = despesa.cod_programa
                      
                            JOIN orcamento.pao_ppa_acao
                                 ON pao_ppa_acao.num_pao = despesa.num_pao
                                AND pao_ppa_acao.exercicio = despesa.exercicio
                      
                            JOIN orcamento.programa_ppa_programa
                                 ON programa_ppa_programa.exercicio = orcamento_programa.exercicio
                                AND programa_ppa_programa.cod_programa = orcamento_programa.cod_programa
                      
                            JOIN ppa.programa AS ppa_programa
                                 ON ppa_programa.cod_programa = programa_ppa_programa.cod_programa
                      
                            JOIN ppa.acao
                                 ON pao_ppa_acao.cod_acao = acao.cod_acao

                            WHERE empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
                            AND empenho.dt_empenho BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                            AND atributo_empenho_valor.cod_atributo <> 2001
                             
                            GROUP BY empenho.cod_empenho,cod_und_gestora,num_empenho,dt_empenho,vl_empenho,tipo,carac_peculiar,
                                num_orgao, num_unidade, funcao.cod_funcao, subfuncao.cod_subfuncao, ppa_programa.num_programa, conta_despesa.cod_estrutural,
                                recurso.cod_recurso, cod_credor, modal_licita,  pre_empenho.cod_pre_empenho, atributo_empenho_valor.valor, historico,
                                num_acao, acao.cod_programa
                         
                    UNION ALL

                            SELECT empenho.cod_empenho
                                , (SELECT PJ.cnpj
                                     FROM orcamento.entidade
                                     JOIN sw_cgm
                                       ON sw_cgm.numcgm = entidade.numcgm
                                     JOIN sw_cgm_pessoa_juridica AS PJ
                                       ON sw_cgm.numcgm = PJ.numcgm
                                    WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                      AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                                  ) AS Cod_Und_Gestora
                                , ".$this->getDado('bimestre')." AS bimestre
                                , '".$this->getDado('exercicio')."' AS exercicio
                                , (empenho.exercicio::varchar || LPAD(empenho.cod_empenho::VARCHAR, 9, '0')) AS num_empenho
                                , TO_CHAR(empenho_anulado_item.timestamp,'yyyy-mm-dd') AS dt_empenho
                                , empenho_anulado_item.vl_anulado::varchar AS vl_empenho
                                , '-' AS sinal
                                , CASE WHEN tipo_empenho.cod_tipo = 2 THEN 1
                                       WHEN tipo_empenho.cod_tipo = 3 THEN 2
                                       WHEN tipo_empenho.cod_tipo = 1 THEN 3
                                  END AS tipo
                                , LPAD(despesa.num_orgao::VARCHAR, 2, '0') AS num_orgao
                                , LPAD(despesa.num_unidade::VARCHAR, 4 ,'0') AS num_unidade
                                , LPAD(funcao.cod_funcao::VARCHAR, 2, '0') AS cod_funcao
                                , LPAD(subfuncao.cod_subfuncao::VARCHAR, 3, '0') AS cod_subfuncao
                                , LPAD(ppa_programa.num_programa::VARCHAR, 4, '0') AS cod_programa
                                , REPLACE(conta_despesa.cod_estrutural, '.', '') AS cod_estrutural
                                , '6221301000000' AS cod_conta_contabil
                                ,  recurso.cod_recurso AS cod_recurso
                                , '0' AS contra_partida
                                , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL
                                       THEN sw_cgm_pessoa_fisica.cpf
                                       ELSE sw_cgm_pessoa_juridica.cnpj
                                  END AS cod_credor
                                , CASE WHEN atributo_empenho_valor.valor = '1'  THEN '11'
                                       WHEN atributo_empenho_valor.valor = '2'  THEN '03'
                                       WHEN atributo_empenho_valor.valor = '3'  THEN '04'
                                       WHEN atributo_empenho_valor.valor = '4'  THEN '05'
                                       WHEN atributo_empenho_valor.valor = '5'  THEN '01'
                                       WHEN atributo_empenho_valor.valor = '6'  THEN '02'
                                       WHEN atributo_empenho_valor.valor = '7'  THEN '99'
                                       WHEN atributo_empenho_valor.valor = '11' THEN '07'
                                       WHEN atributo_empenho_valor.valor = '12' THEN '08'
                                       WHEN atributo_empenho_valor.valor = '14' THEN '06'
                                  END AS modal_licita
                                , '0' AS num_processo
                                , '0' AS num_contrato
                                , '0' AS dt_contrato
                                , '0' AS num_convenio
                                , '0' AS num_obra
                                , CASE WHEN atributo_peculiar.cod_valor = 1 THEN '000' ELSE '000' END AS carac_peculiar
                                , TRIM(pre_empenho.descricao) AS historico
                                , acao.num_acao
                                
                            FROM empenho.empenho
                            
                            JOIN empenho.pre_empenho
                              ON pre_empenho.exercicio = empenho.exercicio
                             AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                          
                            JOIN empenho.item_pre_empenho
                              ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND item_pre_empenho.exercicio = pre_empenho.exercicio
                            
                            JOIN empenho.empenho_anulado
                              ON empenho_anulado.exercicio = empenho.exercicio
                             AND empenho_anulado.cod_entidade = empenho.cod_entidade
                             AND empenho_anulado.cod_empenho = empenho.cod_empenho
                            
                            JOIN empenho.empenho_anulado_item
                              ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                             AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                             AND empenho_anulado_item.num_item = item_pre_empenho.num_item
                            
                            JOIN empenho.pre_empenho_despesa
                              ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                            
                            JOIN orcamento.despesa
                              ON despesa.exercicio = pre_empenho_despesa.exercicio
                             AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                            
                            JOIN orcamento.recurso
                              ON recurso.exercicio = despesa.exercicio
                             AND recurso.cod_recurso = despesa.cod_recurso
                            
                            JOIN orcamento.conta_despesa
                              ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                             AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                          
                            JOIN empenho.tipo_empenho
                              ON tipo_empenho.cod_tipo = pre_empenho.cod_tipo
                             
                            JOIN orcamento.funcao
                              ON funcao.cod_funcao = despesa.cod_funcao
                             AND funcao.exercicio = despesa.exercicio
                            
                            JOIN orcamento.subfuncao
                              ON subfuncao.exercicio = despesa.exercicio
                             AND subfuncao.cod_subfuncao = despesa.cod_subfuncao
                            
                            JOIN empenho.atributo_empenho_valor
                              ON atributo_empenho_valor.exercicio = pre_empenho.exercicio
                             AND atributo_empenho_valor.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND atributo_empenho_valor.cod_atributo = 101
                             AND atributo_empenho_valor.timestamp = (SELECT MAX(timestamp) FROM empenho.atributo_empenho_valor AS aev
									  WHERE aev.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho
									    AND aev.exercicio = atributo_empenho_valor.exercicio )
                               
                            JOIN administracao.atributo_valor_padrao
                              ON atributo_valor_padrao.cod_modulo = atributo_empenho_valor.cod_modulo
                             AND atributo_valor_padrao.cod_cadastro = atributo_empenho_valor.cod_cadastro
                             AND atributo_valor_padrao.cod_atributo = atributo_empenho_valor.cod_atributo
                             AND atributo_valor_padrao.cod_valor::VARCHAR = atributo_empenho_valor.valor
                             
                            LEFT JOIN (SELECT atributo_empenho_valor.exercicio
                                       , atributo_empenho_valor.cod_pre_empenho
                                       , atributo_empenho_valor.cod_cadastro
                                       , atributo_empenho_valor.cod_modulo
                                       , COALESCE(atributo_valor_padrao.cod_valor, 0) AS cod_valor
                                    FROM administracao.atributo_valor_padrao
                  
                               JOIN empenho.atributo_empenho_valor
                                      ON atributo_valor_padrao.cod_modulo   = atributo_empenho_valor.cod_modulo
                                     AND atributo_valor_padrao.cod_cadastro = atributo_empenho_valor.cod_cadastro
                                     AND atributo_valor_padrao.cod_atributo = atributo_empenho_valor.cod_atributo
                                     AND atributo_valor_padrao.cod_valor::VARCHAR = atributo_empenho_valor.valor
                                  
                                   WHERE atributo_valor_padrao.cod_atributo = 101
                               ) AS atributo_peculiar
                              ON atributo_peculiar.cod_cadastro    = atributo_empenho_valor.cod_cadastro
                             AND atributo_peculiar.cod_modulo      = atributo_empenho_valor.cod_modulo
                             AND atributo_peculiar.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho
                             AND atributo_peculiar.exercicio       = atributo_empenho_valor.exercicio
                          
                            JOIN sw_cgm
                              ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario
                                 
                            LEFT JOIN sw_cgm_pessoa_fisica                                                                             
                              ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
                               
                            LEFT JOIN sw_cgm_pessoa_juridica                                                                            
                              ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm
                              
                            JOIN orcamento.programa AS orcamento_programa
                              ON orcamento_programa.exercicio = despesa.exercicio
                             AND orcamento_programa.cod_programa = despesa.cod_programa
                      
                            JOIN orcamento.pao_ppa_acao
                              ON pao_ppa_acao.num_pao = despesa.num_pao
                             AND pao_ppa_acao.exercicio = despesa.exercicio
                      
                            JOIN orcamento.programa_ppa_programa
                              ON programa_ppa_programa.exercicio = orcamento_programa.exercicio
                             AND programa_ppa_programa.cod_programa = orcamento_programa.cod_programa
                      
                            JOIN ppa.programa AS ppa_programa
                              ON ppa_programa.cod_programa = programa_ppa_programa.cod_programa
                      
                            JOIN ppa.acao
                              ON pao_ppa_acao.cod_acao = acao.cod_acao

                            WHERE empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
                            AND empenho_anulado_item.timestamp BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                            AND atributo_empenho_valor.cod_atributo <> 2001
                            GROUP BY empenho.cod_empenho,cod_und_gestora,num_empenho,dt_empenho,vl_empenho,tipo,carac_peculiar,
                                num_orgao, num_unidade, funcao.cod_funcao, subfuncao.cod_subfuncao, ppa_programa.num_programa, conta_despesa.cod_estrutural,
                                recurso.cod_recurso, cod_credor, modal_licita,  pre_empenho.cod_pre_empenho, atributo_empenho_valor.valor, historico,
                                num_acao, acao.cod_programa, empenho_anulado_item.timestamp 
                    UNION ALL

                            SELECT empenho.cod_empenho
                                , (SELECT PJ.cnpj
                                     FROM orcamento.entidade
                                     JOIN sw_cgm
                                       ON sw_cgm.numcgm = entidade.numcgm
                                     JOIN sw_cgm_pessoa_juridica AS PJ
                                       ON sw_cgm.numcgm = PJ.numcgm
                                    WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                      AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                                  ) AS Cod_Und_Gestora
                                , ".$this->getDado('bimestre')." AS bimestre
                                , '".$this->getDado('exercicio')."' AS exercicio
                                , (empenho.exercicio::varchar || LPAD(empenho.cod_empenho::VARCHAR, 9, '0')) AS num_empenho
                                , TO_CHAR(empenho_anulado_item.timestamp,'yyyy-mm-dd') AS dt_empenho
                                , empenho_anulado_item.vl_anulado::varchar AS vl_empenho
                                , '-' AS sinal
                                , CASE WHEN tipo_empenho.cod_tipo = 2 THEN 1
                                       WHEN tipo_empenho.cod_tipo = 3 THEN 2
                                       WHEN tipo_empenho.cod_tipo = 1 THEN 3
                                  END AS tipo
                                , LPAD(restos_pre_empenho.num_orgao::VARCHAR, 2, '0') AS num_orgao
                                , LPAD(restos_pre_empenho.num_unidade::VARCHAR, 4 ,'0') AS num_unidade
                                , LPAD(restos_pre_empenho.cod_funcao::VARCHAR, 2, '0') AS cod_funcao
                                , LPAD(restos_pre_empenho.cod_subfuncao::VARCHAR, 3, '0') AS cod_subfuncao
                                , LPAD(restos_pre_empenho.cod_programa::VARCHAR, 4, '0') AS cod_programa
                                , REPLACE(restos_pre_empenho.cod_estrutural, '.', '') AS cod_estrutural
                                , '6221301000000' AS cod_conta_contabil
                                ,  restos_pre_empenho.recurso AS cod_recurso
                                , '0' AS contra_partida
                                , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL
                                       THEN sw_cgm_pessoa_fisica.cpf
                                       ELSE sw_cgm_pessoa_juridica.cnpj
                                  END AS cod_credor
                                , CASE WHEN atributo_empenho_valor.valor = '1'  THEN '11'
                                       WHEN atributo_empenho_valor.valor = '2'  THEN '03'
                                       WHEN atributo_empenho_valor.valor = '3'  THEN '04'
                                       WHEN atributo_empenho_valor.valor = '4'  THEN '05'
                                       WHEN atributo_empenho_valor.valor = '5'  THEN '01'
                                       WHEN atributo_empenho_valor.valor = '6'  THEN '02'
                                       WHEN atributo_empenho_valor.valor = '7'  THEN '99'
                                       WHEN atributo_empenho_valor.valor = '11' THEN '07'
                                       WHEN atributo_empenho_valor.valor = '12' THEN '08'
                                       WHEN atributo_empenho_valor.valor = '14' THEN '06'
                                  END AS modal_licita
                                , '0' AS num_processo
                                , '0' AS num_contrato
                                , '0' AS dt_contrato
                                , '0' AS num_convenio
                                , '0' AS num_obra
                                , CASE WHEN atributo_peculiar.cod_valor = 1 THEN '000' ELSE '000' END AS carac_peculiar
                                , TRIM(pre_empenho.descricao) AS historico
                                , restos_pre_empenho.num_pao
                                
                            FROM empenho.empenho
                            
                            JOIN empenho.pre_empenho
                              ON pre_empenho.exercicio = empenho.exercicio
                             AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                            JOIN empenho.restos_pre_empenho                    
                              ON restos_pre_empenho.cod_pre_empenho   = pre_empenho.cod_pre_empenho
                             AND restos_pre_empenho.exercicio        = pre_empenho.exercicio

                            JOIN empenho.item_pre_empenho
                              ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND item_pre_empenho.exercicio = pre_empenho.exercicio
                            
                            JOIN empenho.empenho_anulado
                              ON empenho_anulado.exercicio = empenho.exercicio
                             AND empenho_anulado.cod_entidade = empenho.cod_entidade
                             AND empenho_anulado.cod_empenho = empenho.cod_empenho
                            
                            JOIN empenho.empenho_anulado_item
                              ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                             AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                             AND empenho_anulado_item.num_item = item_pre_empenho.num_item
                          
                            JOIN empenho.tipo_empenho
                              ON tipo_empenho.cod_tipo = pre_empenho.cod_tipo
                            
                            JOIN empenho.atributo_empenho_valor
                              ON atributo_empenho_valor.exercicio = pre_empenho.exercicio
                             AND atributo_empenho_valor.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND atributo_empenho_valor.cod_atributo = 101
                             AND atributo_empenho_valor.timestamp = (SELECT MAX(timestamp) FROM empenho.atributo_empenho_valor AS aev
                                      WHERE aev.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho
                                        AND aev.exercicio = atributo_empenho_valor.exercicio )
                             
                            LEFT JOIN (SELECT atributo_empenho_valor.exercicio
                                            , atributo_empenho_valor.cod_pre_empenho
                                            , atributo_empenho_valor.cod_cadastro
                                            , atributo_empenho_valor.cod_modulo
                                            , COALESCE(atributo_valor_padrao.cod_valor, 0) AS cod_valor
                                        FROM administracao.atributo_valor_padrao
                                        LEFT JOIN empenho.atributo_empenho_valor
                                             ON atributo_valor_padrao.cod_modulo   = atributo_empenho_valor.cod_modulo
                                            AND atributo_valor_padrao.cod_cadastro = atributo_empenho_valor.cod_cadastro
                                            AND atributo_valor_padrao.cod_atributo = atributo_empenho_valor.cod_atributo
                                            AND atributo_valor_padrao.cod_valor::VARCHAR = atributo_empenho_valor.valor
                                        WHERE atributo_valor_padrao.cod_atributo = 101
                            ) AS atributo_peculiar
                              ON atributo_peculiar.cod_cadastro    = atributo_empenho_valor.cod_cadastro
                             AND atributo_peculiar.cod_modulo      = atributo_empenho_valor.cod_modulo
                             AND atributo_peculiar.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho
                             AND atributo_peculiar.exercicio       = atributo_empenho_valor.exercicio
                          
                            JOIN sw_cgm
                              ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario
                                 
                            LEFT JOIN sw_cgm_pessoa_fisica                                                                             
                              ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
                               
                            LEFT JOIN sw_cgm_pessoa_juridica                                                                            
                              ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm                             

                            WHERE empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
                            AND empenho_anulado_item.timestamp BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                            AND atributo_empenho_valor.cod_atributo <> 2001
                            GROUP BY empenho.cod_empenho,cod_und_gestora,num_empenho,dt_empenho,vl_empenho,tipo,carac_peculiar,
                                num_orgao, num_unidade, cod_funcao, cod_subfuncao, cod_programa, cod_estrutural,
                                cod_recurso, cod_credor, modal_licita,  pre_empenho.cod_pre_empenho, atributo_empenho_valor.valor, historico,
                                num_pao, cod_programa, empenho_anulado_item.timestamp

                    ) AS tabela 
             ORDER BY cod_empenho ";
                
        return $stSql;
    }
}
?>
