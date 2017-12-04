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
    * Arquivo de geracao do arquivo sertTerceiros TCM/MG
    * Data de Criação   : 19/01/2009
    * 
    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Henrique Boaventura
    * 
    * @package URBEM
    * @subpackage
    * 
    * @ignore
    * 
    * $Id: TTCMBADiarias.class.php 63494 2015-09-02 16:50:48Z evandro $
    * $Rev: 63494 $
    * $Author: evandro $
    * $Date: 2015-09-02 13:50:48 -0300 (Wed, 02 Sep 2015) $
    * 
*/
//include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBADiarias extends Persistente {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaDiarias(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDiarias().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaDiarias()
    {
        $stSql = "
              SELECT 1 AS tipo_registro
                   , (SELECT valor
                        FROM administracao.configuracao_entidade
                       WHERE cod_modulo = 45
                         AND parametro = 'tceba_codigo_unidade_gestora'
                         AND cod_entidade = '".$this->getDado('entidade')."'
                     ) AS unidade_gestora
                   , autorizacao_empenho.num_unidade AS cod_unidade_orcamentaria
                   , empenho.cod_empenho AS nu_empenho
                   , autorizacao_empenho.dt_autorizacao AS dt_pagamento_empenho
                   , contrato.registro AS nu_matricula_funcionario
                   , ".$this->getDado('exercicio')." AS dt_ano
                   , sw_cgm.nom_cgm AS nm_funcionario
                   , diaria.motivo AS de_motivo_viagem
                   , diaria.dt_inicio AS dt_saida
                   , diaria.cod_diaria AS cod_regra
                   , diaria.dt_termino AS dt_retorno
                   , 1 AS cod_sub_regra
                   , REPLACE(diaria.quantidade::varchar,'.','') AS qt_diarias
                   , diaria.vl_total AS vl_total_diarias
                   , ".$this->getDado('periodo')." AS competencia
                   , autorizacao_empenho.num_orgao AS cod_orgao
                   , empenho.cod_empenho AS nu_empenho_sub
                   , 1 AS nu_diaria
                FROM diarias".$this->getDado('entidade_rh').".diaria
          INNER JOIN diarias".$this->getDado('entidade_rh').".diaria_empenho
                  ON diaria_empenho.cod_diaria   = diaria.cod_diaria
                 AND diaria_empenho.timestamp    = diaria.timestamp
                 AND diaria_empenho.cod_contrato = diaria.cod_contrato
          INNER JOIN empenho.autorizacao_empenho
                  ON autorizacao_empenho.exercicio       = diaria_empenho.exercicio
                 AND autorizacao_empenho.cod_entidade    = diaria_empenho.cod_entidade
                 AND autorizacao_empenho.cod_autorizacao = diaria_empenho.cod_autorizacao
          INNER JOIN empenho.empenho_autorizacao
                  ON empenho_autorizacao.exercicio       = autorizacao_empenho.exercicio
                 AND empenho_autorizacao.cod_entidade    = autorizacao_empenho.cod_entidade
                 AND empenho_autorizacao.cod_autorizacao = autorizacao_empenho.cod_autorizacao
          INNER JOIN empenho.empenho
                  ON empenho.exercicio    = empenho_autorizacao.exercicio
                 AND empenho.cod_entidade = empenho_autorizacao.cod_entidade
                 AND empenho.cod_empenho  = empenho_autorizacao.cod_empenho
          INNER JOIN pessoal".$this->getDado('entidade_rh').".contrato
                  ON contrato.cod_contrato = diaria.cod_contrato
          INNER JOIN pessoal".$this->getDado('entidade_rh').".contrato_servidor
                  ON contrato_servidor.cod_contrato = contrato.cod_contrato
          INNER JOIN pessoal".$this->getDado('entidade_rh').".servidor_contrato_servidor
                  ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
          INNER JOIN pessoal".$this->getDado('entidade_rh').".servidor
                  ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
          INNER JOIN sw_cgm_pessoa_fisica
                  ON sw_cgm_pessoa_fisica.numcgm = servidor.numcgm
          INNER JOIN sw_cgm
                  ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
               WHERE autorizacao_empenho.dt_autorizacao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy')
                                                            AND TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
        ";
        return $stSql;
    }
    
    /**
        * Método Destruct
        * @access Private
    */
    public function __destruct(){}
}


?>