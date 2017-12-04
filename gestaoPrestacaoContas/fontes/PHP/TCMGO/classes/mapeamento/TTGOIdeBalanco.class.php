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

    * Data de Criação:

    * @author Analista: Gelson
    * @author Desenvolvedor: Vitor Hugo

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGOIdeBalanco.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOIdeBalanco extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTGOIdeBalanco()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{
    $stSql  = "
    SELECT
           '10'                                AS tipo_registro
           ,configuracao_entidade.cod_entidade AS codigo_entidade
           ,configuracao_entidade.exercicio    AS ano_referencia
           ,to_char(NOW(),'ddmmyyyy')          AS data_geracao
           ,cgm.nom_cgm                        AS nom_prefeito
           ,cgm.cpf                            AS cpf_prefeito
           ,pft.logradouro                     AS lograd_prefeitura
           ,pft.bairro                         AS bairro_prefeitura
           ,pft.cidade                         AS cid_prefeitura
           ,pft.sigla_uf                       AS uf_prefeitura
           ,pft.cep                            AS cep_prefeitura
           ,cgm.logradouro                     AS lograd_prefeito
           ,cgm.bairro                         AS bairro_prefeito
           ,cgm.cidade                         AS cid_prefeito
           ,cgm.sigla_uf                       AS uf_prefeito
           ,cgm.cep                            AS cep_prefeito
           ,cont.nom_cgm                       AS nom_contador
           ,cont.cpf                           AS cpf_contador
           ,cont.num_registro                  AS crc_contador
           ,cont.sigla_uf                      AS uf_contador
           , 0                                 AS nom_contr_int
           , 0                                 AS cpf_contr_int

     FROM
           administracao.configuracao_entidade
           JOIN  ( SELECT
                      configuracao_entidade.exercicio
                      ,configuracao_entidade.cod_entidade
                      ,substr(trim(logradouro)||', '||trim(numero)||' '||trim(complemento), 1,50) as logradouro
                      ,bairro
                      ,nom_uf as cidade
                      ,sigla_uf
                      ,cep
                    FROM
                      orcamento.entidade
                      ,administracao.configuracao_entidade
                      ,sw_cgm
                      ,sw_uf
                    WHERE
                      orcamento.entidade.cod_entidade = administracao.configuracao_entidade.cod_entidade AND
                      orcamento.entidade.numcgm       = sw_cgm.numcgm                                    AND
                      orcamento.entidade.cod_entidade IN ( ".$this->getDado('stEntidades')." )           AND
                      orcamento.entidade.exercicio = ".$this->getDado('exercicio')."                     AND
                      configuracao_entidade.parametro = 'tc_codigo_unidade_gestora'                      AND
                      sw_cgm.cod_uf = sw_uf.cod_uf
                 ) AS pft ON (
                      pft.exercicio = administracao.configuracao_entidade.exercicio AND
                      pft.cod_entidade = administracao.configuracao_entidade.cod_entidade
                      )

           JOIN  ( SELECT
                      entidade.exercicio
                      ,entidade.cod_entidade
                      ,nom_cgm
                      ,cpf
                      ,substr(trim(logradouro)||', '||trim(numero)||' '||trim(complemento), 1,50) as logradouro
                      ,bairro
                      ,nom_uf as cidade
                      ,sigla_uf
                      ,cep
                   FROM
                      sw_uf
                      ,sw_cgm_pessoa_fisica
                      ,sw_cgm
                      ,administracao.configuracao
                      ,administracao.configuracao_entidade
                      ,orcamento.entidade
                   WHERE
                      administracao.configuracao.parametro = 'CGMPrefeito'  AND
                      sw_cgm.numcgm  = administracao.configuracao.valor     AND
                      sw_cgm.numcgm  = sw_cgm_pessoa_fisica.numcgm          AND
                      sw_cgm.cod_uf = sw_uf.cod_uf                          AND
                      orcamento.entidade.cod_entidade = administracao.configuracao_entidade.cod_entidade AND
                      configuracao_entidade.parametro = 'tc_codigo_unidade_gestora'                      AND
                      orcamento.entidade.exercicio = ".$this->getDado('exercicio')."                     AND
                      orcamento.entidade.cod_entidade IN ( ".$this->getDado('stEntidades')." )
                 ) AS cgm ON (
                      cgm.exercicio = administracao.configuracao_entidade.exercicio AND
                      cgm.cod_entidade = administracao.configuracao_entidade.cod_entidade
                      )
           JOIN ( SELECT
                     entidade.exercicio
                     ,entidade.cod_entidade
                     ,nom_cgm
                     ,cpf
                     ,economico.responsavel_tecnico.num_registro
                     ,sigla_uf
                  FROM
                     economico.responsavel_tecnico
                     ,sw_uf
                     ,sw_cgm_pessoa_fisica
                     ,sw_cgm
                     ,orcamento.entidade
                  WHERE
                     orcamento.entidade.exercicio = ".$this->getDado('exercicio')." AND
                     orcamento.entidade.cod_entidade IN ( ".$this->getDado('stEntidades')." ) AND
                     orcamento.entidade.cod_resp_tecnico = sw_cgm.numcgm   AND
                     orcamento.entidade.cod_resp_tecnico = economico.responsavel_tecnico.numcgm AND
                     sw_cgm.numcgm  = sw_cgm_pessoa_fisica.numcgm          AND
                     sw_cgm.cod_uf = sw_uf.cod_uf
                ) AS cont ON (
                     cont.exercicio = administracao.configuracao_entidade.exercicio AND
                     cont.cod_entidade = administracao.configuracao_entidade.cod_entidade
                     )

    WHERE  configuracao_entidade.cod_entidade IN (  ".$this->getDado('stEntidades')." ) AND
           configuracao_entidade.exercicio = ".$this->getDado('exercicio')."            AND
           configuracao_entidade.parametro = 'tc_codigo_unidade_gestora'
    ";

    return $stSql;
}
}
