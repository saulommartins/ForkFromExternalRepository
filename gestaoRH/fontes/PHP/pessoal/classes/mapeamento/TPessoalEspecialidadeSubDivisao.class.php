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
  * Data de Criação: 29/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.ESPECIALIDADE_SUB_DIVISAO
  * Data de Criação: 29/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalEspecialidadeSubDivisao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalEspecialidadeSubDivisao()
{
    parent::Persistente();
    $this->setTabela('pessoal.especialidade_sub_divisao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_especialidade, cod_sub_divisao, timestamp');

    $this->AddCampo('cod_especialidade', 'integer',   true,  '', true,  true  );
    $this->AddCampo('cod_sub_divisao',   'integer',   true,  '', true,  true  );
    $this->AddCampo('timestamp',         'timestamp', false, '', true,  false );
    $this->AddCampo('cod_norma',         'integer',   true,  '', false, true  );
    $this->AddCampo('nro_vaga_criada',   'integer',   true,  '', false, false );
}

function recuperaVagasEspecialidade(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaVagasEspecialidade().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaVagasEspecialidade()
{
    $stSql .= "SELECT especialidade_sub_divisao.*                                                                      \n";
    $stSql .= "     , sub_divisao.cod_regime                                                                           \n";
    $stSql .= "  FROM pessoal.especialidade_sub_divisao                                                                \n";
    $stSql .= "     , (   SELECT especialidade_sub_divisao.cod_sub_divisao                                             \n";
    $stSql .= "                , especialidade_sub_divisao.cod_especialidade                                           \n";
    $stSql .= "                , max(especialidade_sub_divisao.timestamp) as timestamp                                 \n";
    $stSql .= "             FROM pessoal.especialidade_sub_divisao                                                     \n";
    $stSql .= "         GROUP BY especialidade_sub_divisao.cod_sub_divisao                                             \n";
    $stSql .= "                , especialidade_sub_divisao.cod_especialidade) as max_especialidade_sub_divisao         \n";
    $stSql .= "     , pessoal.especialidade                                                                            \n";
    $stSql .= "     , pessoal.sub_divisao                                                                              \n";
    $stSql .= "     , pessoal.regime                                                                                   \n";
    $stSql .= " WHERE especialidade_sub_divisao.cod_especialidade = max_especialidade_sub_divisao.cod_especialidade    \n";
    $stSql .= "   AND especialidade_sub_divisao.cod_sub_divisao = max_especialidade_sub_divisao.cod_sub_divisao        \n";
    $stSql .= "   AND especialidade_sub_divisao.timestamp = max_especialidade_sub_divisao.timestamp                    \n";
    $stSql .= "   AND especialidade_sub_divisao.cod_especialidade = especialidade.cod_especialidade                    \n";
    $stSql .= "   AND especialidade_sub_divisao.cod_sub_divisao = sub_divisao.cod_sub_divisao                          \n";
    $stSql .= "   AND sub_divisao.cod_regime = regime.cod_regime                                                       \n";

    return $stSql;
}

function getVagasOcupadasEspecialidade(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro = $this->executaRecupera("montaGetVagasOcupadasEspecialidade",$rsRecordSet, $stFiltro, $stOrdem , $boTransacao);

    return $obErro;
}

function montaGetVagasOcupadasEspecialidade()
{
    $inCodPeriodoMovimentacao = 0;
    if ($this->getDado('cod_periodo_movimentacao') != "") {
        $inCodPeriodoMovimentacao = $this->getDado('cod_periodo_movimentacao');
    }

    $stSql  = "SELECT getVagasOcupadasEspecialidade(".$this->getDado("cod_regime")."                 \n";
    $stSql .= "                                     ,".$this->getDado("cod_sub_divisao")."           \n";
    $stSql .= "                                     ,".$this->getDado("cod_especialidade")."         \n";
    $stSql .= "                                     ,".$inCodPeriodoMovimentacao."                   \n";
    $stSql .= "                                     ,true                                            \n";
    $stSql .= "                                     ,'".$this->getDado("entidade")."') as vagas;     \n";

    return $stSql;
}

function getVagasDisponiveisEspecialidade(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro = $this->executaRecupera("montaGetVagasDisponiveisEspecialidade",$rsRecordSet, $stFiltro, $stOrdem , $boTransacao);

    return $obErro;
}

function montaGetVagasDisponiveisEspecialidade()
{
    $inCodPeriodoMovimentacao = 0;
    if ($this->getDado('cod_periodo_movimentacao') != "") {
        $inCodPeriodoMovimentacao = $this->getDado('cod_periodo_movimentacao');
    }

    $stSql  = "SELECT getVagasDisponiveisEspecialidade(".$this->getDado("cod_regime")."              \n";
    $stSql .= "                                        ,".$this->getDado("cod_sub_divisao")."        \n";
    $stSql .= "                                        ,".$this->getDado("cod_especialidade")."      \n";
    $stSql .= "                                        ,".$inCodPeriodoMovimentacao."                \n";
    $stSql .= "                                        ,true                                         \n";
    $stSql .= "                                        ,'".$this->getDado("entidade")."') as vagas;  \n";

    return $stSql;
}

function getVagasCadastradasEspecialidade(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro = $this->executaRecupera("montaGetVagasCadastradasEspecialidade",$rsRecordSet, $stFiltro, $stOrdem , $boTransacao);

    return $obErro;
}

function montaGetVagasCadastradasEspecialidade()
{
    $inCodPeriodoMovimentacao = 0;
    if ($this->getDado('cod_periodo_movimentacao') != "") {
        $inCodPeriodoMovimentacao = $this->getDado('cod_periodo_movimentacao');
    }

    $stSql  = "SELECT getVagasCadastradasEspecialidade(".$this->getDado("cod_regime")."              \n";
    $stSql .= "                                        ,".$this->getDado("cod_sub_divisao")."        \n";
    $stSql .= "                                        ,".$this->getDado("cod_especialidade")."      \n";
    $stSql .= "                                        ,".$inCodPeriodoMovimentacao."                \n";
    $stSql .= "                                        ,'".$this->getDado("entidade")."') as vagas;  \n";

    return $stSql;
}

function consultarServidoresPorEspecialidade(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro = $this->executaRecupera("montaConsultarServidoresPorEspecialidade",$rsRecordSet, $stFiltro, $stOrdem , $boTransacao);

    return $obErro;
}

function montaConsultarServidoresPorEspecialidade()
{
    $stSql  = "SELECT * FROM consultarServidoresPorEspecialidade(".$this->getDado("cod_regime")."         \n";
    $stSql .= "                                                  ,".$this->getDado("cod_sub_divisao")."   \n";
    $stSql .= "                                                  ,".$this->getDado("cod_especialidade")." \n";
    $stSql .= "                                                  ,'".Sessao::getEntidade()."') as vagas;  \n";

    return $stSql;
}

}
