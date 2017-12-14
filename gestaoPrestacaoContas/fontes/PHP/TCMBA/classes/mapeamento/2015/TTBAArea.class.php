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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTBAArea extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    function TTBAArea() {
        $this->setEstrutura( array() );
        $this->setEstruturaAuxiliar( array() );
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    function recuperaArea(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaArea().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaArea()
    {
        $stSql = " SELECT 1 AS tipo_registro
                        , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                        , concurso_cargo.cod_cargo
                        , edital.cod_edital AS num_concurso
                        , (SELECT i
                             FROM (SELECT row_number() over(partition by cod_edital) AS i, cod_cargo 
                                     FROM concurso.concurso_cargo as cc WHERE cc.cod_edital = edital.cod_edital
                                  ) AS gg
                             WHERE gg.cod_cargo = concurso_cargo.cod_cargo
                        ) AS sequencial_area
                        , cargo.descricao
                        , '' AS reservado_tcm1
                        , SUM(cargo_sub_divisao.nro_vaga_criada) AS vagas_oferecidas
                        , '' AS reservado_tcm2
                        , ".$this->getDado('periodo')." AS competencia
                        , '' AS unidade_trabalho

                     FROM concurso".$this->getDado('entidade_rh').".edital

               INNER JOIN concurso".$this->getDado('entidade_rh').".concurso_cargo
                       ON concurso_cargo.cod_edital = edital.cod_edital

               INNER JOIN pessoal".$this->getDado('entidade_rh').".cargo
                       ON cargo.cod_cargo = concurso_cargo.cod_cargo

               INNER JOIN pessoal".$this->getDado('entidade_rh').".cargo_sub_divisao
                       ON cargo_sub_divisao.cod_cargo = cargo.cod_cargo

                    WHERE edital.dt_aplicacao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')

                    GROUP BY concurso_cargo.cod_cargo,num_concurso,cargo.descricao

                    ORDER BY unidade_gestora, num_concurso, sequencial_area
        ";
        
        return $stSql;
    }
}
