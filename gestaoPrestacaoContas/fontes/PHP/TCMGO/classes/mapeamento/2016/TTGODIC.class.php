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
    * Data de Criação: 02/02/2012

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGODIC.class.php 65220 2016-05-03 21:30:22Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTGODIC extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function __construct()
    {
    parent::Persistente();

    $this->setDado('dt_inicio', Sessao::getExercicio() );
        $this->setDado('dt_fim', Sessao::getExercicio() );
    }

    public function recuperaPorPeriodo(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;

       $stSql = $this->montaRecuperaPorPeriodo();
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

       return $obErro;
    }

    public function montaRecuperaPorPeriodo()
    {
        $stSql = "   SELECT
                            '10' as tipo_registro,
                            divida_consolidada.num_orgao,
                            divida_consolidada.num_unidade,
                            divida_consolidada.tipo_lancamento,
                            replace(divida_consolidada.nro_lei_autorizacao, '/', '')||divida_consolidada.exercicio as nro_lei_autorizacao,
                            to_char(divida_consolidada.dt_lei_autorizacao, 'ddmmyyyy') as dt_lei_autorizacao,
                            sw_cgm.nom_cgm,

                            CASE WHEN sw_cgm.cod_pais IS NOT NULL AND sw_cgm.cod_pais = 1 THEN 3 
                                 WHEN coalesce(sw_cgm_pessoa_fisica.numcgm, 0)   != 0 THEN 1
                                 WHEN coalesce(sw_cgm_pessoa_juridica.numcgm, 0) != 0 THEN 2
                            END::varchar as tipo_pessoa,

                            sw_cgm_pessoa_fisica.cpf,
                            sw_cgm_pessoa_juridica.cnpj,
                            CASE
                                 WHEN coalesce(sw_cgm_pessoa_fisica.cpf, '') != '' THEN sw_cgm_pessoa_fisica.cpf
                                 WHEN coalesce(sw_cgm_pessoa_juridica.cnpj, '') != '' THEN sw_cgm_pessoa_juridica.cnpj
                            END::varchar as cpf_cnpj,

                            case when lpad(trim(to_char(divida_consolidada.vl_saldo_anterior, '9999999999D99')), 13, '0') != '' then
                lpad(trim(to_char(divida_consolidada.vl_saldo_anterior, '9999999999D99')), 13, '0')
                else '0000000000,00' end as vl_saldo_anterior,

                case when lpad(trim(to_char(divida_consolidada.vl_contratacao, '9999999999D99')), 13, '0') != '' then
                lpad(trim(to_char(divida_consolidada.vl_contratacao, '9999999999D99')), 13, '0')
                else '0000000000,00' end as vl_contratacao,

                case when lpad(trim(to_char(divida_consolidada.vl_amortizacao, '9999999999D99')), 13, '0') != '' then
                lpad(trim(to_char(divida_consolidada.vl_amortizacao, '9999999999D99')), 13, '0')
                else '0000000000,00' end as vl_amortizacao,

                case when lpad(trim(to_char(divida_consolidada.vl_cancelamento, '9999999999D99')), 13, '0') != '' then
                lpad(trim(to_char(divida_consolidada.vl_cancelamento, '9999999999D99')), 13, '0')
                else '0000000000,00' end as vl_cancelamento,

                case when lpad(trim(to_char(divida_consolidada.vl_encampacao, '9999999999D99')), 13, '0') != '' then
                lpad(trim(to_char(divida_consolidada.vl_encampacao, '9999999999D99')), 13, '0')
                else '0000000000,00' end as vl_encampacao,

                case when lpad(trim(to_char(divida_consolidada.vl_atualizacao, '9999999999D99')), 13, '0') != '' then
                lpad(trim(to_char(divida_consolidada.vl_atualizacao, '9999999999D99')), 13, '0')
                else '0000000000,00' end as vl_atualizacao,

                case when lpad(trim(to_char(divida_consolidada.vl_saldo_atual, '9999999999D99')), 13, '0') != '' then
                lpad(trim(to_char(divida_consolidada.vl_saldo_atual, '9999999999D99')), 13, '0')
                else '0000000000,00' end as vl_saldo_atual,

                            '1' as numero_registro
                         FROM tcmgo.divida_consolidada
                    LEFT JOIN sw_cgm
                           ON sw_cgm.numcgm = divida_consolidada.numcgm

                    LEFT JOIN sw_cgm_pessoa_fisica
                           ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                    LEFT JOIN sw_cgm_pessoa_juridica
                           ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

                    WHERE divida_consolidada.dt_inicio >= '".$this->getDado('dt_inicio')."'
                      AND divida_consolidada.dt_fim <= '".$this->getDado('dt_fim')."'";

        return $stSql;
    }
}
