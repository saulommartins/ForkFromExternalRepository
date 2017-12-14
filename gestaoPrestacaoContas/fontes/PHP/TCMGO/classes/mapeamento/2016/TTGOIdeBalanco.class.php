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

    $Id: TTGOIdeBalanco.class.php 65247 2016-05-04 18:50:31Z jean $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTGOIdeBalanco extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{
  $stSql = "
            SELECT
                    30 AS tipo_registro
                  , cgm_entidade.cod_municipio
                  , configuracao_ide.exercicio AS ano_referencia
                  , TO_CHAR(now(),'ddmmyyyy') AS data_geracao
                  , cgm_chefe.nom_cgm AS chefe_governo
                  , cgm_chefe.cpf AS cpf_chefe
                  , cgm_entidade.logradouro
                  , cgm_entidade.bairro AS setor_logra
                  , cgm_entidade.nom_municipio AS cidade_logra
                  , cgm_entidade.cod_uf AS uf_cidade_logra
                  , cgm_entidade.cep AS cep_logra
                  , cgm_chefe.logradouro AS logra_res_gestor
                  , cgm_chefe.bairro AS setor_logra_gestor
                  , cgm_chefe.nom_municipio AS cidade_logra_gestor
                  , cgm_chefe.cod_uf AS uf_cidade_logra_gestor
                  , cgm_chefe.cep AS cep_logra_gestor
                  , cgm_contador.nom_cgm AS nome_contador
                  , cgm_contador.cpf AS cpf_contador
                  , configuracao_ide.crc_contador
                  , configuracao_ide.uf_crc_contador
                  , cgm_controle.nom_cgm AS nome_controle_interno
                  , cgm_controle.cpf AS cpf_controle_interno

              FROM tcmgo.configuracao_ide

        INNER JOIN orcamento.entidade
                ON entidade.exercicio = configuracao_ide.exercicio
               AND entidade.cod_entidade = configuracao_ide.cod_entidade

        INNER JOIN (
                    SELECT sw_cgm.numcgm
                         , sw_cgm.logradouro
                         , sw_cgm.cep
                         , sw_cgm.cod_uf
                         , sw_cgm.bairro
                         , sw_cgm.cod_municipio
                         , sw_municipio.nom_municipio
              FROM sw_cgm
        INNER JOIN sw_municipio
                ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
               AND sw_municipio.cod_uf = sw_cgm.cod_uf
                   ) AS cgm_entidade
                ON cgm_entidade.numcgm = entidade.numcgm

        INNER JOIN (
                    SELECT sw_cgm.nom_cgm
                         , sw_cgm.numcgm
                         , sw_cgm_pessoa_fisica.cpf
                         , sw_cgm.logradouro
                         , sw_cgm.bairro
                         , sw_municipio.nom_municipio
                         , sw_municipio.cod_uf
                         , sw_cgm.cep
                  FROM sw_cgm
    
            INNER JOIN sw_cgm_pessoa_fisica
                    ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

            INNER JOIN sw_municipio
                    ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
                    AND sw_municipio.cod_uf = sw_cgm.cod_uf
                  ) AS cgm_chefe
                ON cgm_chefe.numcgm = configuracao_ide.cgm_chefe_governo

            INNER JOIN (
                        SELECT sw_cgm.nom_cgm
                             , sw_cgm.numcgm
                             , sw_cgm_pessoa_fisica.cpf
                          FROM sw_cgm
                    INNER JOIN sw_cgm_pessoa_fisica
                            ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                       ) AS cgm_contador
                    ON cgm_contador.numcgm = configuracao_ide.cgm_contador

            INNER JOIN (
                        SELECT sw_cgm.nom_cgm
                             , sw_cgm.numcgm
                             , sw_cgm_pessoa_fisica.cpf
                          FROM sw_cgm
                    INNER JOIN sw_cgm_pessoa_fisica
                            ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                       ) AS cgm_controle
                    ON cgm_controle.numcgm = configuracao_ide.cgm_controle_interno

                 WHERE configuracao_ide.cod_entidade IN (".$this->getDado('stEntidades').")
                   AND configuracao_ide.exercicio = '".$this->getDado('exercicio')."'
          ";

    return $stSql;
}
}
