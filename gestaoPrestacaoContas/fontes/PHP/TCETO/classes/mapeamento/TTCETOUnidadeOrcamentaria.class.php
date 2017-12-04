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

    * Extensão da Classe de Mapeamento TTCETOUnidadeOrcamentaria
    *
    * Data de Criação: 11/11/2014
    *
    * @author: Evandro Melos
    *
    * $Id: TTCETOUnidadeOrcamentaria.class.php 60722 2014-11-11 18:20:25Z evandro $
    *
    * @ignore
    *
*/

class TTCETOUnidadeOrcamentaria extends Persistente {
    /**
        * Método Construtor
        * @access Public
    */
    public function TTCETOUnidadeOrcamentaria()
    {
        parent::Persistente();
    }
    /**
     * Método para trazer todos os registros de Projeto Atividade, para o TCEAL
     * @access Public
     * @param  Object  $rsRecordSet Objeto RecordSet
     * @param  String  $stCondicao  String de condição do SQL (WHERE)
     * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
     * @param  Boolean $boTransacao
     * @return Object  Objeto Erro
    */
    public function recuperaUndOrcamentaria(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaUndOrcamentaria().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaUndOrcamentaria()
    {
        $stSql = " SELECT
                            cod_und_gestora
                            ,exercicio
                            ,lpad(cod_und_orcamentaria::varchar,4,'0') AS cod_und_orcamentaria
                            ,lpad(cod_orgao::varchar,2,'0') AS cod_orgao
                            ,nome
                            ,lpad(identificador::varchar,2,'0') as identificador
                            ,cnpj
                    FROM (
                            SELECT (SELECT PJ.cnpj
                                        FROM orcamento.entidade
                                        JOIN sw_cgm
                                            ON sw_cgm.numcgm=entidade.numcgm
                                        JOIN sw_cgm_pessoa_juridica AS PJ
                                            ON sw_cgm.numcgm=PJ.numcgm
                                        WHERE entidade.exercicio=consulta.exercicio
                                        AND entidade.cod_entidade=consulta.cod_entidade)
                                    AS cod_und_gestora
                                    , exercicio AS exercicio
                                    , num_unidade AS cod_und_orcamentaria
                                    , num_orgao AS cod_orgao
                                    , nom_unidade AS nome
                                    , identificador
                                    , cnpj
                            FROM (  SELECT  despesa.exercicio
                                            , unidade.num_unidade
                                            , unidade.num_orgao
                                            , unidade.nom_unidade
                                            , uniorcam.identificador
                                            , PJ.cnpj
                                            , despesa.cod_entidade
                                    FROM orcamento.unidade
                                    JOIN orcamento.despesa
                                         ON despesa.exercicio = unidade.exercicio
                                        AND despesa.num_unidade = unidade.num_unidade
                                        AND despesa.num_orgao = unidade.num_orgao
                                    LEFT JOIN tceto.uniorcam
                                      ON uniorcam.num_unidade = unidade.num_unidade
                                     AND uniorcam.num_orgao = unidade.num_orgao
                                    LEFT JOIN sw_cgm
                                      ON sw_cgm.numcgm = uniorcam.numcgm
                                    LEFT JOIN sw_cgm_pessoa_juridica AS PJ
                                      ON sw_cgm.numcgm = PJ.numcgm  
                                    WHERE despesa.exercicio = '".$this->getDado('exercicio')."'
                                    AND despesa.cod_entidade IN (".$this->getDado('cod_entidade').")
                                    GROUP BY despesa.exercicio
                                            , unidade.num_unidade
                                            , unidade.num_orgao
                                            , unidade.nom_unidade
                                            , uniorcam.identificador
                                            , PJ.cnpj
                                            , despesa.cod_entidade
                                    ORDER BY unidade.num_unidade
                                            , unidade.num_orgao ASC
                            ) AS consulta
                    ) as tabela
        ";
        return $stSql;
    }
}
?>