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
    * $Id: TTCEMGConfiguracaoREGLIC.class.php 59719 2014-09-08 15:00:53Z franver $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGConfiguracaoREGLIC extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
     public function TTCEMGConfiguracaoREGLIC()
    {

        parent::Persistente();
        $this->setTabela('tcemg.configuracao_reglic');
        $this->setComplementoChave('exercicio');
        $this->setCampoCod('cod_entidade');
        
        $this->AddCampo('exercicio','char',true,'4',true,true);
        $this->AddCampo('cod_entidade','integer',true,'',true,true);
        $this->AddCampo('regulamento_art_47','integer',true,'',false,true);
        $this->AddCampo('cod_norma','integer',true,'',false,true);
        $this->AddCampo('reg_exclusiva','integer',true,'',false,true);
        $this->AddCampo('artigo_reg_exclusiva','varchar',true,'6',false,true);
        $this->AddCampo('valor_limite_reg_exclusiva','numeric',true,'14,2',false,true);
        $this->AddCampo('proc_sub_contratacao','integer',true,'',false,true);
        $this->AddCampo('artigo_proc_sub_contratacao','varchar',true,'6',false,true);
        $this->AddCampo('percentual_sub_contratacao','numeric',true,'6,2',false,true);
        $this->AddCampo('criterio_empenho_pagamento','integer',true,'',false,true);
        $this->AddCampo('artigo_empenho_pagamento','varchar',true,'6',false,true);
        $this->AddCampo('estabeleceu_perc_contratacao','integer',true,'',false,true);
        $this->AddCampo('artigo_perc_contratacao','varchar',true,'6',false,true);
        $this->AddCampo('percentual_contratacao','numeric',true,'6,2',false,true);
    }

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
    
    public function recuperaREGLIC20(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaREGLIC20",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaREGLIC20()
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
    
    public function __destruct(){}

}
