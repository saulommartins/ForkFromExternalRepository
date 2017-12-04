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
    * Data de Criação: 30/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGOIde.class.php 37992 2009-02-10 18:07:46Z eduardoschitz $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOUOC extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function TTGOUOC()
    {
    parent::Persistente();

    $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaUnidade(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;

       $stSql = $this->montaRecuperaUnidade();
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

       return $obErro;
    }

    public function montaRecuperaUnidade()
    {
    $stSql = "SELECT MAX(unidade_responsavel.timestamp) AS timestamp,
               unidade_responsavel.num_orgao,
               unidade_responsavel.num_unidade,
               unidade_responsavel.exercicio,
               unidade.nom_unidade,
               '10'::VARCHAR AS tipo_registro,
               '1' AS numero_registro,
               '00' AS num_consolidacao

          FROM tcmgo.unidade_responsavel

        INNER JOIN orcamento.unidade
            ON unidade.num_orgao   = unidade_responsavel.num_orgao
           AND unidade.num_unidade = unidade_responsavel.num_unidade
           AND unidade.exercicio   = unidade_responsavel.exercicio

        INNER JOIN orcamento.orgao
            ON unidade.num_orgao   = unidade_responsavel.num_orgao
           AND unidade.exercicio   = unidade_responsavel.exercicio

         WHERE unidade_responsavel.exercicio = '".$this->getDado('exercicio')."'

          GROUP BY unidade_responsavel.num_orgao,
               unidade_responsavel.num_unidade,
               unidade_responsavel.exercicio,
               unidade.nom_unidade";

    return $stSql;
    }

    public function recuperaGestorDespesa(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;

       $stSql = $this->montaRecuperaGestorDespesa();
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

       return $obErro;
    }

    public function montaRecuperaGestorDespesa()
    {
    $stSql = "SELECT '11'::VARCHAR AS tipo_registro,
            unidade_responsavel.num_orgao,
            unidade_responsavel.num_unidade,
            sw_cgm_pessoa_fisica.cpf,
            to_char(unidade_responsavel.gestor_dt_inicio, 'ddmmyyyy') AS dt_inicio,
            unidade_responsavel.tipo_responsavel,
            to_char(unidade_responsavel.gestor_dt_fim, 'ddmmyyyy') AS dt_fim,
            sw_cgm.nom_cgm,
            unidade_responsavel.gestor_cargo,
            sw_cgm.logradouro,
            sw_cgm.bairro,
            sw_municipio.nom_municipio,
            sw_uf.sigla_uf,
            sw_cgm.cep,
            COALESCE(sw_cgm.fone_comercial, sw_cgm.fone_celular, sw_cgm.fone_residencial) AS telefone,
            sw_cgm.e_mail, ";
    if (Sessao::getExercicio() > 2012) {
        $stSql .= "
            CASE WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 0 OR sw_cgm_pessoa_fisica.cod_escolaridade = 1 OR sw_cgm_pessoa_fisica.cod_escolaridade = 4 THEN
                '01'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 5 OR sw_cgm_pessoa_fisica.cod_escolaridade = 2 THEN
                '02'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 6 THEN
                '03'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 7 THEN
                '04'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 8 THEN
                '05'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 9 THEN
                '06'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 15 THEN
                '07'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 14 THEN
                '08'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 12 THEN
                '09'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 10 THEN
                '10'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 13 THEN
                '11'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 11 THEN
                '12'
           END AS escolaridade, ";
    }
    $stSql .= "
           '1' AS numero_registro

        FROM tcmgo.unidade_responsavel

        INNER JOIN ( SELECT MAX(unidade_responsavel.timestamp) AS timestamp,
                unidade_responsavel.num_orgao,
                unidade_responsavel.num_unidade,
                unidade_responsavel.exercicio
               FROM tcmgo.unidade_responsavel
               GROUP BY unidade_responsavel.num_orgao,
                unidade_responsavel.num_unidade,
                unidade_responsavel.exercicio
          ) AS max_unidade_responsavel
          ON max_unidade_responsavel.num_orgao   = unidade_responsavel.num_orgao
         AND max_unidade_responsavel.num_unidade = unidade_responsavel.num_unidade
         AND max_unidade_responsavel.exercicio   = unidade_responsavel.exercicio
         AND max_unidade_responsavel.timestamp   = unidade_responsavel.timestamp

        INNER JOIN sw_cgm
            ON sw_cgm.numcgm = unidade_responsavel.cgm_gestor

        INNER JOIN sw_cgm_pessoa_fisica
            ON sw_cgm_pessoa_fisica.numcgm = unidade_responsavel.cgm_gestor

         LEFT JOIN sw_municipio
            ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
           AND sw_municipio.cod_uf 	  = sw_cgm.cod_uf

         LEFT JOIN sw_uf
            ON sw_uf.cod_uf = sw_municipio.cod_uf

             WHERE unidade_responsavel.exercicio = '".$this->getDado('exercicio')."'";

    return $stSql;
    }

    public function recuperaContador(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;

       $stSql = $this->montaRecuperaContador();
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

       return $obErro;
    }

    public function montaRecuperaContador()
    {
    $stSql = "SELECT '12'::VARCHAR AS tipo_registro,
            unidade_responsavel.num_orgao,
            unidade_responsavel.num_unidade,
            sw_cgm_pessoa_fisica.cpf,
            to_char(unidade_responsavel.contador_dt_inicio, 'ddmmyyyy') AS dt_inicio,
            to_char(unidade_responsavel.contador_dt_fim, 'ddmmyyyy') AS dt_fim,
            sw_cgm.nom_cgm,
            unidade_responsavel.contador_crc,
            sw_uf_crc.sigla_uf AS uf_crc,
            unidade_responsavel.cod_provimento_contabil,
            sw_cgm_pessoa_juridica.cnpj,
            sw_cgm_pessoa_juridica.nom_cgm AS razao,
            sw_cgm.logradouro,
            sw_cgm.bairro,
            sw_municipio.nom_municipio,
            sw_uf.sigla_uf,
            sw_cgm.cep,
            COALESCE(sw_cgm.fone_comercial, sw_cgm.fone_celular, sw_cgm.fone_residencial) AS telefone,
            sw_cgm.e_mail, ";
    if (Sessao::getExercicio() > 2012) {
        $stSql .= "
            CASE WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 0 OR sw_cgm_pessoa_fisica.cod_escolaridade = 1 OR sw_cgm_pessoa_fisica.cod_escolaridade = 4 THEN
                '01'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 5 OR sw_cgm_pessoa_fisica.cod_escolaridade = 2 THEN
                '02'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 6 THEN
                '03'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 7 THEN
                '04'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 8 THEN
                '05'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 9 THEN
                '06'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 15 THEN
                '07'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 14 THEN
                '08'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 12 THEN
                '09'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 10 THEN
                '10'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 13 THEN
                '11'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 11 THEN
                '12'
           END AS escolaridade, ";
    }
    $stSql .= "
           '1' AS numero_registro

        FROM tcmgo.unidade_responsavel

        INNER JOIN ( SELECT MAX(unidade_responsavel.timestamp) AS timestamp,
                unidade_responsavel.num_orgao,
                unidade_responsavel.num_unidade,
                unidade_responsavel.exercicio
               FROM tcmgo.unidade_responsavel
               GROUP BY unidade_responsavel.num_orgao,
                unidade_responsavel.num_unidade,
                unidade_responsavel.exercicio
          ) AS max_unidade_responsavel
          ON max_unidade_responsavel.num_orgao   = unidade_responsavel.num_orgao
         AND max_unidade_responsavel.num_unidade = unidade_responsavel.num_unidade
         AND max_unidade_responsavel.exercicio   = unidade_responsavel.exercicio
         AND max_unidade_responsavel.timestamp   = unidade_responsavel.timestamp

        INNER JOIN sw_cgm
            ON sw_cgm.numcgm = unidade_responsavel.cgm_contador

        INNER JOIN sw_cgm_pessoa_fisica
            ON sw_cgm_pessoa_fisica.numcgm = unidade_responsavel.cgm_contador

        INNER JOIN sw_uf AS sw_uf_crc
            ON sw_uf_crc.cod_uf = unidade_responsavel.uf_crc

         LEFT JOIN tcmgo.contador_terceirizado
            ON contador_terceirizado.num_orgao   = unidade_responsavel.num_orgao
           AND contador_terceirizado.num_unidade = unidade_responsavel.num_unidade
           AND contador_terceirizado.exercicio   = unidade_responsavel.exercicio
           AND contador_terceirizado.timestamp   = unidade_responsavel.timestamp

         LEFT JOIN (   SELECT sw_cgm_pessoa_juridica.cnpj,
                  sw_cgm_pessoa_juridica.numcgm,
                  sw_cgm.nom_cgm
                 FROM sw_cgm_pessoa_juridica
               INNER JOIN sw_cgm
                   ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm
              ) AS sw_cgm_pessoa_juridica
           ON sw_cgm_pessoa_juridica.numcgm = contador_terceirizado.numcgm

         LEFT JOIN sw_municipio
            ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
           AND sw_municipio.cod_uf 	  = sw_cgm.cod_uf

         LEFT JOIN sw_uf
            ON sw_uf.cod_uf = sw_municipio.cod_uf

             WHERE unidade_responsavel.exercicio = '".$this->getDado('exercicio')."'";

    return $stSql;
    }

    public function recuperaControleInterno(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;

       $stSql = $this->montaRecuperaControleInterno();
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

       return $obErro;
    }

    public function montaRecuperaControleInterno()
    {
    $stSql = "SELECT '13'::VARCHAR AS tipo_registro,
            unidade_responsavel.num_orgao,
            unidade_responsavel.num_unidade,
            sw_cgm_pessoa_fisica.cpf,
            to_char(unidade_responsavel.controle_interno_dt_inicio, 'ddmmyyyy') AS dt_inicio,
            to_char(unidade_responsavel.controle_interno_dt_fim, 'ddmmyyyy') AS dt_fim,
            sw_cgm.nom_cgm,
            sw_cgm.logradouro,
            sw_cgm.bairro,
            sw_municipio.nom_municipio,
            sw_uf.sigla_uf,
            sw_cgm.cep,
            COALESCE(sw_cgm.fone_comercial, sw_cgm.fone_celular, sw_cgm.fone_residencial) AS telefone,
            sw_cgm.e_mail, ";
    if (Sessao::getExercicio() > 2012) {
        $stSql .= "
            CASE WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 0 OR sw_cgm_pessoa_fisica.cod_escolaridade = 1 OR sw_cgm_pessoa_fisica.cod_escolaridade = 4 THEN
                '01'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 5 OR sw_cgm_pessoa_fisica.cod_escolaridade = 2 THEN
                '02'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 6 THEN
                '03'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 7 THEN
                '04'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 8 THEN
                '05'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 9 THEN
                '06'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 15 THEN
                '07'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 14 THEN
                '08'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 12 THEN
                '09'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 10 THEN
                '10'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 13 THEN
                '11'
                 WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 11 THEN
                '12'
           END AS escolaridade, ";
    }
    $stSql .= "
           '1' AS numero_registro

        FROM tcmgo.unidade_responsavel

        INNER JOIN ( SELECT MAX(unidade_responsavel.timestamp) AS timestamp,
                unidade_responsavel.num_orgao,
                unidade_responsavel.num_unidade,
                unidade_responsavel.exercicio
               FROM tcmgo.unidade_responsavel
               GROUP BY unidade_responsavel.num_orgao,
                unidade_responsavel.num_unidade,
                unidade_responsavel.exercicio
          ) AS max_unidade_responsavel
          ON max_unidade_responsavel.num_orgao   = unidade_responsavel.num_orgao
         AND max_unidade_responsavel.num_unidade = unidade_responsavel.num_unidade
         AND max_unidade_responsavel.exercicio   = unidade_responsavel.exercicio
         AND max_unidade_responsavel.timestamp   = unidade_responsavel.timestamp

        INNER JOIN sw_cgm
            ON sw_cgm.numcgm = unidade_responsavel.cgm_controle_interno

        INNER JOIN sw_cgm_pessoa_fisica
            ON sw_cgm_pessoa_fisica.numcgm = unidade_responsavel.cgm_controle_interno

         LEFT JOIN sw_municipio
            ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
           AND sw_municipio.cod_uf 	  = sw_cgm.cod_uf

         LEFT JOIN sw_uf
            ON sw_uf.cod_uf = sw_municipio.cod_uf

             WHERE unidade_responsavel.exercicio = '".$this->getDado('exercicio')."'";

    return $stSql;
    }

    public function recuperaJuridico(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;

       $stSql = $this->montaRecuperaJuridico();
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

       return $obErro;
    }

    public function montaRecuperaJuridico()
    {
    $stSql = "SELECT '14'::VARCHAR AS tipo_registro,
            unidade_responsavel.num_orgao,
            unidade_responsavel.num_unidade,
            sw_cgm_pessoa_fisica.cpf,
            to_char(unidade_responsavel.juridico_dt_inicio, 'ddmmyyyy') AS dt_inicio,
            to_char(unidade_responsavel.juridico_dt_fim, 'ddmmyyyy') AS dt_fim,
            sw_cgm.nom_cgm,
            unidade_responsavel.juridico_oab,
            sw_uf_oab.sigla_uf AS uf_oab,
            unidade_responsavel.cod_provimento_juridico,
            sw_cgm_pessoa_juridica.cnpj,
            sw_cgm_pessoa_juridica.nom_cgm AS razao,
            sw_cgm.logradouro,
            sw_cgm.bairro,
            sw_municipio.nom_municipio,
            sw_uf.sigla_uf,
            sw_cgm.cep,
            COALESCE(sw_cgm.fone_comercial, sw_cgm.fone_celular, sw_cgm.fone_residencial) AS telefone,
            sw_cgm.e_mail,
           '1' AS numero_registro

        FROM tcmgo.unidade_responsavel

        INNER JOIN ( SELECT MAX(unidade_responsavel.timestamp) AS timestamp,
                unidade_responsavel.num_orgao,
                unidade_responsavel.num_unidade,
                unidade_responsavel.exercicio
               FROM tcmgo.unidade_responsavel
               GROUP BY unidade_responsavel.num_orgao,
                unidade_responsavel.num_unidade,
                unidade_responsavel.exercicio
          ) AS max_unidade_responsavel
          ON max_unidade_responsavel.num_orgao   = unidade_responsavel.num_orgao
         AND max_unidade_responsavel.num_unidade = unidade_responsavel.num_unidade
         AND max_unidade_responsavel.exercicio   = unidade_responsavel.exercicio
         AND max_unidade_responsavel.timestamp   = unidade_responsavel.timestamp

        INNER JOIN sw_cgm
            ON sw_cgm.numcgm = unidade_responsavel.cgm_juridico

        INNER JOIN sw_cgm_pessoa_fisica
            ON sw_cgm_pessoa_fisica.numcgm = unidade_responsavel.cgm_juridico

        INNER JOIN sw_uf AS sw_uf_oab
            ON sw_uf_oab.cod_uf = unidade_responsavel.uf_oab

         LEFT JOIN tcmgo.juridico_terceirizado
            ON juridico_terceirizado.num_orgao   = unidade_responsavel.num_orgao
           AND juridico_terceirizado.num_unidade = unidade_responsavel.num_unidade
           AND juridico_terceirizado.exercicio   = unidade_responsavel.exercicio
           AND juridico_terceirizado.timestamp   = unidade_responsavel.timestamp

         LEFT JOIN (   SELECT sw_cgm_pessoa_juridica.cnpj,
                  sw_cgm_pessoa_juridica.numcgm,
                  sw_cgm.nom_cgm
                 FROM sw_cgm_pessoa_juridica
               INNER JOIN sw_cgm
                   ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm
              ) AS sw_cgm_pessoa_juridica
           ON sw_cgm_pessoa_juridica.numcgm = juridico_terceirizado.numcgm

         LEFT JOIN sw_municipio
            ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
           AND sw_municipio.cod_uf 	  = sw_cgm.cod_uf

         LEFT JOIN sw_uf
            ON sw_uf.cod_uf = sw_municipio.cod_uf

             WHERE unidade_responsavel.exercicio = '".$this->getDado('exercicio')."'";

    return $stSql;
    }
}
