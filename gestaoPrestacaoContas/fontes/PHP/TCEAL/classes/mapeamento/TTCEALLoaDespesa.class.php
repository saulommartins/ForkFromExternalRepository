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
    * Extensão da Classe de Mapeamento TTCEALLoaDespesa
    *
    * Data de Criação: 03/07/2014
    *
    * @author: Evandro Melos
    *
    $Id: TTCEALLoaDespesa.class.php 64806 2016-04-04 21:09:58Z carlos.silva $
    *
    * @ignore
    *
*/
class TTCEALLoaDespesa extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALLoaDespesa()
    {
        parent::Persistente();
        $this->setTabela('orcamento.fn_balancete_despesa');
        $this->setDado('exercicio',Sessao::getExercicio());
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
        $stSql = "  SELECT  codigo_ua
                            ,cod_und_gestora
                            ,cod_orgao
                            ,cod_unid_orcamentaria
                            ,cod_funcao
                            ,cod_subfuncao
                            ,cod_programa
                            ,cod_proj_atividade
                            ,RPAD(cod_conta_despesa, 16, '0') AS cod_conta_despesa
                            ,cod_rec_vinculado
                            ,dotacao_inicial
                            ,SUM(janeiro) as janeiro
                            ,SUM(fevereiro) as fevereiro
                            ,SUM(marco) as marco
                            ,SUM(abril) as abril
                            ,SUM(maio) as maio
                            ,SUM(junho) as junho
                            ,SUM(julho) as julho
                            ,SUM(agosto) as agosto
                            ,SUM(setembro) as setembro
                            ,SUM(outubro) as outubro
                            ,SUM(novembro) as novembro
                            ,SUM(dezembro) as dezembro
                    FROM(
                        SELECT (SELECT CASE WHEN valor = '' THEN '0000' ELSE valor END as valor
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
                                , LPAD(despesa.num_orgao::varchar,2,'0')                as cod_orgao
                                , LPAD(despesa.num_unidade::varchar,4,'0')              as cod_unid_orcamentaria 
                                , LPAD(despesa.cod_funcao::varchar,2,'0')               as cod_funcao
                                , LPAD(despesa.cod_subfuncao::varchar,3,'0')            as cod_subfuncao
                                , LPAD(programa.num_programa::varchar,4,'0')            as cod_programa
                                , LPAD(num_acao::varchar,4,'0')                         as cod_proj_atividade                                                                
                                , RPAD(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''), 16, '0') as cod_conta_despesa
                                , LPAD(recurso.cod_recurso::varchar,9,'0')              as cod_rec_vinculado
                                , despesa.vl_original                                   as dotacao_inicial
                                , CASE WHEN previsao_despesa.periodo = 1 THEN
                                        previsao_despesa.vl_previsto
                                    ELSE
                                        0.00
                                 END as janeiro
                                ,CASE WHEN previsao_despesa.periodo = 2 THEN
                                        previsao_despesa.vl_previsto
                                    ELSE
                                        0.00
                                 END as fevereiro
                                , CASE WHEN previsao_despesa.periodo = 3 THEN
                                        previsao_despesa.vl_previsto
                                    ELSE
                                        0.00
                                 END as marco
                                , CASE WHEN previsao_despesa.periodo = 4 THEN
                                        previsao_despesa.vl_previsto
                                    ELSE
                                        0.00
                                 END as abril
                                , CASE WHEN previsao_despesa.periodo = 5 THEN
                                        previsao_despesa.vl_previsto
                                    ELSE
                                        0.00
                                 END as maio
                                , CASE WHEN previsao_despesa.periodo = 6 THEN
                                        previsao_despesa.vl_previsto
                                    ELSE
                                        0.00
                                 END as junho
                                , CASE WHEN previsao_despesa.periodo = 7 THEN
                                        previsao_despesa.vl_previsto
                                    ELSE
                                        0.00
                                 END as julho
                                , CASE WHEN previsao_despesa.periodo = 8 THEN
                                        previsao_despesa.vl_previsto
                                    ELSE
                                        0.00
                                 END as agosto
                                , CASE WHEN previsao_despesa.periodo = 9 THEN
                                        previsao_despesa.vl_previsto
                                    ELSE
                                        0.00
                                 END as setembro
                                , CASE WHEN previsao_despesa.periodo = 10 THEN
                                        previsao_despesa.vl_previsto
                                    ELSE
                                        0.00
                                 END as outubro
                                , CASE WHEN previsao_despesa.periodo = 11 THEN
                                        previsao_despesa.vl_previsto
                                    ELSE
                                        0.00
                                 END as novembro
                                , CASE WHEN previsao_despesa.periodo = 12 THEN
                                        previsao_despesa.vl_previsto
                                    ELSE
                                        0.00
                                 END as dezembro    

                        FROM orcamento.conta_despesa
                        JOIN orcamento.despesa
                            ON conta_despesa.exercicio  = despesa.exercicio
                            AND conta_despesa.cod_conta = despesa.cod_conta
                        JOIN orcamento.recurso
                             ON recurso.exercicio   = despesa.exercicio
                            AND recurso.cod_recurso = despesa.cod_recurso
                        JOIN orcamento.despesa_acao
                             ON despesa_acao.exercicio_despesa  = despesa.exercicio
                            AND despesa_acao.cod_despesa        = despesa.cod_despesa   
                        JOIN ppa.acao
                            ON acao.cod_acao = despesa_acao.cod_acao
                        JOIN ppa.programa
                            ON programa.cod_programa = acao.cod_programa
                        LEFT JOIN orcamento.previsao_despesa
                             ON previsao_despesa.exercicio      = despesa.exercicio
                            AND previsao_despesa.cod_despesa    = despesa.cod_despesa
    
                        WHERE despesa.exercicio = '".$this->getDado('exercicio')."'
                        AND despesa.cod_entidade = ".$this->getDado('cod_entidade')."

                        ORDER BY despesa.cod_despesa
                        
                    )as resultado
                    GROUP BY codigo_ua
                            ,cod_und_gestora
                            ,cod_orgao
                            ,cod_unid_orcamentaria
                            ,cod_funcao
                            ,cod_subfuncao
                            ,cod_programa 
                            ,cod_proj_atividade
                            ,cod_conta_despesa
                            ,cod_rec_vinculado
                            ,dotacao_inicial
        ";
        
        return $stSql;
    }
}
?>
