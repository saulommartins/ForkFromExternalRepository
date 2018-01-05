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
    * Classe de mapeamento da tabela tcemg.contrato
    * Data de Criação   : 06/03/2014

    * @author Analista      Sergio Luiz dos Santos
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: TTCEMGCONTRATOS.class.php 65378 2016-05-17 18:20:12Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEMGCONTRATOS extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function TTCEMGCONTRATOS()
    {
        parent::Persistente();
    }
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato10.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato10().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaContrato10()
    {
        $stSql  = " SELECT 10 AS tipo_registro
                         , contrato.exercicio||LPAD(contrato.cod_entidade::VARCHAR,2,'0')||LPAD(contrato.num_contrato::VARCHAR,9,'0') as cod_contrato
                         , contrato.numero_contrato AS nro_contrato
                         , ( SELECT valor 
                               FROM administracao.configuracao_entidade
                              WHERE exercicio=contrato.exercicio
                                AND parametro='tcemg_codigo_orgao_entidade_sicom'
                                AND cod_entidade=contrato.cod_entidade) AS cod_orgao
                         , LPAD(LPAD(contrato.num_orgao::VARCHAR,2,'0')||LPAD(contrato.num_unidade::VARCHAR,2,'0'),5,'0') as cod_unidade_sub
                         , contrato.exercicio AS exercicio_contrato
                         , TO_CHAR(contrato.dt_assinatura, 'ddmmyyyy') AS dt_assinatura
                         , tipo_contrato.tipo_tc AS cont_dec_licitacao
                         , CASE WHEN tipo_contrato.tipo_tc=5 OR tipo_contrato.tipo_tc =6 THEN
                                ( SELECT valor 
                                    FROM administracao.configuracao_entidade
                                   WHERE exercicio=contrato.exercicio
                                     AND parametro='tcemg_codigo_orgao_entidade_sicom'
                                     AND cod_entidade=contrato.cod_entidade
                                )
                                ELSE ''
                           END AS cod_orgao_resp
                         , CASE WHEN ( tipo_contrato.tipo_tc != 1 OR tipo_contrato.tipo_tc != 8 ) THEN
                                LPAD(LPAD(contrato.num_orgao::VARCHAR,2,'0')||LPAD(contrato.num_unidade::VARCHAR,2,'0'),5,'0')
                                ELSE ''
                           END AS cod_unidade_sub_resp
                         , CASE WHEN (contrato_licitacao.cod_modalidade > 0) THEN
                                contrato.exercicio::varchar||LPAD(contrato.cod_entidade::varchar,2, '0')||LPAD(contrato_licitacao.cod_modalidade::varchar,2, '0')||LPAD(contrato.num_contrato::varchar,4, '0')
                                WHEN (contrato_compra_direta.cod_modalidade > 0) THEN
                                contrato.exercicio::varchar||LPAD(contrato.cod_entidade::varchar,2, '0')||LPAD(contrato_compra_direta.cod_modalidade::varchar,2, '0')||LPAD(contrato.num_contrato::varchar,4, '0')
                           END AS nro_processo
                         , contrato.exercicio AS exercicio_processo
                         , CASE WHEN (contrato_licitacao.cod_modalidade > 0) THEN 
                                     CASE WHEN contrato_licitacao.cod_modalidade = 8 AND licitacao.tipo_chamada_publica = 0 THEN 1
                                          WHEN contrato_licitacao.cod_modalidade = 9 AND licitacao.tipo_chamada_publica = 0 THEN 2
                                          WHEN contrato_licitacao.cod_modalidade = 9 AND licitacao.tipo_chamada_publica = 2 THEN 3
                                          WHEN contrato_licitacao.cod_modalidade = 8 AND licitacao.tipo_chamada_publica = 1 THEN 4
                                     END
                                WHEN (contrato_compra_direta.cod_modalidade > 0) THEN 
                                     CASE WHEN contrato_compra_direta.cod_modalidade = 8 THEN 1
                                          WHEN contrato_compra_direta.cod_modalidade = 9 THEN 2
                                     END
                           END AS tipo_processo
                         , CASE WHEN (licitacao.cod_tipo_objeto > 0) THEN
                                     CASE WHEN licitacao.cod_tipo_objeto = 2 THEN 1
                                          WHEN licitacao.cod_tipo_objeto = 1 THEN 2
                                          WHEN licitacao.cod_tipo_objeto = 6 THEN 3
                                          WHEN licitacao.cod_tipo_objeto = 3 THEN 4
                                          WHEN licitacao.cod_tipo_objeto = 5 THEN 5
                                     END
                                WHEN (compra_direta.cod_tipo_objeto > 0) THEN
                                     CASE WHEN compra_direta.cod_tipo_objeto = 2 THEN 1
                                          WHEN compra_direta.cod_tipo_objeto = 1 THEN 2
                                          WHEN compra_direta.cod_tipo_objeto = 6 THEN 3
                                          WHEN compra_direta.cod_tipo_objeto = 3 THEN 4
                                          WHEN compra_direta.cod_tipo_objeto = 5 THEN 5
                                     END
                           END AS natureza_objeto
                         , sem_acentos(objeto.descricao) AS objeto_contrato
                         , CASE WHEN contrato.cod_tipo_instrumento > 0 THEN contrato.cod_tipo_instrumento
                                ELSE 1
                           END AS tipo_instrumento 
                         , TO_CHAR(contrato.inicio_execucao, 'ddmmyyyy') AS dt_inicio_vigencia
                         , TO_CHAR(contrato.fim_execucao, 'ddmmyyyy') AS dt_final_vigencia
                         , contrato.valor_contratado
                         , sem_acentos(contrato.forma_fornecimento) AS forma_fornecimento
                         , sem_acentos(contrato.forma_pagamento) AS forma_pagamento
                         , sem_acentos(contrato.prazo_execucao) AS prazo_execucao
                         , sem_acentos(contrato.multa_rescisoria) AS multa_rescisoria
                         , contrato.multa_inadimplemento AS multa_inadimplemento 
                         , contrato.cod_garantia 
                         , sw_cgm_pessoa_fisica.cpf AS cpf_signatario_contratante
                         , TO_CHAR(publicacao_contrato.dt_publicacao, 'ddmmyyyy') AS dt_publicacao 
                         , sw_cgm.nom_cgm AS veiculo_divulgacao
                      FROM licitacao.contrato
                INNER JOIN licitacao.tipo_contrato
                        ON tipo_contrato.cod_tipo = contrato.cod_tipo_contrato
                   -------Licitacao
                 LEFT JOIN licitacao.contrato_licitacao
                        ON contrato_licitacao.num_contrato = contrato.num_contrato
                       AND contrato_licitacao.exercicio = contrato.exercicio
                       AND contrato_licitacao.cod_entidade = contrato.cod_entidade
                 LEFT JOIN licitacao.licitacao
                        ON licitacao.cod_licitacao = contrato_licitacao.cod_licitacao
                       AND licitacao.cod_modalidade = contrato_licitacao.cod_modalidade
                       AND licitacao.exercicio = contrato_licitacao.exercicio_licitacao
                       AND licitacao.cod_entidade = contrato_licitacao.cod_entidade 
                   -------Compras
                LEFT JOIN licitacao.contrato_compra_direta
                        ON contrato_compra_direta.num_contrato = contrato.num_contrato
                       AND contrato_compra_direta.exercicio = contrato.exercicio
                       AND contrato_compra_direta.cod_entidade = contrato.cod_entidade
                 LEFT JOIN compras.compra_direta 
                        ON compra_direta.cod_compra_direta = contrato_compra_direta.cod_compra_direta
                       AND compra_direta.cod_modalidade = contrato_compra_direta.cod_modalidade
                       AND compra_direta.exercicio_entidade = contrato_compra_direta.exercicio_compra_direta
                       AND compra_direta.cod_entidade = contrato_compra_direta.cod_entidade 
                INNER JOIN compras.objeto
                        ON ( objeto.cod_objeto = licitacao.cod_objeto OR  objeto.cod_objeto = compra_direta.cod_objeto )
                 LEFT JOIN sw_cgm_pessoa_fisica
                        ON sw_cgm_pessoa_fisica.numcgm = contrato.cgm_signatario
                INNER JOIN licitacao.publicacao_contrato
                        ON publicacao_contrato.exercicio = contrato.exercicio
                       AND publicacao_contrato.num_contrato = contrato.num_contrato
                       AND publicacao_contrato.cod_entidade = contrato.cod_entidade
                INNER JOIN sw_cgm
                        ON sw_cgm.numcgm = publicacao_contrato.numcgm
                      WHERE contrato.exercicio = '".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                       AND (contrato.inicio_execucao <= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                        OR  contrato.inicio_execucao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                          )--ENTRADA MES
                       AND (contrato.fim_execucao >= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        OR  contrato.fim_execucao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                          )--ENTRADA MES
                       AND contrato.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
                  GROUP BY cod_contrato
                         , numero_contrato
                         , cod_orgao
                         , cod_unidade_sub
                         , exercicio_contrato
                         , contrato.dt_assinatura
                         , cont_dec_licitacao
                         , cod_orgao_resp
                         , nro_processo
                         , tipo_processo
                         , natureza_objeto
                         , objeto.descricao
                         , dt_inicio_vigencia
                         , dt_final_vigencia
                         , contrato.valor_contratado
                         , contrato.forma_fornecimento
                         , contrato.forma_pagamento
                         , contrato.prazo_execucao   
                         , contrato.multa_rescisoria
                         , contrato.cod_garantia
                         , cpf
                         , dt_publicacao
                         , veiculo_divulgacao
                         , tipo_instrumento
                         , multa_inadimplemento
                  ORDER BY numero_contrato
        ";
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato11.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato11(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato11().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato11()
    {
        $stSql  = " SELECT 11 as tipo_registro
                         , contrato.exercicio||LPAD(contrato.cod_entidade::VARCHAR,2,'0')||LPAD(contrato.num_contrato::VARCHAR,9,'0') as cod_contrato
                         , item_pre_empenho.cod_item AS cod_item
                         , REPLACE(ROUND(item_pre_empenho.quantidade, 4)::TEXT, '.', ',') AS quantidade_item
                         , REPLACE(ROUND((item_pre_empenho.vl_total/item_pre_empenho.quantidade), 4)::TEXT, '.', ',') AS valor_unitario_item
                      FROM licitacao.contrato
                INNER JOIN empenho.empenho_contrato 
                        ON empenho_contrato.num_contrato=contrato.num_contrato
                       AND empenho_contrato.exercicio_contrato=contrato.exercicio
                       AND empenho_contrato.cod_entidade=contrato.cod_entidade
                INNER JOIN empenho.empenho 
                        ON empenho.exercicio=empenho_contrato.exercicio
                       AND empenho.cod_entidade=empenho_contrato.cod_entidade
                       AND empenho.cod_empenho=empenho_contrato.cod_empenho
                INNER JOIN empenho.item_pre_empenho 
                        ON item_pre_empenho.cod_pre_empenho=empenho.cod_pre_empenho
                       AND item_pre_empenho.exercicio=empenho.exercicio
                     WHERE contrato.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                       AND (contrato.inicio_execucao <= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                        OR  contrato.inicio_execucao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                          )--ENTRADA MES
                       AND(contrato.fim_execucao >= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        OR  contrato.fim_execucao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                          )--ENTRADA MES
                       AND contrato.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE";
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato12.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato12(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato12().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato12()
    {
        $stSql  = " SELECT 12 as tipo_registro
                         , contrato.exercicio||LPAD(contrato.cod_entidade::VARCHAR,2,'0')||LPAD(contrato.num_contrato::VARCHAR,9,'0') as cod_contrato
                         , ( SELECT valor 
                               FROM administracao.configuracao_entidade
                              WHERE exercicio=contrato.exercicio
                                AND parametro='tcemg_codigo_orgao_entidade_sicom'
                                AND cod_entidade=contrato.cod_entidade) AS cod_orgao
                         , LPAD(LPAD(contrato.num_orgao::VARCHAR,2,'0')||LPAD(contrato.num_unidade::VARCHAR,2,'0'),5,'0') as cod_unidade_sub
                         , LPAD(''||OD.cod_funcao,2, '0') AS cod_funcao
                         , OD.cod_subfuncao AS cod_sub_funcao
                         , LPAD(''||(SELECT num_programa FROM ppa.programa
                                      WHERE cod_programa=OP.cod_programa AND ativo=true LIMIT 1),4, '0')
                           AS cod_programa
                         , LPAD(ACAO.num_acao::VARCHAR,4, '0') AS id_acao
                         , ''::TEXT AS id_sub_acao
                         , LPAD(REPLACE(OCD.cod_estrutural, '.', ''),6, '') AS natureza_despesa
                         , recurso.cod_fonte AS cod_font_recursos
                         , REPLACE(empenho.fn_consultar_valor_empenhado( EE.exercicio, EE.cod_empenho ,EE.cod_entidade)::TEXT, '.', ',') AS vl_recurso 
                      FROM licitacao.contrato 
                INNER JOIN empenho.empenho_contrato 
                        ON empenho_contrato.num_contrato=contrato.num_contrato
                       AND empenho_contrato.exercicio_contrato=contrato.exercicio
                       AND empenho_contrato.cod_entidade=contrato.cod_entidade
                INNER JOIN empenho.empenho AS EE
                        ON EE.exercicio=empenho_contrato.exercicio
                       AND EE.cod_entidade=empenho_contrato.cod_entidade
                       AND EE.cod_empenho=empenho_contrato.cod_empenho
                INNER JOIN empenho.pre_empenho AS EPE
                        ON EPE.cod_pre_empenho=EE.cod_pre_empenho
                       AND EPE.exercicio=EE.exercicio
                INNER JOIN empenho.pre_empenho_despesa AS EPED
                        ON EPED.cod_pre_empenho=EPE.cod_pre_empenho
                       AND EPED.exercicio=EPE.exercicio
                INNER JOIN orcamento.conta_despesa AS OCD
                        ON OCD.exercicio=EPED.exercicio
                       AND OCD.cod_conta=EPED.cod_conta
                INNER JOIN orcamento.despesa AS OD
                        ON OD.exercicio=EPED.exercicio AND OD.cod_despesa=EPED.cod_despesa
                INNER JOIN orcamento.programa AS OP
                        ON OP.cod_programa=OD.cod_programa
                       AND OP.exercicio=OD.exercicio
                INNER JOIN orcamento.despesa_acao AS ODA
                        ON ODA.cod_despesa=OD.cod_despesa
                       AND ODA.exercicio_despesa=OD.exercicio
                INNER JOIN ppa.acao AS ACAO
                        ON ACAO.cod_acao=ODA.cod_acao
                INNER JOIN orcamento.recurso
                        ON recurso.exercicio=OD.exercicio
                       AND recurso.cod_recurso=OD.cod_recurso 
                     WHERE contrato.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                       AND (contrato.inicio_execucao <= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                        OR  contrato.inicio_execucao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                          )--ENTRADA MES
                       AND(contrato.fim_execucao >= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        OR  contrato.fim_execucao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                          )--ENTRADA MES
                       AND contrato.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE";
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato13.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato13(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato13().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato13()
    {
        $stSql  = " SELECT 13 as tipo_registro
                         , contrato.exercicio||LPAD(contrato.cod_entidade::VARCHAR,2,'0')||LPAD(contrato.num_contrato::VARCHAR,9,'0') as cod_contrato
                         , CASE WHEN CGM.cod_pais!=1 THEN 3
                                WHEN CGMPJ.cnpj IS NOT NULL THEN 2
                           ELSE 1
                           END AS tipo_documento
                         , CASE WHEN CGMPJ.cnpj IS NOT NULL THEN CGMPJ.cnpj
                                ELSE CGMPF.cpf
                           END AS nro_documento
                         , representante_legal.cpf AS cpf_representante_legal
                      FROM licitacao.contrato 
                 LEFT JOIN sw_cgm_pessoa_juridica AS CGMPJ
                        ON CGMPJ.numcgm=contrato.cgm_contratado 
                 LEFT JOIN sw_cgm_pessoa_fisica AS CGMPF
                        ON CGMPF.numcgm=contrato.cgm_contratado
                 LEFT JOIN sw_cgm_pessoa_fisica AS representante_legal
                        ON representante_legal.numcgm = contrato.cgm_representante_legal
                INNER JOIN sw_cgm AS CGM
                        ON CGM.numcgm=contrato.cgm_contratado
                     WHERE contrato.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                       AND (contrato.inicio_execucao <= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                        OR  contrato.inicio_execucao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                          )--ENTRADA MES
                       AND(contrato.fim_execucao >= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        OR  contrato.fim_execucao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                          )--ENTRADA MES
                       AND contrato.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE";
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato20.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato20(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato20().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato20()
    {
        $stSql  = " SELECT 20 as tipo_registro
                         , contrato.exercicio||LPAD(contrato.cod_entidade::VARCHAR,2,'0')||LPAD(contrato.num_contrato::VARCHAR,9,'0') as cod_contrato
                         , ( SELECT valor
                               FROM administracao.configuracao_entidade
                              WHERE exercicio=contrato.exercicio
                                AND parametro='tcemg_codigo_orgao_entidade_sicom'
                                AND cod_entidade=contrato.cod_entidade) AS cod_orgao
                         , LPAD(LPAD(contrato.num_orgao::VARCHAR,2,'0')||LPAD(contrato.num_unidade::VARCHAR,2,'0'),5,'0') as cod_unidade_sub
                         , (contrato_aditivos.exercicio||(LPAD(''||contrato_aditivos.cod_entidade,2, '0'))
                           ||(LPAD(''||contrato_aditivos.num_aditivo,2, '0'))||(LPAD(''||contrato.num_contrato,3, '0'))
                           ||(LPAD(''||contrato.cod_entidade,2, '0'))||(RIGHT (contrato.exercicio, 2))) AS cod_aditivo
                         , contrato.numero_contrato AS nro_contrato
                         , TO_CHAR(contrato.dt_assinatura, 'ddmmyyyy') AS dt_assinatura
                         , contrato_aditivos.num_aditivo
                         , to_char(contrato_aditivos.dt_assinatura, 'ddmmyyyy') AS data_assinatura_aditivo
                         , contrato_aditivos.tipo_valor AS tipo_alteracao_valor
                         , contrato_aditivos.tipo_termo_aditivo
                         , CASE WHEN tipo_termo_aditivo = 6 OR tipo_termo_aditivo = 14 THEN sem_acentos(contrato_aditivos.justificativa) END AS descricao_alteracao
                         , CASE WHEN tipo_termo_aditivo = 7 OR tipo_termo_aditivo = 13 THEN to_char(contrato_aditivos.dt_vencimento, 'ddmmyyyy')
                                WHEN tipo_termo_aditivo = 14 THEN to_char(contrato_aditivos.fim_execucao, 'ddmmyyyy')
                           END AS nova_data_termino
                         , contrato_aditivos.valor_contratado AS valor_aditivo
                         , to_char(publicacao_contrato_aditivos.dt_publicacao, 'ddmmyyyy') AS data_publicacao
                         , sw_cgm.nom_cgm AS veiculo_divulgacao
                      FROM licitacao.contrato
                INNER JOIN licitacao.contrato_aditivos
                        ON contrato_aditivos.num_contrato=contrato.num_contrato
                       AND contrato_aditivos.exercicio_contrato=contrato.exercicio
                       AND contrato_aditivos.cod_entidade=contrato.cod_entidade
                INNER JOIN licitacao.publicacao_contrato_aditivos
                        ON publicacao_contrato_aditivos.num_contrato=contrato_aditivos.num_contrato
                       AND publicacao_contrato_aditivos.exercicio_contrato=contrato_aditivos.exercicio_contrato
                       AND publicacao_contrato_aditivos.exercicio=contrato_aditivos.exercicio
                       AND publicacao_contrato_aditivos.cod_entidade=contrato_aditivos.cod_entidade
                       AND publicacao_contrato_aditivos.num_aditivo=contrato_aditivos.num_aditivo
                INNER JOIN sw_cgm
                        ON sw_cgm.numcgm=publicacao_contrato_aditivos.numcgm
                     WHERE contrato_aditivos.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                       AND contrato_aditivos.dt_assinatura BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy') --ENTRADA MES
                       AND contrato.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
                  ORDER BY nro_contrato, num_aditivo
        ";

        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato21.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato21(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato21().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato21()
    {
        $stSql  = " SELECT 21 as tipo_registro
                         , contrato.exercicio||LPAD(contrato.cod_entidade::VARCHAR,2,'0')||LPAD(contrato.num_contrato::VARCHAR,9,'0') as cod_contrato
                         , (contrato_aditivos.exercicio||(LPAD(''||contrato_aditivos.cod_entidade,2, '0'))
                           ||(LPAD(''||contrato_aditivos.num_aditivo,2, '0'))||(LPAD(''||contrato.num_contrato,3, '0'))
                           ||(LPAD(''||contrato.cod_entidade,2, '0'))||(RIGHT (contrato.exercicio, 2))) AS cod_aditivo
                         , mapa_item.cod_item
                         , contrato_aditivos.tipo_termo_aditivo
                         , contrato.tipo_objeto
                         , CASE WHEN contrato_aditivos.tipo_termo_aditivo = 9 then 1
                                WHEN contrato_aditivos.tipo_termo_aditivo = 10 then 2
                                ELSE 1
                           END AS tipo_alteracao_item
                         , REPLACE(ROUND(mapa_item.quantidade, 4)::TEXT, '.', ',') AS quantidade
                         , REPLACE(ROUND((mapa_item.vl_total/mapa_item.quantidade), 4)::TEXT, '.', ',') AS valor_unitario
                    FROM licitacao.contrato          
              INNER JOIN licitacao.contrato_aditivos 
                      ON contrato_aditivos.num_contrato=contrato.num_contrato
                     AND contrato_aditivos.exercicio_contrato=contrato.exercicio
                     AND contrato_aditivos.cod_entidade=contrato.cod_entidade
             -----Licitacao
               LEFT JOIN licitacao.contrato_licitacao
                      ON contrato_licitacao.num_contrato = contrato_aditivos.num_contrato
                     AND contrato_licitacao.exercicio = contrato_aditivos.exercicio_contrato
                     AND contrato_licitacao.cod_entidade = contrato_aditivos.cod_entidade
               LEFT JOIN licitacao.licitacao
                      ON licitacao.cod_licitacao = contrato_licitacao.cod_licitacao
                     AND licitacao.cod_modalidade = contrato_licitacao.cod_modalidade
                     AND licitacao.exercicio = contrato_licitacao.exercicio_licitacao
                     AND licitacao.cod_entidade = contrato_licitacao.cod_entidade 
             -------Compras
               LEFT JOIN licitacao.contrato_compra_direta
                      ON contrato_compra_direta.num_contrato = contrato_aditivos.num_contrato
                     AND contrato_compra_direta.exercicio = contrato_aditivos.exercicio_contrato
                     AND contrato_compra_direta.cod_entidade = contrato_aditivos.cod_entidade
               LEFT JOIN compras.compra_direta 
                      ON compra_direta.cod_compra_direta = contrato_compra_direta.cod_compra_direta
                     AND compra_direta.cod_modalidade = contrato_compra_direta.cod_modalidade
                     AND compra_direta.exercicio_entidade = contrato_compra_direta.exercicio_compra_direta
                     AND compra_direta.cod_entidade = contrato_compra_direta.cod_entidade 
              INNER JOIN compras.mapa
                      ON (mapa.exercicio =compra_direta.exercicio_mapa OR mapa.exercicio = licitacao.exercicio_mapa  )
                     AND (mapa.cod_mapa = compra_direta.cod_mapa OR mapa.cod_mapa = licitacao.cod_mapa)
              INNER JOIN compras.mapa_solicitacao
                      ON mapa_solicitacao.exercicio = mapa.exercicio
                     AND mapa_solicitacao.cod_mapa = mapa.cod_mapa 
              INNER JOIN compras.mapa_item 
                      ON mapa_item.exercicio = mapa_solicitacao.exercicio
                     AND mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
                     AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
                     AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
                     AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
                   WHERE contrato_aditivos.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                     AND contrato_aditivos.dt_assinatura BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy') --ENTRADA MES
                     AND contrato.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
                GROUP BY cod_contrato, cod_aditivo, cod_item, tipo_termo_aditivo, tipo_objeto, tipo_alteracao_item, quantidade, valor_unitario
        ";

        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato30.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato30(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato30().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato30()
    {
        $stSql  = " SELECT 30 as tipo_registro
                         , contrato.exercicio||LPAD(contrato.cod_entidade::VARCHAR,2,'0')||LPAD(contrato.num_contrato::VARCHAR,9,'0') as cod_contrato
                         , ( SELECT valor 
                               FROM administracao.configuracao_entidade
                              WHERE exercicio=contrato.exercicio
                                AND parametro='tcemg_codigo_orgao_entidade_sicom'
                                AND cod_entidade=contrato.cod_entidade) AS cod_orgao
                         , LPAD(LPAD(contrato.num_orgao::VARCHAR,2,'0')||LPAD(contrato.num_unidade::VARCHAR,2,'0'),5,'0') as cod_unidade_sub 
                         , contrato.numero_contrato AS nro_contrato
                         , TO_CHAR(contrato.dt_assinatura, 'ddmmyyyy') AS dt_assinatura_contrato_original
                         , contrato_apostila.cod_tipo AS tipo_apostila
                         , licitacao.seq_nro_contrato_apostila(contrato_apostila.exercicio, contrato_apostila.cod_entidade, contrato_apostila.num_contrato,contrato_apostila.cod_apostila)::varchar AS cod_apostila
                         , contrato_apostila.data_apostila
                         , contrato_apostila.cod_alteracao AS tipo_alteracao_apostila
                         , contrato_apostila.descricao
                         , contrato_apostila.valor_apostila
                      FROM licitacao.contrato
                INNER JOIN licitacao.contrato_apostila 
                        ON contrato_apostila.num_contrato= contrato.num_contrato
                       AND contrato_apostila.exercicio=contrato.exercicio
                       AND contrato_apostila.cod_entidade=contrato.cod_entidade
                     WHERE contrato_apostila.data_apostila BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy') --ENTRADA MES
                       AND contrato.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE ";
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato40.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato40(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato40().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato40()
    {
        $stSql  = " SELECT 40 as tipo_registro
                         , contrato.exercicio||LPAD(contrato.cod_entidade::VARCHAR,2,'0')||LPAD(contrato.num_contrato::VARCHAR,9,'0') as cod_contrato
                         , ( SELECT valor 
                               FROM administracao.configuracao_entidade
                              WHERE exercicio=contrato.exercicio
                                AND parametro='tcemg_codigo_orgao_entidade_sicom'
                                AND cod_entidade=contrato.cod_entidade) AS cod_orgao
                         , LPAD(LPAD(contrato.num_orgao::VARCHAR,2,'0')||LPAD(contrato.num_unidade::VARCHAR,2,'0'),5,'0') as cod_unidade_sub 
                         , contrato.numero_contrato AS nro_contrato
                         , TO_CHAR(contrato.dt_assinatura, 'ddmmyyyy') AS dt_assinatura_contrato
                         , TO_CHAR(rescisao_contrato.dt_rescisao, 'ddmmyyyy') AS dt_rescisao
                         , rescisao_contrato.vlr_indenizacao AS valor_cancelamento_contrato
                      FROM licitacao.contrato
                INNER JOIN licitacao.rescisao_contrato
                        ON rescisao_contrato.num_contrato=contrato.num_contrato
                       AND rescisao_contrato.exercicio_contrato=contrato.exercicio
                       AND rescisao_contrato.cod_entidade=contrato.cod_entidade
                     WHERE rescisao_contrato.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                       AND rescisao_contrato.dt_rescisao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy') --ENTRADA MES
                       AND contrato.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE ";
        return $stSql;
    }
	
	public function __destruct(){}

}
?>
