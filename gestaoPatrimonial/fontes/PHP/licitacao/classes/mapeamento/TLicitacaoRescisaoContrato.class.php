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
    * Data de Criação: 08/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Id: TLicitacaoRescisaoContrato.class.php 66447 2016-08-30 14:21:17Z michel $

    * Casos de uso : uc-03.05.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TLicitacaoRescisaoContrato extends Persistente
{

    /**
    * Método Construtor
    * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela("licitacao.rescisao_contrato");

        $this->setCampoCod('num_contrato');
        $this->setComplementoChave('exercicio_contrato, cod_entidade');

        $this->AddCampo('exercicio_contrato', 'character' , true , '4'   , true , true);
        $this->AddCampo('cod_entidade'      , 'integer'   , true , ''    , true , true);
        $this->AddCampo('num_contrato'      , 'integer'   , true , ''    , true , true);
        $this->AddCampo('exercicio'         , 'character' , true , '4'   , false, false);
        $this->AddCampo('num_rescisao'      , 'integer'   , true , ''    , false, false);
        $this->AddCampo('dt_rescisao'       , 'date'      , true , ''    , false, false);
        $this->AddCampo('vlr_cancelamento'  , 'numeric'   , true , '14,2', false, false);
        $this->AddCampo('vlr_multa'         , 'numeric'   , true , '14,2', false, false);
        $this->AddCampo('vlr_indenizacao'   , 'numeric'   , true , '14,2', false, false);
        $this->AddCampo('motivo'            , 'text'      , true , ''    , false, false);
    }

    public function recuperaContratoRescisao(&$rsRecordSet, $stFiltro ="" ,$stOrder ="" ,$boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaContratoRescisao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContratoRescisao()
    {
        /*
         * true  => Executa bloco de código referente a licitação
         * false => Executa bloco de código referente a compra direta
         */
        if ($this->getDado('licitacao')) {

        $stSQL = "SELECT contrato.num_contrato
                        , contrato.cod_entidade
                        , sw_cgm.nom_cgm AS entidade
                        , licitacao.contrato.cgm_contratado
                        , sw_cgm2.nom_cgm as contratado
                        , contrato_licitacao.exercicio
                        , contrato_licitacao.exercicio_licitacao
                        , rescisao_contrato.exercicio_contrato
                        , rescisao_contrato.exercicio AS exercicio_rescisao
                        , rescisao_contrato.num_rescisao
                        , TO_CHAR(rescisao_contrato.dt_rescisao,'dd/mm/yyyy') AS dt_rescisao
                        , rescisao_contrato.vlr_cancelamento
                        , rescisao_contrato.vlr_multa
                        , rescisao_contrato.vlr_indenizacao
                        , rescisao_contrato.motivo 

                    FROM licitacao.contrato

              INNER JOIN orcamento.entidade
                      ON orcamento.entidade.exercicio    = licitacao.contrato.exercicio
                     AND orcamento.entidade.cod_entidade = licitacao.contrato.cod_entidade

              INNER JOIN licitacao.contrato_licitacao
                      ON contrato.num_contrato = contrato_licitacao.num_contrato
                     AND contrato.cod_entidade = contrato_licitacao.cod_entidade
                     AND contrato.exercicio    = contrato_licitacao.exercicio
                     
               LEFT JOIN licitacao.rescisao_contrato
                      ON rescisao_contrato.num_contrato       = contrato.num_contrato
                     AND rescisao_contrato.cod_entidade       = contrato.cod_entidade
                     AND rescisao_contrato.exercicio_contrato = contrato.exercicio

              INNER JOIN sw_cgm
                      ON orcamento.entidade.numcgm = sw_cgm.numcgm

              INNER JOIN sw_cgm AS sw_cgm2
                      ON sw_cgm2.numcgm = licitacao.contrato.cgm_contratado
                ";

        } else {

            $stSQL = "SELECT contrato.num_contrato
                        , contrato.cod_entidade
                        , sw_cgm.nom_cgm AS entidade
                        , licitacao.contrato.cgm_contratado
                        , sw_cgm2.nom_cgm as contratado
                        , contrato_compra_direta.exercicio
                        , contrato_compra_direta.exercicio_compra_direta
                        , rescisao_contrato.exercicio_contrato
                        , rescisao_contrato.exercicio AS exercicio_rescisao
                        , rescisao_contrato.num_rescisao
                        , TO_CHAR(rescisao_contrato.dt_rescisao,'dd/mm/yyyy') AS dt_rescisao
                        , rescisao_contrato.vlr_cancelamento
                        , rescisao_contrato.vlr_multa
                        , rescisao_contrato.vlr_indenizacao
                        , rescisao_contrato.motivo 

                    FROM licitacao.contrato
                    
               LEFT JOIN licitacao.rescisao_contrato
                      ON rescisao_contrato.num_contrato       = contrato.num_contrato
                     AND rescisao_contrato.cod_entidade       = contrato.cod_entidade
                     AND rescisao_contrato.exercicio_contrato = contrato.exercicio

              INNER JOIN orcamento.entidade
                      ON orcamento.entidade.exercicio    = licitacao.contrato.exercicio
                     AND orcamento.entidade.cod_entidade = licitacao.contrato.cod_entidade

              INNER JOIN licitacao.contrato_compra_direta
                     ON contrato.num_contrato  = contrato_compra_direta.num_contrato
                     AND contrato.cod_entidade = contrato_compra_direta.cod_entidade
                     AND contrato.exercicio    = contrato_compra_direta.exercicio

              INNER JOIN sw_cgm
                      ON orcamento.entidade.numcgm = sw_cgm.numcgm

              INNER JOIN sw_cgm AS sw_cgm2
                      ON sw_cgm2.numcgm = licitacao.contrato.cgm_contratado
                 ";
        }

        $stSQL .= "WHERE ";
        if ($this->getDado("num_contrato")) {
            $stSQL .= "licitacao.contrato.num_contrato = ".$this->getDado("num_contrato")." AND  ";
        }
        if ($this->getDado("exercicio")) {
            $stSQL .= "contrato.exercicio = '".$this->getDado("exercicio")."' AND  ";
        }

        $stSQL = substr($stSQL, 0, strlen($stFiltro)-6);

        return $stSQL;
    }

    public function recuperaProximoNumRescisao(&$rsRecordSet, $stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaProximoNumRescisao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaProximoNumRescisao()
    {
        $stSQL = " SELECT COALESCE(MAX(num_rescisao),0) + 1 AS maximo";
        $stSQL .= " FROM licitacao.rescisao_contrato";

        return $stSQL;
    }
}
