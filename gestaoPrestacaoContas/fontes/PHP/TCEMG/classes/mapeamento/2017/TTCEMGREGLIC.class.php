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

    * Classe de mapeamento 
    * Data de Criação: 25/03/2014

    * @category    Urbem
    * @package    TCE/MG
    * @author      Carolina Schwaab Marcal
    * $Id: TTCEMGREGLIC.class.php 62269 2015-04-15 18:28:39Z franver $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGREGLIC extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
     
    public function recuperaNorma(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaNorma",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaNorma()
    {
        $inMes = explode('/',$this->getDado('dtInicio'));
        $inMes = $inMes[1];
        $stSql = "
                            SELECT cod_norma                                                              
                                       , cod_tipo_norma                                                        
                                       , to_char( dt_publicacao::date, 'dd/mm/yyyy'::text )  as dt_publicacao                
                                       , nom_norma                                                                
                                       , descricao                                                                
                                       , exercicio                                                                
                                       , dt_assinatura                                                            
                                       , to_char( dt_assinatura::date, 'dd/mm/yyyy'::text )  as dt_assinatura_formatado       
                                       , lpad(num_norma,6,'0') as num_norma                              
                                       , link                                                                     
                                       , (lpad(num_norma,6,'0')||'/'||exercicio)  as num_norma_exercicio  
                                FROM  normas.norma
                              WHERE exercicio =  '".$this->getDado('exercicio')."'";
        
        return $stSql;
    }
    
     public function recuperaREGLIC10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaREGLIC10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaREGLIC10()
    {
       
        $stSql = "
                            SELECT norma.cod_norma                                                              
                                       , '10' as tipo_registro
                                       , tipo_registro_preco.cod_entidade as cod_orgao
                                       , tipo_registro_preco.cod_tipo_decreto as tipo_decreto
                                       , num_norma::integer as nro_decreto_municipal
                                       , to_char( norma.dt_assinatura::date, 'ddmmyyyy'::text )  as data_decreto_municipal
                                       , to_char( norma.dt_publicacao::date, 'ddmmyyyy'::text )  as data_publicacao_decreto_municipal         
                                FROM  tcemg.tipo_registro_preco
                        INNER JOIN normas.norma
                                    ON norma.cod_norma = tipo_registro_preco.cod_norma

                             WHERE tipo_registro_preco.exercicio =  '".$this->getDado('exercicio')."'
                                 AND tipo_registro_preco.cod_entidade IN (".$this->getDado('entidades').")
                                 AND tipo_registro_preco.cod_tipo_decreto IS NOT NULL 
                       GROUP BY norma.cod_norma
                                     , tipo_registro
                                     , cod_orgao
                                     , tipo_decreto
                                     , num_norma";  

        return $stSql;
    }
    
    
    public function recuperaREGLIC20(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaREGLIC20",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaREGLIC20()
    { 
        $stSql = "
                SELECT
                        20 AS tipo_registro
                      , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                      , configuracao_reglic.*
                      , to_char( norma.dt_assinatura::date, 'ddmmyyyy'::text )  as data_norma_reg
                      , to_char( norma.dt_publicacao::date, 'ddmmyyyy'::text )  as data_pub_norma_reg
                      , num_norma as  nro_norma_reg
                      
                FROM tcemg.configuracao_reglic
                
                JOIN normas.norma
                  ON norma.cod_norma = configuracao_reglic.cod_norma
                
                JOIN administracao.configuracao_entidade
                  ON configuracao_entidade.cod_entidade = configuracao_reglic.cod_entidade
                 AND configuracao_entidade.exercicio = configuracao_reglic.exercicio
                 AND configuracao_entidade.cod_modulo = 55
                 AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                 
               WHERE configuracao_reglic.exercicio =  '".$this->getDado('exercicio')."'
                 AND configuracao_reglic.cod_entidade IN (".$this->getDado('entidades').")
                 AND TO_DATE(TO_CHAR(norma.dt_assinatura,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
                 AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
                 
            GROUP BY tipo_registro, cod_orgao, configuracao_reglic.exercicio, configuracao_reglic.cod_entidade, norma.dt_assinatura, norma.dt_publicacao, norma.num_norma
        ";
        
        return $stSql;
    }
    
    public function __destruct(){}

    
}
