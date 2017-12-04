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
    * Extensão da Classe de Mapeamento TTCETOProjetoAtividade
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Franver Sarmento de Moraes
    *
    * $Id: TTCETOLoaDespesa.class.php 60895 2014-11-21 13:34:45Z arthur $
    *
    * @ignore
    *
*/
class TTCETOLoaDespesa extends Persistente
{
    /**
        * Método Construtor
        * @access Public
    */
    public function TTCETOLoaDespesa()
    {
        parent::Persistente();
    }

    public function recuperaDespesa(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDespesa().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDespesa()
    {
        $stSql = "
            SELECT cod_und_gestora
                 , cod_orgao
                 , cod_unid_orcamentaria
                 , cod_funcao
                 , cod_subfuncao
                 , cod_programa
                 , cod_proj_atividade
                 , rubrica_despesa
                 , cod_rec_vinculado
                 , dotacao_inicial
                 , SUM(janeiro) as janeiro
                 , SUM(fevereiro) as fevereiro
                 , SUM(marco) AS marco
                 , SUM(abril) AS abril
                 , SUM(maio) AS maio
                 , SUM(junho) AS junho
                 , SUM(julho) AS julho
                 , SUM(agosto) AS agosto
                 , SUM(setembro) AS setembro
                 , SUM(outubro) AS outubro
                 , SUM(novembro) AS novembro
                 , SUM(dezembro) AS dezembro
                 
              FROM ( SELECT (SELECT PJ.cnpj
                               FROM orcamento.entidade
                               JOIN sw_cgm
                                 ON sw_cgm.numcgm = entidade.numcgm
                               JOIN sw_cgm_pessoa_juridica AS PJ
                                 ON sw_cgm.numcgm = PJ.numcgm
                              WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                            ) AS cod_und_gestora
                          , LPAD(despesa.num_orgao::varchar,2,'0')                AS cod_orgao
                          , LPAD(despesa.num_unidade::varchar,4,'0')              AS cod_unid_orcamentaria 
                          , LPAD(despesa.cod_funcao::varchar,2,'0')               AS cod_funcao
                          , LPAD(despesa.cod_subfuncao::varchar,3,'0')            AS cod_subfuncao
                          , LPAD(p_programa.num_programa::varchar,4,'0')          AS cod_programa
                          , LPAD(num_acao::varchar,4,'0')                         AS cod_proj_atividade                                                                
                          , REPLACE(conta_despesa.cod_estrutural::varchar,'.','') AS rubrica_despesa
                          , LPAD(recurso.cod_recurso::varchar,9,'0')              AS cod_rec_vinculado
                          , despesa.vl_original                                   AS dotacao_inicial
                          , CASE WHEN previsao_despesa.periodo = 1
                                 THEN previsao_despesa.vl_previsto
                                 ELSE 0.00
                             END as janeiro
                          , CASE WHEN previsao_despesa.periodo = 2
                                 THEN previsao_despesa.vl_previsto
                                 ELSE 0.00
                             END as fevereiro
                          , CASE WHEN previsao_despesa.periodo = 3
                                 THEN previsao_despesa.vl_previsto
                                 ELSE 0.00
                             END as marco
                          , CASE WHEN previsao_despesa.periodo = 4
                                 THEN previsao_despesa.vl_previsto
                                 ELSE 0.00
                             END as abril
                          , CASE WHEN previsao_despesa.periodo = 5
                                 THEN previsao_despesa.vl_previsto
                                 ELSE 0.00
                             END as maio
                          , CASE WHEN previsao_despesa.periodo = 6
                                 THEN previsao_despesa.vl_previsto
                                 ELSE 0.00
                             END as junho
                          , CASE WHEN previsao_despesa.periodo = 7
                                 THEN previsao_despesa.vl_previsto
                                 ELSE 0.00
                             END as julho
                          , CASE WHEN previsao_despesa.periodo = 8
                                 THEN previsao_despesa.vl_previsto
                                 ELSE 0.00
                             END as agosto
                          , CASE WHEN previsao_despesa.periodo = 9
                                 THEN previsao_despesa.vl_previsto
                                 ELSE 0.00
                             END as setembro
                          , CASE WHEN previsao_despesa.periodo = 10
                                 THEN previsao_despesa.vl_previsto
                                 ELSE 0.00
                             END as outubro
                          , CASE WHEN previsao_despesa.periodo = 11
                                 THEN previsao_despesa.vl_previsto
                                 ELSE 0.00
                             END as novembro
                          , CASE WHEN previsao_despesa.periodo = 12
                                 THEN previsao_despesa.vl_previsto
                                 ELSE 0.00
                             END as dezembro    
                       FROM orcamento.conta_despesa
                       
                       JOIN orcamento.despesa
                         ON conta_despesa.exercicio  = despesa.exercicio
                        AND conta_despesa.cod_conta = despesa.cod_conta
                       
                       JOIN orcamento.recurso
                         ON recurso.exercicio   = despesa.exercicio
                        AND recurso.cod_recurso = despesa.cod_recurso
                       
                       JOIN orcamento.programa
                         ON programa.exercicio    = despesa.exercicio
                        AND programa.cod_programa = despesa.cod_programa
                       
                       JOIN orcamento.programa_ppa_programa
                         ON programa_ppa_programa.exercicio    = programa.exercicio
                        AND programa_ppa_programa.cod_programa = programa.cod_programa
                       
                       JOIN ppa.programa AS p_programa
                         ON p_programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                       
                       JOIN orcamento.pao
                         ON pao.exercicio = despesa.exercicio
                        AND pao.num_pao   = despesa.num_pao
                       
                       JOIN orcamento.pao_ppa_acao
                         ON pao_ppa_acao.exercicio = pao.exercicio
                        AND pao_ppa_acao.num_pao   = pao.num_pao
                       
                       JOIN ppa.acao
                         ON acao.cod_acao = pao_ppa_acao.cod_acao
                  
                  LEFT JOIN orcamento.previsao_despesa
                         ON previsao_despesa.exercicio   = despesa.exercicio
                        AND previsao_despesa.cod_despesa = despesa.cod_despesa
                  
                      WHERE despesa.exercicio    = '".$this->getDado('exercicio')."'
                        AND despesa.cod_entidade = ".$this->getDado('cod_entidade')."
                   ORDER BY despesa.cod_despesa
                    ) AS resultado
           
           GROUP BY cod_und_gestora
                  , cod_orgao
                  , cod_unid_orcamentaria
                  , cod_funcao
                  , cod_subfuncao
                  , cod_programa 
                  , cod_proj_atividade
                  , rubrica_despesa
                  , cod_rec_vinculado
                  , dotacao_inicial ";
        
        return $stSql;
    }

}

?>