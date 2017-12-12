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
  * Classe de mapeamento da tabela PESSOAL.CONTRATO_SERVIDOR_LOCAL
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CONTRATO_SERVIDOR_LOCAL
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidorLocal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidorLocal()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor_local');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,cod_local,timestamp');

    $this->AddCampo('cod_contrato','INTEGER',true,'',true,true);
    $this->AddCampo('cod_local','INTEGER',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);

}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaRelacionamento
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT cont_local.*                                  \n";
    $stSql .= "     , local.descricao                               \n";
    $stSql .= "  FROM pessoal.contrato_servidor_local as cont_local \n";
    $stSql .= "     , (SELECT cod_contrato
                            , max(timestamp) as timestamp
                         FROM pessoal.contrato_servidor_local
                       GROUP BY cod_contrato) as max_contrato_servidor_local\n";
    $stSql .= "     , organograma.local as local                    \n";
    $stSql .= " WHERE cont_local.cod_local = local.cod_local        \n";
    $stSql .= "   AND cont_local.cod_contrato = max_contrato_servidor_local.cod_contrato        \n";
    $stSql .= "   AND cont_local.timestamp = max_contrato_servidor_local.timestamp        \n";

    return $stSql;
}

function recuperaContratosFerias(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosFerias",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosFerias()
{
    $stSql .= "SELECT contrato_servidor_local.*                                                                         \n";
    $stSql .= "     , servidor.numcgm                                                                                   \n";
    $stSql .= "  FROM pessoal.contrato_servidor_local                                         \n";
    $stSql .= "     , ( SELECT cod_contrato                                                                             \n";
    $stSql .= "              , max(timestamp) as timestamp                                                              \n";
    $stSql .= "           FROM pessoal.contrato_servidor_local                                \n";
    $stSql .= "       GROUP BY cod_contrato) as max_contrato_servidor_local                                             \n";
    $stSql .= "     , pessoal.contrato_servidor_regime_funcao                                 \n";
    $stSql .= "     , (  SELECT cod_contrato                                                                            \n";
    $stSql .= "               , max(timestamp) as timestamp                                                             \n";
    $stSql .= "            FROM pessoal.contrato_servidor_regime_funcao                       \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao                                    \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                      \n";
    $stSql .= "     , pessoal.servidor                                                        \n";
    $stSql .= " WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                   \n";
    $stSql .= "   AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp                         \n";
    $stSql .= "   AND contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato   \n";
    $stSql .= "   AND contrato_servidor_regime_funcao.timestamp    = max_contrato_servidor_regime_funcao.timestamp      \n";
    $stSql .= "   AND contrato_servidor_regime_funcao.cod_contrato = contrato_servidor_local.cod_contrato               \n";
    $stSql .= "   AND contrato_servidor_local.cod_contrato = servidor_contrato_servidor.cod_contrato                    \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                   \n";
    $stSql .= "   AND NOT EXISTS (SELECT 1                                                                        \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_caso_causa           \n";
    $stSql .= "                    WHERE contrato_servidor_caso_causa.cod_contrato = contrato_servidor_local.cod_contrato   )    \n";

    return $stSql;
}

function recuperaContratosDoLocal(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosDoLocal",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosDoLocal()
{
    $stSql .= "SELECT contrato_servidor_local.*                                                             \n";
    $stSql .= "     , (SELECT registro FROM pessoal.contrato where cod_contrato = contrato_servidor_local.cod_contrato) as registro  \n";
    $stSql .= "     , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm                \n";
    $stSql .= "  FROM pessoal.contrato_servidor_local                              \n";
    $stSql .= "  JOIN pessoal.servidor_contrato_servidor                           \n";
    $stSql .= "    ON contrato_servidor_local.cod_contrato = servidor_contrato_servidor.cod_contrato       \n";
    $stSql .= "  JOIN pessoal.servidor                                             \n";
    $stSql .= "    ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                       \n";
    $stSql .= "  JOIN (  SELECT cod_contrato                                                                \n";
    $stSql .= "               , MAX(timestamp) as timestamp                                                 \n";
    $stSql .= "            FROM pessoal.contrato_servidor_local                    \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_local                                \n";
    $stSql .= "    ON contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato       \n";
    $stSql .= "   AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp             \n";

    return $stSql;
}

}
