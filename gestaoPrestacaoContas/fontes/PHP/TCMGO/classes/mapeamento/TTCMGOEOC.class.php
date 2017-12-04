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
    * Data de Criação: 30/01/2007

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Davi Aroldi

    * @package URBEM
    * @subpackage Mapeamento
    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOEOC extends Persistente
{
    public function recuperaEmpenhoVinculo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEmpenhoVinculo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaEmpenhoVinculo()
    {
        $stSql = "SELECT '10' as tipo_registro
                       , programa.num_programa AS cod_programa
                       , despesa.num_orgao as cod_orgao
                       , despesa.num_unidade as cod_unidade
                       , despesa.cod_funcao
                       , despesa.cod_subfuncao
                       , SUBSTR(despesa.num_pao::varchar, (SELECT valor from administracao.configuracao where parametro = 'pao_posicao_digito_id' and exercicio = '".$this->getDado('exercicio')."')::integer, 1) as natureza_acao
                       , acao.num_acao as sequencial_pao
                       , SUBSTR(REPLACE(publico.fn_mascarareduzida(conta_despesa.cod_estrutural)::varchar, '.', ''), 1, 6) as elemento_despesa
                       , CASE WHEN elemento_de_para.estrutural IS NOT NULL THEN
                            SUBSTR(REPLACE(elemento_de_para.estrutural::varchar, '.', ''), 7, 2)
                         ELSE
                            '00'
                         END as subelemento_despesa
                       , empenho.cod_empenho
                    FROM empenho.pre_empenho
              INNER JOIN empenho.pre_empenho_despesa
                      ON pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                     AND pre_empenho.exercicio       = pre_empenho_despesa.exercicio
              INNER JOIN orcamento.despesa
                      ON pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                     AND pre_empenho_despesa.exercicio   = despesa.exercicio
                    JOIN orcamento.despesa_acao
                      ON despesa_acao.exercicio_despesa = despesa.exercicio
                     AND despesa_acao.cod_despesa = despesa.cod_despesa
                    JOIN ppa.acao
                      ON acao.cod_acao = despesa_acao.cod_acao
                    JOIN ppa.programa
                      ON programa.cod_programa = despesa.cod_programa
              INNER JOIN orcamento.conta_despesa
                      ON pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
                     AND pre_empenho_despesa.exercicio = conta_despesa.exercicio
              INNER JOIN empenho.empenho
                      ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                     AND pre_empenho.exercicio       = empenho.exercicio
               LEFT JOIN tcmgo.elemento_de_para
                      ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                     AND elemento_de_para.exercicio = conta_despesa.exercicio
               LEFT JOIN tcmgo.obra_empenho
                      ON empenho.cod_empenho  = obra_empenho.cod_empenho
                     AND empenho.cod_entidade = obra_empenho.cod_entidade
                     AND empenho.exercicio    = obra_empenho.exercicio
               LEFT JOIN tcmgo.contrato_empenho
                      ON empenho.cod_empenho  = contrato_empenho.cod_empenho
                     AND empenho.cod_entidade = contrato_empenho.cod_entidade
                     AND empenho.exercicio    = contrato_empenho.exercicio
                   WHERE (obra_empenho.cod_obra IS NOT NULL OR contrato_empenho.cod_contrato IS NOT NULL)
                     AND empenho.cod_entidade in ( ".$this->getDado('stEntidades')." )
                     AND empenho.exercicio = '".$this->getDado('exercicio')."'
                     AND empenho.dt_empenho >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
                     AND empenho.dt_empenho <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' ) ";

        return $stSql;
    }

    public function recuperaEmpenhoVinculoObras(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEmpenhoVinculoObras",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaEmpenhoVinculoObras()
    {
        $stSql = "SELECT '11' as tipo_registro
                       , programa.num_programa AS cod_programa
                       , despesa.num_orgao as cod_orgao
                       , despesa.num_unidade as cod_unidade
                       , despesa.cod_funcao
                       , despesa.cod_subfuncao
                       , SUBSTR(despesa.num_pao::varchar, (SELECT valor from administracao.configuracao where parametro = 'pao_posicao_digito_id' and exercicio = '".$this->getDado('exercicio')."')::integer, 1) as natureza_acao
                       , acao.num_acao as sequencial_pao
                       , SUBSTR(REPLACE(publico.fn_mascarareduzida(conta_despesa.cod_estrutural)::varchar, '.', ''), 1, 6) as elemento_despesa
                       , CASE WHEN elemento_de_para.estrutural IS NOT NULL THEN
                            SUBSTR(REPLACE(elemento_de_para.estrutural::varchar, '.', ''), 7, 2)
                         ELSE
                            '00'
                         END as subelemento_despesa
                       , empenho.cod_empenho
                       , obra.cod_obra
                       , obra.ano_obra
                       , REPLACE(SUM(COALESCE(item_pre_empenho.vl_total, 0.00))::varchar, '.', ',') as vl_empenho
                    FROM empenho.pre_empenho
              INNER JOIN empenho.pre_empenho_despesa
                      ON pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                     AND pre_empenho.exercicio       = pre_empenho_despesa.exercicio
              INNER JOIN orcamento.despesa
                      ON pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                     AND pre_empenho_despesa.exercicio   = despesa.exercicio
                    JOIN orcamento.despesa_acao
                      ON despesa_acao.exercicio_despesa = despesa.exercicio
                     AND despesa_acao.cod_despesa = despesa.cod_despesa
                    JOIN ppa.acao
                      ON acao.cod_acao = despesa_acao.cod_acao
                    JOIN ppa.programa
                      ON programa.cod_programa = despesa.cod_programa
              INNER JOIN orcamento.conta_despesa
                      ON pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
                     AND pre_empenho_despesa.exercicio = conta_despesa.exercicio
              INNER JOIN empenho.empenho
                      ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                     AND pre_empenho.exercicio       = empenho.exercicio
              INNER JOIN tcmgo.obra_empenho
                      ON empenho.cod_empenho  = obra_empenho.cod_empenho
                     AND empenho.cod_entidade = obra_empenho.cod_entidade
                     AND empenho.exercicio    = obra_empenho.exercicio
              INNER JOIN tcmgo.obra
                      ON obra_empenho.cod_obra = obra.cod_obra
                     AND obra_empenho.ano_obra = obra.ano_obra
              INNER JOIN empenho.item_pre_empenho
                      ON pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                     AND pre_empenho.exercicio       = item_pre_empenho.exercicio
               LEFT JOIN tcmgo.elemento_de_para
                      ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                     AND elemento_de_para.exercicio = conta_despesa.exercicio
                   WHERE empenho.cod_entidade in ( ".$this->getDado('stEntidades')." )
                     AND empenho.exercicio = '".$this->getDado('exercicio')."'
                     AND empenho.dt_empenho >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
                     AND empenho.dt_empenho <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                GROUP BY programa.cod_programa
                       , despesa.num_orgao
                       , despesa.num_unidade
                       , acao.num_acao
                       , despesa.cod_funcao
                       , despesa.cod_subfuncao
                       , despesa.num_pao
                       , conta_despesa.cod_estrutural
                       , empenho.cod_empenho
                       , obra.cod_obra
                       , obra.ano_obra
                       , elemento_de_para.estrutural";

        return $stSql;
    }

    public function recuperaEmpenhoVinculoContrato(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEmpenhoVinculoContrato",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaEmpenhoVinculoContrato()
    {
        $stSql = "SELECT '12' as tipo_registro
                       , programa.num_programa AS cod_programa
                       , despesa.num_orgao as cod_orgao
                       , despesa.num_unidade as cod_unidade
                       , despesa.cod_funcao
                       , despesa.cod_subfuncao
                       , SUBSTR(despesa.num_pao::varchar, (SELECT valor from administracao.configuracao where parametro = 'pao_posicao_digito_id' and exercicio = '".$this->getDado('exercicio')."')::integer, 1) as natureza_acao
                       , acao.num_acao as sequencial_pao
                       , SUBSTR(REPLACE(publico.fn_mascarareduzida(conta_despesa.cod_estrutural)::varchar, '.', ''), 1, 6) as elemento_despesa
                       , CASE WHEN elemento_de_para.estrutural IS NOT NULL THEN
                            SUBSTR(REPLACE(elemento_de_para.estrutural::varchar, '.', ''), 7, 2)
                         ELSE
                            '00'
                         END as subelemento_despesa
                       , empenho.cod_empenho
                       , contrato.cod_contrato
                       , contrato.nro_contrato
                       , contrato.exercicio as ano_contrato
                       , contrato.cod_tipo as tipo_ajuste
                       , REPLACE(SUM(COALESCE(item_pre_empenho.vl_total, 0.00))::varchar, '.', ',') as vl_empenho
                    FROM empenho.pre_empenho
              INNER JOIN empenho.pre_empenho_despesa
                      ON pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                     AND pre_empenho.exercicio       = pre_empenho_despesa.exercicio
              INNER JOIN orcamento.despesa
                      ON pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                     AND pre_empenho_despesa.exercicio   = despesa.exercicio
                    JOIN orcamento.despesa_acao
                      ON despesa_acao.exercicio_despesa = despesa.exercicio
                     AND despesa_acao.cod_despesa = despesa.cod_despesa
                    JOIN ppa.acao
                      ON acao.cod_acao = despesa_acao.cod_acao
                    JOIN ppa.programa
                      ON programa.cod_programa = despesa.cod_programa
              INNER JOIN orcamento.conta_despesa
                      ON pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
                     AND pre_empenho_despesa.exercicio = conta_despesa.exercicio
              INNER JOIN empenho.empenho
                      ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                     AND pre_empenho.exercicio       = empenho.exercicio
              INNER JOIN tcmgo.contrato_empenho
                      ON empenho.cod_empenho  = contrato_empenho.cod_empenho
                     AND empenho.cod_entidade = contrato_empenho.cod_entidade
                     AND empenho.exercicio    = contrato_empenho.exercicio_empenho
              INNER JOIN tcmgo.contrato
                      ON contrato_empenho.cod_contrato = contrato.cod_contrato
                     AND contrato_empenho.exercicio    = contrato.exercicio
                     AND contrato_empenho.cod_entidade = contrato.cod_entidade
              INNER JOIN empenho.item_pre_empenho
                      ON pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                     AND pre_empenho.exercicio       = item_pre_empenho.exercicio
               LEFT JOIN tcmgo.elemento_de_para
                      ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                     AND elemento_de_para.exercicio = conta_despesa.exercicio
                   WHERE empenho.cod_entidade in ( ".$this->getDado('stEntidades')." )
                     AND empenho.exercicio = '".$this->getDado('exercicio')."'
                     AND empenho.dt_empenho >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
                     AND empenho.dt_empenho <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                GROUP BY programa.cod_programa
                       , despesa.num_orgao
                       , despesa.num_unidade
                       , despesa.cod_funcao
                       , acao.num_acao
                       , despesa.cod_subfuncao
                       , despesa.num_pao
                       , conta_despesa.cod_estrutural
                       , empenho.cod_empenho
                       , contrato.cod_contrato
                       , contrato.exercicio
                       , contrato.cod_tipo
                       , contrato.nro_contrato
                       , elemento_de_para.estrutural";

        return $stSql;
    }
}
