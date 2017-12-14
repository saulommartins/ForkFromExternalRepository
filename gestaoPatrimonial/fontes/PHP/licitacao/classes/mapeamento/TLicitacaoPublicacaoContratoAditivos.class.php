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

    * Classe de mapeamento da tabela licitacao.publicacao_contrato
    * Data de Criação: 12/10/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Rodrigo

    * $Id: TLicitacaoPublicacaoContrato.class.php 46285 2012-03-08 12:19:45Z gelson $

    * Casos de uso: uc-03.05.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLicitacaoPublicacaoContratoAditivos extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */

    public function TLicitacaoPublicacaoContratoAditivos()
    {
        parent::Persistente();
        $this->setTabela("licitacao.publicacao_contrato_aditivos");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio_contrato, cod_entidade, num_contrato, exercicio, num_aditivo, numcgm, dt_publicacao');

        $this->AddCampo('exercicio_contrato'   ,'char'      ,false ,'4',true,'TLicitacaoContrato');
        $this->AddCampo('cod_entidade'         ,'integer'   ,false,'',true  ,'TLicitacaoContrato');
        $this->AddCampo('exercicio'            ,'char'      ,false ,'4',true,'TLicitacaoContrato');
        $this->AddCampo('num_contrato'         ,'integer'   ,false,'',true,'TLicitacaoContrato');
        $this->AddCampo('num_aditivo'          ,'integer'   ,false,'',true,'TLicitacaoContrato');
        $this->AddCampo('numcgm'               ,'integer'   ,false,'',true, 'TLicitacaoVeiculosPublicidade');
        $this->AddCampo('dt_publicacao'        ,'date'      ,true,'',false,false);
        $this->AddCampo('observacao'           ,'varchar'   ,false ,'80' ,false,false);
        $this->AddCampo('num_publicacao'       ,'integer'   ,false,'',false,false);
    }

    public function recuperaVeiculosPublicacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaVeiculosPublicacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaVeiculosPublicacao()
    {
        $stSql = " SELECT publicacao_contrato_aditivos.num_contrato	                                              \n";
        $stSql .= " 	  , publicacao_contrato_aditivos.exercicio 			                                      \n";
        $stSql .= "	      , publicacao_contrato_aditivos.cod_entidade			                                  \n";
        $stSql .= "	      , to_char( publicacao_contrato_aditivos.dt_publicacao, 'dd/mm/yyyy' ) as dt_publicacao  \n";
        $stSql .= "	      , publicacao_contrato_aditivos.numcgm as num_veiculo	                                  \n";
        $stSql .= "	      , publicacao_contrato_aditivos.num_publicacao                                           \n";
        $stSql .= "	      , sw_cgm.nom_cgm as nom_veiculo 				                                          \n";
        $stSql .= "	      , publicacao_contrato_aditivos.observacao 				                              \n";
        $stSql .= "       FROM licitacao.publicacao_contrato_aditivos     		                                  \n";
        $stSql .= " INNER JOIN sw_cgm 							                                                  \n";
        $stSql .= "	    ON sw_cgm.numcgm        = publicacao_contrato_aditivos.numcgm 	                          \n";
        $stSql .= "      WHERE num_contrato         = ".$this->getDado('num_contrato')."	                      \n";
        $stSql .= "        AND exercicio            = '".$this->getDado('exercicio')."' 	                      \n";
        $stSql .= "        AND exercicio_contrato   = '".$this->getDado('exercicio_contrato')."'                  \n";
        $stSql .= "	   AND cod_entidade         = ".$this->getDado('cod_entidade')."                              \n";

        if ($this->getDado('num_aditivo') != '') {
            $stSql .= "        AND num_aditivo      = ".$this->getDado('num_aditivo')."                           \n";
        }

        return $stSql;
    }

}
