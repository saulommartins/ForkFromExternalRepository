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
    * Classe de mapeamento da tabela EMPENHO.EMPENHO
    * Data de Criação: 30/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TEmpenhoEmpenho.class.php 65975 2016-07-05 14:47:21Z lisiane $

    * Casos de uso: uc-02.01.23
                    uc-02.03.03
                    uc-02.03.04
                    uc-02.03.14
                    uc-02.03.16
                    uc-02.01.01
                    uc-02.03.31
                    uc-03.04.24
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TEmpenhoEmpenho extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoEmpenho()
{
    parent::Persistente();
    $this->setTabela('empenho.empenho');

    $this->setCampoCod('cod_empenho');
    $this->setComplementoChave('exercicio,cod_entidade');

    $this->AddCampo('cod_empenho','integer',true,'',true,false);
    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('cod_entidade','integer',true,'',true,true);
    $this->AddCampo('cod_pre_empenho','integer',true,'',false,true);
    $this->AddCampo('cod_categoria','integer',true,'',false,true);
    $this->AddCampo('dt_empenho','date',true,'',false,false);
    $this->AddCampo('dt_vencimento','date',true,'',false,false);
    $this->AddCampo('vl_saldo_anterior','numeric',true,'14,2',false,false);
    $this->AddCampo('hora','time',false,'',false,false);
    $this->AddCampo('restos_pagar','character varying(50)',false,'',false,false);

}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaRelacionamento()
{
    $stSql = "  SELECT                                                                        
                    tabela.*                                                                
                FROM (                                                                        
                        SELECT                                                                   
                                AE.cod_autorizacao,                                              
                                EE.cod_empenho,                                                  
                                EE.vl_saldo_anterior,                                            
                                TO_CHAR(EE.dt_vencimento,'dd/mm/yyyy') AS dt_vencimento,         
                                TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,               
                                PD.cod_despesa,                                                  
                                PE.descricao,                                                    
                                EE.exercicio as exercicio_empenho,                               
                                PE.exercicio,                                                    
                                PE.cod_pre_empenho,                                              
                                PE.cgm_beneficiario as credor,                                   
                                EE.cod_entidade,                                                 
                                AR.cod_reserva,                                                  
                                PD.cod_conta,                                                    
                                C.nom_cgm AS nom_fornecedor,                                     
                                R.vl_reserva,                                                    
                                OD.num_orgao,                                                    
                                OD.num_unidade,                                                  
                                OCD.cod_estrutural,                                              
                                OD.cod_recurso,                                                  
                                PE.cod_historico,                                                
                                empenho.fn_consultar_valor_empenhado(                          
                                                                     PE.exercicio               
                                                                    ,EE.cod_empenho             
                                                                    ,EE.cod_entidade            
                                ) AS vl_empenhado,                                               
                                empenho.fn_consultar_valor_empenhado_anulado(                  
                                                                             PE.exercicio       
                                                                            ,EE.cod_empenho     
                                                                            ,EE.cod_entidade    
                                ) AS vl_empenhado_anulado,                                       
                                empenho.fn_consultar_valor_liquidado(                          
                                                       PE.exercicio               
                                                      ,EE.cod_empenho             
                                                      ,EE.cod_entidade            
                                ) AS vl_liquidado,                                               
                                empenho.fn_consultar_valor_liquidado_anulado(                  
                                                                             PE.exercicio       
                                                                            ,EE.cod_empenho     
                                                                            ,EE.cod_entidade    
                                ) AS vl_liquidado_anulado,                                       
                                empenho.fn_consultar_valor_empenhado_pago(                     
                                                                         PE.exercicio       
                                                                        ,EE.cod_empenho     
                                                                        ,EE.cod_entidade    
                                ) AS vl_pago,                                                    
                                empenho.fn_consultar_valor_empenhado_pago_anulado(             
                                                                                PE.exercicio       
                                                                               ,EE.cod_empenho     
                                                                               ,EE.cod_entidade    
                                ) AS vl_pago_anulado                                             
                        FROM                                                                     
                                empenho.empenho             AS EE                            
                        LEFT JOIN empenho.empenho_autorizacao AS EA 
                             ON EA.exercicio       = EE.exercicio                             
                            AND EA.cod_entidade    = EE.cod_entidade                          
                            AND EA.cod_empenho     = EE.cod_empenho 
                        LEFT JOIN empenho.autorizacao_empenho AS AE 
                             ON AE.exercicio       = EA.exercicio                             
                            AND AE.cod_autorizacao = EA.cod_autorizacao                       
                            AND AE.cod_entidade    = EA.cod_entidade  
                        LEFT JOIN empenho.autorizacao_reserva AS AR 
                             ON AR.exercicio       = AE.exercicio                             
                            AND AR.cod_entidade    = AE.cod_entidade                          
                            AND AR.cod_autorizacao = AE.cod_autorizacao 
                        LEFT JOIN orcamento.reserva AS  R 
                             ON R.cod_reserva = AR.cod_reserva                           
                            AND R.exercicio   = AR.exercicio 
                        JOIN empenho.pre_empenho AS PE                           
                             ON EE.cod_pre_empenho = PE.cod_pre_empenho                       
                            AND EE.exercicio       = PE.exercicio                             
                        JOIN sw_cgm AS  C
                            ON C.numcgm = PE.cgm_beneficiario                           
                        JOIN empenho.pre_empenho_despesa AS PD
                             ON PD.cod_pre_empenho = PE.cod_pre_empenho                       
                            AND PD.exercicio       = PE.exercicio                                                       
                        JOIN orcamento.despesa AS OD  
                             ON OD.exercicio       = PD.exercicio                             
                            AND OD.cod_despesa     = PD.cod_despesa                                                     
                        LEFT OUTER JOIN orcamento.conta_despesa AS OCD 
                             ON OD.cod_conta = OCD.cod_conta 
                            AND OD.exercicio = OCD.exercicio                              
                        
                        WHERE                                                                    
                            CAST(OD.num_unidade as varchar)||CAST(OD.num_orgao as varchar) IN (                              
                                                                                                SELECT                                            
                                                                                                      CAST(num_unidade as varchar)||CAST(num_orgao as varchar)                       
                                                                                                FROM                                              
                                                                                                    empenho.permissao_autorizacao             
                                                                                                WHERE numcgm    = ".$this->getDado("numcgm")."    
                                                                                                AND   exercicio = '".$this->getDado("exercicio")."'
                            )                            
    ) AS tabela 
    ";                                                                  

    return $stSql;
}

function MontaRecuperaEmpenhoCompraLicitacaoAnulado()
{
    $stSql = " SELECT DISTINCT tabela.*                                                                
                FROM (                                                                        
                     SELECT  AE.cod_autorizacao,                                              
                             EE.cod_empenho,                                                  
                             EE.vl_saldo_anterior,                                            
                             TO_CHAR(EE.dt_vencimento,'dd/mm/yyyy') AS dt_vencimento,         
                             TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,               
                             PD.cod_despesa,                                                  
                             PE.descricao,                                                    
                             EE.exercicio as exercicio_empenho,                               
                             PE.exercicio,                                                    
                             PE.cod_pre_empenho,                                              
                             PE.cgm_beneficiario as credor,                                   
                             EE.cod_entidade,                                                 
                             AR.cod_reserva,                                                  
                             PD.cod_conta,                                                    
                             C.nom_cgm AS nom_fornecedor,                                     
                             R.vl_reserva,                                                    
                             OD.num_orgao,                                                    
                             OD.num_unidade,                                                  
                             OCD.cod_estrutural,                                              
                             OD.cod_recurso,                                                  
                             PE.cod_historico,                                                
                             empenho.fn_consultar_valor_empenhado(  PE.exercicio               
                                                                   ,EE.cod_empenho             
                                                                   ,EE.cod_entidade            
                             ) AS vl_empenhado,                                               
                             empenho.fn_consultar_valor_empenhado_anulado(  PE.exercicio       
                                                                            ,EE.cod_empenho     
                                                                            ,EE.cod_entidade    
                             ) AS vl_empenhado_anulado,                                       
                             empenho.fn_consultar_valor_liquidado(  PE.exercicio               
                                                                    ,EE.cod_empenho             
                                                                    ,EE.cod_entidade            
                                ) AS vl_liquidado,                                               
                             empenho.fn_consultar_valor_liquidado_anulado(  PE.exercicio       
                                                                            ,EE.cod_empenho     
                                                                            ,EE.cod_entidade    
                                ) AS vl_liquidado_anulado,                                       
                             empenho.fn_consultar_valor_empenhado_pago( PE.exercicio       
                                                                        ,EE.cod_empenho     
                                                                        ,EE.cod_entidade    
                                ) AS vl_pago,                                                    
                             empenho.fn_consultar_valor_empenhado_pago_anulado( PE.exercicio       
                                                                                ,EE.cod_empenho     
                                                                                ,EE.cod_entidade    
                             ) AS vl_pago_anulado,
                             compra_direta.cod_modalidade AS compra_cod_modalidade,
                             compra_direta.cod_compra_direta,
                             adjudicacao.cod_modalidade AS licitacao_cod_modalidade,
                             adjudicacao.cod_licitacao
                
                                                                          
                        FROM empenho.empenho AS EE                            
                
                    LEFT JOIN empenho.empenho_autorizacao AS EA
                           ON EA.exercicio       = EE.exercicio                             
                          AND EA.cod_entidade    = EE.cod_entidade                          
                          AND EA.cod_empenho     = EE.cod_empenho
                          
                    LEFT JOIN empenho.autorizacao_empenho AS AE
                           ON  AE.exercicio       = EA.exercicio                             
                          AND  AE.cod_autorizacao = EA.cod_autorizacao                       
                          AND  AE.cod_entidade    = EA.cod_entidade                        
                    
                    LEFT JOIN empenho.autorizacao_reserva AS AR
                           ON AR.exercicio       = AE.exercicio                             
                          AND AR.cod_entidade    = AE.cod_entidade                          
                          AND AR.cod_autorizacao = AE.cod_autorizacao
                    
                    LEFT JOIN orcamento.reserva AS  R
                           ON R.cod_reserva     = AR.cod_reserva                           
                          AND R.exercicio       = AR.exercicio
                           
                    INNER JOIN empenho.pre_empenho  AS PE
                            ON EE.cod_pre_empenho = PE.cod_pre_empenho                       
                           AND EE.exercicio       = PE.exercicio                                
                      
                    INNER JOIN sw_cgm AS  C
                            ON C.numcgm  = PE.cgm_beneficiario
                    
                    INNER JOIN empenho.pre_empenho_despesa AS PD    
                            ON PD.cod_pre_empenho = PE.cod_pre_empenho                       
                           AND PD.exercicio       = PE.exercicio                             
                     
                    INNER JOIN orcamento.despesa AS OD
                            ON OD.exercicio       = PD.exercicio                             
                           AND OD.cod_despesa     = PD.cod_despesa
                    
                    LEFT OUTER JOIN orcamento.conta_despesa AS OCD
                                 ON OD.cod_conta = OCD.cod_conta
                                AND OD.exercicio = OCD.exercicio
                    
                    LEFT JOIN empenho.item_pre_empenho
                           ON item_pre_empenho.cod_pre_empenho = pe.cod_pre_empenho
                          AND item_pre_empenho.exercicio       = pe.exercicio
                          
                    LEFT JOIN empenho.item_pre_empenho_julgamento
                           ON item_pre_empenho_julgamento.cod_pre_empenho  = item_pre_empenho.cod_pre_empenho   
                          AND item_pre_empenho_julgamento.exercicio        = item_pre_empenho.exercicio
                          AND item_pre_empenho_julgamento.num_item         = item_pre_empenho.num_item
                    
                    LEFT JOIN compras.julgamento_item
                           ON julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio_julgamento
                          AND julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao 
                          AND julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item
                          AND julgamento_item.lote           = item_pre_empenho_julgamento.lote
                          AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
                    
                    LEFT JOIN compras.cotacao_item
                           ON cotacao_item.exercicio   = julgamento_item.exercicio
                          AND cotacao_item.cod_cotacao = julgamento_item.cod_cotacao
                          AND cotacao_item.lote        = julgamento_item.lote
                          AND cotacao_item.cod_item    = julgamento_item.cod_item
                    
                    LEFT JOIN compras.cotacao
                           ON cotacao.cod_cotacao = cotacao_item.cod_cotacao
                          AND cotacao.exercicio   = cotacao_item.exercicio
                    
                    LEFT JOIN compras.mapa_cotacao
                           ON mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                          AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                    
                    LEFT JOIN compras.mapa
                           ON mapa.cod_mapa  = mapa_cotacao.cod_mapa
                          AND mapa.exercicio = mapa_cotacao.exercicio_mapa
                    
                    LEFT JOIN compras.compra_direta
                           ON compra_direta.cod_mapa       = mapa.cod_mapa
                          AND compra_direta.exercicio_mapa = mapa.exercicio
                    
                    LEFT JOIN licitacao.adjudicacao
                           ON adjudicacao.exercicio_cotacao = cotacao_item.exercicio 
                          AND adjudicacao.cod_cotacao       = cotacao_item.cod_cotacao
                          AND adjudicacao.lote              = cotacao_item.lote
                          AND adjudicacao.cod_item          = cotacao_item.cod_item 
                       
                       WHERE CAST(OD.num_unidade as varchar)||CAST(OD.num_orgao as varchar)
                          IN ( SELECT CAST(num_unidade as varchar)||CAST(num_orgao as varchar)                       
                                FROM empenho.permissao_autorizacao             
                               WHERE numcgm      = ".$this->getDado("numcgm")."    
                                 AND   exercicio = '".$this->getDado("exercicio")."'
                             )                            
            ) AS tabela                                                                       
    ";
    return $stSql;
}

public function recuperaEmpenhoCompraLicitacaoAnulado(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY") === false)?" ORDER BY $stOrdem":$stOrdem;
        
    $stSql = $this->MontaRecuperaEmpenhoCompraLicitacaoAnulado().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTimestampAnulado()
{
    $stSql  = "SELECT                                                   \n";
    $stSql .= "    timestamp as timestampAnulado                        \n";
    $stSql .= "FROM                                                     \n";
    $stSql .= "    empenho.empenho_anulado                              \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaAdiantamentoSubvencao(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaAdiantamentoSubvencao().$stCondicao;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAdiantamentoSubvencao()
{
    $stSql ="SELECT empenho.exercicio                                  \n";
    $stSql.="      ,empenho.cod_entidade                               \n";
    $stSql.="      ,empenho.cod_empenho                                \n";
    $stSql.="      ,empenho.cod_categoria                              \n";
    $stSql.="      ,empenho.cod_pre_empenho                            \n";
    $stSql.="      ,empenho.dt_empenho                                 \n";
    $stSql.="      ,empenho.dt_vencimento                              \n";
    $stSql.="      ,empenho.hora                                       \n";
    $stSql.="      ,empenho.vl_saldo_anterior                          \n";
    $stSql.="      ,sw_cgm.nom_cgm                                     \n";
    $stSql.="  FROM empenho.empenho                                    \n";
    $stSql.="      ,sw_cgm                                             \n";
    $stSql.="      ,orcamento.entidade                                 \n";
    $stSql.=" WHERE empenho.cod_entidade = entidade.cod_entidade       \n";
    $stSql.="   AND empenho.exercicio    = entidade.exercicio          \n";
    $stSql.="   AND entidade.numcgm      = sw_cgm.numcgm               \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaTimestampAnulado(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaTimestampAnulado().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMaiorDataEmpenho()
{
    $stSql  ="SELECT                                                                    \n";
    $stSql .="    CASE WHEN (max(dt_empenho) < to_date('01/01/".Sessao::getExercicio()."','dd/mm/yyyy')) OR (max(dt_empenho) is NULL) THEN   \n";
    $stSql .="        '01/01/".Sessao::getExercicio()."'                                                     \n";
    $stSql .="    ELSE                                                                  \n";
    $stSql .="        to_char(max(dt_empenho),'dd/mm/yyyy')                             \n";
    $stSql .="    END AS dataEmpenho                                                    \n";
    $stSql .="FROM                                                                      \n";
    $stSql .="    empenho.empenho as e                                                  \n";
    $stSql .="    LEFT JOIN ( SELECT coalesce(sum(vl_total),0.00) - coalesce(sum(vl_anulado),0.00) as valor      \n";
    $stSql .="                 ,ea.cod_empenho                                                              \n";
    $stSql .="                 ,ea.cod_entidade                                                             \n";
    $stSql .="                 ,ea.exercicio                                                                \n";
    $stSql .="            FROM empenho.empenho_anulado as ea                                                \n";
    $stSql .="                 JOIN ( SELECT sum(vl_anulado) as vl_anulado                                  \n";
    $stSql .="                              ,ipe.vl_total                                                   \n";
    $stSql .="                              ,eai.cod_empenho                                                \n";
    $stSql .="                              ,eai.cod_entidade                                               \n";
    $stSql .="                              ,eai.exercicio                                                  \n";
    $stSql .="                          FROM empenho.empenho_anulado_item eai                               \n";
    $stSql .="                               JOIN empenho.item_pre_empenho as ipe                           \n";
    $stSql .="                               ON (   ipe.exercicio       = eai.exercicio                     \n";
    $stSql .="                                  AND ipe.cod_pre_empenho = eai.cod_pre_empenho               \n";
    $stSql .="                                  AND ipe.num_item        = eai.num_item                      \n";
    $stSql .="                               )                                                              \n";
    $stSql .="                      GROUP BY ipe.vl_total, eai.cod_empenho, eai.cod_entidade, eai.exercicio \n";
    $stSql .="                  ) as itens ON ( itens.cod_empenho  = ea.cod_empenho                         \n";
    $stSql .="                              AND itens.exercicio    = ea.exercicio                           \n";
    $stSql .="                              AND itens.cod_entidade = ea.cod_entidade                        \n";
    $stSql .="                  )                                                                           \n";
    $stSql .="          WHERE ea.exercicio = '".Sessao::getExercicio()."'                                       \n";
    $stSql .="       GROUP BY ea.cod_empenho, ea.cod_entidade, ea.exercicio                                 \n";
    $stSql .="       ) as it ON ( it.cod_empenho = e.cod_empenho                                            \n";
    $stSql .="                AND it.exercicio   = e.exercicio                                              \n";
    $stSql .="                AND it.cod_entidade = e.cod_entidade                                          \n";
    $stSql .="       )                                                                                      \n";
    $stSql .="  WHERE e.cod_empenho is not null AND (it.valor != 0.00 or it.valor is null)      \n";

    return $stSql;

}

function montaRecuperaMaiorDataAnulada()
{
    $stSql  =" SELECT                                                                                                                                               \n";
    $stSql .="    CASE WHEN to_date('".$this->getDado("stDataEmpenho")."'::varchar,'dd/mm/yyyy') < to_date('01/01/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy') THEN      \n";
    $stSql .="        CASE WHEN to_date(max(timestamp)::varchar,'yyyy-mm-dd' ) < to_date('01/01/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy') THEN                        \n";
    $stSql .="            '01/01/".$this->getDado("stExercicio")."'                                                                                                 \n";
    $stSql .="        ELSE                                                                                                                                          \n";
    $stSql .="            to_char(to_date(max(timestamp)::varchar,'yyyy-mm-dd' ),'dd/mm/yyyy')                                                                               \n";
    $stSql .="        END                                                                                                                                           \n";
    $stSql .="    ELSE                                                                                                                                              \n";
    $stSql .="        CASE WHEN to_date(max(timestamp)::varchar,'yyyy-mm-dd' ) < to_date('01/01/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy') THEN                        \n";
    $stSql .="            '".$this->getDado("stDataEmpenho")."'                                                                                                     \n";
    $stSql .="        ELSE                                                                                                                                          \n";
    $stSql .="            CASE WHEN to_date(max(timestamp)::varchar,'yyyy-mm-dd' ) < to_date('".$this->getDado("stDataEmpenho")."'::varchar,'dd/mm/yyyy') THEN                        \n";
    $stSql .="                '".$this->getDado("stDataEmpenho")."'                                                                                                 \n";
    $stSql .="            ELSE                                                                                                                                      \n";
    $stSql .="                to_char(to_date(max(timestamp)::varchar,'yyyy-mm-dd' ),'dd/mm/yyyy')                                                                           \n";
    $stSql .="            END                                                                                                                                       \n";
    $stSql .="        END                                                                                                                                           \n";
    $stSql .="    END AS dataAnulacao                                                                                                                               \n";
    $stSql .="FROM                                                                                                                                                  \n";
    $stSql .="    empenho.empenho_anulado                                                                                                                           \n";

    return $stSql;
}

function montaRecuperaMaiorDataEmpenhoAutorizacao()
{
    $stSql  ="SELECT                                                                                        \n";
    $stSql .="    CASE WHEN to_date('".$this->getDado("stDataAutorizacao")."','dd/mm/yyyy') < to_date('01/01/".$this->getDado("stExercicio")."','dd/mm/yyyy') THEN    \n";
    $stSql .="        CASE WHEN max(dt_empenho) < to_date('01/01/".$this->getDado("stExercicio")."','dd/mm/yyyy') THEN                   \n";
    $stSql .="            '01/01/".$this->getDado("stExercicio")."'                                         \n";
    $stSql .="        ELSE                                                                                  \n";
    $stSql .="            to_char(max(dt_empenho),'dd/mm/yyyy')                                             \n";
    $stSql .="        END                                                                                   \n";
    $stSql .="    ELSE                                                                                      \n";
    $stSql .="        CASE WHEN max(dt_empenho) < to_date('01/01/".$this->getDado("stExercicio")."','dd/mm/yyyy') THEN                   \n";
    $stSql .="            '".$this->getDado("stDataAutorizacao")."'                                         \n";
    $stSql .="        ELSE                                                                                  \n";
    $stSql .="            CASE WHEN max(dt_empenho) < to_date('".$this->getDado("stDataAutorizacao")."','dd/mm/yyyy') THEN   \n";
    $stSql .="                '".$this->getDado("stDataAutorizacao")."'                                     \n";
    $stSql .="            ELSE                                                                              \n";
    $stSql .="                to_char(max(dt_empenho),'dd/mm/yyyy')                                         \n";
    $stSql .="            END                                                                               \n";
    $stSql .="        END                                                                                   \n";
    $stSql .="    END AS dataEmpenho                                                                        \n";
    $stSql .="FROM                                                                                          \n";
    $stSql .="    empenho.empenho as e                                                                      \n";
    $stSql .="    LEFT JOIN ( SELECT coalesce(sum(vl_total),0.00) - coalesce(sum(vl_anulado),0.00) as valor      \n";
    $stSql .="                 ,ea.cod_empenho                                                              \n";
    $stSql .="                 ,ea.cod_entidade                                                             \n";
    $stSql .="                 ,ea.exercicio                                                                \n";
    $stSql .="            FROM empenho.empenho_anulado as ea                                                \n";
    $stSql .="                 JOIN ( SELECT sum(vl_anulado) as vl_anulado                                  \n";
    $stSql .="                              ,ipe.vl_total                                                   \n";
    $stSql .="                              ,eai.cod_empenho                                                \n";
    $stSql .="                              ,eai.cod_entidade                                               \n";
    $stSql .="                              ,eai.exercicio                                                  \n";
    $stSql .="                          FROM empenho.empenho_anulado_item eai                               \n";
    $stSql .="                               JOIN empenho.item_pre_empenho as ipe                           \n";
    $stSql .="                               ON (   ipe.exercicio       = eai.exercicio                     \n";
    $stSql .="                                  AND ipe.cod_pre_empenho = eai.cod_pre_empenho               \n";
    $stSql .="                                  AND ipe.num_item        = eai.num_item                      \n";
    $stSql .="                               )                                                              \n";
    $stSql .="                      GROUP BY ipe.vl_total, eai.cod_empenho, eai.cod_entidade, eai.exercicio \n";
    $stSql .="                  ) as itens ON ( itens.cod_empenho  = ea.cod_empenho                         \n";
    $stSql .="                              AND itens.exercicio    = ea.exercicio                           \n";
    $stSql .="                              AND itens.cod_entidade = ea.cod_entidade                        \n";
    $stSql .="                  )                                                                           \n";
    $stSql .="          WHERE ea.exercicio = '".Sessao::getExercicio()."'                                       \n";
    $stSql .="       GROUP BY ea.cod_empenho, ea.cod_entidade, ea.exercicio                                 \n";
    $stSql .="       ) as it ON ( it.cod_empenho = e.cod_empenho                                            \n";
    $stSql .="                AND it.exercicio   = e.exercicio                                              \n";
    $stSql .="                AND it.cod_entidade = e.cod_entidade                                          \n";
    $stSql .="       )                                                                                      \n";
    $stSql .=" WHERE e.cod_empenho is not null AND (it.valor != 0.00 or it.valor is null)             \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaMaiorDataEmpenho(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "" , $stDataAutorizacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    if($stDataAutorizacao)
        $stSql = $this->montaRecuperaMaiorDataEmpenhoAutorizacao().$stCondicao.$stOrdem;
    else
        $stSql = $this->montaRecuperaMaiorDataEmpenho().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaUltimaDataEmpenho(&$rsRecordSet,$stFiltro = "",$stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaUltimaDataEmpenho().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaUltimaDataEmpenho()
{
    $stSql  =" SELECT empenho.dt_empenho                 \n";
    $stSql .="   FROM empenho.empenho                    \n";
    $stSql .="        LEFT JOIN ( SELECT coalesce(sum(vl_total),0.00) - coalesce(sum(vl_anulado),0.00) as valor      \n";
    $stSql .="                     ,ea.cod_empenho                                                              \n";
    $stSql .="                     ,ea.cod_entidade                                                             \n";
    $stSql .="                     ,ea.exercicio                                                                \n";
    $stSql .="                FROM empenho.empenho_anulado as ea                                                \n";
    $stSql .="                     JOIN ( SELECT sum(vl_anulado) as vl_anulado                                  \n";
    $stSql .="                                  ,ipe.vl_total                                                   \n";
    $stSql .="                                  ,eai.cod_empenho                                                \n";
    $stSql .="                                  ,eai.cod_entidade                                               \n";
    $stSql .="                                  ,eai.exercicio                                                  \n";
    $stSql .="                              FROM empenho.empenho_anulado_item eai                               \n";
    $stSql .="                                   JOIN empenho.item_pre_empenho as ipe                           \n";
    $stSql .="                                   ON (   ipe.exercicio       = eai.exercicio                     \n";
    $stSql .="                                      AND ipe.cod_pre_empenho = eai.cod_pre_empenho               \n";
    $stSql .="                                      AND ipe.num_item        = eai.num_item                      \n";
    $stSql .="                                   )                                                              \n";
    $stSql .="                          GROUP BY ipe.vl_total, eai.cod_empenho, eai.cod_entidade, eai.exercicio \n";
    $stSql .="                      ) as itens ON ( itens.cod_empenho  = ea.cod_empenho                         \n";
    $stSql .="                                  AND itens.exercicio    = ea.exercicio                           \n";
    $stSql .="                                  AND itens.cod_entidade = ea.cod_entidade                        \n";
    $stSql .="                      )                                                                           \n";
    $stSql .="              WHERE ea.exercicio = '".Sessao::getExercicio()."'                                       \n";
    $stSql .="           GROUP BY ea.cod_empenho, ea.cod_entidade, ea.exercicio                                 \n";
    $stSql .="           ) as it ON ( it.cod_empenho = empenho.cod_empenho                                            \n";
    $stSql .="                    AND it.exercicio   = empenho.exercicio                                              \n";
    $stSql .="                    AND it.cod_entidade = empenho.cod_entidade                                          \n";
    $stSql .="           )                                                                                      \n";
    $stSql .="  WHERE empenho.cod_empenho is not null AND (it.valor != 0.00 or it.valor is null)      \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaMaiorDataAnulada(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaMaiorDataAnulada().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoAnulados()
{
    $stSql  = "SELECT                                                   \n";
    $stSql .= "    to_char(eai.timestamp,'dd/mm/yyyy') as dt_anulado,   \n";
    $stSql .= "    eai.vl_anulado as vl_anulado                         \n";
    $stSql .= "FROM                                                     \n";
    $stSql .= "    empenho.empenho              as e,                   \n";
    $stSql .= "    empenho.empenho_anulado      as ea,                  \n";
    $stSql .= "    empenho.empenho_anulado_item as eai                  \n";
    $stSql .= "WHERE                                                    \n";
    $stSql .= "        e.exercicio         = ea.exercicio               \n";
    $stSql .= "    AND e.cod_entidade      = ea.cod_entidade            \n";
    $stSql .= "    AND e.cod_empenho       = ea.cod_empenho             \n";

    $stSql .= "    AND ea.exercicio        = eai.exercicio              \n";
    $stSql .= "    AND ea.timestamp        = eai.timestamp              \n";
    $stSql .= "    AND ea.cod_entidade     = eai.cod_entidade           \n";
    $stSql .= "    AND ea.cod_empenho      = eai.cod_empenho            \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoAnulados(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoAnulados().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoReemitirAnulados()
{
    $stSql  = "SELECT                                                  
        e.cod_empenho,                                      
        e.exercicio,                                        
        e.cod_entidade,                                     
        e.cod_pre_empenho,                                  
        c.nom_cgm AS nom_fornecedor,                        
        pe.implantado ,                                     
        to_char(eai.timestamp,'dd/mm/yyyy') as dt_anulado,  
        eai.timestamp,                                      
        sum(eai.vl_anulado) as vl_anulado                   
    FROM                                                    
        empenho.empenho              as e
        JOIN empenho.empenho_anulado as ea
             ON e.exercicio         = ea.exercicio               
            AND e.cod_entidade      = ea.cod_entidade            
            AND e.cod_empenho       = ea.cod_empenho              
        JOIN empenho.empenho_anulado_item as eai
             ON ea.exercicio        = eai.exercicio              
            AND ea.timestamp        = eai.timestamp              
            AND ea.cod_entidade     = eai.cod_entidade           
            AND ea.cod_empenho      = eai.cod_empenho              
        JOIN empenho.pre_empenho as pe
             ON e.exercicio         = pe.exercicio               
            AND e.cod_pre_empenho   = pe.cod_pre_empenho         
        JOIN sw_cgm as  c
            ON pe.cgm_beneficiario = c.numcgm           
        
        LEFT OUTER JOIN empenho.autorizacao_empenho  as ae
                     ON pe.exercicio        = ae.exercicio              
                    AND pe.cod_pre_empenho  = ae.cod_pre_empenho
                    
              LEFT JOIN empenho.pre_empenho_despesa  as ped       
                     ON( pe.exercicio        = ped.exercicio             
                    AND  pe.cod_pre_empenho  = ped.cod_pre_empenho )
                    
              LEFT JOIN empenho.item_pre_empenho
                     ON item_pre_empenho.exercicio        = eai.exercicio
                    AND item_pre_empenho.cod_pre_empenho  = eai.cod_pre_empenho
                    AND item_pre_empenho.num_item        = eai.num_item
                    
              LEFT JOIN empenho.item_pre_empenho_julgamento
                     ON item_pre_empenho_julgamento.cod_pre_empenho  = item_pre_empenho.cod_pre_empenho   
                    AND item_pre_empenho_julgamento.exercicio        = item_pre_empenho.exercicio
                    AND item_pre_empenho_julgamento.num_item         = item_pre_empenho.num_item
              
              LEFT JOIN compras.julgamento_item
                     ON julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio_julgamento
                    AND julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao 
                    AND julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item
                    AND julgamento_item.lote           = item_pre_empenho_julgamento.lote
                    AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
              
              LEFT JOIN compras.cotacao_item
                     ON cotacao_item.exercicio   = julgamento_item.exercicio
                    AND cotacao_item.cod_cotacao = julgamento_item.cod_cotacao
                    AND cotacao_item.lote        = julgamento_item.lote
                    AND cotacao_item.cod_item    = julgamento_item.cod_item
                    
              LEFT JOIN compras.cotacao
                     ON cotacao.cod_cotacao = cotacao_item.cod_cotacao
                    AND cotacao.exercicio   = cotacao_item.exercicio
                    
              LEFT JOIN compras.mapa_cotacao
                     ON mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                    AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                    
              LEFT JOIN compras.mapa
                     ON mapa.cod_mapa  = mapa_cotacao.cod_mapa
                    AND mapa.exercicio = mapa_cotacao.exercicio_mapa
                    
              LEFT JOIN compras.compra_direta
                     ON compra_direta.cod_mapa       = mapa.cod_mapa
                    AND compra_direta.exercicio_mapa = mapa.exercicio
                    
              LEFT JOIN licitacao.adjudicacao
                     ON adjudicacao.exercicio_cotacao = cotacao_item.exercicio 
                    AND adjudicacao.cod_cotacao       = cotacao_item.cod_cotacao
                    AND adjudicacao.lote              = cotacao_item.lote
                    AND adjudicacao.cod_item          = cotacao_item.cod_item
                    
    WHERE 1=1                                                    
        
         ";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoReemitirAnulados(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stGrupo  = " GROUP BY                  \n";
    $stGrupo .= "   eai.timestamp,          \n";
    $stGrupo .= "   e.cod_empenho,          \n";
    $stGrupo .= "   e.exercicio,            \n";
    $stGrupo .= "   e.cod_pre_empenho,      \n";
    $stGrupo .= "   c.nom_cgm,              \n";
    $stGrupo .= "   e.cod_entidade,         \n";
    $stGrupo .= "   pe.implantado           \n";
    $stSql = $this->montaRecuperaRelacionamentoReemitirAnulados().$stCondicao.$stGrupo.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoReemitirLiquidacao()
{
    $stSql  = "SELECT                                                                        \n";
    $stSql .= "     nl.*,                                                                    \n";
    $stSql .= "     to_char(na.timestamp,'dd/mm/yyyy') as dt_anulacao,                       \n";
    $stSql .= "     c.nom_cgm  as nom_fornecedor,                                            \n";
    $stSql .= "     sum(na.vl_anulado)  as valor,                                            \n";
    $stSql .= "     na.timestamp                                                             \n";
    $stSql .= "FROM                                                                          \n";
    $stSql .= "     empenho.nota_liquidacao               as nl                          \n";
    if ((strtolower(SistemaLegado::pegaConfiguracao( 'seta_tipo_documento_liq_tceam',30, Sessao::getExercicio()))=='true')) {
        $stSql .= " LEFT JOIN tceam.documento                                                \n";
        $stSql .= "        ON nl.exercicio    = documento.exercicio                          \n";
        $stSql .= "       AND nl.cod_entidade = documento.cod_entidade                       \n";
        $stSql .= "       AND nl.cod_nota     = documento.cod_nota                           \n";
    }
    $stSql .= "     ,empenho.nota_liquidacao_item         as ni                          \n";
    $stSql .= "     ,empenho.nota_liquidacao_item_anulado as na                          \n";
    $stSql .= "     ,empenho.empenho as e                                                \n";
    $stSql .= "     ,empenho.pre_empenho as pe                                           \n";
    $stSql .= "     ,empenho.pre_empenho_despesa as ped                                  \n";
    $stSql .= "     ,sw_cgm as c                                                         \n";

    $stSql .= "WHERE                                                                         \n";
    $stSql .= "         nl.exercicio       = ni.exercicio                                    \n";
    $stSql .= "     AND nl.cod_nota        = ni.cod_nota                                     \n";
    $stSql .= "     AND nl.cod_entidade    = ni.cod_entidade                                 \n";
    $stSql .= "     AND ni.exercicio       = na.exercicio                                    \n";
    $stSql .= "     AND ni.cod_nota        = na.cod_nota                                     \n";
    $stSql .= "     AND ni.num_item        = na.num_item                                     \n";
    $stSql .= "     AND ni.exercicio_item  = na.exercicio_item                               \n";
    $stSql .= "     AND ni.cod_pre_empenho = na.cod_pre_empenho                              \n";
    $stSql .= "     AND ni.cod_entidade    = na.cod_entidade                                 \n";
    $stSql .= "     AND nl.cod_empenho     = e.cod_empenho                                   \n";
    $stSql .= "     AND nl.exercicio       = e.exercicio                                     \n";
    $stSql .= "     AND nl.cod_entidade    = e.cod_entidade                                  \n";
    $stSql .= "     AND e.exercicio        = pe.exercicio                                    \n";
    $stSql .= "     AND e.cod_pre_empenho  = pe.cod_pre_empenho                              \n";
    $stSql .= "     AND pe.cgm_beneficiario= c.numcgm                                        \n";
    $stSql .= "     AND pe.exercicio       = ped.exercicio                                   \n";
    $stSql .= "     AND pe.cod_pre_empenho = ped.cod_pre_empenho                             \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoReemitirLiquidacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stGrupo  = " GROUP BY                  \n";
    $stGrupo .= "   nl.exercicio,           \n";
    $stGrupo .= "   nl.cod_nota,            \n";
    $stGrupo .= "   nl.cod_entidade,        \n";
    $stGrupo .= "   nl.exercicio_empenho,   \n";
    $stGrupo .= "   nl.cod_empenho,         \n";
    $stGrupo .= "   nl.dt_vencimento,       \n";
    $stGrupo .= "   nl.dt_liquidacao,       \n";
    $stGrupo .= "   nl.observacao,          \n";
    $stGrupo .= "   nl.hora,                \n";
    $stGrupo .= "   na.timestamp,           \n";
    $stGrupo .= "   c.nom_cgm,              \n";
    $stGrupo .= "   to_char(na.timestamp,'dd/mm/yyyy')           \n";
    $stSql = $this->montaRecuperaRelacionamentoReemitirLiquidacao().$stCondicao.$stGrupo.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function executaChecaImplantado(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = empty($stOrdem) ? "" : $stOrdem;
    $stSql = $this->montaChecaImplantado().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaChecaImplantado()
{
    $stSQL  = "SELECT PE.implantado                          \n";
    $stSQL .= "FROM empenho.empenho     AS EE            \n";
    $stSQL .= "    ,empenho.pre_empenho AS PE            \n";
    $stSQL .= "WHERE EE.exercicio       = PE.exercicio       \n";
    $stSQL .= "AND   EE.cod_pre_empenho = PE.cod_pre_empenho \n";

    return $stSQL;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function executaRetornaCategoria(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = empty($stOrdem) ? "" : $stOrdem;

    $stSql = $this->montaRetornaCategoria().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRetornaCategoria()
{
    $stSQL  = "SELECT EE.cod_categoria                       \n";
    $stSQL .= "FROM empenho.empenho     AS EE                \n";
    $stSQL .= " WHERE 1 = 1                                  \n";

    return $stSQL;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaValorNotaItem(&$rsRecordSet, $stCondicao = "",$stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaValorNotaItem().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaConsultaAdiantamentoSubvencao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

    $stSql = $this->montaRecuperaConsultaAdiantamentoSubvencao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaConsultaAdiantamentoSubvencao()
{
    $stSql  = "SELECT                                                                        \n";
    $stSql .= "       tabela.cod_entidade                                                    \n";
    $stSql .= "      ,tabela.cod_empenho                                                     \n";
    $stSql .= "      ,tabela.cod_pre_empenho                                                 \n";
    $stSql .= "      ,tabela.cod_autorizacao                                                 \n";
    $stSql .= "      ,tabela.cod_reserva                                                     \n";
    $stSql .= "      ,tabela.exercicio                                                       \n";
    $stSql .= "      ,tabela.dt_empenho                                                      \n";
    $stSql .= "      ,tabela.nom_fornecedor                                                  \n";
    $stSql .= "      ,tabela.vl_empenhado                                                    \n";
    $stSql .= "      ,tabela.vl_pago - tabela.vl_pago_anulado as vl_pago                     \n";
    $stSql .= "      ,tabela.vl_prestado                                                     \n";
    $stSql .= "      ,tabela.conta_contrapartida                                             \n";
    $stSql .= "      ,tabela.mascara_classificacao                                           \n";
    $stSql .= "FROM (                                                                        \n";
    $stSql .= "     SELECT                                                                   \n";
    $stSql .= "              EE.cod_entidade                                                 \n";
    $stSql .= "             ,EE.cod_empenho                                                  \n";
    $stSql .= "             ,AE.cod_autorizacao                                              \n";
    $stSql .= "             ,EE.cod_categoria                                                \n";
    $stSql .= "             ,EE.vl_saldo_anterior                                            \n";
    $stSql .= "             ,TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho               \n";
    $stSql .= "             ,PD.cod_despesa                                                  \n";
    $stSql .= "             ,PE.descricao                                                    \n";
    $stSql .= "             ,PE.exercicio                                                    \n";
    $stSql .= "             ,PE.cod_pre_empenho                                              \n";
    $stSql .= "             ,PE.cgm_beneficiario as credor                                   \n";
    $stSql .= "             ,ECE.conta_contrapartida                                         \n";
    $stSql .= "             ,AR.cod_reserva                                                  \n";
    $stSql .= "             ,PD.cod_conta                                                    \n";
    $stSql .= "             ,C.nom_cgm AS nom_fornecedor                                     \n";
    $stSql .= "             ,R.vl_reserva                                                    \n";
    $stSql .= "             ,OD.num_orgao                                                    \n";
    $stSql .= "             ,OD.num_unidade                                                  \n";
    $stSql .= "             ,OCD.cod_estrutural                                              \n";
    $stSql .= "             ,OD.cod_recurso                                                  \n";
    $stSql .= "             ,PE.cod_historico                                                \n";
    $stSql .= "             ,ovwd.mascara_classificacao                                      \n";
    $stSql .= "             ,COALESCE(empenho.fn_consultar_valor_empenhado(                  \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "              ), 0.00) AS vl_empenhado                                        \n";
    $stSql .= "             ,COALESCE(empenho.fn_consultar_valor_empenhado_anulado(          \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "              ), 0.00) AS vl_empenhado_anulado                                \n";
    $stSql .= "             ,COALESCE(empenho.fn_consultar_valor_liquidado(                  \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "              ), 0.00) AS vl_liquidado                                        \n";
    $stSql .= "             ,COALESCE(empenho.fn_consultar_valor_liquidado_anulado(          \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "              ), 0.00) AS vl_liquidado_anulado                                \n";
    $stSql .= "             ,COALESCE(empenho.fn_consultar_valor_empenhado_pago(             \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "              ), 0.00) AS vl_pago                                             \n";
    $stSql .= "             ,COALESCE(empenho.fn_consultar_valor_empenhado_pago_anulado(     \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "              ), 0.00) AS vl_pago_anulado                                     \n";
    $stSql .= "             ,COALESCE(empenho.fn_consultar_valor_prestado_nao_anulado(       \n";
    $stSql .= "                                                           EE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ), 0.00) AS vl_prestado                                          \n";
    $stSql .= "     FROM                                                                     \n";
    $stSql .= "             empenho.empenho             AS EE                                \n";
    $stSql .= "             LEFT JOIN empenho.contrapartida_empenho as ECE                   \n";
    $stSql .= "             ON (                                                             \n";
    $stSql .= "                     EE.exercicio    = ECE.exercicio                          \n";
    $stSql .= "                 AND EE.cod_entidade = ECE.cod_entidade                       \n";
    $stSql .= "                 AND EE.cod_empenho  = ECE.cod_empenho                        \n";
    $stSql .= "             )                                                                \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.empenho_autorizacao AS EA ON (                           \n";
    $stSql .= "                EA.exercicio       = EE.exercicio                             \n";
    $stSql .= "          AND   EA.cod_entidade    = EE.cod_entidade                          \n";
    $stSql .= "          AND   EA.cod_empenho     = EE.cod_empenho   )                       \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.autorizacao_empenho AS AE ON (                           \n";
    $stSql .= "                AE.exercicio       = EA.exercicio                             \n";
    $stSql .= "          AND   AE.cod_autorizacao = EA.cod_autorizacao                       \n";
    $stSql .= "          AND   AE.cod_entidade    = EA.cod_entidade  )                       \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.autorizacao_reserva AS AR ON (                           \n";
    $stSql .= "                AR.exercicio       = AE.exercicio                             \n";
    $stSql .= "          AND   AR.cod_entidade    = AE.cod_entidade                          \n";
    $stSql .= "          AND   AR.cod_autorizacao = AE.cod_autorizacao )                     \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             orcamento.reserva           AS  R ON (                           \n";
    $stSql .= "                 R.cod_reserva     = AR.cod_reserva                           \n";
    $stSql .= "          AND    R.exercicio       = AR.exercicio     ),                      \n";
    $stSql .= "             sw_cgm                         AS  C,                            \n";
    $stSql .= "             empenho.pre_empenho         AS PE,                               \n";
    $stSql .= "             empenho.pre_empenho_despesa AS PD,                               \n";
    $stSql .= "             orcamento.vw_classificacao_despesa as ovwd,                      \n";
    $stSql .= "             orcamento.despesa           AS OD                                \n";
    $stSql .= "                LEFT OUTER JOIN orcamento.conta_despesa AS OCD ON             \n";
    $stSql .= "                    OD.cod_conta = OCD.cod_conta AND                          \n";
    $stSql .= "                    OD.exercicio = OCD.exercicio                              \n";
    $stSql .= "     WHERE                                                                    \n";
    $stSql .= "                EE.cod_pre_empenho = PE.cod_pre_empenho                       \n";
    $stSql .= "          AND   EE.exercicio       = PE.exercicio                             \n";
    $stSql .= "          AND   EE.cod_empenho     = EE.cod_empenho                           \n";
    $stSql .= "          AND  (EE.cod_categoria = 2 OR EE.cod_categoria = 3 )                \n";
    $stSql .= "          AND    C.numcgm          = PE.cgm_beneficiario                      \n";
    $stSql .= "          AND   PD.cod_pre_empenho = PE.cod_pre_empenho                       \n";
    $stSql .= "          AND   PD.exercicio       = PE.exercicio                             \n";
    $stSql .= "          AND   OD.exercicio       = PD.exercicio                             \n";
    $stSql .= "          AND   OD.cod_despesa     = PD.cod_despesa                           \n";
    $stSql .= "          AND ovwd.exercicio = PE.exercicio                                   \n";
    $stSql .= "          AND ovwd.cod_conta = PD.cod_conta                                   \n";
    $stSql .= "          AND  OD.num_unidade::varchar || OD.num_orgao::varchar IN (                              \n";
    $stSql .= "                            SELECT                                            \n";
    $stSql .= "                                  num_unidade::varchar || num_orgao::varchar                      \n";
    $stSql .= "                            FROM                                              \n";
    $stSql .= "                                empenho.permissao_autorizacao                 \n";
    $stSql .= "                            WHERE numcgm    = ".$this->getDado("numcgm")."    \n";
    $stSql .= "                            AND   exercicio = '".$this->getDado("exercicio")."'\n";
    $stSql .= "                                                 )                            \n";
    $stSql .= ") AS tabela                                                                   \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaConsultaEmpenho(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stGroup = "
    GROUP BY
      tabela.cod_entidade,
      tabela.cod_empenho,
      tabela.cod_pre_empenho,
      tabela.cod_autorizacao,
      tabela.cod_reserva,
      tabela.exercicio,
      tabela.dt_empenho,
      tabela.nom_fornecedor,
      tabela.vl_empenhado,
      tabela.mascara_classificacao
    ";
    $stSql = $this->montaRecuperaConsultaEmpenho().$stCondicao.$stGroup.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaConsultaEmpenho()
{
    $stSql  = "SELECT                                                                        \n";
    $stSql .= "      tabela.cod_entidade,                                                    \n";
    $stSql .= "      tabela.cod_empenho,                                                     \n";
    $stSql .= "      tabela.cod_pre_empenho,                                                 \n";
    $stSql .= "      tabela.cod_autorizacao,                                                 \n";
    $stSql .= "      tabela.cod_reserva,                                                     \n";
    $stSql .= "      tabela.exercicio,                                                       \n";
    $stSql .= "      tabela.dt_empenho,                                                      \n";
    $stSql .= "      tabela.nom_fornecedor,                                                  \n";
    $stSql .= "      tabela.vl_empenhado                                                     \n";
    $stSql .= "      ,tabela.mascara_classificacao                                           \n";
    $stSql .= "FROM (                                                                        \n";
    $stSql .= "     SELECT                                                                   \n";
    $stSql .= "             AE.cod_autorizacao,                                              \n";
    $stSql .= "             EE.cod_empenho,                                                  \n";
    $stSql .= "             EE.cod_categoria,                                                \n";
    $stSql .= "             EE.vl_saldo_anterior,                                            \n";
    $stSql .= "             TO_CHAR(EE.dt_vencimento,'dd/mm/yyyy') AS dt_vencimento,         \n";
    $stSql .= "             TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,               \n";
    $stSql .= "             PD.cod_despesa,                                                  \n";
    $stSql .= "             PE.descricao,                                                    \n";
    $stSql .= "             PE.exercicio,                                                    \n";
    $stSql .= "             PE.cod_pre_empenho,                                              \n";
    $stSql .= "             PE.cgm_beneficiario as credor,                                   \n";
    $stSql .= "             EE.cod_entidade,                                                 \n";
    $stSql .= "             AR.cod_reserva,                                                  \n";
    $stSql .= "             PD.cod_conta,                                                    \n";
    $stSql .= "             C.nom_cgm AS nom_fornecedor,                                     \n";
    $stSql .= "             R.vl_reserva,                                                    \n";
    $stSql .= "             OD.num_orgao,                                                    \n";
    $stSql .= "             OD.num_unidade,                                                  \n";
    $stSql .= "             OCD.cod_estrutural,                                              \n";
    $stSql .= "             OD.cod_recurso,                                                  \n";
    $stSql .= "             rec.cod_detalhamento,                                            \n";
    $stSql .= "             rec.cod_fonte,                                            \n";
    $stSql .= "             PE.cod_historico,                                                \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado(                          \n";
    $stSql .= "                                                   PE.exercicio               \n";
    $stSql .= "                                                  ,EE.cod_empenho             \n";
    $stSql .= "                                                  ,EE.cod_entidade            \n";
    $stSql .= "             ) AS vl_empenhado,                                               \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado_anulado(                  \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_empenhado_anulado,                                       \n";
    $stSql .= "           empenho.fn_consultar_valor_liquidado(                          \n";
    $stSql .= "                                                   PE.exercicio               \n";
    $stSql .= "                                                  ,EE.cod_empenho             \n";
    $stSql .= "                                                  ,EE.cod_entidade            \n";
    $stSql .= "             ) AS vl_liquidado,                                               \n";
    $stSql .= "           empenho.fn_consultar_valor_liquidado_anulado(                  \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_liquidado_anulado,                                       \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado_pago(                     \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_pago,                                                    \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado_pago_anulado(             \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_pago_anulado                                             \n";
    $stSql .= "             ,ovwd.mascara_classificacao                                      \n";
    $stSql .= "     FROM                                                                     \n";
    $stSql .= "             empenho.empenho             AS EE                            \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.empenho_autorizacao AS EA ON (                       \n";
    $stSql .= "                EA.exercicio       = EE.exercicio                             \n";
    $stSql .= "          AND   EA.cod_entidade    = EE.cod_entidade                          \n";
    $stSql .= "          AND   EA.cod_empenho     = EE.cod_empenho   )                       \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.autorizacao_empenho AS AE ON (                       \n";
    $stSql .= "                AE.exercicio       = EA.exercicio                             \n";
    $stSql .= "          AND   AE.cod_autorizacao = EA.cod_autorizacao                       \n";
    $stSql .= "          AND   AE.cod_entidade    = EA.cod_entidade  )                       \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.autorizacao_reserva AS AR ON (                       \n";
    $stSql .= "                AR.exercicio       = AE.exercicio                             \n";
    $stSql .= "          AND   AR.cod_entidade    = AE.cod_entidade                          \n";
    $stSql .= "          AND   AR.cod_autorizacao = AE.cod_autorizacao )                     \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             orcamento.reserva           AS  R ON (                       \n";
    $stSql .= "                 R.cod_reserva     = AR.cod_reserva                           \n";
    $stSql .= "          AND    R.exercicio       = AR.exercicio     ),                      \n";
    $stSql .= "             sw_cgm                         AS  C,                           \n";
    $stSql .= "             empenho.pre_empenho         AS PE,                           \n";
    $stSql .= "             empenho.pre_empenho_despesa AS PD,                           \n";
    $stSql .= "             orcamento.vw_classificacao_despesa as ovwd,                  \n";
    $stSql .= "             orcamento.despesa           AS OD                            \n";
    $stSql .= "                LEFT OUTER JOIN orcamento.conta_despesa AS OCD ON         \n";
    $stSql .= "                    OD.cod_conta = OCD.cod_conta AND                          \n";
    $stSql .= "                    OD.exercicio = OCD.exercicio                              \n";
    $stSql .= "                JOIN orcamento.recurso('".$this->getDado("exercicio")."') as rec \n";
    $stSql .= "                 ON ( rec.cod_recurso = od.cod_recurso                        \n";
    $stSql .= "                  AND rec.exercicio   = od.exercicio )                        \n";
    $stSql .= "     WHERE                                                                    \n";
    $stSql .= "                EE.cod_pre_empenho = PE.cod_pre_empenho                       \n";
    $stSql .= "          AND   EE.exercicio       = PE.exercicio                             \n";
    $stSql .= "          AND   EE.cod_empenho     = EE.cod_empenho                           \n";
    $stSql .= "          AND    C.numcgm          = PE.cgm_beneficiario                      \n";
    $stSql .= "          AND   PD.cod_pre_empenho = PE.cod_pre_empenho                       \n";
    $stSql .= "          AND   PD.exercicio       = PE.exercicio                             \n";
    $stSql .= "          AND   OD.exercicio       = PD.exercicio                             \n";
    $stSql .= "          AND   OD.cod_despesa     = PD.cod_despesa                           \n";
    $stSql .= "          AND ovwd.exercicio = PE.exercicio                                   \n";
    $stSql .= "          AND ovwd.cod_conta = PD.cod_conta                                   \n";
    if ( $this->getDado("tribunal") != "TCEMG" ) {
    $stSql .= "          AND  OD.num_unidade::varchar || OD.num_orgao::varchar IN (          \n";
    $stSql .= "                            SELECT                                            \n";
    $stSql .= "                                  num_unidade::varchar || num_orgao::varchar  \n";
    $stSql .= "                            FROM                                              \n";
    $stSql .= "                                empenho.permissao_autorizacao                 \n";
    $stSql .= "                            WHERE numcgm    = ".$this->getDado("numcgm")."    \n";
    $stSql .= "                            AND   exercicio = '".$this->getDado("exercicio")."'\n";
    $stSql .= "                                                 )                            \n";
    }
    $stSql .= ") AS tabela                                                                   \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaConsultaEmpenhoCompraLicitacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY") === false)?" ORDER BY $stOrdem":$stOrdem;
    
    $stGroup = " GROUP BY tabela.cod_entidade,
                          tabela.cod_empenho,
                          tabela.cod_pre_empenho,
                          tabela.cod_autorizacao,
                          tabela.cod_reserva,
                          tabela.exercicio,
                          tabela.dt_empenho,
                          tabela.nom_fornecedor,
                          tabela.vl_empenhado,
                          tabela.mascara_classificacao
                          
                    , tabela.compra_cod_modalidade
                    , tabela.compra_modalidade
                    , tabela.cod_compra_direta
                    , tabela.licitacao_cod_modalidade
                    , tabela.cod_licitacao
                    , tabela.licitacao_modalidade
                    , tabela.compra_modalidade
                          
                          
                          ";
                          
    $stSql = $this->montaRecuperaConsultaEmpenhoCompraLicitacao().$stCondicao.$stGroup.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaConsultaEmpenhoCompraLicitacao()
{
   $stSql = "  SELECT tabela.cod_entidade
                    , tabela.cod_empenho                                                     
                    , tabela.cod_pre_empenho
                    , tabela.cod_autorizacao
                    , tabela.cod_reserva
                    , tabela.exercicio
                    , tabela.dt_empenho
                    , tabela.nom_fornecedor
                    , tabela.vl_empenhado                                                     
                    , tabela.mascara_classificacao
                    , tabela.compra_cod_modalidade
                    , tabela.compra_modalidade
                    , tabela.cod_compra_direta
                    , tabela.licitacao_cod_modalidade
                    , tabela.cod_licitacao
                    , tabela.licitacao_modalidade
                    , tabela.compra_modalidade
                    
               FROM (                                                                        
                    SELECT  AE.cod_autorizacao,                                              
                            EE.cod_empenho,                                                  
                            EE.cod_categoria,                                                
                            EE.vl_saldo_anterior,                                            
                            TO_CHAR(EE.dt_vencimento,'dd/mm/yyyy') AS dt_vencimento,         
                            TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,               
                            PD.cod_despesa,                                                  
                            PE.descricao,                                                    
                            PE.exercicio,                                                    
                            PE.cod_pre_empenho,                                              
                            PE.cgm_beneficiario as credor,                                   
                            EE.cod_entidade,                                                 
                            AR.cod_reserva,                                                  
                            PD.cod_conta,                                                    
                            C.nom_cgm AS nom_fornecedor,                                     
                            R.vl_reserva,                                                    
                            OD.num_orgao,                                                    
                            OD.num_unidade,                                                  
                            OCD.cod_estrutural,                                              
                            OD.cod_recurso,                                                  
                            rec.cod_detalhamento,                                            
                            rec.cod_fonte,                                            
                            PE.cod_historico,                                                
                            empenho.fn_consultar_valor_empenhado(  PE.exercicio               
                                                                  ,EE.cod_empenho             
                                                                  ,EE.cod_entidade            
                            ) AS vl_empenhado,                                               
                            empenho.fn_consultar_valor_empenhado_anulado( PE.exercicio       
                                                                         ,EE.cod_empenho     
                                                                         ,EE.cod_entidade    
                            ) AS vl_empenhado_anulado,                                       
                            empenho.fn_consultar_valor_liquidado( PE.exercicio               
                                                                 ,EE.cod_empenho             
                                                                 ,EE.cod_entidade            
                            ) AS vl_liquidado,                                               
                            empenho.fn_consultar_valor_liquidado_anulado( PE.exercicio       
                                                                         ,EE.cod_empenho     
                                                                         ,EE.cod_entidade    
                            ) AS vl_liquidado_anulado,                                       
                            empenho.fn_consultar_valor_empenhado_pago(  PE.exercicio       
                                                                       ,EE.cod_empenho     
                                                                       ,EE.cod_entidade    
                            ) AS vl_pago,                                                    
                            empenho.fn_consultar_valor_empenhado_pago_anulado( PE.exercicio       
                                                                              ,EE.cod_empenho     
                                                                              ,EE.cod_entidade    
                            ) AS vl_pago_anulado                                             
                           , ovwd.mascara_classificacao
                           , compra_direta.cod_modalidade AS compra_cod_modalidade
                           , compra_direta.cod_compra_direta
                           , compra_modalidade.descricao AS compra_modalidade
                           , licitacao_modalidade.descricao AS licitacao_modalidade
                           , adjudicacao.cod_modalidade AS licitacao_cod_modalidade
                           , adjudicacao.cod_licitacao
               
                     FROM empenho.empenho AS EE
                
                LEFT JOIN empenho.empenho_autorizacao AS EA
                       ON EA.exercicio       = EE.exercicio                             
                      AND EA.cod_entidade    = EE.cod_entidade                          
                      AND EA.cod_empenho     = EE.cod_empenho
                      
                LEFT JOIN empenho.autorizacao_empenho AS AE
                       ON AE.exercicio       = EA.exercicio                             
                      AND AE.cod_autorizacao = EA.cod_autorizacao                       
                      AND AE.cod_entidade    = EA.cod_entidade
                      
               LEFT JOIN empenho.autorizacao_reserva AS AR
                      ON AR.exercicio       = AE.exercicio                             
                     AND AR.cod_entidade    = AE.cod_entidade                          
                     AND AR.cod_autorizacao = AE.cod_autorizacao                    
               
               LEFT JOIN orcamento.reserva AS  R
                      ON R.cod_reserva     = AR.cod_reserva                           
                     AND R.exercicio    = AR.exercicio
                     
              INNER JOIN empenho.pre_empenho AS PE
                      ON EE.cod_pre_empenho = PE.cod_pre_empenho                       
                     AND EE.exercicio       = PE.exercicio                             
            
              INNER JOIN sw_cgm AS  C
                      ON C.numcgm = PE.cgm_beneficiario
                      
              INNER JOIN empenho.pre_empenho_despesa AS PD
                      ON PD.cod_pre_empenho = PE.cod_pre_empenho                       
                     AND PD.exercicio       = PE.exercicio       
                      
              INNER JOIN orcamento.despesa AS OD
                      ON OD.exercicio       = PD.exercicio                             
                     AND OD.cod_despesa     = PD.cod_despesa                           
                           
              INNER JOIN orcamento.vw_classificacao_despesa as ovwd
                      ON ovwd.exercicio = PE.exercicio                                   
                     AND ovwd.cod_conta = PD.cod_conta
                           
              LEFT OUTER JOIN orcamento.conta_despesa AS OCD
                           ON OD.cod_conta = OCD.cod_conta
                          AND OD.exercicio = OCD.exercicio                              
              
              INNER JOIN orcamento.recurso('".$this->getDado("exercicio")."') as rec 
                      ON rec.cod_recurso  = od.cod_recurso                        
                     AND rec.exercicio    = od.exercicio

               LEFT JOIN empenho.item_pre_empenho
                      ON item_pre_empenho.cod_pre_empenho = pe.cod_pre_empenho
                     AND item_pre_empenho.exercicio       = pe.exercicio
                          
               LEFT JOIN empenho.item_pre_empenho_julgamento
                      ON item_pre_empenho_julgamento.cod_pre_empenho  = item_pre_empenho.cod_pre_empenho   
                     AND item_pre_empenho_julgamento.exercicio        = item_pre_empenho.exercicio
                     AND item_pre_empenho_julgamento.num_item         = item_pre_empenho.num_item
                
                LEFT JOIN compras.julgamento_item
                       ON julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio_julgamento
                      AND julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao 
                      AND julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item
                      AND julgamento_item.lote           = item_pre_empenho_julgamento.lote
                      AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
                
                LEFT JOIN compras.cotacao_item
                       ON cotacao_item.exercicio   = julgamento_item.exercicio
                      AND cotacao_item.cod_cotacao = julgamento_item.cod_cotacao
                      AND cotacao_item.lote        = julgamento_item.lote
                      AND cotacao_item.cod_item    = julgamento_item.cod_item
                
                LEFT JOIN compras.cotacao
                       ON cotacao.cod_cotacao = cotacao_item.cod_cotacao
                      AND cotacao.exercicio   = cotacao_item.exercicio
                
                LEFT JOIN compras.mapa_cotacao
                       ON mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                      AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                
                LEFT JOIN compras.mapa
                       ON mapa.cod_mapa  = mapa_cotacao.cod_mapa
                      AND mapa.exercicio = mapa_cotacao.exercicio_mapa
                
                LEFT JOIN compras.compra_direta
                       ON compra_direta.cod_mapa       = mapa.cod_mapa
                      AND compra_direta.exercicio_mapa = mapa.exercicio
                      
                LEFT JOIN compras.modalidade AS compra_modalidade
                       ON compra_modalidade.cod_modalidade = compra_direta.cod_modalidade
                
                LEFT JOIN licitacao.adjudicacao
                       ON adjudicacao.exercicio_cotacao = cotacao_item.exercicio 
                      AND adjudicacao.cod_cotacao       = cotacao_item.cod_cotacao
                      AND adjudicacao.lote              = cotacao_item.lote
                      AND adjudicacao.cod_item          = cotacao_item.cod_item 
                      
                LEFT JOIN compras.modalidade AS licitacao_modalidade
                       ON licitacao_modalidade.cod_modalidade = adjudicacao.cod_modalidade \n";
        
    if ( $this->getDado("tribunal") != "TCEMG" ) {
        $stSql .= " WHERE OD.num_unidade::varchar || OD.num_orgao::varchar
                       IN ( SELECT num_unidade::varchar || num_orgao::varchar  
                              FROM empenho.permissao_autorizacao                 
                              WHERE numcgm    = ".$this->getDado("numcgm")."    
                                AND exercicio = '".$this->getDado("exercicio")."'
                        ) \n";
    }
    
    $stSql .= ") AS tabela \n";
    return $stSql; 
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRestosAPagar(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRestosAPagar().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaRestosAPagarAjustes(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stGroup  = "   GROUP BY                                         \n";
    $stGroup .= "       tabela.cod_autorizacao,                      \n";
    $stGroup .= "       tabela.cod_empenho,                          \n";
    $stGroup .= "       tabela.vl_saldo_anterior,                    \n";
    $stGroup .= "       tabela.dt_vencimento,                        \n";
    $stGroup .= "       tabela.dt_empenho,                           \n";
    $stGroup .= "       tabela.cod_despesa,                          \n";
    $stGroup .= "       tabela.descricao,                            \n";
    $stGroup .= "       tabela.exercicio,                            \n";
    $stGroup .= "       tabela.cod_pre_empenho,                      \n";
    $stGroup .= "       tabela.credor,                               \n";
    $stGroup .= "       tabela.cod_entidade,                         \n";
    $stGroup .= "       tabela.cod_reserva,                          \n";
    $stGroup .= "       tabela.cod_conta,                            \n";
    $stGroup .= "       tabela.nom_fornecedor,                       \n";
    $stGroup .= "       tabela.vl_reserva,                           \n";
    $stGroup .= "       tabela.implantado,                           \n";
    $stGroup .= "       tabela.num_orgao,                            \n";
    $stGroup .= "       tabela.num_unidade,                          \n";
    $stGroup .= "       tabela.cod_estrutural,                       \n";
    $stGroup .= "       tabela.cod_recurso,                          \n";
    $stGroup .= "       tabela.cod_historico,                        \n";
    $stGroup .= "       tabela.vl_empenhado,                         \n";
    $stGroup .= "       tabela.vl_empenhado_anulado,                 \n";
    $stGroup .= "       tabela.vl_liquidado,                         \n";
    $stGroup .= "       tabela.vl_liquidado_anulado,                 \n";
    $stGroup .= "       tabela.vl_pago,                              \n";
    $stGroup .= "       tabela.vl_pago_anulado                       \n";

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRestosAPagarAjustes().$stCondicao.$stGroup.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaRestosAPagarAjustesCompraLicitacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stGroup  = "   GROUP BY tabela.cod_autorizacao,                      
                             tabela.cod_empenho,                          
                             tabela.vl_saldo_anterior,                    
                             tabela.dt_vencimento,                        
                             tabela.dt_empenho,                           
                             tabela.cod_despesa,                          
                             tabela.descricao,                            
                             tabela.exercicio,                            
                             tabela.cod_pre_empenho,                      
                             tabela.credor,                               
                             tabela.cod_entidade,                         
                             tabela.cod_reserva,                          
                             tabela.cod_conta,                            
                             tabela.nom_fornecedor,                       
                             tabela.vl_reserva,                           
                             tabela.implantado,                           
                             tabela.num_orgao,                            
                             tabela.num_unidade,                          
                             tabela.cod_estrutural,                       
                             tabela.cod_recurso,                          
                             tabela.cod_historico,                        
                             tabela.vl_empenhado,                         
                             tabela.vl_empenhado_anulado,                 
                             tabela.vl_liquidado,                         
                             tabela.vl_liquidado_anulado,                 
                             tabela.vl_pago,                              
                             tabela.vl_pago_anulado,
                             tabela.compra_cod_modalidade,
                             tabela.cod_compra_direta,
                             tabela.licitacao_cod_modalidade,
                             tabela.cod_licitacao ";

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY") === false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRestosAPagarAjustesCompraLicitacao().$stCondicao.$stGroup.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRestosAPagar()
{
    $stSql  = "SELECT                                                                        \n";
    $stSql .= "      tabela.*                                                                \n";
    $stSql .= "FROM (                                                                        \n";
    $stSql .= "     SELECT                                                                   \n";
    $stSql .= "             AE.cod_autorizacao,                                              \n";
    $stSql .= "             EE.cod_empenho,                                                  \n";
    $stSql .= "             EE.vl_saldo_anterior,                                            \n";
    $stSql .= "             TO_CHAR(EE.dt_vencimento,'dd/mm/yyyy') AS dt_vencimento,         \n";
    $stSql .= "             TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,               \n";
    $stSql .= "             PED_D_CD.cod_despesa,                                            \n";
    $stSql .= "             PE.descricao,                                                    \n";
    $stSql .= "             EE.exercicio as exercicio_empenho,                               \n";
    $stSql .= "             PE.exercicio,                                                    \n";
    $stSql .= "             PE.cod_pre_empenho,                                              \n";
    $stSql .= "             PE.cgm_beneficiario as credor,                                   \n";
    $stSql .= "             EE.cod_entidade,                                                 \n";
    $stSql .= "             AR.cod_reserva,                                                  \n";
    $stSql .= "             PED_D_CD.cod_conta,                                              \n";
    $stSql .= "             C.nom_cgm AS nom_fornecedor,                                     \n";
    $stSql .= "             R.vl_reserva,                                                    \n";
    $stSql .= "             PE.implantado,                                                   \n";
    $stSql .= "             CASE WHEN PE.implantado = true THEN                              \n";
    $stSql .= "                 RE.num_orgao                                                 \n";
    $stSql .= "             ELSE                                                             \n";
    $stSql .= "                 PED_D_CD.num_orgao                                           \n";
    $stSql .= "             END as num_orgao,                                                \n";
    $stSql .= "             CASE WHEN PE.implantado = true THEN                              \n";
    $stSql .= "                 RE.num_unidade                                               \n";
    $stSql .= "             ELSE                                                             \n";
    $stSql .= "                 PED_D_CD.num_unidade                                         \n";
    $stSql .= "             END as num_unidade,                                              \n";
    $stSql .= "             CASE WHEN PE.implantado = true THEN                              \n";
    $stSql .= "                 RE.cod_estrutural                                            \n";
    $stSql .= "             ELSE                                                             \n";
    $stSql .= "                 PED_D_CD.cod_estrutural                                      \n";
    $stSql .= "             END as cod_estrutural,                                           \n";
    $stSql .= "             CASE WHEN PE.implantado = true THEN                              \n";
    $stSql .= "                 RE.recurso                                                   \n";
    $stSql .= "             ELSE                                                             \n";
    $stSql .= "                 PED_D_CD.cod_recurso                                         \n";
    $stSql .= "             END as cod_recurso,                                              \n";
    $stSql .= "             PE.cod_historico,                                                \n";
    if ( $this->getDado( "inSituacao" ) == 3 ) {
        $stSql .= "             NL.cod_nota,                                                     \n";
        $stSql .= "             NL.exercicio as exercicio_liquidacao,                            \n";
    }
    $stSql .= "           empenho.fn_consultar_valor_empenhado(                          \n";
    $stSql .= "                                                   PE.exercicio               \n";
    $stSql .= "                                                  ,EE.cod_empenho             \n";
    $stSql .= "                                                  ,EE.cod_entidade            \n";
    $stSql .= "             ) AS vl_empenhado,                                               \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado_anulado(                  \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_empenhado_anulado,                                       \n";
    $stSql .= "           empenho.fn_consultar_valor_liquidado(                          \n";
    $stSql .= "                                                   PE.exercicio               \n";
    $stSql .= "                                                  ,EE.cod_empenho             \n";
    $stSql .= "                                                  ,EE.cod_entidade            \n";
    $stSql .= "             ) AS vl_liquidado,                                               \n";
    $stSql .= "           empenho.fn_consultar_valor_liquidado_anulado(                  \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_liquidado_anulado,                                       \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado_pago(                         \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_pago,                                                    \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado_pago_anulado(                 \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_pago_anulado                                             \n";
    $stSql .= "     FROM                                                                     \n";
    $stSql .= "             empenho.empenho             AS EE                                \n";
    if ( $this->getDado( "inSituacao" ) == 3 ) {
        $stSql .= "             LEFT JOIN                                                    \n";
        $stSql .= "             empenho.nota_liquidacao AS NL ON (                           \n";
        $stSql .= "                NL.exercicio_empenho = EE.exercicio                       \n";
        $stSql .= "          AND   NL.cod_entidade      = EE.cod_entidade                    \n";
        $stSql .= "          AND   NL.cod_empenho       = EE.cod_empenho   )                 \n";
    }
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.empenho_autorizacao AS EA ON (                           \n";
    $stSql .= "                EA.exercicio       = EE.exercicio                             \n";
    $stSql .= "          AND   EA.cod_entidade    = EE.cod_entidade                          \n";
    $stSql .= "          AND   EA.cod_empenho     = EE.cod_empenho   )                       \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.autorizacao_empenho AS AE ON (                       \n";
    $stSql .= "                AE.exercicio       = EA.exercicio                             \n";
    $stSql .= "          AND   AE.cod_autorizacao = EA.cod_autorizacao                       \n";
    $stSql .= "          AND   AE.cod_entidade    = EA.cod_entidade  )                       \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.autorizacao_reserva AS AR ON (                       \n";
    $stSql .= "                AR.exercicio       = AE.exercicio                             \n";
    $stSql .= "          AND   AR.cod_entidade    = AE.cod_entidade                          \n";
    $stSql .= "          AND   AR.cod_autorizacao = AE.cod_autorizacao )                     \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             orcamento.reserva           AS  R ON (                       \n";
    $stSql .= "                 R.cod_reserva     = AR.cod_reserva                           \n";
    $stSql .= "          AND    R.exercicio       = AR.exercicio     ),                      \n";
    $stSql .= "             sw_cgm                         AS  C,                           \n";
    $stSql .= "             empenho.pre_empenho AS PE                                    \n";
    $stSql .= "                 LEFT OUTER JOIN empenho.restos_pre_empenho as RE ON      \n";
    $stSql .= "                     PE.exercicio = RE.exercicio AND                          \n";
    $stSql .= "                     PE.cod_pre_empenho = RE.cod_pre_empenho                  \n";
    $stSql .= "                 LEFT OUTER JOIN (                                            \n";
    $stSql .= "                     SELECT                                                   \n";
    $stSql .= "                         PED.exercicio, PED.cod_pre_empenho, D.cod_despesa, D.num_pao, D.num_orgao, D.num_unidade,D.cod_recurso, CD.cod_conta, CD.cod_estrutural \n";
    $stSql .= "                     FROM                                                     \n";
    $stSql .= "                         empenho.pre_empenho_despesa as PED, orcamento.despesa as D, orcamento.conta_despesa as CD \n";
    $stSql .= "                     WHERE                                                    \n";
    $stSql .= "                         PED.cod_despesa = D.cod_despesa and PED.exercicio = D.exercicio and PED.cod_conta = CD.cod_conta and D.exercicio = CD.exercicio \n";
    $stSql .= "                 ) as PED_D_CD ON                                             \n";
    $stSql .= "                     PE.exercicio = PED_D_CD.exercicio AND                    \n";
    $stSql .= "                     PE.cod_pre_empenho = PED_D_CD.cod_pre_empenho            \n";
    $stSql .= "     WHERE                                                                    \n";
    $stSql .= "                EE.cod_pre_empenho = PE.cod_pre_empenho                       \n";
    $stSql .= "          AND   EE.exercicio       = PE.exercicio                             \n";
    $stSql .= "          AND   EE.cod_empenho     = EE.cod_empenho                           \n";
    $stSql .= "          AND    C.numcgm          = PE.cgm_beneficiario                      \n";
    $stSql .= ") AS tabela                                                                   \n";

    return $stSql;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRestosAPagarAjustes()
{
    $stSql  = "SELECT                                                                        \n";
    $stSql .= "      tabela.*                                                                \n";
    $stSql .= "FROM (                                                                        \n";
    $stSql .= "     SELECT                                                                   \n";
    $stSql .= "             AE.cod_autorizacao,                                              \n";
    $stSql .= "             EE.cod_empenho,                                                  \n";
    $stSql .= "             EE.vl_saldo_anterior,                                            \n";
    $stSql .= "             TO_CHAR(EE.dt_vencimento,'dd/mm/yyyy') AS dt_vencimento,         \n";
    $stSql .= "             TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,               \n";
    $stSql .= "             PED_D_CD.cod_despesa,                                            \n";
    $stSql .= "             PE.descricao,                                                    \n";
    $stSql .= "             PE.exercicio,                                                    \n";
    $stSql .= "             PE.cod_pre_empenho,                                              \n";
    $stSql .= "             PE.cgm_beneficiario as credor,                                   \n";
    $stSql .= "             EE.cod_entidade,                                                 \n";
    $stSql .= "             AR.cod_reserva,                                                  \n";
    $stSql .= "             PED_D_CD.cod_conta,                                              \n";
    $stSql .= "             C.nom_cgm AS nom_fornecedor,                                     \n";
    $stSql .= "             R.vl_reserva,                                                    \n";
    $stSql .= "             PE.implantado,                                                   \n";
    $stSql .= "             CASE WHEN PE.implantado = true THEN                              \n";
    $stSql .= "                 RE.num_orgao                                                 \n";
    $stSql .= "             ELSE                                                             \n";
    $stSql .= "                 PED_D_CD.num_orgao                                           \n";
    $stSql .= "             END as num_orgao,                                                \n";
    $stSql .= "             CASE WHEN PE.implantado = true THEN                              \n";
    $stSql .= "                 RE.num_unidade                                               \n";
    $stSql .= "             ELSE                                                             \n";
    $stSql .= "                 PED_D_CD.num_unidade                                         \n";
    $stSql .= "             END as num_unidade,                                              \n";
    $stSql .= "             CASE WHEN PE.implantado = true THEN                              \n";
    $stSql .= "                 RE.cod_estrutural                                            \n";
    $stSql .= "             ELSE                                                             \n";
    $stSql .= "                 PED_D_CD.cod_estrutural                                      \n";
    $stSql .= "             END as cod_estrutural,                                           \n";
    $stSql .= "             CASE WHEN PE.implantado = true THEN                              \n";
    $stSql .= "                 RE.recurso                                                   \n";
    $stSql .= "             ELSE                                                             \n";
    $stSql .= "                 PED_D_CD.cod_recurso                                         \n";
    $stSql .= "             END as cod_recurso,                                              \n";
    $stSql .= "             PE.cod_historico,                                                \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado(                          \n";
    $stSql .= "                                                   PE.exercicio               \n";
    $stSql .= "                                                  ,EE.cod_empenho             \n";
    $stSql .= "                                                  ,EE.cod_entidade            \n";
    $stSql .= "             ) AS vl_empenhado,                                               \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado_anulado(                  \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_empenhado_anulado,                                       \n";
    $stSql .= "           empenho.fn_consultar_valor_liquidado(                          \n";
    $stSql .= "                                                   PE.exercicio               \n";
    $stSql .= "                                                  ,EE.cod_empenho             \n";
    $stSql .= "                                                  ,EE.cod_entidade            \n";
    $stSql .= "             ) AS vl_liquidado,                                               \n";
    $stSql .= "           empenho.fn_consultar_valor_liquidado_anulado(                  \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_liquidado_anulado,                                       \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado_pago(                     \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_pago,                                                    \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado_pago_anulado(             \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_pago_anulado                                             \n";
    $stSql .= "     FROM                                                                     \n";
    $stSql .= "             empenho.empenho             AS EE                            \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.empenho_autorizacao AS EA ON (                       \n";
    $stSql .= "                EA.exercicio       = EE.exercicio                             \n";
    $stSql .= "          AND   EA.cod_entidade    = EE.cod_entidade                          \n";
    $stSql .= "          AND   EA.cod_empenho     = EE.cod_empenho   )                       \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.autorizacao_empenho AS AE ON (                       \n";
    $stSql .= "                AE.exercicio       = EA.exercicio                             \n";
    $stSql .= "          AND   AE.cod_autorizacao = EA.cod_autorizacao                       \n";
    $stSql .= "          AND   AE.cod_entidade    = EA.cod_entidade  )                       \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.autorizacao_reserva AS AR ON (                       \n";
    $stSql .= "                AR.exercicio       = AE.exercicio                             \n";
    $stSql .= "          AND   AR.cod_entidade    = AE.cod_entidade                          \n";
    $stSql .= "          AND   AR.cod_autorizacao = AE.cod_autorizacao )                     \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             orcamento.reserva           AS  R ON (                       \n";
    $stSql .= "                 R.cod_reserva     = AR.cod_reserva                           \n";
    $stSql .= "          AND    R.exercicio       = AR.exercicio     ),                      \n";
    $stSql .= "             sw_cgm                         AS  C,                           \n";
    $stSql .= "             empenho.pre_empenho AS PE                                    \n";
    $stSql .= "                 LEFT OUTER JOIN empenho.restos_pre_empenho as RE ON      \n";
    $stSql .= "                     PE.exercicio = RE.exercicio AND                          \n";
    $stSql .= "                     PE.cod_pre_empenho = RE.cod_pre_empenho                  \n";
    $stSql .= "                 LEFT OUTER JOIN (                                            \n";
    $stSql .= "                     SELECT                                                   \n";
    $stSql .= "                         PED.exercicio, PED.cod_pre_empenho, D.cod_despesa, D.num_pao, D.num_orgao, D.num_unidade,D.cod_recurso, CD.cod_conta, CD.cod_estrutural \n";
    $stSql .= "                     FROM                                                     \n";
    $stSql .= "                         empenho.pre_empenho_despesa as PED, orcamento.despesa as D, orcamento.conta_despesa as CD \n";
    $stSql .= "                     WHERE                                                    \n";
    $stSql .= "                         PED.cod_despesa = D.cod_despesa and PED.exercicio = D.exercicio and PED.cod_conta = CD.cod_conta and D.exercicio = CD.exercicio \n";
    $stSql .= "                 ) as PED_D_CD ON                                             \n";
    $stSql .= "                     PE.exercicio = PED_D_CD.exercicio AND                    \n";
    $stSql .= "                     PE.cod_pre_empenho = PED_D_CD.cod_pre_empenho            \n";
    $stSql .= "     WHERE                                                                    \n";
    $stSql .= "                EE.cod_pre_empenho = PE.cod_pre_empenho                       \n";
    $stSql .= "          AND   EE.exercicio       = PE.exercicio                             \n";
    $stSql .= "          AND   EE.cod_empenho     = EE.cod_empenho                           \n";
    $stSql .= "          AND    C.numcgm          = PE.cgm_beneficiario                      \n";
    $stSql .= ") AS tabela                                                                   \n";

    return $stSql;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRestosAPagarAjustesCompraLicitacao()
{
    $stSql = " SELECT tabela.*                                                                
                FROM (                                                                        
                   SELECT AE.cod_autorizacao,                                              
                          EE.cod_empenho,                                                  
                          EE.vl_saldo_anterior,                                            
                          TO_CHAR(EE.dt_vencimento,'dd/mm/yyyy') AS dt_vencimento,         
                          TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,               
                          PED_D_CD.cod_despesa,                                            
                          PE.descricao,                                                    
                          PE.exercicio,                                                    
                          PE.cod_pre_empenho,                                              
                          PE.cgm_beneficiario as credor,                                   
                          EE.cod_entidade,                                                 
                          AR.cod_reserva,                                                  
                          PED_D_CD.cod_conta,                                              
                          C.nom_cgm AS nom_fornecedor,                                     
                          R.vl_reserva,                                                    
                          PE.implantado,                                                   
                          CASE WHEN PE.implantado = true
                               THEN RE.num_orgao                                                 
                               ELSE PED_D_CD.num_orgao                                           
                          END as num_orgao,                                                
                          CASE WHEN PE.implantado = true
                               THEN RE.num_unidade                                               
                               ELSE PED_D_CD.num_unidade                                         
                          END as num_unidade,                                              
                          CASE WHEN PE.implantado = true
                               THEN RE.cod_estrutural                                            
                               ELSE PED_D_CD.cod_estrutural                                      
                          END as cod_estrutural,                                           
                          CASE WHEN PE.implantado = true
                               THEN RE.recurso                                                   
                               ELSE PED_D_CD.cod_recurso                                         
                          END as cod_recurso,                                              
                          PE.cod_historico,                                                
                          empenho.fn_consultar_valor_empenhado( PE.exercicio               
                                                               ,EE.cod_empenho             
                                                               ,EE.cod_entidade            
                           ) AS vl_empenhado,                                               
                          empenho.fn_consultar_valor_empenhado_anulado( PE.exercicio       
                                                                       ,EE.cod_empenho     
                                                                       ,EE.cod_entidade    
                           ) AS vl_empenhado_anulado,                                       
                          empenho.fn_consultar_valor_liquidado( PE.exercicio               
                                                               ,EE.cod_empenho             
                                                               ,EE.cod_entidade            
                           ) AS vl_liquidado,                                               
                          empenho.fn_consultar_valor_liquidado_anulado( PE.exercicio       
                                                                       ,EE.cod_empenho     
                                                                       ,EE.cod_entidade    
                           ) AS vl_liquidado_anulado,                                       
                          empenho.fn_consultar_valor_empenhado_pago( PE.exercicio       
                                                                    ,EE.cod_empenho     
                                                                    ,EE.cod_entidade    
                           ) AS vl_pago,                                                    
                          empenho.fn_consultar_valor_empenhado_pago_anulado( PE.exercicio       
                                                                            ,EE.cod_empenho     
                                                                            ,EE.cod_entidade    
                           ) AS vl_pago_anulado,
                          compra_direta.cod_modalidade AS compra_cod_modalidade,
                          compra_direta.cod_compra_direta,
                          adjudicacao.cod_modalidade AS licitacao_cod_modalidade,
                          adjudicacao.cod_licitacao
                   
                   FROM  empenho.empenho  AS EE                            
                   
                   LEFT JOIN empenho.empenho_autorizacao AS EA
                          ON EA.exercicio       = EE.exercicio                             
                         AND EA.cod_entidade    = EE.cod_entidade                          
                         AND EA.cod_empenho     = EE.cod_empenho 
                  
                  LEFT JOIN empenho.autorizacao_empenho AS AE
                         ON AE.exercicio       = EA.exercicio                             
                        AND AE.cod_autorizacao = EA.cod_autorizacao                       
                        AND AE.cod_entidade    = EA.cod_entidade
                        
                  LEFT JOIN empenho.autorizacao_reserva AS AR
                         ON AR.exercicio       = AE.exercicio                             
                        AND AR.cod_entidade    = AE.cod_entidade                          
                        AND AR.cod_autorizacao = AE.cod_autorizacao
                        
                  LEFT JOIN orcamento.reserva AS  R
                         ON R.cod_reserva     = AR.cod_reserva                           
                        AND R.exercicio       = AR.exercicio 
                        
                  INNER JOIN empenho.pre_empenho AS PE
                          ON EE.cod_pre_empenho = PE.cod_pre_empenho                       
                         AND EE.exercicio       = PE.exercicio                             
                        
                  INNER JOIN sw_cgm AS  C
                          ON C.numcgm = PE.cgm_beneficiario
              
                  LEFT OUTER JOIN empenho.restos_pre_empenho as RE
                               ON PE.exercicio = RE.exercicio
                              AND PE.cod_pre_empenho = RE.cod_pre_empenho
              
              LEFT JOIN empenho.item_pre_empenho
                     ON item_pre_empenho.cod_pre_empenho = pe.cod_pre_empenho
                    AND item_pre_empenho.exercicio       = pe.exercicio
                    
              LEFT JOIN empenho.item_pre_empenho_julgamento
                     ON item_pre_empenho_julgamento.cod_pre_empenho  = item_pre_empenho.cod_pre_empenho   
                    AND item_pre_empenho_julgamento.exercicio        = item_pre_empenho.exercicio
                    AND item_pre_empenho_julgamento.num_item         = item_pre_empenho.num_item
              
              LEFT JOIN compras.julgamento_item
                    ON julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio_julgamento
                   AND julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao 
                   AND julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item
                   AND julgamento_item.lote           = item_pre_empenho_julgamento.lote
                   AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
              
              LEFT JOIN compras.cotacao_item
                    ON cotacao_item.exercicio   = julgamento_item.exercicio
                   AND cotacao_item.cod_cotacao = julgamento_item.cod_cotacao
                   AND cotacao_item.lote        = julgamento_item.lote
                   AND cotacao_item.cod_item    = julgamento_item.cod_item
              
              LEFT JOIN compras.cotacao
                    ON cotacao.cod_cotacao = cotacao_item.cod_cotacao
                   AND cotacao.exercicio   = cotacao_item.exercicio
              
              LEFT JOIN compras.mapa_cotacao
                    ON mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                   AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio
              
              LEFT JOIN compras.mapa
                    ON mapa.cod_mapa  = mapa_cotacao.cod_mapa
                   AND mapa.exercicio = mapa_cotacao.exercicio_mapa
              
              LEFT JOIN compras.compra_direta
                    ON compra_direta.cod_mapa       = mapa.cod_mapa
                   AND compra_direta.exercicio_mapa = mapa.exercicio
              
              LEFT JOIN licitacao.adjudicacao
                    ON adjudicacao.exercicio_cotacao = cotacao_item.exercicio 
                   AND adjudicacao.cod_cotacao       = cotacao_item.cod_cotacao
                   AND adjudicacao.lote              = cotacao_item.lote
                   AND adjudicacao.cod_item          = cotacao_item.cod_item 

             LEFT OUTER JOIN (   SELECT PED.exercicio
                                      , PED.cod_pre_empenho
                                      , D.cod_despesa
                                      , D.num_pao
                                      , D.num_orgao
                                      , D.num_unidade
                                      , D.cod_recurso
                                      , CD.cod_conta
                                      , CD.cod_estrutural 
                                   FROM empenho.pre_empenho_despesa as PED
                                      , orcamento.despesa as D
                                      , orcamento.conta_despesa as CD 
                                  WHERE PED.cod_despesa = D.cod_despesa
                                    AND PED.exercicio   = D.exercicio
                                    AND PED.cod_conta   = CD.cod_conta
                                    AND D.exercicio     = CD.exercicio 
                ) as PED_D_CD
                  ON PE.exercicio       = PED_D_CD.exercicio AND                    
                     PE.cod_pre_empenho = PED_D_CD.cod_pre_empenho
                     
            ) AS tabela ";
    
    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRestosConsultaEmpenho(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stGroup = "
    GROUP BY
      tabela.cod_entidade,
      tabela.cod_empenho,
      tabela.cod_pre_empenho,
      tabela.cod_autorizacao,
      tabela.cod_reserva,
      tabela.implantado,
      tabela.exercicio,
      tabela.dt_empenho,
      tabela.nom_fornecedor,
      tabela.vl_empenhado
    ";
    $stSql = $this->montaRestosConsultaEmpenho().$stCondicao.$stGroup.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRestosConsultaEmpenho()
{
    $stSql  = "SELECT                                                                        \n";
    $stSql .= "      tabela.cod_entidade,                                                    \n";
    $stSql .= "      tabela.cod_empenho,                                                     \n";
    $stSql .= "      tabela.cod_pre_empenho,                                                 \n";
    $stSql .= "      tabela.cod_autorizacao,                                                 \n";
    $stSql .= "      tabela.cod_reserva,                                                 \n";
    $stSql .= "      tabela.implantado,                                                 \n";
    $stSql .= "      tabela.exercicio,                                                       \n";
    $stSql .= "      tabela.dt_empenho,                                                      \n";
    $stSql .= "      tabela.nom_fornecedor,                                                  \n";
    $stSql .= "      tabela.vl_empenhado                                                     \n";
    $stSql .= "FROM (                                                                        \n";
    $stSql .= "     SELECT                                                                   \n";
    $stSql .= "             AE.cod_autorizacao,                                              \n";
    $stSql .= "             EE.cod_empenho,                                                  \n";
    $stSql .= "             EE.vl_saldo_anterior,                                            \n";
    $stSql .= "             TO_CHAR(EE.dt_vencimento,'dd/mm/yyyy') AS dt_vencimento,         \n";
    $stSql .= "             TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,               \n";
    $stSql .= "             PED_D_CD.cod_despesa,                                            \n";
    $stSql .= "             PE.descricao,                                                    \n";
    $stSql .= "             PE.exercicio,                                                    \n";
    $stSql .= "             PE.cod_pre_empenho,                                              \n";
    $stSql .= "             PE.cgm_beneficiario as credor,                                   \n";
    $stSql .= "             EE.cod_entidade,                                                 \n";
    $stSql .= "             AR.cod_reserva,                                                  \n";
    $stSql .= "             PED_D_CD.cod_conta,                                              \n";
    $stSql .= "             C.nom_cgm AS nom_fornecedor,                                     \n";
    $stSql .= "             R.vl_reserva,                                                    \n";
    $stSql .= "             PE.implantado,                                                   \n";
    $stSql .= "             CASE WHEN PE.implantado = true THEN                              \n";
    $stSql .= "                 RE.num_orgao                                                 \n";
    $stSql .= "             ELSE                                                             \n";
    $stSql .= "                 PED_D_CD.num_orgao                                           \n";
    $stSql .= "             END as num_orgao,                                                \n";
    $stSql .= "             CASE WHEN PE.implantado = true THEN                              \n";
    $stSql .= "                 RE.num_unidade                                               \n";
    $stSql .= "             ELSE                                                             \n";
    $stSql .= "                 PED_D_CD.num_unidade                                         \n";
    $stSql .= "             END as num_unidade,                                              \n";
    $stSql .= "             CASE WHEN PE.implantado = true THEN                              \n";
    $stSql .= "                 RE.cod_estrutural                                            \n";
    $stSql .= "             ELSE                                                             \n";
    $stSql .= "                 PED_D_CD.cod_estrutural                                      \n";
    $stSql .= "             END as cod_estrutural,                                           \n";
    $stSql .= "             CASE WHEN PE.implantado = true THEN                              \n";
    $stSql .= "                 RE.recurso                                                   \n";
    $stSql .= "             ELSE                                                             \n";
    $stSql .= "                 PED_D_CD.cod_recurso                                         \n";
    $stSql .= "             END as cod_recurso,                                              \n";
    $stSql .= "             PED_D_CD.cod_fonte,		                                      	 \n";
    $stSql .= "             PE.cod_historico,                                                \n";
    $stSql .= "             NL.cod_nota,                                                     \n";
    $stSql .= "             NL.exercicio as exercicio_liquidacao,                            \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado(                         	 \n";
    $stSql .= "                                                   PE.exercicio               \n";
    $stSql .= "                                                  ,EE.cod_empenho             \n";
    $stSql .= "                                                  ,EE.cod_entidade            \n";
    $stSql .= "             ) AS vl_empenhado,                                               \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado_anulado(                  	 \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_empenhado_anulado,                                       \n";
    $stSql .= "           empenho.fn_consultar_valor_liquidado(                  	         \n";
    $stSql .= "                                                   PE.exercicio               \n";
    $stSql .= "                                                  ,EE.cod_empenho             \n";
    $stSql .= "                                                  ,EE.cod_entidade            \n";
    $stSql .= "             ) AS vl_liquidado,                                               \n";
    $stSql .= "           empenho.fn_consultar_valor_liquidado_anulado(               	     \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_liquidado_anulado,                                       \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado_pago(                     	 \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_pago,                                                    \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado_pago_anulado(             \n";
    $stSql .= "                                                           PE.exercicio       \n";
    $stSql .= "                                                          ,EE.cod_empenho     \n";
    $stSql .= "                                                          ,EE.cod_entidade    \n";
    $stSql .= "             ) AS vl_pago_anulado                                             \n";
    $stSql .= "     FROM                                                                     \n";
    $stSql .= "             empenho.empenho             AS EE                            \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.nota_liquidacao AS NL ON (                           \n";
    $stSql .= "                NL.exercicio_empenho = EE.exercicio                           \n";
    $stSql .= "          AND   NL.cod_entidade      = EE.cod_entidade                        \n";
    $stSql .= "          AND   NL.cod_empenho       = EE.cod_empenho   )                     \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.empenho_autorizacao AS EA ON (                       \n";
    $stSql .= "                EA.exercicio       = EE.exercicio                             \n";
    $stSql .= "          AND   EA.cod_entidade    = EE.cod_entidade                          \n";
    $stSql .= "          AND   EA.cod_empenho     = EE.cod_empenho   )                       \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.autorizacao_empenho AS AE ON (                       \n";
    $stSql .= "                AE.exercicio       = EA.exercicio                             \n";
    $stSql .= "          AND   AE.cod_autorizacao = EA.cod_autorizacao                       \n";
    $stSql .= "          AND   AE.cod_entidade    = EA.cod_entidade  )                       \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             empenho.autorizacao_reserva AS AR ON (                       \n";
    $stSql .= "                AR.exercicio       = AE.exercicio                             \n";
    $stSql .= "          AND   AR.cod_entidade    = AE.cod_entidade                          \n";
    $stSql .= "          AND   AR.cod_autorizacao = AE.cod_autorizacao )                     \n";
    $stSql .= "             LEFT JOIN                                                        \n";
    $stSql .= "             orcamento.reserva           AS  R ON (                       \n";
    $stSql .= "                 R.cod_reserva     = AR.cod_reserva                           \n";
    $stSql .= "          AND    R.exercicio       = AR.exercicio     ),                      \n";
    $stSql .= "             sw_cgm                         AS  C,                           \n";
    $stSql .= "             empenho.pre_empenho AS PE                                    \n";
    $stSql .= "                 LEFT OUTER JOIN empenho.restos_pre_empenho as RE ON      \n";
    $stSql .= "                     PE.exercicio = RE.exercicio AND                          \n";
    $stSql .= "                     PE.cod_pre_empenho = RE.cod_pre_empenho                  \n";
    $stSql .= "                 LEFT OUTER JOIN (                                            \n";
    $stSql .= "                     SELECT                                                   \n";
    $stSql .= "                           PED.exercicio
                                        , PED.cod_pre_empenho
                                        , D.cod_despesa
                                        , D.num_pao
                                        , D.num_orgao
                                        , D.num_unidade
                                        , D.cod_recurso
                                        , rec.cod_fonte
                                        , CD.cod_conta
                                        , CD.cod_estrutural \n";
    $stSql .= "                     FROM                                                     \n";
    $stSql .= "                           empenho.pre_empenho_despesa as PED
                                        , orcamento.despesa as D
                                          JOIN orcamento.recurso as rec
                                            ON ( rec.cod_recurso = d.cod_recurso
                                             AND rec.exercicio = d.exercicio )
                                        , orcamento.conta_despesa as CD \n";
    $stSql .= "                     WHERE                                                    \n";
    $stSql .= "                         PED.cod_despesa = D.cod_despesa
                                    and PED.exercicio = D.exercicio
                                    and PED.cod_conta = CD.cod_conta
                                    and D.exercicio = CD.exercicio \n";
    $stSql .= "                 ) as PED_D_CD ON                                             \n";
    $stSql .= "                     PE.exercicio = PED_D_CD.exercicio AND                    \n";
    $stSql .= "                     PE.cod_pre_empenho = PED_D_CD.cod_pre_empenho            \n";
    $stSql .= "     WHERE                                                                    \n";
    $stSql .= "                EE.cod_pre_empenho = PE.cod_pre_empenho                       \n";
    $stSql .= "          AND   EE.exercicio       = PE.exercicio                             \n";
    $stSql .= "          AND   EE.cod_empenho     = EE.cod_empenho                           \n";
    $stSql .= "          AND    C.numcgm          = PE.cgm_beneficiario                      \n";
    $stSql .= ") AS tabela                                                                   \n";

    return $stSql;
}


/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRestosConsultaEmpenhoCompraLicitacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY") === false)?" ORDER BY $stOrdem":$stOrdem;
    $stGroup = "
    GROUP BY tabela.cod_entidade,
             tabela.cod_empenho,
             tabela.cod_pre_empenho,
             tabela.cod_autorizacao,
             tabela.cod_reserva,
             tabela.implantado,
             tabela.exercicio,
             tabela.dt_empenho,
             tabela.nom_fornecedor,
             tabela.vl_empenhado ";
    $stSql = $this->montaRestosConsultaEmpenhoCompraLicitacao().$stCondicao.$stGroup.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRestosConsultaEmpenhoCompraLicitacao()
{
  $stSql = " SELECT tabela.cod_entidade,                                                    
                    tabela.cod_empenho,                                                     
                    tabela.cod_pre_empenho,                                                 
                    tabela.cod_autorizacao,                                                 
                    tabela.cod_reserva,                                                 
                    tabela.implantado,                                                 
                    tabela.exercicio,                                                       
                    tabela.dt_empenho,                                                      
                    tabela.nom_fornecedor,                                                  
                    tabela.vl_empenhado 
              FROM (                                                                        
                   SELECT  AE.cod_autorizacao,                                              
                           EE.cod_empenho,                                                  
                           EE.vl_saldo_anterior,                                            
                           TO_CHAR(EE.dt_vencimento,'dd/mm/yyyy') AS dt_vencimento,         
                           TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,               
                           PED_D_CD.cod_despesa,                                            
                           PE.descricao,                                                    
                           PE.exercicio,                                                    
                           PE.cod_pre_empenho,                                              
                           PE.cgm_beneficiario as credor,                                   
                           EE.cod_entidade,                                                 
                           AR.cod_reserva,                                                  
                           PED_D_CD.cod_conta,                                              
                           C.nom_cgm AS nom_fornecedor,                                     
                           R.vl_reserva,                                                    
                           PE.implantado,                                                   
                           CASE WHEN PE.implantado = true
                                THEN RE.num_orgao                                                 
                                ELSE PED_D_CD.num_orgao                                           
                           END as num_orgao,                                                
                           CASE WHEN PE.implantado = true
                                THEN RE.num_unidade                                               
                                ELSE PED_D_CD.num_unidade                                         
                           END as num_unidade,                                              
                           CASE WHEN PE.implantado = true
                                THEN RE.cod_estrutural                                            
                                ELSE PED_D_CD.cod_estrutural                                      
                           END as cod_estrutural,                                           
                           CASE WHEN PE.implantado = true
                                THEN RE.recurso                                                   
                                ELSE PED_D_CD.cod_recurso                                         
                           END as cod_recurso,                                              
                           PED_D_CD.cod_fonte,                                                   
                           PE.cod_historico,                                                
                           NL.cod_nota,                                                     
                           NL.exercicio as exercicio_liquidacao,                            
                           empenho.fn_consultar_valor_empenhado(  PE.exercicio               
                                                                 ,EE.cod_empenho             
                                                                 ,EE.cod_entidade            
                           ) AS vl_empenhado,                                               
                           empenho.fn_consultar_valor_empenhado_anulado(  PE.exercicio       
                                                                         ,EE.cod_empenho     
                                                                         ,EE.cod_entidade    
                           ) AS vl_empenhado_anulado,                                       
                           empenho.fn_consultar_valor_liquidado(  PE.exercicio               
                                                                 ,EE.cod_empenho             
                                                                 ,EE.cod_entidade            
                           ) AS vl_liquidado,                                               
                           empenho.fn_consultar_valor_liquidado_anulado( PE.exercicio       
                                                                        ,EE.cod_empenho     
                                                                        ,EE.cod_entidade    
                           ) AS vl_liquidado_anulado,                                       
                           empenho.fn_consultar_valor_empenhado_pago( PE.exercicio       
                                                                     ,EE.cod_empenho     
                                                                     ,EE.cod_entidade    
                           ) AS vl_pago,                                                    
                           empenho.fn_consultar_valor_empenhado_pago_anulado( PE.exercicio       
                                                                           ,EE.cod_empenho     
                                                                           ,EE.cod_entidade    
                           ) AS vl_pago_anulado 
                         , compra_direta.cod_modalidade AS compra_cod_modalidade
                         , compra_direta.cod_compra_direta
                         , adjudicacao.cod_modalidade AS licitacao_cod_modalidade
                         , adjudicacao.cod_licitacao                                            
                   
                   FROM empenho.empenho AS EE
                           
              LEFT JOIN empenho.nota_liquidacao AS NL
                     ON NL.exercicio_empenho = EE.exercicio                           
                    AND NL.cod_entidade      = EE.cod_entidade                        
                    AND NL.cod_empenho       = EE.cod_empenho
                    
             LEFT JOIN empenho.empenho_autorizacao AS EA
                    ON EA.exercicio       = EE.exercicio                             
                   AND EA.cod_entidade    = EE.cod_entidade                          
                   AND EA.cod_empenho     = EE.cod_empenho
                   
            LEFT JOIN empenho.autorizacao_empenho AS AE
                   ON AE.exercicio       = EA.exercicio                             
                  AND AE.cod_autorizacao = EA.cod_autorizacao                       
                  AND AE.cod_entidade    = EA.cod_entidade
                  
           LEFT JOIN empenho.autorizacao_reserva AS AR
                  ON AR.exercicio       = AE.exercicio                             
                 AND AR.cod_entidade    = AE.cod_entidade                          
                 AND AR.cod_autorizacao = AE.cod_autorizacao
              
           LEFT JOIN orcamento.reserva AS  R
                  ON R.cod_reserva = AR.cod_reserva                           
                 AND R.exercicio   = AR.exercicio
                       
          INNER JOIN empenho.pre_empenho AS PE
                  ON EE.cod_pre_empenho = PE.cod_pre_empenho                       
                 AND EE.exercicio       = PE.exercicio     
                 
          INNER JOIN sw_cgm AS  C
                  ON C.numcgm = PE.cgm_beneficiario
                           
     LEFT OUTER JOIN empenho.restos_pre_empenho as RE
                  ON PE.exercicio = RE.exercicio
                 AND PE.cod_pre_empenho = RE.cod_pre_empenho                  
     
     LEFT OUTER JOIN ( SELECT PED.exercicio
                            , PED.cod_pre_empenho
                            , D.cod_despesa
                            , D.num_pao
                            , D.num_orgao
                            , D.num_unidade
                            , D.cod_recurso
                            , rec.cod_fonte
                            , CD.cod_conta
                            , CD.cod_estrutural 
                            
                            FROM empenho.pre_empenho_despesa as PED
                         
                      INNER JOIN orcamento.despesa as D
                              ON PED.cod_despesa = D.cod_despesa
                             AND PED.exercicio   = D.exercicio
                              
                      INNER JOIN orcamento.recurso as rec
                              ON rec.cod_recurso = d.cod_recurso
                             AND rec.exercicio   = d.exercicio
                          
                      INNER JOIN orcamento.conta_despesa as CD 
                              ON CD.cod_conta = PED.cod_conta
                             AND CD.exercicio = D.exercicio
                     ) as PED_D_CD
                  ON PE.exercicio       = PED_D_CD.exercicio
                 AND PE.cod_pre_empenho = PED_D_CD.cod_pre_empenho
    
            LEFT JOIN empenho.item_pre_empenho
                   ON item_pre_empenho.cod_pre_empenho = pe.cod_pre_empenho
                  AND item_pre_empenho.exercicio       = pe.exercicio
                  
            LEFT JOIN empenho.item_pre_empenho_julgamento
                   ON item_pre_empenho_julgamento.cod_pre_empenho  = item_pre_empenho.cod_pre_empenho   
                  AND item_pre_empenho_julgamento.exercicio        = item_pre_empenho.exercicio
                  AND item_pre_empenho_julgamento.num_item         = item_pre_empenho.num_item
            
            LEFT JOIN compras.julgamento_item
                   ON julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio_julgamento
                  AND julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao 
                  AND julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item
                  AND julgamento_item.lote           = item_pre_empenho_julgamento.lote
                  AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
            
            LEFT JOIN compras.cotacao_item
                   ON cotacao_item.exercicio   = julgamento_item.exercicio
                  AND cotacao_item.cod_cotacao = julgamento_item.cod_cotacao
                  AND cotacao_item.lote        = julgamento_item.lote
                  AND cotacao_item.cod_item    = julgamento_item.cod_item
            
            LEFT JOIN compras.cotacao
                   ON cotacao.cod_cotacao = cotacao_item.cod_cotacao
                  AND cotacao.exercicio   = cotacao_item.exercicio
            
            LEFT JOIN compras.mapa_cotacao
                   ON mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                  AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio
            
            LEFT JOIN compras.mapa
                   ON mapa.cod_mapa  = mapa_cotacao.cod_mapa
                  AND mapa.exercicio = mapa_cotacao.exercicio_mapa
            
            LEFT JOIN compras.compra_direta
                   ON compra_direta.cod_mapa       = mapa.cod_mapa
                  AND compra_direta.exercicio_mapa = mapa.exercicio
            
            LEFT JOIN licitacao.adjudicacao
                   ON adjudicacao.exercicio_cotacao = cotacao_item.exercicio 
                  AND adjudicacao.cod_cotacao       = cotacao_item.cod_cotacao
                  AND adjudicacao.lote              = cotacao_item.lote
                  AND adjudicacao.cod_item          = cotacao_item.cod_item 
         
        ) AS tabela \n";
    
    return $stSql;   
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRestosPorNota(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRestosAPagarPorNota().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRestosAPagarPorNota()
{
    $stSql  = " SELECT                                                                          \n";
    $stSql .= "      tabela.*                                                                   \n";
    $stSql .= " FROM (                                                                          \n";
    $stSql .= "      SELECT                                                                     \n";
    $stSql .= "            tabela.*,                                                            \n";
    $stSql .= "            empenho.fn_consultar_valor_empenhado(                            \n";
    $stSql .= "                                                    tabela.exercicio             \n";
    $stSql .= "                                                   ,tabela.cod_empenho           \n";
    $stSql .= "                                                   ,tabela.cod_entidade          \n";
    $stSql .= "              ) AS vl_empenhado,                                                 \n";
    $stSql .= "            empenho.fn_consultar_valor_empenhado_anulado(                    \n";
    $stSql .= "                                                            tabela.exercicio     \n";
    $stSql .= "                                                           ,tabela.cod_empenho   \n";
    $stSql .= "                                                           ,tabela.cod_entidade  \n";
    $stSql .= "              ) AS vl_empenhado_anulado,                                         \n";
    $stSql .= "            empenho.fn_consultar_valor_liquidado_nota(                       \n";
    $stSql .= "                                                    tabela.exercicio_nota        \n";
    $stSql .= "                                                   ,tabela.cod_empenho           \n";
    $stSql .= "                                                   ,tabela.cod_entidade          \n";
    $stSql .= "                                                   ,tabela.cod_nota              \n";
    $stSql .= "              ) AS vl_liquidado,                                                 \n";
    $stSql .= "            empenho.fn_consultar_valor_liquidado_anulado_nota(               \n";
    $stSql .= "                                                            tabela.exercicio_nota\n";
    $stSql .= "                                                           ,tabela.cod_empenho   \n";
    $stSql .= "                                                           ,tabela.cod_entidade  \n";
    $stSql .= "                                                           ,tabela.cod_nota      \n";
    $stSql .= "              ) AS vl_liquidado_anulado,                                         \n";
    $stSql .= "            empenho.fn_consultar_valor_pagamento_nota(                       \n";
    $stSql .= "                                                         tabela.exercicio_nota   \n";
    $stSql .= "                                                        ,tabela.cod_nota         \n";
    $stSql .= "                                                        ,tabela.cod_entidade     \n";
    $stSql .= "              ) AS vl_pago,                                                      \n";
    $stSql .= "            empenho.fn_consultar_valor_pagamento_anulado_nota(               \n";
    $stSql .= "                                                          tabela.exercicio_nota  \n";
    $stSql .= "                                                         ,tabela.cod_nota        \n";
    $stSql .= "                                                        ,tabela.cod_entidade     \n";
    $stSql .= "              ) AS vl_pago_anulado                                               \n";
    $stSql .= "      FROM (                                                                     \n";
    $stSql .= "           SELECT                                                                \n";
    $stSql .= "                 NL.cod_nota,                                                    \n";
    $stSql .= "                 NL.exercicio AS exercicio_nota,                                 \n";
    $stSql .= "                 to_char(NL.dt_liquidacao,'dd/mm/yyyy') as dt_liquidacao,        \n";
    $stSql .= "                 to_char(NL.dt_vencimento,'dd/mm/yyyy') as dt_vencimento_liquidacao,        \n";
    $stSql .= "                 AE.cod_autorizacao,                                             \n";
    $stSql .= "                 EE.cod_empenho,                                                 \n";
    $stSql .= "                 TO_CHAR(EE.dt_vencimento,'dd/mm/yyyy') AS dt_vencimento,        \n";
    $stSql .= "                 TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,              \n";
    $stSql .= "                 PE.descricao,                                                   \n";
    $stSql .= "                 PE.exercicio,                                                   \n";
    $stSql .= "                 PE.cod_pre_empenho,                                             \n";
    $stSql .= "                 PE.implantado,                                                  \n";
    $stSql .= "                 EE.cod_entidade,                                                \n";
    $stSql .= "                 AR.cod_reserva,                                                 \n";
    $stSql .= "                 C.nom_cgm AS nom_fornecedor,                                    \n";
    $stSql .= "                 C.numcgm AS num_fornecedor,                                    \n";
    $stSql .= "                 R.vl_reserva                                                    \n";
    $stSql .= "           FROM                                                                  \n";
    $stSql .= "                  empenho.empenho             AS EE                          \n";
    $stSql .= "                  LEFT JOIN                                                      \n";
    $stSql .= "                  empenho.empenho_autorizacao AS EA ON (                     \n";
    $stSql .= "                     EA.exercicio       = EE.exercicio                           \n";
    $stSql .= "               AND   EA.cod_entidade    = EE.cod_entidade                        \n";
    $stSql .= "               AND   EA.cod_empenho     = EE.cod_empenho   )                     \n";
    $stSql .= "                  LEFT JOIN                                                      \n";
    $stSql .= "                  empenho.autorizacao_empenho AS AE ON (                     \n";
    $stSql .= "                     AE.exercicio       = EA.exercicio                           \n";
    $stSql .= "               AND   AE.cod_autorizacao = EA.cod_autorizacao                     \n";
    $stSql .= "               AND   AE.cod_entidade    = EA.cod_entidade  )                     \n";
    $stSql .= "                  LEFT JOIN                                                      \n";
    $stSql .= "                  empenho.autorizacao_reserva AS AR ON (                     \n";
    $stSql .= "                     AR.exercicio       = AE.exercicio                           \n";
    $stSql .= "               AND   AR.cod_entidade    = AE.cod_entidade                        \n";
    $stSql .= "               AND   AR.cod_autorizacao = AE.cod_autorizacao )                   \n";
    $stSql .= "                  LEFT JOIN                                                      \n";
    $stSql .= "                  orcamento.reserva           AS  R ON (                     \n";
    $stSql .= "                      R.cod_reserva     = AR.cod_reserva                         \n";
    $stSql .= "               AND    R.exercicio       = AR.exercicio     ),                    \n";
    $stSql .= "                  sw_cgm                         AS  C,                         \n";
    $stSql .= "                  empenho.pre_empenho         AS PE,                         \n";
    $stSql .= "                  (                                                              \n";
    $stSql .= "                   SELECT                                                        \n";
    $stSql .= "                       NL.*                                                      \n";
    $stSql .= "                   FROM                                                          \n";
    $stSql .= "                       empenho.nota_liquidacao AS NL                         \n";
    $stSql .= "                  ) AS NL                                                        \n";
    $stSql .= "          WHERE                                                                  \n";
    $stSql .= "                     EE.cod_pre_empenho = PE.cod_pre_empenho                     \n";
    $stSql .= "               AND   EE.exercicio       = PE.exercicio                           \n";
    $stSql .= "               AND   EE.cod_empenho     = EE.cod_empenho                         \n";
    $stSql .= "               AND    C.numcgm          = PE.cgm_beneficiario                    \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "               AND   EE.cod_empenho     = NL.cod_empenho                         \n";
    $stSql .= "               AND   EE.exercicio       = NL.exercicio_empenho                   \n";
    $stSql .= "               AND   EE.cod_entidade    = NL.cod_entidade                        \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "      ) AS tabela                                                                              \n";
    $stSql .= ") AS tabela                                                                                    \n";
    $stSql .= "WHERE  tabela.cod_nota IS NOT NULL                                               \n";
    if ( $this->getDado('stAcao')=='anular' ) {
        $stSql .= "             AND     tabela.vl_liquidado > tabela.vl_liquidado_anulado       \n";
        $stSql .= "             AND     (tabela.vl_liquidado - tabela.vl_liquidado_anulado) > (tabela.vl_pago - tabela.vl_pago_anulado) \n";
    }

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaLiquidacaoAnuladaPorItem(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaLiquidacaoAnuladaPorItem();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLiquidacaoAnuladaPorItem()
{
     $stSql  = " SELECT * FROM empenho.fn_consultar_valor_liquidado_anulado_por_liquidacao('".$this->getDado("exercicio")."', ".$this->getDado("cod_empenho").", ".$this->getDado("cod_entidade").", ".$this->getDado("cod_liquidacao").", ".$this->getDado("cod_item_liquidacao").")   \n";
     $stSql .= "      AS retorno (                                   \n";
     $stSql .= "                    cod_nota       integer           \n";
     $stSql .= "                   ,dt_liquidacao  text              \n";
     $stSql .= "                   ,vl_total       numeric(14,2)     \n";
     $stSql .= "                   ,vl_anulado     numeric(14,2)     \n";
     $stSql .= "                   ,timestamp      text              \n";
     $stSql .= "                 ) ;                                 \n";

     return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoPorNota(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoPorNota().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaRelacionamentoPorNota()
{
    $stSql  = " SELECT                                                                          \n";
    $stSql .= "      tabela.*                                                                   \n";
    $stSql .= " FROM (                                                                          \n";
    $stSql .= "      SELECT                                                                     \n";
    $stSql .= "            tabela.*,                                                            \n";
    $stSql .= "            empenho.fn_consultar_valor_empenhado(                            \n";
    $stSql .= "                                                    tabela.exercicio             \n";
    $stSql .= "                                                   ,tabela.cod_empenho           \n";
    $stSql .= "                                                   ,tabela.cod_entidade          \n";
    $stSql .= "              ) AS vl_empenhado,                                                 \n";
    $stSql .= "            empenho.fn_consultar_valor_empenhado_anulado(                    \n";
    $stSql .= "                                                            tabela.exercicio     \n";
    $stSql .= "                                                           ,tabela.cod_empenho   \n";
    $stSql .= "                                                           ,tabela.cod_entidade  \n";
    $stSql .= "              ) AS vl_empenhado_anulado,                                         \n";
    $stSql .= "            empenho.fn_consultar_valor_liquidado_nota(                       \n";
    $stSql .= "                                                    tabela.exercicio_nota        \n";
    $stSql .= "                                                   ,tabela.cod_empenho           \n";
    $stSql .= "                                                   ,tabela.cod_entidade          \n";
    $stSql .= "                                                   ,tabela.cod_nota              \n";
    $stSql .= "              ) AS vl_liquidado,                                                 \n";
    $stSql .= "            empenho.fn_consultar_valor_liquidado_anulado_nota(               \n";
    $stSql .= "                                                            tabela.exercicio_nota \n";
    $stSql .= "                                                           ,tabela.cod_empenho   \n";
    $stSql .= "                                                           ,tabela.cod_entidade  \n";
    $stSql .= "                                                           ,tabela.cod_nota      \n";
    $stSql .= "              ) AS vl_liquidado_anulado,                                         \n";
    $stSql .= "            empenho.fn_consultar_valor_pagamento_nota(                       \n";
    $stSql .= "                                                         tabela.exercicio_nota   \n";
    $stSql .= "                                                        ,tabela.cod_nota         \n";
    $stSql .= "                                                        ,tabela.cod_entidade     \n";
    $stSql .= "              ) AS vl_pago,                                                      \n";
    $stSql .= "            empenho.fn_consultar_valor_pagamento_anulado_nota(               \n";
    $stSql .= "                                                          tabela.exercicio_nota  \n";
    $stSql .= "                                                         ,tabela.cod_nota        \n";
    $stSql .= "                                                        ,tabela.cod_entidade     \n";
    $stSql .= "              ) AS vl_pago_anulado                                               \n";
    $stSql .= "      FROM (                                                                     \n";
    $stSql .= "           SELECT                                                                \n";
    $stSql .= "                 NL.cod_nota,                                                    \n";
    $stSql .= "                 NL.exercicio AS exercicio_nota,                                 \n";
    $stSql .= "                 to_char(NL.dt_liquidacao,'dd/mm/yyyy') as dt_liquidacao,        \n";
    $stSql .= "                 to_char(NL.dt_vencimento,'dd/mm/yyyy') as dt_vencimento_liquidacao,        \n";
    $stSql .= "                 AE.cod_autorizacao,                                             \n";
    $stSql .= "                 EE.cod_empenho,                                                 \n";
    $stSql .= "                 TO_CHAR(EE.dt_vencimento,'dd/mm/yyyy') AS dt_vencimento,        \n";
    $stSql .= "                 TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,              \n";
    $stSql .= "                 PD.cod_despesa,                                                 \n";
    $stSql .= "                 PE.descricao,                                                   \n";
    $stSql .= "                 PE.exercicio,                                                   \n";
    $stSql .= "                 PE.cod_pre_empenho,                                             \n";
    $stSql .= "                 EE.cod_entidade,                                                \n";
    $stSql .= "                 AR.cod_reserva,                                                 \n";
    $stSql .= "                 PD.cod_conta,                                                   \n";
    $stSql .= "                 C.nom_cgm AS nom_fornecedor,                                    \n";
    $stSql .= "                 C.numcgm AS num_fornecedor,                                    \n";
    $stSql .= "                 R.vl_reserva                                                    \n";
    if ((strtolower(SistemaLegado::pegaConfiguracao( 'seta_tipo_documento_liq_tceam',30, Sessao::getExercicio()))=='true')) {
        $stSql .= "            ,tceamdoc.cod_tipo                                               \n";
    }
    $stSql .= "           FROM                                                                  \n";
    $stSql .= "                  empenho.empenho             AS EE                          \n";
    $stSql .= "                  LEFT JOIN                                                      \n";
    $stSql .= "                  empenho.empenho_autorizacao AS EA ON (                     \n";
    $stSql .= "                     EA.exercicio       = EE.exercicio                           \n";
    $stSql .= "               AND   EA.cod_entidade    = EE.cod_entidade                        \n";
    $stSql .= "               AND   EA.cod_empenho     = EE.cod_empenho   )                     \n";
    $stSql .= "                  LEFT JOIN                                                      \n";
    $stSql .= "                  empenho.autorizacao_empenho AS AE ON (                     \n";
    $stSql .= "                     AE.exercicio       = EA.exercicio                           \n";
    $stSql .= "               AND   AE.cod_autorizacao = EA.cod_autorizacao                     \n";
    $stSql .= "               AND   AE.cod_entidade    = EA.cod_entidade  )                     \n";
    $stSql .= "                  LEFT JOIN                                                      \n";
    $stSql .= "                  empenho.autorizacao_reserva AS AR ON (                     \n";
    $stSql .= "                     AR.exercicio       = AE.exercicio                           \n";
    $stSql .= "               AND   AR.cod_entidade    = AE.cod_entidade                        \n";
    $stSql .= "               AND   AR.cod_autorizacao = AE.cod_autorizacao )                   \n";
    $stSql .= "                  LEFT JOIN                                                      \n";
    $stSql .= "                  orcamento.reserva           AS  R ON (                     \n";
    $stSql .= "                      R.cod_reserva     = AR.cod_reserva                         \n";
    $stSql .= "               AND    R.exercicio       = AR.exercicio     )                     \n";
    $stSql .= "                  LEFT JOIN                                                      \n";
    $stSql .= "                  empenho.nota_liquidacao AS NL ON    (                          \n";
    $stSql .= "                     EE.cod_empenho  = NL.cod_empenho                            \n";
    $stSql .= "               AND   EE.exercicio    = NL.exercicio_empenho                      \n";
    $stSql .= "               AND   EE.cod_entidade = NL.cod_entidade)                          \n";
    if ((strtolower(SistemaLegado::pegaConfiguracao( 'seta_tipo_documento_liq_tceam',30, Sessao::getExercicio()))=='true')) {
        $stSql .= " LEFT JOIN tceam.documento AS tceamdoc ON      (                             \n";
        $stSql .= "           NL.exercicio    = tceamdoc.exercicio                             \n";
        $stSql .= "       AND NL.cod_entidade = tceamdoc.cod_entidade                          \n";
        $stSql .= "       AND NL.cod_nota     = tceamdoc.cod_nota )                            \n";
    }
    $stSql .= "                 ,sw_cgm                      AS  C,                         \n";
    $stSql .= "                  empenho.pre_empenho         AS PE,                         \n";
    $stSql .= "                  empenho.pre_empenho_despesa AS PD,                         \n";
    $stSql .= "                  orcamento.despesa           AS OD                          \n";
    $stSql .= "          WHERE                                                                  \n";
    $stSql .= "                     EE.cod_pre_empenho = PE.cod_pre_empenho                     \n";
    $stSql .= "               AND   EE.exercicio       = PE.exercicio                           \n";
    $stSql .= "               AND   EE.cod_empenho     = EE.cod_empenho                         \n";
    $stSql .= "               AND    C.numcgm          = PE.cgm_beneficiario                    \n";
    $stSql .= "               AND   PD.cod_pre_empenho = PE.cod_pre_empenho                     \n";
    $stSql .= "               AND   PD.exercicio       = PE.exercicio                           \n";
    $stSql .= "               AND   OD.exercicio       = PD.exercicio                           \n";
    $stSql .= "               AND   OD.cod_despesa     = PD.cod_despesa                         \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "               AND   EE.cod_empenho     = NL.cod_empenho                         \n";
    $stSql .= "               AND   EE.exercicio       = NL.exercicio_empenho                   \n";
    $stSql .= "               AND   EE.cod_entidade    = NL.cod_entidade                        \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "               AND   OD.num_unidade::varchar||OD.num_orgao::varchar IN (                           \n";
    $stSql .= "                               SELECT                                            \n";
    $stSql .= "                                     num_unidade::varchar||num_orgao::varchar                      \n";
    $stSql .= "                               FROM                                              \n";
    $stSql .= "                                   empenho.permissao_autorizacao             \n";
    $stSql .= "                               WHERE numcgm    = ".$this->getDado("numcgm")."    \n";
    $stSql .= "                               AND   exercicio = '".$this->getDado("exercicio")."'\n";
    $stSql .= "                                                      )                          \n";
    $stSql .= "      ) AS tabela                                                                \n";
    $stSql .= ") AS tabela                                                                      \n";
    $stSql .= "WHERE  tabela.cod_nota IS NOT NULL                                               \n";
    if ( $this->getDado('stAcao')=='anular' ) {
        $stSql .= "             AND     (tabela.vl_liquidado - tabela.vl_liquidado_anulado) > (tabela.vl_pago - tabela.vl_pago_anulado) \n";

    }

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao Flag de transação
    * @return Object  Objeto Erro
*/
function recuperaLiquidacaoAnular(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaLiquidacaoAnular().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLiquidacaoAnular($stCampos = '*')
{
    $stSql  = "select                                                            \n";
    $stSql .= "  ". $stCampos ."                                                 \n";
    $stSql .= "from (                                                            \n";
    $stSql .= "    select                                                        \n";
    $stSql .= "        nl.exercicio,                                             \n";
    $stSql .= "        nl.cod_entidade,                                          \n";
    $stSql .= "        nl.cod_nota,                                              \n";
    $stSql .= "        pl.cod_ordem ,                                            \n";
    $stSql .= "        pl.vl_pagamento ,                                         \n";
    $stSql .= "        oa.vl_anulado,                                            \n";
    $stSql .= "        t.total_liquidacao,                                       \n";
    $stSql .= "        CASE WHEN                                                 \n";
    $stSql .= "            (t.total_liquidacao - oa.vl_anulado) <> 0             \n";
    $stSql .= "        THEN                                                      \n";
    $stSql .= "            (t.total_liquidacao - oa.vl_anulado)                  \n";
    $stSql .= "        ELSE                                                      \n";
    $stSql .= "            0.00                                                  \n";
    $stSql .= "        END as a_anular                                           \n";
    $stSql .= "    from                                                          \n";
    $stSql .= "        empenho.nota_liquidacao      as nl                        \n";
    $stSql .= "    left join                                                     \n";
    $stSql .= "        empenho.pagamento_liquidacao as pl                        \n";
    $stSql .= "        on ( pl.cod_nota     = nl.cod_nota                        \n";
    $stSql .= "        AND  pl.exercicio_liquidacao = nl.exercicio               \n";
    $stSql .= "        AND  pl.cod_entidade = nl.cod_entidade )                  \n";
    $stSql .= "    left join                                                     \n";
    $stSql .= "        empenho.ordem_pagamento as op                             \n";
    $stSql .= "        on ( op.cod_ordem    = pl.cod_ordem                       \n";
    $stSql .= "        AND  op.exercicio    = pl.exercicio                       \n";
    $stSql .= "        AND  op.cod_entidade = pl.cod_entidade )                  \n";
    $stSql .= "    left join                                                     \n";
    $stSql .= "        empenho.ordem_pagamento_anulada as oa                     \n";
    $stSql .= "        on ( oa.cod_ordem    = op.cod_ordem                       \n";
    $stSql .= "        AND  oa.exercicio    = op.exercicio                       \n";
    $stSql .= "        AND  oa.cod_entidade = op.cod_entidade )                  \n";

    /* CONSULTA VALORES DAS LIQUIDAÇÕES DA OP */
    $stSql .= "    join empenho.fn_consulta_valores_op ( op.exercicio, op.cod_entidade, op.cod_ordem ) \n";
    $stSql .= "         as valores_op ( cod_nota                integer,   \n";
    $stSql .= "                         exercicio_liquidacao    varchar,   \n";
    $stSql .= "                         vl_pagamento            numeric, \n";
    $stSql .= "                         vl_pagamento_anulado    numeric, \n";
    $stSql .= "                         vl_pago                 numeric, \n";
    $stSql .= "                         vl_pago_anulado         numeric, \n";
    $stSql .= "                         vl_a_anular             numeric  \n";
    $stSql .= "                       ) \n";
    $stSql .= "         on (     valores_op.cod_nota = pl.cod_nota \n";
    $stSql .= "              and valores_op.exercicio_liquidacao = pl.exercicio_liquidacao \n";
    $stSql .= "            )\n";

    $stSql .= "    join (                                                        \n";
    $stSql .= "           select                                                 \n";
    $stSql .= "                sum(vl_pagamento) as total_liquidacao,            \n";
    $stSql .= "                nl.cod_nota,                                      \n";
    $stSql .= "                nl.exercicio,                                     \n";
    $stSql .= "                nl.cod_entidade                                   \n";
    $stSql .= "           from                                                   \n";
    $stSql .= "                empenho.nota_liquidacao as nl                     \n";
    $stSql .= "           left join                                              \n";
    $stSql .= "               empenho.pagamento_liquidacao as pl                 \n";
    $stSql .= "                    on ( pl.cod_nota     = nl.cod_nota            \n";
    $stSql .= "                    AND  pl.exercicio    = nl.exercicio           \n";
    $stSql .= "                    AND  pl.cod_entidade = nl.cod_entidade )      \n";
    $stSql .= "           group by                                               \n";
    $stSql .= "                nl.cod_nota, nl.exercicio, nl.cod_entidade        \n";
    $stSql .= "                                                                  \n";
    $stSql .= "          ) as t                                                  \n";
    $stSql .= "          on (                                                    \n";
    $stSql .= "               t.cod_nota     = nl.cod_nota                       \n";
    $stSql .= "          AND  t.exercicio    = nl.exercicio                      \n";
    $stSql .= "          AND  t.cod_entidade = nl.cod_entidade )                 \n";
    $stSql .= "    order by exercicio,cod_entidade,cod_nota,cod_ordem            \n";
    $stSql .= "     ) as liquid                                                  \n";
    $stSql .= "where liquid.a_anular > 0                                         \n";

    return $stSql;

}

function montaRecuperaValorNotaItem()
{
    $stSql  = "SELECT                                                                                       \n";
    $stSql .= "      EE.cod_empenho                                                                         \n";
    $stSql .= "     ,PE.cod_pre_empenho                                                                     \n";
    $stSql .= "     ,IE.num_item                                                                            \n";
    $stSql .= "     ,IE.nom_item                                                                            \n";
    $stSql .= "     ,IE.vl_total                                                                            \n";
    $stSql .= "     ,empenho.fn_consultar_valor_empenhado_anulado_item(                                 \n";
    $stSql .= "                                                              EE.exercicio                   \n";
    $stSql .= "                                                             ,EE.cod_empenho                 \n";
    $stSql .= "                                                             ,EE.cod_entidade                \n";
    $stSql .= "                                                             ,IE.num_item                    \n";
    $stSql .= "                                                           ) as vl_item_anulado              \n";
    $stSql .= "     ,empenho.fn_consultar_valor_liquidado_nota_item (                                        \n";
    $stSql .= "                                                      EE.exercicio                           \n";
    $stSql .= "                                                     ,EE.cod_empenho                         \n";
    $stSql .= "                                                     ,EE.cod_entidade                        \n";
    $stSql .= "                                                     ,IE.num_item                            \n";
    $stSql .= "                                                             ,NL.cod_nota                    \n";
    $stSql .= "                                                    ) as vl_item_liquidado                   \n";
    $stSql .= "     ,empenho.fn_consultar_valor_liquidado_anulado_nota_item (                                \n";
    $stSql .= "                                                              EE.exercicio                   \n";
    $stSql .= "                                                             ,EE.cod_empenho                 \n";
    $stSql .= "                                                             ,EE.cod_entidade                \n";
    $stSql .= "                                                             ,NL.cod_nota                    \n";
    $stSql .= "                                                             ,IE.num_item                    \n";
    $stSql .= "                                                            ) as vl_item_liquidado_anulado   \n";
    $stSql .= "FROM                                                                                         \n";
    $stSql .= "   empenho.pre_empenho      AS PE                                                        \n";
    $stSql .= "  ,empenho.item_pre_empenho AS IE                                                        \n";
    $stSql .= "  ,empenho.empenho          AS EE                                                        \n";
    $stSql .= "  ,empenho.nota_liquidacao  AS NL                                                        \n";
    $stSql .= "WHERE                                                                                        \n";
    $stSql .= "     PE.exercicio        = EE.exercicio                                                      \n";
    $stSql .= "AND  PE.cod_pre_empenho  = EE.cod_pre_empenho                                                \n";
    $stSql .= "AND  EE.cod_empenho      = NL.cod_empenho                                                    \n";
    $stSql .= "AND  EE.cod_entidade     = NL.cod_entidade                                                   \n";
    $stSql .= "AND  EE.exercicio        = NL.exercicio_empenho                                              \n";
    $stSql .= "AND  IE.cod_pre_empenho  = PE.cod_pre_empenho                                                \n";
    $stSql .= "AND  IE.exercicio        = PE.exercicio                                                      \n";

    return $stSql;
}

function montaRecuperaValorItem()
{
    $stSql  = "SELECT                                                                                       \n";
    $stSql .= "      EE.cod_empenho                                                                         \n";
    $stSql .= "     ,PE.cod_pre_empenho                                                                     \n";
    $stSql .= "     ,IE.num_item                                                                            \n";
    $stSql .= "     ,IE.nom_item                                                                            \n";
    $stSql .= "     ,IE.vl_total                                                                            \n";
    $stSql .= "     ,empenho.fn_consultar_valor_empenhado_anulado_item(                                 \n";
    $stSql .= "                                                              EE.exercicio                   \n";
    $stSql .= "                                                             ,EE.cod_empenho                 \n";
    $stSql .= "                                                             ,EE.cod_entidade                \n";
    $stSql .= "                                                             ,IE.num_item                    \n";
    $stSql .= "                                                           ) as vl_item_anulado              \n";
    $stSql .= "     ,empenho.fn_consultar_valor_liquidado_item (                                        \n";
    $stSql .= "                                                      EE.exercicio                           \n";
    $stSql .= "                                                     ,EE.cod_empenho                         \n";
    $stSql .= "                                                     ,EE.cod_entidade                        \n";
    $stSql .= "                                                     ,IE.num_item                            \n";
    $stSql .= "                                                    ) as vl_item_liquidado                   \n";
    $stSql .= "     ,empenho.fn_consultar_valor_liquidado_anulado_item (                                \n";
    $stSql .= "                                                              EE.exercicio                   \n";
    $stSql .= "                                                             ,EE.cod_empenho                 \n";
    $stSql .= "                                                             ,EE.cod_entidade                \n";
    $stSql .= "                                                             ,IE.num_item                    \n";
    $stSql .= "                                                            ) as vl_item_liquidado_anulado   \n";
    $stSql .= "FROM                                                                                         \n";
    $stSql .= "   empenho.pre_empenho      AS PE                                                        \n";
    $stSql .= "  ,empenho.item_pre_empenho AS IE                                                        \n";
    $stSql .= "  ,empenho.empenho          AS EE                                                        \n";
    $stSql .= "WHERE                                                                                        \n";
    $stSql .= "     PE.exercicio       = EE.exercicio                                                       \n";
    $stSql .= "AND  PE.cod_pre_empenho = EE.cod_pre_empenho                                                 \n";
    $stSql .= "AND  IE.cod_pre_empenho = PE.cod_pre_empenho                                                 \n";
    $stSql .= "AND  IE.exercicio       = PE.exercicio                                                       \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaValorItem(&$rsRecordSet, $stCondicao = "",$stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaValorItem().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelatorioEmpenho(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRelatorioEmpenho().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRelatorioEmpenho()
{
    $stSql  = " SELECT                                                                                                                  \n";
    $stSql .= "     publico.fn_mascara_dinamica( ( SELECT valor FROM administracao.configuracao WHERE parametro = 'masc_despesa' AND exercicio = '".$this->getDado('exercicio')."' ), tabela.dotacao ) as dotacao_formatada,  \n";
    $stSql .= "     tabela.*                                                                                                            \n";
    $stSql .= " FROM (                                                                                                                  \n";
    $stSql .= "     SELECT                                                                                                              \n";
    $stSql .= "         e.cod_empenho,                                                                                                  \n";
    $stSql .= "         e.cod_entidade,                                                                                                 \n";
    $stSql .= "         e.cod_categoria,                                                                                                 \n";
    $stSql .= "         ece.descricao as categoria,                                                                                                 \n";
    $stSql .= "         cgm_entidade.nom_cgm as nom_entidade,                                                                           \n";
    $stSql .= "         te.nom_tipo as nom_tipo_pre_empenho,                                                                            \n";
    $stSql .= "         cgm.numcgm,                                                                                                     \n";
    $stSql .= "         cgm.nom_cgm,                                                                                                    \n";
    $stSql .= "         pe.descricao,                                                                                                   \n";
    $stSql .= "         CASE WHEN pf.numcgm IS NOT NULL THEN                                                                            \n";
    $stSql .= "             pf.cpf                                                                                                      \n";
    $stSql .= "         ELSE                                                                                                            \n";
    $stSql .= "             pj.cnpj                                                                                                     \n";
    $stSql .= "         END as cpf_cnpj,                                                                                                \n";
    $stSql .= "         CASE WHEN pf.numcgm IS NOT NULL THEN                                                                            \n";
    $stSql .= "             cgm.fone_residencial                                                                                        \n";
    $stSql .= "         ELSE                                                                                                            \n";
    $stSql .= "             CASE WHEN cgm.fone_comercial != '' THEN                                                                     \n";
    $stSql .= "                 cgm.fone_comercial                                                                                      \n";
    $stSql .= "             ELSE cgm.fone_residencial                                                                                   \n";
    $stSql .= "             END                                                                                                         \n";
    $stSql .= "         END as fone,                                                                                                    \n";
    $stSql .= "         cgm.tipo_logradouro||' '||cgm.logradouro||' '||cgm.numero||' '||cgm.complemento as endereco,                    \n";
    $stSql .= "         mu.nom_municipio,                                                                                               \n";
    $stSql .= "         uf.nom_uf,                                                                                                      \n";
    $stSql .= "         uf.sigla_uf,                                                                                                    \n";
    $stSql .= "         ea.cod_autorizacao,                                                                                             \n";
    $stSql .= "         CASE WHEN (hdf.historico is not null)                                                                           \n";
    $stSql .= "              then hdf.historico                                                                                         \n";
    $stSql .= "              WHEN (he.nom_historico is not null)                                                                        \n";
    $stSql .= "                  then he.nom_historico                                                                                  \n";
    $stSql .= "         END  AS historico,                                                                                              \n";
    $stSql .= "         coalesce(e.vl_saldo_anterior,0.00) as saldo_anterior,                                                           \n";
    $stSql .= "         to_char(e.dt_empenho,'dd/mm/yyyy') as dt_empenho,                                                               \n";
    $stSql .= "         to_char(e.dt_vencimento,'dd/mm/yyyy') as dt_vencimento,                                                         \n";
    $stSql .= "                                                                                                                         \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                             \n";
    $stSql .= "             rpe.cod_recurso                                                                                             \n";
    $stSql .= "         ELSE                                                                                                            \n";
    $stSql .= "             ped_d_cd.cod_recurso                                                                                        \n";
    $stSql .= "         END as cod_recurso,                                                                                             \n";
    $stSql .= "         ped_d_cd.cod_fonte,                                                                                          \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                             \n";
    $stSql .= "             rpe.nom_recurso                                                                                             \n";
    $stSql .= "         ELSE                                                                                                            \n";
    $stSql .= "             ped_d_cd.nom_recurso                                                                                        \n";
    $stSql .= "         END as nom_recurso,                                                                                             \n";
    $stSql .= "                                                                                                                         \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                             \n";
    $stSql .= "             rpe.num_pao                                                                                                 \n";
    $stSql .= "         ELSE                                                                                                            \n";
    $stSql .= "             ped_d_cd.num_pao                                                                                            \n";
    $stSql .= "         END as num_pao,                                                                                                 \n";

    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                             \n";
    $stSql .= "             rpe.num_pao                                                                                                 \n";
    $stSql .= "         ELSE                                                                                                            \n";
    $stSql .= "             ped_d_cd.num_acao                                                                                            \n";
    $stSql .= "         END as num_acao,                                                                                                 \n";

    $stSql .= "                                                                                                                         \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                             \n";
    $stSql .= "             rpe.nom_pao                                                                                                 \n";
    $stSql .= "         ELSE                                                                                                            \n";
    $stSql .= "             ped_d_cd.nom_pao                                                                                            \n";
    $stSql .= "         END as nom_pao,                                                                                                 \n";
    $stSql .= "                                                                                                                         \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                             \n";
    $stSql .= "             rpe.num_nom_orgao                                                                                           \n";
    $stSql .= "         ELSE                                                                                                            \n";
    $stSql .= "             ped_d_cd.num_nom_orgao                                                                                      \n";
    $stSql .= "         END as num_nom_orgao,                                                                                           \n";
    $stSql .= "                                                                                                                         \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                             \n";
    $stSql .= "             rpe.num_nom_unidade                                                                                         \n";
    $stSql .= "         ELSE                                                                                                            \n";
    $stSql .= "             ped_d_cd.num_nom_unidade                                                                                    \n";
    $stSql .= "         END as num_nom_unidade,                                                                                         \n";
    $stSql .= "                                                                                                                         \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                             \n";
    $stSql .= "             0                                                                                                           \n";
    $stSql .= "         ELSE                                                                                                            \n";
    $stSql .= "             ped_d_cd.dotacao_reduzida                                                                                   \n";
    $stSql .= "         END as dotacao_reduzida,                                                                                        \n";
    $stSql .= "                                                                                                                         \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                             \n";
    $stSql .= "             rpe.dotacao                                                                                                 \n";
    $stSql .= "         ELSE                                                                                                            \n";
    $stSql .= "             ped_d_cd.dotacao                                                                                            \n";
    $stSql .= "         END as dotacao,                                                                                                 \n";
    $stSql .= "                                                                                                                         \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                             \n";
    $stSql .= "             ''                                                                                                          \n";
    $stSql .= "         ELSE                                                                                                            \n";
    $stSql .= "             ped_d_cd.nom_conta                                                                                          \n";
    $stSql .= "         END as nom_conta,                                                                                               \n";
    $stSql .= "                                                                                                                         \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                             \n";
    $stSql .= "             0.00                                                                                                        \n";
    $stSql .= "         ELSE                                                                                                            \n";
    $stSql .= "             ped_d_cd.valor_orcado                                                                                       \n";
    $stSql .= "         END as valor_orcado                                                                                             \n";
    $stSql .= "     FROM                                                                                                                \n";
    $stSql .= "         empenho.empenho            as e                                                                                 \n";
    $stSql .= "         JOIN empenho.categoria_empenho as ece ON ( e.cod_categoria = ece.cod_categoria )                                \n";
    $stSql .= "     LEFT JOIN (                                                                                                         \n";
    $stSql .= "     SELECT  df.historico, e.cod_empenho, e.cod_entidade, e.exercicio                                                    \n";
    $stSql .= "     FROM                                                                                                                \n";
    $stSql .= "             empenho.empenho             as e,                                                                           \n";
    $stSql .= "             empenho.pre_empenho_despesa as ped,                                                                         \n";
    $stSql .= "             empenho.despesas_fixas      as df,                                                                          \n";
    $stSql .= "             empenho.item_empenho_despesas_fixas as iedf                                                                 \n";
    $stSql .= "     WHERE                                                                                                               \n";
    $stSql .= "             e.exercicio    = ped.exercicio                                                                              \n";
    $stSql .= "             AND e.cod_entidade = df.cod_entidade                                                                        \n";
    $stSql .= "             AND e.cod_pre_empenho = ped.cod_pre_empenho                                                                 \n";
    $stSql .= "             AND ped.cod_despesa     = df.cod_despesa                                                                    \n";
    $stSql .= "             AND ped.exercicio       = df.exercicio                                                                      \n";
    $stSql .= "             AND iedf.cod_pre_empenho = ped.cod_pre_empenho                                                              \n";
    $stSql .= "             AND iedf.cod_despesa = df.cod_despesa                                                                       \n";
    $stSql .= "             AND iedf.cod_despesa_fixa = df.cod_despesa_fixa                                                             \n";
    $stSql .= "             AND iedf.cod_entidade = df.cod_entidade                                                                     \n";
    $stSql .= "             AND e.cod_entidade = ".$this->getDado("cod_entidade")."                                                                                      \n";
    $stSql .= "             AND e.cod_empenho  = ".$this->getDado("cod_empenho")."                                                                                    \n";
    $stSql .= "             AND e.exercicio = '".$this->getDado('exercicio')."'                                                                                       \n";
    $stSql .= "     ) AS hdf ON (hdf.exercicio = e.exercicio                                                                            \n";
    $stSql .= "                AND hdf.cod_entidade = e.cod_entidade                                                                    \n";
    $stSql .= "                AND hdf.cod_empenho = e.cod_empenho )                                                                    \n";
    $stSql .= "     LEFT JOIN (                                                                                                         \n";
    $stSql .= "     SELECT  h.nom_historico, e.cod_empenho, e.cod_entidade, e.exercicio                                                 \n";
    $stSql .= "     FROM                                                                                                                \n";
    $stSql .= "             empenho.historico as h,                                                                                     \n";
    $stSql .= "             empenho.pre_empenho as pe,                                                                                  \n";
    $stSql .= "             empenho.empenho as e                                                                                        \n";
    $stSql .= "     WHERE                                                                                                               \n";
    $stSql .= "             e.exercicio    = pe.exercicio                                                                               \n";
    $stSql .= "             AND e.cod_pre_empenho = pe.cod_pre_empenho                                                                  \n";
    $stSql .= "             AND pe.cod_historico   = h.cod_historico                                                                    \n";
    $stSql .= "             AND pe.exercicio       = h.exercicio                                                                        \n";
    $stSql .= "             AND e.exercicio = '".$this->getDado('exercicio')."'                                                                                      \n";
    $stSql .= "             AND e.cod_empenho = ".$this->getDado("cod_empenho")."                                                                                     \n";
    $stSql .= "             AND e.cod_entidade = ".$this->getDado("cod_entidade")."                                                                                      \n";
    $stSql .= "     )                                                                                                                   \n";
    $stSql .= "     AS he ON (he.exercicio = e.exercicio                                                                                \n";
    $stSql .= "               AND he.cod_entidade = e.cod_entidade                                                                      \n";
    $stSql .= "               AND he.cod_empenho = e.cod_empenho )                                                                      \n";

    $stSql .= "             LEFT JOIN empenho.empenho_autorizacao AS ea ON (                                                            \n";
    $stSql .= "                     ea.exercicio       = e.exercicio                                                                    \n";
    $stSql .= "                 AND ea.cod_entidade    = e.cod_entidade                                                                 \n";
    $stSql .= "                 AND ea.cod_empenho     = e.cod_empenho   )                                                              \n";
    $stSql .= "         ,empenho.pre_empenho        as pe                                                                               \n";
    $stSql .= "             LEFT OUTER JOIN (                                                                                           \n";
    $stSql .= "                SELECT                                                                                                   \n";
    $stSql .= "						r.nom_recurso,																						\n";
    $stSql .= "                     rpe.exercicio,                                                                                      \n";
    $stSql .= "                     rpe.cod_pre_empenho,                                                                                \n";
    $stSql .= "                     r.cod_recurso,                                                                                      \n";
    $stSql .= "                     pao.num_pao,                                                                                      \n";
    $stSql .= "                     pao.nom_pao,                                                                                      \n";
    $stSql .= "                     rpe.num_orgao   ||' - '||o.nom_orgao   as num_nom_orgao,                                            \n";
    $stSql .= "                     rpe.num_unidade ||' - '||o.nom_unidade as num_nom_unidade,                                          \n";
    $stSql .= "                     rpe.num_orgao::varchar ||'.'|| rpe.num_unidade::varchar ||'.'|| rpe.cod_funcao::varchar ||'.'|| rpe.cod_subfuncao::varchar ||'.'|| rpe.cod_programa::varchar ||'.'|| rpe.num_pao::varchar ||'.'|| rpad(rpe.cod_estrutural,14,0::varchar) as dotacao \n";
    $stSql .= "                 FROM                                                                                                    \n";
    $stSql .= "                     empenho.restos_pre_empenho  as rpe                                                                  \n";
    $stSql .= "                         LEFT OUTER JOIN orcamento.recurso as r ON (                                                     \n";
    $stSql .= "                                 rpe.recurso         = r.cod_recurso                                                     \n";
    $stSql .= "                             AND rpe.exercicio       = r.exercicio)                                                      \n";
    $stSql .= "                         LEFT OUTER JOIN orcamento.pao as pao ON (                                                     \n";
    $stSql .= "                                 rpe.num_pao         = pao.num_pao                                                         \n";
    $stSql .= "                             AND rpe.exercicio       = pao.exercicio)                                                      \n";
    $stSql .= "                         LEFT OUTER JOIN (                                                                               \n";
    $stSql .= "                             SELECT                                                                                      \n";
    $stSql .= "                                 oo.nom_orgao,                                                                           \n";
    $stSql .= "                                 ou.nom_unidade,                                                                         \n";
    $stSql .= "                                 ou.num_orgao,                                                                           \n";
    $stSql .= "                                 ou.num_unidade,                                                                         \n";
    $stSql .= "                                 ou.exercicio                                                                            \n";
    $stSql .= "                             FROM                                                                                        \n";
    $stSql .= "                                 orcamento.unidade  as ou,                                                               \n";
    $stSql .= "                                 orcamento.orgao    as oo                                                               \n";
    $stSql .= "                             WHERE                                                                                       \n";
    $stSql .= "                                     ou.num_orgao     = oo.num_orgao                                                     \n";
    $stSql .= "                                 AND ou.exercicio     = oo.exercicio                                                     \n";
    $stSql .= "                         ) as o ON (                                                                                     \n";
    $stSql .= "                                 rpe.num_orgao       = o.num_orgao                                                       \n";
    $stSql .= "                             AND rpe.num_unidade     = o.num_unidade                                                     \n";
    $stSql .= "                             AND rpe.exercicio       = o.exercicio                                                       \n";
    $stSql .= "                         )                                                                                               \n";
    $stSql .= "             ) as rpe ON pe.exercicio = rpe.exercicio AND pe.cod_pre_empenho = rpe.cod_pre_empenho                       \n";
    $stSql .= "             LEFT OUTER JOIN (                                                                                           \n";
    $stSql .= "                 SELECT                                                                                                  \n";
    $stSql .= "                     ped.exercicio,                                                                                      \n";
    $stSql .= "                     ped.cod_pre_empenho,                                                                                \n";
    $stSql .= "                     r.cod_recurso,                                                                                      \n";
    $stSql .= "                     r.cod_fonte,                                                                                      \n";
    $stSql .= "                     r.nom_recurso,                                                                                      \n";
    $stSql .= "                     pao.num_pao,                                                                                        \n";
    $stSql .= "                     pao.nom_pao,                                                                                        \n";
    $stSql .= "                     oo.num_orgao::varchar   ||' - '|| oo.nom_orgao::varchar as num_nom_orgao,                                             \n";
    $stSql .= "                     ou.num_unidade::varchar ||' - '|| ou.nom_unidade as num_nom_unidade,                                         \n";
    $stSql .= "                     d.cod_despesa as dotacao_reduzida,                                                                  \n";
    $stSql .= "                     d.num_orgao::varchar ||'.'|| d.num_unidade::varchar ||'.'|| d.cod_funcao::varchar ||'.'|| d.cod_subfuncao::varchar ||'.'|| ppa.programa.num_programa::varchar ||'.'|| ppa.acao.num_acao::varchar ||'.'|| replace(cd.cod_estrutural,'.','') as dotacao, \n";
    $stSql .= "                     cd.descricao as nom_conta,                                                                          \n";
    $stSql .= "                     coalesce(d.vl_original,0.00) as valor_orcado ,                                                      \n";
    $stSql .= "                     ppa.acao.num_acao                                                                                   \n";
    $stSql .= "                 FROM                                                                                                    \n";
    $stSql .= "                     empenho.pre_empenho_despesa as ped,                                                                 \n";
    $stSql .= "                     orcamento.despesa           as d                                                                   \n";

    $stSql .= "                  JOIN orcamento.programa_ppa_programa                                                                   \n";
    $stSql .= "                ON programa_ppa_programa.cod_programa = d.cod_programa                                             \n";
    $stSql .= "               AND programa_ppa_programa.exercicio   = d.exercicio                                                 \n";
    $stSql .= "              JOIN ppa.programa                                                                                          \n";
    $stSql .= "                ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa                                    \n";
    $stSql .= "              JOIN orcamento.pao_ppa_acao                                                                                \n";
    $stSql .= "              ON pao_ppa_acao.num_pao = d.num_pao                                                                  \n";
    $stSql .= "               AND pao_ppa_acao.exercicio = d.exercicio                                                            \n";
    $stSql .= "              JOIN ppa.acao                                                                                              \n";
    $stSql .= "                ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao                                                             \n";

    $stSql .= "                    , orcamento.recurso as r,                                                                             \n";
    $stSql .= "                     orcamento.pao               as pao,                                                                  \n";
    $stSql .= "                     orcamento.unidade           as ou,                                                                 \n";
    $stSql .= "                     orcamento.orgao             as oo,                                                                  \n";
    $stSql .= "                     orcamento.conta_despesa     as cd                                                                   \n";
    $stSql .= "                 WHERE                                                                                                   \n";
    $stSql .= "                     --Orcamento/Despesa                                                                                 \n";
    $stSql .= "                         ped.cod_despesa     = d.cod_despesa                                                             \n";
    $stSql .= "                     AND ped.exercicio       = d.exercicio                                                               \n";
    $stSql .= "                     --Órgão                                                                                             \n";
    $stSql .= "                     AND d.num_orgao         = ou.num_orgao                                                              \n";
    $stSql .= "                     AND d.num_unidade       = ou.num_unidade                                                            \n";
    $stSql .= "                     AND d.exercicio         = ou.exercicio                                                              \n";
    $stSql .= "                     AND ou.num_orgao        = oo.num_orgao                                                              \n";
    $stSql .= "                     AND ou.exercicio        = oo.exercicio                                                              \n";
    $stSql .= "                     --Unidade                                                                                           \n";
    $stSql .= "                     --Conta Despesa                                                                                     \n";
    $stSql .= "                     AND ped.cod_conta       = cd.cod_conta                                                              \n";
    $stSql .= "                     AND ped.exercicio       = cd.exercicio                                                              \n";
    $stSql .= "                     --Recurso                                                                                           \n";
    $stSql .= "                     AND d.cod_recurso       = r.cod_recurso                                                             \n";
    $stSql .= "                     AND d.exercicio         = r.exercicio                                                               \n";
    $stSql .= "                     --PAO                                                                                               \n";
    $stSql .= "                     AND d.num_pao           = pao.num_pao                                                               \n";
    $stSql .= "                     AND d.exercicio         = pao.exercicio                                                             \n";
    $stSql .= "             ) as ped_d_cd ON pe.exercicio = ped_d_cd.exercicio AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho        \n";
    $stSql .= "         ,empenho.tipo_empenho       as te                                                                               \n";
    $stSql .= "       --  ,empenho.despesas_fixas     as h                                                                                \n";
    $stSql .= "         ,orcamento.entidade         as oe                                                                               \n";
    $stSql .= "             LEFT OUTER JOIN sw_cgm as cgm_entidade ON ( cgm_entidade.numcgm = oe.numcgm )                               \n";
    $stSql .= "         ,sw_cgm       as cgm                                                                                            \n";
    $stSql .= "             LEFT OUTER JOIN sw_cgm_pessoa_fisica      as pf ON (cgm.numcgm = pf.numcgm)                                 \n";
    $stSql .= "             LEFT OUTER JOIN sw_cgm_pessoa_juridica    as pj ON (cgm.numcgm = pj.numcgm)                                 \n";
    $stSql .= "         ,sw_municipio                as mu                                                                              \n";
    $stSql .= "         ,sw_uf                       as uf                                                                              \n";
    $stSql .= "     WHERE                                                                                                               \n";
    $stSql .= "             e.cod_pre_empenho   = pe.cod_pre_empenho                                                                    \n";
    $stSql .= "         AND e.exercicio         = pe.exercicio                                                                          \n";
    $stSql .= "                                                                                                                         \n";
    $stSql .= "                                                                                                                         \n";
    $stSql .= "         --Tipo Empenho                                                                                                  \n";
    $stSql .= "         AND pe.cod_tipo         = te.cod_tipo                                                                           \n";
    $stSql .= "                                                                                                                         \n";
    $stSql .= "         --Entidade                                                                                                      \n";
    $stSql .= "         AND oe.cod_entidade     = e.cod_entidade                                                                        \n";
    $stSql .= "         AND oe.exercicio        = e.exercicio                                                                           \n";
    $stSql .= "                                                                                                                         \n";
    $stSql .= "         --CGM                                                                                                           \n";
    $stSql .= "         AND pe.cgm_beneficiario = cgm.numcgm                                                                            \n";
    $stSql .= "                                                                                                                         \n";
    $stSql .= "         --Municipio                                                                                                     \n";
    $stSql .= "         AND cgm.cod_municipio   = mu.cod_municipio                                                                      \n";
    $stSql .= "         AND cgm.cod_uf          = mu.cod_uf                                                                             \n";
    $stSql .= "         --Uf                                                                                                            \n";
    $stSql .= "         AND mu.cod_uf           = uf.cod_uf                                                                             \n";
    $stSql .= "         AND e.exercicio        = '".$this->getDado('exercicio')."'                                                      \n";
    $stSql .= "         --FILTROS                                                                                                       \n";
    $stSql .= "         ".$this->getDado('stFiltro')."                                                                                  \n";
    $stSql .= " ) as tabela                                                                                                             \n";

    return $stSql;
}
/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelatorioEmpenhoItens(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRelatorioEmpenhoItens().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRelatorioEmpenhoItens()
{
    $stSql="SELECT                                                              
                 it.vl_total                    as valor_total                  
                ,(it.vl_total/it.quantidade)    as valor_unitario               
                ,it.num_item                                                    
                ,it.quantidade                                                  
                ,CASE WHEN it.cod_marca IS NOT NULL
                    THEN it.nom_item||' (Marca: '||marca.cod_marca||' - '||marca.descricao||')'
                    ELSE it.nom_item
                END AS nom_item                                                    
                
                ,it.complemento                                                 
                ,it.sigla_unidade as simbolo                                    
                ,it.cod_item                                                    
            FROM empenho.item_pre_empenho   as it
            LEFT JOIN almoxarifado.marca
                ON marca.cod_marca = it.cod_marca
    ";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelatorioRazaoEmpenho(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRelatorioRazaoEmpenho().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRelatorioRazaoEmpenho()
{
    $stSql  = " SELECT                                                                                                                                     \n";
    $stSql .= "       tabela.*,                                                                                                                            \n";
    $stSql .= "       (vl_empenhado - vl_empenhado_anulado - vl_pago) as saldo_atual                                                                       \n";
    $stSql .= " FROM (                                                                                                                                     \n";
    $stSql .= "      SELECT                                                                                                                                \n";
    $stSql .= "         EE.cod_entidade,                                                                                                                   \n";
    $stSql .= "         EE.exercicio,                                                                                                                      \n";
    $stSql .= "         cgm_entidade.nom_cgm as nom_entidade,                                                                                              \n";
    $stSql .= "         EE.cod_empenho,                                                                                                                    \n";
    $stSql .= "         TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,                                                                                 \n";
    $stSql .= "         TO_CHAR(EE.dt_vencimento,'dd/mm/yyyy') AS dt_vencimento,                                                                           \n";
    $stSql .= "         HI.cod_historico, HI.nom_historico,                                                                                                \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                                                \n";
    $stSql .= "             restos.cod_recurso                                                                                                             \n";
    $stSql .= "         ELSE                                                                                                                               \n";
    $stSql .= "             ped_d_cd.cod_recurso                                                                                                           \n";
    $stSql .= "         END as cod_recurso,                                                                                                                \n";
    $stSql .= "         ped_d_cd.nom_recurso,                                                                                                              \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                                                \n";
    $stSql .= "             restos.num_orgao                                                                                                               \n";
    $stSql .= "         ELSE                                                                                                                               \n";
    $stSql .= "             ped_d_cd.num_orgao                                                                                                             \n";
    $stSql .= "         END as num_orgao,                                                                                                                  \n";
    $stSql .= "         ped_d_cd.nom_orgao,                                                                                                                \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                                                \n";
    $stSql .= "             restos.num_unidade                                                                                                             \n";
    $stSql .= "         ELSE                                                                                                                               \n";
    $stSql .= "             ped_d_cd.num_unidade                                                                                                           \n";
    $stSql .= "         END as num_unidade,                                                                                                                \n";
    $stSql .= "         ped_d_cd.nom_unidade,                                                                                                              \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                                                \n";
    $stSql .= "             restos.num_pao                                                                                                                 \n";
    $stSql .= "         ELSE                                                                                                                               \n";
    $stSql .= "             ped_d_cd.num_pao                                                                                                               \n";
    $stSql .= "         END as num_pao,                                                                                                                    \n";
    $stSql .= "         CASE WHEN pe.implantado = true THEN                                                                                                \n";
    $stSql .= "             restos.num_pao                                                                                                                 \n";
    $stSql .= "         ELSE                                                                                                                               \n";
    $stSql .= "             ped_d_cd.num_acao                                                                                                               \n";
    $stSql .= "         END as num_acao,                                                                                                                    \n";
    $stSql .= "         ped_d_cd.nom_pao,                                                                                                                  \n";
    $stSql .= "         ped_d_cd.cod_despesa,                                                                                                              \n";
    $stSql .= "         CASE                                                                                                                               \n";
    $stSql .= "             WHEN ped_d_cd.cod_estrutural <> ped_d_cd.cod_estrutural_dot THEN ped_d_cd.cod_estrutural                                       \n";
    $stSql .= "             ELSE ''                                                                                                                        \n";
    $stSql .= "         END AS cod_estrutural_desdobramento,                                                                                               \n";
    $stSql .= "         CASE                                                                                                                               \n";
    $stSql .= "             WHEN ped_d_cd.cod_estrutural <> ped_d_cd.cod_estrutural_dot THEN ped_d_cd.descricao                                            \n";
    $stSql .= "             ELSE ''                                                                                                                        \n";
    $stSql .= "         END AS descricao_desdobramento,                                                                                                    \n";
    $stSql .= "         ped_d_cd.cod_estrutural_dot,                                                                                                       \n";
    $stSql .= "         ped_d_cd.descricao_dot,                                                                                                            \n";
    $stSql .= "         PE.descricao,                                                                                                                      \n";
    $stSql .= "         PE.cgm_beneficiario as num_cgm,                                                                                                               \n";
    $stSql .= "         CG.nom_cgm AS nom_fornecedor,                                                                                                      \n";
    $stSql .= "         substr(CG.cep,1,2)||'.'||substr(CG.cep,3,3)||'-'||substr(CG.cep,6,3) as cep,                                                       \n";
    $stSql .= "         CASE                                                                                                                               \n";
    $stSql .= "             WHEN PF.numcgm IS NOT NULL THEN CG.fone_residencial                                                                            \n";
    $stSql .= "             ELSE CASE                                                                                                                      \n";
    $stSql .= "                 WHEN CG.fone_comercial != '' THEN  CG.fone_comercial                                                                       \n";
    $stSql .= "                 ELSE CG.fone_residencial                                                                                                   \n";
    $stSql .= "             END                                                                                                                            \n";
    $stSql .= "         END AS fone,                                                                                                                       \n";
    $stSql .= "         CG.tipo_logradouro||' '||CG.logradouro||' '||CG.numero||' '||CG.complemento as endereco,                                           \n";
    $stSql .= "         MU.nom_municipio||'/'||UF.sigla_uf as municipio_uf,                                                                                \n";
    $stSql .= "         EA.cod_autorizacao,                                                                                                                \n";
    $stSql .= "         empenho.fn_consultar_valor_empenhado(PE.exercicio,EE.cod_empenho,EE.cod_entidade) AS vl_empenhado,                                 \n";
    $stSql .= "         empenho.fn_consultar_valor_empenhado_anulado(PE.exercicio,EE.cod_empenho,EE.cod_entidade) AS vl_empenhado_anulado,                 \n";
    $stSql .= "         (empenho.fn_consultar_valor_liquidado(PE.exercicio,EE.cod_empenho,EE.cod_entidade) -                                               \n";
    $stSql .= "         empenho.fn_consultar_valor_liquidado_anulado(PE.exercicio,EE.cod_empenho,EE.cod_entidade) )AS vl_liquidado,                        \n";
    $stSql .= "         (empenho.fn_consultar_valor_empenhado_pago(PE.exercicio,EE.cod_empenho,EE.cod_entidade) -                                          \n";
    $stSql .= "         empenho.fn_consultar_valor_empenhado_pago_anulado(PE.exercicio,EE.cod_empenho,EE.cod_entidade) ) AS vl_pago                        \n";
    $stSql .= "      FROM                                                                                                                                  \n";
    $stSql .= "         empenho.empenho             AS EE                                                                                                  \n";
    $stSql .= "         LEFT JOIN                                                                                                                          \n";
    $stSql .= "         empenho.empenho_autorizacao AS EA ON (                                                                                             \n";
    $stSql .= "               EA.exercicio       = EE.exercicio                                                                                            \n";
    $stSql .= "         AND   EA.cod_entidade    = EE.cod_entidade                                                                                         \n";
    $stSql .= "         AND   EA.cod_empenho     = EE.cod_empenho   )                                                                                      \n";
    $stSql .= "         LEFT JOIN                                                                                                                          \n";
    $stSql .= "         empenho.autorizacao_empenho AS AE ON (                                                                                             \n";
    $stSql .= "               AE.exercicio       = EA.exercicio                                                                                            \n";
    $stSql .= "         AND   AE.cod_autorizacao = EA.cod_autorizacao                                                                                      \n";
    $stSql .= "         AND   AE.cod_entidade    = EA.cod_entidade  )                                                                                      \n";
    $stSql .= "         LEFT JOIN                                                                                                                          \n";
    $stSql .= "         empenho.autorizacao_reserva AS AR ON (                                                                                             \n";
    $stSql .= "               AR.exercicio       = AE.exercicio                                                                                            \n";
    $stSql .= "         AND   AR.cod_entidade    = AE.cod_entidade                                                                                         \n";
    $stSql .= "         AND   AR.cod_autorizacao = AE.cod_autorizacao )                                                                                    \n";
    $stSql .= "         LEFT JOIN                                                                                                                          \n";
    $stSql .= "         orcamento.reserva           AS  R ON (                                                                                             \n";
    $stSql .= "                R.cod_reserva     = AR.cod_reserva                                                                                          \n";
    $stSql .= "         AND    R.exercicio       = AR.exercicio     ),                                                                                     \n";
    $stSql .= "         orcamento.entidade          AS OE                                                                                                  \n";
    $stSql .= "         LEFT JOIN sw_cgm as cgm_entidade ON (                                                                                              \n";
    $stSql .= "                cgm_entidade.numcgm = oe.numcgm ),                                                                                          \n";
    $stSql .= "         sw_cgm                      AS  CG                                                                                                 \n";
    $stSql .= "         LEFT JOIN                                                                                                                          \n";
    $stSql .= "         sw_cgm_pessoa_fisica        AS PF ON (                                                                                             \n";
    $stSql .= "               cg.numcgm = pf.numcgm)                                                                                                       \n";
    $stSql .= "         LEFT JOIN                                                                                                                          \n";
    $stSql .= "         sw_cgm_pessoa_juridica      AS PJ ON (                                                                                             \n";
    $stSql .= "               cg.numcgm = pj.numcgm),                                                                                                      \n";
    $stSql .= "         sw_municipio                AS MU,                                                                                                 \n";
    $stSql .= "         sw_uf                       AS UF,                                                                                                 \n";
    $stSql .= "         empenho.pre_empenho         AS PE                                                                                                  \n";
    $stSql .= "             LEFT JOIN (                                                                                                                    \n";
    $stSql .= "                 SELECT                                                                                                                     \n";
    $stSql .= "                     ped.exercicio,                                                                                                         \n";
    $stSql .= "                     ped.cod_pre_empenho,                                                                                                   \n";
    $stSql .= "                     r.cod_recurso,                                                                                                         \n";
    $stSql .= "                     r.nom_recurso,                                                                                                         \n";
    $stSql .= "                     oo.num_orgao, oo.nom_orgao,                                                                                            \n";
    $stSql .= "                     ou.num_unidade, ou.nom_unidade,                                                                                        \n";
    $stSql .= "                     pao.num_pao, pao.nom_pao,                                                                                              \n";
    $stSql .= "                     d.cod_despesa,                                                                                                         \n";
    $stSql .= "                     cd.cod_estrutural,                                                                                                     \n";
    $stSql .= "                     cd.descricao,                                                                                                          \n";
    $stSql .= "                     cd_dot.cod_estrutural as cod_estrutural_dot,                                                                           \n";
    $stSql .= "                     cd_dot.descricao as descricao_dot,                                                                                     \n";
    $stSql .= "                     ppa.acao.num_acao AS num_acao                                                                                          \n";
    $stSql .= "                 FROM                                                                                                                       \n";
    $stSql .= "                     empenho.pre_empenho_despesa as ped,                                                                                    \n";
    $stSql .= "                     orcamento.despesa           as d,                                                                                      \n";
    $stSql .= "                     orcamento.recurso           as r,                                                                                      \n";
    $stSql .= "                     orcamento.unidade           as ou,                                                                                     \n";
    $stSql .= "                     orcamento.orgao             as oo,                                                                                     \n";
    $stSql .= "                     orcamento.pao               as pao                                                                                    \n";
    $stSql .= "                JOIN orcamento.pao_ppa_acao                                                                                                 \n";
    $stSql .= "                  ON pao_ppa_acao.num_pao = pao.num_pao                                                                                     \n";
    $stSql .= "                 AND pao_ppa_acao.exercicio = pao.exercicio                                                                                 \n";
    $stSql .= "                JOIN ppa.acao                                                                                                               \n";
    $stSql .= "                  ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao                                                                              \n";
    $stSql .= "                     ,orcamento.conta_despesa     as cd_dot,                                                                                 \n";
    $stSql .= "                     orcamento.conta_despesa     as cd                                                                                      \n";
    $stSql .= "                 WHERE                                                                                                                      \n";
    $stSql .= "                     --Orcamento/Despesa                                                                                                    \n";
    $stSql .= "                         ped.cod_despesa     = d.cod_despesa                                                                                \n";
    $stSql .= "                     AND ped.exercicio       = d.exercicio                                                                                  \n";
    $stSql .= "                     --Órgão
                                    AND d.exercicio         = oo.exercicio
                                    AND d.num_orgao         = oo.num_orgao
                                    --Unidade
                                    AND d.exercicio        = ou.exercicio
                                    AND d.num_orgao        = ou.num_orgao
                                    AND d.num_unidade      = ou.num_unidade";
    $stSql .= "                     --Conta Despesa Dotação                                                                                                \n";
    $stSql .= "                     AND d.cod_conta         = cd_dot.cod_conta                                                                             \n";
    $stSql .= "                     AND d.exercicio         = cd_dot.exercicio                                                                             \n";
    $stSql .= "                     --Conta Despesa                                                                                                        \n";
    $stSql .= "                     AND ped.cod_conta       = cd.cod_conta                                                                                 \n";
    $stSql .= "                     AND ped.exercicio       = cd.exercicio                                                                                 \n";
    $stSql .= "                     --Recurso                                                                                                              \n";
    $stSql .= "                     AND d.cod_recurso       = r.cod_recurso                                                                                \n";
    $stSql .= "                     AND d.exercicio         = r.exercicio                                                                                  \n";
    $stSql .= "                     --Pao                                                                                                                  \n";
    $stSql .= "                     AND d.num_pao           = pao.num_pao                                                                                  \n";
    $stSql .= "                     AND d.exercicio         = pao.exercicio                                                                                \n";
    $stSql .= "             ) as ped_d_cd ON pe.exercicio = ped_d_cd.exercicio AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho                           \n";
    $stSql .= "             LEFT JOIN (                                                                                                                    \n";
    $stSql .= "                 SELECT                                                                                                                     \n";
    $stSql .= "                    r.num_orgao,                                                                                                            \n";
    $stSql .= "                    r.num_unidade,                                                                                                          \n";
    $stSql .= "                    r.num_pao,                                                                                                              \n";
    $stSql .= "                    r.recurso as cod_recurso,                                                                                               \n";
    $stSql .= "                    r.exercicio,                                                                                                            \n";
    $stSql .= "                    r.cod_pre_empenho                                                                                                       \n";
    $stSql .= "                 FROM                                                                                                                       \n";
    $stSql .= "                    empenho.restos_pre_empenho as r                                                                                         \n";
    $stSql .= "              ) as restos on pe.exercicio = restos.exercicio AND pe.cod_pre_empenho = restos.cod_pre_empenho,                               \n";
    $stSql .= "             empenho.historico as HI                                                                                                        \n";
    $stSql .= "      WHERE  EE.cod_pre_empenho = PE.cod_pre_empenho                                                                                        \n";
    $stSql .= "      AND    EE.exercicio       = PE.exercicio                                                                                              \n";
    $stSql .= "                                                                                                                                            \n";
    $stSql .= "      AND    PE.cod_historico   = HI.cod_historico                                                                                          \n";
    $stSql .= "      AND    PE.exercicio       = HI.exercicio                                                                                              \n";
    $stSql .= "                                                                                                                                            \n";
    $stSql .= "      AND    OE.cod_entidade    = EE.cod_entidade                                                                                           \n";
    $stSql .= "      AND    OE.exercicio       = EE.exercicio                                                                                              \n";
    $stSql .= "                                                                                                                                            \n";
    $stSql .= "      AND    CG.numcgm          = PE.cgm_beneficiario                                                                                       \n";
    $stSql .= "                                                                                                                                            \n";
    $stSql .= "      AND    EE.cod_empenho     =  ".$this->getDado('inCodEmpenho')."                                                                       \n";
    $stSql .= "      AND    EE.exercicio       = '".$this->getDado('inExercicio')."'                                                                       \n";
    $stSql .= "      AND    EE.cod_entidade    =  ".$this->getDado('inCodEntidade')."                                                                      \n";
    $stSql .= "                                                                                                                                            \n";
    $stSql .= "      AND    CG.cod_municipio   = MU.cod_municipio                                                                                          \n";
    $stSql .= "      AND    CG.cod_uf          = MU.cod_uf                                                                                                 \n";
    $stSql .= "                                                                                                                                            \n";
    $stSql .= "      AND    MU.cod_uf          = UF.cod_uf                                                                                                 \n";
    $stSql .= " ) AS tabela                                                                                                                                \n";
    $stSql .= " ;                                                                                                                                          \n";
return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelatorioRazaoEmpenhoLancamentos(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRelatorioRazaoEmpenhoLancamentos().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRelatorioRazaoEmpenhoLancamentos()
{
    $stSql  = "SELECT                                                                  \n";
    $stSql .= "     to_char(CLO.dt_lote,'dd/mm/yyyy') as data,                         \n";
    $stSql .= "     CHC.nom_historico as historico,                                    \n";
    $stSql .= "     CASE WHEN CHC.complemento = true THEN CL.complemento               \n";
    $stSql .= "          ELSE ''                                                       \n";
    $stSql .= "     END AS complemento,                                                \n";
    $stSql .= "     abs(coalesce(CVL.vl_lancamento,0.00)) as valor,                    \n";
    $stSql .= "     contabilidade.fn_recupera_conta_lancamento(CVL.exercicio,CVL.cod_entidade,CVL.cod_lote,CVL.tipo,CVL.sequencia,'D') as debito, \n";
    $stSql .= "     contabilidade.fn_recupera_conta_lancamento(CVL.exercicio,CVL.cod_entidade,CVL.cod_lote,CVL.tipo,CVL.sequencia,'C') as credito \n";
    $stSql .= "FROM                                                                    \n";
    $stSql .= "    (                                                                   \n";
    $stSql .= "    SELECT                                                              \n";
    $stSql .= "        CE.exercicio,                                                   \n";
    $stSql .= "        CE.exercicio_empenho,                                           \n";
    $stSql .= "        CE.cod_entidade,                                                \n";
    $stSql .= "        CE.cod_empenho,                                                 \n";
    $stSql .= "        CE.cod_lote,                                                    \n";
    $stSql .= "        CE.tipo,                                                        \n";
    $stSql .= "        CE.sequencia                                                    \n";
    $stSql .= "    FROM                                                                \n";
    $stSql .= "        contabilidade.empenhamento  AS CE                               \n";
    $stSql .= "    WHERE                                                               \n";
    $stSql .= "            CE.cod_empenho    =  ".$this->getDado('inCodEmpenho')."     \n";
    $stSql .= "        AND CE.cod_entidade   =  ".$this->getDado('inCodEntidade')."    \n";
    $stSql .= "        AND CE.exercicio_empenho = '".$this->getDado('inExercicio')."'     \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    UNION                                                               \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    SELECT                                                              \n";
    $stSql .= "        CL.exercicio,                                                   \n";
    $stSql .= "        EE.exercicio as exercicio_empenho,                              \n";
    $stSql .= "        CL.cod_entidade,                                                \n";
    $stSql .= "        ENL.cod_empenho,                                                \n";
    $stSql .= "        CL.cod_lote,                                                    \n";
    $stSql .= "        CL.tipo,                                                        \n";
    $stSql .= "        CL.sequencia                                                    \n";
    $stSql .= "    FROM                                                                \n";
    $stSql .= "        empenho.empenho            AS EE,                               \n";
    $stSql .= "        empenho.nota_liquidacao    AS ENL,                              \n";
    $stSql .= "        contabilidade.liquidacao   AS CL                                \n";
    $stSql .= "    WHERE                                                               \n";
    $stSql .= "            EE.exercicio        = ENL.exercicio_empenho                 \n";
    $stSql .= "        AND EE.cod_entidade     = ENL.cod_entidade                      \n";
    $stSql .= "        AND EE.cod_empenho      = ENL.cod_empenho                       \n";
    $stSql .= "                                                                        \n";
    $stSql .= "        AND ENL.exercicio       = CL.exercicio_liquidacao               \n";
    $stSql .= "        AND ENL.cod_entidade    = CL.cod_entidade                       \n";
    $stSql .= "        AND ENL.cod_nota        = CL.cod_nota                           \n";
    $stSql .= "                                                                        \n";
    $stSql .= "        AND EE.cod_empenho    =  ".$this->getDado('inCodEmpenho')."     \n";
    $stSql .= "        AND EE.cod_entidade   =  ".$this->getDado('inCodEntidade')."    \n";
    $stSql .= "        AND EE.exercicio      = '".$this->getDado('inExercicio')."'     \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    UNION                                                               \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    SELECT                                                              \n";
    $stSql .= "        CP.exercicio,                                                   \n";
    $stSql .= "        EE.exercicio as exercicio_empenho,                              \n";
    $stSql .= "        CP.cod_entidade,                                                \n";
    $stSql .= "        ENL.cod_empenho,                                                \n";
    $stSql .= "        CP.cod_lote,                                                    \n";
    $stSql .= "        CP.tipo,                                                        \n";
    $stSql .= "        CP.sequencia                                                    \n";
    $stSql .= "    FROM                                                                \n";
    $stSql .= "        empenho.empenho              AS EE,                             \n";
    $stSql .= "        empenho.nota_liquidacao      AS ENL,                            \n";
    $stSql .= "        empenho.nota_liquidacao_paga AS ENLP,                           \n";
    $stSql .= "        contabilidade.pagamento      AS CP                              \n";
    $stSql .= "    WHERE                                                               \n";
    $stSql .= "            EE.exercicio      = ENL.exercicio_empenho                   \n";
    $stSql .= "        AND EE.cod_entidade   = ENL.cod_entidade                        \n";
    $stSql .= "        AND EE.cod_empenho    = ENL.cod_empenho                         \n";
    $stSql .= "                                                                        \n";
    $stSql .= "        AND ENL.exercicio     = ENLP.exercicio                          \n";
    $stSql .= "        AND ENL.cod_entidade  = ENLP.cod_entidade                       \n";
    $stSql .= "        AND ENL.cod_nota      = ENLP.cod_nota                           \n";
    $stSql .= "                                                                        \n";
    $stSql .= "        AND ENLP.exercicio    = CP.exercicio_liquidacao                 \n";
    $stSql .= "        AND ENLP.cod_entidade = CP.cod_entidade                         \n";
    $stSql .= "        AND ENLP.cod_nota     = CP.cod_nota                             \n";
    $stSql .= "        AND ENLP.timestamp    = CP.timestamp                            \n";
    $stSql .= "                                                                        \n";
    $stSql .= "        AND EE.cod_empenho    =  ".$this->getDado('inCodEmpenho')."     \n";
    $stSql .= "        AND EE.cod_entidade   =  ".$this->getDado('inCodEntidade')."    \n";
    $stSql .= "        AND EE.exercicio      = '".$this->getDado('inExercicio')."'     \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    ORDER BY                                                            \n";
    $stSql .= "        cod_entidade,                                                   \n";
    $stSql .= "        exercicio,                                                      \n";
    $stSql .= "        cod_empenho,                                                    \n";
    $stSql .= "        cod_lote,                                                       \n";
    $stSql .= "        sequencia                                                       \n";
    $stSql .= "    )                                 AS tbl                            \n";
    $stSql .= "    ,contabilidade.lancamento_empenho AS CLE                            \n";
    $stSql .= "    ,contabilidade.lancamento         AS CL                             \n";
    $stSql .= "    ,contabilidade.historico_contabil AS CHC                            \n";
    $stSql .= "    ,contabilidade.valor_lancamento   AS CVL                            \n";
    $stSql .= "    ,contabilidade.lote               AS CLO                            \n";
    $stSql .= "                                                                        \n";
    $stSql .= "WHERE                                                                   \n";
    $stSql .= "        tbl.exercicio    = CLE.exercicio                                \n";
    $stSql .= "    AND tbl.cod_entidade = CLE.cod_entidade                             \n";
    $stSql .= "    AND tbl.tipo         = CLE.tipo                                     \n";
    $stSql .= "    AND tbl.cod_lote     = CLE.cod_lote                                 \n";
    $stSql .= "    AND tbl.sequencia    = CLE.sequencia                                \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    AND CLE.exercicio    = CL.exercicio                                 \n";
    $stSql .= "    AND CLE.cod_entidade = CL.cod_entidade                              \n";
    $stSql .= "    AND CLE.tipo         = CL.tipo                                      \n";
    $stSql .= "    AND CLE.cod_lote     = CL.cod_lote                                  \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    AND CL.cod_historico = CHC.cod_historico                            \n";
    $stSql .= "    AND CL.exercicio     = CHC.exercicio                                \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    AND CL.exercicio     = CVL.exercicio                                \n";
    $stSql .= "    AND CL.cod_entidade  = CVL.cod_entidade                             \n";
    $stSql .= "    AND CL.tipo          = CVL.tipo                                     \n";
    $stSql .= "    AND CL.cod_lote      = CVL.cod_lote                                 \n";
    $stSql .= "    AND CL.sequencia     = CVL.sequencia                                \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    AND CL.exercicio     = CLO.exercicio                                \n";
    $stSql .= "    AND CL.cod_entidade  = CLO.cod_entidade                             \n";
    $stSql .= "    AND CL.cod_lote      = CLO.cod_lote                                 \n";
    $stSql .= "    AND CL.tipo          = CLO.tipo                                     \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    AND CVL.tipo_valor      = 'D'                                       \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    AND tbl.cod_empenho  =  ".$this->getDado('inCodEmpenho')."          \n";
    $stSql .= "    AND tbl.cod_entidade =  ".$this->getDado('inCodEntidade')."         \n";
    $stSql .= "    AND tbl.exercicio_empenho = '".$this->getDado('inExercicio')."'          \n";
    $stSql .= "                                                                        \n";
    $stSql .= "GROUP BY                                                                \n";
    $stSql .= "    tbl.cod_empenho,                                                    \n";
    $stSql .= "    CLO.dt_lote,                                                        \n";
    $stSql .= "    CHC.nom_historico,                                                  \n";
    $stSql .= "    CHC.complemento,                                                    \n";
    $stSql .= "    CL.complemento,                                                     \n";
    $stSql .= "    CVL.vl_lancamento,                                                  \n";
    $stSql .= "    CVL.exercicio,                                                      \n";
    $stSql .= "    CVL.cod_entidade,                                                   \n";
    $stSql .= "    CVL.cod_lote,                                                       \n";
    $stSql .= "    CVL.tipo,                                                           \n";
    $stSql .= "    CVL.sequencia                                                       \n";
    $stSql .= "                                                                        \n";
    $stSql .= "ORDER BY                                                                \n";
    $stSql .= "    CLO.dt_lote,                                                        \n";
    $stSql .= "    CHC.nom_historico,                                                  \n";
    $stSql .= "    CHC.complemento,                                                    \n";
    $stSql .= "    CVL.vl_lancamento                                                   \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelatorioEmpenhoAnulado(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRelatorioEmpenhoAnulado().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRelatorioEmpenhoAnulado()
{
    $stSql  = " SELECT                                                                                                   \n";
    $stSql .= "     publico.fn_mascara_dinamica( ( SELECT valor FROM administracao.configuracao WHERE parametro = 'masc_despesa' AND exercicio = '".$this->getDado('exercicio')."' ), dotacao ) as dotacao_formatada  \n";
    $stSql .= "     ,*                                                                                                   \n";
    $stSql .= " FROM                                                                                                     \n";
    $stSql .= " (                                                                                                        \n";
    $stSql .= "     SELECT                                                                                               \n";
    $stSql .= "          em.cod_empenho                                                                                  \n";
    $stSql .= "         ,em.cod_entidade                                                                                 \n";
    $stSql .= "         ,em.exercicio    as exercicio_empenho                                                            \n";
    $stSql .= "         ,cgm.nom_cgm as nom_entidade                                                                     \n";
    $stSql .= "         ,pe.descricao                                                                                    \n";
    $stSql .= "         ,re.nom_recurso                                                                                  \n";
    $stSql .= "         ,it.vl_total                    as valor_total                                                   \n";
    $stSql .= "         ,(it.vl_total/it.quantidade)    as valor_unitario                                                \n";
    $stSql .= "         ,it.num_item                                                                                     \n";
    $stSql .= "         ,it.quantidade                                                                                   \n";
    $stSql .= "         ,it.nom_item                                                                                     \n";
    $stSql .= "         ,it.cod_item                                                                                     \n";
    $stSql .= "         ,it.complemento                                                                                  \n";
    $stSql .= "         ,te.nom_tipo as nom_tipo_pre_empenho                                                             \n";
    $stSql .= "         ,oo.num_orgao::varchar   ||' - '|| oo.nom_orgao as num_nom_orgao                                          \n";
    $stSql .= "         ,ou.num_unidade::varchar ||' - '|| ou.nom_unidade as num_nom_unidade                                      \n";
    $stSql .= "         ,   de.num_orgao::varchar                                                                                 \n";
    $stSql .= "          ||'.'||de.num_unidade::varchar                                                                           \n";
    $stSql .= "          ||'.'||de.cod_funcao::varchar                                                                            \n";
    $stSql .= "          ||'.'||de.cod_subfuncao::varchar                                                                         \n";
    $stSql .= "          ||'.'||ppa.programa.num_programa::varchar                                                                          \n";
    $stSql .= "          ||'.'||ppa.acao.num_acao::varchar                                                                               \n";
    $stSql .= "          ||'.'||replace(cd.cod_estrutural,'.','')                                                        \n";
    $stSql .= "          AS dotacao                                                                                      \n";
    $stSql .= "         ,cd.descricao AS nom_conta                                                                       \n";
    $stSql .= "         ,de.cod_despesa as dotacao_reduzida                                                              \n";
    $stSql .= "         ,cg.numcgm                                                                                       \n";
    $stSql .= "         ,cg.nom_cgm                                                                                      \n";
    $stSql .= "         ,CASE WHEN pf.numcgm IS NOT NULL THEN pf.cpf                                                     \n";
    $stSql .= "               ELSE pj.cnpj                                                                               \n";
    $stSql .= "          END as cpf_cnpj                                                                                 \n";
    $stSql .= "         ,cg.tipo_logradouro||' '||cg.logradouro||' '||cg.numero||' '||cg.complemento as endereco         \n";
    $stSql .= "         ,mu.nom_municipio                                                                                \n";
    $stSql .= "         ,uf.nom_uf                                                                                       \n";
    $stSql .= "         ,uf.sigla_uf                                                                                     \n";
    $stSql .= "         ,cg.numcgm                                                                                       \n";
    $stSql .= "         ,to_char(de.vl_original,'999999999999999.99')       as valor_orcado                              \n";
    $stSql .= "         ,to_char(em.vl_saldo_anterior,'999999999999999.99') as saldo_anterior                            \n";
    $stSql .= "         ,to_char(em.dt_empenho,'dd/mm/yyyy') as dt_empenho                                               \n";
    $stSql .= "         ,to_char(em.dt_vencimento,'dd/mm/yyyy') as dt_vencimento                                         \n";
    $stSql .= "         ,ea.cod_autorizacao                                                                              \n";
    $stSql .= "         ,ea.exercicio as exercicio_autorizacao                                                                       \n";
    $stSql .= "         ,it.sigla_unidade as simbolo                                                                     \n";
    $stSql .= "         ,eh.cod_historico                                                                                \n";
    $stSql .= "         ,eh.nom_historico                                                                                \n";
    $stSql .= "         ,empenho.fn_consultar_valor_empenhado_anulado_item( em.exercicio, em.cod_empenho, em.cod_entidade, it.num_item ) as vl_anulado   \n";
    $stSql .= "     FROM                                                                                                 \n";
    $stSql .= "          empenho.empenho            as em                                                                \n";
    $stSql .= "         LEFT JOIN                                                                                        \n";
    $stSql .= "           empenho.empenho_autorizacao as ea                                                              \n";
    $stSql .= "         ON (    em.cod_empenho  = ea.cod_empenho                                                         \n";
    $stSql .= "             AND em.exercicio    = ea.exercicio                                                           \n";
    $stSql .= "             AND em.cod_entidade = ea.cod_entidade )                                                      \n";
    $stSql .= "         ,empenho.pre_empenho        as pe                                                                \n";
    $stSql .= "         ,empenho.pre_empenho_despesa as pd                                                               \n";
    $stSql .= "         ,empenho.tipo_empenho       as te                                                                \n";
    $stSql .= "         ,empenho.item_pre_empenho   as it                                                                \n";
    $stSql .= "         ,empenho.historico           as eh                                                               \n";
    $stSql .= "         ,orcamento.despesa          as de                                                                \n";
    $stSql .= "     JOIN orcamento.programa_ppa_programa                                                                   \n";
    $stSql .= "       ON programa_ppa_programa.cod_programa = de.cod_programa                                             \n";
    $stSql .= "      AND programa_ppa_programa.exercicio   = de.exercicio                                                 \n";
    $stSql .= "     JOIN ppa.programa                                                                                          \n";
    $stSql .= "       ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa                                    \n";
    $stSql .= "     JOIN orcamento.pao_ppa_acao                                                                                \n";
    $stSql .= "     ON pao_ppa_acao.num_pao = de.num_pao                                                                  \n";
    $stSql .= "      AND pao_ppa_acao.exercicio = de.exercicio                                                            \n";
    $stSql .= "     JOIN ppa.acao                                                                                              \n";
    $stSql .= "       ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao                                                             \n";
    $stSql .= "         ,orcamento.recurso as re                                                                \n";
    $stSql .= "         ,orcamento.unidade          as ou                                                                \n";
    $stSql .= "         ,orcamento.entidade         as oe                                                                \n";
    $stSql .= "          JOIN                                                                                            \n";
    $stSql .= "               sw_cgm                 as cgm                                                              \n";
    $stSql .= "          ON ( cgm.numcgm = oe.numcgm )                                                                   \n";
    $stSql .= "         ,orcamento.orgao            as oo                                                                \n";
    $stSql .= "         ,orcamento.funcao           as fu                                                                \n";
    $stSql .= "         ,orcamento.subfuncao        as sf                                                                \n";
    $stSql .= "         ,orcamento.programa         as pr                                                                \n";
    $stSql .= "         ,orcamento.pao              as pa                                                                \n";
    $stSql .= "         ,orcamento.conta_despesa    as cd                                                                \n";
    $stSql .= "         ,sw_cgm                     as cg                                                                \n";
    $stSql .= "         LEFT JOIN                                                                                        \n";
    $stSql .= "          sw_cgm_pessoa_fisica       as pf                                                                \n";
    $stSql .= "         ON (cg.numcgm = pf.numcgm)                                                                       \n";
    $stSql .= "         LEFT JOIN                                                                                        \n";
    $stSql .= "          sw_cgm_pessoa_juridica     as pj                                                                \n";
    $stSql .= "         ON (cg.numcgm = pj.numcgm)                                                                       \n";
    $stSql .= "        ,sw_municipio                as mu                                                                \n";
    $stSql .= "        ,sw_uf                       as uf                                                                \n";
    $stSql .= "        ,administracao.unidade_medida          as um                                                                 \n";
    $stSql .= "     WHERE   em.cod_pre_empenho  = pe.cod_pre_empenho                                                     \n";
    $stSql .= "     AND     em.exercicio        = pe.exercicio                                                           \n";
    $stSql .= "     AND     pe.cod_pre_empenho  = it.cod_pre_empenho                                                     \n";
    $stSql .= "     AND     pe.exercicio        = it.exercicio                                                           \n";
    $stSql .= "     --Empenho Despesa                                                                                    \n";
    $stSql .= "     AND     pd.cod_pre_empenho  = pe.cod_pre_empenho                                                     \n";
    $stSql .= "     AND     pd.exercicio        = pe.exercicio                                                           \n";
    $stSql .= "     --Empenho Historico                                                                                  \n";
    $stSql .= "     AND     eh.exercicio        = pe.exercicio                                                           \n";
    $stSql .= "     AND     eh.cod_historico    = pe.cod_historico                                                       \n";
    $stSql .= "     --Orcamento/Despesa                                                                                  \n";
    $stSql .= "     AND     pd.cod_despesa      = de.cod_despesa                                                         \n";
    $stSql .= "     AND     pd.exercicio        = de.exercicio                                                           \n";
    $stSql .= "     --Tipo Empenho                                                                                       \n";
    $stSql .= "     AND     pe.cod_tipo         = te.cod_tipo                                                            \n";
    $stSql .= "     --Órgão                                                                                              \n";
    $stSql .= "     AND     de.num_orgao        = ou.num_orgao                                                           \n";
    $stSql .= "     AND     de.num_unidade      = ou.num_unidade                                                         \n";
    $stSql .= "     AND     de.exercicio        = ou.exercicio                                                           \n";
    $stSql .= "     AND     ou.num_orgao        = oo.num_orgao                                                           \n";
    $stSql .= "     AND     ou.exercicio        = oo.exercicio                                                           \n";
    $stSql .= "     --Unidade                                                                                            \n";
    $stSql .= "     AND     de.num_orgao        = ou.num_orgao                                                           \n";
    $stSql .= "     AND     de.num_unidade      = ou.num_unidade                                                         \n";
    $stSql .= "     AND     de.exercicio        = ou.exercicio                                                           \n";
    $stSql .= "     --Entidade                                                                                           \n";
    $stSql .= "     AND     oe.cod_entidade     = em.cod_entidade                                                        \n";
    $stSql .= "     AND     oe.exercicio        = em.exercicio                                                           \n";
    $stSql .= "     --Função                                                                                             \n";
    $stSql .= "     AND     de.cod_funcao       = fu.cod_funcao                                                          \n";
    $stSql .= "     AND     de.exercicio        = fu.exercicio                                                           \n";
    $stSql .= "     --SubFunção                                                                                          \n";
    $stSql .= "     AND     de.cod_subfuncao    = sf.cod_subfuncao                                                       \n";
    $stSql .= "     AND     de.exercicio        = sf.exercicio                                                           \n";
    $stSql .= "     --Programa                                                                                           \n";
    $stSql .= "     AND     de.cod_programa     = pr.cod_programa                                                        \n";
    $stSql .= "     AND     de.exercicio        = pr.exercicio                                                           \n";
    $stSql .= "     --Pão                                                                                                \n";
    $stSql .= "     AND     de.num_pao          = pa.num_pao                                                             \n";
    $stSql .= "     AND     de.exercicio        = pa.exercicio                                                           \n";
    $stSql .= "     --Conta Despesa                                                                                      \n";
    $stSql .= "     AND     pd.cod_conta        = cd.cod_conta                                                           \n";
    $stSql .= "     AND     pd.exercicio        = cd.exercicio                                                           \n";
    $stSql .= "     --Recurso                                                                                            \n";
    $stSql .= "     AND     de.cod_recurso      = re.cod_recurso                                                         \n";
    $stSql .= "     AND     de.exercicio        = re.exercicio                                                           \n";
    $stSql .= "     --Unidade Medida                                                                                     \n";
    $stSql .= "     AND     it.cod_unidade      = um.cod_unidade                                                         \n";
    $stSql .= "     AND     it.cod_grandeza     = um.cod_grandeza                                                        \n";
    $stSql .= "     --CGM                                                                                                \n";
    $stSql .= "     AND     pe.cgm_beneficiario = cg.numcgm                                                              \n";
    $stSql .= "     --Municipio                                                                                          \n";
    $stSql .= "     AND     cg.cod_municipio    = mu.cod_municipio                                                       \n";
    $stSql .= "     AND     cg.cod_uf           = mu.cod_uf                                                              \n";
    $stSql .= "     --Uf                                                                                                 \n";
    $stSql .= "     AND     mu.cod_uf           = uf.cod_uf                                                              \n";
    $stSql .= "     ORDER BY em.cod_empenho, it.num_item                                                                 \n";
    $stSql .= " ) as tabela                                                                                              \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelatorioEmpenhoAnuladoImplantado(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRelatorioEmpenhoAnuladoImplantado().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRelatorioEmpenhoAnuladoImplantado()
{
    $stSql  = "SELECT                                                                               \n";
    $stSql .= "     publico.fn_mascara_dinamica( ( SELECT valor FROM administracao.configuracao                \n";
    $stSql .= "                                    WHERE parametro = 'masc_despesa'                 \n";
    $stSql .= "                                    AND   exercicio = '".$this->getDado('exercicio')."' ), dotacao              \n";
    $stSql .= "     ) as dotacao_formatada                                                          \n";
    $stSql .= "     ,*                                                                              \n";
    $stSql .= "FROM                                                                                 \n";
    $stSql .= "(                                                                                    \n";
    $stSql .= "     SELECT                                                                          \n";
    $stSql .= "          em.cod_empenho                                                             \n";
    $stSql .= "         ,em.cod_entidade                                                            \n";
    $stSql .= "         ,em.exercicio       as exercicio_empenho                                    \n";
    $stSql .= "         ,cgm.nom_cgm as nom_entidade                                                \n";
    $stSql .= "         ,pe.descricao                                                               \n";
    $stSql .= "         ,it.vl_total                    as valor_total                              \n";
    $stSql .= "         ,(it.vl_total/it.quantidade)    as valor_unitario                           \n";
    $stSql .= "         ,it.num_item                                                                \n";
    $stSql .= "         ,it.quantidade                                                              \n";
    $stSql .= "         ,it.nom_item                                                                \n";
    $stSql .= "         ,it.cod_item                                                                \n";
    $stSql .= "         ,it.complemento                                                             \n";
    $stSql .= "         ,te.nom_tipo    as nom_tipo_pre_empenho                                     \n";
    $stSql .= "         ,rp.num_orgao   as num_nom_orgao                                            \n";
    $stSql .= "         ,rp.num_unidade as num_nom_unidade                                          \n";
    $stSql .= "         ,rp.recurso     as nom_recurso                                              \n";
    $stSql .= "         ,rp.num_orgao::varchar                                                               \n";
    $stSql .= "          ||'.'||rp.num_unidade::varchar                                                      \n";
    $stSql .= "          ||'.'||rp.cod_funcao::varchar                                                       \n";
    $stSql .= "          ||'.'||rp.cod_subfuncao::varchar                                                    \n";
    $stSql .= "          ||'.'||rp.cod_programa::varchar                                                     \n";
    $stSql .= "          ||'.'||rp.num_pao::varchar                                                          \n";
    $stSql .= "          ||'.'||replace(rp.cod_estrutural,'.','')                                   \n";
    $stSql .= "          AS dotacao                                                                 \n";
    $stSql .= "         ,'' as dotacao_reduzida                                                     \n";
    $stSql .= "         ,cg.numcgm                                                                  \n";
    $stSql .= "         ,cg.nom_cgm                                                                 \n";
    $stSql .= "         ,CASE WHEN pf.numcgm IS NOT NULL THEN pf.cpf                                \n";
    $stSql .= "               ELSE pj.cnpj                                                          \n";
    $stSql .= "          END as cpf_cnpj                                                            \n";
    $stSql .= "         ,cg.tipo_logradouro||' '||cg.logradouro||' '||cg.numero||' '||cg.complemento as endereco  \n";
    $stSql .= "         ,mu.nom_municipio                                                                         \n";
    $stSql .= "         ,uf.nom_uf                                                                                \n";
    $stSql .= "         ,uf.sigla_uf                                                                              \n";
    $stSql .= "         ,cg.numcgm                                                                                \n";
    $stSql .= "         ,'0,00'     as valor_orcado                                                               \n";
    $stSql .= "         ,to_char(em.vl_saldo_anterior,'999999999999999.99') as saldo_anterior                     \n";
    $stSql .= "         ,to_char(em.dt_empenho,'dd/mm/yyyy') as dt_empenho                                        \n";
    $stSql .= "         ,to_char(em.dt_vencimento,'dd/mm/yyyy') as dt_vencimento                                  \n";
    $stSql .= "         ,ea.cod_autorizacao                                                                       \n";
    $stSql .= "         ,it.sigla_unidade as simbolo                                                              \n";
    $stSql .= "         ,eh.cod_historico                                                                         \n";
    $stSql .= "         ,eh.nom_historico                                                                         \n";
    $stSql .= "         ,empenho.fn_consultar_valor_empenhado_anulado_item(  em.exercicio                         \n";
    $stSql .= "                                                            , em.cod_empenho                       \n";
    $stSql .= "                                                            , em.cod_entidade                      \n";
    $stSql .= "                                                            , it.num_item                          \n";
    $stSql .= "         ) as vl_anulado                                                                           \n";
    $stSql .= "     FROM                                                                                          \n";
    $stSql .= "          empenho.empenho            as em                                                         \n";
    $stSql .= "         LEFT JOIN                                                                                 \n";
    $stSql .= "           empenho.empenho_autorizacao as ea                                                       \n";
    $stSql .= "         ON (    em.cod_empenho  = ea.cod_empenho                                                  \n";
    $stSql .= "             AND em.exercicio    = ea.exercicio                                                    \n";
    $stSql .= "             AND em.cod_entidade = ea.cod_entidade )                                               \n";
    $stSql .= "         ,empenho.pre_empenho         as pe                                                        \n";
    $stSql .= "         ,empenho.restos_pre_empenho  as rp                                                        \n";
    $stSql .= "         ,empenho.tipo_empenho        as te                                                        \n";
    $stSql .= "         ,empenho.item_pre_empenho    as it                                                        \n";
    $stSql .= "         ,empenho.historico           as eh                                                        \n";
    $stSql .= "         ,orcamento.entidade          as oe                                                        \n";
    $stSql .= "          JOIN                                                                                     \n";
    $stSql .= "               sw_cgm                 as cgm                                                       \n";
    $stSql .= "          ON ( cgm.numcgm = oe.numcgm )                                                            \n";
    $stSql .= "         ,sw_cgm                     as cg                                                         \n";
    $stSql .= "         LEFT JOIN                                                                                 \n";
    $stSql .= "          sw_cgm_pessoa_fisica       as pf                                                         \n";
    $stSql .= "         ON (cg.numcgm = pf.numcgm)                                                                \n";
    $stSql .= "         LEFT JOIN                                                                                 \n";
    $stSql .= "          sw_cgm_pessoa_juridica     as pj                                                         \n";
    $stSql .= "         ON (cg.numcgm = pj.numcgm)                                                                \n";
    $stSql .= "        ,sw_municipio                as mu                                                         \n";
    $stSql .= "        ,sw_uf                       as uf                                                         \n";
    $stSql .= "        ,administracao.unidade_medida          as um                                                          \n";
    $stSql .= "     WHERE   em.cod_pre_empenho  = pe.cod_pre_empenho                                              \n";
    $stSql .= "     AND     em.exercicio        = pe.exercicio                                                    \n";
    $stSql .= "     AND     pe.cod_pre_empenho  = it.cod_pre_empenho                                              \n";
    $stSql .= "     AND     pe.exercicio        = it.exercicio                                                    \n";
    $stSql .= "     --Empenho Restos                                                                              \n";
    $stSql .= "     AND     pe.exercicio        = rp.exercicio                                                    \n";
    $stSql .= "     AND     pe.cod_pre_empenho  = rp.cod_pre_empenho                                              \n";
    $stSql .= "     --Empenho Historico                                                                           \n";
    $stSql .= "     AND     eh.exercicio        = pe.exercicio                                                    \n";
    $stSql .= "     AND     eh.cod_historico    = pe.cod_historico                                                \n";
    $stSql .= "     --Tipo Empenho                                                                                \n";
    $stSql .= "     AND     pe.cod_tipo         = te.cod_tipo                                                     \n";
    $stSql .= "     --Entidade                                                                                    \n";
    $stSql .= "     AND     oe.cod_entidade     = em.cod_entidade                                                 \n";
    $stSql .= "     AND     oe.exercicio        = em.exercicio                                                    \n";
    $stSql .= "     --Unidade Medida                                                                              \n";
    $stSql .= "     AND     it.cod_unidade      = um.cod_unidade                                                  \n";
    $stSql .= "     AND     it.cod_grandeza     = um.cod_grandeza                                                 \n";
    $stSql .= "     --CGM                                                                                         \n";
    $stSql .= "     AND     pe.cgm_beneficiario = cg.numcgm                                                       \n";
    $stSql .= "     --Municipio                                                                                   \n";
    $stSql .= "     AND     cg.cod_municipio    = mu.cod_municipio                                                \n";
    $stSql .= "     AND     cg.cod_uf           = mu.cod_uf                                                       \n";
    $stSql .= "     --Uf                                                                                          \n";
    $stSql .= "     AND     mu.cod_uf           = uf.cod_uf                                                       \n";
    $stSql .= "     ORDER BY em.cod_empenho, it.num_item                                                          \n";
    $stSql .= ") as tabela                                                                                        \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaExercicios(&$rsRecordSet, $stCondicao = "", $boTransacao = "" , $stExercicio = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if($stExercicio)
        $stFiltro = " WHERE exercicio <= '" . $stExercicio . "'";

    $stSql = $this->montaExercicios($stFiltro);
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaExercicios($stFiltro)
{
    $stSql  = " SELECT                                                              \n";
    $stSql .= "      exercicio                                                      \n";
    $stSql .= " FROM                                                                \n";
    $stSql .= "      empenho.empenho                                            \n";
    $stSql .= " ".$stFiltro." \n";
    $stSql .= " GROUP BY                                                            \n";
    $stSql .= "      exercicio                                                      \n";
    $stSql .= " ORDER BY                                                            \n";
    $stSql .= "      exercicio                                                      \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaExerciciosRP(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaExerciciosRP();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaExerciciosRP()
{
    $stSql  = " SELECT                                                              \n";
    $stSql .= "      exercicio                                                      \n";
    $stSql .= " FROM                                                                \n";
    $stSql .= "      empenho.empenho                                            \n";
    $stSql .= " WHERE                                                               \n";
    $stSql .= "      exercicio < '" . Sessao::getExercicio() . "'                       \n";
    $stSql .= " GROUP BY                                                            \n";
    $stSql .= "      exercicio                                                      \n";
    $stSql .= " ORDER BY                                                            \n";
    $stSql .= "      exercicio                                                      \n";

    return $stSql;
}

/**********************************************************************/
/**
* Funções para Modulo de Exportação
* Autor : Lucas Stephanou
*/

function recuperaTransparenciaExportacao(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->MontaRecuperaTransparenciaExportacao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function MontaRecuperaTransparenciaExportacao()
{
    $stSql  = "SELECT                                                           \r\n";
    $stSql .= "tabela.cod_entidade     ,                                        \r\n";
    $stSql .= "tabela.num_orgao        ,                                        \r\n";
    $stSql .= "tabela.num_unidade      ,                                        \r\n";
    $stSql .= "tabela.cod_funcao       ,                                        \r\n";
    $stSql .= "tabela.cod_subfuncao    ,                                        \r\n";
    $stSql .= "tabela.cod_programa     ,                                        \r\n";
    $stSql .= "0 as cod_subprograma    ,                                        \r\n";
    $stSql .= "tabela.num_pao          ,                                        \r\n";
    $stSql .= "replace(tabela.cod_estrutural,'.','') as cod_estrutural ,        \r\n";
    $stSql .= "tabela.cod_recurso      ,                                        \r\n";
    $stSql .= "0 as contrapartida      ,                                        \r\n";
    $stSql .= "(tabela.exercicio || LPAD(tabela.cod_entidade::varchar,2,'0') || LPAD(tabela.cod_empenho::varchar,7,'0')) as num_empenho  ,\r\n";
    $stSql .= "to_char(tabela.dt_empenho,'ddmmyyyy')   as dt_empenho   ,        \r\n";
    $stSql .= "replace(cast(tabela.vl_empenhado as varchar),'.','') as vl_empenhado    ,     \r\n";
    $stSql .= "tabela.sinal            ,                                        \r\n";
    $stSql .= "tabela.cgm              ,                                        \r\n";
    $stSql .= "tabela.exercicio        ,                                        \r\n";
    $stSql .= "tabela.cod_empenho      ,                                        \r\n";
    $stSql .= "tabela.ordem            ,                                        \r\n";
    $stSql .= "tabela.historico        ,                                        \r\n";
    $stSql .= "tabela.caracteristica   ,                                        \r\n";
    $stSql .= "tabela.modalidade       ,                                        \r\n";
    $stSql .= "tabela.nro_licitacao    ,                                        \r\n";
    $stSql .= "tabela.nom_modalidades  ,                                        \r\n";
    $stSql .= "tabela.preco                                                     \r\n";
    $stSql .= "FROM                                                             \r\n";
    $stSql .= "    fn_transparenciaExportacaoEmpenho('".$this->getDado('stExercicio')."','".$this->getDado('dtInicial')."','".$this->getDado('dtFinal')."','".$this->getDado('stCodEntidades')."')  \r\n";
    $stSql .= "as                                                   \r\n";
    $stSql .= "    tabela                                           \r\n";
    $stSql .= "        (                                            \r\n";
    $stSql .= "            num_orgao       integer ,        \r\n";
    $stSql .= "            num_unidade     integer ,        \r\n";
    $stSql .= "            cod_funcao      integer ,        \r\n";
    $stSql .= "            cod_subfuncao   integer ,        \r\n";
    $stSql .= "            cod_programa    integer ,        \r\n";
    $stSql .= "            num_pao         integer ,        \r\n";
    $stSql .= "            cod_recurso     integer ,        \r\n";
    $stSql .= "            cod_estrutural  varchar ,        \r\n";
    $stSql .= "            cod_empenho     integer ,        \r\n";
    $stSql .= "            dt_empenho      date    ,        \r\n";
    $stSql .= "            vl_empenhado    numeric ,        \r\n";
    $stSql .= "            sinal           varchar ,        \r\n";
    $stSql .= "            cgm             integer ,        \r\n";
    $stSql .= "            historico       varchar ,        \r\n";
    $stSql .= "            cod_pre_empenho integer ,        \r\n";
    $stSql .= "            exercicio       char(4) ,        \r\n";
    $stSql .= "            cod_entidade    integer ,        \r\n";
    $stSql .= "            ordem           integer ,        \r\n";
    $stSql .= "            oid             oid     ,        \r\n";
    $stSql .= "            caracteristica  integer ,        \r\n";
    $stSql .= "            modalidade      integer ,        \r\n";
    $stSql .= "            nro_licitacao   text    ,        \r\n";
    $stSql .= "            nom_modalidades text    ,        \r\n";
    $stSql .= "            preco           text             \r\n";
    $stSql .= "        )                                    \r\n";
    $stSql .= "ORDER BY tabela.exercicio,tabela.cod_empenho, tabela.ordem;  \r\n";

    return $stSql;
}

function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->MontaRecuperaDadosExportacao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function MontaRecuperaDadosExportacao()
{
    $stSql  = " SELECT                                                           
                        tabela.num_orgao                                                
                        ,tabela.num_unidade                                              
                        ,tabela.cod_funcao                                               
                        ,tabela.cod_subfuncao                                            
                        ,tabela.cod_programa                                             
                        ,0 as cod_subprograma                                            
                        ,tabela.num_pao                                                  
                        ,replace(tabela.cod_estrutural,'.','') as cod_estrutural       
                        ,tabela.cod_recurso                                              
                        ,0 as contrapartida                                              
                        ,(tabela.exercicio || LPAD(tabela.cod_entidade::varchar,2,'0') || LPAD(tabela.cod_empenho::varchar,7,'0')) as num_empenho  
                        ,to_char(tabela.dt_empenho,'ddmmyyyy')   as dt_empenho   
                        ,replace(cast(tabela.vl_empenhado as varchar),'.','') as vl_empenhado    
                        ,tabela.sinal                                                    
                        ,tabela.cgm                                                      
                        ,tabela.exercicio                                                
                        ,tabela.cod_empenho                                              
                        ,tabela.ordem                                                    
                        ,tabela.historico                                                
                        ,tabela.caracteristica                                           
                        ,tabela.modalidade                                               
                        ,tabela.nro_licitacao                                            
                        ,tabela.outras_modalidades
                        ,tabela.preco             
                        ,'' as branco
                        ,modalidade_licitacao
                FROM                                                             
                    tcers.exportacaoEmpenho('".$this->getDado('stExercicio')."','".$this->getDado('dtInicial')."','".$this->getDado('dtFinal')."','".$this->getDado('stCodEntidades')."')  
                    AS tabela
                    (                                            
                        num_orgao               integer                 
                        ,num_unidade            integer                 
                        ,cod_funcao             integer                 
                        ,cod_subfuncao          integer                 
                        ,cod_programa           integer                 
                        ,num_pao                integer                 
                        ,cod_recurso            integer                 
                        ,cod_estrutural         varchar                 
                        ,cod_empenho            integer                 
                        ,dt_empenho             date                    
                        ,vl_empenhado           numeric                 
                        ,sinal                  varchar                 
                        ,cgm                    integer                 
                        ,historico              varchar                 
                        ,cod_pre_empenho        integer                 
                        ,exercicio              char(4)                 
                        ,cod_entidade           integer                 
                        ,ordem                  integer                 
                        ,oid                    oid                     
                        ,caracteristica         integer                 
                        ,modalidade             integer                 
                        ,nro_licitacao          text                    
                        ,outras_modalidades     text                
                        ,preco                  text                  
                        ,modalidade_licitacao   text               
                    )                                            
                ORDER BY tabela.exercicio
                        ,tabela.cod_empenho
                        ,tabela.ordem; 
    "; 

    return $stSql;
}

/**
    * Método que executa montaRecuperaPreEmpenho no banco de dados
    * @access Public
    * @param Object $rsRecordSet
    * @param Object $boTransacao
    * @return Object $obErro
*/
function recuperaPreEmpenho(&$rsRecordSet, $boTransacao)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaPreEmpenho();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPreEmpenho()
{
    $stSql  = "SELECT cod_pre_empenho                                          \n";
    $stSql .= "FROM empenho.empenho                                        \n";
    $stSql .= "WHERE cod_pre_empenho =  ".$this->getDado('cod_pre_empenho')."  \n";
    $stSql .= "AND   exercicio       = '".$this->getDado('exercicio')      ."' \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoManutencaoDatas(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoManutencaoDatas().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaRelacionamentoManutencaoDatas()
{
    $stSql  = " SELECT                                                                                            \n";
    $stSql .= "     e.cod_empenho,                                                                                \n";
    $stSql .= "     e.cod_entidade,                                                                               \n";
    $stSql .= "     e.exercicio,                                                                                  \n";
    $stSql .= "     to_char(e.dt_empenho,'dd/mm/yyyy') as dt_empenho                                              \n";
    $stSql .= " FROM                                                                                              \n";
    $stSql .= "     empenho.empenho e                                                                         \n";
    $stSql .= "                                                                                                   \n";
  /*$stSql .= "         -- VERIFICA SE DATA EMPENHO É MAIOR QUE DATA DE ANULAÇÃO DE EMPENHO                       \n";  */
    $stSql .= "         LEFT OUTER JOIN empenho.empenho_anulado as ea ON (                                    \n";
    $stSql .= "             e.cod_empenho   = ea.cod_empenho    AND                                               \n";
    $stSql .= "             e.exercicio     = ea.exercicio      AND                                               \n";
    $stSql .= "             e.cod_entidade  = ea.cod_entidade   AND                                               \n";
    $stSql .= "             e.exercicio='".$this->getDado('stExercicio')."' AND                                   \n";
    $stSql .= "             e.cod_entidade in (".$this->getDado('cod_entidade').")  AND                           \n";
    $stSql .= "             e.dt_empenho    > to_date(to_char(ea.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')            \n";
    $stSql .= "         )                                                                                         \n";
    $stSql .= "                                                                                                   \n";
  /*$stSql .= "         -- VERIFICA SE DATA LIQUIDAÇÃO É MAIOR QUE DATA DE ANULAÇÃO DE LIQUIDAÇÃO                 \n";  */
    $stSql .= "         LEFT OUTER JOIN (                                                                         \n";
    $stSql .= "             SELECT                                                                                \n";
    $stSql .= "                 nl.cod_empenho,                                                                   \n";
    $stSql .= "                 nl.exercicio_empenho,                                                             \n";
    $stSql .= "                 nl.cod_entidade                                                                   \n";
    $stSql .= "             FROM                                                                                  \n";
    $stSql .= "                 empenho.nota_liquidacao             nl,                                       \n";
    $stSql .= "                 empenho.nota_liquidacao_item        nli,                                      \n";
    $stSql .= "                 empenho.nota_liquidacao_item_anulado nlia                                     \n";
    $stSql .= "             WHERE                                                                                 \n";
    $stSql .= "                 nl.exercicio        = nli.exercicio         AND                                   \n";
    $stSql .= "                 nl.cod_nota         = nli.cod_nota          AND                                   \n";
    $stSql .= "                 nl.cod_entidade     = nli.cod_entidade      AND                                   \n";
    $stSql .= "                                                                                                   \n";
    $stSql .= "                 nli.exercicio       = nlia.exercicio        AND                                   \n";
    $stSql .= "                 nli.cod_nota        = nlia.cod_nota         AND                                   \n";
    $stSql .= "                 nli.num_item        = nlia.num_item         AND                                   \n";
    $stSql .= "                 nli.exercicio_item  = nlia.exercicio_item   AND                                   \n";
    $stSql .= "                 nli.cod_pre_empenho = nlia.cod_pre_empenho  AND                                   \n";
    $stSql .= "                 nli.cod_entidade    = nlia.cod_entidade     AND                                   \n";
    $stSql .= "                 nl.exercicio_empenho = '".$this->getDado('stExercicio')."' AND                    \n";
    $stSql .= "                 nl.cod_entidade in (".$this->getDado('cod_entidade').")    AND                    \n";
    $stSql .= "                 nl.dt_liquidacao    > to_date(to_char(nlia.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')  \n";
    $stSql .= "         ) as l ON (                                                                               \n";
    $stSql .= "             e.cod_empenho       = l.cod_empenho         AND                                       \n";
    $stSql .= "             e.cod_entidade      = l.cod_entidade        AND                                       \n";
    $stSql .= "             e.exercicio         = l.exercicio_empenho                                             \n";
    $stSql .= "         )                                                                                         \n";
    $stSql .= "                                                                                                   \n";
/*  $stSql .= "         -- VERIFICA SE DATA ORDEM PAGAMENTO É MAIOR QUE DATA DE ANULAÇÃO DE ORDEM DE PAGAMENTO    \n";  */
    $stSql .= "         LEFT OUTER JOIN (                                                                         \n";
    $stSql .= "             SELECT                                                                                \n";
    $stSql .= "                 nl.cod_empenho,                                                                   \n";
    $stSql .= "                 nl.exercicio_empenho,                                                             \n";
    $stSql .= "                 nl.cod_entidade                                                                   \n";
    $stSql .= "             FROM                                                                                  \n";
    $stSql .= "                 empenho.nota_liquidacao         nl,                                           \n";
    $stSql .= "                 empenho.pagamento_liquidacao    pl,                                           \n";
    $stSql .= "                 empenho.ordem_pagamento         op,                                           \n";
    $stSql .= "                 empenho.ordem_pagamento_anulada opa                                           \n";
    $stSql .= "             WHERE                                                                                 \n";
    $stSql .= "                 nl.exercicio        = pl.exercicio_liquidacao   AND                               \n";
    $stSql .= "                 nl.cod_nota         = pl.cod_nota               AND                               \n";
    $stSql .= "                 nl.cod_entidade     = pl.cod_entidade           AND                               \n";
    $stSql .= "                                                                                                   \n";
    $stSql .= "                 pl.exercicio        = op.exercicio              AND                               \n";
    $stSql .= "                 pl.cod_ordem        = op.cod_ordem              AND                               \n";
    $stSql .= "                 pl.cod_entidade     = op.cod_entidade           AND                               \n";
    $stSql .= "                                                                                                   \n";
    $stSql .= "                 op.exercicio        = opa.exercicio             AND                               \n";
    $stSql .= "                 op.cod_ordem        = opa.cod_ordem             AND                               \n";
    $stSql .= "                 op.cod_entidade     = opa.cod_entidade          AND                               \n";
    $stSql .= "                 nl.exercicio_empenho='".$this->getDado('stExercicio')."'   AND                    \n";
    $stSql .= "                 nl.cod_entidade in (".$this->getDado('cod_entidade').")    AND                    \n";
    $stSql .= "                 op.dt_emissao       > to_date(to_char(opa.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')   \n";
    $stSql .= "         ) as o ON (                                                                               \n";
    $stSql .= "             e.cod_empenho       = o.cod_empenho         AND                                       \n";
    $stSql .= "             e.cod_entidade      = o.cod_entidade        AND                                       \n";
    $stSql .= "             e.exercicio         = o.exercicio_empenho                                             \n";
    $stSql .= "         )                                                                                         \n";
    $stSql .= "                                                                                                   \n";
/*  $stSql .= "         -- VERIFICA SE DATA EMPENHO É MAIOR QUE DATA DE LIQUIDAÇÃO                                \n";  */
    $stSql .= "         LEFT OUTER JOIN (                                                                         \n";
    $stSql .= "             SELECT                                                                                \n";
    $stSql .= "                 nl.cod_empenho,                                                                   \n";
    $stSql .= "                 nl.exercicio_empenho,                                                             \n";
    $stSql .= "                 nl.cod_entidade,                                                                  \n";
    $stSql .= "                 nl.dt_liquidacao                                                                  \n";
    $stSql .= "             FROM                                                                                  \n";
    $stSql .= "                 empenho.nota_liquidacao     nl                                                \n";
    $stSql .= "             WHERE nl.exercicio_empenho='".$this->getDado('stExercicio')."'                        \n";
    $stSql .= "             AND   nl.cod_entidade in (".$this->getDado('cod_entidade').")                         \n";
    $stSql .= "         ) as li ON (                                                                              \n";
    $stSql .= "             e.cod_empenho       = li.cod_empenho         AND                                      \n";
    $stSql .= "             e.cod_entidade      = li.cod_entidade        AND                                      \n";
    $stSql .= "             e.exercicio         = li.exercicio_empenho   AND                                      \n";
    $stSql .= "             e.dt_empenho        > li.dt_liquidacao                                                \n";
    $stSql .= "         )                                                                                         \n";
    $stSql .= "                                                                                                   \n";
/*  $stSql .= "         -- VERIFICA SE DATA LIQUIDAÇÃO É MAIOR QUE DATA DE ORDEM DE PAGAMENTO                     \n";  */
    $stSql .= "         LEFT OUTER JOIN (                                                                         \n";
    $stSql .= "            SELECT                                                                                 \n";
    $stSql .= "                 nl.cod_empenho,                                                                   \n";
    $stSql .= "                 nl.exercicio_empenho,                                                             \n";
    $stSql .= "                 nl.cod_entidade                                                                   \n";
    $stSql .= "             FROM                                                                                  \n";
    $stSql .= "                 empenho.nota_liquidacao         nl,                                           \n";
    $stSql .= "                 empenho.pagamento_liquidacao    pl,                                           \n";
    $stSql .= "                 empenho.ordem_pagamento         op                                            \n";
    $stSql .= "             WHERE                                                                                 \n";
    $stSql .= "                 nl.exercicio        = pl.exercicio_liquidacao   AND                               \n";
    $stSql .= "                 nl.cod_nota         = pl.cod_nota               AND                               \n";
    $stSql .= "                 nl.cod_entidade     = pl.cod_entidade           AND                               \n";
    $stSql .= "                                                                                                   \n";
    $stSql .= "                 pl.exercicio        = op.exercicio              AND                               \n";
    $stSql .= "                 pl.cod_ordem        = op.cod_ordem              AND                               \n";
    $stSql .= "                 pl.cod_entidade     = op.cod_entidade           AND                               \n";
    $stSql .= "                 nl.exercicio_empenho='".$this->getDado('stExercicio')."' AND                      \n";
    $stSql .= "                 nl.cod_entidade in (".$this->getDado('cod_entidade').")  AND                      \n";
    $stSql .= "                 op.dt_emissao       < nl.dt_liquidacao                                            \n";
    $stSql .= "         ) as lo ON (                                                                              \n";
    $stSql .= "             e.cod_empenho       = lo.cod_empenho         AND                                      \n";
    $stSql .= "             e.cod_entidade      = lo.cod_entidade        AND                                      \n";
    $stSql .= "             e.exercicio         = lo.exercicio_empenho                                            \n";
    $stSql .= "         )                                                                                         \n";
    $stSql .= "                                                                                                   \n";
/*  $stSql .= "         -- VERIFICA SE DATA DE ORDEM DE PAGAMENTO É MAIOR QUE DATA DE PAGAMENTO                   \n";  */
    $stSql .= "         LEFT OUTER JOIN (                                                                         \n";
    $stSql .= "            SELECT                                                                                 \n";
    $stSql .= "                 nl.cod_empenho,                                                                   \n";
    $stSql .= "                 nl.exercicio_empenho,                                                             \n";
    $stSql .= "                 nl.cod_entidade                                                                   \n";
    $stSql .= "             FROM                                                                                  \n";
    $stSql .= "                 empenho.nota_liquidacao                             nl,                       \n";
    $stSql .= "                 empenho.nota_liquidacao_paga                        nlp,                      \n";
    $stSql .= "                 empenho.pagamento_liquidacao_nota_liquidacao_paga   plnlp,                    \n";
    $stSql .= "                 empenho.pagamento_liquidacao                        pl,                       \n";
    $stSql .= "                 empenho.ordem_pagamento                             op                        \n";
    $stSql .= "             WHERE                                                                                 \n";
    $stSql .= "                 nl.exercicio        = nlp.exercicio                     AND                       \n";
    $stSql .= "                 nl.cod_nota         = nlp.cod_nota                      AND                       \n";
    $stSql .= "                 nl.cod_entidade     = nlp.cod_entidade                  AND                       \n";
    $stSql .= "                                                                                                   \n";
    $stSql .= "                 nlp.cod_entidade    = plnlp.cod_entidade                AND                       \n";
    $stSql .= "                 nlp.cod_nota        = plnlp.cod_nota                    AND                       \n";
    $stSql .= "                 nlp.exercicio       = plnlp.exercicio_liquidacao        AND                       \n";
    $stSql .= "                 nlp.timestamp       = plnlp.timestamp                   AND                       \n";
    $stSql .= "                                                                                                   \n";
    $stSql .= "                 plnlp.cod_ordem             = pl.cod_ordem              AND                       \n";
    $stSql .= "                 plnlp.exercicio             = pl.exercicio              AND                       \n";
    $stSql .= "                 plnlp.cod_entidade          = pl.cod_entidade           AND                       \n";
    $stSql .= "                 plnlp.exercicio_liquidacao  = pl.exercicio_liquidacao   AND                       \n";
    $stSql .= "                 plnlp.cod_nota              = pl.cod_nota               AND                       \n";
    $stSql .= "                                                                                                   \n";
    $stSql .= "                 pl.exercicio        = op.exercicio                      AND                       \n";
    $stSql .= "                 pl.cod_ordem        = op.cod_ordem                      AND                       \n";
    $stSql .= "                 pl.cod_entidade     = op.cod_entidade                   AND                       \n";
    $stSql .= "                 nl.exercicio_empenho='".$this->getDado('stExercicio')."' AND                      \n";
    $stSql .= "                 nl.cod_entidade in (".$this->getDado('cod_entidade').")  AND                      \n";
    $stSql .= "                 op.dt_emissao       > to_date(to_char(nlp.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')   \n";
    $stSql .= "         ) as op ON (                                                                              \n";
    $stSql .= "             e.cod_empenho       = op.cod_empenho         AND                                      \n";
    $stSql .= "             e.cod_entidade      = op.cod_entidade        AND                                      \n";
    $stSql .= "             e.exercicio         = op.exercicio_empenho                                            \n";
    $stSql .= "         )                                                                                         \n";
    $stSql .= "                                                                                                   \n";
    $stSql .= " WHERE                                                                                             \n";
    $stSql .= "     ea.cod_empenho      > 0   OR                                                                  \n";
    $stSql .= "     l.cod_empenho       > 0   OR                                                                  \n";
    $stSql .= "     o.cod_empenho       > 0   OR                                                                  \n";
    $stSql .= "     li.cod_empenho      > 0   OR                                                                  \n";
    $stSql .= "     lo.cod_empenho      > 0   OR                                                                  \n";
    $stSql .= "     op.cod_empenho      > 0   AND                                                                 \n";
    $stSql .= "     e.exercicio = '".$this->getDado('stExercicio')."' AND                                         \n";
    $stSql .= "     e.cod_entidade in (".$this->getDado('cod_entidade').")                                        \n";
    $stSql .= " GROUP BY                                                                                          \n";
    $stSql .= "     e.exercicio,                                                                                  \n";
    $stSql .= "     e.cod_entidade,                                                                               \n";
    $stSql .= "     e.cod_empenho,                                                                                \n";
    $stSql .= "     e.dt_empenho                                                                                 \n";
    $stSql .= " ORDER BY e.exercicio, e.cod_entidade, e.cod_empenho                                               \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosLiquidacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosLiquidacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaDadosLiquidacao()
{
    $stSql  = "SELECT                                                                        \n";
    $stSql .= "     nl.*                                                                     \n";
    $stSql .= "FROM                                                                          \n";
    $stSql .= "     empenho.nota_liquidacao              as nl                               \n";
    $stSql .= "WHERE                                                                         \n";
    $stSql .= "         nl.exercicio       = '".$this->getDado("exercicio")."'               \n";
    $stSql .= "     AND nl.cod_nota        =  ".$this->getDado("cod_nota") ."                \n";
    $stSql .= "     AND nl.cod_entidade    =  ".$this->getDado("cod_entidade")."             \n";

    return $stSql;

}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosLiquidacaoAnulada(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stGrupo  = "GROUP BY     \n";
    $stGrupo .= "na.timestamp \n";
    $stSql = $this->montaRecuperaDadosLiquidacaoAnulada().$stCondicao.$stGrupo.$stOrdem;
//    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaDadosLiquidacaoAnulada()
{
    $stSql  = "SELECT                                                                        \n";
    $stSql .= "     na.timestamp                                                             \n";
    $stSql .= "FROM                                                                          \n";
    $stSql .= "     empenho.nota_liquidacao              as nl,                              \n";
    $stSql .= "     empenho.nota_liquidacao_item         as ni,                              \n";
    $stSql .= "     empenho.nota_liquidacao_item_anulado as na                               \n";
    $stSql .= "WHERE                                                                         \n";
    $stSql .= "         nl.exercicio       = ni.exercicio                                    \n";
    $stSql .= "     AND nl.cod_nota        = ni.cod_nota                                     \n";
    $stSql .= "     AND nl.cod_entidade    = ni.cod_entidade                                 \n";
    $stSql .= "     AND ni.exercicio       = na.exercicio                                    \n";
    $stSql .= "     AND ni.cod_nota        = na.cod_nota                                     \n";
    $stSql .= "     AND ni.num_item        = na.num_item                                     \n";
    $stSql .= "     AND ni.exercicio_item  = na.exercicio_item                               \n";
    $stSql .= "     AND ni.cod_pre_empenho = na.cod_pre_empenho                              \n";
    $stSql .= "     AND ni.cod_entidade    = na.cod_entidade                                 \n";
    $stSql .= "     AND nl.exercicio       = '".$this->getDado("exercicio")."'               \n";
    $stSql .= "     AND nl.cod_nota        =  ".$this->getDado("cod_nota") ."                \n";
    $stSql .= "     AND nl.cod_entidade    =  ".$this->getDado("cod_entidade")."             \n";

    return $stSql;

}

/**
    * Verifica se há empenhos realizados no exercicio atual
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function verificaEmpenhoRealizado(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaVerificaEmpenhoRealizado();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );

    return $obErro;
}

function montaVerificaEmpenhoRealizado()
{
    $stSql  = "SELECT                                        \n";
    $stSql .= "     count(*) as empenhos                     \n";
    $stSql .= "FROM                                          \n";
    $stSql .= "     empenho.empenho                          \n";
    $stSql .= "WHERE                                         \n";
    $stSql .= "     exercicio = '".Sessao::getExercicio()."' \n";

    return $stSql;
}

/**
    * Seta os dados pra fazer o recuperaSaldoAnteriorData
    * @access Private
    * @return $stSql
*/
function montaRecuperaSaldoDotacaoData()
{
    $stSql  = "SELECT                                                                \n";
    $stSql .= "  empenho.fn_saldo_dotacao_data (                                     \n";
    $stSql .= "                               '".$this->getDado( "exercicio"   )."'  \n";
    $stSql .= "                               ,".$this->getDado( "cod_despesa" )."   \n";
    $stSql .= "                               ,'".$this->getDado( "timestamp"  )."'  \n";
    $stSql .= "                               ) AS saldo_anterior                    \n";

    return $stSql;
}

/**
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaSaldoDotacaoData(&$rsRecordSet, $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaSaldoDotacaoData();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Seta os dados pra fazer o recuperaEmpenhoEsfinge
    * @access Private
    * @return $stSql
*/
function montaRecuperaEmpenhoEsfinge()
{
    $stSql  = "
select empenho.cod_entidade
      ,empenho.cod_empenho
      ,substring(conta_despesa.cod_estrutural from 1 for 1) as cod_cat_economica
      ,substring(conta_despesa.cod_estrutural from 5 for 1) as cod_grupo_natureza
      ,substring(conta_despesa.cod_estrutural from 9 for 2) as cod_modalidade
      ,substring(conta_despesa.cod_estrutural from 12 for 2) as cod_elemento
      ,recurso.cod_fonte
      ,substring(despesa.num_pao::VARCHAR from 1 for 2) as cod_tipo_acao
      ,despesa.num_pao
      ,total_empenho.vl_total
      ,historico.nom_historico
      ,case pre_empenho.cod_tipo
           when 1 then 1
           when 2 then 3
           when 3 then 2
      end as cod_tipo_empenho
      ,to_char(empenho.dt_empenho, 'dd/mm/yyyy') as dt_empenho
      ,'Sem Licitação' as sem_num_licitacao
      ,'Sem Contrato' as sem_num_contrato
      ,'Sem Convênio' as sem_num_convenio
      ,case
         when sw_cgm_pessoa_fisica.numcgm is not null then 1
         when sw_cgm_pessoa_juridica.numcgm is not null then 2
      end as tipo_pessoa
      ,case
         when sw_cgm_pessoa_fisica.numcgm is not null then sw_cgm_pessoa_fisica.cpf
         when sw_cgm_pessoa_juridica.numcgm is not null then sw_cgm_pessoa_juridica.cnpj
      end as cod_cic
      ,sw_cgm.nom_cgm
from empenho.empenho
   join empenho.pre_empenho_despesa
  on empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
 and empenho.exercicio = pre_empenho_despesa.exercicio
join ( select exercicio, cod_pre_empenho, sum(vl_total) as vl_total
       from empenho.item_pre_empenho
       group by exercicio, cod_pre_empenho) as total_empenho
   on empenho.cod_pre_empenho = total_empenho.cod_pre_empenho
  and empenho.exercicio = total_empenho.exercicio
 join orcamento.despesa
   on despesa.exercicio = pre_empenho_despesa.exercicio
  and despesa.cod_despesa = pre_empenho_despesa.cod_despesa
 join orcamento.conta_despesa
   on despesa.exercicio = conta_despesa.exercicio
  and despesa.cod_conta = conta_despesa.cod_conta
 join orcamento.recurso as recurso
   on despesa.cod_recurso = recurso.cod_recurso
  and despesa.exercicio = recurso.exercicio
 join empenho.pre_empenho
   on empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
  and empenho.exercicio = pre_empenho.exercicio
 join empenho.historico
   on pre_empenho.cod_historico = historico.cod_historico
  and pre_empenho.exercicio = historico.exercicio
 join sw_cgm
   on pre_empenho.cgm_beneficiario = sw_cgm.numcgm
left outer join sw_cgm_pessoa_fisica
   on sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
left outer join sw_cgm_pessoa_juridica
   on sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

where empenho.cod_entidade in (".$this->getDado('cod_entidade').")
  and empenho.exercicio = '".$this->getDado('exercicio')."'
  and empenho.dt_empenho between to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy')
  and to_date('".$this->getDado("dt_final")."','dd/mm/yyyy')";

    return $stSql;
}

/**
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaEmpenhoEsfinge(&$rsRecordSet, $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaEmpenhoEsfinge();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaEmpenhoObra(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaEmpenhoObra",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaEmpenhoObra()
{
    $arFiltro = array();
    $stFiltro = '';
    $stSql    = '';

    if ( $this->getDado ( 'cod_empenho' ) ) {
        $arFiltro[] = 'empenho.cod_empenho = ' . $this->getDado ( 'cod_empenho' ) ;
    }
    if ( $this->getDado( 'stExercicio' ) ) {
        $arFiltro[] = "empenho.exercicio = '" . $this->getDado( 'stExercicio' ) . "'";
    }
    if ( $this->getDado ( 'cod_entidade' ) ) {
        $arFiltro[] = "empenho.cod_entidade = ".$this->getDado ( 'cod_entidade' );
    }
    if ( $this->getDado( 'cod_estrutural' ) ) {
        $arFiltro[] = "conta_despesa.cod_estrutural like '" . $this->getDado( 'cod_estrutural' ) . "%'";
    }
    if ( $this->getDado ( 'dt_empenho_ini' ) ) {
        $arFiltro[] = "empenho.dt_empenho >= to_date( '".$this->getDado ( 'dt_empenho_ini' ). "' , 'dd/mm/yyyy' )";
    }
    if ( $this->getDado ( 'dt_empenho_fim' ) ) {
        $arFiltro[] = "empenho.dt_empenho <= to_date( '".$this->getDado ( 'dt_empenho_fim' ). "' , 'dd/mm/yyyy' )";
    }
    if ( $this->getDado ( 'cod_empenho_ini' ) ) {
        $arFiltro[] = "empenho.cod_empenho >= " .$this->getDado( 'cod_empenho_ini' ) ;
    }
    if ( $this->getDado ( 'cod_empenho_fim' ) ) {
        $arFiltro[] = "empenho.cod_empenho <= " .$this->getDado( 'cod_empenho_fim' ) ;
    }
    if ( $this->getDado( 'exercicio' ) ) {
        $arFiltro[] = "empenho.exercicio = '". $this->getDado( 'exercicio' ) ."'";
    }

    if ( count ( $arFiltro ) > 0 ) {
        $stFiltro = 'where ' . implode ( ' and ', $arFiltro );
    }

    $stSql = " select empenho.cod_empenho
                    , empenho.exercicio as exercicio_empenho
                    , conta_despesa.cod_estrutural
                    , sw_cgm.nom_cgm as nom_fornecedor
                    , to_char ( empenho.dt_empenho , 'dd/mm/yyyy' ) as dt_empenho
                    , empenho.cod_entidade
                    , despesa.cod_recurso
                 from empenho.empenho
                 join empenho.pre_empenho_despesa
                   on ( empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                  and   empenho.exercicio       = pre_empenho_despesa.exercicio )
                 join orcamento.despesa
                   on ( pre_empenho_despesa.exercicio   = despesa.exercicio
                  and   pre_empenho_despesa.cod_despesa = despesa.cod_despesa )
                 join orcamento.conta_despesa
                   on ( despesa.exercicio   = conta_despesa.exercicio
                  and   despesa.cod_conta = conta_despesa.cod_conta )
                 join empenho.pre_empenho
                   on ( empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                  and   empenho.exercicio       = pre_empenho.exercicio )
                 join sw_cgm
                   on ( pre_empenho.cgm_beneficiario = sw_cgm.numcgm )
                 $stFiltro
             ";

    return $stSql;
}

function recuperaEmpenhoPreEmpenho(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $groupBy = "	GROUP BY  e.cod_empenho
                         , e.exercicio
                         , to_char( e.dt_empenho,'dd/mm/yyyy')
                         , to_char( e.dt_vencimento,'dd/mm/yyyy')
                         , sc.nom_cgm
                         , e.cod_entidade 
                    ORDER BY e.cod_empenho  \n";
    $stSql = $this->montaRecuperaEmpenhoPreEmpenho().$stFiltro.$groupBy;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEmpenhoPreEmpenho()
{
    $stSql  = " SELECT e.cod_empenho    				   		 			  		\n";
    $stSql .= "		 , e.exercicio 								 			  		\n";
    $stSql .= "		 , sum(ie.vl_total) as vl_saldo_anterior	                	\n";
    $stSql .= "		 , to_char( e.dt_empenho,   'dd/mm/yyyy') as dt_empenho     	\n";
    $stSql .= "		 , to_char( e.dt_vencimento	,   'dd/mm/yyyy') as dt_vencimento 	\n";
    $stSql .= "		 , sc.nom_cgm		as credor				 			  		\n";
    $stSql .= "		 , e.cod_entidade				 			  		            \n";
    $stSql .= "	  FROM											 			  		\n";
    $stSql .= "		   empenho.empenho		as e				 			  		\n";

    if ($this->getDado('dt_emissao')) {
        $stSql .= "        JOIN( SELECT nl.exercicio_empenho                                            \n";
        $stSql .= "                   , nl.cod_entidade                                                 \n";
        $stSql .= "                   , nl.cod_empenho                                                  \n";
        $stSql .= "                   , nl.dt_liquidacao                                                \n";
        $stSql .= "                   , (coalesce(sum(nli.vl_total), 0.00) - coalesce(sum(nlia.vl_anulado), 0.00)) as vl_nota_liquidacao    \n";
        $stSql .= "                FROM empenho.nota_liquidacao as nl                                   \n";
        $stSql .= "                     LEFT JOIN empenho.nota_liquidacao_item as nli                   \n";
        $stSql .= "                            ON (     nli.exercicio    = nl.exercicio                 \n";
        $stSql .= "                                 AND nli.cod_entidade = nl.cod_entidade              \n";
        $stSql .= "                                 AND nli.cod_nota     = nl.cod_nota     )            \n";
        $stSql .= "                     LEFT JOIN empenho.nota_liquidacao_item_anulado as nlia          \n";
        $stSql .= "                            ON (     nlia.exercicio       = nli.exercicio            \n";
        $stSql .= "                                 AND nlia.cod_nota        = nli.cod_nota             \n";
        $stSql .= "                                 AND nlia.num_item        = nli.num_item             \n";
        $stSql .= "                                 AND nlia.exercicio_item  = nli.exercicio_item       \n";
        $stSql .= "                                 AND nlia.cod_pre_empenho = nli.cod_pre_empenho      \n";
        $stSql .= "                                 AND nlia.cod_entidade    = nli.cod_entidade    )    \n";
        $stSql .= "            GROUP BY nl.exercicio_empenho                                            \n";
        $stSql .= "                   , nl.cod_entidade                                                 \n";
        $stSql .= "                   , nl.cod_empenho                                                  \n";
        $stSql .= "                   , nl.dt_liquidacao                 ) as nl                        \n";
        $stSql .= "          ON (     nl.exercicio_empenho = e.exercicio                                \n";
        $stSql .= "               AND nl.cod_entidade      = e.cod_entidade                             \n";
        $stSql .= "               AND nl.cod_empenho       = e.cod_empenho  )                           \n";
    }

    $stSql .= "		 , empenho.pre_empenho	as pe				 			  		\n";
    $stSql .= "      , empenho.item_pre_empenho as ie                               \n";
    $stSql .= "		 , sw_cgm				as sc				 			 		\n";
    $stSql .= "  WHERE e.exercicio         = pe.exercicio		 			  		\n";
    $stSql .= "    AND e.cod_pre_empenho   = pe.cod_pre_empenho  			 		\n";
    $stSql .= "    AND pe.cgm_beneficiario = sc.numcgm           			  		\n";
    $stSql .= "	   AND ie.cod_pre_empenho  = pe.cod_pre_empenho						\n";
    $stSql .= "	   AND ie.exercicio        = pe.exercicio                           \n";
    
    # Busca somente empenhos da modalidade Registro de Preços
    if ($this->getDado('registro_precos')) {
        $stSql .= "	   
           AND EXISTS (
                SELECT 1
                 FROM empenho.atributo_empenho_valor
                WHERE atributo_empenho_valor.exercicio = pe.exercicio
                  AND atributo_empenho_valor.cod_pre_empenho = pe.cod_pre_empenho
                  AND atributo_empenho_valor.cod_modulo = 10
                  AND atributo_empenho_valor.cod_cadastro = 1
                  AND atributo_empenho_valor.cod_atributo = 101
                  AND atributo_empenho_valor.valor = '14' 
            ) \n ";
    }    

    if ($this->getDado('cod_entidade')) {
        $stSql .= "    AND e.cod_entidade = ".$this->getDado('cod_entidade')." \n";
    }

    if ($this->getDado('cod_empenho')) {
        $stSql .= "    AND e.cod_empenho = ".$this->getDado('cod_empenho')." \n";
    }
    
    if ($this->getDado('exercicio')) {
        $stSql .= "    AND e.exercicio = '".$this->getDado('exercicio')."' \n";
    }
    
    if ($this->getDado('dt_emissao')) {
       $stSql .= "    AND nl.vl_nota_liquidacao <> 0.00                                                 \n";
       $stSql .= "    AND nl.dt_liquidacao >= to_date( '".$this->getDado('dt_emissao')."'  , 'dd/mm/yyyy')         \n";
    }
    if ($this->getDado('dt_final')) {
       $stSql .= "    AND e.dt_empenho <= to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')"."                \n";
    }

    return $stSql;
}

function recuperaEmpenhoNotaFiscal(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    $stSql  = " SELECT e.cod_empenho    				   		 			  		\n";
    $stSql .= "		 , e.exercicio 								 			  		\n";
    $stSql .= "		 , sum(ie.vl_total) as vl_saldo_anterior	                	\n";
    $stSql .= "		 , to_char( e.dt_empenho,   'dd/mm/yyyy') as dt_empenho     	\n";
    $stSql .= "		 , to_char( e.dt_vencimento	,   'dd/mm/yyyy') as dt_vencimento 	\n";
    $stSql .= "		 , sc.nom_cgm as credor				 			  		\n";
    $stSql .= "		 , e.cod_entidade				 			  		            \n";
    $stSql .= "	  FROM											 			  		\n";
    $stSql .= "		   empenho.empenho as e				 			  		        \n";

    if ($this->getDado('dt_emissao')) {
        $stSql .= "        JOIN( SELECT nl.exercicio_empenho                                            \n";
        $stSql .= "                   , nl.cod_entidade                                                 \n";
        $stSql .= "                   , nl.cod_empenho                                                  \n";
        $stSql .= "                   , nl.dt_liquidacao                                                \n";
        $stSql .= "                   , (coalesce(sum(nli.vl_total), 0.00) - coalesce(sum(nlia.vl_anulado), 0.00)) as vl_nota_liquidacao    \n";
        $stSql .= "                FROM empenho.nota_liquidacao as nl                                   \n";
        $stSql .= "                     LEFT JOIN empenho.nota_liquidacao_item as nli                   \n";
        $stSql .= "                            ON (     nli.exercicio    = nl.exercicio                 \n";
        $stSql .= "                                 AND nli.cod_entidade = nl.cod_entidade              \n";
        $stSql .= "                                 AND nli.cod_nota     = nl.cod_nota     )            \n";
        $stSql .= "                     LEFT JOIN empenho.nota_liquidacao_item_anulado as nlia          \n";
        $stSql .= "                            ON (     nlia.exercicio       = nli.exercicio            \n";
        $stSql .= "                                 AND nlia.cod_nota        = nli.cod_nota             \n";
        $stSql .= "                                 AND nlia.num_item        = nli.num_item             \n";
        $stSql .= "                                 AND nlia.exercicio_item  = nli.exercicio_item       \n";
        $stSql .= "                                 AND nlia.cod_pre_empenho = nli.cod_pre_empenho      \n";
        $stSql .= "                                 AND nlia.cod_entidade    = nli.cod_entidade    )    \n";
        $stSql .= "            GROUP BY nl.exercicio_empenho                                            \n";
        $stSql .= "                   , nl.cod_entidade                                                 \n";
        $stSql .= "                   , nl.cod_empenho                                                  \n";
        $stSql .= "                   , nl.dt_liquidacao                 ) as nl                        \n";
        $stSql .= "          ON (     nl.exercicio_empenho = e.exercicio                                \n";
        $stSql .= "               AND nl.cod_entidade      = e.cod_entidade                             \n";
        $stSql .= "               AND nl.cod_empenho       = e.cod_empenho  )                           \n";
    }

    $stSql .= "		 , empenho.pre_empenho as pe				 			  		\n";
    $stSql .= "      , empenho.item_pre_empenho as ie                               \n";
    $stSql .= "		 , sw_cgm				as sc				 			 		\n";
    $stSql .= "  WHERE e.exercicio         = pe.exercicio		 			  		\n";
    $stSql .= "    AND e.cod_pre_empenho   = pe.cod_pre_empenho  			 		\n";
    $stSql .= "    AND pe.cgm_beneficiario = sc.numcgm           			  		\n";
    $stSql .= "	   AND ie.cod_pre_empenho  = pe.cod_pre_empenho						\n";
    $stSql .= "	   AND ie.exercicio        = pe.exercicio                           \n";

    if ($this->getDado('cod_entidade')) {
        $stSql .= "    AND e.cod_entidade = ".$this->getDado('cod_entidade')."                          \n";
    }
    if ($this->getDado('cod_empenho')) {
        $stSql .= "    AND e.cod_empenho = ".$this->getDado('cod_empenho')."                            \n";
    }
    if ($this->getDado('cod_empenho_ini')) {
        $stSql .= "    AND e.cod_empenho >= ".$this->getDado('cod_empenho_ini')."                       \n";
    }
    if ($this->getDado('cod_empenho_fim')) {
        $stSql .= "    AND e.cod_empenho <= ".$this->getDado('cod_empenho_fim')."                       \n";
    }

    if ($this->getDado('exercicio')) {
        $stSql .= "    AND e.exercicio = '".$this->getDado('exercicio')."'                              \n";
    }
    if ($this->getDado('dt_emissao')) {
       $stSql .= "    AND e.dt_empenho >= to_date( '".$this->getDado('dt_emissao')."'  , 'dd/mm/yyyy')         \n";
    }
    if ($this->getDado('dt_final')) {
       $stSql .= "    AND e.dt_empenho <= to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')"."                \n";
    }

    $stOrder = "  GROUP BY  e.cod_empenho
                         , e.exercicio
                         , to_char( e.dt_empenho,'dd/mm/yyyy')
                         , to_char( e.dt_vencimento,'dd/mm/yyyy')
                         , sc.nom_cgm
                         , e.cod_entidade \n";

    return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function recuperaEmpenhoLiquidacaoNotaFiscal(&$rsRecordSet, $stFiltro="", $boTransacao="")
{
    $stSql  = "SELECT empenho.cod_empenho                                               \n";
    $stSql .= "     , empenho.cod_entidade                                              \n";
    $stSql .= "     , empenho.exercicio AS exercicio_empenho                            \n";
    $stSql .= "     , TO_CHAR(empenho.dt_empenho, 'dd/mm/yyyy') AS dt_empenho           \n";
    $stSql .= "     , sw_cgm.nom_cgm AS credor                                          \n";
    $stSql .= "     , empenho.exercicio                                                 \n";
    $stSql .= "     , despesa.cod_recurso                                               \n";
    $stSql .= "  FROM empenho.nota_liquidacao                                           \n";
    $stSql .= "  JOIN empenho.empenho                                                   \n";
    $stSql .= "    ON empenho.cod_empenho  = nota_liquidacao.cod_empenho                \n";
    $stSql .= "   AND empenho.cod_entidade = nota_liquidacao.cod_entidade               \n";
    $stSql .= "   AND empenho.exercicio    = nota_liquidacao.exercicio_empenho          \n";
    $stSql .= "  JOIN empenho.pre_empenho                                               \n";
    $stSql .= "    ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho             \n";
    $stSql .= "   AND pre_empenho.exercicio       = empenho.exercicio                   \n";
    $stSql .= "  LEFT JOIN empenho.pre_empenho_despesa                                       \n";
    $stSql .= "    ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho \n";
    $stSql .= "   AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio       \n";
    $stSql .= "  LEFT JOIN orcamento.despesa                                                 \n";
    $stSql .= "    ON despesa.exercicio   = pre_empenho_despesa.exercicio               \n";
    $stSql .= "   AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa             \n";
    $stSql .= "  JOIN sw_cgm                                                            \n";
    $stSql .= "    ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario                      \n";
    $stSql .= " WHERE TRUE  \n";
    if ($this->getDado('cod_nota_fiscal')) {
        $stSql .= "     AND EXISTS (SELECT 1                                     \n";
        $stSql .= "                  FROM tcmgo.nota_fiscal_empenho_liquidacao  \n";
        $stSql .= "                 WHERE nota_fiscal_empenho_liquidacao.cod_nota = ".$this->getDado('cod_nota_fiscal');
        $stSql .= "                   AND nota_fiscal_empenho_liquidacao.cod_nota_liquidacao  = nota_liquidacao.cod_nota \n";
        $stSql .= "                   AND nota_fiscal_empenho_liquidacao.cod_entidade         = nota_liquidacao.cod_entidade \n";
        $stSql .= "                   AND nota_fiscal_empenho_liquidacao.exercicio_liquidacao = nota_liquidacao.exercicio) \n";
    }

    if ($this->getDado('cod_empenho')) {
        $stSql .= " AND empenho.cod_empenho = ".$this->getDado('cod_empenho')." \n";
    }

    if ($this->getDado('cod_empenho_ini')) {
        $stSql .= " AND empenho.cod_empenho >= ".$this->getDado('cod_empenho_ini')." \n";
    }
    if ($this->getDado('cod_empenho_fim')) {
        $stSql .= " AND empenho.cod_empenho <= ".$this->getDado('cod_empenho_fim')." \n";
    }

    if ($this->getDado('exercicio')) {
        $stSql .= " AND empenho.exercicio = '".$this->getDado('exercicio')."' \n";
    }
    if ($this->getDado('dt_emissao')) {
        $stSql .= " AND empenho.dt_empenho >= to_date( '".$this->getDado('dt_emissao')."', 'dd/mm/yyyy') \n";
    }
    if ($this->getDado('dt_final')) {
        $stSql .= " AND empenho.dt_empenho <= to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')"." \n";
    }
    if ( $this->getDado('cod_entidade') ) {
        $stSql .= " AND empenho.cod_entidade IN (".$this->getDado('cod_entidade').") \n";
    }

    $stOrder  = "GROUP BY empenho.cod_empenho       \n";
    $stOrder .= "       , empenho.cod_entidade      \n";
    $stOrder .= "       , empenho.exercicio         \n";
    $stOrder .= "       , empenho.dt_empenho        \n";
    $stOrder .= "       , sw_cgm.nom_cgm            \n";
    $stOrder .= "       , despesa.cod_recurso       \n";
    $stOrder .= "ORDER BY empenho.cod_empenho       \n";

    return $this->executaRecuperaSql($stSql, $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
}

function recuperaLiquidacoesNotaFiscal(&$rsRecordSet, $stFiltro="", $boTransacao="")
{
    $stSql  = "SELECT empenho.cod_empenho                                       \n";
    $stSql .= "     , nota_liquidacao.cod_nota                                  \n";
    $stSql .= "     , nota_liquidacao.exercicio AS exercicio_nota               \n";
    $stSql .= "     , empenho.cod_entidade                                      \n";
    $stSql .= "     , empenho.exercicio AS exercicio_empenho                    \n";
    $stSql .= "     , TO_CHAR(nota_liquidacao.dt_liquidacao, 'dd/mm/yyyy') AS dt_liquidacao     \n";
    $stSql .= "     , sw_cgm.nom_cgm AS credor                                  \n";
    $stSql .= "     , vl_nota                                                   \n";
    $stSql .= "     , empenho.exercicio                                         \n";
    $stSql .= "  FROM empenho.nota_liquidacao                                   \n";
    $stSql .= "  JOIN empenho.empenho                                           \n";
    $stSql .= "    ON empenho.cod_empenho  = nota_liquidacao.cod_empenho        \n";
    $stSql .= "   AND empenho.cod_entidade = nota_liquidacao.cod_entidade       \n";
    $stSql .= "   AND empenho.exercicio    = nota_liquidacao.exercicio_empenho  \n";
    $stSql .= "  JOIN empenho.pre_empenho                                       \n";
    $stSql .= "    ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho     \n";
    $stSql .= "   AND pre_empenho.exercicio       = empenho.exercicio           \n";
    $stSql .= "  JOIN sw_cgm                                                    \n";
    $stSql .= "    ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario              \n";
    $stSql .= "  JOIN (SELECT SUM(vl_total) AS vl_nota  /* - COALESCE(SUM(vl_anulado), 0.00) */   \n";
    $stSql .= "             , nota_liquidacao_item.cod_nota                     \n";
    $stSql .= "             , nota_liquidacao_item.exercicio                    \n";
    $stSql .= "             , nota_liquidacao_item.cod_entidade                 \n";
    $stSql .= "          FROM empenho.nota_liquidacao_item                      \n";
    $stSql .= "    /* LEFT JOIN empenho.nota_liquidacao_item_anulado              \n";
    $stSql .= "            ON nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item          \n";
    $stSql .= "           AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item    \n";
    $stSql .= "           AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota          \n";
    $stSql .= "           AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho   \n";
    $stSql .= "           AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade      \n";
    $stSql .= "           AND nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio     */    \n";
    $stSql .= "         WHERE empenho.nota_liquidacao_item.exercicio = '".$this->getDado('exercicio')."'   \n";
    $stSql .= "      GROUP BY nota_liquidacao_item.cod_nota                     \n";
    $stSql .= "             , nota_liquidacao_item.exercicio                    \n";
    $stSql .= "             , nota_liquidacao_item.cod_entidade                 \n";
    $stSql .= "     ) AS liquidacao_item                                        \n";
    $stSql .= "    ON liquidacao_item.cod_nota     = nota_liquidacao.cod_nota   \n";
    $stSql .= "   AND liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade   \n";
    $stSql .= "   AND liquidacao_item.exercicio    = nota_liquidacao.exercicio  \n";
    $stSql .= " WHERE /* liquidacao_item.vl_nota > 0                               \n";
    $stSql .= "   AND */ (NOT EXISTS (SELECT 1                                      \n";
    $stSql .= "                     FROM tcmgo.nota_fiscal_empenho_liquidacao   \n";
    $stSql .= "                    WHERE nota_fiscal_empenho_liquidacao.cod_nota_liquidacao  = nota_liquidacao.cod_nota     \n";
    $stSql .= "                      AND nota_fiscal_empenho_liquidacao.cod_entidade         = nota_liquidacao.cod_entidade \n";
    $stSql .= "                      AND nota_fiscal_empenho_liquidacao.exercicio_liquidacao = nota_liquidacao.exercicio)   \n";
    if ($this->getDado('cod_nota_fiscal')) {
        $stSql .= "     OR EXISTS (SELECT 1                                     \n";
        $stSql .= "                  FROM tcmgo.nota_fiscal_empenho_liquidacao  \n";
        $stSql .= "                 WHERE nota_fiscal_empenho_liquidacao.cod_nota = ".$this->getDado('cod_nota_fiscal');
        $stSql .= "                   AND nota_fiscal_empenho_liquidacao.cod_nota_liquidacao  = nota_liquidacao.cod_nota \n";
        $stSql .= "                   AND nota_fiscal_empenho_liquidacao.cod_entidade         = nota_liquidacao.cod_entidade \n";
        $stSql .= "                   AND nota_fiscal_empenho_liquidacao.exercicio_liquidacao = nota_liquidacao.exercicio) \n";
    }
    $stSql .= "     ) \n";

    if ($this->getDado('cod_empenho')) {
        $stSql .= " AND empenho.cod_empenho = ".$this->getDado('cod_empenho')." \n";
    }

    if ($this->getDado('cod_entidade')) {
        $stSql .= " AND empenho.cod_entidade = '".$this->getDado('cod_entidade')."' \n";
    }

    $stOrder  = "GROUP BY empenho.cod_empenho                                     \n";
    $stOrder .= "       , nota_liquidacao.cod_nota                                \n";
    $stOrder .= "       , nota_liquidacao.exercicio                               \n";
    $stOrder .= "       , nota_liquidacao.dt_liquidacao                           \n";
    $stOrder .= "       , vl_nota                                                 \n";
    $stOrder .= "       , empenho.cod_entidade                                    \n";
    $stOrder .= "       , empenho.exercicio                                       \n";
    $stOrder .= "       , sw_cgm.nom_cgm                                          \n";
    $stOrder .= "ORDER BY empenho.cod_empenho                                     \n";

    return $this->executaRecuperaSql($stSql, $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
}

/**
 * Método que retorna os empenhos que podem ser utilizados para obras
 *
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * @param       object    $rsRecordSet
 * @param       string    $stFiltro       Filtros alternativos que podem ser passados
 * @param       string    $stOrder        Ordenacao do SQL
 * @param       boolean   $boTransacao    Usar transacao
 *
 * @return      object    $rsRecordSet
 */
function recuperaEmpenhoPreEmpenhoObras(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    $stSql  = "
        SELECT e.cod_empenho
             , e.exercicio
             , SUM(ie.vl_total) AS vl_saldo_anterior
             , TO_CHAR(e.dt_empenho, 'dd/mm/yyyy') AS dt_empenho
             , TO_CHAR(e.dt_vencimento, 'dd/mm/yyyy') AS dt_vencimento
             , sc.nom_cgm AS credor
             , sc.nom_cgm AS nom_fornecedor
             , e.cod_entidade
          FROM empenho.empenho AS e
    INNER JOIN ( SELECT pre_empenho.exercicio
                      , pre_empenho.cod_pre_empenho
                      , pre_empenho.cgm_beneficiario
                      , CASE WHEN (restos_pre_empenho.cod_estrutural IS NOT NULL)
                             THEN restos_pre_empenho.cod_estrutural
                             ELSE conta_despesa.cod_estrutural
                        END AS cod_estrutural
                   FROM empenho.pre_empenho
              LEFT JOIN empenho.restos_pre_empenho
                     ON pre_empenho.exercicio       = restos_pre_empenho.exercicio
                    AND pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
              LEFT JOIN empenho.pre_empenho_despesa
                     ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                    AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
              LEFT JOIN orcamento.despesa
                     ON pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                    AND pre_empenho_despesa.exercicio   = despesa.exercicio
              LEFT JOIN orcamento.conta_despesa
                     ON despesa.exercicio = conta_despesa.exercicio
                    AND despesa.cod_conta = conta_despesa.cod_conta
               ) AS pe
            ON e.exercicio       = pe.exercicio
           AND e.cod_pre_empenho = pe.cod_pre_empenho
    INNER JOIN empenho.item_pre_empenho AS ie
            ON pe.exercicio       = ie.exercicio
           AND pe.cod_pre_empenho = ie.cod_pre_empenho
    INNER JOIN sw_cgm AS sc
            ON pe.cgm_beneficiario = sc.numcgm ";

    if ($this->getDado('dt_emissao')) {
        $stSql .= "        JOIN( SELECT nl.exercicio_empenho
                                      , nl.cod_entidade
                                      , nl.cod_empenho
                                      , nl.dt_liquidacao
                                      , (coalesce(sum(nli.vl_total), 0.00) - coalesce(sum(nlia.vl_anulado), 0.00)) as vl_nota_liquidacao
                                   FROM empenho.nota_liquidacao as nl
                                        LEFT JOIN empenho.nota_liquidacao_item as nli
                                               ON (     nli.exercicio    = nl.exercicio
                                                    AND nli.cod_entidade = nl.cod_entidade
                                                    AND nli.cod_nota     = nl.cod_nota     )
                                        LEFT JOIN empenho.nota_liquidacao_item_anulado as nlia
                                               ON (     nlia.exercicio       = nli.exercicio
                                                    AND nlia.cod_nota        = nli.cod_nota
                                                    AND nlia.num_item        = nli.num_item
                                                    AND nlia.exercicio_item  = nli.exercicio_item
                                                    AND nlia.cod_pre_empenho = nli.cod_pre_empenho
                                                    AND nlia.cod_entidade    = nli.cod_entidade    )
                               GROUP BY nl.exercicio_empenho
                                      , nl.cod_entidade
                                      , nl.cod_empenho
                                      , nl.dt_liquidacao                 ) as nl
                             ON (     nl.exercicio_empenho = e.exercicio
                                  AND nl.cod_entidade      = e.cod_entidade
                                  AND nl.cod_empenho       = e.cod_empenho  ) ";
    }

    if ($this->getDado('cod_empenho')) {
        $stFiltro .= "    AND e.cod_empenho = ".$this->getDado('cod_empenho')."                            \n";
    }
    if ($this->getDado('exercicio')) {
        $stFiltro .= "    AND e.exercicio = '".$this->getDado('exercicio')."'                              \n";
    }
    if ($this->getDado('dt_emissao')) {
       $stFiltro .= "    AND nl.vl_nota_liquidacao <> 0.00                                                 \n";
       $stFiltro .= "    AND nl.dt_liquidacao >= to_date( '".$this->getDado('dt_emissao')."'  , 'dd/mm/yyyy')         \n";
    }
    if ($this->getDado('dt_final')) {
       $stFiltro .= "    AND e.dt_empenho <= to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')"."                \n";
    }

    $stFiltro = ' WHERE ' . substr($stFiltro,4);

    $stOrder = " GROUP BY  e.cod_empenho
                        , e.exercicio
                        , to_char( e.dt_empenho,'dd/mm/yyyy')
                        , to_char( e.dt_vencimento,'dd/mm/yyyy')
                        , sc.nom_cgm
                        , e.cod_entidade \n";

    return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function recuperaEmpenhoPreEmpenhoCgm(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaEmpenhoPreEmpenhoCgm().$stFiltro.$groupBy;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEmpenhoPreEmpenhoCgm()
{
    $stSql  = " SELECT e.cod_empenho                                                \n";
    $stSql .= "      , e.exercicio                                                  \n";
    $stSql .= "      , e.cod_entidade                                               \n";
    $stSql .= "      , e.cod_pre_empenho                                            \n";
    $stSql .= "      , to_char(e.dt_empenho,'dd/mm/yyyy') as dt_empenho             \n";
    $stSql .= "      , e.dt_vencimento                                              \n";
    $stSql .= "      , e.vl_saldo_anterior                                          \n";
    $stSql .= "      , e.hora                                                       \n";
    $stSql .= "      , e.cod_categoria                                              \n";
    $stSql .= "      , sc.nom_cgm           as credor                               \n";
    $stSql .= "   FROM                                                              \n";
    $stSql .= "        empenho.empenho            as e                              \n";
    $stSql .= "      , empenho.pre_empenho        as pe                             \n";
    $stSql .= "      , empenho.item_pre_empenho   as ie                             \n";
    $stSql .= "      , sw_cgm                     as sc                             \n";
    $stSql .= "  WHERE e.exercicio         = pe.exercicio                           \n";
    $stSql .= "    AND e.cod_pre_empenho   = pe.cod_pre_empenho                     \n";
    $stSql .= "    AND pe.cgm_beneficiario = sc.numcgm                              \n";
    $stSql .= "    AND ie.cod_pre_empenho  = pe.cod_pre_empenho                     \n";
    $stSql .= "    AND ie.exercicio        = pe.exercicio                           \n";

    return $stSql;
}

/**
 * Função para Exportação de dados do MANAD.
 *
 * @param  Object  $rsRecordSet Objeto RecordSet
 * @param  String  $stCondicao  String de condição do SQL (WHERE)
 * @param  Boolean $boTransacao
 * @return Object  Objeto Erro
 */
function recuperaDadosMANAD(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro();
    $obConexao   = new Conexao();
    $rsRecordSet = new RecordSet();

    $stSql = $this->montaRecuperaDadosMANAD();
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $obErro;
}

function montaRecuperaDadosMANAD()
{
    $stSql  = "   SELECT 'L050' as reg                                                                                   \r\n";
    $stSql .= "        , tabela.num_orgao as cod_org                                                                     \r\n";
    $stSql .= "        , sinal                                                                    \r\n";
    $stSql .= "        , tabela.num_unidade as cod_un_orc                                                                \r\n";
    $stSql .= "        , tabela.cod_funcao as cod_fun                                                                    \r\n";
    $stSql .= "        , tabela.cod_subfuncao as cod_subfun                                                              \r\n";
    $stSql .= "        , tabela.cod_programa as cod_progr                                                                \r\n";
    $stSql .= "        , 0 as cod_subprogr                                                                               \r\n";
    $stSql .= "        , tabela.num_pao as cod_proj_ativ_oe                                                              \r\n";
    $stSql .= "        , replace(tabela.cod_estrutural,'.','') as cod_cta_desp                                           \r\n";
    $stSql .= "        , tabela.cod_recurso as cod_rec_vinc                                                              \r\n";
    $stSql .= "        , 0 as cod_cont_rec                                                                               \r\n";
    $stSql .= "        , tabela.cod_empenho as nm_emp                                                                    \r\n";
    $stSql .= "        , to_char(tabela.dt_empenho,'ddmmyyyy') as dt_emp                                                 \r\n";
    $stSql .= "        , replace(cast(tabela.vl_empenhado as varchar),'.',',') as vl_emp                                  \r\n";
    $stSql .= "      , CASE WHEN sinal = '-'  THEN        \n";
    $stSql .= "                       'C'                     \n";
    $stSql .= "                 ELSE                                              \n";
    $stSql .= "                       'D'                     \n";
    $stSql .= "            END AS ind_deb_cred                                   \n";
    $stSql .= "        , tabela.cgm as cod_credor                                                                        \r\n";
    $stSql .= "        , tabela.historico as hist_emp                                                                    \r\n";
    $stSql .= "     FROM fn_transparenciaExportacaoEmpenho( '".$this->getDado("stExercicio")."'                          \r\n";
    $stSql .= "                                 , '".$this->getDado("dtInicial")."'                                      \r\n";
    $stSql .= "                                 , '".$this->getDado("dtFinal")."'                                        \r\n";
    $stSql .= "                                 , '".$this->getDado("stCodEntidades")."')                                \r\n";
    $stSql .= "       as tabela ( num_orgao           integer,                                                            \r\n";
    $stSql .= "            num_unidade     integer         ,        \r\n";
    $stSql .= "            cod_funcao      integer         ,        \r\n";
    $stSql .= "            cod_subfuncao   integer         ,        \r\n";
    $stSql .= "            cod_programa    integer         ,        \r\n";
    $stSql .= "            num_pao         integer         ,        \r\n";
    $stSql .= "            cod_recurso     integer         ,        \r\n";
    $stSql .= "            cod_estrutural  varchar         ,        \r\n";
    $stSql .= "            cod_empenho     integer         ,        \r\n";
    $stSql .= "            dt_empenho      date            ,        \r\n";
    $stSql .= "            vl_empenhado    numeric(14,2)   ,        \r\n";
    $stSql .= "            sinal           varchar(1)      ,        \r\n";
    $stSql .= "            cgm             integer         ,        \r\n";
    $stSql .= "            historico       varchar         ,        \r\n";
    $stSql .= "            cod_pre_empenho integer         ,        \r\n";
    $stSql .= "            exercicio       char(4)         ,        \r\n";
    $stSql .= "            cod_entidade    integer         ,        \r\n";
    $stSql .= "            ordem           integer         ,        \r\n";
    $stSql .= "            oid             oid             ,        \r\n";
    $stSql .= "            caracteristica  integer         ,        \r\n";
    $stSql .= "            modalidade      integer         ,        \r\n";
    $stSql .= "            nro_licitacao   text            ,        \r\n";
    $stSql .= "            nom_modalidades text            ,        \r\n";
    $stSql .= "            preco           text                     \r\n";
    $stSql .= "                 )                                                                                               \r\n";
    $stSql .= " WHERE tabela.cod_entidade in (".$this->getDado('stCodEntidades').") \n";
    $stSql .= " ORDER BY tabela.exercicio                                                                                \r\n";
    $stSql .= "        , tabela.cod_empenho                                                                              \r\n";

    return $stSql;
}

function recuperaEmpenhoBuscaInner(&$rsRecordSet, $stFiltro="", $boTransacao="")
{
    $stSql  = "SELECT empenho.cod_empenho                                               \n";
    $stSql .= "     , empenho.cod_entidade                                              \n";
    $stSql .= "     , empenho.exercicio AS exercicio_empenho                            \n";
    $stSql .= "     , TO_CHAR(empenho.dt_empenho, 'dd/mm/yyyy') AS dt_empenho           \n";
    $stSql .= "     , sw_cgm.nom_cgm AS credor                                          \n";
    $stSql .= "     , empenho.exercicio                                                 \n";
    $stSql .= "     , despesa.cod_recurso                                               \n";
    $stSql .= "  FROM empenho.nota_liquidacao                                           \n";
    $stSql .= "  JOIN empenho.empenho                                                   \n";
    $stSql .= "    ON empenho.cod_empenho  = nota_liquidacao.cod_empenho                \n";
    $stSql .= "   AND empenho.cod_entidade = nota_liquidacao.cod_entidade               \n";
    $stSql .= "   AND empenho.exercicio    = nota_liquidacao.exercicio_empenho          \n";
    $stSql .= "  JOIN empenho.pre_empenho                                               \n";
    $stSql .= "    ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho             \n";
    $stSql .= "   AND pre_empenho.exercicio       = empenho.exercicio                   \n";
    $stSql .= "  LEFT JOIN empenho.pre_empenho_despesa                                       \n";
    $stSql .= "    ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho \n";
    $stSql .= "   AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio       \n";
    $stSql .= "  LEFT JOIN orcamento.despesa                                                 \n";
    $stSql .= "    ON despesa.exercicio   = pre_empenho_despesa.exercicio               \n";
    $stSql .= "   AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa             \n";
    $stSql .= "  JOIN sw_cgm                                                            \n";
    $stSql .= "    ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario                      \n";

    $stFiltro.=" WHERE exercicio_empenho = '".$this->getDado('exercicio')."' ";

    if ( $this->getDado('cod_empenho') != "") {
        $stFiltro.=" AND empenho.cod_empenho = ".$this->getDado('cod_empenho')." ";
    }
    if ($this->getDado('dt_emissao')) {
        $stSql .= " AND empenho.dt_empenho >= to_date( '".$this->getDado('dt_emissao')."', 'dd/mm/yyyy') \n";
    }
    if ($this->getDado('dt_final')) {
        $stSql .= " AND empenho.dt_empenho <= to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')"." \n";
    }

    $stOrder  = "GROUP BY empenho.cod_empenho       \n";
    $stOrder .= "       , empenho.cod_entidade      \n";
    $stOrder .= "       , empenho.exercicio         \n";
    $stOrder .= "       , empenho.dt_empenho        \n";
    $stOrder .= "       , sw_cgm.nom_cgm            \n";
    $stOrder .= "       , despesa.cod_recurso       \n";
    $stOrder .= "ORDER BY empenho.cod_empenho       \n";

    return $this->executaRecuperaSql($stSql, $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
}

function recuperaEmpenhosPopUp(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaEmpenhosPopUp().$stCondicao.$stOrdem;    
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEmpenhosPopUp()
{
    $stSql = "  SELECT                                                                        
                    tabela.*                                                                
                FROM (                                                                        
                        SELECT                                                                                                   
                                EE.cod_empenho                                                  
                                ,TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho
                                ,EE.exercicio as exercicio_empenho                                                
                                ,EE.exercicio
                                ,EE.cod_entidade
                                ,C.nom_cgm AS nom_fornecedor                                                                      
                        FROM                                                                     
                                empenho.empenho             AS EE                            
                                             
                        JOIN empenho.pre_empenho AS PE                           
                             ON EE.cod_pre_empenho = PE.cod_pre_empenho                       
                            AND EE.exercicio       = PE.exercicio                             
                        JOIN sw_cgm AS  C
                            ON C.numcgm = PE.cgm_beneficiario                             
                                                    
    ) AS tabela 
    ";                                                                  

    return $stSql;
}

    function recuperaEmpenhosPorModalidade(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false) ? " ORDER BY ".$stOrdem : $stOrdem;
        else{
            $stOrdem = "
                  ORDER BY empenho.modalidade
                         , empenho.cod_entidade
                         , empenho.exercicio
                         , empenho.dt_empenho_order
                         , empenho.cod_empenho
                         , empenho.exercicio_nota
                         , empenho.cod_nota
                         , empenho.dt_nota
            ";
        }

        $stSql = $this->montaRecuperaEmpenhosPorModalidade().$stCondicao.$stOrdem;

        $this->setDebug($stSql);

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaEmpenhosPorModalidade()
    {
        $stSql = "SELECT *
                    FROM (
                           SELECT empenho.cod_entidade
                                , cgm_entidade.nom_cgm AS entidade
                                , empenho.exercicio
                                , empenho.cod_empenho
                                , TO_CHAR(empenho.dt_empenho,'mm') AS mes_empenho
                                , TO_CHAR(empenho.dt_empenho,'dd/mm/yyyy') AS dt_empenho
                                , empenho.dt_empenho AS dt_empenho_order
                                , atributo_valor_padrao.cod_valor AS cod_modalidade
                                , TRIM(atributo_valor_padrao.valor_padrao) AS modalidade
                                , sw_cgm.numcgm AS cgm_credor
                                , sw_cgm.nom_cgm AS credor
                                , pre_empenho.descricao
                                , item_pre_empenho.vl_empenho
                                , nota_liquidacao.exercicio AS exercicio_nota
                                , nota_liquidacao.cod_nota
                                , TO_CHAR(nota_liquidacao.dt_liquidacao,'dd/mm/yyyy') AS dt_nota
                                , nota_liquidacao_item.vl_nota
                                , TO_CHAR(nota_liquidacao_paga.timestamp::DATE,'dd/mm/yyyy') AS dt_pagamento
                                , SUM(nota_liquidacao_paga.vl_pago) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado, 0.00)) AS vl_pagamento

                             FROM empenho.empenho

                       INNER JOIN empenho.pre_empenho
                               ON pre_empenho.exercicio       = empenho.exercicio
                              AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                       INNER JOIN ( SELECT item_pre_empenho.exercicio
                                         , item_pre_empenho.cod_pre_empenho
                                         , SUM(item_pre_empenho.vl_total)-SUM(COALESCE(empenho_anulado_item.vl_anulado, 0.00)) AS vl_empenho
                                      FROM empenho.item_pre_empenho
                                 LEFT JOIN empenho.empenho_anulado_item
                                        ON empenho_anulado_item.exercicio       = item_pre_empenho.exercicio
                                       AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                                       AND empenho_anulado_item.num_item        = item_pre_empenho.num_item
                                  GROUP BY item_pre_empenho.exercicio
                                         , item_pre_empenho.cod_pre_empenho
                                    HAVING SUM(item_pre_empenho.vl_total)-SUM(COALESCE(empenho_anulado_item.vl_anulado, 0.00)) > 0
                                  ) AS item_pre_empenho
                               ON pre_empenho.exercicio       = item_pre_empenho.exercicio
                              AND pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho

                       INNER JOIN sw_cgm
                               ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario

                       INNER JOIN empenho.atributo_empenho_valor
                               ON pre_empenho.exercicio       = atributo_empenho_valor.exercicio
                              AND pre_empenho.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho

                       INNER JOIN administracao.atributo_dinamico
                               ON atributo_dinamico.cod_modulo   = atributo_empenho_valor.cod_modulo
                              AND atributo_dinamico.cod_cadastro = atributo_empenho_valor.cod_cadastro
                              AND atributo_dinamico.cod_atributo = atributo_empenho_valor.cod_atributo
                              AND atributo_dinamico.nom_atributo ILIKE 'modalidade%'

                       INNER JOIN administracao.atributo_valor_padrao
                               ON atributo_empenho_valor.cod_modulo     = atributo_valor_padrao.cod_modulo
                              AND atributo_empenho_valor.cod_cadastro   = atributo_valor_padrao.cod_cadastro
                              AND atributo_empenho_valor.cod_atributo   = atributo_valor_padrao.cod_atributo
                              AND atributo_empenho_valor.valor          = atributo_valor_padrao.cod_valor::TEXT

                       INNER JOIN empenho.nota_liquidacao
                               ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                              AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                              AND empenho.cod_empenho  = nota_liquidacao.cod_empenho

                       INNER JOIN ( SELECT nota_liquidacao_item.exercicio
                                         , nota_liquidacao_item.cod_nota
                                         , nota_liquidacao_item.cod_entidade
                                         , SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS vl_nota
                                      FROM empenho.nota_liquidacao_item
                                 LEFT JOIN empenho.nota_liquidacao_item_anulado
                                        ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                                       AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                                       AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                                       AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
                                       AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                                       AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                                  GROUP BY nota_liquidacao_item.exercicio
                                         , nota_liquidacao_item.cod_nota
                                         , nota_liquidacao_item.cod_entidade
                                    HAVING SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) > 0
                                  ) AS nota_liquidacao_item
                               ON nota_liquidacao_item.exercicio       = nota_liquidacao.exercicio
                              AND nota_liquidacao_item.cod_nota        = nota_liquidacao.cod_nota
                              AND nota_liquidacao_item.cod_entidade    = nota_liquidacao.cod_entidade

                       INNER JOIN empenho.nota_liquidacao_paga
                               ON nota_liquidacao_paga.exercicio       = nota_liquidacao.exercicio
                              AND nota_liquidacao_paga.cod_nota        = nota_liquidacao.cod_nota
                              AND nota_liquidacao_paga.cod_entidade    = nota_liquidacao.cod_entidade

                        LEFT JOIN empenho.nota_liquidacao_paga_anulada
                               ON nota_liquidacao_paga_anulada.exercicio       = nota_liquidacao_paga.exercicio
                              AND nota_liquidacao_paga_anulada.cod_nota        = nota_liquidacao_paga.cod_nota
                              AND nota_liquidacao_paga_anulada.cod_entidade    = nota_liquidacao_paga.cod_entidade
                              AND nota_liquidacao_paga_anulada.timestamp       = nota_liquidacao_paga.timestamp

                       INNER JOIN orcamento.entidade
                               ON entidade.exercicio    = empenho.exercicio
                              AND entidade.cod_entidade = empenho.cod_entidade

                       INNER JOIN sw_cgm AS cgm_entidade
                               ON cgm_entidade.numcgm = entidade.numcgm

                         GROUP BY empenho.cod_entidade
                                , empenho.exercicio
                                , empenho.cod_empenho
                                , empenho.dt_empenho
                                , sw_cgm.numcgm
                                , sw_cgm.nom_cgm
                                , atributo_valor_padrao.cod_valor
                                , atributo_valor_padrao.valor_padrao
                                , pre_empenho.descricao
                                , item_pre_empenho.vl_empenho
                                , nota_liquidacao.exercicio
                                , nota_liquidacao.cod_nota
                                , nota_liquidacao.dt_liquidacao
                                , nota_liquidacao_item.vl_nota
                                , nota_liquidacao_paga.timestamp::DATE
                                , cgm_entidade.nom_cgm
                         ) AS empenho
                   WHERE empenho.exercicio = '".$this->getDado('exercicio')."'
                     AND empenho.cod_entidade IN ( ".$this->getDado('entidade')." )
        ";

        return $stSql;
    }

}
