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
  * Classe de mapeamento da tabela CALENDARIO_CALENDARIO_DIA_COMPENSADO
  * Data de Criação: 07/04/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Renan O. C. Ferreira - CNM

  * @package URBEM
  * @subpackage Mapeamento

  $Revision: 30566 $
  $Name$
  $Author: tiago $
  $Date: 2007-06-19 16:12:43 -0300 (Ter, 19 Jun 2007) $

  * Caso de uso: uc-04.02.05

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCalendarioCalendarioDiaCompensado extends Persistente
{
  /**
    * Método Construtor
    * @access Private
*/
function TCalendarioCalendarioDiaCompensado()
{
    parent::Persistente();
    $this->setTabela('calendario.calendario_dia_compensado');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_calendar,cod_feriado');

    $this->AddCampo('cod_calendar','integer',true,'',true,true);
    $this->AddCampo('cod_feriado' ,'integer',true,'',true,true);

}

/**
    * Método que pode ser sobreposto através de uma extensão desta classe.
    * @access Private
    * @return String  Comando SQL
*/
function montaRecuperaFeriadosSelecionados()
{
  $stSql  = "  SELECT                                                           \n";
  $stSql .= "      f.cod_feriado,                                               \n";
  $stSql .= "      to_char(f.dt_feriado,'dd/mm/yyyy') as dt_feriado,            \n";
  $srSQL .= "      CASE abrangencia                                             \n";
  $stSql .= "        WHEN 'E' THEN 'Estadual'                                   \n";
  $stSql .= "        WHEN 'F' THEN 'Federal'                                    \n";
  $stSql .= "        WHEN 'M' THEN 'Municipal'                                  \n";
  $stSql .= "      END as abrangencia,                                          \n";
  $stSql .= "      CASE tipoferiado                                             \n";
  $stSql .= "        WHEN 'S' THEN 'Fixo'                                       \n";
  $stSql .= "        WHEN 'N' THEN 'Não Fixo'                                   \n";
  $stSql .= "      END as tipoferiado                                           \n";
  $stSql .= "  FROM                                                             \n";
  $stSql .= "    calendario.feriado  AS f                                       \n";
  $stSql  = "  INNER JOIN                                                       \n";
  $stSql .= "     calendario.calendario_dia_compensado        AS pf             \n";
  $stSql  = "      (f.cod_feriado = pf.cod_feriado) AND                         \n";
  $stSql .= "      (pf.cod_feriado IN                                           \n";
  $stSql .= "      (SELECT                                                      \n";
  $stSql .= "            cod_feriado                                            \n";
  $stSql .= "       FROM                                                        \n";
  $stSql .= "            calendario.calendario_dia_compensado                   \n";
  $stSql .= "       WHERE cod_calendar = ". $this->getDado("cod_calendar") ." ))\n";

  return $stSql;
}

function montaRecuperaFeriadosDisponiveis()
{
  $stSql  = "  SELECT                                                           \n";
  $stSql .= "      f.cod_feriado,                                               \n";
  $stSql .= "      to_char(f.dt_feriado,'dd/mm/yyyy') as dt_feriado,            \n";
  $srSQL .= "      CASE abrangencia                                             \n";
  $stSql .= "        WHEN 'E' THEN 'Estadual'                                   \n";
  $stSql .= "        WHEN 'F' THEN 'Federal'                                    \n";
  $stSql .= "        WHEN 'M' THEN 'Municipal'                                  \n";
  $stSql .= "      END as abrangencia,                                          \n";
  $stSql .= "      CASE tipoferiado                                             \n";
  $stSql .= "        WHEN 'S' THEN 'Fixo'                                       \n";
  $stSql .= "        WHEN 'N' THEN 'Não Fixo'                                   \n";
  $stSql .= "      END as tipoferiado                                           \n";
  $stSql .= "  FROM                                                             \n";
  $stSql .= "    calendario.feriado  AS f                                       \n";
  $stSql  = "  INNER JOIN                                                       \n";
  $stSql .= "    calendario.calendario_dia_compensado  AS pf on                 \n";
  $stSql  = "      (f.cod_feriado = pf.cod_feriado) AND                         \n";
  $stSql .= "      (pf.cod_feriado NOT IN                                       \n";
  $stSql .= "      (SELECT                                                      \n";
  $stSql .= "            cod_feriado                                            \n";
  $stSql .= "       FROM                                                        \n";
  $stSql .= "            calendario.calendario_dia_compensado                   \n";
  $stSql .= "       WHERE cod_calendar = ". $this->getDado("cod_calendar") ." ))\n";

  return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaFeriadosSelecionados.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaFeriadosSelecionados(&$rsRecordSet, $stFiltro = "", $stOrdem = "dt_feriado" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = ' order by ' . $stOrdem;

    $stSql = $this->montaRecuperaFeriadosSelecionados().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaAtributosDisponiveis.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaFeriadosDisponiveis(&$rsRecordSet, $stFiltro = "", $stOrdem = "dt_feriado" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = ' order by ' . $stOrdem;

    $stSql = $this->montaRecuperaFeriadosDisponiveis().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
