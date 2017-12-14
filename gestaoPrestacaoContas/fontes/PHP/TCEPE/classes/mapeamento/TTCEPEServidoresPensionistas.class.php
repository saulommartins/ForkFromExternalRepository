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
    * Data de Criação   : 17/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Michel Teixeira
    $Id: TTCEPEServidoresPensionistas.class.php 60570 2014-10-30 16:03:22Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEServidoresPensionistas extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPEServidoresPensionistas()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = "
            --SERVIDOR
                SELECT contrato.registro AS matricula
                     , sw_cgm_pessoa_fisica.cpf
                     , sw_cgm_pessoa_fisica.rg
                     , sw_cgm_pessoa_fisica.orgao_emissor
                     , sw_uf.sigla_uf
                     , sw_cgm.nom_cgm
                     , TO_CHAR(sw_cgm_pessoa_fisica.dt_nascimento,'ddmmyyyy') AS dt_nascimento 
                     , UPPER(sw_cgm_pessoa_fisica.sexo) as sexo
                     , servidor.nr_titulo_eleitor
                     , CASE WHEN contrato_servidor_situacao.situacao IN ('A', 'R') THEN 0
                       WHEN contrato_servidor_situacao.situacao IN ('P') THEN 1
                       WHEN contrato_servidor_situacao.situacao IN ('E') THEN 2
                       END AS situacao_funcional
                     , '' AS orgao_classe
                     , contrato_servidor_sindicato.nom_classe
                     , contrato_servidor_sindicato.uf_classe
                     , '' AS cpf_pai
                     , servidor.nome_pai
                     , '' AS cpf_mae
                     , servidor.nome_mae
                     , NULL::INTEGER AS matricula_servidor
                     , ''::VARCHAR AS nom_servidor
                     , ''::VARCHAR AS cpf_servidor
                     , NULL::INTEGER AS cod_contrato
                     , contrato.cod_contrato AS cod_contrato_servidor
                     , registro_evento_periodo.cod_periodo_movimentacao
                     
                  FROM sw_cgm

            INNER JOIN sw_cgm_pessoa_fisica
                    ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
                    
            INNER JOIN sw_uf
                    ON sw_uf.cod_uf=sw_cgm_pessoa_fisica.cod_uf_orgao_emissor

            INNER JOIN pessoal".$this->getDado('stEntidades').".servidor
                    ON servidor.numcgm = sw_cgm.numcgm

            INNER JOIN pessoal".$this->getDado('stEntidades').".servidor_contrato_servidor 
                    ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

            INNER JOIN pessoal".$this->getDado('stEntidades').".contrato
                    ON servidor_contrato_servidor.cod_contrato = contrato.cod_contrato

            INNER JOIN pessoal".$this->getDado('stEntidades').".contrato_servidor
                    ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato

             LEFT JOIN ( SELECT contrato_servidor_sindicato.*, sw_cgm.nom_cgm AS nom_classe, sw_uf.sigla_uf AS uf_classe
                           FROM pessoal".$this->getDado('stEntidades').".contrato_servidor_sindicato
                           JOIN sw_cgm
                             ON sw_cgm.numcgm = contrato_servidor_sindicato.numcgm_sindicato
                           JOIN sw_uf
                             ON sw_uf.cod_uf=sw_cgm.cod_uf
                            AND sw_uf.cod_pais=sw_cgm.cod_pais
                       ) AS contrato_servidor_sindicato                           
                    ON contrato_servidor_sindicato.cod_contrato = contrato.cod_contrato

            INNER JOIN ( SELECT cod_contrato, cod_periodo_movimentacao FROM folhapagamento".$this->getDado('stEntidades').".registro_evento_periodo
                          WHERE registro_evento_periodo.cod_periodo_movimentacao = (
                                                                                        SELECT cod_periodo_movimentacao
                                                                                          FROM folhapagamento".$this->getDado('stEntidades').".periodo_movimentacao
                                                                                         WHERE TO_CHAR(periodo_movimentacao.dt_final, 'mmyyyy') = '".$this->getDado('stMesAno')."' 
                                                                                   )
                      GROUP BY cod_contrato, cod_periodo_movimentacao
                       ) AS registro_evento_periodo
                    ON registro_evento_periodo.cod_contrato = contrato.cod_contrato

            INNER JOIN pessoal".$this->getDado('stEntidades').".contrato_servidor_situacao
                    ON contrato_servidor_situacao.cod_contrato=contrato.cod_contrato
                   AND contrato_servidor_situacao.cod_periodo_movimentacao<=registro_evento_periodo.cod_periodo_movimentacao
                   AND contrato_servidor_situacao.timestamp = (
                                                                SELECT MAX(contrato_servidor_situacao.timestamp)
                                                                  FROM pessoal".$this->getDado('stEntidades').".contrato_servidor_situacao
                                                                 WHERE contrato_servidor_situacao.cod_contrato=contrato.cod_contrato
                                                                   AND contrato_servidor_situacao.cod_periodo_movimentacao<=registro_evento_periodo.cod_periodo_movimentacao
                                                              )

             UNION ALL
             
         --PENSIONISTA
                SELECT contrato.registro AS matricula
                     , sw_cgm_pessoa_fisica.cpf
                     , sw_cgm_pessoa_fisica.rg
                     , sw_cgm_pessoa_fisica.orgao_emissor
                     , sw_uf.sigla_uf
                     , sw_cgm.nom_cgm
                     , TO_CHAR(sw_cgm_pessoa_fisica.dt_nascimento,'ddmmyyyy') AS dt_nascimento 
                     , UPPER(sw_cgm_pessoa_fisica.sexo) as sexo
                     , ''::VARCHAR AS nr_titulo_eleitor
                     , 2 AS situacao_funcional
                     , '' AS orgao_classe
                     , contrato_servidor_sindicato.nom_classe
                     , contrato_servidor_sindicato.uf_classe
                     , '' AS cpf_pai
                     , ''::VARCHAR AS nome_pai
                     , '' AS cpf_mae
                     , ''::VARCHAR AS nome_mae
                     , contrato_servidor.registro AS matricula_servidor
                     , contrato_servidor.nom_cgm AS nom_servidor
                     , contrato_servidor.cpf AS cpf_servidor
                     , contrato.cod_contrato
                     , contrato_servidor.cod_contrato AS cod_contrato_servidor
                     , registro_evento_periodo.cod_periodo_movimentacao
                     
                  FROM sw_cgm

            INNER JOIN sw_cgm_pessoa_fisica
                    ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm

            INNER JOIN sw_uf
                    ON sw_uf.cod_uf=sw_cgm_pessoa_fisica.cod_uf_orgao_emissor

            INNER JOIN pessoal".$this->getDado('stEntidades').".pensionista
                    ON pensionista.numcgm = sw_cgm.numcgm

            INNER JOIN pessoal".$this->getDado('stEntidades').".contrato_pensionista
                    ON contrato_pensionista.cod_pensionista=pensionista.cod_pensionista
                   AND contrato_pensionista.cod_contrato_cedente=pensionista.cod_contrato_cedente

            INNER JOIN pessoal".$this->getDado('stEntidades').".contrato
                    ON contrato.cod_contrato=contrato_pensionista.cod_contrato

            INNER JOIN ( SELECT contrato_servidor.*, contrato.registro, sw_cgm.nom_cgm, sw_cgm_pessoa_fisica.cpf
                           FROM pessoal".$this->getDado('stEntidades').".contrato_servidor
                           JOIN pessoal".$this->getDado('stEntidades').".contrato
                             ON contrato.cod_contrato = contrato_servidor.cod_contrato
                           JOIN pessoal".$this->getDado('stEntidades').".servidor_contrato_servidor 
                             ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato
                           JOIN pessoal".$this->getDado('stEntidades').".servidor
                             ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
                           JOIN sw_cgm_pessoa_fisica
                             ON servidor.numcgm = sw_cgm_pessoa_fisica.numcgm
                           JOIN sw_cgm
                             ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                       ) AS contrato_servidor
                    ON contrato_servidor.cod_contrato = pensionista.cod_contrato_cedente

            LEFT JOIN (  SELECT contrato_servidor_sindicato.*, sw_cgm.nom_cgm AS nom_classe, sw_uf.sigla_uf AS uf_classe
                           FROM pessoal".$this->getDado('stEntidades').".contrato_servidor_sindicato
                           JOIN sw_cgm
                             ON sw_cgm.numcgm = contrato_servidor_sindicato.numcgm_sindicato
                           JOIN sw_uf
                             ON sw_uf.cod_uf=sw_cgm.cod_uf
                            AND sw_uf.cod_pais=sw_cgm.cod_pais
                       ) AS contrato_servidor_sindicato                           
                    ON contrato_servidor_sindicato.cod_contrato = contrato_servidor.cod_contrato

            INNER JOIN ( SELECT cod_contrato, cod_periodo_movimentacao FROM folhapagamento".$this->getDado('stEntidades').".registro_evento_periodo
                          WHERE registro_evento_periodo.cod_periodo_movimentacao = (
                                                                                        SELECT cod_periodo_movimentacao
                                                                                          FROM folhapagamento".$this->getDado('stEntidades').".periodo_movimentacao
                                                                                         WHERE TO_CHAR(periodo_movimentacao.dt_final, 'mmyyyy') = '".$this->getDado('stMesAno')."' 
                                                                                   )
                       GROUP BY cod_contrato, cod_periodo_movimentacao
                       ) AS registro_evento_periodo
                    ON registro_evento_periodo.cod_contrato = contrato.cod_contrato

             LEFT JOIN pessoal".$this->getDado('stEntidades').".contrato_servidor_situacao
                    ON contrato_servidor_situacao.cod_contrato=contrato_servidor.cod_contrato
                   AND contrato_servidor_situacao.cod_periodo_movimentacao<=registro_evento_periodo.cod_periodo_movimentacao
                   AND contrato_servidor_situacao.timestamp = (
                                                                SELECT MAX(contrato_servidor_situacao.timestamp)
                                                                  FROM pessoal".$this->getDado('stEntidades').".contrato_servidor_situacao
                                                                 WHERE contrato_servidor_situacao.cod_contrato=contrato_servidor.cod_contrato
                                                                   AND contrato_servidor_situacao.cod_periodo_movimentacao<=registro_evento_periodo.cod_periodo_movimentacao
                                                              )

              ORDER BY matricula
        ";
        return $stSql;
    }
}

?>