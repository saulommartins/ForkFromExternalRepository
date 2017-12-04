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
    * Classe de mapeamento
    * Data de Criação: 13/08/2014

    * @author Analista: Ane
    * @author Desenvolvedor: Carlos Adriano 

    * @package URBEM
    * @subpackage Mapeamento
    *
    * $Id: TTCEMGRelatorioRazaoDespesa.class.php 64774 2016-03-30 22:01:05Z michel $
    *
*/

class TTCEMGRelatorioRazaoDespesa extends Persistente
{
    public function __construct()
    {
        parent::Persistente();
    }
     
   /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosFundeb60.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosConsultaPrincipal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosConsultaPrincipal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );    
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosConsultaPrincipal() {
        $stSql  = "SELECT entidade
                        , empenho
                        , retorno.exercicio
                        , cgm
                        , credor
                        , dt_empenho
                        , valor
                        , valor_anulado
                        , descricao
                        , cod_recurso
                        , recurso::varchar
                        , despesa
                        , dotacao
                        , stData
                        , banco
                        , num_documento
                        , cod_nota
                        , cod_orgao
                        , cod_unidade
                        , vl_total
                        , vl_total_anulado
                        , unidade.nom_unidade
                        , orgao.nom_orgao     
                      FROM tcemg.razao_despesa('".$this->getDado('exercicio')."',                             
                                               '".$this->getDado('dt_inicial')."',                                    
                                               '".$this->getDado('dt_final')."',
                                               '".$this->getDado('entidade')."',                                        
                                               '".$this->getDado('num_orgao')."',
                                               '".$this->getDado('num_unidade')."',
                                               '".$this->getDado('num_pao')."',
                                               '".$this->getDado('cod_recurso')."',
                                               '".$this->getDado('situacao')."',
                                               '".$this->getDado('tipo_relatorio')."'
                                               ) as retorno( entidade            integer,        
                                                             empenho             integer,   
                                                             exercicio           char(4),  
                                                             cgm                 integer,                                                                       	                                                                                
                                                             credor              text,  
                                                             dt_empenho          text,   
                                                             valor               numeric,                                                                                       
                                                             valor_anulado       numeric,       
                                                             descricao           varchar, 
                                                             cod_recurso         integer,                                                                                      
                                                             recurso             varchar,                                                                                       
                                                             despesa             text,
                                                             dotacao             text,
                                                             stData              text,  
                                                             banco               varchar, 
                                                             num_documento       varchar,
                                                             cod_nota            integer,
                                                             cod_orgao           integer,
                                                             cod_unidade         integer,
                                                             vl_total            numeric,
                                                             vl_total_anulado    numeric
                                                          )
                      JOIN orcamento.unidade
	                    ON unidade.num_unidade = retorno.cod_unidade
	                   AND unidade.num_orgao   = retorno.cod_orgao
	                   AND unidade.exercicio   = retorno.exercicio

	                  JOIN orcamento.orgao
	                    ON orgao.num_orgao   = retorno.cod_orgao
	                   AND orgao.exercicio   = retorno.exercicio
                                 
                  ORDER BY cod_orgao
                         , cod_unidade
                         , cod_recurso
                         , dt_empenho
                         , entidade
                         , empenho
                         , cod_nota ";
        return $stSql;
    }
    public function recuperaDadosConsultaEmpenhoLiquidadoPago(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosConsultaEmpenhoLiquidadoPago().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosConsultaEmpenhoLiquidadoPago() {
        $stSql  = "CREATE TEMPORARY TABLE tmp_liquidado_razaodespesa AS (
                        SELECT nota_liquidacao.cod_entidade,
                               nota_liquidacao.cod_empenho,
                               nota_liquidacao.exercicio_empenho,
                               SUM(nota_liquidacao_item.vl_total) - SUM(coalesce(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_total
                                
                          FROM empenho.nota_liquidacao
                    
                    INNER JOIN empenho.nota_liquidacao_item
                            ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                           AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade 
                           AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota 
                    
                     LEFT JOIN empenho.nota_liquidacao_item_anulado
                            ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                           AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                           AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                           AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item                   
                           AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                           AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
                    
                         WHERE nota_liquidacao_item.exercicio_item = '".$this->getDado('exercicio')."'
                           AND to_date( to_char(nota_liquidacao.dt_liquidacao, 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')
                    
                      GROUP BY nota_liquidacao.cod_entidade,
                         nota_liquidacao.cod_empenho,
                         nota_liquidacao.exercicio_empenho
                    );
                    
                 SELECT num_orgao,
                        num_unidade,
                        entidade, 
                        empenho, 
                        exercicio, 
                        cgm, 
                        cgm||' - '||nom_cgm AS credor, 
                        dt_empenho, 
                        valor, 
                        valor_liquidado,
                        valor_pago,
                        descricao, 
                        cod_recurso,
                        recurso, 
                        cod_estrutural||' - '||descricao_despesa AS despesa,
                        dotacao,
                        dt_pagamento,
                        num_banco||' / '||num_agencia||' / '||num_conta_corrente AS banco,
                        cod_recurso_banco,
                        num_documento
                    
                    FROM (
                            SELECT empenho.cod_entidade AS entidade, 
                               empenho.cod_empenho AS empenho, 
                               empenho.exercicio AS exercicio, 
                               pre_empenho.cgm_beneficiario AS cgm, 
                               sw_cgm.nom_cgm,
                               cast( pre_empenho.descricao AS varchar ) AS descricao, 
                               sw_cgm.nom_cgm AS nome_conta, 
                               to_char(empenho.dt_empenho,'dd/mm/yyyy') AS dt_empenho,

                               sum(coalesce(empenho.vl_total, 0.00)) - sum(coalesce(empenho.vl_anulado, 0.00)) AS valor,
                               coalesce(pago.vl_total, 0.00) - coalesce(pago.vl_anulado, 0.00) AS valor_pago,
                               
                               ( SELECT vl_total 
                                   FROM tmp_liquidado_razaodespesa tlr 
                                  WHERE tlr.cod_entidade      = empenho.cod_entidade 
                                    AND tlr.exercicio_empenho = empenho.exercicio
                                    AND tlr.cod_empenho       = empenho.cod_empenho) AS valor_liquidado,

                               ped_d_cd.cod_recurso,
                               ped_d_cd.nom_recurso AS recurso, 
                               ped_d_cd.cod_estrutural AS cod_estrutural,
                               ped_d_cd.descricao AS descricao_despesa,
                               ped_d_cd.dotacao,
                               ped_d_cd.num_orgao,
                               ped_d_cd.num_unidade,
                               to_char(pago.timestamp,'dd/mm/yyyy') AS dt_pagamento,
                               banco.num_banco,
                               agencia.num_agencia,
                               conta_corrente.num_conta_corrente,
                               plano_recurso.cod_recurso AS cod_recurso_banco,
                               pago.num_documento
                            FROM 
                              (
                                 SELECT
                                        empenho.cod_entidade
                                      , empenho.cod_empenho
                                      , empenho.exercicio
                                      , empenho.dt_empenho
                                      , empenho.cod_categoria
                                      , item_pre_empenho.vl_total
                                      , item_pre_empenho.cod_pre_empenho
                                      , item_pre_empenho.num_item
                                      , sum(empenho_anulado_item.vl_anulado) AS vl_anulado
                                      
                                    FROM empenho.empenho
                
                              INNER JOIN empenho.item_pre_empenho
                                      ON empenho.exercicio       = item_pre_empenho.exercicio
                                     AND empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho 
                
                               LEFT JOIN empenho.empenho_anulado
                                      ON empenho_anulado.exercicio    = empenho.exercicio
                                     AND empenho_anulado.cod_entidade = empenho.cod_entidade
                                     AND empenho_anulado.cod_empenho  = empenho.cod_empenho
                                     AND to_date( to_char(empenho_anulado.timestamp, 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')
                
                               LEFT JOIN empenho.empenho_anulado_item
                                      ON empenho_anulado_item.exercicio    = empenho_anulado.exercicio
                                     AND empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                                     AND empenho_anulado_item.cod_empenho  = empenho_anulado.cod_empenho
                                     AND empenho_anulado_item.timestamp    = empenho_anulado.timestamp
                                     AND empenho_anulado_item.exercicio    = empenho.exercicio
                                     AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                                     AND empenho_anulado_item.num_item = item_pre_empenho.num_item
                                     
                                WHERE to_date(to_char(empenho.dt_empenho, 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')
                                
                                GROUP BY  empenho.cod_entidade
                                        , empenho.cod_empenho
                                        , empenho.exercicio
                                        , empenho.dt_empenho
                                        , empenho.cod_categoria
                                        , item_pre_empenho.vl_total
                                        , item_pre_empenho.cod_pre_empenho
                                        , item_pre_empenho.num_item
                                ) AS empenho
                                  
                         INNER JOIN empenho.pre_empenho
                                 ON empenho.exercicio       = pre_empenho.exercicio
                                AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                
                         INNER JOIN empenho.historico
                                 ON historico.cod_historico = pre_empenho.cod_historico    
                                AND historico.exercicio     = pre_empenho.exercicio    
                
                         INNER JOIN sw_cgm
                                 ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario
                               
                         LEFT JOIN ( SELECT nota_liquidacao.cod_entidade,
                                        nota_liquidacao.cod_empenho,
                                        nota_liquidacao.exercicio,
                                        nota_liquidacao.exercicio_empenho,
                                        nota_liquidacao_paga.timestamp,
                                        nota_liquidacao_conta_pagadora.cod_plano,
                                        pagamento_tipo_documento.num_documento,
                                        sum(nota_liquidacao_paga.vl_pago)            as vl_total,
                                        sum(nota_liquidacao_paga_anulada.vl_anulado) as vl_anulado
                
                                       FROM empenho.nota_liquidacao
                           
                                 INNER JOIN empenho.nota_liquidacao_paga 
                                     ON nota_liquidacao_paga.exercicio    = nota_liquidacao.exercicio 
                                    AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                                    AND nota_liquidacao_paga.cod_nota     = nota_liquidacao.cod_nota
                                           
                                LEFT JOIN empenho.nota_liquidacao_paga_anulada 
                                         ON nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio 
                                    AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                                    AND nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
                                    AND nota_liquidacao_paga_anulada.timestamp    = nota_liquidacao_paga.timestamp 
                
                                 INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                                     ON pagamento_liquidacao_nota_liquidacao_paga.cod_entidade         = nota_liquidacao_paga.cod_entidade
                                    AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota             = nota_liquidacao_paga.cod_nota
                                    AND pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                                    AND pagamento_liquidacao_nota_liquidacao_paga.timestamp            = nota_liquidacao_paga.timestamp 
                
                                 INNER JOIN empenho.nota_liquidacao_conta_pagadora
                                     ON nota_liquidacao_conta_pagadora.cod_entidade         = nota_liquidacao_paga.cod_entidade
                                    AND nota_liquidacao_conta_pagadora.cod_nota             = nota_liquidacao_paga.cod_nota
                                    AND nota_liquidacao_conta_pagadora.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                                    AND nota_liquidacao_conta_pagadora.timestamp            = nota_liquidacao_paga.timestamp 
                
                                  LEFT JOIN tcemg.pagamento_tipo_documento
                                     ON pagamento_tipo_documento.exercicio    = nota_liquidacao_paga.exercicio
                                    AND pagamento_tipo_documento.cod_nota     = nota_liquidacao_paga.cod_nota
                                    AND pagamento_tipo_documento.cod_entidade = nota_liquidacao_paga.cod_entidade
                                    AND pagamento_tipo_documento.timestamp    = nota_liquidacao_paga.timestamp
                
                                      WHERE to_date(to_char(nota_liquidacao_paga.timestamp, 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')
                
                                   GROUP BY nota_liquidacao.cod_entidade,
                                            nota_liquidacao.cod_empenho,
                                            nota_liquidacao.exercicio,
                                            nota_liquidacao.exercicio_empenho,
                                            nota_liquidacao_paga.timestamp,
                                            pagamento_tipo_documento.num_documento,
                                            nota_liquidacao_conta_pagadora.cod_plano
                                  ) AS pago
                
                                 ON pago.exercicio_empenho = empenho.exercicio
                                AND pago.cod_entidade      = empenho.cod_entidade
                                AND pago.cod_empenho       = empenho.cod_empenho    
                               
                        LEFT JOIN contabilidade.plano_analitica
                                ON plano_analitica.exercicio = pago.exercicio
                               AND plano_analitica.cod_plano = pago.cod_plano
                
                        LEFT JOIN contabilidade.plano_recurso
                                ON plano_recurso.exercicio = plano_analitica.exercicio
                               AND plano_recurso.cod_plano = plano_analitica.cod_plano
                               
                        LEFT JOIN contabilidade.plano_banco
                                ON plano_banco.exercicio = plano_analitica.exercicio
                               AND plano_banco.cod_plano = plano_analitica.cod_plano
                
                        LEFT JOIN monetario.conta_corrente
                                ON conta_corrente.cod_banco          = plano_banco.cod_banco
                               AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                               AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                
                        LEFT JOIN monetario.agencia
                                ON agencia.cod_banco   = conta_corrente.cod_banco
                               AND agencia.cod_agencia = conta_corrente.cod_agencia
                
                        LEFT JOIN monetario.banco
                                ON banco.cod_banco = conta_corrente.cod_banco
                
                        LEFT JOIN (
                                    SELECT
                                        pre_empenho_despesa.exercicio, 
                                        pre_empenho_despesa.cod_pre_empenho,
                                        despesa.num_orgao,     
                                        despesa.num_unidade,   
                                        despesa.cod_funcao,    
                                        despesa.cod_subfuncao, 
                                        programa.num_programa,
                                        despesa.num_pao,      
                                        LPAD(despesa.num_orgao::VARCHAR, 2, '0')||'.'||LPAD(despesa.num_unidade::VARCHAR, 2, '0')||'.'||despesa.cod_funcao||'.'||despesa.cod_subfuncao||'.'||programa.num_programa||'.'||LPAD(despesa.num_pao::VARCHAR, 4, '0')||'.'||REPLACE(conta_despesa.cod_estrutural, '.', '') AS dotacao,
                                        despesa.cod_recurso, 
                                        despesa.cod_despesa,
                                        recurso.nom_recurso, 
                                        despesa.cod_conta,
                                        conta_despesa.cod_estrutural, 
                                        conta_despesa.descricao, 
                                        recurso.masc_recurso_red,
                                        recurso.cod_detalhamento,
                                        ppa.acao.num_acao
                                    
                                  FROM  empenho.pre_empenho_despesa
                
                            INNER JOIN orcamento.despesa
                                    ON pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                                   AND pre_empenho_despesa.exercicio   = despesa.exercicio  
                                  
                            INNER JOIN orcamento.recurso('".$this->getDado('exercicio')."') AS recurso
                                    ON recurso.cod_recurso = despesa.cod_recurso
                                   AND recurso.exercicio   = despesa.exercicio
                
                            INNER JOIN orcamento.programa_ppa_programa
                                    ON programa_ppa_programa.cod_programa = despesa.cod_programa
                                   AND programa_ppa_programa.exercicio   = despesa.exercicio
                 
                            INNER JOIN ppa.programa
                                    ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                  
                            INNER JOIN orcamento.pao_ppa_acao
                                    ON pao_ppa_acao.num_pao = despesa.num_pao
                                   AND pao_ppa_acao.exercicio = despesa.exercicio
                
                            INNER JOIN ppa.acao 
                                    ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                
                            INNER JOIN orcamento.conta_despesa
                                    ON pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
                                   AND pre_empenho_despesa.exercicio = conta_despesa.exercicio
                                          
                                 WHERE pre_empenho_despesa.exercicio = '".$this->getDado('exercicio')."'   
                            ) AS ped_d_cd 
                            
                            ON pre_empenho.exercicio       = ped_d_cd.exercicio 
                           AND pre_empenho.cod_pre_empenho = ped_d_cd.cod_pre_empenho 
                
                       WHERE empenho.exercicio = '".$this->getDado('exercicio')."'
                         AND empenho.cod_entidade  IN (".$this->getDado('entidade').") ";
                         
                         
                    switch($this->getDado('tipo_relatorio')) {
                             case 'fundeb_60':
                             $stSql .= " AND ped_d_cd.cod_recurso = 118";
                             break;
                             
                             case 'fundeb_40':
                             $stSql .= " AND ped_d_cd.cod_recurso = 119";
                             break;
                             
                             case 'ensino_fundamental':
                             $stSql .= " AND ped_d_cd.cod_subfuncao =  361";
                             break;
                             
                             case 'gasto_25':
                             $stSql .= " AND ped_d_cd.cod_recurso = 101";
                             break;
                             
                             case 'saude':
                             $stSql .= " AND ped_d_cd.cod_recurso = 102";
                             break;
                             
                             case 'diversos':
                             $stSql .= " AND ped_d_cd.cod_recurso = 100";
                             break;
                            //falta educacao_extra_orcamentario e restos_pagar
                         }
                         
                         if($this->getDado('num_orgao') != '') {
                            $stSql .= " AND ped_d_cd.num_orgao = ".$this->getDado('num_orgao');
                         }

                         if($this->getDado('num_unidade') != '') {
                            $stSql .= " AND ped_d_cd.num_unidade = ".$this->getDado('num_unidade');
                         }
                         
                         if($this->getDado('num_pao') != '') {
                            $stSql .= " AND ped_d_cd.num_pao = ".$this->getDado('num_pao');
                         }     

                   $stSql.= " GROUP BY  ped_d_cd.num_orgao,
                                        ped_d_cd.num_unidade,
                                        empenho.dt_empenho, 
                                        empenho.cod_pre_empenho, 
                                        empenho.cod_entidade, 
                                        empenho.cod_empenho, 
                                        empenho.exercicio, 
                                        pre_empenho.cgm_beneficiario, 
                                        sw_cgm.nom_cgm, 
                                        pre_empenho.descricao, 
                                        ped_d_cd.cod_estrutural, 
                                        ped_d_cd.cod_recurso,
                                        ped_d_cd.nom_recurso, 
                                        ped_d_cd.descricao,
                                        ped_d_cd.dotacao,
                                        pago.vl_total,
                                        pago.vl_anulado,
                                        pago.timestamp,
                                        banco.num_banco,
                                        agencia.num_agencia,
                                        conta_corrente.num_conta_corrente,
                                        plano_recurso.cod_recurso,
                                        pago.num_documento
                    ) AS tbl
        
                    WHERE valor <> '0.00'
                    
                ORDER BY num_orgao, num_unidade, entidade, exercicio, empenho, dt_pagamento;
        ";
                          
        return $stSql;
    }
    
/**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosEducacaoExtraOrcamentario.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosRestosPagar(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosRestosPagar().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaDadosRestosPagar() 
    {
        $stSql  = " SELECT   
                            *
                    FROM tcemg.relatorio_restos_pagar_dotacao_credor
                    (    ''
                         , ''                      
                         , '".$this->getDado('dt_inicial')."'
                         , '".$this->getDado('dt_final')."'
                         , '".$this->getDado('entidade')."'
                         , ''
                         , ''
                         , ''
                         , ''
                         , ''
                         , ''
                         , ''
                         , '1'
                         , ''
                         , 'true'
                         , ''
                         , ''
                    )
              
                    WHERE 1=1 ";
        
            if($this->getDado('cod_recurso') != '') {
               $stSql .= " AND cod_recurso IN (".$this->getDado('cod_recurso').")";
            }

        return $stSql;
    }

/**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosDespesaExtraOrcamentaria.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosDespesaExtraOrcamentaria(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosDespesaExtraOrcamentaria().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaDadosDespesaExtraOrcamentaria() {
        $stSql  = " SELECT tabela.*
                         , banco.num_banco||' - '||banco.nom_banco AS banco
                         , plano_recurso.cod_recurso AS cod_recurso_banco
                      FROM (
                          SELECT plano_debito.exercicio
                               , plano_debito.dt_pagamento
                               , sum(coalesce(plano_debito.vl_pago,0.00)) AS valor
                               , sum(coalesce(plano_debito.vl_estornado,0.00)) AS valor_estornado
                               , plano_debito.tipo_despesa
                               , plano_debito.nom_conta
                               , plano_debito.cod_entidade
                               , plano_debito.nom_entidade
                               , NULL AS documento
                               , plano_debito.timestamp_transferencia AS dt_transferencia
                               , plano_debito.nome_despesa
                               , plano_debito.cgm_credor
                               , plano_debito.credor
                               , coalesce(plano_debito.cod_recurso, 9999999999) AS cod_recurso
                               , coalesce(plano_debito.cod_recurso, 9999999999) ||' - '|| coalesce(plano_debito.nom_recurso, '') AS nom_recurso
                               , plano_debito.bo_estornado
                               , plano_debito.cod_plano                              
                          FROM (
                              ---------------------------------------------
                              --                   PAGAMENTOS EXTRA 
                              ---------------------------------------------
                      
                             SELECT transferencia.exercicio
                                  , to_char(transferencia.timestamp_transferencia::DATE, 'DD/MM/YYYY') AS dt_pagamento
                                  , SUM(coalesce(transferencia.valor,0.00)) AS vl_pago
                                  , SUM(coalesce(transferencia_estornada.valor,0.00)) AS vl_estornado
                                  , CAST('EXT' AS VARCHAR) AS tipo_despesa
                                  , CPC.nom_conta
                                  , OE.cod_entidade
                                  , OE.nom_cgm AS nom_entidade
                                  , transferencia.timestamp_transferencia::DATE
                                  , CPCD.nome_despesa
                                  , transferencia_credor.numcgm AS cgm_credor
                                  , cgm_credor.nom_cgm AS credor
                                  , CPCD.cod_recurso
                                  , CPCD.nom_recurso
                                  , CASE WHEN transferencia_estornada.cod_lote IS NOT NULL
                                         THEN TRUE
                                         ELSE FALSE
                                    END AS bo_estornado
                                  , transferencia.cod_plano_credito AS cod_plano
                               FROM tesouraria.transferencia
                                 -- BUSCA CONTA BANCO        
                         INNER JOIN ( SELECT CPA.cod_plano || ' - ' || CPC.nom_conta AS nom_conta                
                                           , CPA.cod_plano
                                           , CPA.exercicio 
                                        FROM contabilidade.plano_conta AS CPC
                                  INNER JOIN contabilidade.plano_analitica AS CPA
                                          ON CPC.cod_conta = CPA.cod_conta
                                         AND CPC.exercicio = CPA.exercicio 
                                    ) AS CPC
                                 ON transferencia.cod_plano_credito= CPC.cod_plano
                                AND transferencia.exercicio        = CPC.exercicio 

                                 -- BUSCA CONTA DESPESA        
                         INNER JOIN ( SELECT CPC.cod_estrutural || ' - ' || CPA.cod_plano || ' - ' || CPC.nom_conta AS nome_despesa                
                                           , CPA.cod_plano
                                           , CPA.exercicio
                                           , recurso.cod_recurso
                                           , recurso.nom_recurso
                                        FROM contabilidade.plano_conta AS CPC
                                  INNER JOIN contabilidade.plano_analitica AS CPA
                                          ON CPC.cod_conta = CPA.cod_conta
                                         AND CPC.exercicio = CPA.exercicio
                                   LEFT JOIN contabilidade.plano_recurso
                                          ON plano_recurso.exercicio = CPA.exercicio
                                         AND plano_recurso.cod_plano = CPA.cod_plano
                                   LEFT JOIN orcamento.recurso
                                          ON recurso.exercicio   = plano_recurso.exercicio
                                         AND recurso.cod_recurso = plano_recurso.cod_recurso
                                    ) AS CPCD
                                 ON transferencia.cod_plano_debito = CPCD.cod_plano
                                AND transferencia.exercicio        = CPCD.exercicio 

                                 -- BUSCA ENTIDADE
                         INNER JOIN ( SELECT OE.cod_entidade || ' - ' || CGM.nom_cgm AS entidade
                                           , CGM.nom_cgm
                                           , OE.cod_entidade
                                           , OE.exercicio     
                                        FROM orcamento.entidade AS OE
                                  INNER JOIN sw_cgm AS CGM 
                                          ON OE.numcgm = CGM.numcgm
                                    ) AS OE
                                 ON OE.cod_entidade = transferencia.cod_entidade
                                AND OE.exercicio    = transferencia.exercicio

                          LEFT JOIN tesouraria.transferencia_credor
                                 ON transferencia_credor.cod_entidade    = transferencia.cod_entidade
                                AND transferencia_credor.tipo            = transferencia.tipo
                                AND transferencia_credor.exercicio       = transferencia.exercicio
                                AND transferencia_credor.cod_lote        = transferencia.cod_lote

                          LEFT JOIN sw_cgm AS cgm_credor 
                                 ON cgm_credor.numcgm = transferencia_credor.numcgm
                                
                          LEFT JOIN tesouraria.transferencia_estornada
                                 ON transferencia_estornada.cod_entidade    = transferencia.cod_entidade
                                AND transferencia_estornada.tipo            = transferencia.tipo
                                AND transferencia_estornada.exercicio       = transferencia.exercicio
                                AND transferencia_estornada.cod_lote        = transferencia.cod_lote

                              WHERE transferencia.cod_tipo = 1
                                AND TO_DATE(TO_CHAR(transferencia.timestamp_transferencia,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN to_date('".$this->getDado('dt_inicial')."'::VARCHAR,'dd/mm/yyyy')  AND to_date('".$this->getDado('dt_final')."'::VARCHAR,'dd/mm/yyyy')
                                AND TO_CHAR(transferencia.timestamp_transferencia,'yyyy') = '".$this->getDado('exercicio')."'

                           GROUP BY transferencia.exercicio
                                  , transferencia.timestamp_transferencia::DATE
                                  , CPC.nom_conta
                                  , OE.cod_entidade
                                  , OE.nom_cgm
                                  , CPCD.nome_despesa
                                  , transferencia_credor.numcgm
                                  , cgm_credor.nom_cgm
                                  , CPCD.cod_recurso
                                  , CPCD.nom_recurso
                                  , transferencia_estornada.cod_lote
                                  , transferencia.cod_plano_credito
                      
                              UNION ALL
                      
                              ---------------------------------------------
                              --                  PAGAMENTOS RESTOS 
                              ---------------------------------------------
                              SELECT plano.exercicio
                                   , plano.dt_pagamento
                                   , SUM(coalesce(plano.vl_pago,0.00)) AS vl_pago
                                   , SUM(coalesce(plano.vl_estornado,0.00)) AS vl_estornado
                                   , plano.tipo_despesa
                                   , plano.nom_conta
                                   , plano.cod_entidade
                                   , plano.nom_entidade
                                   , plano.timestamp_transferencia
                                   , CPC.cod_estrutural || ' - ' || cpa.cod_plano || ' - ' || CPC.nom_conta AS nome_despesa
                                   , plano.cgm_credor
                                   , plano.credor
                                   , recurso.cod_recurso
                                   , recurso.nom_recurso
                                   , plano.bo_estornado
                                   , plano.cod_plano
                                   
                                FROM (
                                     SELECT tp.exercicio_plano AS exercicio
                                          , to_char(tp.timestamp::DATE, 'DD/MM/YYYY') AS dt_pagamento
                                          , SUM(coalesce(nlp.vl_pago,0.00)) AS vl_pago
                                          , SUM(coalesce(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_estornado 
                                          , cast('RES' AS varchar) AS tipo_despesa
                                          , plano_banco.nom_conta AS nom_conta
                                          , oe.cod_entidade
                                          , cgm.nom_cgm AS nom_entidade
                                          , tp.timestamp::DATE AS timestamp_transferencia
                                          , contabilidade.fn_recupera_conta_lancamento( CP.exercicio
                                                                                      , CP.cod_entidade
                                                                                      , CP.cod_lote
                                                                                      , CP.tipo
                                                                                      , CP.sequencia
                                                                                      , 'D'
                                                                                      ) AS cod_plano_debito
                                          , cgm_beneficiario.numcgm AS cgm_credor
                                          , cgm_beneficiario.nom_cgm AS credor
                                          , CASE WHEN nota_liquidacao_paga_anulada.timestamp::DATE IS NOT NULL
                                                 THEN TRUE
                                                 ELSE FALSE
                                            END AS bo_estornado
                                          , tp.cod_plano

                                       FROM (
                                             SELECT CPA.cod_plano || ' - ' || CPC.nom_conta AS nom_conta                
                                                  , cpa.cod_plano AS cod_plano
                                                  , cpa.exercicio
                                               FROM contabilidade.plano_conta AS cpc
                                         INNER JOIN contabilidade.plano_analitica AS cpa
                                                 ON cpa.cod_conta = cpc.cod_conta
                                                AND cpc.exercicio = cpa.exercicio
                                            ) AS plano_banco
                                 INNER JOIN tesouraria.pagamento AS TP
                                         ON tp.cod_plano = plano_banco.cod_plano
                                        AND tp.exercicio_plano = plano_banco.exercicio
                                          
                                 INNER JOIN orcamento.entidade AS oe
                                         ON oe.cod_entidade  = tp.cod_entidade
                                        AND oe.exercicio = tp.exercicio

                                 INNER JOIN sw_cgm AS cgm
                                         ON oe.numcgm = cgm.numcgm

                                 INNER JOIN empenho.nota_liquidacao_paga AS nlp
                                         ON nlp.cod_nota     = tp.cod_nota
                                        AND nlp.cod_entidade = tp.cod_entidade
                                        AND nlp.exercicio    = tp.exercicio
                                        AND nlp.timestamp    = tp.timestamp

                                  LEFT JOIN empenho.nota_liquidacao_paga_anulada
                                         ON nota_liquidacao_paga_anulada.exercicio    = nlp.exercicio
                                        AND nota_liquidacao_paga_anulada.cod_entidade = nlp.cod_entidade
                                        AND nota_liquidacao_paga_anulada.cod_nota     = nlp.cod_nota
                                        AND nota_liquidacao_paga_anulada.timestamp    = nlp.timestamp

                                 INNER JOIN empenho.nota_liquidacao AS nl
                                         ON nl.cod_nota     = nlp.cod_nota
                                        AND nl.exercicio    = nlp.exercicio
                                        AND nl.cod_entidade = nlp.cod_entidade
                                        AND nl.exercicio_empenho < '".$this->getDado('exercicio')."'

                                 INNER JOIN empenho.empenho
                                         ON empenho.exercicio    = nl.exercicio_empenho
                                        AND empenho.cod_entidade = nl.cod_entidade
                                        AND empenho.cod_empenho  = nl.cod_empenho

                                 INNER JOIN empenho.pre_empenho
                                         ON empenho.exercicio       = pre_empenho.exercicio
                                        AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                                 INNER JOIN sw_cgm as cgm_beneficiario
                                         ON cgm_beneficiario.numcgm = pre_empenho.cgm_beneficiario

                                 INNER JOIN contabilidade.pagamento AS cp
                                         ON cp.cod_entidade         = nlp.cod_entidade
                                        AND cp.exercicio_liquidacao = nlp.exercicio
                                        AND cp.cod_nota             = nlp.cod_nota
                                        AND cp.timestamp            = nlp.timestamp

                                 INNER JOIN contabilidade.lancamento_empenho AS cle
                                         ON cle.cod_lote     = cp.cod_lote
                                        AND cle.cod_entidade = cp.cod_entidade
                                        AND cle.sequencia    = cp.sequencia
                                        AND cle.exercicio    = cp.exercicio
                                        AND cle.tipo         = cp.tipo

                                      WHERE TO_DATE(TO_CHAR(tp.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN to_date('".$this->getDado('dt_inicial')."'::VARCHAR,'dd/mm/yyyy')  AND to_date('".$this->getDado('dt_final')."'::VARCHAR,'dd/mm/yyyy')
                                        AND TO_CHAR(tp.timestamp,'yyyy') = '".$this->getDado('exercicio')."'

                                   GROUP BY tp.exercicio_plano
                                          , tp.timestamp::DATE
                                          , plano_banco.nom_conta
                                          , oe.cod_entidade
                                          , cgm.nom_cgm
                                          , CP.exercicio
                                          , CP.cod_entidade
                                          , CP.cod_lote
                                          , CP.tipo
                                          , CP.sequencia
                                          , cgm_beneficiario.numcgm
                                          , cgm_beneficiario.nom_cgm
                                          , nota_liquidacao_paga_anulada.timestamp::DATE
                                          , tp.cod_plano
                                     ) AS plano
                          INNER JOIN contabilidade.plano_analitica AS cpa
                                  ON plano.cod_plano_debito   = cpa.cod_plano
                                 AND plano.exercicio          = cpa.exercicio

                          INNER JOIN contabilidade.plano_conta AS cpc
                                  ON cpa.cod_conta = cpc.cod_conta
                                 AND cpa.exercicio = cpc.exercicio
                                 AND CPC.cod_estrutural like '2.1.2.1.1%'

                           LEFT JOIN contabilidade.plano_recurso
                                  ON plano_recurso.exercicio = cpa.exercicio
                                 AND plano_recurso.cod_plano = cpa.cod_plano
                                  
                           LEFT JOIN orcamento.recurso
                                  ON recurso.exercicio   = plano_recurso.exercicio
                                 AND recurso.cod_recurso = plano_recurso.cod_recurso

                            GROUP BY plano.exercicio
                                   , plano.dt_pagamento
                                   , plano.tipo_despesa
                                   , plano.nom_conta
                                   , plano.cod_entidade
                                   , plano.nom_entidade
                                   , plano.timestamp_transferencia
                                   , CPC.cod_estrutural
                                   , CPC.nom_conta
                                   , cpa.cod_plano
                                   , plano.cgm_credor
                                   , plano.credor
                                   , recurso.cod_recurso
                                   , recurso.nom_recurso
                                   , plano.bo_estornado
                                   , plano.cod_plano
                               ) AS plano_debito
                      GROUP BY plano_debito.exercicio
                             , plano_debito.dt_pagamento
                             , plano_debito.tipo_despesa
                             , plano_debito.nom_conta
                             , plano_debito.cod_entidade
                             , plano_debito.nom_entidade
                             , plano_debito.timestamp_transferencia
                             , plano_debito.nome_despesa
                             , plano_debito.cgm_credor
                             , plano_debito.credor
                             , plano_debito.cod_recurso
                             , plano_debito.nom_recurso
                             , plano_debito.bo_estornado
                             , plano_debito.cod_plano
                      ) AS tabela
                   -- BLOCO UTILIZADO PARA BUSCAR BANCO
            INNER JOIN contabilidade.plano_banco
                    ON plano_banco.exercicio = tabela.exercicio
                   AND plano_banco.cod_plano = tabela.cod_plano

            INNER JOIN contabilidade.plano_recurso
                    ON plano_recurso.exercicio = plano_banco.exercicio
                   AND plano_recurso.cod_plano = plano_banco.cod_plano
                      
            INNER JOIN monetario.conta_corrente
                    ON conta_corrente.cod_banco          = plano_banco.cod_banco
                   AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                   AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                      
            INNER JOIN monetario.agencia
                    ON agencia.cod_banco   = conta_corrente.cod_banco
                   AND agencia.cod_agencia = conta_corrente.cod_agencia
                      
            INNER JOIN monetario.banco
                    ON banco.cod_banco = conta_corrente.cod_banco

                 WHERE 1=1
        ";

        if($this->getDado('cod_recurso') != '') {
            $stSql .= " AND plano_recurso.cod_recurso IN (".$this->getDado('cod_recurso').") ";
        }

        if($this->getDado('entidade') != '') {
            $stSql .= " AND tabela.cod_entidade IN (".$this->getDado('entidade').") ";
        }

        $stSql .=  " ORDER BY dt_transferencia ";

        return $stSql;
    }
    
    
/**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosDespesaExtraOrcamentaria.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosReceitaExtraOrcamentaria(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosReceitaExtraOrcamentaria().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaDadosReceitaExtraOrcamentaria() {
    
        $stSql = " SELECT   nome_conta_banco AS banco
                           , TO_CHAR(dt_transferencia,'dd/mm/yyyy') as dt_transferencia
                           , valor
                           , num_conta_corrente
                           , nom_banco
                           , cod_estrutural||' - '||nom_conta AS nome_conta
                           , cod_recurso||' - '|| nom_recurso AS nom_recurso
           
             FROM (
                   SELECT  transferencia.exercicio
                        ,  transferencia.timestamp_transferencia as dt_transferencia
                        ,  transferencia.timestamp_transferencia
                        ,  transferencia.cod_lote
                        ,  transferencia.tipo
                        ,  transferencia.cod_tipo
                        ,  plano_analitica.cod_plano
                        ,  transferencia.cod_plano_credito 
                        ,  transferencia.valor AS valor
                        ,  recibo_extra_transferencia.cod_recibo_extra
                        ,  'ARR' AS tipo_receita
                        ,  conta_corrente.num_conta_corrente
                        ,  banco.nom_banco
                        ,  entidade.cod_entidade
                        ,  entidade_cgm.nom_cgm as nom_entidade
                        ,  plano_conta.nom_conta
                        ,  plano_conta.cod_estrutural
                        ,  CASE WHEN plano_recurso.cod_recurso  IS NULL
                               THEN '9999999999'
                               ELSE TO_CHAR(plano_recurso.cod_recurso,'9999999999')
                           END as cod_recurso
                        ,  recurso.nom_recurso
                        ,  recurso.masc_recurso_red
                        ,  recurso.cod_detalhamento
                        ,  conta_banco.nome_conta_banco
                        ,  conta_banco.cod_plano_banco
           
                           FROM  tesouraria.transferencia
                           
                      LEFT JOIN tesouraria.recibo_extra_transferencia 
                             ON recibo_extra_transferencia.cod_lote = transferencia.cod_lote
                            AND recibo_extra_transferencia.cod_entidade = transferencia.cod_entidade
                            AND recibo_extra_transferencia.exercicio = transferencia.exercicio
                            AND recibo_extra_transferencia.tipo = transferencia.tipo
           
           
                     INNER JOIN contabilidade.plano_analitica 
                     ON plano_analitica.cod_plano = transferencia.cod_plano_credito
                            AND plano_analitica.exercicio = transferencia.exercicio
           
                      LEFT JOIN contabilidade.plano_recurso 
                     ON plano_recurso.exercicio = plano_analitica.exercicio
                            AND plano_recurso.cod_plano = plano_analitica.cod_plano
                     
                      LEFT JOIN orcamento.recurso('".$this->getDado('exercicio')."') AS recurso 
                             ON recurso.cod_recurso = plano_recurso.cod_recurso
                            AND recurso.exercicio = plano_recurso.exercicio
           
                     INNER JOIN contabilidade.plano_conta 
                             ON plano_conta.cod_conta = plano_analitica.cod_conta
                            AND plano_conta.exercicio = plano_analitica.exercicio
           
                      LEFT JOIN contabilidade.plano_banco 
                             ON plano_banco.cod_plano = transferencia.cod_plano_credito
                            AND plano_banco.exercicio = transferencia.exercicio
                     
                      LEFT JOIN monetario.conta_corrente 
                     ON conta_corrente.cod_banco = plano_banco.cod_banco
                            AND conta_corrente.cod_agencia = plano_banco.cod_agencia
                            AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                            
                      LEFT JOIN monetario.banco 
                     ON banco.cod_banco = conta_corrente.cod_banco
           
                     INNER JOIN orcamento.entidade 
                     ON entidade.cod_entidade = transferencia.cod_entidade
                            AND entidade.exercicio = transferencia.exercicio
           
                     INNER JOIN sw_cgm as entidade_cgm 
                     ON entidade_cgm.numcgm = entidade.numcgm
           
                     INNER JOIN (SELECT
                         CPA.cod_plano || ' - ' || CPC.nom_conta as nome_conta_banco
                       , CPA.cod_plano as cod_plano_banco
                       , CPA.exercicio 
                       FROM
                       contabilidade.plano_conta as CPC,
                       contabilidade.plano_analitica as CPA
                       WHERE 
                       CPC.cod_conta = CPA.cod_conta AND
                       CPC.exercicio = CPA.exercicio
                       ) as conta_banco 
                        ON conta_banco.cod_plano_banco = transferencia.cod_plano_debito
                   AND conta_banco.exercicio = transferencia.exercicio
               
               UNION
               
                   SELECT  transferencia.exercicio
                        ,  transferencia.timestamp_transferencia as dt_transferencia
                        ,  transferencia_estornada.timestamp_estornada as timestamp_transferencia
                        ,  transferencia.cod_lote
                        ,  transferencia.tipo
                        ,  transferencia.cod_tipo
                        ,  transferencia.cod_plano_credito 
                        ,  plano_analitica.cod_plano
                        ,  (transferencia_estornada.valor * -1) AS valor
                        ,  recibo_extra_transferencia.cod_recibo_extra
                        ,  'EST' AS tipo_receita
                        ,  conta_corrente.num_conta_corrente
                        ,  banco.nom_banco
                        ,  entidade.cod_entidade
                        ,  entidade_cgm.nom_cgm as nom_entidade
                        ,  plano_conta.nom_conta
                        ,  plano_conta.cod_estrutural
                        ,  CASE WHEN plano_recurso.cod_recurso  IS NULL
                               THEN '9999999999'
                               ELSE TO_CHAR(plano_recurso.cod_recurso,'9999999999')
                           END AS cod_recurso
                        ,  recurso.nom_recurso
                        ,  recurso.masc_recurso_red
                    ,  recurso.cod_detalhamento
                        ,  conta_banco.nome_conta_banco
                        ,  conta_banco.cod_plano_banco
           
                     FROM  tesouraria.transferencia
               INNER JOIN  tesouraria.transferencia_estornada
                       ON  transferencia_estornada.exercicio = transferencia.exercicio
                      AND  transferencia_estornada.cod_entidade = transferencia.cod_entidade
                      AND  transferencia_estornada.cod_lote = transferencia.cod_lote
                      AND  transferencia_estornada.tipo = transferencia.tipo
                      
                LEFT JOIN  tesouraria.recibo_extra_transferencia
                       ON  recibo_extra_transferencia.cod_lote = transferencia.cod_lote
                      AND  recibo_extra_transferencia.cod_entidade = transferencia.cod_entidade
                      AND  recibo_extra_transferencia.exercicio = transferencia.exercicio
                      AND  recibo_extra_transferencia.tipo = transferencia.tipo
           
               INNER JOIN  contabilidade.plano_analitica
                       ON  plano_analitica.cod_plano = transferencia.cod_plano_credito
                      AND  plano_analitica.exercicio = transferencia.exercicio
                      
                LEFT JOIN  contabilidade.plano_recurso
                       ON  plano_recurso.exercicio = plano_analitica.exercicio
                      AND  plano_recurso.cod_plano = plano_analitica.cod_plano
                      
                LEFT JOIN  orcamento.recurso('".$this->getDado('exercicio')."') AS recurso
                       ON  recurso.cod_recurso = plano_recurso.cod_recurso
                      AND  recurso.exercicio = plano_recurso.exercicio
                      
               INNER JOIN  contabilidade.plano_conta
                       ON  plano_conta.cod_conta = plano_analitica.cod_conta
                      AND  plano_conta.exercicio = plano_analitica.exercicio
                      
                LEFT JOIN  contabilidade.plano_banco
                       ON  plano_banco.cod_plano = transferencia.cod_plano_credito
                      AND  plano_banco.exercicio = transferencia.exercicio
                      
                LEFT JOIN  monetario.conta_corrente
                       ON  conta_corrente.cod_banco = plano_banco.cod_banco
                      AND  conta_corrente.cod_agencia = plano_banco.cod_agencia
                      AND  conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                      
                LEFT JOIN  monetario.banco
                       ON  banco.cod_banco = conta_corrente.cod_banco
                       
               INNER JOIN  orcamento.entidade
                       ON  entidade.cod_entidade = transferencia.cod_entidade
                      AND  entidade.exercicio = transferencia.exercicio
                      
               INNER JOIN  sw_cgm as entidade_cgm
                   ON  entidade_cgm.numcgm = entidade.numcgm
                     INNER JOIN (SELECT
                             CPA.cod_plano || ' - ' || CPC.nom_conta as nome_conta_banco  
                           , CPA.cod_plano as cod_plano_banco
                           , CPA.exercicio 
                       FROM
                           contabilidade.plano_conta as CPC,
                           contabilidade.plano_analitica as CPA
                       WHERE
                           CPC.cod_conta = CPA.cod_conta AND
                           CPC.exercicio = CPA.exercicio
                       ) as conta_banco ON (
                               conta_banco.cod_plano_banco = transferencia.cod_plano_debito
                           AND conta_banco.exercicio = transferencia.exercicio
                       )
           ) AS relacao
                 WHERE relacao.cod_tipo = 2
                   AND relacao.tipo = 'T'
                   AND to_date(timestamp_transferencia::VARCHAR,'yyyy-mm-dd') BETWEEN to_date('".$this->getDado('dt_inicial')."'::VARCHAR,'dd/mm/yyyy') and to_date('".$this->getDado('dt_final')."'::VARCHAR,'dd/mm/yyyy')";
                   
            
          if($this->getDado('cod_recurso') != '') {
             $stSql .= " AND relacao.cod_recurso::BIGINT IN (".$this->getDado('cod_recurso').")";
          }
        
        return $stSql;
    }
    
    public function __destruct(){}

}