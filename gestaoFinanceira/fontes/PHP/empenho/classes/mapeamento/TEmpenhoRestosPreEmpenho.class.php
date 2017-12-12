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
    * Classe de mapeamento da tabela EMPENHO.RESTOS_PRE_EMPENHO
    * Data de Criação: 11/01/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: tonismar $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.01.23
                    uc-02.03.03
*/

/*
$Log$
Revision 1.9  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  EMPENHO.RESTOS_PRE_EMPENHO
  * Data de Criação: 11/01/2005

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoRestosPreEmpenho extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoRestosPreEmpenho()
{
    parent::Persistente();
    $this->setTabela('empenho.restos_pre_empenho');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_pre_empenho,exercicio');

    $this->AddCampo( 'exercicio'        ,'char'   ,true , '04',true ,true  );
    $this->AddCampo( 'cod_pre_empenho'  ,'integer',true ,   '',true ,true  );
    $this->AddCampo( 'num_orgao'        ,'integer',false,   '',false,false );
    $this->AddCampo( 'num_unidade'      ,'integer',false,   '',false,false );
    $this->AddCampo( 'cod_funcao'       ,'integer',false,   '',false,false );
    $this->AddCampo( 'cod_subfuncao'    ,'integer',false,   '',false,false );
    $this->AddCampo( 'cod_programa'     ,'integer',false,   '',false,false );
    $this->AddCampo( 'num_pao'          ,'integer',false,   '',false,false );
    $this->AddCampo( 'recurso'          ,'integer',false,   '',false,false );
    $this->AddCampo( 'cod_estrutural'   ,'varchar',false,'160',false,false );

}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDespesa(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDespesa().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDespesa()
{
    $stSql .= "SELECT                                                            \n";
    $stSql .= "     CASE WHEN tabela.masc_despesa != '' THEN                     \n";
    $stSql .= "        publico.fn_mascara_dinamica( tabela.masc_despesa          \n";
    $stSql .= "                                    ,tabela.despesa      )        \n";
    $stSql .= "     ELSE                                                         \n";
    $stSql .= "        tabela.despesa                                            \n";
    $stSql .= "     END AS dotacao_formatada                                     \n";
    $stSql .= "    ,tabela.num_unidade                                           \n";
    $stSql .= "    ,tabela.num_orgao                                             \n";
    $stSql .= "FROM (                                                            \n";
    $stSql .= "    SELECT num_orgao                                              \n";
    $stSql .= "           ||'.'||num_unidade                                     \n";
    $stSql .= "           ||'.'||cod_funcao                                      \n";
    $stSql .= "           ||'.'||cod_subfuncao                                   \n";
    $stSql .= "           ||'.'||cod_programa                                    \n";
    $stSql .= "           ||'.'||num_pao                                         \n";
    $stSql .= "           ||'.'||replace(cod_estrutural,'.','') AS despesa       \n";
    $stSql .= "          , CASE WHEN tabela.masc_despesa IS NULL THEN            \n";
    $stSql .= "                ''                                                \n";
    $stSql .= "            ELSE                                                  \n";
    $stSql .= "                tabela.masc_despesa                               \n";
    $stSql .= "            END AS masc_despesa                                   \n";
    $stSql .= "           ,num_unidade                                           \n";
    $stSql .= "           ,num_orgao                                             \n";
    $stSql .= "    FROM empenho.restos_pre_empenho                           \n";
    $stSql .= "         ,( SELECT max(valor) AS masc_despesa                     \n";
    $stSql .= "            FROM   administracao.configuracao                                \n";
    $stSql .= "            WHERE  parametro  = 'masc_despesa'                    \n";
    $stSql .= "              AND  exercicio  = '".$this->getDado('exercicio')."' \n";
    $stSql .= "              AND  cod_modulo = 8                                 \n";
    $stSql .= "          ) as tabela                                             \n";
    $stSql .= "    WHERE  cod_pre_empenho = ".$this->getDado('cod_pre_empenho')."\n";
    $stSql .= "      AND  exercicio       = '".$this->getDado('exercicio')."'    \n";
    $stSql .= ") as tabela                                                       \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosExportacao()
{
    $stSql .= "SELECT                                                           \n";
    $stSql .= "     ep.exercicio,                                               \n";
    $stSql .= "     '' as cod_subprograma,                                      \n";
    $stSql .= "     'SUBPROGRAMA' as nom_subprograma                            \n";
    $stSql .= "FROM                                                             \n";
    $stSql .= "     empenho.pre_empenho AS ep                                   \n";
    $stSql .= "WHERE ep.exercicio <= '".$this->getDado('exercicio')."'          \n";
    $stSql .= "UNION                                                            \n";
    $stSql .= "SELECT                                                           \n";
    $stSql .= "     ee.exercicio,                                               \n";
    $stSql .= "     '' as cod_subprograma,                                      \n";
    $stSql .= "     'SUBPROGRAMA' as nom_subprograma                            \n";
    $stSql .= "FROM                                                             \n";
    $stSql .= "     empenho.restos_pre_empenho AS ee                        \n";

    return $stSql;
}

}
