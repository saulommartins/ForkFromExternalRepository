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
    * Extensão da Classe de Mapeamento TTCEALCredor
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Michel Teixeira
    *
    $Id: TTCEALCredor.class.php 58365 2014-05-27 18:43:38Z michel $
    *
    * @ignore
    *
*/
class TTCEALEmpenho extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALEmpenho()
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
        $stSql = "
                    SELECT Cod_Und_Gestora
                         , codigo_ua
                         , bimestre
                         , exercicio
                         , num_empenho
                         , dt_empenho
                         , vl_empenho
                         , sinal
                         , tipo
                         , num_orgao
                         , num_unidade
                         , cod_funcao
                         , cod_subfuncao
                         , cod_programa
                         , num_pao
                         , cod_estrutural
                         , cod_conta_contabil
                         , cod_recurso
                         , contra_partida
                         , cod_credor
                         , modal_licita
                         , registro_preco
                         , referencia_legal
                         , num_processo
                         , dt_processo
                         , num_contrato
                         , dt_contrato
                         , num_convenio
                         , dt_convenio
                         , num_obra
                         , carac_peculiar
                         , historico
                    FROM ( SELECT DISTINCT empenho.cod_empenho
                                  , (SELECT PJ.cnpj
                                       FROM orcamento.entidade
                                       JOIN sw_cgm
                                         ON sw_cgm.numcgm = entidade.numcgm
                                       JOIN sw_cgm_pessoa_juridica AS PJ
                                         ON sw_cgm.numcgm = PJ.numcgm
                                      WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                        AND entidade.cod_entidade = ".$this->getDado('und_gestora')."
                                    ) AS Cod_Und_Gestora
                                  , (SELECT LPAD(valor,4,'0')
                                       FROM administracao.configuracao_entidade
                                      WHERE exercicio    = '".$this->getDado('exercicio')."'
                                        AND cod_entidade = ".$this->getDado('und_gestora')."
                                        AND cod_modulo   = 62
                                        AND parametro    = 'tceal_configuracao_unidade_autonoma'
                                    ) AS Codigo_UA
                                  , ".$this->getDado('bimestre')." AS bimestre
                                  , '".$this->getDado('exercicio')."' AS exercicio
                                  , (empenho.exercicio::varchar || TO_CHAR(empenho.dt_empenho,'mm') || LPAD(empenho.cod_empenho::VARCHAR, 7, '0')) AS num_empenho
                                  , TO_CHAR(empenho.dt_empenho,'DD/MM/YYYY') AS dt_empenho
                                  , (SELECT SUM(vl_total)::VARCHAR
                                       FROM empenho.item_pre_empenho
                                      WHERE exercicio = pre_empenho.exercicio
                                        AND cod_pre_empenho = empenho.cod_pre_empenho
                                    ) AS vl_empenho
                                  , '+' AS sinal
                                  , CASE WHEN tipo_empenho.cod_tipo = 2 THEN '1'
                                         WHEN tipo_empenho.cod_tipo = 3 THEN '2'
                                         WHEN tipo_empenho.cod_tipo = 1 THEN '3'
                                    END AS tipo
                                  , LPAD(despesa.num_orgao::VARCHAR, 2, '0') AS num_orgao
                                  , LPAD(despesa.num_unidade::VARCHAR, 4 ,'0') AS num_unidade
                                  , LPAD(funcao.cod_funcao::VARCHAR, 2, '0') AS cod_funcao
                                  , LPAD(subfuncao.cod_subfuncao::VARCHAR, 3, '0') AS cod_subfuncao
                                  , LPAD(ppa_programa.num_programa::VARCHAR, 4, '0') AS cod_programa
                                  , LPAD(despesa.num_pao::VARCHAR, 4, '0') AS num_pao
                                  , RPAD(REPLACE(conta_despesa.cod_estrutural::VARCHAR,'.',''),16,'0') AS cod_estrutural
                                  , RPAD('6221301000000',17,'0') AS cod_conta_contabil
                                  , LPAD(recurso.cod_recurso::VARCHAR, 9, '0') AS cod_recurso
                                  , '' AS contra_partida
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
                                         WHEN atributo_empenho_valor.valor = '11' THEN '07'
                                         WHEN atributo_empenho_valor.valor = '12' THEN '08'
                                    ELSE '99'
                                    END AS modal_licita
                                  , 2 AS registro_preco
                                  , CASE WHEN convenio.num_convenio IS NOT NULL THEN convenio.fundamentacao 
                                         WHEN contrato.num_contrato IS NOT NULL THEN contrato.fundamentacao_legal
                                    END AS referencia_legal
                                  , processo_administrativo_ano.valor||LPAD(processo_administrativo.valor,11,'0') AS num_processo
                                  , TO_CHAR(processo_administrativo.timestamp, 'dd/mm/yyyy') AS dt_processo
                                  , contrato.num_contrato::VARCHAR AS num_contrato
                                  , contrato.dt_assinatura::VARCHAR AS dt_contrato
                                  , convenio.num_convenio::VARCHAR AS num_convenio
                                  , convenio.dt_assinatura::VARCHAR AS dt_convenio
                                  , '000' AS num_obra
                                  , CASE WHEN atributo_peculiar.cod_valor = 1 THEN '000' ELSE '000' END AS carac_peculiar
                                  , TRIM(pre_empenho.descricao) AS historico
                                  
                               FROM empenho.empenho
                              
                         INNER JOIN empenho.pre_empenho
                                 ON pre_empenho.exercicio = empenho.exercicio
                                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                               
                         INNER JOIN empenho.item_pre_empenho
                                 ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                AND item_pre_empenho.exercicio = pre_empenho.exercicio
                         
                         --numero processo administrativo 
                         INNER JOIN empenho.atributo_empenho_valor processo_administrativo
                                 ON processo_administrativo.exercicio = pre_empenho.exercicio
                                AND processo_administrativo.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                AND processo_administrativo.cod_atributo = 120
                        
                        --exercicio processo administrativo 
                         INNER JOIN empenho.atributo_empenho_valor processo_administrativo_ano
                                 ON processo_administrativo_ano.exercicio = pre_empenho.exercicio
                                AND processo_administrativo_ano.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                AND processo_administrativo_ano.cod_atributo = 121
                               
                          LEFT JOIN empenho.empenho_anulado
                                 ON empenho_anulado.exercicio = empenho.exercicio
                                AND empenho_anulado.cod_entidade = empenho.cod_entidade
                                AND empenho_anulado.cod_empenho = empenho.cod_empenho
                               
                          LEFT JOIN empenho.empenho_anulado_item
                                 ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                                AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                                AND empenho_anulado_item.num_item = item_pre_empenho.num_item
                         
                           -- CONVENIO
                          LEFT JOIN empenho.empenho_convenio 
                                 ON empenho_convenio.exercicio = empenho.exercicio 
                                AND empenho_convenio.cod_entidade = empenho.cod_entidade
                                AND empenho_convenio.cod_empenho = empenho.cod_empenho

                          LEFT JOIN licitacao.convenio
                                 ON convenio.exercicio = empenho_convenio.exercicio
                                AND convenio.num_convenio =  empenho_convenio.num_convenio

                          -- CONTRATO
                          LEFT JOIN empenho.empenho_contrato  
                                 ON empenho_contrato.exercicio = empenho.exercicio 
                                AND empenho_contrato.cod_entidade = empenho.cod_entidade
                                AND empenho_contrato.cod_empenho = empenho.cod_empenho
                          
                          LEFT JOIN licitacao.contrato
                                 ON contrato.exercicio = empenho_contrato.exercicio
                                AND contrato.num_contrato =  empenho_contrato.num_contrato
                                AND contrato.cod_entidade = empenho_contrato.cod_entidade	
                         
                         INNER JOIN empenho.pre_empenho_despesa
                                 ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                               
                         INNER JOIN orcamento.despesa
                                 ON despesa.exercicio = pre_empenho_despesa.exercicio
                                AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                               
                         INNER JOIN orcamento.recurso
                                 ON recurso.exercicio = despesa.exercicio
                                AND recurso.cod_recurso = despesa.cod_recurso
                               
                         INNER JOIN orcamento.conta_despesa
                                 ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                                AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                               
                         INNER JOIN empenho.tipo_empenho
                                 ON tipo_empenho.cod_tipo = pre_empenho.cod_tipo
                                
                         INNER JOIN orcamento.funcao
                                 ON funcao.cod_funcao = despesa.cod_funcao
                                AND funcao.exercicio = despesa.exercicio
                               
                         INNER JOIN orcamento.subfuncao
                                 ON subfuncao.exercicio = despesa.exercicio
                                AND subfuncao.cod_subfuncao = despesa.cod_subfuncao
                               
                         INNER JOIN empenho.atributo_empenho_valor
                                 ON atributo_empenho_valor.exercicio = pre_empenho.exercicio
                                AND atributo_empenho_valor.cod_pre_empenho = pre_empenho.cod_pre_empenho
                               
                         INNER JOIN administracao.atributo_valor_padrao
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

                                 INNER JOIN empenho.atributo_empenho_valor
                                         ON atributo_valor_padrao.cod_modulo   = atributo_empenho_valor.cod_modulo
                                        AND atributo_valor_padrao.cod_cadastro = atributo_empenho_valor.cod_cadastro
                                        AND atributo_valor_padrao.cod_atributo = atributo_empenho_valor.cod_atributo
                                        AND atributo_valor_padrao.cod_valor::VARCHAR = atributo_empenho_valor.valor
                                     
                                      WHERE atributo_valor_padrao.cod_atributo = 2001
                                  ) AS atributo_peculiar
                                 ON atributo_peculiar.cod_cadastro    = atributo_empenho_valor.cod_cadastro
                                AND atributo_peculiar.cod_modulo      = atributo_empenho_valor.cod_modulo
                                AND atributo_peculiar.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho
                                AND atributo_peculiar.exercicio       = atributo_empenho_valor.exercicio
                             
                         INNER JOIN sw_cgm
                                 ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario
                                 
                          LEFT JOIN sw_cgm_pessoa_fisica                                                                             
                                 ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
                                  
                          LEFT JOIN sw_cgm_pessoa_juridica                                                                            
                                 ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm
                                 
                         INNER JOIN orcamento.programa AS orcamento_programa
                                 ON orcamento_programa.exercicio = despesa.exercicio
                                AND orcamento_programa.cod_programa = despesa.cod_programa
                         
                         INNER JOIN orcamento.programa_ppa_programa
                                 ON programa_ppa_programa.exercicio = orcamento_programa.exercicio
                                AND programa_ppa_programa.cod_programa = orcamento_programa.cod_programa
                         
                         INNER JOIN ppa.programa AS ppa_programa
                                 ON ppa_programa.cod_programa = programa_ppa_programa.cod_programa

                              WHERE empenho.exercicio = '".$this->getDado('exercicio')."'
                                AND empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
                                AND empenho.dt_empenho BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                                AND atributo_empenho_valor.cod_atributo <> 2001

                          UNION ALL

                             SELECT DISTINCT empenho.cod_empenho
                                  , (SELECT PJ.cnpj
                                       FROM orcamento.entidade
                                       JOIN sw_cgm
                                         ON sw_cgm.numcgm = entidade.numcgm
                                       JOIN sw_cgm_pessoa_juridica AS PJ
                                         ON sw_cgm.numcgm = PJ.numcgm
                                      WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                        AND entidade.cod_entidade = ".$this->getDado('und_gestora')."
                                    ) AS Cod_Und_Gestora
                                  , (SELECT LPAD(valor,4,'0')
                                       FROM administracao.configuracao_entidade
                                      WHERE exercicio = '".$this->getDado('exercicio')."'
                                        AND cod_entidade = ".$this->getDado('und_gestora')."
                                        AND cod_modulo   = 62
                                        AND parametro    = 'tceal_configuracao_unidade_autonoma'
                                    ) AS Codigo_UA
                                  , ".$this->getDado('bimestre')." AS bimestre
                                  , '".$this->getDado('exercicio')."' AS exercicio
                                  , (empenho.exercicio::varchar || TO_CHAR(empenho.dt_empenho,'mm') || LPAD(empenho.cod_empenho::VARCHAR, 7, '0')) AS num_empenho
                                  , TO_CHAR(empenho.dt_empenho,'DD/MM/YYYY') AS dt_empenho
                                  , (SELECT SUM(vl_anulado)::VARCHAR
                                       FROM empenho.empenho_anulado_item
                                      WHERE exercicio = pre_empenho.exercicio
                                        AND cod_pre_empenho = empenho.cod_pre_empenho
                                    ) AS vl_empenho
                                  , '-' AS sinal
                                  , CASE WHEN tipo_empenho.cod_tipo = 2 THEN '1'
                                         WHEN tipo_empenho.cod_tipo = 3 THEN '2'
                                         WHEN tipo_empenho.cod_tipo = 1 THEN '3'
                                    END AS tipo
                                  , LPAD(despesa.num_orgao::VARCHAR, 2, '0') AS num_orgao
                                  , LPAD(despesa.num_unidade::VARCHAR, 4 ,'0') AS num_unidade
                                  , LPAD(funcao.cod_funcao::VARCHAR, 2, '0') AS cod_funcao
                                  , LPAD(subfuncao.cod_subfuncao::VARCHAR, 3, '0') AS cod_subfuncao
                                  , LPAD(ppa_programa.num_programa::VARCHAR, 4, '0') AS cod_programa
                                  , LPAD(despesa.num_pao::VARCHAR, 4, '0') AS num_pao
                                  , RPAD(REPLACE(conta_despesa.cod_estrutural::VARCHAR,'.',''),16,'0') AS cod_estrutural
                                  , RPAD('6221301000000',17,'0') AS cod_conta_contabil
                                  , LPAD(recurso.cod_recurso::VARCHAR, 9, '0') AS cod_recurso
                                  , '' AS contra_partida
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
                                         WHEN atributo_empenho_valor.valor = '11' THEN '07'
                                         WHEN atributo_empenho_valor.valor = '12' THEN '08'
                                    ELSE '99'
                                    END AS modal_licita
                                  , 2 AS registro_preco
                                  , CASE WHEN convenio.num_convenio IS NOT NULL THEN convenio.fundamentacao 
                                         WHEN contrato.num_contrato IS NOT NULL THEN contrato.fundamentacao_legal
                                    END AS referencia_legal
                                  , processo_administrativo_ano.valor||LPAD(processo_administrativo.valor,11,'0') AS num_processo
                                  , TO_CHAR(processo_administrativo.timestamp, 'dd/mm/yyyy') AS dt_processo
                                  , contrato.num_contrato::VARCHAR AS num_contrato
                                  , contrato.dt_assinatura::VARCHAR AS dt_contrato
                                  , convenio.num_convenio::VARCHAR AS num_convenio
                                  , convenio.dt_assinatura::VARCHAR AS dt_convenio
                                  , '000' AS num_obra
                                  , CASE WHEN atributo_peculiar.cod_valor = 1 THEN '000' ELSE '000' END AS carac_peculiar
                                  , TRIM(pre_empenho.descricao) AS historico
                                  
                               FROM empenho.empenho
                            
                         INNER JOIN empenho.pre_empenho
                                 ON pre_empenho.exercicio = empenho.exercicio
                                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                             
                         INNER JOIN empenho.item_pre_empenho
                                 ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                AND item_pre_empenho.exercicio = pre_empenho.exercicio
                         
                         --Numero processo administrativo 
                         INNER JOIN empenho.atributo_empenho_valor processo_administrativo
                                 ON processo_administrativo.exercicio = pre_empenho.exercicio
                                AND processo_administrativo.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                AND processo_administrativo.cod_atributo = 120
                        --exercicio processo administrativo 
                         INNER JOIN empenho.atributo_empenho_valor processo_administrativo_ano
                                 ON processo_administrativo_ano.exercicio = pre_empenho.exercicio
                                AND processo_administrativo_ano.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                AND processo_administrativo_ano.cod_atributo = 121
                                
                         INNER JOIN empenho.empenho_anulado
                                 ON empenho_anulado.exercicio = empenho.exercicio
                                AND empenho_anulado.cod_entidade = empenho.cod_entidade
                                AND empenho_anulado.cod_empenho = empenho.cod_empenho
                               
                         INNER JOIN empenho.empenho_anulado_item
                                 ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                                AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                                AND empenho_anulado_item.num_item = item_pre_empenho.num_item
                         
                            -- CONVENIO
                          LEFT JOIN empenho.empenho_convenio 
                                 ON empenho_convenio.exercicio = empenho.exercicio 
                                AND empenho_convenio.cod_entidade = empenho.cod_entidade
                                AND empenho_convenio.cod_empenho = empenho.cod_empenho

                          LEFT JOIN licitacao.convenio
                                 ON convenio.exercicio = empenho_convenio.exercicio
                                AND convenio.num_convenio =  empenho_convenio.num_convenio
                                
                         -- CONTRATO
                          LEFT JOIN empenho.empenho_contrato  
                                 ON empenho_contrato.exercicio = empenho.exercicio 
                                AND empenho_contrato.cod_entidade = empenho.cod_entidade
                                AND empenho_contrato.cod_empenho = empenho.cod_empenho
                                
                         LEFT JOIN licitacao.contrato
                                 ON contrato.exercicio = empenho_contrato.exercicio
                                AND contrato.num_contrato =  empenho_contrato.num_contrato
                                AND contrato.cod_entidade = empenho_contrato.cod_entidade	
                       
                         INNER JOIN empenho.pre_empenho_despesa
                                 ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                               
                         INNER JOIN orcamento.despesa
                                 ON despesa.exercicio = pre_empenho_despesa.exercicio
                                AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                               
                         INNER JOIN orcamento.recurso
                                 ON recurso.exercicio = despesa.exercicio
                                AND recurso.cod_recurso = despesa.cod_recurso
                               
                         INNER JOIN orcamento.conta_despesa
                                 ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                                AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                             
                         INNER JOIN empenho.tipo_empenho
                                 ON tipo_empenho.cod_tipo = pre_empenho.cod_tipo
                                
                         INNER JOIN orcamento.funcao
                                 ON funcao.cod_funcao = despesa.cod_funcao
                                AND funcao.exercicio = despesa.exercicio
                               
                         INNER JOIN orcamento.subfuncao
                                 ON subfuncao.exercicio = despesa.exercicio
                                AND subfuncao.cod_subfuncao = despesa.cod_subfuncao
                               
                         INNER JOIN empenho.atributo_empenho_valor
                                 ON atributo_empenho_valor.exercicio = pre_empenho.exercicio
                                AND atributo_empenho_valor.cod_pre_empenho = pre_empenho.cod_pre_empenho
                               
                         INNER JOIN administracao.atributo_valor_padrao
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
                  
                                 INNER JOIN empenho.atributo_empenho_valor
                                         ON atributo_valor_padrao.cod_modulo   = atributo_empenho_valor.cod_modulo
                                        AND atributo_valor_padrao.cod_cadastro = atributo_empenho_valor.cod_cadastro
                                        AND atributo_valor_padrao.cod_atributo = atributo_empenho_valor.cod_atributo
                                        AND atributo_valor_padrao.cod_valor::VARCHAR = atributo_empenho_valor.valor
                                     
                                      WHERE atributo_valor_padrao.cod_atributo = 2001
                                  ) AS atributo_peculiar
                                 ON atributo_peculiar.cod_cadastro    = atributo_empenho_valor.cod_cadastro
                                AND atributo_peculiar.cod_modulo      = atributo_empenho_valor.cod_modulo
                                AND atributo_peculiar.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho
                                AND atributo_peculiar.exercicio       = atributo_empenho_valor.exercicio
                             
                         INNER JOIN sw_cgm
                                 ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario
                                 
                          LEFT JOIN sw_cgm_pessoa_fisica                                                                             
                                 ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
                                  
                          LEFT JOIN sw_cgm_pessoa_juridica                                                                            
                                 ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm
                                 
                         INNER JOIN orcamento.programa AS orcamento_programa
                                 ON orcamento_programa.exercicio = despesa.exercicio
                                AND orcamento_programa.cod_programa = despesa.cod_programa
                         
                         INNER JOIN orcamento.programa_ppa_programa
                                 ON programa_ppa_programa.exercicio = orcamento_programa.exercicio
                                AND programa_ppa_programa.cod_programa = orcamento_programa.cod_programa
                         
                         INNER JOIN ppa.programa AS ppa_programa
                                 ON ppa_programa.cod_programa = programa_ppa_programa.cod_programa

                              WHERE empenho.exercicio = '".Sessao::getExercicio()."'
                                AND empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
                                AND empenho.dt_empenho BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                                AND atributo_empenho_valor.cod_atributo <> 2001
                                
                UNION ALL
                
                   SELECT DISTINCT empenho.cod_empenho
                          , (SELECT PJ.cnpj
                               FROM orcamento.entidade
                               JOIN sw_cgm
                                 ON sw_cgm.numcgm = entidade.numcgm
                               JOIN sw_cgm_pessoa_juridica AS PJ
                                 ON sw_cgm.numcgm = PJ.numcgm
                              WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                AND entidade.cod_entidade = ".$this->getDado('und_gestora')."
                            ) AS Cod_Und_Gestora
                          , (SELECT LPAD(valor,4,'0')
                               FROM administracao.configuracao_entidade
                              WHERE exercicio = '".$this->getDado('exercicio')."'
                                AND cod_entidade = ".$this->getDado('und_gestora')."
                                AND cod_modulo   = 62
                                AND parametro    = 'tceal_configuracao_unidade_autonoma'
                            ) AS Codigo_UA
                          , ".$this->getDado('bimestre')." AS bimestre
                          , '".$this->getDado('exercicio')."' AS exercicio
                          , (empenho.exercicio::varchar || TO_CHAR(empenho.dt_empenho,'mm') || LPAD(empenho.cod_empenho::VARCHAR, 7, '0')) AS num_empenho
                          , TO_CHAR(empenho.dt_empenho,'DD/MM/YYYY') AS dt_empenho
                          , (SELECT SUM(vl_total)::VARCHAR
                               FROM empenho.item_pre_empenho
                              WHERE exercicio = pre_empenho.exercicio
                                AND cod_pre_empenho = empenho.cod_pre_empenho
                            ) AS vl_empenho
                          , '+' AS sinal
                          , CASE WHEN tipo_empenho.cod_tipo = 2 THEN '1'
                                 WHEN tipo_empenho.cod_tipo = 3 THEN '2'
                                 WHEN tipo_empenho.cod_tipo = 1 THEN '3'
                            END AS tipo
                          , LPAD(restos_pre_empenho.num_orgao::VARCHAR, 2, '0') AS num_orgao
                          , LPAD(restos_pre_empenho.num_unidade::VARCHAR, 4 ,'0') AS num_unidade
                          , LPAD(restos_pre_empenho.cod_funcao::VARCHAR, 2, '0') AS cod_funcao
                          , LPAD(restos_pre_empenho.cod_subfuncao::VARCHAR, 3, '0') AS cod_subfuncao
                          , LPAD(restos_pre_empenho.cod_programa::VARCHAR, 4, '0') AS cod_programa
                          , LPAD(restos_pre_empenho.num_pao::VARCHAR, 4, '0') AS num_pao
                          , RPAD(REPLACE(restos_pre_empenho.cod_estrutural::VARCHAR,'.',''),16,'0') AS cod_estrutural
                          , RPAD('6221301000000',17,'0') AS cod_conta_contabil
                          , LPAD(restos_pre_empenho.recurso::VARCHAR, 9, '0') AS cod_recurso
                          , '' AS contra_partida
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
                                 WHEN atributo_empenho_valor.valor = '11' THEN '07'
                                 WHEN atributo_empenho_valor.valor = '12' THEN '08'
                            ELSE '99'
                            END AS modal_licita
                          , 2 AS registro_preco
                          , '' AS referencia_legal
                          , processo_administrativo_ano.valor||LPAD(processo_administrativo.valor,11,'0') AS num_processo
                          , TO_CHAR(processo_administrativo.timestamp, 'dd/mm/yyyy') AS dt_processo
                          , '' AS num_contrato
                          , '' AS dt_contrato
                          , '' AS num_convenio
                          , '' AS dt_convenio
                          , '000' AS num_obra
                          , CASE WHEN atributo_peculiar.cod_valor = 1 THEN '000' ELSE '000' END AS carac_peculiar
                          , TRIM(pre_empenho.descricao) AS historico
                                
                      FROM empenho.empenho
                               
                INNER JOIN empenho.pre_empenho
                        ON pre_empenho.exercicio = empenho.exercicio
                       AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho     
                       
                INNER JOIN empenho.restos_pre_empenho
                        ON restos_pre_empenho.exercicio = pre_empenho.exercicio
                       AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                INNER JOIN empenho.item_pre_empenho
                        ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                       AND item_pre_empenho.exercicio = pre_empenho.exercicio

                 LEFT JOIN empenho.empenho_anulado_item
                        ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                       AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                       AND empenho_anulado_item.num_item = item_pre_empenho.num_item

                INNER JOIN empenho.tipo_empenho
                        ON tipo_empenho.cod_tipo = pre_empenho.cod_tipo

                INNER JOIN sw_cgm
                        ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario
                        
                 LEFT JOIN sw_cgm_pessoa_fisica                                                                             
                        ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
                         
                 LEFT JOIN sw_cgm_pessoa_juridica                                                                            
                        ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm

                 INNER JOIN empenho.atributo_empenho_valor
                        ON atributo_empenho_valor.exercicio = pre_empenho.exercicio
                       AND atributo_empenho_valor.cod_pre_empenho = pre_empenho.cod_pre_empenho
                      
                INNER JOIN administracao.atributo_valor_padrao
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

                        INNER JOIN empenho.atributo_empenho_valor
                                ON atributo_valor_padrao.cod_modulo   = atributo_empenho_valor.cod_modulo
                               AND atributo_valor_padrao.cod_cadastro = atributo_empenho_valor.cod_cadastro
                               AND atributo_valor_padrao.cod_atributo = atributo_empenho_valor.cod_atributo
                               AND atributo_valor_padrao.cod_valor::VARCHAR = atributo_empenho_valor.valor
                            
                             WHERE atributo_valor_padrao.cod_atributo = 2001
                         ) AS atributo_peculiar
                        ON atributo_peculiar.cod_cadastro    = atributo_empenho_valor.cod_cadastro
                       AND atributo_peculiar.cod_modulo      = atributo_empenho_valor.cod_modulo
                       AND atributo_peculiar.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho
                       AND atributo_peculiar.exercicio       = atributo_empenho_valor.exercicio
			     
                INNER JOIN empenho.atributo_empenho_valor processo_administrativo
                        ON processo_administrativo.exercicio = pre_empenho.exercicio
                       AND processo_administrativo.cod_pre_empenho = pre_empenho.cod_pre_empenho
                       AND processo_administrativo.cod_atributo = 120
                  
                INNER JOIN empenho.atributo_empenho_valor processo_administrativo_ano
                        ON processo_administrativo_ano.exercicio = pre_empenho.exercicio
                       AND processo_administrativo_ano.cod_pre_empenho = pre_empenho.cod_pre_empenho
                       AND processo_administrativo_ano.cod_atributo = 121
                   
                     WHERE empenho.exercicio = '".Sessao::getExercicio()."'
                       AND empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
                       AND empenho.dt_empenho BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                       AND atributo_empenho_valor.cod_atributo <> 2001
                    ) AS tabela 
             ORDER BY cod_empenho ";
                
        return $stSql;
    }
}
?>
