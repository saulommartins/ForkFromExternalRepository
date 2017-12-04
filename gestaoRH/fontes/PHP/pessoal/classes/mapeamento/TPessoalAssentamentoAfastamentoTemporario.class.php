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
    * Classe de mapeamento da tabela PESSOAL.ASSENTAMENTO_AFASTAMENTO_TEMPORARIO
    * Data de Criação: 10/08/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-11-20 13:08:58 -0200 (Ter, 20 Nov 2007) $

    * Casos de uso: uc-04.04.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.ASSENTAMENTO_AFASTAMENTO_TEMPORARIO
  * Data de Criação: 10/08/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAssentamentoAfastamentoTemporario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamentoAfastamentoTemporario()
{
    parent::Persistente();
    $this->setTabela('pessoal.assentamento_afastamento_temporario');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_assentamento,timestamp');

    $this->AddCampo('cod_assentamento','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,true);
}

function recuperaAssentamentoTemporarioRais(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaAssentamentoTemporarioRais",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaAssentamentoTemporarioRais()
{
    $stSql  = "SELECT assentamento_rais_afastamento.cod_rais                                                                                       \n";
    $stSql .= "     , (assentamento_gerado.periodo_final - assentamento_gerado.periodo_inicial) as dias                                            \n";
    $stSql .= "     , to_char(assentamento_gerado.periodo_inicial,'ddmm') as dt_inicial                                                            \n";
    $stSql .= "     , to_char(assentamento_gerado.periodo_final,'ddmm') as dt_final                                                              \n";
    $stSql .= "  FROM pessoal.assentamento_afastamento_temporario                                                          \n";
    $stSql .= "     , (SELECT cod_assentamento                                                                                                     \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                                          \n";
    $stSql .= "          FROM pessoal.assentamento_afastamento_temporario                                                  \n";
    $stSql .= "        GROUP BY cod_assentamento) AS max_assentamento_afastamento_temporario                                                       \n";
    $stSql .= "     , (SELECT assentamento_gerado.cod_assentamento_gerado                                                                          \n";
    $stSql .= "             , assentamento_gerado.cod_assentamento                                                                                 \n";
    $stSql .= "             , assentamento_gerado.timestamp                                                                                        \n";
    $stSql .= "             , CASE WHEN assentamento_gerado.periodo_final IS NULL OR                                                               \n";
    $stSql .= "                         to_char(assentamento_gerado.periodo_final,'yyyy') > '".$this->getDado("exercicio")."'                      \n";
    $stSql .= "               THEN to_date('".$this->getDado("exercicio")."-12-31','yyyy-mm-dd')::date                                             \n";
    $stSql .= "               ELSE assentamento_gerado.periodo_final END as periodo_final                                                          \n";
    $stSql .= "             , CASE WHEN to_char(assentamento_gerado.periodo_inicial,'yyyy') < '".$this->getDado("exercicio")."'                    \n";
    $stSql .= "               THEN to_date('".$this->getDado("exercicio")."-01-01','yyyy-mm-dd')::date                                             \n";
    $stSql .= "               ELSE assentamento_gerado.periodo_inicial END as periodo_inicial                                                      \n";
    $stSql .= "          FROM pessoal.assentamento_gerado) as assentamento_gerado                                          \n";
    $stSql .= "     , (SELECT cod_assentamento_gerado                                                                                              \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                                          \n";
    $stSql .= "          FROM pessoal.assentamento_gerado                                                                  \n";
    $stSql .= "        GROUP BY cod_assentamento_gerado) AS max_assentamento_gerado                                                                \n";
    $stSql .= "     , pessoal.assentamento_gerado_contrato_servidor                                                        \n";
    $stSql .= "     , pessoal.assentamento_rais_afastamento                                                                \n";
    $stSql .= " WHERE assentamento_afastamento_temporario.cod_assentamento = max_assentamento_afastamento_temporario.cod_assentamento              \n";
    $stSql .= "   AND assentamento_afastamento_temporario.timestamp = max_assentamento_afastamento_temporario.timestamp                            \n";
    $stSql .= "   AND assentamento_afastamento_temporario.cod_assentamento = assentamento_gerado.cod_assentamento                                  \n";
    $stSql .= "   AND assentamento_afastamento_temporario.timestamp = assentamento_rais_afastamento.timestamp                                      \n";
    $stSql .= "   AND assentamento_afastamento_temporario.cod_assentamento = assentamento_rais_afastamento.cod_assentamento                        \n";
    $stSql .= "   AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado                                \n";
    $stSql .= "   AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp                                                            \n";
    $stSql .= "   AND assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado                  \n";
    $stSql .= "   AND NOT EXISTS (SELECT 1                                                                                                         \n";
    $stSql .= "                     FROM pessoal.assentamento_gerado_excluido                                              \n";
    $stSql .= "                    WHERE assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_excluido.cod_assentamento_gerado        \n";
    $stSql .= "                      AND assentamento_gerado.timestamp = assentamento_gerado_excluido.timestamp)                                   \n";
    $stSql .= "   AND (to_char(assentamento_gerado.periodo_inicial,'yyyy') = '".$this->getDado("exercicio")."' OR to_char(assentamento_gerado.periodo_final,'yyyy') = '".$this->getDado("exercicio")."') \n";
    $stSql .= "   AND (assentamento_gerado.periodo_final - assentamento_gerado.periodo_inicial) > ".$this->getDado("dias")."               \n";
    $stSql .= "   AND cod_contrato = ".$this->getDado("cod_contrato")."                                                                            \n";
    $stSql .= "   ORDER BY dias DESC                                                                                                               \n";

    return $stSql;
}

}
