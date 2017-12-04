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
    * Data de Criação   : 07/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Evandro Melos
    $Id: TTCEPEEmpenhoEstorno.class.php 60212 2014-10-07 13:28:55Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEEmpenhoEstorno extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPEEmpenhoEstorno()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = "  SELECT
                             TO_CHAR (empenho.dt_empenho, 'yyyy') AS exercicio_emissao
                            , LPAD(''||despesa.num_orgao,2, '0') || LPAD(''||despesa.num_unidade,2, '0') AS cod_unidade  
                            , empenho.cod_empenho AS num_empenho
                            , tc.numero_anulacao_empenho( empenho.exercicio , empenho.cod_entidade,  empenho.cod_empenho, empenho_anulado.timestamp ) AS num_estorno
                            , TO_CHAR(empenho_anulado.timestamp,'ddmmyyyy') AS dt_estorno                                                   
                            , ( SELECT SUM(vl_anulado) FROM empenho.empenho_anulado_item
                                                    WHERE empenho_anulado_item.exercicio = empenho_anulado.exercicio
                                                      AND empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                                                      AND empenho_anulado_item.cod_empenho = empenho_anulado.cod_empenho
                                                      AND empenho_anulado_item.timestamp = empenho_anulado.timestamp
                            ) AS vl_estornado
                            ,empenho_anulado.motivo as historico_estorno

                    FROM empenho.empenho

                    JOIN empenho.pre_empenho
                         ON pre_empenho.exercicio = empenho.exercicio
                        AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                    JOIN empenho.pre_empenho_despesa
                         ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        AND pre_empenho_despesa.exercicio = pre_empenho.exercicio

                    JOIN orcamento.despesa
                         ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                        AND despesa.exercicio = pre_empenho_despesa.exercicio

                    JOIN empenho.empenho_anulado
                         ON empenho_anulado.exercicio = empenho.exercicio
                        AND empenho_anulado.cod_entidade = empenho.cod_entidade
                        AND empenho_anulado.cod_empenho = empenho.cod_empenho
                  
                    WHERE empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
                    AND empenho.exercicio = '".$this->getDado('exercicio')."'
                    AND empenho_anulado.timestamp::date BETWEEN TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy') 
                    AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || ".$this->getDado('mes')." || '-' || '01','yyyy-mm-dd'))

                    ORDER BY empenho.cod_empenho,empenho_anulado.timestamp
        ";
        return $stSql;
    }
}

?>