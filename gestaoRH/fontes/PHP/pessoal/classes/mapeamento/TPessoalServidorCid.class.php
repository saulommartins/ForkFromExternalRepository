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
  * Classe de mapeamento da tabela PESSOAL.SERVIDOR_CID
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
  * Efetua conexão com a tabela  PESSOAL.SERVIDOR_CID
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalServidorCid extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalServidorCid()
{
    parent::Persistente();
    $this->setTabela('pessoal.servidor_cid');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_servidor,cod_cid');

    $this->AddCampo('cod_servidor','INTEGER',true,'',true,false);
    $this->AddCampo('cod_cid','INTEGER',true,'',true,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
    $this->AddCampo('data_laudo','date',true,'',false,true);

}

function montaRecuperaCid()
{
$stSql  = "   select                                                            \n";
$stSql .= "    cod_servidor as cod_servidor,                                    \n";
$stSql .= "    cod_cid as cod_cid,                                              \n";
$stSql .= "    data_laudo as data_laudo,                                        \n";
$stSql .= "    MAX( timestamp ) as cod_norma                                    \n";
$stSql .= "   FROM                                                              \n";
$stSql .= "       pessoal.servidor_cid                                          \n";
return $stSql;

}

/*
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaRelacionamentoCargo.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaCid(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " GROUP BY cod_servidor, cod_cid, data_laudo ";
    $stSql = $this->montaRecuperaCid().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
