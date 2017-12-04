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
    * Extensão da Classe de mapeamento
    * Data de Criação: 09/10/2014
    * @author Analista: 
    * @author Desenvolvedor: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCETOAlteracaoLei extends Persistente
{

    function montaRecuperaTodos()
    {
        $stSql = " SELECT ( SELECT PJ.cnpj
                              FROM orcamento.entidade
                              JOIN sw_cgm
                                ON sw_cgm.numcgm = entidade.numcgm
                              JOIN sw_cgm_pessoa_juridica AS PJ
                                ON sw_cgm.numcgm = PJ.numcgm
                             WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                               AND entidade.cod_entidade = ".$this->getDado('entidade')."
                           ) AS unidade_gestora
                         , ".$this->getDado("bimestre")." AS bimestre
                         , ".$this->getDado("exercicio")." AS exercicio
                         , norma.num_norma AS num_lei
                         , norma.dt_publicacao
                         , norma_detalhe.cod_lei_alteracao AS lei
                         , norma_detalhe.percentual_credito_adicional AS percentual
                 
                       FROM normas.norma
                       
                       JOIN normas.norma_tipo_norma 
                         ON norma.cod_norma = norma_tipo_norma.cod_norma
                         
                       JOIN tceto.norma_detalhe
                         ON norma_detalhe.cod_norma = norma.cod_norma
                         
                       JOIN normas.lei
                         ON lei.cod_lei = norma_detalhe.cod_lei_alteracao
                
                      WHERE norma.exercicio = '".$this->getDado('exercicio')."'
                      --AND norma.dt_assinatura BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy') ";
        
        return $stSql;
    }

}//FIM CLASSE
