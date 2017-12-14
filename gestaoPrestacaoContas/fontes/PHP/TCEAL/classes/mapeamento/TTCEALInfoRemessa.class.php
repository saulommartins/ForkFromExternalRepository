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

    * Extensão da Classe de Mapeamento TTCEALInfoRemessa
    *
    * Data de Criação: 30/05/2014
    *
    * @author: Carolina Schwaab Marçal
    *
    * $Id: TTCEALInfoRemessa.class.php 65525 2016-05-31 13:27:07Z lisiane $
    *
    * @ignore
    *
*/
class TTCEALInfoRemessa extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALInfoRemessa()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
       
    public function listarExportacaoInfoRemessa(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
    {
        
        $stSql = "SELECT (SELECT PJ.cnpj
                                       FROM orcamento.entidade
                                       JOIN sw_cgm
                                         ON sw_cgm.numcgm=entidade.numcgm
                                       JOIN sw_cgm_pessoa_juridica AS PJ
                                         ON sw_cgm.numcgm=PJ.numcgm
                                      WHERE entidade.exercicio='".$this->getDado('exercicio')."'
                                        AND entidade.cod_entidade=".$this->getDado('und_gestora')."
                                    ) AS cod_und_gestora
                                  , (SELECT lpad(valor,4,'0') as valor
                                        FROM administracao.configuracao_entidade
                                      WHERE configuracao_entidade.cod_modulo = 62
                                           AND configuracao_entidade.exercicio = '".$this->getDado('stExercicio')."'
                                           AND configuracao_entidade.parametro like 'tceal_configuracao_unidade_autonoma'
                                           AND configuracao_entidade.cod_entidade =  ".$this->getDado('inCodEntidade')."
                                     ) AS codigo_ua

                    FROM orcamento.entidade
                   WHERE entidade.exercicio ='".$this->getDado('stExercicio')."'
                     AND entidade.cod_entidade IN (".$this->getDado('inCodEntidade').")

                GROUP BY cod_und_gestora
                       , codigo_ua";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }
     
}
?>
