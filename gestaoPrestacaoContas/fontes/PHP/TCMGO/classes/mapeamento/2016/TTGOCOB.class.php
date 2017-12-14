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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 10/10/2007/

    * @author Analista: Gelson
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGOCOB.class.php 65190 2016-04-29 19:36:51Z michel $

    * Casos de uso: uc-06.04.00
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTGOCOB extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/

    public function recuperaObras(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaObras",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaObras()
    {
        $stSql = "
           SELECT '10' AS tipo_registro
                , despesa.num_orgao
                , despesa.num_unidade
                , obra.cod_obra
                , obra.ano_obra
                , obra.especificacao
                , obra.grau_latitude
                , obra.minuto_latitude
                , SPLIT_PART(obra.segundo_latitude::varchar, '.', 1) || SPLIT_PART(obra.segundo_latitude::varchar, '.', 2) as segundo_latitude
                , obra.grau_longitude
                , obra.minuto_longitude
                , SPLIT_PART(obra.segundo_longitude::varchar, '.', 1) || SPLIT_PART(obra.segundo_longitude::varchar, '.', 2) as segundo_longitude
                , unidade_medida.simbolo
                , obra.quantidade
                , obra.endereco
                , obra.bairro
                , obra.fiscal
             FROM tcmgo.obra
        LEFT JOIN administracao.unidade_medida
               ON unidade_medida.cod_unidade = obra.cod_unidade
              AND unidade_medida.cod_grandeza = obra.cod_grandeza
       INNER JOIN tcmgo.obra_empenho
               ON obra_empenho.cod_obra = obra.cod_obra
              AND obra_empenho.ano_obra = obra.ano_obra
       INNER JOIN empenho.empenho
               ON empenho.exercicio = obra_empenho.exercicio
              AND empenho.cod_empenho = obra_empenho.cod_empenho
              AND empenho.cod_entidade = obra_empenho.cod_entidade
       INNER JOIN empenho.pre_empenho
               ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
              AND pre_empenho.exercicio = empenho.exercicio
       INNER JOIN empenho.pre_empenho_despesa
               ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
              AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
       INNER JOIN orcamento.despesa
               ON despesa.exercicio = pre_empenho_despesa.exercicio
              AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
            WHERE obra_empenho.exercicio = '".$this->getDado('exercicio')."'";
              $arCodEntidade = explode(",",$this->getDado('cod_entidade'));
              $i = 0;
              while ($arCodEntidade[$i]!= "") {
             $stSql.= "
              AND obra_empenho.cod_entidade = ".$arCodEntidade[$i];
                $i++;
              }
             $stSql.= "
         GROUP BY despesa.num_orgao
                , despesa.num_unidade
                , obra.cod_obra
                , obra.ano_obra
                , obra.especificacao
                , obra.grau_latitude
                , obra.minuto_latitude
                , obra.segundo_latitude
                , obra.grau_longitude
                , obra.minuto_longitude
                , obra.segundo_longitude
                , unidade_medida.simbolo
                , obra.quantidade
                , obra.endereco
                , obra.bairro
                , obra.fiscal

        ";

        return $stSql;
    }

}
