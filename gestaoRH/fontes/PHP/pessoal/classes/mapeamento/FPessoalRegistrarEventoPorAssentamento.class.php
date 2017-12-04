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
    * Classe de mapeamento da funcao registrarEventoPorAssentamento
    * Data de Criação: 24/08/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 13:03:23 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a funcao registrarEventoPorAssentamento
  * Data de Criação: 24/08/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class FPessoalRegistrarEventoPorAssentamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FPessoalRegistrarEventoPorAssentamento()
{
    parent::Persistente();
    $this->setTabela('registrarEventoPorAssentamento');
}

function registrarEventoPorAssentamento($boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaRegistrarEventoPorAssentamento().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRegistrarEventoPorAssentamento()
{
    $stSql .= "SELECT ".$this->getTabela()."(".$this->getDado('cod_contrato').",".$this->getDado('cod_assentamento').",'".$this->getDado('acao')."','".Sessao::getEntidade()."') as retorno  \n";

    return $stSql;
}

function recuperaContratosParaRegistroNaVirada(&$rsRecordSet,$stFiltro,$stOrdem,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaRecuperaContratosParaRegistroNaVirada().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosParaRegistroNaVirada()
{
    $stSql .= "SELECT assentamento_gerado_contrato_servidor.cod_contrato                                                               \n";
    $stSql .= "     , assentamento_gerado.cod_assentamento                                                                             \n";
    $stSql .= "     , to_char(assentamento_gerado.periodo_inicial,'yyyy-mm') as periodo_inicial                                        \n";
    $stSql .= "     , to_char(assentamento_gerado.periodo_final  ,'yyyy-mm') as periodo_final                                          \n";
    $stSql .= "  FROM pessoal.assentamento_gerado_contrato_servidor                                                                    \n";
    $stSql .= "     , pessoal.assentamento_gerado                                                                                      \n";
    $stSql .= "     , (SELECT cod_assentamento_gerado                                                                                  \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                              \n";
    $stSql .= "          FROM pessoal.assentamento_gerado                                                                              \n";
    $stSql .= "        GROUP BY cod_assentamento_gerado) AS max_assentamento_gerado                                                    \n";
    $stSql .= " WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado      \n";
    $stSql .= "   AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado                    \n";
    $stSql .= "   AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp                                                \n";
    $stSql .= "   AND NOT EXISTS (SELECT *                                                                                             \n";
    $stSql .= "                     FROM pessoal.assentamento_gerado_excluido                                                          \n";
    $stSql .= "                    WHERE assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp                        \n";
    $stSql .= "                      AND assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado)\n";

    return $stSql;
}

function excluirRegistroEventoAutomatico($boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaExcluirRegistroEventoAutomatico().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaExcluirRegistroEventoAutomatico()
{
    $stSql .= "SELECT excluirRegistroEventoAutomatico(".$this->getDado('cod_contrato').",".$this->getDado('cod_periodo_movimentacao').",".$this->getDado('cod_evento').",'".$this->getDado("tipo")."') as retorno  \n";

    return $stSql;
}

}
