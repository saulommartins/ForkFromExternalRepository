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
    $Id: TTCEALCredor.class.php 58679 2014-06-16 19:36:57Z jean $
    *
    * @ignore
    *
*/
class TTCEALLoaReceita extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALLoaReceita()
    {
        parent::Persistente();
        $this->setTabela('tceal.fn_orcamento_loa_receita');
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
        $stSql  = "
                    SELECT
                              CASE WHEN codigo_ua <> ''
                                 THEN codigo_ua
                                 ELSE '0000'
                              END AS codigo_ua
                            , cod_und_gestora
                            , cod_und_orcamentaria
                            , cod_orgao
                            , LPAD(cod_recurso, 9, '0') AS cod_recurso
                            , RPAD(cod_receita, 16, '0') AS cod_receita
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
                          
                      FROM
                            (
                                SELECT
                                         (SELECT valor
                                            FROM administracao.configuracao_entidade
                                           WHERE exercicio    = '".$this->getDado('exercicio')."'
                                             AND cod_entidade = ".$this->getDado('und_gestora')."
                                             AND cod_modulo   = 62
                                             AND parametro    = 'tceal_configuracao_unidade_autonoma'
                                         ) AS codigo_ua
                                        , (SELECT PJ.cnpj
                                            FROM orcamento.entidade
                                            JOIN sw_cgm
                                              ON sw_cgm.numcgm = entidade.numcgm
                                            JOIN sw_cgm_pessoa_juridica AS PJ
                                              ON sw_cgm.numcgm = PJ.numcgm
                                           WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                             AND entidade.cod_entidade = ".$this->getDado('und_gestora')."
                                         ) AS cod_und_gestora
                                        , LPAD(tceal.recupera_codigo_orgao('".$this->getDado('exercicio')."',receita.cod_entidade, 'orgao')::varchar,2,'0') AS cod_orgao
                                        , LPAD(tceal.recupera_codigo_orgao('".$this->getDado('exercicio')."',receita.cod_entidade, 'unidade')::varchar,4,'0') AS cod_und_orcamentaria
                                        , tabela.cod_recurso
                                        , REPLACE(tabela.cod_estrutural::VARCHAR,'.','') AS cod_receita
                                        , tabela.vl_original
                                        , tabela.descricao
                                        , tabela.tipo
                                        , tabela.nivel
                                        , (SELECT vl_periodo FROM orcamento.previsao_receita WHERE exercicio = '".$this->getDado("exercicio")."'
                                                                              AND cod_receita = receita.cod_receita
                                                                              AND periodo = 1
                                        ) AS meta_1b
                                        , (SELECT vl_periodo FROM orcamento.previsao_receita WHERE exercicio = '".$this->getDado("exercicio")."'
                                                                              AND cod_receita = receita.cod_receita
                                                                              AND periodo = 2
                                        ) AS meta_2b
                                        , (SELECT vl_periodo FROM orcamento.previsao_receita WHERE exercicio = '".$this->getDado("exercicio")."'
                                                                              AND cod_receita = receita.cod_receita
                                                                              AND periodo = 3
                                        ) AS meta_3b
                                        , (SELECT vl_periodo FROM orcamento.previsao_receita WHERE exercicio = '".$this->getDado("exercicio")."'
                                                                              AND cod_receita = receita.cod_receita
                                                                              AND periodo = 4
                                        ) AS meta_4b
                                        , (SELECT vl_periodo FROM orcamento.previsao_receita WHERE exercicio = '".$this->getDado("exercicio")."'
                                                                              AND cod_receita = receita.cod_receita
                                                                              AND periodo = 5
                                        ) AS meta_5b
                                        , (SELECT vl_periodo FROM orcamento.previsao_receita WHERE exercicio = '".$this->getDado("exercicio")."'
                                                                              AND cod_receita = receita.cod_receita
                                                                              AND periodo = 6
                                        ) AS meta_6b

                                FROM ".$this->getTabela()."('".$this->getDado("exercicio")     ."',
                                                              '".$this->getDado("cod_entidade")  ."',
                                                              '".$this->getDado("dtInicial")     ."',
                                                              '".$this->getDado("dtFinal")       ."')
                                AS tabela( 
                                            cod_estrutural     varchar,           
                                            cod_recurso        varchar(13),
                                            cod_receita        integer,
                                            descricao          varchar,           
                                            vl_original        numeric,                     
                                            tipo               varchar,           
                                            nivel              integer,           
                                            cod_caracteristica integer
                                        )
                                                            
                                JOIN orcamento.receita
                                     ON receita.cod_receita = tabela.cod_receita
                                    AND receita.exercicio = '".$this->getDado("exercicio")."'
                                   
                                WHERE tabela.nivel <> 0
                                 
                                ORDER BY tabela.cod_estrutural
                            ) AS tabela
        ";
        
        return $stSql;
    }
}
?>
