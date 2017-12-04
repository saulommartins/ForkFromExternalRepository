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
/*
 * Classe de mapeamento

 * @package Urbem
 * @subpackage Mapeamento

 * @author Diogo Zarpelon

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEALAlteracoesLeis extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEALAlteracoesLeis()
    {
        parent::Persistente();
        
        $this->setDado('exercicio', Sessao::getExercicio());
    }

    public function recuperaAlteracoesLeis(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem)){
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        }
        
        $stSql = $this->montaRecuperaAlteracoesLeis().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaAlteracoesLeis()
    {
               
            $stSql = "   
               SELECT (
                   SELECT sw_cgm_pj.cnpj
                    FROM orcamento.entidade
              INNER JOIN sw_cgm
                      ON sw_cgm.numcgm = entidade.numcgm
              INNER JOIN sw_cgm_pessoa_juridica AS sw_cgm_pj
                      ON sw_cgm.numcgm = sw_cgm_pj.numcgm
                   WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                     AND entidade.cod_entidade = ".$this->getDado('und_gestora')."
                   ) AS cod_und_gestora
                 , (
                  SELECT LPAD(valor,4,'0') AS valor
                    FROM administracao.configuracao_entidade
                   WHERE exercicio = '".$this->getDado('exercicio')."'
                     AND cod_entidade = ".$this->getDado('und_gestora')."
                     AND cod_modulo = 62
                     AND parametro = 'tceal_configuracao_unidade_autonoma'
                   ) AS codigo_ua
                 , ".$this->getDado('bimestre')." AS bimestre
                 , '".$this->getDado('exercicio')."' AS exercicio
                 , normas.norma_detalhe_al.cod_norma_alteracao AS num_documento_alteracao
                 , TO_CHAR(norma.dt_assinatura,'dd/mm/yyyy') AS data_documento_alteracao
                 , TO_CHAR(norma.dt_publicacao,'dd/mm/yyyy') AS data_publicacao_documento_alteracao                                                           
                 , norma_detalhe_al.cod_lei_alteracao AS lei_alterada                                    
                 , norma.num_norma AS num_lei_alterada
                 , norma_detalhe_al.descricao_alteracao AS descricao_alteracao 
                 
              FROM normas.norma
        
        INNER JOIN normas.norma_tipo_norma 
                ON norma.cod_norma = norma_tipo_norma.cod_norma
                
        INNER JOIN normas.norma_detalhe_al
                ON norma.cod_norma = norma_detalhe_al.cod_norma_alteracao
       
             WHERE norma.cod_tipo_norma IN (1,2)                         
               AND norma.exercicio = '".$this->getDado('exercicio')."' ";
       
        return $stSql;
    }

}
