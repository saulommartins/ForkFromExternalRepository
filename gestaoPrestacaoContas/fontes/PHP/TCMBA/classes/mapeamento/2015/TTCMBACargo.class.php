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

    * Página de Mapeamentp - Exportação Arquivos TCM-BA
    * Data de Criação   : 02/07/2015
    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    * $Id: $
*/


include_once ( CLA_PERSISTENTE );

class TTCMBACargo extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct() 
    {
        parent::Persistente();
    }

    public function recuperaArquivo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaArquivo().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaArquivo()
    {
        $stSql = "  SELECT * 
                    FROM (
                        SELECT DISTINCT ON (unidade_gestora,cod_cargo,tipo_cargo,data_vigencia) 
                            1 as tipo_registro 
                            ,'".$this->getDado('unidade_gestora')."' as unidade_gestora
                            , cargo.cod_cargo as cod_cargo
                            , de_para_tipo_cargo_tcmba.cod_tipo_cargo_tce as tipo_cargo
                            , cargo.descricao as nome_cargo
                            , cargo_sub_divisao.nro_vaga_criada as quantidade_vagas
                            , (SELECT getVagasOcupadasCargo(regime.cod_regime,cargo_sub_divisao.cod_sub_divisao,cargo_sub_divisao.cod_cargo,536,true,'') ) as vagas_ocupadas
                            , norma.num_norma as lei
                            , TO_CHAR(norma.dt_publicacao,'ddmmyyyy') as data_lei
                            ,''::varchar as brancos
                            , TO_CHAR(norma.dt_publicacao,'ddmmyyyy') as data_vigencia
                        FROM pessoal.cargo 
                    
                        INNER JOIN pessoal.cargo_sub_divisao
                             ON cargo_sub_divisao.cod_cargo = cargo.cod_cargo
                            AND cargo_sub_divisao.timestamp = ( SELECT MAX(timestamp)
                                                                FROM pessoal.cargo_sub_divisao 
                                                                WHERE cod_cargo = cargo.cod_cargo
                                                                  AND cargo_sub_divisao.nro_vaga_criada > 0)
            
                        INNER JOIN pessoal.sub_divisao 
                            ON sub_divisao.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao
    
                        INNER JOIN pessoal.regime
                            ON sub_divisao.cod_regime = regime.cod_regime
    
                        INNER JOIN pessoal.de_para_tipo_cargo_tcmba 
                            ON de_para_tipo_cargo_tcmba.cod_sub_divisao = sub_divisao.cod_sub_divisao
    
                        INNER JOIN normas.norma
                            ON norma.cod_norma = cargo_sub_divisao.cod_norma
    
                        WHERE cargo_sub_divisao.nro_vaga_criada > 0
        
                        GROUP BY 
                                 cargo.cod_cargo
                                , tipo_cargo
                                , nome_cargo
                                , quantidade_vagas
                                , vagas_ocupadas
                                , lei
                                , data_lei        
                                , data_vigencia
                    )AS arquivo
                    
                    WHERE vagas_ocupadas> 0
                ";

        return $stSql;
    }

}
