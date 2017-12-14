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
  * Página Mapeamento arquivo TCM-BA EmpreSubven
  * Data de Criação: 27/08/2015
  * @author Analista:      Valtair Santos 
  * @author Desenvolvedor: Evandro Melos
  * $Id: $
  * $Date: $
  * $Author: $
  * $Rev: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAEmpreSubven extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }    

    public function recuperaSubvencoes(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaSubvencoes().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montaRecuperaSubvencoes()
    {
        $stSql = "SELECT '1' as tipo_registro
                         , '".$this->getDado('unidade_gestora')."' as  unidade_gestora
                         , sw_cgm_pessoa_juridica.cnpj
                         , CASE WHEN sw_cgm_pessoa_juridica.nom_fantasia = '' THEN
                                sw_cgm.nom_cgm
                           ELSE
                                sw_cgm_pessoa_juridica.nom_fantasia
                           END as nome_empresa
                         , TO_CHAR(subvencao_empenho.dt_inicio,'dd/mm/yyyy') as dt_inicio
                         , TO_CHAR(subvencao_empenho.dt_termino,'dd/mm/yyyy') as dt_enceramento
                         , subvencao_empenho.prazo_aplicacao
                         , subvencao_empenho.prazo_comprovacao
                         , norma_utilidade.num_norma AS cod_norma_utilidade
                         , TO_CHAR(norma_utilidade.dt_publicacao,'dd/mm/yyyy') as dt_publicacao_utilidade
                         , norma_valor.num_norma AS cod_norma_valor
                         , TO_CHAR(norma_valor.dt_publicacao,'dd/mm/yyyy') as dt_publicacao_valor
                         , '".$this->getDado('competencia')."' as competencia
                         , banco.num_banco AS cod_banco
                         , agencia.num_agencia AS cod_agencia
                         , conta_corrente.num_conta_corrente AS cod_conta_corrente
        
                    FROM tcmba.subvencao_empenho

              INNER JOIN normas.norma as norma_utilidade
                      ON norma_utilidade.cod_norma = subvencao_empenho.cod_norma_utilidade

              INNER JOIN normas.norma as norma_valor
                      ON norma_valor.cod_norma = subvencao_empenho.cod_norma_valor

               LEFT JOIN sw_cgm_pessoa_juridica
                      ON sw_cgm_pessoa_juridica.numcgm = subvencao_empenho.numcgm

               LEFT JOIN sw_cgm
                      ON sw_cgm.numcgm = subvencao_empenho.numcgm

              INNER JOIN monetario.banco
                      ON banco.cod_banco = subvencao_empenho.cod_banco

              INNER JOIN monetario.agencia
                      ON agencia.cod_banco = subvencao_empenho.cod_banco
                     AND agencia.cod_agencia = subvencao_empenho.cod_agencia

              INNER JOIN monetario.conta_corrente
                      ON conta_corrente.cod_banco = subvencao_empenho.cod_banco
                     AND conta_corrente.cod_agencia = subvencao_empenho.cod_agencia
                     AND conta_corrente.cod_conta_corrente = subvencao_empenho.cod_conta_corrente

                   WHERE subvencao_empenho.dt_inicio <= TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')
                     AND subvencao_empenho.dt_termino >= TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')
                ";
        return $stSql;
    }

}//fim da classe
?>
 