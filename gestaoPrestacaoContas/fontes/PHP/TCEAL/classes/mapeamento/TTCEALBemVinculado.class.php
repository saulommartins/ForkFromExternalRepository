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

    * Extensão da Classe de Mapeamento TTCEALBemVinculado
    *
    * Data de Criação: 28/05/2014
    *
    * @author: Carlos Adriano Vernieri da Silva
    *
    * $Id: TTCEALBemVinculado.class.php 61420 2015-01-15 16:24:25Z jean $
    *
    * @ignore
    *
*/
class TTCEALBemVinculado extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALBemVinculado()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
  
   public function recuperaBemVinculado(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
  {
         $stSql = "
                SELECT cod_und_gestora
                     , codigo_ua
                     , bimestre
                     , exercicio
                     , cod_orgao
                     , cod_und_orcamentaria
                     , num_bem
                     , num_tombamento
                     , num_empenho
                     
                    FROM ( SELECT  (SELECT PJ.cnpj 
                             FROM orcamento.entidade 
                       INNER JOIN sw_cgm 
                               ON sw_cgm.numcgm = entidade.numcgm
                       INNER JOIN sw_cgm_pessoa_juridica AS PJ 
                               ON PJ.numcgm = sw_cgm.numcgm
                            WHERE entidade.exercicio    = '".$this->getDado('exercicio')."' 
                              AND entidade.cod_entidade = ".$this->getDado('inCodEntidade')." ) AS cod_und_gestora
                             
                        , (SELECT LPAD(valor,4,'0') 
                             FROM administracao.configuracao_entidade 
                            WHERE exercicio    = '".$this->getDado('exercicio')."'
                              AND cod_entidade = ".$this->getDado('inCodEntidade')." 
                              AND cod_modulo   = 62 
                              AND parametro    = 'tceal_configuracao_unidade_autonoma') AS codigo_ua
                                 
                        , ".$this->getDado('bimestre')." AS bimestre
                        , '".$this->getDado('exercicio')."' AS exercicio
                        , LPAD(bem_comprado.num_orgao::VARCHAR,2,'0') AS cod_orgao
                        , LPAD(bem_comprado.num_unidade::VARCHAR,4,'0') AS cod_und_orcamentaria
                        , bem.cod_bem AS num_bem
                        , bem.num_placa AS num_tombamento
                        , CASE WHEN bem_comprado.cod_empenho::VARCHAR  <> '' THEN
                            TO_CHAR(dt_empenho,'yyyymm')||LPAD(bem_comprado.cod_empenho::VARCHAR,7,'0')
                          ELSE 
                            '9999010000001'::VARCHAR
                          END AS num_empenho
                       
                    FROM patrimonio.bem
               LEFT JOIN patrimonio.bem_comprado
                      ON bem_comprado.cod_bem      = bem.cod_bem
                     AND bem_comprado.exercicio    = '".$this->getDado('exercicio')."'
                     AND bem_comprado.cod_entidade = ".$this->getDado('inCodEntidade')."
               LEFT JOIN empenho.empenho
                      ON bem_comprado.cod_empenho = empenho.cod_empenho
                     AND bem_comprado.cod_entidade = empenho.cod_entidade
                     AND bem_comprado.exercicio = empenho.exercicio
                   WHERE bem.dt_aquisicao < to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')
                    ) AS tabela
                ORDER BY num_bem";
                
        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);

     }
}
?>
