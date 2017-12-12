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
    * Classe de mapeamento da tabela PESSOAL.SERVIDOR_CONJUGE
    * Data de Criação: 08/09/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.SERVIDOR_ESTADO_CIVIL
  * Data de Criação: 08/09/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalServidorConjuge extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalServidorConjuge()
{
    parent::Persistente();
    $this->setTabela('pessoal.servidor_conjuge');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_servidor,timestamp');

    $this->AddCampo( 'cod_servidor'          , 'integer'   ,true,  ''   ,true,  true  );
    $this->AddCampo( 'timestamp'             , 'timestamp' ,false, ''   ,true,  false );
    $this->AddCampo( 'numcgm'                , 'integer'   ,true , ''   ,false, true  );
    $this->AddCampo( 'bo_excluido'           , 'boolean'   ,true , false,false, false );

}

function montaRecuperaConjuge()
{
    $stSql .= "SELECT servidor_conjuge.*                                                             \n";
    $stSql .= "  FROM pessoal.servidor_conjuge                                      \n";
    $stSql .= "     , (SELECT cod_servidor                                          \n";
    $stSql .= "             , max(timestamp) as timestamp                           \n";
    $stSql .= "          FROM pessoal.servidor_conjuge                              \n";
    $stSql .= "        GROUP BY cod_servidor) as max_servidor_conjuge               \n";
    $stSql .= " WHERE servidor_conjuge.cod_servidor = max_servidor_conjuge.cod_servidor \n";
    $stSql .= "   AND servidor_conjuge.timestamp = max_servidor_conjuge.timestamp   \n";

    return $stSql;
}

/*
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaConjuge.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaConjuge(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaConjuge().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
