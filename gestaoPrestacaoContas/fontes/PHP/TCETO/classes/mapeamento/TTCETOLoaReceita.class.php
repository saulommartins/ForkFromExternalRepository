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
    * Extensão da Classe de Mapeamento TTCETOLoaReceita
    *
    * Data de Criação: 10/11/2014
    *
    * @author: Evandro Melos
    *
    * $Id: TTCETOLoaReceita.class.php 60797 2014-11-17 15:26:38Z evandro $
    *
    * @ignore
    *
*/
class TTCETOLoaReceita extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCETOLoaReceita()
    {
        parent::Persistente();
        $this->setTabela('tceto.fn_orcamento_loa_receita');
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
    public function recuperaReceita(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaReceita().$stCondicao.$stOrdem;        
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaReceita()
    {
        $stSql  = " SELECT DISTINCT
                             cod_und_gestora
                            , cod_und_orcamentaria
                            , cod_orgao
                            , cod_recurso
                            , cod_receita
                            , vl_original AS vl_receita
                            , TRIM(descricao) as descricao
                            , tipo
                            , nivel
                            , COALESCE(meta_1b, 0.00) AS meta_1b
                            , COALESCE(meta_2b, 0.00) AS meta_2b
                            , COALESCE(meta_3b, 0.00) AS meta_3b
                            , COALESCE(meta_4b, 0.00) AS meta_4b
                            , COALESCE(meta_5b, 0.00) AS meta_5b
                            , COALESCE(meta_6b, 0.00) AS meta_6b
                            , '51211000000000000' as conta_contabil                                         
                    FROM (    SELECT
                                        (SELECT PJ.cnpj
                                            FROM orcamento.entidade
                                            JOIN sw_cgm
                                              ON sw_cgm.numcgm = entidade.numcgm
                                            JOIN sw_cgm_pessoa_juridica AS PJ
                                              ON sw_cgm.numcgm = PJ.numcgm
                                           WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                             AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                                         ) AS cod_und_gestora
                                        , LPAD(tceto.recupera_codigo_orgao('".$this->getDado('exercicio')."',receita.cod_entidade, 'orgao')::varchar,2,'0') AS cod_orgao
                                        , LPAD(tceto.recupera_codigo_orgao('".$this->getDado('exercicio')."',receita.cod_entidade, 'unidade')::varchar,4,'0') AS cod_und_orcamentaria
                                        , recurso.cod_recurso
                                        , REPLACE(conta_receita.cod_estrutural::VARCHAR,'.','') AS cod_receita
                                        , receita.vl_original
                                        , conta_receita.descricao
                                        , orcamento.fn_tipo_conta_receita('".$this->getDado('exercicio')."', conta_receita.cod_estrutural) as tipo
                                        , publico.fn_nivel(conta_receita.cod_estrutural) as nivel   
                                        , (SELECT vl_periodo FROM orcamento.previsao_receita WHERE exercicio = '".$this->getDado('exercicio')."'
                                                                              AND cod_receita = receita.cod_receita
                                                                              AND periodo = 1
                                        ) AS meta_1b
                                        , (SELECT vl_periodo FROM orcamento.previsao_receita WHERE exercicio = '".$this->getDado('exercicio')."'
                                                                              AND cod_receita = receita.cod_receita
                                                                              AND periodo = 2
                                        ) AS meta_2b
                                        , (SELECT vl_periodo FROM orcamento.previsao_receita WHERE exercicio = '".$this->getDado('exercicio')."'
                                                                              AND cod_receita = receita.cod_receita
                                                                              AND periodo = 3
                                        ) AS meta_3b
                                        , (SELECT vl_periodo FROM orcamento.previsao_receita WHERE exercicio = '".$this->getDado('exercicio')."'
                                                                              AND cod_receita = receita.cod_receita
                                                                              AND periodo = 4
                                        ) AS meta_4b
                                        , (SELECT vl_periodo FROM orcamento.previsao_receita WHERE exercicio = '".$this->getDado('exercicio')."'
                                                                              AND cod_receita = receita.cod_receita
                                                                              AND periodo = 5
                                        ) AS meta_5b
                                        , (SELECT vl_periodo FROM orcamento.previsao_receita WHERE exercicio = '".$this->getDado('exercicio')."'
                                                                              AND cod_receita = receita.cod_receita
                                                                              AND periodo = 6
                                        ) AS meta_6b                                        

                                FROM orcamento.receita
                                                              
                                JOIN orcamento.conta_receita 
                                        ON conta_receita.exercicio  = receita.exercicio
                                        AND conta_receita.cod_conta = receita.cod_conta

                                JOIN orcamento.recurso('".$this->getDado('exercicio')."')
                                        ON recurso.exercicio    = receita.exercicio
                                        AND recurso.cod_recurso = receita.cod_recurso
                                WHERE receita.cod_entidade = ".$this->getDado('cod_entidade')."
                            
                            ) AS tabela
                    ORDER BY cod_recurso, cod_receita
                    
        ";
        
        return $stSql;
    }
}
?>
