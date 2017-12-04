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
    * Data de Criação   : 28/10/2014

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor:  Michel Teixeira
    $Id: TTCEPEHistoricoPessoal.class.php 60582 2014-10-31 13:38:10Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEHistoricoPessoal extends Persistente
{
    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEHistoricoPessoal()
    {
        parent::Persistente();
    }

    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaHistoricoPessoal.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaHistoricoPessoal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaHistoricoPessoal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaHistoricoPessoal()
    {
        $stSql = "
               --SERVIDOR
                   SELECT *
                     FROM (
                               SELECT contrato.registro AS matricula
                                    , sw_cgm_pessoa_fisica.cpf
                                    , CASE WHEN sw_nome_logradouro.nom_logradouro IS NOT NULL
                                                THEN TRIM(sw_nome_logradouro.nom_logradouro)
                                                ELSE TRIM(sw_cgm.logradouro)
                                      END AS nom_logradouro
                                    , sw_cgm.numero
                                    , sw_cgm.complemento
                                    , TRIM(sw_cgm.bairro) AS bairro
                                    , CASE WHEN sw_cgm_logradouro.cep IS NOT NULL
                                                THEN sw_cgm_logradouro.cep
                                                ELSE TRIM(sw_cgm.cep)
                                      END AS cep
                                    , sw_municipio.nom_municipio
                                    , sw_uf.sigla_uf
                                    , sw_cgm.fone_residencial
                                    , sw_cgm.fone_celular
                                    , CASE WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (0)     THEN 0
                                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (1,2)   THEN 1
			                               WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (4,5)   THEN 2
			                               WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (6,7)   THEN 3
			                               WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (8)     THEN 4
			                               WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (9)     THEN 5
			                               WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (14,15) THEN 6
			                               WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (10,12) THEN 7
			                               WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (11,13) THEN 8
                                      END AS escolaridade
                                    , TO_CHAR(periodo_movimentacao.dt_final, 'ddmmyyyy') AS dt_alteracao
                                    , banco.num_banco
                                    , lpad(regexp_replace(agencia.num_agencia,'[.|-]','','gi'),6,'0') as num_agencia
                                    , lpad(regexp_replace(ultimo_contrato_servidor_conta_salario.nr_conta,'[.|-]','','gi'),12,'0') as num_conta
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
                                   ON sw_municipio.cod_uf=sw_uf.cod_uf

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
                                   ON registro_evento_periodo.cod_periodo_movimentacao  = periodo_movimentacao.cod_periodo_movimentacao
                                  AND registro_evento_periodo.cod_contrato              = servidor_contrato_servidor.cod_contrato

                            LEFT JOIN ultimo_contrato_servidor_conta_salario( '".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao')." ) AS ultimo_contrato_servidor_conta_salario
                                   ON ultimo_contrato_servidor_conta_salario.cod_contrato = contrato.cod_contrato

                            LEFT JOIN monetario.banco
                                   ON ultimo_contrato_servidor_conta_salario.cod_banco = banco.cod_banco

                            LEFT JOIN monetario.agencia
                                   ON ultimo_contrato_servidor_conta_salario.cod_banco      = agencia.cod_banco
                                  AND ultimo_contrato_servidor_conta_salario.cod_agencia    = agencia.cod_agencia

                             GROUP BY sw_cgm.nom_cgm
                                    , sw_cgm_pessoa_fisica.cpf
                                    , sw_nome_logradouro.nom_logradouro
                                    , sw_cgm.numero
                                    , sw_cgm.logradouro
                                    , sw_cgm.complemento
                                    , sw_cgm.bairro
                                    , sw_cgm.cep
                                    , sw_cgm.fone_residencial
                                    , sw_cgm.fone_celular
                                    , contrato.cod_contrato
                                    , sw_cgm_logradouro.cod_logradouro
                                    , sw_cgm_logradouro.cep
                                    , sw_municipio.nom_municipio
                                    , sw_uf.sigla_uf
                                    , sw_cgm_pessoa_fisica.cod_escolaridade
                                    , periodo_movimentacao.dt_final
                                    , periodo_movimentacao.cod_periodo_movimentacao
                                    , banco.num_banco
                                    , agencia.num_agencia
                                    , ultimo_contrato_servidor_conta_salario.cod_agencia
                                    , ultimo_contrato_servidor_conta_salario.cod_banco
                                    , ultimo_contrato_servidor_conta_salario.nr_conta
                          ) AS servidor
                UNION ALL
                
            --PENSIONISTA
                   SELECT *
                     FROM (
                               SELECT contrato.registro AS matricula
                                    , sw_cgm_pessoa_fisica.cpf
                                    , CASE WHEN sw_nome_logradouro.nom_logradouro IS NOT NULL
                                                THEN TRIM(sw_nome_logradouro.nom_logradouro)
                                                ELSE TRIM(sw_cgm.logradouro)
                                      END AS nom_logradouro
                                    , sw_cgm.numero
                                    , sw_cgm.complemento
                                    , TRIM(sw_cgm.bairro) AS bairro
                                    , CASE WHEN sw_cgm_logradouro.cep IS NOT NULL
                                                THEN sw_cgm_logradouro.cep
                                                ELSE TRIM(sw_cgm.cep)
                                      END AS cep
                                    , sw_municipio.nom_municipio
                                    , sw_uf.sigla_uf
                                    , sw_cgm.fone_residencial
                                    , sw_cgm.fone_celular
                                    , CASE WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (0)     THEN 0
                                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (1,2)   THEN 1
                                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (4,5)   THEN 2
                                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (6,7)   THEN 3
                                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (8)     THEN 4
                                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (9)     THEN 5
                                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (14,15) THEN 6
                                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (10,12) THEN 7
                                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (11,13) THEN 8
                                      END AS escolaridade
                                    , TO_CHAR(periodo_movimentacao.dt_final, 'ddmmyyyy') AS dt_alteracao
                                    , banco.num_banco
                                    , lpad(regexp_replace(agencia.num_agencia,'[.|-]','','gi'),6,'0') as num_agencia
                                    , lpad(regexp_replace(ultimo_contrato_pensionista_conta_salario.nr_conta,'[.|-]','','gi'),12,'0')  as num_conta
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
                                   ON sw_municipio.cod_uf=sw_uf.cod_uf

                           INNER JOIN pessoal".$this->getDado('stEntidades').".pensionista
                                   ON pensionista.numcgm = sw_cgm.numcgm

                           INNER JOIN pessoal".$this->getDado('stEntidades').".contrato_pensionista
                                   ON contrato_pensionista.cod_pensionista=pensionista.cod_pensionista
                                  AND contrato_pensionista.cod_contrato_cedente=pensionista.cod_contrato_cedente

                           INNER JOIN pessoal".$this->getDado('stEntidades').".contrato
                                   ON contrato.cod_contrato=contrato_pensionista.cod_contrato

                           INNER JOIN pessoal".$this->getDado('stEntidades').".contrato_servidor
                                   ON contrato_servidor.cod_contrato = pensionista.cod_contrato_cedente

                           INNER JOIN pessoal".$this->getDado('stEntidades').".servidor_contrato_servidor
                                   ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato

                           INNER JOIN pessoal".$this->getDado('stEntidades').".servidor
                                   ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                           INNER JOIN folhapagamento".$this->getDado('stEntidades').".periodo_movimentacao
                                   ON TO_CHAR(periodo_movimentacao.dt_final, 'mmyyyy') = '".$this->getDado('stMes')."'

                           LEFT JOIN folhapagamento".$this->getDado('stEntidades').".registro_evento_periodo
                                   ON registro_evento_periodo.cod_periodo_movimentacao  = periodo_movimentacao.cod_periodo_movimentacao
                                  AND registro_evento_periodo.cod_contrato              = contrato.cod_contrato

                            LEFT JOIN ultimo_contrato_pensionista_conta_salario( '".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao')." ) AS ultimo_contrato_pensionista_conta_salario
                                   ON ultimo_contrato_pensionista_conta_salario.cod_contrato = contrato.cod_contrato

                            LEFT JOIN monetario.banco
                                   ON ultimo_contrato_pensionista_conta_salario.cod_banco = banco.cod_banco

                            LEFT JOIN monetario.agencia
                                   ON ultimo_contrato_pensionista_conta_salario.cod_banco      = agencia.cod_banco
                                  AND ultimo_contrato_pensionista_conta_salario.cod_agencia    = agencia.cod_agencia

                             GROUP BY sw_cgm.nom_cgm
                                    , sw_cgm_pessoa_fisica.cpf
                                    , sw_nome_logradouro.nom_logradouro
                                    , sw_cgm.numero
                                    , sw_cgm.logradouro
                                    , sw_cgm.complemento
                                    , sw_cgm.bairro
                                    , sw_cgm.cep
                                    , sw_cgm.fone_residencial
                                    , sw_cgm.fone_celular
                                    , contrato.cod_contrato
                                    , sw_cgm_logradouro.cod_logradouro
                                    , sw_cgm_logradouro.cep
                                    , sw_municipio.nom_municipio
                                    , sw_uf.sigla_uf
                                    , sw_cgm_pessoa_fisica.cod_escolaridade
                                    , periodo_movimentacao.dt_final
                                    , periodo_movimentacao.cod_periodo_movimentacao
                                    , banco.num_banco
                                    , agencia.num_agencia
                                    , ultimo_contrato_pensionista_conta_salario.cod_agencia
                                    , ultimo_contrato_pensionista_conta_salario.cod_banco
                                    , ultimo_contrato_pensionista_conta_salario.nr_conta
                          ) AS pensionista

                 ORDER BY matricula
                ";
        return $stSql;
    }

}
?>