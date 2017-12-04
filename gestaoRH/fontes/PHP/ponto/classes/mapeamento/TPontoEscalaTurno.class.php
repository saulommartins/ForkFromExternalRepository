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
    * Classe de mapeamento da tabela ponto.escala_turno
    * Data de Criação: 10/10/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.10.02

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoEscalaTurno extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoEscalaTurno()
{
    parent::Persistente();
    $this->setTabela("ponto.escala_turno");

    $this->setCampoCod('cod_turno');
    $this->setComplementoChave('cod_escala');

    $this->AddCampo('cod_escala'    ,'integer'  ,true  ,''   ,true,'TPontoEscala');
    $this->AddCampo('cod_turno'     ,'sequence' ,true  ,''   ,true,false);
    $this->AddCampo('timestamp'     ,'timestamp_now',true  ,''   ,false,false);
    $this->AddCampo('dt_turno'      ,'date'     ,true  ,''   ,false,false);
    $this->AddCampo('hora_entrada_1','time'     ,true  ,''   ,false,false);
    $this->AddCampo('hora_saida_1'  ,'time'     ,true  ,''   ,false,false);
    $this->AddCampo('hora_entrada_2','time'     ,true  ,''   ,false,false);
    $this->AddCampo('hora_saida_2'  ,'time'     ,true  ,''   ,false,false);
    $this->AddCampo('tipo'          ,'char'     ,true  ,'1'  ,false,false);

}

function recuperaTurnosAtivos(&$rsRecordset,$stFiltro="",$stOrdem="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaTurnosAtivos",$rsRecordset,$stFiltro,$stOrdem,$boTransacao);
}

function montaRecuperaTurnosAtivos()
{
    $stSql .= "SELECT escala_turno.*                                                                \n";
    $stSql .= "     , to_char(escala_turno.dt_turno,'dd/mm/yyyy') as dt_turno                       \n";
    $stSql .= "     , ( CASE WHEN escala_turno.tipo = 'T' THEN 'Trabalho'                           \n";
    $stSql .= "              WHEN escala_turno.tipo = 'F' THEN 'Folga'                              \n";
    $stSql .= "         END ) as tipo_formatado                                                     \n";
    $stSql .= "     , to_char(escala_turno.hora_entrada_1,'hh24:mi') as hora_entrada_1_formatado    \n";
    $stSql .= "     , to_char(escala_turno.hora_entrada_2,'hh24:mi') as hora_entrada_2_formatado    \n";
    $stSql .= "     , to_char(escala_turno.hora_saida_1,'hh24:mi') as hora_saida_1_formatado        \n";
    $stSql .= "     , to_char(escala_turno.hora_saida_2,'hh24:mi') as hora_saida_2_formatado        \n";
    $stSql .= "  FROM ponto.escala                                         \n";
    $stSql .= "     , ponto.escala_turno                                   \n";
    $stSql .= " WHERE escala.cod_escala = escala_turno.cod_escala                                   \n";
    $stSql .= "   AND escala.ultimo_timestamp = escala_turno.timestamp                              \n";
    $stSql .= "   AND NOT EXISTS (SELECT 1                                                          \n";
    $stSql .= "                     FROM ponto.escala_exclusao             \n";
    $stSql .= "                    WHERE escala_exclusao.cod_escala = escala.cod_escala)            \n";

    return $stSql;
}

}
?>
