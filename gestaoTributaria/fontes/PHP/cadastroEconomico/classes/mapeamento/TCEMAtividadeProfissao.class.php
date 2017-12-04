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
  * Classe de mapeamento da tabela ECONOMICO.ATIVIDADE_PROFISSAO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMAtividadeProfissao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.07
*/

/*
$Log$
Revision 1.9  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.ATIVIDADE_PROFISSAO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMAtividadeProfissao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMAtividadeProfissao()
{
    parent::Persistente();
    $this->setTabela('economico.atividade_profissao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_atividade,cod_profissao');

    $this->AddCampo('cod_atividade','integer',true,'',true,true);
    $this->AddCampo('cod_profissao','integer',true,'',true,true);

}

function recuperaAtividadeProfissao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtividadeProfissao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaAtividadeProfissaoSelecionados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtividadeProfissaoSelecionados().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaAtividadeProfissaoDisponiveis(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtividadeProfissaoDisponiveis().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAtividadeProfissao()
{
    $stSql .="    SELECT                                       \n";
    $stSql .="        PR.cod_profissao,                        \n";
    $stSql .="        PR.nom_profissao                         \n";
    $stSql .="    FROM                                         \n";
    $stSql .="        cse.profissao as PR,                     \n";
    $stSql .="        economico.atividade as A,                \n";
    $stSql .="        economico.atividade_profissao as AP      \n";
    $stSql .="    WHERE                                        \n";
    $stSql .="        A.cod_atividade = AP.cod_atividade AND   \n";
    $stSql .="        AP.cod_profissao = PR.cod_profissao      \n";

    return $stSql;
}

function montaRecuperaAtividadeProfissaoSelecionados()
{
    $stSql .="   SELECT                                    \n ";
    $stSql .="       PR.COD_PROFISSAO,                     \n ";
    $stSql .="       PR.NOM_PROFISSAO                      \n ";
    $stSql .="   FROM                                      \n ";
    $stSql .="       cse.profissao AS PR                   \n ";
    $stSql .="   LEFT JOIN                                 \n ";
    $stSql .="       economico.atividade_profissao AS AP   \n ";
    $stSql .="   ON                                        \n ";
    $stSql .="       PR.COD_PROFISSAO = AP.COD_PROFISSAO   \n ";
    $stSql .="   WHERE                                     \n ";
    $stSql .="       AP.COD_ATIVIDADE IS NOT NULL          \n ";

    return $stSql;
}

function montaRecuperaAtividadeProfissaoDisponiveis()
{
    $stSql .="   SELECT                                    \n ";
    $stSql .="       PR.COD_PROFISSAO,                     \n ";
    $stSql .="       PR.NOM_PROFISSAO                      \n ";
    $stSql .="   FROM                                      \n ";
    $stSql .="       cse.profissao AS PR                   \n ";
   /* $stSql .="    LEFT JOIN                                \n ";
    $stSql .="        economico.atividade_profissao AS AP     \n ";
    $stSql .="    ON                                       \n ";
    $stSql .="        PR.COD_PROFISSAO = AP.COD_PROFISSAO    \n ";
    $stSql .="    WHERE                                    \n ";
    $stSql .="        AP.COD_ATIVIDADE IS NULL              \n ";*/

    return $stSql;
}

function recuperaAtividadesProfissoes(&$rsRecordSet, $stFiltro, $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtividadesProfissoes().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAtividadesProfissoes()
{
    $stSql .="    SELECT                                       \n";
    $stSql .="      DISTINCT                                   \n";
    $stSql .="        PR.cod_profissao,                        \n";
    $stSql .="        PR.nom_profissao                         \n";
    $stSql .="    FROM                                         \n";
    $stSql .="        cse.profissao as PR                      \n";
    $stSql .="    INNER JOIN                                   \n";
    $stSql .="        economico.atividade_profissao as AP      \n";
    $stSql .="    ON                                           \n";
    $stSql .="        AP.cod_profissao = PR.cod_profissao      \n";
    //$stSql .="    WHERE                                        \n";
    return $stSql;
}

}
