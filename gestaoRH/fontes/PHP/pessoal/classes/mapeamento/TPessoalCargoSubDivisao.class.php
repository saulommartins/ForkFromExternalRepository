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
  * Classe de mapeamento da tabela PESSOAL.CARGO_SUB_DIVISAO
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Gustavo Tourinho

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2008-04-02 10:05:18 -0300 (Qua, 02 Abr 2008) $

    Caso de uso: uc-04.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CARGO_SUB_DIVISAO
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Gustavo Tourinho

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCargoSubDivisao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCargoSubDivisao()
{
    parent::Persistente();
    $this->setTabela('pessoal.cargo_sub_divisao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_cargo, cod_sub_divisao, timestamp');

    $this->AddCampo('cod_cargo',       'integer',    true, '',  true,  true);
    $this->AddCampo('cod_sub_divisao', 'integer',    true, '',  true,  true);
    $this->AddCampo('timestamp',       'timestamp', false, '', false, false);
    $this->AddCampo('cod_norma',       'integer',    true, '', false, false);
    $this->AddCampo('nro_vaga_criada', 'integer',   false, '', false, false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT cargo_sub_divisao.cod_cargo                                                   \n";
    $stSql .= "     , cargo_sub_divisao.nro_vaga_criada                                             \n";
    $stSql .= "     , cargo_sub_divisao.cod_norma as norma_maxima                                   \n";
    $stSql .= "     , (SELECT cod_norma FROM pessoal.cargo_sub_divisao AS minima WHERE cod_cargo = ".$this->getDado('cod_cargo')." and cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao order by timestamp limit 1) as norma_minima \n";
    $stSql .= "     , to_char(max_cargo_sub_divisao.timestamp,'dd/mm/yyyy') as timestamp            \n";
    $stSql .= "     , sub_divisao.descricao AS nom_sub_divisao                                      \n";
    $stSql .= "     , sub_divisao.cod_sub_divisao                                                   \n";
    $stSql .= "     , regime.descricao as nom_regime                                                \n";
    $stSql .= "     , regime.cod_regime                                                             \n";
    $stSql .= "  FROM pessoal.cargo_sub_divisao                            \n";
    $stSql .= "  JOIN (  SELECT cod_sub_divisao                                                     \n";
    $stSql .= "               , cod_cargo                                                           \n";
    $stSql .= "               , max(timestamp) as timestamp                                         \n";
    $stSql .= "            FROM pessoal.cargo_sub_divisao                  \n";
    $stSql .= "        GROUP BY cod_sub_divisao                                                     \n";
    $stSql .= "               , cod_cargo) as max_cargo_sub_divisao                                 \n";
    $stSql .= "    ON cargo_sub_divisao.cod_sub_divisao = max_cargo_sub_divisao.cod_sub_divisao     \n";
    $stSql .= "   AND cargo_sub_divisao.cod_cargo = max_cargo_sub_divisao.cod_cargo                 \n";
    $stSql .= "   AND cargo_sub_divisao.timestamp = max_cargo_sub_divisao.timestamp                 \n";
    $stSql .= "  JOIN pessoal.sub_divisao                                  \n";
    $stSql .= "    ON sub_divisao.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao               \n";
    $stSql .= "  JOIN pessoal.regime                                       \n";
    $stSql .= "    ON sub_divisao.cod_regime = regime.cod_regime                                    \n";
    $stSql .= " WHERE cargo_sub_divisao.cod_cargo = ".$this->getDado('cod_cargo');

    return $stSql;
}

function recuperaVagasServidor(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaVagasServidor().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaVagasServidor()
{
    $stSql .= "SELECT cargo_sub_divisao.*                                                                  \n";
    $stSql .= "     , sub_divisao.cod_regime                                                               \n";
    $stSql .= "  FROM pessoal.cargo_sub_divisao                                   \n";
    $stSql .= "     , (   SELECT cargo_sub_divisao.cod_sub_divisao                                         \n";
    $stSql .= "                , cargo_sub_divisao.cod_cargo                                               \n";
    $stSql .= "                , max(cargo_sub_divisao.timestamp) as timestamp                             \n";
    $stSql .= "             FROM pessoal.cargo_sub_divisao                        \n";
    $stSql .= "         GROUP BY cargo_sub_divisao.cod_sub_divisao                                         \n";
    $stSql .= "                , cargo_sub_divisao.cod_cargo) as max_cargo_sub_divisao                     \n";
    $stSql .= "     , pessoal.sub_divisao                                         \n";
    $stSql .= "     , pessoal.regime                                              \n";
    $stSql .= " WHERE cargo_sub_divisao.cod_cargo = max_cargo_sub_divisao.cod_cargo                        \n";
    $stSql .= "   AND cargo_sub_divisao.cod_sub_divisao = max_cargo_sub_divisao.cod_sub_divisao            \n";
    $stSql .= "   AND cargo_sub_divisao.timestamp = max_cargo_sub_divisao.timestamp                        \n";
    $stSql .= "   AND cargo_sub_divisao.cod_sub_divisao = sub_divisao.cod_sub_divisao                      \n";
    $stSql .= "   AND sub_divisao.cod_regime = regime.cod_regime                                           \n";

    return $stSql;
}

function getVagasOcupadasCargo(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro = $this->executaRecupera("montaGetVagasOcupadasCargo",$rsRecordSet, $stFiltro, $stOrdem , $boTransacao);

    return $obErro;
}

function montaGetVagasOcupadasCargo()
{
    $inCodPeriodoMovimentacao = 0;
    if ($this->getDado('cod_periodo_movimentacao') != "") {
        $inCodPeriodoMovimentacao = $this->getDado('cod_periodo_movimentacao');
    }

    $stSql  = "SELECT getVagasOcupadasCargo(".$this->getDado("cod_regime")."         \n";
    $stSql .= "                             ,".$this->getDado("cod_sub_divisao")."   \n";
    $stSql .= "                             ,".$this->getDado("cod_cargo")."         \n";
    $stSql .= "                             ,".$inCodPeriodoMovimentacao."           \n";
    $stSql .= "                             ,true                                    \n";
    $stSql .= "                             ,'".Sessao::getEntidade()."') as vagas;  \n";

    return $stSql;
}

function getVagasDisponiveisCargo(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro = $this->executaRecupera("montaGetVagasDisponiveisCargo",$rsRecordSet, $stFiltro, $stOrdem , $boTransacao);

    return $obErro;
}

function montaGetVagasDisponiveisCargo()
{
    $inCodPeriodoMovimentacao = 0;
    if ($this->getDado('cod_periodo_movimentacao') != "") {
        $inCodPeriodoMovimentacao = $this->getDado('cod_periodo_movimentacao');
    }

    $stSql  = "SELECT getVagasDisponiveisCargo(".$this->getDado("cod_regime")."            \n";
    $stSql .= "                                ,".$this->getDado("cod_sub_divisao")."      \n";
    $stSql .= "                                ,".$this->getDado("cod_cargo")."            \n";
    $stSql .= "                                ,".$inCodPeriodoMovimentacao."              \n";
    $stSql .= "                                ,true                                       \n";
    $stSql .= "                                ,'".Sessao::getEntidade()."') as vagas;     \n";

    return $stSql;
}

function getVagasCadastradasCargo(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro = $this->executaRecupera("montaGetVagasCadastradasCargo",$rsRecordSet, $stFiltro, $stOrdem , $boTransacao);

    return $obErro;
}

function montaGetVagasCadastradasCargo()
{
    $inCodPeriodoMovimentacao = 0;
    if ($this->getDado('cod_periodo_movimentacao') != "") {
        $inCodPeriodoMovimentacao = $this->getDado('cod_periodo_movimentacao');
    }

    $stSql  = "SELECT getVagasCadastradasCargo(".$this->getDado("cod_regime")."            \n";
    $stSql .= "                                ,".$this->getDado("cod_sub_divisao")."      \n";
    $stSql .= "                                ,".$this->getDado("cod_cargo")."            \n";
    $stSql .= "                                ,".$inCodPeriodoMovimentacao."              \n";
    $stSql .= "                                ,'".Sessao::getEntidade()."') as vagas;     \n";

    return $stSql;
}

function consultarServidoresPorCargo(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro = $this->executaRecupera("montaConsultarServidoresPorCargo",$rsRecordSet, $stFiltro, $stOrdem , $boTransacao);

    return $obErro;
}

function montaConsultarServidoresPorCargo()
{
    $stSql  = "SELECT * FROM consultarServidoresPorCargo(".$this->getDado("cod_regime")."                \n";
    $stSql .= "                                    ,".$this->getDado("cod_sub_divisao")."         \n";
    $stSql .= "                                    ,".$this->getDado("cod_cargo")."               \n";
    $stSql .= "                                    ,'".Sessao::getEntidade()."') as vagas;        \n";

    return $stSql;
}

}
