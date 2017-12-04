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
  * Classe de mapeamento da tabela CALENDARIO_CALENDARIO_CADASTRO
  * Data de Criação: 04/08/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento

  $Revision: 30566 $
  $Name$
  $Author: souzadl $
  $Date: 2007-06-11 14:58:23 -0300 (Seg, 11 Jun 2007) $

  * Casos de uso: uc-04.02.04

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CALENDARIO_CALENDARIO
  * Data de Criação: 04/08/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCalendarioCalendarioCadastro extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCalendarioCalendarioCadastro()
{
    ;
    parent::Persistente();
    $this->setTabela('calendario.calendario_cadastro');

    $this->setCampoCod('cod_calendar');
    $this->setComplementoChave('');

    $this->AddCampo('cod_calendar','integer',true,'',true,false);
    $this->AddCampo('descricao','varchar',true,'100',false,false);

}

function montaRecuperaRelacionamento()
{
    ;
  $stSql .= "select                                                                                       \n";
  $stSql .= "  cf.cod_feriado,                                                                            \n";
  $stSql .= "  to_char(cf.dt_feriado, 'dd/mm/yyyy') as dt_feriado,                                        \n";
  $stSql .= "  cf.dt_feriado as dt_feriado_order,                                                         \n";
  $stSql .= "  cf.descricao,                                                                              \n";
  $stSql .= "  CASE abrangencia                                                                           \n";
  $stSql .= "    WHEN 'E' THEN 'Estadual'                                                                 \n";
  $stSql .= "    WHEN 'F' THEN 'Federal'                                                                  \n";
  $stSql .= "    WHEN 'M' THEN 'Municipal'                                                                \n";
  $stSql .= "  END as abrangencia,                                                                        \n";
  $stSql .= "  CASE tipoferiado                                                                           \n";
  $stSql .= "    WHEN 'D' THEN 'Dia compensado'                                                           \n";
  $stSql .= "    WHEN 'F' THEN 'Fixo'                                                                     \n";
  $stSql .= "    WHEN 'P' THEN 'Ponto facultativo'                                                        \n";
  $stSql .= "    WHEN 'V' THEN 'Variável'                                                                 \n";
  $stSql .= "  END as tipoferiado,                                                                        \n";
  $stSql .= "  tipo_feriado_calendario(to_char(dt_feriado,'dd/mm/yyyy'),'".$this->getDado("cod_calendar")."','".Sessao::getEntidade()."') as tipo_cor          \n";
  $stSql .= "                                                                                             \n";
  $stSql .= "  from                                                                                       \n";
  $stSql .= "     calendario.feriado cf                                                               \n";
  $stSql .= "where                                                                                        \n";
  $stSql .= "       cod_feriado not in (select * from calendario.feriado_variavel)  and               \n";
  $stSql .= "       cod_feriado not in (select * from calendario.ponto_facultativo) and               \n";
  $stSql .= "       cod_feriado not in (select * from calendario.dia_compensado)                      \n";
    if ($this->getDado("dt_feriado")) {
     $stSql .= "and  dt_feriado = to_date('". $this->getDado("dt_feriado") ."','dd/mm/yyyy')";
  }
  if ($this->getDado("dt_inicial") && $this->getDado("dt_inicial")) {
     $stSql .=" and  ( dt_feriado BETWEEN to_date('".$this->getDado("dt_inicial")."','dd/mm,yyyy') AND to_date('".$this->getDado("dt_final")."','dd/mm/yyyy') )\n";
  }
  $stSql .= "                                                                                             \n";
  $stSql .= "union                                                                                        \n";
  $stSql .= "                                                                                             \n";
  $stSql .= "        select                                                                               \n";
  $stSql .= "              cf.cod_feriado,                                                                \n";
  $stSql .= "              to_char(cf.dt_feriado, 'dd/mm/yyyy') as dt_feriado,                            \n";
  $stSql .= "  cf.dt_feriado as dt_feriado_order,                                                         \n";
  $stSql .= "              cf.descricao,                                                                  \n";
  $stSql .= "              CASE abrangencia                                                               \n";
  $stSql .= "                WHEN 'E' THEN 'Estadual'                                                     \n";
  $stSql .= "                WHEN 'F' THEN 'Federal'                                                      \n";
  $stSql .= "                WHEN 'M' THEN 'Municipal'                                                    \n";
  $stSql .= "              END as abrangencia,                                                            \n";
  $stSql .= "              CASE tipoferiado                                                               \n";
  $stSql .= "                WHEN 'D' THEN 'Dia compensado'                                               \n";
  $stSql .= "                WHEN 'F' THEN 'Fixo'                                                         \n";
  $stSql .= "                WHEN 'P' THEN 'Ponto facultativo'                                            \n";
  $stSql .= "                WHEN 'V' THEN 'Variável'                                                     \n";
  $stSql .= "              END as tipoferiado,                                                            \n";
//  $stSql .= "              calendario".Sessao::getEntidade().".tipo_feriado(to_char(dt_feriado,'dd/mm/yyyy')) as tipo_cor          \n";
 $stSql .= "  tipo_feriado_calendario(to_char(dt_feriado,'dd/mm/yyyy'),'".$this->getDado("cod_calendar")."','".Sessao::getEntidade()."') as tipo_cor          \n";
  $stSql .= "         from                                                                                \n";
  $stSql .= "              calendario.feriado_variavel             cfv,                               \n";
  $stSql .= "              calendario.calendario_feriado_variavel ccfv,                               \n";
  $stSql .= "              calendario.feriado                       cf                                \n";
  $stSql .= "        where                                                                                \n";
  $stSql .= "              cfv.cod_feriado   = ccfv.cod_feriado                                           \n";
  $stSql .= "          and cf.cod_feriado = cfv.cod_feriado                                               \n";
  $stSql .= "          and ccfv.cod_calendar = ".$this->getDado("cod_calendar")."                         \n";
  if ($this->getDado("dt_feriado")) {
     $stSql .= "and  dt_feriado = to_date('". $this->getDado("dt_feriado") ."','dd/mm/yyyy')";
  }
  if ($this->getDado("dt_inicial") && $this->getDado("dt_inicial")) {
     $stSql .=" and  ( dt_feriado BETWEEN to_date('".$this->getDado("dt_inicial")."','dd/mm,yyyy') AND to_date('".$this->getDado("dt_final")."','dd/mm/yyyy') )\n";
  }
  $stSql .= "                                                                                             \n";
  $stSql .= "union                                                                                        \n";
  $stSql .= "                                                                                             \n";
  $stSql .= "       select                                                                                \n";
  $stSql .= "              cf.cod_feriado,                                                                \n";
  $stSql .= "              to_char(cf.dt_feriado, 'dd/mm/yyyy') as dt_feriado,                            \n";
  $stSql .= "  cf.dt_feriado as dt_feriado_order,                                                         \n";
  $stSql .= "              cf.descricao,                                                                  \n";
  $stSql .= "              CASE abrangencia                                                               \n";
  $stSql .= "                WHEN 'E' THEN 'Estadual'                                                     \n";
  $stSql .= "                WHEN 'F' THEN 'Federal'                                                      \n";
  $stSql .= "                WHEN 'M' THEN 'Municipal'                                                    \n";
  $stSql .= "              END as abrangencia,                                                            \n";
  $stSql .= "              CASE tipoferiado                                                               \n";
  $stSql .= "                WHEN 'D' THEN 'Dia compensado'                                               \n";
  $stSql .= "                WHEN 'F' THEN 'Fixo'                                                         \n";
  $stSql .= "                WHEN 'P' THEN 'Ponto facultativo'                                            \n";
  $stSql .= "                WHEN 'V' THEN 'Variável'                                                     \n";
  $stSql .= "              END as tipoferiado,                                                            \n";
 // $stSql .= "              calendario".Sessao::getEntidade().".tipo_feriado(to_char(dt_feriado,'dd/mm/yyyy')) as tipo_cor          \n";
 $stSql .= "  tipo_feriado_calendario(to_char(dt_feriado,'dd/mm/yyyy'),'".$this->getDado("cod_calendar")."','".Sessao::getEntidade()."') as tipo_cor          \n";
  $stSql .= "                                                                                             \n";
  $stSql .= "         from                                                                                \n";
  $stSql .= "              calendario.ponto_facultativo              cpf,                             \n";
  $stSql .= "              calendario.calendario_ponto_facultativo  ccpf,                             \n";
  $stSql .= "              calendario.feriado                         cf                              \n";
  $stSql .= "        where                                                                                \n";
  $stSql .= "              cpf.cod_feriado   = ccpf.cod_feriado                                           \n";
  $stSql .= "          and cf.cod_feriado = cpf.cod_feriado                                               \n";
  $stSql .= "          and ccpf.cod_calendar = ".$this->getDado("cod_calendar")."                         \n";
  if ($this->getDado("dt_feriado")) {
     $stSql .= "and  dt_feriado = to_date('". $this->getDado("dt_feriado") ."','dd/mm/yyyy')";
  }
  if ($this->getDado("dt_inicial") && $this->getDado("dt_inicial")) {
     $stSql .=" and  ( dt_feriado BETWEEN to_date('".$this->getDado("dt_inicial")."','dd/mm,yyyy') AND to_date('".$this->getDado("dt_final")."','dd/mm/yyyy') )\n";
  }
  $stSql .= "union                                                                                        \n";
  $stSql .= "                                                                                             \n";
  $stSql .= "        select                                                                               \n";
  $stSql .= "              cf.cod_feriado,                                                                \n";
  $stSql .= "              to_char(cf.dt_feriado, 'dd/mm/yyyy') as dt_feriado,                            \n";
  $stSql .= "  cf.dt_feriado as dt_feriado_order,                                                         \n";
  $stSql .= "              cf.descricao,                                                                  \n";
  $stSql .= "              CASE abrangencia                                                               \n";
  $stSql .= "                WHEN 'E' THEN 'Estadual'                                                     \n";
  $stSql .= "                WHEN 'F' THEN 'Federal'                                                      \n";
  $stSql .= "                WHEN 'M' THEN 'Municipal'                                                    \n";
  $stSql .= "              END as abrangencia,                                                            \n";
  $stSql .= "              CASE tipoferiado                                                               \n";
  $stSql .= "                WHEN 'D' THEN 'Dia compensado'                                               \n";
  $stSql .= "                WHEN 'F' THEN 'Fixo'                                                         \n";
  $stSql .= "                WHEN 'P' THEN 'Ponto facultativo'                                            \n";
  $stSql .= "                WHEN 'V' THEN 'Variável'                                                     \n";
  $stSql .= "              END as tipoferiado,                                                            \n";
 // $stSql .= "              calendario".Sessao::getEntidade().".tipo_feriado(to_char(dt_feriado,'dd/mm/yyyy')) as tipo_cor          \n";
 $stSql .= "  tipo_feriado_calendario(to_char(dt_feriado,'dd/mm/yyyy'),'".$this->getDado("cod_calendar")."','".Sessao::getEntidade()."') as tipo_cor          \n";
  $stSql .= "         from                                                                                \n";
  $stSql .= "              calendario.dia_compensado              cdc,                                \n";
  $stSql .= "              calendario.calendario_dia_compensado  ccdc,                                \n";
  $stSql .= "              calendario.feriado                      cf                                 \n";
  $stSql .= "         where                                                                               \n";
  $stSql .= "               cdc.cod_feriado   = ccdc.cod_feriado                                          \n";
  $stSql .= "           and cf.cod_feriado = cdc.cod_feriado                                              \n";
  $stSql .= "           and ccdc.cod_calendar = ".$this->getDado("cod_calendar")."                        \n";
  if ($this->getDado("dt_feriado")) {
     $stSql .= "and  dt_feriado = to_date('". $this->getDado("dt_feriado") ."','dd/mm/yyyy')";
  }
  if ($this->getDado("dt_inicial") && $this->getDado("dt_inicial")) {
     $stSql .=" and  ( dt_feriado BETWEEN to_date('".$this->getDado("dt_inicial")."','dd/mm,yyyy') AND to_date('".$this->getDado("dt_final")."','dd/mm/yyyy') )\n";
  }
  $stSql .= "   order by dt_feriado_order                                                                 \n";

  return $stSql;
}

}
