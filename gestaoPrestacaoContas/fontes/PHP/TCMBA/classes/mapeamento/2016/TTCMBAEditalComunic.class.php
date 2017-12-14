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
    * Página de Mapeamento para o arquivo EditalComunic, para o TCMBA
    * Data de Criação   : 11/09/2015

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @ignore

    $Revision: 63573 $
    $Name: $
    $Author: franver $
    $Date: 2015-09-11 11:49:27 -0300 (Sex, 11 Set 2015) $
    $Id: TTCMBAEditalComunic.class.php 63573 2015-09-11 14:49:27Z franver $
*/
include_once CLA_PERSISTENTE;

class TTCMBAEditalComunic extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }
    function recuperaEditalComunic(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaEditalComunic().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaEditalComunic()
    {
        $stSql = "
     SELECT *
       FROM (
              SELECT 1 AS tipo_registro
                   , ".$this->getDado('inCodGestora')." AS unidade_gestora
                   , LPAD(edital.exercicio, 4, '0')||LPAD(edital.num_edital::VARCHAR, 12,'0') AS nro_edital
                   , CASE licitacao.cod_modalidade
                          WHEN 1 THEN CASE WHEN licitacao.registro_precos IS TRUE
                                           THEN 16
                                           WHEN licitacao.cod_tipo_objeto = 1
                                           THEN 5
                                           WHEN licitacao.cod_tipo_objeto = 2
                                           THEN 6
                                           ELSE licitacao.cod_tipo_objeto
                                       END
                          WHEN 2 THEN CASE WHEN licitacao.registro_precos IS TRUE
                                           THEN 17
                                           WHEN licitacao.cod_tipo_objeto = 1
                                           THEN 10
                                           WHEN licitacao.cod_tipo_objeto = 2
                                           THEN 12
                                       END
                          WHEN 3 THEN CASE WHEN licitacao.registro_precos IS TRUE
                                           THEN 3
                                           WHEN licitacao.cod_tipo_objeto = 3
                                           THEN 23
                                           WHEN licitacao.cod_tipo_objeto = 4
                                           THEN 22
                                           ELSE licitacao.cod_tipo_objeto
                                       END
                          WHEN 4 THEN 7
                          WHEN 5 THEN 4
                          WHEN 6 THEN CASE WHEN licitacao.registro_precos IS TRUE
                                           THEN 18
                                           ELSE 14
                                       END
                          WHEN 7 THEN CASE WHEN licitacao.registro_precos IS TRUE
                                           THEN 19
                                           ELSE 15
                                       END
                      END AS cod_modalidade_tce
                   , CASE WHEN LENGTH(regexp_replace(sw_cgm.fone_residencial, '[-A-Za-z()\s\.]', '','gi')) = 8
                          THEN '0'
                          ELSE SUBSTRING(regexp_replace(sw_cgm.fone_residencial, '[-A-Za-z()\s\.]', '','gi'), 1,(LENGTH(regexp_replace(sw_cgm.fone_residencial, '[-A-Za-z()\s\.]', '','gi')) - 8))
                      END AS nu_ddd
                   , CASE WHEN LENGTH(regexp_replace(sw_cgm.fone_residencial, '[-A-Za-z()\s\.]', '','gi')) = 8
                          THEN sw_cgm.fone_residencial
                          ELSE SUBSTRING(regexp_replace(sw_cgm.fone_residencial, '[-A-Za-z()\s\.]', '','gi'), (LENGTH(regexp_replace(sw_cgm.fone_residencial, '[-A-Za-z()\s\.]', '','gi')) - 7))
                      END AS nu_telefone_1
                   , CASE WHEN LENGTH(regexp_replace(sw_cgm.fone_comercial, '[-A-Za-z()\s\.]', '','gi')) = 8
                          THEN sw_cgm.fone_comercial
                          ELSE SUBSTRING(regexp_replace(sw_cgm.fone_comercial, '[-A-Za-z()\s\.]', '','gi'), (LENGTH(regexp_replace(sw_cgm.fone_comercial, '[-A-Za-z()\s\.]', '','gi')) - 7))
                      END AS nu_telefone_2
                   , CASE WHEN LENGTH(regexp_replace(sw_cgm.fone_celular, '[-A-Za-z()\s\.]', '','gi')) = 8
                          THEN sw_cgm.fone_celular
                          ELSE SUBSTRING(regexp_replace(sw_cgm.fone_celular, '[-A-Za-z()\s\.]', '','gi'), (LENGTH(regexp_replace(sw_cgm.fone_celular, '[-A-Za-z()\s\.]', '','gi')) - 7))
                      END AS nu_telefone_3
                   , sw_cgm.site AS site_unidade_licitacao
                   , divulgacao_oficial.site AS site_divugacao_oficial
                   , responsavel_licitacao.e_mail
                   , TO_CHAR(edital.dt_aprovacao_juridico, 'yyyymm') AS competencia
                FROM licitacao.edital
          INNER JOIN licitacao.licitacao
                  ON licitacao.cod_licitacao  = edital.cod_licitacao
                 AND licitacao.cod_modalidade = edital.cod_modalidade
                 AND licitacao.cod_entidade   = edital.cod_entidade
                 AND licitacao.exercicio      = edital.exercicio_licitacao
           LEFT JOIN sw_cgm AS responsavel_licitacao
                  ON responsavel_licitacao.numcgm = edital.responsavel_juridico
          INNER JOIN licitacao.publicacao_edital
                  ON publicacao_edital.num_edital = edital.num_edital
                 AND publicacao_edital.exercicio  = edital.exercicio
           LEFT JOIN sw_cgm AS divulgacao_oficial
                  ON divulgacao_oficial.numcgm = publicacao_edital.numcgm
          INNER JOIN orcamento.entidade
                  ON entidade.exercicio = licitacao.exercicio
                 AND entidade.cod_entidade = licitacao.cod_entidade
          INNER JOIN sw_cgm
                  ON sw_cgm.numcgm = entidade.numcgm
               WHERE edital.exercicio = '".$this->getDado('stExercicio')."'
                 AND edital.cod_entidade IN (".$this->getDado('stEntidade').")
                 AND edital.dt_aprovacao_juridico BETWEEN TO_DATE('".$this->getDado('dtInicio')."', 'DD/MM/YYYY')
                                                      AND TO_DATE('".$this->getDado('dtFim')."', 'DD/MM/YYYY')
            GROUP BY nro_edital
                   , cod_modalidade_tce
                   , nu_ddd
                   , nu_telefone_1
                   , nu_telefone_2
                   , nu_telefone_3
                   , site_unidade_licitacao
                   , site_divugacao_oficial
                   , responsavel_licitacao.e_mail
                   , competencia
            ORDER BY nro_edital
            ) AS edital_comunic
      WHERE edital_comunic.cod_modalidade_tce IS NOT NULL

        ";
        return $stSql;
    }

    public function __destruct() {}
}

?>