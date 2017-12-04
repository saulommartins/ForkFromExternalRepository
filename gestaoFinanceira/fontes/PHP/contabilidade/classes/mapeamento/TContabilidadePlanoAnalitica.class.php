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

    * Classe de mapeamento da tabela CONTABILIDADE.PLANO_ANALITICA
    * Data de Criação: 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TContabilidadePlanoAnalitica.class.php 65673 2016-06-08 15:04:01Z franver $

    * Casos de uso: uc-02.02.02, uc-02.04.03, uc-02.02.31,uc-02.04.09, uc-02.03.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CONTABILIDADE.PLANO_ANALITICA
  * Data de Criação: 01/11/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TContabilidadePlanoAnalitica extends Persistente
{
    /**
        * Método Construtor
        * @access Public
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('contabilidade.plano_analitica');
    
        $this->setCampoCod('cod_plano');
        $this->setComplementoChave('exercicio');
    
        $this->AddCampo('cod_plano'     , 'integer', true,   '',  true, false);
        $this->AddCampo('exercicio'     ,    'char', true, '04',  true,  true);
        $this->AddCampo('cod_conta'     , 'integer', true,   '', false,  true);
        $this->AddCampo('natureza_saldo',    'char', true, '01', false,  true);
    
    }

/**
    * Seta dados para fazer o recuperaRelacionamento
    * @access Public
    * @return String $stSql
*/
function montaRecuperaRelacionamento()
{
    $stSQL  = " SELECT                                                                            \n";
    $stSQL .= "     pa.cod_plano,pc.cod_estrutural,pc.nom_conta,pc.cod_conta,                     \n";
    $stSQL .= "     publico.fn_mascarareduzida(pc.cod_estrutural) as cod_reduzido,                \n";
    $stSQL .= "     pc.cod_classificacao,pc.cod_sistema,                                          \n";
    $stSQL .= "     pb.exercicio, pb.cod_banco, pb.cod_agencia,                  \n";
    $stSQL .= "     pb.cod_entidade,pa.natureza_saldo,                           \n";
    $stSQL .= "     CASE WHEN publico.fn_nivel(cod_estrutural) > 4 THEN                           \n";
    $stSQL .= "             5                                                                     \n";
    $stSQL .= "          ELSE                                                                     \n";
    $stSQL .= "             publico.fn_nivel(cod_estrutural)                                      \n";
    $stSQL .= "     END as nivel                                                                  \n";
    $stSQL .= " FROM                                                                              \n";
    $stSQL .= "     contabilidade.plano_conta as pc                                           \n";
    $stSQL .= " LEFT JOIN contabilidade.plano_analitica as pa on (                            \n";
    $stSQL .= " pc.cod_conta = pa.cod_conta and pc.exercicio = pa.exercicio )                     \n";
    $stSQL .= " LEFT JOIN contabilidade.plano_banco as pb on (                                \n";
    $stSQL .= " pb.cod_plano = pa.cod_plano and pb.exercicio = pa.exercicio                       \n";
    if ($this->getDado('cod_entidade')) {
       $stSQL .= " AND pb.cod_entidade IN (".$this->getDado('cod_entidade').")                    \n";
    }
    $stSQL .= " )                                                                                 \n";

    return $stSQL;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método
    * montaRecuperaRelacionamentoRelatorioConta.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoRelatorioConta(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoRelatorioConta().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoRelatorioConta()
{
    $stSQL  = " SELECT                                                                            \n";
    $stSQL .= "     pa.cod_plano,pc.cod_estrutural,pc.nom_conta,pc.cod_conta,                     \n";
    $stSQL .= "     publico.fn_mascarareduzida(pc.cod_estrutural) as cod_reduzido,                \n";
    $stSQL .= "     pc.cod_classificacao,pc.cod_sistema, sc.nom_sistema,                          \n";
    $stSQL .= "     pb.exercicio, pb.cod_banco, pb.cod_agencia,                                   \n";
    $stSQL .= "     pa.natureza_saldo,                                           \n";
    $stSQL .= "     CASE WHEN publico.fn_nivel(cod_estrutural) > 4 THEN                           \n";
    $stSQL .= "             5                                                                     \n";
    $stSQL .= "          ELSE                                                                     \n";
    $stSQL .= "             publico.fn_nivel(cod_estrutural)                                      \n";
    $stSQL .= "     END as nivel                                                                  \n";
    $stSQL .= " FROM                                                                              \n";
    $stSQL .= "     contabilidade.plano_conta as pc                                               \n";
    $stSQL .= " LEFT JOIN contabilidade.plano_analitica as pa on (                                \n";
    $stSQL .= " pc.cod_conta = pa.cod_conta and pc.exercicio = pa.exercicio )                     \n";
    $stSQL .= " LEFT JOIN contabilidade.sistema_contabil as sc on (                               \n";
    $stSQL .= " pc.cod_sistema = sc.cod_sistema and pc.exercicio = sc.exercicio )                 \n";
    $stSQL .= " LEFT JOIN contabilidade.plano_banco as pb on (                                    \n";
    $stSQL .= " pb.cod_plano = pa.cod_plano and pb.exercicio = pa.exercicio                       \n";
    if ($this->getDado('cod_entidade')) {
       $stSQL .= " AND pb.cod_entidade IN (".$this->getDado('cod_entidade').")                    \n";
    }
    $stSQL .= " )                                                                                 \n";

    return $stSQL;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método
    * montaRecuperaRelacionamentoContaEntidade.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoContaEntidade(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoContaEntidade().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Seta dados para fazer o recuperaRelacionamentoContaEntidade
    * @access Public
    * @return String $stSql
*/
function montaRecuperaRelacionamentoContaEntidade()
{
    $stSQL  = " SELECT                                                                 
                          pa.cod_plano
                        , pc.cod_estrutural
                        , pc.nom_conta
                        , pc.cod_conta
                        , publico.fn_mascarareduzida(pc.cod_estrutural) as cod_reduzido
                        , pc.cod_classificacao,pc.cod_sistema
                        , pb.exercicio
                        , pb.cod_banco
                        , pb.cod_agencia
                        , pb.conta_corrente
                        , pb.cod_conta_corrente
                        , mb.num_banco
                        , ma.num_agencia
                        , pa.natureza_saldo
                        , pc.atributo_tcepe
                        , pc.atributo_tcemg
                        , CASE WHEN publico.fn_nivel(cod_estrutural) > 4 THEN
                                    5                                                                     
                            ELSE                                                                     
                                    publico.fn_nivel(cod_estrutural)                                      
                         END as nivel
                        , rec.cod_recurso                                                               
                FROM contabilidade.plano_conta as pc                                               
                LEFT JOIN contabilidade.plano_conta_encerrada
                     ON plano_conta_encerrada.cod_conta = pc.cod_conta
                    AND plano_conta_encerrada.exercicio = pc.exercicio
                LEFT JOIN contabilidade.plano_analitica as pa                                 
                     ON pc.cod_conta = pa.cod_conta                                           
                    AND pc.exercicio = pa.exercicio
                LEFT JOIN contabilidade.plano_recurso as pr                                   
                     ON pr.cod_plano    = pa.cod_plano                                        
                    AND pr.exercicio    = pa.exercicio
                LEFT JOIN orcamento.recurso as rec                                            
                     ON rec.cod_recurso = pr.cod_recurso                                      
                    AND rec.exercicio   = pr.exercicio 
                LEFT JOIN contabilidade.plano_banco as pb                                     
                     ON pb.cod_plano = pa.cod_plano
                    AND pb.exercicio = pa.exercicio
                LEFT JOIN monetario.banco as mb                                               
                    ON pb.cod_banco = mb.cod_banco
                LEFT JOIN monetario.agencia as ma                                             
                     ON pb.cod_agencia = ma.cod_agencia                                       
                    AND pb.cod_banco   = ma.cod_banco
        ";                                       

    return $stSQL;
}
/**
    * Seta dados para fazer o recuperaRelacionamentoPlanoContaAnalitica
    * @access Public
    * @return String $stSql
*/
function montaRecuperaPlanoContaAnalitica()
{
    $stSQL  = "SELECT                                                                                                                          \n ";
    $stSQL .= "     pc.cod_estrutural                                                                                                          \n ";
    $stSQL .= "    ,pc.exercicio                                                                                                               \n ";
    $stSQL .= "    ,pc.nom_conta                                                                                                               \n ";
    $stSQL .= "    ,pc.cod_conta                                                                                                               \n ";
    $stSQL .= "    ,publico.fn_mascarareduzida(pc.cod_estrutural) as cod_reduzido                                                              \n ";
    $stSQL .= "    ,tabela.vl_lancamento                                                                                                       \n ";
    $stSQL .= "    ,pa.cod_plano                                                                                                               \n ";
    $stSQL .= "    ,tabela.sequencia                                                                                                           \n ";
    $stSQL .= "    ,pa.natureza_saldo                                                                                                          \n ";
    $stSQL .= "FROM                                                                                                                            \n ";
    $stSQL .= "    contabilidade.plano_conta       as pc                                                                                       \n ";
    $stSQL .= "   ,contabilidade.plano_analitica   as pa                                                                                       \n ";
    $stSQL .= "LEFT JOIN                                                                                                                       \n ";
    $stSQL .= "    (                                                                                                                           \n ";
    $stSQL .= "        SELECT CASE WHEN pac.cod_plano IS NOT NULL                                                                              \n ";
    $stSQL .= "                    THEN pac.cod_plano                                                                                          \n ";
    $stSQL .= "                    ELSE pad.cod_plano                                                                                          \n ";
    $stSQL .= "               END as cod_plano                                                                                                 \n ";
    $stSQL .= "               ,CASE WHEN cc.cod_entidade IS NOT NULL                                                                           \n ";
    $stSQL .= "                     THEN cc.cod_entidade                                                                                       \n ";
    $stSQL .= "                     ELSE cd.cod_entidade                                                                                       \n ";
    $stSQL .= "                END as cod_entidade                                                                                             \n ";
    $stSQL .= "                ,CASE WHEN vlc.vl_lancamento IS NOT NULL                                                                        \n ";
    $stSQL .= "                      THEN vlc.vl_lancamento                                                                                    \n ";
    $stSQL .= "                      ELSE vld.vl_lancamento                                                                                    \n ";
    $stSQL .= "                END as vl_lancamento                                                                                            \n ";
    $stSQL .= "                ,CASE WHEN vlc.sequencia IS NOT NULL                                                                            \n ";
    $stSQL .= "                      THEN vlc.sequencia                                                                                        \n ";
    $stSQL .= "                      ELSE vld.sequencia                                                                                        \n ";
    $stSQL .= "                 END as sequencia                                                                                               \n ";
    $stSQL .= "                ,CASE WHEN cc.cod_entidade IS NOT NULL                                                                          \n ";
    $stSQL .= "                      THEN cc.cod_entidade                                                                                      \n ";
    $stSQL .= "                      ELSE cd.cod_entidade                                                                                      \n ";
    $stSQL .= "                 END as cod_entidade                                                                                            \n ";
    $stSQL .= "                ,CASE WHEN cc.exercicio IS NOT NULL                                                                             \n ";
    $stSQL .= "                      THEN cc.exercicio                                                                                         \n ";
    $stSQL .= "                      ELSE cd.exercicio                                                                                         \n ";
    $stSQL .= "                 END as exercicio                                                                                               \n ";
    $stSQL .= "        FROM contabilidade.plano_analitica as pad                                                                               \n ";
    $stSQL .= "             LEFT JOIN                                                                                                        \n ";
    $stSQL .= "               contabilidade.conta_debito as cd                                                                                 \n ";
    $stSQL .= "             ON ( pad.cod_plano = cd.cod_plano AND pad.exercicio = cd.exercicio AND cd.tipo = 'I' AND cd.cod_lote = 1 AND cd.cod_entidade = ".$this->getDado('cod_entidade').")         \n ";
    $stSQL .= "             LEFT JOIN                                                                                                        \n ";
    $stSQL .= "               contabilidade.valor_lancamento as vld                                                                            \n ";
    $stSQL .= "             ON (                                                                                                               \n ";
    $stSQL .= "                        cd.cod_lote = vld.cod_lote                                                                              \n ";
    $stSQL .= "                    AND cd.tipo = vld.tipo                                                                                      \n ";
    $stSQL .= "                    AND cd.sequencia = vld.sequencia                                                                            \n ";
    $stSQL .= "                    AND cd.exercicio = vld.exercicio                                                                            \n ";
    $stSQL .= "                    AND cd.tipo_valor = vld.tipo_valor                                                                          \n ";
    $stSQL .= "                    AND cd.cod_entidade = vld.cod_entidade                                                                      \n ";
    $stSQL .= "                )                                                                                                               \n ";
    $stSQL .= "            ,contabilidade.plano_analitica as pac                                                                               \n ";
    $stSQL .= "             LEFT JOIN                                                                                                          \n ";
    $stSQL .= "               contabilidade.conta_credito as cc                                                                                \n ";
    $stSQL .= "             ON ( pac.cod_plano = cc.cod_plano AND pac.exercicio = cc.exercicio AND cc.tipo = 'I' AND cc.cod_lote = 1 AND cc.cod_entidade = ".$this->getDado('cod_entidade').")         \n ";
    $stSQL .= "             LEFT JOIN                                                                                                          \n ";
    $stSQL .= "               contabilidade.valor_lancamento as vlc                                                                            \n ";
    $stSQL .= "             ON (                                                                                                               \n ";
    $stSQL .= "                        cc.cod_lote = vlc.cod_lote                                                                              \n ";
    $stSQL .= "                    AND cc.tipo = vlc.tipo                                                                                      \n ";
    $stSQL .= "                    AND cc.sequencia = vlc.sequencia                                                                            \n ";
    $stSQL .= "                    AND cc.exercicio = vlc.exercicio                                                                            \n ";
    $stSQL .= "                    AND cc.tipo_valor = vlc.tipo_valor                                                                          \n ";
    $stSQL .= "                    AND cc.cod_entidade = vlc.cod_entidade                                                                      \n ";
    $stSQL .= "                )                                                                                                               \n ";
    $stSQL .= "                                                                                                                                \n ";
    $stSQL .= "        WHERE   pad.cod_plano = pac.cod_plano                                                                                   \n ";
    $stSQL .= "        AND     pad.exercicio = pac.exercicio                                                                                   \n ";
    $stSQL .= "        AND     ( cc.cod_entidade = ".$this->getDado('cod_entidade')." OR cd.cod_entidade = ".$this->getDado('cod_entidade').") \n ";
    $stSQL .= "    ) as tabela                                                                                                                 \n ";
    $stSQL .= "    ON(                                                                                                                         \n ";
    $stSQL .= "            pa.cod_plano = tabela.cod_plano                                                                                     \n ";
    $stSQL .= "        AND pa.exercicio = tabela.exercicio                                                                                     \n ";
    $stSQL .= "      )                                                                                                                         \n ";
    $stSQL .= "LEFT JOIN   contabilidade.plano_recurso as pr                                                                                   \n ";
    $stSQL .= "       ON pr.cod_plano = pa.cod_plano                                                                                           \n ";
    $stSQL .= "      AND pr.exercicio = pa.exercicio                                                                                           \n ";
    $stSQL .= "WHERE   pc.cod_conta = pa.cod_conta                                                                                             \n ";
    $stSQL .= "AND     pc.exercicio = pa.exercicio                                                                                             \n ";

    return $stSQL;
}

/**
    * Seta dados para fazer o montaRecuperaContasAberturaOrcamento
    * @access Public
    * @return String $stSql
*/
function montaRecuperaContasAberturaOrcamento()
{
    $stSQL = "SELECT pc.exercicio
                    ,pc.nom_conta
                    ,pc.cod_estrutural  
                    ,pa.cod_conta                                          
                    ,tabela.cod_plano
                    ,pc.funcao
                    ,pa.natureza_saldo
                    ,tabela.nom_lote
                    ,tabela.cod_historico
                    ,tabela.cod_lote
                    ,tabela.vl_lancamento 
                FROM contabilidade.plano_conta       as pc                                                                                       
                    ,contabilidade.plano_analitica   as pa                                                                                       
                LEFT JOIN                                                                                                                       
                    (                                                                                                                           
                            SELECT  CASE WHEN pac.cod_plano IS NOT NULL                                                                              
                                        THEN pac.cod_plano                                                                                          
                                     ELSE pad.cod_plano                                                                                          
                                    END as cod_plano                                                                                                 
                                    ,CASE WHEN cc.cod_entidade IS NOT NULL                                                                           
                                            THEN cc.cod_entidade                                                                                       
                                     ELSE cd.cod_entidade                                                                                       
                                    END as cod_entidade                                                                                             
                                    ,CASE WHEN vlc.vl_lancamento IS NOT NULL                                                                        
                                            THEN vlc.vl_lancamento                                                                                    
                                     ELSE vld.vl_lancamento                                                                                    
                                    END as vl_lancamento                                                                                            
                                    ,CASE WHEN vlc.sequencia IS NOT NULL                                                                            
                                            THEN vlc.sequencia                                                                                        
                                     ELSE vld.sequencia                                                                                        
                                    END as sequencia                                                                                               
                                    ,CASE WHEN cc.cod_entidade IS NOT NULL                                                                          
                                            THEN cc.cod_entidade                                                                                      
                                     ELSE cd.cod_entidade                                                                                      
                                    END as cod_entidade                                                                                            
                                    ,CASE WHEN cc.exercicio IS NOT NULL                                                                             
                                            THEN cc.exercicio                                                                                         
                                     ELSE cd.exercicio                                                                                         
                                    END as exercicio
                                    
                                    ,CASE WHEN cld.nom_lote IS NOT NULL
                                            THEN cld.nom_lote
                                     ELSE clc.nom_lote
                                    END as nom_lote
                                    
                                    ,CASE WHEN lancamento_despesa.cod_lote IS NOT NULL
                                        THEN lancamento_despesa.cod_lote
                                     ELSE lancamento_credito.cod_lote
                                    END as cod_lote
                                    
                                    ,CASE WHEN lancamento_despesa.cod_historico IS NOT NULL
                                        THEN lancamento_despesa.cod_historico
                                     ELSE lancamento_credito.cod_historico
                                    END as cod_historico
                                    
                                    --,lancamento.cod_lote
                                    --,lancamento.cod_historico
                                    
                            FROM contabilidade.plano_analitica as pad                                                                               
                            
                            LEFT JOIN contabilidade.conta_debito as cd                                                                                 
                                ON( pad.cod_plano   = cd.cod_plano 
                                AND pad.exercicio   = cd.exercicio 
                                AND cd.tipo         = 'M' 
                                AND cd.cod_entidade = ".$this->getDado('cod_entidade').")         
                            
                            LEFT JOIN contabilidade.valor_lancamento as vld
                                ON( cd.cod_lote     = vld.cod_lote                                                                              
                                AND cd.tipo         = vld.tipo                                                                                      
                                AND cd.sequencia    = vld.sequencia                                                                            
                                AND cd.exercicio    = vld.exercicio                                                                            
                                AND cd.tipo_valor   = vld.tipo_valor                                                                          
                                AND cd.cod_entidade = vld.cod_entidade )

                            LEFT JOIN contabilidade.lancamento as lancamento_despesa
                                ON( lancamento_despesa.exercicio    = vld.exercicio
                                AND lancamento_despesa.cod_lote     = vld.cod_lote
                                AND lancamento_despesa.tipo         = vld.tipo
                                AND lancamento_despesa.sequencia    = vld.sequencia
                                AND lancamento_despesa.cod_entidade = vld.cod_entidade )   
                            
                            LEFT JOIN contabilidade.lote as cld
                                ON( cld.cod_lote     = lancamento_despesa.cod_lote
                                AND cld.exercicio    = lancamento_despesa.exercicio
                                AND cld.tipo         = lancamento_despesa.tipo
                                AND cld.cod_entidade = lancamento_despesa.cod_entidade )                                                                                                               
                            
                            ,contabilidade.plano_analitica as pac                                                                               
                            
                            LEFT JOIN contabilidade.conta_credito as cc                                                                                
                                ON( pac.cod_plano   = cc.cod_plano 
                                AND pac.exercicio   = cc.exercicio 
                                AND cc.tipo         = 'M' 
                                AND cc.cod_entidade = ".$this->getDado('cod_entidade').")         
                            
                            LEFT JOIN contabilidade.valor_lancamento as vlc                                                                            
                                ON( cc.cod_lote     = vlc.cod_lote                                                                              
                                AND cc.tipo         = vlc.tipo                                                                                      
                                AND cc.sequencia    = vlc.sequencia                                                                            
                                AND cc.exercicio    = vlc.exercicio                                                                            
                                AND cc.tipo_valor   = vlc.tipo_valor                                                                          
                                AND cc.cod_entidade = vlc.cod_entidade )
                                
                            LEFT JOIN contabilidade.lancamento as lancamento_credito
                                ON( lancamento_credito.exercicio    = vlc.exercicio
                                AND lancamento_credito.cod_lote     = vlc.cod_lote
                                AND lancamento_credito.tipo         = vlc.tipo
                                AND lancamento_credito.sequencia    = vlc.sequencia
                                AND lancamento_credito.cod_entidade = vlc.cod_entidade )  
                            
                            LEFT JOIN contabilidade.lote as clc
                                ON( clc.cod_lote     = vlc.cod_lote
                                AND clc.exercicio    = vlc.exercicio
                                AND clc.tipo         = vlc.tipo
                                AND clc.cod_entidade = vlc.cod_entidade )  
                                                                                                                             
                            WHERE pad.cod_plano = pac.cod_plano                                                                                   
                            AND pad.exercicio = pac.exercicio                                                                                   
                            AND ( cc.cod_entidade = ".$this->getDado('cod_entidade')." OR cd.cod_entidade = ".$this->getDado('cod_entidade').") 
                    
                    ) as tabela                                                                                                                 
                    ON( pa.cod_plano = tabela.cod_plano                                                                                     
                    AND pa.exercicio = tabela.exercicio )                                                                                                                         

                WHERE pc.cod_conta = pa.cod_conta                                                                                             
                AND   pc.exercicio = pa.exercicio                                                                                             
                AND   pc.exercicio = '".$this->getDado('exercicio')."'
                AND   tabela.cod_historico in (220,221,222,223)
                --AND   pa.natureza_saldo like 'C'
                AND ( 
                            ---Receita Bruta Orcada para o Exercicio
                               (pc.cod_estrutural like '%5.2.1.1.1.00.00.00.00.00%' and tabela.cod_historico = 220)
                            OR (pc.cod_estrutural like '%6.2.1.1.0.00.00.00.00.00%' and tabela.cod_historico = 220)    
                            
                            --Receita Dedutora Bruta Orcada para o Exercicio
                                --Fundeb
                            OR (pc.cod_estrutural like '%5.2.1.1.2.01.01.00.00.00%' and tabela.cod_historico = 222)
                                --Renuncia
                            OR (pc.cod_estrutural like '%5.2.1.1.2.02.00.00.00.00%' and tabela.cod_historico = 222)
                                --Outras Deducoes
                            OR (pc.cod_estrutural like '%5.2.1.1.2.99.00.00.00.00%' and tabela.cod_historico = 222)

                            --Despesa Prevista para o Exercicio
                            OR (pc.cod_estrutural like '%5.2.2.1.1.01.00.00.00.00%' and tabela.cod_historico = 221)
                            OR (pc.cod_estrutural like '%6.2.2.1.1.00.00.00.00.00%' and tabela.cod_historico = 221)
                            
                            --Receita Dedutora Somatorio dos Outros Campos
                            --OR (pc.cod_estrutural like '%6.2.1.1.0.00.00.00.00.00%' and tabela.cod_historico = 222)
                            
                            --Lancamentos de Abertura dos Recursos-Fontes
                            --OR (pc.cod_estrutural like '%7.2.1.1.1.00.01.00.00.00%' and tabela.cod_historico = 223)
                            --OR (pc.cod_estrutural like '%8.2.1.1.1.00.01.00.00.00%' and tabela.cod_historico = 223)
                        )
        ";
    return $stSQL;
}

/**
    * Seta dados para fazer o recuperaRelacionamentoPlanoBancoAnalitica
    * @access Public
    * @return String $stSql
*/
function montaRecuperaPlanoBancoAnalitica()
{
    $stSQL  = "SELECT                                                                                                                          \n ";
    $stSQL .= "     pc.cod_estrutural                                                                                                          \n ";
    $stSQL .= "    ,pc.exercicio                                                                                                               \n ";
    $stSQL .= "    ,pc.nom_conta                                                                                                               \n ";
    $stSQL .= "    ,pc.cod_conta                                                                                                               \n ";
    $stSQL .= "    ,publico.fn_mascarareduzida(pc.cod_estrutural) as cod_reduzido                                                              \n ";
    $stSQL .= "    ,tabela.vl_lancamento                                                                                                       \n ";
    $stSQL .= "    ,pa.cod_plano                                                                                                               \n ";
    $stSQL .= "    ,tabela.sequencia                                                                                                           \n ";
    $stSQL .= "    ,pa.natureza_saldo                                                                                                          \n ";
    $stSQL .= "    ,CASE WHEN                                                                                                                  \n ";
    $stSQL .= "     (SELECT cod_plano from contabilidade.plano_banco where exercicio = pc.exercicio and cod_plano=pa.cod_plano) IS NOT NULL    \n ";
    $stSQL .= "     THEN                                                                                                                       \n ";
    $stSQL .= "	        CASE WHEN (SELECT cod_entidade from contabilidade.plano_banco where exercicio = pc.exercicio and cod_plano=pa.cod_plano and cod_entidade=".$this->getDado('cod_entidade').") IS NOT NULL \n ";
    $stSQL .= "		THEN 'OK'                                                                                                              \n ";
    $stSQL .= "		ELSE 'NOK'                                                                                                             \n ";
    $stSQL .= "	        END	                                                                                                               \n ";
    $stSQL .= "     ELSE 'OK'                                                                                                                  \n ";
    $stSQL .= "     END as plano_banco                                                                                                         \n ";
    $stSQL .= "FROM                                                                                                                            \n ";
    $stSQL .= "    contabilidade.plano_conta       as pc                                                                                       \n ";
    $stSQL .= "   ,contabilidade.plano_analitica   as pa                                                                                       \n ";
    $stSQL .= "LEFT JOIN                                                                                                                       \n ";
    $stSQL .= "    (                                                                                                                           \n ";
    $stSQL .= "        SELECT CASE WHEN pac.cod_plano IS NOT NULL                                                                              \n ";
    $stSQL .= "                    THEN pac.cod_plano                                                                                          \n ";
    $stSQL .= "                    ELSE pad.cod_plano                                                                                          \n ";
    $stSQL .= "               END as cod_plano                                                                                                 \n ";
    $stSQL .= "               ,CASE WHEN cc.cod_entidade IS NOT NULL                                                                           \n ";
    $stSQL .= "                     THEN cc.cod_entidade                                                                                       \n ";
    $stSQL .= "                     ELSE cd.cod_entidade                                                                                       \n ";
    $stSQL .= "                END as cod_entidade                                                                                             \n ";
    $stSQL .= "                ,CASE WHEN vlc.vl_lancamento IS NOT NULL                                                                        \n ";
    $stSQL .= "                      THEN vlc.vl_lancamento                                                                                    \n ";
    $stSQL .= "                      ELSE vld.vl_lancamento                                                                                    \n ";
    $stSQL .= "                END as vl_lancamento                                                                                            \n ";
    $stSQL .= "                ,CASE WHEN vlc.sequencia IS NOT NULL                                                                            \n ";
    $stSQL .= "                      THEN vlc.sequencia                                                                                        \n ";
    $stSQL .= "                      ELSE vld.sequencia                                                                                        \n ";
    $stSQL .= "                 END as sequencia                                                                                               \n ";
    $stSQL .= "                ,CASE WHEN cc.cod_entidade IS NOT NULL                                                                          \n ";
    $stSQL .= "                      THEN cc.cod_entidade                                                                                      \n ";
    $stSQL .= "                      ELSE cd.cod_entidade                                                                                      \n ";
    $stSQL .= "                 END as cod_entidade                                                                                            \n ";
    $stSQL .= "                ,CASE WHEN cc.exercicio IS NOT NULL                                                                             \n ";
    $stSQL .= "                      THEN cc.exercicio                                                                                         \n ";
    $stSQL .= "                      ELSE cd.exercicio                                                                                         \n ";
    $stSQL .= "                 END as exercicio                                                                                               \n ";
    $stSQL .= "        FROM contabilidade.plano_analitica as pad                                                                               \n ";
    $stSQL .= "             LEFT JOIN                                                                                                        \n ";
    $stSQL .= "               contabilidade.conta_debito as cd                                                                                 \n ";
    $stSQL .= "             ON ( pad.cod_plano = cd.cod_plano AND pad.exercicio = cd.exercicio AND cd.tipo = 'I' AND cd.cod_lote = 1 AND cd.cod_entidade = ".$this->getDado('cod_entidade').")         \n ";
    $stSQL .= "             LEFT JOIN                                                                                                        \n ";
    $stSQL .= "               contabilidade.valor_lancamento as vld                                                                            \n ";
    $stSQL .= "             ON (                                                                                                               \n ";
    $stSQL .= "                        cd.cod_lote = vld.cod_lote                                                                              \n ";
    $stSQL .= "                    AND cd.tipo = vld.tipo                                                                                      \n ";
    $stSQL .= "                    AND cd.sequencia = vld.sequencia                                                                            \n ";
    $stSQL .= "                    AND cd.exercicio = vld.exercicio                                                                            \n ";
    $stSQL .= "                    AND cd.tipo_valor = vld.tipo_valor                                                                          \n ";
    $stSQL .= "                    AND cd.cod_entidade = vld.cod_entidade                                                                      \n ";
    $stSQL .= "                )                                                                                                               \n ";
    $stSQL .= "            ,contabilidade.plano_analitica as pac                                                                               \n ";
    $stSQL .= "             LEFT JOIN                                                                                                          \n ";
    $stSQL .= "               contabilidade.conta_credito as cc                                                                                \n ";
    $stSQL .= "             ON ( pac.cod_plano = cc.cod_plano AND pac.exercicio = cc.exercicio AND cc.tipo = 'I' AND cc.cod_lote = 1 AND cc.cod_entidade = ".$this->getDado('cod_entidade').")         \n ";
    $stSQL .= "             LEFT JOIN                                                                                                          \n ";
    $stSQL .= "               contabilidade.valor_lancamento as vlc                                                                            \n ";
    $stSQL .= "             ON (                                                                                                               \n ";
    $stSQL .= "                        cc.cod_lote = vlc.cod_lote                                                                              \n ";
    $stSQL .= "                    AND cc.tipo = vlc.tipo                                                                                      \n ";
    $stSQL .= "                    AND cc.sequencia = vlc.sequencia                                                                            \n ";
    $stSQL .= "                    AND cc.exercicio = vlc.exercicio                                                                            \n ";
    $stSQL .= "                    AND cc.tipo_valor = vlc.tipo_valor                                                                          \n ";
    $stSQL .= "                    AND cc.cod_entidade = vlc.cod_entidade                                                                      \n ";
    $stSQL .= "                )                                                                                                               \n ";
    $stSQL .= "                                                                                                                                \n ";
    $stSQL .= "        WHERE   pad.cod_plano = pac.cod_plano                                                                                   \n ";
    $stSQL .= "        AND     pad.exercicio = pac.exercicio                                                                                   \n ";
    $stSQL .= "        AND     ( cc.cod_entidade = ".$this->getDado('cod_entidade')." OR cd.cod_entidade = ".$this->getDado('cod_entidade').") \n ";
    $stSQL .= "    ) as tabela                                                                                                                 \n ";
    $stSQL .= "    ON(                                                                                                                         \n ";
    $stSQL .= "            pa.cod_plano = tabela.cod_plano                                                                                     \n ";
    $stSQL .= "        AND pa.exercicio = tabela.exercicio                                                                                     \n ";
    $stSQL .= "      )                                                                                                                         \n ";
    $stSQL .= "WHERE   pc.cod_conta = pa.cod_conta                                                                                             \n ";
    $stSQL .= "AND     pc.exercicio = pa.exercicio                                                                                             \n ";

    return $stSQL;
}

    /**
        * Seta dados para fazer o recuperaRelacionamento
        * @access Public
        * @return String $stSql
    */
    public function montaRecuperaRelacionamentoRecurso()
    {
        $stSQL = "
          SELECT pa.cod_plano
               , pa.exercicio
               , pa.cod_conta
               , pr.cod_recurso
               , pr.tipo
               , pa.natureza_saldo
               , pr.cod_recurso_contrapartida
            FROM contabilidade.plano_analitica AS pa
       LEFT JOIN (
                  SELECT pr.cod_plano
                       , pr.exercicio
                       , r.cod_recurso
                       , r.tipo
                       , pr.cod_recurso_contrapartida
                    FROM contabilidade.plano_recurso AS pr
              INNER JOIN orcamento.recurso('".$this->getDado('exercicio')."') AS r
                      ON pr.cod_recurso = r.cod_recurso
                     AND pr.exercicio   = r.exercicio
                 ) AS pr
              ON pa.cod_plano = pr.cod_plano
             AND pa.exercicio = pr.exercicio
        ";
    
        return $stSQL;
    }

    /**
        * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método
        * montaRecuperaRelacionamentoRecurso.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaRelacionamentoRecurso(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRelacionamentoRecurso().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método
    * montaRecuperaPlanoContaAnalitica.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaPlanoContaAnalitica(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaPlanoContaAnalitica().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaContasAberturaOrcamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaContasAberturaOrcamento().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método
    * montaRecuperaPlanoBancoAnalitica.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaPlanoBancoAnalitica(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaPlanoBancoAnalitica().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaCodPlanoPorEstrutural(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaCodPlanoPorEstrutural().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta Sql para recuperaRelacionamento
    * @access public
    * @return String $stSql
*/
function montaRecuperaCodPlanoPorEstrutural()
{
    $stSql  = " SELECT                                              \n";
    $stSql .= "     pa.cod_plano                                    \n";
    $stSql .= " FROM                                                \n";
    $stSql .= "     contabilidade.plano_analitica as pa,            \n";
    $stSql .= "     contabilidade.plano_conta as pc                 \n";
    $stSql .= " WHERE                                               \n";
    $stSql .= "     pa.cod_conta = pc.cod_conta AND                 \n";
    $stSql .= "     pa.exercicio = pc.exercicio AND                 \n";
    $stSql .= "     pc.cod_estrutural = '".$this->getDado('cod_estrutural')."' AND  \n";
    $stSql .= "     pc.exercicio = '".$this->getDado('exercicio')."'\n";

    return $stSql;
}

/**
    * Monta Sql para recuperaRelacionamento
    * @access public
    * @return String $stSql
*/
function montaRecuperaRelacionamentoEntidade()
{
    $stSql .= " SELECT                                                  \n";
    $stSql .= "     E.*,                                                \n";
    $stSql .= "     CGM.numcgm,                                         \n";
    $stSql .= "     CGM.nom_cgm,                                        \n";
    $stSql .= "     contabilidade.fn_soma_valor_contabil(               \n";
    $stSql .= "    '".$this->getDado('exercicio')."',                   \n";
    $stSql .= "    ".$this->getDado('cod_plano').",                     \n";
    $stSql .= "    '".$this->getDado('cod_estrutural')."',              \n";
    $stSql .= "    'S',                                                 \n";
    $stSql .= "    '01/01/".$this->getDado('exercicio')."',             \n";
    $stSql .= "    '31/12/".$this->getDado('exercicio')."',             \n";
    $stSql .= "    cast( E.cod_entidade as varchar ),                   \n";
    $stSql .= "    true                                                 \n";
    $stSql .= "     ) as valor                                          \n";

    if ($this->getDado('dtSaldo')) {
        $stSql .= "     , contabilidade.fn_soma_valor_contabil(               \n";
        $stSql .= "    '".$this->getDado('exercicio')."',                   \n";
        $stSql .= "    ".$this->getDado('cod_plano').",                     \n";
        $stSql .= "    '".$this->getDado('cod_estrutural')."',              \n";
        $stSql .= "    'S',                                                 \n";
        $stSql .= "    '01/01/".$this->getDado('exercicio')."',             \n";
        $stSql .= "    '".$this->getDado('dtSaldo')."',                     \n";
        $stSql .= "    cast( E.cod_entidade as varchar ),                   \n";
        $stSql .= "    true                                                 \n";
        $stSql .= "     ) as valor_dt_saldo                                 \n";
    }
    $stSql .= " FROM                                                    \n";
    $stSql .= "     orcamento.entidade             AS E,               \n";
    $stSql .= "     sw_cgm                         AS CGM,             \n";
    $stSql .= "     orcamento.usuario_entidade     AS U                \n";
    $stSql .= " WHERE                                                   \n";
    $stSql .= "     E.numcgm = CGM.numcgm            AND                \n";
    $stSql .= "     E.cod_entidade = U.cod_entidade  AND                \n";
    $stSql .= "     E.exercicio    = U.exercicio                        \n";

    return $stSql;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método
    * montaRecuperaRelacionamentoEntidade.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoEntidade(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoEntidade().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método
    * montaRecuperaRelacionamentoEntidade.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoContaCredito(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoContaCredito($stCondicao).$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta Sql para recuperaRelacionamento
    * @access public
    * @return String $stSql
*/
function montaRecuperaRelacionamentoContaCredito($stFiltro = "")
{
    $stSql = "
    SELECT  CPA.cod_plano
      , CPC.nom_conta
      , CPAC.cod_credito
      , CPAC.cod_especie
      , CPAC.cod_genero
      , CPAC.cod_natureza
      , MC.descricao_credito
      , null as cod_acrescimo
      , null as cod_tipo
      , null as descricao_acrescimo
    FROM contabilidade.plano_conta      AS CPC
         INNER JOIN contabilidade.plano_analitica  AS CPA
                 ON CPC.exercicio = CPA.exercicio
                AND CPC.cod_conta = CPA.cod_conta
         inner join orcamento.conta_receita
                 on conta_receita.cod_estrutural = substring( CPC.cod_estrutural, 3)
                and conta_receita.exercicio = CPC.exercicio
         inner join orcamento.receita
                 on receita.cod_conta = conta_receita.cod_conta
                and receita.exercicio = conta_receita.exercicio

          LEFT JOIN contabilidade.plano_analitica_credito AS CPAC
         ON( CPA.exercicio = CPAC.exercicio
         AND CPA.cod_plano = CPAC.cod_plano )
          LEFT JOIN monetario.credito AS MC
         ON( CPAC.cod_credito  = MC.cod_credito
         AND CPAC.cod_especie  = MC.cod_especie
         AND CPAC.cod_genero   = MC.cod_genero
         AND CPAC.cod_natureza = MC.cod_natureza )
         where 1=1
         ";
    $stSql .= " " . $stFiltro . " ";
    $stSql .= "
    UNION
    SELECT CPA.cod_plano
          ,CPC.nom_conta
          ,CPACA.cod_credito
          ,CPACA.cod_especie
          ,CPACA.cod_genero
          ,CPACA.cod_natureza
          , ( select descricao_credito
                from monetario.credito
               where cod_credito = CPACA.cod_credito
                 and cod_especie = CPACA.cod_especie
                 and cod_genero  = CPACA.cod_genero
                 and cod_natureza = CPACA.cod_natureza
            ) as descricao_credito
          ,MA.cod_acrescimo
          ,MA.cod_tipo
          ,MA.descricao_acrescimo
    FROM contabilidade.plano_conta      AS CPC
         INNER JOIN contabilidade.plano_analitica  AS CPA
                 ON CPC.exercicio = CPA.exercicio
                AND CPC.cod_conta = CPA.cod_conta
         INNER JOIN contabilidade.plano_analitica_credito_acrescimo AS CPACA
         ON( CPA.exercicio = CPACA.exercicio
         AND CPA.cod_plano = CPACA.cod_plano )
         INNER JOIN monetario.acrescimo AS MA
         ON( MA.cod_acrescimo  = CPACA.cod_acrescimo
         AND MA.cod_tipo  = CPACA.cod_tipo  )
         where 1=1 ";	// eh, fica feio, mas tu nao sabe a pressa que eu estava quando fiz isso
    $stSql .= " " . $stFiltro . " ";

    return $stSql;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método
    * montaRecuperaSaldoContaAnalitica.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaSaldoContaAnalitica(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaSaldoContaAnalitica().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta Sql para recuperaRelacionamento - Utilizado para gerar saldos de balanço para o próximo exercicio
    * @access public
    * @return String $stSql
*/

function montaRecuperaSaldoContaAnalitica()
{
    $stSql  = " SELECT * FROM (                                                             \n";
    $stSql .= "     SELECT                                                                  \n";
    $stSql .= "         cod_plano,                                                          \n";
    $stSql .= "         cod_estrutural,                                                     \n";
    $stSql .= "         cod_entidade,                                                       \n";
    $stSql .= "         coalesce(sum(vl_lancamento),0.00) as saldo                          \n";
    $stSql .= "     FROM(                                                                   \n";
    $stSql .= "         SELECT                                                              \n";
    $stSql .= "             cd.cod_plano,                                                   \n";
    $stSql .= "             pc.cod_estrutural,                                              \n";
    $stSql .= "             cd.cod_entidade,                                                \n";
    $stSql .= "             coalesce(sum(vl.vl_lancamento),0.00) as vl_lancamento,          \n";
    $stSql .= "             cd.tipo_valor                                                   \n";
    $stSql .= "         FROM                                                                \n";
    $stSql .= "             contabilidade.plano_conta       as pc,                          \n";
    $stSql .= "             contabilidade.plano_analitica   as pa,                          \n";
    $stSql .= "             contabilidade.conta_debito      as cd,                          \n";
    $stSql .= "             contabilidade.valor_lancamento  as vl,                          \n";
    $stSql .= "             contabilidade.lancamento        as la,                          \n";
    $stSql .= "             contabilidade.lote              as lo                           \n";
    $stSql .= "         WHERE   pc.cod_conta    = pa.cod_conta                              \n";
    $stSql .= "         AND     pc.exercicio    = pa.exercicio                              \n";
    $stSql .= "         AND     pa.cod_plano    = cd.cod_plano                              \n";
    $stSql .= "         AND     pa.exercicio    = cd.exercicio                              \n";
    $stSql .= "         AND     cd.cod_lote     = vl.cod_lote                               \n";
    $stSql .= "         AND     cd.tipo         = vl.tipo                                   \n";
    $stSql .= "         AND     cd.sequencia    = vl.sequencia                              \n";
    $stSql .= "         AND     cd.exercicio    = vl.exercicio                              \n";
    $stSql .= "         AND     cd.tipo_valor   = vl.tipo_valor                             \n";
    $stSql .= "         AND     cd.cod_entidade = vl.cod_entidade                           \n";
    $stSql .= "         AND     vl.cod_lote     = la.cod_lote                               \n";
    $stSql .= "         AND     vl.tipo         = la.tipo                                   \n";
    $stSql .= "         AND     vl.sequencia    = la.sequencia                              \n";
    $stSql .= "         AND     vl.exercicio    = la.exercicio                              \n";
    $stSql .= "         AND     vl.cod_entidade = la.cod_entidade                           \n";
    $stSql .= "         AND     la.cod_lote     = lo.cod_lote                               \n";
    $stSql .= "         AND     la.tipo         = lo.tipo                                   \n";
    $stSql .= "         AND     la.exercicio    = lo.exercicio                              \n";
    $stSql .= "         AND     la.cod_entidade = lo.cod_entidade                           \n";
    $stSql .= "         AND lo.exercicio='".$this->getDado('exercicio')."'                  \n";
    $stSql .= "         AND lo.dt_lote >='".$this->getDado('exercicio')."-01-01'            \n";
    $stSql .= "         AND lo.dt_lote <='".$this->getDado('exercicio')."-12-31'            \n";
    $stSql .= "         GROUP BY pc.cod_estrutural,cd.cod_plano,cd.cod_entidade,cd.tipo_valor \n";
    $stSql .= "         UNION                                                               \n";
    $stSql .= "         SELECT                                                              \n";
    $stSql .= "             cd.cod_plano,                                                   \n";
    $stSql .= "             pc.cod_estrutural,                                              \n";
    $stSql .= "             cd.cod_entidade,                                                \n";
    $stSql .= "             coalesce(sum(vl.vl_lancamento),0.00) as vl_lancamento,          \n";
    $stSql .= "             cd.tipo_valor                                                   \n";
    $stSql .= "         FROM                                                                \n";
    $stSql .= "             contabilidade.plano_conta       as pc,                          \n";
    $stSql .= "             contabilidade.plano_analitica   as pa,                          \n";
    $stSql .= "             contabilidade.conta_credito     as cd,                          \n";
    $stSql .= "             contabilidade.valor_lancamento  as vl,                          \n";
    $stSql .= "             contabilidade.lancamento        as la,                          \n";
    $stSql .= "             contabilidade.lote              as lo                           \n";
    $stSql .= "         WHERE   pc.cod_conta    = pa.cod_conta                              \n";
    $stSql .= "         AND     pc.exercicio    = pa.exercicio                              \n";
    $stSql .= "         AND     pa.cod_plano    = cd.cod_plano                              \n";
    $stSql .= "         AND     pa.exercicio    = cd.exercicio                              \n";
    $stSql .= "         AND     cd.cod_lote     = vl.cod_lote                               \n";
    $stSql .= "         AND     cd.tipo         = vl.tipo                                   \n";
    $stSql .= "         AND     cd.sequencia    = vl.sequencia                              \n";
    $stSql .= "         AND     cd.exercicio    = vl.exercicio                              \n";
    $stSql .= "         AND     cd.tipo_valor   = vl.tipo_valor                             \n";
    $stSql .= "         AND     cd.cod_entidade = vl.cod_entidade                           \n";
    $stSql .= "         AND     vl.cod_lote     = la.cod_lote                               \n";
    $stSql .= "         AND     vl.tipo         = la.tipo                                   \n";
    $stSql .= "         AND     vl.sequencia    = la.sequencia                              \n";
    $stSql .= "         AND     vl.exercicio    = la.exercicio                              \n";
    $stSql .= "         AND     vl.cod_entidade = la.cod_entidade                           \n";
    $stSql .= "         AND     la.cod_lote     = lo.cod_lote                               \n";
    $stSql .= "         AND     la.tipo         = lo.tipo                                   \n";
    $stSql .= "         AND     la.exercicio    = lo.exercicio                              \n";
    $stSql .= "         AND     la.cod_entidade = lo.cod_entidade                           \n";
    $stSql .= "         AND lo.exercicio='".$this->getDado('exercicio')."'                  \n";
    $stSql .= "         AND lo.dt_lote >='".$this->getDado('exercicio')."-01-01'            \n";
    $stSql .= "         AND lo.dt_lote <='".$this->getDado('exercicio')."-12-31'            \n";
    $stSql .= "         GROUP BY pc.cod_estrutural,cd.cod_plano,cd.cod_entidade,cd.tipo_valor \n";
    $stSql .= "     ) as tabela                                                             \n";
    $stSql .= "     GROUP BY cod_estrutural,cod_plano,cod_entidade                          \n";
    $stSql .= "     ORDER BY cod_plano                                                      \n";
    $stSql .= " ) AS tabela                                                                 \n";
    $stSql .= " WHERE saldo <> 0.00                                                         \n";

    return $stSql;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método
    * montaRecuperaContaBorderoTransferencia.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaContaBorderoTransferencia(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaContaBorderoTransferencia().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta Sql para recuperaRelacionamento - Utilizado para contas para o Bordero de Transferencia
    * @access public
    * @return String $stSql
*/

function montaRecuperaContaBorderoTransferencia()
{
    $stSql  = "SELECT                                                                                   \n";
    $stSql .= "    tabela.cod_plano,                                                                    \n";
    $stSql .= "    tabela.nom_conta,                                                                    \n";
    $stSql .= "    tabela.cod_estrutural,                                                               \n";
    $stSql .= "    tabela.exercicio,                                                                    \n";
    $stSql .= "    tabela.cod_entidade                                                                  \n";
    $stSql .= "    tabela.natureza_saldo                                         \n";
    $stSql .= "FROM (                                                                                   \n";
    $stSql .= "    SELECT                                                                               \n";
    $stSql .= "        pa.cod_plano,                                                                    \n";
    $stSql .= "        pc.nom_conta,                                                                    \n";
    $stSql .= "        pc.cod_estrutural,                                                               \n";
    $stSql .= "        pa.exercicio,                                                                    \n";
    $stSql .= "        pb.cod_entidade,                                                                 \n";
    $stSql .= "        pa.natureza_saldo                                         \n";
    $stSql .= "    FROM                                                                                 \n";
    $stSql .= "        contabilidade.plano_conta              AS pc                                     \n";
    $stSql .= "        INNER JOIN                                                                       \n";
    $stSql .= "           contabilidade.plano_analitica       AS pa                                     \n";
    $stSql .= "        ON ( pc.exercicio = pa.exercicio and pc.cod_conta = pa.cod_conta )               \n";
    $stSql .= "        INNER JOIN                                                                       \n";
    $stSql .= "           contabilidade.plano_banco           AS pb                                     \n";
    $stSql .= "        ON ( pa.exercicio = pb.exercicio and pa.cod_plano = pb.cod_plano )               \n";
    $stSql .= "                                                                                         \n";
    $stSql .= "    UNION                                                                                \n";
    $stSql .= "                                                                                         \n";
    $stSql .= "    SELECT                                                                               \n";
    $stSql .= "        pa.cod_plano,                                                                    \n";
    $stSql .= "        pc.nom_conta,                                                                    \n";
    $stSql .= "        pc.cod_estrutural,                                                               \n";
    $stSql .= "        pa.exercicio,                                                                    \n";
    $stSql .= "        pb.cod_entidade,                                                                 \n";
    $stSql .= "        pa.natureza_saldo                                         \n";
    $stSql .= "    FROM                                                                                 \n";
    $stSql .= "        contabilidade.plano_conta              AS pc                                     \n";
    $stSql .= "        INNER JOIN                                                                       \n";
    $stSql .= "           contabilidade.plano_analitica       AS pa                                     \n";
    $stSql .= "        ON ( pc.exercicio = pa.exercicio and pc.cod_conta = pa.cod_conta )               \n";
    $stSql .= "        LEFT  JOIN                                                                       \n";
    $stSql .= "           contabilidade.plano_banco           AS pb                                     \n";
    $stSql .= "        ON ( pa.exercicio = pb.exercicio and pa.cod_plano = pb.cod_plano )               \n";
    $stSql .= "    WHERE                                                                                \n";
    $stSql .= "        pb.cod_plano is null                                                             \n";
    $stSql .= "                                                                                         \n";
    $stSql .= ") AS tabela                                                                              \n";

    return $stSql;
}
/**
    * Seta dados para fazer o recuperaRelacionamento
    * @access Public
    * @return String $stSql
*/
function montaRecuperaContaAnalitica()
{
    $stSQL  =" SELECT                                                   \n";
    $stSQL .="     pa.cod_plano,                                        \n";
    $stSQL .="     pc.exercicio,                                        \n";
    $stSQL .="     pc.cod_conta,                                        \n";
    $stSQL .="     pc.nom_conta,                                        \n";
    $stSQL .="     pc.cod_estrutural,                                   \n";
    $stSQL .="     pa.natureza_saldo                                    \n";
    $stSQL .=" FROM                                                     \n";
    $stSQL .="     contabilidade.plano_conta     as pc,                 \n";
    $stSQL .="     contabilidade.plano_analitica as pa                  \n";
    $stSQL .=" WHERE                                                    \n";
    $stSQL .="     pc.cod_conta  = pa.cod_conta                         \n";
    $stSQL .=" AND pc.exercicio  = pa.exercicio                         \n";

    return $stSQL;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método
    * montaRecuperaContaAnalitica.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaContaAnalitica(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaContaAnalitica().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
