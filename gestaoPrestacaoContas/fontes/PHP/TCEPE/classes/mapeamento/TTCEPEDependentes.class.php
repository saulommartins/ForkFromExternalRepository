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
    * 
    * Data de Criação   : 29/10/2014

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor:  Michel Teixeira
    $Id: TTCEPEDependentes.class.php 60656 2014-11-06 13:40:39Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEDependentes extends Persistente
{
    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEDependentes()
    {
        parent::Persistente();
    }

    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDependentes.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaDependentes(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDependentes().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDependentes()
    {
        $stSql = "
               --SERVIDOR
                   SELECT *
                     FROM (
                               SELECT contrato.registro AS matricula
                                    , sw_cgm_pessoa_fisica.cpf
                                    , dependente.cpf_dependente
                                    , dependente.nom_dependente
                                    , TO_CHAR(dependente.dt_nascimento, 'ddmmyyyy') AS dt_nascimento
                                    , CASE WHEN dependente.cod_grau IN (2,3)
                                                THEN 1
                                           WHEN dependente.cod_grau IN (4)
                                                THEN 3
                                           WHEN dependente.cod_grau IN (17)
                                                THEN 4
                                           WHEN dependente.cod_grau IN (1,5)
                                                THEN 6
                                           WHEN dependente.cod_grau IN (0,6,7,8,9,10,11,12,13,14,15,16,18,19,20)
                                                THEN 9
                                      END AS tipo_parentesco
                                    , TO_CHAR(servidor_dependente.dt_inicio, 'ddmmyyyy') AS dt_inicio
                                    , sw_cgm.nom_cgm
                                    , contrato.cod_contrato
                                    , periodo_movimentacao.cod_periodo_movimentacao

                                 FROM sw_cgm

                           INNER JOIN sw_cgm_pessoa_fisica
                                   ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm

                            LEFT JOIN sw_cgm_logradouro
                                   ON sw_cgm.numcgm = sw_cgm_logradouro.numcgm

                            LEFT JOIN ( SELECT DISTINCT sw_nome_logradouro.cod_logradouro, sw_nome_logradouro.nom_logradouro, MAX(timestamp) 
                                          FROM sw_nome_logradouro
                                      GROUP BY sw_nome_logradouro.cod_logradouro, sw_nome_logradouro.nom_logradouro, timestamp
                                      ) AS sw_nome_logradouro
                                   ON sw_cgm_logradouro.cod_logradouro = sw_nome_logradouro.cod_logradouro

                            LEFT JOIN sw_municipio
                                   ON (    sw_cgm_logradouro.cod_uf         = sw_municipio.cod_uf
                                       AND sw_cgm_logradouro.cod_municipio  = sw_municipio.cod_municipio
                                      )
                                   OR (    sw_cgm.cod_uf                    = sw_municipio.cod_uf
                                       AND sw_cgm.cod_municipio             = sw_municipio.cod_municipio
                                      )

                            LEFT JOIN sw_uf
                                   ON sw_municipio.cod_uf = sw_uf.cod_uf

                           INNER JOIN pessoal".$this->getDado('stEntidades').".servidor
                                   ON servidor.numcgm = sw_cgm.numcgm

                           INNER JOIN pessoal".$this->getDado('stEntidades').".servidor_contrato_servidor 
                                   ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                           INNER JOIN pessoal".$this->getDado('stEntidades').".contrato
                                   ON servidor_contrato_servidor.cod_contrato = contrato.cod_contrato

                           INNER JOIN pessoal".$this->getDado('stEntidades').".contrato_servidor
                                   ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato

                           INNER JOIN pessoal".$this->getDado('stEntidades').".contrato_servidor_nomeacao_posse
                                   ON contrato_servidor_nomeacao_posse.cod_contrato = contrato.cod_contrato

                           INNER JOIN folhapagamento".$this->getDado('stEntidades').".periodo_movimentacao
                                   ON TO_CHAR(periodo_movimentacao.dt_final, 'mmyyyy') = '".$this->getDado('stMes')."'

                           INNER JOIN folhapagamento".$this->getDado('stEntidades').".registro_evento_periodo
                                   ON registro_evento_periodo.cod_periodo_movimentacao = ".$this->getDado('inCodMovimentacao')."
                                  AND registro_evento_periodo.cod_contrato             = servidor_contrato_servidor.cod_contrato

                           INNER JOIN pessoal".$this->getDado('stEntidades').".servidor_dependente
                                   ON servidor_dependente.cod_servidor = servidor.cod_servidor

                           INNER JOIN ( SELECT dependente.*
                                             , sw_cgm_pessoa_fisica.cpf AS cpf_dependente
                                             , sw_cgm.nom_cgm AS nom_dependente
                                             , sw_cgm_pessoa_fisica.dt_nascimento
                                             , dependente_excluido.data_exclusao
                                          FROM pessoal".$this->getDado('stEntidades').".dependente
                                          JOIN sw_cgm_pessoa_fisica
                                            ON dependente.numcgm = sw_cgm_pessoa_fisica.numcgm
                                          JOIN sw_cgm
                                            ON sw_cgm.numcgm	 = sw_cgm_pessoa_fisica.numcgm
                                     LEFT JOIN pessoal.dependente_excluido
                                            ON dependente_excluido.cod_dependente=dependente.cod_dependente
                                           AND TO_DATE(TO_CHAR(dependente_excluido.data_exclusao, 'mmyyyy'), 'mmyyyy') <= TO_DATE('".$this->getDado('stMes')."', 'mmyyyy')
                                      ) AS dependente
                                   ON dependente.cod_dependente  = servidor_dependente.cod_dependente
                                  AND dependente.data_exclusao IS NULL

                             GROUP BY sw_cgm.nom_cgm
                                    , sw_cgm_pessoa_fisica.cpf
                                    , contrato.registro
                                    , contrato.cod_contrato
                                    , dependente.cpf_dependente
                                    , dependente.nom_dependente
                                    , dependente.dt_nascimento
                                    , dependente.cod_grau
                                    , servidor_dependente.dt_inicio
                                    , periodo_movimentacao.dt_final
                                    , periodo_movimentacao.cod_periodo_movimentacao
                          ) AS servidor

                 ORDER BY matricula
                ";
        return $stSql;
    }

}
?>