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
    * Classe de mapeamento
    * Data de Criação: 14/06/2007

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.00.00

    $Id: TEntidade.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão
  * Data de Criação: 14/06/20075

  * @author Analista: Diego Lemos de Souza
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEntidade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEntidade()
{
    parent::Persistente();
}

function recuperaEsquemasCriados(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaEsquemasCriados().$stFiltro;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEsquemasCriados()
{
    $stSql  = "SELECT nspname       \n";
    $stSql .= "  FROM pg_namespace  \n";

    return $stSql;
}

function replicarEsquemasRH(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaReplicarEsquemasRH();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaReplicarEsquemasRH()
{
    $stSql .= "SELECT replicarEsquemasRH(".$this->getDado("cod_entidade").",'".Sessao::getExercicio()."');       \n";

    return $stSql;
}

###########################################################
##GESTÃO RECURSOS HUMANOS
##Função: recuperaEntidadesUsuarios
##Autor: Diego Lemos de Souza
##Data: 01/06/2007
##Função utilizada no controle de entidades do usuários
##Não alterar essa consulta, sob pena de ocorrer algum
##problema no controle de entidades da gestão de recursos
##humanos.
###########################################################
function recuperaEntidadesUsuarios(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaEntidadesUsuarios().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEntidadesUsuarios()
{
    $stSql  = "SELECT sw_cgm.numcgm                                            \n";
    $stSql .= "     , sw_cgm.nom_cgm                                           \n";
    $stSql .= "     , usuario_entidade.numcgm as numcgm_usuario                \n";
    $stSql .= "     , entidade.cod_entidade                                    \n";
    $stSql .= "  FROM orcamento.entidade                                       \n";
    $stSql .= "     , orcamento.usuario_entidade                               \n";
    $stSql .= "     , sw_cgm                                                   \n";
    $stSql .= " WHERE entidade.numcgm = sw_cgm.numcgm                          \n";
    $stSql .= "   AND entidade.exercicio = usuario_entidade.exercicio          \n";
    $stSql .= "   AND entidade.cod_entidade = usuario_entidade.cod_entidade    \n";

    return $stSql;
}
###########################################################
##GESTÃO RECURSOS HUMANOS
##Função: recuperaEntidadesUsuarios
##Autor: Diego Lemos de Souza
##Data: 01/06/2007
###########################################################

function recuperaEntidades(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaEntidades().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaEntidades()
{
    $stSql  = "SELECT entidade.*                           \n";
    $stSql .= "     , sw_cgm.nom_cgm                       \n";
    $stSql .= "  FROM orcamento.entidade                   \n";
    $stSql .= "     , sw_cgm                               \n";
    $stSql .= " WHERE entidade.numcgm = sw_cgm.numcgm      \n";

    return $stSql;
}

function recuperaInformacoesCGMEntidade(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaInformacoesCGMEntidade",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaInformacoesCGMEntidade()
{
    $stSql  = "SELECT entidade.*                                                                     \n";
    $stSql .= "     , sw_cgm.*                                                                       \n";
    $stSql .= "     , sw_cgm_pessoa_juridica.*                                                       \n";
    $stSql .= "     , publico.mascara_cpf_cnpj(sw_cgm_pessoa_juridica.cnpj,'cnpj') as cnpj_formatado \n";
    $stSql .= "  FROM orcamento.entidade                                                             \n";
    $stSql .= "     , sw_cgm                                                                         \n";
    $stSql .= "     , sw_cgm_pessoa_juridica                                                         \n";
    $stSql .= " WHERE entidade.numcgm = sw_cgm.numcgm                                                \n";
    $stSql .= "   AND entidade.numcgm = sw_cgm_pessoa_juridica.numcgm                                \n";

    return $stSql;
}

function montaRecuperaBibliotecaEntidade()
{
    $stSql  = "SELECT cod_biblioteca                       \n";
    $stSql .= "  FROM administracao.biblioteca_entidade    \n";

    return $stSql;
}

function recuperaBibliotecaEntidade(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaBibliotecaEntidade",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function recuperaLogotipoEntidade(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaLogotipoEntidade",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaLogotipoEntidade()
{
    $stSql  = "SELECT logotipo FROM orcamento.entidade_logotipo \n";

    return $stSql;
}

function recuperaSchemasRH(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaSchemasRH",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaSchemasRH()
{
    $stSql = "SELECT * FROM administracao.schema_rh";

    return $stSql;
}
}
