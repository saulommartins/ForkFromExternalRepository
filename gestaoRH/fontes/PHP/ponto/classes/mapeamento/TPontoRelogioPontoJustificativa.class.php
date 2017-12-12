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
    * Classe de mapeamento da tabela ponto.relogio_ponto_justificativa
    * Data de Criação: 21/10/2008

    * @author Analista     : Dagiane Vieira
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoRelogioPontoJustificativa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoRelogioPontoJustificativa()
{
    parent::Persistente();
    $this->setTabela("ponto.relogio_ponto_justificativa");

    $this->setCampoCod('sequencia');
    $this->setComplementoChave('cod_contrato,cod_justificativa,timestamp');

    $this->AddCampo('cod_contrato'     ,'integer'      ,true  ,'',true ,'TPontoDadosRelogioPonto');
    $this->AddCampo('timestamp'        ,'timestamp_now',true  ,'',true);
    $this->AddCampo('cod_justificativa','integer'  ,true  ,'',true ,'TPontoJustificativa');
    $this->AddCampo('sequencia'        ,'sequence' ,true  ,'',true ,false);
    $this->AddCampo('periodo_inicio'   ,'date'     ,true  ,'',false,false);
    $this->AddCampo('periodo_termino'  ,'date'     ,true  ,'',false,false);
    $this->AddCampo('horas_falta'      ,'time'     ,true  ,'',false,false);
    $this->AddCampo('horas_abonar'     ,'time'     ,true  ,'',false,false);
    $this->AddCampo('observacao'       ,'text'     ,false ,'',false,false);

}

function montaJustificativa()
{
    $stSql  = "     SELECT relogio_ponto_justificativa.cod_justificativa                                                          \n";
    $stSql .= "          , relogio_ponto_justificativa.cod_contrato                                                               \n";
    $stSql .= "          , relogio_ponto_justificativa.sequencia                                                                  \n";
    $stSql .= "          , relogio_ponto_justificativa.timestamp                                                                  \n";
    $stSql .= "          , to_char(relogio_ponto_justificativa.periodo_inicio, 'dd/mm/yyyy') as periodo_inicio                    \n";
    $stSql .= "          , to_char(relogio_ponto_justificativa.periodo_termino, 'dd/mm/yyyy') as periodo_termino                  \n";
    $stSql .= "          , coalesce(to_char(relogio_ponto_justificativa.horas_falta,'hh24:mi'), justificativa_horas.horas_falta) as horas_falta      \n";
    $stSql .= "          , coalesce(to_char(relogio_ponto_justificativa.horas_abonar,'hh24:mi'), justificativa_horas.horas_abono) as horas_abono     \n";
    $stSql .= "          , relogio_ponto_justificativa.observacao                                                                 \n";
    $stSql .= "          , justificativa.anular_faltas                                                                            \n";
    $stSql .= "          , justificativa.lancar_dias_trabalho                                                                     \n";
    $stSql .= "          , justificativa.descricao                                                                                \n";
    $stSql .= "       FROM ponto.relogio_ponto_justificativa                                             \n";
    $stSql .= " INNER JOIN ( SELECT cod_contrato                                                                                  \n";
    $stSql .= "                   , cod_justificativa                                                                             \n";
    $stSql .= "                   , sequencia                                                                                     \n";
    $stSql .= "                   , MAX(timestamp) as timestamp                                                                   \n";
    $stSql .= "                FROM ponto.relogio_ponto_justificativa                                    \n";
    $stSql .= "            GROUP BY cod_contrato                                                                                  \n";
    $stSql .= "                   , cod_justificativa                                                                             \n";
    $stSql .= "                   , sequencia ) as max_relogio_ponto_justificativa                                                \n";
    $stSql .= "         ON relogio_ponto_justificativa.cod_contrato = max_relogio_ponto_justificativa.cod_contrato                \n";
    $stSql .= "        AND relogio_ponto_justificativa.cod_justificativa = max_relogio_ponto_justificativa.cod_justificativa      \n";
    $stSql .= "        AND relogio_ponto_justificativa.sequencia = max_relogio_ponto_justificativa.sequencia                      \n";
    $stSql .= "        AND relogio_ponto_justificativa.timestamp = max_relogio_ponto_justificativa.timestamp                      \n";
    $stSql .= "  LEFT JOIN ponto.justificativa                                                           \n";
    $stSql .= "         ON relogio_ponto_justificativa.cod_justificativa = justificativa.cod_justificativa                        \n";
    $stSql .= "  LEFT JOIN ponto.justificativa_horas                                                     \n";
    $stSql .= "         ON justificativa.cod_justificativa = justificativa_horas.cod_justificativa                                \n";
    $stSql .= "      WHERE NOT EXISTS (SELECT 1                                                                                   \n";
    $stSql .= "                          FROM ponto.relogio_ponto_justificativa_exclusao                 \n";
    $stSql .= "                         WHERE relogio_ponto_justificativa_exclusao.cod_contrato = relogio_ponto_justificativa.cod_contrato     \n";
    $stSql .= "                           AND relogio_ponto_justificativa_exclusao.cod_justificativa = relogio_ponto_justificativa.cod_justificativa\n";
    $stSql .= "                           AND relogio_ponto_justificativa_exclusao.sequencia = relogio_ponto_justificativa.sequencia\n";
    $stSql .= "                           AND relogio_ponto_justificativa_exclusao.timestamp = relogio_ponto_justificativa.timestamp)\n";

    return $stSql;
}

function recuperaJustificativa(&$rsRecordset,$stFiltro="",$stOrdem="",$boTransacao="")
{
    return $this->executaRecupera("montaJustificativa",$rsRecordset,$stFiltro,$stOrdem,$boTransacao);
}

}
?>
