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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBABenefPen extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct() {
      parent::Persistente();
      $this->setEstrutura( array() );
      $this->setEstruturaAuxiliar( array() );
      $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDados(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDados().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDados()
    {
        $stSql = " SELECT 1 AS tipo_registro
                        , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                        , 15 AS tipo
                        , (SELECT registro FROM pessoal".$this->getDado('entidade_rh').".contrato WHERE contrato.cod_contrato = contrato_pensionista.cod_contrato_cedente) AS matricula_funcionario
                        , contrato_pensionista.dt_inicio_beneficio AS dt_validade
                        , (SELECT registro FROM pessoal".$this->getDado('entidade_rh').".contrato WHERE contrato.cod_contrato = contrato_pensionista.cod_contrato) AS seq_benef
                        , sw_cgm.nom_cgm AS nom_benef
                        , ".$this->getDado('exercicio')."||TO_CHAR(contrato_pensionista.dt_inicio_beneficio,'mm') AS competencia
                        , sw_cgm_pessoa_fisica.cpf
                        , contrato_pensionista.percentual_pagamento AS percent_pensao
                        , contrato_pensionista.dt_inicio_beneficio AS dt_ato

                    FROM pessoal".$this->getDado('entidade_rh').".pensionista

              INNER JOIN pessoal".$this->getDado('entidade_rh').".contrato_pensionista
                      ON contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
                     AND contrato_pensionista.cod_pensionista = pensionista.cod_pensionista

              INNER JOIN sw_cgm
                      ON sw_cgm.numcgm = pensionista.numcgm

              INNER JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                   WHERE contrato_pensionista.dt_inicio_beneficio <= TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy')
                     AND ((contrato_pensionista.dt_encerramento >= TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')) OR (contrato_pensionista.dt_encerramento IS NULL))
        ";
        
        return $stSql;
    }

}
