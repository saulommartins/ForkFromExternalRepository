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
   /*
    * Classe de Mapeamento da tabela tcemg.cronograma_execucao_mensal_desembolso
    * Data de Criação   : 29/02/2016

    * @author Analista      Ane Caroline
    * @author Desenvolvedor Lisiane Morais

    * @package URBEM
    * @subpackage

    * $Id:$
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TTCEMGCronogramaExecucaoMensalDesembolso extends Persistente
{
    public function TTCEMGCronogramaExecucaoMensalDesembolso()
    {
        parent::Persistente();
        $this->setTabela("tcemg.cronograma_execucao_mensal_desembolso");
        
        $this->setCampoCod('cod_grupo');
        $this->setComplementoChave('num_orgao,periodo,cod_entidade,exercicio,num_unidade');
        
        $this->AddCampo('cod_grupo'   ,'integer' , true,''       ,true  ,true   );
        $this->AddCampo('periodo'     ,'integer' , true,''       ,true  ,false  );
        $this->AddCampo('cod_entidade','integer' , true,''       ,true  ,false  );
        $this->AddCampo('exercicio'   ,'char'    , true,''       ,true  ,false  );
        $this->AddCampo('num_orgao'   ,'integer' , true,''       ,true  ,true   );
        $this->AddCampo('num_unidade' ,'integer' , true, ''      ,true , true   );
        $this->AddCampo('valor'       ,'numeric' , true  , '14,2',false ,false  );
        
    }

    public function listarPeriodo(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = "ORDER BY uniorcam.num_orgao, uniorcam.num_unidade, grupos_despesa.cod_grupo";
        $stSql = $this->montaListarPeriodo().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListarPeriodo()
    {
        $stSql  = " 
                  SELECT grupos_despesa.cod_grupo 
                       , cronem.cod_entidade
                       , cronem.exercicio
                       , cronem.periodo
                       , cronem.valor
                       , uniorcam.num_unidade
                       , uniorcam.num_orgao
                    FROM tcemg.grupos_despesa
                    JOIN tcemg.cronograma_execucao_mensal_desembolso AS cronem
                      ON cronem.cod_grupo = grupos_despesa.cod_grupo
                    JOIN tcemg.uniorcam
                      ON cronem.num_unidade = uniorcam.num_unidade
                     AND cronem.num_orgao = uniorcam.num_orgao
                     AND cronem.exercicio = uniorcam.exercicio
                    JOIN orcamento.orgao 
                      ON orgao.num_orgao = uniorcam.num_orgao
                     AND orgao.exercicio = uniorcam.exercicio
                    JOIN orcamento.unidade
                      ON unidade.num_unidade = uniorcam.num_unidade
                     AND unidade.num_orgao = uniorcam.num_orgao
                     AND unidade.exercicio = uniorcam.exercicio
                   WHERE cronem.exercicio = '".$this->getDado('exercicio')."'
                     AND uniorcam.num_orgao = ".$this->getDado('num_orgao')."
                     AND cronem.cod_entidade = ".$this->getDado('cod_entidade')."
                     AND uniorcam.num_unidade = ".$this->getDado('num_unidade')." 
                GROUP BY uniorcam.num_orgao
                       , uniorcam.num_unidade
                       , grupos_despesa.cod_grupo 
                       , cronem.cod_entidade
                       , cronem.exercicio
                       , cronem.valor
                       , cronem.periodo
            ";
        return $stSql;
    }
    
     public function insereNovosGruposPeriodo($boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaInsereNovosGruposPeriodo();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
     public function montaInsereNovosGruposPeriodo()
    {
         $stSql = "
            INSERT INTO tcemg.cronograma_execucao_mensal_desembolso VALUES ( 1,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",'".$this->getDado('exercicio')."',".$this->getDado('num_unidade').",".$this->getDado('num_orgao').", 0.00 );
            INSERT INTO tcemg.cronograma_execucao_mensal_desembolso VALUES ( 2,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",'".$this->getDado('exercicio')."',".$this->getDado('num_unidade').",".$this->getDado('num_orgao').", 0.00 );
            INSERT INTO tcemg.cronograma_execucao_mensal_desembolso VALUES ( 3,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",'".$this->getDado('exercicio')."',".$this->getDado('num_unidade').",".$this->getDado('num_orgao').", 0.00 );
            INSERT INTO tcemg.cronograma_execucao_mensal_desembolso VALUES ( 4,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",'".$this->getDado('exercicio')."',".$this->getDado('num_unidade').",".$this->getDado('num_orgao').", 0.00 );
            INSERT INTO tcemg.cronograma_execucao_mensal_desembolso VALUES ( 5,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",'".$this->getDado('exercicio')."',".$this->getDado('num_unidade').",".$this->getDado('num_orgao').", 0.00 );
            INSERT INTO tcemg.cronograma_execucao_mensal_desembolso VALUES ( 6,".$this->getDado('periodo').",".$this->getDado('cod_entidade').",'".$this->getDado('exercicio')."',".$this->getDado('num_unidade').",".$this->getDado('num_orgao').", 0.00 );
            ";
        return $stSql;
    }
    
     public function recuperaOrgaoUnidade(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = "ORDER BY uniorcam.num_orgao, uniorcam.num_unidade";
        $stSql = $this->montaRecuperaOrgaoUnidade().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
        
    public function montaRecuperaOrgaoUnidade()
    {
        $stSql = " SELECT DISTINCT uniorcam.num_orgao
                        , uniorcam.num_unidade
                        , uniorcam.exercicio
                        , orgao.nom_orgao 
                        , unidade.nom_unidade
                    FROM tcemg.uniorcam
                    JOIN orcamento.orgao 
                      ON orgao.num_orgao = uniorcam.num_orgao
                     AND orgao.exercicio = uniorcam.exercicio
                    JOIN orcamento.unidade
                      ON unidade.num_unidade = uniorcam.num_unidade
                     AND unidade.num_orgao = uniorcam.num_orgao
                     AND unidade.exercicio = uniorcam.exercicio
                   WHERE uniorcam.exercicio = '".$this->getDado('exercicio')."'
                     AND uniorcam.num_orgao = ".$this->getDado('num_orgao')."
                     AND uniorcam.num_unidade = ".$this->getDado('num_unidade')."
                ";
        
        return $stSql;
    }
    
     public function recuperaGruposDespesa(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = "ORDER BY grupos_despesa.cod_grupo";
        $stSql = $this->montaRecuperaGruposDespesa().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
        
    public function montaRecuperaGruposDespesa()
    {
        $stSql = " SELECT *
                    FROM tcemg.grupos_despesa
                ";
        
        return $stSql;
    }
    
     public function recuperaSaldoInicial(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = "";
        $stSql = $this->montaRecuperaSaldoInicial().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
        
    public function montaRecuperaSaldoInicial()
    {
        $stSql = " SELECT SUM(saldo_inicial)AS saldo_inicial
                        , num_orgao
                        , num_unidade
                    FROM (
                          SELECT DISTINCT cod_despesa
                               , COALESCE(saldo_inicial,0.00) AS saldo_inicial
                               , retorno.num_orgao
                               , retorno.num_unidade
                            FROM orcamento.fn_balancete_despesa('".$this->getDado('exercicio')."',' AND od.cod_entidade IN  (".$this->getDado('cod_entidade').")','01/01/".Sessao::getExercicio()."','31/12/".Sessao::getExercicio()."','','','','','' ,'".$this->getDado('num_orgao')."','".$this->getDado('num_unidade')."', '' )
                                                    as retorno( 
                                                                exercicio       char(4),                                                                                
                                                                cod_despesa     integer,                                                                                
                                                                cod_entidade    integer,                                                                                
                                                                cod_programa    integer,                                                                                
                                                                cod_conta       integer,                                                                                
                                                                num_pao         integer,                                                                                
                                                                num_orgao       integer,                                                                                
                                                                num_unidade     integer,                                                                                
                                                                cod_recurso     integer,                                                                                
                                                                cod_funcao      integer,                                                                                
                                                                cod_subfuncao   integer,                                                                                
                                                                tipo_conta      varchar,                                                                                
                                                                vl_original     numeric,                                                                                
                                                                dt_criacao      date,                                                                                   
                                                                classificacao   varchar,                                                                                
                                                                descricao       varchar,                                                                                
                                                                num_recurso     varchar,                                                                                
                                                                nom_recurso     varchar,                                                                                
                                                                nom_orgao       varchar,                                                                                
                                                                nom_unidade     varchar,                                                                                
                                                                nom_funcao      varchar,                                                                                
                                                                nom_subfuncao   varchar,                                                                                
                                                                nom_programa    varchar,                                                                                
                                                                nom_pao         varchar,                                                                                
                                                                empenhado_ano   numeric,                                                                                
                                                                empenhado_per   numeric,                                                                                 
                                                                anulado_ano     numeric,                                                                                
                                                                anulado_per     numeric,                                                                                
                                                                pago_ano        numeric,                                                                                
                                                                pago_per        numeric,                                                                                 
                                                                liquidado_ano   numeric,                                                                                
                                                                liquidado_per   numeric,                                                                                
                                                                saldo_inicial   numeric,                                                                                
                                                                suplementacoes  numeric,                                                                                
                                                                reducoes        numeric,                                                                                
                                                                total_creditos  numeric,                                                                                
                                                                credito_suplementar  numeric,                                                                            
                                                                credito_especial  numeric,                                                                            
                                                                credito_extraordinario  numeric,                                                                            
                                                                num_programa varchar,                                                                            
                                                                num_acao varchar                                                                            
                                                            )
                        ) AS t
                 GROUP BY num_orgao
                        , num_unidade
                ";
        return $stSql;
    }
        
    public function __destruct(){}

}
?>
