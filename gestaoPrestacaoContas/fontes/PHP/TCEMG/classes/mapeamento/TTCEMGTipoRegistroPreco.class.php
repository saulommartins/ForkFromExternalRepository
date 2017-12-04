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

    * Classe de mapeamento da tabela tcemg.tipo_registro_preco
    * Data de Criação   : 10/04/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @ignore
    *
    * $Id: TTCEMGTipoRegistroPreco.class.php 59719 2014-09-08 15:00:53Z franver $
    *
    * $Revision: 59719 $
    * $Author: franver $
    * $Date: 2014-09-08 12:00:53 -0300 (Mon, 08 Sep 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");

class TTCEMGTipoRegistroPreco extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGTipoRegistroPreco()
    {
        parent::Persistente();
        $this->setTabela('tcemg.tipo_registro_preco');
        $this->setComplementoChave('exercicio');
        $this->setCampoCod('cod_entidade');
        $this->setCampoCod('cod_norma');

        $this->AddCampo('exercicio','char',true,'4',true,true);
        $this->AddCampo('cod_entidade','integer',true,'',true,true);            
        $this->AddCampo('cod_norma','integer',true,'',true,true);
        $this->AddCampo('cod_tipo_decreto','integer',true,'',false,true);

    }
    public function recuperaTipoRegistroPreco(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaNormaTipoRegistroPreco",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaTipoRegistroPreco()
    {
                $stSql = "
                            SELECT norma.cod_norma                                                              
                                       , norma.cod_tipo_norma                                                        
                                       , to_char( norma.dt_publicacao::date, 'dd/mm/yyyy'::text )  as dt_publicacao                
                                       , norma.nom_norma            
                                       , norma.dt_assinatura                                                            
                                       , to_char( norma.dt_assinatura::date, 'dd/mm/yyyy'::text )  as dt_assinatura_formatado       
                                       , lpad(norma.num_norma,6,'0') as num_norma                                                         
                                       , (lpad(norma.num_norma,6,'0')||'/'||norma.exercicio)  as num_norma_exercicio  
                                       , tipo_registro_preco.cod_tipo_decreto
                                FROM tcemg.tipo_registro_preco  
                                
                          LEFT JOIN  normas.norma
                                    ON  norma.cod_norma =tipo_registro_preco.cod_norma 
                                    
                              WHERE tipo_registro_preco.exercicio =  '".$this->getDado('exercicio')."'
                                  AND tipo_registro_preco.cod_entidade =".$this->getDado('cod_entidade')."
                         ORDER BY norma.num_norma
                               
                                  ";
               
        return $stSql;
    }
    public function recuperaNormaTipoRegistroPreco(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaNormaTipoRegistroPreco",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaNormaTipoRegistroPreco()
    {
                $stSql = "
                            SELECT norma.cod_norma                                                              
                                       , norma.cod_tipo_norma                                                        
                                       , to_char( norma.dt_publicacao::date, 'dd/mm/yyyy'::text )  as dt_publicacao                
                                       , norma.nom_norma            
                                       , norma.dt_assinatura                                                            
                                       , to_char( norma.dt_assinatura::date, 'dd/mm/yyyy'::text )  as dt_assinatura_formatado       
                                       , lpad(norma.num_norma,6,'0') as num_norma                                                         
                                       , (lpad(norma.num_norma,6,'0')||'/'||norma.exercicio)  as num_norma_exercicio  
                                       , tipo_registro_preco.cod_tipo_decreto
                                FROM normas.norma
                                
                          LEFT JOIN  tcemg.tipo_registro_preco 
                                    ON  norma.cod_norma =tipo_registro_preco.cod_norma 
                                    
                              WHERE norma.exercicio =  '".$this->getDado('exercicio')."'
                         ORDER BY norma.num_norma
                                  ";
               
        return $stSql;
    }
    
    public function __destruct(){}

}
